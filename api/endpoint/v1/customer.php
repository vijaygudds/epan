<?php


class endpoint_v1_customer extends Endpoint_REST {
    public $model_class = 'xepan\commerce\Customer';
    public $allow_add = true;

    function post($data){
    	$m=$this->model;

        if($m->loaded()){
            if(!$this->allow_edit)throw $this->exception('Editing is not allowed');
            $data=$this->_input($data, $this->allow_edit);
        }else{
            if(!$this->allow_add)throw $this->exception('Adding is not allowed');
            $data=$this->_input($data, $this->allow_add);
        }

        $out_data = $m->set($data)->save()->get();
    	if(isset($data['phone_numbers'])){
    		foreach (explode(",", $data['phone_numbers']) as $num) {
				$this->model->addPhone(trim($num));
    		}
		}

		if(isset($data['email_ids'])){
    		foreach (explode(",", $data['email_ids']) as $email) {
				$this->model->addEmail(trim($email));
    		}
		}

        return $this->outputOne($out_data);
    }
}