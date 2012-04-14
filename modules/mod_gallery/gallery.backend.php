<?
// ================================================================================================
//    System     : PrCSM05
//    Module     : Gallery
//    Version    : 1.0.0
//    Date       : 01.07.2010
//    Licensed To: Yaroslav Gyryn 
//    Purpose    : Class definition for Gallery - moule
// ================================================================================================
if (!defined("SITE_PATH")) define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] );
include_once( SITE_PATH.'/admin/include/defines.inc.php' );
include_once( SITE_PATH.'/modules/mod_gallery/gallery.defines.php' );

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
$logon = new  Authorization();
if (!$logon->LoginCheck()) {
    ?><script>window.location.href="<?=$goto?>";</script><?; 
}
//=============================================================================================
// END
//=============================================================================================

if( !isset( $_REQUEST['task'] ) ) $task = 'show';
else $task = $_REQUEST['task'];

if( !isset( $_REQUEST['display'] ) ) $display = 20;
else $display = $_REQUEST['display'];

if( !isset( $_REQUEST['start'] ) ) $start = 0;
else $start = $_REQUEST['start'];

if( !isset( $_REQUEST['sort'] ) ) $sort = NULL;
else $sort = $_REQUEST['sort'];

if( !isset( $_REQUEST['fltr'] ) ) $fltr=NULL;
else $fltr = $_REQUEST['fltr'];

 if( !isset( $_REQUEST['id'] ) ) $id = NULL;
 else $id = $_REQUEST['id'];

 if( !isset( $_REQUEST['dttm'] ) ) $dttm = NULL;
 else $dttm = $_REQUEST['dttm'];

 if( !isset( $_REQUEST['category'] ) ) $category = NULL;
 else $category = $_REQUEST['category'];

 if( !isset( $_REQUEST['status'] ) ) $status = NULL;
 else $status = $_REQUEST['status'];

 if( !isset( $_REQUEST['position'] ) ) $position = NULL;
 else $position = $_REQUEST['position'];

 if( !isset( $_REQUEST['name'] ) ) $name = NULL;
 else $name = $_REQUEST['name'];

 if( !isset( $_REQUEST['short'] ) ) $short = NULL;
 else $short = $_REQUEST['short'];

 if( !isset( $_REQUEST['full'] ) ) $full = NULL;
 else $full = $_REQUEST['full'];

if( !isset( $_REQUEST['sel'] ) ) $sel = NULL;
else $sel = $_REQUEST['sel'];


if( !isset( $_REQUEST['id_del'] ) ) $id_del = NULL;
else $id_del = $_REQUEST['id_del'];

if( !isset( $_REQUEST['move'] ) ) $move = NULL;
else $move = $_REQUEST['move'];

if( !isset( $_REQUEST['title'] ) ) $title = NULL;
else $title = $_REQUEST['title'];

if( !isset( $_REQUEST['keywords'] ) ) $keywords = NULL;
else $keywords = $_REQUEST['keywords'];

if( !isset( $_REQUEST['description'] ) ) $description = NULL;
else $description = $_REQUEST['description'];

if( !isset( $_REQUEST['translit'] ) ) $translit = NULL;
else $translit = $_REQUEST['translit']; 

if( !isset( $_REQUEST['translit_old'] ) ) $translit_old = NULL;
else $translit_old = $_REQUEST['translit_old']; 

if( !isset($_REQUEST['id_tag']) ) $id_tag=NULL;
else $id_tag = $_REQUEST['id_tag'];

if( isset($_REQUEST['saveimg']) ) $task='saveimg';
if( isset($_REQUEST['updimg']) ) $task='updimg'; 
if( isset($_REQUEST['delimg']) ) $task='delimg'; 
if( isset($_REQUEST['cancel']) ) $task='cancel';



 $gallery = new GalleryCtrl($logon->user_id, $module);
 $gallery->id = $id;
 $gallery->dttm = $dttm;
 $gallery->category = $category;
 $gallery->status = $status;
 $gallery->position = $position;
 $gallery->name = $name;
 $gallery->short = $short;
 $gallery->title = $title;
 $gallery->full = $full;

 $gallery->task = $task;
 $gallery->display = $display;
 $gallery->start = $start;
 $gallery->sort = $sort;
 $gallery->fltr = $fltr;
 $gallery->sel = $sel;
 $gallery->sel = $sel;
 $gallery->id_tag = $id_tag;
 $gallery->move=$move;
 $gallery->keywords = $keywords;
 $gallery->description = $description;   
 $gallery->translit=$translit;
 $gallery->translit_old=$translit_old;
 
 if( !isset( $_REQUEST['fln'] ) ) $gallery->fln = _LANG_ID;
 else $gallery->fln = $_REQUEST['fln'];

 $script = 'module='.$gallery->module.'&display='.$gallery->display.'&start='.$gallery->start.'&sort='.$gallery->sort.'&fltr='.$gallery->fltr;
 $script = $_SERVER['PHP_SELF']."?$script";

//echo "<br>task=".$task;
switch( $task ) {

    case 'show':      $gallery->show(); break;

    case 'new':     
    case 'edit':      $gallery->edit(); break;

    case 'save':        
        if ( $gallery->CheckFields()!=NULL ) {
           $gallery->edit();
           return false;
        }
        if ( $gallery->save() ){
            $gallery->UploadImages->SaveImages($gallery->id);
            echo "<script>window.location.href='$script';</script>";
        }
        break;

    case 'delete':
                       $del = $gallery->del( $id_del );
                       if( $del==0 ) $gallery->Msg->show_msg('_ERROR_DELETE');
                       //else echo "<script>window.alert('Deleted OK! ($del records)');</script>";
                       echo "<script>window.location.href='$script';</script>";
                       break;
    case 'cancel':
                       echo "<script>window.location.href='$script';</script>";
                       break;
    case 'up':
                    $gallery->up( $move );
                    echo "<script>window.location.href='$script';</script>";
                    break;

    case 'down':
                    $gallery->down( $move );
                    echo "<script>window.location.href='$script';</script>";
                    break;

    case 'preview':
                    $gallery->preview();
                    break;

}
?>