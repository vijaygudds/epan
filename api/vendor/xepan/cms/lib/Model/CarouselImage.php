<?php

namespace xepan\cms;

class Model_CarouselImage extends \xepan\base\Model_Table{
	public $table = "carouselimage";
	public $status=[
		'Visible',
		'Hidden'
	];

	public $actions=[
		'Visible'=>['view','layers','edit','delete','hide'],
		'Hidden'=>['view','edit','delete','show']
	];

	function init(){
		parent::init();
		 
		$this->hasOne('xepan\cms\Model_CarouselCategory','carousel_category_id');
		$this->hasOne('xepan\hr\Model_Employee','created_by_id')->defaultValue(@$this->app->employee->id)->system(true);
		
		$this->addField('file_id')->display(['form'=>'xepan\base\ElImage']);
		
		$this->addField('title');
		$this->addField('text_to_display')->display(['form'=>'xepan\base\RichText'])->type('text');
		$this->addField('alt_text');
		$this->addField('order');
		$this->addField('link');

		$this->addField('slide_type')->enum(['Image','Video']);

		$this->addField('created_at')->type('datetime')->defaultValue($this->app->now);
		$this->addField('status')->enum($this->status)->defaultValue('Visible');
		
		$this->addField('type');
		$this->addCondition('type','CarouselImage');

		// $this->addExpression('thumb_url')->set(function($m,$q){
		// 	return $q->expr('[0]',[$m->getElement('file')]);
		// });

		$this->addHook('afterSave',[$this,'updateJsonFile']);
		$this->add('dynamic_model/Controller_AutoCreator');
	}

	function show(){
		$this['status']='Visible';
		$this->app->employee
            ->addActivity("Carousel Image : '".$this['title']."' is now visible", null/* Related Document ID*/, $this->id /*Related Contact ID*/,null,null,null)
            ->notifyWhoCan('hide','Visible',$this);
		$this->save();
	}

	function hide(){
		$this['status']='Hidden';
		$this->app->employee
            ->addActivity("Carousel Image : '".$this['title']."' is now hidden", null/* Related Document ID*/, $this->id /*Related Contact ID*/,null,null,null)
            ->notifyWhoCan('hide','Hidden',$this);
		$this->save();
	}

	function updateJsonFile(){

		// if(!$this->app->epan['is_template']) return;
		
		if(isset($this->app->skipDefaultTemplateJsonUpdate) && $this->app->skipDefaultTemplateJsonUpdate) return;
		
		try{
			$master = $this->add('xepan\cms\Model_CarouselCategory');
			$master->load($this['carousel_category_id'])->updateJsonFile();
		}catch(\Exception $e){
			
		}
	}


	function page_layers($page){

		$img = $page->add('xepan\cms\Model_CarouselLayer');
		$img->addCondition('carousel_image_id',$this->id);
		$crud = $page->add('xepan\base\CRUD');
		$form = $crud->form;
		$form->add('xepan\base\Controller_FLC')
        ->showLables(true)
        ->addContentSpot()
        // ->makePanelsCoppalsible(true)
        ->layout([
        		'layer_type'=>'Layer Info~c1~4',
        		'image_id~Image'=>'c2~4',
        		'video_url'=>'c3~4',
        		'text'=>'c4~12',
        		'position'=>'c5~6~Sets the position of the layer. Can be set to topLeft (which is the default value), topCenter, topRight, bottomLeft, bottomCenter, bottomRight, centerLeft, centerRight and centerCenter.',
        		'width'=>'c5~6~Sets the width of the layer. Can be set to a fixed or percentage value. If it\'s not set, the layer\'s width will adapt to the width of the inner content.',
        		'depth'=>'c5~6~Sets the depth (z-index, in CSS terms) of the layer.',
        		
        		'layer_class'=>'c6~6~sp-white, sp-black, sp-padding, sp-rounded',
        		'height'=>'c6~6~Sets the height of the layer. Can be set to a fixed or percentage value. If it\'s not set, the layer\'s height will adapt to the height of the inner content.',
        		'status'=>'c6~6',
        		'is_static~Static'=>'c6~6~Sets the layer to be visible all the time, not animated.',
        		
        		'horizontal_position'=>'Layer Position~c11~6~Sets the horizontal position of the layer, using the value specified for data-position as a reference point. Can be set to a fixed or percentage value.',
        		'vertical_position'=>'c12~6~Sets the vertical position of the layer, using the value specified for data-position as a reference point. Can be set to a fixed or percentage value.',
        		
        		'show_transition'=>'Layer Show/Hide Details~c21~6~Sets the transition of the layer when it appears in the slide. Can be set to left, right, up or down, these values describing the direction in which the layer will move when it appears.',
        		'show_offset'=>'c21~6~Sets an offset for the position of the layer from which the layer will be animated towards the final position when it appears in the slide. Needs to be set to a fixed value.',
        		'show_delay'=>'c21~6~Sets a delay for the show transition. This delay starts from the moment when the transition to the new slide starts.',
        		'show_duration'=>'c21~6~Sets the duration of the show transition.',
        		
        		'hide_transition'=>'c22~6~Sets the transition of the layer when it disappears from the slide. Can be set to left, right, up or down, these values describing the direction in which the layer will move when it disappears.',
        		'hide_offset'=>'c22~6~Sets an offset for the position of the layer towards which the layer will be animated from the original position when it disappears from the slide. Needs to be set to a fixed value.',
        		'hide_delay'=>'c22~6~Sets a delay for the hide transition.',
        		'hide_duration'=>'c22~6~Sets the duration of the hide transition.',
        		
        		
        	]);
		$all_fields = $img->getActualFields();
		$crud->setModel($img,$all_fields,['layer_type','image_id','horizontal_position','vertical_position','layer_class','show_delay','hide_delay','status']);
	}

}