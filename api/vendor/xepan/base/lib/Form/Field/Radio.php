<?php

namespace xepan\base;

class Form_Field_Radio extends \Form_Field_ValueList {
    Public $image_field = null;
    
    // function validate(){
    //     if(!isset($this->value_list[$this->value]) && (!$this->value && $this->empty_value)){
    //         $this->displayFieldError("Value ".$this->value." is not one of offered values");
    //     }
    //     return parent::validate();
    // }
    function getInput($attr=array()){
        
        $output = '<div id="'.$this->name.'" class="atk-form-options">';

        foreach($this->getValueList() as $value=>$descr){
            if($descr instanceof AbstractView){
                $descr=$descr->getHTML();
            }else{
                $descr=$this->api->encodeHtmlChars($descr);
            }

            $image = "";
            if($value and $this->image_field){
                $temp_model = clone($this->model);
                $temp_model->load($value);
                $image = '<img src="'.$temp_model[$this->image_field].'" style="width:100%;"/>';
            }
            
            $output.=
                "<div style='float:left; width : 25% '>".$this->getTag('input',
                        array_merge(
                            array(
                                'id'=>$this->name.'_'.$value,
                                'name'=>$this->name,
                                'data-shortname'=>$this->short_name,
                                'type'=>'radio',
                                'value'=>$value,
                                'checked'=>$value == $this->value
                                 ),
                            $this->attr,
                            $attr
                            ))
                .'<label for="'.$this->name.'_'.$value.'">'.$descr."</label>".$image."</div>";
        }
        $output .= '</div>';
        return $output;
    }


    function setImageField($image_field = null)
    {   

        if(!$image_field)
            throw new \Exception("Please Specify Field Name");

        $this->image_field = $image_field;
        return $this;
    }
}
