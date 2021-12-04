<?php

namespace xepan\webwidgets;

class page_webwidgets extends \xepan\base\Page{
	public $title = "Web Widgets";

	function init(){
		parent::init();				

		$files = scandir("vendor/xepan/webwidgets/lib/Tool");
		
		$files_array = [];
		foreach($files as $file){
			if($file == '.' or $file == '..')
				continue;
			$files_array[] = ['name'=>strstr($file, '.', true)];
		}
		

		asort($files_array);
		$grid = $this->add('Grid');
		$grid->setSource($files_array);
		$grid->addColumn('name');
		$grid->removeColumn('id');		
	}
}