<?php


namespace xepan\base;

class Controller_PointEventManager extends \AbstractController {
	
	function handleEvent($app,$event_name,$param){
		switch ($event_name) {
			case 'telemarketing_response':
				$m=$this->add('xepan\base\Model_PointSystem');
				$m['contact_id']=$param['lead']['id'];
				if($param['score'])
					$m['score']=$m['score']+10;
				else
					$m['score']=$m['score']-10;
				$m->save();
				break;
			case 'landing_response':
				$m=$this->add('xepan\base\Model_PointSystem');
				$m['contact_id']=$param['lead']['id'];
				$m['landing_content_id']=$param['response']['content_id'];
				$m['landing_campaign_id']=$param['response']['campaign_id'];
				$m['score']=$m['score']+30;
				$m->save();
				break;
			case 'Subscription':
				$m=$this->add('xepan\base\Model_PointSystem');
				$m['contact_id']=$param['lead']['id'];
				$m['score']=50;
				$m->save();
				break;	
			default:
				# code...
				break;
		}
	}
}