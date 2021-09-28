<?php

namespace misc;
class Export_Advanced extends Export_Basic{
	public $export_fields=[];

    function init(){
        parent::init();
        
        $this->add("misc/Export_Parser_CSV",['export_fields'=>$this->export_fields]);
        $this->add("misc/Export_Parser_XLS",['export_fields'=>$this->export_fields]);
    }
}
