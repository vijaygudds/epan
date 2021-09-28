<?php

namespace xepan\base;

class Controller_Profiler extends \AbstractController {
	public $init_time=null;
	public $prev_time=null;
	public $time_taken = [];
	function init(){
		parent::init();
		if(!$this->app->getConfig('profiler',false)) return;
		$this->init_time = microtime(true);
		$this->prev_time = $GLOBALS['profiler_time'];
		$this->mark('Init');
	}

	function mark($spot){
		if(!$this->app->getConfig('profiler',false)) return;
		$cur = microtime(true);
		$prev = $this->prev_time?:$this->init_time;

		$this->time_taken[$spot] = $cur-$prev;
		$this->prev_time = $cur;
	}

	function dump(){
		var_dump($this->time_taken);
	}
}