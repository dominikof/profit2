<?php                                     /* contacts.php */
if (!defined("SITE_PATH")) define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] ); 
include_once( SITE_PATH.'/include/defines.php' );

$Page = new PageUser();
if(!isset ($Page->FrontendPages)) 
    $Page->FrontendPages= &check_init('FrontendPages', 'FrontendPages');

if( !isset ( $_REQUEST['page'] ) ) $Page->FrontendPages->page = PAGE_FEEDBACK; 
else $Page->FrontendPages->page = $Page->Form->GetRequestTxtData($_REQUEST['page'], 1);  

$Page->FrontendPages->lang_id = _LANG_ID;  
$Page->FrontendPages->page_txt = $Page->FrontendPages->GetPageTxt($Page->FrontendPages->page);
$Page->FrontendPages->GetTitle()==NULL          ? $title = META_TITLE               : $title = $Page->FrontendPages->GetTitle();
$Page->FrontendPages->GetDescription()==NULL    ? $Description = META_DESCRIPTION   : $Description = $Page->FrontendPages->GetDescription();
$Page->FrontendPages->GetKeywords()==NULL       ? $Keywords = META_KEYWORDS         : $Keywords = $Page->FrontendPages->GetKeywords();

$Page->SetTitle( $title );
$Page->SetDescription( $Description );
$Page->SetKeywords( $Keywords );   

$Page->WriteHeader();
$Page->FrontendPages->showContent('don_show_image_border');
include_once(SITE_PATH.'/modules/mod_feedback/feedback.php');
$Page->WriteFooter();
?>