<?php

if(!file_exists('atk4')){
	header('Location: ./install');
	exit;
}

require_once'./vendor/autoload.php';
require_once 'lib/Frontend.php';

$api=new Frontend('front');
$api->main();
