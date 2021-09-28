<?php
namespace xepan\base;
class View_User_ForgotPassword extends \View{
	public $options = [];
	function init(){
		parent::init();

		$form=$this->add('Form');
		$form->setLayout('view/tool/userpanel/form/xepanforgotpassword');

		if($this->options['registration_mode'] === "sms"){
			$form->layout->template->tryDel('email_wrapper');
			$email_field = $form->addField('line','email','Mobile No')->validate('required');
			$form->layout->template->trySetHtml('sub_heading','<p>Please enter your mobile number to get otp.</p>');
		}else{
			$form->layout->template->tryDel('sms_wrapper');
			$email_field = $form->addField('line','email')->validate('required');
			$form->layout->template->trySetHtml('sub_heading','<p>Please enter your email address to get reset password link.</p>');
		}

		if($form->isSubmitted()){
			$user=$this->add('xepan\base\Model_User');
			$user->addCondition('username',$form['email']);
			$user->tryLoadAny();
			
			if(!$user->loaded()){
				$form->displayError('email','This Username is not registered');
			}else{
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

				$user['hash']=rand(9999,100000);
				$user->update();

				$contact = $user->ref('Contacts')->tryLoadAny();
				$merge_model_array=[];
				$merge_model_array = array_merge($merge_model_array,$user->get());
				$merge_model_array = array_merge($merge_model_array,$contact->get());
				
				if($this->options['registration_mode'] === "email"){
					$email_settings = $this->add('xepan\communication\Model_Communication_DefaultEmailSetting')->tryLoadAny();
					$mail = $this->add('xepan\communication\Model_Communication_Email');
					
					$email_subject = $frontend_config_m['reset_subject'];
					$email_body = $frontend_config_m['reset_body'];

					
					$url=$this->api->url(null,
											[
											'secret_code'=>$user['hash'],
											'activate_email'=>$form['email'],
											'layout'=>'reset_form'
											]
										)->useAbsoluteURL();

					$tag_url="<a href=\"".$url."\">Click Here to Activate </a>"	;
				
					$subject_temp=$this->add('GiTemplate');
					$subject_temp->loadTemplateFromString($email_subject);
					$subject_v=$this->add('View',null,null,$subject_temp);
					$subject_v->template->trySet($merge_model_array);

					$temp=$this->add('GiTemplate');
					$temp->loadTemplateFromString($email_body);
					$body_v=$this->add('View',null,null,$temp);
					$body_v->template->trySet($merge_model_array);					
					$t=$body_v->template->trySetHTML('click_here',$tag_url);		
					$t=$body_v->template->trySetHTML('url',$url);	

					$mail->setfrom($email_settings['from_email'],$email_settings['from_name']);
					$mail->addTo($form['email']);
					$mail->setSubject($subject_v->getHtml());
					$mail->setBody($body_v->getHtml());
					$mail->send($email_settings);
				}

				if($this->options['registration_mode'] === "sms"){
					if($message = $frontend_config_m['reset_sms_content']){
						$temp = $this->add('GiTemplate');
						$temp->loadTemplateFromString($message);
						$msg = $this->add('View',null,null,$temp);
						$msg->template->trySet($merge_model_array);
						$this->add('xepan\communication\Controller_Sms')->sendMessage($user['username'],$msg->getHtml());
					}
				}

				return $form->js(null)->redirect($this->app->url(null,['layout'=>'reset_form', 'message'=>$this->options['forgot_message']]))->execute();
			}
		}
	}
}