<?php                                     /* article_settings.backend.php */
// ================================================================================================
// System : SEOCMS
// Module : article_settings.backend.php
// Version : 1.0.0
// Date : 23.05.2007
// Licensed To:
// Igor Trokhymchuk ihoru@mail.ru
// Andriy Lykhodid las_zt@mail.ru
//
// Purpose : script for all actions with settings for article on the back-end
//
// ================================================================================================
if (!defined("SITE_PATH")) define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] );
include_once( SITE_PATH.'/admin/include/defines.inc.php' );
include_once( $_SERVER['DOCUMENT_ROOT'].'/modules/mod_article/article.defines.php' );


if( !isset( $_REQUEST['module'] ) ) $module = NULL;
else $module = $_REQUEST['module'];

//============================================================================================
// START
// Blocking to execute a script from outside (not Admin-part) 
//============================================================================================
$Msg = new ShowMsg();
$goto = "http://".NAME_SERVER."/admin/index.php?logout=1";
//echo '<br>$goto='.$goto;
if ( !isset($_SESSION[ 'session_id']) OR empty($_SESSION[ 'session_id']) OR empty( $module ) ) {
     //$Msg->show_msg( '_NOT_AUTH' );
     //return false;
     ?><script>window.location.href="<?=$goto?>";</script><?;
}

$logon = new  Authorization();
if (!$logon->LoginCheck()) {
    //return false;
    ?><script>window.location.href="<?=$goto?>";</script><?; 
}
//=============================================================================================
// END
//=============================================================================================


if( !isset($_REQUEST['task']) || empty($_REQUEST['task']) ) $task='show';
else $task=$_REQUEST['task'];

if (isset($_REQUEST['img'])) $img=1;
else $img=0;

if (isset($_REQUEST['set_keywrd'])) $set_keywrd=1;
else $set_keywrd=0;

if (isset($_REQUEST['set_descr'])) $set_descr=1;
else $set_descr=0;

if (isset($_REQUEST['rewrite'])) $rewrite=1;
else $rewrite=0;

if (isset($_REQUEST['dt'])) $dt=1;
else $dt=0;

if (isset($_REQUEST['rss'])) $rss=1;
else $rss=0;

if(isset($_REQUEST['img_path'])) $img_path = $_REQUEST['img_path'];
else $img_path = ArticleImg_Path;

if(isset($_REQUEST['title'])) $title = $_REQUEST['title'];
else $title = NULL;

if(isset($_REQUEST['description'])) $description = $_REQUEST['description'];
else $description = NULL;

if(isset($_REQUEST['keywords'])) $keywords = $_REQUEST['keywords'];
else $keywords= NULL; 

$Art = new Article_settings($logon->user_id, $module,NULL,NULL,NULL,"500");
$Art->img = $img; 
$Art->set_keywrd = $set_keywrd; 
$Art->set_descr = $set_descr; 
$Art->rewrite = $rewrite; 
$Art->dt = $dt;
$Art->img_path = $img_path;
$Art->rss = $rss;
$Art->title = $title;
$Art->description = $description;
$Art->keywords = $keywords;

$scriptact = $_SERVER['PHP_SELF']."?module=$Art->module";

 switch( $task ) {
    case 'show':      
                $Art->ShowSettings();
                break;
    case 'save':      
                if ( $Art->SaveSettings() ) echo "<script>window.location.href='$scriptact';</script>";
                break;
 }
?>
