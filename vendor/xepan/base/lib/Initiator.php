<?php

namespace xepan\base;

class Initiator extends \Controller_Addon {
    public $addon_name = 'xepan_base';

    function init(){
        parent::init();

        // $this->app->forget($this->app->current_website_name.'_epan');

        $this->addAppFunctions();

        if(!($this->app->epan = $this->app->recall($this->app->current_website_name.'_epan',false))){
            $this->app->epan = $this->add('xepan\base\Model_Epan')->tryLoadBy('name',$this->app->current_website_name);
            $this->app->memorize($this->app->current_website_name.'_epan', $this->app->epan);
        }
                                
        if(!$this->app->epan->loaded()){
            $this->app->forget($this->app->current_website_name.'_epan');
            die('No site found, forwarding to 404 service');
        }

        $this->app->epan->config = $this->app->epan->ref('Configurations');
        
        //$this->runDBVersionUpdate();

        $misc_m = $this->add('xepan\base\Model_Config_Misc');

        $misc_m->tryLoadAny();

        date_default_timezone_set($misc_m['time_zone']?:'UTC');
        // $this->app->today = date('Y-m-d');
        // $this->app->now   = date('Y-m-d H:i:s');
        $this->app->today = date('Y-m-d',strtotime($this->app->recall('current_date',date('Y-m-d'))));
        $this->app->now = date('Y-m-d H:i:s',strtotime($this->app->recall('current_date',date('Y-m-d H:i:s'))));

        //Todo load default location of a customer from browser or 
        $this->app->country = $this->app->recall('xepan-customer-current-country');
        $this->app->state = $this->app->recall('xepan-customer-current-state');

        $event_cont = $this->add('xepan\base\Controller_PointEventManager');
        $this->app->addHook('pointable_event',[$event_cont,'handleEvent']);
        $this->app->addHook('widget_collection',[$this,'exportWidgets']);
        $this->app->addHook('entity_collection',[$this,'exportEntities']);

        // updates
        if($this->app->getConfig('company_support_on_all_pages',false) && $this->app->page !='index'){
            try{
                $updates = file_get_contents($this->app->getConfig('epan_api_base_path')."/updates");
                $this->app->layout->add('View',null,'page_top_center')->setHtml($updates);
            }catch(\Exception $e){

            }
        }

    }

    function runDBVersionUpdate(){

        $db_model = $this->add('xepan/base/Model_DbVersion',array('dir'=>'dbversion','namespace'=>'xepan\base'));
        $path = $this->path = $this->api->pathfinder->base_location->base_path.'/vendor/xepan/base/dbversion';
        $this->app->epan->reload();        
        if(!isset($this->app->is_install) AND $this->app->epan['epan_dbversion'] < $db_model->max_count){ 
            $this->app->memorize($this->app->current_website_name.'_epan', $this->app->epan);
            foreach ($db_model as $file) {
                if(!file_exists($path."/".$file['name'])) continue;
                $file_name=explode('.', $file['name']);
                // echo "working on ".$file['name'].'<br/>';
                if(isset($file_name[0]) && (int)$file_name[0] > $this->app->epan['epan_dbversion']){
                    try{
                        $sql = file_get_contents($path."/".$file['name']);
                        if($file_name[1]=='sql'){
                            $this->app->db->dsql()->expr('SET FOREIGN_KEY_CHECKS = 0;')->execute();
                            $this->app->db->dsql()->expr('SET unique_checks=0;')->execute();

                            $this->api->db->beginTransaction();
                            $this->app->db->dsql()->expr($sql)->execute();
                            
                            
                            $this->api->db->commit();
                        }elseif($file_name[1]=='php'){
                            include_once $path."/".$file['name'];
                            // echo "including_file ". $file['name'];
                        }
                        $this->app->epan['epan_dbversion']=(int)$file_name[0];
                        $this->app->epan->save();
                        $this->app->memorize($this->app->current_website_name.'_epan', $this->app->epan);
                    }catch(\Exception_StopInit $e){

                    }catch(\Exception $e){
                        if($file_name[1]=='sql'){
                            $this->app->db->dsql()->expr('SET FOREIGN_KEY_CHECKS = 1;')->execute();
                            $this->app->db->dsql()->expr('SET unique_checks=1;')->execute();
                            $this->api->db->rollback();
                            
                        }
                        throw $e;
                    }
                }   
                
            }

        }   

    }

