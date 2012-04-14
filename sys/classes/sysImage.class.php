<?php
// ================================================================================================
// System : SEOCMS
// Module : sysImage.class.php
// Version : 1.0.0
// Date : 19.03.2010
// Licensed To:
//
// Purpose : Class definition for working with images
//
// ================================================================================================
include_once( SITE_PATH.'/admin/include/defines.inc.php' );  
include_once( SITE_PATH.'/sys/classes/sysDatabase.single.class.php' );
 
// ================================================================================================
//    Class             : Image
//    Version           : 1.0.0
//    Date              : 19.03.2010
//    Constructor       : Yes
//    Parms             : table  - table to store information
//                        max_image_width
//                        max_image_height 
//                        max_quality
//                        max_image_quantity
//                        path_to_upload - path to upload
//                        zoom - text wich adds to thumbs
//    Returns           : None
//    Description       : Class definition for working with images
// ================================================================================================
//    Programmer        :  Oleg Morgalyuk
//    Date              :  19.03.2010
//    Reason for change :  Creation
//    Change Request Nbr:  N/A
// ================================================================================================
 class Image {
     
     var $table = NULL;
     var $db = NULL;
     var $Msg = NULL;
     var $Err = NULL ;
     var $path_to_upload = '/images';
     var $max_image_width = 2500;
     var $max_image_height = 2500;
     var $max_image_size = 1024000;
     var $max_quality = 1024000;
     var $max_image_quantity = 20;
     var $zoom = '_zoom_';

  function Image($table,$max_image_width=NULL,$max_image_height=NULL,$max_image_size=NULL,$max_image_quantity=NULL,$path_to_upload=NULL,$max_quality=NULL,$zoom=NULL) {
      $this->table = $table;
      if(isset($max_image_width)) $this->max_image_width = $max_image_width;
      if(isset($max_image_height)) $this->max_image_height = $max_image_height;
      if(isset($max_image_size)) $this->max_image_size = $max_image_size;
      if(isset($max_image_quantity)) $this->max_image_quantity = $max_image_quantity;
      if(isset($path_to_upload)) $this->path_to_upload = $path_to_upload;
      if(isset($max_quality)) $this->max_quality = $max_quality;
      if(isset($zoom)) $this->zoom = $zoom;
      $this->db = DBs::getInstance();
      $this->Msg = &check_init_txt('TblSysMsg',TblSysMsg);
  } // End of Image Constructor
   
  // ================================================================================================
    // Function : SavePicture
    // Version : 1.0.0
    // Date : 21.03.2010
    //
    // Parms :
    // Returns : $res / Void
    // Description : Save the image to the folder $this->path_to_upload and save path in the database ($this->table) and get images from $_FILES with index $_files
    // ================================================================================================
    // Programmer : Oleg Moragalyuk
    // Date : 21.03.2010
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function SavePicture($alias,$random_name = false, $_files = 'image')
    {
     $this->Err = NULL;
     $valid_types =  array("gif", "GIF", "jpg", "JPG", "png", "PNG", "jpeg", "JPEG");
     $cols = count($_FILES[$_files]["name"]);
     for ($i=1; $i<=$cols; $i++) {
         if ( !empty($_FILES[$_files]["name"][$i]) ) {
           if ( isset($_FILES[$_files]) && is_uploaded_file($_FILES[$_files]["tmp_name"][$i]) && $_FILES[$_files]["size"][$i] ){
            $filename = $_FILES[$_files]['tmp_name'][$i];
            $ext = substr($_FILES[$_files]['name'][$i],1 + strrpos($_FILES[$_files]['name'][$i], "."));
            if (!in_array($ext, $valid_types)) {
                $this->Err = $this->Err.$this->Msg['MSG_ERR_FILE_TYPE'].' ('.$_FILES[$_files]['name']["$i"].')<br>';  
            }
            else {
              $size = GetImageSize($filename);
                 $uploaddir = SITE_PATH.$this->path_to_upload.'/'.$alias;
                 if ( !file_exists (SITE_PATH.$this->path_to_upload) ) mkdir(SITE_PATH.$this->path_to_upload,0777);
                 echo $uploaddir;
                 $uploaddir_0 =$uploaddir; 
                 if ( !file_exists ($uploaddir) ) mkdir($uploaddir,0777); 
                 else @chmod($uploaddir,0777);
                 if($random_name)
                     $uploaddir2 = time().$i.'.'.$ext;
                 else    
                     $uploaddir2 = $_FILES[$_files]['name'][$i];
                 $uploaddir = $uploaddir."/".$uploaddir2; 
                 if ( @copy($filename,$uploaddir) ) {
                    //====== set next max value for move START ============
                    $maxx = NULL; 
                    $q = "SELECT MAX(move) FROM `".$this->table."`";
                    $res = $this->db->db_Query( $q );
                    $row = $this->db->db_FetchAssoc();
                    $maxx = $row['MAX(move)']+1;     
                    //====== set next max value for move END ==============
                     $q="INSERT into `".$this->table."` SET `item_id`='".$alias."', `path`='".$uploaddir2."',`show`='1', `move`='$maxx'";
                     $res = $this->db->db_Query( $q );
                     if( !$this->db->result ) $this->Err = $this->Err.$this->Msg['MSG_ERR_SAVE_FILE_TO_DB'].' ('.$_FILES[$_files]['name']["$i"].')<br>';
//                     echo '<br>q='.$q.' res='.$res.' $this->db->result='.$this->db->result;
                     if (($size) AND (($size[0] > $this->max_image_width) OR ($size[1] > $this->max_image_height)) ){
                         //============= resize original image to size from settings =============
                         $thumb = new Thumbnail($uploaddir);
                         if($this->max_image_width==$this->max_image_height) $thumb->size_auto($this->max_image_width);
                         else{ 
                            $thumb->size_width($this->max_image_width);
                            $thumb->size_height($this->max_image_height);
                         }
                         $thumb->quality = $this->max_quality;
                         $thumb->process();       // generate image
                         $thumb->save($uploaddir); //make new image
                         //=======================================================================
                     }
                 }
                 else {
                     $this->Err = $this->Err.$this->Msg['MSG_ERR_FILE_MOVE'].' ('.$_FILES[$_files]['name']["$i"].')<br>';
                 }
                 @chmod($uploaddir_0,0755);
              }
           }
           else $this->Err = $this->Err.$this->Msg['MSG_ERR_FILE'].' ('.$_FILES[$_files]['name']["$i"].')<br>';
         } 
     } // end for
     return $this->Err;
    }  // end of function SavePicture() 
    // ================================================================================================
    // Function : DelPicture
    // Version : 1.0.0
    // Date : 22.03.2010
    //
    // Parms :  $id_img_del - array of id of picture to delete 
    // Parms :  $item_del - to delete item's images 
    // Returns : $res / Void
    // Description : Remove images from table and server
    // ================================================================================================
    // Programmer : Oleg Morgalyuk
    // Date : 22.03.2010 
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function DelPicture($id_img_del = NULL, $item_del = NULL)
    {
     $del=0;
     $tmp_db = &DBs::getInstance();
     $count = count($id_img_del);
     if ($count>0)
     {
     for($i=0; $i<$count; $i++){
       $u=$id_img_del[$i];
       
       $q="SELECT `path`,`item_id` FROM `".$this->table."` WHERE `id`='".$u."'";
       $res = $tmp_db->db_Query( $q );
//       echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$tmp_db->result;  
       if( !$res) return false; 
       if( !$tmp_db->result ) return false;
       $row = $tmp_db->db_FetchAssoc();
       $path = SITE_PATH.$this->path_to_upload.'/'.$row['item_id'].'/'.$row['path'];
       if (file_exists($path)) {
          $res = unlink ($path);
          if( !$res ) return false;
       }
       $q="DELETE FROM `".$this->table."` WHERE `id`='".$u."'";
       $res = $tmp_db->db_Query( $q );
//       echo '<br>2q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;  
       if( !$res) return false; 
       if( !$tmp_db->result ) return false;
       $del=$del+1;
       $path = SITE_PATH.$this->path_to_upload.'/'.$row['item_id'];
       $handle = @opendir($path);
       //echo '<br> $handle='.$handle; 
       $cols_files = 0;
       while ( ($file = readdir($handle)) !==false ) {
           //echo '<br> $file='.$file;
           $mas_file=explode(".",$file);
           $mas_img_name=explode(".",$row['path']);
           if ( strstr($mas_file[0], $mas_img_name[0].$this->zoom) and $mas_file[1]==$mas_img_name[1] ) {
              $res = unlink ($path.'/'.$file);
              if( !$res ) return false;                    
           }
           if ($file == "." || $file == ".." ) {
               $cols_files++;
           }
       }
       //if ($cols_files==2) rmdir($path);
       closedir($handle);
     }
     }
     else
     {
         if(isset($item_del))
         {
          $q="SELECT `id`,`path`,`item_id` FROM `".$this->table."` WHERE `item_id`='".$item_del."'";
          $res = $tmp_db->db_Query( $q );
//          echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$tmp_db->result;  
            if( !$res) return false; 
            if( !$tmp_db->result ) return false;
            $rows = $tmp_db->db_GetNumRows();
            $id_list = array();
            $str_id_list ='';
            for($i=0;$i<$rows;$i++){
                $row = $tmp_db->db_FetchAssoc();
                $id_list[$i] = $row;
                if($i!=0) $str_id_list.= ', '.$row['id'];
                else $str_id_list.= $row['id'];
            }
            $q="DELETE FROM `".$this->table."` WHERE `id` in (".$str_id_list.")";
            $res = $tmp_db->db_Query( $q );
//            echo '<br>2q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;  
            if( !$res) return false; 
            if( !$tmp_db->result ) return false;
//            $del=$tmp_db->db_GetNumRows();
            for($i=0;$i<$rows;$i++){
                    $path = SITE_PATH.$this->path_to_upload.'/'.$id_list[$i]['item_id'].'/'.$id_list[$i]['path'];
                   // delete file which store in the database
                   if (file_exists($path)) {
                      $res = unlink ($path);
                      if( !$res ) return false;
                   }
                   $path = SITE_PATH.$this->path_to_upload.'/'.$id_list[$i]['item_id'];
                   //echo '<br> $path='.$path;
                   $handle = @opendir($path);
                   //echo '<br> $handle='.$handle; 
                   $cols_files = 0;
                   while ( ($file = readdir($handle)) !==false ) {
                       //echo '<br> $file='.$file;
                       $mas_file=explode(".",$file);
                       $mas_img_name=explode(".",$id_list[$i]['path']);
                       if ( strstr($mas_file[0], $mas_img_name[0].$this->zoom) and $mas_file[1]==$mas_img_name[1] ) {
                          $res = unlink ($path.'/'.$file);
                          if( !$res ) return false;                    
                       }
                       if ($file == "." || $file == ".." ) {
                           $cols_files++;
                       }
                   }
                   //if ($cols_files==2) rmdir($path);
                   closedir($handle);
            }
         } 
     }          
     return $del;
    } // end of function DelPicture()     
    
        // ================================================================================================
    // Function : DelThumbs
    // Version : 1.0.0
    // Date : 22.03.2010
    //
    // Parms :  $array_item - array of item id
    // Returns : $res / Void
    // Description : Remove small copies of images from array items $array_item
    // ================================================================================================
    // Programmer : Oleg Morgalyuk
    // Date : 22.03.2010  
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function DelThumbs($array_item)
    {
     $del=0;
     $rows = count($array_item);
     //echo '<br>$rows='.$rows;
     for ($i=0;$i<$rows;$i++) {
       $path = SITE_PATH.$this->path_to_upload.'/'.$array_item[$i];
       //echo '<br> $path='.$path;
       $handle = @opendir($path);
       //echo '<br> $handle='.$handle;
       if($handle){
           while ( ($file = readdir($handle)) !==false ) {
               //echo '<br> $file='.$file;
               $mas_file=explode(".",$file);
               //echo '<br>$mas_file[0]='.$mas_file[0].' $mas_file[1]='.$mas_file[1].' ADDITIONAL_FILES_TEXT='.ADDITIONAL_FILES_TEXT;
               if ( strstr($mas_file[0], $this->zoom) ) {
                  $res = unlink ($path.'/'.$file);
                  //echo '<br>$res='.$res;
                  if( !$res ) return false;
                  $del++; 
                  //echo '<br>$del='.$del;                   
               }
           }//end while
       }//end if
     }//end for  
     return $del;
    } // end of function DelThumbs()   
    
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
    function GetPictureData($item_id = NULL, $limit =NULL,$show = 1)
    {
       $q="SELECT `path`,`item_id` FROM `".$this->table."` WHERE 1 AND `show`='".$show."'";
       if(isset($item_id)) $q.= " AND `item_id` = '".$item_id."'";
       $q.=" ORDER by move asc";
       if(isset($limit)) $q.= " LIMIT $limit";
       $res = $this->db->db_Query( $q );
       //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;  
       if( !$res OR !$this->db->result ) return false;
       $rows = $this->db->db_GetNumRows();
       $result = array();
       for($i=0;$i<$rows;$i++){
           $row = $this->db->db_FetchAssoc();
           $result[$i] = $row;
       }
       return $result;            
    } // end of function GetPictureData() 
     
    // ================================================================================================
    // Function : ShowCurrentImage
    // Version : 1.0.0
    // Date : 13.06.2006
    //
    // Parms :  $img - id of the picture, or path of the picture
    //          $parameters - other parameters for TAG <img> like border
    // Returns : $res / Void
    // Description : Show images from catalogue
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 13.06.2006 
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function GetImagesItem($item_id = NULL, $show = 1, $count=NULL, $size = NULL, $quality = NULL, $wtm = NULL)
    {
        $size_auto = NULL;
        $size_width = NULL;
        $size_height = NULL;
        $result = array();
        $img_data = $this->GetPictureData($item_id, $count, $show);
        $pic_count = count($img_data);
        for($i=0;$i<$pic_count;$i++){
               
        $img_with_path = $this->path_to_upload.'/'.$img_data[$i]['item_id'].'/'.$img_data[$i]['path']; // like /uploads/45/R1800TII_big.jpg
        $mas_img_name=explode(".",$img_with_path);

        if ( strstr($size,'size_width') ){ 
            $size_width = substr( $size, strrpos($size,'=')+1, strlen($size) );
            $img_name_new = $mas_img_name[0].$this->zoom.'width_'.$size_width.'.'.$mas_img_name[1];
        }
        elseif ( strstr($size,'size_auto') ) {
            $size_auto = substr( $size, strrpos($size,'=')+1, strlen($size) );
            $img_name_new = $mas_img_name[0].$this->zoom.'auto_'.$size_auto.'.'.$mas_img_name[1];
        }
        elseif ( strstr($size,'size_height') ) {
            $size_height = substr( $size, strrpos($size,'=')+1, strlen($size) );
            $img_name_new = $mas_img_name[0].$this->zoom.'height_'.$size_height.'.'.$mas_img_name[1];
        }
        elseif(empty($size)) $img_name_new = $mas_img_name[0].'.'.$mas_img_name[1];
         $img_full_path_new = SITE_PATH.$img_name_new; 
        if( file_exists($img_full_path_new)){
            $result[] =  $img_name_new;
         }
        else {         
            $img_full_path = SITE_PATH.$img_with_path; // like z:/home/speakers/www/uploads/45/R1800TII_big.jpg
            if ( !file_exists($img_full_path) ) return false;

            $thumb = new Thumbnail($img_full_path);
            $src_x = $thumb->img['x_thumb'];
            $src_y = $thumb->img['y_thumb'];
            if ( !empty($size_width ) and empty($size_height) ) $thumb->size_width($size_width);
            if ( !empty($size_height) and empty($size_width) ) $thumb->size_height($size_height);
            if ( !empty($size_width) and !empty($size_height) ) $thumb->size($size_width,$size_height); 
            if ( !$size_width and !$size_height and $size_auto ) $thumb->size_auto($size_auto);                    // [OPTIONAL] set the biggest width and height for thumbnail
            //if original image smaller than thumbnail then use original image and don't create thumbnail
            if($thumb->img['x_thumb']>=$src_x OR $thumb->img['y_thumb']>=$src_y){
                $img_full_path = $this->path_to_upload.'/'.$img_data[$i]['path'];
                $result[] =  $img_full_path;
            }
            else{
                $thumb->quality=$quality;                  //default 75 , only for JPG format  
                if ( $wtm == 'img' ) {
                    $thumb->img_watermark = NULL; //SITE_PATH.'/images/design/m01.png';        // [OPTIONAL] set watermark source file, only PNG format [RECOMENDED ONLY WITH GD 2 ]
                    $thumb->img_watermark_Valing='CENTER';           // [OPTIONAL] set watermark vertical position, TOP | CENTER | BOTTOM
                    $thumb->img_watermark_Haling='CENTER';           // [OPTIONAL] set watermark horizonatal position, LEFT | CENTER | RIGHT
                }
                if ( $wtm == 'txt' ) {
                    if ( defined('WATERMARK_TEXT') ) $thumb->txt_watermark=WATERMARK_TEXT;        // [OPTIONAL] set watermark text [RECOMENDED ONLY WITH GD 2 ]
                    else $thumb->txt_watermark='';
                    $thumb->txt_watermark_color='000000';        // [OPTIONAL] set watermark text color , RGB Hexadecimal[RECOMENDED ONLY WITH GD 2 ]
                    $thumb->txt_watermark_font=5;                // [OPTIONAL] set watermark text font: 1,2,3,4,5
                    $thumb->txt_watermark_Valing='TOP';           // [OPTIONAL] set watermark text vertical position, TOP | CENTER | BOTTOM
                    $thumb->txt_watermark_Haling='LEFT';       // [OPTIONAL] set watermark text horizonatal position, LEFT | CENTER | RIGHT
                    $thumb->txt_watermark_Hmargin=10;          // [OPTIONAL] set watermark text horizonatal margin in pixels
                    $thumb->txt_watermark_Vmargin=10;           // [OPTIONAL] set watermark text vertical margin in pixels     
                }
                    if(!empty($size_width )) 
                        $img_name_new = $mas_img_name[0].$this->zoom.'width_'.$size_width.'.'.$mas_img_name[1];
                    elseif(!empty($size_auto )) 
                        $img_name_new = $mas_img_name[0].$this->zoom.'auto_'.$size_auto.'.'.$mas_img_name[1];
                    elseif(!empty($size_height )) 
                        $img_name_new = $mas_img_name[0].$this->zoom.'height_'.$size_height.'.'.$mas_img_name[1];
                    $img_full_path_new = SITE_PATH.$img_name_new; 
                    $img_src = $img_name_new;
                    $result[] = $img_src;
                    $uploaddir = SITE_PATH.substr($img_with_path, 0, strrpos($img_with_path,'/'));                      
                if ( !file_exists($img_full_path_new) ) {
                    if( file_exists ($uploaddir) ) 
                        @chmod($uploaddir,0777);
                    else
                        mkdir($uploaddir,0777);               
                    $thumb->process();       // generate image  
                    $thumb->save($img_full_path_new);
                    @chmod($uploaddir,0755);
                }
            }//end else  
        }//end else
        }  
     return $result;    
    } // end of function ShowCurrentImage()
    
    // ================================================================================================
    // Function : ShowCurrentImageExSize
    // Version : 1.0.0
    // Date : 07.09.2009
    //
    // Parms :  $img - id of the picture, or path of the picture
    //          $parameters - other parameters for TAG <img> like border
    // Returns : $res / Void
    // Description : Show images from catalogue
    // ================================================================================================
    // Programmer : Oleg Morgalyuk
    // Date : 07.09.2009
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function ShowCurrentImageExSize($item_id = NULL, $show = 1, $count=NULL, $width = NULL,$height=NULL,$hor_align=true,$ver_align=true,$quality = 85, $wtm = NULL,$border=false, $parameters = NULL)
    {
        $size_auto = NULL;
        $size_width = NULL;
        $size_height = NULL;
        $result = array();
        $img_data = $this->GetPictureData($item_id, $count, $show);
        $pic_count = count($img_data);
        for($i=0;$i<$pic_count;$i++){
               
        $img_with_path = $this->path_to_upload.'/'.$img_data[$i]['item_id'].'/'.$img_data[$i]['path']; // like /uploads/45/R1800TII_big.jpg
        $mas_img_name=explode(".",$img_with_path);

        if ( isset($width) && isset($height)){ 
            $img_name_new = $mas_img_name[0].$this->zoom.$width.'x'.$height.'.'.$mas_img_name[1];
            if ($border)  $img_name_new = $mas_img_name[0].$this->zoom.'_r_'.$width.'x'.$height.'.png';
        }
        elseif(empty($size)) $img_name_new = $mas_img_name[0].'.'.$mas_img_name[1];
        $img_full_path_new = SITE_PATH.$img_name_new; 
        if( file_exists($img_full_path_new)){
            $result[] =  $img_name_new;
        }
        else {         
            $img_full_path = SITE_PATH.$img_with_path; // like z:/home/speakers/www/uploads/45/R1800TII_big.jpg
            if ( !file_exists($img_full_path) ) return false;

            $thumb = new Thumbnail($img_full_path);
            $src_x = $thumb->img['x_thumb'];
            $src_y = $thumb->img['y_thumb'];
            if ( !empty($width) and !empty($height) ) $thumb->sizeEx($width,$height); 
            //if original image smaller than thumbnail then use original image and don't create thumbnail
            if($thumb->img['x_thumb']>=$src_x OR $thumb->img['y_thumb']>=$src_y){
                $img_full_path = $this->path_to_upload.'/'.$img_data[$i]['path'];
                $result[] =  $img_full_path;
            }
            else{
                $thumb->quality=$quality;                  //default 75 , only for JPG format  
                if ( $wtm == 'img' ) {
                    $thumb->img_watermark = NULL; //SITE_PATH.'/images/design/m01.png';        // [OPTIONAL] set watermark source file, only PNG format [RECOMENDED ONLY WITH GD 2 ]
                    $thumb->img_watermark_Valing='CENTER';           // [OPTIONAL] set watermark vertical position, TOP | CENTER | BOTTOM
                    $thumb->img_watermark_Haling='CENTER';           // [OPTIONAL] set watermark horizonatal position, LEFT | CENTER | RIGHT
                }
                if ( $wtm == 'txt' ) {
                    if ( defined('WATERMARK_TEXT') ) $thumb->txt_watermark=WATERMARK_TEXT;        // [OPTIONAL] set watermark text [RECOMENDED ONLY WITH GD 2 ]
                    else $thumb->txt_watermark='';
                    $thumb->txt_watermark_color='000000';        // [OPTIONAL] set watermark text color , RGB Hexadecimal[RECOMENDED ONLY WITH GD 2 ]
                    $thumb->txt_watermark_font=5;                // [OPTIONAL] set watermark text font: 1,2,3,4,5
                    $thumb->txt_watermark_Valing='TOP';           // [OPTIONAL] set watermark text vertical position, TOP | CENTER | BOTTOM
                    $thumb->txt_watermark_Haling='LEFT';       // [OPTIONAL] set watermark text horizonatal position, LEFT | CENTER | RIGHT
                    $thumb->txt_watermark_Hmargin=10;          // [OPTIONAL] set watermark text horizonatal margin in pixels
                    $thumb->txt_watermark_Vmargin=10;           // [OPTIONAL] set watermark text vertical margin in pixels     
                }
                if ( isset($width) && isset($height)){ 
                    $img_name_new = $mas_img_name[0].$this->zoom.$width.'x'.$height.'.'.$mas_img_name[1];
                }
                $img_full_path_new = SITE_PATH.$img_name_new;
                $img_src =$img_name_new;
                $uploaddir = SITE_PATH.$this->path_to_upload;
                if ( !file_exists($img_full_path_new) ) {
                    if( !file_exists ($uploaddir) ) mkdir($uploaddir,0777);
                    if( file_exists($uploaddir) ) @chmod($uploaddir,0777);
                    $thumb->processEx($ver_align,$hor_align);       // generate image  
                    if ($border) $thumb->saveEx($img_full_path_new);
                    else $thumb->save($img_full_path_new);
                    @chmod($uploaddir,0755);
                }
                $result[]=$img_src;
            }//end else  
          }//end else  
        }
     return $result;    
    } // end of function ShowCurrentImage()  
   
 }  //end of class Image  