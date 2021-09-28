<?php

namespace xepan\cms;

class Model_CarouselLayer extends \xepan\base\Model_Table{
	public $table = "carousellayer";
	public $status = ['Active','Inactive'];

	public $actions=[
		'Active'=>['view','edit','delete','deactivate'],
		'Inactive'=>['view','edit','delete','activate']
	];

	function init(){
		parent::init();
		 
		$this->hasOne('xepan\cms\Model_CarouselImage','carousel_image_id');
		
		$this->addField('layer_type')->enum(['Text','Image','Video']);

		$this->addField('image_id')->display(['form'=>'xepan\base\ElImage']);
		$this->addField('video_url');
		$this->addField('text')->type('text')->display(['form'=>'xepan\base\RichText']);
		$this->addField('horizontal_position')->hint('Sets the horizontal position of the layer, using the value specified for data-position as a reference point. Can be set to a fixed or percentage value.');
		$this->addField('vertical_position')->hint('Sets the vertical position of the layer, using the value specified for data-position as a reference point. Can be set to a fixed or percentage value.');
				
		$this->addField('show_transition')->enum(['left','right','up','down'])->hint('Sets the transition of the layer when it appears in the slide. Can be set to left, right, up or down, these values describing the direction in which the layer will move when it appears.');
		$this->addField('hide_transition')->enum(['left','right','up','down'])->hint('Sets the transition of the layer when it disappears from the slide. Can be set to left, right, up or down, these values describing the direction in which the layer will move when it disappears.');
		$this->addField('show_delay')->hint('Sets a delay for the show transition. This delay starts from the moment when the transition to the new slide starts.');
		$this->addField('show_offset')->hint('Sets an offset for the position of the layer from which the layer will be animated towards the final position when it appears in the slide. Needs to be set to a fixed value.');
		$this->addField('hide_offset')->hint('Sets an offset for the position of the layer towards which the layer will be animated from the original position when it disappears from the slide. Needs to be set to a fixed value.');
		$this->addField('hide_delay')->hint('Sets a delay for the hide transition.');
		$this->addField('show_duration')->type('int')->hint('Sets the duration of the show transition.')->defaultValue(50);
		$this->addField('hide_duration')->type('int')->hint('Sets the duration of the hide transition.')->defaultValue(50);

		$this->addField('is_static')->type('boolean')->hint('Sets the layer to be visible all the time, not animated.');

		$this->addField('layer_class')->hint('sp-white, sp-black, sp-padding, sp-rounded');
		$this->addField('position')->enum(['topLeft','topCenter','topRight','bottomLeft','bottomCenter','bottomRight','centerLeft','centerRight','centerCenter'])->hint('Sets the position of the layer. Can be set to topLeft (which is the default value), topCenter, topRight, bottomLeft, bottomCenter, bottomRight, centerLeft, centerRight and centerCenter.');

		$this->addField('width')->hint('Sets the width of the layer. Can be set to a fixed or percentage value. If it\'s not set, the layer\'s width will adapt to the width of the inner content.')->defaultValue(100);
		$this->addField('height')->hint('Sets the height of the layer. Can be set to a fixed or percentage value. If it\'s not set, the layer\'s height will adapt to the height of the inner content.')->defaultValue(50);
		$this->addField('depth')->type('int')->hint('Sets the depth (z-index, in CSS terms) of the layer.')->defaultValue(10);

		$this->addField('type');
		$this->addCondition('type','CarouselLayer');

		$this->addField('status')->enum(['Active','Inactive'])->defaultValue('Active');
		// $this->add('dynamic_model/Controller_AutoCreator');

		$this->addExpression('category_id')->set($this->refSQL('carousel_image_id')->fieldQuery('carousel_category_id'));

		$this->addHook('afterSave',[$this,'updateJsonFile']);
	}

	function updateJsonFile(){

		if(isset($this->app->skipDefaultTemplateJsonUpdate) && $this->app->skipDefaultTemplateJsonUpdate) return;
		
		try{
			$master = $this->add('xepan\cms\Model_CarouselCategory');
			$master->load($this['category_id'])->updateJsonFile();
		}catch(\Exception $e){
			
		}
	}
}