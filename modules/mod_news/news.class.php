<?
// ================================================================================================
//    System     : CMS
//    Module     : News
//    Date       : 04.02.2007
//    Licensed To:   Yaroslav Gyryn
//    Purpose    : Class definition for News - moule
// ================================================================================================

 include_once( SITE_PATH.'/modules/mod_news/news.defines.php' );

// ================================================================================================
//    Class             : News
//    Date              : 23.05.2007
//    Constructor       : Yes
//    Returns           : None
//    Description       : News Module
//    Programmer      :  Yaroslav Gyryn
// ================================================================================================
class News {

    var $Right;
    var $Form;
    var $Msg;
    var $Spr;
    var $db;
    
    var $page;  
    var $display;
    var $sort;
    var $start;
    var $user_id;
    var $module;
    var $fltr;    // filter of group news
    var $id_news = NULL;
    var $width = NULL;
    var $id = NULL;
    var $img = NULL;
    var $search_keywords =NULL;
    var $category = NULL;
    var $sel = NULL;
    var $Err = NULL;
    var $script = NULL;
    var $title = NULL;
    var $source;
     
    var $keywords = NULL;
    var $description = NULL;
    var $lang_id = NULL;
     
    var $str_cat = NULL;
    var $str_news = NULL;
     
    var $subscriber = NULL;
    var $subscr_pass = NULL;
    var $categories = NULL;

    var $subscr = NULL;
    var $full_descr = NULL;
    var $rewrite = NULL;
    var $dt = NULL;
    var $img_path = NULL;
    var $task = NULL;
    var $rss = NULL;
    var $rss_impor = NULL;
     
    // ================================================================================================
    //    Function          : News (Constructor)
    //    Date              : 04.02.2005
    //    Description       : News
    // ================================================================================================
    function News()
    {
     $this->db =  DBs::getInstance();
     $this->Right =  &check_init('RightsNews', 'Rights',"'".$this->user_id."','".$this->module."'");
     $this->Form = &check_init('FormNews', 'Form', "'mod_pages'");         /* create Form object as a property of this class */
     if(empty($this->Msg)) $this->Msg = &check_init('ShowMsg', 'ShowMsg');
     //$this->Msg->SetShowTable(TblModNewsSprTxt);
     if (empty($this->Spr)) $this->Spr = &check_init('SysSpr', 'SysSpr');
      //$this->Article = &check_init('Article', 'Article');
     //if (empty($this->Category))  $this->Category = Singleton::getInstance('Category');
     $this->width = '750';
     if( defined("_LANG_ID") ) $this->lang_id = _LANG_ID;
     
     $this->CheckStatus();
    }

    // ================================================================================================
    // Function : GetNewsCategory()
    // Date :    25.09.2006
    // Parms :   $id - news id
    // Returns : true/false
    // Description : get categ of news
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function GetNewsCategory($id){
      $q = "select id_category from `".TblModNews."` where 1 and `status`='a' and `id`='".$id."'";
      $res = $this->db->db_Query($q);
      $row = $this->db->db_FetchAssoc($res);
      return $row['id_category'];
    } //end of function GetNewsCategory

    // ================================================================================================
    // Function : CheckStatus()
    // Date :    02.05.2005
    // Parms :   $id - poll id
    // Returns : true/false
    // Description : Check News Status
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
      function CheckStatus( $id = NULL ){
        $dt = date('YmdHi');
        $q = "select * from ".TblModNews." where `status`='a'";
        $res = $this->db->db_Query( $q );
        $rows = $this->db->db_GetNumRows();
        $arr = array();
        for( $i = 0; $i < $rows; $i++ )
        {
          $arr[] = $this->db->db_FetchAssoc();
        }
        
        for( $i = 0; $i < $rows; $i++ )
        {
          $tmp = $arr[$i];
          $m = explode( '-', $tmp['end_date'] );
          $m1 = explode( ' ', $m[2] );
          $m2 = explode( ':', $m1[1] );
          $dt2 = $m[0].$m[1].$m1[0].$m2[0].$m2[1];
          if( $dt > $dt2 )
           {
            $q = "update ".TblModNews." set `status`='e' where `id`='".$tmp['id']."'";
            $res = $this->db->db_Query( $q );
           }
        }
        return  true;
      } //--- end of CheckStatus

     // ================================================================================================
     // Function : ConvertDate()
     // Date : 12.05.2011
     // Returns :      true,false / Void
     // Description :  Convert Date Time
     // Programmer :  Yaroslav Gyryn
     // ================================================================================================
     function ConvertDate($date_to_convert, $showTimeOnly = false, $showMonth = false,$show_month_txt=false,$devider='.'){
        $tmp = explode("-", $date_to_convert);
        $tmp2 = explode(" ", $tmp[2]);
        $month = NULL;
        $day = NULL;
        $year = NULL;
        $month =  $tmp[1];
        $day = intval($tmp2[0]);
        $year = $tmp[0];
	if($show_month_txt){
	    if(!isset($this->month[$month]))
                $this->month[$month] = $this->Spr->GetShortNameByCod(TblSysSprMonth, $month, $this->lang_id, 1);
            $month =  $this->month[$month];
	}
        if($showMonth) {
            $month = intval($month);
            if(!isset($this->month[$month]))
                $this->month[$month] = $this->Spr->GetShortNameByCod(TblSysSprMonth, $month, $this->lang_id, 1);
            $month =  $this->month[$month];
            return $day.$devider.$month;
        }
        if($showTimeOnly) {
            $time = $tmp2[1];
            $tmp3 = explode(":", $time);
            return $tmp3[0].':'.$tmp3[1];      //18:30
        }
        return $day.$devider.$month.$devider.$year;
    } // end of function ConvertDate()



    // ===========================================================================================================
    // Function    : GetStartDate()
    // Date        : 02.04.2007
    // Parms       : $start_date - date to convert
    // Returns     : true,false / Void
    // Description : Return start date of news
    // Programmer :  Yaroslav Gyryn
    // ===========================================================================================================
    function GetStartDate($start_date)
    {
           $tmp = explode( '-', $start_date );
           $tmp1 = explode( ' ', $tmp[2] );
           $tmp2 = explode( ':', $tmp1[1] );
           $start_date = $tmp[0].$tmp[1].$tmp1[0].$tmp2[0].$tmp2[1].$tmp2[2];
           return $start_date;
    } // end of function GetStartDate

    // ===========================================================================================================
    // Function    : GetEndDate()
    // Date        : 02.04.2007
    // Parms       : $end_date - date to convert
    // Returns     : true,false / Void
    // Description : Return end date of news
    // Programmer :  Yaroslav Gyryn
    // ===========================================================================================================
    function GetEndDate($end_date)
    {
           $tmp = explode( '-', $end_date );
           $tmp1 = explode( ' ', $tmp[2] );
           $tmp2 = explode( ':', $tmp1[1] );
           $end_date = $tmp[0].$tmp[1].$tmp1[0].$tmp2[0].$tmp2[1].$tmp2[2];
           return $end_date;
    } // end of function GetEndDate 

    // ===========================================================================================================
    // Function    : GetCurrentDate()
    // Date        : 02.04.2007
    // Returns     : true,false / Void
    // Description : Return Current Date for news
    // Programmer :  Yaroslav Gyryn
    // ===========================================================================================================
    function GetCurrentDate()
    {
           $date = date('Y-m-d H:i:s'); 
           $tmp = explode( '-', $date );
           $tmp1 = explode( ' ', $tmp[2] );
           $tmp2 = explode( ':', $tmp1[1] );
           $date = $tmp[0].$tmp[1].$tmp1[0].$tmp2[0].$tmp2[1].$tmp2[2];
           return $date;
    } // end of function GetCurrentDate 

    // ================================================================================================
    // Function : CheckImages
    // Date : 17.11.2006
    // Returns : $res / Void
    // Description : check uploaded images for size, type and other.
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function CheckImages()
    {
         $this->Err = NULL;
         $max_image_width= NEWS_MAX_IMAGE_WIDTH;
         $max_image_height= NEWS_MAX_IMAGE_HEIGHT;
         $max_image_size= NEWS_MAX_IMAGE_SIZE;
         $valid_types =  array("gif", "GIF", "jpg", "JPG", "png", "PNG", "jpeg", "JPEG");
         //print_r($_FILES["image"]);
         if (!isset($_FILES["image"])) return false;
         $cols = count($_FILES["image"]);

         //echo '<br><br>$cols='.$cols;
         for ($i=0; $i<$cols; $i++) {
             //echo '<br>$_FILES["image"]='.$_FILES["image"].' $_FILES["image"]["tmp_name"]["'.$i.'"]='.$_FILES["image"]["tmp_name"]["$i"].' $_FILES["image"]["size"]["'.$i.'"]='.$_FILES["image"]["size"]["$i"];
             //echo '<br>$_FILES["image"]["name"][$i]='.$_FILES["image"]["name"][$i];
             if ( !empty($_FILES["image"]["name"][$i]) ) {
               if ( isset($_FILES["image"]) && is_uploaded_file($_FILES["image"]["tmp_name"][$i]) && $_FILES["image"]["size"][$i] ){
                $filename = $_FILES['image']['tmp_name'][$i];
                $ext = substr($_FILES['image']['name'][$i],1 + strrpos($_FILES['image']['name'][$i], "."));
                //echo '<br>filesize($filename)='.filesize($filename).' $max_image_size='.$max_image_size;
                if (filesize($filename) > $max_image_size) {
                    $this->Err = $this->Err.$this->Msg->show_text('MSG_ERR_FILE_SIZE', TblSysTxt).' ('.$_FILES['image']['name']["$i"].')<br>';
                    continue;
                }
                if (!in_array($ext, $valid_types)) {
                    $this->Err = $this->Err.$this->Msg->show_text('MSG_ERR_FILE_TYPE', TblSysTxt).' ('.$_FILES['image']['name']["$i"].')<br>';  
                }
                else {
                  $size = GetImageSize($filename);
                  //echo '<br>$size='.$size.'$size[0]='.$size[0].' $max_image_width='.$max_image_width.' $size[1]='.$size[1].' $max_image_height='.$max_image_height;
                  if (($size) && ($size[0] < $max_image_width) && ($size[1] < $max_image_height)) {
                     //$alias = $this->Spr->GetNameByCod( TblModCatalogPropSprName, $this->id );
                  }
                  else {
                     $this->Err = $this->Err.$this->Msg->show_text('MSG_ERR_FILE_PROPERTIES', TblSysTxt).' ['.$max_image_width.'x'.$max_image_height.'] ('.$_FILES['image']['name']["$i"].')<br>'; 
                  }
                }
               }
               else $this->Err = $this->Err.$this->Msg->show_text('MSG_ERR_FILE', TblSysTxt).' ('.$_FILES['image']['name']["$i"].')<br>';
             } 
             //echo '<br>$i='.$i;
         } // end for
         return $this->Err;
    }  // end of function CheckImages() 
     
           
    // ================================================================================================
    // Function : SavePicture
    // Date : 03.04.2006
    // Returns : $res / Void
    // Description : Save the file (image) to the folder  and save path in the database (table user_images)
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function SavePicture()
    {
         $tmp_db = new DB();
         $this->Err = NULL;
         $max_image_width= NEWS_MAX_IMAGE_WIDTH;
         $max_image_height= NEWS_MAX_IMAGE_HEIGHT;
         $max_image_size= NEWS_MAX_IMAGE_SIZE;
         $valid_types =  array("gif", "GIF", "jpg", "JPG", "png", "PNG", "jpeg", "JPEG");
        // print_r($_FILES["filename"]);
         //echo $this->img; 
         if (!isset($_FILES["filename"])) return false; 
         $cols = count($_FILES["filename"]);

         //echo '<br><br>$cols='.$cols;
         for ($i=0; $i<$cols; $i++) {
             //echo '<br>$_FILES["image"]='.$_FILES["image"].' $_FILES["image"]["tmp_name"]["'.$i.'"]='.$_FILES["image"]["tmp_name"]["$i"].' $_FILES["image"]["size"]["'.$i.'"]='.$_FILES["image"]["size"]["$i"];
             //echo '<br>$_FILES["image"]["name"][$i]='.$_FILES["image"]["name"][$i];
             if ( !empty($_FILES["filename"]["name"][$i]) ) {
               if ( isset($_FILES["filename"]) && is_uploaded_file($_FILES["filename"]["tmp_name"][$i]) && $_FILES["filename"]["size"][$i] ){
                $filename = $_FILES['filename']['tmp_name'][$i];
                $ext = substr($_FILES['filename']['name'][$i],1 + strrpos($_FILES['filename']['name'][$i], "."));
                $imgNameNoExt = substr($_FILES['filename']['name'][$i], 0, strrpos($_FILES['filename']['name'][$i], "."));
                //echo '<br>filesize($filename)='.filesize($filename).' $max_image_size='.$max_image_size;
                if (filesize($filename) > $max_image_size) {
                    $this->Err = $this->Err.$this->Msg->show_text('MSG_ERR_FILE_SIZE').' ('.$_FILES['filename']['name']["$i"].')<br>';
                    continue;
                }
                if (!in_array($ext, $valid_types)) {
                    $this->Err = $this->Err.$this->Msg->show_text('MSG_ERR_FILE_TYPE').' ('.$_FILES['filename']['name']["$i"].')<br>';  
                }
                else {
                  $size = GetImageSize($filename);
                  //echo '<br>$size='.$size.'$size[0]='.$size[0].' $max_image_width='.$max_image_width.' $size[1]='.$size[1].' $max_image_height='.$max_image_height;
                  if (($size) && ($size[0] < $max_image_width) && ($size[1] < $max_image_height)) {
                     //$alias = $this->Spr->GetNameByCod( TblModCatalogPropSprName, $this->id );
                     $news_img_full_path = SITE_PATH.$this->settings['img_path'];
                     if ( !file_exists ($news_img_full_path) ) mkdir($news_img_full_path,0777);
                     $uploaddir = $news_img_full_path.'/'.$this->id;
                     if ( !file_exists ($uploaddir) ) mkdir($uploaddir,0777); 
                     else @chmod($uploaddir,0777);
                     
                     $Crypt = &check_init('Crypt', 'Crypt');
                     $uploaddir2 = $Crypt->GetTranslitStr($imgNameNoExt).'.'.$ext;
                     $uploaddir1 = $uploaddir."/".$uploaddir2;
                  
                     //echo '<br>$filename='.$filename.'<br> $uploaddir='.$uploaddir.'<br> $uploaddir2='.$uploaddir2;
                     //if (@move_uploaded_file($filename, $uploaddir)) {
                     if ( copy($filename,$uploaddir1) ) {
                         $q="select `move` from `".TblModNewsImg."` where 1";
                         $res = $tmp_db->db_Query( $q );
                         $rows = $tmp_db->db_GetNumRows();
                         $maxx=0;  //add link with position auto_incremental
                         for($i_maxx=0;$i_maxx<$rows;$i_maxx++)
                         {
                            $my = $tmp_db->db_FetchAssoc();
                            if($maxx < $my['move'])
                            $maxx=$my['move'];
                         }
                         $maxx=$maxx+1;                         
                         
                         $q="INSERT into `".TblModNewsImg."` values(NULL,'".$this->id."','".$uploaddir2."','1', '$maxx', NULL)";
                         $res = $tmp_db->db_Query( $q );
                         if( !$res OR !$tmp_db->result ) $this->Err = $this->Err.$this->Msg->show_text('MSG_ERR_SAVE_FILE_TO_DB').' ('.$_FILES['image']['name']["$i"].')<br>';
                         //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
                     }
                     else {
                         $this->Err = $this->Err.$this->Msg->show_text('MSG_ERR_FILE_MOVE').' ('.$_FILES['filename']['name']["$i"].')<br>';
                     }
                     @chmod($uploaddir,0755);
                     @chmod($news_img_full_path,0755);
                  }
                  else {
                     $this->Err = $this->Err.$this->Msg->show_text('MSG_ERR_FILE_PROPERTIES').' ['.$max_image_width.'x'.$max_image_height.'] ('.$_FILES['filename']['name']["$i"].')<br>'; 
                  }
                }
               }
               else $this->Err = $this->Err.$this->Msg->show_text('MSG_ERR_FILE').' ('.$_FILES['filename']['name']["$i"].')<br>';
             } 
             //echo '<br>$i='.$i;
         } // end for
         return $this->Err;

    }  // end of function SavePicture() 


