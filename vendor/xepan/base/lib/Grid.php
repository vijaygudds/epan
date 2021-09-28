<?php

/**
* description: xEPAN Grid, lets you defined template by options
* 
* @author : Gowrav Vishwakarma
* @email : gowravvishwakarma@gmail.com, info@xavoc.com
* @website : http://xepan.org
* 
*/

namespace xepan\base;

class Grid extends \Grid{
	
    public $row_edit=true;
	public $row_delete=true;
    public $defaultTemplate = null;
    public $paginator_class='xepan\base\Paginator';
    
    public $add_sno=true;
    public $sno=1;
    public $sno_decending=false;
    public $skip_sno = false;
    public $order=null;

    public $add_footable=false;
    public $fixed_header=false;

    public $sort_icons = array(
        ' fa fa-sort',
        ' fa fa-sort-asc',
        ' fa  fa-sort-desc'
    );

    function init(){
        parent::init();
        $this->order = $this->addOrder();
    }

    function defaultTemplate(){
        if($this->defaultTemplate) return $this->defaultTemplate;
        return parent::defaultTemplate();
    }

    function removeSearchIcon(){
        $this->template->tryDel('quick_search_icon');
        return $this;
    }

    function removeAttachment(){
        $this->removeColumn('attachment_icon');
    }
	
	function precacheTemplate(){
		if($this->template->template_file != 'grid'){
            foreach ($this->columns as $name => $column) {
                if (isset($column['sortable'])) {
                    $s = $column['sortable'];
                    $temp_template= $this->add('GiTemplate')
                        ->loadTemplateFromString('<span class="{$sorticon}">');
                    $temp_template->trySet('order', $s[0])
                        ->trySet('sorticon', $this->sort_icons[$s[0]]);
                    $this->template
                        ->trySet($name.'_sortid', $sel = $this->name.'_sort_'.$name)
                        ->trySetHTML($name.'_sort', $temp_template->render());

                    $this->js('click', $this->js()->reload(array($this->name.'_sort'=>$s[1])))
                        ->_selector('#'.$sel);
                }
            }  
            return;
        }
		return parent::precacheTemplate();
	}

	function formatRow(){

	    parent::formatRow();

	    if($this->owner instanceof \CRUD){
            if(!$this->current_row_html['edit']){
                if($this->row_edit)
                    $this->current_row_html['edit']= '<a class="table-link pb_edit" href="#" data-id="'.$this->model->id.'"><span class="fa-stack"><i class="fa fa-square fa-stack-2x"></i><i class="fa fa-pencil fa-stack-1x fa-inverse"></i></span></a>';
                else
                    $this->current_row_html['edit']= '<span class="fa-stack table-link"><i class="fa fa-square fa-stack-2x"></i><i class="fa fa-pencil fa-stack-1x fa-inverse"></i></span>';
            }

            if(!$this->current_row_html['delete']){
    			if($this->row_delete)
    			    $this->current_row_html['delete']= '<a class="table-link danger do-delete" href="#" data-id="'.$this->model->id.'"><span class="fa-stack"><i class="fa fa-square fa-stack-2x"></i><i class="fa fa-trash-o fa-stack-1x fa-inverse"></i></span></a>';
    			else
    			    $this->current_row_html['delete']= '<span class="table-link fa-stack"><i class="fa fa-square fa-stack-2x"></i><i class="fa fa-trash-o fa-stack-1x fa-inverse"></i></span>';
            }
	    }
	}

	function applyTDParams($field, &$row_template = null)
    {
        // data row template by default
        if (!$row_template) {
            $row_template = &$this->row_t;
        }

        // setting cell parameters (tdparam)
        $tdparam = @$this->tdparam[$this->getCurrentIndex()][$field];
        $tdparam_str = '';
        if (is_array($tdparam)) {
            if (is_array($tdparam['style'])) {
                $tdparam_str .= 'style="';
                foreach ($tdparam['style'] as $key=>$value) {
                    $tdparam_str .= $key . ':' . $value . ';';
                }
                $tdparam_str .= '" ';
                unset($tdparam['style']);
            }

            //walking and combining string
            foreach ($tdparam as $id=>$value) {
                $tdparam_str .= $id . '="' . $value . '" ';
            }

            // set TD param to appropriate row template
            $row_template->trySet("tdparam_$field", trim($tdparam_str));
        }
    }

    /**
     * Initialize column with delete buttons
     *
     * @param string $field
     *
     * @return void
     */
    function init_delete($field)
    {
        // set special CSS class for delete buttons to add some styling
        $this->columns[$field]['button_class'] = 'atk-effect-danger atk-delete-button';
        $this->columns[$field]['icon'] = 'trash';

        // if this was clicked, then delete record
        if ($id = @$_GET[$this->name.'_'.$field]) {

            // delete record
            $this->_performDelete($id);

            if($this->app->db->inTransaction()) $this->app->db->commit();
            // show message
            $this->js()->univ()
                ->successMessage('Deleted Successfully')
                ->reload()
                ->execute();
        }

        // move button column at the end (to the right)
        $self = $this;
        $this->app->addHook('post-init', function() use($self, $field) {
            if ($self->hasColumn($field)) {
                $self->addOrder()->move($field, 'last')->now();
            }
        });

        // ask for confirmation
        $this->init_confirm($field);
    }

    function format_gmdate($f){
        $this->current_row[$f] = gmdate("H:i:s", $this->current_row[$f]);
    }

    function render(){
        if($this->add_footable)
            $this->js(true)->_load('footable')->_css('libs/footable.core')->find('table')->footable();
        if($this->fixed_header){
            $options=['zIndex'=>1];
            if($this->app->isAjaxOutput()){
                // $options['scrollContainer']=$this->js(null,"return ev.closest('.atk-table')")->_enclose();
                // $options['position']='absolute';
            }
            $this->js(true)->_load('jquery.floatThead.min')->find('table:not(.ui-dialog table)')->floatThead($options);
        }
        parent::render();
    }

