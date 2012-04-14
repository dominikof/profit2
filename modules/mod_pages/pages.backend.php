<?
/**
* pages.backend.php  
* script for all actions with dynamic pages
* @package Pages Package of SEOCMS
* @author Igor Trokhymchuk  <ihor@seotm.com>
* @version 1.1, 24.02.2011
* @copyright (c) 2010+ by SEOTM
*/
if (!defined("SITE_PATH")) define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] );  
include_once(SITE_PATH.'/modules/mod_pages/pages.defines.php');

// Init JsHttpRequest and specify the encoding. It's important!
$JsHttpRequest = new JsHttpRequest("utf-8");

if(!defined("_LANG_ID")) {$pg = new PageAdmin();} 

if( !isset( $_REQUEST['module'] ) ) $module = NULL;
else $module = $_REQUEST['module'];

//Blocking to execute a script from outside (not Admin-part) 
if ( !$pg->logon->isAccessToScript($module)) exit;


if( !isset( $_REQUEST['task'] ) ) $task = 'show';
else $task = $_REQUEST['task'];

if( isset($_REQUEST['item_img']) and ( !empty($_REQUEST['item_img'])) ) {
    $task='delitemimg';
    $item_img = $_REQUEST['item_img'];
}
else $item_img = NULL;

if( !isset( $_REQUEST['display'] ) ) $display = 20;
else $display = $_REQUEST['display'];

if( !isset( $_REQUEST['start'] ) ) $start = 0;
else $start = $_REQUEST['start'];

if( !isset( $_REQUEST['sort'] ) ) $sort = NULL;
else $sort = $_REQUEST['sort'];

if( !isset( $_REQUEST['fltr'] ) ) $fltr=NULL;
else $fltr = $_REQUEST['fltr'];

if( !isset( $_REQUEST['textarea_editor'] ) ) $textarea_editor=NULL;
else $textarea_editor = $_REQUEST['textarea_editor'];



if( !isset( $_REQUEST['id_categ'] ) ) $id_categ=NULL;
else $id_categ = $_REQUEST['id_categ'];

if( !isset( $_REQUEST['id'] ) ) $id=NULL;
else $id = $_REQUEST['id'];

if( !isset( $_REQUEST['title'] ) ) $title = NULL;
else $title = $_REQUEST['title'];

if( !isset( $_REQUEST['description'] ) ) $description = NULL;
else $description = $_REQUEST['description'];

if( !isset( $_REQUEST['body'] ) ) $body = NULL;
else $body = $_REQUEST['body'];

if( !isset( $_REQUEST['keywords'] ) ) $keywords = NULL;
else $keywords = $_REQUEST['keywords'];

if( !isset( $_REQUEST['name'] ) ) $name = NULL;
else $name = $_REQUEST['name'];

if( !isset( $_REQUEST['name_old'] ) ) $name_old = NULL;
else $name_old = $_REQUEST['name_old'];

if( isset( $_REQUEST['visible'] ) AND $_REQUEST['visible']=='visible' ) $visible = 1;
else $visible = 0;

if( isset( $_REQUEST['publish'] ) AND $_REQUEST['publish']=='publish' ) $publish = 1;
else $publish = 0;

if( !isset( $_REQUEST['descr'] ) ) $descr=NULL;
else $descr = $_REQUEST['descr'];

if( !isset( $_REQUEST['short'] ) ) $short = NULL; 
else $short = $_REQUEST['short'];

if( !isset( $_REQUEST['id_del'] ) ) $id_del=NULL;
else $id_del = $_REQUEST['id_del'];

if( !isset( $_REQUEST['level'] ) ) $level=0;
else $level = $_REQUEST['level'];

if( !isset( $_REQUEST['move'] ) ) $move=NULL;
else $move = $_REQUEST['move'];

if( !isset($_REQUEST['img']) ) $img=NULL;
else $img = $_REQUEST['img'];

if( !isset( $_REQUEST['replace_to'] ) ) $replace_to = NULL;
else $replace_to = $_REQUEST['replace_to'];

if( !isset( $_REQUEST['sel'] ) ) $sel = NULL;
else $sel = $_REQUEST['sel'];

if( isset( $_REQUEST['ctrlscript'] ) AND $_REQUEST['ctrlscript']=='ctrlscript' ) $ctrlscript = 1;
else $ctrlscript = 0;

if( isset( $_REQUEST['special_pos'] ) AND $_REQUEST['special_pos']=='special_pos' ) $special_pos = 1;
else $special_pos = 0;


if( !isset( $_REQUEST['textarea_editor'] ) ) $textarea_editor = NULL;
else $textarea_editor = $_REQUEST['textarea_editor'];

if( !isset( $_REQUEST['lang_id'] ) ) $lang_id = NULL;
else $lang_id = $_REQUEST['lang_id'];

if( !isset($_REQUEST['id_tag']) ) $id_tag=NULL;
else $id_tag = $_REQUEST['id_tag'];

if( !isset($_REQUEST['main_page']) ) $main_page=0;
else $main_page=1;

if( $task=='savereturn') {$task='save'; $action='return';}
else $action=NULL;
if( $task=='to_publish') {$task='save'; $publish=1;}

$PagesBackend = new PagesBackend($pg->logon->user_id, $module);
$PagesBackend->task = $task;
$PagesBackend->action = $action; 
$PagesBackend->display = $display;
$PagesBackend->start = $start;
$PagesBackend->sort = $sort;
$PagesBackend->fltr = $fltr;
$PagesBackend->textarea_editor = $textarea_editor; 

