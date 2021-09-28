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

class Menu_TopBar extends \Menu_Advanced{

	public function addMenu($title, $class=null, $options=array())
    {
        $m = $this->add('xepan\base\Menu_TopBar',null,'SubMenu',['menu/menu']);
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


    // $this->app->top_menu->getMenuName('HR/Department',true)->destroy();
    // $this->app->top_menu->getMenuName('HR',true)->destroy();
    // $this->app->top_menu->getMenuName('HR'); // return key string

    // or

    // $this->app->top_menu->add('Order')
    //     ->move($this->app->top_menu->getMenuName('HR'),'last')
    //     ->now();
    
    function getMenuName($menu,$obj = false){
        // foreach ($this->app->layout->getElement('xepan_base_menu_topbar')->elements as $key => $value) {
        //     echo $key. ' => ' . print_r($value->template->get('Content'),true) . ' <br/>';
        //     foreach ($value->elements as $key1 => $value1) {
        //         echo ' - '. $key1. ' => '.print_r($value1->template->get('Content'),true) .'<br/>';
        //     }
        // }

        $menu_arr = explode("/", $menu);

        foreach ($this->app->layout->getElement('xepan_base_menu_topbar')->elements as $key => $value) {
            if($value->template->get('Content')[0] == $menu_arr[0]){
                if(count($menu_arr) == 1) {
                    if($obj)
                        return $this->app->layout->getElement('xepan_base_menu_topbar')->getElement($key);
                    else
                        return $key;
                }
                foreach ($value->elements as $key1 => $value1) {
                    if($value1->template->get('Content')[0] == $menu_arr[1]) 
                        if($obj)
                            return $this->app->layout->getElement('xepan_base_menu_topbar')->getElement($key)->getElement($key1);
                        else
                            return $key.'/'.$key1;
                }
            }
        }
        
        return null;
    }

	function defaultTemplate() {
        return array('menu/bar');
    }
}
