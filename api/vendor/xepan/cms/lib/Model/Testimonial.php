<?php 

namespace xepan\cms;

class Model_Testimonial extends \xepan\base\Model_Table{

	public $table = "testimonial";
	public $rating_list = ['1','2','3','4','5'];
	public $acl_type = "xepan_testimonial";
	public $status = ['Pending','Approved','Cancelled'];
	public $actions = [
					'Pending'=>['view','Approve','Cancel','edit','delete'],
					'Approved'=>['view','Cancel','edit','delete'],
					'Cancelled'=>['view','edit','delete']
				];

	function init(){
		parent::init();
		

		$this->hasOne('xepan\cms\Model_TestimonialCategory','category_id');
		$this->hasOne('xepan\base\contact','contact_id');
		$this->hasOne('xepan\base\contact','created_by_id')->system(true)->defaultValue(@$this->app->employee->id);

        $this->addField('name')->caption('Title');
		$this->addField('created_at')->type('datetime')->defaultValue($this->app->now);
        $this->add('xepan\filestore\Field_File','image_id');
		$this->addField('description')->type('text');
		$this->addField('status')->enum($this->status)->defaultValue('Pending');
		$this->addField('video_url')->type('text');
		$this->addField('rating')->enum($this->rating_list);

		$this->addExpression('contact_image')->set($this->refSQL('contact_id')->fieldQuery('image'));
		
		// $this->add('dynamic_model\Controller_AutoCreator');
	}
}