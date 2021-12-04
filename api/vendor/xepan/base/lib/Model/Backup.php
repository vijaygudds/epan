<?php

/**
 * DB Backup Model
 */

namespace xepan\base;

class Model_Backup extends \Model {
    
    public $dir ='backup';
    public $path="";

    function init(){

        parent::init();

        $this->add('xepan\base\Controller_Validator');
        $this->addField('name')->defaultValue('backup_'.$this->app->normalizeName($this->app->now).'.sql.gz');

        /**
         * This model automatically sets its source by traversing 
         * and searching for suitable files
        */
        $path = $this->path = $this->api->pathfinder->base_location->base_path.'/./websites/'.$this->app->current_website_name.'/'.$this->dir;
        $p = [];        
        if(!file_exists($path)){
            mkdir($path);
        }

        $p = scandir($path);
        unset($p[0]);
        unset($p[1]);
        arsort($p);

        $this->setSource('Array',$p);
        $this->addHook('afterSave',$this);
        $this->addHook('beforeDelete',$this);

        $this->is(['name|to_trim|required']);
        return $this;
    }

    function beforeDelete($m){
        if(file_exists($this->path.'/'.$this['name'])){
            unlink($this->path.'/'.$this['name']);
        }
    }

    function afterSave(){
        $this->add('xepan\base\Controller_Backup')
            ->setFileName($this['name'])
            ->export();
    }

    function getPath(){
        return $this->api->pathfinder->base_location->base_path.'/./websites/'.$this->app->current_website_name.'/backup';
    }

}
