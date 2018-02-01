<?php

class PostIt extends SeedObject {
	
	public $element='postit';
	
	public $table_element='postit';
	
	function __construct($db) {
		
		$this->db = &$db;
		
        global $langs;
        
        $this->fields=array(
        		'fk_object'=>array('type'=>'integer','index'=>true)
        		,'fk_actioncomm'=>array('type'=>'integer','index'=>true)
        		,'fk_user'=>array('type'=>'integer','index'=>true)
        		,'fk_user_todo'=>array('type'=>'integer','index'=>true)
        		,'fk_postit'=>array('type'=>'integer','index'=>true)
        		,'position_top'=>array('type'=>'double')
        		,'position_left'=>array('type'=>'double')
        		,'position_width'=>array('type'=>'double')
        		,'position_height'=>array('type'=>'double')
        		,'type_object'=>array('type'=>'string','index'=>true,'length'=>50)
        		,'status'=>array('type'=>'string','index'=>true,'length'=>50)
        		,'comment'=>array('type'=>'text')
        		,'title'=>array('type'=>'string')
        		,'color'=>array('type'=>'string')
        );
       
        $this->init();
        
		$this->title = $langs->trans('NewNote');
		$this->comment = $langs->trans('NoteComment');
		//status = private, shared, public
		$this->status='private';
		
    }

	static function getPostit($fk_object,$type_object,$fk_user) {
		
		global $db;
		
		$sql = "SELECT rowid FROM ".MAIN_DB_PREFIX."postit 
		WHERE (fk_user=".$fk_user." OR fk_user_todo=".$fk_user." OR status='public' OR status='shared')
		AND (fk_object=".$fk_object." OR status='shared') AND type_object='".$type_object."' ORDER BY rowid";
		
		$res = $db->query($sql);
		
		$TPostit=array();
		while($obj = $db->fetch_object($res)) {
			
			$p=new PostIt($db);
			$p->fetch( $obj->rowid);
			$TPostit[] = $p;
			
		}
		
		return $TPostit;
		
	}
	
}
