<?php

/**
* description: ATK Page
* 
*/

namespace xepan\cms;

class page_componentcreator extends \xepan\base\Page {
	
	public $allow_frontend = true;
	function init(){
		parent::init();
		
		$move_to = $_POST['move_to'];
		$template = $_POST['template'];
		$move_html = $_POST['move_html'];

		$www_absolute = getcwd().'/websites/'.$this->app->current_website_name.'/www/';
		$www_relative = './websites/'.$this->app->current_website_name.'/www/';

		$template_file = $www_relative.$template.".html";

		$orig_html = file_get_contents($template_file);

		$pq = new phpQuery();
		$dom = $pq->newDocument($orig_html);

		if($move_to == "header")
			$pq->pq($move_html)->insertBefore('.xepan-page-wrapper');
		else
			$pq->pq($move_html)->insertAfter('.xepan-page-wrapper');

		file_put_contents($template_file, $dom->html());
		
		echo $this->js()->univ()->successMessage('Moved');
		exit();
	}
}
