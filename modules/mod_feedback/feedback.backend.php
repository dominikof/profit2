<?php
/**
* feedback.backend.php  
* script for all actions with feedback
* @package Feedback Package of SEOCMS
* @author Igor Trokhymchuk  <ihor@seotm.com>
* @version 1.1, 22.12.2010
* @copyright (c) 2010+ by SEOTM
*/
if (!defined("SITE_PATH")) define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] );
include_once( SITE_PATH.'/modules/mod_feedback/feedback.defines.php' );

//echo '<br>_LANG_ID='._LANG_ID;
if(!defined("_LANG_ID")) {$pg = new PageAdmin();} 

if( !isset( $_REQUEST['module'] ) ) $module = NULL;
else $module = $_REQUEST['module'];

//Blocking to execute a script from outside (not Admin-part) 
if ( !$pg->logon->isAccessToScript($module)) exit;


if( !isset($_REQUEST['task']) || empty($_REQUEST['task']) ) $task='show';
else $task=$_REQUEST['task'];

if( !isset( $_REQUEST['fltr'] ) ) $fltr= NULL;
else $fltr = $_REQUEST['fltr'];

if( !isset( $_REQUEST['srch'] ) ) $srch = NULL;
else $srch = $_REQUEST['srch'];

if( !isset( $_REQUEST['sort'] ) ) $sort = 'id';  
else $sort = $_REQUEST['sort'];

if( !isset( $_REQUEST['sort_old'] ) ) $sort_old = NULL;  
else $sort_old = $_REQUEST['sort_old'];

if( !isset( $_REQUEST['asc_desc'] ) ) $asc_desc = 'desc';  
else $asc_desc = $_REQUEST['asc_desc'];

if( !isset( $_REQUEST['start'] ) ) $start = 0;  
else $start = $_REQUEST['start']; 

if( !isset($_REQUEST['display']) ) $display=50;
else $display=$_REQUEST['display'];

if( !isset( $_REQUEST['id'] ) ) $id = NULL;
else $id = $_REQUEST['id']; 

$Feedback = new FeedbackCtrl($pg->logon->user_id, $module, 10, $sort, $start, '100%');
$Feedback->display = $display;
$Feedback->sort = $sort;
$Feedback->sort_old = $sort_old;
$Feedback->asc_desc = $asc_desc;
$Feedback->start = $start;
$Feedback->srch = $srch;
$Feedback->fltr = $fltr;

$Feedback->id=$id;  

//echo '<br />$Feedback->sort='.$Feedback->sort.' $Feedback->sort_old='.$Feedback->sort_old.' $Feedback->asc_desc='.$Feedback->asc_desc;
if( $Feedback->sort==$Feedback->sort_old){
    if($Feedback->asc_desc=='asc') $Feedback->asc_desc = 'desc';
    else $Feedback->asc_desc = 'asc'; 
}
//echo '<br />$Feedback->asc_desc='.$Feedback->asc_desc;

$Feedback->script=$_SERVER['PHP_SELF']."?module=$Feedback->module&display=$Feedback->display&start=$Feedback->start&sort=$Feedback->sort&asc_des=$Feedback->asc_desc&fltr=$Feedback->fltr&srch=$Feedback->srch";

 switch( $task ) {
    case 'show':      $Feedback->show();
                      break;
    case 'edit':
                      if (!$Feedback->edit()) echo "<script>window.location.href='$Feedback->script';</script>";
                      break;
    case 'delete':
                      if( !isset($_REQUEST['id_del']) ) $id_del=NULL;
                      else $id_del = $_REQUEST['id_del'];
                      if ( !empty($id_del) ) {
                         $del=$Feedback->del( $id_del );
                         if ( !$del > 0 ) $pg->Msg->show_msg('_ERROR_DELETE');
                         //else echo "<script>window.alert('".$pg->Msg->get_msg('_SYS_DELETED_OK')." $del');</script>";
                      }
                      else $pg->Msg->show_msg('_ERROR_SELECT_FOR_DEL');
                      echo "<script>window.location.href='$Feedback->script';</script>";
                      break;
    case 'cancel':
                      echo "<script>window.location.href='$Feedback->script';</script>";
                      break;                     
 }
?>