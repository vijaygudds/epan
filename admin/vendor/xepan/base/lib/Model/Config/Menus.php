<?php

namespace xepan\base;


class Model_Config_Menus extends \xepan\base\Model_ConfigJsonModel{
	public $fields =[
						'value'=>'Text',
						'name'=>'Line',
						'is_set'=>'CheckBox',
						'sub_menus'=>'Text'
					];
	public $config_key = 'CustomMenuSystem';
	public $application='base';

	public $actions=['All'=>['edit','delete','view']];

	function init(){
		parent::init();

		// XEC_DEFAULT is a reserve system name not alowed for custom menus
		// DEFAULT can only be saved just once (name should be unique anyway)
		// is_set defined if a menu is collection of other menus
		$this->getElement('sub_menus')->display(['form'=>'xepan\base\NoValidateDropDown']);

		$this->addHook('beforeSave',[$this,'updateSetValue'],[],3);
	}

	function updateSetValue(){
		if($this['name'] == "XEC_DEFAULT") return;

		if($this['is_set']){
			$sub_menu_array = [];
			foreach (explode(",", $this['sub_menus']) as $sub_menu) {
                $menu_config = $this->add('xepan\base\Model_Config_Menus')
                                ->addCondition('name',$sub_menu)
                                ->tryLoadAny();
                if($menu_config->loaded()){
                    $arr = json_decode($menu_config['value'],true);
                    if(is_array($arr))
                        $sub_menu_array = array_merge($sub_menu_array, $arr);
                }
            }

        	$this['value'] = json_encode($sub_menu_array);
		}
	}

}