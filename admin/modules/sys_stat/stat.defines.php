<?
/**
* stat.defines.php
* script for all defenitions for Statictic module
* @package System Package of SEOCMS
* @author Igor Trokhymchuk  <ihor@seotm.com>
* @version 1.1, 02.04.2012
* @copyright (c) 2005+ by SEOTM
*/
include_once( SITE_PATH.'/admin/modules/sys_stat/stat.class.php' );
include_once( SITE_PATH.'/admin/modules/sys_stat/stat_ctrl.class.php' );
include_once( SITE_PATH.'/admin/modules/sys_stat/stat_report.class.php' );
include_once( SITE_PATH.'/admin/modules/sys_stat/stat_set.class.php' );
include_once( SITE_PATH.'/admin/modules/sys_stat/stat_set_ctrl.class.php' );
include_once( SITE_PATH.'/admin/modules/sys_stat/stat_agent.class.php' );
/*
define("STAT_DBNAME", "cms"); // Database Name
define("STAT_USER",_USER); // User to access the database
define("STAT_PASSWD", _PASSWD); // Password to access the database
define('BACK_END_STAT','0');
define('FRONT_END_STAT','1');
define('FIELDS_STAT','1;1;1;1;1;1;1;1;1;1;1;1;1');
*/
define("TblModStatLog","mod_stat_log");
define("TblModStatIP","mod_stat_ip");
define("TblModStatAgent","mod_stat_agent");
define("TblSysSprLanguages","sys_spr_language");
define("TblModStatSet","sys_set_stat");
?>
