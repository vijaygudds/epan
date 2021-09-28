<?php

/**
* description: ATK Model
* 
* @author : Deepak Kanojia
* @email : dkanojia93@gmail.com, info@xavoc.com
* @website : http://xepan.org
* 
*/

namespace xepan\blog;

class Model_BlogPost extends \xepan\base\Model_Table{
	public $table='blog_post';
	public $status = ['Published','UnPublished'];
	public $actions = [
					'Published'=>['view','edit','delete','unpublish','social_schedule'],
					'UnPublished'=>['view','edit','delete','publish','post_schedule','social_schedule']
					];

	public $title_field= 'title';

	function init(){
		parent::init();

		$this->hasOne('xepan\hr\Employee','created_by_id')->defaultValue(@$this->app->employee->id);

		$this->addField('title');
		$this->addField('slug_url');
		$this->addField('short_description')->type('text')->display(array('form'=>'xepan\base\RichText'));
		$this->addField('description')->type('text')->display(array('form'=>'xepan\base\RichText'));
		$this->addField('tag')->type('text');
		$this->addField('meta_title');
		$this->addField('order')->type('number')->defaultValue(0);
		$this->addField('updated_at')->type('DateTime');
		$this->addField('status')->enum(['Published','UnPublished']);
		$this->addField('meta_description');
		$this->addField('created_at')->defaultValue($this->app->now)->sortable(true)->system(true);
		$this->addField('type');
		$this->addField('anonymous_comment_config')->enum(['none','moderate','permit'])->defaultValue('permit');
		$this->addField('registered_comment_config')->enum(['moderate','permit'])->defaultValue('permit');
		$this->addField('show_comments')->enum(['show','hide'])->defaultValue('show');
		$this->add('xepan\filestore\Field_Image','image_id');

		$this->hasMany('xepan\blog\Association_PostCategory','blog_post_id',null,'PostCategoryAssociation');
		$this->hasMany('xepan\blog\Comment','blog_post_id',null,'Comments');

		$this->addCondition('type','BlogPost');
		$this->getElement('status')->defaultValue('Published');

		$this->addExpression('comment_count')->set(function($m,$q){
			return $m->refSQL('Comments')
					 ->addCondition('status','Approved')
					 ->count();
		});

		$this->addExpression('year')->set(function($m,$q){
			return $q->expr("( EXTRACT(year from [0] ) )",[$m->getElement('created_at')]);
		});

		$this->addExpression('month')->set(function($m,$q){
			return $q->expr("EXTRACT(YEAR_MONTH from [0])",[$m->getElement('created_at')]);
		});

		$this->addExpression('created_at_date')->set('DATE(created_at)');

		$this->setOrder('id','asc');
		$this->is([
				'title|to_strip_tags|required',
				'description|required'
			]);

		// $this->addHook('afterLoad',function($m){
		// 	if(!$m['short_description'] || $m['short_description']=='') $m['short_description']= $m['description'];
		// });
		
		$this->addHook('beforeSave',[$this,'updated_meta_description']);
		$this->addHook('beforeSave',[$this,'updated_at']);
	}

	function page_post_schedule($p){
		$schedule = $p->add('xepan\blog\Model_PublishSchedule');
		$schedule->addCondition('blog_post_id',$this->id);
		$schedule->addCondition('is_posted',0);
		$crud = $p->add('xepan\hr\CRUD',['entity_name'=>'Schedule'],null,['view\post\postschedule']);	
		$crud->setModel($schedule);
	}

