<?php
/**
* feedback.defines.php 
* All Definitions for module of feedback
* @package Feedback Package of SEOCMS
* @author Igor Trokhymchuk  <ihor@seotm.com>
* @version 1.1, 22.12.2010
* @copyright (c) 2010+ by SEOTM
*/
include_once( SITE_PATH.'/include/defines.php' );
include_once( SITE_PATH.'/modules/mod_feedback/feedback.class.php' );
include_once( SITE_PATH.'/modules/mod_feedback/feedbackCtrl.class.php' );
include_once( SITE_PATH.'/modules/mod_feedback/feedbackLayout.class.php' ); 
include_once( SITE_PATH.'/include/mail/Mail.class.php' );

define("MOD_FEEDBACK", true);

define("TblModfeedback","mod_feedback");
define("TblModFeedbackSerfing","mod_feedback_serfing");
define("TblModFeedbackSprTxt","mod_feedback_spr_txt");

//------------ defines for Files ----------------
define("FeedbackUseFiles", "1");
define("FeedbackUploadFilesPath","/images/mod_feedback/");
if (!defined("Feedback_MAX_FILE_SIZE")) define("Feedback_MAX_FILE_SIZE", 11000*1024);
?>