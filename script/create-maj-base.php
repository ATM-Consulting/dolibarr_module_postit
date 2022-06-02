<?php
/*
 * Script créant et vérifiant que les champs requis s'ajoutent bien
 */

if(!defined('INC_FROM_DOLIBARR')) {
	define('INC_FROM_CRON_SCRIPT', true);
}

dol_include_once('/postit/class/postit.class.php');

global $db;

$db->query("ALTER TABLE ".MAIN_DB_PREFIX."postit CHANGE rowid rowid int(11) NOT NULL AUTO_INCREMENT FIRST");

$db->query("UPDATE".MAIN_DB_PREFIX."postit SET date_creation=date_cre,tms=date_maj WHERE date_creation IS NULL");
