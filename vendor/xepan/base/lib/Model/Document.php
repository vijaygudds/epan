<?php

/**
* description: Document is a global model for almost all documents in xEpan platform.
* Main purpose of document model/table is to give a system wide unique id for all documents spreaded 
* in various tables.
* 
* @author : Gowrav Vishwakarma
* @email : gowravvishwakarma@gmail.com, info@xavoc.com
* @website : http://xepan.org
* 
*/

namespace xepan\base;

class Model_Document extends \xepan\base\Model_Table{
	
	public $table='document';
	public $strict_fields=false;
	
	public $status=[];
	public $actions=[];
	public $addOtherInfo=false;
	public $otherInfoFields=[];

	public $document_type=null;

	function init(){
		parent::init();
		
		// $this->hasOne('xepan\base\Epan');
		$this->hasOne('xepan\base\Contact','created_by_id')->system(true);
		$this->hasOne('xepan\base\Contact','updated_by_id')->system(true);
		$this->hasOne('xepan\base\Branch','branch_id')->system(true)->defaultValue(@$this->app->branch->id);

		$this->addField('status')->enum($this->status)->mandatory(true)->system(true);
		$this->addField('type')->mandatory(true);
		if($this->document_type) $this->addCondition('type',$this->document_type);
		$this->addField('sub_type')->system(true);

		$this->hasMany('xepan\base\Document_Attachment','document_id',null,'Attachments');

		$this->addExpression('attachments_count')->set($this->refSQL('Attachments')->count());
		$this->addField('search_string')->type('text')->system(true)->defaultValue(null);

		$this->addField('created_at')->type('date')->defaultValue($this->app->now)->sortable(true)->system(true);
		$this->addField('updated_at')->type('date')->defaultValue($this->app->now)->sortable(true)->system(true);

		$this->hasMany('xepan\base\Document_Other','document_id',null,'OtherDocumentInfos');


		if($this->addOtherInfo){

			$other_info_config_m = $this->add('xepan\base\Model_Config_DocumentOtherInfo');
			$other_info_config_m->addCondition('for',$this->document_type);
			// todo check json model condition is not working
			// $other_fields = array_column($other_info_config_m->config_data, 'name');	

			foreach ($other_info_config_m->config_data as $data) {
				if($data['for'] != $this->document_type) continue;

				$ot_fields = $data['name'];
				if(!trim($ot_fields)) continue;

				$ot_fields = strtolower($ot_fields);
				$normalize_name = $this->app->normalizeName($ot_fields);
				$this->otherInfoFields[$normalize_name] = $ot_fields;

				$this->addExpression($normalize_name)->set(function($m,$q)use($ot_fields){
					return $m->refSQL('OtherDocumentInfos')->addCondition('head',$ot_fields)->fieldQuery('value');
				});
			}
		}

		$this->addHook('beforeDelete',[$this,'DeleteAttachements']);
		$this->addHook('afterSave',[$this,'shootStatusAction']);

		$this->is([
				'created_at|required',
				'type|to_trim|required'
			]);

	}

	function shootStatusAction(){
		if(!isset($this->wasDirty['status'])) return;

		$status = $this['status'];
		$model = $this->add('xepan\base\Model_Config_DocumentActionNotification');
		$model->addCondition('for',$this->document_type);
		$model->addCondition('on_status',$this['status']);
		$model->tryLoadAny();
		

		// manage list of notification data
		foreach ($model as $m) {
			if($m['sms_content'] && $m['sms_send_from'] && ($m['sms_send_to_custom_mobile_no'] || $m['sms_send_to_related_contact'])){
				try{
					$this->shootSMSStatusAction($m);
				}catch(\Exception $e){
					
				}
			}

			if($m['email_subject'] && $m['email_send_from'] && ($m['email_send_to_custom_email_ids'] || $m['email_send_to_related_contact'])){
				try{
					$this->shootEmailStatusAction($m);
				}catch(\Exception $e){
					
				}
			}
		}
	}

