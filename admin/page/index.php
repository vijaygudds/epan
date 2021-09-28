<?php

class page_index extends \xepan\base\Page {
    public $title ="Graphical Report";

    public $widget_list = [];
    public $entity_list = [];
    public $filter_form;
    public $breadcrumb=['Dashboard'=>'/','Quick Setup' => 'xepan_base_wizard' ];

    function init() {
        parent::init();
        
        try{
            $post = $this->add('xepan\hr\Model_Post');
            $post->load($this->app->employee['post_id']);
            
            $form = $this->app->page_top_right_button_set->add('Form',null,null,['form/empty'])->addClass('btn btn-xs');
            $report_field = $form->addField('DropDown','reports');
            $report_field->setModel('xepan\base\Model_GraphicalReport');

            $report_field->js('change',$form->js()->submit());

            // $this->app->page_top_right_button_set->add('Order')->move($form,'first')->now();

            $rpt = $this->add('xepan\base\Model_GraphicalReport');

            if($rpt_id = $this->app->stickyGET('graphical_report_id')){
                $rpt->load($rpt_id);
            }elseif($rpt_id = $this->app->employee['graphical_report_id']){                        
                $rpt->tryLoadBy('id',$rpt_id); 
            }else{
                $rpt->tryLoadBy('name',$post['permission_level']?:'Individual'); 
            }
            $report_field->set($rpt->id);
            
            $runner_view = $this->add('xepan\hr\View_GraphicalReport_Runner',['report_id'=>$rpt->id]); 
            
            $this->title = $rpt['name'].' Report';

            // updates
            try{
                $updates = file_get_contents($this->app->getConfig('epan_api_base_path')."/updates");
                $this->app->layout->add('View',null,'page_top_center')->setHtml($updates);
            }catch(\Exception $e){

            }

            if($form->isSubmitted()){

                $this->js()->univ()->location($this->app->url(null,['graphical_report_id'=>$form['reports'],'submit'=>null]))->execute();
            }

        }
        catch(\Exception $e){
            throw $e;
            
        }

    }
}
