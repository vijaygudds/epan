<?php
$config['url_prefix']='?page=';
$config['url_postfix']='';
$config['xepan-mysql-host']='127.0.0.1';
$config['dsn'] = 'mysql://root:winserver@localhost/epan';
$config['locale']['date_js'] = 'dd/mm/yyyy';

$config['js']['versions']['jqueryui']='1.11.master';

$config['tmail']['transport'] = 'Echo';

$config['epan_base_path'] = "http://www.xavoc.com";

$config['developer_mode'] = true;
$config['all_rights_to_superuser'] = true;
$config['status_icon'] = [];

$config['filestore']['chmod'] = 0755;
$config['paymentgateways'] = ['Instamojo','CCAvenue'];

$config['custom_app_path'] = false; // ['array of path','like','websites/'.$this->app->current_website_name.'/assets/xepan_vendor'];
$config['custom_app_list']=[];// ['xepan\iec','any_folder\namespace'];

$config['geolocationtrack']=[
	'location_mode'=>'payload', //payload or GET or POST
	'longitude_field'=>'longitude',
	'latitude_field'=>'latitude',
	'time_field'=>false,

	'employee_mode'=>'GET', // payload or GET or POST
	'employee_field'=>'emp'
];