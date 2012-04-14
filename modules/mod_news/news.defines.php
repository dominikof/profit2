<?
// ================================================================================================
//    System     : CMS
//    Module     : News
//    Date       : 01.03.2011
//    Licensed To: Yaroslav Gyryn
//    Purpose    : Defines News
// ================================================================================================
include_once( SITE_PATH.'/include/defines.php' ); 
include_once( SITE_PATH.'/modules/mod_news/news.class.php' );
include_once( SITE_PATH.'/modules/mod_news/newsLayout.class.php' );
include_once( SITE_PATH.'/modules/mod_news/newsCtrl.class.php' );
include_once( SITE_PATH.'/modules/mod_news/news_settings.class.php' );
include_once( SITE_PATH.'/include/mail/Mail.class.php' ); 

define("MOD_NEWS", true);

define("TblModNews","mod_news");
define("TblModNewsCat","mod_news_spr_category");   // Спільний загальний довідник для всіх модулів
define("TblModNewsRel","mod_news_relart");
define("TblModNewsSprSbj","mod_news_spr_subject");
define("TblModNewsTop","mod_news_top_txt");           // Топ новина
define("TblModNewsSprMain","mod_news_spr_main");   // Головне в новині

define("TblModNewsSprShrt","mod_news_spr_short");
define("TblModNewsSprFull","mod_news_spr_full");
define("TblModNewsSprTxt","mod_news_spr_txt");
define("TblModNewsSprKeywords","mod_news_spr_keywords");
define("TblModNewsSprDescription","mod_news_spr_description");
define("TblModNewsImgSprName","mod_news_img_spr_name");
define("TblModNewsImgSprDescr","mod_news_img_spr_descr");
define("TblModNewsRelatProd","mod_news_relat_prod");

// --------------- defines for news subscribe  ---------------
define("TblModNewsSubscr","mod_news_subscribers");
define("TblModNewsSubscrCat","mod_news_subscribe_cat");
define("TblModNewsDispatch","mod_news_dispatch"); 
define("TblModNewsDispatchSet","mod_news_dispatch_set");

// --------------- defines for news settings  ---------------  
define("TblModNewsSet","mod_news_set");
define("TblModNewsSetSprTitle","mod_news_set_spr_title"); 
define("TblModNewsSetSprDescription","mod_news_set_spr_description"); 
define("TblModNewsSetSprKeywords","mod_news_set_spr_keywords"); 

// --------------------------  defines for RSS chanels ---------------------
define("TblModNewsRss","mod_news_rss");
define("TblModNewsRssSprDescr","mod_news_rss_decription");
define("ADD_COUNT_CHANEL", "3");

//------------ defines for news system links ----------------
define("TblModNewsLinks","mod_news_links");

//------------ defines for images ----------------
define("TblModNewsImg","mod_news_img");
define("NewsImg_Path","/images/mod_news/");  
define("NewsImg_Full_Path",SITE_PATH.NewsImg_Path);  
if (!defined("NEWS_MAX_IMAGE_WIDTH")) define("NEWS_MAX_IMAGE_WIDTH","2024"); 
if (!defined("NEWS_MAX_IMAGE_HEIGHT")) define("NEWS_MAX_IMAGE_HEIGHT","2024");
if (!defined("NEWS_MAX_IMAGE_SIZE")) define("NEWS_MAX_IMAGE_SIZE",2048 * 1024);  
if (!defined("NEWS_WATERMARK_TEXT")) define("NEWS_WATERMARK_TEXT","SEOTM");
if (!defined("NEWS_ADDITIONAL_FILES_TEXT")) define("NEWS_ADDITIONAL_FILES_TEXT","_zoom_");
if (!defined("NEWS_MAX_IMAGES_QUANTITY")) define("NEWS_MAX_IMAGES_QUANTITY","85"); 
?>