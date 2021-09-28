<?php

namespace xepan\cms;

class Model_CarouselCategory extends \xepan\base\Model_Table{
	public $table = 'carouselcategory';
	public $status = ['Active','InActive'];
	public $actions = [
					'Active'=>['view','image','edit','delete','deactivate'],
					'InActive'=>['view','image','edit','delete','activate']
					];

	function init(){
		parent::init();
		
		$this->hasOne('xepan\hr\Employee','created_by_id')->defaultValue(@$this->app->employee->id);

		$this->addField('name');
		$this->addField('status')->enum(['Active','InActive'])->defaultValue('Active');
		$this->addField('created_at')->type('datetime')->defaultValue($this->app->now);

		$this->addField('layout')->enum(['highlighted-horizontal-text','multislide','highlighted-horizontal-thumbnail','mixed-video','highlighted-vertical-thumbnail'])->defaultValue('highlighted-horizontal-text');

		$this->addField('width')->defaultValue('100%');
		$this->addField('height');
		$this->addField('show_arrows')->type('boolean')->defaultValue(true);
		$this->addField('autoplay')->type('boolean')->defaultValue(true);
		$this->addField('show_buttons')->type('boolean')->defaultValue(true);
		$this->addField('auto_slide_size')->type('boolean')->defaultValue(true);
		$this->addField('auto_height')->type('boolean')->defaultValue(true);

		$this->addField('full_screen')->type('boolean')->defaultValue(true);
		$this->addField('visible_size')->defaultValue(100);
		$this->addField('force_size')->defaultValue('fullWidth');
		$this->addField('shuffle')->type('boolean')->defaultValue(true);
		$this->addField('orientation')->enum(['vertical','horizontal']);
		
		$this->addField('thumbnails_position')->enum(['right','left']);
		$this->addField('thumbnail_pointer')->type('boolean')->defaultValue(true);
		$this->addField('thumbnail_width')->type('int')->defaultValue(200);
		$this->addField('thumbnail_height')->type('int')->defaultValue(100);
		$this->addField('thumbnail_arrows')->type('int')->defaultValue(100);
		
		$this->addField('type');
		$this->addCondition('type','CarouselCategory');

		$this->addExpression('images')->set($this->add('xepan\cms\Model_CarouselImage')->addCondition('carousel_category_id',$this->getElement('id'))->count());
		$this->hasMany('xepan\cms\CarouselImage','carousel_image_id');

		$this->addHook('afterSave',[$this,'updateJsonFile']);
		$this->addHook('beforeDelete',$this);

		$this->is([
				'name|to_trim|required'
			]);
	}

	function beforeDelete(){
		$this->add('xepan\cms\Model_CarouselImage')
			->addCondition('carousel_category_id',$this->id)
			->deleteAll();
	}

	function activate(){
		$this['status']='Active';
		$this->app->employee
            ->addActivity("Carousel Category : '".$this['name']."' Activated", null/* Related Document ID*/, $this->id /*Related Contact ID*/,null,null,null)
            ->notifyWhoCan('deactivate','Active',$this);
		$this->save();
	}

	function deactivate(){
		$this['status']='InActive';
		$this->app->employee
            ->addActivity("Carousel Category : '".$this['name']."' is deactivated", null/* Related Document ID*/, $this->id /*Related Contact ID*/,null,null,null)
            ->notifyWhoCan('activate','InActive',$this);
		$this->save();
	}

	function image(){
		$this->app->redirect($this->app->url('xepan_cms_carouselimage',['carouselcategory_id'=>$this->id]));
	}

	function updateJsonFile(){
		
		// if(!$this->app->epan['is_template']) return;

		if(isset($this->app->skipDefaultTemplateJsonUpdate) && $this->app->skipDefaultTemplateJsonUpdate) return;

		$path = $this->api->pathfinder->base_location->base_path.'/websites/'.$this->app->current_website_name."/www/layout";
		if(!file_exists(realpath($path))){
			\Nette\Utils\FileSystem::createDir('./websites/'.$this->app->current_website_name.'/www/layout');
		}

		$cats = $this->add('xepan\cms\Model_CarouselCategory')->getRows();
		foreach ($cats as &$cat) {
			$images = $this->add('xepan\cms\Model_CarouselImage');
			$images->addCondition('carousel_category_id',$cat['id']);
			$cat['images'] = $images->getRows();

			foreach ($cat['images'] as &$img_data) {
				$layers = $this->add('xepan\cms\Model_CarouselLayer');
				$layers->addCondition('carousel_image_id',$img_data['id']);
				$img_data['layers'] = $layers->getRows();
			}
		}
		
		$file_content = json_encode($cats);
		$fs = \Nette\Utils\FileSystem::write('./websites/'.$this->app->current_website_name.'/www/layout/carousel.json',$file_content);
	}
}