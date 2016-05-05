<?php
/*
 * Script créant et vérifiant que les champs requis s'ajoutent bien
 */

if(!defined('INC_FROM_DOLIBARR')) {
	define('INC_FROM_CRON_SCRIPT', true);

	require('../config.php');

}





dol_include_once('/postit/class/postit.class.php');

$PDOdb=new TPDOdb;

$o=new TPostIt($db);
$o->init_db_by_vars($PDOdb);

