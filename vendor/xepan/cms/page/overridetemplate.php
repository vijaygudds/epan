<?php

/**
* description: ATK Page
* 
* @author : Gowrav Vishwakarma
* @email : gowravvishwakarma@gmail.com, info@xavoc.com
* @website : http://xepan.org
* 
*/

namespace xepan\cms;


class page_overridetemplate extends \Page {
	
	public $title='Over Ride template';

	function init(){
		parent::init();

		if(!$this->app->auth->isLoggedIn()) return;

		// $this->app->print_r($_POST);
		// $this->app->print_r($_GET);
		// exit;
		if($_POST['xepan-tool-to-clone'])
			$tool_name = $_POST['xepan-tool-to-clone'];

		if($_GET['xepan-tool-to-clone']){
			$tool_name = $this->app->stickyGET('xepan-tool-to-clone');	
		}

		if(!$tool_name){
			return $this->add('View')->set('Please select a tool');
		}	

		$tool_options = json_decode($this->api->stickyGET('options'),true);
		if(!is_array($tool_options)) $tool_options = [];

		$tool = $this->add($tool_name,['_options'=>$tool_options]);

		if(!$tool->templateOverridable){
			$this->add('View')->set('You cannot override template for this tool');
			return;
		}


		$original_path = $tool->getTemplateFile();
		// echo '$original_path '. $original_path."\n";
		if(strpos($original_path, '/websites/'.$this->app->current_website_name.'/www/') !== false && !isset($_REQUEST['custom_template_name'])){
			// echo "in if".'<br/>';
			$override_path = $original_path;
		}else{
			if(strpos($original_path, '/templates/') !==false){
				$relative_path = substr($original_path, strpos($original_path,'/templates/')+strlen('/templates/'));
			}elseif(strpos($original_path, '/websites/'.$this->app->current_website_name.'/www/') !==false){
				$relative_path = substr($original_path, strpos($original_path,'/websites/'.$this->app->current_website_name.'/www/')+strlen('/websites/'.$this->app->current_website_name.'/www/'));
			}else{
				die('File not identified');
			}
			// echo '$relative_path b4 '. $relative_path."\n";
			if(isset($_REQUEST['custom_template_name']) and $_REQUEST['custom_template_name']){
				$t = explode("/", $relative_path);
				unset($t[count($t)-1]);
				$t[] = $_REQUEST['custom_template_name'].'.html';
				$relative_path = implode("/", $t);
			}
			// echo '$relative_path after '. $relative_path."\n";
			$override_path = $this->app->pathfinder->base_location->base_path.'/websites/'.$this->app->current_website_name.'/www/'.$relative_path;
		}
			// echo '$override_path '. $override_path."\n";

		if(!file_exists($override_path)){
			$fs = \Nette\Utils\FileSystem::copy($original_path,$override_path,true);
			// $this->add('View')->set('File allrealy overrided at "'. $relative_path.'", Please remove this file and click again to reset');
			// return;
		}


		if($_POST['xepan-tool-to-clone'] && $_POST['template_html']){

			$result = ['status'=>'failed'];
			try{
				// echo "file putting at path " . $override_path.'<br/>';
				file_put_contents($override_path, $_POST['template_html']);
				$result = ['status'=>'success','override_path'=>$override_path];
			}catch(\Exception $e){
				
			}

			echo json_encode($result);
			exit();
		}

		// echo "here why?";

		$class = new \ReflectionClass($tool);
		$original_file = getcwd(). '/vendor/'.str_replace("\\", "/", $class->getNamespaceName()).'/templates/'.$tool->getDefaultTemplate().'.html';
		if(!file_exists($original_file)) $original_file = $override_path;
		
		$original_file_content = file_get_contents($original_file);

		preg_match_all('/{[$_a-zA-Z]*}/', $original_file_content, $tags);


		$temp = [[]];
		foreach ($tags[0] as $key => $tag) {
			if($tag == "{row}") continue;
			if($tag == "{rows}"){
				$temp[0][] = '{repetative_section}';
			}else{
				$temp[0][] = $tag;
			}
		}

		if($_GET['required'] == 'htmlcode'){
			$_temp = [
				'original_content'=> $original_file_content,
				'tags'=> $temp,
				'custom_template_attribute'=>$tool->getCustomtemplateOptionName()
			];
			echo  json_encode($_temp);
			exit();
		}

		$tabs = $this->add('TabsDefault');
		$edit_tab = $tabs->addtab('Edit');
		$original_tab = $tabs->addtab('Original');
		$edit_tab->add('View')->setHTML('<pre>Used tags <br/>'.implode(", ", $tags[0]).'</pre>');
		$f = $edit_tab->add('Form');
		$f->add('View_Info')->set($override_path);
		$field = $f->addField('xepan\base\CodeEditor','content')->set(file_get_contents($override_path));
		$field->lang='html';
		$field->setRows(20);

		$f->addSubmit('Save');

		if($f->isSubmitted()){
			file_put_contents($override_path, $f['content']);
			$f->js()->univ()->successMessage('Saved at '. $override_path)->execute();
		}


		$original_tab->add('View')->set($original_file_content);


		$tool->destroy();
	}
}
