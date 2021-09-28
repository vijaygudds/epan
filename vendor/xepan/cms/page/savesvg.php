<?php


namespace xepan\cms;

class page_savesvg extends \xepan\base\Page{
	function init(){
		parent::init();
		
		if (!isset($_POST['output_svg'])) {
			print "You must supply output_svg";
			exit;
		}
		$svg = $_POST['output_svg'];
		$filename = $_POST['filename'];//(isset($_POST['filename']) && !empty($_POST['filename']) ? preg_replace('@[\\\\/:*?"<>|]@u', '_', $_POST['filename']) : 'saved') . '.svg'; // These characters are indicated as prohibited by Windows
		$filename = str_replace("..%2F", "", $filename);
		
		$fh = fopen(getcwd().'/'.$filename, 'w') or die("Can't open file");
		fwrite($fh, $svg);
		fclose($fh);
		echo "saved";
		exit;
	}	
}

