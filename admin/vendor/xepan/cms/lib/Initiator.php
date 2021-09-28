<?php

namespace xepan\cms;

class Initiator extends \Controller_Addon {
    
    public $addon_name = 'xepan_cms';

    function setup_admin(){

        if($this->app->is_admin){
            $this->routePages('xepan_cms');
            $this->addLocation(array('template'=>'templates','js'=>'templates/js','css'=>['templates/css','templates/js']))
            ->setBaseURL('../vendor/xepan/cms/');
        }

        if($this->app->inConfigurationMode)
            $this->populateConfigurationMenus();
        else
            $this->populateApplicationMenus();

        

        $this->app->addHook('entity_collection',[$this,'exportEntities']);
        $this->app->addHook('collect_shortcuts',[$this,'collect_shortcuts']);
        
        return $this;
    }

    function populateConfigurationMenus(){
        $m = $this->app->top_menu->addMenu('CMS & Websites');
        $m->addItem(['Make website Offline','icon'=>'fa fa-cog'],$this->app->url('xepan_cms_configuration'));
    }

    function populateApplicationMenus(){
        if(!$this->app->getConfig('hidden_xepan_cms',false)){
            // $this->app->cms_menu = $m = $this->app->top_menu->addMenu('CMS');
            // $menu = $this->app->side_menu->addMenu(['Website','icon'=>' fa fa-globe','badge'=>['xoxo' ,'swatch'=>' label label-primary pull-right']],'#');
            // $m->addItem([' Edit Site','icon'=>' fa fa-pencil'],'xepan_cms_editfrontlogin');
            // $m->addItem([' Carousel','icon'=>' fa fa-file-image-o'],'xepan_cms_carousel');
            // $m->addItem([' Gallery','icon'=>' fa fa-file-image-o'],'xepan_cms_gallery');
            // $m->addItem([' Template & Pages','icon'=>' fa fa-file'],'xepan_cms_cmspagemanager');
            // $m->addItem([' Themes','icon'=>' fa fa-file'],'xepan_cms_theme');
            // $m->addItem([' FileManager','icon'=>' fa fa-edit'],'xepan_cms_websites');
            // $m->addItem([' CMS Editors','icon'=>' fa fa-edit'],'xepan_cms_cmseditors');
            // $m->addItem([' Custom Form','icon'=>' fa fa-wpforms'],'xepan_cms_customform');
            // $m->addItem([' SEF Config','icon'=>' fa fa-globe'],'xepan_cms_sefconfig');
            // $m->addItem([' Configuration','icon'=>' fa fa-cog'],'xepan_cms_configuration');
            // $m->addItem([' Testimonial' , 'icon'=>'fa fa-edit'],'xepan_cms_testimonial');
        }

        $this->app->addHook('entity_collection',[$this,'exportEntities']);
        $this->app->addHook('collect_shortcuts',[$this,'collect_shortcuts']);
        
        return $this;
    }

    function getTopApplicationMenu(){
        if($this->app->getConfig('hidden_xepan_cms',false)){return [];}

        return [
                'CMS'=>[
                        [
                            'name'=>'Edit Site',
                            'icon'=>' fa fa-pencil',
                            'url'=>'xepan_cms_editfrontlogin'
                        ],
                        [   'name'=>'Carousel',
                            'icon'=>' fa fa-file-image-o',
                            'url'=>'xepan_cms_carousel'
                        ],
                        [   'name'=>'Gallery',
                            'icon'=>'fa fa-file-image-o',
                            'url'=>'xepan_cms_gallery'
                        ],
                        [   'name'=>'Template & Pages',
                            'icon'=>'fa fa-file',
                            'url'=>'xepan_cms_cmspagemanager'
                        ],
                        // [   'name'=>'Themes',
                        //     'icon'=>'fa fa-file',
                        //     'url'=>'xepan_cms_theme'
                        // ],
                        [   'name'=>'FileManager',
                            'icon'=>' fa fa-edit',
                            'url'=>'xepan_cms_websites'
                        ],
                        [   'name'=>'CMS Editors',
                            'icon'=>'fa fa-edit',
                            'url'=>'xepan_cms_cmseditors'
                        ],
                        [   'name'=>'Custom Form',
                            'icon'=>'fa fa-wpforms',
                            'url'=>'xepan_cms_customform'
                        ],
                        [   'name'=>'SEF Config',
                            'icon'=>'fa fa-globe',
                            'url'=>'xepan_cms_sefconfig'
                        ],
                        [   'name'=>'Configuration',
                            'icon'=>' fa fa-cog',
                            'url'=>'xepan_cms_configuration'
                        ],
                        [   'name'=>'Testimonial',
                            'icon'=>'fa fa-edit',
                            'url'=>'xepan_cms_testimonial'
                        ]
                    ]
            ];
    }

