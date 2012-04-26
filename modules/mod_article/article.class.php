<?
// ================================================================================================
//    System     : SEOCMS
//    Module     : Article
//    Version    : 1.0.0
//    Date       : 27.01.2006
//    Licensed To:
//                 Igor  Trokhymchuk  ihoru@mail.ru
//                 Andriy Lykhodid    las_zt@mail.ru
//
//    Purpose    : Class definition for Article - moule
//
// ================================================================================================


// ================================================================================================
//    Class             : Article
//    Version           : 1.0.0
//    Date              : 27.01.2006
//    Constructor       : Yes
//    Parms             :
//    Returns           : None
//    Description       : Article Module
// ================================================================================================
//    Programmer        :  Andriy Lykhodid
//    Date              :  27.01.2006
//    Reason for change :  Creation
//    Change Request Nbr:  N/A
// ================================================================================================
class Article {
    var $id;
    var $dttm;
    var $category;
    var $status;
    var $img;
    var $position;
    var $name;
    var $short;
    var $full;

    var $Right;
    var $Form;
    var $Msg;
    var $Spr;

    var $page;   
    var $display;
    var $sort;
    var $start;
    var $rows;

    var $user_id;
    var $use_image;
    var $module;
    var $task = NULL;

    var $fltr;    // filter of group Production

    var $lang_id;
    var $sel = NULL;
    var $Err = NULL;
    var $title;
    var $keywords;
    var $description;

    var $str_cat;
    var $str_art;

    var $settings = null;
 
    // ================================================================================================
    //    Function          : Article (Constructor)
    //    Version           : 1.0.0
    //    Date              : 27.01.2006
    //    Parms             :
    //    Returns           :
    //    Description       : Article
    // ================================================================================================
    function Article()
    {
        if( defined("_LANG_ID") ) $this->lang_id = _LANG_ID;
        $this->Right =  new Rights;                   /* create Rights obect as a property of this class */
        $this->Form = new Form( 'form_art' );        /* create Form object as a property of this class */
        $this->Msg = new ShowMsg();                   /* create ShowMsg object as a property of this class */
        $this->Msg->SetShowTable(TblModArticleSprTxt);
        $this->use_image=1;
        $this->Spr = new SysSpr( NULL, NULL, NULL, NULL, NULL, NULL, NULL ); /* create SysSpr object as a property of this class */
     
        $this->settings = $this->GetSettings();
    }// end of Article (Constructor) 

 
    // ================================================================================================
    // Function : GetImagesCount
    // Version : 1.0.0
    // Date : 28.11.2006
    //
    // Parms : $id_art  / id of the article
    // Returns :
    // Description : return count of images for current article with $id
    // ================================================================================================
    // Programmer : Ihor Trokhymchuk
    // Date : 28.11.2006 
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
   function GetImagesCount($id_art)
   {
       $image = NULL;
       $tmp_db = DBs::getInstance();
           
       $q = "SELECT * FROM `".TblModArticleImg."` WHERE 1 AND `id_art`='".$id_art."' order by `move`";
       $res = $tmp_db->db_Query($q);
       //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
       if ( !$res or !$tmp_db->result ) return false;
       $rows = $tmp_db->db_GetNumRows();
       return $rows;           
   } //end of function GetImagesCount()       
       
   // ================================================================================================
   // Function : GetImages
   // Version : 1.0.0
   // Date : 13.10.2006
   //
   // Parms : $id_art  / id of the article
   // Returns : return $image for current value with cod=$cod
   // Description : return image for current value with cod=$cod, if it is exist 
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 13.10.2006
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function GetImages($id_art)
   {
       $image = NULL;
       $tmp_db = DBs::getInstance();
           
       $q = "SELECT * FROM `".TblModArticleImg."` WHERE 1 AND `id_art`='".$id_art."' order by `move`";
       $res = $tmp_db->db_Query($q);
       //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
       if ( !$res or !$tmp_db->result ) return false;
       $rows = $tmp_db->db_GetNumRows();
       //echo '<br>$rows='.$rows;
       $arr = NULL;
       for($i=0; $i<$rows; $i++){
         $row = $tmp_db->db_FetchAssoc();
         //echo '<br>$row[id_val]'.$row['id_val'];
         $arr[$i] = $row['path']; 
       }
       return $arr;           
   } //end of function GetImages()
       
       
   // ================================================================================================
   // Function : GetImagesToShow
   // Version : 1.0.0
   // Date : 13.10.2006
   //
   // Parms : $id_art  / id of the user
   // Returns : return $image for current value with cod=$cod
   // Description : return image for current value with cod=$cod, if it is exist 
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 13.10.2006
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function GetImagesToShow($id_art)
   {
       $image = NULL;
       $tmp_db = DBs::getInstance();
           
       $q = "SELECT * FROM `".TblModArticleImg."` WHERE 1 AND `id_art`='".$id_art."' AND `show`=1 order by `move`";
       $res = $tmp_db->db_Query($q);
       //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
       if ( !$res or !$tmp_db->result ) return false;
       $rows = $tmp_db->db_GetNumRows();
       //echo '<br>$rows='.$rows;
       $arr = NULL;
       for($i=0; $i<$rows; $i++){
         $row = $tmp_db->db_FetchAssoc();
         //echo '<br>$row[id_val]'.$row['id_val'];
         $arr[$i]['id'] = $row['id'];
         $arr[$i]['path'] = $row['path']; 
         $arr[$i]['descr'] = $row['descr'];
         $arr[$i]['move'] = $row['move'];
         $arr[$i]['show'] = $row['show'];
       }
       return $arr;           
   } //end of function GetImagesToShow()
   
   // ================================================================================================
   // Function : GetMainImage
   // Version : 1.0.0
   // Date : 13.10.2006
   //
   // Parms :   $id_art    / id of the user
   //           $part       /  for front-end or for back-end
   // Returns : return $image for current value with cod=$cod
   // Description : return image for current value with cod=$cod, if it is exist 
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 13.10.2006
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function GetMainImage($id_art, $part = 'front')
   {
       $image = NULL;
       $tmp_db = DBs::getInstance();
           
       $q = "SELECT * FROM `".TblModArticleImg."` WHERE 1 AND `id_art`='".$id_art."'";
       if ($part=='front') $q = $q." AND `show`=1";
       $q = $q." order by `move`";
       $res = $tmp_db->db_Query($q);
       //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
       if ( !$res or !$tmp_db->result ) return false;
       $rows = $tmp_db->db_GetNumRows();
       //echo '<br>$rows='.$rows;
       $row = $tmp_db->db_FetchAssoc();
       return $row['path'];           
   } //end of function GetMainImage()
   
   // ================================================================================================
   // Function : GetMainImageData
   // Version : 1.0.0
   // Date : 13.10.2006
   //
   // Parms :   $id_art    / id of the user
   //           $part       /  for front-end or for back-end
   // Returns : return $image for current value with cod=$cod
   // Description : return image for current value with cod=$cod, if it is exist 
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 13.10.2006
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function GetMainImageData($id_art, $part = 'front')
   {
       $image = NULL;
       $tmp_db = DBs::getInstance();
           
       $q = "SELECT * FROM `".TblModArticleImg."` WHERE 1 AND `id_art`='".$id_art."'";
       if ($part=='front') $q = $q." AND `show`=1";
       $q = $q." order by `move`";
       $res = $tmp_db->db_Query($q);
       //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
       if ( !$res or !$tmp_db->result ) return false;
       $rows = $tmp_db->db_GetNumRows();
       //echo '<br>$rows='.$rows;
       $row = $tmp_db->db_FetchAssoc();
       return $row;           
   } //end of function GetMainImageData()       
     
