<?php

namespace xepan\cms;

class Tool_Marquee extends \xepan\cms\View_Tool{
	public $options = [
				'width'=>'100%',
				'height'=>'',
				'direction'=>'left',
				'behavior'=>'scroll',
				'Scrolldelay'=>'600',
				'Scrollamount'=>'10',
				'bgcolor'=>'#fff',
				'hspace'=>'',
				'vspace'=>'',
				'action'=>'stop'
			];
	public $runatServer=false;

	public $templateOverridable = false; 		

	function init(){
		parent::init();
	}

	function defaultTemplate(){
		return['view\tool\marquee'];
	}
}