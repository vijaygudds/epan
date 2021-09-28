<?php

class Form_Field_DateTimePicker extends Form_Field_Line {
    public $options=array();
    function init(){
        parent::init();

        $this->addCalendarIcon();
    }
    function addCalendarIcon() {
        $this->addButton('',array('options'=>array('text'=>false)))
            ->setHtml('')
            ->setIcon(' fa fa-calendar')
            // ->js('click',$this->js()->handleDtpicker('show'))
            ;
        // $this->js('focus', $this->js()->handleDtpicker('show'));
    }
    function getInput($attr=array()){
        
        $this->js(true)->_load('bootstrap-datetimepicker')
        ->_css('libs/bootstrap-datetimepicker')
        ;
        $this->js(true)->appendDtpicker(array_merge(array(
                    'minuteInterval' => 15,
                    'closeOnSelected'=> true,
                    'dateFormat' => 'YYYY-MM-DD hh:mm:00',
                    'autodateOnStart'=> false
                    ),$this->options));

        return parent::getInput(array_merge(
                    array(
                        'value'=>date('Y-m-d H:i:00',strtotime($this->value)),
                         ),$attr
                    ));
    }
    function set($value){

        // value can be valid date format, as in config['locale']['date']
        if(!$value)return parent::set(null);
        if(is_int($value)){
            return parent::set(date('Y-m-d H-i-s',$value));
        }
        @list($d,$m,$y)=explode('/',$value);
        if($y)$value=join('/',array($m,$d,$y));
        elseif($m)$value=join('/',array($m,$d));
        $value=date('Y-m-d H:i:00',strtotime($value));
        return parent::set($value);
    }
    function get(){
        $value=parent::get();
        // date cannot be empty string
        if($value=='')return null;
        return $value;
    }
}
