<?php

namespace xepan\webwidgets;

class Tool_Testimonials extends \xepan\cms\View_Tool{
	public $count = 1;
	public $options = [
				'template_type' =>'grid',
				'record_limit'=>null,
				'add_paginator'=>true,
				'paginator_limit'=>10,
				'list_column_width'=>4,
				'show_created_at'=>true,
				'show_title'=>true,
			];

	function init(){
		parent::init();

		$testimonial_m = $this->add('xepan\webwidgets\Model_Testimonial');
		
		if($record_limit = $this->options['record_limit'])
			$testimonial_m->setLimit($record_limit);

		$template = 'tool\testimonial/'.$this->options['template_type'];
		$testimonial_cl = $this->add('CompleteLister',null,null,[$template]);
		$testimonial_cl->setModel($testimonial_m);	
		
		if($this->options['template_type'] != 'corusel' And $this->options['add_paginator'] == true){
			$paginator = $testimonial_cl->add('Paginator');				
			$paginator->setRowsPerPage($this->options['paginator_limit']);
		}

		$testimonial_cl->addHook('formatRow',function($l){
			if($this->options['template_type'] == 'corusel'){
				if($this->count == 1)
					$l->current_row_html['active'] = "active";
				else
					$l->current_row_html['active'] = "deactive";
				$this->count++;
			}

			if($this->options['show_title'] ==false){
				$l->current_row_html['title_wrapper'] = ' ';
			}

			if($this->options['show_created_at'] == false){
				$l->current_row_html['created_at_wrapper'] = ' ';
			}

			if($this->options['template_type'] =='list'){
				$l->current_row_html['list_column_width'] = $this->options['list_column_width'];
			}
		});
	}	
}