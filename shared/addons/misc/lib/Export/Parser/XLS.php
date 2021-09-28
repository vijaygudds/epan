<?php

namespace misc;
class Export_Parser_XLS extends Export_Parser_Generic {

    public $column_separator = "";
    public $row_separator = "";
    public $button_label = "Export XLS";
    public $export_fields = [];

    function init(){
        parent::init();
        $this->setOutput("application/vnd.ms-excel", "attachment", "export.xls");
    }

    function parse($captions, $data){

        if(count($this->export_fields))
            $this->export_fields = array_combine($this->export_fields,$this->export_fields);

        $data = array_merge(array($captions), $data);
        $this->output = "<table>";
        foreach ($data as $row){
            $cols = array();
            foreach ($row as $field_name => $col){
                if(count($this->export_fields) && !isset($this->export_fields[$field_name])) continue;

                $cols[] = "<td>" . strtr($col, array("<" => "&lt;", ">" => "&gt;")) . "</td>";
            }
            $this->output .= "<tr>" . implode($this->column_separator, $cols) . "</tr>";
        }
        
        $this->output .= "</table>";
        $this->hook("output");
    }

}
