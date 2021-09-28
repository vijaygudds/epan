<?php // vim:ts=4:sw=4:et:fdm=marker
/*
 * Undocumented
 *
 * @link http://agiletoolkit.org/
*//*
==ATK4===================================================
   This file is part of Agile Toolkit 4
    http://agiletoolkit.org/

   (c) 2008-2013 Agile Toolkit Limited <info@agiletoolkit.org>
   Distributed under Affero General Public License v3 and
   commercial license.

   See LICENSE or LICENSE_COM for more information
 =====================================================ATK4=*/
namespace xepan\base;

class Form_Field_RadioButton extends \Form_Field_ValueList {
    function getInput($attr=array()){
        $output = '<div id="'.$this->name.'" class="atk-form-options btn-group xepan-form-radiobtn" data-toggle="buttons">';
        foreach($this->getValueList() as $value=>$descr){

            if($descr instanceof AbstractView){
                $descr=$descr->getHTML();
            }else{
                $descr=$this->app->encodeHtmlChars($descr);
            }
                $class = "pull-left btn btn-primary";
                if($value == $this->value){
                    $class .= " btn-primary active";
                }
                $output.=
                    "<label class='".$class."'  for='".$this->name.'_'.$value."'>"
                    .$this->getTag('input',
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
                .$descr."</label>";
        }             
        $output .= '</div>';    
        return $output;
    }
}
