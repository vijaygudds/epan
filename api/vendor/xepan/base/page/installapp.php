<?php


namespace xepan\base;


class page_installapp extends \xepan\base\Page {
	public $table = "Install custom app from git account";

	function init(){
		parent::init();

		$this->install_app_vp = $this->add('VirtualPage');
		$this->install_app_vp->set([$this,'install_custom_app']);

		$form = $this->add('Form');
		$form->addField('namespace');
		$form->addField('git_path');

		$form->addSubmit('Install');

		if($form->isSubmitted()){
			$n_a = explode('\\', $form['namespace']);
			if(count($n_a) != 2 || !is_string($n_a[0]) || !is_string($n_a[1]))
				$form->displayError('namespace','namespace format should be \'company\application\'');
			$form->js()->univ()->frameURL('Installing '. $form['namespace'],$this->app->url($this->install_app_vp->getURL(),$form->get()))->execute();
		}

	}

	function install_custom_app($page){
		$this->app->stickyGET('namespace');
		$this->app->stickyGET('git_path');

		$page->add('View_Console')->set(function($c){

			chdir('../shared');
			$c->out('Changing Path to '. getcwd());
			$c->out('Creating app folder if not exists');
			\Nette\Utils\FileSystem::createDir('apps');
			$c->out('App Folder created, creating namespace folder');
			$namespace_arr = explode("\\", $_GET['namespace']);
			\Nette\Utils\FileSystem::createDir('apps/'.$namespace_arr[0]);
			$c->out('Namespace folder created');
			chdir('apps/'.$namespace_arr[0]);
			$c->out('Cloing application now');
			
			$output= shell_exec('git clone '. $_GET['git_path'].' '. $namespace_arr[1]);
			$c->out('<pre>'.$output.'</pre>');

			$c->out('Installing application');
			$application = $this->add('xepan\base\Model_Application');
			$application->addCondition('namespace',$_GET['namespace']);
			$application->addCondition('name', ucwords($namespace_arr[1]));
			$application->tryLoadAny();

			$application['user_installable']=1;
			$application->save();

			try{
				$this->app->epan->installApp($application);
			}catch(\Exception $e){
				$c->err($e->getMessage());
			}

			$c->out('Back in '. getcwd());
			chdir('../../../admin');
			$c->out('Executing Initiator Reset DB');
			$this->add($_GET['namespace']."\Initiator")->resetDB();

			$c->out('Done');

		});
	}

}