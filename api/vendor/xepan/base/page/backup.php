<?php

/**
* description: ATK Page
* 
* @author : Rk Sinha
* @email : gowravvishwakarma@gmail.com, info@xavoc.com
* @website : http://xepan.org
* 
*/

namespace xepan\base;

class page_backup extends \Page {
	public $title='Backup Management';

	function init(){
		parent::init();

		$model = $this->add('xepan\base\Model_Backup');
		
		$crud = $this->add('xepan\hr\CRUD',['allow_edit'=>false,'pass_acl'=>true]);
		$crud->setModel($model,['name']);


		// size formatter
		$crud->grid->addHook('formatRow',function($g){
			$decimals = 2;
			$file_path = $g->model->getPath()."/".$g->model['name'];
			$bytes = filesize($file_path);
			$sz = 'BKMGTP';
			$factor = floor((strlen($bytes) - 1) / 3);
			$dimention = @$sz[$factor];
			if($dimention == 'B')
				$dimention = "Bytes";
			else
				$dimention .= "B";
			$g->current_row_html['size'] = sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) ." ". $dimention;
			
			$relative_path = 'websites/'.$this->app->current_website_name.'/backup/'.$g->model['name'];
			$g->current_row_html['name'] = '<a href="'.$relative_path.'" download>'.$g->model['name'].'</a>';
		});

		$crud->grid->addColumn('size');
		$crud->grid->addSno();
	}
}