    /**
    * Class method ShowImage
    * function for show image 
    * @param $img - id of the picture, or relative path of the picture /images/mod_articles/24094/12984541610.jpg or name of the picture 12984541610.jpg
    * @param $id_art - id of the news
    * @param $size - Can be "size_auto" or  "size_width" or "size_height"
    * @param $quality - quality of the image from 0 to 100
    * @param $wtm - make watermark or not. Can be "txt" or "img"
    * @param $parameters - other parameters for TAG <img> like border
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.1, 20.10.2011
    * @return true or false
    */
    function ShowImage($img = NULL, $id_art, $size = NULL, $quality = NULL, $wtm = NULL, $parameters = NULL, $return_src=false)
    {
        $size_auto = NULL;
        $size_width = NULL;
        $size_height = NULL;
        $alt = NULL;
        $title = NULL;
        $settings_img_path = $this->settings['img_path'];
        //echo "<br>img=".$img;
        
        if( !strstr($img, '.') AND !strstr($img, '/') ){
            $img_data = $this->GetPictureData($img);
            if(!isset($img_data['id_art'])) {return false;}
            $settings_img_path = $this->settings['img_path'].'/'.$img_data['id_art']; // like /uploads/45
            $img_name = $img_data['path'];  // like R1800TII_big.jpg
            $img_with_path = $settings_img_path.$img_name; // like /uploads/45/R1800TII_big.jpg
            if ( !strstr($parameters, 'alt') ) $alt = $this->GetPictureAlt($img);
            if ( !strstr($parameters, 'title') ) $title = $this->GetPictureTitle($img);
        }
        else {
            $rpos = strrpos($img,'/');
            if($rpos>0){
                $settings_img_path = substr($img, 0, $rpos);
                $img_name = substr($img, $rpos+1, strlen($img)-$rpos );
                $img_with_path = $img;
            }                                                                                    
            else{
                if(!$id_art) return false;
                $settings_img_path = $this->settings['img_path'].'/'.$id_art; // like /uploads/45
                $img_name = $img; 
                $img_with_path = $settings_img_path.'/'.$img;
            }
            $alt ='';
            $title= '';
        }
        //echo '<br>$img_name='.$img_name.'<br>$img_with_path='.$img_with_path;
        $mas_img_name=explode(".",$img_with_path);
        
        if ( strstr($size,'size_width') ){ 
            $size_width = substr( $size, strrpos($size,'=')+1, strlen($size) );
            $img_name_new = $mas_img_name[0].ARTICLE_ADDITIONAL_FILES_TEXT.'width_'.$size_width.'.'.$mas_img_name[1];
        }
        elseif ( strstr($size,'size_auto') ) {
            $size_auto = substr( $size, strrpos($size,'=')+1, strlen($size) );
            $img_name_new = $mas_img_name[0].ARTICLE_ADDITIONAL_FILES_TEXT.'auto_'.$size_auto.'.'.$mas_img_name[1];
        }
        elseif ( strstr($size,'size_height') ) {
            $size_height = substr( $size, strrpos($size,'=')+1, strlen($size) );
            $img_name_new = $mas_img_name[0].ARTICLE_ADDITIONAL_FILES_TEXT.'height_'.$size_height.'.'.$mas_img_name[1];
        }
        elseif(empty($size)) $img_name_new = $mas_img_name[0].'.'.$mas_img_name[1];
        //echo '$img_name_new='.$img_name_new;
        $img_full_path_new = SITE_PATH.$img_name_new; 
        //if exist local small version of the image then use it
        if( file_exists($img_full_path_new)){
            //echo 'exist';
            //echo '<br>$settings_img_path='.$settings_img_path.' $img_full_path='.$img_full_path;
            if ( !strstr($parameters, 'alt') ) $alt = $this->GetPictureAlt($img);
            if ( !strstr($parameters, 'title') ) $title = $this->GetPictureTitle($img);
            if ( !strstr($parameters, 'alt') )  $parameters = $parameters.' alt="'.$alt.'"';
            if ( !strstr($parameters, 'title') ) $parameters = $parameters.' title=" '.$title.' "';        
            if($return_src) $str = $img_name_new;
            else $str = '<img src="'.$img_name_new.'" '.$parameters.' />';
        }                 
        //else use original image on the server SITE_PATH and make small version on local server
        else {         
            //echo 'Not  exist';
            $img_full_path = SITE_PATH.$img_with_path; // like z:/home/speakers/www/uploads/45/R1800TII_big.jpg
            //echo '<br> $img_full_path='.$img_full_path;
            if ( !file_exists($img_full_path) ) return false;

            $thumb = new Thumbnail($img_full_path);
            //echo '<br>$thumb->img[x_thumb]='.$thumb->img['x_thumb'].' $thumb->img[y_thumb]='.$thumb->img['y_thumb'];
            $src_x = $thumb->img['x_thumb'];
            $src_y = $thumb->img['y_thumb'];
            if ( !empty($size_width ) and empty($size_height) ) $thumb->size_width($size_width);
            if ( !empty($size_height) and empty($size_width) ) $thumb->size_height($size_height);
            if ( !empty($size_width) and !empty($size_height) ) $thumb->size($size_width,$size_height); 
            if ( !$size_width and !$size_height and $size_auto ) $thumb->size_auto($size_auto); // [OPTIONAL] set the biggest width and height for thumbnail
            //echo '<br>$thumb->img[x_thumb]='.$thumb->img['x_thumb'].' $thumb->img[y_thumb]='.$thumb->img['y_thumb'];
            
            //if original image smaller than thumbnail then use original image and don't create thumbnail
            if($thumb->img['x_thumb']>=$src_x OR $thumb->img['y_thumb']>=$src_y){
                $img_full_path = $settings_img_path.'/'.$img_name;
                //echo '<br>$settings_img_path='.$settings_img_path.' $img_full_path='.$img_full_path;
                if ( !strstr($parameters, 'alt') ) $alt = $this->GetPictureAlt($img);
                if ( !strstr($parameters, 'title') ) $title = $this->GetPictureTitle($img);
                if ( !strstr($parameters, 'alt') )  $parameters = $parameters.' alt="'.$alt.'"';
                if ( !strstr($parameters, 'title') ) $parameters = $parameters.' title=" '.$title.' "';        
                if($return_src) $str = $img_with_path;
                else $str = '<img src="'.$img_with_path.'" '.$parameters.' />';
            }
            else{
                $thumb->quality=$quality;                  //default 75 , only for JPG format  
                //echo '<br>$wtm='.$wtm;
                if ( $wtm == 'img' ) {
                    $thumb->img_watermark = NULL; //SITE_PATH.'/images/design/m01.png';        // [OPTIONAL] set watermark source file, only PNG format [RECOMENDED ONLY WITH GD 2 ]
                    $thumb->img_watermark_Valing='CENTER';           // [OPTIONAL] set watermark vertical position, TOP | CENTER | BOTTOM
                    $thumb->img_watermark_Haling='CENTER';           // [OPTIONAL] set watermark horizonatal position, LEFT | CENTER | RIGHT
                }
                if ( $wtm == 'txt' ) {
                    if ( defined('WATERMARK_TEXT') ) $thumb->txt_watermark=ARTICLE_WATERMARK_TEXT;        // [OPTIONAL] set watermark text [RECOMENDED ONLY WITH GD 2 ]
                    else $thumb->txt_watermark='';
                    $thumb->txt_watermark_color='000000';        // [OPTIONAL] set watermark text color , RGB Hexadecimal[RECOMENDED ONLY WITH GD 2 ]
                    $thumb->txt_watermark_font=5;                // [OPTIONAL] set watermark text font: 1,2,3,4,5
                    $thumb->txt_watermark_Valing='TOP';           // [OPTIONAL] set watermark text vertical position, TOP | CENTER | BOTTOM
                    $thumb->txt_watermark_Haling='LEFT';       // [OPTIONAL] set watermark text horizonatal position, LEFT | CENTER | RIGHT
                    $thumb->txt_watermark_Hmargin=10;          // [OPTIONAL] set watermark text horizonatal margin in pixels
                    $thumb->txt_watermark_Vmargin=10;           // [OPTIONAL] set watermark text vertical margin in pixels     
                }

                if( !strstr($img, '.') AND !strstr($img, '/') ){   
                    $mas_img_name=explode(".",$img_name);
                    //$img_name_new = $mas_img_name[0].NEWS_NEWS_ADDITIONAL_FILES_TEXT.intval($thumb->img['x_thumb']).'x'.intval($thumb->img['y_thumb']).'.'.$mas_img_name[1];
                    if(!empty($size_width )) 
                        $img_name_new = $mas_img_name[0].ARTICLE_ADDITIONAL_FILES_TEXT.'width_'.$size_width.'.'.$mas_img_name[1];
                    elseif(!empty($size_auto )) 
                        $img_name_new = $mas_img_name[0].ARTICLE_ADDITIONAL_FILES_TEXT.'auto_'.$size_auto.'.'.$mas_img_name[1];
                    elseif(!empty($size_height )) 
                        $img_name_new = $mas_img_name[0].ARTICLE_ADDITIONAL_FILES_TEXT.'height_'.$size_height.'.'.$mas_img_name[1];
                    $img_full_path_new = SITE_PATH.$settings_img_path.'/'.$img_name_new;
                    $img_src = $settings_img_path.'/'.$img_name_new;
                    $uploaddir = SITE_PATH.$settings_img_path;
                }
                else {
                    $mas_img_name=explode(".",$img_with_path);
                    //$img_name_new = $mas_img_name[0].NEWS_NEWS_ADDITIONAL_FILES_TEXT.intval($thumb->img['x_thumb']).'x'.intval($thumb->img['y_thumb']).'.'.$mas_img_name[1];
                    if(!empty($size_width )) 
                        $img_name_new = $mas_img_name[0].ARTICLE_ADDITIONAL_FILES_TEXT.'width_'.$size_width.'.'.$mas_img_name[1];
                    elseif(!empty($size_auto )) 
                        $img_name_new = $mas_img_name[0].ARTICLE_ADDITIONAL_FILES_TEXT.'auto_'.$size_auto.'.'.$mas_img_name[1];
                    elseif(!empty($size_height )) 
                        $img_name_new = $mas_img_name[0].ARTICLE_ADDITIONAL_FILES_TEXT.'height_'.$size_height.'.'.$mas_img_name[1];
                    $img_full_path_new = SITE_PATH.$img_name_new; 
                    $img_src = $img_name_new;
                    $rpos = strrpos($img_with_path,'/');
                    //echo '<br />$img_with_path='.$img_with_path.' $rpos='.$rpos;
                    if($rpos>0){
                        $uploaddir = SITE_PATH.substr($img_with_path, 0, $rpos);
                    }
                    else $uploaddir = SITE_PATH.$settings_img_path;  
                }
                if ( !strstr($parameters, 'alt') ) $alt = $this->GetPictureAlt($img);
                if ( !strstr($parameters, 'title') ) $title = $this->GetPictureTitle($img);
                
                //echo '<br>$img_name_new='.$img_name_new;  
                //echo '<br>$img_full_path_new='.$img_full_path_new;
                //echo '<br>$img_src='.$img_src;
                //echo '<br>$uploaddir='.$uploaddir;
                
                if ( !strstr($parameters, 'alt') )  $parameters = $parameters.' alt="'.htmlspecialchars($alt).'"';
                if ( !strstr($parameters, 'title') ) $parameters = $parameters.' title=" '.htmlspecialchars($title).' "';

                //echo '<br>$uploaddir='.$uploaddir; 
                if ( !file_exists($img_full_path_new) ) {
                    if( !file_exists ($uploaddir) ) mkdir($uploaddir,0777);
                    if( file_exists($uploaddir) ) @chmod($uploaddir,0777);
                    $thumb->process();       // generate image  
                    //make new image like R1800TII_big.jpg -> R1800TII_big_autozoom_100x84.jpg 
                    $thumb->save($img_full_path_new);
                    @chmod($uploaddir,0755);
                    $params = "img=".$img."&".$size;
                }
                if($return_src) $str = $img_src;
                else $str = '<img src="'.$img_src.'" '.$parameters.' />';
            }//end else  
        }//end else  
        return $str;
    } // end of function ShowImage()    
    
