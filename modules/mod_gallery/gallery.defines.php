<?
// ================================================================================================
//    System     : SEOCMS
//    Module     : Gallery
//    Version    : 1.0.0
//    Date       : 01.07.2010   
//    Purpose    : Defines Gallery Module
//    Licensed To: Yaroslav Gyryn   
// ================================================================================================
 include_once( SITE_PATH.'/include/defines.php' );
 include_once( SITE_PATH.'/modules/mod_gallery/gallery.class.php' );
 include_once( SITE_PATH.'/modules/mod_gallery/galleryCtrl.class.php' );
 include_once( SITE_PATH.'/modules/mod_gallery/galleryLayout.class.php' );
 include_once( SITE_PATH.'/modules/mod_gallery/gallery_settings.class.php' );

 define("TblModGallery","mod_gallery");
define("TblModGalleryCat","mod_gallery_spr_category");
// define("TblModGalleryCat","sys_spr_category");   // Спільний загальний довідник для всіх модулів
 define("TblModGalleryTxt","mod_gallery_txt");
 define("TblModGallerySprTxt","mod_gallery_spr_txt");
 
  // --------------- defines for news settings  ---------------  
 define("TblModGallerySet","mod_gallery_set");
 define("TblModGallerySetSprMeta","mod_gallery_set_meta"); 
 ?>