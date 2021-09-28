<?php

namespace xepan\cms;

class page_carouselimage extends \xepan\base\Page{
	public $title = "Carousel Image ";

	function init(){
        parent::init();

        $category_id = $this->app->stickyGET('carouselcategory_id');

        $cat = $this->add('xepan\cms\Model_CarouselCategory')->tryLoad($category_id);
        if(!$cat->loaded()){
            $this->add('View')->addClass('alert alert-danger')->set("category not found");
            return;            
        }else{
            $this->title = "Carousel Image of Category: ".$cat['name'];
        }

        $image_m = $this->add('xepan\cms\Model_CarouselImage');
        $image_m->addCondition('carousel_category_id',$category_id);
        $image_c = $this->add('xepan\hr\CRUD');
        $form = $image_c->form;
        $form->add('xepan\base\Controller_FLC')
        ->showLables(true)
        ->addContentSpot()
        // ->makePanelsCoppalsible(true)
        ->layout([
                'file_id~Attach Image'=>'Carousel Image Details~c1~6',
                'title'=>'c2~6',
                'text_to_display'=>'c3~12',
                'alt_text'=>'c4~4',
                'order'=>'c5~4',
                'link'=>'c6~4',
                'slide_type'=>'Slide Info~c11~4',
                'created_at'=>'c12~4',
                'status'=>'c13~4',


            ]);
        // $all_fields = $image_m->getActualFields();
        $image_c->setModel($image_m,null,['file_id','title','order','slide_type','created_at','status']);
        $image_c->grid->removeColumn('status');
        $image_c->grid->removeAttachment();
        $image_c->addButton('Carousel Category')->addClass('btn btn-primary')->js('click')->univ()->location('xepan_cms_carousel');

        $image_c->grid->addHook('formatRow',function($g){
            $path = './websites/'.$this->app->current_website_name."/".$g->model['file_id'];
            $g->current_row_html['text_to_display'] = $g->model['text_to_display'];
            $g->current_row_html['file_id'] = '<img style="width:100px;" src="'.$path.'" />';
        });
        $image_c->grid->addPaginator($ipp=50);
        $image_c->grid->addQuickSearch(['title','file_id']);
        // $image_c->js('reload',$this->js()->univ()->location());
        
        // $image_c->grid->addFormatter('link','wrap');
    }
}