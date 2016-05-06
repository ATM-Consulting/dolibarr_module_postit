<?php

class TPostIt extends TObjetStd {
	
	function __construct() {
        global $langs;
         
        parent::set_table(MAIN_DB_PREFIX.'postit');
        parent::add_champs('fk_object,fk_actioncomm,fk_user,fk_user_todo,fk_postit',array('type'=>'int','index'=>true));
		parent::add_champs('position_top,position_left,position_width,position_height',array('type'=>'float'));
		parent::add_champs('type_object',array('index'=>true));
		
        parent::_init_vars('title,comment,color');
        parent::start();    
		
    }

	static function getPostit(&$PDOdb,$fk_object,$type_object,$fk_user) {
		
		$sql = "SELECT rowid FROM ".MAIN_DB_PREFIX."postit WHERE fk_user=".$fk_user." AND fk_object=".$fk_object." AND type_object='".$type_object."'";
		$Tab = TRequeteCore::_get_id_by_sql($PDOdb, $sql);
		
		$TPostit=array();
		foreach($Tab as $id) {
			
			$p=new TPostIt;
			$p->load($PDOdb, $id);
			$TPostit[] = $p;
			
		}
		
		return $TPostit;
		
	}
	
}
