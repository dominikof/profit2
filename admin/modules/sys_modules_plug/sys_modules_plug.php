<?
// ================================================================================================
//    System     : SEOCMS
//    Module     : ModulesPlug
//    Version    : 1.0.0
//    Date       : 02.02.2005
//    Licensed To:
//                 Igor  Trokhymchuk  ihoru@mail.ru
//                 Andriy Lykhodid    las_zt@mail.ru
//
//    Purpose    : Class definition for Modules Plug of System
// ================================================================================================
if (!defined("SITE_PATH")) define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] );
include_once( SITE_PATH.'/admin/include/defines.inc.php' ); 
include_once( SITE_PATH.'/admin/modules/sys_modules_plug/sys_modules_plag.class.php' );

if( !isset( $_REQUEST['module'] ) ) $module = NULL;
else $module = $_REQUEST['module'];

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

$logon = new  Authorization();
if (!$logon->LoginCheck()) {
	//return false;
	?><script>window.location.href="<?=$goto?>";</script><?; 
}
//=============================================================================================
// END
//=============================================================================================

$task = AntiHacker::AntiHackRequest('task','show');
$display = AntiHacker::AntiHackRequest('display',10);
$start = AntiHacker::AntiHackRequest('start',0);
$sort = AntiHacker::AntiHackRequest('sort');


 $m = new ModulesPlug();
 $m->module = $module;
 $m->user_id = $logon->user_id;
 $m->task = $task;
 $m->display = $display;
 $m->start = $start;
 $m->sort = $sort;

 $script = 'index.php?module='.$module;

switch( $task )
{
 case 'show':
			  $m->show();
			  break;
 case 'edit':
			  if( !$m->edit( $_REQUEST['id'], NULL ) ) echo "<script>window.location.href='$script';</script>";
			  break;
 case 'new':
			  if( !$m->edit( NULL, NULL ) ) echo "<script>window.location.href='$script';</script>";
			  break;
 case 'save':
			  if( $m->save( $_REQUEST['id'], $_REQUEST['sys_func'], $_REQUEST['plugin'], $_REQUEST['maintenance'], $_REQUEST['forlogon'], $_REQUEST['layout'] ) )
			  {
				   echo "<script>window.location.href='$script';</script>";
			  }
			  break;

 case 'delete':
			  $del = $m->del( $_REQUEST['id_del'] );
			  if ( $del > 0 ) echo "<script>window.alert('Deleted OK! ($del records)');</script>";
			  else $Msg->show_msg('_ERROR_DELETE');

			  echo "<script>window.location.href='$script';</script>";

			  break;
}
?>