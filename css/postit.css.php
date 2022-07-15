<?php

//if (! defined('NOREQUIREUSER')) define('NOREQUIREUSER','1');	// Not disabled cause need to load personalized language
//if (! defined('NOREQUIREDB'))   define('NOREQUIREDB','1');	// Not disabled to increase speed. Language code is found on url.
if (! defined('NOREQUIRESOC'))    define('NOREQUIRESOC','1');
//if (! defined('NOREQUIRETRAN')) define('NOREQUIRETRAN','1');	// Not disabled cause need to do translations
if (! defined('NOCSRFCHECK'))     define('NOCSRFCHECK',1);
if (! defined('NOTOKENRENEWAL'))  define('NOTOKENRENEWAL',1);
//if (! defined('NOLOGIN'))         define('NOLOGIN',1);
if (! defined('NOREQUIREMENU'))   define('NOREQUIREMENU',1);
if (! defined('NOREQUIREHTML'))   define('NOREQUIREHTML',1);
if (! defined('NOREQUIREAJAX'))   define('NOREQUIREAJAX','1');

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
if (!$res && file_exists("../main.inc.php")) {
	$res = @include "../main.inc.php";
}
if (!$res && file_exists("../../main.inc.php")) {
	$res = @include "../../main.inc.php";
}
if (!$res && file_exists("../../../main.inc.php")) {
	$res = @include "../../../main.inc.php";
}
if (!$res) {
	die("Include of main fails");
}

dol_include_once('/postit/class/postit.class.php');

// Define css type
header('Content-type: text/css');
// Important: Following code is to avoid page request by browser and PHP CPU at
// each Dolibarr page access.
if (empty($dolibarr_nocache)) header('Cache-Control: max-age=3600, public, must-revalidate');
else header('Cache-Control: no-cache');


?>


.postit {
    position:absolute;
    padding:3px;
    transform: rotate(-2deg);
    overflow:hidden;


    text-decoration:none;
    color: rgba(0, 0, 0, 0.90);
    background:#ffc;
    display:block;
    height:10em;
    width:10em;

	min-width: 200px;
	min-height:200px ;

    padding:1em;

    -moz-box-shadow:2px 2px 4px rgba(0,0,0,.4);
    -webkit-box-shadow: 2px 2px 4px rgba(0,0,0,.4);
    box-shadow: 2px 2px 2px rgba(0,0,0,.4);

    -moz-transition:-moz-transform .15s linear;
    -o-transition:-o-transform .15s linear;
    -webkit-transition:-webkit-transform .15s linear;
	transition: box-shadow 0.15s linear, transform  .15s linear;

}
.postit:hover {
    transform: rotate(0)  scale(1.1);
	box-shadow: 10px 10px 7px rgba(0,0,0,.2);
	z-index: 9999;
}
.postit div[rel=content] {
    position:relative;
    height:100%;
}


.postit div[rel=actions] {
    position: absolute;
    bottom:0;
    left:0;
    display:none;
}
.postit:hover div[rel=actions] {
    display:block;
	animation: postit-action-slide-up 0.2s forwards, fade-in 0.2ms forwards;
}
.postit div[rel=actions] span {
    cursor: pointer;
    margin-right:10px;
}
.postit div[rel=actions] span img {
    width:18px;
    height:18px;
}
.postit [rel=postit-title] {
    font-weight:bold;
	font-size: 13.3333px; /* FIX :  wierd behavior jquery editable add allways this font size so it make an display change */
}
.postit div[rel=content] div[rel=postit-author], .postit div[rel=content] div[rel=postit-tms] {
    text-align: right;
    font-size: 9px;
    color:#333;
}
.postit div[rel=content] div[rel=postit-title],.postit div[rel=content] div[rel=postit-comment] {
    position:relative;
    margin-bottom:2px;
}


.postit div[rel=postit-comment] {
	max-height: calc(100% - 64px);
	overflow: auto;
	font-size: 13.3333px; /* FIX :  wierd behavior jquery editable add allways this font size so it make an display change */
}

.postit div[rel=postit-comment] textarea {
	border: none;
	padding: 0;
	margin: 0;
	background: none;
	height: 100%;
	width: 100%;
	line-height: 1.4;
}

.postit div[rel=postit-title] textarea {
	border: none;
	padding: 0;
	margin: 0;
	background: none;
	height: 100%;
	width: 100%;
	font-weight: bold;
	line-height: 1.4;
}



.postit div[rel=content] div.ifempty:empty {
    min-height: 20px;
    border:1px dashed #eee;

}
.postit[rel=status]{
    margin-left: 10px;
}




	/*
	* Scroll barr
	 */
.postit{
	--scrollbar-size : 4px;
	--scrollbar-track-color : rgba(0, 0, 0, 0.05);
	--scrollbar-thumb-color : rgba(0,0,0, 0.2);
}

.postit ::-webkit-scrollbar {
	width: var(--scrollbar-size);
	height: var(--scrollbar-size);
}

.postit ::-webkit-scrollbar-track {
	background-color: var(--scrollbar-track-color);
	border-radius:  10px;
}

.postit ::-webkit-scrollbar-thumb {
	border-radius:  10px;
	background: var(--scrollbar-thumb-color);
}

.postit ::-webkit-scrollbar-corner{
	background: rgba(0, 0, 0, 0.1);
}

/*::-webkit-scrollbar-thumb:window-inactive {*/
/*	background: rgba(0, 0, 0, 0.1)*/
/*}*/

/*
.postit input, .postit textarea {
	background: none;
	border:0 none;
	box-shadow: none;
}
*/

.yellowPaperTemporary {
    background-color: <?php print PostIt::getcolor('default', $user); ?>; /* Old browsers */
    background-image: linear-gradient(135deg, rgba(255,255,255,0) , rgba(255,255,255,0) 90%, rgba(255,255,255,0.1) 93%,rgba(255,255,255,0.4) 100%); /* W3C */
}

.yellowPaper {
    background: <?php print PostIt::getcolor('private', $user); ?> !important;
}

.bluePaper {
    background-color:<?php print PostIt::getcolor('public', $user); ?> !important; /*#7FC6BC;*/
}

.greenPaper {
    background-color:<?php print PostIt::getcolor('shared', $user); ?> !important;
}

#addNote[data-theme="eldy"]{
	transition: transform 0.2s ease-in-out;
}
#addNote[data-theme="eldy"]:hover{
	transform: scale(1.1);
}


@keyframes postit-action-slide-up {
	0% {
		transform: translate(0, 100%);
	}
	100% {
		transform: translate(0, 0%);
	}
}

.postit-icon, .statusText{
	color: rgba(0,0,0,0.5);
}

.postit-icon:hover{
	color: rgba(0,0,0,1);
}