    function getConfigTopApplicationMenu(){
        if($this->app->getConfig('hidden_xepan_cms',false)){return [];}

        return [
                'CMS_&_Website_Config'=>[
                        [
                            'name'=>'Make website Offline',
                            'icon'=>'fa fa-cog',
                            'url'=>'xepan_cms_configuration'
                        ]
                    ]
            ];

    }

    function exportEntities($app,&$array){
        $array['CarouselCategory'] = ['caption'=>'CarouselCategory','type'=>'DropDown','model'=>'xepan\cms\Model_CarouselCategory'];
        $array['CarouselImage'] = ['caption'=>'CarouselImage','type'=>'DropDown','model'=>'xepan\cms\Model_CarouselImage'];        
        $array['Webpage'] = ['caption'=>'Webpage','type'=>'DropDown','model'=>'xepan\cms\Model_Model_Webpage'];
        $array['Custom_Form'] = ['caption'=>'Custom_Form','type'=>'DropDown','model'=>'xepan\cms\Model_Custom_Form'];
        $array['ImageGalleryCategory'] = ['caption'=>'Image Gallery Cateory','type'=>'DropDown','model'=>'xepan\cms\Model_ImageGalleryCategory'];
        $array['ImageGalleryImages'] = ['caption'=>'Image Gallery Images','type'=>'DropDown','model'=>'xepan\cms\Model_ImageGalleryImages'];
        $array['xepan_testimonial'] = ['caption'=>'Testimonial','type'=>'DropDown','model'=>'xepan\cms\Model_Testimonial'];
        $array['xepan_testimonial_category'] = ['caption'=>'Testimonial Category','type'=>'DropDown','model'=>'xepan\cms\Model_TestimonialCategory'];
    }

    function collect_shortcuts($app,&$shortcuts){
        $shortcuts[]=["title"=>"Carousel","keywords"=>"carousel carousal slideshow","description"=>"Manage Carousel","normal_access"=>"CMS -> Carousel","url"=>$this->app->url('xepan_cms_carousel'),'mode'=>'frame'];
        $shortcuts[]=["title"=>"Gallery","keywords"=>"gallery","description"=>"Manage Gallery","normal_access"=>"CMS -> Gallery","url"=>$this->app->url('xepan_cms_gallery'),'mode'=>'frame'];
        $shortcuts[]=["title"=>"Templates and Pages","keywords"=>"website pages frontend template meta tag keywords google analytics code","description"=>"Manage Website's Pages and Templates and website meta details","normal_access"=>"CMS -> Templates And Pages","url"=>$this->app->url('xepan_cms_cmspagemanager'),'mode'=>'frame'];
        $shortcuts[]=["title"=>"Change website template","keywords"=>"change website template theme","description"=>"Change your websites template","normal_access"=>"CMS -> Theme","url"=>$this->app->url('xepan_cms_theme'),'mode'=>'frame'];
        $shortcuts[]=["title"=>"FileManager","keywords"=>"website edit file html css code www","description"=>"Manage and edit files of your website","normal_access"=>"CMS -> FileManager","url"=>$this->app->url('xepan_cms_websites')];
        $shortcuts[]=["title"=>"Website Editors","keywords"=>"editors cms website frontend who can edit","description"=>"Manage which employee can edit page or template for your website","normal_access"=>"CMS -> CMS Editors","url"=>$this->app->url('xepan_cms_cmseditors'),'mode'=>'frame'];
        $shortcuts[]=["title"=>"Custom Forms","keywords"=>"subscriptions custom contact forms enquiry inquiry forms submitted data","description"=>"Manage subscription / contact / custom forms for website and their data","normal_access"=>"CMS -> Custom Form","url"=>$this->app->url('xepan_cms_customform'),'mode'=>'frame'];
        $shortcuts[]=["title"=>"SEF Config","keywords"=>"sef search engine freindly url blog commerce","description"=>"Configure Epan for SEF Urls","normal_access"=>"CMS -> SEF Config","url"=>$this->app->url('xepan_cms_sefconfig'),'mode'=>'frame'];
        $shortcuts[]=["title"=>"Make Website offline","keywords"=>"coming soon website offline down close maintenance","description"=>"Put your website on maintenance mode","normal_access"=>"CMS -> Configuration","url"=>$this->app->url('xepan_cms_configuration'),'mode'=>'frame'];
        $shortcuts[]=["title"=>"Blog Cateory","keywords"=>"blog catgory sections segments","description"=>"Manage Blog Categories","normal_access"=>"CMS -> Blog Category","url"=>$this->app->url('xepan_blog_blogpostcategory'),'mode'=>'frame'];
        $shortcuts[]=["title"=>"Blog Post","keywords"=>"blog post content article write","description"=>"Manage your blogs / articles","normal_access"=>"CMS -> Blog Post","url"=>$this->app->url('xepan_blog_blogpost'),'mode'=>'frame'];
    }

