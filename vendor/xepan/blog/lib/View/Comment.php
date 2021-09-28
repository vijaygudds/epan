<?php

namespace xepan\blog;

class View_Comment extends \CompleteLister{
	public $options=[];
	
	function init(){
		parent::init();
	}

	function setModel($model){
		$m = parent::setModel($model);

		$post = $this->add('xepan\blog\Model_BlogPost')->load($m['blog_post_id']);
		

		if($post['show_comments']==='hide'){			
			$this->template->del('comment_wrapper');
			$this->template->trySet('msg','Comments are closed for this post');
		}else if($post['anonymous_comment_config'] === 'none' && $post['registered_comment_config'] === 'none'){
			 return $m;
		}

		$sub_form = $this->add('Form',null,'comment_form_'.$this->options['comment_form_position']);
		$comment_field = $sub_form->addField('text','Comment')->validate('required');
		$sub_form->addSubmit('Submit')->addClass('btn btn-primary btn-lg');

		if($this->app->recall('comment')){			
			$comment_field->set($this->app->recall('comment'));
			$this->app->forget('comment');			
		}

		if($sub_form->isSubmitted()){
			if($this->options['allow_anonymous_comment'] === false || $post['anonymous_comment_config']==='none'){
				$contact = $this->add('xepan\base\Model_Contact');
				if($contact->loadLoggedIn()){
					
					$comment_model = $this->add('xepan\blog\Model_Comment');
					$comment_model['comment'] = $sub_form['Comment'];
					$comment_model['blog_post_id'] = $m['blog_post_id'];	
					$comment_model['created_by_id'] = $contact->id;
					$comment_model['status'] = $post['registered_comment_config']=='moderate'?'Pending':'Approved';
					$comment_model->save();

					if($comment_model['status'] === 'Pending'){
						$msg_string = "Your message has been sent for approval";
					}else{
						$msg_string = "Done";
					}

					$sub_form->js(null,$this->js()->reload())->univ()->successMessage($msg_string)->execute();
				}else{
					$this->api->memorize('comment',$sub_form['Comment']);					
					$this->api->memorize('next_url',array('page'=>$_GET['page'],'post_id'=>$_GET['post_id']));
					$this->app->redirect($this->options['login_page']);
				}
			}
			
			$comment_model = $this->add('xepan\blog\Model_Comment');
			$comment_model['comment'] = $sub_form['Comment'];
			$comment_model['blog_post_id'] = $m['blog_post_id'];			
			$comment_model['status'] = $post['anonymous_comment_config']=='moderate'?'Pending':'Approved';
			$comment_model->save();

			if($comment_model['status'] === 'Pending'){
				$msg_string = "Your message has been sent for approval";
			}else{
				$msg_string = "Done";
			}

			$sub_form->js(null,$this->js()->reload())->univ()->successMessage($msg_string)->execute();
		}
		return $m;
	}	



	function defaultTemplate(){
		return ['view/tool/post/extra-detail/comment-list'];
	}	
}