    /*  
    // ================================================================================================
    // Function : ShowImage
    // Version : 1.0.0
    // Date : 17.11.2006
    //
    // Parms :  $img - path of the picture
    //          $size -  Can be "size_auto" or  "size_width" or "size_height"
    //          $quality - quality of the image
    //          $wtm - make watermark or not. Can be "txt" or "img"
    //          $parameters - other parameters for TAG <img> like border
    // Returns : $res / Void
    // Description : Show images from catalogue
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 17.11.2006  
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function ShowImage($img = NULL, $id_art, $size = NULL, $quality = NULL, $wtm = NULL, $parameters = NULL)
    {
        $size_auto = NULL;
        $size_width = NULL;
        $size_height = NULL;
        $alt = NULL;
        $title = NULL;
        $settings_img_path = ArticleImg_Path;
        //echo "<br>img=".$img.'<br>$settings_img_path='.$settings_img_path;
        
        if( !strstr($img, '.') ){
            $img_data = $this->GetPictureData($img);
            if(!isset($img_data['id_art'])) {return false;}
            $img_name = $img_data['path'];  // like R1800TII_big.jpg
            $img_with_path = $this->GetImgPath($img_name, $id_art);
        }
        else {
            //$img_with_path = $this->GetImgPath($img, $id_art);
            $img_with_path = $settings_img_path.$id_art.'/'.$img;
            $img_name = $img;
            //echo $img_with_path;
            //echo '|'.$img;
        }
        //echo '<br>$img_name='.$img_name.'<br>$img_with_path='.$img_with_path;
        $mas_img_name=explode(".",$img_with_path);
        
        if ( strstr($size,'size_width') ){ 
            $size_width = substr( $size, strrpos($size,'=')+1, strlen($size) );
            $img_name_new = $mas_img_name[0].ARTICLE_ADDITIONAL_FILES_TEXT.'width_'.$size_width.'.'.$mas_img_name[1];
        }
        elseif ( strstr($size,'size_auto') ) {
            $size_auto = substr( $size, strrpos($size,'=')+1, strlen($size) );
            $img_name_new = $mas_img_name[0].ARTICLE_ADDITIONAL_FILES_TEXT.'auto_'.$size_auto.'.'.$mas_img_name[1];
        }
        elseif ( strstr($size,'size_height') ) {
            $size_height = substr( $size, strrpos($size,'=')+1, strlen($size) );
            $img_name_new = $mas_img_name[0].ARTICLE_ADDITIONAL_FILES_TEXT.'height_'.$size_height.'.'.$mas_img_name[1];
        }
        elseif(empty($size)) $img_name_new = $mas_img_name[0].'.'.$mas_img_name[1];
        //echo '$img_name_new='.$img_name_new;
        $img_full_path_new = SITE_PATH.$img_name_new; 
        //if exist local small version of the image then use it
        if( file_exists($img_full_path_new)){
            //echo 'exist';
            //echo '<br>$settings_img_path='.$settings_img_path.' $img_full_path='.$img_full_path;
            //if ( !strstr($parameters, 'alt') ) $alt = $this->GetPictureAlt($img);
            //if ( !strstr($parameters, 'title') ) $title = $this->GetPictureTitle($img);
            //if ( !strstr($parameters, 'alt') )  $parameters = $parameters.' alt="'.$alt.'"';
            //if ( !strstr($parameters, 'title') ) $parameters = $parameters.' title=" '.$title.' "';        
            $str = '<img src="'.$img_name_new.'" '.$parameters.' />';
        }                 
        //else use original image on the server SITE_PATH and make small version on local server
        else {         
            //echo 'Not  exist';
            $img_full_path = SITE_PATH.$img_with_path; // like z:/home/speakers/www/uploads/45/R1800TII_big.jpg
            //echo '<br> $img_full_path='.$img_full_path;
            if ( !file_exists($img_full_path) ) return false;

            $thumb = new Thumbnail($img_full_path);
            //echo '<br>$thumb->img[x_thumb]='.$thumb->img['x_thumb'].' $thumb->img[y_thumb]='.$thumb->img['y_thumb'];
            $src_x = $thumb->img['x_thumb'];
            $src_y = $thumb->img['y_thumb'];
            if ( !empty($size_width ) and empty($size_height) ) $thumb->size_width($size_width);
            if ( !empty($size_height) and empty($size_width) ) $thumb->size_height($size_height);
            if ( !empty($size_width) and !empty($size_height) ) $thumb->size($size_width,$size_height); 
            if ( !$size_width and !$size_height and $size_auto ) $thumb->size_auto($size_auto); // [OPTIONAL] set the biggest width and height for thumbnail
            //echo '<br>$thumb->img[x_thumb]='.$thumb->img['x_thumb'].' $thumb->img[y_thumb]='.$thumb->img['y_thumb'];
            
            //if original image smaller than thumbnail then use original image and don't create thumbnail
            if($thumb->img['x_thumb']>=$src_x OR $thumb->img['y_thumb']>=$src_y){
                $img_full_path = $settings_img_path.'/'.$img_name;
                //echo '<br>$settings_img_path='.$settings_img_path.' $img_full_path='.$img_full_path;
                //if ( !strstr($parameters, 'alt') ) $alt = $this->GetPictureAlt($img);
                //if ( !strstr($parameters, 'title') ) $title = $this->GetPictureTitle($img);
                //if ( !strstr($parameters, 'alt') )  $parameters = $parameters.' alt="'.$alt.'"';
                //if ( !strstr($parameters, 'title') ) $parameters = $parameters.' title=" '.$title.' "';        
                $str = '<img src="'.$img_with_path.'" '.$parameters.' />';
            }
            else{
                $thumb->quality=$quality;                  //default 75 , only for JPG format  
                //echo '<br>$wtm='.$wtm;
                if ( $wtm == 'img' ) {
                    $thumb->img_watermark = NULL; //SITE_PATH.'/images/design/m01.png';        // [OPTIONAL] set watermark source file, only PNG format [RECOMENDED ONLY WITH GD 2 ]
                    $thumb->img_watermark_Valing='CENTER';           // [OPTIONAL] set watermark vertical position, TOP | CENTER | BOTTOM
                    $thumb->img_watermark_Haling='CENTER';           // [OPTIONAL] set watermark horizonatal position, LEFT | CENTER | RIGHT
                }
                if ( $wtm == 'txt' ) {
                    if ( defined('WATERMARK_TEXT') ) $thumb->txt_watermark=ARTICLE_WATERMARK_TEXT;        // [OPTIONAL] set watermark text [RECOMENDED ONLY WITH GD 2 ]
                    else $thumb->txt_watermark='';
                    $thumb->txt_watermark_color='000000';        // [OPTIONAL] set watermark text color , RGB Hexadecimal[RECOMENDED ONLY WITH GD 2 ]
                    $thumb->txt_watermark_font=5;                // [OPTIONAL] set watermark text font: 1,2,3,4,5
                    $thumb->txt_watermark_Valing='TOP';           // [OPTIONAL] set watermark text vertical position, TOP | CENTER | BOTTOM
                    $thumb->txt_watermark_Haling='LEFT';       // [OPTIONAL] set watermark text horizonatal position, LEFT | CENTER | RIGHT
                    $thumb->txt_watermark_Hmargin=10;          // [OPTIONAL] set watermark text horizonatal margin in pixels
                    $thumb->txt_watermark_Vmargin=10;           // [OPTIONAL] set watermark text vertical margin in pixels     
                }

                $mas_img_name=explode(".",$img_with_path);
                if(!empty($size_width )) 
                    $img_name_new = $mas_img_name[0].ARTICLE_ADDITIONAL_FILES_TEXT.'width_'.$size_width.'.'.$mas_img_name[1];
                elseif(!empty($size_auto )) 
                    $img_name_new = $mas_img_name[0].ARTICLE_ADDITIONAL_FILES_TEXT.'auto_'.$size_auto.'.'.$mas_img_name[1];
                elseif(!empty($size_height )) 
                    $img_name_new = $mas_img_name[0].ARTICLE_ADDITIONAL_FILES_TEXT.'height_'.$size_height.'.'.$mas_img_name[1];
                $img_full_path_new = SITE_PATH.$img_name_new; 
                $img_src = $img_name_new;
                $uploaddir = SITE_PATH.substr($img_with_path, 0, strrpos($img_with_path,'/'));                      

                if ( !strstr($parameters, 'alt') ) $alt = $this->GetPictureAlt($img);
                if ( !strstr($parameters, 'title') ) $title = $this->GetPictureTitle($img);
                
                //echo '<br>$img_name_new='.$img_name_new;  
                //echo '<br>$img_full_path_new='.$img_full_path_new;
                //echo '<br>$img_src='.$img_src;
                //echo '<br>$uploaddir='.$uploaddir;
                
                if ( !strstr($parameters, 'alt') )  $parameters = $parameters.' alt="'.$alt.'"';
                if ( !strstr($parameters, 'title') ) $parameters = $parameters.' title=" '.$title.' "';

                //echo '<br>$uploaddir='.$uploaddir; 
                if ( !file_exists($img_full_path_new) ) {
                    if( !file_exists ($uploaddir) ) mkdir($uploaddir,0777);
                    if( file_exists($uploaddir) ) @chmod($uploaddir,0777);
                    $thumb->process();       // generate image  
                    //make new image like R1800TII_big.jpg -> R1800TII_big_autozoom_100x84.jpg 
                    $thumb->save($img_full_path_new);
                    @chmod($uploaddir,0755);
                    $params = "img=".$img."&".$size;
                }
                $str = '<img src="'.$img_src.'" '.$parameters.' />';
            }//end else  
        }//end else  
        return $str;
    } // end of function ShowImage()   
    */
    
