<?php

namespace xepan\cms;

class page_editor extends \xepan\base\Page{
	
	function page_updatelayout(){
		
		$current_domain = $this->app->extract_domain($_SERVER['HTTP_HOST']);

		$service_host = $this->getConfig('xepan-service-host',false);
        if($service_host && !in_array($current_domain,$service_host)){
			$this->js()->univ()->errorMessage('Layout update facility is not available')->execute();		
		}
		
		if(!$this->app->epan['xepan_template_id']){
			$this->js()->univ()->errorMessage('Template is not defined')->execute();
		}

		$template_name = null;
		$template_id = $this->app->epan['xepan_template_id'];
		$this->add('xepan\epanservices\Controller_RemoteEpan')
			->setEpan('www')
			->do(function($app)use(&$template_name,$template_id){
				$temp_epan_model = $app->add('xepan\base\Model_Epan')
					->tryLoad($template_id);
				if(!$temp_epan_model->loaded()) return;
				$template_name = $temp_epan_model['name'];
			});

		if(!$template_name)
			$this->js()->univ()->errorMessage('Template not found on host')->execute();

		\Nette\Utils\FileSystem::copy('./websites/'.$template_name.'/www/customlayout','./websites/'.$this->app->epan['name'].'/www/customlayout',true);
		\Nette\Utils\FileSystem::copy('./websites/'.$template_name.'/www/themelayout','./websites/'.$this->app->epan['name'].'/www/themelayout',true);
			
		$this->js()->univ()->successMessage('Layouts updated, please reload the page')->execute();
	}
}