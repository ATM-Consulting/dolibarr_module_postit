<?php
/* Copyright (C) 2004-2018  Laurent Destailleur     <eldy@users.sourceforge.net>
 * Copyright (C) 2018-2019  Nicolas ZABOURI         <info@inovea-conseil.com>
 * Copyright (C) 2019-2020  Frédéric France         <frederic.france@netlogic.fr>
 * Copyright (C) 2022 SuperAdmin
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * 	\defgroup   postit     Module PostIt
 *  \brief      PostIt module descriptor.
 *
 *  \file       htdocs/postit/core/modules/modPostIt.class.php
 *  \ingroup    postit
 *  \brief      Description and activation file for module PostIt
 */
include_once DOL_DOCUMENT_ROOT.'/core/modules/DolibarrModules.class.php';

/**
 *  Description and activation class for module PostIt
 */
class modPostIt extends DolibarrModules
{
	/**
	 * Constructor. Define names, constants, directories, boxes, permissions
	 *
	 * @param DoliDB $db Database handler
	 */
	public function __construct($db)
	{
		global $langs, $conf;
		$this->db = $db;

		// Id for module (must be unique).
		// Use here a free id (See in Home -> System information -> Dolibarr for list of used modules id).
		$this->numero = 104852; // TODO Go on page https://wiki.dolibarr.org/index.php/List_of_modules_id to reserve an id number for your module

		// Key text used to identify module (for permissions, menus, etc...)
		$this->rights_class = 'postit';

		// Family can be 'base' (core modules),'crm','financial','hr','projects','products','ecm','technic' (transverse modules),'interface' (link with external tools),'other','...'
		// It is used to group modules by family in module setup page
		$this->family = "other";

		// Module position in the family on 2 digits ('01', '10', '20', ...)
		$this->module_position = '90';

		// Gives the possibility for the module, to provide his own family info and position of this family (Overwrite $this->family and $this->module_position. Avoid this)
		//$this->familyinfo = array('myownfamily' => array('position' => '01', 'label' => $langs->trans("MyOwnFamily")));
		// Module label (no space allowed), used if translation string 'ModulePostItName' not found (PostIt is name of module).
		$this->name = preg_replace('/^mod/i', '', get_class($this));

		// Module description, used if translation string 'ModulePostItDesc' not found (PostIt is name of module).
		$this->description = "Permet d'ajouter sur les fiches Dolibarr des notes repositionnables, privées ou publiques, qui attirent l'œil";
		// Used only if file README.md and README-LL.md not found.
		$this->descriptionlong = "PostItDescription";

		// Author
		$this->editor_name = 'ATM Consulting';
		$this->editor_url = 'https://www.atm-consulting.fr';

		// Possible values for version are: 'development', 'experimental', 'dolibarr', 'dolibarr_deprecated' or a version string like 'x.y.z'

		$this->version = '2.4.3';

		// Url to the file with your last numberversion of this module
		require_once __DIR__ . '/../../class/techatm.class.php';
		$this->url_last_version = \postit\TechATM::getLastModuleVersionUrl($this);

		// Key used in llx_const table to save module status enabled/disabled (where POSTIT is value of property name of module in uppercase)
		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);

		// Name of image file used for this module.
		// If file is in theme/yourtheme/img directory under name object_pictovalue.png, use this->picto='pictovalue'
		// If file is in module/img directory under name object_pictovalue.png, use this->picto='pictovalue@module'
		// To use a supported fa-xxx css style of font awesome, use this->picto='xxx'
		$this->picto = 'module.svg@postit';

		// Define some features supported by module (triggers, login, substitutions, menus, css, etc...)
		$this->module_parts = array(
			'css' => array(
				'/postit/css/postit.css.php'
			),
			// Set this to relative path of js file if module must load a js on all pages
			'js' => array(
				'/postit/lib/jquery.editable.min.js'
			),
			// Set here all hooks context managed by module. To find available hook context, make a "grep -r '>initHooks(' *" on source code. You can also set hook context to 'all'
			'hooks' => array(
				'index',
				'globalcard',
				'main'
			)
		);

		// Data directories to create when module is enabled.
		// Example: this->dirs = array("/postit/temp","/postit/subdir");
		$this->dirs = array();

		// Config pages. Put here list of php page, stored into postit/admin directory, to use to setup module.
		$this->config_page_url = array("setup.php@postit");

