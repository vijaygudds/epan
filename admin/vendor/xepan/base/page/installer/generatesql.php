<?php

/**
* description: ATK Page
* 
* @author : Gowrav Vishwakarma
* @email : gowravvishwakarma@gmail.com, info@xavoc.com
* @website : http://xepan.org
* 
*/


namespace xepan\base;


class page_installer_generatesql extends \Page {
	public $title='Generate Installer SQL';

	function init(){
		parent::init();


		preg_match(
                    '|([a-z]+)://([^:]*)(:(.*))?@([A-Za-z0-9\.-]*)'.
                    '(/([0-9a-zA-Z_/\.-]*))|',
                    $this->app->getConfig('dsn'),
                    $matches
                );
		
		$form= $this->add('Form');
		$form->addField('username');
		$form->addField('password','password');

		$form->addField('mysql_host')->set($matches[5]);
		$form->addField('mysql_user')->set($matches[2]);
		$form->addField('password','mysql_password')->set($matches[4]);
		$form->addField('mysql_db')->set($matches[7]);

		$form->onSubmit(function($f){
			if(!$this->app->auth->verifyCredentials($f['username'],$f['password'])){
				$f->displayError('username','Sorry, you cant reset database');
			}

			try{
				$user = clone $this->app->auth->model;
				$this->api->db->beginTransaction();
				$this->app->db->dsql()->expr('SET FOREIGN_KEY_CHECKS = 0;')->execute();
				$this->app->resetDB = true;
				foreach ($this->app->xepan_addons as $addon) {
					$this->app->xepan_app_initiators[$addon]->resetDB();	
				}
				$this->app->db->dsql()->expr('SET FOREIGN_KEY_CHECKS = 1;')->execute();        
				$this->api->db->commit();
			}catch(\Exception_StopInit $e){

			}catch(\Exception $e){
				$this->api->db->rollback();
				$this->app->auth->login($user);
				throw $e;
			}

			$dump = new \MySQLDump(new \mysqli($f['mysql_host'], $f['mysql_user'], $f['mysql_password'], $f['mysql_db']));
            $dump->save(getcwd().'/../install.sql');

			return "SQL Generated";

		});
	}
}
