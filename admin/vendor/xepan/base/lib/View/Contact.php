<?php

/**
* description: ATK Model
* 
* @author : Gowrav Vishwakarma
* @email : gowravvishwakarma@gmail.com, info@xavoc.com
* @website : http://xepan.org
* 
*/

namespace xepan\base;

class View_Contact extends \View{
	public $acl=null;
	public $document_view=null;
	public $vp ;

	public $view_document_class= 'xepan\base\View_Document';
	public $page_reload=false;


	function init(){
		parent::init();
		
		$page_url = $this->api->url();
		$this->vp = $this->add('VirtualPage');
		$this->vp->set(function($p)use($page_url){
			$f=$p->add('Form');
			$f->setModel('xepan\base\Contact',['image_id'])->load($this->api->stickyGET('contact_id'));
			$f->addSubmit('Save');
			if($f->submitted()){
				$f->save();
				$f->js()->univ()->location($page_url)->execute();
			}
		});

		$this->action = $action = $this->api->stickyGET('action')?:'view';
		$this->document_view = $this->add($this->view_document_class,['action'=> $action,'id_field_on_reload'=>'contact_id','page_reload'=>$this->page_reload],null,['view/contact']);

		
	}

	function setModel(Model_Contact $contact){
		parent::setModel($contact);
		$this->document_view->setModel($this->model,null,['first_name','branch_id','last_name','address','city','state_id','country_id','pin_code','organization','post','website']);
		if($this->action=='edit')
			$this->document_view->form->layout->add('xepan\base\Controller_Avatar',['extra_classes'=>'profile-img center-block','options'=>['size'=>200,'display'=>'block','margin'=>'auto'],'float'=>null,'model'=>$this->model]);
		else
			$this->document_view->add('xepan\base\Controller_Avatar',['extra_classes'=>'profile-img center-block','options'=>['size'=>200,'display'=>'block','margin'=>'auto'],'float'=>null]);

		$country_field=$this->document_view->form->getElement('country_id');
		$state_field=$this->document_view->form->getElement('state_id');

		if($this->app->stickyGET('country_id'))
			$state_field->getModel()->addCondition('country_id',$_GET['country_id'])->setOrder('name','asc');

		$country_field->js('change',$state_field->js()->reload(null,null,[$this->app->url(null,['cut_object'=>$state_field->name]),'country_id'=>$country_field->js()->val()]));

		if($this->model->loaded()){

			$email_m = $this->add('xepan\base\Model_Contact_Email');
			$email_m->addCondition('contact_id',$this->model->id);
			
			if($this->acl)
				$email_m->acl=$this->acl;
			$e = $this->document_view->addMany('Emails',null,'Emails',['view/addmany']);
			$e->setModel($email_m);

			
			$g=$e;
			if($e instanceof \CRUD && !$e->isEditing()) $g = $e->grid;
			if($g instanceof \Grid){
				$g->addMethod('format_value',function($g,$f){
					if($g->model['is_active']!=true){
						$g->current_row_html[$f]="<span style='color:gray'>".$g->model['value']."</span>";
					}
					if($g->model['is_valid']!=true){
						$g->current_row_html[$f]="<span style='color:red'>".$g->model['value']."</span>";
					}	
				});
				$g->addFormatter('value','value');
			}

			$e->template->tryDel('Pannel');

			$phone_m=$contact->ref('Phones');
			if($this->acl)
				$phone_m->acl=$this->acl;
			$phone = $this->document_view->addMany('Phones',null,'Phones',['view/addmany']);
			$phone->setModel($phone_m);
			$phone->template->tryDel('Pannel');

			$OtherContactInfos_m=$this->document_view->add('xepan\base\Model_Contact_Other',['for'=>$contact['type']])->addCondition('contact_id',$contact->id);
			if($this->acl)
				$OtherContactInfos_m->acl=$this->acl;
			$OtherContactInfos = $this->document_view->addMany('OtherContactInfos',['form_class'=>'xepan\base\Form_ContactOtherInfo'],'OtherContactInfos',['view/addmanywithhead']);

			$OtherContactInfos->setModel($OtherContactInfos_m);
			if($OtherContactInfos instanceof \CRUD && !$OtherContactInfos->isEditing() && $OtherContactInfos->add_button){
				$OtherContactInfos->add_button->set('Manage Other Info');
			} 

			$OtherContactInfos->template->tryDel('Pannel');

			$ims_m=$contact->ref('IMs');
			if($this->acl)
				$ims_m->acl=$this->acl;
			$im = $this->document_view->addMany('IMs',null,'IMs',['view/addmany']);
			$im->setModel($ims_m);
			$im->template->tryDel('Pannel');
			
			$event_m=$contact->ref('Events');
			if($this->acl)
				$event_m->acl=$this->acl;			
			$event = $this->document_view->addMany('Events',null,'Events',['view/addmany']);
			$event->setModel($event_m);
			$event->template->tryDel('Pannel');
			$relation_m=$contact->ref('Relations');
			if($this->acl)
				$relation_m->acl=$this->acl;			
			$relation = $this->document_view->addMany('Relations',null,'Relations',['view/addmany']);
			$relation->setModel($relation_m);
			$relation->template->tryDel('Pannel');

		}

		$contact_emails=implode(',',$this->model->getEmails());
		$this->document_view->js('click')->_selector('.do-contact-email')
			->univ()->location(
				$this->app->url(
								'xepan_communication_composeemail',
								[
									'send_email_contact'=>true,
									'contact_id'=>$this->model->id
								]
							)
				);


		return $this->model;
	}

	function recursiveRender(){
		$this->js('hover',$this->js()->slideToggle('slow')->_selector('.image-caption'))->_selector('.image-wrapper');
	
		$action = $this->api->stickyGET('action')?:'view';
		if($action == 'edit')

			$this->js('click')->_selector('.profile-img')->univ()->frameURL('Change Image',$this->vp->getURL());
		return parent::recursiveRender();
	}

	function format_datetime($fiels,$value,$m){
		$date = "<div>".date('d M Y',strtotime($value))."</div>";
		return $date;
	}

	
}
