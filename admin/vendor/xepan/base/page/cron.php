<?php

/**
* description: ATK Page
* 
* @author : Gowrav Vishwakarma
* @email : gowravvishwakarma@gmail.com, info@xavoc.com
* @website : http://xepan.org
* 
*/

namespace xepan\base;

class page_cron extends \Page {
	public $title='Cron Job Collector and Executor';

	function init(){
		parent::init();

		ini_set('memory_limit', '2048M');
		set_time_limit(0);

		if($_GET['now']) $this->app->now = urldecode($_GET['now']);
		
		$resolver = new \Cron\Resolver\ArrayResolver();

		$this->app->hook('cron_executor',[$resolver]);
	}
}
