<?php

require_once'./vendor/autoload.php';

include '../admin/config-default.php';

if(file_exists('../admin/config.php'))
	include '../admin/config.php';

	$clients=[];

	if(isset($config['ssl-websocket-notifications']) && $config['ssl-websocket-notifications']){
		$Context = \Hoa\Stream\Context::getInstance('EpanTLS');
		$Context->setOptions([
		    'ssl' => [
		        'local_cert' => $config['ssl-certificate-pem-path'],
		        'verify_peer'=>false,
		        'allow_self_signed'=>false,
			'cafile'=>'/xavoc.com-ssl-cert/xavoc.com/ca.cer'
		    ]
		]);
		$ssl_config='EpanTLS';
	}else{
		$ssl_config=null;
	}

	echo "Running at ".$config['websocket-server']."\n";

	$websocket = new Hoa\Websocket\Server(
	    new Hoa\Socket\Server($config['websocket-server'],30,-1,$ssl_config)
	);

	$websocket->on('open', function (Hoa\Event\Bucket $bucket) {
	    echo 'new connection '."\n";
	    return;
	});


	$websocket->on('message', function (Hoa\Event\Bucket $bucket) use(&$clients) {
	    $data = $bucket->getData();
	    $message = json_decode($data['message'],true);
	    $response="";
	    if(isset($message['cmd'])){
	    	switch ($message['cmd']) {
	    		case 'register':
	    			$clients[$message['uu_id']] = $bucket->getSource()->getConnection()->getCurrentNode();
	    			$response  = "Client ".$message['uu_id']." registred \n";
	    			$response  = "";
	    			break;
	    		
	    		case "notification":
	    		default:
	    			$response=[];
	    			foreach ($message['clients'] as $client) {
	    				if(isset($clients[$client])){
		    				$bucket->getSource()->send($message['message'],$clients[$client]);
		    				$response[]=$client; 
	    				}
	    			}

	    			$response = json_encode($response);
	    			break;
	    	}
	    }

	    $bucket->getSource()->send($response);
	    return;
	});
	$websocket->on('close', function (Hoa\Event\Bucket $bucket) {
	    echo 'connection closed', "\n";

	    return;
	});

	$websocket->run();