    // ================================================================================================
    // Function : ShowImageSquare
    // Version : 1.0.0
    // Date : 17.11.2006
    //
    // Parms :  $img - path of the picture
    //          $id_art - id of the news
    //          $size -  Can be "size_auto" or  "size_width" or "size_height"
    //          $quality - quality of the image
    //          $wtm - make watermark or not. Can be "txt" or "img"
    //          $parameters - other parameters for TAG <img> like border
    // Returns : $res / Void
    // Description : Show images for news
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 17.11.2006  
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function ShowImageSquare($img = NULL, $id_art,  $plain=true, $size_width = 90, $quality = 85, $parameters = NULL)
    {
        $return_value='';   
        $alt = NULL;
        $title = NULL;
        $settings_img_path = ArticleImg_Path.'/';
        //echo "<br>img=".$img;
     
        if ( !strstr($img, '.') ) {
            $img_data = $this->GetPictureData($img);
            if(!isset($img_data['id_art'])) {return false;}
            $img_name = $img_data['path'];  // like R1800TII_big.jpg
            $img_with_path = $this->GetImgPath($img_name, $id_art);
        }
        else {
            $img_with_path = $this->GetImgPath($img, $id_art);  
            $img_name = $img;
        }   
        $img_full_path = SITE_PATH.$img_with_path; // like z:/home/speakers/www/uploads/45/R1800TII_big.jpg

        if ( !file_exists($img_full_path) ) return false;

        $ext = strtolower($this->GetExtationOfFile($img_full_path));
        //echo $img_full_path;
        switch ($ext)
        {
            case 'jpg':
            case 'jpeg':
                $src = imagecreatefromjpeg($img_full_path);
                break;
            case 'gif':
                $src = imagecreatefromgif($img_full_path);
                break;
            case 'png':
                $src = imagecreatefrompng($img_full_path);
                break;
        }
        $w_src = imagesx($src); 
        $h_src = imagesy($src);
        //echo '<br> $img_full_path='.$img_full_path.'<br> $size_auto='.$size_auto;
        if (  intval($img)>0 ) {
            $mas_img_name=explode(".",$img);
            $img_name_new = $mas_img_name[0].'_'.intval($size_width).'x'.intval($size_width).'.'.$mas_img_name[1];
            $settings_img_path=$settings_img_path.$id_art;  
            $img_full_path_new = SITE_PATH.$settings_img_path.'/'.$img_name_new;
            $img_src = $settings_img_path.'/'.$img_name_new;
            $uploaddir = SITE_PATH.$settings_img_path;
        }
        //header("Content-type: image/jpeg");
        $dest = @imagecreatetruecolor($size_width,$size_width); 

        // ???????? ?????????? ????????? ?? x, ???? ???? ?????????????? 
        if ($w_src>$h_src)
        if ($plain){ 
            imagecopyresampled($dest, $src, 0, 0,
            round((max($w_src,$h_src)-min($w_src,$h_src))/2),
            0, $size_width, $size_width, min($w_src,$h_src), min($w_src,$h_src));
        }
        else { 
            imagecopyresized($dest, $src, 0, 0,
            round((max($w_src,$h_src)-min($w_src,$h_src))/2),
            0, $size_width, $size_width, min($w_src,$h_src), min($w_src,$h_src)); 
        }
        // ???????? ?????????? ???????? ?? y, 
        // ???? ???? ???????????? (???? ????? ???? ?????????) 
        //echo $src;
        if ($w_src<$h_src)
            if ($plain) 
                imagecopyresampled($dest, $src, 0, 0, 0, 0, $size_width, $size_width,
                min($w_src,$h_src), 
                min($w_src,$h_src)); 
            else
                imagecopyresized($dest, $src, 0, 0, 0, 0, $size_width, $size_width,min($w_src,$h_src), min($w_src,$h_src)); 

        // ?????????? ???????? ?????????????? ??? ??????? 
        if ($w_src==$h_src)
            if ($plain) imagecopyresampled($dest, $src, 0, 0, 0, 0, $size_width, $size_width, $w_src, $w_src);
            else imagecopyresized($dest, $src, 0, 0, 0, 0, $size_width, $size_width, $w_src, $w_src); 
            $uploaddir = substr($img_with_path, 0, strrpos($img_with_path,'/'));                      
        if ( !strstr($parameters, 'alt') ) $alt = $this->GetPictureAlt($img);
        if ( !strstr($parameters, 'title') ) $title = $this->GetPictureTitle($img);
        //echo '<br>$img_name_new='.$img_name_new;  
        //echo '<br>$img_full_path_new='.$img_full_path_new;
        //echo '<br>$img_src='.$img_src;
        if ( !strstr($parameters, 'alt') )  $parameters = $parameters.' alt="'.$alt.'"';
        if ( !strstr($parameters, 'title') ) $parameters = $parameters.' title=" '.$title.' "';
        if ( !file_exists($img_full_path_new) ) {
            //if( !file_exists ($uploaddir) ) mkdir($uploaddir,0777);
            if( file_exists($uploaddir) ) @chmod($uploaddir,0777);
            //echo $quality;
            switch ($ext)
            {
                case 'jpg':
                case 'jpeg':
                    imagejpeg($dest,$img_full_path_new,$quality);
                    break;
                case 'gif': 
                    imagegif($dest,$img_full_path_new);
                    break;
                case 'png':
                    imagepng($dest,$img_full_path_new);
                    break;
            }
            imagedestroy($dest); 
            imagedestroy($src);
            @chmod($uploaddir,0755);
            $params = "img=$img&$size_width";
            //echo '<br> $params='.$params;
            /*?><img src="http://<?=NAME_SERVER;?>/thumb.php?<?=$params;?>" <?=$parameters;?> ><?*/
            $str = '<img src="'.$img_src.'" '.$parameters.' />';
        }
        else {
            $str = '<img src="'.$img_src.'" '.$parameters.' />'; 
        }
        return $str;  
    } // end of function ShowImageSquare()
    
