<?php
namespace xepan\base;

class View_User_AlreadyLoggedin extends \View{
	public $options = [];

	function init(){
		parent::init();

		$auth = $this->app->auth;
		$auth->login($this->app->auth->model['username']);
		$this->app->hook('login_panel_user_loggedin',[$auth->model]);
		
		if(!$this->app->isEditing && $this->options['redirect_to_success_page_if_logged_in'] && $this->options['login_success_url'] && $this->app->page != $this->options['login_success_url']){
			$this->app->redirect($this->app->url($this->options['login_success_url']));
		}
		
	}
	
	function defaultTemplate(){
		return ['view/tool/userpanel/form/alreadyloggedin'];
	}
}	