<?php
/* Copyright (C) 2004-2017 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2022 SuperAdmin
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
 * \file    postit/admin/setup.php
 * \ingroup postit
 * \brief   PostIt setup page.
 */

// Load Dolibarr environment
$res = 0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (!$res && !empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) {
	$res = @include $_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php";
}
// Try main.inc.php into web root detected using web root calculated from SCRIPT_FILENAME
$tmp = empty($_SERVER['SCRIPT_FILENAME']) ? '' : $_SERVER['SCRIPT_FILENAME']; $tmp2 = realpath(__FILE__); $i = strlen($tmp) - 1; $j = strlen($tmp2) - 1;
while ($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i] == $tmp2[$j]) {
	$i--; $j--;
}
if (!$res && $i > 0 && file_exists(substr($tmp, 0, ($i + 1))."/main.inc.php")) {
	$res = @include substr($tmp, 0, ($i + 1))."/main.inc.php";
}
if (!$res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i + 1)))."/main.inc.php")) {
	$res = @include dirname(substr($tmp, 0, ($i + 1)))."/main.inc.php";
}
// Try main.inc.php using relative path
if (!$res && file_exists("../../main.inc.php")) {
	$res = @include "../../main.inc.php";
}
if (!$res && file_exists("../../../main.inc.php")) {
	$res = @include "../../../main.inc.php";
}
if (!$res) {
	die("Include of main fails");
}

global $langs, $user;

// Libraries
require_once DOL_DOCUMENT_ROOT."/core/lib/admin.lib.php";
require_once '../lib/postit.lib.php';
dol_include_once('postit/class/postit.class.php');

// Libraries
require_once DOL_DOCUMENT_ROOT . "/core/lib/admin.lib.php";

// Translations
$langs->loadLangs(array("admin", "postit@postit"));

// Initialize technical object to manage hooks of page. Note that conf->hooks_modules contains array of hook context
$hookmanager->initHooks(array('postitsetup', 'globalsetup'));

// Access control
if (!$user->admin) {
	accessforbidden();
}

// Parameters
$action = GETPOST('action', 'aZ09');
$backtopage = GETPOST('backtopage', 'alpha');
$modulepart = GETPOST('modulepart', 'aZ09');	// Used by actions_setmoduleoptions.inc.php

$value = GETPOST('value', 'alpha');
$label = GETPOST('label', 'alpha');
$scandir = GETPOST('scan_dir', 'alpha');
$type = 'myobject';

$error = 0;
$setupnotempty = 0;

// Set this to 1 to use the factory to manage constants. Warning, the generated module will be compatible with version v15+ only
$useFormSetup = 1;

if (!class_exists('FormSetup')) {
	// For retrocompatibility Dolibarr < 16.0
	if (floatval(DOL_VERSION) < 16.0 && !class_exists('FormSetup')) {
		require_once __DIR__.'/../backport/v16/core/class/html.formsetup.class.php';
	} else {
		require_once DOL_DOCUMENT_ROOT.'/core/class/html.formsetup.class.php';
	}
}

$formSetup = new FormSetup($db);

// Setup conf POSTIT_COLOR_PRIVATE
if(empty($conf->global->POSTIT_COLOR_PRIVATE)){ $conf->global->POSTIT_COLOR_PRIVATE = PostIt::getcolor('private'); }
$item = $formSetup->newItem('POSTIT_COLOR_PRIVATE');
$item->setAsColor();
$item->defaultFieldValue = '#FF0000';
$item->nameText = $item->getNameText();
$item->fieldInputOverride = '';

// Setup conf POSTIT_COLOR_PUBLIC
if(empty($conf->global->POSTIT_COLOR_PUBLIC)){ $conf->global->POSTIT_COLOR_PUBLIC = PostIt::getcolor('public'); }
$item = $formSetup->newItem('POSTIT_COLOR_PUBLIC');
$item->setAsColor();
$item->defaultFieldValue = '#FF0000';
$item->nameText = $item->getNameText();
$item->fieldInputOverride = '';

// Setup conf POSTIT_COLOR_SHARED
if(empty($conf->global->POSTIT_COLOR_SHARED)){ $conf->global->POSTIT_COLOR_SHARED = PostIt::getcolor('shared'); }
$item = $formSetup->newItem('POSTIT_COLOR_SHARED');
$item->setAsColor();
$item->defaultFieldValue = '#FF0000';
$item->nameText = $item->getNameText();
$item->fieldInputOverride = '';


