<?php

	require '../config.php';
	
	dol_include_once('/postit/class/postit.class.php');
	
	$put = GETPOST('put');
	$get = GETPOST('get');
	
	$top = (int)GETPOST('top');
	$left = (int)GETPOST('left');
	$width = (int)GETPOST('width');
	$height = (int)GETPOST('heigth');
	
	$fk_object = (int)GETPOST('fk_object');
	$type_object = GETPOST('type_object');


	$PDOdb = new TPDOdb;
		
	switch ($get) {
		case 'postit-of-object':
			
			$Tab = TRequeteCore::_get_id_by_sql($PDOdb, "SELECT rowid FROM ".MAIN_DB_PREFIX."postit 
			WHERE fk_user=".$user->id." AND fk_object=".$fk_object." AND type_object='".$type_object."'");
			
			__out($Tab,'json');
			
			break;
		
		case 'postit':
			
			$p=new TPostIt;
			if(!$p->load($PDOdb, GETPOST('id'))) {
				__out($p,'json');
			}
			
			break;
		
		default:
			
			break;
	}
		
	switch ($put) {
		case 'postit':
			
			$p=new TPostIt;
			if(!$p->load($PDOdb, GETPOST('id'))) {
				$p->fk_object = $fk_object;
				$p->type_object = $type_object;
			}
			$p->fk_user = $user->id;
			if(!empty($width)) $p->position_width = $width;
			if(!empty($height)) $p->position_height = $height;
			if(!empty($top)) $p->position_top = $top;
			if(!empty($left)) $p->position_left= $left;
			$p->save($PDOdb);
			
			echo $p->getId();
			
			break;
		
		default:
			
			break;
	}
