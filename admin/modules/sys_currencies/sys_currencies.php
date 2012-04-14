<?php                                     /* sys_currencies.php */
// ================================================================================================
// System : SEOCMS
// Module : sys_currencies.php
// Version : 1.0.0
// Date : 26.09.2007
// Licensed To:
// Igor Trokhymchuk ihoru@mail.ru
// Andriy Lykhodid las_zt@mail.ru
//
// Purpose : script for all actions with currencies
//
// ================================================================================================
if (!defined("SITE_PATH")) define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] ); 
include_once( SITE_PATH.'/admin/include/defines.inc.php' ); 
include_once( SITE_PATH.'/admin/modules/sys_currencies/sys_currencies.class.php' );

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

$logon =&check_init('logon','Authorization');  
/*if (!$logon->LoginCheck()) {
	//return false;
	?><script>window.location.href="<?=$goto?>";</script><?; 
} */
if ( ! defined('BASEPATH')) {
	//return false;
	?><script>window.location.href="<?=$goto?>";</script><?; 
//    exit('No direct script access allowed');
}
//=============================================================================================
// END
//=============================================================================================
$fln = AntiHacker::AntiHackRequest('fln',_LANG_ID);
$task = AntiHacker::AntiHackRequest('task','show');
$fltr = AntiHacker::AntiHackRequest('fltr');
$fltr2 = AntiHacker::AntiHackRequest('fltr2');
$fltr3 = AntiHacker::AntiHackRequest('fltr3');
$srch = AntiHacker::AntiHackRequest('srch');
$sort = AntiHacker::AntiHackRequest('sort');
$asc_desc = AntiHacker::AntiHackRequest('asc_desc','asc');
$start = AntiHacker::AntiHackRequest('start',0);
$display = AntiHacker::AntiHackRequest('display',20);
$id = AntiHacker::AntiHackRequest('id');
$name = AntiHacker::AntiHackArrayRequest('name');
$short = AntiHacker::AntiHackArrayRequest('short');
$prefix = AntiHacker::AntiHackArrayRequest('prefix');
$sufix = AntiHacker::AntiHackArrayRequest('sufix');
$value = AntiHacker::AntiHackRequest('value');
$cashless = AntiHacker::AntiHackRequest('cashless');
if( isset($_REQUEST['is_default']) ) $is_default = 1;
else $is_default = 0; 
$move = AntiHacker::AntiHackRequest('move');
$visible = AntiHacker::AntiHackRequest('visible',2);

 $Obj = new SysCurrencies($logon->user_id, $module, $display, $sort, $start);
 $Obj->user_id = $logon->user_id;
 $Obj->module = $module;
 $Obj->display = $display;
 $Obj->sort = $sort;
 $Obj->asc_desc = $asc_desc;
 $Obj->start = $start;
 $Obj->fln = $fln;
 $Obj->srch = $srch;
 $Obj->fltr = $fltr;
 $Obj->fltr2 = $fltr2;  
 $Obj->fltr3 = $fltr3; 
 
 $Obj->id = $id;
 $Obj->name = $name;
 $Obj->short = $short; 
 $Obj->prefix = $prefix;
 $Obj->sufix = $sufix;
 $Obj->value = addslashes($value);
 $Obj->cashless = addslashes($cashless);
 $Obj->is_default = $is_default;
 $Obj->move = $move; 
 $Obj->visible = $visible;
 
 //echo '<br>$Catalog->id='.$Catalog->id;
 //echo '<br> $task='.$task;
 $Obj->script=$_SERVER['PHP_SELF']."?module=$Obj->module&display=$Obj->display&start=$Obj->start&sort=$Obj->sort&fltr=$Obj->fltr&fltr2=$Obj->fltr2&srch=$Obj->srch&id=$Obj->id";
 switch( $task ) {
	case 'show':    
		$Obj->Show();
		break;
	case 'edit':
		if (!$Obj->Edit()) echo "<script>window.location.href='$Obj->script';</script>";
		break;
	case 'new':       
		$Obj->id =NULL;
		$Obj->Edit();
		break;
	case 'save':
		//phpinfo(); 
		if ( $Obj->CheckFields()!=NULL ) {
		   $Obj->Edit();
		   return false;
		}
		$res = $Obj->Save();
		if ( $res ){
			//$Obj->Show();
			echo "<script>window.location.href='$Obj->script';</script>";
		}
		else echo '<br>'.$Msg->show_text('FLD_ERROR');
		break;
	case 'delete':
		if( !isset($_REQUEST['id_del']) ) $id_del=NULL;
		else $id_del = $_REQUEST['id_del'];
		if ( !empty($id_del) ) {
		   $del=$Obj->Del( $id_del );
		   if ( $del > 0 ) echo "<script>window.alert('".$Msg->get_msg('_SYS_DELETED_OK')." $del');</script>";
		   else $Msg->show_msg('_ERROR_DELETE');
		}
		else $Msg->show_msg('_ERROR_SELECT_FOR_DEL');
		//$Catalog->ShowContent();
		echo "<script>window.location.href='$Obj->script';</script>";
		break;
	case 'cancel':
		echo "<script>window.location.href='$Obj->script';</script>";
		break;
	case 'up':
		$Obj->up(TblSysCurrencies);
		echo "<script>window.location.href='$Obj->script';</script>";
		break;

	case 'down':
		$Obj->down(TblSysCurrencies);
		echo "<script>window.location.href='$Obj->script';</script>";
		break;
 }
?>