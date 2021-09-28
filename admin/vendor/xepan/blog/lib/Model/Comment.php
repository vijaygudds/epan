<?php

namespace xepan\blog;

class Model_Comment extends \xepan\base\Model_Table{
	public $table='blog_comment';
	public $status = ['Approved','Pending','Rejected'];
	public $actions = [
					'Approved'=>['view','edit','delete','pending'],
					'Pending'=>['view','edit','delete','approve','reject'],
					'Rejected'=>['view','edit','delete','pending','approve']
					];

	function init(){
		parent::init();

		$this->hasOne('xepan\base\Contact','created_by_id');
		$this->hasOne('xepan\blog\BlogPost','blog_post_id');
		
		$this->addField('comment_date')->defaultValue($this->app->now)->sortable(true)->system(true);
		$this->addField('comment')->type('text');
		$this->addField('status')->enum(['Approved','Pending','Rejected']);
		$this->addField('type');
		$this->addCondition('type','BlogComment');

		$this->getElement('status')->defaultValue('Pending');

		$this->addExpression('commented_by')->set(function($m,$q){
			return $q->expr('(IFNULL([0],"Anonymous Person"))',[$m->refSQL('created_by_id')->fieldQuery('name')]);			
		});

		$this->_dsql()->order('comment_date','desc');
	}

	//Approve Post Comment
	function approve(){
		$this['status']='Approved';
		$this->app->employee
            ->addActivity("Blog Comment : '".$this['comment']."' has been approved to show on web", null/* Related Document ID*/, $this->id /*Related Contact ID*/)
            ->notifyWhoCan('reject','Approved',$this);
		$this->save();
	}

	function reject(){
		$this['status']='Rejected';
		$this->app->employee
            ->addActivity("Blog Comment : '".$this['comment']."' has been rejected", null/* Related Document ID*/, $this->id /*Related Contact ID*/)
            ->notifyWhoCan('approve','Rejected',$this);
		$this->save();
	}

	function pending(){
		$this['status']='Pending';
		$this->app->employee
            ->addActivity("Blog Comment : '".$this['comment']."' is pending for approval", null/* Related Document ID*/, $this->id /*Related Contact ID*/)
            ->notifyWhoCan('approve','Pending',$this);
		$this->save();
	}
}