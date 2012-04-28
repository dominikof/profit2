<?php
// ================================================================================================
// Module : upload.view.php
// Version : 1.0.0
// Date : 25.06.2010
// Licensed To:
// Oleg Morgalyuk oleg@seotm.com
//
// Purpose : Class definition(model) for working with uploads (files, images, videos)
//
// ================================================================================================
include_once( SITE_PATH.'/admin/include/defines.inc.php' );
// ================================================================================================
//
//    Programmer        :  Oleg Morgalyuk
//    Date              :  25.06.2010
//    Reason for change :  Creation
//    Change Request Nbr:
//
//    Function          :  Class definition(model) for working with uploads (files, images, videos)
//
//  ================================================================================================

class UploadModel{
    static $db = NULL;

// ================================================================================================
//    Function          : UploadModel (Constructor)
//    Version           : 1.0.0
//    Date              : 29.06.2010
//    Returns           : none
//
//    Description       : Set the variabels
// ================================================================================================
    function UploadModel(){
        if(!isset(UploadModel::$db))
            UploadModel::$db = new DB;
    }
 // ================================================================================================
// Function : MakeValidFileTypePos
// Version : 1.0.0
// Date : 29.06.2010
//
// Parms :   $filename       / $filename
// Returns : $res / Void
// Description : delete files
// ================================================================================================
// Programmer : Oleg MOrgalyuk
// Date : 29.06.2010
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================
    function MakeValidFileTypePos($filename)
    {
        $arr = explode(".",$filename);
        $count = count($arr);
        $ext = $arr[$count-1];
        for ($i=1;$i<($count-1); $i++)
        {
            $arr[0] .= $arr[$i];
        }
        $arr[1] = $ext;
     return $arr;
    }  // end of function MakeValidFileTypePos()
// ================================================================================================
// Function : CreateTables
// Version : 1.0.0
// Date : 29.06.2010
//
// Parms :
// Returns : $res / errors
// Description : Create tables
// ================================================================================================
// Programmer : Oleg Morgalyuk
// Date : 29.06.2010
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================
    function CreateTables($table1,$table2)
    {
        $q = "CREATE TABLE `$table1` (
        `id` mediumint(8) unsigned NOT NULL auto_increment,
        `id_module` smallint(5) unsigned NOT NULL default '0',
        `id_position` mediumint(8) unsigned NOT NULL default '0',
        `path` varchar(255) NOT NULL default '',
        `visible` enum('0','1') NOT NULL default '1',
        `move` mediumint(8) unsigned default NULL,
        PRIMARY KEY  (`id`),
        KEY `id_module` (`id_module`),
        KEY `id_position` (`id_position`),
        KEY `visible` (`visible`),
        KEY `move` (`move`)
        )";
        $res = UploadModel::$db->db_query( $q );
//        echo '<br>q='.$q.' res='.$res;
        if( !$res OR !UploadModel::$db->result )
            return false;
        $q = "CREATE TABLE `$table2` (
        `ids` mediumint(8) unsigned NOT NULL auto_increment,
        `cod` mediumint(8)  unsigned NOT NULL default '0',
        `lang_id` tinyint(3) unsigned NOT NULL default '0',
        `name` varchar(255) NOT NULL default '',
        `text` text NOT NULL,
        PRIMARY KEY  (`ids`),
        KEY `cod` (`cod`),
        KEY `lang_id` (`lang_id`)
        )";
        $res = UploadModel::$db->db_query( $q );
//        echo '<br>q='.$q.' res='.$res;
        if( !$res OR !UploadModel::$db->result )
            return false;
        return true;
    }//CreateTables

// ================================================================================================
// Function : SaveFiles
// Version : 1.0.0
// Date : 29.06.2010
//
// Parms :   $max_image_size  / max size of file, which can be uploaded
//           $text_mess       / array of multilanguage captions
//           $uploaddir       / path to upload
//           $id              / id of position to upload
//           $table1          / table to store general information
//           $table2          / table to store text information
//           $module          / module id
//           $lang            / array of languages
//           $save_file_names / true or false to save original name
// Returns : $res / eroors
// Description : Save the files to the folder and save path in the database
// ================================================================================================
// Programmer : Oleg Morgalyuk
// Date : 29.06.2010
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================
    function SaveFiles($max_image_size,$text_mess,$uploaddir,$id,$table1,$table2,$module,$lang,$save_file_names,$formId=1)
    {
        $this->Err= NULL;
     //$valid_types =  array("gif", "GIF", "jpg", "JPG", "png", "PNG", "jpeg", "JPEG");
     //print_r($_FILES["file".$formId]);
     $sys_crypt = new Crypt();
     if( !isset($_FILES["file".$formId]["name"])) return false;
     $cols = count($_FILES["file".$formId]["name"]);
     $title = $_REQUEST['titleF'.$formId];
     $decription = $_REQUEST['descriptionF'.$formId];
     $uploaddir0 = SITE_PATH.'/'.$uploaddir;
     if ( !file_exists ($uploaddir0) )
        mkdir($uploaddir0,0777);
     else
        @chmod($uploaddir0,0777);
     $alias = $id;
     $uploaddir1 = $uploaddir0.'/'.$alias;
     if ( !file_exists ($uploaddir1) )
        mkdir($uploaddir1,0777);
     else
        @chmod($uploaddir1,0777);
     for ($i=0; $i<$cols; $i++) {
         //echo '<br>$_FILES["file".$formId]["name"][$i]='.$_FILES["file".$formId]["name"][$i];
            if ( empty($_FILES["file".$formId]["name"][$i]) ) continue;
            //echo '<br>$_FILES["file".$formId]='.$_FILES["file".$formId].' $_FILES["file".$formId]["tmp_name"]["'.$i.'"]='.$_FILES["file".$formId]["tmp_name"]["$i"].' $_FILES["file".$formId]["size"]["'.$i.'"]='.$_FILES["file".$formId]["size"]["$i"];
           if ( isset($_FILES["file".$formId]) && is_uploaded_file($_FILES["file".$formId]["tmp_name"][$i]) && $_FILES["file".$formId]["size"][$i] ){
            $filename = $_FILES["file".$formId]['tmp_name'][$i];
            $file_array = explode(".",$_FILES["file".$formId]['name'][$i]);
            $ext = $file_array[1];
            //echo '<br>filesize($filename)='.filesize($filename).' $max_image_size='.$max_image_size;
            if (filesize($filename) > $max_image_size) {
                $this->Err = $this->Err.$text_mess['MSG_ERR_FILE_SIZE'].': ('.$_FILES["file".$formId]['name']["$i"].')<br />';
                continue;
            }
            else {

             if($save_file_names) $uploaddir2 = $sys_crypt->GetTranslitStr($file_array[0]).'.'.$ext;
             else $uploaddir2 = time().$i.'.'.$ext;
             $uploaddir = $uploaddir1."/".$uploaddir2;

//             echo '<br>$filename='.$filename.'<br> $uploaddir='.$uploaddir.'<br> $uploaddir1='.$uploaddir1.'<br> $uploaddir2='.$uploaddir2;
             //if (@move_uploaded_file($filename, $uploaddir)) {
             if ( copy($filename,$uploaddir) ) {
                 //====== set next max value for move START ============
                 $maxx = NULL;
                 $q = "SELECT MAX(move) FROM `".$table1."` where `id_position` = $id";
                 $res = UploadModel::$db->db_Query( $q);
                 $row = UploadModel::$db->db_FetchAssoc();
                 $maxx = $row['MAX(move)']+1;
                 if($maxx==1) $maxx=0;
                 //====== set next max value for move END ============

                 $q="INSERT `$table1` values(NULL,'".$module."','".$id."','".$uploaddir2."','1', '$maxx')";
                 $res = UploadModel::$db->db_Query( $q);
                 if( !UploadModel::$db->result ) $this->Err = $this->Err.$text_mess['MSG_ERR_SAVE_FILE_TO_DB'].' ('.$_FILES["file".$formId]['name']["$i"].')<br />';
//                 echo '<br>q='.$q.' res='.$res.' UploadModel::$db->result='.UploadModel::$db->result;
                  $lang_count = count($lang);
                  $lang_keys = array_keys($lang);
                  $id_file = UploadModel::$db->db_GetInsertID();
                 for($j=0;$j<$lang_count;$j++){
                        $key =$lang_keys[$j];
                        if(isset($title[$key][$i])) $title[$key][$i] = addslashes($title[$key][$i]);
                        else $title[$key][$i]='';
                        if(isset($decription[$key][$i])) $decription[$key][$i] = addslashes($decription[$key][$i]);
                        else $decription[$key][$i]='';
                        $q="INSERT `$table2` values(NULL,'".$id_file."','".$key."','".$title[$key][$i]."','".$decription[$key][$i]."')";
                        $res = UploadModel::$db->db_Query( $q);
                        if( !UploadModel::$db->result ) $this->Err = $this->Err.$text_mess['MSG_ERR_SAVE_FILE_TO_DB'].' ('.$_FILES["file".$formId]['name']["$i"].')<br />';
//                        echo '<br>q='.$q.' res='.$res.' UploadModel::$db->result='.UploadModel::$db->result;
                    }
             }
             else {
                 $this->Err = $this->Err.$text_mess['MSG_ERR_FILE_MOVE'].': ('.$_FILES["file".$formId]['name']["$i"].')<br>';
             }
             @chmod($uploaddir1,0755);
             @chmod($uploaddir0,0755);
            }
           }
           else $this->Err = $this->Err.$text_mess['MSG_ERR_FILE'].': ('.$_FILES["file".$formId]['name']["$i"].')<br>';
     } // end for
     return $this->Err;
    }  // end of function SaveFiles()

