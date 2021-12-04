<?php

namespace xepan\base;

class Tool_Location extends \xepan\cms\View_Tool{
	public $options = [
				"show_country_name"=>true,
				"show_state_name"=>true,
				'custom_template'=>'location'
			];	
	function init(){
		parent::init();
		
		if(isset($_COOKIE['xepan_state_cookies']) AND isset($_COOKIE['xepan_country_cookies'])) {
			$c_model = $this->add('xepan\base\Model_Country')->load($_COOKIE['xepan_country_cookies']);
			$s_model = $this->add('xepan\base\Model_State')->load($_COOKIE['xepan_state_cookies']);
			$this->app->memorize('xepan-customer-current-country',$c_model);
			$this->app->memorize('xepan-customer-current-state',$s_model);
			$this->app->country = $c_model;
			$this->app->state = $s_model;
		}

		// $this->app->country = $this->add('xepan\base\Model_Country')->load(100); 
		// $this->app->state = $this->add('xepan\base\Model_State')->load(95);

		$country_model = $this->add('xepan\base\Model_Country')
							->addCondition('status','Active')
							->setOrder('name','asc');

		$state_model = $this->add('xepan\base\Model_State');
		// $state_model->addExpression('country_status')->set($state_model->refSQL('country_id')->fieldQuery('status'));
		$state_model->addCondition('status','Active');
		$state_model->addCondition('country_status','Active');
		$state_model->setOrder('name','asc');

		$form = $this->add('Form',null,'form_layout');
		$form->setLayout(['view\tool\location\location','form_layout']);

		$country_field = $form->addField("xepan\base\DropDownNormal","country");
		$country_field->validate('required');

		$state_field = $form->addField("xepan\base\DropDownNormal","state");
		$state_field->validate('required');
		
		$country_field->setModel($country_model);
		$country_field->setEmptyText("Please Select Your Country");

		$selected_country_id = 0;
		if( isset($this->app->country) and ($this->app->country instanceof \xepan\base\Model_Country)){
			$country_field->set($this->app->country->id);
			$selected_country_id = $this->app->country->id;
			
			if($this->options['show_country_name'])
				$this->template->trySet('country_name',$this->app->country['name']);
		}

		if($_GET['location_country_id']){
			$selected_country_id = $_GET['location_country_id'];
		}


		if($selected_country_id){
			$state_model->addCondition('country_id',$selected_country_id);
		}

		$state_field->setEmptyText("Please Select State");
		$state_field->setModel($state_model);

		if(isset($this->app->state) and ($this->app->state instanceof \xepan\base\Model_State)){
			$selected_state_id = $this->app->state->id;
			if(!$_GET['location_country_id']){
				$state_field->set($selected_state_id);
			}
			if($this->options['show_state_name'])
				$this->template->trySet('state_name',$this->app->state['name']);
		}

		// Save Button
		$save_btn = $this->add('Button',null,"save_button");
		$save_btn->set("Update")
			->setAttr(array("type"=>"button"))
			->addClass('btn btn-primary')
			->setStyle('margin-top',0);
		$save_btn->js('click',$this->js()->find('.atk-form')->atk4_form("submitForm"));

		$country_field->js('change',$state_field->js()->reload(null,null,[$this->app->url(null,['cut_object'=>$state_field->name]),'location_country_id'=>$country_field->js()->val()]));

		// Form Submission
		if($form->isSubmitted()){
			$c_model = $this->add('xepan\base\Model_Country')->load($form['country']);
			$s_model = $this->add('xepan\base\Model_State')->load($form['state']);

			$this->app->memorize('xepan-customer-current-country',$c_model);
			$this->app->memorize('xepan-customer-current-state',$s_model);
			$this->app->country = $c_model;
			$this->app->state = $s_model;
			
			setcookie('xepan_state_cookies',$form['state'], time()+31556926 ,'/' );
			setcookie('xepan_country_cookies',$form['country'], time()+31556926 ,'/');;
			// throw new \Exception(setcookie($cookie_state, $cookie_country), 1);
			$this->app->redirect($_SERVER['HTTP_REFERER']);
			
		}
		// if($this->app->country or $this->app->state)
		// $this->template->tryDel('location_fetcher_wrapper');
		// $this->js(true)->_library('navigator')->geolocation->getCurrentPosition($this->js(null,'$.ajax({url:})'))->_enclose());
	}

	function defaultTemplate(){

		if($temp = $this->options['custom_template']){
			$path = getcwd()."/websites/".$this->app->current_website_name."/www/view/tool/location/".$this->options['custom_template'].".html";
			if(!file_exists($path)){
				$temp = 'location';
				$this->add('View_Warning')->set('template not found');
			}
		}
		return['view\tool\location\/'.$temp];
	}

	function render(){
		parent::render();
		if(!$this->app->country->id and !$this->app->state->id)
			$this->js(true)->_selector('.xepan-location-tool')->trigger('click');
	}
}