<?php

class TPostIt extends TObjetStd {
	
	function __construct() {
        global $langs;
         
        parent::set_table(MAIN_DB_PREFIX.'postit');
        parent::add_champs('fk_object,fk_actioncomm,fk_user,fk_user_todo,fk_postit',array('type'=>'int','index'=>true));
		parent::add_champs('position_top,position_left,position_width,position_height',array('type'=>'float'));
		parent::add_champs('type_object,status',array('index'=>true));
		parent::add_champs('comment',array('type'=>'text'));
		
        parent::_init_vars('title,color');
        parent::start(); 
		
		//status = private, shared, public
		$this->status='private';
		
    }

	static function getPostit(&$PDOdb,$fk_object,$type_object,$fk_user) {
		
		$sql = "SELECT rowid FROM ".MAIN_DB_PREFIX."postit 
		WHERE (fk_user=".$fk_user." OR fk_user_todo=".$fk_user." OR status='public')
		AND fk_object=".$fk_object." AND type_object='".$type_object."' ORDER BY rowid";
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
