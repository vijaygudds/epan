<?php


class View_Lister_Blog extends \CompleteLister{
	//Display or hide the Load More Button
	public $loadmore = false;

	function init(){
		parent::init();

	}

	function setModel($model,$fields=null){
		// $model->setOrder('name');
		parent::setModel($model,$fields);

		if($this->model->count()->getOne() > 0){
			$this->template->tryDel('no_record_found');
		}

		if(!$this->loadmore)
			$this->template->tryDel('loadmore');

	}
	
	function formatRow(){
		if(!$this->current_row['image'])
                $this->current_row['image'] = "images/blog/1.jpg";
	}

	function defaultTemplate(){
		return array('page/home','svc_blog');
	}
}
