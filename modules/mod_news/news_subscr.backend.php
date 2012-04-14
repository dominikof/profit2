<?
// ================================================================================================
//    System     : SEOCMS
//    Module     : News
//    Version    : 2.0.0
//    Date       : 01.04.2007
//    Licensed To:
//                 Igor  Trokhymchuk  ihoru@mail.ru
//                 Andriy Lykhodid    las_zt@mail.ru
//
//    Purpose    : Class definition for control News Subscribers
//
// ================================================================================================
if (!defined("SITE_PATH")) define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] ); 
include_once( SITE_PATH.'/modules/mod_news/news.defines.php' );

if(!defined("_LANG_ID")) {$pg = new PageAdmin();} 

if( !isset( $_REQUEST['module'] ) ) $module = NULL;
else $module = $_REQUEST['module'];

//Blocking to execute a script from outside (not Admin-part) 
if ( !$pg->logon->isAccessToScript($module)) exit;

if( !isset( $_REQUEST['task'] ) ) $task = 'show_subscr';
else $task = $_REQUEST['task'];

if( !isset( $_REQUEST['display'] ) ) $display = 50;
else $display = $_REQUEST['display'];

if( !isset( $_REQUEST['start'] ) ) $start = 0;
else $start = $_REQUEST['start'];

if( !isset( $_REQUEST['sort'] ) ) $sort = 'id';
else $sort = $_REQUEST['sort'];

if( !isset( $_REQUEST['id'] ) ) $id = NULL;
else $id = $_REQUEST['id'];

if( !isset( $_REQUEST['fltr'] ) ) $fltr=NULL;
else $fltr = $_REQUEST['fltr'];

if( !isset( $_REQUEST['user_status'] ) ) $user_status = 1;
else $user_status = $_REQUEST['user_status'];

if( !isset( $_REQUEST['login'] ) ) $login = NULL;
else $login = $_REQUEST['login'];

if( !isset( $_REQUEST['pass'] ) ) $pass = NULL;
else $pass = $_REQUEST['pass'];

if( !isset( $_REQUEST['dt'] ) ) $dt = NULL;
else $dt = $_REQUEST['dt'];

if( !isset( $_REQUEST['is_send'] ) ) $is_send = NULL;
else $is_send = $_REQUEST['is_send'];

if( !isset( $_REQUEST['categories'] ) ) $categories = NULL;
else $categories = $_REQUEST['categories'];

if( !isset( $_REQUEST['id_del'] ) ) $id_del = NULL;
else $id_del = $_REQUEST['id_del'];


if( $task=='savereturn') {$task='save'; $action='return';}
else $action=NULL;

$m = new NewsCtrl($pg->logon->user_id, $module, 10, $sort, $start, '100%');
$m->module = $module;
$m->user_id = $pg->logon->user_id;
$m->task = $task;
$m->id = $id;
$m->display = $display;
$m->start = $start;
$m->sort = $sort;
$m->fltr = $fltr;
$m->user_status = $user_status;
$m->login = strip_tags(trim($login));
$m->pass = strip_tags(trim($pass));
$m->dt = strip_tags(trim($dt));
$m->is_send = $is_send;
$m->categories = $categories;

if( !isset( $_REQUEST['fln'] ) ) $m->fln = _LANG_ID;
else $m->fln = $_REQUEST['fln'];

$script = 'module='.$m->module.'&display='.$m->display.'&start='.$m->start.'&sort='.$m->sort.'&fltr='.$m->fltr;
$script = $_SERVER['PHP_SELF']."?$script";
$m->script = $script;

//phpinfo();
//echo "task=".$task."<br>";
switch( $task ) {
    case 'show_subscr':
        $m->ShowSubscribe();
        break;
    case 'edit_subscr':
        $m->EditSubscribe();
        break;
    case 'save_subscr':
        if( $m->CheckSubscr()!=NULL ) {
            $m->EditSubscribe();
            return false;
        }
        $m->SaveSubscribe();
        echo "<script>window.location.href='$script';</script>"; 
        break;
    case 'del_subscr':
        if( !isset($_REQUEST['id_del']) ) $id_del=NULL;
        else $id_del = $_REQUEST['id_del'];
        if ( !empty($id_del) ) {
           $del=$m->DelSubscribe($id_del);
           //if ( $del > 0 ) echo "<script>window.alert('".$pg->Msg->get_msg('_SYS_DELETED_OK')." $del');</script>";
           //else $pg->Msg->show_msg('_ERROR_DELETE');
           if ( $del == 0 ) $pg->Msg->show_msg('_ERROR_DELETE');
        }
        else $pg->Msg->show_msg('_ERROR_SELECT_FOR_DEL');
        echo "<script>window.location.href='$script';</script>";                    
        break;
    case 'cancel':  
        echo "<script>window.location.href='$script';</script>";
        break;        
}

?>