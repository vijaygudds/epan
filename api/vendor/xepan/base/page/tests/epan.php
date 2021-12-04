<?php
namespace xepan\base;



class page_tests_epan extends Page_Tester {
    public $title = 'Epan Related Basic Tests';

     public $proper_responses=[
        "Test_defaultEpan"=>'default',
        'Test_frontendFolder'=>true,
        'Test_createEpan'=>'epan_created',
        'Test_epanFolderCreated'=>'folder_created',
        'Test_createDefaultSuperUser'=>1,
        'Test_defaultInstalledApplications'=>10,
        'Test_installFreeApplication'=>10,
        'Test_removeEpan'=>['epan_deleted','comp_removed']
        ];

     function test_defaultEpan(){
     	return $this->add('xepan\base\Model_Epan')->setOrder('id')->tryLoadANy()->get('name');
     }

     function test_frontendFolder(){
     	return is_dir(getcwd().'/../websites/default');
     }

     function test_createEpan(){
        $ep_last = $this->add('xepan\base\Model_Epan')->setOrder('id','desc')->tryLoadAny()->get('id');
    
        $errros = [];
        $ep = $this->add('xepan\base\Model_Epan');
        $ep['epan_category_id'] = $this->add('xepan\base\Model_Epan_Category')->loadAny()->get('id');
        $ep['name'] = 'new_epan_'.($ep_last+1);
        $ep->save();        
        return $this->add('xepan\base\Model_Epan')->tryLoadBy('name','new_epan_'.($ep_last+1))->loaded()?'epan_created':false;
     }

     function test_epanFolderCreated(){
        $ep_last = $this->add('xepan\base\Model_Epan')->setOrder('id','desc')->tryLoadAny()->get('id');
        $folder_path = $this->app->pathfinder->base_location->base_path.'/../website/'.'new_epan_'.($ep_last);
        return is_dir($folder_path)?'folder_created':$folder_path;

     }

     function prepare_createDefaultSuperUser(){
        $ep_last = $this->add('xepan\base\Model_Epan')->setOrder('id','desc')->tryLoadAny()->get('id');
        return [$ep_last];
     }

     function test_createDefaultSuperUser($ep_last){
        return $this->add('xepan\base\Model_User_SuperUser')
                    ->addCondition('epan_id',$ep_last)
                    ->count()->getOne();

     }

     function test_detectCurrentEpan(){
        
     }

     function test_defaultInstalledApplications(){
        return false;
     }

     function test_installFreeApplication(){

     }

     function test_AvailableCredits(){

     }

     function test_installPaidApplication(){
        // Available credits
        

        // Less credits
        

     }

     function test_emailToPaidApplicationRenewal(){
        // repeated emails from few days before licence erxpires
     }

     function test_emailToDemoExpireApplication(){
        
     }

     function test_removeEpan(){
        $ep_last = $this->add('xepan\base\Model_Epan')->setOrder('id','desc')->tryLoadAny()->get('id');
        $this->add('xepan\base\Model_Epan')->load($ep_last)->delete();
        $deleted = $this->add('xepan\base\Model_Epan')->tryLoad($ep_last);

        return [
                $deleted->loaded()?'epan_exists':'epan_deleted',
                $this->add('xepan\base\Model_Epan_InstalledApplication')->tryLoadBy('epan_id',$ep_last)->count()->getOne() > 0 ? 'comp_exists': 'comp_removed'
        ];

     }


}