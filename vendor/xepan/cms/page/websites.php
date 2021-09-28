<?php

namespace xepan\cms;

class page_websites extends \xepan\base\Page{
		public $breadcrumb=[
						'Dashboard'=>'/','Meta Info'=>'xepan_cms_sitemetainfo'
					];

	public $title = "Website";

	function init(){
		parent::init();

		// if($s= $_GET['step']){
		// 	$s='step'.$s;
		// 	$this->$s();
		// }
	}
	
	function page_index(){

		if($_GET['step']) return;

		$template_initializer = $this->app->page_top_right_button_set->addButton('Initialize Template')->addClass('btn btn-primary');
		$previous_themes = $this->app->page_top_right_button_set->addButton('Previous Themes')->addClass('btn btn-primary');
		$template_initializer->js('click')->univ()->location($this->app->url('./step1'));
		$previous_themes->js('click')->univ()->frameURL('Previous Themes',$this->app->url('./previousthemes'));
		// as per page 
		// http://codepen.io/kaizoku-kuma/pen/JDxtC
		$this->app->jui->addStylesheet('codemirror/codemirror-5.15.2/lib/codemirror');
		$this->app->jui->addStylesheet('codemirror/codemirror-5.15.2/theme/solarized');
		// $this->app->jui->addStylesheet('theme');

		$this->app->jui->addStaticInclude('codemirror/codemirror-5.15.2/lib/codemirror');
		$this->app->jui->addStaticInclude('codemirror/codemirror-5.15.2/mode/htmlmixed/htmlmixed');
		$this->app->jui->addStaticInclude('codemirror/codemirror-5.15.2/mode/jade/jade');
		$this->app->jui->addStaticInclude('codemirror/codemirror-5.15.2/mode/php/php');
		$this->app->jui->addStaticInclude('codemirror/codemirror-5.15.2/mode/xml/xml');
		$this->app->jui->addStaticInclude('codemirror/codemirror-5.15.2/mode/css/css');
		$this->app->jui->addStaticInclude('codemirror/codemirror-5.15.2/mode/javascript/javascript');
		$file_manager_view = $this->add('View');
		$this->js(true,'
				$("#'.$file_manager_view->name.'").elfinder({
					url: "index.php?page=xepan_base_adminelconnector",
					height:450,
					commandsOptions: {
						edit : { 
							// list of allowed mimetypes to edit // if empty - any text files can be edited mimes : [],
							// you can have a different editor for different mimes 
							editors : [{
								mimes : ["text/plain", "text/html","text/x-jade", "text/javascript", "text/css", "text/x-php", "application/x-httpd-php", "text/x-markdown", "text/plain", "text/html", "text/javascript", "text/css"],
								load : function(textarea) {
									this.myCodeMirror = CodeMirror.fromTextArea(textarea, { 
																					lineNumbers: true,
																					theme: "solarized",
																					viewportMargin: Infinity, 
																					lineWrapping: true, 
																					mode:"javascript",json:true,
																					mode:"css",css:true , 
																					htmlMode: true
																				});
								},
								close : function(textarea, instance) { 
									this.myCodeMirror = null; 
								},
								save : function(textarea, editor) {
									textarea.value = this.myCodeMirror.getValue(); 
								}
							}] //editors 
						} //edit
					} //commandsOptions 
				}).elfinder("instance");
			');

		$quota_view = $this->add('xepan\base\View_Widget_ProgressStatus');
		$quota_view->setHeading('Space Quota Status');
		$quota_view->setIcon('fa fa-hdd-o');

		
		$folder = getcwd().'/websites/'.$this->app->epan['name'].'/';
		$folder=str_replace('admin/', '', $folder);
		$size = $this->uf_getDirSize($folder,'b');

		preg_match(
                    '|([a-z]+)://([^:]*)(:(.*))?@([A-Za-z0-9\.-]*)'.
                    '(/([0-9a-zA-Z_/\.-]*))|',
                    $this->app->getConfig('dsn'),
                    $matches
                );
		$db_size = $this->app->db->dsql()->expr("SELECT SUM(data_length + index_length) AS 'size' FROM information_schema.TABLES WHERE table_schema='".$matches[7]."';")->getOne();

		$extra_info = $this->app->recall('epan_extra_info_array',false);
		
		if(isset($extra_info ['specification']['Storage Limit']) && $extra_info ['specification']['Storage Limit'])
			$total_storage_limit = $extra_info ['specification']['Storage Limit'];
		else
			$total_storage_limit = $this->app->byte2human(disk_free_space("/"));

		$per=0;
		if($total_storage_limit){
			$per = (int) (($this->app->human2byte($size)+$db_size)/$this->app->human2byte($total_storage_limit)*100);
			if($per<80){
				$quota_view->makeSuccess();
			}elseif($per>=80){
				$quota_view->makeWarning();
			}elseif($per>=100) {
				$per=100;
				$quota_view->makeDanger();
			}
		}

		$total = $this->app->byte2human($this->app->human2byte($size)+$db_size);
		$db_size = $this->app->byte2human($db_size);

		$quota_view->setProgressPercentage($per);
		$quota_view->setFooter("Filesystem: $size + Database: $db_size [ $total / $total_storage_limit]");
	}

	function uf_getDirSize($dir, $unit = 'g'){
	    // $dir = trim($dir, '/');
	    // if (!is_dir($dir)) {
	    //     trigger_error("{$dir} not a folder/dir/path.", E_USER_WARNING);
	    //     return false;
	    // }
	    // if (!function_exists('exec')) {
	    //     trigger_error('The function exec() is not available.', E_USER_WARNING);
	    //     return false;
	    // }
	    $output = exec('du -sh ' . $dir);
	    $filesize = str_replace($dir, '', $output);
	    return $filesize;
	}

	function page_step1(){

		$www_absolute = getcwd().'/websites/'.$this->app->current_website_name.'/www/';
		$www_relative = './websites/'.$this->app->current_website_name.'/www/';

		$html_files = glob('./websites/'.$this->app->current_website_name.'/www/*.html');
		
		array_walk($html_files, function(&$value,$key){
			$value = str_replace('./websites/'.$this->app->current_website_name.'/www/', '', $value);
		});

		$form = $this->add('Form');
		$base_file_field = $form->addField('DropDown','base_file')->setValueList(array_combine($html_files, $html_files) )->set('index.html');
		$form->addField('page_template_name')->set('default');
		
		$form->addField('DropDown','leave_un_touched')->setValueList(array_combine($html_files, $html_files))->setAttr('multiple',true);

		$form->addSubmit('Execute');

		if($form->isSubmitted()){
			$page_template_name = str_replace('.html', '', trim($form['page_template_name'])).'.html';

			if(file_exists($www_relative.'layout/'.$page_template_name))
				$form->displayError('page_template_name','File Already Exists');

			$this->app->redirect($this->app->url('./step2',['base_file'=>$form['base_file'],'page_template_name'=>$page_template_name,'leave_un_touched'=>$form['leave_un_touched'],'step'=>'2']))->execute();
		}

		
		// creating layout folder
		// read index.html (selected ?? dropdown)
		// get everything including body
			// - remove everything other than script in body 
		
		// open senitised content and let user remove js file included

		// remove title and meta keyword/description from head
		// add our own js widget atk block
		// add v-body and page-wrapper in body 
		// save as default.html (asked ?? dropdown) in layout from senitised content
		// pick every .html page in root (selected ?? checkboxes) 
			// get code in body - script tags
			// rewrite to file


		// echo "OKAY";
	}

	function page_step1_step2(){
		
		$this->app->stickyGET('base_file');
		$this->app->stickyGET('page_template_name');
		$this->app->stickyGET('leave_un_touched');

		$this->add('H1')->set('Define Page Template from '. $_GET['base_file']);
		$this->add('View')
					->addClass('alert alert-danger')
					->set('Select border elements that defines range of Page Temaplte, either select two nodes that belongs to PageTemplate as top-end and bottom-start that will contain pages in between them or select one node that will contain page');

		$www_absolute = getcwd().'/websites/'.$this->app->current_website_name.'/www/';
		$www_relative = './websites/'.$this->app->current_website_name.'/www/';

		$page_structure_vp = $this->add('VirtualPage');
		$page_structure_vp->set([$this,'generatePageStructure']);

		$this->js()->_load('jstree\dist\jstree.min');
		$this->js()->_load('xepanjstree');
		$this->app->jui->addStaticStyleSheet('jstree/dist/themes/default/style.min');
		
		// $this->js(true)->univ()->frameURL($page_structure_vp->getURL());
		// return;

		$base_file = file_get_contents($www_relative.$_GET['base_file']);
		
		
		$this->structure = $this->getHtmlStructure($base_file);
		
		if(!$this->structure[0]['children']) $this->add('View_Error')->addClass('alert alert-danger')->set('Page bes not have body tag or body tag children');

		// echo "<pre>";
		// print_r($this->structure);
		// echo "</pre>";
		// exit();

		$v = $this->add('View');
		$v->js(true)->univ()->placeTemplateContentRegion($v,$this->structure,$this->app->url('xepan_cms_websites_verifyselectors'),$this->app->url('xepan_cms_websites_preparePageTemaplate'));

	}

	function getHtmlStructure($file,$root_node='body'){
		$this->pq = new phpQuery();
		$dom = $this->pq->newDocument($file);
		$arr=[];
		foreach ($dom->find($root_node) as $ch) {
			$attributes="";
			foreach ($ch->attributes as $attr) {
				$attributes .=" $attr->name = '$attr->value'";
			}
			$arr[] = [
						'id'=>'x'.uniqid(),
						'text'=>"&lt;$ch->tagName $attributes&gt; ",
						'children'=> $this->getHtmlChildren($ch)
					];
		}

		return $arr;
	}

	function getHtmlChildren($dom){
		$dom = $this->pq->pq($dom);
		$children=[];
		foreach ($dom['> *'] as $ch) {
			$attributes="";
			foreach ($ch->attributes as $attr) {
				$attributes .=" $attr->name = '$attr->value'";
			}
			$id='x'.uniqid();
			$children[] = [
							'id'=>$id,
							'text'=>"&lt;$ch->tagName $attributes&gt; " ,
							'children'=>$this->getHtmlChildren($this->pq->pq($ch)),
						// 'li_attr'=>['title'=>'HIII']
					];
		}
		if(count($children))
			return $children;
		if($dom->text()){
			$id='x'.uniqid();
			$children[]=['id'=>$id,'text'=>$dom->text(),'children'=>false];
			return $children;
		}

		return false;
	}

	function page_verifyselectors(){
		
		$this->app->stickyGET('base_file');
		$this->app->stickyGET('page_template_name');
		$this->app->stickyGET('leave_un_touched');
		$this->app->stickyGET('page_content_border');
		
		$this->add('View_Console')->set(function($c){
			$c->out('Starting checking if selectors are present in all selected files');

			$www_absolute = getcwd().'/websites/'.$this->app->current_website_name.'/www/';
			$www_relative = './websites/'.$this->app->current_website_name.'/www/';

			$page_content_border = $this->app->stickyGET('page_content_border');
			$page_content_border = json_decode($_GET['page_content_border']);

			$start_selector = $end_selector = null;

			switch (count($page_content_border)) {
				case 1:
					$start_selector = $this->getSelector($page_content_border[0]);
					break;
				case 2:
					$start_selector = $this->getSelector($page_content_border[0]);
					$end_selector = $this->getSelector($page_content_border[1]);
					break;
				default:
					# code...
					echo "oops";
					break;
			}

			// Loop throgh all pages excluded intensionally and keep only page content
			
			$c->out('$start_selector = '.$start_selector);
			$c->out('$end_selector = '.$end_selector);
			
			$c->out('Starting to verify pages');

			$pq = new phpQuery();

			$html_files = glob('./websites/'.$this->app->current_website_name.'/www/*.html');
			$leave_un_touched = explode(",", $_GET['leave_un_touched']);
			$ok=true;
			foreach ($html_files as $file) {
					if(in_array(array_reverse(explode("/", $file))[0], $leave_un_touched)) continue;
					$not_found= '';
					$dom = $pq->newDocument(file_get_contents($file));
					if($start_selector && $end_selector){
						
						if($l=$dom[$start_selector]->length()){
							if($l>1){
								$c->err('Multiple objects found for "'. $start_selector. '" in '. $file);
								$ok=false; 	
							}
						}else{
							$not_found .= '"'.$start_selector.'"';
							$ok=false;
						}

						if($l=$dom[$end_selector]->length()){
							if($l>1) {
								$c->err('Multiple objects found for "'. $end_selector. '" in '. $file);
								$ok=false;
							}
						}else{
							$not_found .= ' and "'. $end_selector.'"';
							$ok=false;
						}

					}elseif($start_selector){
						if($l=$dom[$start_selector]->length()){
							if($l>1) {
								$c->err('Multiple objects found for "'. $start_selector. '" in '. $file);
								$ok=false;
							}
						}else{
							$not_found .='"'.$start_selector.'"';
							$ok=false;
						}
					}else{
						$c->err('No start_selector defined ?');
						$ok=false;
					}

					if($not_found !== '')
						$c->err($file . " does not contains element $not_found");
					else
						$c->out("$file will be parsed easily");
					
				}

			$c->out('Pages checked');

			if(!$ok){
				$c->err("========");
				$c->err("Solve Error First or mark error pages un-touched in previous page");
				$c->err("========");
				$c->jsEval($this->js(true)->_selector('#replace_button')->show()->removeClass('btn-success')->addClass('btn-danger'));
			}else{
				$c->out('========');
				$c->out('Close this window and proceed to create page Template and prepare pages');
				$c->out('========');
				$c->jsEval($this->js(true)->_selector('#replace_button')->show()->removeClass('btn-danger')->addClass('btn-success'));
					
			}


		});
		
		// $this->js()->univ()->successMessage('DONE')->execute();

		// exit;
	}

	function page_preparePageTemaplate(){
		
		$this->app->stickyGET('base_file');
		$this->app->stickyGET('page_template_name');
		$this->app->stickyGET('leave_un_touched');
		$this->app->stickyGET('page_content_border');
		
		$this->add('View_Console')->set(function($c){
			$c->out('Starting');
			$www_absolute = getcwd().'/websites/'.$this->app->current_website_name.'/www/';
			$www_relative = './websites/'.$this->app->current_website_name.'/www/';

			$page_content_border = $this->app->stickyGET('page_content_border');
			$page_content_border = json_decode($_GET['page_content_border']);


			$base_file = file_get_contents($www_relative.$_GET['base_file']);
			
			$pq = new phpQuery();
			$dom = $pq->newDocument($base_file);

			// ===============  Seperate Page Template and Populate required tags/things in Page Template ======== 
			$template_content_block = '<div xepan-component="xepan/cms/Tool_TemplateContentRegion" class="xepan-component xepan-page-wrapper xepan-sortable-component">{$Content}</div>';
			
			$start_selector = $end_selector = null;

			switch (count($page_content_border)) {
				case 1:
					$start_selector = $this->getSelector($page_content_border[0]);
					$dom[$start_selector]->html($template_content_block);
					break;
				case 2:
					$start_selector = $this->getSelector($page_content_border[0]);
					$end_selector = $this->getSelector($page_content_border[1]);
					
					foreach($dom[$start_selector] as $d){
						while(!$pq->pq($d)->next()->is($end_selector)){
							$pq->pq($d)->next()->remove();
						}
					}
					$dom[$start_selector]->after($template_content_block);

					break;
				default:
					# code...
					echo "oops";
					break;
			}
			$c->out('Selectors identified as '.$start_selector. ' '. $end_selector);
			$c->out('Tool_TemplateContentRegion added');
			
			$pq->pq($dom['title'])->remove();
			$pq->pq($dom['meta[name="description"]'])->remove();
			$pq->pq($dom['meta[name="keywords"]'])->remove();

			$pq->pq('<meta name="description" content="{meta_description}{/}">
					<meta name="keywords" content="{meta_keywords}{/}">
					<title>{title}xEpan CMS{/}</title>
					<!--xEpan-ATK-Header-Start
					 {$js_block}
					 {$js_include}
					 <script type="text/javascript">
					 $(function(){
					 {$document_ready}
					 });
					 </script>
					 <script src="//cdnjs.cloudflare.com/ajax/libs/cookieconsent2/1.0.9/cookieconsent.min.js"></script>
					 xEpan-ATK-Header-End-->')
				->prependTo('head');

			$dom['body']->html('<div class="xepan-v-body xepan-component xepan-sortable-component" xepan-component-name="Main Body">'.$dom['body']->html().'</div>');
			// $template_html = $dom->html();
			// echo "\n -- new template ----\n";
			// echo $template_html;

			$page_template_file = $www_relative.'layout/'.$_GET['page_template_name'];
			if(!file_exists($www_relative.'layout')) \Nette\Utils\FileSystem::createDir($www_relative.'layout');
			$html = $dom->html();
			$jquery_file_pattern =  '(<script\s+src\s*=\s*[\'"](.*)jquery(\.|\d|\-|min)*\.js[\'"]\s*\>\s*<\/script>)';
			
			$html = preg_replace($jquery_file_pattern, '', $html);

			file_put_contents($page_template_file, str_replace("{}", "{ }", str_replace('</body>', '</body>{$after_body_code}', $html)));

			$this->add('xepan\cms\Model_Webpage')->deleteAll();

			$template_model = $this->add('xepan\cms\Model_Template');
			$template_model->addCondition('name',$_GET['page_template_name']);
			$template_model->addCondition('path',$_GET['page_template_name']);
			$template_model->tryLoadAny();
			$template_model->save();

			$c->out('layout/'. $_GET['page_template_name'].' saved');

			// ===============  END OF : Seperate Page Template and Populate required tags/things in Page Template ======== 

			// Loop throgh all pages excluded intensionally and keep only page content
			
			$c->out('Starting to clear pages');

			$html_files = glob('./websites/'.$this->app->current_website_name.'/www/*.html');
			$leave_un_touched = explode(",", $_GET['leave_un_touched']);
			foreach ($html_files as $file) {
					if(in_array(array_reverse(explode("/", $file))[0], $leave_un_touched)) continue;
					$c->out('Working on '. $file);
					// $c->out('<pre>'.htmlentities(file_get_contents($file)).'</pre>');
					$dom = $pq->newDocument(file_get_contents($file));
					// $pq->pq($dom['body > script'])->remove();
					// $content=$dom['body']->html();
					$content="";
					if($start_selector && $end_selector){
						$c->out('$start_selector = '.$start_selector);
						$c->out('$end_selector = '.$end_selector);
						foreach($dom[$start_selector]->nextAll() as $d){
							if($pq->pq($d)->is($end_selector)) break;
							// $c->out('<pre>'.htmlentities($pq->pq($d)->htmlOuter()).'</pre>');
							$pq->pq($d)->addClass('xepan-component');
							$content .= $pq->pq($d)->htmlOuter();
						}
					}elseif($start_selector){
						foreach($dom[$start_selector] as $d){
							$content .= $pq->pq($d)->html();
						}
					}else{
						$c->err('No start_selector defined ?');
						return;
					}
					// $c->out('<pre>'.$content.'</pre>');
					$content = str_replace("{}", "{ }", $content);
					$c->out('Celared Content made for page '. $file);

					if($content){
						file_put_contents($file, $content);
						$temp_array = explode("/", $file);
						$page_name = end($temp_array);
						$page_name = str_replace(".html", "", $page_name);

						$page_model = $this->add('xepan\cms\Model_Webpage');
						$page_model->addCondition('name',$page_name);
						$page_model->addCondition('path',$page_name);
						$page_model->tryLoadAny();
						$page_model['template_id'] = $template_model->id;
						$page_model->saveAndUnload();
						$c->out($file.' Saved');
					}
					// echo ($file."<br/>".$dom['body']->html());
				}

			$c->out('Pages cleared');
			$c->jsEval($this->js()->show()->_selector('.manage_pages_btn'));
			$c->jsEval($this->js()->show()->_selector('.create_another_page_template_btn'));
			$c->jsEval($this->js()->show()->_selector('.file_manager_btn'));

		});
		

		$this->add('Button')->set('1. Pages & Templates')->addClass('btn btn-primary manage_pages_btn')->setStyle('display','none')
			->js('click')->univ()->frameURL($this->app->url('xepan_cms_cmspagemanager'));
		
		$this->add('Button')->set('Go To FileManager')->addClass('btn btn-primary file_manager_btn')->setStyle('display','none')
			->js('click')->univ()->redirect($this->app->url('xepan_cms_websites'));

		$this->add('Button')->set('[Create Another Page Template]')->addClass('btn btn-primary create_another_page_template_btn')->setStyle('display','none')
			->js('click')->univ()->redirect($this->app->url('xepan_cms_websites_step1'));
		
	}

	function page_component_creator(){
		$file_selected = $this->app->stickyGET('file');


		$menu = $this->app->page_top_right_button_set->add('Button')->set('Pages'.($file_selected?': '.$file_selected:''))->addClass('btn btn-primary')->addMenu()->setStyle(['position'=>'absolute','height'=>'300px','overflow-y'=>'scroll']);
		$html_files = glob('./websites/'.$this->app->current_website_name.'/www/*.html');
		$p_template_files = glob('./websites/'.$this->app->current_website_name.'/www/layout/*.html');

		foreach ($html_files as $file) {
			$file = str_replace('./websites/'.$this->app->current_website_name.'/www/', '', $file);
			$menu->addMenuItem($this->app->url(null,['file'=>$file]),$file);
		}

		foreach ($p_template_files as $file) {
			$file = str_replace('./websites/'.$this->app->current_website_name.'/www/', '', $file);
			$menu->addMenuItem($this->app->url(null,['file'=>$file]),$file);
		}

		if(!$file_selected){
			$this->add('View')->addClass('alert alert-danger')->set('Please Select any page from Top Bar Pages button');
			return;
		}
		
		$cols = $this->add('Columns');
		$tree_col = $cols->addColumn(4);
		$preview_col = $cols->addColumn(8);

		$this->structure = $this->getHtmlStructure(file_get_contents('./websites/'.$this->app->current_website_name.'/www/'.$file_selected),'> *');
		// echo htmlentities(file_get_contents('./websites/'.$this->app->current_website_name.'/www/'.$file_selected));
		// echo "<pre>";
		// print_r($this->structure);
		// echo "</pre>";
		// exit();
		$this->js()->_load('jstree\dist\jstree.min');
		$this->js()->_load('xepanjstree');
		$this->app->jui->addStaticStyleSheet('jstree/dist/themes/default/style.min');

		$v = $tree_col->add('View');
		$v->js(true)->univ()->placeTemplateContentRegion($v,$this->structure,$this->app->url('xepan_cms_websites_verifyselectors'),$this->app->url('xepan_cms_websites_preparePageTemaplate'));

	}

	function getSelector($html_tag_string){
		$tag = html_entity_decode($html_tag_string);
		preg_match_all('%\s+id\s*=\s*[\'"]?([a-zA-Z0-9\-_]*)[\'"]?\s*%i', $tag,$match);
		$selector = '#'.@$match[1][0];
		if($selector == '#'){
			preg_match_all('%<([a-zA-Z]+)\s+class\s*=\s*[\'"]?([a-zA-Z0-9\-_\s]*)[\'"]?\s*%i', $tag,$match);
			$selector = $match[1][0].'.'.preg_replace("/\s+/", '.', isset($match[2][0])?$match[2][0]:"");
		}if($selector==$match[1][0].'.'){
			preg_match_all('%<([a-zA-Z]+)\s*(.*)>%i', $tag,$match);
			$selector=$match[1][0];
		}
		return $selector;
	}


	function page_previousthemes(){

		$m = $this->add('Model');
		$m->addField('name');

		$m->addHook('beforeDelete',function($m){
	        if(file_exists($m['name'])){	        	
	            \Nette\Utils\FileSystem::delete($m['name']);
	        }
    	});

		// $p = scandir('websites/'.$this->app->current_website_name);
		$p = glob('websites/'.$this->app->current_website_name.'/*',GLOB_ONLYDIR);
		$p =array_filter($p,function($v){
			return strpos($v, 'www-') !== false;
		});
        arsort($p);
        $m->setSource('Array',$p);

		$crud = $this->add('xepan\hr\CRUD',['allow_add'=>false,'allow_edit'=>false,'pass_acl'=>true]);
		$crud->setModel($m);
		$crud->grid->removeColumn('id');
		$crud->grid->addColumn('Button','Revert');

		if($_GET['Revert']){
			$m->load($_GET['Revert']);
			\Nette\Utils\FileSystem::delete('./websites/'.$this->app->current_website_name.'/www-before_revert');
			\Nette\Utils\FileSystem::rename('./websites/'.$this->app->current_website_name.'/www','./websites/'.$this->app->current_website_name.'/www-before_revert');
			\Nette\Utils\FileSystem::createDir('./websites/'.$this->app->current_website_name.'/www');
			\Nette\Utils\FileSystem::copy($m['name'],'./websites/'.$this->app->current_website_name.'/www',true);
			$this->js()->univ()->successMessage($m['name'].' Is copied to www and old www is saved in www-before_revert')->execute();
		}

	}
}