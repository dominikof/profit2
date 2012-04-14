<?php
/**
* sys_set.php
* script for all actions with system settings
* @package System Package of SEOCMS
* @author Igor Trokhymchuk  <ihor@seotm.com>
* @version 1.1, 02.04.2012
* @copyright (c) 2005+ by SEOTM
*/
if (!defined("SITE_PATH")) define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] );
include_once( SITE_PATH.'/admin/include/defines.inc.php' );
include_once( SITE_PATH.'/admin/modules/sys_set/sys_set.class.php' );
include_once( SITE_PATH.'/admin/include/defines.inc.php' );

 $module = AntiHacker::AntiHackRequest('module');   
//============================================================================================
// START
// Blocking to execute a script from outside (not Admin-part) 
//============================================================================================
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
//echo '<br /><br /><br /><br /><br /><br /><br />';
//=============================================================================================
// END
//=============================================================================================

$task = AntiHacker::AntiHackRequest('task','show'); 
$fln = AntiHacker::AntiHackRequest('fln',_LANG_ID); 
$fltr = AntiHacker::AntiHackRequest('fltr'); 
$fltr2 = AntiHacker::AntiHackRequest('fltr2'); 
$fltr3 = AntiHacker::AntiHackRequest('fltr3'); 
$srch = AntiHacker::AntiHackRequest('srch'); 
$sort = AntiHacker::AntiHackRequest('sort'); 
$asc_desc = AntiHacker::AntiHackRequest('asc_desc','asc'); 
$start = AntiHacker::AntiHackRequest('start',0); 
$display = AntiHacker::AntiHackRequest('display',20); 

// read self parameters
if( !isset($_REQUEST['mail_smtp_auth']) ) $mail_smtp_auth=0;
else $mail_smtp_auth = 1;
if( !isset($_REQUEST['mail_is_html']) ) $mail_is_html=0;
else $mail_is_html = 1;

$id = AntiHacker::AntiHackRequest('id'); 
$mail_host = AntiHacker::AntiHackRequest('mail_host'); 
$mail_port = AntiHacker::AntiHackRequest('mail_port'); 
$mail_mailer = AntiHacker::AntiHackRequest('mail_mailer'); 
$mail_username = AntiHacker::AntiHackRequest('mail_username'); 
$mail_password = AntiHacker::AntiHackRequestPass('mail_password'); 
$mail_from = AntiHacker::AntiHackRequest('mail_from'); 
$mail_from_name = AntiHacker::AntiHackArrayRequest('mail_from_name'); 
$mail_header = AntiHacker::AntiHackArrayRequest('mail_header'); 
$mail_footer = AntiHacker::AntiHackArrayRequest('mail_footer'); 
$mail_word_wrap = AntiHacker::AntiHackRequest('mail_word_wrap'); 
$mail_priority = AntiHacker::AntiHackRequest('mail_priority'); 
$mail_charset = AntiHacker::AntiHackRequest('mail_charset'); 
$mail_encoding = AntiHacker::AntiHackRequest('mail_encoding'); 
$mail_auto_emails = AntiHacker::AntiHackRequest('mail_auto_emails'); 
$mail_admin_email = AntiHacker::AntiHackRequest('mail_admin_email'); 

$editer = AntiHacker::AntiHackRequest('editer'); 


 $Obj = new SysSettingsAdm($logon->user_id, $module, $display, $sort, $start, '100%');
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
 $Obj->mail_host = addslashes($mail_host);
 $Obj->mail_port = addslashes($mail_port);
 $Obj->mail_mailer = addslashes($mail_mailer);
 $Obj->mail_smtp_auth = $mail_smtp_auth;
 $Obj->mail_username = addslashes($mail_username);
 $Obj->mail_password = addslashes($mail_password);
 $Obj->mail_from = addslashes($mail_from);
 $Obj->mail_from_name = $mail_from_name; 
 $Obj->mail_header = $mail_header; 
 $Obj->mail_footer = $mail_footer; 
 $Obj->mail_word_wrap = addslashes($mail_word_wrap); 
 $Obj->mail_is_html = $mail_is_html;
 $Obj->mail_priority = addslashes($mail_priority);
 $Obj->mail_charset = addslashes($mail_charset);
 $Obj->mail_encoding = addslashes($mail_encoding);
 $Obj->mail_auto_emails = addslashes($mail_auto_emails);
 $Obj->mail_admin_email = addslashes($mail_admin_email);
 $Obj->editer=$editer;
 
 //echo '<br>$Catalog->id='.$Catalog->id;
 //echo '<br> $task='.$task;
 $Obj->script=$_SERVER['PHP_SELF']."?module=$Obj->module";
 
 switch( $task ) {
	case 'show':    
		$Obj->Show();
		break;
	case 'save':
		$res = $Obj->Save();
		if ( $res ){
			?><div class="warning"><?=$Obj->Msg['_OK_SAVE'];?></div><?
			$Obj->Show($res);
//            echo "<script>window.location.href='$Obj->script';</script>";
		}
		else echo '<br>'.$Obj->Msg['FLD_ERROR'];
		break;
 }

?>