    // ================================================================================================
    // Function : GetExtationOfFile
    // Version : 1.0.0
    // Date : 31.08.2009
    //
    // Parms :  $filename - name of the image
    // Returns : $res / Void
    // Description : return extenation of file
    // ================================================================================================
    // Programmer : Oleg Morgalyuk
    // Date : 31.08.2009  
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function GetExtationOfFile($filename)
    {
        return $ext = substr($filename,1 + strrpos($filename, ".")); 
    }// end of function GetExtationOfFile()         
        
    // ================================================================================================
    // Function : GetImgFullPath
    // Version : 1.0.0
    // Date : 06.11.2006
    //
    // Parms :  $img - name of the image
    //          $id_art - id of the user
    // Returns : $res / Void
    // Description : return path to the image like /images/mod_art/120/1162648375_0.jpg 
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 06.11.2006  
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function GetImgFullPath($img = NULL, $id_art = NULL )
    {
        return ArticleImg_Full_Path.$id_art.'/'.$img;
    } //end of function GetImgFullPath() 
    
    // ================================================================================================
    // Function : GetImgPath
    // Date : 01.03.2011
    // Parms :  $img - name of the image
    //          $id_art - id of the article
    // Returns : $res / Void
    // Description : return path to the image like /images/mod_article/120/1162648375_0.jpg 
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function GetImgPath($img = NULL, $id_art = NULL )
    {
        if(!isset($this->settings))
            $this->settings = $this->GetSettings();
         return $this->settings['img_path'].'/'.$id_art.'/'.$img; // like /uploads/45;
    } //end of function GetImgPath()   
    
    
    // ================================================================================================
    // Function : GetPictureData
    // Version : 1.0.0
    // Date : 03.04.2006
    //
    // Parms :  $id_img - id of the image
    // Returns : $res / Void
    // Description : return array with path to the pictures of current product
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 03.04.2006 
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function GetPictureData($id_img)
    {
       $tmp_db = DBs::getInstance();           
       
       $q="SELECT `name`,`descr` FROM `".TblModArticleImgSpr."` WHERE `cod`='".$id_img."' and `lang_id`='".$this->lang_id."'";
       $res = $tmp_db->db_Query( $q );
       //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;  
       if( !$res OR !$tmp_db->result ) return false;
       $row = $tmp_db->db_FetchAssoc();
       //echo "<br> row['id_prop']=".$row['id_prop'];
       return $row;            
        
    } // end of function GetPictureData()
    
    
    // ================================================================================================
    // Function : GetPictureAlt
    // Version : 1.0.0
    // Date : 19.05.2006
    //
    // Parms :  $id_img - id of the image
    // Returns : $res / Void
    // Description : return alt for this image
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date :  19.05.2006 
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function GetPictureAlt($img, $show_name = true)
    {
        
        if ( strstr($img, '.') ) {   
        $id_img = $this->GetImgIdByPath($img);
        } else {
        $id_img = $img;
        }
        
       // echo "<br>id_img=".$id_img;
        $alt = $this->Spr->GetNameByCod(TblModArticleImgSpr, $id_img, _LANG_ID, 1);
       // echo '<br>$alt='.$alt;
        if ( empty($alt) and $show_name ) {
          $tmp_db = DBs::getInstance();           
          $q="SELECT `id_art` FROM `".TblModArticleImg."` WHERE `id`='".$id_img."'";
          $res = $tmp_db->db_Query( $q );
         // echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;  
          if( !$res OR !$tmp_db->result ) return false;
          $row = $tmp_db->db_FetchAssoc();
          
           $alt = $this->Spr->GetNameByCod(TblModArticleImgSpr, $row['id_art'], _LANG_ID, 1);
          //$id_cat = $this->GetCategory($row['id_prop']);
          //echo '<br>$id_cat='.$id_cat;                
          //$name_ind = $this->Spr->GetNameByCod(TblModCatalogSprNameInd, $id_cat, $this->lang_id, 1 );
         // $alt = $name_ind.' '.$alt;
        }
        
      //  echo '<br> $alt='.$alt;
        return htmlspecialchars($alt);            
        
    } // end of function GetPictureAlt() 
    
