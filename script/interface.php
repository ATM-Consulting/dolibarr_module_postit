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
			foreach($Tab as &$p) {
				if($user->rights->postit->allaction->write || ($user->rights->postit->myaction->write && $p->fk_user == $user->id) ) {
					$p->rightToDelete = 1;	
					$p->rightToSetStatus = 1;	
				}
				else{
					$p->rightToDelete = 0;
					$p->rightToSetStatus = 0;
				}
			}
			
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
				$p->fk_user = $user->id;
			
			}
			if(!empty($width)) $p->position_width = $width;
			if(!empty($height)) $p->position_height = $height;
			if(!empty($top)) $p->position_top = $top;
			if(!empty($left)) $p->position_left= $left;
			if(!empty($title)) $p->title = $title;
			if(!empty($comment)) $p->comment= $comment;
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
		
		case 'change-status':
			
			$p=new TPostIt;
			if($p->load($PDOdb, GETPOST('id'))) {
				$current = GETPOST('current');
				if($current == 'private') $p->status = 'public';// $p->status = 'shared';
				//else if($current == 'shared') $p->status = 'public';
				else $p->status = 'private';
				$p->save($PDOdb);
				
				echo $p->status;
				
			}
			else{
				echo 'ko';
			}
			
			break;
		
		default:
			
			break;
	}