    // ================================================================================================
    // Function : SavePictureTop
    // Date : 03.04.2011
    // Returns : $res / Void
    // Description : Save the file (image) to the folder  and save path in the database (table user_images)
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function SavePictureTop()
    {
         $tmp_db = new DB();
         $this->Err = NULL;
         $max_image_width= NEWS_MAX_IMAGE_WIDTH;
         $max_image_height= NEWS_MAX_IMAGE_HEIGHT;
         $max_image_size= NEWS_MAX_IMAGE_SIZE;
         $valid_types =  array("gif", "GIF", "jpg", "JPG", "png", "PNG", "jpeg", "JPEG");
         //$ln_arr = $ln_sys->LangArray( _LANG_ID ); 
         //print_r($_FILES["topImage"]);
         if (!isset($_FILES["topImage"])) return false; 
         $cols = count($_FILES["topImage"]["name"]);
         for ($i=0; $i<$cols; $i++) {
             //echo '<br>$_FILES["topImage"]='.$_FILES["topImage"].' $_FILES["topImage"]["tmp_name"]["'.$i.'"]='.$_FILES["topImage"]["tmp_name"]["$i"].' $_FILES["topImage"]["size"]["'.$i.'"]='.$_FILES["topImage"]["size"]["$i"];
             //echo '<br>$_FILES["topImage"]["name"][$i]='.$_FILES["topImage"]["name"][$i];
             if ( !empty($_FILES["topImage"]["name"][$i]) ) {
               if ( isset($_FILES["topImage"]) && is_uploaded_file($_FILES["topImage"]["tmp_name"][$i]) && $_FILES["topImage"]["size"][$i] ){
                $filename = $_FILES['topImage']['tmp_name'][$i];
                $ext = substr($_FILES['topImage']['name'][$i],1 + strrpos($_FILES['topImage']['name'][$i], "."));
                //echo '<br>filesize($filename)='.filesize($filename).' $max_image_size='.$max_image_size;
                if (filesize($filename) > $max_image_size) {
                    $this->Err = $this->Err.$this->Msg->show_text('MSG_ERR_FILE_SIZE', TblSysTxt).' ('.$_FILES['topImage']['name']["$i"].')<br>';
                    continue;
                }
                if (!in_array($ext, $valid_types)) {
                    $this->Err = $this->Err.$this->Msg->show_text('MSG_ERR_FILE_TYPE', TblSysTxt).' ('.$_FILES['topImage']['name']["$i"].')<br>';  
                }
                else {
                  $size = GetImageSize($filename);
                  //echo '<br>$size='.$size.'$size[0]='.$size[0].' $max_image_width='.$max_image_width.' $size[1]='.$size[1].' $max_image_height='.$max_image_height;
                  if (($size) && ($size[0] < $max_image_width) && ($size[1] < $max_image_height)) {
                      
                      // Удаление предыдущего изображения для Топ Новости
                      $res = $this->DelTopPicture($this->id); 
                      
                     //$alias = $this->Spr->GetNameByCod( TblModCatalogPropSprName, $this->id );
                     if ( !file_exists (NewsImg_Full_Path) ) mkdir(NewsImg_Full_Path,0777);
                     $uploaddir = NewsImg_Full_Path.$this->id;
                     if ( !file_exists ($uploaddir) ) mkdir($uploaddir,0777); 
                     else @chmod($uploaddir,0777);
                    
                     $uploaddir2 = time().'_'.$i.'.'.$ext;
                     $uploaddir1 = $uploaddir."/".$uploaddir2;
                  
                     //echo '<br>$filename='.$filename.'<br> $uploaddir='.$uploaddir.'<br> $uploaddir2='.$uploaddir2;
                     //if (@move_uploaded_file($filename, $uploaddir)) {
                     if ( copy($filename,$uploaddir1) ) {
                         //$q="INSERT into `".TblModNewsT."` values(NULL,'".$this->id."','".$uploaddir2."','1', '$maxx', NULL)";
                         $q = "UPDATE 
                                    `".TblModNewsTop."` 
                                SET
                                    `image`='".$uploaddir2."'
                                WHERE 
                                    cod ='".$this->id."'
                                AND 
                                    lang_id ='2'
                         ";
                         $res = $tmp_db->db_Query( $q );
                         if( !$res OR !$tmp_db->result ) 
                            $this->Err = $this->Err.$this->Msg->show_text('MSG_ERR_SAVE_FILE_TO_DB', TblSysTxt).' ('.$_FILES['topImage']['name']["$i"].')<br>';
                         //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
                     }
                     else {
                         $this->Err = $this->Err.$this->Msg->show_text('MSG_ERR_FILE_MOVE', TblSysTxt).' ('.$_FILES['topImage']['name']["$i"].')<br>';
                     }
                     @chmod($uploaddir,0755);
                     @chmod(NewsImg_Full_Path,0755);
                  }
                  else {
                     $this->Err = $this->Err.$this->Msg->show_text('MSG_ERR_FILE_PROPERTIES', TblSysTxt).' ['.$max_image_width.'x'.$max_image_height.'] ('.$_FILES['topImage']['name']["$i"].')<br>'; 
                  }
                }
               }
               else $this->Err = $this->Err.$this->Msg->show_text('MSG_ERR_FILE', TblSysTxt).' ('.$_FILES['topImage']['name']["$i"].')<br>';
             } 
             //echo '<br>$i='.$i;
         } // end for       */
         return $this->Err;
    }  // end of function SavePictureTop() 
                
            
    // ================================================================================================
    // Function : UpdatePicture
    // Date : 28.11.2006
    // Returns : $err / error string
    // Description : Save comments of the image to the table
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function UpdatePicture()
    {
         $this->Err = NULL;
         //print_r($this->img_descr);
         for($i=0; $i<count($this->id_img); $i++){
            //if ( isset($this->img_show[$i]) ) $img_show = 1;
            //else $img_show = 0;
            $key = array_search($this->id_img[$i], $this->img_show);
            if ($key!==false) $img_show = 1;
            else $img_show = 0; 
            
            $q="update `".TblModNewsImg."` set `show`='".$img_show."' where `id`='".$this->id_img[$i]."'";
            $res = $this->Right->Query( $q, $this->user_id, $this->module );
            //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;  
            if( !$this->Right->result ) $this->Err = $this->Err.$this->Msg->show_text('MSG_ERR_SAVE_FILE_TO_DB').'<br>';
           
               $res=$this->Spr->SaveNameArr( $this->id_img[$i], $this->img_title[$this->id_img[$i]], TblModNewsImgSprName );
                //echo "<br>res1=".$res;
                if( !$res ) return false;
            
                $res=$this->Spr->SaveNameArr( $this->id_img[$i], $this->img_descr[$this->id_img[$i]], TblModNewsImgSprDescr );
                //echo " <br>res2=".$res;
                if( !$res ) return false;
          
         }
         return $this->Err;
    }  // end of function UpdatePicture()
    

