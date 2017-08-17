<?php

require 'config.php';
dol_include_once('/postit/class/postit.class.php');

require_once DOL_DOCUMENT_ROOT.'/core/lib/usergroups.lib.php';
require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';

// vérifie les droits en lecture
if(empty($user->rights->postit->myaction->read)) accessforbidden();

$langs->load('abricot@abricot');
$langs->load('postit@postit');

$PDOdb = new TPDOdb;
$object = new TPostIt;

$action = GETPOST('action', 'alpha');
$id = GETPOST('id', 'int');

$hookmanager->initHooks(array('postitlist'));

/*
 * Actions
 */

$parameters=array();
$reshook=$hookmanager->executeHooks('doActions',$parameters,$object);    // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

if (empty($reshook))
{   
    if($action == 'del_postit' && $user->rights->postit->myaction->write){
        $object->load($PDOdb, $id);
        $object->delete($PDOdb);
    }
}


/*
 * View
 */

llxHeader('',$langs->trans('PostitList'),'','');

if($user->id > 0) {
    
    $head = user_prepare_head($user);

    dol_fiche_head($head, 'postit', $langs->trans("User"), 0, 'user');
}

// création de la liste des auteurs pour la recherche dans la liste
$userSql = 'SELECT DISTINCT u.rowid as id, u.lastname, u.firstname FROM '.MAIN_DB_PREFIX.'user u INNER JOIN '.MAIN_DB_PREFIX.'postit p ON (p.fk_user = u.rowid)';
$result = $db->query($userSql);
if($result){
    $authors = array();
    while($obj = $db->fetch_object($result)){
        $authors[$obj->id] = dolGetFirstLastname($obj->firstname, $obj->lastname);
    }
    $authors[$user->id] = dolGetFirstLastname($user->firstname,$user->lastname);

}


$sql = 'SELECT DISTINCT t.rowid, t.fk_user, t.title, t.comment, t.status, \'\' as Page, \'\' as Action';
$sql.= ' FROM '.MAIN_DB_PREFIX.'postit t';
$sql.= ' WHERE (t.fk_user='.$user->id . ' OR t.status!=\'private\')';

$formcore = new TFormCore($_SERVER['PHP_SELF'], 'form_list_postit', 'GET');

$nbLine = !empty($user->conf->MAIN_SIZE_LISTE_LIMIT) ? $user->conf->MAIN_SIZE_LISTE_LIMIT : $conf->global->MAIN_SIZE_LISTE_LIMIT;

$r = new TListviewTBS('postit');
echo $r->render($PDOdb, $sql, array(
    'view_type' => 'list' // default = [list], [raw], [chart]
    ,'limit'=>array(
        'nbLine' => $nbLine
    )
    ,'subQuery' => array()
    ,'link' => array()
    ,'type' => array()
    ,'search' => array(
        'fk_user' => array('recherche' => $authors)  // problème avec la requête : quelque soit l'user demandé, les postit du user courant apparaissent toujours...
        ,'title' => array('recherche' => true, 'table' => 't', 'field' => 'title')
        ,'comment' => array('recherche' => true, 'table' => 't', 'field' => 'comment')
        ,'status' => array('recherche' => array('private' => $langs->trans('private'), 'public' => $langs->trans('public'), 'shared' =>$langs->trans('shared')) , 'to_translate' => true) // select html, la clé = le status de l'objet, 'to_translate' à true si nécessaire
    )
    ,'translate' => array()
    ,'hide' => array(
        'rowid'
    )
    ,'liste' => array(
        'titre' => $langs->trans('PostitList')
        ,'image' => img_picto('','title_generic.png', '', 0)
        ,'picto_precedent' => '<'
        ,'picto_suivant' => '>'
        ,'noheader' => 0
        ,'messageNothing' => $langs->trans('NoPostit')
        ,'picto_search' => img_picto('','search.png', '', 0)
    )
    ,'title'=>array(
        'fk_user' => $langs->trans('Author')
        ,'title' => $langs->trans('Title')
        ,'comment' => $langs->trans('Comment')
        ,'status' => $langs->trans('Status')
    )
    ,'eval'=>array(
        'fk_user' => '_getAuthor(@val@)',
        'status' => '_getLibStatut("@val@")',
        'Page' => '_getPageLink(@rowid@)',
        'Action' => '_getLineAction(@rowid@)'
    )
));

$parameters=array('sql'=>$sql);
$reshook=$hookmanager->executeHooks('printFieldListFooter', $parameters, $object);    // Note that $action and $object may have been modified by hook
print $hookmanager->resPrint;

$formcore->end_form();

if($user->id > 0) {
    dol_fiche_end();
}

llxFooter('');

/**
 * TODO remove if unused
 */

function _getLibStatut($status)
{
    global $langs;
    $langs->load('postit@postit');
    
    return $langs->trans($status);
}

/**
 * fonction qui retourne un lien vers la page où figure le postit spécifié
 * @param $id du postit
 * @return string lien vers la page du postit
 */
function _getPageLink($id)
{
    global $db, $langs;
    
    $sql = "SELECT fk_object, type_object FROM ".MAIN_DB_PREFIX.'postit t WHERE rowid='.$id;
    $res = $db->query($sql);
    if($res){
        $obj = $db->fetch_object($res);
        
        $link = '';
        if($obj->type_object == 'global'){
            // global correspond à la page d'accueil
            $link = '<a href="'.dol_buildpath('/',1).'">'.$langs->trans('Home').'</a>';
        } else {
            // sinon on instancie un objet du type voulu, on le récupère et on génère son url
            $o = new $obj->type_object($db);
            if($o->fetch($obj->fk_object)){
                $link = $link = $o->getNomUrl();
            }
        } 
    }
    
    return $link;
}

/**
 * Function qui renvoie le lien vers le profil utilisateur de l'auteur
 * @param $id de l'auteur de la note
 * @return string
 */
function _getAuthor($id){
    global $db;
    
    $u = new User($db);
    $u->fetch($id);
    
    return $u->getNomUrl();
}


function _getLineAction($id){
    global $db, $user;
    
    $sql = "SELECT fk_user FROM ".MAIN_DB_PREFIX.'postit t WHERE rowid='.$id;
    $res = $db->query($sql);
    if($res){
        $obj = $db->fetch_object($res);
        if(($obj->fk_user == $user->id) && !empty($user->rights->postit->myaction->write)){
            return '<a href="?action=del_postit&id='.$id.'">' . img_picto('delete','delete').'</a>';
        }
    }
}