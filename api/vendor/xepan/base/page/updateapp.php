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

class page_updateapp extends \xepan\base\Page {
	public $title='xEpan App Updator';

	public $application_namespace=null; //xepan\xyz style
	public $source_uri=null; // zip file path
	public $sub_path=null; // folder path inside zip to be extracted on destination

	function init(){
		parent::init();

		ini_set('memory_limit', '2048M');
		set_time_limit(0);

		$this->add('H2')->set('IMPORTANT NOTICE: Please take backup first before proceedings, both database and filesystems');
		if(!$this->application_namespace){
			throw $this->exception('Please set application_namespace like xepan\listing');
		}

		if(!$this->source_uri){
			throw $this->exception('Please set source_uri like https://github.com/xepan/listing/archive/master.zip');
		}

		$vp = $this->add('VirtualPage');
		$vp->set([$this,'updateZip']);

		$btn = $this->add('Button')->addClass('btn btn-primary btn-block')->set('UPDATE '.strtoupper($this->application_namespace));
		$btn->js('click')->univ()->frameURL('UPDATE RUNNNING, DO NO CLOSE THIS WINDOW',$vp->getURL());
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

			if(file_exists('./shared/apps/'.str_replace('\\', '/', $this->application_namespace))){
				$destination = 'shared/apps/'.str_replace('\\', '/', $this->application_namespace);
			}elseif(file_exists('./vendor/'.str_replace('\\', '/', $this->application_namespace))){
				$destination = 'vendor/'.str_replace('\\', '/', $this->application_namespace);
			}else{
				$c->err('Destination could not determined, exiting update');
				return;
			}

			if(file_exists($destination.'/.git')){
				$c->err('Looks like '.$destination.' is managed by git repositories, update by git manually');
				$c->err('NOT UPDATING');
				return;
			}			

			chdir($destination);
			$c->out('Moved to '. $destination);
			$c->out('Downloading zip from '.$this->source_uri);
			
			$this->c = $c;

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $this->source_uri);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, array($this, 'progress'));
			$data = curl_exec ($ch);
			curl_close ($ch);
			// save as wordpress.zip
			$c->out('File downloaded, saving to local zip');
			
			$file_destination = "master.zip"; // NEW FILE LOCATION
			$file = fopen($file_destination, "w+");
			fputs($file, $data);
			fclose($file);

			$c->out('File Saved, extracting zip now ...');

			$zip = new \xepan\base\zip;
			if($this->sub_path)
				$res= $zip->extract_zip_subdir('master.zip', $this->sub_path, getcwd(), '/tmp');
			else
				$res = $zip->extractZip('master.zip',$destination); // zip datei
			if ($res === TRUE) {
			    $c->out('Zip Extracted');
			    // unlink('master.zip');
			    // $c->out('master.zip removed');
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
}
