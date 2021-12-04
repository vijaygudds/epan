<?php


namespace xepan\base;

class Controller_SideBarStatusFilter extends \AbstractController{

	public $add_all_menu = true;
	public $add_status_to_sidebar = []; // if empty then all staus

	function init(){
		parent::init();

		if(!$this->owner instanceof \SQL_Model)
			throw $this->exception('Please add SideBarStatusFilter Controller on main model of page only')
						->addMoreInfo('current_owner',$this->owner);
		
		$this->add_status_to_sidebar = array_combine($this->add_status_to_sidebar, $this->add_status_to_sidebar);

		$count_model = $this->owner->owner->add(get_class($this->owner));
		$counts = $count_model->_dsql()->del('fields')->field('status')->field('count(*) counts')->group('status')->get();
		$counts_redefined =[];
		$total=0;

		foreach ($counts as $cnt) {
			$counts_redefined[$cnt['status']] = $cnt['counts'];
			if(count($this->add_status_to_sidebar) AND !isset($this->add_status_to_sidebar[$cnt['status']])) continue;

			$total += $cnt['counts'];
		}

		$icon_array = $this->app->status_icon;
		$model_class=get_class($this->owner);
		
		if($this->add_all_menu){
			$this->app->side_menu->addItem(['All','icon'=>$icon_array[$model_class]['All'],'badge'=>[$total,'swatch'=>' label label-primary label-circle pull-right']],$this->api->url(),['status','condition'])->setAttr(['title'=>'All']);
		}
		
		foreach ($this->owner->status as $s) {
			if(count($this->add_status_to_sidebar) AND !isset($this->add_status_to_sidebar[$s])) continue;

			$this->app->side_menu->addItem([$s,'icon'=>$icon_array[$model_class][$s],'badge'=>[$counts_redefined[$s],'swatch'=>' label label-primary label-circle pull-right']],$this->api->url(null,['status'=>$s]),['status'])->setAttr(['title'=>$s]);
		}

		if($status=$this->api->stickyGET('status')){
			$this->owner->addCondition('status',$status);
			$this->owner->owner->title .= ' ['.$status .' :'. $counts_redefined[$status] .']';
		}

	}
}