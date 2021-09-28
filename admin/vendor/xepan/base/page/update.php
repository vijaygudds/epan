<?php

/**
* description: ATK Page
* 
* @author : Gowrav Vishwakarma
* @email : gowravvishwakarma@gmail.com, info@xavoc.com
* @website : http://xepan.org
* 
*/

namespace xepan\base;

class page_update extends \xepan\base\Page {
	public $title='xEpan Updator';

	function init(){
		parent::init();

		// updates
        try{
            $updates = file_get_contents($this->app->getConfig('epan_api_base_path')."/updates");
            $this->app->layout->add('View',null,'page_top_center')->setHtml($updates);
        }catch(\Exception $e){

        }

		ini_set('memory_limit', '2048M');
		set_time_limit(0);

		$v = $this->add('View')->addClass('panel panel-warning')->addStyle('padding','10px');
		$v->add('H2')->set('IMPORTANT NOTICE: Please take backup first before proceedings, both database and filesystems');
		$v->add('H5')->set('"Backup Addon" is provided Free for now, Will be available as paid addon later');

		$cols = $v->add('Columns');
		$db_col = $cols->addColumn(6);
		$fs_col = $cols->addColumn(6);

		$db_vp = $this->add('VirtualPage');
		$db_vp->set([$this,'download_db']);
		$fs_vp = $this->add('VirtualPage');
		$fs_vp->set([$this,'download_fs']);

		$db_col->add('Button')
			->addClass('btn btn-primary btn-block')
			->set('Download Current Database Backup')
			->js('click')->univ()->frameURL('Download Database',$db_vp->getURL());
		$db_col->add('View')->setElement('a')->setAttr('href','?page=xepan_base_backup')->set('Db Backup History');
		$fs_col->add('Button')
			->addClass('btn btn-primary btn-block')
			->set('Download Current Files System')
			->js('click')->univ()->frameURL('Download FileSystem',$fs_vp->getURL());
		
		$this->add('HR');

		$vp = $this->add('VirtualPage');
		$vp->set([$this,'updateZip']);

		$btn = $this->add('Button')->addClass('btn btn-danger btn-block')->set('UPDATE XEPAN AND ALL APPLICATIONS');
		$btn->js('click')->univ()->frameURL('UPDATE RUNNNING, DO NOT CLOSE THIS WINDOW',$vp->getURL());
	}

