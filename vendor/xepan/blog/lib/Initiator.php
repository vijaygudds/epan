<?php

namespace xepan\blog;

class Initiator extends \Controller_Addon {
	
	public $addon_name = 'xepan_blog';

	function setup_admin(){

		$this->routePages('xepan_blog');
		$this->addLocation(array('template'=>'templates','js'=>'templates/js'))
		->setBaseURL('../vendor/xepan/blog/');

		// if(isset($this->app->cms_menu)){
			// $this->app->cms_menu->addItem(['Blog Category','icon'=>' fa fa-sitemap'],'xepan_blog_blogpostcategory');//->setAttr(['title'=>'Blogs']);
			// $this->app->cms_menu->addItem(['Blog Category Group','icon'=>' fa fa-file-text-o'],'xepan_blog_blogcategorygroup');//->setAttr(['title'=>'Blogs']);
			// $this->app->cms_menu->addItem(['Blog Post','icon'=>' fa fa-file-text-o'],'xepan_blog_blogpost');//->setAttr(['title'=>'Blogs']);
		// }
		$this->app->addHook('entity_collection',[$this,'exportEntities']);
		$this->app->addHook('sef-config-form-layout',[$this,'sefConfigFormLayout']);
		$this->app->addHook('sef-config-form',[$this,'sefConfigForm']);

		$this->app->status_icon["xepan\blog\Model_BlogPost"] = ['All'=>' fa fa-globe','Published'=>"fa fa-file-text-o text-success",'UnPublished'=>'fa fa-file-o text-success'];

		return $this;

	}

	function setup_pre_frontend(){
		$this->app->addHook('sef-router',[$this,'addSEFRouter']);
		$this->app->addHook('sitemap_generation',[$this,'addSiteMapEntries']);
	}

	function setup_frontend(){
		$this->routePages('xepan_blog');
		$this->addLocation(array('template'=>'templates','js'=>'templates/js','css'=>'templates/css'))
		->setBaseURL('./vendor/xepan/blog/');

		 $this->app->exportFrontEndTool('xepan\blog\Tool_PostList','Blog');
		 $this->app->exportFrontEndTool('xepan\blog\Tool_PostDetail','Blog');
		 $this->app->exportFrontEndTool('xepan\blog\Tool_CategoryList','Blog');
		 $this->app->exportFrontEndTool('xepan\blog\Tool_Search','Blog');
		 $this->app->exportFrontEndTool('xepan\blog\Tool_Archieve','Blog');

		 // $this->app->app_router->addRule("blog\/(.*)\/(.*)", "blog-item", array("post_category","blog_post_code"));
		 // $this->app->app_router->addRule("blog\/(.*)\/(.*)\/(\d*)", "blog-item", array("post_category","blog_post_code","post_id"));
		
		return $this;
	}

	function exportEntities($app,&$array){
        $array['PostCategory'] = ['caption'=>'PostCategory','type'=>'DropDown','model'=>'xepan\blog\Model_BlogPostCategory'];
        $array['BlogPost'] = ['caption'=>'BlogPost','type'=>'DropDown','model'=>'xepan\blog\Model_BlogPost'];
        $array['xepan\blog\CategoryGroup'] = ['caption'=>'Blog Category Group','type'=>'DropDown','model'=>'xepan\blog\Model_CategoryGroup'];
    }

    function sefConfigForm($app,$form, $values){
		$form->addField('blog_list_page')->setFieldHint('Blog Category List Page in front website')
			->set($values['blog_list_page']);
		$form->addField('blog_detail_page')->setFieldHint('Blog Detail Page in front website')
			->set($values['blog_detail_page']);
	}

	function getTopApplicationMenu(){
		if($this->app->getConfig('hidden_xepan_blog',false)){return [];}
		return [
				'CMS'=>[
						[	'name'=>'Blog Category',
							'icon'=>' fa fa-sitemap',
							'url'=>'xepan_blog_blogpostcategory'
						],
						[	'name'=>'Blog Category Group',
							'icon'=>' fa fa-file-text-o',
							'url'=>'xepan_blog_blogcategorygroup'
						],
						[	'name'=>'Blog Post',
							'icon'=>' fa fa-file-text-o',
							'url'=>'xepan_blog_blogpost'
						]
					]
			];
	}
	

	function sefConfigFormLayout($app,&$layout){
		$layout ['blog_list_page']='Blog~c1~6'; 
		$layout ['blog_detail_page']='c2~6'; 
	}

	function addSEFRouter($app, $value){

		if($value['blog_list_page'])
			$this->app->app_router->addRule('\/'.$value['blog_list_page'], $value['blog_list_page']);
		
		if($value['blog_list_page'])
			$this->app->app_router->addRule('\/'.$value['blog_list_page']."\/(.*)", $value['blog_list_page'], ['blog_category_slug_url']);

		if($value['blog_detail_page'])
			$this->app->app_router->addRule('\/'.$value['blog_detail_page']."\/([a-z0-9\-]*)[\/]?", $value['blog_detail_page'], ['blog_post_slug_url']);
	}

	function addSiteMapEntries($app,&$urls,$sef_config_page_lists){

		if($page = $sef_config_page_lists['blog_detail_page']){
			$post = $this->add('xepan\blog\Model_BlogPost')
				->addCondition('status','Published');

			foreach ($post as $p) {
				if($this->app->enable_sef){
					$url = $this->app->url($page.'/'.$p['slug_url']);
				}else{
					$url = $this->app->url($page,['post_id'=>$p->id]);
				}
				
				$urls[] = (string)$url;
			}

		}
	}

	function resetDB(){
		// Clear DB
		// if(!isset($this->app->old_epan)) $this->app->old_epan = $this->app->epan;
  //       if(!isset($this->app->new_epan)) $this->app->new_epan = $this->app->epan;
        
		// $this->app->epan=$this->app->old_epan;
  //       $truncate_models = ['BlogPostCategory','BlogPost'];
  //       foreach ($truncate_models as $t) {
  //           $m=$this->add('xepan\blog\Model_'.$t);
  //           foreach ($m as $mt) {
  //               $mt->delete();
  //           }
  //       }
		// $this->app->epan=$this->app->new_epan;
	}

}
