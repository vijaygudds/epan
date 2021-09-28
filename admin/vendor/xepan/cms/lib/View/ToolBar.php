<?php


namespace xepan\cms;


class View_ToolBar extends \View {

	function init(){
		parent::init();

		$this->app->jui->addStaticInclude('colorpicker/tinycolor-0.9.15.min');
		$this->app->jui->addStaticInclude('colorpicker/jquery.colorpicker');
		$this->app->jui->addStaticInclude('colorpicker/colorpicker');
		
		$this->app->jui->addStaticInclude('ace/ace/ace');
		$this->app->jui->addStaticInclude('ace/ace/mode-html');
		$this->app->jui->addStaticInclude('ace/ace/theme-tomorrow');
		$this->app->jui->addStaticInclude('ace/ace/worker-html');
		$this->app->jui->addStaticInclude('ace/jquery-ace.min');


        $this->app->jui->addStylesheet('elfinder.full');
        $this->app->jui->addStylesheet('elfindertheme');
        $this->app->jui->addStylesheet('xepan-editing');
        $this->app->jui->addStylesheet('bootstrap-iso');
        $this->addClass('bootstrap-iso');

		$this->app->jui->addStaticInclude('elfinder.full');
		$this->app->jui->addStaticInclude('jquery.dom-outline-1.0');
		
		$this->js(true)
				->_load('tinymce.min')
				->_load('jquery.tinymce.min')
				->_load('xepan_richtext_admin')
				->_load('shortcut')
				// ->_load('xepanEditor')
				// ->_load('xepanComponent')
				->_load('jquery.livequery')
				->_load('html2canvas.min')
				;
        
		$this->js()->_load('jstree\dist\jstree.min');
		$this->js()->_load('xepanjstree');
		$this->app->jui->addStaticStyleSheet('jstree/dist/themes/default/style.min');

		$this->app->jui->addStaticInclude('xepanEditor');
		$this->app->jui->addStaticInclude('xepanComponent');

		$tools = $this->app->getFrontEndTools();

		$view = $this->add('AbstractController');
		$bs_view=$view->add('xepan\cms\View_CssOptions',['name'=>'xepan_cms_basic_options']);

		$tools_array = [];
		$tool_number=1;
		//tools_array
		foreach (array_keys($tools) as $group) {
			$tools_array[$group] = [];

			foreach ($tools[$group] as $key => $tool) {

				$t_v = $view->add($tool);
				$t_option_v = $t_v->getOptionPanel($view,null,$tool_number++);

				$tool_arr = explode("\\", $tool);
				$tool_name = array_pop($tool_arr);
				$tool_name = str_replace("Tool_", '', $tool_name);
				$tool_namespace = implode("/", $tool_arr);

				$drop_html = $t_v->runatServer ? '<div class="xepan-component xepan-serverside-component" xepan-component-name="'.$tool_name.'" xepan-component="'.str_replace('\\', '/', get_class($t_v)).'">' .$t_v->getHTML(). '</div>': $t_v->getHTML();
				$tools_array[$group][$tool] = [
											'name'=>$tool_name,
											'drop_html'=>$drop_html,
											'is_serverside'=>$t_v->runatServer,
											'option_html'=>$t_option_v->getHTML(),
											'icon_img'=>'./vendor/'.$tool_namespace.'/templates/images/'.$tool_name.'_icon.png'
										];

			}
		}

		// add layouts
		$tools_array['Layouts']=[];
		$layouts= $this->add('xepan/cms/Model_Layout');
		foreach ($layouts as $l) {

			if(strpos($l['name'], ".png") or strpos($l['name'], ".jpg") or strpos($l['name'], ".jpeg")) continue;
			$t_v = $view->add('xepan\cms\Tool_Layout',null,null,["xepan\\tool\\layouts\\".str_replace(".html", "", $l['name']) ]);
			$t_option_v = $t_v->getOptionPanel($view,null,$tool_number++);
			$tools_array['Layouts'][] = [
											'name'=>'',
											'tool'=>'xepan/cms/Tool_Layout',
											'is_serverside'=>$t_v->runatServer,
											'category'=>explode("-", str_replace(".html", "", $l['name']))[0],
											'drop_html'=>$t_v->getHTML(),
											'option_html'=>$t_option_v->getHTML(),
											'icon_img'=>'./vendor/xepan/cms/templates/xepan/tool/layouts/'.str_replace(".html", ".png", $l['name'])
										];
		}

		// theme layout
		$layout_folder_list = ['themelayout','customlayout'];

		$this->pq = $pq = new phpQuery();
		
		$domain = $this->app->pm->base_url.$this->app->pm->base_path.'websites/'.$this->app->current_website_name.'/www';
		$rel_path = 'websites/'.$this->app->current_website_name.'/www/';

		foreach ($layout_folder_list as $key => $folder_name) {
			$absolute_theme_path = $this->api->pathfinder->base_location->base_path.'/websites/'.$this->app->current_website_name.'/www/'.$folder_name.'/';
			$theme_path = '/'.$folder_name.'/';
			
			$layouts = $this->add('xepan/cms/Model_Layout',['path'=>$absolute_theme_path]);
			foreach ($layouts as $l) {
				if(strpos($l['name'], ".png") or strpos($l['name'], ".jpg") or strpos($l['name'], ".jpeg")) continue;

				$file_name = str_replace(".html", "", $l['name']);

				$t_v = $view->add('xepan\cms\Tool_Layout',null,null,[$theme_path.$file_name]);
				$t_option_v = $t_v->getOptionPanel($view,null,$tool_number++);

				$icon_img = $absolute_theme_path.$file_name.".png";
				if(file_exists($icon_img)){
					$icon_img = './websites/'.$this->app->current_website_name.'/www/'.$folder_name.'/'.$file_name.'.png';
				}else{
					$icon_img = '';
				}

				$layout_html = $t_v->getHTML();
				$layout_html = preg_replace('/url\(\s*[\'"]?\/?(.+?)[\'"]?\s*\)/i', 'url('.$rel_path.'$1)', $layout_html);
				$dom = $pq->newDocument($layout_html);

				foreach ($dom['img']->not('[src^="http"]')->not('[src^="data:"]')->not('[src^="websites/'.$this->app->current_website_name.'"')->not('[src^="vendor/"]') as $img) {
					$img= $this->pq->pq($img);
					$img->attr('src',$rel_path.$img->attr('src'));
				}

				$layout_html = $dom->html();

				// continue;
				$tools_array['Layouts'][] = [
												'name'=>$file_name,
												'is_serverside'=>$t_v->runatServer,
												'tool'=>'xepan/cms/Tool_Layout',
												'category'=>$folder_name,
												'drop_html'=>$layout_html,
												'option_html'=>$t_option_v->getHTML(),
												'icon_img'=>$icon_img
												// 'icon_img'=>$theme_path.'/'.str_replace(".html", ".png", $l['name'])
											];
			}
		}


		$component_selector=".xepan-page-wrapper.xepan-component, .xepan-page-wrapper .xepan-component";
		$editing_template = null;

		if($this->app->editing_template){
			$editing_template = $this->app->editing_template;
			$this->js(true)->_selector('.xepan-v-body')->addClass('xepan-component xepan-sortable-component');
		}
		
		$template_m = $this->add('xepan\cms\Model_Webpage')
							->addCondition('is_template',true)
							->addCondition('path',str_replace("layout/", '', $this->app->template->template_file.'.html'))
							->tryLoadAny();
		if(!$template_m->loaded()) {
			if(!$template_m['name']) $template_m['name']=$template_m['path'];
			$template_m->save();
		}
		
		$webtemplate_id = $template_m->id;
		$webpage_id = @$this->app->xepan_cms_page->id;

		$component_selector = '.xepan-component';

		$this->js(true)
			// ->_load('xepanComponent')
			// ->_load('xepanEditor')
			->xepanEditor([
				'base_url'=>$this->api->url()->absolute()->getBaseURL(),
				'file_path'=>$this->app->page_object instanceof \xepan\cms\page_cms?realpath($this->app->page_object->template->origin_filename):'false',
				'template_file_path'=>$this->app->page_object instanceof \xepan\cms\page_cms?realpath($this->app->template->origin_filename):'false',
				'template'=>$this->app->page_object instanceof \xepan\cms\page_cms?$this->app->template->template_file:'false',
				'save_url'=> $this->api->url()->absolute()->getBaseURL().'?page=xepan/cms/admin/save_page&cut_page=1',
				'template_editing'=> isset($this->app->editing_template),
				'tools'=>$tools_array,
				'basic_properties'=>$bs_view->getHTML(),
				'component_selector'=>$component_selector,
				'editor_id'=>$this->getJSID(),
				'current_page'=> ucwords($this->app->xepan_cms_page['name']),
				'webpage_id'=> $webpage_id,
				'webtemplate_id'=> $webtemplate_id
			]);

		// Moved to xepanEditor.js
		// $this->js(true)->xepanComponent(['editing_template'=>$editing_template,'component_selector'=>$component_selector,'editor_id'=>$this->getJSID()])->_selector($component_selector);

		// html define in xepan editor.js
		// $this->js('click',$this->js()->univ()->frameURL('Override Tool Template',[$this->app->url('xepan_cms_overridetemplate'),'options'=> $this->js(null,'JSON.stringify($(current_selected_component).attr())') ,'xepan-tool-to-clone'=>$this->js()->_selector('.xepan-tools-options div[for-xepan-component]:visible')->attr('for-xepan-component')]))->_selector('#override-xepan-tool-template');
		$this->js('click',$this->js()->univ()->frameURL('Define Custom CSS',[$this->app->url('xepan_cms_customcss',['xepan-template-edit'=>""])]))->_selector('#xepan-tool-mystyle');
		// $this->js('click',$this->js()->univ()->frameURL('Create Layout',[$this->app->url('xepan_cms_createlayout')]))->_selector('#xepan-tool-createlayout');

		// $this->api->jquery->addStaticStyleSheet('colorpicker/pick-a-color-1.1.8.min');
		$this->api->jquery->addStaticStyleSheet('colorpicker/jquery.colorpicker');
		
		$this->js(true)->_selector('.epan-color-picker')->univ()->xEpanColorPicker();

		// inspector
		$this->js(true)
			->_load('xepanComponentCreator')
			->xepanComponentCreator([
					'tools'=>$tools_array,
					'template_file'=>$this->app->page_object instanceof \xepan\cms\page_cms?realpath($this->app->template->origin_filename):'false',
					'template'=>$this->app->page_object instanceof \xepan\cms\page_cms?$this->app->template->template_file:'false',
					'template_editing'=> isset($_GET['xepan-template-edit'])
				])
			->_selector('#xepan-tool-inspector');
	}

	function render(){
		
		parent::render();
	}

	function defaultTemplate(){
		return ['view/cms/toolbar/layout'];
	}
}
