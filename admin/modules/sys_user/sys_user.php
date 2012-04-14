<?php
/**
* sys_user.php
* Script for all action with control of system users
* @package System Package of SEOCMS
* @author Igor Trokhymchuk  <ihor@seotm.com>
* @version 1.1, 02.04.2012
* @copyright (c) 2005+ by SEOTM
*/
if (!defined("SITE_PATH")) define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] );
include_once( SITE_PATH.'/admin/include/defines.inc.php' );
include_once( SITE_PATH.'/admin/modules/sys_user/sys_user.class.php' );

if(!defined("_LANG_ID")) {session_start(); $pg = new PageAdmin();} 

$module = AntiHacker::AntiHackRequest('module'); 

//Blocking to execute a script from outside (not Admin-part) 
if ( !$pg->logon->isAccessToScript($module)) exit;
 
 /*
//============================================================================================
// START
// Blocking to execute a script from outside (not Admin-part) 
//============================================================================================
$msg = new ShowMsg(_LANG_ID);
$goto = "http://".NAME_SERVER."/admin/index.php?logout=1";
//echo '<br>$goto='.$goto;
if ( ! defined('BASEPATH')) {
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
*/

$task=AntiHacker::AntiHackRequest('task','show');
if( isset($_REQUEST['change_pass']) AND $task!='cancel' ) $task = 'change_pass';

$display = AntiHacker::AntiHackRequest('display','20');
$fltr = AntiHacker::AntiHackRequest('fltr');

$id = AntiHacker::AntiHackRequest('id');
$group_id = AntiHacker::AntiHackRequest('group_id');

$login = AntiHacker::AntiHackRequest('login');
$pass = AntiHacker::AntiHackRequestPass('pass');
$confirm_pass = AntiHacker::AntiHackRequestPass('confirm_pass');
$enrol_date = AntiHacker::AntiHackRequest('enrol_date');
$change_pass = AntiHacker::AntiHackRequest('change_pass');
$login_multi_use = AntiHacker::AntiHackRequest('login_multi_use');
$fltr_user = AntiHacker::AntiHackRequest('fltr_user');
$alias = AntiHacker::AntiHackRequest('alias');
$old_login = AntiHacker::AntiHackRequest('old_login');
$old_alias = AntiHacker::AntiHackRequest('old_alias');
$check_up = AntiHacker::AntiHackRequest('check_up');

$sys_user->display = AntiHacker::AntiHackRequest('display',20);  
$sys_user = new UserBackend($pg->logon->user_id, $module,$sys_user->display);
$sys_user->module = $module;

$sys_user->srch = AntiHacker::AntiHackRequest('srch');
$sys_user->fltr2 = AntiHacker::AntiHackRequest('fltr2');
$sys_user->srch_dtfrom = AntiHacker::AntiHackRequest('srch_dtfrom');
$sys_user->srch_dtto = AntiHacker::AntiHackRequest('srch_dtto');

$sys_user->start = AntiHacker::AntiHackRequest('start',0);
$sys_user->sort = AntiHacker::AntiHackRequest('sort');
$sys_user->fltr=$fltr;
$sys_user->fltr_user = $fltr_user; 
$sys_user->task = $task;
$sys_user->id = $id;
$sys_user->group_id = $group_id; 
$sys_user->login = addslashes(strip_tags($login));
$sys_user->pass = $pass;
$sys_user->confirm_pass = $confirm_pass;
$sys_user->change_pass = $change_pass;
$sys_user->login_multi_use=$login_multi_use;
$sys_user->enrol_date=addslashes(strip_tags($enrol_date));
$sys_user->alias=addslashes(strip_tags($alias));
$sys_user->old_login = addslashes(strip_tags($old_login));
$sys_user->old_alias = addslashes(strip_tags($old_alias));

$sys_user->script_ajax = "module=$sys_user->module&display=$sys_user->display&start=$sys_user->start&sort=$sys_user->sort&fltr=$sys_user->fltr&srch=$sys_user->srch&fltr2=$sys_user->fltr2&srch_dtfrom=$sys_user->srch_dtfrom&srch_dtto=$sys_user->srch_dtto";
$sys_user->script="index.php?".$sys_user->script_ajax;

