<?php

namespace xepan\webwidgets;

class Model_GalleryImage extends \xepan\base\Model_Table{
	public $table = "galleryimage";
	public $status=[
		'Visible',
		'Hidden'
	];

	public $actions=[
		'Visible'=>['view','edit','delete','hide'],
		'Hidden'=>['view','edit','delete','show']
	];

	function init(){
		parent::init();
		
		$this->hasOne('xepan\webwidgets\Model_GalleryCategory','gallery_category_id');
		$this->hasOne('xepan\hr\Model_Employee','created_by_id')->defaultValue($this->app->employee->id);
		
		$this->add('xepan\filestore\Field_File','file_id');
		
		$this->addField('title');
		$this->addField('alt_text');

		$this->addField('created_at')->type('datetime')->defaultValue($this->app->now);
		$this->addField('status')->enum($this->status)->defaultValue('Visible');
		
		$this->addField('type');
		$this->addCondition('type','GalleryImage');
					
		$this->addExpression('thumb_url')->set(function($m,$q){
			return $q->expr('[0]',[$m->getElement('file')]);
		});

		$this->addHook('beforeSave',[$this,'beforeSave']);
		
		$this->hasMany('xepan\webwidgets\GalleryImageAssociation','gallery_image_id');
	}

	function beforeSave(){
		$category_array = [];
		$category_array = explode(',', $this['gallery_category_id']);
		
		$this->removeAssociateCategory();
		foreach ($category_array as $cat){
			$this->associateCategory($cat);
		}
	}

	function show(){
		$this['status']='Visible';
		$this->app->employee
            ->addActivity("Gallery Image : '".$this['title']."' is now visible", null/* Related Document ID*/, $this->id /*Related Contact ID*/,null,null,null)
            ->notifyWhoCan('hide','Visible',$this);
		$this->save();
	}

	function hide(){
		$this['status']='Hidden';
		$this->app->employee
            ->addActivity("Gallery Image : '".$this['title']."' is now hidden", null/* Related Document ID*/, $this->id /*Related Contact ID*/,null,null,null)
            ->notifyWhoCan('hide','Hidden',$this);
		$this->save();
	}

	function getAssociatedCategories(){
		$associated_categories = $this->ref('xepan\webwidgets\GalleryImageAssociation')
								->_dsql()->del('fields')->field('gallery_category_id')->getAll();
		return iterator_to_array(new \RecursiveIteratorIterator(new \RecursiveArrayIterator($associated_categories)),false);
	}

	function removeAssociateCategory(){
		$association_m = $this->add('xepan\webwidgets\Model_GalleryImageAssociation');
		$association_m->addCondition('gallery_image_id',$this->id);

		if($association_m->count()->getOne())
			$association_m->deleteAll();
	}

	function associateCategory($category){
		return $this->add('xepan\webwidgets\Model_GalleryImageAssociation')
						->addCondition('gallery_image_id',$this->id)
		     			->addCondition('gallery_category_id',$category)
			 			->tryLoadAny()	
			 			->save();
	}
}