	function shootEmailStatusAction($m){

		$data_array = $this->data;

		$email_settings = $this->add('xepan\communication\Model_Communication_EmailSetting')
				->load($m['email_send_from']);

		$mail = $this->add('xepan\communication\Model_Communication_Email_Sent');

		$email_subject = $m['email_subject'];
		$email_body = $m['email_body'];
			
		$temp = $this->add('GiTemplate');
		$temp->loadTemplateFromString($email_body);

		$subject_temp = $this->add('GiTemplate');
		$subject_temp->loadTemplateFromString($email_subject);
		$subject_v=$this->add('View',null,null,$subject_temp);
		$subject_v->template->trySet($data_array);

		$temp = $this->add('GiTemplate');
		$temp->loadTemplateFromString($email_body);
		$body_v=$this->add('View',null,null,$temp);
		$body_v->template->trySet($data_array);					

		$mail->setfrom($email_settings['from_email'],$email_settings['from_name']);

		$email_array = explode(",",$m['email_send_to_custom_email_ids']);

		if($m['email_send_to_related_contact'] && $m['related_contact_field']){
			$temp = $this->ref($m['related_contact_field'])->getEmails();
			$email_array = array_merge($email_array,$temp);
			$mail['related_contact_id'] = $this[$m['related_contact_field']];
		}

		$email_array = array_unique($email_array);

		$total_email_id = 0;
		foreach ($email_array as $email_id) {
			if(!trim($email_id)) continue;
			$mail->addTo($email_id);
			$total_email_id += 1;
		}

		if(!$total_email_id) return;

		$mail['status'] = "Outbox";
		$mail->save();

		if($m['send_document_as_attachment'] && $this->hasMethod('generatePDF')){
			$file =	$this->add('xepan/filestore/Model_File',array('policy_add_new_type'=>true,'import_mode'=>'string','import_source'=>$this->generatePDF('return')));
			$file['filestore_volume_id'] = $file->getAvailableVolumeID();
			$file['original_filename'] =  strtolower($this['type']).'_'.$this['document_no_number'].'_'.$this->id.'.pdf';
			$file->save();
			$mail->addAttachment($file->id);
		}

		$mail->setSubject($subject_v->getHtml());
		$mail->setBody($body_v->getHtml());
		$mail->send($email_settings);
	}

	function shootSMSStatusAction($m){

		$sms_setting = $this->add('xepan\communication\Model_Communication_SMSSetting')->load($m['sms_send_from']);

		$temp = $this->add('GiTemplate');
		$temp->loadTemplateFromString(trim($m['sms_content']));
		$msg = $this->add('View',null,null,$temp);
		$msg->template->trySet($this->data);

		$phone_array = explode(",",$m['sms_send_to_custom_mobile_no']);

		$sms_commu = $this->add('xepan\communication\Model_Communication_SMS');
		$sms_commu->setBody($msg->getHtml());

		if($m['sms_send_to_related_contact'] && $m['related_contact_field']){
			$temp = $this->ref($m['related_contact_field'])->getPhones();
			$phone_array = array_merge($phone_array,$temp);

			$sms_commu['related_contact_id'] = $this[$m['related_contact_field']];
		}

		$phone_array = array_unique($phone_array);

		$total_phone_no = 0;
		foreach ($phone_array as $number) {
			if(!$number) continue;
			$sms_commu->addTo($number);

			$total_phone_no += 1;
		}

		if(!$total_phone_no) return;
		$sms_commu->send($sms_setting);
	}

	function DeleteAttachements(){
		foreach($this->ref('Attachments') as $a){
			$a->delete();
		}
	}

	function convertNumberToWords($number){
		$words = array(
				'0' => '', 
				'1' => 'one', 
				'2' => 'two',
		    	'3' => 'three', 
		    	'4' => 'four', 
		    	'5' => 'five', 
		    	'6' => 'six',
			    '7' => 'seven', 
			    '8' => 'eight', 
			    '9' => 'nine',
			    '10' => 'ten', 
			    '11' => 'eleven', 
			    '12' => 'twelve',
			    '13' => 'thirteen', 
			    '14' => 'fourteen',
			    '15' => 'fifteen', 
			    '16' => 'sixteen', 
			    '17' => 'seventeen',
			    '18' => 'eighteen', 
			    '19' => 'nineteen', 
			    '20' => 'twenty',
			    '30' => 'thirty', 
			    '40' => 'forty', 
			    '50' => 'fifty',
			    '60' => 'sixty', 
			    '70' => 'seventy',
			    '80' => 'eighty', 
			    '90' => 'ninety'
			);
		$digits = array('', 'hundred', 'thousand', 'lakh', 'crore');
		$amount = $number;
		$no = round($number);
		$point = round($number - $no, 2) * 100;
		$hundred = null;
		$digits_1 = strlen($no);
		$i = 0;
		$str = array();
		while ($i < $digits_1) {
			$divider = ($i == 2) ? 10 : 100;
		 	$number = floor($no % $divider);
		 	$no = floor($no / $divider);
		 	$i += ($divider == 10) ? 1 : 2;
		 	if ($number) {
		    	$plural = (($counter = count($str)) && $number > 9) ? '' : null;
		    	$hundred = ($counter == 1 && $str[0]) ? ' ' : null;
		    	$str [] = ($number < 21) ? $words[$number] .
			        " " . $digits[$counter] . $plural . " " . $hundred
			        :
			        $words[floor($number / 10) * 10]
			        . " " . $words[$number % 10] . " "
			        . $digits[$counter] . $plural . " " . $hundred;
			} else $str[] = null;
		}
		$str = array_reverse($str);
		return $result = implode('', $str);
	}

