<?php // vim:ts=4:sw=4:et:fdm=marker
/**
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
class Columns extends View_Columns
{ 
  function init(){
    parent::init();

    $this->addClass('row');
  }

	function addColumn($lg,$md=null,$sm=12,$xs=12){
		
        $c=$this->add('View');
        if(!$md) $md = $lg;
        $c->addClass("col-lg-$lg col-md-$md col-sm-$sm col-xs-$xs");
        $this->template->trySet('row_class','atk-cells');
        $c->addClass('atk-cell');
        return $c;
    }
}