		// Dependencies
		// A condition to hide module
		$this->hidden = false;
		// List of module class names as string that must be enabled if this module is enabled. Example: array('always1'=>'modModuleToEnable1','always2'=>'modModuleToEnable2', 'FR1'=>'modModuleToEnableFR'...)
		$this->depends = array();
		$this->requiredby = array(); // List of module class names as string to disable if this one is disabled. Example: array('modModuleToDisable1', ...)
		$this->conflictwith = array(); // List of module class names as string this module is in conflict with. Example: array('modModuleToDisable1', ...)

		// The language file dedicated to your module
		$this->langfiles = array("postit@postit");

		// Prerequisites
		$this->phpmin = array(7,0); // Minimum version of PHP required by module
		$this->need_dolibarr_version = array(16,0); // Minimum version of Dolibarr required by module

		// Messages at activation
		$this->warnings_activation = array(); // Warning to show when we activate module. array('always'='text') or array('FR'='textfr','MX'='textmx'...)
		$this->warnings_activation_ext = array(); // Warning to show when we activate an external module. array('always'='text') or array('FR'='textfr','MX'='textmx'...)
		//$this->automatic_activation = array('FR'=>'PostItWasAutomaticallyActivatedBecauseOfYourCountryChoice');
		//$this->always_enabled = true;								// If true, can't be disabled

		// Constants
		// List of particular constants to add when module is enabled (key, 'chaine', value, desc, visible, 'current' or 'allentities', deleteonunactive)
		// Example: $this->const=array(1 => array('POSTIT_MYNEWCONST1', 'chaine', 'myvalue', 'This is a constant to add', 1),
		//                             2 => array('POSTIT_MYNEWCONST2', 'chaine', 'myvalue', 'This is another constant to add', 0, 'current', 1)
		// );
		$this->const = array();

		// Some keys to add into the overwriting translation tables
		/*$this->overwrite_translation = array(
			'en_US:ParentCompany'=>'Parent company or reseller',
			'fr_FR:ParentCompany'=>'Maison mère ou revendeur'
		)*/

		if (!isset($conf->postit) || !isModEnabled('postit')) {
			$conf->postit = new stdClass();
			$conf->postit->enabled = 0;
		}

		// Array to add new pages in new tabs
		$this->tabs = array(
			'user:+postit:Postit:postit@postit:$user->hasRight("postit","myaction","read"):/postit/list.php?id=__ID__'
		);
		// Example:
		// $this->tabs[] = array('data'=>'objecttype:+tabname1:Title1:mylangfile@postit:$user->rights->postit->read:/postit/mynewtab1.php?id=__ID__');  					// To add a new tab identified by code tabname1
		// $this->tabs[] = array('data'=>'objecttype:+tabname2:SUBSTITUTION_Title2:mylangfile@postit:$user->rights->othermodule->read:/postit/mynewtab2.php?id=__ID__',  	// To add another new tab identified by code tabname2. Label will be result of calling all substitution functions on 'Title2' key.
		// $this->tabs[] = array('data'=>'objecttype:-tabname:NU:conditiontoremove');                                                     										// To remove an existing tab identified by code tabname
		//
		// Where objecttype can be
		// 'categories_x'	  to add a tab in category view (replace 'x' by type of category (0=product, 1=supplier, 2=customer, 3=member)
		// 'contact'          to add a tab in contact view
		// 'contract'         to add a tab in contract view
		// 'group'            to add a tab in group view
		// 'intervention'     to add a tab in intervention view
		// 'invoice'          to add a tab in customer invoice view
		// 'invoice_supplier' to add a tab in supplier invoice view
		// 'member'           to add a tab in fundation member view
		// 'opensurveypoll'	  to add a tab in opensurvey poll view
		// 'order'            to add a tab in customer order view
		// 'order_supplier'   to add a tab in supplier order view
		// 'payment'		  to add a tab in payment view
		// 'payment_supplier' to add a tab in supplier payment view
		// 'product'          to add a tab in product view
		// 'propal'           to add a tab in propal view
		// 'project'          to add a tab in project view
		// 'stock'            to add a tab in stock view
		// 'thirdparty'       to add a tab in third party view
		// 'user'             to add a tab in user view

