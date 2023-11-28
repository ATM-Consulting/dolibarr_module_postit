<?php
/* Copyright (C) 2022 SuperAdmin
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * \file    postit/lib/postit.lib.php
 * \ingroup postit
 * \brief   Library files with common functions for PostIt
 */

/**
 * Prepare admin pages header
 *
 * @return array
 */
function postitAdminPrepareHead()
{
	global $langs, $conf;

	$langs->load("postit@postit");

	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/postit/admin/setup.php", 1);
	$head[$h][1] = $langs->trans("Settings");
	$head[$h][2] = 'settings';
	$h++;

	/*
	$head[$h][0] = dol_buildpath("/postit/admin/myobject_extrafields.php", 1);
	$head[$h][1] = $langs->trans("ExtraFields");
	$head[$h][2] = 'myobject_extrafields';
	$h++;
	*/

	$head[$h][0] = dol_buildpath("/postit/admin/about.php", 1);
	$head[$h][1] = $langs->trans("About");
	$head[$h][2] = 'about';
	$h++;

	// Show more tabs from modules
	// Entries must be declared in modules descriptor with line
	//$this->tabs = array(
	//	'entity:+tabname:Title:@postit:/postit/mypage.php?id=__ID__'
	//); // to add new tab
	//$this->tabs = array(
	//	'entity:-tabname:Title:@postit:/postit/mypage.php?id=__ID__'
	//); // to remove a tab
	complete_head_from_modules($conf, $langs, null, $head, $h, 'postit');

	return $head;
}

/**
 * @return void
 */
function removePostitFromMultiConf(){
	global $conf,$db;

	if (!empty(getDolGlobalString('MULTICOMPANY_EXTERNAL_MODULES_SHARING'))){
		$currentConf = json_decode(getDolGlobalString('MULTICOMPANY_EXTERNAL_MODULES_SHARING'));
		// verifier si pas déjà valoriré
		foreach ($currentConf as $key => $element){

			if($element->sharingelements->postit){
				unset( $currentConf[$key]);
				// on retire les configurations existantes
				dolibarr_del_const($db, 'MULTICOMPANY_EXTERNAL_MODULES_SHARING',  0);

				// sert pour l'affichage de la ligne de configuration du module dans multicompany
				dolibarr_del_const($db, 'MULTICOMPANY_POSTIT_SHARING_ENABLED');

				// on réecrit la conf existante sans notre module
				if (count($currentConf) > 0)
					dolibarr_set_const($db, 'MULTICOMPANY_EXTERNAL_MODULES_SHARING', json_encode(array($currentConf)), 'chaine', 0, '', 0);
				break;
			}
		}
	}

}


/**
 * @param $arr
 * @return bool
 */

function isModuleEntryExist($arr){
	$postitAllreadyIn = false;

	if (is_array($arr)){
		foreach ($arr as $element){
			if($element->sharingelements->postit){
				$postitAllreadyIn = true;
				break;
			}
		}
	}
	return $postitAllreadyIn;
}
