<?php
// ================================================================================================
// System : SEOCMS
// Module : FrontSpr.class.php
// Version : 1.0.0
// Date : 01.09.2007
// Licensed To:
// Igor Trokhymchuk ihoru@mail.ru
// Andriy Lykhodid las_zt@mail.ru
//
// Purpose : Class definition for system functions with layout of reference-books on front-end
//
// ================================================================================================

include_once( SITE_PATH.'/admin/include/defines.inc.php' );  

// ================================================================================================
//    Class             : FrontSpr
//    Version           : 1.0.0
//    Date              : 01.09.2007
//
//    Constructor       : Yes
//    Parms             : session_id / session id
//                        usre_id    / UserID
//                        user_      /
//                        user_type  / id of group of user
//    Returns           : None
//    Description       : Class definition for system functions with layout of reference-books on front-end 
// ================================================================================================
//    Programmer        :  Igor Trokhymchuk
//    Date              :  01.09.2007
//    Reason for change :  Creation
//    Change Request Nbr:  N/A
// ================================================================================================
 class FrontSpr extends SysSpr {

       // ================================================================================================
       //    Function          : FrontSpr (Constructor)
       //    Version           : 1.0.0
       //    Date              : 01.09.2007
       //    Parms             : usre_id   / User ID
       //                        module    / module ID
       //                        sort      / field by whith data will be sorted
       //                        display   / count of records for show
       //                        start     / first records for show
       //                        width     / width of the table in with all data show
       //                        spr       / name of the table for this module
       //    Returns           : Error Indicator
       //
       //    Description       : Opens and selects a dabase
       // ================================================================================================
       function FrontSpr() {
                if ( defined("_LANG_ID") ) $this->lang_id = _LANG_ID;
                
                if( defined("AJAX_RELOAD") AND AJAX_RELOAD==1){
                    $this->make_encoding = 1;
                    $this->encoding_from = 'utf-8';
                    $this->encoding_to = 'utf-8';        
                }                 
                
                if (empty($this->Msg)) $this->Msg = new ShowMsg();
                if (empty($this->Form)) $this->Form = new FrontForm('');
                
                
       } // End of FrontSpr Constructor
       
       // ================================================================================================
       // Function : ShowInComboBox
       // Version : 1.0.0
       // Date : 04.02.2005
       //
       // Parms : $Table        / table, from which data will be shown
       //         $name_fld     / name of field
       //         $val          / value seleced by default
       //         $width        / width of SELECT-field
       //         $default_val  / default value
       //         $sort_name    / sortation of a list by which field
       //         $asc_desc     / type of sirtation - asc or desc
       // Returns : true
       // Description : show the list of the records from table to combobox
       // ================================================================================================
       // Programmer : Igor Trokhymchuk
       // Date : 04.02.2005
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function ShowInComboBox( $Table, $name_fld, $val, $width, $default_val = '&nbsp;', $sort_name = 'move', $asc_desc = 'asc' )
       {
          if (empty($name_fld)) $name_fld=$Table;
          if ($width==0) $width=250;

          $tmp_db = new DB();
          $q = "SELECT * FROM `".$Table."` WHERE 1 LIMIT 1";
          $res = $tmp_db->db_Query($q);
          //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
          if ( !$res ) return false;
          if ( !$tmp_db->result ) return false;
          $fields_col = mysql_num_fields($tmp_db->result);
          
          if ($fields_col>4) $q = "SELECT * FROM `".$Table."` WHERE `lang_id`='"._LANG_ID."' order by `$sort_name` $asc_desc"; 
          else $q = "SELECT * FROM `".$Table."` WHERE `lang_id`='"._LANG_ID."'";
          
          $res = $tmp_db->db_Query($q);
          //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
          if (!$res) return false;
          $rows = $tmp_db->db_GetNumRows();

          $mas_spr['']=$default_val;
          for($i=0; $i<$rows; $i++)
          {
               $row_spr=$tmp_db->db_FetchAssoc();
               $mas_spr[$row_spr['cod']]=stripslashes($row_spr['name']);
          }
          $this->Form->Select( $mas_spr, $name_fld, $val,  $width );
       }  //end of fuinction ShowInComboBox
       
       
       // ================================================================================================
       // Function : ShowInComboBoxWithShortName
       // Version : 1.0.0
       // Date : 04.02.2005
       //
       // Parms : $Table        / table, from which data will be shown
       //         $name_fld     / name of field
       //         $val          / value seleced by default
       //         $width        / width of SELECT-field
       //         $default_val  / default value
       //         $sort_name    / sortation of a list by which field
       //         $asc_desc     / type of sirtation - asc or desc
       //         $short_name_position  / position of short name. It can be: left or right
       //         $divider      / divider between short name and full name
       // Returns : true
       // Description : show the list of the records from table to combobox
       // ================================================================================================
       // Programmer : Igor Trokhymchuk
       // Date : 04.02.2005
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function ShowInComboBoxWithShortName( $Table, $name_fld, $val, $width = '250', $default_val = '&nbsp;', $sort_name = 'move', $asc_desc = 'asc', $short_name_position='left', $divider=' ' )
       {
          if (empty($name_fld)) $name_fld=$Table;

          $tmp_db = new DB();
          $q = "SELECT * FROM `".$Table."` WHERE 1 LIMIT 1";
          $res = $tmp_db->db_Query($q);
          //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
          if ( !$res ) return false;
          if ( !$tmp_db->result ) return false;
          $fields_col = mysql_num_fields($tmp_db->result);
          
          if ($fields_col>4) $q = "SELECT * FROM `".$Table."` WHERE `lang_id`='"._LANG_ID."' order by `$sort_name` $asc_desc"; 
          else $q = "SELECT * FROM `".$Table."` WHERE `lang_id`='"._LANG_ID."'";
          
          $res = $tmp_db->db_Query($q);
          //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
          if (!$res) return false;
          $rows = $tmp_db->db_GetNumRows();

          $mas_spr['']=$default_val;
          for($i=0; $i<$rows; $i++)
          {
               $row_spr=$tmp_db->db_FetchAssoc();
               if( !empty($row_spr['short']) ){
                 if($short_name_position=='left') $mas_spr[$row_spr['cod']] = stripslashes($row_spr['short']).$divider.stripslashes($row_spr['name']);
                 else $mas_spr[$row_spr['cod']] = stripslashes($row_spr['name']).$divider.stripslashes($row_spr['short']);
               }
               else $mas_spr[$row_spr['cod']] = stripslashes($row_spr['name']);
          }
          $this->Form->Select( $mas_spr, $name_fld, $val,  $width );
       }  //end of fuinction ShowInComboBoxWithShortName()       
      

       // ================================================================================================
       // Function : ShowActSprInCombo
       // Version : 1.0.0
       // Date : 09.03.2005
       //
       // Parms : table     / name of the table from with the data will be shown
       //         fld_name  / name of the field
       //         val       / value for the field
       // Returns : true,false / Void
       // Description : Show the Combobox with data from $table with action after selections
       // ================================================================================================
       // Programmer : Igor Trokhymchuk
       // Date : 09.03.2005
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function ShowActSprInCombo($Table, $name_fld, $val = NULL, $scriptact = NULL, $default_val = NULL)
       {
          if ( empty($scriptact) ) $scriptact = 'module='.$this->module;
          //echo '<br>$this->module='.$this->module;
          $scriplink = $_SERVER['PHP_SELF']."?$scriptact";

          if (empty($name_fld)) $name_fld=$Table;

          $tmp_db = new DB();
          $q = "SELECT * FROM `".$Table."` WHERE 1 LIMIT 1";
          $res = $tmp_db->db_Query($q);
          //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
          if ( !$res ) return false;
          if ( !$tmp_db->result ) return false;
          $fields_col = mysql_num_fields($tmp_db->result);
          
          if ($fields_col>4) $q = "select * from `".$Table."` where lang_id='"._LANG_ID."' order by `move`"; 
          else $q = "select * from `".$Table."` where lang_id='"._LANG_ID."'";          

          $res = $tmp_db->db_Query($q);
          //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result; 
          if (!$res) return false;
          $rows = $tmp_db->db_GetNumRows();

          $mas_spr['']=$default_val;
          for($i=0; $i<$rows; $i++)
          {
               $row_spr=$tmp_db->db_FetchAssoc();
               $mas_spr[$row_spr['cod']]=stripslashes($row_spr['name']);
          }
          $this->Form->SelectAct( $mas_spr, $name_fld, $val, "onChange=\"location='$scriplink&$name_fld='+this.value\"" ); 
       } //end of function ShowActSprInCombo()

       // ================================================================================================
       // Function : ShowActSprInComboWithScript
       // Version : 1.0.0
       // Date : 09.03.2005
       //
       // Parms : $Table     / name of the table from with the data will be shown
       //         $name_fld  / name of the field
       //         $val       / value for the field
       //         $scriptact / script  
       //         $default_val / default value
       // Returns : true,false / Void
       // Description : Show the Combobox with data from $table with action after selections
       // ================================================================================================
       // Programmer : Igor Trokhymchuk
       // Date : 09.03.2005
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function ShowActSprInComboWithScript($Table, $name_fld, $val = NULL, $scriptact = NULL, $default_val = NULL)
       {
          $scriplink = $scriptact;

          if (empty($name_fld)) $name_fld=$Table;

          $tmp_db = new DB();
          $q = "SELECT * FROM `".$Table."` WHERE 1 LIMIT 1";
          $res = $tmp_db->db_Query($q);
          //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
          if ( !$res ) return false;
          if ( !$tmp_db->result ) return false;
          $fields_col = mysql_num_fields($tmp_db->result);
          
          if ($fields_col>4) $q = "select * from `".$Table."` where lang_id='"._LANG_ID."' order by `move`"; 
          else $q = "select * from `".$Table."` where lang_id='"._LANG_ID."'";          

          $res = $tmp_db->db_Query($q);
          //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result; 
          if (!$res) return false;
          $rows = $tmp_db->db_GetNumRows();

          $mas_spr['']=$default_val;
          for($i=0; $i<$rows; $i++)
          {
               $row_spr=$tmp_db->db_FetchAssoc();
               $mas_spr[$row_spr['cod']]=stripslashes($row_spr['name']);
          }
          $this->Form->SelectAct( $mas_spr, $name_fld, $val, "onChange=\"location='$scriplink&$name_fld='+this.value\"" ); 
       } //end of function ShowActSprInComboWithScript()        
       
       
       // ================================================================================================
       // Function : ShowInCheckBox
       // Version : 1.0.0
       // Date : 04.02.2005
       //
       // Parms : $Table      / table, from which data will be shown
       //         $name_fld   / name of field
       //         $cols       / count of checkboxes in one line
       //         $val        / value seleced by default
       //         $position   / position of the combo box ( "right" - right from tite, "left" - left from title)
       // Returns : true
       // Description : show the list of the records from table to checkbox
       // ================================================================================================
       // Programmer : Igor Trokhymchuk
       // Date : 04.02.2005
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function ShowInCheckBox( $Table, $name_fld, $cols, $val, $position = "left", $disabled = NULL, $sort_name = 'move', $asc_desc = 'asc' )
       {
          //$Tbl = new html_table(1);

          $row1 = NULL;
          if (empty($name_fld)) $name_fld=$Table;

          $tmp_db = new DB();
          $q = "SELECT * FROM `".$Table."` WHERE 1 LIMIT 1";
          $res = $tmp_db->db_Query($q);
          //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
          if ( !$res ) return false;
          if ( !$tmp_db->result ) return false;
          $fields_col = mysql_num_fields($tmp_db->result);
          
          if ($fields_col>4) $q = "select * from `".$Table."` where lang_id='"._LANG_ID."' order by `$sort_name` $asc_desc"; 
          else $q = "select * from `".$Table."` where lang_id='"._LANG_ID."'";          

          $res = $tmp_db->db_Query($q);
          if (!$res) return false;
          $rows = $tmp_db->db_GetNumRows();

          $col_check=1;
          //$Tbl->table_header();
          //$Tbl->tr();
          echo '<table border="0" cellpadding="1" cellspacing="1" align="left" class="checkbox_tbl"><tr>';
          for( $i = 0; $i < $rows; $i++ )
          {
               $row1 = $tmp_db->db_FetchAssoc();
               if ($col_check > $cols) {
                   //$Tbl->tr();
                   echo '</tr><tr>';
                   $col_check=1;
               }
               
               $checked ='>';
               if (is_array($val)) {
                   if (isset($val))
                   foreach($val as $k=>$v)
                   {
                    if (isset($k) and ($v==$row1['cod'])) $checked = " checked".$checked;
                    //echo '<br>$k='.$k.' $v='.$v.' $row1[cod]='.$row1['cod']; 
                   }
               }
               if ( $position == "left" ) $align= 'left';
               else $align= 'right';               
               echo "\n<td align='".$align."' valign='middle' class='checkbox'>";
               
               if ( $position == "left" ) {
                  //echo "<table border='0' cellpadding='1' cellspacing='0'><tr><td><input class='checkbox' type='checkbox' name='".$name_fld."[]' value='".$row1['cod']."' ".$disabled." ".$checked.'</td><td>'.stripslashes($row1['name']).'</td></tr></table>'; 
                  ?><table border="0" cellpadding="1" cellspacing="0"><tr><td><input class="checkbox" type="checkbox" name="<?=$name_fld;?>[]" value="<?=$row1['cod'];?>" <?=$disabled;?> <?=$checked;?> </td><td class="checkbox_td"><?=stripslashes($row1['name']);?></td></tr></table><?
               }
               else {
                   //echo stripslashes($row1['name'])."<input class='checkbox' type='checkbox' name='".$name_fld."[]' value='".$row1['cod']."' ".$disabled." ".$checked;
                   ?><table border="0" cellpadding="1" cellspacing="0"><tr><td><td class="checkbox_td"><?=stripslashes($row1['name']);?></td><input class="checkbox" type="checkbox" name="<?=$name_fld;?>[]" value="<?=$row1['cod'];?>" <?=$disabled;?> <?=$checked;?> </td></tr></table><?
               }
               echo "</td>";
                $col_check++;
          }
          echo '</tr></table>';
          //$Tbl->table_footer();
       }  //end of fuinction ShowInCheckBox
       
       
       // ================================================================================================
       // Function : ShowInRadioBox
       // Version : 1.0.0
       // Date : 04.02.2005
       //
       // Parms : $Table      / table, from which data will be shown
       //         $name_fld   / name of field
       //         $cols       / count of checkboxes in one line
       //         $val        / value seleced by default
       //         $position   / position of the combo box ( "right" - right from tite, "left" - left from title)
       // Returns : true
       // Description : show the list of the records from table to checkbox
       // ================================================================================================
       // Programmer : Igor Trokhymchuk
       // Date : 04.02.2005
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function ShowInRadioBox( $Table, $name_fld, $cols, $val, $position = "left", $disabled = NULL, $sort_name = 'move', $asc_desc = 'asc' )
       {
          //$Tbl = new html_table(1);

          $row1 = NULL;
          if (empty($name_fld)) $name_fld=$Table;

          $tmp_db = new DB();
          $q = "SELECT * FROM `".$Table."` WHERE 1 LIMIT 1";
          $res = $tmp_db->db_Query($q);
          //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
          if ( !$res ) return false;
          if ( !$tmp_db->result ) return false;
          $fields_col = mysql_num_fields($tmp_db->result);
          
          if ($fields_col>4) $q = "select * from `".$Table."` where lang_id='"._LANG_ID."' order by `$sort_name` $asc_desc"; 
          else $q = "select * from `".$Table."` where lang_id='"._LANG_ID."'";          

          $res = $tmp_db->db_Query($q);
          if (!$res) return false;
          $rows = $tmp_db->db_GetNumRows();

          $col_check=1;
          //$Tbl->table_header();
          //$Tbl->tr();
          echo '<table border="0" cellpadding="1" cellspacing="1" align="left" class="radio_tbl"><tr>';
          for( $i = 0; $i < $rows; $i++ )
          {
               $row1 = $tmp_db->db_FetchAssoc();
               if ($col_check > $cols) {
                   //$Tbl->tr();
                   echo '</tr><tr>';
                   $col_check=1;
               }
                   
               $checked ='>';
               if ($val==$row1['cod']) $checked = " checked".$checked;

               if ( $position == "left" ) $align= 'left';
               else $align= 'right';               
               echo "\n<td align=".$align." valign='middle' class='radio'>";
               
               if ( $position == "left" ) echo "<input class='radio' type='radio' name='".$name_fld."' value='".$row1['cod']."' ".$disabled." ".$checked.' '.stripslashes($row1['name']); 
               else echo stripslashes($row1['name'])."<input class='radio' type='radio' name=".$name_fld." value='".$row1['cod']."' ".$disabled." ".$checked;
               echo "</td>";
                $col_check++;
          }
          echo '</tr></table>';
          //$Tbl->table_footer();
       }  //end of fuinction ShowInRadioBox       

      
        // ================================================================================================
        // Function : ShowImage
        // Version : 1.0.0
        // Date : 06.11.2006
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
        // Date : 06.11.2006  
        // Reason for change : Creation
        // Change Request Nbr:
        // ================================================================================================
        function ShowImage($spr = NULL, $lang_id = NULL, $img = NULL, $size = NULL, $quality = NULL, $wtm = NULL, $parameters = NULL)
        {
         $size_auto = NULL;
         $size_width = NULL;
         $size_height = NULL;
         if ( strstr($size,'size_auto') ) $size_auto = substr( $size, strrpos($size,'=')+1, strlen($size) );
         if ( strstr($size,'size_width') ) $size_width = substr( $size, strrpos($size,'=')+1, strlen($size) );
         if ( strstr($size,'size_height') ) $size_height = substr( $size, strrpos($size,'=')+1, strlen($size) );
         
         if (empty($quality)) $quality=100;
         
         $img_with_path = $this->GetImgPath($img, $spr, $lang_id);
         $img_full_path = $this->GetImgFullPath($img, $spr, $lang_id);   
           
         //$img_full_path = Spr_Img_Path.$spr.'/'.$img_with_path; // like z:/home/speakers/www/uploads/45/R1800TII_big.jpg
         //echo '<br> $img_full_path='.$img_full_path.'<br> $size_auto='.$size_auto;
         if ( !file_exists($img_full_path) ) return false;

         $thumb = new Thumbnail($img_full_path);

         if ( !empty($size_width ) and empty($size_height) ) $thumb->size_width($size_width);
         if ( !empty($size_height) and empty($size_width) ) $thumb->size_height($size_height);
         if ( !empty($size_width) and !empty($size_height) ) $thumb->size($size_width,$size_height); 
         if ( !$size_width and !$size_height and $size_auto ) $thumb->size_auto($size_auto);                    // [OPTIONAL] set the biggest width and height for thumbnail
         
         $thumb->quality=$quality;                  //default 75 , only for JPG format  
         //echo '<br>$wtm='.$wtm;
         if ( $wtm == 'img' ) {
            $thumb->img_watermark = SITE_PATH.'/images/design/m01.png';        // [OPTIONAL] set watermark source file, only PNG format [RECOMENDED ONLY WITH GD 2 ]
            $thumb->img_watermark_Valing='CENTER';           // [OPTIONAL] set watermark vertical position, TOP | CENTER | BOTTOM
            $thumb->img_watermark_Haling='CENTER';           // [OPTIONAL] set watermark horizonatal position, LEFT | CENTER | RIGHT
         }
         if ( $wtm == 'txt' ) {
             if ( defined('WATERMARK_TEXT') ) $thumb->txt_watermark=SPR_WATERMARK_TEXT;        // [OPTIONAL] set watermark text [RECOMENDED ONLY WITH GD 2 ]
             else $thumb->txt_watermark='';
             $thumb->txt_watermark_color='000000';        // [OPTIONAL] set watermark text color , RGB Hexadecimal[RECOMENDED ONLY WITH GD 2 ]
             $thumb->txt_watermark_font=5;                // [OPTIONAL] set watermark text font: 1,2,3,4,5
             $thumb->txt_watermark_Valing='TOP';           // [OPTIONAL] set watermark text vertical position, TOP | CENTER | BOTTOM
             $thumb->txt_watermark_Haling='LEFT';       // [OPTIONAL] set watermark text horizonatal position, LEFT | CENTER | RIGHT
             $thumb->txt_watermark_Hmargin=10;          // [OPTIONAL] set watermark text horizonatal margin in pixels
             $thumb->txt_watermark_Vmargin=10;           // [OPTIONAL] set watermark text vertical margin in pixels     
         }

         $thumb->process();       // generate image  

         //make new image like R1800TII_big.jpg -> R1800TII_big_autozoom_100x84.jpg  
         $mas_img_name=explode(".",$img_with_path);
         $img_name_new = $mas_img_name[0].SPR_ADDITIONAL_FILES_TEXT.intval($thumb->img['x_thumb']).'x'.intval($thumb->img['y_thumb']).'.'.$mas_img_name[1];
         $img_src = $img_name_new; 
         $img_full_path_new = SITE_PATH.$img_name_new; 
         $uploaddir = SITE_PATH.substr($img_with_path, 0, strrpos($img_with_path,'/')).'/';                      

         
         $alt = $this->GetImgTitle( $spr, $img);
         $title = $this->GetImgTitle( $spr, $img); 
         //echo '<br>$img_name_new='.$img_name_new;  
         //echo '<br>$img_full_path_new='.$img_full_path_new;
         //echo '<br>$img_src='.$img_src;
         if ( !strstr($parameters, 'alt') )  $parameters = $parameters.' alt="'.$alt.'"';
         if ( !strstr($parameters, 'title') ) $parameters = $parameters.' title=" '.$title.' "';
         
         if ( !file_exists($img_full_path_new) ){
             //echo '<br>$uploaddir='.$uploaddir.'<br>$img_full_path_new='.$img_full_path_new;
             chmod($uploaddir,0777);
             $thumb->save($img_full_path_new);
             chmod($uploaddir,0755);
             $params = "img=$img&amp;spr=$spr&amp;lang_id=$lang_id&amp;$size&amp;quality=$quality";
             //echo '<br> $params='.$params;
             ?><img src="<?=$img_src;?>" <?=$parameters?>><? 
         }
         else {
             ?><img src="<?=$img_src;?>" <?=$parameters?>><?
         }
         return;  
        } // end of function ShowImage()
       
       
 }  //end of class SystemSpr  