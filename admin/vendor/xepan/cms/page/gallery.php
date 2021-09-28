<?php


namespace xepan\cms;

/**
* 
*/
class page_gallery extends \xepan\base\Page{
	public $title = "Image Gallery";
	
    function init(){
        parent::init();

        $gallery = $this->add('xepan\cms\Model_ImageGalleryCategory');
		$c = $this->add('xepan\hr\CRUD');
		$c->setModel($gallery,['name'],['name','images','status']);

        $c->removeAttachment();
        $c->grid->addPaginator($ipp=30);
        $c->grid->addQuickSearch(['name']);
    }
}