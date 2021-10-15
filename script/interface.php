<?php

if (!defined('NOCSRFCHECK')) define('NOCSRFCHECK', 1);
if (!defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL', 1);

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
$id = (int)GETPOST('id');

$fk_object = (int)GETPOST('fk_object');
$type_object = GETPOST('type_object');

$title = GETPOST('title');
$comment = GETPOST('comment');

$serializeOnlyTheseFields = array(
	'rightToDelete',
	'rightToSetStatus',
	'rightEdit',
	'author',
	'id',
	'status',
	'date_creation',
	'tms',
	'position_top',
	'position_left',
	'position_width',
	'position_height',
	'type_object',
	'comment',
	'title',
	'color',
	'to_delete',
	'rightResponse',
);

switch ($get) {
	case 'postit-of-object':

		$Tab = PostIt::getPostit($fk_object, $type_object, $user->id);
		foreach ($Tab as &$p) {

			$p->rightResponse = ($p->fk_user == $user->id) ? 0 : 1;

			if ($user->rights->postit->allaction->write || ($user->rights->postit->myaction->write && $p->fk_user == $user->id)) {
				$p->rightToDelete = 1;
				$p->rightToSetStatus = 1;
				$p->rightEdit = 1;
			} else {
				$p->rightToDelete = 0;
				$p->rightToSetStatus = 0;
				$p->rightEdit = 0;
			}

			${'u' . $p->fk_user} = new User($db);
			${'u' . $p->fk_user}->fetch($p->fk_user);
			$p->author = ${'u' . $p->fk_user}->getFullName($langs);

			// Petit bout de code pour éviter de balancer en ajax TOUS LES CHAMPS du postit, y compris $db.
			// Je n'aime faire la substitution de cette manière, mais ça évite de réécrire toute la boucle
			$newP = array();
			foreach ($serializeOnlyTheseFields as $fieldName) {
				$newP[$fieldName] = $p->{$fieldName};
			}
			$p = (object)$newP;
		}

		__out($Tab, 'json');

		break;

	case 'postit':

		$p = new PostIt($db);

		if ($p->fetch($id) > 0) {
			$newP = array();
			foreach ($serializeOnlyTheseFields as $fieldName) {
				$newP[$fieldName] = $p->{$fieldName};
			}
			__out($newP, 'json');
		}

		break;

	default:

		break;
}

switch ($put) {
	case 'postit':

		$p = new PostIt($db);
		if ($id == 0 || $p->fetch($id) <= 0) {
			$p->fk_object = $fk_object;
			$p->type_object = $type_object;
			$p->fk_user = $user->id;

			if ($fk_postit > 0) {
				$parent = new PostIt($db);
				if ($parent->fetch($fk_postit)) {
					$p->fk_postit = $fk_postit;
					$p->title = $langs->trans('NewResponse');
					$p->comment = '';

					$p->position_top = $parent->position_top + 30;
					$p->position_left = $parent->position_left + 30;
				}
			}

		}

		$updateAuthorTMS = true;

		// if the actual content of the note has not changed, we don't update the TMS and author information
		// (this might mean we only moved the note to a different position on screen for instance)
		if (!GETPOSTISSET('comment') && !GETPOSTISSET('title')) {
			$updateAuthorTMS = false;
		}

		if (!empty($width)) $p->position_width = $width;
		if (!empty($height)) $p->position_height = $height;
		if (!empty($top)) $p->position_top = $top;
		if (!empty($left)) $p->position_left = $left;
		if (!empty($title)) $p->title = $title;
		if (!empty($comment)) $p->comment = $comment;

		$author = $user;
		if ($updateAuthorTMS) {
			$p->tms = dol_now();
			$p->author = $user->getFullName($langs);
			$p->fk_user = $user->id;
		} elseif ($p->fk_user > 0) {
			$author = new User($db);
			$author->fetch($p->fk_user);
		}
		$res = $p->id > 0 ? $p->update($author) : $p->create($author);
		if ($res <= 0) {
			__out(array('errors' => $p->errors, 'error' => $p->error/*, 'dberror' => $db->lasterror()*/));
			exit;
		}

		if ($user->rights->postit->allaction->write || ($user->rights->postit->myaction->write && $p->fk_user == $user->id)) {
			$p->rightToDelete = 1;
			$p->rightToSetStatus = 1;
			$p->rightEdit = 1;
		} else {
			$p->rightToDelete = 0;
			$p->rightToSetStatus = 0;
			$p->rightEdit = 0;
		}

		$newP = array();
		foreach ($serializeOnlyTheseFields as $fieldName) {
			$newP[$fieldName] = $p->{$fieldName};
		}
		__out($newP, 'json');

		break;

	case 'delete':
		$p = new PostIt($db);
		if ($p->fetch(GETPOST('id'))) {
			$p->delete($user);
			echo 'ok';
		} else {
			echo 'ko';
		}


		break;

	case 'change-status':

		$p = new PostIt($db);
		if ($p->fetch(GETPOST('id'))) {
			$current = trim(GETPOST('current'));

			if ($p->fk_object == -1) {
				if ($current == 'private') $p->status = 'public';
				else $p->status = 'private';
			} else {
				if ($current == 'private') $p->status = 'public';
				else if ($current == 'public') $p->status = 'shared';
				else $p->status = 'private';

			}

			$p->update($user);

			echo trim($p->status);

		} else {
			echo 'ko';
		}

		break;

	default:

		break;
}
