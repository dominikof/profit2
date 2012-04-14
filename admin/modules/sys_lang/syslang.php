<?
/**
* syslang.php
* script for all actions with languages in SEOCMS
* @package System Package of SEOCMS
* @author Igor Trokhymchuk  <ihor@seotm.com>
* @version 1.1, 02.04.2012
* @copyright (c) 2005+ by SEOTM
*/

if (!defined("SITE_PATH")) define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] );
include_once( SITE_PATH.'/admin/include/defines.inc.php' ); 
include_once( SITE_PATH.'/admin/modules/sys_lang/syslang.defines.php' );

$module = AntiHacker::AntiHackRequest('module');
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
$display = AntiHacker::AntiHackRequest('display',10);
$start = AntiHacker::AntiHackRequest('start',0);
$sort = AntiHacker::AntiHackRequest('sort');
$fltr = AntiHacker::AntiHackRequest('fltr');

$m = new SysLangCtrl($logon->user_id, $module);
$m->task = $task;
$m->display = $display;
$m->start = $start;
$m->sort = $sort;
$m->fltr = $fltr;

$m->id = AntiHacker::AntiHackRequest('id');
$m->cod = AntiHacker::AntiHackRequest('cod');

if( !isset( $_REQUEST['short_name'] ) ) $m->short_name = NULL;
else $m->short_name = $_REQUEST['short_name'];

if( !isset( $_REQUEST['front'] ) ) $m->front = NULL;
else $m->front = $_REQUEST['front'];

if( !isset( $_REQUEST['back'] ) ) $m->back = NULL;
else $m->back = $_REQUEST['back'];

if( !isset( $_REQUEST['def_front'] ) ) $m->def_front = NULL;
else $m->def_front = $_REQUEST['def_front'];

if( !isset( $_REQUEST['def_back'] ) ) $m->def_back = NULL;
else $m->def_back = $_REQUEST['def_back'];

if( !isset( $_REQUEST['encoding'] ) ) $m->encoding = NULL;
else $m->encoding = $_REQUEST['encoding'];

if( !isset( $_REQUEST['lang_img'] ) ) $m->lang_img = NULL;
else $m->lang_img = $_REQUEST['lang_img'];

$script = 'module='.$m->module.'&display='.$m->display.'&start='.$m->start.'&sort='.$m->sort.'&fltr='.$m->fltr;
$script = $_SERVER['PHP_SELF']."?$script";
switch( $task ) {

	case 'show':      $m->show(); break;

	case 'new':       $m->edit( NULL, NULL ); break;

	case 'edit':      $m->edit(); break;

	case 'save':
					  if ( $m->save() )
					  {
						echo "<script>window.location.href='$script';</script>";
					  }
					  break;

	case 'delete':
					   if(!isset($_REQUEST['id_del'])) $id_del=NULL;
			           else $id_del=$_REQUEST['id_del'];
                       $del = $m->del( $id_del );
					   if ( $del > 0 ) echo "<script>window.alert('Deleted OK! ($del records)');</script>";
					   else $Msg->show_msg('_ERROR_DELETE');
					   echo "<script>window.location.href='$script';</script>";
					   break;

}?>