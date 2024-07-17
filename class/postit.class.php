<?php
/* Copyright (C) 2017  Laurent Destailleur <eldy@users.sourceforge.net>
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
 * \file        class/postit.class.php
 * \ingroup     postit
 * \brief       This file is a CRUD class file for PostIt (Create/Read/Update/Delete)
 */

// Put here all includes required by your class file
require_once DOL_DOCUMENT_ROOT.'/core/class/commonobject.class.php';

/**
 * Class for PostIt
 */
class PostIt extends CommonObject
{
	/**
	 * @var string ID of module.
	 */
	public $module = 'postit';
	/**
	 * @var string ID to identify managed object.
	 */
	public $element = 'postit';

	public $entity;
	/**
	 * @var string Name of table without prefix where object is stored. This is also the key used for extrafields management.
	 */
	public $table_element = 'postit';
	/**
	 * @var int  Does this object support multicompany module ?
	 * 0=No test on entity, 1=Test with field entity, 'field@table'=Test with link by field@table
	 */
	public $ismultientitymanaged = 0;
	/**
	 * @var int  Does object support extrafields ? 0=No, 1=Yes
	 */
	public $isextrafieldmanaged = 0;
	/**
	 * @var string String with name of icon for postit. Must be the part after the 'object_' into object_postit.png
	 */
	public $picto = 'postit@postit';

	const STATUS_PUBLIC = 'public';
	const STATUS_SHARED = 'shared';

	/**
	 *  'type' field format ('integer', 'integer:ObjectClass:PathToClass[:AddCreateButtonOrNot[:Filter[:Sortfield]]]', 'sellist:TableName:LabelFieldName[:KeyFieldName[:KeyFieldParent[:Filter[:Sortfield]]]]', 'varchar(x)', 'double(24,8)', 'real', 'price', 'text', 'text:none', 'html', 'date', 'datetime', 'timestamp', 'duration', 'mail', 'phone', 'url', 'password')
	 *         Note: Filter can be a string like "(t.ref:like:'SO-%') or (t.date_creation:<:'20160101') or (t.nature:is:NULL)"
	 *  'label' the translation key.
	 *  'picto' is code of a picto to show before value in forms
	 *  'enabled' is a condition when the field must be managed (Example: 1 or '$conf->global->MY_SETUP_PARAM' or 'isModEnabled('multicurrency')' ...)
	 *  'position' is the sort order of field.
	 *  'notnull' is set to 1 if not null in database. Set to -1 if we must set data to null if empty ('' or 0).
	 *  'visible' says if field is visible in list (Examples: 0=Not visible, 1=Visible on list and create/update/view forms, 2=Visible on list only, 3=Visible on create/update/view form only (not list), 4=Visible on list and update/view form only (not create). 5=Visible on list and view only (not create/not update). Using a negative value means field is not shown by default on list but can be selected for viewing)
	 *  'noteditable' says if field is not editable (1 or 0)
	 *  'default' is a default value for creation (can still be overwrote by the Setup of Default Values if field is editable in creation form). Note: If default is set to '(PROV)' and field is 'ref', the default value will be set to '(PROVid)' where id is rowid when a new record is created.
	 *  'index' if we want an index in database.
	 *  'foreignkey'=>'tablename.field' if the field is a foreign key (it is recommanded to name the field fk_...).
	 *  'searchall' is 1 if we want to search in this field when making a search from the quick search button.
	 *  'isameasure' must be set to 1 or 2 if field can be used for measure. Field type must be summable like integer or double(24,8). Use 1 in most cases, or 2 if you don't want to see the column total into list (for example for percentage)
	 *  'css' and 'cssview' and 'csslist' is the CSS style to use on field. 'css' is used in creation and update. 'cssview' is used in view mode. 'csslist' is used for columns in lists. For example: 'css'=>'minwidth300 maxwidth500 widthcentpercentminusx', 'cssview'=>'wordbreak', 'csslist'=>'tdoverflowmax200'
	 *  'help' is a 'TranslationString' to use to show a tooltip on field. You can also use 'TranslationString:keyfortooltiponlick' for a tooltip on click.
	 *  'showoncombobox' if value of the field must be visible into the label of the combobox that list record
	 *  'disabled' is 1 if we want to have the field locked by a 'disabled' attribute. In most cases, this is never set into the definition of $fields into class, but is set dynamically by some part of code.
	 *  'arrayofkeyval' to set a list of values if type is a list of predefined values. For example: array("0"=>"Draft","1"=>"Active","-1"=>"Cancel"). Note that type can be 'integer' or 'varchar'
	 *  'autofocusoncreate' to have field having the focus on a create form. Only 1 field should have this property set to 1.
	 *  'comment' is not used. You can store here any text of your choice. It is not used by application.
	 *    'validate' is 1 if need to validate with $this->validateField()
	 *  'copytoclipboard' is 1 or 2 to allow to add a picto to copy value into clipboard (1=picto after label, 2=picto after value)
	 *
	 *  Note: To have value dynamic, you can set value to 0 in definition and edit the value on the fly into the constructor.
	 */

