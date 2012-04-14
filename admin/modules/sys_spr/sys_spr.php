<?php
/**
* sys_spr.php
* script for all actions with reference-books
* @package System Package of SEOCMS
* @author Igor Trokhymchuk  <ihor@seotm.com>
* @version 1.1, 02.04.2012
* @copyright (c) 2005+ by SEOTM
*/
if (!defined("SITE_PATH")) define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] );
include_once( SITE_PATH.'/admin/include/defines.inc.php' );
include_once( SITE_PATH.'/admin/modules/sys_spr/sys_spr.class.php' );
if(!defined("_LANG_ID")) {$pg = new PageAdmin();} 

//echo '<br>$_REQUEST='.print_r($_REQUEST); 
$module = AntiHacker::AntiHackRequest('module'); 
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
     exit;
}

$logon = &check_init('logon','Authorization');
//if ( ! defined('BASEPATH')) {
if (!$logon->LoginCheck()) {
	//return false;
	?><script>window.location.href="<?=$goto?>";</script><?; 
}
//=============================================================================================
// END
//=============================================================================================
$task = AntiHacker::AntiHackRequest('task','show');

if( isset($_REQUEST['item_img']) and ( !empty($_REQUEST['item_img'])) ) {
	$task='delitemimg';
	$item_img = $_REQUEST['item_img'];
}
else $item_img = NULL;
if ( !isset($_FILES['image']) ) $image = NULL;
else $image = $_FILES['image'];

$sort = AntiHacker::AntiHackRequest('sort');
$start = AntiHacker::AntiHackRequest('start','0');
$display = AntiHacker::AntiHackRequest('display','20');
$id = AntiHacker::AntiHackRequest('id');
$cod = AntiHacker::AntiHackRequest('cod');
$cod_old = AntiHacker::AntiHackRequest('cod_old');
$img = AntiHacker::AntiHackArrayRequest('img');
$fln = AntiHacker::AntiHackRequest('fln',_LANG_ID);
$srch = AntiHacker::AntiHackRequest('srch');
$module_name = AntiHacker::AntiHackRequest('module_name');
$spr = AntiHacker::AntiHackRequest('spr');
$usemove = AntiHacker::AntiHackRequest('usemove');
$root_script = AntiHacker::AntiHackRequest('root_script');
$parent_script = AntiHacker::AntiHackRequest('parent_script');
$parent_id = AntiHacker::AntiHackRequest('parent_id');
$info_msg = AntiHacker::AntiHackRequest('info_msg');

/////////////////////////////////////////////////////////////////////////////
$name = AntiHacker::AntiHackArrayRequest('name');
$short = AntiHacker::AntiHackArrayRequest('short');
$mtitle = AntiHacker::AntiHackArrayRequest('mtitle');
$mdescr = AntiHacker::AntiHackArrayRequest('mdescr');
$mkeywords = AntiHacker::AntiHackArrayRequest('mkeywords');
$translit = AntiHacker::AntiHackArrayRequest('translit');
$descr = AntiHacker::AntiHackArrayRequest('descr');
$move = AntiHacker::AntiHackArrayRequest('move');
$colorBit = AntiHacker::AntiHackArrayRequest('colorBit');
/////////////////////////////////////////////////////////////////////////////

if( !isset( $_REQUEST['replace_to'] ) ) $replace_to = NULL;
else $replace_to = stripslashes(trim($_REQUEST['replace_to']));

$uselevels = AntiHacker::AntiHackRequest('uselevels');
$usecolors = AntiHacker::AntiHackRequest('usecolors');
$usecodpli = AntiHacker::AntiHackRequest('usecodpli');
$cod_pli = AntiHacker::AntiHackRequest('cod_pli');
$level = AntiHacker::AntiHackRequest('level',0);
$node = AntiHacker::AntiHackRequest('node',0);
$level_new = AntiHacker::AntiHackRequest('level_new','0');
$usemeta = AntiHacker::AntiHackRequest('usemeta');
$edit_lang = AntiHacker::AntiHackRequest('edit_lang', _LANG_ID);
$usetranslit = AntiHacker::AntiHackRequest('usetranslit');
$translit_from = AntiHacker::AntiHackArrayRequest('translit_from');
$usedescr = AntiHacker::AntiHackRequest('usedescr');
$useshort = AntiHacker::AntiHackRequest('useshort',0);
$useimg = AntiHacker::AntiHackRequest('useimg','0');

