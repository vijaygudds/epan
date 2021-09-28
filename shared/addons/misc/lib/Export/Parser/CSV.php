<?php

namespace misc;

class Export_Parser_CSV extends Export_Parser_Generic {
    public $column_separator = ",";
    public $row_separator = "\n";
    public $button_label = "Export CSV";
    public $export_fields = [];
    
    function parse($captions, $data){
        
        if(count($this->export_fields))
            $this->export_fields = array_combine($this->export_fields,$this->export_fields);
        
        $data = array_merge(array($captions), $data);
        foreach ($data as $row){
            $cols = array();
            foreach ($row as $field_name => $col){
               if(count($this->export_fields) && !isset($this->export_fields[$field_name])) continue;
                $cols[] = "\"" . preg_replace("/\"/", "\"\"", $col) . "\"";
            }
            if($this->output){
                $this->output .= $this->row_separator;
            }
            $this->output .= implode($this->column_separator, $cols);
        }
        $this->hook("output");
    }
}
