<?php

namespace xepan\hr;

/**
* 
*/
class Model_File_SpreadSheet extends \xepan\hr\Model_File
{
	
	function init()
	{
		parent::init();
		
		$this->addCondition('type','spreadsheet');

	}

	function createNew($name,$folder_id=null){
		if(!trim($name))
			throw new \Exception("folder name must not be empty");

		$new_file = $this->add('xepan\hr\Model_File_SpreadSheet');
		$new_file['name'] = $name;
		$new_file['folder_id'] = $folder_id;
		$new_file->save();
		return $new_file;
	}

	function renderEdit($page){
		
	}

	function renderView($page){

	}
}