// ================================================================================================
// Function : SaveImages
// Version : 1.0.0
// Date : 04.07.2010
//
// Parms :   $max_image_size  / max size of file, which can be uploaded
//           $text_mess       / array of multilanguage captions
//           $uploaddir       / path to upload
//           $id              / id of position to upload
//           $table1          / table to store general information
//           $table2          / table to store text information
//           $module          / module id
//           $lang            / array of languages
//           $save_file_names / true or false to save original name
//           $valid_types     / valid images types
//           $max_image_width / max image size (width)
//           $max_image_height/ max image size (height)
// Returns : $res / errors
// Description : Save the images to the folder and save path in the database
// ================================================================================================
// Programmer : Oleg Morgalyuk
// Date : 04.07.2010
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================
function SaveImages($max_image_size,$text_mess,$uploaddir,$id,$table1,$table2,$module,$lang,$save_file_names,$valid_types,$max_image_width=NULL,$max_image_height=NULL,$formId=1)
{
    $this->Err= NULL;
    $sys_crypt = new Crypt();
    if( !isset($_FILES["image".$formId]["name"]) ) return false;
    $cols = count($_FILES["image".$formId]["name"]);
    $title = $_REQUEST['titleI'.$formId];
    $decription = $_REQUEST['descriptionI'.$formId];
    $uploaddir0 = SITE_PATH.'/'.$uploaddir;
    if ( !file_exists ($uploaddir0) )
       mkdir($uploaddir0,0777);
    else
       @chmod($uploaddir0,0777);
    $alias = $id;
    $uploaddir1 = $uploaddir0.'/'.$alias;
    if ( !file_exists ($uploaddir1) )
       mkdir($uploaddir1,0777);
    else
       @chmod($uploaddir1,0777);
    for ($i=0; $i<$cols; $i++) {
//         echo '<br>$_FILES["image"]["name"][$i]='.$_FILES["image"]["name"][$i];
            if ( empty($_FILES["image".$formId]["name"][$i]) ) continue;
            //echo '<br>$_FILES["file"]='.$_FILES["file"].' $_FILES["file"]["tmp_name"]["'.$i.'"]='.$_FILES["file"]["tmp_name"]["$i"].' $_FILES["file"]["size"]["'.$i.'"]='.$_FILES["file"]["size"]["$i"];
           if ( isset($_FILES["image".$formId]) && is_uploaded_file($_FILES["image".$formId]["tmp_name"][$i]) && $_FILES["image".$formId]["size"][$i] ){
             $filename = $_FILES['image'.$formId]['tmp_name'][$i];
             $file_array = $this->MakeValidFileTypePos($_FILES['image'.$formId]['name'][$i]);
             $ext = $file_array[1];
            //echo '<br>filesize($filename)='.filesize($filename).' $max_image_size='.$max_image_size;
            if (filesize($filename) > $max_image_size) {
                $this->Err = $this->Err.$text_mess['MSG_ERR_FILE_SIZE'].': ('.$_FILES['image'.$formId]['name']["$i"].')<br />';
                continue;
            }
            if (!in_array($ext, $valid_types)) {
                $this->Err = $this->Err.$text_mess['MSG_ERR_FILE_TYPE'].' ('.$_FILES['image'.$formId]['name']["$i"].')<br />';
                continue;
            }
            else {
             if($save_file_names) $uploaddir2 =  $sys_crypt->GetTranslitStr($file_array[0]).'.'.$ext;
             else $uploaddir2 = time().$i.'.'.$ext;
             $uploaddir = $uploaddir1."/".$uploaddir2;
//             echo '<br>$filename='.$filename.'<br> $uploaddir='.$uploaddir.'<br> $uploaddir1='.$uploaddir1.'<br> $uploaddir2='.$uploaddir2;
             //if (@move_uploaded_file($filename, $uploaddir)) {
             if ( copy($filename,$uploaddir) ) {
                 if(isset($max_image_width) && (isset($max_image_height))){
                     $size = GetImageSize($filename);
                     if (is_array($size) AND (($size[0] > $max_image_width) OR ($size[1] > $max_image_height)) ){
                         //============= resize original image to size from settings =============
                         $thumb = new Thumbnail($uploaddir);
                         if($max_image_width==$max_image_height) $thumb->size_auto($max_image_width);
                         else{
                             if(($size[0]/$max_image_width) < ($size[1] / $max_image_height))
                                $thumb->size_height($max_image_height);
                            else
                                $thumb->size_width($max_image_width);

                         }
                         $thumb->quality = 85;
                         $thumb->process();       // generate image
                         $thumb->save($uploaddir); //make new image
                         //=======================================================================
                     }
                 }
                 //====== set next max value for move START ============
                 $maxx = NULL;
                 $q = "SELECT MAX(move) FROM `".$table1."` where `id_position` = $id";
                 $res = UploadModel::$db->db_Query( $q);
                 $row = UploadModel::$db->db_FetchAssoc();
                 $maxx = $row['MAX(move)']+1;
                 if($maxx==1) $maxx=0;
                 //====== set next max value for move END ============

                 $q="INSERT `$table1` values(NULL,'".$module."','".$id."','".$uploaddir2."','1', '$maxx')";
                 $res = UploadModel::$db->db_Query( $q);
                 if( !UploadModel::$db->result ) $this->Err = $this->Err.$text_mess['MSG_ERR_SAVE_FILE_TO_DB'].' ('.$_FILES['image'.$formId]['name']["$i"].')<br />';
//                 echo '<br>q='.$q.' res='.$res.' UploadModel::$db->result='.UploadModel::$db->result;
                  $lang_count = count($lang);
                  $lang_keys = array_keys($lang);
                  $id_file = UploadModel::$db->db_GetInsertID();
                 for($j=0;$j<$lang_count;$j++){
                        $key =$lang_keys[$j];
                        if(isset($title[$key][$i])) $title[$key][$i] = addslashes($title[$key][$i]);
                        else $title[$key][$i]='';
                        if(isset($decription[$key][$i])) $decription[$key][$i] = addslashes($decription[$key][$i]);
                        else $decription[$key][$i]='';
                        $q="INSERT `$table2` values(NULL,'".$id_file."','".$key."','".$title[$key][$i]."','".$decription[$key][$i]."')";
                        $res = UploadModel::$db->db_Query( $q);
                        if( !UploadModel::$db->result ) $this->Err = $this->Err.$text_mess['MSG_ERR_SAVE_FILE_TO_DB'].' ('.$_FILES['image'.$formId]['name']["$i"].')<br />';
//                        echo '<br>q='.$q.' res='.$res.' UploadModel::$db->result='.UploadModel::$db->result;
                    }
             }
             else {
                 $this->Err = $this->Err.$text_mess['MSG_ERR_FILE_MOVE'].': ('.$_FILES['image'.$formId]['name']["$i"].')<br>';
             }
             @chmod($uploaddir1,0755);
             @chmod($uploaddir0,0755);
            }
           }
           else $this->Err = $this->Err.$text_mess['MSG_ERR_FILE'].': ('.$_FILES['image'.$formId]['name']["$i"].')<br>';
    } // end for
    return $this->Err;

}  // end of function SaveImages()


