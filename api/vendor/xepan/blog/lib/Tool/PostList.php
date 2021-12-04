<?php

namespace xepan\blog;

class Tool_PostList extends \xepan\cms\View_Tool{
	public $options = [
					'show_description'=>true,
					'show_paginator'=>true,
					'paginator_set_rows_per_page'=>4,
					'description_page_url'=>'blog-item',
					'show_image'=>true,
					'set_by_order'=>'order_by_created_at',
					'add_socialshare'=>true,
					'include_socialshare'=>'email,twitter,facebook,googleplus,linkedin,pinterest,stumbleupon,whatsapp',
					'socialshare_theme'=>"flat", //classic,minima,plain
					'show_microdata'=>true,
					'show_post_of_category'=>0,
					'custom_template'=>'',
					'show_read_more_btn'=>true

				];

	function init(){
		parent::init();

		if($this->owner instanceof \AbstractController) return;

		
		if($this->options['show_microdata']){
			$this->company_m = $this->add('xepan\base\Model_Config_CompanyInfo');
			$this->company_m->tryLoadAny();
		}

		$post = $this->add('xepan\blog\Model_BlogPost');
		$post->addCondition('status','Published');
		$post->setOrder('created_at','desc');

		// if show_post of category is defiend then only show this category post
		$selected_category = [];
		if($this->options['show_post_of_category']){
			$selected_category = explode(",", $this->options["show_post_of_category"]);

		}elseif($category_id = $this->app->stickyGET('category_id')){
			$selected_category[]  = $category_id;
		}elseif($this->app->enable_sef && $slug_url = $this->app->stickyGET('blog_category_slug_url')){
			$selected_category[] = $this->add('xepan\blog\Model_BlogPostCategory')->loadBy('slug_url',$slug_url)->get('id');
		}

		if(count($selected_category)){
			$assoc_j = $post->join('blog_post_category_association.blog_post_id');
			$assoc_j->addField('blog_post_category_id');
			$post->addCondition('blog_post_category_id',$selected_category);
			$post->_dsql()->group('blog_post_id');
		}

		if($search_string = $this->app->stickyGET('search')){
			$post->addExpression('Relevance')->set('MATCH(title, description, tag, meta_title, meta_description) AGAINST ("'.$search_string.'" IN NATURAL LANGUAGE MODE)');
			$post->addCondition('Relevance','>',0);
	 		$post->setOrder('Relevance','Desc');
		}

		if($month = $this->app->stickyGET('month')){
			$post->addCondition('month',$month);
		}

		$post->addExpression('short_description')->set(function($m,$q){
			return $q->expr("LEFT(REGEXP_REPLACE([0], '<.+?>',' '),100)",[$m->getElement('description')]);
		});

		$custom_template = $this->options['custom_template'];
		if($this->options['custom_template']){
			$path = getcwd()."/websites/".$this->app->current_website_name."/www/view/tool/post/".$this->options['custom_template'].".html";
			if(!file_exists($path)){
				$this->add('View_Warning')->set('Please create template at www/view/tool/post/'.$this->options['custom_template']);
				return;	
			}
		}else{
			$custom_template = "list";
		}
		$this->complete_lister =  $cl = $this->add('CompleteLister',null,null,['view/tool/post/'.$custom_template]);
		if(!$post->count()->getOne())
			$cl->template->set('not_found_message','No Record Found');
		else
			$cl->template->del('not_found');

		
		if($this->options['show_paginator'] && $this->options['paginator_set_rows_per_page']){
			$paginator = $cl->add('Paginator',['ipp'=>$this->options['paginator_set_rows_per_page']]);
			$paginator->setRowsPerPage($this->options['paginator_set_rows_per_page']);
		}elseif(!$this->options['show_paginator'] && $this->options['paginator_set_rows_per_page']){
			$post->setLimit($this->options['paginator_set_rows_per_page']);
		}
		
		$cl->setModel($post);
		$cl->add('xepan\cms\Controller_Tool_Optionhelper',['options'=>$this->options,'model'=>$post]);
		
		if($this->options['set_by_order']=='order_by_id'){
				$post->setOrder('order','asc');
			}elseif($this->options['set_by_order']=='order_by_recent_post'){
				$post->setOrder('created_at','Desc');
			}elseif($this->options['set_by_order']=='order_by_created_at'){
				$post->setOrder('created_at','asc');
		}
		
		
	}

