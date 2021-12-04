<?php

namespace xepan\cms;

class page_cmspagemanager extends \xepan\base\Page{
	
	public $title = "Website Pages and Templates";
	
	function page_index(){
		// parent::init();

		$this->app->muteACL = true;

		$tab = $this->add('TabsDefault');
		$page_tab = $tab->addTab('Page');
		$temp_tab = $tab->addTab('Template');
		$meta_tab = $tab->addTab('Default Meta Info');
		$menu_tab = $tab->addTab('Menu Groups');
		
		// Website Template
		$template = $temp_tab->add('xepan\cms\Model_Template');
		$crud = $temp_tab->add('xepan\hr\CRUD');
		$crud->form->add('xepan\base\Controller_FLC')
					->layout([
							'name'=>'Page Info~c1~4',
							'path'=>'c2~6',
							'page_title'=>'Meta Info, Overrides Default Info~c1~12',
							'meta_kewords'=>'c2~12',
							'meta_description'=>'c3~12',
							'after_body_code'=>'Any Code to insert after body tag~c1~12~Mainly used for analytical purpose'
						]
						);
		$crud->setModel('xepan\cms\Template',['name','path','page_title','meta_kewords','meta_description','after_body_code'],['name','path']);
		/*Start Live Edit Template */
		$crud->grid->addColumn('Button','live_edit_template');
		$crud->grid->addMethod('format_live_edit_template',function($g,$f){
			$url =$this->app->url('layout/'.$g->model['name'],['xepan-template-edit'=>"layout/".$g->model['name']]);	
			$url = str_replace('/admin/',"/",$url);
			$g->current_row_html['live_edit_template']= '<a href="javascript:void(0)" onclick="'.$g->js()->univ()->newWindow($url).'"><span class="btn btn-success">Live Edit</span></a>';
		});
		$crud->grid->addFormatter('live_edit_template','live_edit_template');

		$crud->grid->add('VirtualPage')
	      ->addColumn('snapshots')
	      ->set(function($page){
	          $id = $this->app->stickyGET($page->short_name.'_id');
	          $m = $this->add('xepan\cms\Model_Snapshots')
	          		->addCondition('page_id',$id)
	          		->setOrder('created_at','desc');
	          $c = $page->add('xepan\hr\CRUD',['allow_add'=>false]);
	          $c->setModel($m,['name'],['name','created_at','page_url','page','content']);
	          $c->grid->addColumn('Button','Revert');
	          $c->grid->removeColumn('content');
	          $c->grid->removeColumn('page_url');
	          $c->grid->removeColumn('page');

	          if($snap_id = $_GET['Revert']){
	          	$m->load($snap_id);
	          	file_put_contents($m['page_url'], $m['content']);
	          	$this->js()->univ()->successMessage('File Content Replaced, please visit the page')->execute();
	          	// $this->js()->univ()->successMessage($this->app->url(str_replace(".html",'', $m->ref('page_id')->get('path'))))->execute();
	          	// $this->js(true,'document.location.href = document.location.href + "&xepan_snapshot_id="+'.$snap_id)->execute();
	          	// $this->app->redirect($this->app->url('/'.$this->app->page,['snapshot_show'=>$snap_id]));
	          }
	      });


		
		/*END Live Edit Template */

		// // Website Pages
		$page = $page_tab->add('xepan\cms\Model_Page')
					->setOrder(['parent_page_id','order','name'])
					;
		$crud = $page_tab->add('xepan\hr\CRUD');
		$crud->form->add('xepan\base\Controller_FLC')
					->layout([
							'template_id'=>'Template~c1~12',
							'name'=>'Page Info~c1~3',
							'path'=>'c2~4',
							'icon_class'=>'c3~2',
							'order'=>'c4~1',
							'is_muted~'=>'c5~2~<br/>Hidden in menu?',
							'is_active~'=>'c5~2~<br/>Hidden in sitemap?',
							'parent_page_id'=>'Parent Menu~c1~12',
							'page_title'=>'Meta Info, Overrides Default Info~c1~12',
							'meta_kewords'=>'c2~12',
							'meta_description'=>'c3~12',
							'after_body_code'=>'Any Code to insert after body tag~c1~12~Mainly used for analytical purpose',
							'is_secure'=>'Restricted Access~c1~3',
							'secure_only_for'=>'c2~9~Only these types of user can access page',
						]
						);

		$crud->setModel($page,['name','path','parent_page_id','template_id','order','is_muted','is_active','page_title','meta_kewords','meta_description','after_body_code','icon_class','is_secure','secure_only_for'],['name','parent_page','path','template','order','is_muted','is_active','is_secure']);
		if($crud->isEditing()){
			
			$config_m = $page_tab->add('xepan\cms\Model_Config_FrontendWebsiteStatus');
			$config_m->tryLoadAny();
			$f = $crud->form->getElement('secure_only_for');
			$f->setAttr('multiple');
			$f->setValueList(array_combine(explode(",", $config_m['system_contact_types']),explode(",", $config_m['system_contact_types'])));
			$f->set(explode(",", $crud->form->model['secure_only_for']));
		}

		$crud->grid->add('VirtualPage')
	      ->addColumn('snapshots')
	      ->set(function($page){
	          $id = $this->app->stickyGET($page->short_name.'_id');
	          $m = $this->add('xepan\cms\Model_Snapshots')
	          		->addCondition('page_id',$id)
	          		->setOrder('created_at','desc');
	          $c = $page->add('xepan\hr\CRUD',['allow_add'=>false]);
	          $c->setModel($m,['name'],['name','created_at','page_url','page','content']);
	          $c->grid->addColumn('Button','Revert');
	          $c->grid->removeColumn('content');
	          $c->grid->removeColumn('page_url');
	          $c->grid->removeColumn('page');

	          if($snap_id = $_GET['Revert']){
	          	$m->load($snap_id);
	          	file_put_contents($m['page_url'], $m['content']);
	          	$this->js()->univ()->successMessage('File Content Replaced, please visit the page')->execute();
	          	// $this->js()->univ()->successMessage($this->app->url(str_replace(".html",'', $m->ref('page_id')->get('path'))))->execute();
	          	// $this->js(true,'document.location.href = document.location.href + "&xepan_snapshot_id="+'.$snap_id)->execute();
	          	// $this->app->redirect($this->app->url('/'.$this->app->page,['snapshot_show'=>$snap_id]));
	          }
	      });


		$crud->grid->addColumn('Button','live_edit_page');
		$crud->grid->addMethod('format_live_edit_page',function($g,$f){
			$url =$this->app->url($g->model['path']);	
			$url = str_replace('/admin/',"/",$url);
			$url = str_replace('.html',"",$url);
			$g->current_row_html['live_edit_page']= '<a href="javascript:void(0)" onclick="'.$g->js()->univ()->newWindow($url).'"><span class="btn btn-success">Live Edit</span></a>';
		});
		$crud->grid->addFormatter('live_edit_page','live_edit_page');
		/*END Live Edit Page */


		$epan = $this->add('xepan\base\Model_Epan')->load($this->app->epan->id);
		$extra_info = json_decode($epan['extra_info'],true);
		$form = $meta_tab->add('Form');
		$form->add('xepan\base\Controller_FLC')
			->layout([
					'title'=>'Meta Info~c1~12',
					'meta_keyword'=>'c2~12',
					'meta_description'=>'c3~12',
					'after_body_code'=>'c4~12~Mainly used for analytical purpose',
				]);
		$form->addField('title')->set($extra_info['title']);
		$form->addField('meta_keyword')->set($extra_info['meta_keyword']);
		$form->addField('text','meta_description')->set($extra_info['meta_description'])->addClass('xepan-push');
		$form->addField('text','after_body_code')->set($extra_info['after_body_code'])->addClass('xepan-push');
		$form->addSubmit('Save')->addClass('btn btn-primary btn-block');
		if($form->isSubmitted()){
			$extra_info['title'] = $form['title']; 
			$extra_info['meta_keyword'] = $form['meta_keyword'];
			$extra_info['meta_description'] = $form['meta_description'];
			$extra_info['after_body_code'] = $form['after_body_code'];

			$epan['extra_info'] = json_encode($extra_info);
			$epan->save();
			return $form->js()->univ()->successMessage('Done')->execute();
		}

		$menu_model = $this->add('xepan\cms\Model_MenuGroup');
		$menu_crud = $menu_tab->add('xepan\hr\CRUD');
		
		if($menu_crud->isEditing()){
			$form = $menu_crud->form;
			foreach ( $this->add('xepan\cms\Model_Page') as $page) {
			 	$field = $form->addField('checkbox',$this->app->normalizeName($page['name']),$page['name']);
			}
		}
		
		$menu_crud->addHook('formSubmit',function($c,$cf){
			$temp = $cf->getAllFields();
			$cf->model['name'] = $temp['name'];
			unset($temp['name']);
			$cf->model['pages'] = $temp;
			$cf->model->save();
			return true; // do not proceed with default crud form submit behaviour
		});
		
		$menu_crud->setModel($menu_model);
		


		if($menu_crud->isEditing('edit')){
			$form = $menu_crud->form;
			foreach ( $this->add('xepan\cms\Model_Page') as $page) {
				$page_name = $this->app->normalizeName($page['name']);
			 	$field = $form->getElement($page_name);
			 	if(isset($menu_crud->model['pages'][$page_name]) && $menu_crud->model['pages'][$page_name])
			 		$field->set(true);
			}
		}
	}

	function page_getpage(){

		$page = $this->add('xepan\cms\Model_Page')
            	->addCondition('is_active',true)
            	;
        if($gid = $_GET['group_id']){
        	$mg = $this->add('xepan\cms\Model_MenuGroup');
        	$mg->load($gid);
        }
        $drop_down = "";
        // $drop_down = '<ul class="dropdown-menu">';
        foreach ($page as $p) {
        	if($gid && !in_array($this->app->normalizeName($p['name']), array_keys($mg['pages']) )) continue;
            $url = $this->app->url($p['path']);
            $drop_down .= '<li><a href="(string)$url">'.$p['name'].'</a></li>';
        }
        // $drop_down .= "</ul>";
        echo $drop_down;
		exit;
	}
}