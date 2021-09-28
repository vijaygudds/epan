<?php
namespace xepan\base;
class Model_AuditLog extends Model_Table {
	public $table= "xepan_auditlog";
	
	function init(){
		parent::init();

		$this->hasOne('xepan\base\User','user_id')->defaultValue(@$this->api->auth->model->id);
		$this->hasOne('xepan\base\Contact','contact_id')->defaultValue(@$this->api->employee->id);
		
		$this->addField('model_class');
		$this->addField('pk_id')->type('int')->caption('Record Id');

		$this->addField('created_at')->type('datetime')->defaultValue(date('Y-m-d H:i:s'))->sortable(true);

		$this->addField('name')->type('text');
		$this->addField('type');

		$this->add('misc/Field_Callback','changes')->set(function($m){
			$name = json_decode($m['name'],true);
			if(isset($name['extra_info'])) unset($name['extra_info']);

			$str = '';
		    foreach($name as $field=>$change_array) {
		    	$str .= $field.": ".json_encode($change_array)."<br/>";
		        // $str .= $change_array['from'].' => '.$change_array['to'].'<br/>';
		    }
		    return $str;
		})->allowHtml(true);

		$this->add('misc/Field_Callback','extra_info')->set(function($m){
			$name = json_decode($m['name'],true);
			if(!isset($name['extra_info'])) return " ";

			$str = '';
		    foreach($name['extra_info'] as $key=>$item) {
		        $str .= $key.': '.$item.'<br/>';
		    }
		    return $str;
		})->allowHtml(true);
		// $this->add('dynamic_model/Controller_AutoCreator');
	}

	function logFieldEdit($model,$record_id,$edit_what_field,$old_value,$new_value){

	}
}