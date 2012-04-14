<?php
error_reporting(E_ALL);
//ini_set('display_errors', 1);
//ini_set("memory_limit","32M");
date_default_timezone_set('Europe/Kiev');

if (!defined("SITE_PATH"))          define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] );
if (!defined("NAME_SERVER"))        define( "NAME_SERVER", $_SERVER['SERVER_NAME'] );

if (!defined("SEOCMS_DEBUGNAME"))   define( "SEOCMS_DEBUGNAME", "SEOCMS_make_debug" );

if (!defined("MAKE_DEBUG")){
    if( isset($_REQUEST['make_debug']) ){
        define( "MAKE_DEBUG", intval($_REQUEST['make_debug']) );
    }
    elseif( isset($_COOKIE[SEOCMS_DEBUGNAME]) ){
        define( "MAKE_DEBUG", $_COOKIE[SEOCMS_DEBUGNAME] );
    }
    else define( "MAKE_DEBUG", "0" );
}

if (!defined("DEBUG_LANG"))         define( "DEBUG_LANG", "3" );
if (!defined("USE_TAGS"))           define( "USE_TAGS", "0" );
if (!defined("USE_COMMENTS"))       define( "USE_COMMENTS", "0" );
if (!defined("DEBUG_CURR"))         define( "DEBUG_CURR", "1" ); // debug currency = 1 (USD)
if (!defined("DISCOUNT"))           define( "DISCOUNT", "0.5" ); // default user discount
if (!defined("META_TITLE"))         define( "META_TITLE", "SEOCMS" );  
if (!defined("META_DESCRIPTION"))   define( "META_DESCRIPTION", "" );  
if (!defined("META_KEYWORDS"))      define( "META_KEYWORDS", "" );  

//Привязка модуля к Id динамической страницы 
if (!defined("PAGE_ARTICLE"))       define( "PAGE_ARTICLE", "73" );
if (!defined("PAGE_ASKED"))         define( "PAGE_ASKED", "7" );
if (!defined("PAGE_CATALOG"))       define( "PAGE_CATALOG", "72" );
if (!defined("PAGE_COMMENT"))       define( "PAGE_COMMENT", "80" );
if (!defined("PAGE_DEALERS"))       define( "PAGE_DEALERS", "86" );
if (!defined("PAGE_DICTIONARY"))    define( "PAGE_DICTIONARY", "78" );
if (!defined("PAGE_FEEDBACK"))      define( "PAGE_FEEDBACK", "74" );
if (!defined("PAGE_GALLERY"))       define( "PAGE_GALLERY", "76" );
if (!defined("PAGE_NEWS"))          define( "PAGE_NEWS", "63" );
if (!defined("PAGE_DEPARTMENT"))    define( "PAGE_DEPARTMENT", "81" );
if (!defined("PAGE_VIDEO"))         define( "PAGE_VIDEO", "75" );

include_once( SITE_PATH.'/admin/include/defines.inc.php' );

include_once( SITE_PATH.'/include/classes/PageUser.class.php' );
include_once( SITE_PATH.'/include/classes/FrontForm.class.php' );
include_once( SITE_PATH.'/include/classes/FrontSpr.class.php' );
//include_once( SITE_PATH.'/include/classes/UserAuthorize.class.php' ); 
//include_once( SITE_PATH.'/include/classes/FrontTags.class.php' ); 
//include_once( SITE_PATH.'/include/classes/FrontComments.class.php' ); 

include_once( SITE_PATH.'/modules/mod_article/article.defines.php' );
//include_once( SITE_PATH.'/modules/mod_banner/banner.defines.php' );    
//include_once( SITE_PATH.'/modules/mod_catalog/catalog.defines.php' );
//include_once( SITE_PATH.'/modules/mod_comments/comments.defines.php' );
//include_once( SITE_PATH.'/modules/mod_clients/clients.defines.php' );
//include_once( SITE_PATH.'/modules/mod_dealers/dealers.defines.php' );
//include_once( SITE_PATH.'/modules/mod_faq/faq.defines.php' );
include_once( SITE_PATH.'/modules/mod_feedback/feedback.defines.php' );
//include_once( SITE_PATH.'/modules/mod_glossary/glossary.defines.php' ); 
//include_once( SITE_PATH.'/modules/mod_job/job.defines.php' );
include_once( SITE_PATH.'/modules/mod_news/news.defines.php' ); 
//include_once( SITE_PATH.'/modules/mod_order/order.defines.php' );
include_once( SITE_PATH.'/modules/mod_pages/pages.defines.php' );
//include_once( SITE_PATH.'/modules/mod_poll/poll.defines.php' );
//include_once( SITE_PATH.'/modules/mod_public/public.defines.php' );
//include_once( SITE_PATH.'/modules/mod_user/user.defines.php' );
//include_once( SITE_PATH.'/modules/mod_asked/asked.defines.php' );
include_once( SITE_PATH.'/modules/mod_gallery/gallery.defines.php' ); 
//include_once( SITE_PATH.'/modules/mod_video/video.defines.php' );
//include_once( SITE_PATH.'/modules/mod_dictionary/defines.php' );
//include_once( SITE_PATH.'/modules/mod_department/department.defines.php' );
//include_once( SITE_PATH.'/modules/mod_share/share.defines.php' );

?>