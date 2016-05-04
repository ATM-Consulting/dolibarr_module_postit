<?php

class TPostIt extends TObjetStd {
	
	function __construct() {
        global $langs;
         
        parent::set_table(MAIN_DB_PREFIX.'postit');
        parent::add_champs('fk_object,fk_actioncomm',array('type'=>'int','index'=>true));
		parent::add_champs('top,left,width,height',array('type'=>'float'));
		parent::add_champs('type_object',array('index'=>true));
		
        parent::_init_vars('title,comment,color');
        parent::start();    
		
    }
	
}
