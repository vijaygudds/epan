<?php

namespace xepan\webwidgets;

class Model_GalleryImageAssociation extends \xepan\base\Model_Table{
	public $table = "gallery_image_association";
	public $acl=false;

	function init(){
		parent::init();

		$this->hasOne('xepan\webwidgets\GalleryImage','gallery_image_id');
		$this->hasOne('xepan\webwidgets\GalleryCategory','gallery_category_id');
	}	
}
