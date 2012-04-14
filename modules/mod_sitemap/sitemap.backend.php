<?php
/**
* sitemap.backend.php  
* script for all actions with sitemap XML
* @package Sitemap Package of SEOCMS
* @author Igor Trokhymchuk  <ihor@seotm.com>
* @version 1.1, 28.07.2011
* @copyright (c) 2010+ by SEOTM
*/
if (!defined("SITE_PATH")) define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] );
include_once( SITE_PATH.'/modules/mod_sitemap/sitemap.defines.php' );

if(!defined("_LANG_ID")) {$pg = &check_init('PageAdmin', 'PageAdmin');} 

if( !isset( $_REQUEST['module'] ) ) $module = NULL;
else $module = $_REQUEST['module'];

//Blocking to execute a script from outside (not Admin-part) 
if ( !$pg->logon->isAccessToScript($module)) exit;

$Sitemap = new SiteMap($pg->logon->user_id, $module);
 
if( !isset($_REQUEST['task']) || empty($_REQUEST['task']) ) $Sitemap->task='show';
else $Sitemap->task=$_REQUEST['task'];

$Sitemap->user_id = $pg->logon->user_id;
$Sitemap->module = $module;

//$Sitemap->script=$_SERVER['PHP_SELF']."?module=$Feedback->module&display=$Feedback->display&start=$Feedback->start&sort=$Feedback->sort&fltr=$Feedback->fltr&fltr2=$Feedback->fltr2&srch=$Feedback->srch&srch2=$Feedback->srch2";

switch( $Sitemap->task ) {
    case 'show':     
        $Sitemap->show();
        break;
    case 'save_xml':
        $Sitemap->MAP_XML();
        break;
}
?>