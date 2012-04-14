<?php
/**
* sys_group.php
* script for all actions with users groups
* @package System Package of SEOCMS
* @author Igor Trokhymchuk  <ihor@seotm.com>
* @version 1.1, 02.04.2012
* @copyright (c) 2005+ by SEOTM
*/

if (!defined("SITE_PATH")) define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] ); 
include_once( SITE_PATH.'/admin/include/defines.inc.php' ); 
include_once( SITE_PATH.'/admin/modules/sys_group/sys_group.class.php' );

$module=AntiHacker::AntiHackRequest('module'); 
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

$logon = &check_init('logon','Authorization');
if ( ! defined('BASEPATH')) {
	//return false;
	?><script>window.location.href="<?=$goto?>";</script><?; 
//    exit('No direct script access allowed');
}
/*if (!$logon->LoginCheck()) {
	//return false;
	?><script>window.location.href="<?=$goto?>";</script><?; 
}*/
//=============================================================================================
// END
//=============================================================================================
$task=AntiHacker::AntiHackRequest('task','show'); 
$sort=AntiHacker::AntiHackRequest('sort'); 
$start=AntiHacker::AntiHackRequest('start',0); 
$display=AntiHacker::AntiHackRequest('display',20); 

$scriptact='index.php?module='.$module;
$sys_group = new SysGroup($logon->user_id, $module, $display, $sort, $start);

switch( $task )
{
 case 'show':
			  $sys_group->show($logon->user_id, $module, $display, $sort, $start );
			  break;
 case 'edit':
			  $id = AntiHacker::AntiHackRequest('id'); 
			  if (!$sys_group->edit($logon->user_id, $module, $id, NULL)) echo "<script>window.location.href='$scriptact&display=$display&start=$start&sort=$sort';</script>";
			  break;
 case 'new':
			  $sys_group->edit($logon->user_id, $module, NULL, NULL );
			  break;
 case 'save':
			  $id = AntiHacker::AntiHackRequest('id'); 
			  $name = AntiHacker::AntiHackRequest('name'); 
			  if(!isset($_REQUEST['adm_menu'])) $adm_menu=0;
			  else $adm_menu=1;
			  if (!$sys_group->save($logon->user_id, $module, $id, $name, $adm_menu)) {
				 $msg->show_msg('_ERROR_SAVE');
			  }
			  echo "<script>window.location.href='$scriptact&display=$display&start=$start&sort=$sort';</script>";
			  break;

 case 'delete':
			  if(!isset($_REQUEST['id_del'])) $id_del=NULL;
			  else $id_del=$_REQUEST['id_del'];

			  $del = $sys_group->del($logon->user_id, $module, $id_del );
			  if ( $del > 0 ) echo "<script>window.alert('Deleted OK! ($del records)');</script>";
			  else $msg->show_msg('_ERROR_DELETE');
			  echo "<script>window.location.href='$scriptact&display=$display&start=$start&sort=$sort';</script>";
			  break;
}
?>