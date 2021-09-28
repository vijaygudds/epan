<?php
namespace xepan\base;

class View_User_VerifyAccount extends \View{
	public $options = [];
	function init(){
		parent::init();

		$secret_code=$this->app->stickyGET('secret_code');
		$activate_email=$this->app->stickyGET('activate_email');
		
		$form=$this->add('Form',null,null,['form/empty']);
		$form->setLayout('view/tool/userpanel/form/xepanverify');
		$form->addField('line','email','User name')->set($activate_email);	
		$form->addField('line','activation_code')->set($secret_code);
		
		if($message = $this->app->stickyGET('message')){
   	 		$form->layout->template->trySetHtml('message',$message);
        }else{
        	$form->layout->template->tryDel('message_wrapper');
        }

		$form->onSubmit(function($f){

			$user = $this->add('xepan\base\Model_User');	
			$user->addCondition('username',$f['email']);
			$user->tryLoadAny();
			
			if(!$user->loaded())
				$f->displayError('email','username is not registered');

			$frontend_config_m = $this->add('xepan\base\Model_ConfigJsonModel',
				[
					'fields'=>[
								'user_registration_type'=>'DropDown',
								'reset_subject'=>'Line',
								'reset_body'=>'xepan\base\RichText',
								'reset_sms_content'=>'Text',
								'update_subject'=>'Line',
								'update_body'=>'xepan\base\RichText',
								'update_sms_content'=>'Text',
								'registration_subject'=>'Line',
								'registration_body'=>'xepan\base\RichText',
								'registration_sms_content'=>'Text',
								'verification_subject'=>'Line',
								'verification_body'=>'xepan\base\RichText',
								'verification_sms_content'=>'Text',
								'subscription_subject'=>'Line',
								'subscription_body'=>'xepan\base\RichText',
						],
					'config_key'=>'FRONTEND_LOGIN_RELATED_EMAIL',
					'application'=>'communication'
				]);

			$frontend_config_m->tryLoadAny();

			$contact = $user->ref('Contacts')->tryLoadAny();
			
			$merge_model_array = [];
			$merge_model_array = array_merge($merge_model_array,$user->get());
			$merge_model_array = array_merge($merge_model_array,$contact->get());		
			
			if($f['activation_code']!=$user['hash'])
				$f->displayError('activation_code','Activation code did not match');


			if($this->options['registration_mode'] === "email"){

				$email_settings = $this->add('xepan\communication\Model_Communication_DefaultEmailSetting')->tryLoadAny();
				$mail = $this->add('xepan\communication\Model_Communication_Email');

				$email_subject = $frontend_config_m['verification_subject'];
				$email_body = $frontend_config_m['verification_body'];

				$temp=$this->add('GiTemplate');
				$temp->loadTemplateFromString($email_body);

				$subject_temp=$this->add('GiTemplate');
				$subject_temp->loadTemplateFromString($email_subject);
				$subject_v=$this->add('View',null,null,$subject_temp);
				$subject_v->template->trySet($merge_model_array);

				$body_v=$this->add('View',null,null,$temp);
				$body_v->template->trySet($merge_model_array);					

				$mail->setfrom($email_settings['from_email'],$email_settings['from_name']);
				$mail->addTo($f['email']);
				$mail->setSubject($subject_v->getHtml());
				$mail->setBody($body_v->getHtml());
				$mail->send($email_settings);
			}

			if($this->options['registration_mode'] === "sms"){
				if($message = $frontend_config_m['verification_sms_content']){
					$temp = $this->add('GiTemplate');
					$temp->loadTemplateFromString($message);
					$msg = $this->add('View',null,null,$temp);
					$msg->template->trySet($merge_model_array);
					$this->add('xepan\communication\Controller_Sms')->sendMessage($user['username'],$msg->getHtml());
				}
			}
			
			$user['status'] = 'Active';
			$user->save();
			return $f->js(null,$f->js()->redirect($this->app->url('login',['layout'=>'login_view','message'=>$this->options['verify_message']])));
		});
	}			
}			