// ================================================================================================
// Function : SaveVideos
// Version : 1.0.0
// Date : 05.07.2010
// Parms :   $max_image_size  / max size of file, which can be uploaded
//           $text_mess       / array of multilanguage captions
//           $uploaddir       / path to upload
//           $id              / id of position to upload
//           $table1          / table to store general information
//           $table2          / table to store text information
//           $module          / module id
//           $lang            / array of languages
//           $save_file_names / true or false to save original name
//           $valid_types     / valid images types
// Returns : $res / errors
// Description : Save the images to the folder and save path in the database
// ================================================================================================
// Programmer : Oleg Morgalyuk
// Date : 05.07.2010
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================
function SaveVideos($max_image_size,$text_mess,$uploaddir,$id,$table1,$table2,$module,$lang,$save_file_names,$valid_types,$max_image_width=NULL,$max_image_height=NULL)
{
    $this->Err= NULL;
    $sys_crypt = new Crypt();
    if( !isset($_FILES["video"]["name"]) ) return false;
     $cols = count($_FILES["video"]["name"]);
     $title = $_REQUEST['titleV'];
     $decription = $_REQUEST['descriptionV'];
     $uploaddir0 = SITE_PATH.'/'.$uploaddir;
     if ( !file_exists ($uploaddir0) )
        mkdir($uploaddir0,0777);
     else
        @chmod($uploaddir0,0777);
     $alias = $id;
     $uploaddir1 = $uploaddir0.'/'.$alias;
     if ( !file_exists ($uploaddir1) )
        mkdir($uploaddir1,0777);
     else
        @chmod($uploaddir1,0777);
     for ($i=0; $i<$cols; $i++) {
//         echo '<br>$_FILES["image"]["name"][$i]='.$_FILES["image"]["name"][$i];
            if ( empty($_FILES["video"]["name"][$i]) ) continue;
//            echo '<br>$_FILES["file"]='.$_FILES["file"].' $_FILES["file"]["tmp_name"]["'.$i.'"]='.$_FILES["file"]["tmp_name"]["$i"].' $_FILES["file"]["size"]["'.$i.'"]='.$_FILES["file"]["size"]["$i"];
           if ( isset($_FILES["video"]) && is_uploaded_file($_FILES["video"]["tmp_name"][$i]) && $_FILES["video"]["size"][$i] ){
             $filename = $_FILES['video']['tmp_name'][$i];
             $file_array = explode(".",$_FILES['video']['name'][$i]);
             $ext = $file_array[1];
//            echo '<br>filesize($filename)='.filesize($filename).' $max_image_size='.$max_image_size;
            if (filesize($filename) > $max_image_size) {
                $this->Err = $this->Err.$text_mess['MSG_ERR_FILE_SIZE'].': ('.$_FILES['video']['name']["$i"].')<br />';
                continue;
            }
            if (!in_array($ext, $valid_types)) {
                $this->Err = $this->Err.$text_mess['MSG_ERR_FILE_TYPE_VIDEO'].' ('.$_FILES['video']['name']["$i"].')<br />';
                continue;
            }
            else {
             if($save_file_names) $uploaddir2 =  $sys_crypt->GetTranslitStr($file_array[0]).'.'.$ext;
             else $uploaddir2 = time().$i.'.'.$ext;
             $uploaddir = $uploaddir1."/".$uploaddir2;
//             echo '<br>$filename='.$filename.'<br> $uploaddir='.$uploaddir.'<br> $uploaddir1='.$uploaddir1.'<br> $uploaddir2='.$uploaddir2;
             //if (@move_uploaded_file($filename, $uploaddir)) {
             if ( copy($filename,$uploaddir) ) {
                 if(isset($max_image_width) && (isset($max_image_height))){
                if (($size) AND (($size[0] > $max_image_width) OR ($size[1] > $max_image_height)) ){
                         //============= resize original image to size from settings =============
                         $thumb = new Thumbnail($uploaddir);
                         if($max_image_width==$max_image_height) $thumb->size_auto($max_image_width);
                         else{
                            $thumb->size_width($max_image_width);
                            $thumb->size_height($max_image_height);
                         }
                         $thumb->quality = 100;
                         $thumb->process();       // generate image
                         $thumb->save($uploaddir); //make new image
                         //=======================================================================
                     }
                 }
                 //====== set next max value for move START ============
                 $maxx = NULL;
                 $q = "SELECT MAX(move) FROM `".$table1."` where `id_position` = $id";
                 $res = UploadModel::$db->db_Query( $q);
                 $row = UploadModel::$db->db_FetchAssoc();
                 $maxx = $row['MAX(move)']+1;
                 if($maxx==1) $maxx=0;
                 //====== set next max value for move END ============

                 $q="INSERT `$table1` values(NULL,'".$module."','".$id."','".$uploaddir2."','1', '$maxx')";
                 $res = UploadModel::$db->db_Query( $q);
                 if( !UploadModel::$db->result ) $this->Err = $this->Err.$text_mess['MSG_ERR_SAVE_FILE_TO_DB'].' ('.$_FILES['video']['name']["$i"].')<br />';
//                 echo '<br>q='.$q.' res='.$res.' UploadModel::$db->result='.UploadModel::$db->result;
                  $lang_count = count($lang);
                  $lang_keys = array_keys($lang);
                  $id_file = UploadModel::$db->db_GetInsertID();
                 for($j=0;$j<$lang_count;$j++){
                        $key =$lang_keys[$j];
                        if(isset($title[$key][$i])) $title[$key][$i] = addslashes($title[$key][$i]);
                        else $title[$key][$i]='';
                        if(isset($decription[$key][$i])) $decription[$key][$i] = addslashes($decription[$key][$i]);
                        else $decription[$key][$i]='';
                        $q="INSERT `$table2` values(NULL,'".$id_file."','".$key."','".$title[$key][$i]."','".$decription[$key][$i]."')";
                        $res = UploadModel::$db->db_Query( $q);
                        if( !UploadModel::$db->result ) $this->Err = $this->Err.$text_mess['MSG_ERR_SAVE_FILE_TO_DB'].' ('.$_FILES['video']['name']["$i"].')<br />';
//                        echo '<br>q='.$q.' res='.$res.' UploadModel::$db->result='.UploadModel::$db->result;
                    }
             }
             else {
                 $this->Err = $this->Err.$text_mess['MSG_ERR_FILE_MOVE'].': ('.$_FILES['video']['name']["$i"].')<br>';
             }
             @chmod($uploaddir1,0755);
             @chmod($uploaddir0,0755);
            }
           }
           else $this->Err = $this->Err.$text_mess['MSG_ERR_FILE'].': ('.$_FILES['video']['name']["$i"].')<br>';
     } // end for
     return $this->Err;
    }  // end of function SaveImages()
