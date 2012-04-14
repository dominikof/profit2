<?
/**
* articles.backend.php  
* script for all actions with articles
* @package Articles Package of SEOCMS
* @author Igor Trokhymchuk  <ihor@seotm.com>
* @version 1.1, 24.02.2011
* @copyright (c) 2010+ by SEOTM
*/
if (!defined("SITE_PATH")) define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] );
include_once( SITE_PATH.'/admin/include/defines.inc.php' );
include_once( SITE_PATH.'/modules/mod_article/article.defines.php' );

if(!defined("_LANG_ID")) {$pg = new PageAdmin();} 

if( !isset( $_REQUEST['module'] ) ) $module = NULL;
else $module = $_REQUEST['module'];

//Blocking to execute a script from outside (not Admin-part) 
if ( !$pg->logon->isAccessToScript($module)) exit;

$Article = new ArticleCtrl($pg->logon->user_id, $module);

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

if( !isset( $_REQUEST['img'] ) ) $img=NULL;
else $img = $_REQUEST['img'];

if( !isset( $_REQUEST['id_img'] ) ) $id_img = NULL;
else $id_img = $_REQUEST['id_img'];

if( !isset( $_REQUEST['image'] ) ) $image = NULL;
else $image = $_REQUEST['image'];

if( !isset( $_REQUEST['img_title'] ) ) $img_title = NULL;
else $img_title = $_REQUEST['img_title'];

if( !isset( $_REQUEST['img_descr'] ) ) $img_descr = NULL;
else $img_descr = $_REQUEST['img_descr'];

if( !isset( $_REQUEST['id_img_show'] ) ) $img_show = NULL;
else $img_show = $_REQUEST['id_img_show'];

if( !isset( $_REQUEST['id_img_show'] ) ) $img_show = NULL;
else $img_show = $_REQUEST['id_img_show'];

if( !isset( $_REQUEST['id_img_show'] ) ) $id_img_show = NULL;
else $id_img_show = $_REQUEST['id_img_show'];

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

if( !isset( $_REQUEST['id_department'] ) ) $Article->id_department = NULL;
else $Article->id_department = $_REQUEST['id_department'];


if( isset($_REQUEST['saveimg']) ) $task='saveimg';
if( isset($_REQUEST['updimg']) ) $task='updimg'; 
if( isset($_REQUEST['delimg']) ) $task='delimg'; 
if( isset($_REQUEST['cancel']) ) $task='cancel';

if( $task=='savereturn') {$task='save'; $action='return';}
else $action=NULL;

 
 $Article->id = $id;
 $Article->dttm = $dttm;
 $Article->category = $category;
 $Article->status = $status;
 $Article->position = $position;
 $Article->name = $name;
 $Article->short = $short;
 $Article->title = $title;
 $Article->full = $full;

 $Article->task = $task;
 $Article->display = $display;
 $Article->start = $start;
 $Article->sort = $sort;
 $Article->fltr = $fltr;
 $Article->sel = $sel;
 $Article->img = $img;
 $Article->sel = $sel;
 $Article->image = $image;
 $Article->id_img = $id_img;
 $Article->img_title = $img_title;  
 $Article->img_descr = $img_descr; 
 $Article->img_show = $id_img_show; 
 $Article->move=$move;
 $Article->img_show = $img_show;
 $Article->keywords = $keywords;
 $Article->description = $description;   

 if( !isset( $_REQUEST['fln'] ) ) $Article->fln = _LANG_ID;
 else $Article->fln = $_REQUEST['fln'];

$script = 'module='.$Article->module.'&display='.$Article->display.'&start='.$Article->start.'&sort='.$Article->sort.'&fltr='.$Article->fltr;
$script = $_SERVER['PHP_SELF']."?$script";
$Article->script = $script;

//echo "<br>task=".$task;
//phpinfo();
 

