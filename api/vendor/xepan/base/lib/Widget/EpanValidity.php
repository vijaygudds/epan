<?php

namespace xepan\base;

class Widget_EpanValidity extends \xepan\base\Widget{
	function init(){
		parent::init();
		
		$this->view = $this->add('xepan\base\View_Widget_SingleInfo');
		$extra_info = $this->app->recall('epan_extra_info_array',false);
        $valid_till = $extra_info['valid_till'];

        $post = $this->add('xepan\hr\Model_Post');
        $post->tryLoadBy('id',$this->app->employee['post_id']);
        
        if(!$post->loaded())
            return;    

        if($valid_till AND ($post['parent_post_id'] == null OR $post['parent_post_id'] == $post['id'])){                        
            $expiry_view = $this->view;
            $expiry_view->setIcon('fa fa-clock-o')
                    	->setHeading('Expiring At')
                    	->setValue(date('d M\'y',strtotime($valid_till)))
                    	->makeDanger();

            $expiry_view->template->trySet('expiry_date',$valid_till);
        }else{
            $this->destroy();
            $this->owner->destroy();
        }
    }

    function recursiveRender(){

        return parent::recursiveRender();
	}
}