    function noSno(){
        $this->add_sno = false;
    }

    function addSno($name = 's_no',$descending=false){
        if(!$this->add_sno) return;
        $this->sno_decending = $descending;
        $this->addColumn('sno','s_no',$name);
        $this->order->move('s_no','first')->now();
    }

    function init_sno($field){
        if(!$this->add_sno) return;
        if($this->sno_decending) $this->sno = $this->model->count()->getOne();
    }

    function format_sno($field){
        if($this->model->loaded() AND !$this->skip_sno){

            if($this->sno_decending){                
                $this->current_row[$field] = (($this->sno--) - ($_GET[@$this->paginator->name.'_skip']?:0));
            }
            else{
                $this->current_row[$field] = (($this->sno++) + ($_GET[@$this->paginator->name.'_skip']?:0));
            }
        }
    }

    function init_expanderplus($field)
    {
        // set column style
        @$this->columns[$field]['thparam'] .= ' style="width:40px; text-align:center"';

        // set column refid - referenced model table for example
        if (!isset($this->columns[$field]['refid'])) {

            if ($this->model) {
                $refid = $this->model->table;
            } elseif ($this->dq) {
                $refid = $this->dq->args['table'];
            } else {
                $refid = preg_replace('/.*_/', '', $this->app->page);
            }

            $this->columns[$field]['refid'] = $refid;
        }

        // initialize button widget on page load
        $class = $this->name.'_'.$field.'_expander';
        $this->js(true)->find('.'.$class)->button();

        // initialize expander
        $this->js(true)
            ->_selector('.'.$class)
            ->_load('ui.atk4_expander')
            ->atk4_expander();
    }

    function format_expanderplus($field, $column)
    {
        if (!@$this->current_row[$field]) {
            $this->current_row[$field] = $column['descr'];
        }

        // TODO:
        // reformat this using Button, once we have more advanced system to
        // bypass rendering of sub-elements.
        // $this->current_row[$field] = $this->add('Button',null,false)
        $key   = $this->name . '_' . $field . '_';
        $id    = $key . $this->app->normalizeName($this->model->id);
        $class = $key . 'expander';

        @$this->current_row_html[$field] =
            '<input type="button" '.
                'class="'.$class.' btn btn-primary" '.
                'value="'.$column['descr'].'"'.
                'id="'.$id.'" '.
                'rel="'.$this->app->url(
                    $column['page'] ?: './'.$field,
                    array(
                        'expander' => $field,
                        'expanded' => $this->name,
                        'cut_page' => 1,
                        // TODO: id is obsolete
                        //'id' => $this->model->id,
                        $this->columns[$field]['refid'].'_'.$this->model->id_field => $this->model->id
                    )
                ).'" '.
            '/>'.
            '<label for="'.$id.'"></label>';
    }

    function addButton($label, $class = 'Button')
    {
        if (!$this->buttonset) {
            $this->buttonset = $this->add('ButtonSet', null, 'grid_buttons')->setClass('atk-actions btn-group');
        }
        return $this->buttonset
            ->add($class, 'gbtn'.count($this->elements))
            ->set($label);
    }

    function addIntro($intro){

        foreach ($intro as $field => $intro) {
            if($field=='add_button'){                   
                if($this->add_button && $this->add_button !=null)
                    $this->add_button->setAttr('data-intro',$intro);
                continue;
            }
            if(!$this->grid->hasColumn($field)) {
                if($field_elem = $this->grid->hasElement($field)){
                    $field_elem->setAttr('data-intro',$intro);
                }
                continue;
            }
            $this->grid->addFormatter($field,'xepan\base\Intro',['intro'=>$intro]);
        }
    }

    // function format_datetime($field)
    // {
    //     $d = $this->current_row[$field];
    //     if (!$d) {
    //         $this->current_row[$field] = '-';
    //     } else {
    //         if ($d instanceof MongoDate) {
    //             $this->current_row[$field] = date(
    //                 $this->app->getConfig('locale/datetime', 'd/m/Y H:i:s'),
    //                 $d->sec
    //             );
    //         } elseif (is_numeric($d)) {
    //             $this->current_row[$field] = date(
    //                 $this->app->getConfig('locale/datetime', 'd/m/Y H:i:s'),
    //                 $d
    //             );
    //         } else {
    //             $d = strtotime($d);
    //             $this->current_row[$field] = $d
    //                 ? date(
    //                     $this->app->getConfig('locale/datetime', 'd/m/Y H:i:s'),
    //                     $d
    //                 )
    //                 : '-';
    //         }
    //     }
    // }


    // /**
    //  * Format field as date
    //  *
    //  * @param string $field
    //  *
    //  * @return void
    //  */
    // function format_date($field)
    // {
    //     if (!$this->current_row[$field]) {
    //         $this->current_row[$field] = '-';
    //     } else {
    //         $this->current_row_html[$field] = date(
    //             $this->app->getConfig('locale/date', 'd/m/Y'),
    //             strtotime($this->current_row[$field])
    //         );
    //     }
    // }

    // /**
    //  * Format field as time
    //  *
    //  * @param string $field
    //  *
    //  * @return void
    //  */
    // function format_time($field)
    // {
    //     $this->current_row_html[$field] = date(
    //         $this->app->getConfig('locale/time', 'H:i:s'),
    //         strtotime($this->current_row[$field])
    //     );
    // }

    function recursiveRender(){
        $this->addSno();
        return parent::recursiveRender();
    }
}
