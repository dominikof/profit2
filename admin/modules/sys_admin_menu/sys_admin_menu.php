<?php
/**
* sys_admin_menu.php
* script for all actions with Admin Menu of Content System Management
* @package System Package of SEOCMS
* @author Igor Trokhymchuk  <ihor@seotm.com>
* @version 1.1, 02.04.2012
* @copyright (c) 2005+ by SEOTM
*/

if (!defined("SITE_PATH")) define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] ); 
include_once( SITE_PATH.'/admin/include/defines.inc.php' ); 
include_once( SITE_PATH.'/admin/modules/sys_admin_menu/sys_admin_menu.class.php' );
include_once( SITE_PATH.'/admin/modules/sys_group/sys_group.class.php' );

$module = AntiHacker::AntiHackRequest('module');

//============================================================================================
// START
// Blocking to execute a script from outside (not Admin-part) 
//============================================================================================
$Msg = new ShowMsg();
$goto = "http://".NAME_SERVER."/admin/index.php?logout=1";
//echo '<br>$goto='.$goto;
if ( ! defined('BASEPATH')) { 
//if ( !isset($_SESSION[ 'session_id']) OR empty($_SESSION[ 'session_id']) OR empty( $module ) ) {
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
$task = AntiHacker::AntiHackRequest('task','show');
$function = AntiHacker::AntiHackRequest('function');
$sort = AntiHacker::AntiHackRequest('sort');
$start = AntiHacker::AntiHackRequest('start',0);
$display = AntiHacker::AntiHackRequest('display',20);
$id = AntiHacker::AntiHackRequest('id',0);
$level = AntiHacker::AntiHackRequest('level',0);
$group = AntiHacker::AntiHackRequest('group');
$description = AntiHacker::AntiHackArrayRequest('description');
$move = AntiHacker::AntiHackRequest('move');
$scriptact='index.php?module='.$module;
$user_id = $logon->user_id;

$m = new AdminMenu( $user_id, $module, 10, $start, $sort );

if( !isset($_REQUEST['fltr']) )
{
	$arr = SysGroup::GetGrpToArr( $user_id, $module,  $logon->user_type );
	$fltr = $arr[0]['id'];
}
else 
	$fltr = $_REQUEST['fltr'];

$m->fltr = $fltr;

switch( $task )
{
 case 'show':
			  $m->show( $user_id, $module, $display, $start, $sort, $level );
			  break;
 case 'edit':
			  if( !isset($_REQUEST['id']) ) $id=NULL;
			  else $id=$_REQUEST['id'];
			  if (!$m->edit( $user_id, $module, $id, $level, NULL )) echo "<script>window.location.href='$scriptact&fltr=$fltr';</script>";
			  break;
 case 'new':
			  $m->edit( $user_id, $module, NULL, $level, NULL );
			  break;
 case 'save':
			  $res = $m->save( $user_id, $module, $id, $group, $level, $description, $function, $move );
			  if( $res )
			  {
				//$Msg->show_msg('_OK_SAVE');
				echo "<script>window.location.href='$scriptact&level=$level&display=$display&start=$start&sort=$sort&fltr=$fltr';</script>";
			  }
			  //else
			  //  $Msg->show_msg('_ERROR_SAVE');
			  break;
 case 'delete':
			  if(!isset($_REQUEST['id_del'])) $id_del=NULL;
			  else $id_del=$_REQUEST['id_del'];

			  $del = $m->del( $user_id, $module, $id_del );
			  if ( $del > 0 ) echo "<script>window.alert('Deleted OK! ($del records)');</script>";
			  else $Msg->show_msg('_ERROR_DELETE');

			  echo "<script>window.location.href='$scriptact&level=$level&task=show&fltr=$fltr';</script>";
			  break;
  case 'up':
			  $m->up_menu( $user_id, $module, $display, $start, $sort, $level, $move );
			  echo "<script>window.location.href='$scriptact&level=$level&task=show&fltr=$fltr';</script>";
			  break;
  case 'down':
			  $m->down_menu( $user_id, $module, $display, $start, $sort, $level, $move );
			  echo "<script>window.location.href='$scriptact&level=$level&task=show&fltr=$fltr';</script>";
			  break;
}
?>