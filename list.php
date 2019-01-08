<?php

require 'config.php';
dol_include_once('/postit/class/postit.class.php');
dol_include_once('abricot/includes/lib/admin.lib.php');

require_once DOL_DOCUMENT_ROOT.'/core/lib/usergroups.lib.php';
require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';

// vérifie les droits en lecture
if(empty($user->rights->postit->myaction->read)) accessforbidden();

$langs->load('abricot@abricot');
$langs->load('postit@postit');

$PDOdb = new TPDOdb;
$object = new PostIt($db);
$postItUser = new User($db);


$action = GETPOST('action', 'alpha');
$id = GETPOST('id', 'int');

if(empty($id)){
    $id = $user->id;
}

if( $postItUser->fetch($id, '', '', 10) < 1 ){ exit; }


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
    
    if (preg_match('/set_(.*)/',$action,$reg))
    {
        $code=$reg[1];
        if (dol_set_user_param($db, $conf, $postItUser, array($code => GETPOST($code))))
        {
            header("Location: ".$_SERVER["PHP_SELF"]);
            exit;
        }
        else
        {
            dol_print_error($db);
        }
    }
    
    if (preg_match('/del_(.*)/',$action,$reg))
    {
        $code=$reg[1];
        if (dol_set_user_param($db, $conf, $postItUser, array($code => false)))
        {
            Header("Location: ".$_SERVER["PHP_SELF"]);
            exit;
        }
        else
        {
            dol_print_error($db);
        }
    }
    
}


/*
 * View
 */

llxHeader('',$langs->trans('PostitList'),'','');

if($postItUser->id > 0) {
    
    $head = user_prepare_head($postItUser);

    dol_fiche_head($head, 'postit', $langs->trans("User"), 0, 'user');
}



/* DISPLAY COLOR OPTIONS */
if(!function_exists('setup_print_title')){
    print '<div class="error" >'.$langs->trans('AbricotNeedUpdate').' : <a href="http://wiki.atm-consulting.fr/index.php/Accueil#Abricot" target="_blank"><i class="fa fa-info"></i> Wiki</a></div>';
}
else 
{
    print '<table class="noborder" width="100%">';
    setup_print_title("Parameters");
    
    $Tcolors = array('private', 'public', 'shared');
    
    $form=new Form($db);
    $title = false;
    $desc ='';
    $type='input';
    $help = false;
    
    foreach ($Tcolors as $code)
    {
        $confkey = 'POSTIT_COLOR_' . strtoupper($code) ;
        
        $metas = array(
            'name' => $confkey,
            'type'=>'color'
        );
        
        
        
        $metas['value']  = PostIt::getcolor($code, $postItUser);
        
        $metascompil = '';
        foreach ($metas as $key => $values)
        {
            $metascompil .= ' '.$key.'="'.$values.'" ';
        }
        
        print '<tr class="oddeven" >';
        print '<td>';
        
        if(!empty($help)){
            print $form->textwithtooltip( ($title?$title:$langs->trans($confkey)) , $langs->trans($help),2,1,img_help(1,''));
        }
        else {
            print $title?$title:$langs->trans($confkey);
        }
        
        if(!empty($desc))
        {
            print '<br><small>'.$langs->trans($desc).'</small>';
        }
        
        print '</td>';
        print '<td align="center" width="20">&nbsp;</td>';
        print '<td align="right" width="300">';
        print '<form method="POST" action="'.$_SERVER['PHP_SELF'].'">';
        print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
        print '<input type="hidden" name="action" value="set_'.$confkey.'">';
        print '<input '.$metascompil.'  />';
        
        print '<input type="submit" class="butAction" value="'.$langs->trans("Modify").'">';
        print '</form>';
        print '</td></tr>';
    }
    
    print '</table>';
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
$sql.= ' WHERE (t.fk_user='.$postItUser->id . ' OR t.status!=\'private\')';

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
        'Action' => '_getLineAction(@rowid@)',
        'comment' => '_truncComm(@rowid@)'
    )
));

$parameters=array('sql'=>$sql);
$reshook=$hookmanager->executeHooks('printFieldListFooter', $parameters, $object);    // Note that $action and $object may have been modified by hook
print $hookmanager->resPrint;

$formcore->end_form();

if($postItUser->id > 0) {
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
            
            $targetClass = $obj->type_object;
            if($obj->type_object == 'invoice_supplier'){
                $targetClass = 'FactureFournisseur';
            }
            if($obj->type_object == 'order_supplier'){
                $targetClass = 'CommandeFournisseur';
            }
            if($obj->type_object == 'propal'){
                $targetClass = 'Propal';
            }
            
            
            if (!class_exists($targetClass)){
                $FileToLoad = DOL_DOCUMENT_ROOT.'/'.strtolower($obj->type_object).'/class/'.strtolower($obj->type_object).'.class.php';
                if($obj->type_object == 'propal'){
                    $FileToLoad = DOL_DOCUMENT_ROOT.'/comm/propal/class/propal.class.php';
                }
                elseif($obj->type_object == 'invoice_supplier'){
                    $FileToLoad = DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.facture.class.php';
                }
                elseif($obj->type_object == 'order_supplier'){
                    $FileToLoad = DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.commande.class.php';
                }
                
                if(file_exists($FileToLoad)){
                    include_once $FileToLoad;
                }
                // else{ var_dump(array($obj->type_object, $FileToLoad)); }
            }
			
            if (class_exists($targetClass))
			{
			    $o = new $targetClass($db);
				if($o->fetch($obj->fk_object)){
					$link = $link = $o->getNomUrl();
				}
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

function _truncComm($id){
    global $db;
    
    $sql = "SELECT comment FROM ".MAIN_DB_PREFIX.'postit t WHERE rowid='.$id;
    $res = $db->query($sql);
    if($res){
        $obj = $db->fetch_object($res);
        return dol_trunc($obj->comment);
    }
        
}

/**
 * renvoie un lien de suppression si l'utilisateur a les droits
 * @param $id du postit courant
 * @return string
 */
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