	// BEGIN MODULEBUILDER PROPERTIES
	/**
	 * @var array  Array with all fields and their property. Do not use it as a static var. It may be modified by constructor.
	 */
	public $fields = array(
		'rowid' => array('type' => 'integer', 'index' => true)
		, 'entity' =>array('type'=>'integer', 'label'=>'Entity', 'default'=>1, 'enabled'=>1, 'visible'=>-2, 'notnull'=>1, 'position'=>20, 'index'=>1)
		, 'date_creation' => array('type' => 'date')
		, 'tms' => array('type' => 'date')
		, 'fk_object' => array('type' => 'integer', 'index' => true, 'notnull' => 1)
		, 'fk_actioncomm' => array('type' => 'integer', 'index' => true, 'notnull' => 1)
		, 'fk_user' => array('type' => 'integer:User:user/class/user.class.php', 'index' => true, 'notnull' => 1, 'visible'=>1, 'enabled'=>1, 'label'=>'Author')
		, 'fk_user_todo' => array('type' => 'integer', 'index' => true, 'notnull' => 1)
		, 'fk_postit' => array('type' => 'integer', 'index' => true, 'notnull' => 1)
		, 'position_top' => array('type' => 'double', 'notnull' => 1)
		, 'position_left' => array('type' => 'double', 'notnull' => 1)
		, 'position_width' => array('type' => 'double', 'notnull' => 1)
		, 'position_height' => array('type' => 'double', 'notnull' => 1)
		, 'type_object' => array('type' => 'string', 'index' => true, 'length' => 50)
		, 'status' => array('type' => 'string', 'index' => true, 'length' => 50, 'visible'=>1, 'enabled'=>1, 'label'=>'Status')
		, 'comment' => array('type' => 'text', 'visible'=>1, 'enabled'=>1, 'label'=>'Comment')
		, 'title' => array('type' => 'string', 'visible'=>1, 'enabled'=>1, 'label'=>'Title')
		, 'color' => array('type' => 'string')
	);
	public $rowid;
	public $date_creation;
	public $tms;
	public $fk_object;
	public $fk_actioncomm;
	public $fk_user;
	public $fk_user_todo;
	public $fk_postit;
	public $position_top;
	public $position_left;
	public $position_width;
	public $position_height;
	public $type_object;
	public $status;
	public $comment;
	public $title;
	public $color;
	public $author;
	public $to_delete;
	public $rightResponse;
	// END MODULEBUILDER PROPERTIES

	/**
	 * Constructor
	 *
	 * @param DoliDb $db Database handler
	 */
	public function __construct(DoliDB $db) {
		global $conf, $langs;

		$this->db = $db;

		if (empty(getDolGlobalString('MAIN_SHOW_TECHNICAL_ID')) && isset($this->fields['rowid'])) {
			$this->fields['rowid']['visible'] = 0;
		}
		if (!isModEnabled('multicompany') && isset($this->fields['entity'])) {
			$this->fields['entity']['enabled'] = 0;
		}

		// Unset fields that are disabled
		foreach ($this->fields as $key => $val) {
			if (isset($val['enabled']) && empty($val['enabled'])) {
				unset($this->fields[$key]);
			}
		}

		// Translate some data of arrayofkeyval
		if (is_object($langs)) {
			foreach ($this->fields as $key => $val) {
				if (! empty($val['arrayofkeyval']) && is_array($val['arrayofkeyval'])) {
					foreach ($val['arrayofkeyval'] as $key2 => $val2) {
						$this->fields[$key]['arrayofkeyval'][$key2] = $langs->trans($val2);
					}
				}
			}
		}

		$this->title = $langs->trans('NewNote');
		$this->comment = $langs->trans('NoteComment');
		//status = private, shared, public
		$this->status = 'private';
	}

