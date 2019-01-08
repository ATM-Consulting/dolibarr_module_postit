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
    
    /**
     * return sheet color according to user conf 
     * @param var $code
     * @param User $user
     * @return Hex color
     */
    static function getcolor($code, $user = false){
        global  $conf;
        $default = '#ffff88';
        
        $confkey = 'POSTIT_COLOR_' . strtoupper($code) ;
        
        $Tcode = array(
            'private' => !empty( $conf->global->POSTIT_COLOR_PRIVATE)? $conf->global->POSTIT_COLOR_PRIVATE : '#FEFE01',
            'public'  => !empty( $conf->global->POSTIT_COLOR_PUBLIC )? $conf->global->POSTIT_COLOR_PUBLIC  : '#90c6ff',
            'shared'  => !empty( $conf->global->POSTIT_COLOR_SHARED )? $conf->global->POSTIT_COLOR_SHARED  : '#B5E655',
        );
        
        if(!empty($user->conf->{$confkey}))
        {
            return $user->conf->{$confkey};
        }
        elseif(!empty($Tcode[$code]))
        {
            return $Tcode[$code];
        }
        else 
        {
            return $default;
        }
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
