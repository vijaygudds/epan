<?php
class Frontend extends ApiFrontend {
    public $api_public_path;
    public $api_base_path;
    public $currentBridge=null;
    public $isLive = false;
    public $requestDoc= false;
    public $responseDoc = false;

    public $is_frontend= true;
    public $is_admin= false;
    public $isEditing = false;
    public $current_website_name=null;
    public $current_template_name=null;

    public $app_router=null;

    public $page_class='xepan\cms\page_cms';

    function init() {
        parent::init();

        $app_paths = array('vendor','shared/addons2','shared/addons','shared/apps');
        if($c_app_path = $this->getConfig('custom_app_path',false)){
            $app_paths = array_merge($c_app_path,$app_paths);
        }

         $this->api->pathfinder
            ->addLocation(array(
                'addons' => $app_paths,
            ))
            ->setBasePath($this->pathfinder->base_location->getPath() . '/..');
        
        $this->add('jUI');
        
        // Should come from any local DB store
        $this->xepan_addons = $addons = ['xepan\\base'];
        $this->xepan_app_initiators = $app_initiators=[];

        $this->app_router = $this->add("Controller_PatternRouter");
        // ->addRule("(news\/.*)", "news_item", array("u"))

        $app_initiators=[];
        foreach ($addons as $addon) {
            $this->xepan_app_initiators[$addon] = $app_initiators[$addon] = $this->add("$addon\Initiator")->setup_frontend();
        }

        if($c_app_path){
            foreach ($this->getConfig('custom_app_list') as $custom_apps) {
                $this->xepan_app_initiators[$custom_apps] = $app_initiators[$custom_apps] = $this->add("$custom_apps\Initiator")->setup_frontend();
            }
        }

        $this->app_router->route();

    }

