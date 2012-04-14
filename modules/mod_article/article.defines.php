<?
// ================================================================================================
//    System     : SEOCMS
//    Module     : Article
//    Version    : 1.0.0
//    Date       : 4.06.2007   
//    Licensed To:
//                 Igor  Trokhymchuk  ihoru@mail.ru
//                 Andriy Lykhodid    las_zt@mail.ru
//
//    Purpose    : defines Article Module
//
// ================================================================================================
 include_once( SITE_PATH.'/include/defines.php' );
 include_once( SITE_PATH.'/modules/mod_article/article.class.php' );
 include_once( SITE_PATH.'/modules/mod_article/articleCtrl.class.php' );
 include_once( SITE_PATH.'/modules/mod_article/articleLayout.class.php' );
 include_once( SITE_PATH.'/modules/mod_article/article_settings.class.php' );

 define("MOD_ARTICLE", true);   

 define("TblModArticle","mod_article");
 define("TblModArticleCat","mod_article_spr_category");
 define("TblModArticleTxt","mod_article_txt");
 define("TblModArticleSprTxt","mod_article_spr_txt");
 

  // --------------- defines for news settings  ---------------  
 define("TblModArticleSet","mod_article_set");
 define("TblModArticleSetSprMeta","mod_article_set_meta"); 
  
  //------------ defines for article system links ----------------
 define("TblModArticleLinks","mod_article_links");
 //define("TblModArticleLinksCat","mod_article_links_cat");
 
 
 
 //------------ defines for images ----------------
define("TblModArticleImg","mod_article_img");
define("TblModArticleImgSpr","mod_article_img_spr");
define("ArticleImg_Path","/images/mod_article");  
define("ArticleImg_Full_Path",SITE_PATH.ArticleImg_Path);  
if (!defined("ARTICLE_MAX_IMAGE_WIDTH")) define("ARTICLE_MAX_IMAGE_WIDTH","2024"); 
if (!defined("ARTICLE_MAX_IMAGE_HEIGHT")) define("ARTICLE_MAX_IMAGE_HEIGHT","2024");
if (!defined("ARTICLE_MAX_IMAGE_SIZE")) define("ARTICLE_MAX_IMAGE_SIZE",2048 * 1024);  
if (!defined("ARTICLE_WATERMARK_TEXT")) define("ARTICLE_WATERMARK_TEXT","seotm.com");
if (!defined("ARTICLE_ADDITIONAL_FILES_TEXT")) define("ARTICLE_ADDITIONAL_FILES_TEXT","_autozoom_");
if (!defined("ARTICLE_MAX_IMAGES_QUANTITY")) define("ARTICLE_MAX_IMAGES_QUANTITY","85"); 
 
 ?>