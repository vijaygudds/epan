<?php

/**
* description: ATK Model
* 
* @author : Gowrav Vishwakarma
* @email : gowravvishwakarma@gmail.com, info@xavoc.com
* @website : http://xepan.org
* 
*/

namespace xepan\cms;

class Model_Webpage extends \xepan\base\Model_Table{
	public $table='webpage';
	public $acl_type = 'Webpage';

	public $status = ['All'];
	public $actions = ['All'=>['view','edit','delete']];
	
	function init(){
		parent::init();

		$this->hasOne('xepan\cms\Template','template_id');
		$this->hasOne('xepan\cms\ParentPage','parent_page_id')->defaultValue(0);
		$this->hasOne('xepan\hr\Employee','created_by_id')->defaultValue(@$this->app->employee->id);
		
		$this->addField('name')->hint('used for display ie. menu');
		$this->addField('path')->hint('folder_1/folder_2/file_name.html or http://epan.in or #epan-promotion-section');
		
		$this->addField('page_title');
		$this->addField('meta_kewords')->type('text');
		$this->addField('meta_description')->type('text');
		$this->addField('after_body_code')->type('text');
		
		$this->addField('is_template')->type('boolean')->defaultValue(false);
		$this->addField('is_muted')->type('boolean')->hint('for show or hide on menu');
		$this->addField('icon_class')->hint('for show icon on menu, define multiple value space seperate');
			
		$this->addField('is_active')->type('boolean')->defaultValue(1);
		$this->addField('order')->type('number')->defaultValue(0);
		
		$this->addField('is_secure')->type('boolean')->defaultValue(false);
		$this->addField('secure_only_for')->display(['form'=>'xepan\base\NoValidateDropDown']);

		$this->hasMany('xepan\cms\Webpage','template_id',null,'Pages');
		$this->hasMany('xepan\cms\Webpage','parent_page_id',null,'SubPages');

		$this->is([
			'name|to_trim|required',
			'path|to_trim|required',
		]);

		$this->addHook('beforeSave',$this);
		$this->addHook('beforeDelete',$this);
		$this->addHook('afterSave',[$this,'updateJsonFile']);

	}

	function mergeFromTemplate(){
		$template = $this->ref('template_id'); 
		if(!$this['page_title']) $this['page_title'] =$template['page_title'];
		if(!$this['meta_kewords']) $this['meta_kewords'] =$template['meta_kewords'];
		if(!$this['meta_description']) $this['meta_description'] =$template['meta_description'];
		if(!$this['after_body_code']) $this['after_body_code'] =$template['after_body_code'];
		$this['path'] = str_replace(".html", "", $this['path']);		
	}

	function beforeSave(){

		// if page path start with http, https or # then not create the file
		if((strpos($this['path'], "http") === 0) OR (strpos($this['path'], "https") === 0) OR (strpos($this['path'], "#") === 0)OR (strpos($this['path'], "/") === 0)){
			return;
			// do nothing
		}

		if(!$this['order']) $this['order']=0;

		// check for same entry or not
		$this['path'] = str_replace(".html", "", $this['path']);

		$new_path = $this['path'];
		$temp_array = explode(".", $new_path);
		if(strtolower(trim(end($temp_array))) != "html"){
			$new_path .= ".html";
		}
		
		$old_webpage = $this->add('xepan\cms\Model_Webpage');
		$old_webpage->addCondition('path',$new_path);
		if($this['is_template'])
			$old_webpage->addCondition('is_template',1);
		else
			$old_webpage->addCondition([['is_template',0],['is_template',null]]);

		$old_webpage->addCondition('id','<>', $this->id);
		
		$old_webpage->tryLoadAny();
		if($old_webpage->loaded()){
			throw $this->exception('file already exist on this path, change the path','ValidityCheck')->setField('path');
		}

		// creating file System
		$path = $this->api->pathfinder->base_location->base_path.'/websites/'.$this->app->current_website_name."/www";
		if($this['is_template']){
			$path .= "/layout";
		}

		$path_array = explode("/", $this['path']);
		$count = count($path_array);

		$original_name="";
		//for loop check folder or file exist or not
		for ($i=0; $i < $count ; $i++) {
			$name = trim($path_array[$i]);
			$name = $this->app->normalizeName($name,'-');

			if(!strlen($name)){
				throw $this->exception('wrong path file name must define','ValidityCheck')->setField('path');
			}
			
			//check if count is last for file
			if($count == ($i+1)){
				$temp_array = explode(".", $name);
				if(strtolower(trim(end($temp_array))) != "html"){
					$name .= ".html";
				}

				$path .= "/".$name;
				if(!file_exists($path)){
					$file = \Nette\Utils\FileSystem::write($path," ");
		  		}

				$original_name .= $name;
			}else{
				// for creation of folder
				$path .= "/".$name;
				if(!file_exists($path)){
					$folder = \Nette\Utils\FileSystem::createDir($path);
		  		}

				$original_name .= $name."/";
			}

		}

		$this['path'] = $original_name;
	}

	function beforeDelete(){

		if($this['is_template']){
			$count = $this->add('xepan\cms\Model_Page')
				->addCondition('template_id',$this->id)
				->count()
				->getOne()
				;
			if($count)
				throw new \Exception($count." Pages uses this template, first unlink this templete from all pages" );
		}

		$path = $this['path'];
		// if page path start with http, https or # then not create the file
		if((strpos($path, "http") === 0) OR (strpos($path, "https") === 0) OR (strpos($path, "#") === 0)){
			return;
			// do nothing
		}

		if($this->app->getConfig('remove_page_file',true)){
			\Nette\Utils\FileSystem::delete($this->getPagePath());
		}

	}

	function getPagePath($include_file_name=true){
		$path = $this->api->pathfinder->base_location->base_path.'/websites/'.$this->app->current_website_name."/www";
		if($this['is_template']){
			$path .= "/layout";
		}

		if($include_file_name) $path .= "/".$this['path'];

		return $path;
	}

	function updateJsonFile(){
		// if(!$this->app->epan['is_template']) return;
		
		if(isset($this->app->skipDefaultTemplateJsonUpdate) && $this->app->skipDefaultTemplateJsonUpdate) return;
		
		$path = $this->api->pathfinder->base_location->base_path.'/websites/'.$this->app->current_website_name."/www/layout";
		if(!file_exists(realpath($path))){
			\Nette\Utils\FileSystem::createDir('./websites/'.$this->app->current_website_name.'/www/layout');
		}

		$file_content = json_encode($this->add('xepan\cms\Model_Webpage')->setOrder('is_template','desc')->getRows());
		$fs = \Nette\Utils\FileSystem::write('./websites/'.$this->app->current_website_name.'/www/layout/webpage.json',$file_content);
		
	}

}
