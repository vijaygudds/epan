<?php

namespace xepan\base;

class Controller_TopBarStatusFilter extends \AbstractController{
	public $add_all=true;

	public $extra_conditions=null;

	function init(){
		parent::init();
		
		if(!$this->owner instanceof \SQL_Model)
			throw $this->exception('Please add SideBarStatusFilter Controller on main model of page only')
						->addMoreInfo('current_owner',$this->owner);

		$status = $this->api->stickyGET('status');

		if($this->app->isAjaxOutput()){
			if($status){
				$this->owner->addCondition('status','in',explode(",",$status));
			}
			return;
		} 


		$count_m = $this->owner->owner->add(get_class($this->owner));
		if($this->extra_conditions) {
			$count_m->addCondition($this->extra_conditions);
		}

		if(@$this->app->branch->id AND $count_m->hasElement('branch_id')){
			$count_m->addCondition('branch_id',$this->app->branch->id);
		}

		$counts = $count_m->_dsql()->del('fields')->field('status')->field('count(*) counts')->group('Status')->get();
		$counts_redefined = [];
		$total = 0;
		foreach ($counts as $cnt) {
			$counts_redefined[$cnt['status']] = $cnt['counts'];
			$total += $cnt['counts'];
		}

		$icon_array = $this->app->status_icon;
		$model_class = get_class($this->owner);
		if($this->add_all){
			$class='primary';
			if(!$status) $class='success';
			$this->app->page_top_right_button_set->addButton(["All ($total)",'icon'=>$icon_array[$model_class]['All']])
				->addClass('btn btn-'.$class)
				->js('click')->univ()->location($this->api->url(null,['status'=>null]))
				;
			// $this->app->side_menu->addItem(['All','icon'=>$icon_array[$model_class]['All'],'badge'=>[$total,'swatch'=>' label label-primary label-circle pull-right']],$this->api->url(null,['status'=>null]),['status'])->setAttr(['title'=>'All']);
		}

		foreach ($this->owner->status as $s) {
			$class='primary';
			if(in_array($s, explode(',',$status))) $class='success';
			$this->app->page_top_right_button_set->addButton(["$s ($counts_redefined[$s])",'icon'=>$icon_array[$model_class]['All']])
					->addClass('btn btn-'.$class)
					->js('click')->univ()->location($this->api->url(null,['status'=>$s]))
					;
			// $this->app->side_menu->addItem([$s,'icon'=>$icon_array[$model_class][$s],'badge'=>[$counts_redefined[$s],'swatch'=>' label label-primary label-circle pull-right']],$this->api->url(null,['status'=>$s]),['status'])->setAttr(['title'=>$s]);
		}

		if($status){
			$this->owner->addCondition('status','in',explode(",",$status));
			$this->owner->owner->title .= ' ['.$status .' :'. $counts_redefined[$status] .']';
		}
	}
}