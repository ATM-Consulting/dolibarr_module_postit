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

require __DIR__.'/../config.php';
dol_include_once('/postit/class/postit.class.php');

// Define css type
header('Content-type: text/css');
// Important: Following code is to avoid page request by browser and PHP CPU at
// each Dolibarr page access.
if (empty($dolibarr_nocache)) header('Cache-Control: max-age=3600, public, must-revalidate');
else header('Cache-Control: no-cache');


?>


@font-face {
    font-family: "dakotaHand";
    src: url("handwriting-dakota.ttf")  format('truetype');
}

.postit {
	font-family:    'sans-serif','dakotaHand', cursive; /* 'chilanka', */

	position:absolute;
	padding:3px;
	transform: rotate(-2deg);
	overflow:hidden;


	text-decoration:none;
	color:#000;
	background:#ffc;
	display:block;
	height:10em;
	width:10em;
	padding:1em;

	-moz-box-shadow:2px 2px 7px rgba(0,0,0,.5);
	-webkit-box-shadow: 2px 2px 7px rgba(0,0,0,.5);
	box-shadow: 2px 2px 7px rgba(0,0,0,.5);

	-moz-transition:-moz-transform .15s linear;
	-o-transition:-o-transform .15s linear;
	-webkit-transition:-webkit-transform .15s linear;

}
.postit:hover {
	transform: rotate(0)  scale(1.1);
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
.postit div[rel=content] div.ifempty:empty {
	min-height: 20px;
	border:1px dashed #eee;

}
.postit[rel=status]{
	margin-left: 10px;
}

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


