<?php

namespace xepan\cms;

class page_carousel extends \xepan\base\Page{
	public $title = "Carousel";

	function init(){
        parent::init();
                    
        $category_m = $this->add('xepan\cms\Model_CarouselCategory');
        $category_c = $this->add('xepan\hr\CRUD');
        
        $form = $category_c->form;
        $form->add('xepan\base\Controller_FLC')
        ->showLables(true)
        ->addContentSpot()
        // ->makePanelsCoppalsible(true)
        ->layout([
                'name'=>'Carousel Details~c1~8',
                'created_by_id~Created By'=>'c2~4',
                'created_at'=>'c3~4',
                'status'=>'c4~4',
                'layout'=>'c5~4',
                
                'width'=>'Dimensions~c11~6~Either in % like 100%, 50% or in pixels like 500px, 960px',
                'height'=>'c12~6',
                
                'show_arrows'=>'Carousel Options~c21~4',
                'autoplay'=>'c22~4',
                'show_buttons'=>'c23~4',
                'auto_slide_size'=>'c24~4',
                'auto_height'=>'c25~4',
                'full_screen'=>'c26~4',
                
                'visible_size'=>'Carousel Size~c31~4',
                'force_size'=>'c32~4',
                'orientation'=>'c33~4',
                'thumbnail_width'=>'c34~4',
                'thumbnail_height'=>'c35~4',
                'thumbnails_position'=>'c36~4',
                'thumbnail_arrows'=>'c37~4',
                'shuffle'=>'c38~4',
                'thumbnail_pointer'=>'c39~4',
            ]);
        $all_fields = $category_m->getActualFields();
        $category_c->setModel($category_m,$all_fields,['name','created_by','created_at','status','width','height','type']);
        $category_c->removeAttachment();
        $category_c->grid->addPaginator($ipp=30);
        $category_c->grid->addQuickSearch(['name']);


    }
}