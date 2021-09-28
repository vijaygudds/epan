<?php

namespace xepan\cms;

/**
* 
*/
class Model_ImageGalleryCategory extends \xepan\base\Model_Table{
	public $table = 'xepan_cms_image_gallery_categories';
	public $status = ['Active','InActive'];
	public $actions = [
					'Active'=>['view','image','edit','delete','deactivate'],
					'InActive'=>['view','image','edit','delete','activate']
					];

	function init(){
		parent::init();
		$this->hasOne('xepan\hr\Employee','created_by_id')->defaultValue($this->app->employee->id);
		$this->addField('name')->caption('Category Name');
		$this->addField('status')->enum(['Active','InActive'])->defaultValue('Active');
		$this->addField('created_at')->type('datetime')->defaultValue($this->app->now);
		$this->addField('type');
		$this->addCondition('type','ImageGalleryCategory');
		$this->hasMany('xepan\cms\ImageGalleryImages','gallery_cat_id');

		$this->addExpression('images')->set($this->add('xepan\cms\Model_ImageGalleryImages')->addCondition('gallery_cat_id',$this->getElement('id'))->count());

		$this->addHook('afterSave',[$this,'updateJsonFile']);
		
		$this->addHook('beforeDelete',$this);
	}

	function beforeDelete(){
		$this->add('xepan\cms\Model_ImageGalleryImages')
			->addCondition('gallery_cat_id',$this->id)
			->deleteAll();
	}

	function activate(){
		$this['status']='Active';
		$this->app->employee
            ->addActivity("Gallery Category : '".$this['name']."' Activated", null/* Related Document ID*/, $this->id /*Related Contact ID*/,null,null,null)
            ->notifyWhoCan('deactivate','Active',$this);
		$this->save();
	}

	function deactivate(){
		$this['status']='InActive';
		$this->app->employee
            ->addActivity("Gallery Category : '".$this['name']."' is deactivated", null/* Related Document ID*/, $this->id /*Related Contact ID*/,null,null,null)
            ->notifyWhoCan('activate','InActive',$this);
		$this->save();
	}

	function image(){
		$this->app->redirect($this->app->url('xepan_cms_galleryimages',['gallerycategory_id'=>$this->id]));
	}

	function updateJsonFile(){

		// if(!$this->app->epan['is_template']) return;
		
		if(isset($this->app->skipDefaultTemplateJsonUpdate) && $this->app->skipDefaultTemplateJsonUpdate) return;
				
		$path = $this->api->pathfinder->base_location->base_path.'/websites/'.$this->app->current_website_name."/www/layout";
		if(!file_exists(realpath($path))){
			\Nette\Utils\FileSystem::createDir('./websites/'.$this->app->current_website_name.'/www/layout');
		}

		$master = $this->add('xepan\cms\Model_ImageGalleryCategory')->getRows();
		foreach ($master as &$m) {
			$chield = $this->add('xepan\cms\Model_ImageGalleryImages');
			$chield->addCondition('gallery_cat_id',$m['id']);
			$m['images'] = $chield->getRows();
		}

		$file_content = json_encode($master);
		$fs = \Nette\Utils\FileSystem::write('./websites/'.$this->app->current_website_name.'/www/layout/imagegallery.json',$file_content);
	}

}