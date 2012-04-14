<?php
/**
* sys_func.php
* script for all actions with functions (modules)
* @package System Package of SEOCMS
* @author Igor Trokhymchuk  <ihor@seotm.com>
* @version 1.1, 02.04.2012
* @copyright (c) 2005+ by SEOTM
*/
if (!defined("SITE_PATH")) define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] ); 
include_once( SITE_PATH.'/admin/include/defines.inc.php' );
include_once( SITE_PATH.'/admin/modules/sys_func/sys_func.class.php' );

$module = AntiHacker::AntiHackRequest('module');

//============================================================================================
// START
// Blocking to execute a script from outside (not Admin-part) 
//============================================================================================
$Msg = new ShowMsg();
$goto = "http://".NAME_SERVER."/admin/index.php?logout=1";
//echo '<br>$goto='.$goto;
if ( ! defined('BASEPATH')) {
	 //$Msg->show_msg( '_NOT_AUTH' );
	 //return false;
	 ?><script>window.location.href="<?=$goto?>";</script><?;
}

$logon =&check_init('logon','Authorization');
/*if (!$logon->LoginCheck()) {
	//return false;
	?><script>window.location.href="<?=$goto?>";</script><?; 
}*/
if ( ! defined('BASEPATH')) {
	//return false;
	?><script>window.location.href="<?=$goto?>";</script><?; 
//    exit('No direct script access allowed');
}
//=============================================================================================
// END
//=============================================================================================
$task = AntiHacker::AntiHackRequest('task','show');
$display = AntiHacker::AntiHackRequest('display',20);
$start = AntiHacker::AntiHackRequest('start','0');
$sort = AntiHacker::AntiHackRequest('sort');
$fltr = AntiHacker::AntiHackRequest('fltr');
$id = AntiHacker::AntiHackRequest('id');
$target = AntiHacker::AntiHackRequest('target');
$name = AntiHacker::AntiHackRequest('name');
$description = AntiHacker::AntiHackArrayRequest('description');

$user_id = $logon->user_id;
$sys_func = new SysFunc( $user_id, $module, 10, $sort, $start );
$sys_func->user_id = $user_id; 
$sys_func->display = $display; 
$sys_func->start = $start;
$sys_func->sort = $sort;
$sys_func->fltr = $fltr;
$sys_func->task = $task;
$sys_func->id = $id;
$sys_func->name = $name;
$sys_func->target = $target;
$sys_func->description = $description;
$sys_func->scriptact = $_SERVER['PHP_SELF']."?module=$module&display=$sys_func->display&start=$sys_func->start&sort=$sys_func->sort&fltr=$sys_func->fltr";
switch( $task )
{
 case 'show':
			  $sys_func->show( $user_id, $module, $display, $sort, $start );
			  break;
 case 'edit':
			  if ( !$sys_func->edit( $user_id, $module, $id, NULL ) )
				 echo "<script>window.location.href='$sys_func->scriptact';</script>";
			  break;
 case 'new':
			  $sys_func->edit( $user_id, $module, NULL, NULL );
			  break;
 case 'save':
			  if( empty($sys_func->id) AND $sys_func->ExistFunc($sys_func->name)){
				  $sys_func->Err = $sys_func->Err.$Msg->show_text('MSG_FUNCTION_ALREADY_EXIST').'<br>';
				  $sys_func->edit( $user_id, $module, $id, NULL );
				  return false;
			  }
			  
			  if ( $sys_func->save( $user_id, $module, $id, $name, $description, $target ) )
			  {
				   echo "<script>window.location.href='$sys_func->scriptact';</script>";
			  }
			  //else $Msg->show_msg('_ERROR_SAVE');
			  break;
 case 'cancel':
			  echo "<script>window.location.href='$sys_func->scriptact';</script>";
			  break;              
 case 'delete':
			  if( !isset($_REQUEST['id_del']) ) $id_del=NULL;
			  else $id_del = $_REQUEST['id_del'];
			  if ( !empty($id_del) ) {
				 $del = $sys_func->del( $user_id, $module, $id_del );
				 if ( $del > 0 ) echo "<script>window.alert('Deleted OK! ($del records)');</script>";
				 else echo $Msg->show_msg('_ERROR_DELETE');
			  }
			  else $msg->show_msg('_ERROR_SELECT_FOR_DEL');
			  echo "<script>window.location.href='$sys_func->scriptact';</script>";
			  break;
}
?>