<?php



namespace xepan\base;

class Controller_Validator extends \Controller_Validator{

    function init(){
        parent::init();
        $this->is_mb=false;
    }

    function rule_email($a)
    {   
        if( $a and ! filter_var($a, FILTER_VALIDATE_EMAIL)){
            return $this->fail('Must be a valid email address');
        }
    }

    function rule_required($a)
    {
        if ($a==='' || $a===false || $a===null) {
            return $this->fail('must not be empty');
        }
    }
	
	function rule_unique($a,$field){

        $q = clone $this->owner->dsql();

        $result = $q
                ->where($field, $a)
                ->where($q->getField('id'),'<>', $this->owner->id)
                ->field($field)
                ->getOne();

        if($result !== null) return $this->fail('Value "{{arg1}}" already exists', $a);
    }

    function rule_unique_in_epan($a,$field){
        $q = clone $this->owner->dsql();

        $result = $q
                ->where($field, $a)
                ->where($field,'<>', '')
                ->where($q->getField('id'),'<>', $this->owner->id);

        // if($this->owner->hasElement('epan_id'))
        //     $q->where($q->expr('[0] = [1]',[$this->owner->getElement('epan_id'),$this->app->epan->id]));
        
        $result = $q->field($field)
                ->getOne();

        if($result !== null) return $this->fail('Value "{{arg1}}" already exists', $a);
    }

    function rule_unique_in_epan_for_type($a,$field){
        // return ;
        $q = clone $this->owner->dsql();

        $result = $q
                ->where($field, $a)
                ->where($q->getField('id'),'<>', $this->owner->id)
                ->where($q->getField('type'),$this->owner['type'])
                // ->where($q->expr('[0] = [1]',[$this->owner->getElement('epan_id'),$this->app->epan->id]))
                ->field($field)
                ->getOne();
    
        // throw new \Exception($a ." = ".$this->owner->id." type=".$this->owner['type']." field= ".$field." result=".$result);

        if($result !== null) return $this->fail('Value "{{arg1}}" already exists', $a);
    }

    function rule_max_in_epan($a,$field){
         $q = clone $this->owner->dsql();

        $result = $q
                ->where($field, $a)
                ->where($q->getField('id'),'<>', $this->owner->id)
                ->where($q->expr('[0] = [1]',[$this->owner->getElement('epan_id'),$this->app->epan->id]))
                ->field($q->expr('MAX([0]',[$field]))
                ->getOne();
        if($a <= $result) $this->fail('Value "{{arg1}}" is not maximum',$a);
    }

    function rule_date_after($a){
        $b=$this->pullRule();
        $b_val=$this->get($b);

        if($a=="") return $a;

        if(strtotime($a) < strtotime($b_val)) $this->fail('Value "{{arg1}}" must be greater then {{arg2}}',$a,$b_val);

        return $a;
    }

    function rule_date_after_without_time($a){
        $b=$this->pullRule();
        $b_val=$this->get($b);

        if($a=="") return $a;

        $a = date('Y-m-d',strtotime($a));
        $b_val = date('Y-m-d',strtotime($b_val));
        if(strtotime($a) < strtotime($b_val)) $this->fail('Value "{{arg1}}" must be greater then {{arg2}}',$a,$b_val);

        return $a;
    }

    function rule_to_strip_tags($a)
    {
        return strip_tags($a);
    }

    function mb_str_to_lower($a)
    {
        return ($this->is_mb) ? mb_strtolower($a, $this->encoding) : strtolower($a);
    }

    function mb_str_to_upper($a)
    {
        return ($this->is_mb) ? mb_strtoupper($a, $this->encoding) : strtoupper($a);
    }

    function mb_str_to_upper_words($a)
    {
        if ($this->is_mb)
        {
            return mb_convert_case($a, MB_CASE_TITLE, $this->encoding);
        }

        return ucwords(strtolower($a));

    }

    function mb_truncate($a, $len, $append = '...')
    {
        if ($this->is_mb)
        {
            return mb_substr($value, 0, $len, $this->encoding) . $append;
        }

        substr($value, 0, $limit).$end;
    }

    // function rule_len($a)
    // {
    //      return mb_strlen($a, $this->encoding);
    // }

    function mb_str_len($str){
        return mb_strlen($str);
    }
}