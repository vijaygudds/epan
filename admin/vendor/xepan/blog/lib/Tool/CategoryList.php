<?php

namespace xepan\blog;

class Tool_CategoryList extends \xepan\cms\View_Tool{
	public $options = [
						'category_count'=> 0,
						'show_post_count'=>true,
						'redirect_page_url'=>'blog',
						'show_post'=>true,
						'group'=>'',
						'order'=>false,
						'post_detail_page'=>'',
						'category_group'=>null
				];

	function init(){
		parent::init();

		if($this->owner instanceof \AbstractController) return;
		
		$category = $this->add('xepan\blog\Model_BlogPostCategory');
		if($this->options['category_count'])
			$category->setLimit($this->options['category_count']);
		
		$category->addCondition('status','Active');
		
		if($this->options['order'])
			$category->setOrder('order','asc');
		
		// if($this->options['group'])	
		// 	$category->addCondition('group',$this->options['group']);

		$cl = $this->add('CompleteLister',null,null,['view/tool/post/category']);
		
		if($this->options['show_post']){
			$assos_j = $category->leftJoin('blog_post_category_association.blog_post_category_id');
			$blog_j = $assos_j->leftJoin('blog_post','blog_post_id');
			$blog_j->addField('title');
			$blog_j->addField('post_id','id');
			$blog_j->addField('blog_status','status');
			$blog_j->addField('blog_slug_url','slug_url');
			
			if(!$this->app->auth->isLoggedIn() || !in_array($this->app->auth->model['scope'],['AdminUser','SuperUser'])){
				$category->addCondition('blog_status','Published');
			}

			$category->setOrder(['order asc','post_id asc']);
		}

		if(!$category->count()->getOne()){
			$cl->template->set('not_found_message','No Record Found');
		}
		else
			$cl->template->del('not_found');

		$categories=[];
		$posts=[];
		$group_category = [];
		if($gid = $this->options['category_group']){
			$cg = $this->add('xepan\blog\Model_CategoryGroup');
			$cg->load($gid);
			
			foreach ($cg['categories'] as $name => $value) {
				if(!$value) continue;
				$group_category[$name] = $name;
			}
		}
		
		foreach ($cat_rows = $category->getRows() as $cat) {			
			if($this->options['category_group']){
				if(!isset($group_category[$this->app->normalizeName($cat['name'])])) continue;
			}
			
			if(!isset($categories[$cat['id']])) $categories[$cat['id']]=['name'=>$cat['name'],'slug_url'=>$cat['slug_url']];

			$posts[$cat['id']][] =  ['title'=>$cat['title'],'post_id'=>$cat['post_id'], 'status'=>$cat['blog_status'],'slug_url'=>$cat['blog_slug_url']];
		}

		if($this->options['show_post']){
			$cl->addHook('formatRow',function($cl)use($categories,$posts,$category){			
				$pl = $cl->add('CompleteLister',null,'cat_post',['view/tool/post/category','cat_post']);
				
				$pl->addHook('formatRow',function($pl_r)use($cl,$posts){

					if($this->app->enable_sef){
						$url = $this->app->url($this->options['post_detail_page']."/".$pl_r->model['slug_url']);
						$url->arguments = [];
						$pl_r->current_row['post_detail_page_url'] = $url;
					}else
						$pl_r->current_row['post_detail_page_url'] =$this->app->url($this->options['post_detail_page'],['post_id'=>$pl_r->model['post_id']]);
					
					if($pl_r->model['status'] == 'UnPublished')
						$pl_r->current_row['color'] = 'text-muted';

					if($pl_r->model['post_id'] == $_GET['post_id']?:0){
						$pl_r->current_row['active_class']='active';

					}elseif($pl_r->model['slug_url'] == $_GET['blog_post_code']){
						$pl_r->current_row['active_class'] = 'active';
					}else
						$pl_r->current_row['active_class']='';
				});
				
				$pl->setSource($posts[$cl->model->id]);
				$cl->current_row_html['cat_post'] = $pl->getHTMl();				
			});
		}

		// $cl->setModel($category);
		$cl->setSource($categories);

		$cl->add('xepan\cms\Controller_Tool_Optionhelper',['options'=>$this->options,'model'=>$category]);
	}

	function addToolCondition_row_show_post_count($value, $l){
		
		if(!$value){
			$l->current_row_html['post_count_wrapper'] = "";
			return;
		}

		$count = $l->add('xepan\blog\Model_Association_PostCategory')
					->addCondition('post_status','Published')
					->addCondition('blog_post_category_id',$l->model->id)
					->_dsql()->group('blog_post_category_id')
					->del('fields')->field('count(*)');
		$l->current_row_html['post_count'] = $count;
	}

	function addToolCondition_row_redirect_page_url($value, $l){
		if($this->app->enable_sef){
			$url = $this->app->url($this->options['redirect_page_url']."/".$l->model['slug_url']);
			$url->arguments = [];			
			$l->current_row['url'] = $url;
		}else
			$l->current_row['url'] = $this->app->url($this->options['redirect_page_url'],['category_id'=>$l->model->id]);

	}

	function addToolCondition_row_show_post($value, $l){
		
		if(!$value){
			$l->current_row_html['post_wrapper'] = "";
			return;
		}
	}
}