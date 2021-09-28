<?php


namespace xepan\base;

use Rah\Danpu\Dump;
use Rah\Danpu\Export;

class Controller_Backup extends \AbstractController {
	public $db_name = "";
	public $db_host = "localhost";
	public $db_user = "";
	public $db_password = "";
	public $dump;
	public $file_encryption = true;
	public $file_name;
	public $dir = "backup";
	public $namespace = "xepan\base";

	function init(){
		parent::init();

		set_time_limit(0);
		
		$dsn = $this->app->getConfig('dsn');
		if (is_string($dsn)) {
            preg_match(
                '|([a-z]+)://([^:]*)(:(.*))?@([A-Za-z0-9\.-]*)'.
                '(/([0-9a-zA-Z_/\.-]*))|',
                $dsn,
                $matches
            );

      		$this->setDBUser($matches[2]);
      		$this->setDBPassword($matches[4]);
      		$this->setDBHost($matches[5]);
      		$this->setDBName($matches[7]);
        }
		$this->dump = new Dump;
		$this->dump->disableForeignKeyChecks(true);
	}

	function setFileName($file_name,$path=null){
		if(!$path)
			$path = $this->getPath();
		
		$this->file_name = $path."/".$file_name;
		return $this;
	}

	function setDBName($name){
		$this->db_name = $name;
		return $this;
	}

	function setDBHost($host){
		$this->db_host = $host;
		return $this;
	}

	function setDBUser($user){
		$this->db_user = $user;
		return $this;
	}

	function setDBPassword($pass){
		$this->db_password = $pass;
		return $this;
	}

	function getPath(){
		return $this->api->pathfinder->base_location->base_path.'/./websites/'.$this->app->current_website_name.'/'.$this->dir;
	}

	function generateFileName(){
		$extension = ".sql.gz";
		if(!$this->file_encryption)
			$extension = ".sql";

		$file_name = 'backup_'.$this->app->normalizeName($this->app->now).$extension;

		return $this->getPath()."/".$file_name;
	}
	

	function import(){
		if(!$this->file_name) throw new \Exception("import file path with name must be defined");
		
		try {
			$this->dump
				->file($this->file_name)
				->dsn('mysql:dbname='.$this->db_name.';host='.$this->db_host)
				->user($this->db_user)
				->pass($this->db_password)
				->tmp('/tmp');

				new Import($this->dump);
		} catch (\Exception $e) {
		    echo 'Export failed with message: ' . $e->getMessage();
		}
	}

	function export(){

		if(!$this->file_name){
			$this->file_name = $this->generateFileName();
		}

		try {
		    $this->dump
		        ->file($this->file_name)
		        ->dsn('mysql:dbname='.$this->db_name.';host='.$this->db_host)
		        ->user($this->db_user)
		        ->pass($this->db_password)
		        ->tmp('/tmp');

		    new Export($this->dump);
		} catch (\Exception $e) {
		    echo 'Export failed with message: ' . $e->getMessage();
		}
	}
}