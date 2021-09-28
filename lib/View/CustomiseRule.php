<?php

class View_CustomiseRule extends View{

	function init(){
		parent::init();


	}

	function setModel($model){
		parent::setModel($model);
		if(!$this->model->loaded()){
			return;
		}

		$f = $this->add('Form');
		foreach ($this->model->ref('RuleOption') as $model) {
			if(preg_match_all("/{[^}]*}/",$model['gMarks'],$tags))
				foreach ($tags as $key => $value) {
					foreach ($value as $key_id => $value_name) {
						$f->addField($value_name)->validate('required');
					}					
				}

		}

		$f->addSubmit('Go');
		if($f->isSubmitted()){
			$m = $this->model;

			$rule = $this->add('Model_Rule');
			foreach ($m->getActualFields() as $field) {
				if(in_array($field,["id","is_template"])) continue;

				$rule[$field] = $m[$field];
			}

			$rule->save();

			foreach ($m->ref('RuleOption') as $rbo) {
				$ro = $this->add('Model_RuleOption');
				$ro['rule_id'] = $rule->id;
				foreach ($rbo->getActualFields() as $field) {
					if($field=="id") continue;

					if(preg_match_all("/{[^}]*}/",$rbo['gMarks'],$tags))
						foreach ($tags as $key => $values) {
							foreach ($values as $key => $value) {																
								$rbo[$field] = str_replace($value, $f[$this->api->normalizeName($value)], $rbo[$field]);
							}
						}
					
					$ro[$field] = $rbo[$field];	
				}
				$ro->saveAndUnload();
			}
			
			$this->api->stickyForget('rulebook_id');
			$f->js(null,$this->owner->js()->reload())->univ()->successMessage('ok')->execute();
		}

	}

}