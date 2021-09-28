<?php


namespace xepan\base;

class Controller_Grid_Format_Intro extends \Controller_Grid_Format {

	 /**
     * Initialize field
     *
     * Note: $this->owner is Grid object
     * 
     * @param string $name Field name
     * @param string $descr Field title
     *
     * @return void
     */
    public function initField($name, $descr) {
    	if(!isset($descr['intro']))
    		throw $this->exception('Please specify intro text')
    					->addMoreInfo('syntax','$crud->grid->addFormatter("field_name","xepan\base\intro",["intro"=>"Hint html"]);');
		$g = $this->owner;
		$g->columns[$name]['thparam'] .= ' data-intro="'.htmlentities($descr["intro"]).'"';

    }
    
    /**
     * Format output of cell in particular row
     *
     * Note: $this->owner is Grid object
     * 
     * @param string $field Field name
     * @param array $column Array [type=>string, descr=>string]
     *
     * @return void
     */
    public function formatField($field, $column) {
    }

}