    function setup_admin(){
        
        $this->routePages('xepan_base');
        $this->addLocation(array('template'=>'templates','js'=>'templates/js','css'=>['templates/css','templates/js']))
        ->setBaseURL('../vendor/xepan/base/')
        ;

        $tinymce_addon_base_path=$this->app->locatePath('addons','tinymce\tinymce');
        $this->addLocation(array('js'=>'.','css'=>'skins'))
        ->setBasePath($tinymce_addon_base_path)
        ->setBaseURL('../vendor/tinymce/tinymce/');


        $elfinder_addon_base_path=$this->app->locatePath('addons','studio-42\elfinder');
        $this->addLocation(array('js'=>'js','css'=>'css','image'=>'img'))
        ->setBasePath($elfinder_addon_base_path)
        ->setBaseURL('../vendor/studio-42/elfinder/');

        $this->app->page_top_right_button_set = $this->app->layout->add('ButtonSet',null,'page_top_right')->addClass('btn-group-sm');

        $this->app->inConfigurationMode = false;
        if($this->app->recall('configuration_mode',false)){
            $this->app->inConfigurationMode = true;

            $this->app->layout->template->trySet('configuration_mode','configuration_mode');

            $this->app->page_top_right_button_set->addButton('Running in Configuration Mode, Click To Exit to Application Mode')
                ->addClass('btn btn-danger')
                ->js('click')->univ()->location($this->app->url('xepan_base_configurationmode'));
            ;
        }

        $auth = $this->app->add('BasicAuth',['login_layout_class'=>'xepan\base\Layout_Login']);
        $auth->allowPage(['xepan_base_forgotpassword','xepan_base_resetpassword','xepan_base_registration']);
        if(in_array($this->app->page, $auth->getAllowedPages())){
            $this->app->layout->destroy();
            $this->app->add('xepan\base\Layout_Centered');
            $this->app->top_menu = new \Dummy;
            $this->app->side_menu = new \Dummy;
            $this->app->user_menu = new \Dummy;
            $this->app->report_menu = new \Dummy;
        }else{
            $this->app->top_menu = $this->app->layout->add('xepan\base\Menu_TopBar',null,'Main_Menu');
            $this->app->side_menu = $this->app->layout->add('xepan\base\Menu_SideBar',null,'Side_Menu');
            $m = $this->app->layout->add('xepan\base\Menu_TopRightBar',null,'User_Menu');
        
            if(!$this->app->getConfig('hidden_user_menu',false)) {
                $this->app->user_menu = $m->addMenu('My Menu');
                $this->app->user_menu->addItem(['Logout','icon'=>'fa fa-power-off'],$this->app->url('xepan_base_logout'));
            }
            else
                $this->app->user_menu = new \Dummy;

            if(!$this->app->inConfigurationMode && !$this->app->getConfig('hidden_report_menu',false)) 
                $this->app->report_menu = new \Dummy;
                // $this->app->report_menu = $this->app->top_menu->addMenu('Reports');
            else
                $this->app->report_menu = new \Dummy;

        }

        $this->app->top_menu_array=[];
        $this->app->my_menu_array=[];


        $auth->addHook('createForm',function($a,$p){
            $this->app->loggingin=true;            
            $f = $p->add('Form',null,null,['form/minimal']);
            $f->setLayout(['layout/xepanlogin','form_layout']);
            $f->addField('Line','username','Email address');
            $f->addField('Password','password','Password');
            // $cc = $f->add('Columns');
            // $cc->addColumn()->add('Button')->set('Log in')->addClass('atk-size-milli atk-swatch-green');
            // $cc->addColumn()->addClass('atk-align-right')->addField('Checkbox','remember_me','Remember me');
            $this->breakHook($f);

        });
        
        $auth->addHook('loggedIn',function($auth,$user,$pass){
            $config = $this->add('xepan\base\Model_Config_Misc');
            $config->tryLoadAny();
            if($config['admin_restricted_ip']){
                $allowed_ips = explode(",",$config['admin_restricted_ip']);
                $allowed_ips = array_map('trim', $allowed_ips);
                $allow_without_ip=false;
                if($this->app->epan->isApplicationInstalled('xepan\hr')){
                    $employee = $this->add('xepan\hr\Model_Employee');
                    $employee->loadBy('user_id',$auth->model->id);
                    if($employee['allow_login_from_anywhere']) $allow_without_ip=true;
                }
                
                if($auth->model['scope'] == 'SuperUser') $allow_without_ip = true;

                if(!in_array($_SERVER['REMOTE_ADDR'],$allowed_ips) && !$allow_without_ip){                    
                    $this->app->auth->logout();
                    $this->app->redirect('/');
                }
            }
            $this->app->memorize('user_loggedin', $auth->model);
            $auth->model['last_login_date'] = $this->app->now;
            $auth->model->save();
        });


        $auth->add('xepan\base\Controller_Cookie');

        $this->api->addHook('post-init',function($app){
            if(!$this->app->getConfig('developer_mode',false) && !isset($this->app->loggingin) && !$app->page_object instanceof \xepan\base\Page && !in_array($app->page, $app->auth->getAllowedPages())){
                throw $this->exception('Admin Page Must extend \'xepan\base\Page\'')
                            ->addMoreInfo('page',$app->page)
                            ->addMoreInfo('page_object_class',get_class($app->page_object))
                            ;
            }
        });

        $user = $this->add('xepan\base\Model_User_Active');
        // $user->addCondition('epan_id',$this->app->epan->id);
        $user->addCondition('scope',['AdminUser','SuperUser']);

        $auth->usePasswordEncryption('md5');
        $auth->setModel($user,'username','password');
        
        if($_GET['access_token']){
            $u = $this->add('xepan\base\Model_User_Active');
            $u->addCondition('access_token',$_GET['access_token']);
            $u->addCondition('access_token_expiry','>',$this->app->now);
            $u->tryLoadAny();
            if($u->loaded()){
                $auth->login($u);
                $this->app->redirect($this->app->url());
            }else{
                $auth->logout();
            }
        }
        $auth->check();

        
        // if old session is continues and day is changed, consider it as new login of day
        if($auth->isLoggedIn() && strtotime(date('Y-m-d',strtotime($auth->model['last_login_date']))) != strtotime($this->app->today)){
            $auth->hook('loggedIn',[$auth->model,null]);
        }

        $this->app->side_menu->addItem(['Configuration Mode','icon'=>' fa fa-cogs'],'xepan_base_configurationmode')->setAttr(['title'=>'Configuration Mode']);
               
        if(!$this->app->isAjaxOutput()) {
            $this->app->jui->addStaticInclude('pace.min');
            $this->app->jui->addStaticInclude('elfinder.full');
            $this->app->jui->addStaticStyleSheet('elfinder.full');
            $this->app->jui->addStaticStyleSheet('elfindertheme');
            $this->app->jui->addStaticStyleSheet('elfindertheme');
            $this->app->jui->addStaticInclude('pnotify.custom.min');
            $this->app->jui->addStaticInclude('xepan.pnotify');
            $this->app->jui->addStaticStyleSheet('pnotify.custom.min');
            $this->app->jui->addStaticStyleSheet('animate');
            $this->app->jui->addStaticInclude('xepan_jui');
            
            $this->app->js(true,'PNotify.prototype.options.styling = "fontawesome"');
            $this->app->js(true)->_library('PNotify.desktop')->permission();
            $this->app->js(true)->_load('jquery.bootstrap-responsive-tabs.min')->_selector('.responsive-tabs')->responsiveTabs("accordionOn: ['xs', 'sm']");

            $this->app->addHook('collect_shortcuts',[$this,'collect_shortcuts']);
            $this->addGlobalShortcuts();

        }
       
        $this->app->addHook('post-init',function($app){
            if($app->layout->template->hasTag('quick_search_form'))
                $app->layout->add('xepan\base\View_QuickSearch',null,'quick_search_form');
        });

        $this->app->layout->template->trySet('xepan_version',file_get_contents('../version'));

        // Adding all other installed applications
        $this->setup_xepan_apps('admin');
        // throw new \Exception($this->app->employee->id, 1);

        if(!$this->app->inConfigurationMode){
            $this->app->addHook('post-init',function($app){
                $this->setUpApplicationMenus();
            });
        }

        $this->app->addMethod('normalizeSlugUrl',function($app,$name){
            return strtolower(str_replace("_", "-", $this->app->normalizeName($name)));
        });        

        return $this;
	} 