    // ================================================================================================
    // Function : GetPictureTitle
    // Version : 1.0.0
    // Date : 19.05.2006
    //
    // Parms :  $id_img - id of the image
    // Returns : $res / Void
    // Description : return title for this image
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date :  19.05.2006 
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function GetPictureTitle($img)
    {
         if ( strstr($img, '.') ) {   
        $id_img = $this->GetImgIdByPath($img);
        } else {
        $id_img = $img;
        }
        $tmp_db = DBs::getInstance();           
       
       $q="SELECT `descr` FROM `".TblModArticleImgSpr."` WHERE `cod`='".$id_img."' and `lang_id`='".$this->lang_id."'";
       $res = $tmp_db->db_Query( $q );
       //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;  
       if( !$res OR !$tmp_db->result ) return false;
       $row = $tmp_db->db_FetchAssoc();
        $alt = htmlspecialchars($row['descr']);
        //echo '<br>$alt='.$alt;
        if ( empty($alt) ) {
          $alt = $this->GetPictureAlt($id_img);
        }
       // echo '<br> $title='.$alt;
        return $alt;            
        
    } // end of function GetPictureTitle()
     
    // ================================================================================================
    // Function : GetImgTitleByPath
    // Date : 06.11.2006
    // Parms :  $img - name of the picture
    // Returns : $res / Void
    // Description : return title for image 
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetImgIdByPath( $img )
    {
       $tmp_db = DBs::getInstance();
           
       $q = "SELECT * FROM `".TblModArticleImg."` WHERE 1 AND `path`='$img'";
       $res = $tmp_db->db_Query($q);
       //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
       if ( !$res or !$tmp_db->result ) return false;
       //$rows = $tmp_db->db_GetNumRows();
      // echo '<br>$rows='.$rows;
       $row = $tmp_db->db_FetchAssoc();
       $id = $row['id']; 
       return $id;            
    } //end of function GetImgTitleByPath()         
    
    
    // ================================================================================================
    // Function : upImg()
    // Date : 4.04.2007
    // Returns :      true,false / Void
    // Description :  Up position
    // Programmer :  Andriy Lykhodid
    // ================================================================================================
    function upImg($table, $level_name = NULL, $level_val = NULL)
    {
        $tmp_db = DBs::getInstance(); 
        $q="select `move`,`id` from `$table` where `move`='$this->move'";
        if ( !empty($level_name) ) $q = $q." AND `$level_name`='$level_val'"; 
        $res = $tmp_db->db_Query( $q );
//        echo '<br>q='.$q.' res='.$res; // $this->Right->result='.$this->db->rest;
        if( !$res )return false;
        $rows = $tmp_db->db_GetNumRows();
        $row = $tmp_db->db_FetchAssoc();
        $move_down = $row['move'];
        $id_down = $row['id'];

        $q="select `move`,`id` from `$table` where `move`<'$this->move'";
        if ( !empty($level_name) ) $q = $q." AND `$level_name`='$level_val'";
        $q = $q." order by `move` desc";
        $res = $tmp_db->db_Query( $q );
//        echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;
        if( !$res )return false;
        $rows = $tmp_db->db_GetNumRows();
        $row = $tmp_db->db_FetchAssoc();
        $move_up = $row['move'];
        $id_up = $row['id'];

        //echo '<br> $move_down='.$move_down.' $id_down ='.$id_down.' $move_up ='.$move_up.' $id_up ='.$id_up;
        if( $move_down!=0 AND $move_up!=0 )
        {
            $q="update `$table` set
             `move`='$move_down' where id='$id_up'";
            $res = $tmp_db->db_Query( $q );
            //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result; 

            $q="update `$table` set
             `move`='$move_up' where id='$id_down'";
            $res = $tmp_db->db_Query( $q );
            //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result; 
        }
    } // end of function up()


    // ================================================================================================
    // Function : downImg()
    // Date : 4.04.2007
    // Returns :      true,false / Void
    // Description :  Down position
    // Programmer :  Andriy Lykhodid
    // ================================================================================================
    function downImg($table, $level_name = NULL, $level_val = NULL)
    {
        $tmp_db = DBs::getInstance();    
        $q="select * from `$table` where `move`='$this->move'";
        if ( !empty($level_name) ) $q = $q." AND `$level_name`='$level_val'";
        $res = $tmp_db->db_Query( $q );
        //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;
        if( !$res )return false;
        $rows = $tmp_db->db_GetNumRows();
        $row = $tmp_db->db_FetchAssoc();
        $move_up = $row['move'];
        $id_up = $row['id'];


        $q="select * from `$table` where `move`>'$this->move'";
        if ( !empty($level_name) ) $q = $q." AND `$level_name`='$level_val'";
        $q = $q." order by `move` asc";
        $res = $tmp_db->db_Query( $q );
        //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;
        if( !$res )return false;
        $rows = $tmp_db->db_GetNumRows();
        $row = $tmp_db->db_FetchAssoc();
        $move_down = $row['move'];
        $id_down = $row['id'];

        if( $move_down!=0 AND $move_up!=0 )
        {
            $q="update `$table` set
             `move`='$move_down' where id='$id_up'";
            $res = $tmp_db->db_Query( $q );
            // echo '<br>q='.$q.' res='.$res;  
            $q="update `$table` set
             `move`='$move_up' where id='$id_down'";
            $res = $tmp_db->db_Query( $q );
            //echo '<br>q='.$q.' res='.$res;
        }
    } // end of function down()
 
 
    // ================================================================================================
    // Function : GetArticleData()
    // Date : 20.09.2009
    // Returns :      true,false / Void
    // Description :  Return news data
    // Programmer :  Ihor Trokhymchuk
    // ================================================================================================
    function GetArticleData( $art_id = NULL )
    {
        if(!$art_id) return true; 
        //$q = "select * from ".TblModNews." where id='$news_id'";
        $q = "SELECT `".TblModArticle."`.*, `".TblModArticleCat."`.name AS `cat_name`, `".TblModArticleTxt."`.name AS `sbj`, `".TblModArticleTxt."`.short AS `shrt_art`, `".TblModArticleTxt."`.full AS `full_art` 
              FROM `".TblModArticle."`, `".TblModArticleCat."`, `".TblModArticleTxt."`
              WHERE `".TblModArticle."`.id='".$art_id."'
              AND `".TblModArticle."`.category=`".TblModArticleCat."`.cod
              AND `".TblModArticleCat."`.lang_id='".$this->lang_id."'
              AND `".TblModArticle."`.id=`".TblModArticleTxt."`.cod
              AND `".TblModArticleTxt."`.lang_id='".$this->lang_id."'
             ";
        if( !empty($this->fltr)){
            $q .= $this->fltr;
        }
        $res = $this->db->db_Query( $q );
        //echo '<br>'.$q.' $res='.$res.' $this->db->result='.$this->db->result; 
        if ( !$res OR !$this->db->result ) return false;
        $rows = $this->db->db_GetNumRows();
        return $rows;
    } //end of fuinction GetNewsData() 
 
    // ================================================================================================
    // Function : ConvertDate()
    // Date : 07.02.2005
    // Returns :      true,false / Void
    // Description :  Convert Date to nidle format
    // Programmer :  Ihor Trokhymchuk
    // ================================================================================================
    function ConvertDate($date_to_convert, $time_only=false)
    {
        $settings = $this->settings; 
        
        //print_r($tmp = explode("-", $date_to_convert));
        $tmp = explode("-", $date_to_convert);
        $tmp2 = explode(" ", $tmp[2]);
        $m_word = NULL;
        $month = NULL;
        $day = NULL;
        $year = NULL;
        $time = NULL;

        if($time_only) return $tmp2[1];
        
        //$month = $this->Spr->GetShortNameByCod(TblSysSprMonth, intval($tmp[1]), $this->lang_id, 1);
        $month = $tmp[1];
        $day = intval($tmp2[0]);
        $year = $tmp[0];
        $time = $tmp2[1];
     
        if ( isset($settings['dt']) AND $settings['dt']=='0' ) { 
            return $day." ".$month.", ".$year." г.";
        }
        return $day.".".$month.".".$year;    
    } // end of function ConvertDate



