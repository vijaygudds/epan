<?php

namespace xepan\listing;

class Model_List extends \xepan\base\Model_Table{
	public $table='list';
	public $status = ['Published','UnPublished'];
	public $actions = [
					'Active'=>['view','fields','data','filters','list_data_set','manage_layouts','manage_status_activity','plan_management','plan_associate_with_user','category','configuration','edit','delete','deactivate'],
					'Inactive'=>['view','fields','data','filters','list_data_set','manage_status_activity','category','plan_management','edit','delete','activate']
				];

	public $acl_type = "Listing\List";
	function init(){
		parent::init();

		$this->addField('name');
		$this->addField('list_data_status')->type('text')->hint('comma (,) seperated multiple values i.e. Pending,Approved,Rejected');
		$this->addField('status')->enum(['Active','InActive'])->defaultValue('Active');
		$this->addField('list_data_download_layout')->type('text')->display(['form'=>'xepan\base\RichText']);
		$this->addField('list_data_print_layout')->type('text')->display(['form'=>'xepan\base\RichText']);
		$this->addField('related_list_data_print_layout')->type('text')->display(['form'=>'xepan\base\RichText']);

		$this->is(['name|to_trim|required']);

		$this->hasMany('xepan\listing\Category','list_id');

		$this->add('dynamic_model\Controller_AutoCreator');

		$this->addHook('beforeSave',$this);
		$this->addHook('beforeDelete',$this);
	}

	function beforeSave(){
		if($this->checkDuplicate()){
			throw $this->exception('name is already taken','ValidityCheck')->setField('name');						
		}
		
		// if has id and is dirty
			// update table name 
		// if not id
			// create table
		$this->updateDB();
	}

	function beforeDelete(){
		$this->deleteTable();

	}

	function deleteTable(){
		$query = 'DROP TABLE IF EXISTS '.$this->getTableName().";";
		$this->app->db->dsql()->expr($query)->execute();
	}

	function checkDuplicate(){
		$list = $this->add('xepan\listing\Model_List');
		$list->addCondition('name',$this['name']);
		$list->addCondition('id','<>',$this->id);
		return $list->count()->getOne();
	}

	function updateDB(){
		if($this->loaded() && !$this->isDirty('name')) return;

		$table_name = $this->getTableName();
		// creating new table
		$query = 'CREATE TABLE `'.$table_name.'` ( `id` int(11) NOT NULL AUTO_INCREMENT, `created_at` datetime, `updated_at` datetime, `created_by_id` int,`status` varchar(255) ,PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=latin1;';
		if($this->loaded() && $this->isDirty('name')){
			$old_model = $this->add('xepan\listing\Model_List')->load($this->id);
			$old_table_name = 'xepan_listing_'.$this->app->normalizeName(strtolower($old_model['name']));
			$query = 'RENAME TABLE '.$old_table_name.' TO '.$table_name.';';
		}
		$this->app->db->dsql()->expr($query)->execute();
	}

	function getTableName(){
		return $table_name = 'xepan_listing_'.$this->app->normalizeName(strtolower($this['name']));
	}

	function page_category($page){
		$model = $this->add('xepan\listing\Model_Category');
		$model->addCondition('list_id',$this->id);

		$crud = $page->add('xepan\hr\CRUD');
		$crud->setModel($model);
		if($crud->isEditing()){
			$crud->form->getElement('parent_category_id')->getModel()->addCondition('list_id',$this->id);
		}
		$crud->grid->addQuickSearch(['name']);
		$crud->grid->addPaginator(15);
	}

	function filters(){
		$this->app->redirect($this->app->url('xepan_listing_filter',['list_id'=>$this->id]));
	} 
	// function page_filters($page){

	// 	$model = $this->add('xepan\listing\Model_Filter');
	// 	$crud = $page->add('xepan\hr\CRUD');
	// 	$crud->setModel($model);
	// 	$crud->grid->addPaginator(10);
	// }

	function deactivate(){
		$this['status'] = 'Inactive';
		$this->save();
	}

	function activate(){
		$this['status'] = 'Active';
		$this->save();
	}

