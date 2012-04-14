<?
/**
* sys_group_access.php
* script for all actions with Grand rights of the groups of users
* @package System Package of SEOCMS
* @author Igor Trokhymchuk  <ihor@seotm.com>
* @version 1.1, 02.04.2012
* @copyright (c) 2005+ by SEOTM
*/

if (!defined("SITE_PATH")) define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] );
include_once( SITE_PATH.'/admin/include/defines.inc.php' ); 
include_once( SITE_PATH.'/admin/modules/sys_group_access/sys_group_access.class.php' );

 $module=AntiHacker::AntiHackRequest('module'); 
//============================================================================================
// START
// Blocking to execute a script from outside (not Admin-part) 
//============================================================================================
$goto = "http://".NAME_SERVER."/admin/index.php?logout=1";
//echo '<br>$goto='.$goto;
if ( !isset($_SESSION[ 'session_id']) OR empty($_SESSION[ 'session_id']) OR empty( $module ) ) {
	 //$Msg->show_msg( '_NOT_AUTH' );
	 //return false;
	 ?><script>window.location.href="<?=$goto?>";</script><?;
}

$logon = &check_init('logon','Authorization');
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
$sort = AntiHacker::AntiHackRequest('sort');
$fltr = AntiHacker::AntiHackRequest('fltr');
$start = AntiHacker::AntiHackRequest('start','0');
$display = AntiHacker::AntiHackRequest('display',20);

$sys_group_func = new SysGroupFunc($logon->user_id, $module, 20, $sort, $start);
$sys_group_func->sort = $sort;
$sys_group_func->display = $display;
$sys_group_func->start = $start;
$sys_group_func->fltr = $fltr;

$scriptact = $_SERVER['PHP_SELF']."?module=".$module."&display=$display&start=$start&sort=$sort&fltr=$fltr";

switch( $task )
{
 case 'show':
			  $sys_group_func->show( $logon->user_id, $module, $display, $sort, $start );
			  break;

 case 'edit':
			  $id = AntiHacker::AntiHackRequest('id'); 
			  if (!$sys_group_func->edit( $logon->user_id, $module, $id, NULL ))
					echo "<script>window.location.href='$scriptact';</script>";
			  break;

 case 'new':
			  $sys_group_func->edit( $logon->user_id, $module, NULL, NULL );
			  break;

 case 'save':
			   $id = AntiHacker::AntiHackRequest('id'); 
			   $group = AntiHacker::AntiHackRequest('group'); 
			   $function = AntiHacker::AntiHackRequest('function'); 
			  if(!isset($_REQUEST['r'])) $r=0;
			  else $r=1;
			  if(!isset($_REQUEST['w'])) $w=0;
			  else $w=1;
			  if(!isset($_REQUEST['u'])) $u=0;
			  else $u=1;
			  if(!isset($_REQUEST['d'])) $d=0;
			  else $d=1;
			  if(!isset($_REQUEST['e'])) $e=0;
			  else $e=1;
			  if ( $sys_group_func->save( $logon->user_id, $module, $id, $group, $function, $r, $w, $u, $d, $e ) )
			  {
				 echo "<script>window.location.href='$scriptact';</script>";
			  }
			  break;

 case 'delete':
			  if(!isset($_REQUEST['id_del'])) $id_del=NULL;
			  else $id_del=$_REQUEST['id_del'];

			  $del = $sys_group_func->del(  $logon->user_id, $module, $id_del );
			  if ( $del > 0 ) echo "<script>window.alert('Deleted OK! ($del records)');</script>";
			  else echo show_error('_ERROR_DELETE');
			  echo "<script>window.location.href='$scriptact';</script>";
			  break;
}
?>
