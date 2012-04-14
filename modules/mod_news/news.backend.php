<?
// ================================================================================================
//    System     : SEOCMS
//    Module     : News
//    Version    : 2.0.0
//    Date       : 01.04.2007
//    Licensed To:
//                 Igor  Trokhymchuk  ihoru@mail.ru
//    Purpose    : Class definition for News - moule
//
// ================================================================================================
if (!defined("SITE_PATH")) define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] ); 
include_once( SITE_PATH.'/modules/mod_news/news.defines.php' );

if(!defined("_LANG_ID")) {$pg = new PageAdmin();} 

if( !isset( $_REQUEST['module'] ) ) $module = NULL;
else $module = $_REQUEST['module'];

//Blocking to execute a script from outside (not Admin-part) 
if ( !$pg->logon->isAccessToScript($module)) exit;

if( !isset( $_REQUEST['task'] ) ) $task = 'show';
else $task = $_REQUEST['task'];

if( !isset( $_REQUEST['display'] ) ) $display = 50;
else $display = $_REQUEST['display'];

if( !isset( $_REQUEST['start'] ) ) $start = 0;
else $start = $_REQUEST['start'];

if( !isset( $_REQUEST['sort'] ) ) $sort = 'display';
else $sort = $_REQUEST['sort'];

if( !isset( $_REQUEST['id'] ) ) $id = NULL;
else $id = $_REQUEST['id'];

if( !isset( $_REQUEST['fltr'] ) ) $fltr=NULL;
else $fltr = $_REQUEST['fltr'];

if( !isset( $_REQUEST['fltr2'] ) ) $fltr2=NULL;
else $fltr2 = $_REQUEST['fltr2'];

if( !isset( $_REQUEST['img'] ) ) $img=NULL;
else $img = $_REQUEST['img'];

if( !isset( $_REQUEST['topImage'] ) ) $topImage=NULL;
else $topImage = $_REQUEST['topImage'];

if( !isset( $_REQUEST['sel'] ) ) $sel = NULL;
else $sel = $_REQUEST['sel'];

if( !isset( $_REQUEST['start_date'] ) ) $start_date = NULL;
else $start_date = $_REQUEST['start_date'];

if( !isset( $_REQUEST['end_date'] ) ) $end_date = NULL;
else $end_date = $_REQUEST['end_date'];

if( !isset( $_REQUEST['id_category'] ) ) $id_category = NULL;
else $id_category = $_REQUEST['id_category'];

if( !isset( $_REQUEST['keywords'] ) ) $keywords = NULL;
else $keywords = $_REQUEST['keywords'];

if( !isset( $_REQUEST['description'] ) ) $description = NULL;
else $description = $_REQUEST['description'];

if( !isset( $_REQUEST['main'] ) ) $main = NULL; // Головне в новині
else $main = $_REQUEST['main'];

if( !isset( $_REQUEST['subject'] ) ) $subject = NULL;
else $subject = $_REQUEST['subject'];

if( !isset( $_REQUEST['short'] ) ) $short = NULL;
else $short = $_REQUEST['short'];

if( !isset( $_REQUEST['full'] ) ) $full = NULL;
else $full = $_REQUEST['full'];

// ТОП
if( !isset( $_REQUEST['top'] ) ) $top = 0;
else $top = 1;

if( !isset( $_REQUEST['topMain'] ) ) $topMain = 0;        // Головна Топ-новина
else $topMain = 1;
if($top==0) 
    $topMain = 0;

if( !isset( $_REQUEST['property'] ) ) $property = 0;       // Властивість - Новини України
else $property = 1;
    
if( !isset( $_REQUEST['topSubject'] ) ) $topSubject = NULL;
else $topSubject = $_REQUEST['topSubject'];

if( !isset( $_REQUEST['topShort'] ) ) $topShort = NULL;
else $topShort = $_REQUEST['topShort'];
// End TOP

if( !isset( $_REQUEST['line'] ) ) $line = 0;
else $line = 1;

