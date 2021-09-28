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

class page_admin_save_page extends \Page {

	function init(){
		parent::init();
		
		if(!$this->api->auth->isLoggedIn())	{
			$this->js()->univ()->errorMessage('You Are Not Logged In')->execute();
		}

		if ( $_POST['html_length'] != strlen( $_POST['body_html'] ) ) {
			$this->js()->univ()->errorMessage( 'Template Data Length send ' . $_POST['html_length'] . " AND Length calculated again is " . strlen( $_POST['body_html'] ) )->execute();
		}

		if ( $_POST['page_length'] != strlen( $_POST['page_html'] ) ) {
			$this->js()->univ()->errorMessage( 'Page Data Length send ' . $_POST['page_length'] . " AND Length calculated again is " . strlen( $_POST['body_html'] ) )->execute();
		}

		if ( $_POST['html_crc32'] != sprintf("%u",crc32( $_POST['body_html'] ) )) {
			$this->js()->univ()->errorsMessage( 'CRC send ' . $_POST['html_crc32'] . " AND CRC calculated again is " . sprintf("%u",crc32( $_POST['body_html'] )) )->execute();
		}

		if ( $_POST['page_crc32'] != sprintf("%u",crc32( $_POST['page_html'] ) )) {
			$this->js()->univ()->errorsMessage( 'CRC send ' . $_POST['page_crc32'] . " AND CRC calculated again is " . sprintf("%u",crc32( $_POST['page_html'] )) )->execute();
		}


		if(strpos($_POST['file_path'], realpath('websites/'.$this->app->current_website_name)!==0)){
			$this->js()->univ()->errorMessage('You cannot save in this location')->execute();
		}

		if(strpos($_POST['template_file_path'], realpath('websites/'.$this->app->current_website_name)!==0)){
			$this->js()->univ()->errorMessage('You cannot save in this location')->execute();
		}

		$html_content = urldecode( trim( $_POST['body_html'] ) );
		$page_content = urldecode( trim( $_POST['page_html'] ) );
		$body_attributes = urldecode($_POST['body_attributes']);

		// convert all absolute url to relative
		$www_domain = $this->app->pm->base_url.$this->app->pm->base_path.'websites/'.$this->app->current_website_name.'/www/';
		$html_content = str_replace($www_domain, '', $html_content);
		$page_content = str_replace($www_domain, '', $page_content);
		$body_attributes = str_replace($www_domain, '', $body_attributes);

		// $assets_domain = $this->app->pm->base_url.$this->app->pm->base_path.'websites/'.$this->app->current_website_name.'/assets/';
		// $html_content = str_replace($assets_domain, '', $html_content);
		// $page_content = str_replace($assets_domain, '', $page_content);
		// $body_attributes = str_replace($assets_domain, '', $body_attributes);

		$www_domain = 'websites/'.$this->app->current_website_name.'/www/';
		$html_content = str_replace($www_domain, '', $html_content);
		$page_content = str_replace($www_domain, '', $page_content);
		$body_attributes = str_replace($www_domain, '', $body_attributes);

		// $assets_domain = 'websites/'.$this->app->current_website_name.'/assets/';
		// $html_content = str_replace($assets_domain, '', $html_content);
		// $page_content = str_replace($assets_domain, '', $page_content);
		// $body_attributes = str_replace($assets_domain, '', $body_attributes);

		// add {$Content} tag for template content saved
		$this->pq = $pq = new phpQuery();
		$old_dom = $pq->newDocument(file_get_contents($_POST['template_file_path']));
		foreach ($old_dom['body'] as $one_body) {
			// replace body with coming content 
			$d=$pq->pq($one_body);
			$d->attr('style',$body_attributes);
			$d->html($html_content);	
		}

		$html_content = $old_dom->html();
		
		if($_POST['take_snapshot'] !=='N'){
			

			if($_POST['is_template'] !== 'true'){
				// page + template

				// save page first
				$snap = $this->add('xepan\cms\Model_Snapshots');
				$snap['content']= $page_content;
				$snap['page_url']= $_POST['file_path'];
				$snap['page_id']= $_POST['webpage_id'];
				$snap['created_by_id']=$this->add('xepan\base\Model_Contact')->loadLoggedIn(null,true)->get('id');
				$snap['name']=$_POST['take_snapshot'];
				$snap->save();

				// check if last template snapshot is not same .. do not save unnesessory 
				$snap = $this->add('xepan\cms\Model_Snapshots');
				$snap->addCondition('page_url',$_POST['template_file_path']);
				$snap->addCondition('page_id',$_POST['webtemplate_id']);
				$snap->addCondition('content',$html_content);
				$snap->tryLoadAny();
				if(!$snap->loaded()){
					$snap['created_by_id']=$this->add('xepan\base\Model_Contact')->loadLoggedIn(null,true)->get('id');
					$snap['name']=$_POST['take_snapshot'];
					$snap->save();
				}
			}else{
				// forced to snapshot while template editing mode
				$snap = $this->add('xepan\cms\Model_Snapshots');
				$snap['content']= $html_content;
				$snap['page_url']= $_POST['template_file_path'];
				$snap['page_id']=$_POST['webtemplate_id'];
				$snap['created_by_id']=$this->add('xepan\base\Model_Contact')->loadLoggedIn(null,true)->get('id');
				$snap['name']=$_POST['take_snapshot'];
				$snap->save();
			}
		}

		try{
			if(trim($page_content)) file_put_contents($_POST['file_path'], $page_content);
			if(trim($html_content)) file_put_contents($_POST['template_file_path'], $html_content);
			$this->js()->_selectorDocument()->univ()->successMessage("Content Saved")->execute();
		}catch(\Exception $e){
			$this->js()->_selectorDocument()->univ()->errorMessage($e->getMessage())->execute();
		}

	}
}
