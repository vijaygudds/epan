<?php

namespace xepan\base;

class page_logout extends \xepan\base\Page{
	public $title = "Logout Page";
	function init(){
		parent::init();

		$this->app->hook('logout_page',[$this]);
						
		$this->app->hook('user_loggedout',[$this->app->auth->model]);
		$this->app->auth->logout();
		$this->app->redirect('/');
		
	}
}