    function setup_pre_frontend(){

        $this->app->addHook('sitemap_generation',[$this,'addSiteMapEntries']);
        $this->app->addHook('ThemeApplied',[$this,'themeApplied']);

        $this->app->isEditing = false;

        if($this->app->auth->isLoggedIn()) {
            $user = $this->add('xepan\cms\Model_User_CMSEditor');
            $user->tryLoadBy('user_id',$this->app->auth->model->id);

            if($user->loaded() && !$_GET['xepan-template-edit'] && $user['can_edit_page_content']){
                $this->app->isEditing = true;
                $this->app->editing_template = null;
            }elseif($user->loaded() && $_GET['xepan-template-edit']){
                if($user['can_edit_template']){
                    $this->app->isEditing = true;
                    $this->app->editing_template = $_GET['xepan-template-edit'];
                }else{
                    throw $this->exception('You are not authorised to edit templates');
                }
            }
        }

        if($this->app->isEditing){
            $this->app->template->appendHTML('js_include','<link rel="stylesheet" type="text/css" href="'.$this->api->url()->absolute()->getBaseURL().'vendor/xepan/cms/templates/css/xepan_editor_loader.css" />');
            $this->app->jui->addStaticInclude($this->api->url()->absolute()->getBaseURL().'vendor/xepan/cms/templates/js/pace.js');
            $this->app->js(true,"Pace.on('done',function(){
                ".(string) $this->app->js()->_selector('.xepan-toolbar,.xepan-cms-toolbar,.epan-editor-top-panel')->show().";
            });");

            $this->app->js(true,'$("body").on("beforeSave",function(){$("body").find(".pace").remove();$("body").find("meta"); });');
            // $this->app->js(true,'$("body").find("[http-equiv=\"Content-Type\"]").css("border","2px solid red");');

        }

