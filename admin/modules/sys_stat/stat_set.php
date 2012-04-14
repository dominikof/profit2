<?php
/**
* stat_set.php
* script for all actions with system settings on the back-end
* @package System Package of SEOCMS
* @author Igor Trokhymchuk  <ihor@seotm.com>
* @version 1.1, 02.04.2012
* @copyright (c) 2005+ by SEOTM
*/
if (!defined("SITE_PATH")) define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] );
include_once( SITE_PATH.'/admin/include/defines.inc.php' );


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

$logon = &check_init('logon','Authorization');
if (!$logon->LoginCheck()) {
    //return false;
    ?><script>window.location.href="<?=$goto?>";</script><?; 
}
//=============================================================================================
// END
//=============================================================================================

$user_id = $logon->user_id;

if( !isset($_REQUEST['task']) ) $task = 'show';
else $task = $_REQUEST['task'];

if( !isset( $_REQUEST['id'] ) ) $id = 1;
else $id = $_REQUEST['id'];
$id = 1;

if( !isset( $_REQUEST['db_name'] ) ) $db_name = '';
else $db_name = $_REQUEST['db_name'];

if( !isset( $_REQUEST['db_user'] ) ) $db_user = '';
else $db_user = $_REQUEST['db_user'];

if( !isset( $_REQUEST['db_pass'] ) ) $db_pass = '';
else $db_pass = $_REQUEST['db_pass'];

if( !isset( $_REQUEST['front'] ) ) $front = 0;
else $front = $_REQUEST['front'];

if( !isset( $_REQUEST['back'] ) ) $back = 0;
else $back = $_REQUEST['back'];


$script = 'index.php?module='.$module;

$stat = new StatSetCtrl($user_id, $module);
$stat->db_name = $db_name;
$stat->db_user = $db_user;
$stat->db_pass = $db_pass;
$stat->front = $front;
$stat->back = $back;

//echo '<br>$task='.$task;
switch( $task )
{
  case 'show':
              $stat->StatSetShow();
              break;
  case 'save':
              $stat->StatSetSave();
              echo "<script>window.location.href='$script';</script>";
              break;
}
?>
