<?php
namespace xepan\base;
class page_resetpassword extends \Page{
	public $title="Reset Password";
	function init(){
		parent::init();
		$secret_code=$this->app->stickyGET('secret_code');
		$activate_email=$this->app->stickyGET('activate_email');
		$user=$this->app->auth->model;	
		$user->addCondition('username',$activate_email);
		$user->tryLoadAny();
		
		$form=$this->add('Form');
		$form->setLayout('view/admin/user/form/restpassword');
		$form->addField('line','email')->set($_GET['activate_email'])->validate('required');
		$form->addField('line','secret_code','Reset Code')->set($_GET['secret_code'])->validate('required');
		$company_m = $this->add('xepan\base\Model_Config_CompanyInfo');
		// $company_m->add('xepan\hr\Controller_ACL');
		$company_m->tryLoadAny();

		$form->layout->template->trySet('company_name',$company_m['company_name']);
		$form->addField('password','password')->validate('required');
		$form->addField('password','retype_password')->validateNotNull();

		$form->onSubmit(function($f)use($user){
			if($f['secret_code']!=$user['hash']){
				$f->displayError('secret_code','Activation Code Not Match');
			}
			if($f['password']!= $f['retype_password']){
				$f->displayError($f->getElement('retype_password'),'Password Not Match');
			}
			$contact=$user->ref('Contacts')->tryLoadAny();
			$email_settings = $this->add('xepan\communication\Model_Communication_EmailSetting')->tryLoadAny();
			
			$mail = $this->add('xepan\communication\Model_Communication_Email');
			$merge_model_array=[];
			$merge_model_array = array_merge($merge_model_array,$user->get());
			$merge_model_array = array_merge($merge_model_array,$contact->get());
			
			// $reg_model=$this->app->epan->config;
			// $email_subject=$reg_model->getConfig('UPDATE_PASSWORD_SUBJECT_FOR_ADMIN');
			// $email_body=$reg_model->getConfig('UPDATE_PASSWORD_BODY_FOR_ADMIN');
			// $email_body=str_replace("{{name}}",$employee['name'],$email_body);
			
			$config_m = $this->add('xepan\base\Model_ConfigJsonModel',
		        [
		            'fields'=>[
		                        'reset_subject'=>'Line',
		                        'reset_body'=>'xepan\base\RichText',
		                        'update_subject'=>'Line',
		                        'update_body'=>'xepan\base\RichText',
		                        ],
		                'config_key'=>'ADMIN_LOGIN_RELATED_EMAIL',
		                'application'=>'communication'
		        ]);
	        $config_m->tryLoadAny();

			$email_subject=$config_m['update_subject'];
			$email_body=$config_m['update_body'];

			$subject_temp=$this->add('GiTemplate');
			$subject_temp->loadTemplateFromString($email_subject);
			
			$subject_v=$this->add('View',null,null,$subject_temp);
			$subject_v->template->trySet($merge_model_array);
			
			$temp=$this->add('GiTemplate');
			$temp->loadTemplateFromString($email_body);
			$body_v=$this->add('View',null,null,$temp);
			$body_v->template->trySet($merge_model_array);
			
			$mail->setfrom($email_settings['from_email'],$email_settings['from_name']);
			$mail->addTo($f['email']);
			$mail->setSubject($subject_v->getHtml());
			$mail->setBody($body_v->getHtml());
			$mail->send($email_settings);
			
			$user['password']=$f['password'];
			$user->save();
			
			// $this->app->auth->model['password']=$f['new_password'];
			// $this->app->auth->model->save();

			
			return $f->js()->univ()->location('index.php');
		});

	}
}