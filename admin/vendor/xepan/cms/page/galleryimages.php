<?php

namespace xepan\cms;

class page_galleryimages extends \xepan\base\Page{
	public $title = "Gallery Images";
	
    function init(){
        parent::init();

        $category_id = $this->app->stickyGET('gallerycategory_id');

        $cat = $this->add('xepan\cms\Model_ImageGalleryCategory')->tryLoad($category_id);
        if(!$cat->loaded()){
            $this->add('View')->addClass('alert alert-danger')->set("category not found");
            return;
        }else{
            $this->title = "Images of Gallery: ".$cat['name'];
        }
        
        $image_m = $this->add('xepan\cms\Model_ImageGalleryImages');
        $image_m->addCondition('gallery_cat_id',$category_id);
        $image_m->setOrder('sequence_order','desc');

        $image_c = $this->add('xepan\base\CRUD');
        if($image_c->form){
            $form = $image_c->form;
            $form->add('xepan\base\Controller_FLC')
                ->showLables(true)
                ->addContentSpot()
                ->layout([
                        'name~Title'=>'Gallery Image/Vedio Details~c1~4',
                        'image_id~Image'=>'c2~4',
                        'status'=>'c3~4',
                        'description'=>'c1a~12',
                        'custom_link'=>'c11~8',
                        'sequence_order'=>'c12~4~Decending Order',
                        'video_embedded_code'=>'c21~12',
                    ]);
        }

        $image_c->setModel($image_m,['name','image_id','video_embedded_code','status','description','custom_link','sequence_order']);
        $image_c->grid->addHook('formatRow',function($g){
            $path = './websites/'.$this->app->current_website_name."/".$g->model['image_id'];
            $g->current_row_html['text_to_display'] = $g->model['text_to_display'];
            $g->current_row_html['image_id'] = '<img style="width:100px;" src="'.$path.'" />';
        });

        $image_c->addButton('Gallery Category')->addClass('btn btn-primary')->js('click')->univ()->location('xepan_cms_gallery');

        $image_c->grid->removeAttachment();
        $image_c->grid->addPaginator($ipp=50);
        $image_c->grid->addQuickSearch(['name','image_id']);
        // $image_c->js('reload',$this->js()->univ()->location());
    }
}