    function setup_frontend(){
        $this->routePages('xepan_base');
        $this->addLocation(array('template'=>'templates','js'=>'templates/js','css'=>'templates/css'))
        ->setBaseURL('./vendor/xepan/base/')
        ;
        
    

        $auth = $this->app->add('BasicAuth',['login_layout_class'=>'xepan\base\Layout_Login']);
        $auth->usePasswordEncryption('md5');

        $user = $this->add('xepan\base\Model_User_Active');
        $user->addCondition('scope',['WebsiteUser','SuperUser','AdminUser']);
        $auth->setModel($user,'username','password');
        
        if(strpos($this->app->page,'_admin_')!==false){
            $user->addCondition('scope',['SuperUser','AdminUser']);
            $auth->setModel($user,'username','password');
            $auth->check();
        }

        if($_GET['access_token']){    
            $u = $this->add('xepan\base\Model_User_Active');
            $u->addCondition('access_token',$_GET['access_token']);
            $u->addCondition('access_token_expiry','>',$this->app->now);
            $u->tryLoadAny();
            if($u->loaded()){
                $auth->login($u);
                $this->app->redirect($this->app->url());
            }else{
                $auth->logout();
            }
        }

        $this->app->addMethod('normalizeSlugUrl',function($app,$name){
            return str_replace("_", "-", $this->app->normalizeName($name));
        });

        $this->app->addMethod('exportFrontEndTool',function($app,$tool, $group='Basic'){
            if(!isset($app->fronend_tool)) $app->fronend_tool=[];
            $app->fronend_tool[$group][] = $tool;
        });

        $this->app->addMethod('getFrontEndTools',function($app){
            if(!isset($app->fronend_tool)) $app->fronend_tool=[];
            return $app->fronend_tool;
        });

        // $this->app->jui->addStaticStyleSheet('xepan-base');

        if($_SERVER['SERVER_ADDR']){
            
            if(!$this->app->recall('xepan-customer-current-country',false)){
                $ip=str_replace('.',"", $_SERVER['SERVER_ADDR']);
                $s=$this->app->db->dsql()
                                ->table('ip2location-lite-db11')
                                // ->where('ip_from','<=','16777216')
                                // ->where('ip_to','>=','16777471')
                                ->where('ip_from','<=',$ip)
                                ->where('ip_to','>=',$ip)
                                ->del('fields')->get();
                // throw new \Exception(var_dump($s), 1);
                // exit;
                $ip_country= $this->add('xepan\base\Model_Country')->tryLoadBy('name',$s['0']['country']); 
                $ip_state = $this->add('xepan\base\Model_State')->tryLoadBy('name',$s['0']['country']);
                $this->app->memorize('xepan-customer-current-country',$ip_country);
                $this->app->memorize('xepan-customer-current-state',$ip_state);
            }
            $this->app->country=$this->app->recall('xepan-customer-current-country');
            $this->app->state=$this->app->recall('xepan-customer-current-state');
        }
        
        // Adding all other installed applications
        $this->setup_xepan_apps('frontend');

        return $this;
    }


