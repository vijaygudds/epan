<?php


namespace xepan\base;

// Form layout Creator [FLC]

/*
	$form->add('xepan\base\Controller_FLC')
		->showLables(true)
		->makePanelsCoppalsible(true)
		->layout([
				'first_name~Field New Cpation'=>'Name Section|panel-type~c1~4',
				'nick_name'=>'c2~4~closed or any other text as field hint',
				'last_name'=>'c3~4',
				'city'=>'Location~c1~4~closed', // closed to make panel default collapsed
				'state'=>'c2~4',
				'country'=>'c3~4'
			]);
*/
class Controller_FLC extends \AbstractController {
	
	public $collepsible_panel = false; 
	public $add_lables = true; 
	public $addContent = false; 
	public $debug = false;
	public $closeOtherPanels = false;
	public $layout_array=[];

	function init(){
		parent::init();

		// create array first
		// row for each group if not exists
		// add title from third part as full width
		// create column of second arg width 
		// create string from created array
		// create template with created source
		// set layout for form 

	}

	function makePanelCollepsible(){
		$this->collepsible_panel = true;
		return $this;
	}

	function makePanelsCoppalsible(){
		return $this->makePanelCollepsible();
	}

	function closeOtherPanels(){
		$this->closeOtherPanels = true;
		return $this;
	}

	function showLables($show=true){
		$this->add_lables = $show;
		return $this;
	}

	function addContentSpot(){
		$this->addContent=true;
		return $this;
	}


	/**
	 * 
		'first_name'=>'Name Section~c1~6',
		'nick_name'=>'~c1',
		'last_name'=>'~c2~6',
		'city'=>'Location~c1~4',
		'state'=>'~c2~4',
		'country'=>'~c3~4',
	 */

	 /** == second version -- not production ready but workingon 
	 * 
		'r1c1~w8'=>[
			first_name'=>'Name Section~c1~6',
			'nick_name'=>'~c1',
			'last_name'=>'~c2~6',
			'city'=>'Location~c1~4',
			'state'=>'~c2~4',
			'country'=>'~c3~4',
		],
		'r1c2~w4'=>[
			...
		],
		'r1c2~n1'=>[
			... added no-1 in r1c2 column
		],
		'r1c2~n2'=>[
			... added no-2 in r1c2 column
		],
		'r1c2~w6'=>[
		]
	 */
	function layout($array=null){
		$rows=[];
		$collapsed_sections=[];
		$field_hints=[];

		$this->layout_array = $array;

		$last=null;
		$intro=[];
		foreach ($array as $field => $detail) {

			list($title,$column,$width,$collapsed,$field_hint) = explode("~", $detail);

			if(strlen($title)<=3){
				$field_hint = $collapsed;
				$collapsed = $width;
				$width=$column?:'auto';
				$column=$title;
				$title='';
			}

			if($title=='') $title= isset($last_title)?$last_title:'';
			$last_title = $title;

			if(!isset($rows[$title])) $rows[$title]=[];

			if(!isset($rows[$title][$column])) $rows[$title][$column]=['width'=>$width, 'fields'=>[]];

			$rows[$title][$column]['fields'][] = $field;

			if($collapsed){
				if($collapsed=='closed'){
					$collapsed_sections[]=$title;
				}else{
					$field_hint = $collapsed;
				}
			}

			if($field_hint){				
				$field_hint_arr = explode("||", $field_hint);
				$field_hints[$field] = $field_hint_arr[0];
				if(isset($field_hint_arr[1])) {					
					$intro[$field] = $field_hint_arr[1];				
				}
			}


		}

		// echo "<pre>";
		// print_r($rows);
		// echo "</pre>";

		$template_str="<div id='".$this->name."_p'>";		
		if(count($intro) !==0){
			$template_str .= "<i class='fa fa-users' onclick='$.univ().runIntro(\"#".$this->name."_p\")'></i>";
		}
		foreach ($rows as $title => $row) {
			$title_arr = explode("|", $title);
			$title=$title_arr[0];
			$panel_type = isset($title_arr[1])?$title_arr[1]:'default';

			$template_str .= "<div class='panel panel-$panel_type xepan-flc-form'>";
			if(!is_numeric($title)){
				$id=$this->app->normalizeName($title);
				$data_str="";
				$collapse_in_handler_class= "";
				$collapse_in= "";
				$cursor="";
				$xepan_collepsable="";
				if($this->collepsible_panel){
					$data_str ="  data-toggle='collapse' data-target='#$id'";
					if($this->closeOtherPanels){
						$data_str .=" data-parent='#".$this->name."_p'";
					}
					$collapse_in="collapse in ";
					$xepan_collepsable="xepan-flc-collasable-form";
					
					if(in_array($title, $collapsed_sections)){
						$collapse_in_handler_class= "collapsed";
						$collapse_in="collapse";						
					}

					$cursor="style='cursor:pointer'";
				}
				$template_str .= "<div class='panel-heading $collapse_in_handler_class $xepan_collepsable' $data_str $cursor>$title</div>";
				$template_str .="<div class='row panel-body $collapse_in' id='$id'>";
			}
				foreach ($row as $col) {
					$template_str.="<div class='flc-atk-form-row col-sm-12 col-xs-12 col-md-".$col['width']."'>";
						foreach ($col['fields'] as $field) {
							$field_hint = isset($field_hints[$field])?'<small class="text-muted">'.$field_hints[$field].'</small>':'';
							$field_arr=explode("~", $field);
							$field=$field_arr[0];
							$field_caption=isset($field_arr[1])?$field_arr[1]:ucwords(str_replace('_', ' ', $field));
							if($this->add_lables && $field_caption){
								$template_str.= '<b>'.$field_caption.'</b><br/>';
							}
								$template_str.= '<div class="atk-form-field atk-form-row" '.(isset($intro[$field])?' data-intro="'.$intro[$field].'" ':'').'>{$'.$field.'}'.$field_hint.'</div>';
							if($this->add_lables){
							}
						}
					$template_str.="</div>";
				}
			if(!is_numeric($title)){
				$template_str .="</div>";
			}
			$template_str .= "</div>";
		}

		if($this->addContent){
			$template_str .='<div>{$Content}</div>';
		}

		$template_str.="</div>";

		if($this->debug){
			$this->owner->add('View')->setElement('pre')->set($template_str);
		}

		if($this->owner instanceof \Form){
			$t = $this->add('GiTemplate')->loadTemplateFromString($template_str);
			$this->owner->setLayout($t);
		}
		else{
			$this->owner->template->loadTemplateFromString($template_str);
		}

		return $this;

	}

	function getFields(){
		$fields=[];
		foreach ($this->layout_array as $field => $details) {
			$fields[] = explode("~", $field)[0];
		}

		return $fields;
	}

	function debug(){
		$this->debug=true;
		return $this;
	}

	function layoutComplex(){
		// '1.row'=>[
		// 			'Title of the Row'
		// 			'1.col_8'=>[
		// 				'Title of Col1 Section'
		// 				'1.row'=>[
		// 					'1.col_6'=>['{$first_name}','{$nick_name}'],
		// 					'2.col_6'=>['{$last_name}']
		// 				]
		// 			]
		// 			'2.col_4'=>[
		// 				'Title of col2 Section'
		// 			]
		// 		]
	}
}