	function recursiveRender(){
		parent::recursiveRender();
		
		if($this->options['add_socialshare'] && ($this->options['add_socialshare'] =="true" || $this->options['add_socialshare']==1)){
			$this->js(true)->_load('socialshare/jssocials');
			$this->js(true)->_css('socialshare/jssocials');
			$this->js(true)->_css('socialshare/jssocials-theme-'.$this->options['socialshare_theme']);
		}
	}

	function addToolCondition_row_show_description($value, $l){
		
		if(!$value){
			$l->current_row_html['description_wrapper'] = "";
			return;
		}

		$l->current_row_html['description'] =$l->model['description'];
		$l->current_row_html['short_description'] =$l->model['short_description'];
	}

	function addToolCondition_row_show_read_more_btn($value,$l){
		if(!$value){
			$l->current_row_html['read_more_wrapper'] = "";
			return;
		}		
	}

	function addToolCondition_row_show_microdata($value, $l){
		// $v=$this->app->add('View',null,null,['view/schema-micro-data','blog_post_block']);
		// $v->template->trySet($l->model->data);
		// if($l->model['image'])
		// 	$v->template->trySet('blog_image', $this->app->pm->base_url. $l->model['image']);
		// $v->template->trySet('url',$this->app->pm->base_url.$this->app->url(null,['post_id'=>$l->model->id]));
		// $v->template->trySet('blog_description',strip_tags($l->model['description']));
		
		// $l->current_row_html['micro_data']=$v->getHtml();

	}

	function addToolCondition_row_show_image($value, $l){
		if(!$value || !$l->model['image_id']){
			$l->current_row_html['image_wrapper'] = "";
			return;	
		}
	}

	function addToolCondition_row_description_page_url($value, $l){
		// $config = $this->add('xepan\base\Model_ConfigJsonModel',
  //           [
  //               'fields'=>[
  //                           'enable_sef'=>'checkbox'
  //                       ],
  //                   'config_key'=>'SEF_Enable',
  //                   'application'=>'cms'
  //       ]);
  //       $config->tryLoadAny();

		if($this->app->enable_sef){
			$l->current_row['url'] = $this->app->url($this->options['description_page_url'].'/'.$l->model['slug_url']);
		}else{
			$l->current_row['url'] = $this->app->url($this->options['description_page_url'],['post_id'=>$l->model->id]);
		}
	}

	function addToolCondition_row_add_socialshare($value,$l){
		if(!$value){
			$l->current_row_html['socialshare'] = "";
			$l->current_row_html['socialshare_wrapper'] = "";
			return;
		} 

		// $l->current_row_html['socialshare'] = $l->add('View',null,'socialshare')->set("URL")->getHtml();						
		$sharing_url = $this->app->pm->base_url.$this->app->url($this->options['description_page_url'],['xepan_landing_content_id'=>$l->model->id,'post_id'=>$l->model->id]);
		$social_shares = explode(",", $this->options['include_socialshare']?:'email,twitter,facebook,googleplus,linkedin,pinterest,stumbleupon,whatsapp');
		$social_shares = array_values($social_shares);

		$this->js(true)->_selector('#postshare'.$l->model->id)
						->jsSocials(
							[
								'shares'=>$social_shares,
								'url'=> $sharing_url,
								'text'=>$l->model['meta_title']?$l->model['meta_title']:$l->model['title']
							]);
						// 'url'=>
						// 'text'=>
						// 'showLabel'=>,
						// 'showCount'=>
						// 'shareIn'=>
	}

	function getTemplate(){
		return $this->complete_lister->template;
	}

	// function getTemplateFile(){
	// uses $this->getTemplate() in ViewTool that is overrided already above
	// 	return $this->complete_lister->template->origin_filename;
	// }

}