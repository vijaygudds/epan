<?php
/**
 * This model traverses pages of your project to look for test-cases.
 *
 * Test-files must implement a class descending from Page_Tester, refer
 * to that class for more info. 
 */

namespace xepan\base;

class Model_xEpanTester extends \Model {
    
    public $type='page';
    public $dir='tests';
    public $test_dir='tests';
    public $namespace=null;

    function init(){
        parent::init();

        $this->addField('name');
        $this->addField('total');
        $this->addField('success');
        $this->addField('fail');
        $this->addField('exception');
        $this->addField('speed');
        $this->addField('memory');
        $this->addField('result');

        /**
         * This model automatically sets its source by traversing 
         * and searching for suitable files
         */
        $path= $this->api->pathfinder->base_location->base_path.'/../vendor/'.str_replace("\\","/",$this->namespace)."/page/".$this->dir;
        if(!file_exists($path))
            $path = $this->api->pathfinder->base_location->base_path.'/../shared/apps/'.str_replace("\\","/",$this->namespace)."/page/".$this->dir;

        $p= scandir($path);
        // $p=$this->api->pathfinder->searchDir($this->type,$this->dir);

        unset($p[0]);
        unset($p[1]);
        $i=2;
        foreach ($p as $file) {
            if(strpos($file, ".php")===false) unset($p[$i]);
            $i++;
        }

        sort($p);
        $this->setSource('Array',$p);
        $this->addHook('afterLoad',$this);

        return $this;
    }
    function skipped(){
        $this['result']='Skipped';
        return $this;
    }
    function afterLoad(){
        // Extend this method and return skipped() for the tests which
        // you do not want to run
        if (false) {
            return $this->skipped();
        }

        $page=$this->namespace.'/page_'.str_replace('/','_',str_replace('.php','',$this->test_dir.'_'.$this['name']));
        try {
            $p=$this->api->add($page,['auto_test'=>false]);

            if(!$p instanceof \xepan\base\Page_Tester){
                $this['result']='Not Supported';
                return;
            }

            if(!$p->proper_responses){
                $this['result']='No proper responses';
                return;
            }

            // This will execute the actual test
            $res=$p->silentTest();

            if($res['skipped']){
                $this['result']='Test was skiped ('.$res['skipped'].')';
                return;
            }


            $this->set($res);
            $this['speed']=round($this['speed'],3);
            //list($this['total_tests'], $this['successful'], $this['time']) = 
            $this['result']=$this['success']==$this['total']?'OK':('FAIL: '.join(', ',$res['failures']));

            $p->destroy();
        }catch(\Exception $e){
            $this['fail']='!!';
            $this['result']='Exception: '.($e instanceof \BaseException?$e->getText():$e->getMessage());
            return;
        }
    }
}