		// Dictionaries
		$this->dictionaries = array();
		/* Example:
		$this->dictionaries=array(
			'langs'=>'postit@postit',
			// List of tables we want to see into dictonnary editor
			'tabname'=>array(MAIN_DB_PREFIX."table1", MAIN_DB_PREFIX."table2", MAIN_DB_PREFIX."table3"),
			// Label of tables
			'tablib'=>array("Table1", "Table2", "Table3"),
			// Request to select fields
			'tabsql'=>array('SELECT f.rowid as rowid, f.code, f.label, f.active FROM '.MAIN_DB_PREFIX.'table1 as f', 'SELECT f.rowid as rowid, f.code, f.label, f.active FROM '.MAIN_DB_PREFIX.'table2 as f', 'SELECT f.rowid as rowid, f.code, f.label, f.active FROM '.MAIN_DB_PREFIX.'table3 as f'),
			// Sort order
			'tabsqlsort'=>array("label ASC", "label ASC", "label ASC"),
			// List of fields (result of select to show dictionary)
			'tabfield'=>array("code,label", "code,label", "code,label"),
			// List of fields (list of fields to edit a record)
			'tabfieldvalue'=>array("code,label", "code,label", "code,label"),
			// List of fields (list of fields for insert)
			'tabfieldinsert'=>array("code,label", "code,label", "code,label"),
			// Name of columns with primary key (try to always name it 'rowid')
			'tabrowid'=>array("rowid", "rowid", "rowid"),
			// Condition to show each dictionary
			'tabcond'=>array(isModEnabled("postit"), isModEnabled("postit"), isModEnabled("postit"))
			// Help tooltip for each fields of the dictionary
			'tabhelp'=>array(array('code'=>$langs->trans('CodeTooltipHelp')))
		);
		*/

		// Boxes/Widgets
		// Add here list of php file(s) stored in postit/core/boxes that contains a class to show a widget.
		$this->boxes = array(
			//  0 => array(
			//      'file' => 'postitwidget1.php@postit',
			//      'note' => 'Widget provided by PostIt',
			//      'enabledbydefaulton' => 'Home',
			//  ),
			//  ...
		);

		// Cronjobs (List of cron jobs entries to add when module is enabled)
		// unit_frequency must be 60 for minute, 3600 for hour, 86400 for day, 604800 for week
		$this->cronjobs = array(
			//  0 => array(
			//      'label' => 'MyJob label',
			//      'jobtype' => 'method',
			//      'class' => '/postit/class/postit.class.php',
			//      'objectname' => 'PostIt',
			//      'method' => 'doScheduledJob',
			//      'parameters' => '',
			//      'comment' => 'Comment',
			//      'frequency' => 2,
			//      'unitfrequency' => 3600,
			//      'status' => 0,
			//      'test' => 'isModEnabled("postit")',
			//      'priority' => 50,
			//  ),
		);
		// Example: $this->cronjobs=array(
		//    0=>array('label'=>'My label', 'jobtype'=>'method', 'class'=>'/dir/class/file.class.php', 'objectname'=>'MyClass', 'method'=>'myMethod', 'parameters'=>'param1, param2', 'comment'=>'Comment', 'frequency'=>2, 'unitfrequency'=>3600, 'status'=>0, 'test'=>'$conf->postit->enabled', 'priority'=>50),
		//    1=>array('label'=>'My label', 'jobtype'=>'command', 'command'=>'', 'parameters'=>'param1, param2', 'comment'=>'Comment', 'frequency'=>1, 'unitfrequency'=>3600*24, 'status'=>0, 'test'=>'$conf->postit->enabled', 'priority'=>50)
		// );

		// Permissions provided by this module
		$this->rights = array();
		$r = 0;
		// Add here entries to declare new permissions
		/* BEGIN MODULEBUILDER PERMISSIONS */
		$this->rights[$r][0] = $this->numero . sprintf("%02d", $r + 1); // Permission id (must not be already used)
		$this->rights[$r][1] = 'Read'; // Permission label
		$this->rights[$r][3] = 0; 					// Permission by default for new user (0/1)
		$this->rights[$r][4] = 'myaction';
		$this->rights[$r][5] = 'read'; // In php code, permission will be checked by test if ($user->hasRight("postit", "postit", "read"))
		$r++;
		$this->rights[$r][0] = $this->numero . sprintf("%02d", $r + 1); // Permission id (must not be already used)
		$this->rights[$r][1] = 'Write'; // Permission label
		$this->rights[$r][3] = 0; 					// Permission by default for new user (0/1)
		$this->rights[$r][4] = 'myaction';
		$this->rights[$r][5] = 'write'; // In php code, permission will be checked by test if ($user->hasRight("postit", "postit", "write"))
		$r++;
		$this->rights[$r][0] = $this->numero . sprintf("%02d", $r + 1); // Permission id (must not be already used)
		$this->rights[$r][1] = 'Admin'; // Permission label
		$this->rights[$r][3] = 0; 					// Permission by default for new user (0/1)
		$this->rights[$r][4] = 'allaction';
		$this->rights[$r][5] = 'write'; // In php code, permission will be checked by test if ($user->hasRight("postit", "postit", "delete"))
		$r++;
		/* END MODULEBUILDER PERMISSIONS */

