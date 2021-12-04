<?php

/**
* description: xEpan Menu Bar
* 
* @author : Gowrav Vishwakarma
* @email : gowravvishwakarma@gmail.com, info@xavoc.com
* @website : http://xepan.org
* 
*/

namespace xepan\base;

class Menu_TopRightBar extends \Menu_Advanced{

	public function addMenu($title, $class=null, $options=array())
    {
        $m = $this->add('xepan\base\Menu_TopBar',null,'SubMenu',['menu/right-menu']);
        $m->set($title);
        return $m;
    }

    public function addItem($title, $action=null,$match_qs_vars=[]){
    	$i = $this->add('xepan\base\Menu_TopBar',null,'SubMenu',['menu/item']);

        if (is_array($title)) {

            if ($title['badge']) {
                $i->add('View',null,'Badge')
                    ->setElement('span')
                    ->addClass('atk-label')
                    ->set($title['badge']);
                unset($title['badge']);
            }

        }

        if ($action) {
            if (is_string($action) || is_array($action) || $action instanceof \URL) {
                $i->template->set('url',$url = $this->app->url($action));
                if($url->isCurrent()){
                    if(count($match_qs_vars)===0){
                        $i->addClass('active');
                        $i->owner->addClass('active');
                    }else{
                        $active=true;
                        $args= $action->arguments;
                        foreach ($match_qs_vars as $var) {
                            if($_GET[$var]!=$args[$var]){
                                $active = false;
                                break;
                            }
                        }
                        if($active){
                            $i->addClass('active');
                            $i->owner->addClass('active');
                        }
                    }
                }
            } else {
                $i->on('click',$action);
            }
        }

        $i->set($title);

        return $i;
    }

	function defaultTemplate() {
        return array('menu/bar');
    }
}
