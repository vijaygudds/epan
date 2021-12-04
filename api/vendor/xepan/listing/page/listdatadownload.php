<?php
 
namespace xepan\listing;

class page_listdatadownload extends \Page {

	function init(){
		parent::init();

		ini_set("memory_limit", "-1");
   		set_time_limit(0);

		$listing_id = $this->app->stickyGET('listing_id');
		$list_data_id = $this->app->stickyGET('list_data_id');
		// for print document
		$include_related_contact = $this->app->stickyGET('related_contact')?:1;
		$action = $this->app->stickyGET('action');
		// for all data layout 
		$all_record = $this->app->stickyGET('all_record');
		// apply data set condtion to all record
		$data_set_id = $this->app->stickyGET('data_set_id');
		// categories
		$categories = $this->app->stickyGET('categories');
		

		$related_list_data_print_layout = null;

		$list_model = $this->add('xepan\listing\Model_List')->load($listing_id);
		$layout = $list_model['list_data_download_layout'];

		if(isset($action) AND ($action == "print" || $action== "html")){
			$layout = $list_model['list_data_print_layout'];
			
			if($include_related_contact)
				$related_list_data_print_layout = $list_model['related_list_data_print_layout'];
		}

		$pdf_action = "dump";

		$data_model = $list_model->getDataModel();

		// apply data set condition
		if($data_set_id){
			$conditions = $this->add('xepan\listing\Model_ListDataSetCondition')
						->addCondition('list_data_set_id',$data_set_id);
			foreach ($conditions as $condition) {
				$field_db_name = $condition->ref('filter_effected_field_id')
									->dbColumnName();

				$value = $condition['value'];
				if($condition['operator'] == "in"){
					$value = explode(",", $value);
				}

				$operator = $condition['operator'];
				if($condition['operator'] == "contains"){
					$value = "%$value%";
					$operator = "like";
				}
				
				$data_model->addCondition($field_db_name,$operator,$value);
			}
		}

		$data_model->addExpression('category_comma_seperated')->set(function($m,$q){
			$x = $m->add('xepan\listing\Model_Association_ListDataCategory',['table_alias'=>'contacts_str']);
			return $x->addCondition('list_data_id',$q->getField('id'))->_dsql()->del('fields')->field($q->expr('group_concat([0] SEPARATOR ", ")',[$x->getElement('list_category')]));
		})->allowHTML(true);

		// categories
		if($categories AND $cat_array = explode(",",$categories) AND ($cat_array[0] != null or $cat_array[0] != 0)){
			$cat_array = array_combine($cat_array, $cat_array);
			$item_join = $data_model->Join('listing_category_list_data_association.list_data_id');
			$item_join->addField('list_category_id');
			$item_join->addField('category_assos_list_data_id','list_data_id');
			$data_model->addCondition('list_category_id',$cat_array);

			$q = $data_model->dsql();
			$group_element = $q->expr('[0]',[$data_model->getElement('category_assos_list_data_id')]);
			$data_model->_dsql()->group($group_element);
		}

		if(!$all_record AND $list_data_id){
			$data_model->load($list_data_id);
		}
		
		if($all_record){
			$html = "";
			foreach ($data_model as $data) {
				$html .= $data->generatePDF($pdf_action,$layout,$include_related_contact,$related_list_data_print_layout,$return_html_only=true);
			}
		}else{

			$pdf = $data_model->generatePDF($pdf_action,$layout,$include_related_contact,$related_list_data_print_layout);
			header("Content-type: application/pdf");
			header("Content-disposition: attachment ; filename=\"" .'Data_Record_of_'.$list_data_id. ".pdf\"");
			header("Content-Length: " . strlen($pdf));
        	// print $pdf;
		}
		
		echo $html;
        exit;
	}
}

