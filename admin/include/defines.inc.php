<?  
error_reporting(E_ALL);
//ini_set("memory_limit","128M");
if (!defined("SITE_PATH")) define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] );
if (!defined("NAME_SERVER")) define( "NAME_SERVER", $_SERVER['SERVER_NAME'] );

//if (!defined("SESSION_TYPE")) define( "SESSION_TYPE", 'session_by_ip' );
if (!defined("SESSION_TYPE")) define( "SESSION_TYPE", 'session_by_sid' ); 

if (!defined("SEOCMS_SESSNAME"))    define( "SEOCMS_SESSNAME", "SEOCMSSES" );
ini_set("session.name", SEOCMS_SESSNAME);
//ini_set("session.use_only_cookies", false);

if (!defined("MAKE_DEBUG")) define( "MAKE_DEBUG", "1" );

if (!defined("DEBUG_LANG")) define( "DEBUG_LANG", "3" );
if (!defined("DEBUG_LANG_SHORT")) define( "DEBUG_LANG_SHORT", "ru" );

include_once( SITE_PATH."/include/Conf.inc.php");
include_once( SITE_PATH."/sys/define.php");
 
include_once( SITE_PATH.'/admin/include/classes/PageAdmin.class.php' );
include_once( SITE_PATH.'/admin/include/classes/PagePanel.class.php' );
include_once( SITE_PATH.'/admin/include/classes/sysAuthorization.class.php' );
include_once( SITE_PATH.'/admin/include/classes/AdminHTML.class.php' );
include_once( SITE_PATH.'/admin/include/classes/table.class.php' );
include_once( SITE_PATH.'/admin/modules/sys_group/sys_group.class.php' );
include_once( SITE_PATH.'/admin/modules/sys_spr/sys_spr.class.php' );
include_once( SITE_PATH.'/admin/modules/sys_modules_plug/sys_modules_plag.class.php' );
include_once( SITE_PATH.'/sys/classes/upload/upload.class.php' );
include_once( SITE_PATH.'/sys/classes/upload/uploadImage.class.php' );
include_once( SITE_PATH.'/sys/classes/upload/uploadVideo.class.php' );
include_once( SITE_PATH.'/sys/classes/sysSingleton.class.php' );


/* Include Classes for Statistic Module */
include_once( SITE_PATH.'/admin/modules/sys_stat/stat.defines.php' );

// Include AJAX scripts
include_once( SITE_PATH.'/sys/js/ajax/JsHttpRequest.php'); 


// Definitions for Table from DB
#define("TblSysTxt","sys_spr_txt");
#define("TblSysMsg","sys_spr_msg");
define("TblBackMulti","sys_back_txt");
define("TblFrontMulti","sys_front_txt");

define("TblSysSession","sys_session");
define("TblSysSessionHash","sys_session_hash");
define("TblSysLang","sys_spr_lang");
define("TblSysLogic","sys_spr_logic");
define("TblSysSprMonth","sys_spr_mounth");
define("TblSysSprCountry","sys_spr_country");
define("TblSysSprRegions","sys_spr_regions");
define("TblSysSettings","sys_set");
define("TblSysSetGlobal","sys_set_global"); 
define("TblSysSetGlobalSprMail","sys_set_global_spr_mail");

//--- Users
define("TblSysGroupUsers","sys_group_user");
define("TblSysUser","sys_user");
define("TblSysUserStat","sys_user_stat");

//--- Sys Func
define("TblSysFunc","sys_func");
define("TblSysSprFunc","sys_spr_func");

//--- Admin Menu
define("TblSysMenuAdm","sys_menu_adm");
define("TblSysSprMenuAdm","sys_spr_menu_adm");

//--- Access
define("TblSysAccess","sys_group_func");

//--- Panel Manage
define("TblSysPanelManage","sys_panel_manage");
define("TblSysSprPanelManage","sys_spr_panel_manage");
define("TblSysPanelContent","sys_panel_content");
define("TblSysSprPanelContent","sys_spr_panel_content");

//--- Modules Plug
define("TblSysModPlag","sys_modules_plug");

//--- Tags and Lables
define("TblSysModTags","sys_modules_tags");
define("TblSysModTagsSprName","sys_modules_tags_spr_name"); 

//--- Comments
define("TblSysModComments","sys_modules_comments");

//========================== MODULES ============================
//------------ defines for Sys_spr images ----------------
define("Spr_Img_Path_Small","/images/spr/");
define("Spr_Img_Path",SITE_PATH."/images/spr/");    
define("SPR_MAX_IMAGE_WIDTH","1024"); 
define("SPR_MAX_IMAGE_HEIGHT","1024");
define("SPR_MAX_IMAGE_SIZE",2048 * 1024);  
define("SPR_WATERMARK_TEXT","");
define("SPR_ADDITIONAL_FILES_TEXT","_autozoom_");

//--- Currencies
define("TblSysCurrencies","sys_currencies");
define("TblSysCurrenciesSprName","sys_currencies_spr_name");
?>