// ================================================================================================
// Function : UpdateFiles
// Version : 1.0.0
// Date : 29.06.2010
//
// Parms :   $text_mess       / array of multilanguage captions
//           $id              / id of position to upload
//           $table1          / table to store general information
//           $table2          / table to store text information
//           $module          / module id
//           $lang            / array of languages
//           $type            / type of file (F - file, I - image, V - video)
// Returns : $res / Void
// Description : update information of files
// ================================================================================================
// Programmer : Oleg MOrgalyuk
// Date : 29.06.2010
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================
    function UpdateFiles($text_mess,$id,$table1,$table2,$module,$lang,$type,$formId=1)
    {
        $this->Err= NULL;
	$type=$type.$formId;
        if (!isset($_REQUEST['move'.$type])) return NULL;
        $move = $_REQUEST['move'.$type];
        $visible = array();
        if (isset($_REQUEST['visible'.$type]))
            $visible = $_REQUEST['visible'.$type];
        $count_move = count ($move);
        for ($i = 0; $i < $count_move; $i++){
            if(isset($visible[$move[$i]])) $vis = 1;
            else $vis = 0;
            $q="UPDATE `$table1` set `move` = '".$i."',`visible` = '".$vis."'  where `id` = '".$move[$i]."'";
            $res = UploadModel::$db->db_Query( $q);
//            echo '<br>q='.$q.' res='.$res.' UploadModel::$db->result='.UploadModel::$db->result;
            if( !UploadModel::$db->result ) $this->Err = $this->Err.$text_mess['MSG_ERR_CHANGE_INFO'].' ('.$move[$i].')<br />';
        }
        $title  = $_REQUEST['title'.$type.'s'];
        $description  = $_REQUEST['description'.$type.'s'];
        $lang_count = count($lang);
        $lang_keys = array_keys($lang);
        for ($i = 0; $i < $count_move; $i++){
            $pos = $move[$i];
                for($j=0;$j<$lang_count;$j++){
                        $key =$lang_keys[$j];
                        $q="UPDATE `$table2` set `name`='".$title[$pos][$key]."', `text` = '".$description[$pos][$key]."' where `cod` = '".$pos."' and `lang_id` = '".$key."' ";
                        $res = UploadModel::$db->db_Query( $q);
//                        echo '<br>q='.$q.' res='.$res.' UploadModel::$db->result='.UploadModel::$db->result;
                        if (!UploadModel::$db->result) $this->Err = $this->Err.$text_mess['MSG_ERR_CHANGE_INFO'].'('.$key.')<br />';
                }
        }
     return $this->Err;
    }  // end of function UpdateFiles()
// ================================================================================================
// Function : DeleteFiles
// Version : 1.0.0
// Date : 29.06.2010
//
// Parms :   $text_mess       / array of multilanguage captions
//           $id              / id of file
//           $table1          / table to store general information
//           $table2          / table to store text information
//           $path            / path to file
// Returns : $res / Void
// Description : delete files
// ================================================================================================
// Programmer : Oleg MOrgalyuk
// Date : 29.06.2010
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================
    function DeleteFiles($text_mess,$id,$table1,$table2,$path)
    {
        $this->Err= NULL;
        $q="DELETE FROM `$table1` where `id` = '".$id."'";
        $res = UploadModel::$db->db_Query( $q);
//      echo '<br>q='.$q.' res='.$res.' UploadModel::$db->result='.UploadModel::$db->result;
        if( !UploadModel::$db->result ) $this->Err = $this->Err.$text_mess['MSG_DEL_INFO'].' ('.$path.')<br />';
        $q="DELETE FROM `$table2` where `cod` = '".$id."'";
        $res = UploadModel::$db->db_Query( $q);
//      echo '<br>q='.$q.' res='.$res.' UploadModel::$db->result='.UploadModel::$db->result;
        if (!UploadModel::$db->result) $this->Err = $this->Err.$text_mess['MSG_DEL_INFO'].'('.$path.')<br />';
        $uploaddir0 = SITE_PATH.$path;
        if(file_exists($uploaddir0)){
            if(!unlink($uploaddir0))$this->Err = $this->Err.$text_mess['MSG_DEL_'].'('.$path.')<br />';
        }
        else
           $this->Err = $this->Err.$text_mess['MSG_DEL_'].'('.$path.')<br />';
     return $this->Err;
    }  // end of function DeleteFiles()
// ================================================================================================
// Function : DeleteImages
// Version : 1.0.0
// Date : 05.07.2010
//
// Parms :   $text_mess       / array of multilanguage captions
//           $id              / id of file
//           $table1          / table to store general information
//           $table2          / table to store text information
//           $path            / path to file
// Returns : $res / errors
// Description : delete files
// ================================================================================================
// Programmer : Oleg Morgalyuk
// Date : 05.07.2010
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================
    function DeleteImages($text_mess,$id,$table1,$table2,$img,$path)
    {
        $this->Err= NULL;
        $q="DELETE FROM `$table1` where `id` = '".$id."'";
        $res = UploadModel::$db->db_Query( $q);
//      echo '<br>q='.$q.' res='.$res.' UploadModel::$db->result='.UploadModel::$db->result;
        if( !UploadModel::$db->result ) $this->Err = $this->Err.$text_mess['MSG_DEL_INFO'].' ('.$path.')<br />';
        $q="DELETE FROM `$table2` where `cod` = '".$id."'";
        $res = UploadModel::$db->db_Query( $q);
//      echo '<br>q='.$q.' res='.$res.' UploadModel::$db->result='.UploadModel::$db->result;
        if (!UploadModel::$db->result) $this->Err = $this->Err.$text_mess['MSG_DEL_INFO'].'('.$path.')<br />';
        $path = SITE_PATH.$path;
       //echo '<br> $path='.$path;
       $handle = @opendir($path);
       //echo '<br> $handle='.$handle;
       $cols_files = 0;
       $mas_img_name=explode(".",$img);
       while ( ($file = readdir($handle)) !==false ) {
           //echo '<br> $file='.$file;
           $mas_file=explode(".",$file);
           if ( strstr($mas_file[0], $mas_img_name[0]) and $mas_file[1]==$mas_img_name[1] ) {
              $res = unlink ($path.'/'.$file);
              if( !$res ) $this->Err = $this->Err.$text_mess['MSG_DEL_'].'('.$path.'/'.$file.')<br />';
           }
           if ($file == "." || $file == ".." ) {
               $cols_files++;
           }
       }
       //if ($cols_files==2) rmdir($path);
       closedir($handle);
     return $this->Err;
     }  // end of function DeleteImages()
// ================================================================================================
// Function : DeleteImagesThumb
// Version : 1.0.0
// Date : 05.07.2010
//
// Parms :   $text_mess      / array of multilanguage captions
//           $path           / path to image (folder)
//           $module         / module id
//           $add_text       / additional text for thumbs;
//           $table1         / table with general information about images
//           $ids            / array of id to deletes
// Returns : $res / errors
// Description : delete files
// ================================================================================================
// Programmer : Oleg Morgalyuk
// Date : 05.07.2010
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================
    function DeleteImagesThumb($text_mess,$path,$module,$add_text,$table1,$ids= null)
    {
        $this->Err= NULL;
        if(!isset($ids)){
            $q="SELECT `id_position` FROM `$table1` where `id_module` = '".$module."' GROUP BY `id_position`";
            $res = UploadModel::$db->db_Query( $q);
            $rows = UploadModel::$db->db_GetNumRows();
            $ids  = array();
            for($i=0; $i<$rows;$i++)
            {
                $row = UploadModel::$db->db_FetchAssoc();
                $ids[] = $row['id_position'];
            }
        }
        $pos_count = count($ids);
        $cols_files = 0;
        for($i=0; $i<$pos_count;$i++)
        {
            $path = SITE_PATH.'/'.$path.'/'.$ids[$i];
//                   echo '<br> $path='.$path;
            $handle = @opendir($path);
            //       echo '<br> $handle='.$handle;
            if($handle){
           while ( ($file = readdir($handle)) !==false ) {
//               echo '<br> $file='.$file;
               $mas_file=explode(".",$file);
               //echo '<br>$mas_file[0]='.$mas_file[0].' $mas_file[1]='.$mas_file[1].' ADDITIONAL_FILES_TEXT='.ADDITIONAL_FILES_TEXT;
               if ( strstr($mas_file[0], $add_text) ) {
                  $res = unlink ($path.'/'.$file);
//                  echo '<br>$res='.$res;
                  if( !$res ) return false;
                  $cols_files++;
                  //echo '<br>$del='.$del;
               }
           }//end while
           closedir($handle);
         }//end if
       }
     return $this->Err;
     }  // end of function DeleteImagesThumb()