// Setup conf POSTIT_MULTICOMPANY_SHARED
//var_dump($conf->global->POSTIT_MULTICOMPANY_SHARED);
if ($conf->multicompany->enabled){
	$itemMC = $formSetup->newItem('POSTIT_MULTICOMPANY_SHARED')->setAsSelect(array(0 => $langs->transnoentities('No'), 1 => $langs->transnoentities('Yes')));
	$itemMC->defaultFieldValue = 0;
	$itemMC->nameText = $itemMC->getNameText();
	$itemMC->fieldInputOverride = '';
	$itemMC->entity = 0;
}

/**
 * simulation d'object instancié par un autre module
 */
/*$testElement = array();
$testElement['addzero'] = 'user';
$testElement['sharingelements'] = array('LEAVEIT' => array(
	'type' => 'element',
	'icon' => 'building',
	'active' => true,  // for setEntity() function
));*/

//echo '<pre>' . var_export(json_encode($allreadySet), true) . '</pre>';exit;
//var_dump(json_encode($allreadySet));exit;
//dolibarr_del_const($db, 'MULTICOMPANY_EXTERNAL_MODULES_SHARING',  0);
//dolibarr_set_const($db, 'MULTICOMPANY_EXTERNAL_MODULES_SHARING', json_encode(array($testElement)), 'chaine', 0, '', 0);
/**
 * FIN simulation d'object instancié par un autre module
 */



/**
 * On donne la possibilité au module multi-company de gerer le partage entre entité des post-its
 * si cette optio n'est pas activée le partage par entités n'est pas actif.
 */
// le module multicompany est present et activé
// la conf postit POSTIT_MULTICOMPANY_SHARED est activé
//var_dump($conf->global->POSTIT_MULTICOMPANY_SHARED);
//var_dump($conf->global->MULTICOMPANY_EXTERNAL_MODULES_SHARING);

$currentConf  = json_decode($conf->global->MULTICOMPANY_EXTERNAL_MODULES_SHARING);
// conf à zero ou inexistante on supprime le module de la conf multiCompany
$multi = GETPOST('POSTIT_MULTICOMPANY_SHARED','int');
if ( $action == 'update' ){
	if ( empty($multi) || $multi == 0){
		removePostitFromMultiConf();

	}else{

		//prise en compte de multicompany pour les postit
		$postitMulticonpany = array();
		$postitMulticonpany['addzero'] = 'user';
		$postitMulticonpany['sharingelements'] = array('postit' => array(
			'type' => 'element',
			'icon' => 'building',
			'active' => true,
		));
		$postitMulticonpany['sharingmodulename'] = array('postit' => 'postit');
		// si le module n'est pas present dans la conf multiCompany
		if (!isModuleEntryExist($currentConf)){
			$currentConf[] = $postitMulticonpany;
			// on réecrit la conf
			dolibarr_set_const($db, 'MULTICOMPANY_EXTERNAL_MODULES_SHARING', json_encode($currentConf), 'chaine', 0, '', 0);
			dolibarr_set_const($db, 'MULTICOMPANY_POSTIT_SHARING_ENABLED', 1, 'chaine', 0, '', 0);
			setEventMessages($langs->trans('FeatureAddedToMultiCompany'),[],'warnings');
		}
	}
}




$setupnotempty =+ count($formSetup->items);


$dirmodels = array_merge(array('/'), (array) $conf->modules_parts['models']);


/*
 * Actions
 */

// For retrocompatibility Dolibarr < 15.0
if ( versioncompare(explode('.', DOL_VERSION), array(15)) < 0 && $action == 'update' && !empty($user->admin)) {
	$formSetup->saveConfFromPost();
}



include DOL_DOCUMENT_ROOT.'/core/actions_setmoduleoptions.inc.php';


/*
 * View
 */

$form = new Form($db);

$help_url = '';
$page_name = "PostItSetup";

llxHeader('', $langs->trans($page_name), $help_url);

// Subheader
$linkback = '<a href="'.($backtopage ? $backtopage : DOL_URL_ROOT.'/admin/modules.php?restore_lastsearch_values=1').'">'.$langs->trans("BackToModuleList").'</a>';

print load_fiche_titre($langs->trans($page_name), $linkback, 'title_setup');

// Configuration header
$head = postitAdminPrepareHead();
print dol_get_fiche_head($head, 'settings', $langs->trans($page_name), -1, "postit@postit");

// Setup page goes here
if ($action == 'edit') {
	print $formSetup->generateOutput(true);
	print '<br>';
} elseif (!empty($formSetup->items)) {
	print $formSetup->generateOutput();
	print '<div class="tabsAction">';
	print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?action=edit&token='.newToken().'">'.$langs->trans("Modify").'</a>';
	print '</div>';
} else {
	print '<br>'.$langs->trans("NothingToSetup");
}

if (empty($setupnotempty)) {
	print '<br>'.$langs->trans("NothingToSetup");
}

// Page end
print dol_get_fiche_end();

llxFooter();
$db->close();