    function defaultTemplate(){

        $epan_domain_array = $this->recall('epan_domain_array',[]);
        $url = "{$_SERVER['HTTP_HOST']}";
        $domain = str_replace('www.','',$this->extract_domain($url))?:'www';
        $sub_domain = str_replace('www.','',$this->extract_subdomains($url))?:'www';

        $service_host = $this->getConfig('xepan-service-host',false);
        if($service_host && !in_array($domain,$service_host)){
            $epan = $domain;
        }else{
            $epan = $sub_domain;
        }

        if(!isset($epan_domain_array[$epan])){ 
            $this->readConfig("websites/www/config.php");
            $this->dbConnect();
            $epan_hash = $this->db->dsql()->table('epan')->where($this->db->dsql()->orExpr()->where('name',$epan)->where('aliases','like','%"'.$epan.'"%'))->getHash();
            if($service_host && ($epan_hash['status'] == 'Expired' || (strtotime($epan_hash['expiry_date']) < strtotime(date('Y-m-d H:i:s'))) && $epan_hash['status'] !=='Paid') ) {
                if(!is_array($service_host)) $service_host=[$service_host];
                $this->redirect('http://'.$service_host[0].'/expired-info');
            }

            $this->memorize('epan_from_root',$epan_hash);
            $epan_domain_array[$epan] = $epan_hash['name'];
            $this->memorize('epan_domain_array',$epan_domain_array);
        }
        // die(print_r($epan,true));
        // die($epan['name']);
        $epan = $epan_domain_array[$epan];
        $current_website = $this->current_website_name = $epan;

        $this->readConfig("websites/$this->current_website_name/config.php");

        //  Moved from Init to here
        
        $this->dbConnect();

        // Might come handy when multi-timezone base networks integrates
        $this->today = date('Y-m-d',strtotime($this->recall('current_date',date('Y-m-d'))));
        $this->now = date('Y-m-d H:i:s',strtotime($this->recall('current_date',date('Y-m-d H:i:s'))));

        $this->api_public_path = dirname(@$_SERVER['SCRIPT_FILENAME']);
        $this->api_base_path = dirname(dirname(@$_SERVER['SCRIPT_FILENAME']));

        $this->api->pathfinder
            ->addLocation(array(
                'addons' => array('vendor','shared/addons2','shared/addons','shared/apps'),
            ))
            ->setBasePath($this->pathfinder->base_location->getPath() );

        $this->addLocation(array(
            'page'=>array("websites/$current_website/www"),
            'js'=>array("websites/$current_website/www/js"),
            'css'=>array("websites/$current_website/www","websites/$current_website/www/css"),
            'template'=>["websites/$current_website/www"],
            'addons'=> ['websites/'.$current_website.'/www']
        ))->setParent($this->pathfinder->base_location);


        $this->addLocation(array('template'=>'templates','js'=>'templates/js','css'=>['templates/css','templates/js']))
            ->setBaseURL('../vendor/xepan/cms/');

        $x= $this->db->dsql()->table('installed_application')->join('application','application_id')->where('namespace','xepan\cms')->where('is_active',1)->get();

        if(count($x)==0){
            die('Forntend requires xepan\CMS');
        }


        // if using['empty'] as page template and page as current app layout

        // Original lines but since SEF is working and filtering later SEF, url are assuming complete lines
        // like "blog-item/bla-bla-bla" and system is reading this into this->page as "blog-item_bla-bla-bla"
        // and this was causing wrong page to be asked from database -> so not found -> and wrong things got up

        $this->xepan_cms_page = ['name'=>$this->page,'path'=>$this->page];
        $page_m = $this->add('xepan\cms\Model_Page')->tryLoadBy('path',str_replace("_", "/",$this->page.'.html'));
        // ==== As a work around, changed the lines as below 
        
        if(!$page_m->loaded()){
            $this->xepan_cms_page = ['name'=>$this->page,'path'=>explode("_", $this->page)[0]];
            $page_m = $this->add('xepan\cms\Model_Page')->tryLoadBy('path',explode("_", $this->page)[0] .'.html');
        }
        // $page_m = $this->add('xepan\cms\Model_Page')->tryLoadBy('path',$this->page.'.html');
        // die($this->page);

        if($page_m->loaded()){
            $page_m->mergeFromTemplate();
        }

        if($this->api->stickyGET('xepan-template-edit')){
            if($page_m->loaded() && $page_m['template_path'])
                $this->xepan_cms_page = $page_m;
            return [str_replace("_", "/",$this->page)];
        }

            

        if($page_m->loaded() && $page_m['template_path']){
            $this->xepan_cms_page = $page_m;
            return ['layout/'.str_replace(".html", "", $page_m['template_path'])];
        }elseif($this->pathfinder->locate('template',$t='layout/'.$this->page.'.html','path',false)){
            $this->current_template_name = 'layout/'.$this->page;
            return ['layout/'.$this->page];
        }elseif($this->pathfinder->locate('template','layout/default.html','path',false)){            
            $this->current_template_name = 'layout/default';
            return ['layout/default'];
        }elseif(!$this->layout){
            throw new \Exception("No Website content found or No Template in Website for ". $this->current_website_name, 1);
        }else{
            throw new \Exception("Error Processing Request", 1);            
        }  
    }

    protected function loadStaticPage($page){
        $layout = $this->layout ?: $this;

        // IF we are trating template page as page and ['emty'] as template
        if($_GET['xepan-template-edit']){
            return $this->page_object=$layout->add($this->page_class,['name'=>$page,'page_requested'=>$page],'Content',['empty']);
        }

        try{
            $t='page/'.str_replace('_','/',strtolower($page));
            $this->locatePath('template',$t);
            // $this->template->findTemplate($t);
            $this->page_object=$layout->add($page,$page,'Content',array($t));
        }catch(\PathFinder_Exception $e2){
            try{
                $t='page/'.strtolower($page);
                // $this->template->findTemplate($t);
                $this->locatePath('template',$t);
                $this->page_object=$layout->add($page,$page,'Content',array($t));
            }catch(\PathFinder_Exception $e3){
                $t=strtolower($page);
                try{
                    $original_page=null;
                    if(!file_exists(getcwd().'/websites/'.$this->current_website_name.'/www/'.str_replace("_", "/",$page).".html")){
                        throw $e3;
                    }
                    $this->page_object=$layout->add($this->page_class,['name'=>$page,'page_requested'=>$original_page],'Content',[str_replace("_", "/",$page)]);
                }catch(\PathFinder_Exception $e4){
                    throw $e4;
                }
            }
        }

        return $this->page_object;
    }

    function pageNotFound($e) {
        $this->app->redirect('404');
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

}
