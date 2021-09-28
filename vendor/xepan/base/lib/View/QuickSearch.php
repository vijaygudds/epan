<?php

namespace xepan\base;


class View_QuickSearch extends \View {
	
	function init(){
		parent::init();

		$f = $this->add('Form',null,null,['form/minimal']);
		$f->addField('Line','search_xepan','search xEpan');
		$f->addSubmit('Search')->addClass('btn btn-primary');

		$result_view = $this->add('CompleteLister',null,null,['view\quicksearch']);
		$result_array=[];
		
		if($_GET[$f->name.'_term']){
			$search_string = $_GET[$f->name.'_term'];
						
			$relevency_mode = 'IN NATURAL LANGUAGE MODE';
			$this->app->hook('quick_searched',[$search_string,&$result_array,$relevency_mode]);
		}

		usort($result_array, [$this,'sortByRelevence']);

		$result_view->setSource($result_array);

		if($f->isSubmitted()){
			$result_view->js()->reload([$f->name.'_term'=>$f['search_xepan']])->execute();
		}

	}

	function sortByRelevence($a,$b){
		return $a['relevency'] < $b['relevency'];
	}
}