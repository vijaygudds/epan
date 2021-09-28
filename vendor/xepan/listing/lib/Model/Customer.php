<?php

namespace xepan\listing;

class Model_Customer extends \xepan\base\Model_Contact{

	function init(){
		parent::init();

	}	

	function isPremiumUser(){
		return false;
	}
}