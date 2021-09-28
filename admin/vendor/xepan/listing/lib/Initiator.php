<?php

namespace xepan\listing;

class Initiator extends \Controller_Addon {
	
	public $addon_name = 'xepan_listing';

	function setup_admin(){

		$this->routePages('xepan_listing');
		$this->addLocation(array('template'=>'templates','js'=>'templates/js'))
			->setBaseURL('../vendor/xepan/listing/');

		if($this->app->inConfigurationMode)
            $this->populateConfigurationMenus();
        else
            $this->populateApplicationMenus();
		
		return $this;

	}

	function populateConfigurationMenus(){
	}

	function populateApplicationMenus(){
		if(!$this->app->getConfig('hidden_xepan_listing',false)){
			// $this->app->listing_menu = $this->app->top_menu->addMenu('Listing');
			// $this->app->listing_menu->addItem(['Contact','icon'=>' fa fa-sitemap'],'xepan_listing_contact');
			// $this->app->listing_menu->addItem(['Listing List','icon'=>' fa fa-file-text-o'],'xepan_listing_list');
			// $this->app->listing_menu->addItem(['Listing Fields','icon'=>' fa fa-file-text-o'],'xepan_listing_field');
		}
	}

	function getTopApplicationMenu(){
		return ['Listing'=>[
						[	'name'=>'Listing List',
							'icon'=>'fa fa-file-text-o',
							'url'=>'xepan_listing_list'
						]
					]
			];
	}

	function setup_pre_frontend(){

	}

	function setup_frontend(){

		$this->routePages('xepan_listing');
		$this->addLocation(array('template'=>'templates','js'=>'templates/js','css'=>'templates/css'))
			->setBaseURL('./vendor/xepan/listing/');

		$this->app->exportFrontEndTool('xepan\listing\Tool_Category','Listing');
		$this->app->exportFrontEndTool('xepan\listing\Tool_List','Listing');
		$this->app->exportFrontEndTool('xepan\listing\Tool_Filter','Listing');
		$this->app->exportFrontEndTool('xepan\listing\Tool_ListDetail','Listing');
		$this->app->exportFrontEndTool('xepan\listing\Tool_ListImage','Listing');
		$this->app->exportFrontEndTool('xepan\listing\Tool_ManageListData','Listing');

		$contact = $this->add('xepan\listing\Model_Contact');
		$this->app->addHook('userCreated',[$contact,'createContact']);

		return $this;
	}

	function resetDB(){
	}

}
