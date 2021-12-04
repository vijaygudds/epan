<?php

/**
* description: ATK Page
* 
* @author : Gowrav Vishwakarma
* @email : gowravvishwakarma@gmail.com, info@xavoc.com
* @website : http://xavoc.com
* 
*/

namespace xepan\base;

class page_menudesigner extends \xepan\base\Page{
	public $title='Menu Designer';

	public $breadcrumb=['Dashboard'=>'index','Menu Manager'=>'xepan_base_menudesigner'];

	function init(){
		parent::init();
		
		if(!$this->app->auth->model->isSuperUser()){
			$this->add('View_Error')->set('You are not authorized, only super user can do..');
			return;
		}
			
		$this->model = $this->add("xepan\base\Model_Config_Menus");
		$this->model->tryLoadAny();
	}

	function page_index(){
		if(!$this->app->auth->model->isSuperUser()){
			// $this->add('View_Error')->set('You are not authorized, only super user can do..');
			return;
		}

		$tab = $this->add('Tabs');
		$menu = $tab->addTab('Menu');
		$menu_set = $tab->addTab('Menu Set');

		$crud = $menu->add('xepan\hr\CRUD');
		$this->model->addCondition('is_set','!=',true);
		$crud->setModel($this->model,['id','name']);
		$crud->grid->addColumn('Button','design');
		if($_GET['design']){
			$this->app->redirect($this->app->url('./design',['designid'=>$_GET['design']]));
		}
		$crud->grid->removeColumn('id');
		$crud->noAttachment();
		$crud->grid->removeColumn('action');


		$crud_set = $menu_set->add('xepan\hr\CRUD');
		$model_set = $this->add('xepan\base\Model_Config_Menus');
		$model_set->addCondition('is_set','=',true);
		$model_set->getElement('is_set')->defaultValue(true);

		$model_set->addHook('beforeSave',function($m){
			if($m['name']=='XEC_DEFAULT') throw $this->exception('XEC_DEFAULT is not allowed to edit');
		},[],2);
		


		$crud_set->setModel($model_set,['id','name','sub_menus','is_set']);
		if($crud_set->isEditing()){

			$c_model = $this->add("xepan\base\Model_Config_Menus");
			$c_model->addCondition('is_set','!=',true);
			$c_model->tryLoadAny();
			$menu_names=[];

			foreach ($c_model as $m) {
				$menu_names[] = $m['name'];
			}

			$crud_set->form->getElement('sub_menus')
						->enableMultiSelect()
						->setValueList(array_combine($menu_names, $menu_names))
						;
			if($crud_set->isEditing('edit')){
				$crud_set->form->getElement('sub_menus')
					->set(explode(",",$crud_set->form->model['sub_menus']));
			}
		}

		$bottom_btn_set= $this->add('ButtonSet');
		$xec_def_btn = $bottom_btn_set->add('Button')->set('Re-Genrate XEC Default Menu')->addClass('btn btn-primary');
		$bottom_btn_set->add('Button')->set('Manage Posts')->addClass('btn btn-primary')->js('click')->univ()->location($this->app->url('xepan_hr_post'));
		
		if($xec_def_btn->isClicked()){
			$this->app->xepan_app_initiators['xepan\base']->generateXECDefaultMenus();
			$this->js()->univ()->successMessage('XEC Default Menus Re-Updted')->execute();
		}

		$crud_set->grid->removeColumn('id');
		$crud_set->noAttachment();
		$crud_set->grid->removeColumn('action');
	}

	function page_design(){
		$id = $this->app->stickyGET('designid');
		$this->model->load($id);
		
		$available_menus = [];
		foreach($this->app->xepan_app_initiators as $app_namespace =>$app_inits){
            if($this->app->getConfig('hidden_'.str_replace("\\", "_", $app_namespace),false)){
                continue;
            }

            if($app_inits->hasMethod('getTopApplicationMenu')){
                $arr = $app_inits->getTopApplicationMenu();

                foreach ($arr as $menu_name => $menu_array) {
                	if(!isset($available_menus[$menu_name])) $available_menus[$menu_name]=[];
                	
                	foreach ($menu_array as $key => $menu) {
                		$available_menus[$menu_name][] = $menu;
                	}
                }
                // $available_menus = array_merge($available_menus,$arr);
            }
        }
        
        $saved_menus = json_decode($this->model['value'],true);
        $top_menu_caption = $this->model['name'];

        if(is_array($saved_menus) && count(array_keys($saved_menus)))
        	$top_menu_caption = array_keys($saved_menus)[0];

        $v = $this->add('View');
        $v->js(true)
        	->_load('jquery.livequery')
        	->_load('menudesigner')->menudesigner([
        			'designing_menu'=>$this->model['name'],
        			'available_menus'=>$available_menus,
        			'saved_menus'=>$saved_menus?:[],
        			'top_menu_caption'=>$top_menu_caption
        		]);
	}

	function page_save(){
		$menulist = $_POST['menulist'];
		
		$menu_name = urldecode($_POST['menuname']);
		$this->model->addCondition('name','=',$menu_name);
		$this->model->tryLoadAny();

		$this->model['value'] = $menulist;
		$this->model->save();

		echo $this->app->js()->univ()->successMessage('saved successfully');
		exit;
	}
}

