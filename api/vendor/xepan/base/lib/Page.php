<?php


namespace xepan\base;

class Page extends \Page {
	
	public $breadcrumb=[
						'Dashboard'=>'/'
					];
	public $allow_frontend = false;
	function init(){
		parent::init();

		$allowed_scope = ['AdminUser','SuperUser'];
		if($this->allow_frontend)
			$allowed_scope[] = 'WebsiteUser';
		
		if(!$this->app->auth->isLoggedIn() || !in_array($this->app->auth->model['scope'] , $allowed_scope)){
			throw $this->exception('You are not authorised to access this page')
						->addMoreInfo('User Type',$this->app->auth->model['scope'])
						->addMoreInfo('User_id',$this->app->auth->model->id)
						->addMoreInfo('User',$this->app->auth->model['name'])
						->addMoreInfo('isLoggedIn',$this->app->auth->isLoggedIn())
						->addMoreInfo('page',$this->app->page)
						;

		}

		if(	$this->app->getConfig('allowed_menu_based_acl_check',false) &&
			// !$this->app->auth->model->isSuperUser() && 
			!$this->app->isAjaxOutput() && 
			!$this->app->inConfigurationMode && 
			!in_array($this->app->page,[
							'index',
							'xepan_base_configurationmode',
							'xepan_base_logout',
							'xepan_hr_employee_leave',
							'xepan_projects_mytasks',
							'xepan_projects_todaytimesheet',
							'xepan_marketing_mylead',
							'xepan_crm_solution',
						])
		){
			$no_menu_access=true;
			foreach ($this->app->top_menu_array as $TopMenu => $pages) {
				foreach ($pages as $p) {
					if($this->app->page == $p['url']) {
						$no_menu_access=false;						
						break 2;
					}
				}
			}
			if($no_menu_access){
				if($this->template->hasTag('Content'))
					$this->add('View_Error')->set('This Menu is not allowed to you ');
				else
					$this->js(true)->univ()->errorMessage('This Menu is not allowed to you');
				throw $this->exception("",'StopInit');
			}
		}


		if($this->app->is_admin){
			$breadcrumbs = [];
			foreach ($this->breadcrumb as $title => $url) {
				$active='';
				if($url=='#'){
					$active='active';
				}
				else{
					if(is_array($url)){
						$_url = $url[0];
						unset($url[0]);
						$_params=$url;
						$url = $this->app->url($url,$_params);
					}else{
						$url = $this->app->url($url);
					}
				}

				$breadcrumbs[] = ['title'=>$title,'url'=>$url,'active'=>$active];
			}

			if(abs(strtotime(date('Y-m-d H:i:s',strtotime($this->api->now))) - strtotime(date('Y-m-d H:i:s'))) > 10) {
				$breadcrumbs[] = ['title'=>$this->app->now, 'url'=>'#', 'active'=>'label-danger'];
			}

			$br = $this->app->layout->add('CompleteLister',null,'breadcrumb',['layout/cube','breadcrumb']);
			$br->setSource($breadcrumbs);

			$intro_vp = $this->app->add('VirtualPage');
			$intro_vp->set(function($page){
				if(!$doc=$this->app->getConfig('documentation/'.$this->app->page,false)){
					$page->add('View_Error')->set('No Documentation is defined for this page yet ['.$this->app->page.']');
					throw $this->exception('','StopInit');
					return;
				}

				if(isset($doc['title'])) $page->add('H3')->set($doc['title']);
				if(isset($doc['desc'])) $page->add('View')->set($doc['desc']);
				$page->add('HR');

				foreach ($doc['blocks'] as $title=>$block) {
					$page->add('H5')->set($title);
					$bg = $page->add('ButtonSet');
					foreach ($block as $key=>$block_content) {
						$b = $bg->addButton($key);
						if(isset($block_content['xec_page'])){							
								$b->js('click')->univ()->frameURL($this->app->url([$block_content['xec_page'],'admin_virtualpage'=>false]));
						}else{
							$vp = $b->add('VirtualPage');
							$vp->set(function($page)use($block_content){
								if(isset($block_content['video'])){
									$content= '<iframe width="560" height="315" src="'.$block_content['video'].'" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>';
								}
								if(isset($block_content['url'])){
									$content= file_get_contents($block_content['url']);
								}

								if(isset($block_content['file'])){
									$content= file_get_contents($block_content['file']);
								}

								$page->add('View')->setHTML($content);

							});

							$b->js('click')->univ()->frameURL($key,$vp->getURL());
						}
					}
				}

				// if(strpos($doc, 'http') ===0){
				// 	if(strpos($doc, 'youtube')){
				// 		$content= '<iframe width="560" height="315" src="'.$doc.'" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>';
				// 	}else{
				// 		$content = file_get_contents($doc);
				// 	}
				// }else{
				// 	$content = $doc;
				// }

				// $page->add('View')->setHTML($content);

			});
			if(!$this->app->isAjaxOutput()){
				$this->js('click',$this->js()->univ()->frameURL('Documentation / Help',$intro_vp->getURL()))->_selector('.run-page-intro');
			}
			// $this->js(true)->_load('intro.min')->_css('introjs.min')->_load('xintroJS');
			// $this->js('click',$this->js()->univ()->runIntro())->_selector('.run-page-intro');
		}

	}
}