		// Main menu entries to add
		$this->menu = array();
		$r = 0;
		// Add here entries to declare new menus
		/* BEGIN MODULEBUILDER TOPMENU */
//		$this->menu[$r++] = array(
//			'fk_menu'=>'', // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
//			'type'=>'top', // This is a Top menu entry
//			'titre'=>'ModulePostItName',
//			'prefix' => img_picto('', $this->picto, 'class="paddingright pictofixedwidth valignmiddle"'),
//			'mainmenu'=>'postit',
//			'leftmenu'=>'',
//			'url'=>'/postit/postitindex.php',
//			'langs'=>'postit@postit', // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
//			'position'=>1000 + $r,
//			'enabled'=>'isModEnabled("postit")', // Define condition to show or hide menu entry. Use 'isModEnabled("postit")' if entry must be visible if module is enabled.
//			'perms'=>'1', // Use 'perms'=>'$user->hasRight("postit", "postit", "read")' if you want your menu with a permission rules
//			'target'=>'',
//			'user'=>2, // 0=Menu for internal users, 1=external users, 2=both
//		);
		/* END MODULEBUILDER TOPMENU */
		/* BEGIN MODULEBUILDER LEFTMENU POSTIT
		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=postit',      // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',                          // This is a Left menu entry
			'titre'=>'PostIt',
			'prefix' => img_picto('', $this->picto, 'class="paddingright pictofixedwidth valignmiddle"'),
			'mainmenu'=>'postit',
			'leftmenu'=>'postit',
			'url'=>'/postit/postitindex.php',
			'langs'=>'postit@postit',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'isModEnabled("postit")',  // Define condition to show or hide menu entry. Use 'isModEnabled("postit")' if entry must be visible if module is enabled.
			'perms'=>'$user->rights->postit->postit->read',			                // Use 'perms'=>'$user->hasRight("postit", "level1", "level2")' if you want your menu with a permission rules
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
		);
		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=postit,fk_leftmenu=postit',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>'List_PostIt',
			'mainmenu'=>'postit',
			'leftmenu'=>'postit_postit_list',
			'url'=>'/postit/postit_list.php',
			'langs'=>'postit@postit',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$conf->postit->enabled',  // Define condition to show or hide menu entry. Use '$conf->postit->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$user->rights->postit->postit->read',			                // Use 'perms'=>'$user->hasRight("postit", "level1", "level2")' if you want your menu with a permission rules
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
		);
		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=postit,fk_leftmenu=postit',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>'New_PostIt',
			'mainmenu'=>'postit',
			'leftmenu'=>'postit_postit_new',
			'url'=>'/postit/postit_card.php?action=create',
			'langs'=>'postit@postit',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$conf->postit->enabled',  // Define condition to show or hide menu entry. Use '$conf->postit->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$user->rights->postit->postit->write',			                // Use 'perms'=>'$user->hasRight("postit", "level1", "level2")' if you want your menu with a permission rules
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
		);
		*/

//        $this->menu[$r++]=array(
//            // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
//            'fk_menu'=>'fk_mainmenu=postit',
//            // This is a Left menu entry
//            'type'=>'left',
//            'titre'=>'List PostIt',
//            'mainmenu'=>'postit',
//            'leftmenu'=>'postit_postit',
//            'url'=>'/postit/postit_list.php',
//            // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
//            'langs'=>'postit@postit',
//            'position'=>1100+$r,
//            // Define condition to show or hide menu entry. Use '$conf->postit->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
//            'enabled'=>'isModEnabled("postit")',
//            // Use 'perms'=>'$user->hasRight("postit", "level1", "level2")' if you want your menu with a permission rules
//            'perms'=>'1',
//            'target'=>'',
//            // 0=Menu for internal users, 1=external users, 2=both
//            'user'=>2,
//        );
//        $this->menu[$r++]=array(
//            // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
//            'fk_menu'=>'fk_mainmenu=postit,fk_leftmenu=postit_postit',
//            // This is a Left menu entry
//            'type'=>'left',
//            'titre'=>'New PostIt',
//            'mainmenu'=>'postit',
//            'leftmenu'=>'postit_postit',
//            'url'=>'/postit/postit_card.php?action=create',
//            // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
//            'langs'=>'postit@postit',
//            'position'=>1100+$r,
//            // Define condition to show or hide menu entry. Use '$conf->postit->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
//            'enabled'=>'isModEnabled("postit")',
//            // Use 'perms'=>'$user->hasRight("postit", "level1", "level2")' if you want your menu with a permission rules
//            'perms'=>'1',
//            'target'=>'',
//            // 0=Menu for internal users, 1=external users, 2=both
//            'user'=>2
//        );

