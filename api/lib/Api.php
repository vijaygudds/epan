<?php

class Api extends App_REST {

    function init() {
        $this->getWebsite();
        parent::init();
        
        $this->api->pathfinder
            ->addLocation(array(
                'addons' => array('vendor','shared/addons2','shared/addons'),
            ))
            ->setBasePath($this->pathfinder->base_location->getPath() . '/..');

        $this->dbConnect();
        $this->epan = $this->add('xepan\base\Model_Epan')->tryLoadBy('name',$this->app->current_website_name);
        $this->epan->config = $this->app->epan->ref('Configurations');
        $misc_m = $this->add('xepan\base\Model_Config_Misc');
            
        $misc_m->tryLoadAny();

        date_default_timezone_set($misc_m['time_zone']?:'UTC');
        $this->today = date('Y-m-d');
        $this->now   = date('Y-m-d H:i:s');
        
        //Todo load default location of a customer from browser or 
        $this->country = $this->app->recall('xepan-customer-current-country');
        $this->state = $this->app->recall('xepan-customer-current-state');
        $this->app->employee = $this->add('xepan\hr\Model_Employee');



        $this->app->profiler = $this->app->add('xepan/base/Controller_Profiler');

        $this->add('Controller_PatternRouter')
            ->link('v1/contact',array('id','method','arg1','arg2'))
            ->route();
    }

    function getWebsite(){

        $url = "{$_SERVER['HTTP_HOST']}";
        $domain = str_replace('www.','',$this->extract_domain($url))?:'www';
        $sub_domain = str_replace('www.','',$this->extract_subdomains($url))?:'www';

        $service_host = $this->getConfig('xepan-service-host',false);
        if($service_host && !in_array($domain,$service_host)){
            $epan = $domain;
        }else{
            $epan = $sub_domain;
        }

        $this->readConfig("websites/www/config.php");
        $this->dbConnect();
        $epan_hash = $this->db->dsql()->table('epan')->where($this->db->dsql()->orExpr()->where('name',$epan)->where('aliases','like','"%'.$epan.'%"'))->getHash();
        $epan_domain_array[$epan] = $epan_hash['name'];
        if(!$epan_hash['name'])
            throw new \Exception("Required epan name does not found [searched in db/table www >> epan]");
                        
        $extra_info_array  = json_decode($epan_hash['extra_info'],true);
        // die(print_r($epan,true));
        // die($epan['name']);

        $current_website = $this->current_website_name = $epan;
        $this->readConfig("websites/$this->current_website_name/config.php");

        $this->addLocation(array(
            'page'=>array("websites/$current_website/www"),
            'js'=>array("websites/$current_website/www/js"),
            'css'=>array("websites/$current_website/www","websites/$current_website/www/css"),
            'template'=>["websites/$current_website/www"],
            'addons'=> ['websites/'.$current_website.'/www']
        ))->setParent($this->pathfinder->base_location);
        
    }

    function extract_domain($domain)
    {
        if(preg_match("/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i", $domain, $matches))
        {
            return $matches['domain'];
        } else {
            return $domain;
        }
    }

    function extract_subdomains($domain)
    {
        $subdomains = $domain;
        $domain = $this->extract_domain($subdomains);
        $subdomains = rtrim(strstr($subdomains, $domain, true), '.');

        return $subdomains;
    }

    function recall($key,$default=null){
        if(isset($this->$key)) return $this->$key;
        return $default;
    }

    function memorize($key,$value){
        $this->$key = $value;
        return $value;
    }

    function routePages(){

    }

}