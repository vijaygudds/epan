<?php

namespace xepan\cms;

/**
* display all category and image can be used in portfoli or image gallery
*/

class Tool_Gallery extends \xepan\cms\View_Tool{

	public $options=[
				'gallery_type'=>'',  //portfolio, googlegallery
				'detail_page'=>'',
				'show_link'=>true,
				'show_fancybox'=>true,
				'img_gallery_category'=>null,
				'show_title'=>true,
				'show_description'=>true
			];

	public $model_image;
	public $model_category;

	function init(){
		parent::init();

		if($this->owner instanceof \AbstractController){
			$this->add('View_Info')->set('Please select it\'s options by double clicking on it');
			return;	
		}

		if(!$this->options['gallery_type']){
			$this->add('View')->set('Please Select Gallery Type')->addClass('alert alert-info');
			return;
		}

		$this->model_image = $images = $this->add('xepan\cms\Model_ImageGalleryImages');
		$images->addExpression('cat_status')->set(function($m,$q){
			return $q->expr('[0]',[$m->refSQL('gallery_cat_id')->fieldQuery('status')]);
		});
		$images->addCondition('cat_status','Active');
		$images->setOrder('sequence_order','desc');
		
		$this->model_category =  $cat = $this->add('xepan\cms\Model_ImageGalleryCategory');		
		$cat->addCondition('status','Active');

		if($this->options['img_gallery_category']){
			$selected_cat = explode(",", $this->options['img_gallery_category']);
			$this->model_image->addCondition([['gallery_cat_id',$selected_cat],['gallery_cat',$selected_cat]]);
			$this->model_category->addCondition([['id',$selected_cat],['name',$selected_cat]]);
		}

		switch ($this->options['gallery_type']) {
			case 'portfolio':
				$this->portfolioGallery();
				break;
			case 'googlegallery':
				$this->googleGallery();
				break;
		}

	}
	
	function portfolioGallery(){
		$this->js(true)->_css('gallery');
		$this->app->jquery->addStaticInclude('jquery.mixitup.min');
		$this->app->jquery->addStaticInclude('fancybox/jquery.fancybox');
		$this->js(true)->_css('fancybox/jquery.fancybox');

		$v = $this->add('View',null,null,['xepan\tool\gallery\portfolio']);

		$lister = $v->add('Lister',null,'category',['xepan\tool\gallery\portfolio','category_list']);
		$lister->setModel($this->model_category);

		$img_lister = $v->add('Lister',null,'item_list',['xepan\tool\gallery\portfolio','item']);
		$img_lister->setModel($this->model_image);

		$img_lister->addHook('formatRow',function($g){

			if($this->options['show_title']){
				$g->current_row['title'] = $g->model['title'];
			}else
				$g->current_row['title_wrapper'] = "";

			if(!$this->options['show_fancybox'])
				$g->current_row['fancybox_wrapper'] = "";

			if($this->options['show_description']){
				$g->current_row_html['description'] = $g->model['description'];
			}else
				$g->current_row['description_wrapper'] = "";

			if($this->options['show_link']){
				if($g->model['custom_link']){
					$g->current_row['link'] = $g->model['custom_link'];
				}else{
					$g->current_row['link'] = $this->app->url($this->options['detail_page']);
				}
			}else
				$g->current_row['link_wrapper'] = "";

			if($g->model['video_embedded_code']){
				$g->current_row_html['video_wrapper'] = '<div class="gallery-video-wrapper">'.$g->model['video_embedded_code'].'</div>';
				$g->current_row_html['image_wrapper'] = "";

				preg_match('/src="([^"]+)"/', $g->model['video_embedded_code'], $match);
				$video_url = $match[1];
				$g->current_row['image_path'] = $video_url;
				$g->current_row['fancy_class'] = "fancybox.iframe fancybox-video";
			}else{
				$img_url = './websites/'.$this->app->current_website_name."/".$g->model['image_id'];
				$g->current_row['image'] = $img_url;
				$g->current_row_html['image_wrapper'] = '<img style="width:100%;"  src="'.$img_url.'" class="xepan-commerce-gallery-image-to-zoom"/>';
				$g->current_row_html['video_wrapper'] = "";
				$g->current_row['image_path'] = './websites/'.$this->app->current_website_name."/".$g->model['image_id'];
				$g->current_row['fancy_class'] = "fancybox-img";
			}

		});

	}

	function googleGallery(){

		$carousel_cl = $this->add('CompleteLister',null,null,['xepan\tool\gallery\googlegallery']);
		$carousel_cl->setModel($this->model_image);

		$carousel_cl->addHook('formatRow',function($l){
			if($this->options['show_title']){
				$l->current_row_html['show_title'] = $l->model['name'];
			}else{
				$l->current_row_html['show_text_wrapper'] = ' ';				
			}
			$l->current_row_html['show_title'] = $l->model['name'];

			if($this->options['show_description']){
				$l->current_row_html['show_description'] = $l->model['description'];
			}

			if($l->model['video_embedded_code']){
				$l->current_row_html['video_wrapper'] = '<div class="gallery-video-wrapper">'.$l->model['video_embedded_code'].'</div>';
				$l->current_row_html['image_wrapper'] = "";
			}else{
				$img_url = './websites/'.$this->app->current_website_name."/".$l->model['image_id'];
				$l->current_row['image'] = $img_url;
				$l->current_row_html['image_wrapper'] = '<img style="width:100%;"  src="'.$img_url.'" class="xepan-commerce-gallery-image-to-zoom"/>';
				$l->current_row_html['video_wrapper'] = "";
			}
		});

	}
}