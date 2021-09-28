<?php

namespace xepan\base;

class Form_Field_Multiselect extends Form_Field_DropDown {

    function init(){
        parent::init();

        $this->enableMultiSelect();
    }
}