if( !isset( $_REQUEST['source'] ) ) $source = NULL;
else $source = $_REQUEST['source'];

if( !isset( $_REQUEST['status'] ) ) $status = NULL;
else $status = $_REQUEST['status'];

if( !isset( $_REQUEST['display1'] ) ) $display1 = NULL;
else $display1 = $_REQUEST['display1'];

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

if( !isset( $_REQUEST['id_del'] ) ) $id_del = NULL;
else $id_del = $_REQUEST['id_del'];

if( !isset( $_REQUEST['move'] ) ) $move = NULL;
else $move = $_REQUEST['move'];

if( !isset( $_REQUEST['id_img_show'] ) ) $id_img_show = NULL;
else $id_img_show = $_REQUEST['id_img_show'];

if( !isset( $_REQUEST['source'] ) ) $source = NULL;
else $source = $_REQUEST['source'];

if( !isset($_REQUEST['id_tag']) ) $id_tag=NULL;
else $id_tag = $_REQUEST['id_tag'];

if( !isset( $_REQUEST['subscr_start'] ) ) $subscr_start=0;
else $subscr_start = $_REQUEST['subscr_start'];

if( !isset( $_REQUEST['subscr_cnt'] ) ) $subscr_cnt=0;
else $subscr_cnt = $_REQUEST['subscr_cnt'];

if( !isset( $_REQUEST['is_partner'] ) ) $is_partner=0;
else $is_partner = $_REQUEST['is_partner'];


if( !isset($_REQUEST['arr_relat_prop']) ) $arr_relat_prop = NULL;
else $arr_relat_prop = $_REQUEST['arr_relat_prop'];
if( is_array($arr_relat_prop)){
    for($i=0; $i<count($arr_relat_prop); $i++){
      if ( !empty($arr_relat_prop[$i]) ) { // $ltr2=categ=51 => $flrt2=51
          $arr_fltr2_tmp=explode("=", $arr_relat_prop[$i]);
          //echo  '<br>$arr_fltr2_tmp[0]='.$arr_fltr2_tmp[0].' $arr_fltr2_tmp[1]='.$arr_fltr2_tmp[1];
          if( isset($arr_fltr2_tmp[0]) AND $arr_fltr2_tmp[0]=='curcod' AND isset($arr_fltr2_tmp[1]) ) $arr_relat_prop[$i] = $arr_fltr2_tmp[1];
          else $arr_relat_prop[$i] = NULL;
          //echo '<br>$arr_relat_prop[$i]='.$arr_relat_prop[$i];
      }
    }
} 

if( isset($_REQUEST['saveimg']) ) $task='saveimg';
if( isset($_REQUEST['updimg']) ) $task='updimg'; 
if( isset($_REQUEST['delimg']) ) $task='delimg'; 
if( isset($_REQUEST['cancel']) ) $task='cancel';

if( isset( $_REQUEST['dorel'] ) )
{
 if( $task == 'new' ) $task = 'newnews_relart';
 if( $task == 'save' ) $task = 'savenews_relart';
 if( $task == 'delete' ) $task = 'delnews_relart';
}

if( $task=='savereturn') {$task='save'; $action='return';}
else $action=NULL;

$News = new NewsCtrl($pg->logon->user_id, $module);

if( !isset( $_REQUEST['dispatch_sbj'] ) ) $dispatch_sbj = $News->Msg->show_text('TXT_DISPATCH_TITLE_DEFAULT');
else $dispatch_sbj = $_REQUEST['dispatch_sbj'];

$News->top = $top;
$News->topMain = $topMain;
$News->topSubject = $topSubject;
$News->topShort = $topShort;
$News->property  = $property;
$News->line = $line;

$News->user_id = $pg->logon->user_id;
$News->task = $task;
$News->id = $id;
$News->display = $display;
$News->start = $start;
$News->sort = $sort;
$News->fltr = $fltr;
$News->fltr2 = $fltr2;
$News->img = $img;
$News->topImage = $topImage;
$News->sel = $sel;
$News->image = $image;
$News->id_img = $id_img;
$News->img_title = $img_title;  
$News->img_descr = $img_descr; 
$News->img_show = $id_img_show; 
$News->move=$move;
$News->img_show = $img_show;

