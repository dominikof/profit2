<?php
/**
* stat_agent.php
* script for all actions agents for statictic
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

$logon = &check_init('logon','Authorization');;
if (!$logon->LoginCheck()) {
    //return false;
    ?><script>window.location.href="<?=$goto?>";</script><?; 
}
//=============================================================================================
// END
//=============================================================================================


if( !isset( $_REQUEST['task'] ) ) $task = 'show';
else $task = $_REQUEST['task'];

if( !isset( $_REQUEST['display'] ) ) $display = 10;
else $display = $_REQUEST['display'];

if( !isset( $_REQUEST['start'] ) ) $start = 0;
else $start = $_REQUEST['start'];

if( !isset( $_REQUEST['sort'] ) ) $sort = NULL;
else $sort = $_REQUEST['sort'];

if( !isset( $_REQUEST['fltr'] ) ) $fltr=NULL;
else $fltr = $_REQUEST['fltr'];


$m = new Agent($logon->user_id, $module);
$m->task = $task;
$m->display = $display;
$m->start = $start;
$m->sort = $sort;
$m->fltr = $fltr;


 if( !isset( $_REQUEST['id'] ) ) $m->id = NULL;
 else $m->id = $_REQUEST['id'];

 if( !isset( $_REQUEST['name'] ) ) $m->name = NULL;
 else $m->name = $_REQUEST['name'];

 if( !isset( $_REQUEST['comments'] ) ) $m->comments = NULL;
 else $m->comments = $_REQUEST['comments'];

 if( !isset( $_REQUEST['type'] ) ) $m->type = NULL;
 else $m->type = $_REQUEST['type'];

 if( !isset( $_REQUEST['status'] ) ) $m->status = NULL;
 else $m->status = $_REQUEST['status'];

 if( !isset( $_REQUEST['fln'] ) ) $m->fln = _LANG_ID;
 else $m->fln = $_REQUEST['fln'];

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
                       $del = $m->del( $id_del );
                       if ( $del > 0 ) echo "<script>window.alert('Deleted OK! ($del records)');</script>";
                       else $Msg->show_msg('_ERROR_DELETE');
                       echo "<script>window.location.href='$script';</script>";
                       break;

}

?>
