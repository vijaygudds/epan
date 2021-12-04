<?php

namespace xepan\base;

class Tool_UserPanel extends \xepan\cms\View_Tool{
	public $reload_object;
	public $options = [
				'show_tnc'=>true,
				'tnc_page_url'=>'',
				'layout'=>'login_view',
				'login_success_url'=>'index',
				'registration_success_url'=>null,
				'logout_page'=>'logout',
				'login_page'=>'login',
				'member_panel_page'=>'',
				'registration_page_extranal_url'=>null,
				'show_micro_login'=>false,
				'show_footer'=>true,
				'show_login_link'=>true,

				// THESE OPTIONS ARE NOT YET IMPLEMENTED, TIME TO JUMP ON TO FRONTEND
				'show_forgotpassword_link'=>true,
				'show_registration_link'=>true,
				'show_activation_link'=>true,
				'show_verification_link'=>true,
				'show_resendverification_link'=>true,
				'redirect_to_success_page_if_logged_in'=>false,

				// field to show
				'show_field_country'=>0,
				'country_is_mandatory'=>0,
				'show_field_state'=>0,
				'state_is_mandatory'=>0,
				'show_field_city'=>0,
				'city_is_mandatory'=>0,
				'show_field_address'=>0,
				'address_is_mandatory'=>0,
				'show_field_pin_code'=>0,
				'pin_code_is_mandatory'=>0,
				'show_field_mobile_no'=>0,
				'mobile_no_is_mandatory'=>0,
				'registration_mode'=>'email',
				'username_validation_regular_expression'=>'',
				// TO IMPLEMENT, DELETE WRAPPER SPOTS IN FORM TEMPLATES OF RESPECTIVE VIEWS 
				'verify_message'=>'Your account is validated. Login with your username and password to enjoy our services.',
				'registration_message'=>'Registration mail sent. Check your email address linked to the account.',
				'forgot_message'=>'We have sent you a password recovery mail. Check your e-mail address linked to the account.',
				'reactive_message'=>'Verification mail sent. Check your e-mail address linked to the account.'
			];	
	public $active_view=null;

	function init(){
		parent::init();

		if(!in_array($this->options['layout'], ['login_view','forget_password','new_registration','micro_login','verify_again','verify_account','reset_form'])){
			$this->add('View_Error')->set('View ('.$this->options['layout'].') Not Found');
			return;
		}

		$layout = $this->app->stickyGET('layout');
		if($layout){
			$this->options['layout']=$layout;
		}
		
		$view_url = $this->api->url(null,['cut_object'=>$this->name]);
		
		if($this->options['registration_page_extranal_url']){
			$this->on('click','a.xepan-registration-load-panl',function($js,$data)use($view_url){
				if($this->app->page == $this->options['registration_page_extranal_url'])
					return $this->js()->reload(['layout'=>$data['showview']],null,$view_url);
				return $this->app->redirect($this->api->url($this->options['registration_page_extranal_url']))->execute();
			});

		}else{
			$this->on('click','a.xepan-registration-load-panl',function($js,$data)use($view_url){
				return $this->js()->reload(['layout'=>$data['showview']],null,$view_url);
			});
		}
		$this->on('click','a.xepan-login-panl-loadview',function($js,$data)use($view_url){
				return $this->js()->reload(['layout'=>$data['showview']],null,$view_url);
			});

		if($this->options['show_micro_login']){
			$this->active_view = $ml_view=$this->add('xepan\base\View_User_MicroLogin',array('options'=>$this->options));
			$this->app->stickyForget('options');	
			return;
		}

		if(!$this->app->auth->isLoggedIn() OR $this->options['layout'] == "reset_form"){
			
			switch ($this->options['layout']) {
				case 'login_view':
					$this->active_view = $user_login=$this->add('xepan\base\View_User_LoginPanel',array('options'=>$this->options,'reload_object'=>$this->reload_object));
					$this->app->stickyForget('layout');
				break;

				case 'forget_password':
					$this->active_view = $f_view=$this->add('xepan\base\View_User_ForgotPassword',array('options'=>$this->options));
					$this->app->stickyForget('options');
				break;

				case 'new_registration':
					$this->active_view = $r_view=$this->add('xepan\base\View_User_Registration',array('options'=>$this->options));
					$this->app->stickyForget('options');
				break;

				case 'verify_account':
					$this->active_view = $v_view=$this->add('xepan\base\View_User_VerifyAccount',array('options'=>$this->options));
					$this->app->stickyForget('options');
				break;

				case 'verify_again':
					$this->active_view = $va_view=$this->add('xepan\base\View_User_VerifyAgain',array('options'=>$this->options));
					$this->app->stickyForget('options');
				break;

				case 'reset_form':
					$this->active_view = $va_view=$this->add('xepan\base\View_User_ResetPassword',array('options'=>$this->options));
					$this->app->stickyForget('options');
				break;

				case 'micro_login':	
								
				break;

				default:
					$this->add('View_Error')->set('View Not Found .....specify data-showview attr');	
			}
			
		}else{
			// $this->js()->univ()->loaction($this->api->url($this->options['redirect_url']));
			if($this->options['layout'] == "micro_login")
				$this->active_view = $this->add('xepan\base\View_User_MicroLogin',array('options'=>$this->options));
			else
				$this->active_view = $this->add('xepan\base\View_User_AlreadyLoggedin',array('options'=>$this->options));
		}
	}

	function getTemplate(){
		return $this->active_view->template;
	}

	function getTemplateFile(){
		return $this->active_view->template->origin_filename;
	}

}