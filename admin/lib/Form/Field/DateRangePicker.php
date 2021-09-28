<?php

/**

from http://www.daterangepicker.com/

How to use
    
    add Field 
    $fld = $f->addField('DateRangePicker','field_name')
                ->setStartDate('2016-04-07')
                ->setEndDate('2016-04-30')
                ->showTimer(15)
                ->getBackDatesSet() // or set to false to remove
                ->getFutureDatesSet() // or skip to not include
                ;
    NOTE: dd-mm-yyyy format to set date with ' - ' as separator. SPACE DASH SPACE

    To read

    if(Form->isSubmitted()){
        $f['DateRangePicker'] returns as string as given
        or use
        $fld->getStartDate() in 'yyyy-mm-dd'
        $fld->getEndDate() in 'yyyy-mm-dd'
        in form submit
    }
    
*/

class Form_Field_DateRangePicker extends Form_Field_Line {
    public $options=array();
    
    public $start_date=null;
    public $end_date =null;

    public $show_time=false;

    public $separator=' to ';

    public $back_dates = true;
    public $future_dates = false;

    public $back_date_range=[];
    public $future_date_range=[];

    public $range_set = [];

    function init(){
        parent::init();
        if(!$this->start_date) $this->start_date = date('d-m-Y',strtotime($this->app->today));
        if(!$this->end_date) $this->end_date = date('d-m-Y',strtotime($this->app->today));
        
        if(!($this->back_dates && $this->future_dates))
            $this->back_date_range[] = "'Today': [moment(), moment()]";
        $this->back_date_range[] = "'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')]";
        $this->back_date_range[] = "'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')]";
        $this->back_date_range[] = "'Last 7 Days': [moment().subtract(6, 'days'), moment()]";
        $this->back_date_range[] = "'Last 30 Days': [moment().subtract(29, 'days'), moment()]";
        $this->back_date_range[] = "'This Month': [moment().startOf('month'), moment().endOf('month')]";
        $this->back_date_range[] = "'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]";

        $this->future_date_range[] = "'Today': [moment(), moment()]";
        $this->future_date_range[] = "'Tomorrow': [moment().add(1, 'days'), moment().add(1, 'days')]";
        $this->future_date_range[] = "'Next 7 Days': [moment().add(1, 'days'), moment().add(7, 'days')]";
        $this->future_date_range[] = "'Next 30 Days': [moment().add(1, 'days'), moment().add(30, 'days')]";
        $this->future_date_range[] = "'This Month': [moment().startOf('month'), moment().endOf('month')]";
        $this->future_date_range[] = "'Next Month': [moment().add(1, 'month').startOf('month'), moment().add(1, 'month').endOf('month')]";

        // $this->addCalendarIcon();
    }

    function showTimer($minutes_jump=30){
        $this->show_time = $minutes_jump;
        return $this;
    }

    function addCalendarIcon() {
        $this->addButton('',array('options'=>array('text'=>false)))
            ->setHtml('')
            ->setIcon(' fa fa-calendar')
            ;
    }
    function getInput($attr=array()){
        $this->js(true)
        ->_load('moment.min')
        ->_load('daterangepicker1')
        ->_css('daterangepicker')
        ;

        if($this->back_dates)
            $this->range_set = array_merge($this->range_set, $this->back_date_range);

        if($this->future_dates)
            $this->range_set = array_merge($this->range_set, $this->future_date_range);

        $this->js(true,"$('#".$this->name."').daterangepicker({
                            locale: {
                                  format: 'DD-MM-YYYY".($this->show_time?' h:mm A':'')."',
                                  separator: '".$this->separator."'
                            },
                            "
                            .
                            ($this->show_time?
                                "timePicker: true,
                                timePickerIncrement: ".$this->show_time.","
                            :
                                ''
                            )
                            .
                            "
                            'startDate': '".$this->start_date."',
                            'endDate': '".$this->end_date."',
                            'ranges': {
                               "
                               .
                               implode(",", $this->range_set)
                               .
                               "
                            }
                    });");

        return parent::getInput(array_merge(
                    array(
                        'value'=>$this->value,
                         ),$attr
                    ));
    }

    // function setStartDate($date){
    //     $this->js(true)->daterangepicker('setStartDate',$date);
    // }

    // function setEndDate($date){
    //     $this->js(true)->daterangepicker('setEndDate',$date);
    // }

    function set($value){
        // value can be valid date format, as in config['locale']['date']
        if(!$value)return parent::set(null);
        // if(is_int($value)){
        //     return parent::set(date('Y-m-d H-i-s',$value));
        // }
        // @list($d,$m,$y)=explode('/',$value);
        // if($y)$value=join('/',array($m,$d,$y));
        // elseif($m)$value=join('/',array($m,$d));
        // $value=date('Y-m-d H-i-s',strtotime($value));
        $dates = explode($this->separator, $value);
        $this->start_date = date('d-m-Y',strtotime($dates[0]));
        $this->end_date = date('d-m-Y',strtotime($dates[1]));
        // var_dump($this->start_date, $this->end_date);
        // throw new \Exception('set Called' .$value, 1);
        return parent::set($value);
    }

    function setStartDate($date){
        $this->start_date = date('d-m-Y'.($this->show_time?' H:i:s':''),strtotime($date));
        return $this;
    }

    function setEndDate($date){
        $this->end_date = date('d-m-Y'.($this->show_time?' H:i:s':''),strtotime($date));
        return $this;
    }

    function getStartDate(){
        $dates = explode($this->separator, $this->value);
        return date('Y-m-d'.($this->show_time?' H:i:s':''),strtotime($dates[0]));
    }

    function getEndDate(){

        $dates = explode($this->separator, $this->value);
        return date('Y-m-d'.($this->show_time?' H:i:s':''),strtotime($dates[1]));   
    }

    function getBackDatesSet($get=true){
        if(!$get) $this->back_dates = false;
        return $this;
    }

    function getFutureDatesSet(){
        $this->future_dates = true;
        return $this;
    }

    function get(){        
        $value=parent::get();
        // date cannot be empty string
        if($value=='')return null;
        return $value;
    }
}
