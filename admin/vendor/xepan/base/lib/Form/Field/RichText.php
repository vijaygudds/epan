<?php


// mentions from here 
// https://github.com/StevenDevooght/tinyMCE-mention

namespace xepan\base;

/**
===================
Basic mention use 
===================

$source_aray = ['{$a}','b','c'];
or $source_aray = ['name'=>'{$a}','name'=>'b','name'=>'c']; // then setStaticHelperList third parameter will be false

$f = $quotation_form->getElement('master')
			->addStaticHelperList($source_array,'?',true/false-if-source-arrya-has-name-key-already);
		$f->mention_options['items']=10000;
		$f->mention_options['delay']=1000; // no os miliseconds to start showing list once user stop on that delimiter
	
===================
Advanced Mention Used static data
===================
	$groups = $this->add('xepan\accounts\Model_Group')->getRows(['name']);
	$ledgers = $this->add('xepan\accounts\Model_Ledger')->getRows(['name']);
	$bshead = $this->add('xepan\accounts\Model_BalanceSheet')->getRows(['name']);

	$f = $crud->form->getElement('layout');

// --- simpler way ---

	$f->mention_options=[
					// what to replace when selected from item returned
					'insert'=>$this->js(null,'function(item){return "<span>" + item.id +":(" + item.name + ")</span>";}'),
					// what & how to show on selection list
					'render'=>$this->js(null,"function(item) { return '<li>' +'<a href=\"javascript:;\"><span>' + item.id+ ' : ' + item.name + '</span></a>' +'</li>';}")
				];
	$f->mention_options['items']=10000; // maximum records to show in list
	$f->addStaticHelperList($groups,'G',false);
	$f->addStaticHelperList($ledgers,'L',false);
	$f->addStaticHelperList($bshead,'H',false);

// --- COMPLEX WAY FOR SAME BUT TO UNDERSTAND HOW IT WORKS ---
	$f->mention_options=[
					'delimiter'=>['G','L','H'],
					// based on delimiter
					'source'=> $this->js(null,'function(query, process, delimiter){if(delimiter==="G") process('.json_encode($groups).'); if(delimiter==="L") process('.json_encode($ledgers).');  if(delimiter==="H") process('.json_encode($bshead).');}'),
					// what to replace when selected from item returned
					'insert'=>$this->js(null,'function(item){return "<span>" + item.id +":(" + item.name + ")</span>";}'),
					// what & how to show on selection list
					'render'=>$this->js(null,"function(item) { return '<li>' +'<a href=\"javascript:;\"><span>' + item.id+ ' : ' + item.name + '</span></a>' +'</li>';}")
				];
	$f->mention_options['items']=10000; // maximum records to show in list



===================
Advanced ajax use
===================
$tag_helper = $this->add('VirtualPage')
				->set(function($page){
					// $_GET['delimiter']  & $_GET['query']
					if($_GET['delimiter']=='!')
						echo json_encode([['name'=>'{$a !}'],['name'=>'{$b !}'],['name'=>'{$c !}']]);
					if($_GET['delimiter']=='$')
						echo json_encode([['name'=>'{$a $}'],['name'=>'{$b $}'],['name'=>'{$c $}']]);
					exit;
				});
$f = $crud->form->getElement('layout');

// --- simpler way ---

			$f->addAjaxHelper($any_virtual_page->getURL(),['#','@']);

// --- COMPLEX WAY FOR SAME BUT TO UNDERSTAND HOW IT WORKS ---
			$f->mention_options=[
					'delimiter'=>['!',"$"],
					'source'=> $this->js(null,'function(query, process, delimiter){$.getJSON("'.$tag_helper->getURL().'&delimiter="+delimiter, function (data) {process(data)});}')
				];
*/


class Form_Field_RichText extends \Form_Field_Text{
	public $options=[];
	public $mention_options=[];
	public $mention_delimiter_source=[];

	public $extra_options=[];

	public $js_widget='xepan_richtext_admin';

	function init(){
		parent::init();
		$this->addClass('tinymce');
	}

	function addNameKey($arr){
		$final_list=[];
		foreach ($arr as $l) {
			$final_list[] = ['name'=>$l];
		}
		return $final_list;
	}

	function addStaticHelperList($list,$delimiter='?',$add_name_key=true){
		$final_list = $list;
		if($add_name_key){
			$final_list = $this->addNameKey($list);
		}
		$this->mention_delimiter_source[$delimiter] = $final_list;
		return $this;
	}

	function addAjaxHelper($url,$delimiter=['@']){
		$this->mention_options['delimiter'] = $delimiter;
		$this->mention_options['source']= $this->js(null,'function(query, process, delimiter){$.getJSON("'.$url.'&delimiter="+delimiter+"&query="+query, function (data) {process(data)});}');
	}

	function render(){

		if(count($this->mention_delimiter_source)){
			$this->mention_options['delimiter']=array_keys($this->mention_delimiter_source);
			$fn = 'function(query, process, delimiter){';
			foreach ($this->mention_delimiter_source as $delimiter => $values) {
				$fn .= 'if(delimiter==="'.$delimiter.'") process('.json_encode($values).');';
			}
			$fn .= '}';
			$this->mention_options['source']= $this->js(null,$fn);
		}

		$this->js(true)
				->_load('tinymce.min')
				->_load('jquery.tinymce.min')
				->_load($this->js_widget)
				->_css('../js/tinymce-plugins/mention/css/rte-content')
				->_css('../js/tinymce-plugins/mention/css/autocomplete')
				;
		$this->js(true)->univ()->{$this->js_widget}($this,$this->options,null,$this->mention_options,$this->extra_options);
		parent::render();
	}
}