	function page_fields($page){
		$model = $this->add('xepan\listing\Model_Fields');
		$model->addCondition('list_id',$this->id);
		$crud = $page->add('xepan\hr\CRUD');
		$form = $crud->form;
        $form->add('xepan\base\Controller_FLC')
        ->showLables(true)
        ->addContentSpot()
        // ->makePanelsCoppalsible(true)
        ->layout([
                'name'=>'Field Details~c1~6',
                'field_type'=>'c2~6',
                'default_value'=>'c11~6',
                'placeholder'=>'c21~6',
                'hint'=>'c12~6',
                'status'=>'c22~6',
                'is_mandatory'=>'c13~6',
                'is_moderate'=>'c23~6',
                'is_changable'=>'c13~6',
                'is_filterable'=>'c24~6',
                

            ]);
		$crud->setModel($model);

	}

	function data(){
		$this->app->redirect($this->app->url('xepan_listing_listdata',['listid'=>$this->id]));
	}

	function fields(){
		return $this->add('xepan\listing\Model_Fields')->addCondition('list_id',$this->id);
	}

	function getDataModel(){
		return $this->add('xepan\listing\Model_ListData',['listing'=>$this]);
	}

	function getLayoutArray($layout_id){
		$m = $this->add('xepan\listing\Model_ListDataFormLayout');
		return $m->load($layout_id)->getLayoutArray();
	}

	function page_manage_layouts($page){
		$m = $this->add('xepan\listing\Model_ListDataFormLayout');
		$m->addCondition('list_id',$this->id);
		$crud = $page->add('xepan\base\CRUD');
		$crud->setModel($m);

		$crud->form->add('View')->set(implode(", ",$this->getDataModel()->getActualFields())." ,categories");
	}
		
	function list_data_set(){
		$this->app->redirect($this->app->url('xepan_listing_listdataset',['list_id'=>$this->id]));
	}

	function manage_status_activity(){
		$this->app->redirect($this->app->url('xepan_listing_liststatusactivities',['list_id'=>$this->id]));
	}

	function plan_management(){
		$this->app->redirect($this->app->url('xepan_listing_plan',['list_id'=>$this->id]));
	}

	function page_plan_associate_with_user($page){
		$this->app->stickyGET('list_id');
		$model = $page->add('xepan\listing\Model_ContactPlanAssociation');
		$model->addCondition('list_id',$this->id);

		$model->getElement('plan_id')->getModel()->addCondition('list_id',$this->id);
		$crud = $page->add('xepan\hr\CRUD');
		$crud->setModel($model);
		$crud->grid->addPaginator(50);
	}

	function page_configuration($page){
		$tab = $page->add('Tabs');
		$dt = $tab->addTab('List Data Download Layout');
		$pl = $tab->addTab('List Data Print Layout');

		$form = $dt->add('Form');
		$form->addField('xepan\base\RichText','list_data_download_layout')->set($this['list_data_download_layout']);
		$form->addSubmit('Submit');
		$form->add('View')->set(implode(", ",$this->getDataModel()->getActualFields()));
		if($form->isSubmitted()){
			$this['list_data_download_layout'] = $form['list_data_download_layout'];
			$this->save();
			$this->app->page_action_result = $this->app->js(null,$page->js()->univ()->closeDialog())->univ()->successMessage('Updated Successfully');
		}

		$form_pl = $pl->add('Form');
		$pl_field = $form_pl->addField('xepan\base\RichText','list_data_print_layout');
		$pl_field->set($this['list_data_print_layout']);
		$rpl_field = $form_pl->addField('xepan\base\RichText','related_list_data_print_layout');
		$rpl_field->set($this['related_list_data_print_layout']);

		$form_pl->addSubmit('Submit');
		if($form_pl->isSubmitted()){
			$this['list_data_print_layout'] = $form_pl['list_data_print_layout'];
			$this['related_list_data_print_layout'] = $form_pl['related_list_data_print_layout'];
			$this->save();
			$this->app->page_action_result = $this->app->js(null,$page->js()->univ()->closeDialog())->univ()->successMessage('Updated Successfully');
		}

	}
}	