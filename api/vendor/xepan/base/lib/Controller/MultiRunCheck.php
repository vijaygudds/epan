<?php

namespace xepan\base;


class Controller_MultiRunCheck extends \AbstractController{
	function init(){
		parent::init();
	}

	function checkCall($number=null){
		if(!isset($this->app->call_check)) $this->app->call_check = 1;
		echo "Called ". $this->app->call_check .'<br/>';

		if($number && $this->app->call_check == $number){
			throw new \Exception("Called ".$this->app->call_check." == $number time now", 1);
		}

		$this->app->call_check++;
	}
}