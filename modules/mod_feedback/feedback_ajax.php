<?php                                     /* feedback_ajax.php */
// ================================================================================================
// System : SEOCMS
// Module : feedback.php
// Version : 1.0.0
// Date : 17.04.2007
// Licensed To:
// Igor Trokhymchuk ihoru@mail.ru
// Andriy Lykhodid las_zt@mail.ru
//
// Purpose : script for all actions with feddback
//
// ================================================================================================
if (!defined("SITE_PATH")) define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] ); 
include_once( SITE_PATH.'/include/defines.php' );

$page = new PageUser();

$FeedbackLayout = new FeedbackLayout(); 

if( !isset ( $_REQUEST['task'] ) ) $FeedbackLayout->task = NULL;
else $FeedbackLayout->task = $FeedbackLayout->Form->GetRequestTxtData($_REQUEST['task'], 1);
  
if(!isset($_REQUEST['name'])) $FeedbackLayout->name = NULL;
else $FeedbackLayout->name = $FeedbackLayout->Form->GetRequestTxtData($_REQUEST['name'], 1);

if(!isset($_REQUEST['fax'])) $FeedbackLayout->fax = NULL;
else $FeedbackLayout->fax = $FeedbackLayout->Form->GetRequestTxtData($_REQUEST['fax'], 1);

if(!isset($_REQUEST['tel'])) $FeedbackLayout->tel = NULL;
else $FeedbackLayout->tel = $FeedbackLayout->Form->GetRequestTxtData($_REQUEST['tel'], 1);

if(!isset($_REQUEST['e_mail'])) $FeedbackLayout->e_mail = NULL;
else $FeedbackLayout->e_mail = $FeedbackLayout->Form->GetRequestTxtData($_REQUEST['e_mail'], 1);
                                                                               
if(!isset($_REQUEST['question'])) $FeedbackLayout->question = NULL;
else $FeedbackLayout->question = $FeedbackLayout->Form->GetRequestTxtData($_REQUEST['question'], 1);

if(!isset($_REQUEST['to_addr'])) $FeedbackLayout->to_addr = NULL;
else $FeedbackLayout->to_addr = $FeedbackLayout->Form->GetRequestTxtData($_REQUEST['to_addr'], 1);

if(!isset($_REQUEST['quick_form'])) $FeedbackLayout->quick_form = NULL;
else $FeedbackLayout->quick_form = $FeedbackLayout->Form->GetRequestTxtData($_REQUEST['quick_form'], 1);

//=========== kcaptcha ==================
if ( !isset( $_REQUEST['captchacodestr'] ) ) $FeedbackLayout->captchacodestr = NULL;
else $FeedbackLayout->captchacodestr = $FeedbackLayout->Form->GetRequestTxtData($_REQUEST['captchacodestr'], 1);
//=======================================

if(!isset($_COOKIE['refpage'])) $FeedbackLayout->refpage = NULL;
else $FeedbackLayout->refpage = $FeedbackLayout->Form->GetRequestTxtData($_COOKIE['refpage'], 1);

if(!isset($_COOKIE['serfing'])) $FeedbackLayout->cookie_serfing = NULL;
else $FeedbackLayout->cookie_serfing = $_COOKIE['serfing']; 

switch ($FeedbackLayout->task){
  case 'send':
    $err = NULL;
    //echo '<br>$_SESSION[captcha_keystring]='.$_SESSION['captcha_keystring'];
    if ($FeedbackLayout->CheckFields()!=NULL) {
        $FeedbackLayout->show_form_left();
        return false;    
    }
    
    if( !isset($_FILES['filename']) ) $filename = NULL;
    else $filename = $_FILES['filename'];      
    if ( $filename!=null and $_FILES["filename"]["error"]==0) {
      $FeedbackLayout->fpath = $FeedbackLayout->Form->GetRequestTxtData($_FILES["filename"]["name"], 1);
      $tmp_f_name = $_FILES["filename"]["tmp_name"];
  
      $uploaddir = SITE_PATH.FeedbackUploadFilesPath;
      if ( !file_exists ($uploaddir) ) mkdir($uploaddir,0777); 
      else @chmod($uploaddir,0777);
      $uploaddir1 = $uploaddir.$FeedbackLayout->fpath; 
      if ( !copy($tmp_f_name,$uploaddir1) ) {
          $FeedbackLayout->Err = $FeedbackLayout->Err.$FeedbackLayout->multi['MSG_ERR_FILE_MOVE'].'<br>';
          @chmod($uploaddir,0755); 
          $FeedbackLayout->show_form(); 
          return false;
      }
      @chmod($uploaddir,0755);
    }
    else $FeedbackLayout->fpath = NULL;    
    
    if( !$FeedbackLayout->send_form() ) {
        $FeedbackLayout->show_form_left(); 
    }
    else echo $FeedbackLayout->multi['_TXT_MESS_SENT'];
    break;
  case 'print_all':
    if( !isset($_REQUEST['id_del']) ) $id_del=NULL;
    else $id_del = $_REQUEST['id_del'];
    $FeedbackLayout->print_all($id_del);
    break;
  default:
    //$FeedbackLayout->show_form_left();
    break;      
}
?>