    function setup_xepan_apps($side){
        
         foreach ($this->add('xepan\base\Model_Epan_InstalledApplication')->addCondition('is_active',true)->setOrder('application_id') as $apps) {
            if($apps['is_hidden']){
                $this->app->setConfig('hidden_'.str_replace("\\", "_", $apps['application_namespace']),true);
            }
            $this->app->xepan_addons[] = $apps['application_namespace'];   
        }

        foreach ($this->app->xepan_addons as $addon) {
            if($addon == 'xepan\base') continue;
            $this->app->xepan_app_initiators[$addon] = $app_initiators[$addon] = $this->add("$addon\Initiator");
        }

        // Pre setup call

        foreach ($this->app->xepan_app_initiators as $addon_name=>$addon_obj) {
            if($addon == 'xepan\base') continue;
            $func = 'setup_pre_'.$side;
            if($addon_obj->hasMethod($func)){
                $addon_obj->$func();
            }
        }

        // Setup call
        foreach ($this->app->xepan_app_initiators as $addon_name=>$addon_obj) {
            if($addon == 'xepan\base') continue;
            $func = 'setup_'.$side;
            $addon_obj->$func();
        }

        // Post Setup Call

        foreach ($this->app->xepan_app_initiators as $addon_name=>$addon_obj) {
            if($addon == 'xepan\base') continue;
            $func = 'setup_post_'.$side;
            if($addon_obj->hasMethod($func)){
                $addon_obj->$func();
            }
        }
    }

    function setUpApplicationMenus(){
        /*
            if(!empty($this->app->top_menu_array)) {
                draw menus
                return;
            }

            tryLoad XEC Default Menus

            if(loaded) {
                draw menus;
                return;
            }

            collect application menus from all apps 
            create default XEC menus in config
            save and reload page to cactch by above condition

        */

        if(count($this->app->top_menu_array)>0){
            $this->drawMenus($this->app->top_menu_array);
            return;
        }

        $menu_config = $this->add('xepan\base\Model_Config_Menus')
                        ->addCondition('name',$this->app->getConfig('TopMenuBar'))
                        ->tryLoadAny();
        if($menu_config->loaded()){
            $this->drawMenus(json_decode($menu_config['value'],true));
            return;
        }

       $this->generateXECDefaultMenus();

        $this->app->redirect($this->app->url(null));
    }

    function drawMenus($app_menu_array,$menu_object=null){
        if(!$menu_object) $menu_object = $this->app->top_menu;
        foreach ($app_menu_array as $top_menu_name => $menu_array) {
            $m = $menu_object->addMenu($top_menu_name);
            foreach ($menu_array as $menu) {
                if(!is_array($menu['url_param'])) unset($menu['url_param']);
                $url = $this->app->url($menu['url'],isset($menu['url_param'])?$menu['url_param']:null);
                if(!isset($menu['url_param'])) $url->arguments=[];
                $m->addItem([$menu['caption']?:$menu['name'],'icon'=>$menu['icon']],$url);
            }
        }
    }

    function generateXECDefaultMenus(){
        
         foreach($this->app->xepan_app_initiators as $app_namespace =>$app_inits){
            if($this->app->getConfig('hidden_'.str_replace("\\", "_", $app_namespace),false)){
                continue;
            }

            if($app_inits->hasMethod('getTopApplicationMenu')){
                $arr = $app_inits->getTopApplicationMenu();

                foreach ($arr as $menu_name=>&$m1){
                    $sub_menus = $this->add('xepan\base\Model_Config_Menus')
                        ->addCondition('name',$menu_name)
                        ->addCondition('is_set',false)
                        ->tryLoadAny();
                    foreach ($m1 as &$m2){
                        if(!isset($m2['caption'])) $m2['caption']=$m2['name'];
                    }

                    foreach($m1 as $index => &$m2){
                        if(isset($m2['skip_default']))
                            unset($m1[$index]);
                    }

                    if(in_array($menu_name, array_keys($this->app->top_menu_array))){
                        $this->app->top_menu_array[$menu_name] = array_merge($this->app->top_menu_array[$menu_name],$arr[$menu_name]);
                    }else
                        $this->app->top_menu_array = array_merge($this->app->top_menu_array,[$menu_name=>$m1]);
                    
                    $sub_menus['name']=$menu_name;
                    $sub_menus['is_set']=false;
                    $sub_menus['value']=json_encode([$menu_name=>$this->app->top_menu_array[$menu_name]]);
                    $sub_menus->save();
                }

                // foreach ($arr as $key => &$menus) {
                //     foreach ($menus as $index => &$menu) {
                //         if(isset($menu['skip_default']))
                //             unset($arr[$key][$index]);
                //     }
                // }

                // if(in_array($menu_name, array_keys($this->app->top_menu_array)))
                //     $this->app->top_menu_array[$menu_name] = array_merge($this->app->top_menu_array[$menu_name],$arr[$menu_name]);
                // else
                //     $this->app->top_menu_array = array_merge($this->app->top_menu_array,$arr);
            }
        }

        $menu_config = $this->add('xepan\base\Model_Config_Menus')
            ->addCondition('name','XEC_DEFAULT')
            ->tryLoadAny();

        $menu_config['name']='XEC_DEFAULT';
        $menu_config['is_set']=true;
        $menu_config['value']=json_encode($this->app->top_menu_array);
        $menu_config->save();
    }

