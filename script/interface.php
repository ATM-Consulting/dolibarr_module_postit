<?php

	require '../config.php';
	
	dol_include_once('/postit/class/postit.class.php');
	
	$put = GETPOST('put');
	$get = GETPOST('get');
	
	$top = (int)GETPOST('top');
	$left = (int)GETPOST('left');
	$width = (int)GETPOST('width');
	$height = (int)GETPOST('height');
	
	$fk_object = (int)GETPOST('fk_object');
	$type_object = GETPOST('type_object');

	$title = GETPOST('title');
	$comment = GETPOST('comment');

	$PDOdb = new TPDOdb;
		
	switch ($get) {
		case 'postit-of-object':
			
			$Tab = TPostIt::getPostit($PDOdb,$fk_object,$type_object,$user->id);
			
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
			if(!empty($title)) $p->title = $title;
			if(!empty($comment)) $p->comment= nl2br($comment);
			$p->save($PDOdb);
			
			echo $p->getId();
			
			break;
		
		case 'delete':
			$p=new TPostIt;
			if($p->load($PDOdb, GETPOST('id'))) {
				$p->delete($PDOdb);
				echo 'ok';
			}
			else{
				echo 'ko';
			}
			
			
			
			break;
		
		default:
			
			break;
	}