		/* END MODULEBUILDER LEFTMENU POSTIT */
		// Exports profiles provided by this module
		$r = 1;
		/* BEGIN MODULEBUILDER EXPORT POSTIT */
		/*
		$langs->load("postit@postit");
		$this->export_code[$r]=$this->rights_class.'_'.$r;
		$this->export_label[$r]='PostItLines';	// Translation key (used only if key ExportDataset_xxx_z not found)
		$this->export_icon[$r]='postit@postit';
		// Define $this->export_fields_array, $this->export_TypeFields_array and $this->export_entities_array
		$keyforclass = 'PostIt'; $keyforclassfile='/postit/class/postit.class.php'; $keyforelement='postit@postit';
		include DOL_DOCUMENT_ROOT.'/core/commonfieldsinexport.inc.php';
		//$this->export_fields_array[$r]['t.fieldtoadd']='FieldToAdd'; $this->export_TypeFields_array[$r]['t.fieldtoadd']='Text';
		//unset($this->export_fields_array[$r]['t.fieldtoremove']);
		//$keyforclass = 'PostItLine'; $keyforclassfile='/postit/class/postit.class.php'; $keyforelement='postitline@postit'; $keyforalias='tl';
		//include DOL_DOCUMENT_ROOT.'/core/commonfieldsinexport.inc.php';
		$keyforselect='postit'; $keyforaliasextra='extra'; $keyforelement='postit@postit';
		include DOL_DOCUMENT_ROOT.'/core/extrafieldsinexport.inc.php';
		//$keyforselect='postitline'; $keyforaliasextra='extraline'; $keyforelement='postitline@postit';
		//include DOL_DOCUMENT_ROOT.'/core/extrafieldsinexport.inc.php';
		//$this->export_dependencies_array[$r] = array('postitline'=>array('tl.rowid','tl.ref')); // To force to activate one or several fields if we select some fields that need same (like to select a unique key if we ask a field of a child to avoid the DISTINCT to discard them, or for computed field than need several other fields)
		//$this->export_special_array[$r] = array('t.field'=>'...');
		//$this->export_examplevalues_array[$r] = array('t.field'=>'Example');
		//$this->export_help_array[$r] = array('t.field'=>'FieldDescHelp');
		$this->export_sql_start[$r]='SELECT DISTINCT ';
		$this->export_sql_end[$r]  =' FROM '.MAIN_DB_PREFIX.'postit as t';
		//$this->export_sql_end[$r]  =' LEFT JOIN '.MAIN_DB_PREFIX.'postit_line as tl ON tl.fk_postit = t.rowid';
		$this->export_sql_end[$r] .=' WHERE 1 = 1';
		$this->export_sql_end[$r] .=' AND t.entity IN ('.getEntity('postit').')';
		$r++; */
		/* END MODULEBUILDER EXPORT POSTIT */