    function exportWidgets($app,&$array){
        $array[] = ['xepan\base\Widget','level'=>'Global','title'=>'Basic Widget'];
        $array[] = ['xepan\base\Widget_MyActivity','level'=>'Individual','title'=>'Personal Activities'];
        $array[] = ['xepan\base\Widget_GlobalActivity','level'=>'Global','title'=>'Companies Activities'];
        $array[] = ['xepan\base\Widget_SubordinateActivity','level'=>'Sibling','title'=>'Subordinates Activities'];
        $array[] = ['xepan\base\Widget_RecentContacts','level'=>'Global','title'=>'Recent Contacts'];
        $array[] = ['xepan\base\Widget_EpanValidity','level'=>'Global','title'=>'Epan Information'];
        $array[] = ['xepan\base\Widget_EmployeeSpecificActivities','level'=>'Global','title'=>'Employee Activities'];
        $array[] = ['xepan\base\Widget_EmployeeContacts','level'=>'Global','title'=>'Contacts Added'];
    }

    function exportEntities($app,&$array){
        $array['date_range'] = ['caption'=>'Date Range', 'type'=>'DateRangePicker'];
        $array['contact'] = ['caption'=>'Contact','type'=>'xepan\base\Basic','model'=>'xepan\base\Model_Contact'];
        $array['User'] = ['caption'=>'User','type'=>'xepan\base\Basic','model'=>'xepan\base\Model_User'];
        $array['Country'] = ['caption'=>'Country','type'=>'xepan\base\Basic','model'=>'xepan\base\Model_Country'];
        $array['Miscellaneous_Technical_Settings'] = ['caption'=>'Miscellaneous_Technical_Settings','type'=>'xepan\base\Basic','model'=>'xepan\base\Model_Config_Misc'];
        $array['Email_Duplication_Allowed_Settings'] = ['caption'=>'Email_Duplication_Allowed_Settings','type'=>'xepan\base\Basic','model'=>'xepan\base\Model_Email_Duplication_Allowed_Settings'];
        $array['contact_no_duplication_allowed_settings'] = ['caption'=>'contact_no_duplication_allowed_settings','type'=>'xepan\base\Basic','model'=>'xepan\base\Model_contact_no_duplication_allowed_settings'];
        $array['GraphicalReport'] = ['caption'=>'GraphicalReport','type'=>'xepan\base\Basic','model'=>'xepan\base\Model_GraphicalReport'];
        $array['report_type'] = ['caption'=>'Type','type'=>'DropDown','values'=>['chart'=>'Chart','report'=>'Report']];
        $array['Contact_Other_Info_Fields'] = ['caption'=>'Contact_Other_Info_Fields','type'=>"Line"];
        $array['Rules'] = ['caption'=>'Point_System_Rule','type'=>"xepan\base\Rule", 'model'=>'xepan\base\Model_Rules'];
        $array['Branch'] = ['caption'=>'Branch','type'=>"xepan\base\Branch", 'model'=>'xepan\base\Model_Branch'];
    }

    function collect_shortcuts($app,&$shortcuts){
        $shortcuts[]=["title"=>"Contact Other Info","keywords"=>"contact other info fields custom","description"=>"Manage custom fields to be added in conatcts","normal_access"=>"Generl Settings -> Contact Other Info Fields","url"=>$this->app->url('xepan/communication/generalsetting',['cut_page'=>1,'cut_object'=>'admin_layout_cube_generalsetting_tabs_contact-info-fields']),'mode'=>'frame'];
    }

    function addGlobalShortcuts(){
        $this->app->js(true)->_load('shortcut')->_load('fuse.min')->_load('xepan_shortcuts');
        
        // Because all applications yet to have run their initiators and how they will catch event then ??
        $this->api->addHook('post-init',function($app){
            $shortcuts=[];
            $this->app->hook('collect_shortcuts',[&$shortcuts]);
            $popup = $this->app->add('xepan\base\View_ModelPopup')->setStyle('z-index','4096');
            $popup->setTitle('Quick Menu');
            $popup->options['addSaveButton']=false;
            $this->app->js(true)->univ()->setup_shortcuts($shortcuts,$popup);
        });
    }

