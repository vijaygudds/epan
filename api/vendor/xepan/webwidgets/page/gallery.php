<?php
# TODO
//  Category association problem 
//  Categories not shown on field while editing
//  Improve tool grid
//  Implement category option 
//  If category is inactive then don't show images only associated with that category 
//  Commenting
//  Add widgets to Sidemenu

namespace xepan\webwidgets;
class page_gallery extends \xepan\base\Page{
	public $title = "Gallery"; 

	function init(){
		parent::init();

		$tabs = $this->add('Tabs');
        $images = $tabs->addTab('Gallery Images');
        $category = $tabs->addTab('Gallery Category');
	
		$category_m = $this->add('xepan\webwidgets\Model_GalleryCategory');
		$images_m = $this->add('xepan\webwidgets\Model_GalleryImage'); 
		
		$category_c = $category->add('xepan\hr\CRUD',null,null,['grid\gallerycategory']);
		$category_c->setModel($category_m,['name'],['name','status']);        

		$image_c = $images->add('xepan\hr\CRUD',null,null,['grid\galleryimage']);
		$image_c->setModel($images_m,['gallery_category_id','title','description','file_id'],['gallery_category','title','description','file','status','comma_seperated_categories']);
	
		$image_c->grid->addColumn('category');
		$image_c->grid->addMethod('format_gallerycategory',function($grid,$field){	
				$data = $grid->add('xepan\webwidgets\Model_GalleryImageAssociation')->addCondition('gallery_image_id',$grid->model->id);

				$l = $grid->add('CompleteLister',null,'category',['grid/galleryimage','category_lister']);
				$l->setModel($data);
				
				$grid->current_row_html[$field] = $l->getHtml();
		});

		$image_c->grid->addFormatter('category','gallerycategory');

		if($image_c->isEditing()){
			$image_c->form->getElement('gallery_category_id')->setAttr(['multiple'=>'multiple']);
			}
		}
	}
}