	function amountInWords($number, $currency_id=null){
		$currency_model = $this->app->epan->default_currency;
		if($currency_id)
			$currency_model = $this->add('xepan\accounts\Model_Currency')->load($currency_id);
		
		$integer_part = isset($currency_model['integer_part'])?$currency_model['integer_part']:"";
		$fractional_part = isset($currency_model['fractional_part'])?$currency_model['fractional_part']:"";	    
	    $prefix = isset($currency_model['prefix'])?$currency_model['prefix']:"";	    
		$postfix = isset($currency_model['postfix'])?$currency_model['postfix']:"";	    

		$integer_number = 0;			
		if (strpos($number, '.') !== false) {
	       list($integer_number, $fraction) = explode('.', $number);
		}

		//integer part
		$result = $this->convertNumberToWords($integer_number);
		$amount_in_words = ($result?$result:" zero ")." ".$integer_part;

		// fractional part 
		if(isset($fraction) and $fraction > 0){
			$result = $this->convertNumberToWords($fraction);
			$amount_in_words .= " and ".$result." ".$fractional_part;
		}

		return ucwords($amount_in_words." ".$postfix);
	}

	function amountInWordsUK($number,$currency_id=null) {
	    $currency_model = $this->app->epan->default_currency;
		if($currency_id)
			$currency_model = $this->add('xepan\accounts\Model_Currency')->load($currency_id);
		
		$integer_part = isset($currency_model['integer_part'])?$currency_model['integer_part']:"";
		$fractional_part = isset($currency_model['fractional_part'])?$currency_model['fractional_part']:"";	    
	    $prefix = isset($currency_model['prefix'])?$currency_model['prefix']:"";	    
		$postfix = isset($currency_model['postfix'])?$currency_model['postfix']:"";	    
	   
	    $hyphen      = '-';
	    $conjunction = ' ';
	    // $conjunction = ' and ';
	    $separator   = ' ';
	    $negative    = 'negative ';
	    $decimal     = ' and ';
	    $dictionary  = array(
	        0                   => 'Zero',
	        1                   => 'One',
	        2                   => 'Two',
	        3                   => 'Three',
	        4                   => 'Four',
	        5                   => 'Five',
	        6                   => 'Six',
	        7                   => 'Seven',
	        8                   => 'Eight',
	        9                   => 'Nine',
	        10                  => 'Ten',
	        11                  => 'Eleven',
	        12                  => 'Twelve',
	        13                  => 'Thirteen',
	        14                  => 'Fourteen',
	        15                  => 'Fifteen',
	        16                  => 'Sixteen',
	        17                  => 'Seventeen',
	        18                  => 'Eighteen',
	        19                  => 'Nineteen',
	        20                  => 'Twenty',
	        30                  => 'Thirty',
	        40                  => 'Forty',
	        50                  => 'Fifty',
	        60                  => 'Sixty',
	        70                  => 'Seventy',
	        80                  => 'Eighty',
	        90                  => 'Ninety',
	        100                 => 'Hundred',
	        1000                => 'Thousand',
	        1000000             => 'Million',
	        1000000000          => 'Billion',
	        1000000000000       => 'Trillion',
	        1000000000000000    => 'Quadrillion',
	        1000000000000000000 => 'Quintillion'
	    );

	    if (!is_numeric($number)) {
	        return false;
	    }

	    if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
	        // overflow
	        trigger_error(
	            'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
	            E_USER_WARNING
	        );
	        return false;
	    }

	    if ($number < 0) {
	        return $negative . $this->amountInWords(abs($number));
	    }

	    $string = $fraction = null;

