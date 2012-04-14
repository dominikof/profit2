<?php
/**
* sys_comments.php
* script for all actions with control of users comments
* @package System Package of SEOCMS
* @author Igor Trokhymchuk  <ihor@seotm.com>
* @version 1.1, 02.04.2012
* @copyright (c) 2005+ by SEOTM
*/

if (!defined("SITE_PATH")) define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] );
include_once( SITE_PATH.'/admin/include/defines.inc.php' );
include_once( SITE_PATH.'/admin/modules/sys_comments/sys_comments.class.php' );

if (!defined("_LANG_ID")) $Page = new PageAdmin();

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
$logon = check_init('logon','Authorization');
if (!$logon->LoginCheck()) {
	//return false;
	?><script>window.location.href="<?=$goto?>";</script><?; 
}
//=============================================================================================
// END
//=============================================================================================

//if (!defined("_LANG_ID")) define("_LANG_ID", DEBUG_LANG);
   
   
if( !isset($_REQUEST['task']) || empty($_REQUEST['task']) ) $task='show';
else $task=$_REQUEST['task'];

$fln = AntiHacker::AntiHackRequest('fln',_LANG_ID);
$fltr = AntiHacker::AntiHackRequest('fltr');
 
$fltr2 = AntiHacker::AntiHackRequest('fltr2');
$srch = AntiHacker::AntiHackRequest('srch');
$srch2 = AntiHacker::AntiHackRequest('srch2');
$sort = AntiHacker::AntiHackRequest('sort');
$start = AntiHacker::AntiHackRequest('start',0);
$display = AntiHacker::AntiHackRequest('display',20);
$id = AntiHacker::AntiHackRequest('id');
$id_module = AntiHacker::AntiHackRequest('id_module');
$id_item = AntiHacker::AntiHackRequest('id_item');
$dt = AntiHacker::AntiHackRequest('dt'); 
$status = AntiHacker::AntiHackRequest('status');
$text = AntiHacker::AntiHackRequest('text');
$id_user = AntiHacker::AntiHackRequest('id_user');
$name = AntiHacker::AntiHackRequest('name');
$email = AntiHacker::AntiHackRequest('email');
$status = AntiHacker::AntiHackRequest('status');

$Obj = new CommentsCtrl($logon->user_id, $module, 10, $sort, $start, '100%');
$Obj->user_id = $logon->user_id;
$Obj->module = $module;
$Obj->display = $display;
$Obj->sort = $sort;
$Obj->start = $start;
$Obj->fln = $fln;
$Obj->srch = $srch;
$Obj->srch2 = $srch2;
$Obj->fltr = $fltr;
$Obj->fltr2 = $fltr2;

$Obj->id=$id; 
$Obj->id_module=$id_module;
$Obj->id_item=$id_item;
$Obj->dt=$dt;
$Obj->status=$status;
$Obj->text=$text;
$Obj->id_user=$id_user;
$Obj->name=$name;
$Obj->email=$email;
$Obj->status=$status;

$Obj->script_ajax="module=$Obj->module&display=$Obj->display&start=$Obj->start&sort=$Obj->sort&fltr=$Obj->fltr&fltr2=$Obj->fltr2&srch=$Obj->srch&srch2=$Obj->srch2";
$Obj->script = "index.php?".$Obj->script_ajax;
switch( $task ) {
	case 'show':      
		$Obj->show();
		break;
	case 'edit':
		if (!$Obj->edit()) echo "<script>window.location.href='$Obj->script';</script>";
		break;
	case 'new':       
		$Obj->edit();
		break;
   case 'ch_stat':       
		$Obj->change_stat();
		break;
	case 'save':
		if ( $Obj->CheckFields()!=NULL ) {
		  $Obj->edit();
		  return false;
		}
		if ( $Obj->save() ){
			echo "<script>window.location.href='$Obj->script';</script>";
		}
		break;
	case 'delete':
		if(!isset($_REQUEST['id_del'])) $id_del=NULL;
        else $id_del=$_REQUEST['id_del'];
		if ( !empty($id_del) ) {
		 $del=$Obj->del( $id_del );
		 if ( $del > 0 ) echo "<script>window.alert('".$Msg->get_msg('_SYS_DELETED_OK')." $del');</script>";
		 else $msg->show_msg('_ERROR_DELETE');
		}
		else $Msg->show_msg('_ERROR_SELECT_FOR_DEL');
		echo "<script>window.location.href='$Obj->script';</script>";
		break;
	case 'cancel':
		echo "<script>window.location.href='$Obj->script';</script>";
		break;
}
?>