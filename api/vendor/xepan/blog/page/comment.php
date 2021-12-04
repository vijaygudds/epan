<?php
 
namespace xepan\blog;

class page_comment extends \xepan\base\Page {
	public $title='Blog Post Comments';

	function init(){
		parent::init();

		$blog_id = $this->app->stickyGET('blog_id');

		$comment = $this->add('xepan\blog\Model_Comment');
		$comment->addCondition('blog_post_id',$blog_id);			
		
		$crud = $this->add('xepan\hr\CRUD',null,'comments',['view\post\comment']);
		
		if($crud->isEditing('add')){
			$comment->addCondition('created_by_id',$this->app->employee->id);			
		}

		$crud->setModel($comment)->setOrder('comment_date','desc');
		$crud->grid->addQuickSearch(['comment']);
		$crud->grid->addPaginator(50);

		$blog = $this->add('xepan\blog\Model_BlogPost')
			         ->load($blog_id);
		$this->template->trySet('blog_title',$blog['title']);
		
	}


	function defaultTemplate(){
		return['view/post/commentblog'];
	}
}