switch( $task ) {

    case 'show':      $Article->show(); break;

    case 'new':       $Article->edit(); break;

    case 'edit':      $Article->edit(); break;

    case 'save':
        if ( $Article->CheckFields()!=NULL ) {
           $Article->edit();
           return false;
        }
        if ($Article->save())
        {
          if( $action=='return' ) echo "<script>window.location.href='".$Article->script."&task=edit&id=".$Article->id."';</script>";
          else echo "<script>window.location.href='".$script."';</script>";
        }
        break;

    case 'delete':
                       $del = $Article->del( $id_del );
                       //if ( $del > 0 ) echo "<script>window.alert('Deleted OK! ($del records)');</script>";
                       //else $Article->Msg->show_msg('_ERROR_DELETE');
                       if( $del==0 ) $Article->Msg->show_msg('_ERROR_DELETE');
                       echo "<script>window.location.href='$script';</script>";
                       break;
    case 'cancel':
                       echo "<script>window.location.href='$script';</script>";
                       break;
    case 'up':
                    $Article->up( $move );
                    echo "<script>window.location.href='$script';</script>";
                    break;

    case 'down':
                    $Article->down( $move );
                    echo "<script>window.location.href='$script';</script>";
                    break;

    case 'preview':
                    $Article->preview();
                    break;

    case 'addimg':
                $Article->img_form( $id, "" );
                break;

        //-------- Photo Gallary Start -----------                 
    case 'showimages':
                   $Article->ShowImagesBackEnd();
                   break;
    case 'saveimg':
                   if ( $Article->SavePicture()!=NULL ){ 
                        $Article->ShowImagesBackEnd();
                        return false;
                   }
                  else echo "<script>window.location.href='$script&task=showimages&id=$Article->id';</script>";
                   break;
   case 'updimg':
                   if ( $Article->UpdatePicture()!=NULL ){
                        $Article->ShowImagesBackEnd();
                        return false;
                   }
                   else echo "<script>window.location.href='$script';</script>";
                   break;
   case 'delimg':
                   if( !isset($_REQUEST['id_img_del']) ) $id_img_del=NULL;
                   else $id_img_del = $_REQUEST['id_img_del'];
                   if ( !empty($id_img_del) ) {
                      $del=$Article->DelPicture( $id_img_del ); 
                      //if ( $del > 0 ) echo "<script>window.alert('".$Article->Msg->get_msg('_SYS_DELETED_OK')." $del');</script>";
                      //else $Article->Msg->show_msg('_ERROR_DELETE',TblSysTxt);
                      if( $del==0 ) $Article->Msg->show_msg('_ERROR_DELETE',TblSysTxt);
                   }
                   else $Article->Msg->show_msg('_ERROR_SELECT_FOR_DEL');
                   echo "<script>window.location.href='$script&task=showimages&id=$Article->id';</script>"; 
                   break;
  case 'qdelimg':
                   if( !isset($_REQUEST['id_img_del']) ) $id_img_del=NULL;
                   else $id_img_del = $_REQUEST['id_img_del'];
                   $arr[0] = $id_img_del;
                   //print_r($arr);
                   if ( !empty($arr) ) {
                      $del=$Article->DelPicture( $arr );
                      if ( $del > 0 ) { echo "<script>window.alert('".$pf->Msg->get_msg('_SYS_DELETED_OK')." $del');</script>";
                      echo "<script>window.location.href='$script&task=edit&id=$Article->id';</script>";
                      }
                      else $pg->Msg->show_msg('_ERROR_DELETE',TblSysTxt);
                   }    
                  // else $asg->show_msg('_ERROR_SELECT_FOR_DEL');
                  //echo $Article->id;
                 //  echo "<script>window.location.href='$script&task=edit&id=$Article->id';</script>"; 
                   break;

    case 'up_img':
                    $Article->upImg(TblModArticleImg, 'id_art', $Article->id);
                    echo "<script>window.location.href='$script&task=showimages&id=$Article->id';</script>";
                    break;

    case 'down_img':
                    $Article->downImg(TblModArticleImg, 'id_art', $Article->id);
                    echo "<script>window.location.href='$script&task=showimages&id=$Article->id';</script>";  
                    break; 
               break;
    case 'import_edifier_articles':
        echo '<br>==== START IMPORT ===='.
        $Article->ImportEdifierArticles();
        echo '<br>==== End IMPORT ====';
        break;                

}

?>