    function resetDB($write_sql=false,$install_apps=true){
        // $this->app->old_epan = clone $this->app->epan;

        // Clear DB
        $truncate_models = ['Epan_Category','Epan','User','Epan_Configuration','Epan_InstalledApplication','Application','Country','State'];
        foreach ($truncate_models as $t) {
            $this->add('xepan\base\Model_'.$t)->deleteAll();
        }

        // orphan contact_info and contacts
        
        $this->app->db->dsql()->table('contact_info')->where('epan_id',null)->delete();
        $this->app->db->dsql()->table('contact')->where('epan_id',null)->delete();
        
        $d = $this->app->db->dsql();
        $d->sql_templates['delete'] = "delete [table] from  [table] [join] [where]";
        $d->table('contact_info')->where('contact.id is null')->join('contact',null,'left')->delete();

        // orphan document_attachements
        $d = $this->app->db->dsql();
        $d->sql_templates['delete'] = "delete [table] from  [table] [join] [where]";
        $d->table('attachment')->where('document.id is null')->join('document',null,'left')->delete();

        // Create default Epan_Category and Epan

        $epan_category = $this->add('xepan\base\Model_Epan_Category')
            ->set('name','default')
            ->save();

        $epan = $this->add('xepan\base\Model_Epan')
                    ->set('epan_category_id',$epan_category->id)
                    ->set('name','www')
                    ->save();

        $this->app->epan = $epan;
        $this->app->epan->config = $this->app->epan->ref('Configurations');
        // $this->app->new_epan = clone $this->app->epan;

        // Create Default User
        $user = $this->add('xepan\base\Model_User_SuperUser');
        $this->app->auth->addEncryptionHook($user);
        $user=$user->set('username','admin@epan.in')
             ->set('scope','SuperUser')
             ->set('password','admin')
             ->set('epan_id',$epan->id)
             ->saveAs('xepan\base\Model_User_Active');

        // $this->app->auth->login($user);

        // Create Default Applications and INstall with all with root application
        
        $addons = $this->app->getConfig('xepan_available_addons',['xepan\\base','xepan\\communication', 'xepan\\hr','xepan\\projects','xepan\\marketing','xepan\\accounts','xepan\\commerce','xepan\\production','xepan\\crm','xepan\\cms','xepan\\blog'/*,'xepan\\epanservices'*/]);

        foreach ($addons as $ad) {
            if($ad==='xepan\\base' or $ad==='xepan\base') continue;
            
            $ad_array = explode("\\", $ad);
            $app = $this->add('xepan\base\Model_Application')
                ->set('name',array_pop($ad_array))
                ->set('namespace',$ad)
                ->save();
            if($install_apps)
                $epan->installApp($app);
        }

        // create default filestore volume
        
        $fv = $this->add('xepan\filestore\Model_Volume');
        $fv->addCondition('name','upload');
        $fv->tryLoadAny();
        // $fv['dirname']='upload'; // It is now expression to return as per current website folder
        $fv['dirname_field']='upload';
        $fv['total_space'] = '1000000000';
        $fv['used_space'] = '0';
        $fv['stored_files_count'] = '0';
        $fv['enabled'] = '1';
        $fv->save();


        // if($write_sql){
        //     $dump = new \MySQLDump(new \mysqli('localhost', 'root', 'winserver', 'xepan2'));
        //     $dump->save(getcwd().'/../vendor/'.str_replace("\\",'/',__NAMESPACE__).'/install.sql');
        // }

        // Insert default country and states
        $this->api->db->dsql()->expr(file_get_contents(realpath(getcwd().'/vendor/xepan/base/countriesstates.sql')))->execute();
        
        //Set Epan config 
        $config_m = $this->add('xepan\base\Model_ConfigJsonModel',
        [
            'fields'=>[
                        'reset_subject'=>'Line',
                        'reset_body'=>'xepan\base\RichText',
                        'update_subject'=>'Line',
                        'update_body'=>'xepan\base\RichText',
                        ],
                'config_key'=>'ADMIN_LOGIN_RELATED_EMAIL',
                'application'=>'communication'
        ]);
        $config_m->tryLoadAny();

        // $admin_config = $this->app->epan->config;
        $file_reset_subject_admin = file_get_contents(realpath(getcwd().'/vendor/xepan/base/templates/default/reset_subject_admin.html'));
        $file_reset_body_admin = file_get_contents(realpath(getcwd().'/vendor/xepan/base/templates/default/reset_body_admin.html'));
        
        $config_m['reset_subject'] = $file_reset_subject_admin;
        $config_m['reset_body'] = $file_reset_body_admin;
        // $config_m->save();
        // $admin_config->setConfig('RESET_PASSWORD_SUBJECT_FOR_ADMIN',$file_reset_subject_admin,'communication');
        // $admin_config->setConfig('RESET_PASSWORD_BODY_FOR_ADMIN',$file_reset_body_admin,'communication');

        $file_update_subject_admin = file_get_contents(realpath(getcwd().'/vendor/xepan/base/templates/default/update_subject_admin.html'));
        $file_update_body_admin = file_get_contents(realpath(getcwd().'/vendor/xepan/base/templates/default/update_body_admin.html'));
        $config_m['update_subject'] = $file_update_subject_admin;
        $config_m['update_body'] = $file_update_body_admin;
        $config_m->save();
        // $admin_config->setConfig('UPDATE_PASSWORD_SUBJECT_FOR_ADMIN',$file_update_subject_admin,'communication');
        // $admin_config->setConfig('UPDATE_PASSWORD_BODY_FOR_ADMIN',$file_update_body_admin,'communication');
      
        // Do other tasks needed
        // Like empting any folder etc
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

    // function epanDashboard($app,$page){
    //     $extra_info = $this->app->recall('epan_extra_info_array',false);
    //     $valid_till = $extra_info['valid_till'];

    //     $post = $this->add('xepan\hr\Model_Post');
    //     $post->tryLoadBy('id',$this->app->employee['post_id']);
        
    //     if(!$post->loaded())
    //         return;    
        
    //     if($valid_till AND ($post['parent_post_id'] == null OR $post['parent_post_id'] == $post['id'])){
    //         $expiry_view = $page->add('xepan\base\View_Widget_SingleInfo',null,'top_bar');
    //         $expiry_view->setIcon('fa fa-clock-o')
    //                 ->setHeading('Expiring At')
    //                 ->setValue(date('d M\'y',strtotime($valid_till)))
    //                 ->makeDanger()
    //                 ->addClass('col-md-4')
    //                 ;                
    //         $expiry_view->template->trySet('expiry_date',$valid_till);
    //     }

    //     $descendants = $this->app->employee->ref('post_id')->descendantPosts();
    //     $from_date = $_GET['from_date']?:$this->app->today;
    //     $to_date = $_GET['to_date']?:$this->app->today;

    //     $activity_view = $page->add('xepan\base\View_Activity',['activity_on_dashboard'=>true,'paginator_count'=>10,'from_date'=>$from_date,'to_date'=>$to_date,'descendants'=>$descendants,'grid_title'=>'Activities']);
    //     $activity_view->addClass('col-md-4');
        
    //     $page->js(true)->univ()->setInterval($activity_view->js()->reload()->_enclose(),200000);
        
    //     $contact_model = $page->add('xepan\base\Model_Contact');
    //     $contact_model->setOrder('created_at','desc');

    //     $contact_grid = $page->add('xepan\hr\CRUD',['allow_add' =>false],null,['view\contact-grid']);
    //     $contact_grid->addClass('col-md-4');
    //     $contact_grid->setModel($contact_model,['name','type','created_by']);
    //     $contact_grid->grid->addPaginator(10);
    //     $contact_grid->grid->template->trySet('grid_title','Recent Contacts');
    // }

    function addAppFunctions(){

        $this->app->addMethod('print_r',function($app,$arr,$die=false){
            echo "<pre>";
            if(is_array($arr))
                print_r($arr);
            else
                echo $arr;
            echo "</pre>";
            if($die) die();
        });

        $this->app->addMethod('getYear',function($app,$date){
            $date = \DateTime::createFromFormat("Y-m-d", $date);
            return $date->format("Y");
        });
        $this->app->addMethod('getMonth',function($app,$date){
            $date = \DateTime::createFromFormat("Y-m-d", $date);
            return $date->format("m");
        });
        $this->app->addMethod('getDay',function($app,$date){
            $date = \DateTime::createFromFormat("Y-m-d", $date);
            return $date->format("d");
        });
        $this->app->addMethod('getDaysInMonth',function($app,$date){
            return $days = cal_days_in_month(CAL_GREGORIAN, $this->app->getMonth($date), $this->app->getYear($date));
        });

        $this->app->addMethod('nextDate',function($app,$date=null){
            
            if(!$date) $date = $this->api->today;
            $date = date("Y-m-d", strtotime(date("Y-m-d", strtotime($date)) . " +1 DAY"));    
            return $date;
        });

        $this->app->addMethod('setDate',function($app,$date){
            $this->api->memorize('current_date',$date);
            $this->api->now = date('Y-m-d H:i:s',strtotime($date));
            $this->api->today = date('Y-m-d',strtotime($date));
        
        });

        $this->app->addMethod('previousDate',function($app,$date=null){
            if(!$date) $date = $this->api->today;
            $date = date("Y-m-d", strtotime(date("Y-m-d", strtotime($date)) . " -1 DAY"));    
            return $date;
        });

        $this->app->addMethod('monthFirstDate',function($app,$date=null){
            if(!$date) $date = $this->api->now;
            return date('Y-m-01',strtotime($date));
        });

        $this->app->addMethod('monthLastDate',function($app,$date=null){
            if(!$date) $date = $this->api->now;
            return date('Y-m-t',strtotime($date));
        });

        $this->app->addMethod('isMonthLastDate',function($app,$date=null){
            if(!$date) $date = $this->api->now;
            $date = date('Y-m-d',strtotime($date));
            return strtotime($date) == strtotime($this->monthLastDate());

        });

        $this->app->addMethod('nextMonth',function($app,$date=null){
            if(!$date) $date=$this->api->today;
            return date("Y-m-d", strtotime(date("Y-m-d", strtotime($date)) . " +1 MONTH"));
        });

        $this->app->addMethod('previousMonth',function($app,$date=null){
            if(!$date) $date=$this->api->today;
            return date("Y-m-d", strtotime(date("Y-m-d", strtotime($date)) . " -1 MONTH"));
        });

        $this->app->addMethod('nextYear',function($app,$date=null){
            if(!$date) $date=$this->api->today;
            return date("Y-m-d", strtotime(date("Y-m-d", strtotime($date)) . " +1 YEAR"));
        });

        $this->app->addMethod('previousYear',function($app,$date=null){
            if(!$date) $date=$this->api->today;
            return date("Y-m-d", strtotime(date("Y-m-d", strtotime($date)) . " -1 YEAR"));
        });

        $this->app->addMethod('getFinancialYear',function($app,$date=null,$start_end = 'both'){
            if(!$date) $date = $this->api->now;
            $month = date('m',strtotime($date));
            $year = date('Y',strtotime($date));
            if($month >=1 AND $month <=3  ){
                $f_year_start = $year-1;
                $f_year_end = $year;
            }
            else{
                $f_year_start = $year;
                $f_year_end = $year+1;
            }

            if(strpos($start_end, 'start') !==false){
                return $f_year_start.'-04-01';
            }
            if(strpos($start_end, 'end') !==false){
                return $f_year_end.'-03-31';
            }

            return array(
                    'start_date'=>$f_year_start.'-04-01',
                    'end_date'=>$f_year_end.'-03-31'
                );

            });

    $this->app->addMethod('getFinancialQuarter',function ($date=null,$start_end = 'both'){
        if(!$date) $date = $this->api->today;

        $month = date('m',strtotime($date));
        $year = date('Y',strtotime($date));
        
        switch ($month) {
            case 1:
            case 2:
            case 3:
                $q_month_start='-01-01';
                $q_month_end='-03-31';
                break;
            case 4:
            case 5:
            case 6:
                $q_month_start='-04-01';
                $q_month_end='-06-30';
                break;
            case 7:
            case 8:
            case 9:
                $q_month_start='-07-01';
                $q_month_end='-09-30';
                break;
            case 10:
            case 11:
            case 12:
                $q_month_start='-10-01';
                $q_month_end='-12-31';
                break;
        }

        
        if(strpos($start_end, 'start') !== false){
            return $year.$q_month_start;
        }
        if(strpos($start_end, 'end') !== false){
            return $year.$q_month_end;
        }

        return array(
                'start_date'=>$year.$q_month_start,
                'end_date'=>$year.$q_month_end
            );

        });

        $this->app->addMethod('my_date_diff',function($app,$d1,$d2){
            $d1 = (is_string($d1) ? strtotime($d1) : $d1);
            $d2 = (is_string($d2) ? strtotime($d2) : $d2);

            $diff_secs = abs($d1 - $d2);
            $base_year = min(date("Y", $d1), date("Y", $d2));

            $diff = mktime(0, 0, $diff_secs, 1, 1, $base_year);
            return [
                    "years" => date("Y", $diff) - $base_year,
                    "months_total" => (date("Y", $diff) - $base_year) * 12 + date("n", $diff) - 1,
                    "months" => date("n", $diff) - 1,
                    "days_total" => floor($diff_secs / (3600 * 24)),
                    "days" => date("j", $diff) - 1,
                    "hours_total" => floor($diff_secs / 3600),
                    "hours" => date("G", $diff),
                    "minutes_total" => floor($diff_secs / 60),
                    "minutes" => (int) date("i", $diff),
                    "seconds_total" => $diff_secs,
                    "seconds" => (int) date("s", $diff)
                ];
        });

        $this->app->addMethod('byte2human',function($app,$bytes, $decimals = 2){
            $size = array('b','Kb','Mb','Gb','Tb','Pb','Eb','Zb','Yb');
            $factor = floor((strlen($bytes) - 1) / 3);
            return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
        });

        $this->app->addMethod('human2byte',function($app,$value){
              $result =  preg_replace_callback('/^\s*(\d*\.?\d+)\s*(?:([kmgtpy]?)b?)?\s*$/i', function ($m) {
                switch (strtolower($m[2])) {
                  case 'y': $m[1] *= 1024;
                  case 'p': $m[1] *= 1024;
                  case 't': $m[1] *= 1024;
                  case 'g': $m[1] *= 1024;
                  case 'm': $m[1] *= 1024;
                  case 'k': $m[1] *= 1024;
                }
                return $m[1];
              }, $value);

              return round($result,0);
        });

    }

}