$PagesBackend->id = $id; 
$PagesBackend->level = $level;
$PagesBackend->visible = $visible; 
$PagesBackend->publish = $publish;
$PagesBackend->descr = $descr;  
$PagesBackend->short = $short;  
$PagesBackend->title = $title;
$PagesBackend->description = $description;
$PagesBackend->keywords = $keywords; 
$PagesBackend->body = $body;
$PagesBackend->move = $move;
$PagesBackend->img = $img;
$PagesBackend->sel = $sel;
$PagesBackend->ctrlscript = $ctrlscript;
$PagesBackend->special_pos = $special_pos;
$PagesBackend->textarea_editor = $textarea_editor; 
$PagesBackend->id_tag = $id_tag; 
$PagesBackend->main_page = $main_page;
$PagesBackend->name = $PagesBackend->PrepareLink($name, $PagesBackend->ctrlscript);
$PagesBackend->name_old = $name_old;
 
//echo 'level = '.$level;
$PagesBackend->script_ajax = 'module='.$PagesBackend->module.'&display='.$PagesBackend->display.'&start='.$PagesBackend->start.'&sort='.$PagesBackend->sort.'&fltr='.$PagesBackend->fltr."&level=".$PagesBackend->level;
$PagesBackend->script = "index.php?".$PagesBackend->script_ajax; 
                                                                                 
//echo '<br>PagesBackend->task='.$PagesBackend->task;
switch($PagesBackend->task){
	case 'show':	
        $PagesBackend->show();
		break;
    case 'edit':
        $PagesBackend->edit();
        break;
    case 'new':
        $PagesBackend->edit();
        break;
    case 'ajax_refresh_urlname':
        //phpinfo();
        $PageLayout = new FrontendPages();
        if( isset($PagesBackend->name[$PagesBackend->lang_id]) )$pname = $PagesBackend->name[$PagesBackend->lang_id];
        else $pname='';
        $PagesBackend->ShowURLName($PageLayout, $PagesBackend->ctrlscript, $pname);
        break;
    case 'ajax_refresh_editor':
        //phpinfo();
        $PageLayout = new FrontendPages();
        $PagesBackend->EditPageContentHtml($lang_id);
        break;
    case 'save':
        //phpinfo();
        if($PagesBackend->is_image==1){
            if ( $PagesBackend->SavePicture()!=NULL ){
              $PagesBackend->edit();
              return false;
            }
        }
        if( empty($PagesBackend->name) ) {
            //generate translit for empty URL-name only if this is not main page of the site 
            if($PagesBackend->main_page==0) $PagesBackend->name=$PagesBackend->GenerateTranslit($PagesBackend->level, $PagesBackend->id, $PagesBackend->descr);
        }
        else {
             //generate translit from URL-name only for new page Or for exist page only if URL-name is change
            /*
            if($PagesBackend->ctrlscript==1 AND ($PagesBackend->id=='' OR $PagesBackend->name_old!=$PagesBackend->name)){
                $tmp_name[1] = $PagesBackend->name;
                $PagesBackend->name=$PagesBackend->GenerateTranslit($PagesBackend->level, $PagesBackend->id, $tmp_name);
            }*/
        }

        if( $PagesBackend->CheckFields()==NULL ){
            $PagesBackend->save();
            $PagesBackend->UploadFile->SaveFiles($PagesBackend->id);
            $PagesBackend->UploadImages->SaveImages($PagesBackend->id);
            //$PagesBackend->UploadVideo->SaveVideos($PagesBackend->id)
            
            if( $PagesBackend->action=='return' ) $PagesBackend->edit();
            else $PagesBackend->show(); 
        }
        else{       
            if( $PagesBackend->action=='return' ) $PagesBackend->edit();
            else $PagesBackend->show();
        }
		break;
	case 'delete':
        $del = $PagesBackend->delPages( $id_del );
        if ( $del == 0 ) $pg->Msg->show_msg('_ERROR_DELETE');
        //else echo "<script>window.alert('Deleted OK! ($del records)');</script>";
        //echo "<script>window.location.href='".$PagesBackend->script."';</script>";
        $PagesBackend->show();
	    break;
    case 'cancel':
        //echo "<script>window.location.href='".$PagesBackend->script."';</script>";
        $PagesBackend->show();
        break;						
	case 'up':
        $PagesBackend->up(TblModPages, 'level', $PagesBackend->level);
        $PagesBackend->showHTML();
        //echo "<script>window.location.href='".$PagesBackend->script."';</script>";
        break;
    case 'down':
        $PagesBackend->down(TblModPages, 'level', $PagesBackend->level);
        $PagesBackend->showHTML();
        //echo "<script>window.location.href='".$PagesBackend->script."';</script>";
        break; 
    case 'replace':
        //phpinfo();
        $PagesBackend->Form->Replace(TblModPages, 'move', $PagesBackend->id, $replace_to);
        $PagesBackend->showHTML();
        break;
    case 'delitemimg':
        if ( !$PagesBackend->DelItemImage($item_img)){
            $PagesBackend->Err = $PagesBackend->multi['MSG_IMAGE_NOT_DELETED']."<br>";
        }
        $PagesBackend->edit();
        //echo "<script>window.location.href='$sys_spr->script';</script>";
        break;        
}
?>