	function page_social_schedule($p){	
		// if(!$this->installedApplication == true)
		// 	return;
		$page_name = $this->app->epan->config->getConfig('BLOG_PAGE');

		$form = $p->add('Form');
		$form->add('xepan\base\Controller_FLC')
				->showLables(true)
				->addContentSpot()
				->layout([
						'campaign'=>'Social Schedule~c1~12',
						'marketing_category'=>'c2~6',
						'page_name'=>'c3~6',
						'date'=>'c2~6',
						'time'=>'c3~6'
					
					]);		
		$campaign_field = $form->addField('Dropdown','campaign');
		$campaign_field->validate('required');
		$campaign_field->setEmptyText('Please select a campaign')->setModel('xepan\marketing\Model_Campaign');
		$form->addField('Dropdown','marketing_category')->setModel('xepan\marketing\Model_MarketingCategory');
		$form->addField('page_name')->set($page_name)->validate('required');
		$form->addField('DatePicker','date')->validate('required');
		$form->addField('TimePicker','time')->validate('required');

		$form->addSubmit('Schedule')->addClass('btn btn-primary btn-block');

		$url = "?post_id=".$this->id;
		$model_content = $this->add('xepan\marketing\Model_Content');
		$content_schedule_j = $model_content->join('schedule.document_id','id');
		$content_schedule_j->addField('date');
		$model_content->addCondition('url','like', '%'.$url.'%');

		$grid = $p->add('xepan\hr\Grid')->setModel($model_content,['title','date']);
		

		if($form->isSubmitted()){
			if(!$form['date'])				
				$form->error('date','Date field is mandatory');
				
			$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on'? 'https://': 'http://';			
			$blog_post_model = $this->add('xepan\blog\Model_BlogPost')->load($this->id);
			$model_socialpost = $this->add('xepan\marketing\Model_SocialPost');

			$this->app->epan->config->setConfig('BLOG_PAGE',$form['page_name'],'blog');

			$model_socialpost['title'] = $blog_post_model['title'].' - Author: '.$blog_post_model['created_by'];
			$model_socialpost['url'] = $protocol.$_SERVER['SERVER_NAME'].'?page='.$form['page_name'].'&post_id='.$this->id;
			$model_socialpost['marketing_category_id'] = $form['marketing_category'];
			$model_socialpost['status'] = 'Approved';
			$model_socialpost->save();

			$url_config = $this->app->epan->config;
			$url_config->setConfig('URL OF BLOG POST',$form['url'],'blog');

			$schedule_time = date("H:i:s", strtotime($form['time']));
			$schedule_date = $form['date'].' '.$schedule_time;
			
			$campaign = $this->add('xepan\marketing\Model_Campaign');
			$schedule = $this->add('xepan\marketing\Model_Schedule');

			$schedule['campaign_id'] = $form['campaign'];
			$schedule['document_id'] = $model_socialpost->id;
			$schedule['date'] = $schedule_date; 
			$schedule['client_event_id'] = '_fc'.uniqid(); 
			$schedule->save();
			
			$campaign->tryLoadBy('id',$form['campaign']);
			
			$old_schedule = json_decode($campaign['schedule'],true);
			$temp = Array ( 
				'title' => $model_socialpost['title'], 
				'start' => $schedule_date, 
				'document_id' => $model_socialpost->id, 
				'client_event_id' => $schedule['client_event_id'] 
			);
			
			$old_schedule[] = $temp;
			$campaign['schedule'] = json_encode($old_schedule);
			$campaign->save();
			
			$blog_post_model['status'] = 'Published';
			$blog_post_model->save();
			
			return $form->js(null,$form->js()->closest('.dialog')->dialog('close'))->univ()->successMessage('Blog Post Scheduled')->execute();
		}
	}

	function schedule(){		
		$schedule = $this->add('xepan\blog\Model_PublishSchedule');
		$schedule->addCondition('is_posted',0);
		$schedule->addCondition('date','<=',$this->app->now);

		foreach ($schedule as $publish_schedule) {			
			$post = $this->add('xepan\blog\Model_BlogPost')->load($publish_schedule['blog_post_id']);			
			$post['status'] = 'Published';			
			$post->saveAs('xepan\blog\Model_BlogPost');

			$publish_schedule['is_posted'] = true;
			$publish_schedule->saveAs('xepan\blog\Model_PublishSchedule');  
		}
	}

	//publish Blog Post
	function publish(){
		$this['status']='Published';
		$this['created_at'] = $this->app->now;
		$this->app->employee
            ->addActivity("Blog Post '".$this['title']."' has been published, now it can be view on web", $this->id/* Related Document ID*/, $this->id /*Related Contact ID*/,null,null,"xepan_blog_comment&blog_id=".$this->id."")
            ->notifyWhoCan('unPublish','Published',$this);
		$this->save();
	}

	//UnPublish Blog Post
	function unpublish(){
		$this['status']='UnPublished';
		$this->app->employee
            ->addActivity("Blog Post '". $this['title'] ."' has been unpublished, now it not available for show on web", $this->id /*Related Document ID*/, $this->id /*Related Contact ID*/,null,null,"xepan_blog_comment&blog_id=".$this->id."")
            ->notifyWhoCan('publish','UnPublished',$this);
		return $this->save();
	}

	

	function getAssociatedCategories(){
		$associated_categories = $this->ref('PostCategoryAssociation')
								->_dsql()->del('fields')->field('blog_post_category_id')->getAll();
		return iterator_to_array(new \RecursiveIteratorIterator(new \RecursiveArrayIterator($associated_categories)),false);
	}

	function removeAssociateCategory(){

		$this->add('xepan\blog\Model_Association_PostCategory')
			 ->addCondition('blog_post_id',$this->id)
			 ->deleteAll();
	}	

	function associateCategory($category){
		return $this->add('xepan\blog\Model_Association_PostCategory')
						->addCondition('blog_post_id',$this->id)
		     			->addCondition('blog_post_category_id',$category)
			 			->tryLoadAny()	
			 			->save();
	}

	function updated_meta_description(){
		if(!$this['meta_description']){
			preg_match_all("/<h\d*>(\w[^<]*)/i", $this['description'], $matches);
			
			$this['meta_description'] = $this['title'] . ' '. implode(", ", $matches[1]);
		}
	}

	function updated_at(){
		$this['updated_at'] = $this->app->now;
		
		$slug = $this['slug_url'];
		if(!trim($slug)){
			$slug = $this['title'];
		}
		$this['slug_url'] = $this->app->normalizeSlugUrl($slug);
	}
}