    /**
    * Class method DelPicture
    * function for Remove images from table and disk
    * @params $id_img_del - array with list of id images
    * @param $id_news - id of the news
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.1, 08.10.2011
    * @return true or false
    */
    function DelPicture($id_img_del, $id_news=NULL)
    {
         $tmp_db = new DB();
         $del=0;
         if(empty($id_news)) $id_news = $this->GetNewsIdByImgId($id_img_del[0]);
         $path='';
         for($i=0; $i<count($id_img_del); $i++){
           $u=$id_img_del[$i];
           
           $q="SELECT * FROM `".TblModNewsImg."` WHERE `id`='".$u."'";
           $res = $tmp_db->db_Query( $q );
           //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;  
           if( !$res OR !$tmp_db->result ) return false;
           $row = $tmp_db->db_FetchAssoc();
           $path = $this->GetImgFullPath($row['path'], $row['id_news']);
           // delete file which store in the database
           if (file_exists($path)) {
              $res = unlink ($path);
              if( !$res ) return false;
           }
           
           $q="DELETE FROM `".TblModNewsImg."` WHERE `id`='".$u."'";
           $res = $tmp_db->db_Query( $q );
           //echo '<br>2q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;  
           if( !$res) return false; 
           if( !$tmp_db->result ) return false;
           $res = $this->Spr->DelFromSpr( TblModNewsImgSprName, $u ); 
           if( !$res )return false;           
           $res = $this->Spr->DelFromSpr( TblModNewsImgSprDescr, $u ); 
           if( !$res )return false;
           $del=$del+1;
         
           $path = SITE_PATH.$this->settings['img_path'].'/'.$row['id_news'];
           //echo '<br> $path='.$path;
           if( is_dir($path) ){
               $handle = @opendir($path);
               //echo '<br> $handle='.$handle; 
               $cols_files = 0;
               while ( ($file = readdir($handle)) !==false ) {
                   //echo '<br> $file='.$file;
                   $mas_file=explode(".",$file);
                   $mas_img_name=explode(".",$row['path']);
                   if ( strstr($mas_file[0], $mas_img_name[0].NEWS_ADDITIONAL_FILES_TEXT) and $mas_file[1]==$mas_img_name[1] ) {
                      $res = @unlink ($path.'/'.$file);
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
         $n = $this->GetImagesCount($id_news);
         if( $n==0 AND is_dir($path) ) $this->full_rmdir($path);
         
         return $del;
    } // end of function DelPicture()


    // ================================================================================================
    // Function : DelTopPicture
    // Date : 07.04.2011
    // Parms :  $id - id news
    // Returns : $res / Void
    // Description : Remove Top images from table and disk
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function DelTopPicture($id)
    {
    $db = new DB();
    $q = "SELECT image  FROM `".TblModNewsTop."` WHERE cod= '".$id."' ";
    $res = $db->db_Query( $q );
    //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;  
    if( !$res) return false; 
    if( !$db->result ) return false;
    $rows = $db->db_GetNumRows();
    $arr = array();
    for($i=0; $i<$rows; $i++) {
        $arr[] = $db->db_FetchAssoc();
    }
    $del=0;
    $path='';
    for($i=0; $i<$rows; $i++){
        $row = $arr[$i];
        $path = NewsImg_Full_Path.'/'.$id.'/'.$row['image'];
        // delete file which store in the database
        if (file_exists($path)) {
            $res = unlink ($path);
            if( !$res ) return false;
        }
        $del=$del+1;
        $path = NewsImg_Full_Path.$id;
        if( is_dir($path) ){
        $handle = @opendir($path);
        //echo '<br> $handle='.$handle; 
        $cols_files = 0;
        while ( ($file = readdir($handle)) !==false ) {
           //echo '<br> $file='.$file;
           $mas_file=explode(".",$file);
           $mas_img_name=explode(".",$row['image']);
           if ( strstr($mas_file[0], $mas_img_name[0].NEWS_ADDITIONAL_FILES_TEXT) and $mas_file[1]==$mas_img_name[1] ) {
              $res = @unlink ($path.'/'.$file);
              if( !$res ) return false;
           }
           if ($file == "." || $file == ".." ) {
               $cols_files++;
           }
        }
           closedir($handle);
       }
     }
     $n = $this->GetImagesCount($id);
     if( $n==0 AND is_dir($path) ) $this->full_rmdir($path);
     
     return $del;
    } // end of function DelTopPicture()
         
     
     // ================================================================================================
     // Function : full_rmdir
     // Date : 07.04.2011
     // Parms :  $dirname - directory for full del
     // Returns : $res / Void
     // Description : Full remove directory from disk (all files and subdirectory)
     // Programmer : Yaroslav Gyryn
     // ================================================================================================     
     function full_rmdir($dirname)
     {
        if ($dirHandle = opendir($dirname)){
            $old_cwd = getcwd();
            chdir($dirname);

            while ($file = readdir($dirHandle)){
                if ($file == '.' || $file == '..') continue;

                if (is_dir($file)){
                    if (!full_rmdir($file)) return false;
                }else{
                    if (!unlink($file)) return false;
                }
            }

            closedir($dirHandle);
            chdir($old_cwd);
            if (!rmdir($dirname)) return false;

            return true;
        }else{
            return false;
        }
     }

    // ================================================================================================
    // Function : GetImagesCount
    // Date : 28.11.2006
    // Parms : $id_news  / id of the user
    // Description : return count of images for current user with $id_news
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetImagesCount($id_news)
    {
        $image = NULL;
        $tmp_db = new DB();
        
        $q = "SELECT * FROM `".TblModNewsImg."` WHERE 1 AND `id_news`='$id_news' order by `move`";
        $res = $tmp_db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
        if ( !$res or !$tmp_db->result ) return false;
        $rows = $tmp_db->db_GetNumRows();
        return $rows;           
    } //end of function GetImagesCount()       

    // ================================================================================================
    // Function : GetImages
    // Date : 13.10.2006
    // Parms : $id_news  / id of the user
    // Returns : return $image for current value with cod=$cod
    // Description : return image for current value with cod=$cod, if it is exist 
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetImages($id_news)
    {
        $image = NULL;
        $tmp_db = new DB();
        
        $q = "SELECT * FROM `".TblModNewsImg."` WHERE 1 AND `id_news`='$id_news' order by `move`";
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
    // Date : 01.04.2011
    // Parms : $id_news  / id of the news
    // Returns : return all image data 
    // Description : return image for current value 
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function GetImagesToShow($id_news)
    {
        $image = NULL;
        $tmp_db = new DB();
        
        $q = "SELECT 
                    `".TblModNewsImg."`.`id`,
                    `".TblModNewsImg."`.`path`,
                    `".TblModNewsImg."`.`show`,
                    `".TblModNewsImg."`.`move`,
                    `".TblModNewsImg."`.`path`,
                    `".TblModNewsImgSprName."`.name,
                    `".TblModNewsImgSprDescr."`.name as descr
                    FROM `".TblModNewsImg."` 
                    LEFT JOIN (`".TblModNewsImgSprName."`, `".TblModNewsImgSprDescr."`)    
                    ON                 
                     (
                     `".TblModNewsImg."`.`id`=`".TblModNewsImgSprName."`.`cod` 
                        AND 
                     `".TblModNewsImg."`.`id`=`".TblModNewsImgSprDescr."`.`cod` 
                        AND
                        `".TblModNewsImgSprDescr."`.`lang_id` ='".$this->lang_id."' 
                        AND
                        `".TblModNewsImgSprName."`.`lang_id` ='".$this->lang_id."' 
                     )
                WHERE 
                    `".TblModNewsImg."`.`id_news`='".$id_news."' AND `show`=1 
                ORDER BY 
                `".TblModNewsImg."`.`move`";
                              
        $res = $tmp_db->db_Query($q);
        //echo '<br>'.$q.'<br/> res='.$res.' $tmp_db->result='.$tmp_db->result;
        if ( !$res or !$tmp_db->result ) return false;
        $rows = $tmp_db->db_GetNumRows();
        $arr = NULL;
        for($i=0; $i<$rows; $i++){
            $row = $tmp_db->db_FetchAssoc();
            $arr[$i] = $row;
        }
        return $arr;           
    } //end of function GetImagesToShow()

    // ================================================================================================
    // Function : GetMainImage
    // Date : 13.10.2006
    // Parms :   $id_news    / id of the user
    //           $part       /  for front-end or for back-end
    // Returns : return $image for current value with cod=$cod
    // Description : return image for current value with cod=$cod, if it is exist 
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetMainImage($id_news, $part = 'front')
    {
        $image = NULL;
        $tmp_db = new DB();
        
        $q = "SELECT * FROM `".TblModNewsImg."` WHERE 1 AND `id_news`='".$id_news."'";
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
    // Function : GetTopImage
    // Date : 13.10.2006
    // Parms :   $id_news    / id of the user
    // Returns : return $image for current value with cod=$cod
    // Description : return image for current value with cod=$cod, if it is exist 
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetTopImage($id_news)
    {
        $tmp_db = new DB();
        $q = "SELECT * FROM `".TblModNewsTop."` WHERE 1 AND `cod`='".$id_news."'";
        $res = $tmp_db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
        if ( !$res or !$tmp_db->result ) return false;
        $rows = $tmp_db->db_GetNumRows();
        //echo '<br>$rows='.$rows;
        $row = $tmp_db->db_FetchAssoc();
        return $row['image'];           
    } //end of function GetTopImage()           
    // ================================================================================================
    // Function : GetMainImageData
    // Date : 13.10.2006
    // Parms :   $id_news    / id of the user
    //           $part       /  for front-end or for back-end
    // Returns : return $image for current value with cod=$cod
    // Description : return image for current value with cod=$cod, if it is exist 
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetMainImageData($id_news, $part = 'front')
    {
        $image = NULL;
        $tmp_db = new DB();
        
        $q = "SELECT * FROM `".TblModNewsImg."` WHERE 1 AND `id_news`='".$id_news."'";
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
    * function for import data from old Edifier News to new
    * @param $img - id of the picture, or relative path of the picture /images/mod_news/24094/12984541610.jpg or name of the picture 12984541610.jpg
    * @param $id_news - id of the news
    * @param $size - Can be "size_auto" or  "size_width" or "size_height"
    * @param $quality - quality of the image from 0 to 100
    * @param $wtm - make watermark or not. Can be "txt" or "img"
    * @param $parameters - other parameters for TAG <img> like border
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.1, 08.10.2011
    * @return true or false
    */
    function ShowImage($img = NULL, $id_news, $size = NULL, $quality = NULL, $wtm = NULL, $parameters = NULL, $return_src=false)
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
            if(!isset($img_data['id_news'])) {return false;}
            $settings_img_path = $this->settings['img_path'].$img_data['id_news']; // like /uploads/45
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
                if(!$id_news) return false;
                $settings_img_path = $this->settings['img_path'].'/'.$id_news; // like /uploads/45
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
            $img_name_new = $mas_img_name[0].NEWS_ADDITIONAL_FILES_TEXT.'width_'.$size_width.'.'.$mas_img_name[1];
        }
        elseif ( strstr($size,'size_auto') ) {
            $size_auto = substr( $size, strrpos($size,'=')+1, strlen($size) );
            $img_name_new = $mas_img_name[0].NEWS_ADDITIONAL_FILES_TEXT.'auto_'.$size_auto.'.'.$mas_img_name[1];
        }
        elseif ( strstr($size,'size_height') ) {
            $size_height = substr( $size, strrpos($size,'=')+1, strlen($size) );
            $img_name_new = $mas_img_name[0].NEWS_ADDITIONAL_FILES_TEXT.'height_'.$size_height.'.'.$mas_img_name[1];
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
                    if ( defined('WATERMARK_TEXT') ) $thumb->txt_watermark=NEWS_WATERMARK_TEXT;        // [OPTIONAL] set watermark text [RECOMENDED ONLY WITH GD 2 ]
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
                        $img_name_new = $mas_img_name[0].NEWS_ADDITIONAL_FILES_TEXT.'width_'.$size_width.'.'.$mas_img_name[1];
                    elseif(!empty($size_auto )) 
                        $img_name_new = $mas_img_name[0].NEWS_ADDITIONAL_FILES_TEXT.'auto_'.$size_auto.'.'.$mas_img_name[1];
                    elseif(!empty($size_height )) 
                        $img_name_new = $mas_img_name[0].NEWS_ADDITIONAL_FILES_TEXT.'height_'.$size_height.'.'.$mas_img_name[1];
                    $img_full_path_new = SITE_PATH.$settings_img_path.'/'.$img_name_new;
                    $img_src = $settings_img_path.'/'.$img_name_new;
                    $uploaddir = SITE_PATH.$settings_img_path;
                }
                else {
                    $mas_img_name=explode(".",$img_with_path);
                    //$img_name_new = $mas_img_name[0].NEWS_NEWS_ADDITIONAL_FILES_TEXT.intval($thumb->img['x_thumb']).'x'.intval($thumb->img['y_thumb']).'.'.$mas_img_name[1];
                    if(!empty($size_width )) 
                        $img_name_new = $mas_img_name[0].NEWS_ADDITIONAL_FILES_TEXT.'width_'.$size_width.'.'.$mas_img_name[1];
                    elseif(!empty($size_auto )) 
                        $img_name_new = $mas_img_name[0].NEWS_ADDITIONAL_FILES_TEXT.'auto_'.$size_auto.'.'.$mas_img_name[1];
                    elseif(!empty($size_height )) 
                        $img_name_new = $mas_img_name[0].NEWS_ADDITIONAL_FILES_TEXT.'height_'.$size_height.'.'.$mas_img_name[1];
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

    // ================================================================================================
    // Function : ShowImageSquare
    // Date : 17.11.2006
    // Parms :  $img - path of the picture
    //          $id_news - id of the news
    //          $size -  Can be "size_auto" or  "size_width" or "size_height"
    //          $quality - quality of the image
    //          $wtm - make watermark or not. Can be "txt" or "img"
    //          $parameters - other parameters for TAG <img> like border
    // Returns : $res / Void
    // Description : Show images for news
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function ShowImageSquare($img = NULL, $id_news,  $plain=true, $size_width = 90, $quality = 85, $parameters = NULL)
    {
     $return_value='';   
     $alt = NULL;
     $title = NULL;
     $settings_img_path = NewsImg_Path;
     //echo "<br>img=".$img;
     
     if ( !strstr($img, '.') ) {
         if(!isset($img_data['id_news'])) {return false;}
         $settings_img_path = NewsImg_Full_Path.'/'.$id_news.'/'; // like /uploads/45
         $img_name = $img_data['path'];  // like R1800TII_big.jpg
         $img_with_path = $settings_img_path.'/'.$img_name; // like /uploads/45/R1800TII_big.jpg
     }
     else {
         $img_with_path = $this->GetImgPath($img, $id_news);  
         $img_name = $img;
     }   
     $img_full_path = SITE_PATH.$img_with_path; // like z:/home/speakers/www/uploads/45/R1800TII_big.jpg

     if ( !file_exists($img_full_path) ) return false;

        $ext = strtolower($this->GetExtationOfFile($img_full_path));
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
        //echo '<br> $img_full_path='.$img_full_path.'<br>';
        $mas_img_name=explode(".",$img_name);
        $img_name_new = $mas_img_name[0].'_'.intval($size_width).'x'.intval($size_width).'.'.$mas_img_name[1];
        $settings_img_path=$settings_img_path.$id_news;  
        $img_full_path_new = SITE_PATH.$settings_img_path.'/'.$img_name_new;
        $img_src = $settings_img_path.'/'.$img_name_new;
        $uploaddir = SITE_PATH.$settings_img_path;
        
       //header("Content-type: image/jpeg");
       $dest = @imagecreatetruecolor($size_width,$size_width); 

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
       //echo $src;
       if ($w_src<$h_src)
            if ($plain) 
                imagecopyresampled($dest, $src, 0, 0, 0, 0, $size_width, $size_width,
                min($w_src,$h_src), 
                min($w_src,$h_src)); 
            else
                imagecopyresized($dest, $src, 0, 0, 0, 0, $size_width, $size_width,min($w_src,$h_src), min($w_src,$h_src)); 

        if ($w_src==$h_src)
            if ($plain) imagecopyresampled($dest, $src, 0, 0, 0, 0, $size_width, $size_width, $w_src, $w_src);
            else imagecopyresized($dest, $src, 0, 0, 0, 0, $size_width, $size_width, $w_src, $w_src); 
            $uploaddir = substr($img_with_path, 0, strrpos($img_with_path,'/'));                      
        if ( !strstr($parameters, 'alt') ) $alt = $this->GetPictureAlt($img);
        if ( !strstr($parameters, 'title') ) $title = $this->GetPictureTitle($img);
        //echo '<br>$img_name_new='.$img_name_new;  
        //echo '<br>$img_full_path_new='.$img_full_path_new;
        //echo '<br>$img_src='.$img_src;
        if ( !strstr($parameters, 'alt') )  $parameters = $parameters.' alt="'.htmlspecialchars($alt).'"';
        if ( !strstr($parameters, 'title') ) $parameters = $parameters.' title=" '.htmlspecialchars($title).' "';
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
    // Date : 31.08.2009
    // Parms :  $filename - name of the image
    // Returns : $res / Void
    // Description : return extenation of file
    // Programmer : Oleg Morgalyuk
    // ================================================================================================
    function GetExtationOfFile($filename)
    {
        return $ext = substr($filename,1 + strrpos($filename, ".")); 
    }// end of function GetExtationOfFile()
    
    // ================================================================================================
    // Function : GetImgFullPath
    // Date : 06.11.2006
    // Parms :  $img - name of the image
    //          $id_news - id of the user
    // Returns : $res / Void
    // Description : return path to the image like /images/mod_user/120/1162648375_0.jpg 
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetImgFullPath($img = NULL, $id_news = NULL )
    {
        return SITE_PATH.$this->settings['img_path'].'/'.$id_news.'/'.$img;
    } //end of function GetImgFullPath() 

    // ================================================================================================
    // Function : GetImgPath
    // Date : 06.11.2006
    // Parms :  $img - name of the image
    //          $id_news - id of the user
    // Returns : $res / Void
    // Description : return path to the image like /images/mod_user/120/1162648375_0.jpg 
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetImgPath($img = NULL, $id_news = NULL )
    {
        return $this->settings['img_path'].'/'.$id_news.'/'.$img;
    } //end of function GetImgPath()        



    // ================================================================================================
    // Function : GetPictureData
    // Date : 03.04.2006
    // Parms :  $id_img - id of the image
    // Returns : $res / Void
    // Description : return array with path to the pictures of current product
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetPictureData($id_img)
    {
        $tmp_db = DBs::getInstance();           
       
        $q="SELECT `".TblModNewsImg."`.*,
            `".TblModNewsImgSprName."`.`name`,
            `".TblModNewsImgSprDescr."`.`name` AS `descr`
            FROM `".TblModNewsImg."`
            LEFT JOIN `".TblModNewsImgSprName."` ON (`".TblModNewsImg."`.`id`=`".TblModNewsImgSprName."`.`cod` AND `".TblModNewsImgSprName."`.`lang_id`='".$this->lang_id."') 
            LEFT JOIN `".TblModNewsImgSprDescr."` ON (`".TblModNewsImg."`.`id`=`".TblModNewsImgSprDescr."`.`cod` AND `".TblModNewsImgSprDescr."`.`lang_id`='".$this->lang_id."') 
            WHERE `".TblModNewsImg."`.`id`='".$id_img."'";
        $res = $tmp_db->db_Query( $q );
        //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;  
        if( !$res OR !$tmp_db->result ) return false;
        $row = $tmp_db->db_FetchAssoc();
        return $row;                    
        
    } // end of function GetPictureData()


    // ================================================================================================
    // Function : GetPictureAlt
    // Date : 19.05.2006
    // Parms :  $id_img - id of the image
    // Returns : $res / Void
    // Description : return alt for this image
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetPictureAlt($img, $show_name = true)
    {
        
        if ( strstr($img, '.') ) {   
        $id_img = $this->GetImgIdByPath($img);
        } else {
        $id_img = $img;
        }
        
        // echo "<br>id_img=".$id_img;
        $alt = $this->Spr->GetNameByCod(TblModNewsImgSprName, $id_img, $this->lang_id, 1);
        // echo '<br>$alt='.$alt;
        if ( empty($alt) and $show_name ) {
        $tmp_db = new DB();           
        $q="SELECT `id_news` FROM `".TblModNewsImg."` WHERE `id`='".$id_img."'";
        $res = $tmp_db->db_Query( $q );
        // echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;  
        if( !$res OR !$tmp_db->result ) return false;
        $row = $tmp_db->db_FetchAssoc();
        
        $alt = $this->Spr->GetNameByCod(TblModNewsSprSbj, $row['id_news'], $this->lang_id, 1);
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
    // Date : 19.05.2006
    // Parms :  $id_img - id of the image
    // Returns : $res / Void
    // Description : return title for this image
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetPictureTitle($img)
    {
        if ( strstr($img, '.') ) {   
        $id_img = $this->GetImgIdByPath($img);
        } else {
        $id_img = $img;
        }
        
        $alt = htmlspecialchars($this->Spr->GetNameByCod(TblModNewsImgSprDescr, $id_img, $this->lang_id, 1));
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
        $tmp_db = new DB();
        
        $q = "SELECT * FROM `".TblModNewsImg."` WHERE 1 AND `path`='$img'";
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
    // Function : GetNewsIdByImgId
    // Date : 22.06.2007 
    // Parms :  $img - name of the picture
    // Returns : $res / Void
    // Description : return title for image 
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function GetNewsIdByImgId( $img )
    {
        $tmp_db = new DB();
        $q = "SELECT * FROM `".TblModNewsImg."` WHERE 1 AND `id`='$img'";
        $res = $tmp_db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
        if ( !$res or !$tmp_db->result ) return false;
        //$rows = $tmp_db->db_GetNumRows();
        // echo '<br>$rows='.$rows;
        $row = $tmp_db->db_FetchAssoc();
        $id = $row['id_news']; 
        return $id;            
    } //end of function GetNewsIdByImgId()       


    // ================================================================================================
    // Function : upImg()
    // Date : 4.04.2007
    // Returns :      true,false / Void
    // Description :  Up position
    // Programmer :  Yaroslav Gyryn
    // ================================================================================================
    function upImg($table, $level_name = NULL, $level_val = NULL)
    {
        $tmp_db = new DB(); 
        $q="select * from `$table` where `move`='$this->move'";
        if ( !empty($level_name) ) $q = $q." AND `$level_name`='$level_val'"; 
        $res = $tmp_db->db_Query( $q );
        //echo '<br>q='.$q.' res='.$res; // $this->Right->result='.$this->db->rest;
        if( !$res )return false;
        $rows = $tmp_db->db_GetNumRows();
        $row = $tmp_db->db_FetchAssoc();
        $move_down = $row['move'];
        $id_down = $row['id'];

        $q="select * from `$table` where `move`<'$this->move'";
        if ( !empty($level_name) ) $q = $q." AND `$level_name`='$level_val'";
        $q = $q." order by `move` desc";
        $res = $tmp_db->db_Query( $q );
        //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;
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
    // Programmer :  Yaroslav Gyryn
    // ================================================================================================
    function downImg($table, $level_name = NULL, $level_val = NULL)
    {
        $tmp_db = new DB();    
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
    // Function : GetNewsData()
    // Date : 06.04.2007
    // Returns :      true,false / Void
    // Description :  Return news data
    // Programmer :  Yaroslav Gyryn
    // ================================================================================================
    function GetNewsData( $news_id = NULL )
    {
        if(!$news_id) return true; 
        $q = "SELECT `".TblModNews."`.*, `".TblModNewsCat."`.name AS `category`, `".TblModNewsSprSbj."`.name AS `sbj`, `".TblModNewsSprShrt."`.name AS `shrt_news`, `".TblModNewsSprFull."`.name AS `full_news` 
              FROM `".TblModNews."`, `".TblModNewsCat."`, `".TblModNewsSprSbj."`, `".TblModNewsSprShrt."`, `".TblModNewsSprFull."`
              WHERE `".TblModNews."`.id_category=`".TblModNewsCat."`.cod
              AND `".TblModNewsCat."`.lang_id='".$this->lang_id."'
              AND `".TblModNews."`.id=`".TblModNewsSprSbj."`.cod
              AND `".TblModNewsSprSbj."`.lang_id='".$this->lang_id."'
              AND `".TblModNews."`.id=`".TblModNewsSprShrt."`.cod
              AND `".TblModNewsSprShrt."`.lang_id='".$this->lang_id."'
              AND `".TblModNews."`.id=`".TblModNewsSprFull."`.cod
              AND `".TblModNewsSprFull."`.lang_id='".$this->lang_id."'
             ";
        if( !empty($this->fltr)){
            $q .= $this->fltr;
        }
        $res = $this->db->db_Query( $q );
        //echo '<br>'.$q.' $res='.$res.' $this->db->result='.$this->db->result; 
        if ( !$res OR !$this->db->result ) return false;
        //$rows = $this->db->db_GetNumRows();
        $row = $this->db->db_FetchAssoc();
        return $row;
    } //end of fuinction GetNewsData()  
           

    // ================================================================================================
    function GetNewsCatLast( $id_cat=1, $limit=3, $active=true)
    {
        $q = "SELECT 
             `".TblModNews."`.id,
            `".TblModNews."`.start_date,
            `".TblModNews."`.id_category,
            `".TblModNewsSprSbj."`.lang_id,
            `".TblModNewsSprSbj."`.name,
            `".TblModNewsSprShrt."`.name as shrt
        FROM 
            `".TblModNews."`, `".TblModNewsSprSbj."`, `".TblModNewsSprShrt."` 
        WHERE 
            `".TblModNews."`.id = `".TblModNewsSprSbj."`.cod and
            `".TblModNews."`.id = `".TblModNewsSprShrt."`.cod and
            `".TblModNews."`.id_category ='".$id_cat."' and 
            `".TblModNewsSprSbj."`.lang_id ='".$this->lang_id."' and 
            `".TblModNewsSprSbj."`.name !=''  AND 
            `".TblModNewsSprShrt."`.lang_id='".$this->lang_id."' ";
        if($active==true)
           $q .= " and `".TblModNews."`.status='a' ";
        if(isset($this->id))
            $q .= " and `".TblModNews."`.id!= '".$this->id."' ";
        
        $q .="ORDER BY 
            `display` desc LIMIT ".$limit;
            
        $res = $this->db->db_Query( $q );
        //echo '<br>'.$q.' $res='.$res.' $this->db->result='.$this->db->result; 
        if ( !$res OR !$this->db->result ) return false;
        $rows = $this->db->db_GetNumRows();
        $array = array();
        for( $i=0; $i<$rows; $i++ )  {
            $array[$i] =  $this->db->db_FetchAssoc($res);
        }
        return $array;
    } //end of function GetNewsCatLast()  


    // ================================================================================================
    // Function : GetAllNewsIdCat()
    // Date :    26.05.2011
    // Returns : true/false
    // Description : Get all Id news for $cat
    // Programmer :  Yaroslav Gyryn
    // ================================================================================================
    function GetAllNewsIdCat( $id_cat=1, $idModule = null )
    {
        $q = "
            SELECT 
                `".TblModNews."`.id,
                `".TblModNews."`.start_date,
                `".TblModNews."`.display
            FROM 
                `".TblModNews."`
            WHERE 
                `".TblModNews."`.id_category ='".$id_cat."' and 
               `".TblModNews."`.status='a' 
            ORDER BY 
                `display` desc
        ";
            
        $res = $this->db->db_Query( $q );
        //echo '<br>'.$q.' <br/>$res='.$res.' $this->db->result='.$this->db->result; 
        if ( !$res OR !$this->db->result ) return false;
        $rows = $this->db->db_GetNumRows();
        $array = array();
        for( $i=0; $i<$rows; $i++ )  {
            $row =  $this->db->db_FetchAssoc($res);
            $dateId = strtotime ($row['start_date']);
            $array[$dateId]['id'] = $row['id'];
            //$array[$dateId]['start_date'] = $row['start_date'];
            $array[$dateId]['id_module'] = $idModule;
        }
        return $array;
    } //end of function GetAllNewsIdCat()  


    // ================================================================================================
    // Function : GetAllNewsIdRSS()
    // Date :    26.05.2011
    // Returns : true/false
    // Description : Get all Id news for RSS
    // Programmer :  Yaroslav Gyryn
    // ================================================================================================
    function GetAllNewsIdRSS($idModule = null, $limit = 20 )
    {
        $q = "
            SELECT 
                `".TblModNews."`.id,
                `".TblModNews."`.start_date,
                `".TblModNews."`.display
            FROM 
                `".TblModNews."`
            WHERE 
               `".TblModNews."`.status='a' 
               AND 
                `".TblModNews."`.property != 1 
            ORDER BY 
                `display` desc
            LIMIT ".$limit."
        ";
        $res = $this->db->db_Query( $q );
        //echo '<br>'.$q.' <br/>$res='.$res.' $this->db->result='.$this->db->result; 
        if ( !$res OR !$this->db->result ) return false;
        $rows = $this->db->db_GetNumRows();
        $array = array();
        for( $i=0; $i<$rows; $i++ )  {
            $row =  $this->db->db_FetchAssoc($res);
            $dateId = strtotime ($row['start_date']);
            $array[$dateId]['id'] = $row['id'];
            $array[$dateId]['id_module'] = $idModule;
        }
        return $array;
    } //end of function GetAllNewsIdRSS()
    
    // ================================================================================================
    // Function : GetNewsIdByQuickSearch()
    // Date :    26.05.2011
    // Returns : true/false
    // Description : Get all Id news for $search_keywords
    // Programmer :  Yaroslav Gyryn
    // ================================================================================================
    function GetNewsIdByQuickSearch( $search_keywords = null, $idModule = null )
    {
        $search_keywords = stripslashes($search_keywords);
        $sel_table = NULL;
        $str_like = NULL;
        $filter_cr = ' OR ';
        $str_like = $this->build_str_like(TblModNewsSprSbj.'.name', $search_keywords);
        $str_like .= $filter_cr.$this->build_str_like(TblModNewsSprShrt.'.name', $search_keywords);
        $str_like .= $filter_cr.$this->build_str_like(TblModNewsSprFull.'.name', $search_keywords); 
        $sel_table = "`".TblModNews."`, `".TblModNewsCat."`, `".TblModNewsSprSbj."`, `".TblModNewsSprShrt."`, `".TblModNewsSprFull."` ";
   
        $q ="SELECT 
                `".TblModNews."`.id,
                `".TblModNews."`.start_date,
                `".TblModNews."`.display
             FROM ".$sel_table."
             WHERE (".$str_like.")
             AND `".TblModNewsSprSbj."`.lang_id = '".$this->lang_id."'
             AND `".TblModNews."`.id = `".TblModNewsSprSbj."`.cod
             AND `".TblModNewsSprShrt."`.lang_id = '".$this->lang_id."'
             AND `".TblModNews."`.id = `".TblModNewsSprShrt."`.cod
             AND `".TblModNewsSprFull."`.lang_id = '".$this->lang_id."'
             AND `".TblModNews."`.id = `".TblModNewsSprFull."`.cod
             AND `".TblModNews."`.`id_category` = `".TblModNewsCat."`.`cod`
             AND `".TblModNewsCat."`.lang_id = '".$this->lang_id."' 
             AND `".TblModNews."`.status='a' 
             ORDER BY `".TblModNews."`.`display` desc
            ";
            
        $res = $this->db->db_Query( $q );
        //echo '<br>'.$q.' <br/>$res='.$res.' $this->db->result='.$this->db->result; 
        if ( !$res OR !$this->db->result ) return false;
        $rows = $this->db->db_GetNumRows();
        $array = array();
        for( $i=0; $i<$rows; $i++ )  {
            $row =  $this->db->db_FetchAssoc($res);
            $dateId = strtotime ($row['start_date']);
            $array[$dateId]['id'] = $row['id'];
            //$array[$dateId]['start_date'] = $row['start_date'];
            $array[$dateId]['id_module'] = $idModule;
        }
        //print_r($array);
        return $array;
    } //end of function GetNewsIdByQuickSearch() 
                       
    // ================================================================================================
    // Function : CotvertDataToOutputArray
    // Date : 19.05.2006
    // Parms :  $rows - count if founded records stored in object $this->db
    //          $sort - type of sortaion returned array
    //                  (move - default value, name)
    //          $asc_desc - sortation Asc or Desc
    //          $data - count of returned data (full or short)
    // Returns : $arr
    // Description : return arr of content for selected category
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function ConvertDataToOutputArray ($rows, $sort = "id", $asc_desc = "asc", $data = "full")
    {
        // echo '<br> $sort='.$sort.' $rows='.$rows;
        $arr0 = NULL;
        if(!$rows) return true;
        
        $settings = $this->GetSettings(); 
        
        for ($i=0;$i<$rows;$i++){
        $row = $this->db->db_FetchAssoc();
        $main_img_data = $this->GetMainImageData($row['id'], 'front'); 
        switch($sort){
        case 'id':
            $index_sort = $row['id'];
            break;
        case 'display ':
            $index_sort = $row['display'];
            break;                                                          
        default:
            $str_to_eval = '$index_sort = "_".$row['."'".$sort."'".']."_".$row['."'id'".'];';
            //echo '<br> $str_to_eval='.$str_to_eval;
            eval($str_to_eval);
            break;
        }
            
        $arr0[$index_sort]["id"] = $row['id'];
        $arr0[$index_sort]['id_category'] = $row['id_category'];
        
        if ( isset($settings['img']) AND $settings['img']=='1' ) {
        $arr0[$index_sort]["img"]["id"] = $main_img_data['id'];
        $arr0[$index_sort]["img"]["descr"] = $main_img_data['descr'];
        $arr0[$index_sort]["img"]["path"] = $main_img_data['path'];//$this->GetMainImage($row['id'], 'front');
        $arr0[$index_sort]["img"]["img_path"] = $this->GetImgPath( $this->GetMainImage($row['id'], 'front'), $row['id'] );
        $arr0[$index_sort]["img"]["full_img_path"] = $this->GetImgFullPath( $this->GetMainImage($row['id'], 'front'), $row['id'] ); 
        }
        
        $arr0[$index_sort]['start_date'] = $this->ConvertDate($row['start_date']); 
        $arr0[$index_sort]['category'] = $this->Spr->GetNameByCod( TblModNewsCat, $row['id_category'] );
        
        $sbj = strip_tags($this->Spr->GetNameByCod( TblModNewsSprSbj, $row['id'], $this->lang_id, 0 ));
        $sbj = str_replace ( '&amp;', '&', $sbj ); 
        $sbj = str_replace ( '&#039;', '\'', $sbj ); 
        $sbj = str_replace ( '&quot;', '\"', $sbj ); 
        $arr0[$index_sort]["sbj"] = $sbj; 
        
        $shrt_news = strip_tags(stripslashes( $this->Spr->GetNameByCod( TblModNewsSprShrt, $row['id'] ) ), "<p><br><strong><u><i><b><ul><li><table><tr><td>");
        $shrt_news = str_replace ( '&amp;', '&', $shrt_news ); 
        $shrt_news = str_replace ( '&#039;', '\'', $shrt_news ); 
        $shrt_news = str_replace ( '&quot;', '\"', $shrt_news );
        if($shrt_news=='') $shrt_news = $this->Msg->show_text('TXT_NEWS_EMPTY'); 
        if ( isset($settings['short_descr']) AND $settings['short_descr']=='1' ) {  
        $arr0[$index_sort]["shrt_news"] = $shrt_news;
        }
        if(empty($arr0[$index_sort]["shrt_news"])) $arr0[$index_sort]["shrt_news"] = $this->Msg->show_text('TXT_NEWS_EMPTY');   
        
        if( $data=='full' ){         
        
        if ( isset($settings['full_descr']) AND $settings['full_descr']=='1' ) { 
        $full_news = stripslashes($this->Spr->GetNameByCod( TblModNewsSprFull, $row['id'], $this->lang_id, 0 ));
        $full_news = str_replace ( '&amp;', '&', $full_news ); 
        $full_news = str_replace ( '&#039;', '\'', $full_news ); 
        $full_news = str_replace ( '&quot;', '\"', $full_news ); 
        $arr0[$index_sort]["full_news"] = $full_news; 
        }
        if(empty($full_news)) $arr0[$index_sort]["full_news"] = $shrt_news;
        
        $arr0[$index_sort]["source"] = $row['source'];
            
        //-------- get all photos start --------- 
        $img_arr = $this->GetImagesToShow($row['id']);
        for ($ii=0;$ii<count($img_arr);$ii++){               
            $arr0[$index_sort]["img_arr"][$ii] = $img_arr[$ii];
            //$arr0[$index_sort]["img_arr"][$ii]['descr'] = $img_arr[$ii];
        }
        //-------- get all photos end ---------                 
        }   
            
        }//end for
        
        if (is_array($arr0)) {
        if ( $asc_desc == 'desc' ) krsort($arr0);
        else ksort($arr0);  
        reset($arr0);
        }
        // echo '<br>Arr:<br>'; print_r($arr0); echo '<br><br>';
        return $arr0;
    } //end of function CotvertDataToOutputArray()  




    // ================================================================================================
    // Function : GetValueOfFieldByNewsId()
    // Date : 06.01.2006
    // Returns :      true,false / Void
    // Description : 
    // Programmer :  Igor Trokhymchuk
    // ================================================================================================
    function GetValueOfFieldByNewsId( $news_id = NULL, $field = NULL )
    {
        $tmp_db = new DB();
        if ( empty($field) ) return false;

        $q = "select `".$field."` from ".TblModNews." where id='$news_id'";
        $res = $tmp_db->db_Query( $q );
        //echo '<br>'.$q.' $res='.$res.' $tmp_db->result='.$tmp_db->result; 
        if ( !$res OR !$tmp_db->result ) return false;
        $row = $tmp_db->db_FetchAssoc();
        $name = $row[$field];
        //echo '<br> $name='.$name;
        return $name;
    } //end of fuinction GetValueOfFieldByNewsId()
     

       // ================================================================================================
       // Function : GetNewsNameByNewsId()
       // Date : 06.04.2007
       // Returns :      true,false / Void
       // Description :  Return news title
       // Programmer :  Yaroslav Gyryn
       // ================================================================================================
      function GetNewsNameByNewsId( $news_id = NULL )
       {
         $tmp_db = new DB();

         $q = "select * from ".TblModNewsSprSbj." where `cod`='$news_id' and `lang_id`='".$this->lang_id."'";
         $res = $tmp_db->db_Query( $q );
        // echo '<br>'.$q.' $res='.$res.' $tmp_db->result='.$tmp_db->result; 
         if ( !$res OR !$tmp_db->result ) return false;
         $row = $tmp_db->db_FetchAssoc();
         $name = $row['name'];
        // echo '<br> name='.$name;
         return $name;
       } //end of fuinction GetUserNameByUserId()
           
    // ================================================================================================
    // Function : Link()
    // Date : 12.01.2011
    // Description : Return Link 
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function Link( $cat = NULL, $id = NULL, $str_news = NULL)
    {
        if(empty($this->settings))
            $this->settings = $this->GetSettings();

        /*if ( isset($this->settings['rewrite']) AND $this->settings['rewrite']=='0' ) { 
            if($cat!=NULL and $id==NULL) {
                return '/newscat_'.$cat.'.html';
            }
            if($id!=NULL){
                return '/news_'.$id.'.html';
            }
        }*/

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
            //$str_cat =  $this->Category->GetCategoryTranslitById($cat);
            $str_cat = $this->Spr->GetTranslitByCod( TblModNewsCat, $cat, $this->lang_id );
        }
        elseif(!empty($id)){
            //$str_cat =  $this->Category->GetCategoryTranslitById($this->GetIdCatByIdNews($id));
            $str_cat =  $this->Spr->GetTranslitByCod( TblModNewsCat, $this->GetIdCatByIdNews($id), $this->lang_id );
        }
        else{
            $str_cat = NULL; 
        }
    
        if(empty($str_news))
            $str_news = $this->GetLink($id);
        
        if($id!=NULL and $str_news=='') {
            $str_news = $this->SetLink($id, true);
        }
        
     
        if($id==null){
            if(!empty($str_cat)) $link = _LINK.'news/'.$str_cat.'/';
            else{
                if( $this->task=='showa') $link = _LINK.'news/last/';
                elseif( $this->task=='showall') $link = _LINK.'news/all/';
                elseif( $this->task=='arch') $link = _LINK.'news/arch/';
                else $link = _LINK.'news/';
            }
        }
        else  {
                $link = _LINK.'news/'.$str_cat.'/'.$str_news.'.html';
        }
        
        return $link;
    } // end of function Link

    // ================================================================================================
    // Function : SetLink()
    // Date : 12.01.2009
    // Parms : $link - str for link, $cod - id position
    // Description : Set Link 
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function SetLink($cod, $ret=false){
     $Crypt = new Crypt();
     $this->db = new DB();

    $cat_link = $this->Spr->GetNameByCod(TblModNewsSprSbj, $cod, 1, 1);   
    if ($cat_link=="") { 
        $cat_link = $this->Spr->GetNameByCod(TblModNewsSprSbj, $cod, $this->lang_id, 1);
    }
    
    $link = $Crypt->GetTranslitStr($cat_link).'-'.$cod;
    
    

    $q = "insert into `".TblModNewsLinks."` values(NULL,'".$cod."','$link')";
    $res = $this->db->db_Query( $q );
    if( !$res ) return false;
    if($ret) return $link;
    } // end of function SetLink


    // ================================================================================================
    // Function : GetLink()
    // Date : 12.01.2009
    // Description : Get link
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function GetLink($cod){
      $q = "select `link` from ".TblModNewsLinks." where 1 and `cod`='".$cod."'";
      $res = $this->db->db_Query( $q );
      $rows = $this->db->db_GetNumRows();
    //echo "<br> q=".$q." res=".$res." rows=".$rows;
      $row = $this->db->db_FetchAssoc();
      return $row['link']; 
    } // end of function GetLink


    // ================================================================================================
    // Function : GetIdCatByIdNews()
    // Date : 13.05.2007
    // Returns :
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function GetIdCatByIdNews($id){
      $tmp_db = new DB();
      $q = "select * from ".TblModNews." where 1 and `id`='$id'";
      $res = $tmp_db->db_Query( $q );
      $rows = $tmp_db->db_GetNumRows();
    //echo "<br> q=".$q." res=".$res." rows=".$rows;
      $row = $tmp_db->db_FetchAssoc();
    return $row['id_category']; 
    } // end of function GetIdCatByIdNews


    // ================================================================================================
    // Function : GetIdNewsByStrNews()
    // Date : 13.05.2007
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function GetIdNewsByStrNews($str_news){
      $tmp_db = new DB();  
      //$q = "select `cod` from ".TblModNewsLinks." where 1 and `link`='$str_news'";
      $q = "SELECT `".TblModNewsLinks."`.`cod` 
              FROM `".TblModNewsLinks."`, `".TblModNews."`, `".TblModNewsCat."`  
              WHERE BINARY `".TblModNewsLinks."`.`link` = BINARY '".$str_news."'
              AND `".TblModNewsLinks."`.cod=`".TblModNews."`.`id`
              AND `".TblModNews."`.`id_category`=`".TblModNewsCat."`.`cod`
              AND `".TblModNewsCat."`.`cod`='".$this->category."'
              AND `".TblModNewsCat."`.`lang_id`='".$this->lang_id."'
             ";
      $res = $tmp_db->db_Query( $q );
      $rows = $tmp_db->db_GetNumRows();
      //echo "<br>GetIdNewsByStrNews  q=".$q." res=".$res." rows=".$rows;
      $row = $tmp_db->db_FetchAssoc();
      return $row['cod']; 
    } // end of function GetIdNewsByStrNews



    //======================================= SubSribe START =================================================

    // ================================================================================================
    // Function : SubscrSave()
    // Date : 21.05.2007
    // Description : save subscribers
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function SubscrSave()
    {
        $q = "SELECT * FROM ".TblModNewsSubscr." WHERE `login`='".$this->subscriber."'";
        $res = $this->db->db_Query( $q );
        //echo "<br>11 q=".$q." res=".$res;  
        if( !$res ) return false;
        $rows = $this->db->db_GetNumRows();
        $date = date("Y-m-d");
        if( $rows>0 )   //--- update
        { 
            $row = $this->db->db_FetchAssoc(); 
            $q = "UPDATE `".TblModNewsSubscr."` SET
                  `login`='".$this->subscriber."',
                  `pass`='".$this->subscr_pass."'
                  WHERE `id`='".$row['id']."'
                 ";
            $id = $row['id'];
            $res = $this->db->db_Query( $q ); 
            if( !$res ) return false;
        }
        else          //--- insert
        {
            $q = "INSERT INTO `".TblModNewsSubscr."` SET
                  `login`='".$this->subscriber."',
                  `pass`='".$this->subscr_pass."',
                  `user_status`='0',
                  `is_send`='0',
                  `dt`='".$date."'
                 ";
            $res = $this->db->db_Query( $q ); 
            if( !$res ) return false;
        }

        if ( empty($id)) $id = $this->db->db_GetInsertID(); 

        $q="DELETE FROM `".TblModNewsSubscrCat."` WHERE `subscr_id`='".$id."'";
        $res = $this->db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $this->db->result='.$this->db->result;
        if (!$res OR !$this->db->result) return false;;
        
        if($this->categories=='all') $this->categories = $this->Spr->GetListName( TblModNewsCat, $this->lang_id, 'array', 'cod', 'asc', 'cod' ); 
        foreach($this->categories as $k=>$v){
            $q = "INSERT `".TblModNewsSubscrCat."` SET
                  `subscr_id`='".$id."',   
                  `cat_id`='".$v."'
                 ";
            $res = $this->db->db_Query( $q );
            //echo "<br>q=".$q." res=".$res;
            if( !$res ) return false;
        }
        return true;
    } // end of function  SubscrSave

    // ================================================================================================
    // Function : SubscrDel()
    // Date : 22.05.2007
    // Description : save subscribers
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function SubscrDel()
    {
        $tmp_db = new DB();
        $q = "DELETE 
                 FROM `".TblModNewsSubscr."`, `".TblModNewsSubscrCat."`
                 USING  `".TblModNewsSubscr."` INNER JOIN `".TblModNewsSubscrCat."`
                 WHERE `".TblModNewsSubscr."`.`login`='".$this->subscriber."'
                 AND `".TblModNewsSubscr."`.id=`".TblModNewsSubscrCat."`.subscr_id";
        $res = $tmp_db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
        if (!$res) return false;
        return true;
    }

    // ================================================================================================
    // Function : SaveManyValues()
    // Date : 15.11.2006
    // Returns : true,false / Void
    // Description : Store many data to the table for one user
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function SaveManyValues( $table, $id_user, $arr_val )
    {       
       $tmp_db = new DB();
       $q="DELETE FROM `".$table."` WHERE `cod`='$id_user'";
       $res = $tmp_db->db_Query($q);
      // echo '<br>q='.$q.' res='.$res.'<br>'.$tmp_db->result;
       if (!$tmp_db->result) return false;
      // echo '<br>count($arr_val='.count($arr_val);
       for( $i=0; $i<count($arr_val); $i++){
           //echo '<br>char='.$character[$i];
           $q="INSERT into `".$table."` values(NULL,'$id_user','".$arr_val[$i]."')";
           $res = $tmp_db->db_Query($q);
          // echo '<br>q='.$q.' res='.$res.'<br>';
           if (!$tmp_db->result) return false;
       }
       return true;
    } //end of fuinction SaveManyValues()   

    // ================================================================================================
    // Function : DelManyValues
    // Date : 17.11.2006
    // Parms :   $table - name of the table from which will gets data
    //           $id_user - id of the user
    // Returns : $arr - if values exist in the $table ; else - false
    // Description : remove array with values of one property of user
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function DelManyValues( $table, $id_user)
    {
        $tmp_db = new DB();
        $q="DELETE FROM `".$table."` WHERE `cod`='$id_user'";
        $res = $tmp_db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
        if (!$res OR !$tmp_db->result) return false;
        return true;
    } // ed of function DelManyValues()   

    // ================================================================================================
    // Function : SendHTML
    // Date : 22.05.2007
    // Returns : true,false / Void
    // Description : Send the registration mail with profile of the subscriber
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function SendHTML()
    {
     $info = "
      <H3>".$this->Msg->show_text('TXT_SUCCESSFULL_REG')." ".$_SERVER['SERVER_NAME']."</H3>
     <div>".$this->Msg->show_text('TXT_THANK_FOR_REG')."
     <br>".$this->Msg->show_text('TXT_SUPPORT_FOR_REG')."</div><br>
     <table border=0 cellspacing=1 cellpadding=2 align=center width='100%'>
     <tr><td colspan=2 align=left><b>".$this->Msg->show_text('TXT_REG_HEADER')."</b>
     <tr><td>".$this->Msg->show_text('_FLD_EMAIL', 'sys_spr_txt')." :
         ".$this->subscriber."
     <tr><td>".$this->Msg->show_text('TXT_ACTIVE_PAGE').": 
         <a href='http://".$_SERVER['SERVER_NAME']."/news/activate/".$this->subscriber."/'>http://".$_SERVER['SERVER_NAME']."/news/activate/</a></td>
     </tr>
     <tr><td colspan='2'>".$this->Msg->show_text('TXT_WRONG_ADDR')."</td>
     </tr>
    </table>
    ";

     //-------------Send to User ---------------
     $subject = $this->Msg->show_text('TXT_SUCCESSFULL_REG')." ".$_SERVER['SERVER_NAME'];
     $body = $info;
   //echo $body;
     $arr_emails[0]=$this->subscriber;
     $res = $this->SendSysEmail($subject, $body, $arr_emails);  
     
     if( !$res ) {return false;}
     return true;
    } //end of function SendHTML()



    // ================================================================================================
    // Function : SendSysEmail
    // Date : 18.01.2007
    // Parms :   $sbj        - subject of email
    //           $body       - body of email
    //           $arr_emails - array with emails whrere to send ($arr_emails[0]='iii@ii.i'
    //                                                           $arr_emails[1]='aaa@aa.a')
    // Returns : $res / Void
    // Description : Function for send emails 
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function SendSysEmail($sbj=NULL, $body=NULL, $arr_emails=NULL, $headers=NULL)
    {
        if( empty($sbj) ) $sbj=NULL;
        $body .= "
        <p>
        <a href='http://".$_SERVER['SERVER_NAME']."/news/deactivate/".$this->subscriber."/'>".$this->Msg->show_text('TXT_SUBSCR_DEL')."</a>
        </p>
        ";

        $mail = new Mail(); 
        for($i=0;$i<count($arr_emails);$i++){
         $mail->AddAddress($arr_emails[$i]);
        }

        $mail->WordWrap = 500;
        $mail->IsHTML( true );
        $mail->Subject = $sbj;
        $mail->Body = $body;

        $res = $mail->SendMail(); 
        // if(mail($this->subscriber, $sbj, $body, $headers)) return true;
        if(!$res) return false;
        else return true;
    } //End of function SendSysEmail() 

    
    // ================================================================================================
    // Function : ActivateUser()
    // Date : 11.02.2011
    // Returns : true,false / Void
    // Description : Set status of user as Activated
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function ActivateUser( $activate_user )
    {
        $q = "SELECT `".TblModNewsSubscr."`.user_status
                FROM `".TblModNewsSubscr."`
                WHERE `login` = '$activate_user'";
        $res = $this->db->db_Query( $q );
        //echo '<br>$q='.$q.' $res='.$res.' $this->db->result='.$this->db->result;
        $rows = $this->db->db_GetNumRows();
        if( !$rows or !$res) 
            $this->ShowTextMessages($this->multi['TXT_ACTIVE_FALSE']);
        else {
            $row = $this->db->db_FetchAssoc();
            if($row['user_status'] == 0) { // Еще не был активирован
                $q = "UPDATE `".TblModNewsSubscr."` 
                        SET `user_status`=1 
                        WHERE `login` = '$activate_user'";
                $res = $this->db->db_Query( $q );
                //echo '<br>$q='.$q.' $res='.$res.' $this->db->result='.$this->db->result;
                if( !$res) 
                    $this->ShowTextMessages($this->multi['TXT_ACTIVE_FALSE']);
                else
                    $this->ShowTextMessages($this->multi['TXT_ACTIVE_OK']);
            }
            else {
                    $this->ShowTextMessages($this->multi['TXT_ALREADY_ACTIVE']);
            }
        }
        return true;
    } //end of function ActivateUser()

    
    //======================================= SubSribe END ===================================================


    
    //--------------------------------------------------------------------------------------------------------
    //---------------------------- FUNCTION FOR SETTINGS OF NEWS START ---------------------------------------
    //--------------------------------------------------------------------------------------------------------

    // ================================================================================================
    // Function : GetSettings()
    // Date : 27.03.2006
    // Returns : true,false / Void
    // Description : return all settings of catalog
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetSettings($front = true )
    {       
        $q = "SELECT * from `".TblModNewsSet."` where 1";
        //echo '$q ='.$q;
        $res = $this->db->db_Query( $q );
        if( !$this->db->result ) return false;
        $row = $this->db->db_FetchAssoc();
        if($front) {
            $row['title'] =  $this->Spr->GetNameByCod( TblModNewsSetSprTitle, 1, $this->lang_id, 1 );
            $row['description'] = $this->Spr->GetNameByCod( TblModNewsSetSprDescription, 1, $this->lang_id, 1 );
            $row['keywords'] = $this->Spr->GetNameByCod( TblModNewsSetSprKeywords, 1, $this->lang_id, 1 );
        }
        return $row;         
    } // end of function GetSettings() 

    // ================================================================================================
    // Function : SetMetaData()
    // Date : 23.05.2007
    // Parms :
    //           $this->id  - id of news
    //           $this->category  - category of news 
    // Returns : true,false / Void
    // Description : set title, description and keywords for this module or for current news or category
    //               of news
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function SetMetaData($page)
    {
        // Установка через динамічні сторінки
        if(empty($this->id)) {
            if(!isset ($this->FrontendPages)) 
                $this->FrontendPages = &check_init('FrontendPages', 'FrontendPages');
            $this->FrontendPages->page_txt = $this->FrontendPages->GetPageTxt($page);
            
            $this->title = $this->FrontendPages->GetTitle();
            if(empty($this->title))
                $this->title = $this->Spr->GetNameByCod( TblModNewsSetSprTitle, 1, $this->lang_id, 1 );
            
            $this->description = $this->FrontendPages->GetDescription();
            if(empty($this->description))
                $this->description = $this->Spr->GetNameByCod( TblModNewsSetSprDescription, 1, $this->lang_id, 1 );
            
            $this->keywords = $this->FrontendPages->GetKeywords();
            if(empty($this->keywords))
                $this->keywords = $this->Spr->GetNameByCod( TblModNewsSetSprKeywords, 1, $this->lang_id, 1 );
            return;
        }
    
        $this->title = $this->Spr->GetNameByCod( TblModNewsSetSprTitle, 1, $this->lang_id, 1 );
        $this->description = $this->Spr->GetNameByCod( TblModNewsSetSprDescription, 1, $this->lang_id, 1 );
        $this->keywords = $this->Spr->GetNameByCod( TblModNewsSetSprKeywords, 1, $this->lang_id, 1 );

        if( $this->id ) $title = $this->Spr->GetNameByCod( TblModNewsSprSbj, $this->id, $this->lang_id, 1);
        else {
            if( $this->category ){
                $metadata = $this->Spr->GetMetaDataByCod(TblModNewsCat, $this->category, $this->lang_id );
                if( !empty($metadata['mtitle']) ) $title = stripslashes($metadata['mtitle']);
                else {
                    $title = $this->Spr->GetNameByCod( TblModNewsCat, $this->category, $this->lang_id, 1);
                    if( !empty($this->title) ) $title = $title.' | '.$this->title;
                }
            }
            else $title=NULL;
        }
        //echo '<br>$this->title='.$this->title.'<br>$title='.$title;
        if( empty($title) ){
            // echo "<br>task=".$this->task;
            switch($this->task){
                case 'showall':  $title = $this->Msg->show_text('TXT_META_TITLE_ALL');
                        break;
                case 'showa':  $title = $this->Msg->show_text('TXT_META_TITLE_LAST');
                        break;
                case 'arch':  $title = $this->Msg->show_text('TXT_META_TITLE_ARCH');
                        break;
                case 'new_subscriber':  $title = $this->Msg->show_text('TXT_SUBSCRIBE');
                        break;
            }
            if( !empty($this->title) ) $title = $title.' | '.$this->title;
        }
        $this->title = $title;
        
            
        if( $this->id ) {
            $descr = $this->Spr->GetNameByCod( TblModNewsSprDescription, $this->id, $this->lang_id, 1);
            if( !empty($descr) AND !empty($this->description) ) $this->description = $descr.'. '.$this->description;
            else {
                if( !empty($descr) ) $this->description = $descr;
                elseif( !empty($this->title) ) $this->description = $this->title.'. '.$this->description;
            }
        }
        else {
            if( $this->category ){
                if( !empty($metadata['mdescr']) ) $this->description = stripslashes($metadata['mdescr']);
                else {
                    $descr = $this->Spr->GetNameByCod( TblModNewsCat, $this->category, $this->lang_id, 1);
                    if( !empty($descr) AND !empty($this->description) ) $this->description = $descr.'. '.$this->description;
                    else {
                        if( !empty($descr) ) $this->description = $descr;
                        elseif( !empty($this->title) ) $this->description = $this->title.'. '.$this->description;
                    }
                }
            }
        }


        if( $this->id ){
            $keywrds = $this->Spr->GetNameByCod( TblModNewsSprKeywords, $this->id, $this->lang_id, 1);
            if( !empty($keywrds) AND !empty($this->keywords) ) $this->keywords = $keywrds.', '.$this->keywords;
            elseif( !empty($keywrds) ) $this->keywords = $keywrds;
        }
        else {
            if( $this->category ) {
                if( !empty($metadata['mkeywords']) ) $this->keywords = stripslashes($metadata['mkeywords']);
                else {
                    $keywrds = $this->Spr->GetNameByCod( TblModNewsCat, $this->category, $this->lang_id, 1);
                    if( !empty($keywrds) AND !empty($this->keywords) ) $this->keywords = $keywrds.', '.$this->keywords;
                    elseif( !empty($keywrds) ) $this->keywords = $keywrds; 
                }
            }
        }

        if( !empty($keywrds) ) $this->keywords = $keywrds.', '.$this->keywords;
        //else $this->keywords = $title .', '.$this->keywords; 

    } //end of function  SetMetaData() 

    //------------------------------------------------------------------------------------------------------------
    //---------------------------- FUNCTION FOR SETTINGS OF NEWS  END --------------------------------------------
    //------------------------------------------------------------------------------------------------------------
    
    
    
    //------------------------------------------------------------------------------------------------------------
    //----------------------------------- FUNCTION FOR RSS START -------------------------------------------------
    //------------------------------------------------------------------------------------------------------------
    
    // ================================================================================================
    // Function : GetNewsForRSS()
    // Date :    17.06.2011
    // Returns : true/false
    // Description : Get News for Rss
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function GetNewsForRSS( $idNews=null, $idModule = 83)
    {
        $q = "SELECT 
                `".TblModNews."`.*,
                `".TblModNewsSprSbj."`.name as sbj,
                `".TblModNewsSprShrt."`.name as short,
                `".TblModNewsSprFull."`.name as full
            FROM                                             
                `".TblModNews."`, `".TblModNewsSprSbj."`,`".TblModNewsSprShrt."`, `".TblModNewsSprFull."`
            WHERE  
                `".TblModNews."`.status = 'a'
                AND
                `".TblModNews."`.id = `".TblModNewsSprSbj."`.cod
                AND
                `".TblModNewsSprSbj."`.lang_id = '"._LANG_ID."'
                AND
                `".TblModNews."`.id = `".TblModNewsSprShrt."`.cod
                AND
                `".TblModNewsSprShrt."`.lang_id = '"._LANG_ID."'
                AND
                `".TblModNews."`.id = `".TblModNewsSprFull."`.cod
                AND
                `".TblModNewsSprFull."`.lang_id = '"._LANG_ID."'
                AND  
                `".TblModNews."`.id  IN (".$idNews.")
            ORDER BY 
                `".TblModNews."`.id desc
      ";
        $res = $this->db->db_Query($q);
        $rows = $this->db->db_GetNumRows($res);
        $array = array();
         for( $i = 0; $i <$rows; $i++ ){
             $row = $this->db->db_FetchAssoc();
             $array[$row['id']] = $row;  
             $array[$row['id']]['module'] = $idModule;
             $array[$row['id']]['link'] = $this->Link($row['id_category'],$row['id']);
         }
         return $array;
    }
    // ================================================================================================
    // Function : GenerateRSSNews()
    // Date :    17.06.2011
    // Returns : true/false
    // Description : Generate Rss Feed
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function GenerateRSSNews()
    {
        if(empty($this->Crypt)) $this->Crypt = &check_init('Crypt', 'Crypt');
        if(empty($this->Article)) $this->Article = &check_init('Article', 'Article');
        
        $settings = $this->GetSettings();  
        if ( isset($settings['rss']) AND $settings['rss']=='0' ) {
            if (file_exists(SITE_PATH."/rss/news/export.xml")) $res = unlink (SITE_PATH."/rss/news/export.xml");
            return true;
        }
        
        // Массив всех новостей ID отсортированных по дате по убыванию
        $arrNewsId = $this->GetAllNewsIdRSS(72, 25);
        // Массив всех статтей ID отсортированных по дате по убыванию
        $arrArticlesId= $this->Article->GetAllArticlesIdRSS( 83, 25);
        
        // Обьединенный массив всех ID категорий
       $outputArrId = $arrNewsId + $arrArticlesId;
       
       // Количество всех ID 
       $outputCount  = count($outputArrId);
       
       //Сортировка по убыванию всех значений
       krsort($outputArrId);
       
       // Массив данных где ключом задается ID модуля, а значение - список ID элементов через заяптую.
       $data = array();
       foreach($outputArrId as $k=>$v) {
           if(!isset($data[$v['id_module']]))
                $data[$v['id_module']] = $v['id'];
           else
                $data[$v['id_module']] = $data[$v['id_module']].', '.$v['id'];
       }
        
       $keys = array_keys($data);
       $dataCount  = count($data);
       
       for($i=0; $i<$dataCount; $i++) {
           switch($keys[$i]) {
               case '72': 
                        //echo '<br/>News';
                        $arrNews = $this->GetNewsForRSS($data[$keys[$i]], $keys[$i]);
                        break;
               
               case '83': 
                        //echo '<br/>Article';
                        $arrArticles = $this->Article->GetArticlesForRSS($data[$keys[$i]], $keys[$i]);
                        break;
               
               /*case '153': 
                        //echo '<br/>Video';
                        $arrVideos = $this->Video->GetVideosForTags($data[$keys[$i]], $keys[$i]);
                        break;

               case '156': 
                        //echo '<br/>Gallery';
                        $arrGallery = $this->Gallery->GetGalleryForTags($data[$keys[$i]], $keys[$i]);
                        break;*/
          }
       }
       
       // Формування змішаного масиву
       $outputArray = array();
       
       foreach($outputArrId as $k=>$v) { 
           //echo '<br/> '.$v['id_module'];
           switch($v['id_module']) {
              case '72': 
                        //echo '<br/>News';
                        if(isset($arrNews[$v['id']]))
                            $outputArray[] = $arrNews[$v['id']];
                        break;
               
               case '83': 
                        //echo '<br/>Article';
                        if(isset($arrArticles[$v['id']]))
                            $outputArray[] = $arrArticles[$v['id']];
                        break;
               
               /*case '153': 
                        //echo '<br/>Video';
                        if(isset($arrVideos[$v['id']]))
                            $outputArray[] = $arrVideos[$v['id']];
                        break;

               case '156': 
                        //echo '<br/>Gallery';
                        if(isset($arrGallery[$v['id']]))
                            $outputArray[] = $arrGallery[$v['id']];
                        break;*/
          }
       }
       
       $outputArrayCount  = count($outputArray);
        
        $data = '<?xml version="1.0" encoding="utf-8" ?>
        <rss version="2.0">
         <channel>
          <image>
           <url>http://1.zt.ua/images/design/logo_rss.gif</url>
           <title>Перший Житомирський інформаційний портал 1.zt.ua</title>
           <link>http://1.zt.ua/</link>
          </image>
          <title>Перший Житомирський інформаційний портал 1.zt.ua</title>
          <link>http://1.zt.ua/</link>
          <description>Перший Житомирський інформаційний портал 1.zt.ua</description>
        ';
        for($i = 0; $i<$outputArrayCount; $i++){
            $row = $outputArray[$i];
            if($row['module'] == 72)
                $img_path = $this->GetMainImage($row['id'], 'front');
            else {
                $main_img_data = $this->Article->GetMainImageData($row['id'], 'front');
                $img_path = $main_img_data['path'];
            }
            if(!empty($img_path)) {
                if($row['module'] == 72)
                    $enclosure = 'http://1.zt.ua/images/mod_news/'.$row['id'].'/'.$img_path;
                elseif($row['module'] == 83)
                    $enclosure = 'http://1.zt.ua/images/mod_article/'.$row['id'].'/'.$img_path;
                    else $enclosure = NULL;
            }
            else
                $enclosure = NULL;
            
            $sbj = strip_tags(htmlspecialchars(stripslashes($row['sbj'])));
            $link = $row['link'];
            $short = htmlspecialchars(strip_tags(stripslashes($row['short'] )));
            $category = $this->Category->GetCategoryNameById($row['id_category']);
            $full = trim(htmlspecialchars(strip_tags(stripslashes($row['full']))));
            $full = $this->Crypt->TruncateStr($full,1000);
            $date = date("D, d M Y H:i:s", strtotime($row['start_date']));
            
            $data = $data.'
            <item>
             <title>'.$sbj.'</title>
             <link>http://1.zt.ua'.$link.'</link>
             <description>'.$short.'</description>
             <author>http://1.zt.ua</author>
             <category>'.$category.'</category>
             <enclosure url="'.$enclosure.'" type="image/jpeg"></enclosure>
             <pubDate>'.$date.' +0300'.'</pubDate>
             <fulltext>'.$full.'</fulltext>
            </item>
            ';
        }

        $data = $data.'</channel></rss>';
        //$_tmp_time = filemtime("export.xml")+43200;
        $hhh = fopen(SITE_PATH."/rss/export.xml", "w");
        fwrite($hhh, $data);
        fclose($hhh);
    } //end of function GenerateRSSNews()

    // ================================================================================================
    // Function : ReadRss()
    // Date :    14.09.2009
    // Parms :   $url - url of rss chanel
    // Returns : true/false
    // Description : read rss news 
    // Programmer : Ihor Trokhymchuk
    // ================================================================================================
    function ReadRss()
    {
        include_once($_SERVER['DOCUMENT_ROOT'].'/modules/mod_rss/rss_fetch.inc');
        $ss = 0;
        $insert = 0;
        $q="select * from `".TblModNewsRss."` where 1 and `status`='1'";
        $res = $this->db->db_Query( $q);
        //echo "<br> q = ".$q." res = ".$res;
        if( !$this->db->result ) return false;
        $rows = $this->db->db_GetNumRows();
        if($rows>0){
            for($i = 0; $i<$rows; $i++){
                $row = $this->db->db_FetchAssoc($res);
                $descr_rss = $this->Spr->GetNameByCod( TblModNewsRssSprDescr, $row['id'], $this->lang_id );
                //echo "<br /> row['path'] = ".$row['path'];
                //echo "<br /> descr_rss = ".$descr_rss;
                
                $rss = fetch_rss($row['path']);
                //print_r($rss);
                //echo "<br />";
                foreach($rss as $key=>$val){
                    //echo "<br / > key = ".$key;
                    //echo "<br / > val = ".$val;
                    if(is_array($val) and count($val)>0){
                        foreach($val as $k=>$v){
                            //echo "<br / > k = ".$k;
                            //echo "<br / > v = ".$v;
                            $cn = count($v);
                            $j=0;
                            if(is_array($v) and $cn>0){
                                foreach($v as $k1=>$v1){
                                    if($k1=='title') {
                                        if($this->CheckIfNewsExist($v1)) continue;
                                        else{
                                            $this->subj_[$this->lang_id] = $v1;
                                            $insert=1;
                                        }
                                    }
                                    /*
                                    *form data of news for insert to db
                                    */
                                    if($k1=='category') {
                                        $this->category = $v1;
                                    }
                            
                                    //echo "<br / > k1 = ".$k1;
                                    //echo "<br / > v1 = ".$v1;
                                    if($k1=='description') {
                                        $this->short_[$this->lang_id] = $v1;
                                    }
                                
                                    if($k1=='fulltext') {
                                        $this->full_[$this->lang_id] = $v1;
                                    }

                                    if($k1=='link') {
                                        $this->source = $v1;
                                    }
                                
                                    if($k1=='date_timestamp') {
                                        $this->date_timestamp = $v1;
                                    }
                                    $j++;
                            
                                    if($insert==1 and $cn==($j)){    
                                        if(strlen($this->short_[$this->lang_id])>100){
                                        if($this->SaveRssData()) $ss++;
                                        $insert = 0;
                                        $this->category = '';
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        echo "<br />".$this->Msg->show_text('MSG_RSS_IMPORTED_OK')." ".$ss;
    } // end of function ReadRss



    // ================================================================================================
    // Function : SaveRssData()
    // Date : 14.09.2009
    // Returns : true,false / Void
    // Description : Store data to the table
    // Programmer : Ihor Trokhymchuk
    // ================================================================================================
    function SaveRssData()
    {
        $ln_sys = new SysLang();

        $id_relart = NULL;
        if(trim($this->category)=='') $this->category = "Другие";

        $q = "select * from `".TblModNewsCat."` where 1 and `name`='".$this->category."' and `lang_id`='".$this->lang_id."'";
        $res = $this->db->db_Query($q);
        //echo "<br /> q SEL CAT = ".$q." res = ".$res;
        if( !$res ) return false;
        $rows = $this->db->db_GetNumRows();
        if($rows>0) {
            $row = $this->db->db_FetchAssoc();
            $this->id_category = $row['cod'];
        }
        else{
            $this->id_category = $this->GetMaxValueOfField(TblModNewsCat, 'cod')+1;
            $move = $this->GetMaxValueOfField(TblModNewsCat, 'move')+1;
            $q = "insert into `".TblModNewsCat."` values(NULL, '".$this->id_category."', '".$this->lang_id."', '".$this->category."','".$move."', '', '')";
            $res = $this->db->db_Query( $q );
            //echo "<br /> q = ".$q." res = ".$res;
            if( !$res ) return false;
            //$this->id_category =  $this->db->db_GetInsertID();
        }
     
        //echo "<br /> id_cat = ".$this->id_category ;

        if (empty($this->id_category)) {
            //$this->Msg->show_msg('NEWS_CATEGORY_EMPTY');
            echo "<br />".$this->Msg->show_text('MSG_RSS_IMPORT_NO_CATEGORY_FOR_NEWS').' <u>'.$this->subj_[$this->lang_id].'</u>';
        }
        if (empty( $this->subj_[$this->lang_id] )) {
           // $this->Msg->show_msg('NEWS_SUBJECT_EMPTY');
        }
        if (empty( $this->short_[$this->lang_id] )) {
          //  $this->Msg->show_msg('NEWS_SHORT_EMPTY');
        }

        //3600*24 - 1 day
     
        $start_d = date("Y-m-d H:i:s", $this->date_timestamp);
        $end_d = date("Y-m-d H:i:s", $this->date_timestamp+(3600*24*7)); 


        $display = $this->GetMaxValueOfField(TblModNews, 'display')+1;

        $q = "insert into `".TblModNews."` values(NULL,'".$this->id_category."','".$this->id_relart."','a','".$start_d."','".$end_d."','".$display."', '".$this->source."')";
        $res = $this->Right->Query( $q, $this->user_id, $this->module );
        //echo "<br /> q = ".$q." res = ".$res;
        if( !$res ) return false;

        $id =  $this->db->db_GetInsertID();
        $this->id = $id;
        //else return true;

        $id = $this->id;

        $subject = addslashes(trim($this->subj_[$this->lang_id]));
        $keywords = '';
        $description = '';
        $short = addslashes($this->br2p($this->nltobr(trim($this->short_[$this->lang_id]))));
        $full = addslashes($this->br2p($this->nltobr(trim($this->full_[$this->lang_id ]))));
        if(strlen($full)<10) $full = $short;
        //echo "<br /> short = ".$short;
        $tmp = explode("<br>",$short);
        $n = count($tmp);
        //echo "<br /> n= ".$n;
        // echo   strip_tags(stripslashes(stripslashes( $"<br> tmp = ";
        // print_r($tmp);
        if($n>2){
            if(strlen($tmp[0])>150) $short = $tmp[0]."</p>";
            else $short = $tmp[0]."</p><p>".$tmp[1]."</p>";
        }
      
        //echo "<br /> short = ".$short;
        $full = strip_tags($full, "<p><img><br><strong><u><i><b><ul><li><table><tr><td>");
      

        $res = $this->Spr->SaveToSpr( TblModNewsSprSbj, $id, $this->lang_id, $subject );
        if( !$res ) return false;
       
        $res = $this->Spr->SaveToSpr( TblModNewsSprKeywords, $id, $this->lang_id, $keywords );
        if( !$res ) return false;
       
        $res = $this->Spr->SaveToSpr( TblModNewsSprDescription, $id, $this->lang_id, $description );
        if( !$res ) return false;

        $res = $this->Spr->SaveToSpr( TblModNewsSprShrt, $id, $this->lang_id, $short );
        if( !$res ) return false;

        $res = $this->Spr->SaveToSpr( TblModNewsSprFull, $id, $this->lang_id, $full );
        if( !$res ) return false;

        $l_link = $this->Link($this->id_category, $id);
      
        $res = $this->SavePicture();
        // if( !$res ) return false;
        // $res = $this->GenerateRSSNews();  
     
        return true;
    } // end of function SaveRssData


    // ================================================================================================
    // Function : nltobr()
    // Date : 14.09.2009
    // Parms :  $var, $xhtml
    // Returns : true,false / Void
    // Programmer : Ihor Trokhymchuk
    // ================================================================================================
    function nltobr($var, $xhtml = FALSE){
        if($var){
            if($xhtml == FALSE){
                $array = array("\r\n", "\n\r", "\n", "\r");
                $var = str_replace($array, "<br>", $var);
                return $var;
            }
            else{
                $array = array("\r\n", "\n\r", "\n", "\r");
                $var = str_replace($array, "<br />", $var);
                return $var;
            }
        }
        else{
            return FALSE;
        }
    }//end of function nltobr()


    // ================================================================================================
    // Function : br2p()
    // Date : 14.09.2009
    // Parms :  $string
    // Returns : true,false / Void
    // Description :
    // Programmer : Ihor Trokhymchuk
    // ================================================================================================
    function br2p($string)
    {
      return preg_replace('#<p>[\n\r\s]*?</p>#m', '', '<p>'.preg_replace('#(<br\s*?/?>){2,}#m', '</p><p>', $string).'</p>');
    }//end of function br2p()

    //------------------------------------------------------------------------------------------------------------
    //----------------------------------- FUNCTION FOR RSS END ---------------------------------------------------
    //------------------------------------------------------------------------------------------------------------
    
    

    // ================================================================================================
    // Function : GetMaxValueOfField
    // Date : 19.05.2006
    // Parms :  $table  - name of the table  
    // Returns : value
    // Description : return the biggest value
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetMaxValueOfField( $table = TblModNews, $field='move' )
    {
        $tmp_db = new DB();
        
        $q = "SELECT `".$field."` FROM `".$table."` WHERE 1  ORDER BY `".$field."` desc LIMIT 1";
        $res = $tmp_db->db_Query( $q );
        //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;  
        if( !$res OR !$tmp_db->result ) return false;
        //$rows = $tmp_db->db_GetNumRows();
        $row = $tmp_db->db_FetchAssoc();
        return $row[$field];
    } // end of function GetMaxValueOfField(); 

    // ================================================================================================
    //    Function          : CheckIfNewsExist
    //    Date              : 21.03.2006
    //    Parms             : $prod_name 
    //    Returns           : Error Indicator
    //    Description       : Check If News Exist
    // ================================================================================================
    function CheckIfNewsExist($prod_name) {
      $q = "select * from `".TblModNewsSprSbj."` where 1 and `name` LIKE '%".$prod_name."%'";
      $res = $this->Right->Query($q, $this->user_id, $this->module);
      $rows = $this->Right->db_GetNumRows();
      //echo "<br> q = ".$q." res = ".$res." rows = ".$rows;
      if($rows==0){
        $tmp = explode(" ", $prod_name);
       // print_r($tmp);
        
         $str = $this->GetStrForSearch($tmp, 1); 
          //echo "<br> str = ".$str; 
         
         $q = "select * from `".TblModNewsSprSbj."` where 1 and `name` LIKE '%".$str."%' and `lang_id`='3'";
         $res = $this->Right->Query($q, $this->user_id, $this->module);
         $rows = $this->Right->db_GetNumRows();
       //  echo "<br> q = ".$q." res = ".$res." rows = ".$rows;
         if($rows!=1){ 
            $str = $this->GetStrForSearch($tmp, 2); 
            //  echo "<br> str = ".$str; 
              if($str=='') return false; 
             $q = "select * from `".TblModNewsSprSbj."` where 1 and `name` LIKE '%".$str."%' and `lang_id`='3'";
             $res = $this->Right->Query($q, $this->user_id, $this->module);
             $rows = $this->Right->db_GetNumRows();
             //echo "<br> q = ".$q." res = ".$res." rows = ".$rows; 
              if($rows!=1){ 
              $str = $this->GetStrForSearch($tmp, 3); 
           //   echo "<br> str = ".$str; 
              if($str=='') return false; 
             $q = "select * from `".TblModNewsSprSbj."` where 1 and `name` LIKE '%".$str."%' and `lang_id`='3'";
             $res = $this->Right->Query($q, $this->user_id, $this->module);
             $rows = $this->Right->db_GetNumRows();
             //echo "<br> q = ".$q." res = ".$res." rows = ".$rows;
             
                if($rows!=1){ 
                  $str = $this->GetStrForSearch($tmp, 4); 
              //    echo "<br> str = ".$str; 
                 if($str=='') return false;
                 $q = "select * from `".TblModNewsSprSbj."` where 1 and `name` LIKE '%".$str."%' and `lang_id`='3'";
                 $res = $this->Right->Query($q, $this->user_id, $this->module);
                 $rows = $this->Right->db_GetNumRows();
                // echo "<br> q = ".$q." res = ".$res." rows = ".$rows; 
                  } 
              }
         }
      }
      $row = $this->Right->db_FetchAssoc();
      if($rows==1)
      {
        return $row['cod'];
      }
      else return false;
    } // end of function  CheckIfNewsExist

    // ================================================================================================
    // Function : GetStrForSearch()
    // Date : 19.02.2008 
    // Returns : true,false / file
    // Description : build search string
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function GetStrForSearch($mas, $start=0)
    {
     $str = '';
     $count = sizeof($mas);   
     for($i=$start;$i<$count;$i++){
           $str .= $mas[$i]." ";
         }
         $str = trim($str);
      return $str;   
    } // end of function GetStrForSearch

    // ================================================================================================
    // Function : build_str_like
    // Date : 19.01.2005
    // Parms : $find_field_name - name of the field by which we want to do search
    //         $field_value - value of the field
    // Returns : str_like_filter - builded string with special format;
    // Description : create the string for SQL-command SELECT for search in the text field by any word
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function build_str_like($find_field_name, $field_value){
            $str_like_filter=NULL;
            // cut unnormal symbols
            $field_value=preg_replace("/[^\w\x7F-\xFF\s]/", " ", $field_value);
            // delete double spacebars
            $field_value=str_replace(" +", " ", $field_value);
            $wordmas=explode(" ", $field_value);

            for ($i=0; $i<count($wordmas); $i++){
                  $wordmas[$i] = trim($wordmas[$i]);
                  if (EMPTY($wordmas[$i])) continue;
                  if (!EMPTY($str_like_filter)) $str_like_filter=$str_like_filter." OR ".$find_field_name." LIKE '%".$wordmas[$i]."%'";
                  else $str_like_filter=$find_field_name." LIKE '%".$wordmas[$i]."%'";
            }
            if ($i>1) $str_like_filter="(".$str_like_filter.")";
            //echo '<br>$str_like_filter='.$str_like_filter;
     return $str_like_filter;
    } //end of function build_str_like()

    // ================================================================================================
    // Function : QuickSearch()
    // Date : 27.03.2008
    // Parms :  $search_keywords
    // Returns : true,false / Void
    // Programmer : Yaroslav Gyryn
    // ================================================================================================    
    function QuickSearch($search_keywords)
    {
        $tmp_db = new DB();
        $search_keywords = stripslashes($search_keywords);
   
        $sel_table = NULL;
        $str_like = NULL;
        $filter_cr = ' OR ';

        $str_like = $this->build_str_like(TblModNewsSprSbj.'.name', $search_keywords);
        $str_like .= $filter_cr.$this->build_str_like(TblModNewsSprShrt.'.name', $search_keywords);
        $str_like .= $filter_cr.$this->build_str_like(TblModNewsSprFull.'.name', $search_keywords); 
        $sel_table = "`".TblModNews."`, `".TblModNewsCat."`, `".TblModNewsSprSbj."`, `".TblModNewsSprShrt."`, `".TblModNewsSprFull."` ";
   
        $q ="SELECT `".TblModNews."`.id, `".TblModNews."`.id_category, `".TblModNews."`.status, `".TblModNews."`.display, `".TblModNewsSprSbj."`.`name` AS `news_name` 
             FROM ".$sel_table."
             WHERE (".$str_like.")
             AND `".TblModNewsSprSbj."`.lang_id = '".$this->lang_id."'
             AND `".TblModNews."`.id = `".TblModNewsSprSbj."`.cod
             AND `".TblModNewsSprShrt."`.lang_id = '".$this->lang_id."'
             AND `".TblModNews."`.id = `".TblModNewsSprShrt."`.cod
             AND `".TblModNewsSprFull."`.lang_id = '".$this->lang_id."'
             AND `".TblModNews."`.id = `".TblModNewsSprFull."`.cod
             AND `".TblModNews."`.`id_category` = `".TblModNewsCat."`.`cod`
             AND `".TblModNewsCat."`.lang_id = '".$this->lang_id."' 
             ORDER BY `".TblModNewsCat."`.`move` asc, `".TblModNews."`.`display` asc
            ";

        $res = $this->db->db_Query( $q );
        //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$this->db->result;
        if ( !$res) return false;
        if( !$this->db->result ) return false;  
        $rows = $this->db->db_GetNumRows();
        //echo "<br> rows = ";
        //print_r($rows);
        return $rows;
   } // end of function QuickSearch

     // ================================================================================================
     // Function : GetTopNews()
     // Date : 11.09.2009
     // Returns :      true,false / Void
     // Description :  Get Top News()  
     // Programmer : Yaroslav Gyryn
     // ================================================================================================
     function GetTopNews($limit = 5)   {
        $q = "SELECT 
            `".TblModNews."`.id,
            `".TblModNews."`.start_date,
            `".TblModNews."`.id_category,
            `".TblModNews."`.top_main,
            `".TblModNewsTop."`.name,
            `".TblModNewsTop."`.short,
            `".TblModNewsTop."`.image,
            `".TblModNewsLinks."`.link
            FROM 
                `".TblModNews."`, `".TblModNewsTop."`, `".TblModNewsLinks."`
            WHERE
                `".TblModNews."`.id = `".TblModNewsTop."`.cod   and
                `".TblModNews."`.id = `".TblModNewsLinks."`.cod and
                `".TblModNews."`.top = '1'  and
                `".TblModNewsTop."`.lang_id='".$this->lang_id."' and  
                `".TblModNews."`.status='a'
            ORDER BY 
                `".TblModNews."`.top_main, `".TblModNews."`.start_date  DESC
            LIMIT ".$limit;    

        $res = $this->db->db_Query($q);
        //echo "<br> ".$q." <br/> res = ".$res;
        $rows = $this->db->db_GetNumRows($res);
        $arrNews = array();
        for( $i=0; $i<$rows; $i++ ) {
            $arrNews[] = $this->db->db_FetchAssoc($res);
        }
        for( $i=0; $i<$rows; $i++ ) {
            $arrNews[$i]['link'] = $this->Link($arrNews[$i]['id_category'], $arrNews[$i]['id'], $arrNews[$i]['link']);
            $arrNews[$i]['type'] = 'news';
        }
        return $arrNews;
     }
     
     
     
     // ================================================================================================
     // Function : GetNewsNameLinkForId()
     // Date : 11.09.2009
     // Returns :      true,false / Void
     // Description :  Get News Name Link For Id()  
     // Programmer : Yaroslav Gyryn
     // ================================================================================================
     function GetNewsNameLinkForId($str = null) {
          $q = "SELECT 
                `".TblModNews."`.id,
                `".TblModNews."`.id_category,
                `".TblModNewsLinks."`.link,
                `".TblModNewsSprSbj."`.name
            FROM 
                `".TblModNews."`, `".TblModNewsLinks."`, `".TblModNewsSprSbj."`
            WHERE 
                `".TblModNewsSprSbj."`.cod = `".TblModNews."`.id
            AND
                `".TblModNewsLinks."`.cod = `".TblModNews."`.id
            AND 
                `".TblModNewsSprSbj."`.lang_id='".$this->lang_id."'
            AND
                `".TblModNews."`.id in (".$str.")
            ";
            $res = $this->db->db_Query( $q );
            //echo "<br> ".$q." <br/> res = ".$res;
            $rows = $this->db->db_GetNumRows($res);
            
            $arrNews = array();
            for( $i=0; $i<$rows; $i++ ) {
                $row = $this->db->db_FetchAssoc($res);
                $id = $row['id'];
                if(!isset($arrNews[$id])) {
                    $arrNews[$id]['name'] = $row['name'];
                    $arrNews[$id]['link'] = $this->Link($row['id_category'], $id, $row['link']);
                }
            }
            return  $arrNews;
     }
     
     // ================================================================================================
     // Function : GetRelatProdToNews()
     // Version : 1.0.0
     // Date : 28.11.2008
     // Parms : $id_news - id of the news
     // Returns :      true,false / Void
     // Description :  get realit products tho current news
     // ================================================================================================
     // Programmer :  Igor Trokhymchuk
     // Date : 28.11.2008
     // Reason for change : Creation
     // Change Request Nbr:
     // ================================================================================================
     function GetRelatProdToNews($id_news)
     {
        $db1 = new DB();
        $arr = array();
        $q = "SELECT * FROM `".TblModNewsRelatProd."` WHERE `id_news`='".$id_news."' ORDER BY `id` asc";
        $res = $db1->db_Query($q);
        //echo '<br>$q='.$q.' $res='.$res.' $db1->result='.$db1->result;
        if( !$res OR ! $db1->result) return false;
        $rows = $db1->db_GetNumRows();
        for($i=0;$i<$rows;$i++){
            $tmp_row = $db1->db_FetchAssoc();
            $arr[$i] = $tmp_row['id_prod'];
        }
        //print_r($arr);
        return $arr; 
     }//end of GetRelatProdToNews     
     
} //--- end of class News
