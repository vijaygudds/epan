<?php

/**
* description: Marketplace is like center repository for all xEpans. 
* New components/applications can be added to xEpan system. That will be available to install 
* (Free,Shareware or Paid depending on author of application) for any Epan in xEpan Platform.
* 
* @author : Gowrav Vishwakarma
* @email : gowravvishwakarma@gmail.com, info@xavoc.com
* @website : http://xepan.org
*/

namespace xepan\base;

class Model_Application extends \xepan\base\Model_Table{
	public $table='application';

	function init(){
		parent::init();

		$this->addField('name')->mandatory(true)->hint('Identification of xEpan Application');
		$this->addField('namespace')->mandatory(true)->hint('Identification of xEpan Application');
		$this->addField('user_installable')->type('boolean')->defaultValue(true);
		$this->addField('default_currency_price')->type('money');

		$this->hasMany('xepan\base\Epan_InstalledApplication',null,null,'Installations');

		$this->is([
				'name|unique|to_trim|required'
			]);
	}

	function validateRequirements($apps_selected){
		return false;
	}
}