// ================================================================================================
// Function : DeleteImagesAll
// Version : 1.0.0
// Date : 05.07.2010
//
// Parms :   $text_mess       / array of multilanguage captions
//           $id              / id of file
//           $table1          / table to store general information
//           $table2          / table to store text information
//           $path            / path to file
// Returns : $res / errors
// Description : delete files
// ================================================================================================
// Programmer : Oleg Morgalyuk
// Date : 05.0..2010
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================
    function DeleteImagesAll($text_mess,$pos,$module,$table1,$table2,$path)
    {
        $this->Err= NULL;
        $q="SELECT `id` FROM `$table1` where `id_position` = '".$pos."' and `id_module` = '".$module."'";
        $res = UploadModel::$db->db_Query( $q);
        $rows = UploadModel::$db->db_GetNumRows();
        if($rows==0)
            return 0;
        $ids  = array();
        for($i=0; $i<$rows;$i++)
        {
            $row = UploadModel::$db->db_FetchAssoc();
            $ids[] = $row['id'];
        }
        $path = '/'.$path.'/'.$pos;
        $id = implode(',',$ids);
        $q="DELETE FROM `$table1` where `id` in (".$id.")";
        $res = UploadModel::$db->db_Query( $q);
//      echo '<br>q='.$q.' res='.$res.' UploadModel::$db->result='.UploadModel::$db->result;
        if( !UploadModel::$db->result ) $this->Err = $this->Err.$text_mess['MSG_DEL_INFO'].' ('.$path.')<br />';
        $q="DELETE FROM `$table2` where `cod` in (".$id.")";
        $res = UploadModel::$db->db_Query( $q);
//      echo '<br>q='.$q.' res='.$res.' UploadModel::$db->result='.UploadModel::$db->result;
        if (!UploadModel::$db->result) $this->Err = $this->Err.$text_mess['MSG_DEL_INFO'].'('.$path.')<br />';
        $path = SITE_PATH.$path;
//       echo '<br> $path='.$path;
       $handle = @opendir($path);
//       echo '<br> $handle='.$handle;
       $cols_files = 0;
       while ( ($file = readdir($handle)) !==false ) {
           //echo '<br> $file='.$file;
           if ($file == "." || $file == ".." ) {
               $cols_files++;
               continue;
           }
              $res = unlink ($path.'/'.$file);
              if( !$res ) $this->Err = $this->Err.$text_mess['MSG_DEL_'].'('.$path.'/'.$file.')<br />';
           if ($file == "." || $file == ".." ) {
               $cols_files++;
           }
       }
       //if ($cols_files==2) rmdir($path);
       closedir($handle);
       $res = rmdir($path);
       if( !$res ) $this->Err = $this->Err.$text_mess['MSG_DEL_'].'('.$path.')<br />';
     return $this->Err;
     }  // end of function DeleteImagesAll()
// ================================================================================================
// Function : DeleteFilesAll
// Version : 1.0.0
//
// Parms :   $text_mess       / array of multilanguage captions
//           $id              / id of file
//           $table1          / table to store general information
//           $table2          / table to store text information
//           $path            / path to file
// Returns : $res / errors
// Description : delete files
// ================================================================================================
// Programmer : Oleg Morgalyuk
// Date : 22.11.2011
// ================================================================================================
    function DeleteFilesAll($text_mess,$pos,$module,$table1,$table2,$path)
    {
        $this->Err= NULL;
        $q="SELECT `id` FROM `$table1` where `id_position` = '".$pos."' and `id_module` = '".$module."'";
        $res = UploadModel::$db->db_Query( $q);
        $rows = UploadModel::$db->db_GetNumRows();
        if($rows==0)
            return 0;
        $ids  = array();
        for($i=0; $i<$rows;$i++)
        {
            $row = UploadModel::$db->db_FetchAssoc();
            $ids[] = $row['id'];
        }
        $path = '/'.$path.'/'.$pos;
        $id = implode(',',$ids);
        $q="DELETE FROM `$table1` where `id` in (".$id.")";
        $res = UploadModel::$db->db_Query( $q);
//      echo '<br>q='.$q.' res='.$res.' UploadModel::$db->result='.UploadModel::$db->result;
        if( !UploadModel::$db->result ) $this->Err = $this->Err.$text_mess['MSG_DEL_INFO'].' ('.$path.')<br />';
        $q="DELETE FROM `$table2` where `cod` in (".$id.")";
        $res = UploadModel::$db->db_Query( $q);
//      echo '<br>q='.$q.' res='.$res.' UploadModel::$db->result='.UploadModel::$db->result;
        if (!UploadModel::$db->result) $this->Err = $this->Err.$text_mess['MSG_DEL_INFO'].'('.$path.')<br />';
        $path = SITE_PATH.$path;
//       echo '<br> $path='.$path;
       $handle = @opendir($path);
       //echo '<br> $handle='.$handle;
       if(!empty($handle)) { // Если еще существует директория
           $cols_files = 0;
           while ( ($file = readdir($handle)) !==false ) {
                //   echo '<br> $file='.$file;
               if ($file == "." || $file == ".." ) {
                   $cols_files++;
                   continue;
               }
                  $res = unlink ($path.'/'.$file);
                  if( !$res ) $this->Err = $this->Err.$text_mess['MSG_DEL_'].'('.$path.'/'.$file.')<br />';
               if ($file == "." || $file == ".." ) {
                   $cols_files++;
               }
           }
           //if ($cols_files==2) rmdir($path);
           closedir($handle);
           $res = rmdir( $path);
           if( !$res ) $this->Err = $this->Err.$text_mess['MSG_DEL_'].'('.$path.')<br />';
       }
     return $this->Err;
     }  // end of function DeleteFilesAll()
// ================================================================================================
// Function : GetFilesForPosition
// Version : 1.0.0
// Date : 03.07.2010
//
// Parms :   $position       / position to save
//           $module         / id of module
//           $table1         / table to store general information
//           $table2         / table to store text information
//           $lang           / id of language
//           $visible        / visible - 1, unvisible 0, NULL - show both
// Returns : $res / errors
// Description : get all files information
// ================================================================================================
// Programmer : Oleg MOrgalyuk
// Date : 03.07.2010
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================
    function GetFilesForPosition($position,$module,$table1,$table2,$lang=NULL,$visible=NULL)
    {
        $q = "SELECT * FROM `".$table1."` LEFT JOIN `".$table2."` ON (`".$table2."`.cod = `".$table1."`.id) where `id_position` = $position and `id_module` = $module";
        if(isset($lang)) $q .= " AND `lang_id`='$lang'";
        if(isset($visible)) $q .= " AND `visible`='$visible'";
        $q .= " ORDER BY `move` asc";
        $result = array();
        $res = UploadModel::$db->db_Query( $q);
        $rows = UploadModel::$db->db_GetNumRows();
        for($i=0; $i<$rows;$i++)
        {
           $row = UploadModel::$db->db_FetchAssoc();
           $id = $row['id'];
           $lang_id = $row['lang_id'];
           if(isset($result[$id]))
           {
               $result[$id]['name'][$lang_id] = $row['name'];
               $result[$id]['text'][$lang_id] = $row['text'];
           }
           else
           {
               $result[$id]['path'] = $row['path'];
               $result[$id]['visible'] = $row['visible'];
               $result[$id]['name'][$lang_id] = $row['name'];
               $result[$id]['text'][$lang_id] = $row['text'];
           }
        }
        return $result;
    }//GetFilesForPosition
// ================================================================================================
// Function : GetFilesCountForPosition
// Version : 1.0.0
// Date : 03.07.2010
//
// Parms :   $position       / position to save
//           $module         / id of module
//           $table1         / table to store general information
//           $table2         / table to store text information
//           $lang           / id of language
//           $visible        / visible - 1, unvisible 0, NULL - show both
// Returns : $res / errors
// Description : get all files information
// ================================================================================================
// Programmer : Oleg MOrgalyuk
// Date : 03.07.2010
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================
    function GetFilesCountForPosition($position,$module,$table1,$table2,$lang=NULL,$visible=NULL)
    {
        $q = "SELECT COUNT('id') as count,id_position FROM `".$table1."`";// LEFT JOIN `".$table2."` ON (`".$table2."`.cod = `".$table1."`.id) where  `id_module` = $module";
//        if(isset($lang)) $q .= " AND `lang_id`='$lang'";
        if(isset($visible)) $q .= " AND `visible`='$visible'";
        if(isset($position)) $q .= " AND `id_position` = '$position'";
        $q .= " GROUP BY `id_position` ORDER BY `move` asc";
//        echo $q;
        $res = UploadModel::$db->db_Query( $q);
        $rows = UploadModel::$db->db_GetNumRows();
        $row = UploadModel::$db->db_FetchAssoc();
        if(isset($position))
            return $row['count'];
        $result = array();
        $result[$row['id_position']] = $row['count'];
        for($i=1;$i<$rows;$i++){
            $row = UploadModel::$db->db_FetchAssoc();
            $result[$row['id_position']] = $row['count'];
        }
        return $result;
    }//GetFilesForPosition
