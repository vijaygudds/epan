<?php
 
namespace xepan\listing;

class page_getrecord extends \Page {

	function page_list(){

		$lists = $this->add('xepan\listing\Model_List')
				->addCondition('status','Active');
		
		$option = "<option value='0'>Please Select </option>";
		foreach ($lists as $list) {
			$option .= "<option value='".$list['id']."'>".$list['name']."</option>";
		}
		echo $option;
		exit;
	}

	function page_listlayout(){

		$lists = $this->add('xepan\listing\Model_ListDataFormLayout')
				->addCondition('list_id',$_GET['list_id']);
		
		$option = "<option value='0'>Please Select </option>";
		foreach ($lists as $list) {
			$option .= "<option value='".$list['id']."'>".$list['name']."</option>";
		}
		echo $option;
		exit;
	}

	function page_listingstatus(){
		$list = $this->add('xepan\listing\Model_List');
		$list->load($_GET['listing_id']);
		
		$option = "<option value='0'>Please Select </option>";
		foreach (explode(",",$list['list_data_status']) as $key => $status) {
			$option .= "<option value='".trim($status)."'>".trim($status)."</option>";
		}
		echo $option;
		exit;	
	}

	function page_listingcategory(){
		$model = $this->add('xepan\listing\Model_Category');
		$model->addCondition('list_id',$_GET['listing_id']);

		$option = "<option value='0'>Please Select </option>";
		foreach ($model as $cat){
			$option .= "<option value='".$cat['id']."'>".$cat['name']."</option>";
		}

		echo $option;
		exit;	
	}

	function page_listfilter(){
		$model = $this->add('xepan\listing\Model_Filter');
		$model->addCondition('list_id',$_GET['listing_id']);
		$option = "<option value='0'>Please Select </option>";
		foreach ($model as $fil){
			$option .= "<option value='".$fil['id']."'>".$fil['name']."</option>";
		}

		echo $option;
		exit;
	}

	function page_listingdataset(){

		$model = $this->add('xepan\listing\Model_ListDataSet');
		$model->addCondition('list_id',$_GET['listing_id']);
		$option = "<option value='0'>Please Select </option>";
		foreach ($model as $fil){
			$option .= "<option value='".$fil['id']."'>".$fil['name']."</option>";
		}

		echo $option;
		exit;	
	}

	function page_listingactualfields(){

		$listdata_model = $this->add('xepan\listing\Model_ListData',['listing'=>$this->options['listing_id']]);
		$fields = $listdata_model->getActualFields();
		$fields  = array_merge($fields,$fields);
		$option = "<option value='0'>Please Select </option>";
		foreach ($fields as $key => $value){
			$option .= "<option value='".$key."'>".$value."</option>";
		}

		echo $option;
		exit;
	}
}

