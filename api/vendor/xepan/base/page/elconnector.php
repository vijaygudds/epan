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


class page_elconnector extends \Page {
	public $title='Page Title';

	function init(){
		parent::init();

		if(!$this->app->auth->isLoggedIn()) return;

		$path_asset = $this->app->pathfinder->base_location->base_path.'/websites/'.$this->app->current_website_name.'/assets';
		$path_www = $this->app->pathfinder->base_location->base_path.'/websites/'.$this->app->current_website_name.'/www';
		
		$folder = getcwd().'/websites/'.$this->app->epan['name'].'/';
        $folder=str_replace('admin/', '', $folder);
        $size = $this->uf_getDirSize($folder,'b');
        
        $uploadMaxSize = '20M';
		$availabe_space = '20M';       
        if($size){
            $extra_info = $this->app->recall('epan_extra_info_array',false);        
            if(isset($extra_info ['specification']['Storage Limit']) && $extra_info ['specification']['Storage Limit'])
                $total_storage_limit = $extra_info ['specification']['Storage Limit'];
            else
                $total_storage_limit = $this->app->byte2human(disk_free_space("/"));

	        $availabe_space = max($this->app->human2byte($total_storage_limit)-$this->app->human2byte($size),0);
        }else{
        	
            // Might be windows, we are not yet ready for windows serves and this majorly need to be checked on hosted services ... so its okay to skip here
        }

        // die($total_storage_limit.' '. $size. ' '.$uploadMaxSize. ' '.$this->app->byte2human($availabe_space));
        $uploadMaxSize = (min($availabe_space, $this->app->human2byte($uploadMaxSize))+1).'b'; // Making perfect 0 makes checking disabled 

		\elFinder::$netDrivers['ftp'] = 'FTP';
		\elFinder::$netDrivers['dropbox'] = 'Dropbox';

		$roots=array(
		        array(
		            'driver' => 'LocalFileSystem',
		            'path'   => $path_www,
		            'URL'    => 'websites/'.$this->app->current_website_name.'/www',
		            'uploadMaxSize'=>$uploadMaxSize,
		            'plugin' => array(
		                'Sanitizer' => array(
		                    'enable' => true,
		                    'targets'  => array('\\','/',':','*','?','"','<','>','|',' '), // target chars
		                    'replace'  => '_'    // replace to this
		                )
		            )
		        ),
		        array(
		            'driver' => 'LocalFileSystem',
		            'path'   => $path_asset,
		            'URL'    => 'websites/'.$this->app->current_website_name.'/assets',
		            'uploadMaxSize'=>$uploadMaxSize,
		            'plugin' => array(
		                'Sanitizer' => array(
		                    'enable' => true,
		                    'targets'  => array('\\','/',':','*','?','"','<','>','|',' '), // target chars
		                    'replace'  => '_'    // replace to this
		                )
		            )
		        )
		    );

		if($_GET['www_root']){
			$roots=array(
		        array(
		            'driver' => 'LocalFileSystem',
		            'path'   => $path_www,
		            'URL'    => 'websites/'.$this->app->current_website_name.'/www',
		            'plugin' => array(
		                'Sanitizer' => array(
		                    'enable' => true,
		                    'targets'  => array('\\','/',':','*','?','"','<','>','|',' '), // target chars
		                    'replace'  => '_'    // replace to this
		                )
		            )
		        )
		    );
		}
		
		$opts = array(
			'bind' => array(
	 			'upload.pre mkdir.pre mkfile.pre rename.pre archive.pre ls.pre' => array(
	 				'Plugin.Sanitizer.cmdPreprocess'
	 			),
	 			'ls' => array(
	 				'Plugin.Sanitizer.cmdPostprocess'
	 			),
	 			'upload.presave' => array(
	 				'Plugin.Sanitizer.onUpLoadPreSave'
	 			)
	 		),
		    'locale' => '',
		    'roots'  => $roots
		);

		// run elFinder
		$connector = new \elFinderConnector(new \elFinder($opts));
		$connector->run();
		exit;
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
}