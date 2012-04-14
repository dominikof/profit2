<?php                                     /* map.php */
// ================================================================================================
// System : CMS
// Module : map.php
// Date : 14.01.2011
// Licensed To: Yaroslav Gyryn
// Purpose : Show Map of site
// ================================================================================================
  if (!defined("SITE_PATH")) define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] ); 
  include_once( SITE_PATH.'/include/defines.php' );

  $Page = new PageUser();
  if(empty($Page->FrontendPages)) 
        $Page->FrontendPages = new FrontendPages();
  $title = $Page->FrontendPages->multi['_TXT_SITE_MAP'];
  $Page->FrontendPages->page = 9999;
  $Page->SetTitle( $title);
  $Page->SetDescription($title);
  $Page->SetKeywords($title);   
  
  $Page->WriteHeader();
  $Page->Form->WriteContentHeader($title, false,false);
  $Page->FrontendPages->MAP();
  $Page->Form->WriteContentFooter();
  $Page->WriteFooter(); 
?>