<?php
/**
* stat_report.php
* script for all actions with for see Statistic reports
* @package System Package of SEOCMS
* @author Igor Trokhymchuk  <ihor@seotm.com>
* @version 1.1, 02.04.2012
* @copyright (c) 2005+ by SEOTM
*/
if (!defined("SITE_PATH")) define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] );
include_once( SITE_PATH.'/admin/include/defines.inc.php' );


if( !isset( $_REQUEST['module'] ) ) $module = NULL;
else $module = $_REQUEST['module'];

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

$logon = &check_init('logon','Authorization');
if (!$logon->LoginCheck()) {
    //return false;
    ?><script>window.location.href="<?=$goto?>";</script><?; 
}
//=============================================================================================
// END
//=============================================================================================

$user_id = $logon->user_id;


if( !isset($_REQUEST['task']) ) $task = '';
else $task = $_REQUEST['task'];

if( !isset( $_REQUEST['display'] ) ) $display = 50;
else $display = $_REQUEST['display'];

if( !isset( $_REQUEST['start'] ) ) $start = 0;
else $start = $_REQUEST['start'];

if( !isset( $_REQUEST['sort'] ) ) $sort = 'dt desc';
else $sort = $_REQUEST['sort'];

if( !isset( $_REQUEST['fltr_dtfrom'] ) ) $fltr_dtfrom = date('Y-m-d');
else $fltr_dtfrom = $_REQUEST['fltr_dtfrom'];

if( !isset( $_REQUEST['fltr_dtto'] ) ) $fltr_dtto = date('Y-m-d');
else $fltr_dtto = $_REQUEST['fltr_dtto'];

if( !isset( $_REQUEST['type1'] ) ) $type1 = 'hit'; /* Hit */
else $type1 = $_REQUEST['type1'];

if( !isset( $_REQUEST['type2'] ) ) $type2 = 'd';   /* Day */
else $type2 = $_REQUEST['type2'];

if( !isset( $_REQUEST['type3'] ) ) $type3 = '';
else $type3 = $_REQUEST['type3'];

$script = 'index.php?module='.$module;

$stat = new StatRep($user_id,$module);
$stat->display = $display;
$stat->start = $start;
$stat->sort = $sort;

$stat->fltr_dtfrom = $fltr_dtfrom;
$stat->fltr_dtto = $fltr_dtto;
$stat->type1 = $type1;
$stat->type2 = $type2;
$stat->type3 = $type3;

switch( $task )
{

  case 'form':
              $stat->Form();
              break;

  case 'show':
              $stat->Form();
              $stat->Show();
              break;

  default:
           $stat->Form();
           $stat->Statistic();
           break;

}
?>