// ================================================================================================
// Function : GetImagesForPosition
// Version : 1.0.0
// Date : 03.07.2010
//
// Parms :   $position       / position to save
//           $module         / id of module
//           $table1         / table to store general information
//           $table2         / table to store text information
//           $path           / path to images
//           $lang           / id of language
//           $visible        / visible - 1, unvisible 0, NULL - show both
// Returns : $res / Void
// Description : get all images information
// ================================================================================================
// Programmer : Oleg Morgalyuk
// Date : 03.07.2010
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================
    function GetImagesForPosition($position,$module,$table1,$table2,$path,$add_t,$lang=NULL,$visible=NULL)
    {
        $q = "SELECT * FROM `".$table1."` LEFT JOIN `".$table2."` ON (`".$table2."`.cod = `".$table1."`.id) where `id_position` = $position and `id_module` = $module";
        if(isset($lang)) $q .= " AND `lang_id`='$lang'";
        if(isset($visible)) $q .= " AND `visible`='$visible'";
        $q .= " ORDER BY `move` asc";
        $result = array();
        $res = UploadModel::$db->db_Query( $q);
        $rows = UploadModel::$db->db_GetNumRows();
        for($i=0; $i<$rows;$i++)
        {
           $row = UploadModel::$db->db_FetchAssoc();
           $id = $row['id'];
           $lang_id = $row['lang_id'];
           if(isset($result[$id]))
           {
               $result[$id]['name'][$lang_id] = $row['name'];
               $result[$id]['text'][$lang_id] = $row['text'];
           }
           else
           {
               $result[$id]['path'] = $this->GetImagePath('/'.$path.'/'.$position.'/'.$row['path'],'size_auto=150',$add_t,85);
               $result[$id]['path_original'] = $row['path'];
               $result[$id]['visible'] = $row['visible'];
               $result[$id]['name'][$lang_id] = $row['name'];
               $result[$id]['text'][$lang_id] = $row['text'];
           }
        }
        return $result;
    }//GetImagesForPosition
// ================================================================================================
// Function : GetImagesForPositionFront
// Version : 1.0.0
// Date : 03.07.2010
//
// Parms :   $position       / position to save
//           $module         / id of module
//           $table1         / table to store general information
//           $table2         / table to store text information
//           $path           / path to images
//           $lang           / id of language
//           $visible        / visible - 1, unvisible 0, NULL - show both
// Returns : $res / Void
// Description : get all images information
// ================================================================================================
// Programmer : Oleg Morgalyuk
// Date : 03.07.2010
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================
    function GetImagesForPositionFront($position,$module,$table1,$table2,$path,$add_t,$ex_size = false,$limit = NULL,$lang=NULL,$visible=NULL,
    $size = NULL,$height=NULL,$hor_align=true,$ver_align=true,$quality = 85, $wtm = NULL, $width2 = NULL, $height2 = NULL )
    {
	if($position==-1)
	    $q = "SELECT * FROM `".$table1."` LEFT JOIN `".$table2."` ON (`".$table2."`.cod = `".$table1."`.id) where `id_module` = $module";
	else
	    $q = "SELECT * FROM `".$table1."` LEFT JOIN `".$table2."` ON (`".$table2."`.cod = `".$table1."`.id) where `id_position` = $position and `id_module` = $module";
        if(isset($lang)) $q .= " AND `lang_id`='$lang'";
        if(isset($visible)) $q .= " AND `visible`='$visible'";

        $q .= " ORDER BY `move` asc";
        if(isset($limit)) $q .= " limit 0,$limit";
        $result = array();
        $res = UploadModel::$db->db_Query( $q);
        $rows = UploadModel::$db->db_GetNumRows();
        for($i=0; $i<$rows;$i++)
        {
           $row = UploadModel::$db->db_FetchAssoc();
           $id = $row['id'];
           $lang_id = $row['lang_id'];
           if(isset($result[$id]))
           {
               $result[$id]['name'][$lang_id] = $row['name'];
               $result[$id]['text'][$lang_id] = $row['text'];
           }
           else
           {
               if(!$ex_size)
                    $result[$id]['path'] = $this->GetImagePath('/'.$path.'/'.$row['id_position'].'/'.$row['path'],$size,$add_t,$quality);
               else
                    $result[$id]['path'] = $this->GetPathImageExSize('/'.$path.'/'.$row['id_position'].'/'.$row['path'],$size,$height,$add_t,$hor_align,$ver_align,$quality,$wtm);
               if(isset($width2)) {
                    if(!$ex_size)
                        $result[$id]['path2'] = $this->GetImagePath('/'.$path.'/'.$row['id_position'].'/'.$row['path'],$width2,$add_t,$quality);
                    else
                        $result[$id]['path2'] = $this->GetPathImageExSize('/'.$path.'/'.$row['id_position'].'/'.$row['path'],$width2,$height2,$add_t,$hor_align,$ver_align,$quality,$wtm);
               }
               $result[$id]['path_original'] = '/'.$path.'/'.$row['id_position'].'/'.$row['path'];
               $result[$id]['visible'] = $row['visible'];
               $result[$id]['name'][$lang_id] = $row['name'];
               $result[$id]['text'][$lang_id] = $row['text'];
           }
        }
        return $result;
    }//GetImagesForPositionFront
// ================================================================================================
// Function : GetFirstImagesForAllPosition
// Version : 1.0.0
// Date : 03.07.2010
//
// Parms :   $position       / position to save
//           $module         / id of module
//           $table1         / table to store general information
//           $table2         / table to store text information
//           $path           / path to images
//           $lang           / id of language
//           $visible        / visible - 1, unvisible 0, NULL - show both
// Returns : $res / Void
// Description : get all images information
// ================================================================================================
// Programmer : Oleg Morgalyuk
// Date : 03.07.2010
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================
    function GetFirstImagesForAllPosition($count=false,$module,$table1,$table2,$path,$add_t,$ex_size = false,$limit = NULL,$lang=NULL,$visible=NULL,
    $size = NULL,$height=NULL,$hor_align=true,$ver_align=true,$quality = 85, $wtm = NULL,$start=0 , $type=false)
    {
        $q = "SELECT *";
        if($count) $q .= ", COUNT(`".$table1."`.id) as count ";
        $q .= " FROM `".$table1."` LEFT JOIN `".$table2."` ON (`".$table2."`.cod = `".$table1."`.id)
            where `id_module` = $module";
        if(isset($lang)) $q .= " AND `lang_id`='$lang'";
        if(isset($visible)) $q .= " AND `visible`='$visible'";
        if((!$count) && (!$type)) $q .= " AND `move`='0'";
        if($count)
            $q .= " GROUP BY `id_position` ORDER by `move` asc";
        if($type) $q .= "ORDER by RAND() ";
        if(isset($limit)) $q .= " limit $start,$limit";

        $result = array();
//        echo $q;
        $res = UploadModel::$db->db_Query( $q);
        $rows = UploadModel::$db->db_GetNumRows();
        for($i=0; $i<$rows;$i++)
        {
           $row = UploadModel::$db->db_FetchAssoc();
           $id = $row['id'];
           $posId = $row['id_position'];
           $lang_id = $row['lang_id'];
           if($count)
                $result[$posId]['count'] = $row['count'];
               $result[$posId]['name'][$lang_id] = $row['name'];
               $result[$posId]['text'][$lang_id] = $row['text'];
               if(!$ex_size)
                    $result[$posId]['path'] = $this->GetImagePath('/'.$path.'/'.$posId.'/'.$row['path'],$size,$add_t,$quality);
               else
                    $result[$posId]['path'] = $this->GetPathImageExSize('/'.$path.'/'.$posId.'/'.$row['path'],$size,$height,$add_t,$hor_align,$ver_align,$quality,$wtm);
               $result[$posId]['path_original'] = '/'.$path.'/'.$posId.'/'.$row['path'];
               $result[$posId]['visible'] = $row['visible'];
               $result[$posId]['name'][$lang_id] = htmlspecialchars($row['name']);
               $result[$posId]['text'][$lang_id] = htmlspecialchars($row['text']);
        }
        return $result;
    }//GetFirstImagesForAllPosition

