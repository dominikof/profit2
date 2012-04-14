<?php                                     /* news_settings.backend.php */
// ================================================================================================
// System : SEOCMS
// Module : news_settings.backend.php
// Version : 1.0.0
// Date : 23.05.2007
// Licensed To:
// Igor Trokhymchuk ihoru@mail.ru
// Andriy Lykhodid las_zt@mail.ru
//
// Purpose : script for all actions with settings for News on the back-end
//
// ================================================================================================
if (!defined("SITE_PATH")) define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] ); 
include_once( $_SERVER['DOCUMENT_ROOT'].'/modules/mod_news/news.defines.php' );

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

if(isset($_REQUEST['img'])) $img = 1;
else $img = 0;

if(isset($_REQUEST['short_descr'])) $short_descr = 1;
else $short_descr = 0;

if(isset($_REQUEST['full_descr'])) $full_descr = 1;
else $full_descr = 0;

if(isset($_REQUEST['source'])) $source = 1;
else $source = 0;

if(isset($_REQUEST['relat_prod'])) $relat_prod = 1;
else $relat_prod = 0;

if(isset($_REQUEST['rewrite'])) $rewrite =1;
else $rewrite = 0;

if(isset($_REQUEST['subscr'])) $subscr = 1;
else $subscr = 0;

if(isset($_REQUEST['dt'])) $dt =1;
else $dt = 0;

if(isset($_REQUEST['rss'])) $rss =1;
else $rss = 0;

if(isset($_REQUEST['rss_import'])) $rss_import =1;
else $rss_import = 0;

if(isset($_REQUEST['top_news'])) $top_news =1;
else $top_news = 0;

if(isset($_REQUEST['newsline'])) $newsline =1;
else $newsline = 0;
if(isset($_REQUEST['ukraine_news'])) $ukraine_news =1;
else $ukraine_news = 0;
if(isset($_REQUEST['main_thing'])) $main_thing =1;
else $main_thing = 0;


if(isset($_REQUEST['img_path'])) $img_path = $_REQUEST['img_path'];
else $img_path = NewsImg_Path;

if(isset($_REQUEST['title'])) $title = $_REQUEST['title'];
else $title = NULL;

if(isset($_REQUEST['description'])) $description = $_REQUEST['description'];
else $description = NULL;

if(isset($_REQUEST['keywords'])) $keywords = $_REQUEST['keywords'];
else $keywords= NULL; 

if(isset($_REQUEST['rss_id'])) $rss_id = $_REQUEST['rss_id'];
else $rss_id = NULL;

if(isset($_REQUEST['rss_path'])) $rss_path = $_REQUEST['rss_path'];
else $rss_path = NULL;

if(isset($_REQUEST['rss_descr'])) $rss_descr = $_REQUEST['rss_descr'];
else $rss_descr = NULL;

/*if(isset($_REQUEST['top_news'])) $top_news = $_REQUEST['top_news'];
else $top_news = NULL;*/

if(isset($_REQUEST['rss_status'])) $rss_status = $_REQUEST['rss_status'];
else $rss_status = 1; // 1- rss - on 


$News = new News_settings($logon->user_id, $module,NULL,NULL,NULL,"600");
$News->img = $img; 
$News->short_descr = $short_descr; 
$News->full_descr = $full_descr; 
$News->source = $source;
$News->relat_prod = $relat_prod; 
$News->rewrite = $rewrite; 
$News->subscr = $subscr;  
$News->dt = $dt;
$News->rss = $rss;
$News->rss_import = $rss_import;
$News->top_news=$top_news;
$News->ukraine_news=$ukraine_news;
$News->newsline=$newsline;
$News->main_thing=$main_thing;
$News->img_path = $img_path;

$News->title = $title;
$News->description = $description;
$News->keywords = $keywords;

$News->rss_id = $rss_id;
$News->rss_path = $rss_path;
$News->rss_descr = $rss_descr;
$News->rss_status = $rss_status;

$scriptact = $_SERVER['PHP_SELF']."?module=$News->module";

 switch( $task ) {
    case 'show':      
                $News->ShowSettings();
                break;
    case 'save':      
                if ( $News->SaveSettings() ) echo "<script>window.location.href='$scriptact';</script>";
                break;
 }
?>
