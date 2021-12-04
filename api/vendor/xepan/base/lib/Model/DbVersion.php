<?php
/**
 * This model traverses pages of your project to look for test-cases.
 *
 * Test-files must implement a class descending from Page_Tester, refer
 * to that class for more info. 
 */

namespace xepan\base;

class Model_DbVersion extends \Model {
    
    public $dir='dbversion';
    public $namespace=null;
    public $max_count=0;
    public $path="";
    
    function init(){
        parent::init();
        $this->add('xepan\base\Controller_Validator');
        $this->addField('name')->defaultValue();
        $this->addField('code')->type('text');
        $this->is([
                'code|to_trim|required'
            ]);
        /**
         * This model automatically sets its source by traversing 
         * and searching for suitable files
         */
        $path = $this->path = $this->api->pathfinder->base_location->base_path.'/./vendor/'.str_replace("\\","/",$this->namespace)."/".$this->dir;
        $p = scandir($path); 
        unset($p[0]);
        unset($p[1]);

        asort($p);
        $i=2;
        
        foreach ($p as $file) {
            // $temp = explode(".", explode("-", $file)[1]);
            
            $temp = explode(".",$file);
            if(is_array($temp) and isset($temp[0]) and $temp[0] != "index"){
                if($this->max_count < $temp[0] )
                    $this->max_count = $temp[0];
            }

            if(strpos($file, ".sql")===false && strpos($file, ".php")===false) unset($p[$i]);
            $i++;
        }
        asort($p);
        $this->setSource('Array',$p);

        $this->addHook('beforeDelete',$this);

        return $this;
    }

    function beforeDelete($m){
        // throw new \Exception($this->id, 1);
        if(file_exists($this->path.'/'.$this['name'])){
            unlink($this->path.'/'.$this['name']);
        }
    }
}
