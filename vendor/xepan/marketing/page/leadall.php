<?php

namespace xepan\marketing;
	
class page_leadall extends \xepan\base\Page{
	public $title = "Lead";
	public $content=null;
	public $model_class;
	public $crud;
	public $filter_form;

	function page_index(){
		$grid = $this->add('xepan\hr\Grid');
		$lead = $this->add('xepan\marketing\Model_Lead');
		$lead->addCondition('type','<>','Employee');
		$cinfo_j  = $lead->join('contact_info.contact_id');
		$cinfo_j->addField('value');
		$lead->addCondition('first_name','Not like', "Udr%");
		$lead->addCondition('first_name','Not like', "Jhd%");
		$lead->addCondition('first_name','Not like', "Gog%");
		$lead->addCondition('first_name','Not like', "Set%");
		$lead->addCondition('first_name','Not like', "See%");
		$lead->addCondition('first_name','Not like', "Ktr%");
		$lead->addCondition('first_name','Not like', "Ogn%");
		$lead->addCondition('first_name','Not like', "Slb%");
		$lead->addCondition('first_name','Not like', "Syr%");
		$lead->addCondition('first_name','Not like', "Sm%");
		// $lead->_dsql()->group('value');
		// $lead->_dsql()->group('first_name');
		// $lead->_dsql()->group('last_name');
		$grid->setModel($lead,['first_name','last_name','contacts_str','created_at','value']);
		$grid->addPaginator(50);
		$grid->addQuickSearch(['first_name','value']);
	}
}