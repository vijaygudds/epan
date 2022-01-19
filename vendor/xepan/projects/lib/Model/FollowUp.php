<?php

namespace xepan\projects;

class Model_FollowUp extends Model_Task
{	

	public $status=['Pending','Submitted','Completed','Assigned','Inprogress'];
	public $force_delete = false;
	public $actions=[
		'Pending'=>['mark_complete','stop_recurrence','reset_deadline','stop_reminder'],
		'Inprogress'=>['mark_complete','stop_recurrence','stop_reminder'],
		'Assigned'=>['receive','reject','stop_recurrence','reset_deadline','stop_reminder'],
		'Submitted'=>['mark_complete','reopen','stop_recurrence','stop_reminder'],
		'Completed'=>['stop_recurrence']
	];

	function init(){
		parent::init();

		$this->addCondition('type','Followup');
	}

	// function page_mark_Complete($page){
	// 	if(!$this->loaded()){
	// 		throw new \Exception("Record Must be Loaded", 1);
	// 	}
	// 	$lead =$this->add('xepan\base\Model_Contact')->load($this['related_id']);
	// 	$com_m = $this->add('xepan\communication\Model_Communication');
	// 	$com_m->addCondition('from_id',$lead->id);
	// 	$com_m->setOrder('created_at','desc');
	// 	$com_m->setLimit(1);
	// 	$com_m->tryLoadany(); 
	// 	$comm = $page->add('xepan\communication\Form_Communication',['edit_communication_id'=>$com_m->id,'contact'=>$lead->id]);
	// 	$comm->setContact($lead);
	// 	$comm->addSubmit('Mark Completed Followup');
	// 	if($comm->isSubmitted()){
	// 		$com_m = $comm->process();

	// 		$reload_array=
	// 					[
	// 					$comm->js()->univ()->successMessage('Communication Created'),
	// 					// $view_conversation->js()->reload()						
	// 					];

	// 		$comm->js(null,$reload_array)->reload()->execute();
	// 	}

	// }


	function page_mark_Complete($p){
		if($this['type'] =='Followup'){

			// $btn = $p->add('Button')->set('Immediate Complete')->addClass('btn btn-primary xepan-push-large btn-block');
			
			// if($btn->isClicked()){				
			// 	$this->mark_complete();
			// 	$this->app->employee
			//             ->addActivity("Task '".$this['task_name']."' completed by '".$this->app->employee['name']."'",null, $this['assign_to_id'] /*Related Contact ID*/,null,null,null)
			//             ->notifyTo([$this['created_by_id'],$this['assign_to_id']],"Task : ".$this['task_name']."' marked Complete by '".$this->app->employee['name']."'");
			// 	return $this->app->page_action_result = $this->app->js(true,$p->js()->univ()->closeDialog())->_selector('.xepan-mini-task')->trigger('reload');
			// }

			$contact = $this->add('xepan\base\Model_Contact');
			$contact->tryLoad($this['related_id']);

			if($contact->loaded()){
				$p->add('View')->setClass('alert alert-info')->set('Add Communication with '. $contact['name_with_type']);
				$com_m = $this->add('xepan\communication\Model_Communication');
				$com_m->addCondition(
							$com_m->dsql()->orExpr()
  								->where('from_id',$contact->id)
  								->where('to_id',$contact->id));
				$com_m->setOrder('created_at','desc');
				$com_m->setLimit(1);
				$com_m->tryLoadany();
				$comm = $p->add('xepan\communication\View_CommunicationNew',['edit_communication_id'=>$com_m->id,'showFilter'=>false]);
				$comm->setCommunicationsWith($this->ref('related_id'));
				$comm->showCommunicationHistory(false);
				// $comm = $p->add('xepan\communication\Form_Communication',['edit_communication_id'=>$com_m->id,'contact'=>$this->ref('related_id')]);
				$comm->add('H5',null,'filter')->set('Complete Followup by creating Communication: What is status of This Followup');
				
				// return $this->app->page_action_result = $this->app->js(true,$p->js()->univ()->closeDialog())->_selector('.communication_lister, .view-pending-followup, .view-communication')->trigger('reload');


				$comm->addSuccessJs($this->app->js(null,$p->js()->univ()->closeDialog())->_selector('.view-pending-followup, .communication_lister')->trigger('reload'));
				$this->app->addHook('communication_created',function($app)use($p){
					$this->mark_complete();
				});
			}else{
				$p->add('View')->setClass('alert alert-danger')->set('Associated Contact not found or removed');
			}

		}else{
			$form = $p->add('Form');
			$form->addField('text','comment');
			$form->addField('checkbox','work_pending');
			$form->addSubmit('Save');
				
			if($form->isSubmitted()){			
				$this->mark_complete();
				if($form['work_pending']){
					$this['work_pending'] = true;
					$this->save();
				}
				// if($this['assign_to_id'] == $this['created_by_id']){
				// $this->app->employee
			 //            ->addActivity("Task '".$this['task_name']."' completed by '".$this->app->employee['name']."'",null, $this['assign_to_id'] /*Related Contact ID*/,null,null,null);
				// }else{
				// 	$this->app->employee
				//             ->addActivity("Task '".$this['task_name']."' completed by '".$this->app->employee['name']."'",null, $this['assign_to_id'] /*Related Contact ID*/,null,null,null)
				//             ->notifyTo([$this['created_by_id'],$this['assign_to_id']],"Task : ".$this['task_name']."' marked Complete by '".$this->app->employee['name']."'");
				// }

				return $this->app->page_action_result = $this->app->js(true,$p->js()->univ()->closeDialog())->_selector('.xepan-mini-task')->trigger('reload');
			}
		}

	}

	function mark_complete(){		
		// if($form instanceOf \xepan\communication\Form_Communication){			
		// 	$form->process();
		// }

		// if($form != null AND (!$form instanceOf \xepan\communication\Form_Communication)){
		// 	$comment = $this->add('xepan\projects\Model_Comment');
		// 	$comment['task_id'] = $this->id;
		// 	$comment['employee_id'] = $this->app->employee->id;
		// 	$comment['comment'] = $form['comment'];
		// 	$comment->save();
		// }

		$model_close_timesheet = $this->add('xepan\projects\Model_Timesheet');
		$model_close_timesheet->addCondition('task_id',$this->id);
		$model_close_timesheet->addCondition('employee_id',$this->app->employee->id);
		$model_close_timesheet->addCondition('endtime',null);
		$model_close_timesheet->tryLoadAny();

		if($model_close_timesheet->loaded()){
				$model_close_timesheet['endtime'] = $this->app->now;
				$model_close_timesheet->saveAndUnload();
		} 

		$this['status']='Completed';
		$this['updated_at']=$this->app->now;
		$this['completed_at']=$this->app->now;
		$this->save();
		
		if($this['assign_to_id'] == $this['created_by_id']){
			$this->app->employee
		            ->addActivity("Task '".$this['task_name']."' completed by '".$this->app->employee['name']."'",null, $this['assign_to_id'] /*Related Contact ID*/,null,null,null);
		}else{
			$this->app->employee
		            ->addActivity("Task '".$this['task_name']."' completed by '".$this->app->employee['name']."'",null, $this['assign_to_id'] /*Related Contact ID*/,null,null,null)
		            ->notifyTo([$this['created_by_id'],$this['assign_to_id']],"Task : ".$this['task_name']."' marked Complete by '".$this->app->employee['name']."'");
		}

	 	$this->app->page_action_result = $this->app->js()->_selector('.communication_lister, .view-pending-followup')->trigger('reload');
	}

}
