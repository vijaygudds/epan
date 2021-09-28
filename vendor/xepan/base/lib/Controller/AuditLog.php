<?php

namespace xepan\base;

class Controller_AuditLog extends \AbstractController{

	public $skip_fields = [];
	public $extra_info=[];

	function init(){
		parent::init();

		if(!$this->owner instanceof \xepan\base\Model_Table){
			throw $this->exception('Owner of Controller_AuditLog must be a sql_model');
		}
		$this->skip_fields[] = "search_string";
		$this->skip_fields[] = "updated_at";

		$this->owner->addHook('beforeSave',function($model){
			if(@$model->app->skip_audit_log) return;
			
			if($model->loaded()){
				$old_m = $model->newInstance(['name'=>'audit_'.$model->name])->load($model->id);
				$changes=array();
				foreach ($model->dirty as $dirty_field=>$changed) {
					if(in_array($dirty_field, $this->skip_fields)) continue;

					if($old_m[$dirty_field] != $model[$dirty_field]){
						if( $old_m->hasElement($dirty_field) && strtolower($old_m->getElement($dirty_field)->type()) == 'datetime'){
							if(strtotime($old_m[$dirty_field]) == strtotime($model[$dirty_field])) continue;
						}
						$changes[$dirty_field]=array('from'=>$old_m[$dirty_field],'to'=>$model[$dirty_field]);
					}
				}

				if(!count($changes)) return;

				foreach ($this->extra_info as $field) {
					if(!isset($changes['extra_info'])) $changes['extra_info']=[];
					$changes['extra_info'][$field] = $model[$field];
				}

				$log = $model->add('xepan\base\Model_AuditLog');
				$log['model_class'] = get_class($model);
				$log['pk_id'] = $model->id;
				$log['name'] = json_encode($changes);
				$log['type'] = "Edit";
				$log['contact_id'] = @$this->app->employee->id;
				$log->save();
			}
		});

		$this->owner->addHook('beforeDelete',function($model){

				if(@$model->app->skip_audit_log) return;
				$log = $model->add('xepan\base\Model_AuditLog');
				$log['model_class'] = get_class($model);
				$log['pk_id'] = $model->id;
				$log['name'] = json_encode($model->data);
				$log['type'] = "Delete";
				$log['contact_id'] = @$this->app->employee->id;
				$log->save();
		});
	}
}