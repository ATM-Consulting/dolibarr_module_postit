<?php

	require '../config.php';
	
	dol_include_once('/postit/class/postit.class.php');
	
	$langs->load('postit@postit');
	$put = GETPOST('put');
	$get = GETPOST('get');
	
	$top = (int)GETPOST('top');
	$left = (int)GETPOST('left');
	$width = (int)GETPOST('width');
	$height = (int)GETPOST('height');
	$fk_postit = (int)GETPOST('fk_postit');
	
	$fk_object = (int)GETPOST('fk_object');
	$type_object = GETPOST('type_object');

	$title = GETPOST('title');
	$comment = GETPOST('comment');

	$PDOdb = new TPDOdb;
		
	switch ($get) {
		case 'postit-of-object':
			
			$Tab = TPostIt::getPostit($PDOdb,$fk_object,$type_object,$user->id);
			foreach($Tab as &$p) {
				
				$p->rightResponse = ($p->fk_user == $user->id) ? 0 : 1;
				
				if($user->rights->postit->allaction->write || ($user->rights->postit->myaction->write && $p->fk_user == $user->id) ) {
					$p->rightToDelete = 1;	
					$p->rightToSetStatus = 1;
					$p->rightEdit = 1;	
				}
				else{
					$p->rightToDelete = 0;
					$p->rightToSetStatus = 0;
					$p->rightEdit = 0;
				}
				
				${'u'.$p->fk_user}=new User($db);
				${'u'.$p->fk_user}->fetch($p->fk_user);
				$p->author = ${'u'.$p->fk_user}->getFullName($langs);
				
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
				
				if($fk_postit>0) {
					$parent=new TPostIt;
					if($parent->load($PDOdb, $fk_postit)) {
						$p->fk_postit = $fk_postit;
						$p->title = $langs->trans('NewResponse') ;
						$p->comment = '';
										
						$p->position_top = $parent->position_top + 30;
						$p->position_left = $parent->position_left + 30;
					}
				}
				
			}
			if(!empty($width)) $p->position_width = $width;
			if(!empty($height)) $p->position_height = $height;
			if(!empty($top)) $p->position_top = $top;
			if(!empty($left)) $p->position_left= $left;
			if(!empty($title)) $p->title = $title;
			if(!empty($comment)) $p->comment= $comment;
			$p->save($PDOdb);
			
			$p->rightResponse = ($p->fk_user == $user->id) ? 0 : 1;
			
			if($user->rights->postit->allaction->write || ($user->rights->postit->myaction->write && $p->fk_user == $user->id) ) {
				$p->rightToDelete = 1;	
				$p->rightToSetStatus = 1;
				$p->rightEdit = 1;	
			}
			else{
				$p->rightToDelete = 0;
				$p->rightToSetStatus = 0;
				$p->rightEdit = 0;
			}
			
			$u=new User($db);
			$u->fetch($p->fk_user);
			$p->author = $u->getFullName($langs);
			
			__out($p, 'json');
			
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
				$current = trim(GETPOST('current'));
				
				if($p->fk_object == -1) {
					if($current == 'private') $p->status = 'public';
					else $p->status = 'private';
				}
				else{
					if($current == 'private') $p->status = 'public';
					else if($current == 'public') $p->status = 'shared';
					else $p->status = 'private';
					
				}
				
				$p->save($PDOdb);
				
				echo trim($p->status);
				
			}
			else{
				echo 'ko';
			}
			
			break;
			
		case 'hide':
			$p = new TPostIt;
			if($p->load($PDOdb, GETPOST('id'))) {
				$p->hidden = 1;
				$p->save($PDOdb);
				
				echo ok;
			}
			else{
				echo 'ko';
			}
			break;
			
		case 'unhide':
			$sql = 'UPDATE '.MAIN_DB_PREFIX.'postit SET hidden=0';
			
			$PDOdb->Execute($sql);
			
			break;
		
		default:
			
			break;
	}