//--- For Catalog parameters ---
$id_cat = AntiHacker::AntiHackRequest('id_cat');
$id_param = AntiHacker::AntiHackRequest('id_param');

$mas_module=explode("?",$module);
$module=$mas_module[0];
If ( empty($spr) ) { 
	$spr=$mas_module[1];
	$mas_spr=explode("=",$mas_module[1]);
	$spr=$mas_spr[1];
}

if( $module!=NULL )
{
 $sys_spr = new SysSpr($logon->user_id, $module, $display, $sort, $start, '100%', $spr);
 $sys_spr->task=$task;
 $sys_spr->module=$module;
 $sys_spr->module_name=$module_name;
 $sys_spr->spr=$spr;
 $sys_spr->info_msg=$info_msg;
 $sys_spr->useshort=$useshort;
 $sys_spr->useimg=$useimg;
 $sys_spr->uselevels=$uselevels;
 $sys_spr->usecolors=$usecolors;
 $sys_spr->usecodpli=$usecodpli;
 $sys_spr->cod_pli=$cod_pli;
 $sys_spr->usemeta=$usemeta;
 $sys_spr->usetranslit=$usetranslit;
 $sys_spr->usedescr=$usedescr;
 $sys_spr->root_script=$root_script;
 $sys_spr->parent_script=$parent_script;
 $sys_spr->parent_id=$parent_id; 
 $sys_spr->fln = $fln;
 $sys_spr->display = $display;
 $sys_spr->sort = $sort;
 $sys_spr->start = $start;  
 $sys_spr->srch = $srch;
 $sys_spr->move = $move;
 $sys_spr->colorBit = $colorBit;
 
 $sys_spr->id = $id;
 $sys_spr->cod = $cod;
 $sys_spr->cod_old = $cod_old;
 $sys_spr->name = $name;
 $sys_spr->short = $short;
 $sys_spr->img = $img;
 $sys_spr->image = $image; 
 $sys_spr->node = $node; 
 $sys_spr->level = $level; 
 $sys_spr->level_new = $level_new;
 $sys_spr->mtitle = $mtitle; 
 $sys_spr->mdescr = $mdescr;
 $sys_spr->mkeywords = $mkeywords;
 $sys_spr->edit_lang = $edit_lang;
 $sys_spr->translit = $translit;
 $sys_spr->translit_from = $translit_from;
 $sys_spr->descr = $descr;
 
 $sys_spr->id_cat = $id_cat;
 $sys_spr->id_param = $id_param; 
 
 $sys_spr->script_ajax = "module=$sys_spr->module&spr=$sys_spr->spr&display=$display&start=$start&sort=$sort&fln=$sys_spr->fln&usedescr=$sys_spr->usedescr&&useshort=$sys_spr->useshort&useimg=$sys_spr->useimg&uselevels=$sys_spr->uselevels&level=$sys_spr->level&node=$sys_spr->node&usemeta=$sys_spr->usemeta&root_script=$sys_spr->root_script&parent_script=$sys_spr->parent_script&parent_id=$sys_spr->parent_id&srch=$sys_spr->srch&module_name=$sys_spr->module_name";
 if(!empty($sys_spr->id_cat)) $sys_spr->script_ajax .= "&id_cat=".$sys_spr->id_cat;
 if(!empty($sys_spr->id_param)) $sys_spr->script_ajax .= "&id_param=".$sys_spr->id_param;
 $sys_spr->script = "index.php?".$sys_spr->script_ajax;
 //echo '<br> $sys_spr->script='.$sys_spr->script;
 //phpinfo();
 //echo '<br>$sys_spr->task='.$sys_spr->task;
 switch( $sys_spr->task ) {
	case 'show': 
		$sys_spr->show();
		break;
	case 'show_sublevel': 
		$sys_spr->show1();
		//$sys_spr->ShowContentHTML();
		break;
	case 'edit':
		if($sys_spr->use_edit_ajax==1){
			if (!$sys_spr->EditWithAjax()) echo "<script>window.location.href='$sys_spr->script';</script>";
		}
		else {
			$sys_spr->edit();
		}
		break;
	case 'new':
		if($sys_spr->use_edit_ajax==1){
			$sys_spr->EditWithAjax();
		}
		else{ 
			$sys_spr->edit();
		}
		break;
	case 'edit_lng_panel':
		//echo '<br>$_REQUEST='.print_r($_REQUEST);
		$sys_spr->EditLngPanel();
		break;
	case 'add_lang':
		if (!$sys_spr->add_lang( $logon->user_id, $module, $id, NULL, $spr )) echo "<script>window.location.href='$sys_spr->script';</script>";
		break;
	case 'add_img_on_lang':
		if ( $sys_spr->SavePicture()!=NULL ){
		  $sys_spr->Form->ShowErrBackEnd($sys_spr->Err);
		  return false;
		}
		$sys_spr->EditLngPanelImg($sys_spr->img[$sys_spr->edit_lang]);        
		break;
	case 'save':
		//phpinfo();
		if($sys_spr->use_edit_ajax==0){
			if ( $sys_spr->SavePicture()!=NULL ){
			  $sys_spr->edit();
			  return false;
			}
		}
        $sys_spr->CheckFields();
        //echo '<br>$sys_spr->Err='.$sys_spr->Err;
        if( empty( $sys_spr->Err ) ){
            echo 'check ok.';
    		if ( $sys_spr->save() ){
  				 $sys_spr->info_msg = $sys_spr->Msg->show_text('_OK_SAVE');
                 //echo '<br /> good';
    			 echo "<script>window.location.href='$sys_spr->script&info_msg=$sys_spr->info_msg';</script>";
    		}
        }
        else{
            if($sys_spr->use_edit_ajax==1){
                $sys_spr->EditWithAjax();
            }
            else{ 
                $sys_spr->edit();
            }
        }
		break;
	case 'delete':
		if( !isset($_REQUEST['id_del']) ) $id_del=NULL;
		else $id_del = $_REQUEST['id_del'];
		if ( !empty($id_del) ) {
		  $del=$sys_spr->del($id_del);
		  if ( $del == 0 ) $Msg->show_msg('_ERROR_DELETE');
		}
		else $Msg->show_msg('_ERROR_SELECT_FOR_DEL');
		echo '<script>window.location.href="',$sys_spr->script,'";</script>'; 
		break;
	case 'cancel':
		echo '<script>window.location.href="',$sys_spr->script,'";</script>'; 
		break;
	case 'up':
		$sys_spr->up($sys_spr->spr, $sys_spr->level);
		$sys_spr->ShowContentHTML();
		//echo "<script>window.location.href='$sys_spr->script';</script>";
		break;
	case 'down':
		$sys_spr->down($sys_spr->spr, $sys_spr->level);
		$sys_spr->ShowContentHTML();
		//echo "<script>window.location.href='$sys_spr->script';</script>";
		break;                      
	case 'replace':
		$sys_spr->Form->ReplaceByCod($sys_spr->spr, 'move', $sys_spr->id, $replace_to);
		$sys_spr->ShowContentHTML();
		break;
	case 'delitemimg':
		//echo '<br>$item_img='.$item_img;
		if ( !$sys_spr->DelItemImage($item_img, $sys_spr->edit_lang)){
			$sys_spr->Err = $sys_spr->Msg->show_text('MSG_IMAGE_NOT_DELETED')."<br>";
		}
		if($sys_spr->use_edit_ajax==1){
			$sys_spr->EditLngPanelImg($sys_spr->img[$sys_spr->edit_lang]);
		}
		else{
			$sys_spr->edit();
			//echo "<script>window.location.href='$sys_spr->script';</script>";
		}
		break;
	case 'make_search':
		$sys_spr->showList();
		break;

	case 'add_new_tags':
		$sys_spr->EditWithAjax();
		break; 
 } //end switch
} //end if
?>