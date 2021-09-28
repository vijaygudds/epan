<?php

namespace xepan\blog;

class Tool_Archieve extends \xepan\cms\View_Tool{
	public $options = [

					'show_month'=>true,
					'redirect_page_url'=>'blog'
				];

	function init(){
		parent::init();

		if($this->owner instanceof \AbstractController) return;
		
		$post = $this->add('xepan\blog\Model_BlogPost');
		$post->_dsql()->group($post->_dsql()->expr("[0]",[$post->getElement('year')]));		

		$post->addExpression('year_post_count')->set(function($m,$q){
			return $q->expr("count([0])",[$m->getElement('id')]);
		});

		$post->addExpression('month_post_count')->set(function($m,$q){
			return $q->expr("( COUNT([0]) )",[$m->getElement('id')]);
		});
		
		$cl = $this->add('CompleteLister',null,null,['view/tool/post/archieve']);
		if(!$post->count()->getOne())
			$cl->template->set('not_found_message','No Record Found');
		else
			$cl->template->del('not_found');

		$cl->setModel($post);

		$cl->add('xepan\cms\Controller_Tool_Optionhelper',['options'=>$this->options,'model'=>$post]);
	}

	function addToolCondition_row_show_month($value, $l){
		if(!$value){
			$l->current_row_html["month_lister"] = "";
			return;
		}

		$post = $l->add('xepan\blog\Model_BlogPost');
		$post->addCondition('year',$l->model['year']);
		$post->_dsql()->group($post->_dsql()->expr("[0]",[$post->getElement('month')]));


		$month_l = $l->add('CompleteLister',null,"month_lister",['view/tool/post/archieve_month']);		
		$month_l->setModel($post);

		$month_l->addHook('formatRow',function($ml){
			$ml->current_row_html["url"] = $this->app->url($this->options['redirect_page_url'],['month'=>$ml->model['month']]);
			// $ml->current_row_html["url"] = '123';
		
		});
		// $l->current_row['url'] = $this->app->url($this->options['redirect_page_url'],['category_id'=>$l->model->id]);

		// $month_l->addHook('formatRow',function($ml){
		// 	$ml->current_row_html["month"] = date_format($ml->model['created_at'],"Y-m");
		
		// });

		$l->current_row_html['month_lister'] = $month_l->getHtml();
		

	}
}
