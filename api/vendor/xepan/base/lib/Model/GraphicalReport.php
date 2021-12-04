<?php


namespace xepan\base;

class Model_GraphicalReport extends \xepan\base\Model_Table{
	public $table='graphical_report';
	public $widget_list = [];
	public $acl_type='GraphicalReport';
	public $status=['All'];
	public $actions=['All'=>['view','edit','delete','manage_widgets','manage_post_permissions']];

	function init(){
		parent::init();

		$this->hasOne('xepan\base\Contact','created_by_id');
		$this->addField('name');
		$this->addField('permitted_post');
		$this->addField('description')->type('text');
		$this->addField('is_system')->type('boolean')->defaultValue(false)->system(true);

		$this->hasMany('xepan\base\GraphicalReport_Widget','graphical_report_id');
		$this->addExpression('status','"All"');

		$this->addHook('beforeSave',$this);
	}

	function beforeSave(){
		if(!$this->loaded()){
			$c = $this->add('xepan\base\Model_Contact');
			$c->loadLoggedIn();
			$this['created_by_id'] = $c->id;
		}
	} 

	function page_manage_widgets($page){
		$this->app->hook('widget_collection',[&$this->widget_list]);
		$emp_scope = $this->app->employee->ref('post_id')->get('permission_level');

		$enum_array=[];
		foreach ($this->widget_list as $widget) {
			$to_add=false;
			switch($emp_scope) {
				case 'Global':
					$to_add =true;					
					break;
				case 'Department':
					if(in_array($widget['level'], ['Individual','Department'])) $to_add=true;
					break;
				case 'Sibling':
					if(in_array($widget['level'], ['Individual','Sibling'])) $to_add=true;
					break;
				case 'Individual':															
					if(in_array($widget['level'], ['Individual'])) $to_add=true;
					break;
			}

			if($to_add){
				$enum_array[$widget[0]] = $widget['title'];
			}				
				
		}

		$m = $page->add('xepan\base\Model_GraphicalReport_Widget');
		$m->getElement('class_path')->setValueList($enum_array);

		$m->addCondition('graphical_report_id',$this->id);
		$m->setOrder('order','asc');
		$c = $page->add('xepan\base\CRUD');
		$c->setModel($m);
	}

	function page_manage_post_permissions($page){
		$post = $this->add('xepan\hr\Model_Post');
		$post->addCondition('status','Active');

		$form = $page->add('Form');
		$post_field = $form->addField('xepan\base\DropDown','posts');
		$post_field->set(json_decode($this['permitted_post'],true));
		$post_field->setAttr(['multiple'=>'multiple']);
		$post_field->setModel($post);
		$post_field->addClass('xepan-push');
		$form->addSubmit('Permit Posts')->addClass('btn btn-primary');

		$permission_array = [];
		if($form->isSubmitted()){
			$permission_array = explode(',', $form['posts']);
			$this->manage_post_permissions($permission_array);
			return $page->js()->univ()->closeDialog();			
		}

	}

	function manage_post_permissions($permission_array){
		$this['permitted_post']= json_encode($permission_array);
		$this->save();		
		return true;
	}		

	function exportJson(){
		$data = $this->get();
		unset($data['id']);

		$data['widget']=[];
		foreach ($this->ref('xepan\base\GraphicalReport_Widget') as $widget) {
			$reportwidget_data = $widget->get();
			unset($reportwidget_data['id']);
			unset($reportwidget_data['graphical_report_id']);
			$data['widget'][] = $reportwidget_data;
		}
		return json_encode($data);
	}	

	function importJson($json){
		$data=json_decode($json,true);
		$graph_rprt_m=$this->add('xepan\base\Model_GraphicalReport');
		$graph_rprt_m['name']=$data['name'];
		$graph_rprt_m['permitted_post']=$data['permitted_post'];
		$graph_rprt_m['status']=$data['status'];
		$graph_rprt_m['description']=$data['description'];
		$graph_rprt_m['is_system']=true;
		$graph_rprt_m->save();

		foreach ($data['widget'] as $wgt) {
			$widget=$this->add('xepan\base\Model_GraphicalReport_Widget');
			$widget['graphical_report_id']=$graph_rprt_m->id;
			$widget['name']=$wgt['name'];
			$widget['col_width']=$wgt['col_width'];
			$widget['class_path']=$wgt['class_path'];
			$widget['order']=$wgt['order'];
			$widget['is_active']=$wgt['is_active'];
			$widget->save();
		}

	}
}