$News->id_category = $id_category;
$News->subj_ = $subject;
$News->main = $main;
$News->short_ = $short;
$News->full_ = $full;
$News->status = $status;
$News->start_date = $start_date;
$News->end_date = $end_date;
//echo '<br>$News->start_date='.$News->start_date.' $News->end_date='.$News->end_date;
$News->display1 = $display1;
$News->keywords = $keywords;
$News->description =$description;
$News->source = $source;
$News->id_tag = $id_tag;
$News->subscr_start = $subscr_start;   
$News->subscr_cnt = $subscr_cnt;
$News->is_partner = $is_partner; 
$News->dispatch_sbj = addslashes(trim(strip_tags($dispatch_sbj)));

$News->arr_relat_prop = $arr_relat_prop;  

if( !isset( $_REQUEST['fln'] ) ) $News->fln = _LANG_ID;
else $News->fln = $_REQUEST['fln'];

$script = 'module='.$News->module.'&display='.$News->display.'&start='.$News->start.'&sort='.$News->sort.'&fltr='.$News->fltr;
$script = $_SERVER['PHP_SELF']."?$script";
$News->script = $script;

//echo '$task ='.$task;
switch( $task ) {
    case 'show':
    
        $News->show(); 
        break;
    case 'new':
    //echo 'hello';
        $News->edit( NULL, NULL );
        break;
    case 'edit':
        $News->edit( $News->id, NULL );
        break;
    case 'add_lang':
        $News->add_lang( $cod, NULL );
        break;
    case 'save':
        if( $News->CheckFields()!=NULL){
            $News->edit( $News->id, NULL);
            return false;
        }
        if ($News->save())
        {
          if( $action=='return' ) echo "<script>window.location.href='".$News->script."&task=edit&id=".$News->id."';</script>";
          else echo "<script>window.location.href='".$script."';</script>";
        }
        break;
    case 'delete':
        $del = $News->del( $id_del );
        //if ( $del > 0 ) echo "<script>window.alert('Deleted OK! ($del records)');</script>";
        //else $pg->Msg->show_msg('_ERROR_DELETE');
        if( $del==0 ) $pg->Msg->show_msg('_ERROR_DELETE');
        echo "<script>window.location.href='$script';</script>";
        break;
    case 'up':
        $News->up( $move );
        echo "<script>window.location.href='$script';</script>";
        break;

    case 'down':
        $News->down( $move );
        echo "<script>window.location.href='$script';</script>";
        break;
    case 'cancel':  
        echo "<script>window.location.href='$script';</script>";
        break;
    case 'preview':
        $News->preview( $subject, $short, $full );
        break;

    case 'shownews_relart':
        AdminHTML::PanelSubH( $News->Msg->show_text('_FLD_NEWS_RELART') );
        $News->relart( $News->id );
        AdminHTML::PanelSubF();
        break;
    case 'newnews_relart':
        AdminHTML::PanelSubH( $News->Msg->show_text('_FLD_NEWS_RELART') );
        $News->relart_edit( $News->id, NULL );
        AdminHTML::PanelSubF();
        break;
    case 'editnews_relart':
        AdminHTML::PanelSubH( $News->Msg->show_text('_FLD_NEWS_RELART') );
        $News->relart_edit( $News->id, $id_r );
        AdminHTML::PanelSubF();
        break;
    case 'savenews_relart':
        if( $News->relart_save( $News->id, $id_news, $id_relart ) )
        {
          echo "<script>window.location.href='$script&task=shownews_relart&id=$id_news';</script>";
        }
        break;
    case 'delnews_relart':
        $del = $News->relart_del( $id_del );
             if ( $del > 0 ) echo "<script>window.alert('Deleted OK! ($del records)');</script>";
             else echo show_error('_ERROR_DELETE');
        echo "<script>window.location.href='$script&task=shownews_relart&cod=$cod';</script>";
        break;
    
    //-------- Photo Gallary Start -----------                 
    case 'showimages':
        $News->ShowImagesBackEnd();
        break;
    case 'saveimg':
        if ( $News->SavePicture()!=NULL ){
            $News->ShowImagesBackEnd();
            return false;
        }
        else echo "<script>window.location.href='$script&task=showimages&id=$News->id';</script>";
        break;
    case 'updimg':
        if ( $News->UpdatePicture()!=NULL ){
            $News->ShowImagesBackEnd();
            return false;
        }
        else echo "<script>window.location.href='$script';</script>";
        break;
    case 'delimg':
        if( !isset($_REQUEST['id_img_del']) ) $id_img_del=NULL;
        else $id_img_del = $_REQUEST['id_img_del'];
        if ( !empty($id_img_del) ) {
          $del=$News->DelPicture( $id_img_del );
          if ( $del > 0 ) echo "<script>window.alert('".$pg->Msg->get_msg('_SYS_DELETED_OK')." $del');</script>";
          else $pg->Msg->show_msg('_ERROR_DELETE',TblSysTxt);
        }
        else $pg->Msg->show_msg('_ERROR_SELECT_FOR_DEL');
        echo "<script>window.location.href='$script&task=showimages&id=$News->id';</script>"; 
        break;
    case 'qdelimg':
        if( !isset($_REQUEST['id_img_del']) ) $id_img_del=NULL;
        else $id_img_del = $_REQUEST['id_img_del'];
        $arr[0] = $id_img_del;
        //print_r($arr);
        if ( !empty($arr) ) {
          $del=$News->DelPicture( $arr );
          if ( $del > 0 ) { echo "<script>window.alert('".$pg->Msg->get_msg('_SYS_DELETED_OK')." $del');</script>";
          echo "<script>window.location.href='$script&task=edit&id=$News->id';</script>";
          }
          else $pg->Msg->show_msg('_ERROR_DELETE',TblSysTxt);
        }    
        // else $pg->Msg->show_msg('_ERROR_SELECT_FOR_DEL');
        //echo $News->id;
        //  echo "<script>window.location.href='$script&task=edit&id=$News->id';</script>"; 
        break;
    case 'up_img':
        $News->upImg(TblModNewsImg, 'id_news', $News->id);
        echo "<script>window.location.href='$script&task=showimages&id=$News->id';</script>";
        break;
    case 'down_img':
        $News->downImg(TblModNewsImg, 'id_news', $News->id);
        echo "<script>window.location.href='$script&task=showimages&id=$News->id';</script>";  
        break;
    //-------- Photo Gallary End -----------
    
    case 'read_rss':
        $News->ReadRss();
        break;
   
    case 'subscr_send':
        if( count($id_del)>0){
            if($News->subscr_send($id_del)) echo "<script>window.alert('".$pg->Msg->get_msg('MSG_NEWS_SEND_OK', TblModNewsSprTxt)."');</script>"; 
            else echo "<script>window.alert('".$pg->Msg->get_msg('MSG_ERR_NEWS_NOT_SEND', TblModNewsSprTxt)."');</script>"; 
        }
        else{
            $pg->Msg->show_msg('_ERR_SELECT_FOR_SEND', TblModNewsSprTxt);
        }
        echo "<script>window.location.href='$script';</script>"; 
        break;
    case 'news_posting_arr':
        //phpinfo();
        if( count($id_del)>0){
            $News->NewsPostingArr($id_del);
        }
        else{
            $pg->Msg->show_msg('_ERR_SELECT_FOR_SEND', TblModNewsSprTxt);
            echo "<script>window.location.href='$script&dispatch_sbj=".$News->dispatch_sbj."';</script>"; 
        }
        break;
    case 'stop_dispatch':                                     
        $News->StopDispatch();
        $News->show();
        break;
    case 'import_edifier_news':
        echo '<br>==== START IMPORT ===='.
        $News->ImportEdifierNews();
        echo '<br>==== End IMPORT ====';
        break;        
}

?>