		// Imports profiles provided by this module
		$r = 1;
		/* BEGIN MODULEBUILDER IMPORT POSTIT */
		/*
		$langs->load("postit@postit");
		$this->import_code[$r]=$this->rights_class.'_'.$r;
		$this->import_label[$r]='PostItLines';	// Translation key (used only if key ExportDataset_xxx_z not found)
		$this->import_icon[$r]='postit@postit';
		$this->import_tables_array[$r] = array('t' => MAIN_DB_PREFIX.'postit_postit', 'extra' => MAIN_DB_PREFIX.'postit_postit_extrafields');
		$this->import_tables_creator_array[$r] = array('t' => 'fk_user_author'); // Fields to store import user id
		$import_sample = array();
		$keyforclass = 'PostIt'; $keyforclassfile='/postit/class/postit.class.php'; $keyforelement='postit@postit';
		include DOL_DOCUMENT_ROOT.'/core/commonfieldsinimport.inc.php';
		$import_extrafield_sample = array();
		$keyforselect='postit'; $keyforaliasextra='extra'; $keyforelement='postit@postit';
		include DOL_DOCUMENT_ROOT.'/core/extrafieldsinimport.inc.php';
		$this->import_fieldshidden_array[$r] = array('extra.fk_object' => 'lastrowid-'.MAIN_DB_PREFIX.'postit_postit');
		$this->import_regex_array[$r] = array();
		$this->import_examplevalues_array[$r] = array_merge($import_sample, $import_extrafield_sample);
		$this->import_updatekeys_array[$r] = array('t.ref' => 'Ref');
		$this->import_convertvalue_array[$r] = array(
			't.ref' => array(
				'rule'=>'getrefifauto',
				'class'=>(empty($conf->global->POSTIT_POSTIT_ADDON) ? 'mod_postit_standard' : $conf->global->POSTIT_POSTIT_ADDON),
				'path'=>"/core/modules/commande/".(empty($conf->global->POSTIT_POSTIT_ADDON) ? 'mod_postit_standard' : $conf->global->POSTIT_POSTIT_ADDON).'.php'
				'classobject'=>'PostIt',
				'pathobject'=>'/postit/class/postit.class.php',
			),
			't.fk_soc' => array('rule' => 'fetchidfromref', 'file' => '/societe/class/societe.class.php', 'class' => 'Societe', 'method' => 'fetch', 'element' => 'ThirdParty'),
			't.fk_user_valid' => array('rule' => 'fetchidfromref', 'file' => '/user/class/user.class.php', 'class' => 'User', 'method' => 'fetch', 'element' => 'user'),
			't.fk_mode_reglement' => array('rule' => 'fetchidfromcodeorlabel', 'file' => '/compta/paiement/class/cpaiement.class.php', 'class' => 'Cpaiement', 'method' => 'fetch', 'element' => 'cpayment'),
		);
		$r++; */
		/* END MODULEBUILDER IMPORT POSTIT */
	}

	/**
	 *  Function called when module is enabled.
	 *  The init function add constants, boxes, permissions and menus (defined in constructor) into Dolibarr database.
	 *  It also creates data directories
	 *
	 *  @param      string  $options    Options when enabling module ('', 'noboxes')
	 *  @return     int             	1 if OK, 0 if KO
	 */
	public function init($options = '')
	{
		global $conf, $langs;


		$result = $this->_load_tables('/postit/sql/');
		if ($result < 0) {
			return -1; // Do not activate module if error 'not allowed' returned when loading module SQL queries (the _load_table run sql with run_sql with the error allowed parameter set to 'default')
		}

		// Create extrafields during init
		//include_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
		//$extrafields = new ExtraFields($this->db);
		//$result1=$extrafields->addExtraField('postit_myattr1', "New Attr 1 label", 'boolean', 1,  3, 'thirdparty',   0, 0, '', '', 1, '', 0, 0, '', '', 'postit@postit', 'isModEnabled("postit")');
		//$result2=$extrafields->addExtraField('postit_myattr2', "New Attr 2 label", 'varchar', 1, 10, 'project',      0, 0, '', '', 1, '', 0, 0, '', '', 'postit@postit', 'isModEnabled("postit")');
		//$result3=$extrafields->addExtraField('postit_myattr3', "New Attr 3 label", 'varchar', 1, 10, 'bank_account', 0, 0, '', '', 1, '', 0, 0, '', '', 'postit@postit', 'isModEnabled("postit")');
		//$result4=$extrafields->addExtraField('postit_myattr4', "New Attr 4 label", 'select',  1,  3, 'thirdparty',   0, 1, '', array('options'=>array('code1'=>'Val1','code2'=>'Val2','code3'=>'Val3')), 1,'', 0, 0, '', '', 'postit@postit', 'isModEnabled("postit")');
		//$result5=$extrafields->addExtraField('postit_myattr5', "New Attr 5 label", 'text',    1, 10, 'user',         0, 0, '', '', 1, '', 0, 0, '', '', 'postit@postit', 'isModEnabled("postit")');

		// Permissions
		$this->remove($options);

		$sql = array();

		return $this->_init($sql, $options);
	}

	/**
	 *  Function called when module is disabled.
	 *  Remove from database constants, boxes and permissions from Dolibarr database.
	 *  Data directories are not deleted
	 *
	 *  @param      string	$options    Options when enabling module ('', 'noboxes')
	 *  @return     int                 1 if OK, 0 if KO
	 */
	public function remove($options = '')
	{
		$sql = array();
		return $this->_remove($sql, $options);
	}
}