    // ================================================================================================
    // Function : Link()
    // Date : 12.01.2011
    // Description : Return Link 
    // Programmer : Ihor Trokhymchuk
    // ================================================================================================
    function Link( $cat = NULL, $id = NULL)
    {
        if(!isset($this->settings))
            $this->settings = $this->GetSettings();
        if ( isset($this->settings['rewrite']) AND $this->settings['rewrite']=='0' ) { 
            if($cat!=NULL and $id==NULL) {
                return '/articlecat_'.$cat.'.html';
            }
            if($id!=NULL){
                return '/article_'.$id.'.html';
            }
        }

        if( !defined("_LINK")) {
            $Lang = new SysLang(NULL, "front");
            $tmp_lang = $Lang->GetDefFrontLangID();
            if( ($Lang->GetCountLang('front')>1 OR isset($_GET['lang_st'])) AND $this->lang_id!=$tmp_lang) {
                define("_LINK", "/".$Lang->GetLangShortName($this->lang_id)."/");
            }
            else {
                define("_LINK", "/");
            }
        }
        if( !empty($cat) ){
            $str_cat = $this->Spr->GetTranslitByCod( TblModArticleCat, $cat, $this->lang_id );
        }
        elseif(!empty($id)){
            $str_cat =  $this->Spr->GetTranslitByCod( TblModArticleCat, $this->GetIdCatByIdArt($id), $this->lang_id );
        }
        else{
            $str_cat = NULL; 
        }
        $str_art = $this->GetLink($id);
        //echo '<br>$cat='.$cat.' $id='.$id;
        
        /*
        if($id!=NULL and $str_art==''){
            $str_art=$this->SetLink($id, true);
        }
        */
        if($id==null){
            if(!empty($str_cat)) $link = _LINK.'articles/'.$str_cat.'/';
            else{
                if( $this->task=='showa') $link = _LINK.'articles/last/';
                elseif( $this->task=='showall') $link = _LINK.'articles/all/';
                elseif( $this->task=='arch') $link = _LINK.'articles/arch/';
                else $link = _LINK.'articles/';
            }
        }
        else $link = _LINK.'articles/'.$str_cat.'/'.$str_art.'.html';
        return $link;
    } // end of function Link()


    // ================================================================================================
   // Function : GetNameByCod
   // Date : 04.02.2005
   // Parms :   $Table - name of table, from which will be select data
   //           $cod - cod of the record in the table where the name is searched
   //           $lang_id - id of the language
   //           $my_ret_val - parameter for returned value .( 1- return '' for empty records) 
   // Returns : $res / Void
   // Description : Get the name from table by its cod on needed language
   // Programmer : Igor Trokhymchuk
   // ================================================================================================
   function GetRowsByName($Table,$fiels_name, $cod, $lang_id = _LANG_ID)
   {
       if ( empty($cod) ) {
           if (!empty($my_ret_val)) return '';
           else return $this->Msg->show_text('_VALUE_NOT_SET');
       }
       $tmp_db = DBs::getInstance();
       $array=false;
       if (is_array($fiels_name)) {$array=true; $fiels_name= implode(',',$fiels_name);}
       $q="SELECT $fiels_name FROM `".$Table."` WHERE `cod`='".addslashes($cod)."' AND `lang_id`='".$lang_id."'";
       $res = $tmp_db->db_Query($q);
       //echo '<br> $q='.$q.'  $tmp_db->result='.$tmp_db->result;
       if ( !$res OR !$tmp_db->result ) return false;
       $row_res = $tmp_db->db_FetchAssoc();
       //echo '<br> $row_res[name]='.$row_res['name'];
       if ($array)       return $row_res;
       else       return $row_res[$fiels_name];
   }  //end of fuinction GetNameByCod
    
    
    // ================================================================================================
    // Function : SetLink()
    // Date : 12.01.2009
    // Parms : $link - str for link, $cod - id position
    // Description : Set Link 
    // Programmer : Ihor Trokhymchuk
    // ================================================================================================
    function SetLink($cod, $ret=false)
    {
        $Crypt = new Crypt();
        $db = DBs::getInstance();

        $cat_link = stripslashes($this->GetRowsByName(TblModArticleTxt,'name', $cod));   
        if ($cat_link=="") { 
            $cat_link = stripslashes($this->GetRowsByName(TblModArticleTxt,'name', $cod)); 
            if ($cat_link=="") {
                $ln_sys = new SysLang();
                $ln_arr = $ln_sys->LangArray( _LANG_ID );
                while( $el = each( $ln_arr ) ){
                    $lang_id = $el['key'];
                    $cat_link = stripslashes($this->GetRowsByName(TblModArticleTxt,'name', $cod, $lang_id)); 
                    //echo '<br>$cat_link='.$cat_link.' $lang_id='.$lang_id;
                    if( !empty($cat_link) ) break; 
                }
            }
        }

        $link = $Crypt->GetTranslitStr(trim($cat_link));

        $q = "INSERT INTO `".TblModArticleLinks."` values(NULL,'".$cod."','".$link."')";
        $res = $db->db_Query( $q );
        if( !$res ) return false;
        if($ret) return $link;
    } // end of function SetLink

    // ================================================================================================
    // Function : GetLink()
    // Date : 12.01.2009
    // Description : Get link
    // Programmer : Ihor Trokhymchuk
    // ================================================================================================
    function GetLink($cod)
    {
        $tmp_db = DBs::getInstance();
        $q = "select `link` from ".TblModArticleLinks." where `cod`='".$cod."'";
        $res = $tmp_db->db_Query( $q );
        $rows = $tmp_db->db_GetNumRows();
        //echo "<br> q=".$q." res=".$res." rows=".$rows;
        $row = $tmp_db->db_FetchAssoc();
        return $row['link']; 
    } // end of function GetLink

    // ================================================================================================
    // Function : GetIdCatByIdArt()
    // Date : 13.05.2007
    // Parms :
    // Returns :
    // Description : 
    // Programmer : Ihor Trokhymchuk
    // ================================================================================================
    function GetIdCatByIdArt($id){
      $q = "select * from ".TblModArticle." where 1 and `id`='".$id."'";
      $res = $this->db->db_Query( $q );
      $rows = $this->db->db_GetNumRows();
    //echo "<br> q=".$q." res=".$res." rows=".$rows;
      $row = $this->db->db_FetchAssoc();    
    return $row['category']; 
    } // end of function GetIdCatByIdArt

    // ================================================================================================
    // Function : GetIdArtByStrArt()
    // Date : 13.05.2007
    // Programmer : Ihor Trokhymchuk
    // ================================================================================================
    function GetIdArtByStrArt($str_art)
    {
        $q = "SELECT `".TblModArticleLinks."`.`cod` 
              FROM `".TblModArticleLinks."`, `".TblModArticle."`, `".TblModArticleCat."`  
              WHERE BINARY `".TblModArticleLinks."`.`link` = BINARY '".$str_art."'
              AND `".TblModArticleLinks."`.cod=`".TblModArticle."`.`id`
              AND `".TblModArticle."`.`category`=`".TblModArticleCat."`.`cod`
              AND `".TblModArticleCat."`.`cod`='".$this->category."'
              AND `".TblModArticleCat."`.`lang_id`='".$this->lang_id."'
             ";
        $res = $this->db->db_Query( $q );
        $rows = $this->db->db_GetNumRows();
        //echo "<br>GetIdArtByStrArt  q=".$q." res=".$res." rows=".$rows;
        $row = $this->db->db_FetchAssoc();
        //echo "<br>ART q=".$q." res=".$res.' rows='.$rows.' cod='.$row['cod']; 
        return $row['cod']; 
    } // end of function GetIdArtByStrArt

    
    //------------------------------------------------------------------------------------------------------------
    //---------------------------- FUNCTION FOR SETTINGS OF ARTICLES START ---------------------------------------       
    //------------------------------------------------------------------------------------------------------------
        