// ================================================================================================
// Function : GetFirstImagesForAllPosition
// Version : 1.0.0
// Date : 03.07.2010
//
// Parms :   $position       / position to save
//           $module         / id of module
//           $table1         / table to store general information
//           $table2         / table to store text information
//           $path           / path to images
//           $lang           / id of language
//           $visible        / visible - 1, unvisible 0, NULL - show both
// Returns : $res / Void
// Description : get all images information
// ================================================================================================
// Programmer : Oleg Morgalyuk
// Date : 03.07.2010
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================
    function GetFirstImagesForCurrentPosition($page_id=NULL,$module,$table1,$table2,$path,$add_t,$ex_size = false,$limit = NULL,$lang=NULL,$visible=NULL,
    $size = NULL,$height=NULL,$hor_align=true,$ver_align=true,$quality = 85, $wtm = NULL,$start=0 , $type=false)
    {
        $q = "SELECT *";
        $q .= " FROM `".$table1."` LEFT JOIN `".$table2."` ON (`".$table2."`.cod = `".$table1."`.id)
            where `id_module` = $module";
        if(isset($page_id)) $q .= " AND `id_position`='$page_id'";
        if(isset($lang)) $q .= " AND `lang_id`='$lang'";
        if(isset($visible)) $q .= " AND `visible`='$visible'";
        if($type) $q .= "ORDER by RAND() ";
        if(isset($limit)) $q .= " limit $start,$limit";

        $result = array();
//        echo $q;
        $res = UploadModel::$db->db_Query( $q);
        $rows = UploadModel::$db->db_GetNumRows();
        for($i=0; $i<$rows;$i++)
        {
           $row = UploadModel::$db->db_FetchAssoc();
           $id = $row['id'];
           $posId = $row['id_position'];
           $lang_id = $row['lang_id'];
               $result[$posId]['name'][$lang_id] = $row['name'];
               $result[$posId]['text'][$lang_id] = $row['text'];
               if(!$ex_size)
                    $result[$posId]['path'] = $this->GetImagePath('/'.$path.'/'.$posId.'/'.$row['path'],$size,$add_t,$quality);
               else
                    $result[$posId]['path'] = $this->GetPathImageExSize('/'.$path.'/'.$posId.'/'.$row['path'],$size,$height,$add_t,$hor_align,$ver_align,$quality,$wtm);
               $result[$posId]['path_original'] = '/'.$path.'/'.$posId.'/'.$row['path'];
               $result[$posId]['visible'] = $row['visible'];
               $result[$posId]['name'][$lang_id] = htmlspecialchars($row['name']);
               $result[$posId]['text'][$lang_id] = htmlspecialchars($row['text']);
        }
        return $result;
    }//GetFirstImagesForAllPosition

// ================================================================================================
// Function : GetImagePath
// Version : 1.0.0
// Date : 05.07.2010
//
// Parms :  $img - path of the picture,
//          $size - size type and exact size
//          $quality  - quality of copies
//          $wtm - watemark
//          $add_text - text, which used to amke copies
//          $parameters - other parameters for TAG <img> like border
// Returns : $path to picture
// Description : Show images by path
// ================================================================================================
// Programmer : Oleg Morgalyuk
// Date : 05.07.2010
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================
    function GetImagePath($img = NULL, $size = NULL,$add_text=NULL, $quality = NULL, $wtm = NULL)
    {
        $size_auto = NULL;
        $size_width = NULL;
        $size_height = NULL;
        $rpos = strrpos($img,'/');
        $settings_img_path = substr($img, 0, $rpos);
        $img_name = substr($img, $rpos+1, strlen($img)-$rpos );
        $img_with_path = $img;
        $mas_img_name=explode(".",$img_with_path);
         if ( strstr($size,'size_width') ){
            $size_width = substr( $size, strrpos($size,'=')+1, strlen($size) );
            $img_name_new = $mas_img_name[0].$add_text.'width_'.$size_width.'.'.$mas_img_name[1];
         }
         elseif ( strstr($size,'size_auto') ) {
            $size_auto = substr( $size, strrpos($size,'=')+1, strlen($size) );
            $img_name_new = $mas_img_name[0].$add_text.'auto_'.$size_auto.'.'.$mas_img_name[1];
         }
         elseif ( strstr($size,'size_height') ) {
            $size_height = substr( $size, strrpos($size,'=')+1, strlen($size) );
            $img_name_new = $mas_img_name[0].$add_text.'height_'.$size_height.'.'.$mas_img_name[1];
         }
         elseif(empty($size)) $img_name_new = $mas_img_name[0].'.'.$mas_img_name[1];
         //echo '$img_name_new='.$img_name_new;
         $img_full_path_new = SITE_PATH.$img_name_new;
         //if exist local small version of the image then use it
         if( file_exists($img_full_path_new)){
            //echo 'exist';
            //echo '<br>$settings_img_path='.$settings_img_path.' $img_full_path='.$img_full_path;
            return $img_name_new;
         }
        //else use original image on the server SITE_PATH and make small version on local server
        else {
            //echo 'Not  exist';
            $img_full_path = SITE_PATH.$img_with_path; // like z:/home/speakers/www/uploads/45/R1800TII_big.jpg
            //echo '<br> $img_full_path='.$img_full_path.'<br> $size_auto='.$size_auto;
            if ( !file_exists($img_full_path) ) return false;
            $thumb = new Thumbnail($img_full_path);
            //echo '<br>$thumb->img[x_thumb]='.$thumb->img['x_thumb'].' $thumb->img[y_thumb]='.$thumb->img['y_thumb'];
            $src_x = $thumb->img['x_thumb'];
            $src_y = $thumb->img['y_thumb'];
            if ( !empty($size_width ) and empty($size_height) ) $thumb->size_width($size_width);
            if ( !empty($size_height) and empty($size_width) ) $thumb->size_height($size_height);
            if ( !empty($size_width) and !empty($size_height) ) $thumb->size($size_width,$size_height);
            if ( !$size_width and !$size_height and $size_auto ) $thumb->size_auto($size_auto);                    // [OPTIONAL] set the biggest width and height for thumbnail
            //echo '<br>$thumb->img[x_thumb]='.$thumb->img['x_thumb'].' $thumb->img[y_thumb]='.$thumb->img['y_thumb'];
            //if original image smaller than thumbnail then use original image and don't create thumbnail
            if($thumb->img['x_thumb']>=$src_x OR $thumb->img['y_thumb']>=$src_y){
                $img_full_path = $settings_img_path.'/'.$img_name;
                //echo '<br>$settings_img_path='.$settings_img_path.' $img_full_path='.$img_full_path;
                return $img_full_path;
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
                    if ( defined('WATERMARK_TEXT') ) $thumb->txt_watermark=WATERMARK_TEXT;        // [OPTIONAL] set watermark text [RECOMENDED ONLY WITH GD 2 ]
                    else $thumb->txt_watermark='';
                    $thumb->txt_watermark_color='000000';        // [OPTIONAL] set watermark text color , RGB Hexadecimal[RECOMENDED ONLY WITH GD 2 ]
                    $thumb->txt_watermark_font=5;                // [OPTIONAL] set watermark text font: 1,2,3,4,5
                    $thumb->txt_watermark_Valing='TOP';           // [OPTIONAL] set watermark text vertical position, TOP | CENTER | BOTTOM
                    $thumb->txt_watermark_Haling='LEFT';       // [OPTIONAL] set watermark text horizonatal position, LEFT | CENTER | RIGHT
                    $thumb->txt_watermark_Hmargin=10;          // [OPTIONAL] set watermark text horizonatal margin in pixels
                    $thumb->txt_watermark_Vmargin=10;           // [OPTIONAL] set watermark text vertical margin in pixels
                }

                    $mas_img_name=explode(".",$img_with_path);
                    //$img_name_new = $mas_img_name[0].ADDITIONAL_FILES_TEXT.intval($thumb->img['x_thumb']).'x'.intval($thumb->img['y_thumb']).'.'.$mas_img_name[1];
                    $img_src = $img_name_new;
                    $uploaddir = substr($img_with_path, 0, strrpos($img_with_path,'/'));
                //echo '<br>$img_src='.$img_src;
                //echo '<br>$uploaddir='.$uploaddir;
                $uploaddir = SITE_PATH.$uploaddir;
                //echo '<br>SITE_PATH='.SITE_PATH;
//                echo '<br>$uploaddir='.$uploaddir;
                if ( !file_exists($img_full_path_new) ) {
                    if( file_exists ($uploaddir) )
                        @chmod($uploaddir,0777);
                    else
                        mkdir($uploaddir,0777);
                    $thumb->process();       // generate image
                    //make new image like R1800TII_big.jpg -> R1800TII_big_autozoom_100x84.jpg
                    $thumb->save($img_full_path_new);
                    @chmod($uploaddir,0755);
                    $params = "img=".$img."&".$size;
                }
                return $img_src;
            }//end else
        }//end else
     return $img_src;
    } // end of function GetImagePath()
