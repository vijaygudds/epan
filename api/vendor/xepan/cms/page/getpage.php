<?php

namespace xepan\cms;

class page_getpage extends \Page{

	function page_index(){

		if($this->groupid = $_GET['group_id']){
        	$this->mg = $this->add('xepan\cms\Model_MenuGroup');
        	$this->mg->load($this->groupid);
        }


		$pages = [];
		$root_page = $this->add('xepan\cms\Model_Page');
		$root_page->addCondition([['parent_page_id',0],['parent_page_id',null]])
				->addCondition('is_active',true)
				->addCondition([['is_muted',false],['is_muted',null]])
				->setOrder('order')
				;

		foreach ($root_page as $parent_page) {
			if($this->groupid && !$this->mg['pages'][$this->app->normalizeName($parent_page['name'])]) continue;

			$pages["".str_replace(".html", "", $parent_page['path'])] = [
									'name'=>$parent_page['name'],
									// 'template_path'=>$parent_page['template_path'],
									'subpage'=>$this->getPages($parent_page),
									'iconclass'=>$parent_page['icon_class']
								];
		}

		// echo "<pre>";
		// print_r($pages);
		// echo "</pre>";
		echo json_encode($pages);
		exit;		
	}

	function getPages($parent_page){

		$output = [];
		if($parent_page->ref('SubPages')->count()->getOne() > 0){
			
			$sub_pages = $parent_page->ref('SubPages')
							->addCondition('is_active',true)
							->addCondition('is_muted',false)
							->setOrder('order')
							;
			foreach ($sub_pages as $junk_page) {

				if($this->groupid && !$this->mg['pages'][$this->app->normalizeName($junk_page['name'])]) continue;

				$output["".str_replace(".html", "", $junk_page['path'])] = [
										'name'=>$junk_page['name'],
										// 'template_path'=>$junk_page['template_path'],
										'subpage'=>$this->getPages($junk_page),
										'iconclass'=>$junk_page['icon_class']
									];
			}

		}

		return $output;
	}

	function page_allmenugroup(){
		
		$lists = $this->add('xepan\cms\Model_MenuGroup');
		$option = "<option value='0'>Please Select </option>";
		foreach ($lists as $list) {
			$option .= "<option value='".$list['id']."'>".$list['name']."</option>";
		}
		echo $option;
		exit;
	}

}