	function updateZip($page){
		$page->add('View_Console')
		->set(function($c){
			
			if($this->app->epan['name'] != "www" || $this->app->getConfig('xepan-service-host',false) !== false){
				$c->err('You are not authorised or you are already on hosted service and do not requires to update.');
				return;
			}

			set_time_limit(0);

			chdir('..');
			$root = getcwd();

			if(file_exists('.git')){
				$c->err('Looks like it is managed by git repositories, update by git manually');
				$c->err('NOT UPDATED');
				return;
			}			

			$source = $this->app->getConfig('epan_base_path').'/xepan2.zip'; // THE FILE URL
			
			$c->out('Moved to '. $root);
			$c->out('Downloading zip from '.$source);
			
			$this->c = $c;

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $source);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, array($this, 'progress'));
			$data = curl_exec ($ch);
			curl_close ($ch);
			// save as wordpress.zip
			$c->out('File downloaded, saving to local zip');
			
			$destination = "xepan2.zip"; // NEW FILE LOCATION
			$file = fopen($destination, "w+");
			fputs($file, $data);
			fclose($file);

			$c->out('File Saved, extracting zip now ...');

			$zip = new \xepan\base\zip;
			$res = $zip->extractZip('xepan2.zip','.'); // zip datei
			if ($res === TRUE) {
			    $c->out('Zip Extracted');
			    unlink('xepan2.zip');
			    $c->out('xepan2.zip removed');
			} else {
			    $c->err('Unzip Error');
			}

		});
	}

	function progress($resource, $downloadSize, $downloaded, $uploadSize, $uploaded)
	{
	    $this->c->out('Download Status', $downloadSize .'/'.$downloaded);
	    // emit the progress
	    // Cache::put('download_status', [
	    //     'resource' => $resource,
	    //     'download_size' => $downloadSize,
	    //     'downloaded' => $downloaded,
	    //     'upload_size' => $uploadSize,
	    //     'uploaded' => $uploaded
	    // ], 10);
	}

	function checkIfCommandExists($cmd){
	    $prefix = strpos(strtolower(PHP_OS),'win') > -1 ? 'where' : 'which';
	    exec("{$prefix} {$cmd}", $output, $returnVal);
	    $returnVal !== 0;
	}


	function download_db($page){
		$page->add('View_Console')
			->set(function($c){
				$c->out('Wait taking up backup');
				$path = 'websites/'.$this->app->epan['name'];
				$model = $this->add('xepan\base\Model_Backup');
				$model->save();
				$c->out('Database Backup Done, File should start download automatically, if not, please visit <a href="?page=xepan_base_backup">Backup History Page</a>');
				
				$c->jsEval($this->js()->univ()->location($path.'/backup/'.$model['name']));
			});
	}

	function download_fs($page){
		$page->add('View_Console')
			->set(function($c){
				set_time_limit(0);

				$path = 'websites/'.$this->app->epan['name'];
				chdir($path);
				
				$c->out('In Dir <b>'.getcwd().'</b>');

				$file_name = 'www/fs_backup.zip';

				if(file_exists($file_name)) unlink($file_name);

				$zip_cmd= "zip -r $file_name . --exclude *.svn* --exclude *.git* --exclude *.DS_Store* --exclude *.zip* --exclude config.php";
				$c->out('Compressing <b>'.$path.'</b>');
				$output = shell_exec($zip_cmd);
				$c->out("output:<br/> <pre>$output</pre>");

				$c->out('Last file system back is available in your "www" folder, delete immediately after download for security reasons');
				$c->out('Download of file should start automatically, if not <a href="'.$path.'/'.$file_name.'">click here</a> to download file');

				$c->jsEval($this->js()->univ()->location($path.'/'.$file_name));

			});

	}


	function updateGit($page){
		$page->add('View_Console')
		->set(function($c){
			if($this->app->epan['name'] != "www"){
				$c->err('You are not authorised or you are already on hosted service and do not requires to update.');
				return;
			}
			try{
				set_time_limit(0);

				chdir('..');
				
				$root = getcwd();

				$c->err('Root in start is '. $root);

				// update root
				$c->out('In Dir <b>'. getcwd() .'</b><br/>');
				$c->out('Pulling origin master <br/>');
				$output= shell_exec('git init 2>&1');
				$output= shell_exec('git remote remove origin 2>&1');
				$output= shell_exec('git remote add origin https://github.com/xepan/xepan2.git 2>&1');
				$output= shell_exec('git fetch --all 2>&1');
				$output.= shell_exec('git reset --hard origin/master 2>&1');
				// $output.= shell_exec('git pull origin master 2>&1');
				$c->out("output:<br/> <pre>$output</pre>");

				// update base app and call admin first
				chdir('vendor/xepan/base');
				$c->out('In Dir <b>'. getcwd() .'</b><br/>');
				$c->out('Pulling origin master <br/>');
				$output= shell_exec('git checkout origin master && git reset --hard origin/master && git pull origin master');
				$c->out("output:<br/> <pre>$output</pre>");
				
				chdir($root);

				$c->out('***** BASE DONE NOW RUNNING ADMIN WITH WGET ******');
				$c->out('wget -O /dev/null -o /dev/null "'.$this->app->url('/')->absolute().'"');
				$output = shell_exec('wget -O /dev/null -o /dev/null "'.$this->app->url('/')->absolute().'"');
				$c->out("output:<br/> <pre>$output</pre>");

				$apps = array_column($this->add('xepan\base\Model_Epan_InstalledApplication')->getRows(),'application_namespace');
				foreach ($apps as $app_namespace) {
					$path="";
					if(file_exists('./vendor/'.str_replace("\\", "/", $app_namespace))){
						$path = './vendor/'.str_replace("\\", "/", $app_namespace);
					}
					elseif(file_exists('./shared/apps/'.str_replace("\\", "/", $app_namespace))) {
						$path = './shared/apps/'.str_replace("\\", "/", $app_namespace);
					}

					chdir($root);
					chdir($path);

					$c->out('In Dir <b>'. getcwd() .'</b><br/>');
					$c->out('Pulling origin master <br/>');
					$output= shell_exec('git checkout origin master && git reset --hard origin/master && git pull origin master');
					$c->out("output:<br/> <pre>$output</pre>");

					chdir($root);
				}

			}catch(\Exception $e){
				$c->err($path);
				$c->out($e->getMessage());
			}

		});
	}

}
