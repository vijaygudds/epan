<?php
namespace xepan\base;
class View_User_MicroLogin extends \View{
	public $options = [];
	function init(){
		parent::init();
		

		if($this->app->auth->isLoggedIn()){
			$customer_id = $this->app->auth->model['id'];
			$customer = $this->add('xepan/commerce/Model_Customer')->addCondition('user_id',$customer_id);
			$customer->tryLoadAny();
			
			$contact = $this->add('xepan\base\Model_Contact')->addCondition('user_id',$this->app->auth->model->id);
			$contact->tryLoadAny();

			$this->template->tryDel('login_wrapper');
			$this->template->trySet('logout_url',$this->app->url($this->options['logout_page']));
			$this->template->trySet('name',$customer['first_name']);
				
			$temp = [];
			foreach ($contact->data as $key => $value) {
				$temp["contact_".$key] = $value;
			}

			foreach ($customer->data as $key => $value) {
				$temp["customer_".$key] = $value;
			}

			$data = array_merge($temp, $this->app->auth->model->data);
			$this->template->set($data);
			
			// $this->setModel($this->app->auth->model);
		}
		else{
			$this->template->tryDel('logout_wrapper');
			$this->template->trySet('login_url',$this->app->url($this->options['login_page']));
		}
		
		$this->template->trySet('member_panel_url',$this->app->url($this->options['member_panel_page']));
	}

	function defaultTemplate(){		
		return ['view/tool/userpanel/form/micrologin'];
	}
}			