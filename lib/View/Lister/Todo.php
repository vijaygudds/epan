<?php


class View_Lister_Todo extends \CompleteLister{
	//Display or hide the Load More Button
	public $title = "My Todo List";
	public $title_icon = "ion ion-clipboard";
	public $add_btn_name = "Add Item";
	public $add_btn_name_icon = "fa fa-plus";

	function init(){
		parent::init();

		$vp_edit = $this->add('VirtualPage');
 		
 		// $this->js(true)->univ()->frameURL('MyPopup',$vp->getURL());

 		$vp_edit->set(function($vp_edit){
			$vp_edit->add('View_Info')->set($_GET['row_id']);
		});

		$this->js('click',$this->js()->univ()->frameURL('Edit Todo',[$vp_edit->getURL(),'row_id'=>$this->js()->_selectorThis()->closest('li')->attr('data-id')]))->_selector('.todo-edit');
		// $this->js()->univ()->alert($this->js()->_selectorThis()->closest('li')->attr('data-id'))
		$this->on('click','.svc-todo-add',function($js,$data){
			return $js->univ()->frameURL('Add Rule',$this->api->url('addrule'));
		});

	}

	function setModel($model,$fields=null){
		// $model->setOrder('name');
		parent::setModel($model,$fields);
		
		if($this->model->count()->getOne() > 0){
			$this->template->tryDel('no_record_found');
		}
		$paginator = $this->add('Paginator',null,'paginator');
		$paginator->setRowsPerPage($rows=3);

	}
	
	function formatRow(){

	}

	function render(){
		$this->template->setHtml('title',$this->title);
		$this->template->setHtml('title_icon',$this->title_icon);
		parent::render();
	}

	function defaultTemplate(){
		return array('view/todo');
	}
}