// ================================================================================================
// Function : GetPathImageExSize
// Version : 1.0.0
// Date : 04.07.2010
//
// Parms :  $img - path of the picture
//          $add_text - text, which used in thumbs names
// Returns : $path
// Description : get picture of exact size by path
// ================================================================================================
// Programmer : Oleg Morgalyuk
// Date : 07.09.2009
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================
    function GetPathImageExSize($img = NULL, $width = NULL,$height=NULL,$add_text=NULL,$hor_align=true,$ver_align=true,$quality = 85, $wtm = NULL)
    {
            //$settings_img_path = $settings['img_path'].'/categories';
            $rpos = strrpos($img,'/');
            $settings_img_path = substr($img, 0, $rpos);
            $img_name = substr($img, $rpos+1, strlen($img)-$rpos );
            $img_with_path = $img;
        //echo '<br>SITE_PATH.$settings_img_path='.SITE_PATH.$settings_img_path;
        //echo '<br>$img_with_path='.$img_with_path;
        //echo '<br>$img_name='.$img_name;

         $mas_img_name=explode(".",$img_with_path);
         if ( isset($width) && isset($height)){
            $img_name_new = $mas_img_name[0].$add_text.$width.'x'.$height.'.'.$mas_img_name[1];
         }
         elseif(empty($size)) $img_name_new = $mas_img_name[0].'.'.$mas_img_name[1];
         //echo '$img_name_new='.$img_name_new;
         $img_full_path_new = SITE_PATH.$img_name_new;
         //if exist local small version of the image then use it
         if( file_exists($img_full_path_new)){
            //echo 'exist';
            //echo '<br>$settings_img_path='.$settings_img_path.' $img_full_path='.$img_full_path;
            return $img_name_new;
         }
        //else use original image on the server SITE_PATH and make small version on local server
        else {
            //echo 'Not  exist';
            $img_full_path = SITE_PATH.$img_with_path; // like z:/home/speakers/www/uploads/45/R1800TII_big.jpg
            //echo '<br> $img_full_path='.$img_full_path.'<br> $size_auto='.$size_auto;
            if ( !file_exists($img_full_path) ) return false;

            $thumb = new Thumbnail($img_full_path);
//            echo '<br>$thumb->img[x_thumb]='.$thumb->img['x_thumb'].' $thumb->img[y_thumb]='.$thumb->img['y_thumb'];
            $src_x = $thumb->img['x_thumb'];
            $src_y = $thumb->img['y_thumb'];
            if ( !empty($width) and !empty($height) ) $thumb->sizeEx($width,$height);
            //echo '<br>$thumb->img[x_thumb]='.$thumb->img['x_thumb'].' $thumb->img[y_thumb]='.$thumb->img['y_thumb'];

            //if original image smaller than thumbnail then use original image and don't create thumbnail
            if(($thumb->img['x_thumb']>=$src_x) && ($thumb->img['y_thumb']>=$src_y)){
                $img_full_path = $settings_img_path.'/'.$img_name;
                //echo '<br>$settings_img_path='.$settings_img_path.' $img_full_path='.$img_full_path;
                return $img_full_path;
            }
            else{
                $thumb->quality=$quality;                  //default 75 , only for JPG format
                 if($thumb->img['x_thumb']>=$src_x AND $thumb->img['y_thumb']<=$src_y){
                     $this->img['x_thumb'] = $src_x;
                     $width = $src_x;
                 }
                if($thumb->img['x_thumb']<=$src_x AND $thumb->img['y_thumb']>=$src_y){
                     $this->img['y_thumb'] = $src_y;
                     $height = $src_y;
                 }
                //echo '<br>$wtm='.$wtm;
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


                    $img_src = $img_name_new;
                    $uploaddir = substr($img_with_path, 0, strrpos($img_with_path,'/'));
                //echo '<br>$img_name_new='.$img_name_new;
                //echo '<br>$img_full_path_new='.$img_full_path_new;
                //echo '<br>$img_src='.$img_src;
                //echo '<br>$uploaddir='.$uploaddir;
                $uploaddir = SITE_PATH.$uploaddir;
//                echo '<br>$uploaddir='.$uploaddir;
                if ( !file_exists($img_full_path_new) ) {
                    if( !file_exists ($uploaddir) ) mkdir($uploaddir,0777);
                    if( file_exists($uploaddir) ) @chmod($uploaddir,0777);
                    $thumb->processEx($ver_align,$hor_align);       // generate image
                    //make new image like R1800TII_big.jpg -> R1800TII_big_autozoom_100x84.jpg
                    // ($border) $thumb->saveEx($img_full_path_new);
                    //else
                    $thumb->save($img_full_path_new);
                    @chmod($uploaddir,0755);
                    $params = "img=".$img."&".$width;
                }
                 return $img_src;
            }//end else
        }//end else
     return $img_src;
    } // end of function GetPathImageExSize()
 // ================================================================================================
// Function : makeAngle
// Version : 1.0.0
// Date : 22.03.2010
//
// Parms :  $img1 - relative path to picture
//          $angles - array of angles
// Returns : $res / Void
// Description : Make png picture with roundborders
// ================================================================================================
// Programmer : Oleg Morgalyuk
// Date : 28.03.2010
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================

    function makeAngle($img1, $radius=5, $rate=5, $angles = '1;1;1;1')
    {
    $img_src = explode(".",$img1);
    $img_new = $img_src[0].'.png';
    $img_new_path = SITE_PATH.$img_new;
    if ( !file_exists($img_new_path) ) {
        $img = ImageCreateFromJPEG (SITE_PATH.$img1);

     $width = imagesx($img);
     $height = imagesy($img);
     imagealphablending($img, false);
     imagesavealpha($img, true);
     $rs_radius = $radius * $rate;
     $rs_size = $rs_radius * 2;
     $corner = imagecreatetruecolor($rs_size, $rs_size);
     imagealphablending($corner, false);
     $trans = imagecolorallocatealpha($corner, 255, 255, 255, 127);
     imagefill($corner, 0, 0, $trans);
     $angle = explode(";",$angles);
     if(isset($angle[0])&&($angle[0]==1)) $positions[] = array(0, 0, 0, 0);
     if(isset($angle[1])&&($angle[1]==1)) $positions[] = array($rs_radius, 0, $width - $radius, 0);
     if(isset($angle[2])&&($angle[2]==1)) $positions[] = array($rs_radius, $rs_radius, $width - $radius, $height - $radius);
     if(isset($angle[3])&&($angle[3]==1)) $positions[] = array(0, $rs_radius, 0, $height - $radius);
        foreach ($positions as $pos) {
            imagecopyresampled($corner, $img, $pos[0], $pos[1], $pos[2], $pos[3], $rs_radius, $rs_radius, $radius, $radius);
        }

        $lx = $ly = 0;
        $i = -$rs_radius;
        $y2 = -$i;
        $r_2 = $rs_radius * $rs_radius;

    for (; $i <= $y2; $i++) {

        $y = $i;
        $x = sqrt($r_2 - $y * $y);

        $y += $rs_radius;
        $x += $rs_radius;

        imageline($corner, $x, $y, $rs_size, $y, $trans);
        imageline($corner, 0, $y, $rs_size - $x, $y, $trans);

        $lx = $x;
        $ly = $y;
    }

    foreach ($positions as $i => $pos) {
        imagecopyresampled($img, $corner, $pos[2], $pos[3], $pos[0], $pos[1], $radius, $radius, $rs_radius, $rs_radius);
    }
    imagePng($img,$img_new_path);
    imagedestroy($corner);
    }
    return $img_new;
 } //makeAngle
}
?>
