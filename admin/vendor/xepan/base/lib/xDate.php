<?php

namespace xepan\base;
use Carbon\Carbon;

class xDate extends \AbstractController{
	public $default_date_start = '1970-01-01 00:00:00';
	public $default_date_end = '1970-01-01 00:00:00';

	function diff($start_date, $end_date, $in_what=null, $for_humans=true){

		$carbon_end_date = (Carbon::createFromFormat("Y-m-d H:i:s",$end_date ?:$this->default_date_end));
		$carbon_start_date = (Carbon::createFromFormat("Y-m-d H:i:s",$start_date ?:$this->default_date_start));

		if($in_what !==null and in_array(ucfirst(strtolower($in_what)), array('Days','Months','Years','Hours','Minutes'))){
			try{
				$func = 'diffIn'.ucfirst(strtolower($in_what));
				$diff=$carbon_end_date->$func($carbon_start_date);
			}catch(Exception $e){
				echo $e->getMessage();
				throw $e;
			}
		}else{
			// echo ucfirst(strtolower($in_what));
			$diff = $carbon_end_date->diffForHumans($carbon_start_date);
		}
		return $diff;
	}

	function isInBetween($date_one, $date_two, $check_date){
			
	}

	function getHour($ondate){
		return (Carbon::createFromFormat("Y-m-d H:i:s",$ondate)->format('H'));
	}
	
	function getMinute($ondate){
		return (Carbon::createFromFormat("Y-m-d H:i:s",$ondate)->format('i'));
	}

}