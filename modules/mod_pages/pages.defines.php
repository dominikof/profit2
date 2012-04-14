<?
// ================================================================================================
//    System       : CMS
//    Module      : Dynamic Pages control
//    Date          : 14.03.2011
//    Licensed To : Yaroslav Gyryn
//    Purpose      : Defines Pages
// ================================================================================================
include_once( SITE_PATH.'/include/defines.php' ); 
include_once( SITE_PATH.'/modules/mod_pages/pages.class.php' );
include_once( SITE_PATH.'/modules/mod_pages/backend/pages_backend.class.php' );
include_once( SITE_PATH.'/modules/mod_pages/pagesLayout.class.php' );

define("MOD_PAGES", true);

define("TblModPages","mod_pages");
define("TblModPagesTxt","mod_pages_txt");
  
define("Pages_Img_Path_Small","/images/mod_pages/");
define("Pages_Img_Path",SITE_PATH."/images/mod_pages/");

define("PAGES_USE_SHORT_DESCR", 1);
define("PAGES_USE_SPECIAL_POS", 1);
define("PAGES_USE_IMAGE", 1);
define("PAGES_USE_IS_MAIN", 1);
?>