<?php
namespace xepan\cms;

class View_Theme extends \View{
	public $epan_template;
	public $apply_theme_on_website;
	public $apply_theme_on_website_id=0;
	public $dashboard_page = "customer-dashboard";
	public $show_preview_button = 1;
	public $show_applynow_button = 1;
	public $show_search = 1;
	public $show_theme_of_category=0; // "comma seperated category ids"
	public $show_status = 'published'; // all, published, unpublished

	function init(){
		parent::init();
		
		$this->app->readConfig('websites/www/config.php');
		// $this->app->readConfig('websites/'.$this->app->current_website_name.'/config.php');
        $this->app->dbConnect();

        $cat_id = $this->app->stickyGET('epan_category_id');
        if($cat_id){
        	$model = $this->epan_template = $this->app->db->dsql()
	        					->table('epan')
	        					->join('epan_category_association.epan_id')
	        					->where('epan_category_association.epan_category_id',$cat_id);

	        					// ->where('is_published',1)
        }else{
	        $model = $this->epan_template = $this->app->db->dsql()
	        					->table('epan');
	        					// ->where('is_published',1);
	        					// ->where('is_template',1)
	        					// ->get();
        }

        if($this->show_status == "published"){
        	$model->where('is_published',1);
        }elseif($this->show_status == "unpublished"){
        	$model->where('is_published',0);
        }
        
        $this->epan_template = $epan_template = $model->where('is_template',1)
									->get();

		// $this->app->print_r($epan_template,true);

        $epan_category = $this->epan_category = $this->app->db->dsql()->table('epan_category')->where('status','Active')->get();

        $category = [0=>'All'];
        foreach ($epan_category as $key => $array) {
        	$category[$array['id']] = $array['name'];
        }

        $this->epan_category = $category;

        if($this->show_search){
	        $form = $this->add('Form');
	        $form->add('xepan\base\Controller_FLC')
	        	->makePanelsCoppalsible(true)
		        ->layout([
		        		'category'=>'Filter~c1~8~closed',
		        		'FormButtons~&nbsp;'=>'c2~4'
		        	])
		        ;
	        $form->addField('xepan\base\DropDown','category')->setValueList($category);
	        $form->addSubmit('Filter')->addClass('btn btn-primary btn-block');
        }

        $temp = [];
        foreach ($epan_template as $key => $array) {
        	$temp[$array['id']] = $array;
        }
        $this->epan_template = $temp;

        $grid = $this->add('xepan\hr\Grid',null,null,['grid/theme'])->addClass('xepan-theme-grid');
        $grid->setSource($this->epan_template);
        $grid->addColumn('name');
        $grid->addColumn('preview_image');
        $grid->addColumn('ApplyNow');
        $grid->addColumn('preview');

        if($this->show_search){
        	if($form->isSubmitted()){
        		$grid->js()->reload(['epan_category_id'=>$form['category']])->execute();
        	}
        }
        // 	$grid->addQuickSearch(['name']);
        // }else{
        $grid->template->tryDel('filter_wrapper');
        
        $this->app->readConfig('websites/'.$this->app->current_website_name.'/config.php');
        $this->app->dbConnect();


        $this->url = $url = "{$_SERVER['HTTP_HOST']}";        
        $this->protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';
        $this->domain = $domain = str_replace('www.','',$this->app->extract_domain($url))?:'www';
        $this->sub_domain = $sub_domain = str_replace('www.','',$this->app->extract_subdomains($url))?:'www';
        
        $grid->addHook('formatRow',function($g)use($domain,$sub_domain){
			$g->current_row_html['preview'] = '<a class="btn btn-primary" target="_blank" href="http://www.'.$g->model['name'].'.'.$domain.'">Preview</a>';
			$g->current_row_html['preview_image'] = '<div style="height:300px;overflow:auto;"><a target="_blank" href="http://www.'.$g->model['name'].'.'.$domain.'"><img alt=" we are uploading preview image of '.$g->model['name'].'" style="width:250px;" src="./websites/'.$g->model['name'].'/www/img/template_preview.png" /></img></a></div>';
		});

        if($this->show_applynow_button){
			$grid->add('VirtualPage')
				->addColumn('ApplyNow')
				->set(function($page){

					$id = $_GET[$page->short_name.'_id'];
					
					if(!$id){
						$page->add('View')->set('some thing went wrong.')->addClass('alert alert-danger');
						return;
					}
					
					$form = $page->add('Form');
					$form->add('View')->set('are you sure, installing new theme will remove all content ?')->addClass('alert alert-info');
					$form->addSubmit('Yes, Install Theme');
					if($form->isSubmitted()){
						$js_event = [];
						try{
							$selected_template = $this->epan_template[$id];

							if(!file_exists(realpath($this->app->pathfinder->base_location->base_path.'/websites/'.$selected_template['name']))){
								throw $this->exception('Template not found')
											->addMoreInfo('epan',$selected_template['name']);
							}

							if($selected_template == $this->app->current_website_name){
								throw new \Exception("Cannot apply same theme on same epan", 1);
							}

							$apply_theme_epan_name = $this->app->current_website_name;
							if($this->apply_theme_on_website){
								$apply_theme_epan_name = $this->apply_theme_on_website;
							}

							// first delete folder
							$new_name = uniqid('www-').'-'.$this->app->now;
							if(file_exists(realpath($this->app->pathfinder->base_location->base_path.'/websites/'.$apply_theme_epan_name.'/www'))){
								\Nette\Utils\FileSystem::rename('./websites/'.$apply_theme_epan_name.'/www','./websites/'.$apply_theme_epan_name.'/'.$new_name);
							}
							// \Nette\Utils\FileSystem::delete('./websites/www/www');
							$fs = \Nette\Utils\FileSystem::createDir('./websites/'.$apply_theme_epan_name.'/www');
							$fs = \Nette\Utils\FileSystem::copy('./websites/'.$selected_template['name'].'/www','./websites/'.$apply_theme_epan_name.'/www',true);
							
							if($this->apply_theme_on_website_id){
								$js_event =[
										/* stopped jumping to new window and new website as this mislead them the process to login on epan.in and edit website */
										// $form->js()->univ()->newWindow($this->protocol.$apply_theme_epan_name.".".$this->domain),
										$form->js()->univ()->location($this->app->url($this->dashboard_page,['message'=>'New Epan Created, Visit or Edit from the below list']))
									];
							}else{
								$js_event[] = $form->js()->univ()->location()->reload();
							}

							$this->app->readConfig('websites/www/config.php');
					        $this->app->dbConnect();
							$this->add('xepan\base\Model_Epan')->tryLoadBy('name',$apply_theme_epan_name)->set('xepan_template_id',$id)->save();

							$this->app->readConfig('websites/'.$this->app->current_website_name.'/config.php');
					        $this->app->dbConnect();

							$this->add('xepan\base\Model_Epan')->tryLoadBy('name',$apply_theme_epan_name)->set('xepan_template_id',$id)->save();
							// theme applied hook
							$this->app->Hook('ThemeApplied');

						}catch(\Exception $e){
							$js_event[] = $form->js()->univ()->errorMessage("theme not apply, ".$e->getMessage());
						}
						
						$form->js(null,$js_event)->execute();
					}
			});
        }

	}
}