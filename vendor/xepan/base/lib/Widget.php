<?php

namespace xepan\base;


class Widget extends \View{

	public $filter_form = null;
	public $report=null;
	public $report_type=null;

	public $chart=null;

	function init(){
		parent::init();
		$this->set('Base Widget');
		$type_fld = $this->report->enableFilterEntity('report_type');
		if($type_fld)
			$type_fld->setValueList(['report'=>'Report','chart'=>'Graphical Chart']);
	}

	function setFilterForm($form){
		$this->filter_form = $form;
		return $this;
	}

	function recursiveRender(){
		if($this->isChart() && $this->chart){
			$this->chart->onRender($this->js()->masonry(['itemSelector'=>'.widget'])->_selector('.widget-grid')->_enclose());
		}
		return parent::recursiveRender();
	}

	function isChart(){		
		return @$this->report->report_type === 'chart';
	}

}