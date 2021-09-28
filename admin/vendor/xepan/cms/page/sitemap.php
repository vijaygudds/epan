<?php
namespace xepan\cms;

class page_sitemap extends \Page{
	public $title="SiteMap Generator";

	function init(){
		parent::init();

    $epan_info = $this->app->recall('epan_from_root');

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

		// echo sitemap xml
    $urls=[];

    $this->app->hook('sitemap_generation',[&$urls,$config['page_list']]);

    $epan_park_domain = explode(",", $epan_info['aliases']);
    $epan_park_domain[] = $this->app->epan['name'];


    $domain_host_detail = parse_url($this->app->pm->base_url);

    $domain_list = [];
    foreach ($epan_park_domain as $key => $domain_name) {

      $service_host = $this->app->getConfig('xepan-service-host','xavoc.com');
      if(is_array($service_host)) $service_host = $service_host[0];
      
      $domain_name = trim(str_replace('"', '',$domain_name));
      if(strpos( $domain_name, "." ) === false) // its an alias
        $domain_name .= ".".str_replace('www.', '', $service_host); // xepan-service-host defines in root config file that shows what is your epan service hosts, not for opensource version but only for epan services

      $domain_list[] = $domain_host_detail['scheme']."://".$domain_name;
    }

    $site_map_list = [];
    $xml = '<?xml version="1.0" encoding="UTF-8"?>
              <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" >';

    foreach ($domain_list as $key => $domain) {
      foreach ($urls as $key => $url) {
        // $site_map_list[] = $domain.$url;
        $xml .= str_replace("&", "&amp;", "<url><loc>$domain$url</loc></url>");
      }
    }

    $xml .= '</urlset>';

    header('Content-Type: application/xml; charset=utf-8');
    echo $xml;

    exit;

    // for each parked domain and aliases 
    // throw hook for commerce and blogs to add pages
    // like /category/in/commerce :: how to get category page name here
    // or category/product/slug-url  :: how to get item-detail page here
    // or same in blog :: how to get blog pages name here or in initiator

    // may be some backend configuration on sef page for commerce and blog 
    // or may be that page itself has hooks to let others add form field that will be added 
    // in a json string 


		// and exit

	}
}/*

Sample XML file

<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" >
  <url> 
    <loc>http://www.example.com/foo.html</loc>
  </url>
</urlset>
*/