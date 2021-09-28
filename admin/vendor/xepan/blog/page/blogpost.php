<?php
 
namespace xepan\blog;

class page_blogpost extends \xepan\base\Page {
	public $title='Blog Posts';

	function init(){
		parent::init();

		$blog_model = $this->add('xepan\blog\Model_BlogPost');
		$blog_model->add('xepan\blog\Controller_SideBarStatusFilter');

		$crud = $this->add('xepan\hr\CRUD',null,null,['view/post/post']);
		if($crud->form){
			$form = $crud->form;
			$form->add('xepan\base\Controller_FLC')
				->showLables(true)
				->addContentSpot()
				->layout([
						'title'=>'Blog Post Details~c1~8',
						'slug_url'=>'c2~4',
						'image_id~Image'=>'c1a~4',
						'created_by_id~Created By'=>'c1b~4',
						'order'=>'c1c~4',
						'description'=>'c2a~12',
						'tag'=>'Other Details~c1~12',
						'meta_title'=>'c11~6',
						'updated_at'=>'c12~6',
						'meta_description'=>'c13~6',
						'status'=>'c14~6',
						'anonymous_comment_config'=>'c15~6',
						'registered_comment_config'=>'c16~6',
						'show_comments'=>'c17~6',
						'category'=>'Blog Post Category~c1~12',
						
						
					]);
		}
		
		if($crud->isEditing()){			
			$cat = $crud->form->addField('xepan\base\DropDown','category');
			$cat->setAttr(['multiple'=>'multiple']);
			$cat->setModel('xepan\blog\BlogPostCategory');

			$crud->form->addHook('submit', function($f)use($blog_model){
				$blog_model->addHook('afterSave',function($m)use($f){
					$cat_array = [];
					$cat_array = explode(',', $f['category']);
					
					$cat = $this->add('xepan\blog\Model_Association_PostCategory')->addCondition('blog_post_id',$m->id);
					$cat->deleteAll();				
					foreach ($cat_array as $value) {
						$assoc = $this->add('xepan\blog\Model_Association_PostCategory');	
						$assoc['blog_post_category_id'] = $value;
						$assoc['blog_post_id'] = $m->id;
						$assoc->save();	
					}
				});
			});
		}


		$crud->setModel($blog_model)->setOrder('created_at','desc');
		$crud->grid->addQuickSearch(['title']);
		$crud->grid->addPaginator(50);
		
		$crud->grid->addColumn('category');
		$crud->grid->addMethod('format_postcategory',function($grid,$field){
				$data = $grid->add('xepan\blog\Model_Association_PostCategory')->addCondition('blog_post_id',$grid->model->id);
				$l = $grid->add('Lister',null,'category',['view/post/post','category_lister']);
				$l->setModel($data);
				
				$grid->current_row_html[$field] = $l->getHtml();
		});

		$crud->grid->addFormatter('category','postcategory');
		
		if($crud->isEditing()){
			$blog_cat = $this->add('xepan\blog\Model_Association_PostCategory')->addCondition('blog_post_id',$crud->model->id);
			
			$temp = [];
			foreach ($blog_cat as $value) {	
				array_push($temp, $value['blog_post_category_id']);
			}
			$crud->form->getElement('category')->set($temp)->js(true)->trigger('changed');
		}

		if(!$crud->isEditing()){																	
			$crud->grid->js('click')->_selector('.do-view-blog-post')->univ()->frameURL('Blog Post Details',[$this->api->url('xepan_blog_comment'),'blog_id'=>$this->js()->_selectorThis()->closest('[data-id]')->data('id')]);
		}
	}
} 