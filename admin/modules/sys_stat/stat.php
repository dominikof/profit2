<?php
/**
* stat.php
* script for all actions with Statictic module
* @package System Package of SEOCMS
* @author Igor Trokhymchuk  <ihor@seotm.com>
* @version 1.1, 02.04.2012
* @copyright (c) 2005+ by SEOTM
*/
if (!defined("SITE_PATH")) define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] );
include_once( SITE_PATH.'/admin/include/defines.inc.php' );
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

$logon = &check_init('logon','Authorization');
if (!$logon->LoginCheck()) {
	//return false;
	?><script>window.location.href="<?=$goto?>";</script><?; 
}
//=============================================================================================
// END
//=============================================================================================

$user_id = $logon->user_id;

if( !isset($_REQUEST['task']) ) $task = 'show';
else $task = $_REQUEST['task'];

if( !isset( $_REQUEST['display'] ) ) $display = 50;
else $display = $_REQUEST['display'];

if( !isset( $_REQUEST['start'] ) ) $start = 0;
else $start = $_REQUEST['start'];

if( !isset( $_REQUEST['sort'] ) ) $sort = NULL;
else $sort = $_REQUEST['sort'];

if( !isset( $_REQUEST['fltr'] ) ) $fltr = NULL;
else $fltr = $_REQUEST['fltr'];

if( !isset( $_REQUEST['fltr_dtfrom'] ) ) $fltr_dtfrom = date('Y-m-d');
else $fltr_dtfrom = $_REQUEST['fltr_dtfrom'];

if( !isset( $_REQUEST['fltr_dtto'] ) ) $fltr_dtto = date('Y-m-d');
else $fltr_dtto = $_REQUEST['fltr_dtto'];

if( !isset( $_REQUEST['sel'] ) ) $sel = NULL;
else $sel = $_REQUEST['sel'];

if( !isset( $_REQUEST['fltr_user'] ) ) $fltr_user = NULL;
else $fltr_user = $_REQUEST['fltr_user'];

$script = 'index.php?module='.$module;

$stat = new StatCtrl($user_id, $module);
$stat->display = $display;
$stat->start = $start;
$stat->sort = $sort;

if($fltr == 'front') $stat->fltr = 1;
elseif($fltr == 'back') $stat->fltr = 0;
else $stat->fltr = $fltr;

$stat->sel = $sel;

if( $fltr_dtfrom == NULL ) $fltr_dtfrom = $stat->GetStartDate();
if( $fltr_dtto == NULL ) $fltr_dtto = $stat->GetEndDate();

$stat->fltr_dtfrom = $fltr_dtfrom;
$stat->fltr_dtto = $fltr_dtto;
$stat->fltr_user = $fltr_user;

switch( $task )
{
  case 'show':
			  $stat->StatShow();
			  break;
  case 'save':
			  $stat->StatSave();
			  echo "<script>window.location.href='$script';</script>";
			  break;
  case 'delete':
			  if( !isset( $_REQUEST['id_del'] ) ) $id_del = NULL;
			  else $id_del = $_REQUEST['id_del'];
			  $del = $stat->Del( $id_del );
			  if ( $del > 0 ) echo "<script>window.alert('Deleted OK! ($del records)');</script>";
			  else $Msg->show_msg('_ERROR_DELETE');
			  echo "<script>window.location.href='$script';</script>";
			  break;

  default:
		   $stat->StatShow();
		   break;
}

?>
