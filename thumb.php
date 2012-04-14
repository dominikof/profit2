<?php
  if (!defined("SITE_PATH")) define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] ); 
  include_once( SITE_PATH.'/include/defines.php' );

     if( !isset($_REQUEST['img']) ) $img=NULL;
     else $img = $_REQUEST['img'];    

     if( !isset($_REQUEST['size_auto']) ) $size_auto = NULL;
     else $size_auto = $_REQUEST['size_auto'];  

     if( !isset($_REQUEST['size_width']) ) $size_width = NULL;
     else $size_width = $_REQUEST['size_width'];  
     
     if( !isset($_REQUEST['size_height']) ) $size_height = NULL;
     else $size_height = $_REQUEST['size_height'];       
     
     if( !isset($_REQUEST['quality']) ) $quality = 100;
     else $quality = $_REQUEST['quality'];     
     
     if( !isset($_REQUEST['wtm']) ) $wtm = NULL;
     else $wtm = $_REQUEST['wtm'];           
     /*
     if ( isset($_SERVER['HTTP_REFERER']) and strstr($_SERVER['HTTP_REFERER'], 'catalog.php') and intval($img)>0 ) {
         $Catalog = new Catalog();
         $settings = $Catalog->GetSettings();
         $img_data = $Catalog->GetPictureData($img);
         $img = $settings['img_path'].'/'.$img_data['id_prop'].'/'.$img_data['path'];
         if ( !file_exists($img) ) return false;
     }
      */
     if (  intval($img)>0 ) {
         $Catalog = new Catalog();
         $settings = $Catalog->GetSettings();
         $img_data = $Catalog->GetPictureData($img);
		 if(!isset($img_data['id_prop'])) {return false;}
         $settings_img_path = $settings['img_path'].'/'.$img_data['id_prop']; // like /uploads/45
         $img_name = $img_data['path'];  // like R1800TII_big.jpg
         $img_with_path = $settings_img_path.'/'.$img_name; // like /uploads/45/R1800TII_big.jpg
     }
     else {
         $img_with_path = $img;
     }   
       
     $img_full_path = SITE_PATH.$img_with_path; // like z:/home/speakers/www/uploads/45/R1800TII_big.jpg
     //echo '<br> $img_full_path='.$img_full_path;
     if ( !file_exists($img_full_path) ) return false;     
     
     //if ( intval($img)>0 and (!isset($_SERVER['HTTP_REFERER']) or !strstr($_SERVER['HTTP_REFERER'], 'catalog.php')) ) return false;

     $thumb=new Thumbnail($img_full_path);
 
     if ( !empty($size_width ) and empty($size_height) ) $thumb->size_width($size_width);
     if ( !empty($size_height) and empty($size_width) ) $thumb->size_height($size_height);
     if ( !empty($size_width) and !empty($size_height) ) $thumb->size($size_width,$size_height); 
     if ( !$size_width and !$size_height and $size_auto ) $thumb->size_auto($size_auto);		            // [OPTIONAL] set the biggest width and height for thumbnail
     
     $thumb->quality=$quality;                  //default 75 , only for JPG format  
    
     if ( $wtm == 'img' ) {
        $thumb->img_watermark = SITE_PATH.'/images/design/m01.png';	    // [OPTIONAL] set watermark source file, only PNG format [RECOMENDED ONLY WITH GD 2 ]
        $thumb->img_watermark_Valing='CENTER';   	    // [OPTIONAL] set watermark vertical position, TOP | CENTER | BOTTOM
        $thumb->img_watermark_Haling='CENTER';   	    // [OPTIONAL] set watermark horizonatal position, LEFT | CENTER | RIGHT
     }
     if ( $wtm == 'txt' ) {
         if ( defined('WATERMARK_TEXT') ) $thumb->txt_watermark=WATERMARK_TEXT;	    // [OPTIONAL] set watermark text [RECOMENDED ONLY WITH GD 2 ]
         else $thumb->txt_watermark='';
         $thumb->txt_watermark_color='000000';	    // [OPTIONAL] set watermark text color , RGB Hexadecimal[RECOMENDED ONLY WITH GD 2 ]
         $thumb->txt_watermark_font=5;	            // [OPTIONAL] set watermark text font: 1,2,3,4,5
         $thumb->txt_watermark_Valing='TOP';   	    // [OPTIONAL] set watermark text vertical position, TOP | CENTER | BOTTOM
         $thumb->txt_watermark_Haling='LEFT';       // [OPTIONAL] set watermark text horizonatal position, LEFT | CENTER | RIGHT
         $thumb->txt_watermark_Hmargin=10;          // [OPTIONAL] set watermark text horizonatal margin in pixels
         $thumb->txt_watermark_Vmargin=10;           // [OPTIONAL] set watermark text vertical margin in pixels     
     }
     
     $thumb->process();   				        // generate image    
     $thumb->show();

     
     
?>