//echo '<br> $sys_user->task='.$sys_user->task; 
switch( $sys_user->task ){
	case 'show':
		$sys_user->show( $logon->user_id, $module, $display );
		break;
	case 'edit':
		if (!$sys_user->edit()) echo "<script>window.location.href='$sys_user->script';</script>";
		break;
	case 'new':
		$sys_user->edit( $logon->user_id, $module, NULL, NULL );
		break;
	case 'newpass':
		if ( !$sys_user->change_pass_form() ){
			echo "<script>window.location.href='$sys_user->script';</script>";
		}
		break;              
	case 'change_pass':
		if( $sys_user->CheckPassFieldsSysUser($sys_user->login, $sys_user->pass, $sys_user->confirm_pass)!=NULL){
			$sys_user->change_pass_form();
			return false;
		} 
		if ( !$sys_user->change_pass( $login, $pass) ) $msg->show_msg('_ERROR_SAVE');
		echo "<script>window.location.href='$sys_user->script';</script>";
		break;
	case 'save':
		if ( $sys_user->CheckFields()!=NULL ){
		   $sys_user->edit();
		   return false;
		}
		if (!$sys_user->save()) $msg->show_msg('_ERROR_SAVE');
		else echo "<script>window.location.href='$sys_user->script';</script>";
		break;
	case 'delete':
		if( !isset($_REQUEST['id_del']) ) $id_del=NULL;
		else $id_del = $_REQUEST['id_del'];
		if ( !empty($id_del) ) {
			$del=$sys_user->del( $id_del );
			if ( $del > 0 ) echo "<script>window.alert('".$sys_user->Msg_text['_SYS_DELETED_OK']." $del');</script>";
			else $msg->show_msg('_ERROR_DELETE');
		}
		else $msg->show_msg('_ERROR_SELECT_FOR_DEL');
		echo "<script>window.location.href='$sys_user->script';</script>";
		break;
	case 'cancel':
		echo "<script>window.location.href='$sys_user->script';</script>";
		break;
	case 'show_stat':
		$sys_user->ShowStatByUserId($logon->user_id);
		break;
	case 'login_checkup':
		//echo '<br>$make_check='.$make_check;
		if( empty($sys_user->login) ){
			?><span style="font-size:10px; color:red;"><?=$sys_user->Msg->show_text('_EMPTY_LOGIN_FIELD', TblBackMulti);?></span><?
			return false;
		}
		if( $sys_user->old_login!=$sys_user->login OR $check_up ){
			if( !$sys_user->unique_login($sys_user->login) ) {
				?><span style="font-size:10px; color:red;"><?=$sys_user->Msg->show_text('MSG_LOGIN_EXIST', TblBackMulti);?></span><?
			}
			else {?><span style="font-size:10px; color:green;"><?=$sys_user->Msg->show_text('MSG_LOGIN_FREE',TblBackMulti);?></span><?}
		}
		break;
	case 'alias_checkup':
		//echo '<br>$make_check='.$make_check;
		if( empty($sys_user->login) ){
			?><span style="font-size:10px; color:red;"><?=$sys_user->Msg->show_text('_EMPTY_ALIAS_FIELD', TblBackMulti);?></span><?
			return false;
		}
		if( $sys_user->old_alias!=$sys_user->alias OR $check_up ){
			if( !$sys_user->unique_alias($sys_user->alias) ) {
				?><span style="font-size:10px; color:red;"><?=$sys_user->Msg->show_text('MSG_LOGIN_EXIST', TblBackMulti);?></span><?
			}
			else {?><span style="font-size:10px; color:green;"><?=$sys_user->Msg->show_text('MSG_LOGIN_FREE', TblBackMulti);?></span><?}
		}
		break;
	default:
		$sys_user->show( $logon->user_id, $module, $display, $sort, $start );
		break;                                
}


?>