<?php

namespace xepan\cms;

class Page_editfrontlogin extends \xepan\base\Page{
	public $title = "CMS Editor login";
	function init(){
		parent::init();

		$cmseditors_mdl = $this->add('xepan\cms\Model_User_CMSEditor');
		$cmseditors_mdl->tryLoadBy('user_id',$this->app->auth->model->id);

		if(!$cmseditors_mdl->loaded()){
			$this->add('View_Error')->set('You are not permitted to edit website, update from CMS > CMS_Editors menu');
			return;
		}

		$epan = $this->app->epan;
		$token = md5(uniqid());
		$this->add('xepan\epanservices\Controller_RemoteEpan')
			->setEpan($epan)
			->do(function($app)use($token){
				$app->add('xepan\base\Model_User')
					->tryLoadAny()
					->set('access_token',$token)
					->set('access_token_expiry',date('Y-m-d H:i:s',strtotime($app->now.' +10 seconds')))
					->save();
			});

			$this->url = $url = "{$_SERVER['HTTP_HOST']}";        
	        $this->protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';
	        $this->domain = $domain = str_replace('www.','',$this->app->extract_domain($url))?:'www';
	        $this->sub_domain = $sub_domain = str_replace('www.','',$this->app->extract_subdomains($url))?:'www';
	    
	    $this->add('View')->set('A new window should have been opened, edit your site there');
		$this->js(true)->univ()->newWindow($this->app->url(str_replace('/admin/','',(string)$this->app->url('/')->absolute()),['access_token'=>$token]),'LiveEdit');
	}
}