	    if (strpos($number, '.') !== false) {
	        list($number, $fraction) = explode('.', $number);
	    }

	    switch (true) {
	        case $number < 21:
	            $string = $dictionary[$number];
	            break;
	        case $number < 100:
	            $tens   = ((int) ($number / 10)) * 10;
	            $units  = $number % 10;
	            $string = $dictionary[$tens];
	            if ($units) {
	                $string .= $hyphen . $dictionary[$units];
	            }
	            break;
	        case $number < 1000:
	            $hundreds  = $number / 100;
	            $remainder = $number % 100;
	            $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
	            if ($remainder) {
	                $string .= $conjunction .$this->amountInWords($remainder);
	            }
	            break;
	        default:
	            $baseUnit = pow(1000, floor(log($number, 1000)));
	            $numBaseUnits = (int) ($number / $baseUnit);
	            $remainder = $number % $baseUnit;
	            $string = $this->amountInWords($numBaseUnits) . ' ' . $dictionary[$baseUnit];
	            if ($remainder) {
	                $string .= $remainder < 100 ? $conjunction : $separator;
	                $string .= $this->amountInWords($remainder);
	            }
	            break;
	    }

	    

	    if (null !== $fraction && is_numeric($fraction)) {
	    	// concating integer part
	    	$string .= " ".$integer_part;

		    if($prefix)
		    	$string = $prefix . " " . $string;

	        if($fraction > 0){
		        $string .= $decimal;
		        $words = array();
		        foreach (str_split((string) $fraction) as $number) {
		            $words[] = $dictionary[$number];
		        }
		        $string .= implode(' ', $words);

		        // concating fractional part
		        $string .= " ".$fractional_part;

		        if($postfix)	
			    	$string .= " ".$postfix;
	        }else{
	        	if($postfix)	
			    	$string .= " ".$postfix;
	        }
	    }


	    return $string;
	}

	function page_other_info($page){
		$other_fields_model = $page->add('xepan\base\Model_Config_DocumentOtherInfo');
		$other_fields_model->addCondition('for',$this['type']);

		$has_field = false; // used for: when no one other info is added so display error
		$form = $page->add('Form');

		foreach ($other_fields_model as $m) {
			if(!$m['name']) continue;
			$field = $form->addField($m['type'],$m['name']);
			if($m['type']=='DropDown') $field->setValueList(array_combine(explode(",", $m['possible_values']), explode(",", $m['possible_values'])));

			$has_field = true;

			$existing = $this->add('xepan\base\Model_Document_Other')
				->addCondition('document_id',$this->id)
				->addCondition('head',$m['name'])
				->tryLoadAny();
			$field->set($existing['value']);

			if($m['conditional_binding']){
				$field->js(true)->univ()->bindConditionalShow(json_decode($m['conditional_binding'],true),'div.atk-form-row');
			}

			if($m['is_mandatory']){
				$field->validate('required');
			}
		}

		if($has_field)
			$form->addSubmit('Save')->addClass('btn btn-primary');
		else
			$page->add('View_Error')->set('Please configure other info related to '.$this->document_type);

		if($form->isSubmitted()){
			foreach ($other_fields_model as $m) {
				if(!$m['name']) continue;

				$existing = $this->add('xepan\base\Model_Document_Other')
					->addCondition('document_id',$this->id)
					->addCondition('head',$m['name'])
					->tryLoadAny();
				$existing['value'] = $form[$m['name']];
				$existing->save();
			}

			return $page->js()->univ()->closeDialog();

		}
	}

	function getDocumentOtherInfo($document_type,$document_id=null){
		$info = [];
		if(!$document_type) return $info;
		
		$other_fields_model = $this->add('xepan\base\Model_Config_DocumentOtherInfo');
		$other_fields_model->addCondition('for',$document_type);

		foreach ($other_fields_model as $m) {
			if(!$m['name']) continue;

			$info[$m['name']] = [
									'type'=>$m['type'],
									'possible_values'=>$m['possible_values'],
									'is_mandatory'=>$m['is_mandatory'],
									'conditional_binding'=>$m['conditional_binding'],
									'value'=>null,
									'name'=>$m['name']
								];
			if($document_id){
				$existing = $this->add('xepan\base\Model_Document_Other')
					->addCondition('document_id',$document_id)
					->addCondition('head',$m['name'])
					->tryLoadAny();
				
				$info[$m['name']]['value'] = $existing['value'];
			}
		}

		return $info;
	}
}
