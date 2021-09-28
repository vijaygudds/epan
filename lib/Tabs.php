<?php


class Tabs extends View_Tabs_jUItabs {
	
	function addTab($title,$name=null){
        $container=$this->add('View_HtmlElement',$name);

        $url = '#'.$container->name;
        if($_GET['cut_page'])
            $url = $this->app->url('.').'#'.$container->name;

        $this->tab_template->set(array(
                    'url'=>$url,
                    'tab_name'=>$title,
                    'tab_id'=>$container->short_name,
                    ));
        $this->template->appendHTML('tabs',$this->tab_template->render());
        return $container;
    }
}