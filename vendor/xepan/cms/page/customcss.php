<?php

/**
* description: ATK Page
* 
* @author : Gowrav Vishwakarma
* @email : gowravvishwakarma@gmail.com, info@xavoc.com
* @website : http://xepan.org
* 
*/

namespace xepan\cms;


class page_customcss extends \Page {
	
	public $title='Custom CSS';

	function init(){
		parent::init();

		if(!$this->app->auth->isLoggedIn()) return;
		
		// get content of mystyle.css located at /websites/www/assets/css/mystyle.css
		//if not created then create it

		$path = $this->api->pathfinder->base_location->base_path.'/websites/'.$this->app->current_website_name."/www/css";
		// check for css folder is exist or not
		if(!file_exists($path)){
			$folder = \Nette\Utils\FileSystem::createDir($path);
  		}  
  		//  check for css file
  		$path .= "/mystyle.css";
  		$mystyle = "";

  		if(!file_exists($path)){
  			$file = \Nette\Utils\FileSystem::write($path,$mystyle);
  		}else{
  			$mystyle = \Nette\Utils\FileSystem::read($path);
  		}

		$form = $this->add('Form',null,null,['form/stacked']);
		$form->addClass('xepan-editor-customcss-form');
		$code_editor = $form->addField('xepan\base\CodeEditor','custom_css');
		$code_editor->lang = "css";
		$code_editor->worker = "css";
		$code_editor->setRows(25);
		$code_editor->set($mystyle);

		$form->addSubmit('update');
		if($form->isSubmitted()){
  			$file = file_put_contents($path,$form['custom_css']);
			$url = 'websites/'.$this->app->current_website_name.'/www/css/mystyle.css';
			$js_event = [
							$form->js()->_selector('#xepan-cms-custom-mystylecss')->attr('href',$url."?t=".str_replace(" ", "", str_replace("-", "", $this->app->now))),
							$form->js()->univ()->successMessage('Saved Sucessfully')
						];
			$form->js(null,$js_event)->reload()->execute();
		}
	}
}
