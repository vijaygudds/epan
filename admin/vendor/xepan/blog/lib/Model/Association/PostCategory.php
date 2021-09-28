<?php

namespace xepan\blog;

class Model_Association_PostCategory extends \xepan\base\Model_Table{
	
	public $table='blog_post_category_association';
	public $acl=false;
	
	function init(){
		parent::init();

		$this->hasOne('xepan\blog\BlogPostCategory','blog_post_category_id');
		$this->hasOne('xepan\blog\BlogPost','blog_post_id');

		$this->addExpression('post_status')->set($this->refSQL('blog_post_id')->fieldQuery('status'));
	}
}