       // ================================================================================================
       // Function : GetSettings()
       // Date : 27.03.2006
       // Returns : true,false / Void
       // Description : return all settings of Gatalogue
       // Programmer : Igor Trokhymchuk
       // ================================================================================================
       function GetSettings($front = true)
       {       
        $db = DBs::getInstance();
        $q="select * from `".TblModArticleSet."` where 1";
        $res = $db->db_Query( $q );
        //echo "<br /> q = ".$q." res = ".$res;
        if( !$db->result ) return false;
        $row = $db->db_FetchAssoc();
        if($front){
            $q1="select * from `".TblModArticleSetSprMeta."` where `lang_id`='$this->lang_id' ";
            $res1 = $db->db_Query( $q1 );
            //echo "<br /> q = ".$q." res = ".$res;
            if( !$db->result ) return false;
            $row1 = $db->db_FetchAssoc();
            $row['title']=$row1['title'];
            $row['keywords']=$row1['keywords'];
            $row['description']=$row1['description'];
        }
        return $row;         
       } // end of function GetSettings() 

     /**
     * Articles::SetMetaData()
     * @author Yaroslav
     * @param mixed $page
     * @return
     */
     function SetMetaData($page)
       {
        $db1 = DBs::getInstance();
        $q1="select * from `".TblModArticleSetSprMeta."` where `lang_id`='$this->lang_id' ";
        $res1 = $db1->db_Query( $q1 );
        if( !$db1->result ) return false;
        //echo "<br /> ".$q1."<br/> res1 = ".$res1;
        $row1 = $db1->db_FetchAssoc();
        $title = $row1['title'];
        $keywords = $row1['keywords'];
        $description = $row1['description'];
       
       // Установка через динамічні сторінки
        if(empty($this->id)) {
            if(!isset ($this->FrontendPages)) 
                $this->FrontendPages = Singleton::getInstance('FrontendPages');
            $this->FrontendPages->page_txt = $this->FrontendPages->GetPageTxt($page);
            
            $this->title = $this->FrontendPages->GetTitle();
            if(empty($this->title) )
                $this->title = $title;
            
            $this->description = $this->FrontendPages->GetDescription();
            if(empty($this->description))
                $this->description = $description;
            
            $this->keywords = $this->FrontendPages->GetKeywords();
            if(empty($this->keywords))
                $this->keywords = $keywords;
            return;
        }
        
        $this->title = $title;
        $this->keywords = $keywords;
        $this->description = $description;
        
        $q = "SELECT `name`, `title`, `keywords`, `description` 
              FROM `".TblModArticleTxt."`
              WHERE `".TblModArticleTxt."`.cod='".$this->id."'
              AND `".TblModArticleTxt."`.lang_id='".$this->lang_id."'
             ";
        $res = $this->db->db_Query( $q );
//        echo '<br>'.$q.' $res='.$res.' $this->db->result='.$this->db->result; 
        if ( !$res OR !$this->db->result ) return false;
        $rows = $this->db->db_GetNumRows();
        $row=$this->db->db_FetchAssoc();
        if( $this->id ) {$title =  $row['title'];
        if( empty($title) ) $title =  $row['name'];}
        else {
        if( $this->category ) $title = $this->Spr->GetNameByCod( TblModArticleCat, $this->category, _LANG_ID, 1);
        }
        if( !empty($title) ) $this->title = $title.' | '.$this->title;
        else {
        switch($this->task){
        case 'showall':  $title = $this->multi['TXT_ARTICLE_TITLE'];
                    break;
        case 'last':  $title = $this->Msg->show_text('TXT_META_TITLE_LAST');
                    break;
        case 'arch':  $title = $this->Msg->show_text('TXT_META_TITLE_ARCH');
                    break;
        }
         if( !empty($title) ) $this->title = $title.' | '.$this->title;   
        }
         $curr = round($this->start/$this->display, 0);
         $end = round($this->rows/$this->display, 0);
         $page = $end-$curr;
         if($page>1) $this->title = $this->title." | Статьи ".($this->start+1)."...".($this->start+$this->display);
           
       if( $this->id ) $descr = $row['description'];
        else {
        if( $this->category ) $descr = $this->Spr->GetNameByCod( TblModArticleCat, $this->category, _LANG_ID, 1);
        }
        
        if( !empty($descr) ) $this->description = $descr.'. '.$this->description;
        else {
            if( !empty($title) ) $this->description = $title.'. '.$this->description;
        }
   
       if( $this->id ) $keywrds = $row['keywords'];
        else {
       if( $this->category ) $keywrds = $this->Spr->GetNameByCod( TblModArticleCat, $this->category, _LANG_ID, 1);
       }
       
       if( !empty($keywrds) ) $this->keywords = $keywrds.', '.$this->keywords;
       } //end of function  SetMetaData()  

       
     // ================================================================================================
     // Function : GetNameArticle()
     // Date : 27.03.2008
     // Returns : true,false / Void
     // ================================================================================================    
     function GetNameArticle( $id )
        {
         $name = stripslashes( $this->GetRowsByName( TblModArticleTxt,'name', $id ) );
         return $name;
        }
     
     // ================================================================================================
     // Function : QuickSearch()
     // Date : 27.03.2008
     // Returns : true,false / Void
     // Description :
     // Programmer : Alex Kerest
     // ================================================================================================    
     function QuickSearch($search_keywords){
       $tmp_db = DBs::getInstance();
       
       $search_keywords = stripslashes($search_keywords);
       
       $sel_table = NULL;
       $str_like = NULL;
       $filter_cr = ' OR ';

        $str_like = $this->build_str_like(TblModArticleTxt.'.name', $search_keywords);
        $str_like .= $filter_cr.$this->build_str_like(TblModArticleTxt.'.short', $search_keywords);
        $str_like .= $filter_cr.$this->build_str_like(TblModArticleTxt.'.full', $search_keywords); 
        $sel_table = "`".TblModArticle."`, `".TblModArticleTxt."` ";
       
       $q ="SELECT `".TblModArticle."`.id, `".TblModArticle."`.category, `".TblModArticle."`.status,`".TblModArticleTxt."`.name, `".TblModArticle."`.position
            FROM ".$sel_table."
            WHERE (".$str_like.")
            AND `".TblModArticleTxt."`.lang_id = '".$this->lang_id."'
            AND `".TblModArticle."`.id = `".TblModArticleTxt."`.cod
            ORDER BY `".TblModArticle."`.id, `".TblModArticle."`.position";

       
       
       $res = $this->db->db_Query( $q );
    //   echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$this->db->result;
       if ( !$res) return false;
       if( !$this->db->result ) return false;  
       $rows = $this->db->db_GetNumRows();
     // echo "<br> rows = ";
     //  print_r($rows);
       return $rows;
     } // end of function QuickSearch
 
 
        // ================================================================================================
       // Function : build_str_like
       // Version : 1.0.0
       // Date : 19.01.2005
       //
       // Parms : $find_field_name - name of the field by which we want to do search
       //         $field_value - value of the field
       // Returns : str_like_filter - builded string with special format;
       // Description : create the string for SQL-command SELECT for search in the text field by any word
       // ================================================================================================
       // Programmer : Igor Trokhymchuk
       // Date : 19.01.2005
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function build_str_like($find_field_name, $field_value)
       {
        $str_like_filter=NULL;
        // cut unnormal symbols
        $field_value=preg_replace("/[^\w\x7F-\xFF\s]/", " ", $field_value);
        // delete double spacebars
        $field_value=str_replace(" +", " ", $field_value);
        $wordmas=explode(" ", $field_value);

        for ($i=0; $i<count($wordmas); $i++){
              $wordmas[$i] = trim($wordmas[$i]);
              if (EMPTY($wordmas[$i])) continue;
              if (!EMPTY($str_like_filter)) $str_like_filter=$str_like_filter." AND ".$find_field_name." LIKE '%".$wordmas[$i]."%'";
              else $str_like_filter=$find_field_name." LIKE '%".$wordmas[$i]."%'";
        }
        if ($i>1) $str_like_filter="(".$str_like_filter.")";
        //echo '<br>$str_like_filter='.$str_like_filter;
        return $str_like_filter;
       } //end offunction build_str_like()
} //--- end of class