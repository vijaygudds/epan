<?php

namespace xepan\cms;
use \Nette\Utils\Image;

class page_createlayout extends \Page{

	function page_index(){
		$return = ['status'=>'success','message'=>'layout created'];
				
		$file_name = $_POST['lname'].".html";
		$file_content = $_POST['lhtml'];
		$image_data = $_POST['img_data'];

        $url = "{$_SERVER['HTTP_HOST']}";
        $domain = str_replace('www.','',$this->app->extract_domain($url))?:'www';
        $sub_domain = str_replace('www.','',$this->app->extract_subdomains($url))?:'www';
		
		$base_path	= $this->app->pathfinder->base_location->base_path.'/websites/'.$this->app->current_website_name."/www";
		
		// $return['message'] = $image_data;
		// echo json_encode($return);
		// exit;
		// $image_data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $image_data));
		// $data = 'data:image/gif;base64,R0lGODlhEAAOALMAAOazToeHh0tLS/7LZv/0jvb29t/f3//Ub//ge8WSLf/rhf/3kdbW1mxsbP//mf///yH5BAAAAAAALAAAAAAQAA4AAARe8L1Ekyky67QZ1hLnjM5UUde0ECwLJoExKcppV0aCcGCmTIHEIUEqjgaORCMxIC6e0CcguWw6aFjsVMkkIr7g77ZKPJjPZqIyd7sJAgVGoEGv2xsBxqNgYPj/gAwXEQA7';

		// check websitelayout folder is exist or not
		if($this->app->epan['is_template']){
			$folder_name = 'themelayout';
		}else{
			$folder_name = 'customlayout';
		}

		// for template layout
		$folder_path = $base_path."/".$folder_name;
		if(!file_exists(realpath($folder_path))){
			\Nette\Utils\FileSystem::createDir('./websites/'.$this->app->current_website_name.'/www/'.$folder_name);
		}

		//check file with name is already exist
		$file_path = $folder_path."/".$file_name;
		if(file_exists(realpath($file_path))){
			$return['status'] = 'failed';
			$return['message'] = 'file with this is already exist';
			echo json_encode($return);
			exit;
		}

		// remove template/current website specific path, each website adds its path at run time already
		$domain = 'websites/'.$this->app->current_website_name.'/www/';
		$file_content = str_replace($domain, '', $file_content);

		$fs = \Nette\Utils\FileSystem::write('./websites/'.$this->app->current_website_name.'/www/'.$folder_name.'/'.$file_name,$file_content);
		

		$img_path = './websites/'.$this->app->current_website_name.'/www/'.$folder_name.'/'.$_POST['lname'].".png";
		$source = fopen($image_data, 'r');
		$destination = fopen($img_path, 'w');
		stream_copy_to_stream($source, $destination);
		fclose($source);
		fclose($destination);

		$main = Image::fromFile($img_path);
		$main->resize(150,null);
		$main->save($img_path);

		echo json_encode($return);
		exit;
	}

}