        $extra_info = json_decode($this->app->epan['extra_info'],true);
        $this->app->template->trySet('title',@$this->app->xepan_cms_page['page_title']?:@$extra_info['title']);
        $this->app->template->trySet('meta_keywords',@$this->app->xepan_cms_page['meta_kewords']?:@$extra_info['meta_keyword']);
        $this->app->template->trySet('meta_description',@$this->app->xepan_cms_page['meta_description']?:@$extra_info['meta_description']);
        $this->app->template->trySetHTML('after_body_code',@$this->app->xepan_cms_page['after_body_code']?:@$extra_info['after_body_code']);
        
    }

    function setup_frontend(){
        $this->routePages('xepan_cms');
        $this->addLocation(array('template'=>'templates','js'=>'templates/js','css'=>['templates/css','templates/js']))
        ->setBaseURL('./vendor/xepan/cms/');

        $tinymce_addon_base_path=$this->app->locatePath('addons','tinymce\tinymce');
        $this->addLocation(array('js'=>'.','css'=>'skins'))
        ->setBasePath($tinymce_addon_base_path)
        ->setBaseURL('./vendor/tinymce/tinymce/');


        $elfinder_addon_base_path=$this->app->locatePath('addons','studio-42\elfinder');
        $this->addLocation(array('js'=>'js','css'=>'css','image'=>'img'))
        ->setBasePath($elfinder_addon_base_path)
        ->setBaseURL('./vendor/studio-42/elfinder/');

        // execute template server side components
        $old_js_block = $this->app->template->tags['js_block'];
        $old_js_include = $this->app->template->tags['js_include'];
        $old_js_doc_ready = $this->app->template->tags['document_ready'];

        $auth_layout = null;
        if(($offline_content = $this->isSiteOffline()) && !$this->app->recall('offline_continue',false) ){
            if($offline_content['continue_crons'] && $this->app->page == 'xepan_base_cron'){
            }else{
                $this->app->template = $this->app->add('GiTemplate')->loadTemplate('plain');
                $this->app->page_object=$this->app->add('View',null,'Content');
                $this->app->add('View')->setHTML($offline_content['offline_site_content']);

                $this->app->template->appendHTML('js_block',implode("\n", $old_js_block[1]));
                $this->app->template->appendHTML('js_include',implode("\n", $old_js_include[1]));
                $this->app->template->appendHTML('document_ready',implode("\n",$old_js_doc_ready[1]));
                $auth_layout = 'xepan\base\Layout_Login';
            }
        }


        $user = $this->add('xepan\base\Model_User');
        
        $auth = $this->app->add('BasicAuth',['login_layout_class'=>$auth_layout]);
        $auth->usePasswordEncryption('md5');
        $auth->setModel($user,'username','password');

        if($this->isSiteOffline() && !$this->app->recall('offline_continue',false) ){
            $auth->addHook('createForm',function($a,$p){
                $f = $p->add('Form',null,null,['form/minimal']);
                $f->add('H2')->set('Login to proceed')->setAttr('align','center');
                $f->setLayout(['layout/offlinelogin','form_layout']);
                $f->addField('Line','username','Email address');
                $f->addField('Password','password','Password');
                $f->addStyle(['width'=>'30%','margin-left'=>'auto','margin-right'=>'auto']);
                $this->breakHook($f);
            });

            if($offline_content['continue_crons'] && $this->app->page == 'xepan_base_cron'){
            }
            else{
                $auth->check();
                $this->app->memorize('offline_continue',true);
                $this->app->redirect($this->app->url());
            }
        }

        if($this->app->isEditing){
            $this->app->jui
                ->addStaticInclude('ace/ace/ace')
                ->addStaticInclude('ace/ace/mode-html')
                ->addStaticInclude('ace/ace/mode-php')
                ->addStaticInclude('ace/ace/mode-css')
                ->addStaticInclude('ace/ace/theme-tomorrow')
                ->addStaticInclude('ace/jquery-ace.min')

                ->addStaticInclude('iconset/iconset-glyphicon.min')
                ->addStaticInclude('iconset/iconset-fontawesome-4.0.0.min')
                ->addStaticInclude('bootstrap-iconpicker.min')
                ->addStaticStyleSheet('bootstrap-iconpicker.min')
                ;
        }

        $this->app->jui->addStaticInclude('pnotify.custom.min');
        $this->app->jui->addStaticInclude('xepan.pnotify');
        $this->app->jui->addStaticStyleSheet('pnotify.custom.min');
        $this->app->jui->addStaticStyleSheet('animate');
        $this->app->jui->addStaticInclude('xepan_jui');
        $this->app->jui->addStaticInclude('xepan_jui');
        $this->app->jui->addStaticStyleSheet('bootstrap.min');
        $this->app->jui->addStaticInclude('bootstrap.min');
        $this->app->jui->addStaticStyleSheet('font-awesome');

        $this->makeSEF();

        // check my style css is exist or not
        $path = $this->api->pathfinder->base_location->base_path.'/websites/'.$this->app->current_website_name."/www/css";
        if(!file_exists($path)){
            $folder = \Nette\Utils\FileSystem::createDir($path);
        }  
        $path .= "/mystyle.css";
        $mystyle = " /*Define Your Custom CSS*/";
        if(!file_exists($path)){
            $file = \Nette\Utils\FileSystem::write($path,$mystyle);
        }
        $this->app->template->appendHTML('js_include','<link id="xepan-cms-custom-mystylecss" type="text/css" href="websites/'.$this->app->current_website_name.'/www/css/mystyle.css" rel="stylesheet" />'."\n");
        // end of custom css include 

        if($_GET['js_redirect_url']){                                    
            $this->app->js(true)->univ()->dialogOK('Redirecting To Page', 'Website URL'.$_GET['js_redirect_url'])->redirect($_GET['js_redirect_url']);
        }

        $old_title = $this->app->template->tags['title'];
        $old_meta_keywords = $this->app->template->tags['meta_keywords'];
        $old_meta_description = $this->app->template->tags['meta_description'];
        $old_after_body_code = $this->app->template->tags['after_body_code'];


        $this->app->add('xepan\cms\Controller_ServerSideComponentManager');

        $this->app->template->appendHTML('js_block',implode("\n", $old_js_block[1]));
        $this->app->template->appendHTML('js_include',implode("\n", $old_js_include[1]));
        $this->app->template->appendHTML('document_ready',implode("\n",$old_js_doc_ready[1]));

        $this->app->template->trySet('title',@implode("\n",$old_title[1]));
        $this->app->template->trySet('meta_keywords',@implode("\n",$old_meta_keywords[1]));
        $this->app->template->trySet('meta_description',@implode("\n",$old_meta_description[1]));
        $this->app->template->trySetHTML('after_body_code',@implode("\n",$old_after_body_code[1]));
        
        // website base added by Serverside controller
        $this->app->template->trySetHTML('website_base',$this->app->pm->base_url.$this->app->pm->base_path);




        if(isset($this->app->editing_template))
            $this->app->exportFrontEndTool('xepan\cms\Tool_TemplateContentRegion');

        $this->app->jui->addStylesheet('jquery-ui');
            
        $this->app->exportFrontEndTool('xepan\cms\Tool_Columns');
        $this->app->exportFrontEndTool('xepan\cms\Tool_Container');
        $this->app->exportFrontEndTool('xepan\cms\Tool_Section');
        $this->app->exportFrontEndTool('xepan\cms\Tool_Text');
        $this->app->exportFrontEndTool('xepan\cms\Tool_Image');
        $this->app->exportFrontEndTool('xepan\cms\Tool_SVG');
        $this->app->exportFrontEndTool('xepan\cms\Tool_Icon');
        $this->app->exportFrontEndTool('xepan\cms\Tool_CustomForm');
        $this->app->exportFrontEndTool('xepan\cms\Tool_Carousel');
        $this->app->exportFrontEndTool('xepan\cms\Tool_Marquee');
        $this->app->exportFrontEndTool('xepan\cms\Tool_BootStrapMenu');
        $this->app->exportFrontEndTool('xepan\cms\Tool_Button');
        $this->app->exportFrontEndTool('xepan\base\Tool_UserPanel');
        $this->app->exportFrontEndTool('xepan\base\Tool_Location');
        $this->app->exportFrontEndTool('xepan\cms\Tool_HtmlBlock');
        $this->app->exportFrontEndTool('xepan\cms\Tool_ImageGallery');
        $this->app->exportFrontEndTool('xepan\cms\Tool_ImageWithDescription');
        $this->app->exportFrontEndTool('xepan\cms\Tool_PopupToolTip');
        $this->app->exportFrontEndTool('xepan\cms\Tool_BootStrapSlider');
        $this->app->exportFrontEndTool('xepan\cms\Tool_AwesomeSlider');
        // $this->app->exportFrontEndTool('xepan\cms\Tool_EasyFullscreenCarouselSlider');
        $this->app->exportFrontEndTool('xepan\cms\Tool_Gallery');
        $this->app->exportFrontEndTool('xepan\cms\Tool_SlideShow');
        $this->app->exportFrontEndTool('xepan\cms\Tool_Testimonial');
        $this->app->exportFrontEndTool('xepan\cms\Tool_KeepAlive');

        return $this;
    }

    function isSiteOffline(){
        $config_m = $this->add('xepan\cms\Model_Config_FrontendWebsiteStatus');
        $config_m->tryLoadAny();

        if(!$config_m['site_offline']) return false;
        return $config_m;
    }

    function resetDB(){
        
        // $truncate_models = ['User_CMSEditor'];
        // foreach ($truncate_models as $t) {
        //     $this->add('xepan\cms\Model_'.$t)->deleteAll();
        // }

        $user = $this->add('xepan\base\Model_User_SuperUser')->tryLoadAny(); 
        $editor = $this->add('xepan\cms\Model_User_CMSEditor');

        $editor['user_id'] = $user->id;
        $editor['can_edit_template'] = 1;
        $editor['can_edit_page_content'] = 1;
        $editor->save();
    }


    function makeSEF(){
        // sef url
        $config = $this->add('xepan\base\Model_ConfigJsonModel',
            [
                'fields'=>[
                            'enable_sef'=>'checkbox',
                            'page_list'=>'text'
                        ],
                    'config_key'=>'SEF_Enable',
                    'application'=>'cms'
        ]);
        // $config->add('xepan\hr\Controller_ACL');
        $config->tryLoadAny();
        $this->app->enable_sef = false;
        if(!$this->app->isEditing && !$this->app->isAjaxOutput() && !isset($_GET['cut_page']) &&  $this->app->enable_sef = $config['enable_sef']){
            
            $this->app->setConfig('url_prefix',null);

            $this->app->hook('sef-router',[$config['page_list']]);
            
            $config_list = $this->add('xepan\base\Model_ConfigJsonModel',
                [
                    'fields'=>[
                            'expression'=>'line',
                            'page_name'=>'line',
                            'param'=>'text'
                        ],
                    'config_key'=>'SEF_List',
                    'application'=>'cms'
                ]);
            // $config_list->add('xepan\hr\Controller_ACL');
            $config_list->tryLoadAny();
            foreach ($config_list as $key => $value) {
                $this->app->app_router->addRule($value['expression'], $value['page_name'], explode(",", $value['param']));
            }
        }
    }

    function addSiteMapEntries($app,&$urls,$sef_config_page_lists){

        $page = $this->add('xepan\cms\Model_Page')
            ->addCondition('is_active',true)
            ;
        foreach ($page as $p) {
            if(strpos($p['path'], "http") ===0) continue;
            $url = $this->app->url(str_replace(".html", '', $p['path']));
            $urls[] = (string)$url;
        }
    }

    function themeApplied($app){
        $path = $this->api->pathfinder->base_location->base_path.'/websites/'.$this->app->current_website_name."/www/layout";

        $this->app->skipDefaultTemplateJsonUpdate = true;
        $layouts = $this->add('xepan/cms/Model_Layout',['path'=>$path]);
        foreach($layouts as $l) {
            if(!strpos($l['name'], ".json")) continue;

            $data = \Nette\Utils\FileSystem::read($l->path());
            $name = 'import'.str_replace(".json", "", $l['name']);
            try{
                $this->$name(json_decode($data,true));
            }catch(\Exception $e){
                throw $e;
                if($this->app->db->inTransaction()) $this->app->db->rollback();
            }
        }
    }

    // import from json file to database
    function importcarousel($data){

        foreach ($data as $category) {
           $m = $this->add('xepan\cms\Model_CarouselCategory');
           $m->addCondition('name',$category['name']);
           $m->addCondition('layout',$category['layout']);
           $m->tryLoadAny();
           if($m->loaded()) continue;

           unset($category['created_by_id']);
           unset($category['created_at']);
           unset($category['id']);

           $m->set($category);
           $m->save();

           foreach ($category['images'] as $key => $image) {
                $img = $this->add('xepan\cms\Model_CarouselImage');
                $img['carousel_category_id'] = $m->id;
                $img['title'] = $image['title'];
                $img['text_to_display'] = $image['text_to_display'];
                $img['alt_text'] = $image['alt_text'];
                $img['order'] = $image['order'];
                $img['link'] = $image['link'];
                $img['status'] = $image['status'];
                $img['file_id'] = $image['file_id'];
                $img['slide_type'] = $image['slide_type'];
                $img->save();

                foreach ($image['layers'] as $key => $value){
                    unset($value['id']);
                    $value['carousel_image_id'] = $img->id;
                    $layers = $this->add('xepan\cms\Model_CarouselLayer');
                    $layers->set($value);
                    $layers->save();
                }

           }
        }
    }

    function importcustomform($data){

        foreach($data as $form){
            $m = $this->add('xepan\cms\Model_Custom_Form');
            $m->addCondition('name',$form['name']);
            $m->tryLoadAny();
            if($m->loaded()) continue;

            $m['submit_button_name'] = $form['submit_button_name'];
            $m['form_layout'] = $form['form_layout'];
            $m['custom_form_layout_path'] = $form['custom_form_layout_path'];
            $m['auto_reply'] = $form['auto_reply'];
            $m['email_subject'] = $form['email_subject'];
            $m['message_body'] = $form['message_body'];
            $m['message_body'] = $form['message_body'];
            $m['status'] = $form['status'];
            $m['is_create_lead'] = $form['is_create_lead'];
            $m['is_associate_lead'] = $form['is_associate_lead'];
            $m->save();

            
            foreach ($form['formfields'] as $field) {
                $f = $this->add('xepan\cms\Model_Custom_FormField');
                $f['custom_form_id'] = $m->id;
                $f['name'] = $field['name'];
                $f['type'] = $field['type'];
                $f['value'] = $field['value'];
                $f['is_mandatory'] = $field['is_mandatory'];
                $f['hint'] = $field['hint'];
                $f['placeholder'] = $field['placeholder'];
                $f['save_into_field_of_lead'] = $field['save_into_field_of_lead'];
                $f->save();
            }
        }
    }

    function importimagegallery($data){

        foreach ($data as $category) {
           $m = $this->add('xepan\cms\Model_ImageGalleryCategory');
           $m->addCondition('name',$category['name']);
           $m->tryLoadAny();
           if($m->loaded()) continue;

           $m['name'] = $category['name'];
           $m['status'] = $category['status'];
           $m->save();

           foreach ($category['images'] as $key => $image) {
                $fields = $this->add('xepan\cms\Model_ImageGalleryImages');
                $fields['gallery_cat_id'] = $m->id;
                $fields['name'] = $image['name'];
                $fields['status'] = $image['status'];
                $fields['description'] = $image['description'];
                $fields['image_id'] = $image['image_id'];
                $fields->save();
           }
        }
    }

    function importwebpage($data){
        foreach ($data as $webpage) {
            $web = $this->add('xepan\cms\Model_Webpage');
            $web->addCondition('name',$webpage['name']);
            $web->tryLoadAny();
            if($web->loaded()) continue;

            $temp = $this->add('xepan\cms\Model_Webpage');
            $temp->addCondition('is_template',true);
            $temp->addCondition('name',$webpage['template']);
            $temp->tryLoadAny();

            $web['template_id'] = $temp->id;
            $web['path'] = $webpage['path'];
            $web['page_title'] = $webpage['page_title'];
            $web['meta_kewords'] = $webpage['meta_kewords'];
            $web['meta_description'] = $webpage['meta_description'];
            $web['after_body_code'] = $webpage['after_body_code'];
            $web['is_template'] = $webpage['is_template'];
            $web['is_muted'] = $webpage['is_muted'];
            $web['is_active'] = $webpage['is_active'];
            $web->save();
        }

    }
}
