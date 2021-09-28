<?php

namespace xepan\cms;

class Tool_AwesomeSlider extends \xepan\cms\View_Tool{
	public $options = [
				'show_caption'=>true,
				'slider_category'=>null,
				'control_nav'=>true,
				'paginator-position'=>"",
				'data-awesome-theme'=>""
			];
	public $carousel_id = '#slider_';
	public $controlNav=null;		
	function init(){
		parent::init();

		if(!$this->options['slider_category']){
			$this->add('View_Error')->set("Please Select Gallery Option First");
			return;
		}			

		
		$image_m = $this->add('xepan\cms\Model_CarouselImage');
		$image_m->addCondition([['carousel_category_id',$this->options['slider_category']],['carousel_category',$this->options['slider_category']]]);

		$carousel_cl = $this->add('CompleteLister',null,null,['view\tool\awesome-slider']);
		$carousel_cl->setModel($image_m);
		$this->carousel_id = '#slider_'.$carousel_cl->name;
		$this->paginatorPOS = $this->options['paginator-position'];
		$carousel_cl->addHook('formatRow',function($l){
			$l->current_row['file'] = './websites/'.$this->app->current_website_name."/".$l->model['file_id'];
		});

		if($this->options['control_nav']==true){
			$this->controlNav= true;
		}else{
			$this->controlNav= false;
		}
		
	}

	function render(){
		parent::render();
		$this->app->jui->addStaticInclude('awesome/jquery.nivo.slider');
		$this->js(true)->nivoSlider([
					'effect'=> 'random',
					'slices'=> 15,
					'boxCols'=> 8,
					'boxRows'=> 4,
					'animSpeed'=> 500,
					'pauseTime'=> 3000,
					'startSlide'=> 0,
					'directionNav'=> true,
					'controlNav'=> $this->controlNav,
					'controlNavThumbs'=> false,
					'pauseOnHover'=> true,
					'prevText'=> 'Prev',
					'nextText'=> 'Next',
					'randomStart'=> true
				])->_selector($this->carousel_id);
		$this->js(true)->find(".slider-wrapper")->last()->addClass($this->options['data-awesome-theme']);
		$this->js(true)->find(".nivo-controlNav")->last()->addClass($this->options['paginator-position']);
	}
}