<?php

namespace xepan\cms;

class Tool_KeepAlive extends \xepan\cms\View_Tool{
	public $options = [
				'timeout_mili_seconds'=>120000 // 2 minute
			];

	function init(){
		parent::init();

		if($this->owner instanceof \AbstractController) return;
		if($_GET['keepalive_tool']) exit;


		$this->addClass('xepan-cms-keepalive');
		$this->setStyle('display','none');

		$this->js(true)->reload(null,null,null,$this->options['timeout_mili_seconds']);
		// if(!$this->app->isAjaxOutput()){
		// 	$js =$this->js(true)->ajaxec([$this->api->url('.'),'keepalive_tool'=>true])->_enclose();
		// 	// $js = $this->js()->univ()->ajaxec($this->api->url('.',['keepalive_tool'=>true]))->_enclose();
		// 	$this->js(true)->univ()->setInterval($js,$this->options['timeout_seconds']);
		// }

	}
}