	/**
	 * return sheet color according to user conf
	 * @param var $code
	 * @param User $user
	 * @return Hex color
	 */
	public static function getcolor($code, $user = false){
		global  $conf;
		$default = '#ffff88';

		$confkey = 'POSTIT_COLOR_' . strtoupper($code) ;

		$Tcode = array(
			'private' => !empty( getDolGlobalString('POSTIT_COLOR_PRIVATE') )? $conf->global->POSTIT_COLOR_PRIVATE : '#FEFE01',
			'public'  => !empty( getDolGlobalString('POSTIT_COLOR_PUBLIC') )? $conf->global->POSTIT_COLOR_PUBLIC  : '#90c6ff',
			'shared'  => !empty( getDolGlobalString('POSTIT_COLOR_SHARED') )? $conf->global->POSTIT_COLOR_SHARED  : '#B5E655',
		);

		if(!empty($user->conf->{$confkey}))
		{
			return $user->conf->{$confkey};
		}
		elseif(!empty($Tcode[$code]))
		{
			return $Tcode[$code];
		}
		else
		{
			return $default;
		}
	}

	/**
	 * @param int $fk_object
	 * @param string $type_object
	 * @param int $fk_user
	 * @return PostIt[]
	 */
	public static function getPostit($fk_object, $type_object, $fk_user) {

		global $db, $conf;

		$sql = "SELECT rowid, entity, status FROM " . MAIN_DB_PREFIX . "postit ";
		$sql .=" WHERE (fk_user=" . $fk_user . " OR fk_user_todo=" . $fk_user . " OR status='" . self::STATUS_PUBLIC . "' OR status='" . self::STATUS_SHARED . "') ";
		$sql .=" AND (fk_object=" . $fk_object . " OR status='" . self::STATUS_SHARED . "') AND type_object='" . $type_object . "'";
		if(!empty($conf->global->POSTIT_MULTICOMPANY_SHARED)) {
		    $sql .= " AND entity IN (".getEntity('postit').") AND (entity=".$conf->entity." OR status IN('".self::STATUS_SHARED."', '".self::STATUS_PUBLIC."'))";
		 }
		$sql .= " ORDER BY rowid";
		$res = $db->query($sql);
		$TPostit = array();
		while ($obj = $db->fetch_object($res)) {
			$p = new PostIt($db);
			$p->fetch($obj->rowid);
			if ($p > 0) $TPostit[] = $p;
		}

		return $TPostit;
	}

	/**
	 * @param	int    	$id				Id object
	 * @param	string 	$ref			Ref
	 * @param	string	$morewhere		More SQL filters (' AND ...')
     * @param   int     $noextrafields  0=Default to load extrafields, 1=No extrafields
	 * @return 	int         			<0 if KO, 0 if not found, >0 if OK
	 */
	public function fetch($id, $ref = null, $morewhere = '', $noextrafields = 0) {
		return parent::fetchCommon($id, $ref, $morewhere, $noextrafields);
	}

	/**
	 * Initialise object with example values
	 * Id must be 0 if object instance is a specimen
	 *
	 * @return void
	 */
	public function initAsSpecimen() {
		// Set here init that are not commonf fields
		// $this->property1 = ...
		// $this->property2 = ...

		$this->initAsSpecimenCommon();
	}


	/**
	 * @param $user
	 * @return int
	 */
	public function create($user) {
		return parent::createCommon($user);
	}

	/**
	 * @param $user
	 * @return int
	 */
	public function update($user) {
		return parent::updateCommon($user);
	}

	/**
	 * @param $user
	 * @return int
	 */
	public function delete($user) {
		return parent::deleteCommon($user);
	}

	/**
	 * @param string $status status
	 * @return int|string
	 */
	public function getLibStatut($status)
	{
		global $langs;
		$langs->load('postit@postit');

		return $this->status;
	}


}
