<?php


class endpoint_v1_versionquery extends Endpoint_REST {

	function get(){
		$version_array=[];

		$composer_file = file_get_contents(getcwd().'/../composer.json');
		$version_array =  json_decode($composer_file);
		var_dump($version_array);
		exit;

		$applications  =$this->add('xepan\base\Model_Application');
		$version_array['root'] = file_get_contents(getcwd().'/../version');

		foreach ($applications->getRows() as $app) {
			// if(file_exists(getcwd().'/../vendor/'.$app['namespace'].'/version'))
				$version_array[$app['namespace']] = file_get_contents(getcwd().'/vendor/'.str_replace("\\", "/", $app['namespace']).'/version');
			// else
			// 	$version_array[$applications['namespace']] = '';
		}
		return $version_array;
	}
}