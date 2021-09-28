<?php

namespace xepan\base;

class Controller_MultiDelete extends \AbstractController {
	public $grid;
	public $form;
	public $model;
	public $deleted_record = [];
	public $not_delete_record = [];

	function init(){
        parent::init();
        
        // if(!$this->app->auth->model->isSuperUser()) return;

        return;
        if($this->owner instanceof \CRUD ){
        	if($this->owner->isEditing())
        		return;
        	$this->grid = $this->owner->grid;
        	if($this->owner->isEditing()) return;
        }

        if($this->owner instanceof \Grid ){
        	$this->grid = $this->owner;
        }

        if(!$this->grid)
        	throw $this->exception('controller applicable on CRUD or Grid Only');
    	
    	
    	if(!$this->grid->model or !$this->grid->model instanceof \AbstractModel)
        	throw $this->exception('add controller after set model');
		
		$this->model = $this->grid->model;

		$this->form = $this->grid->add('Form',null,'grid_buttons');
		$record_tobe_delete_field = $this->form->addField('hidden','record_tobe_delete');
		$this->form->addSubmit('Delete All Record');

		$this->grid->addSelectable($record_tobe_delete_field);
		
		$model_class = get_class($this->model);
		

		if($this->form->isSubmitted()){
			if(!$this->form['record_tobe_delete'])
				$this->form->js()->univ()->errorMessage('please select at least one record to delete')->execute();

			// delete all selected record one by one
			$selected_record_for_delete = json_decode($this->form['record_tobe_delete'],true);
			$model = $this->add($model_class);

			foreach ($selected_record_for_delete as $key => $record_id) {
				try{
					$this->api->db->beginTransaction();
					$model->load($record_id);
					$model->delete();
					$this->deleted_record[$record_id] = $record_id;
					$this->api->db->commit();
				
				}catch(\Exception $e){
					$this->api->db->rollback();
					
					// unset record_id from delete_record array
					if(isset($this->deleted_record[$record_id]))
						unset($this->deleted_record[$record_id]);

					$this->not_delete_record[$record_id] = $record_id;
				}

			}

			// create activity of total record deleted

			$js = [$this->form->js()->reload(),$this->owner->js()->reload()];

			$total_record_delete = count($this->deleted_record)?:0;
			$total_record_not_delete = count($this->not_delete_record)?:0;
			
			if($total_record_not_delete)
				$js[] = $this->form->js()->univ()->errorMessage(count($this->not_delete_record)." records not delete");
			
			if($total_record_delete)
				$js[] = $this->form->js()->univ()->successMessage(" ".count($this->deleted_record)." records deleted");

			$this->form->js(null,$js)->univ()->execute();
		}
    }
}