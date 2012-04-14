<?php
// ================================================================================================
// Module : uploadImage.class.php
// Version : 1.0.0
// Date : 04.07.2010
// Licensed To:
// Oleg Morgalyuk oleg@seotm.com
//
// Purpose : Class definition(controller) for working with images
//
// ================================================================================================

// ================================================================================================
//
//    Programmer        :  Oleg Morgalyuk
//    Date              :  25.06.2010
//    Reason for change :  Creation
//    Change Request Nbr:
//
//    Function          :  Class definition(controller) for working with images 
//
//  ================================================================================================

include_once( SITE_PATH.'/admin/include/defines.inc.php' );
include_once( SITE_PATH.'/sys/classes/upload/upload.view.php' ); 
include_once( SITE_PATH.'/sys/classes/upload/upload.model.php' ); 
class UploadImage extends UploadClass {
    var $valid_types = NULL;
    var $max_image_height = 800;
    var $max_image_width = 1024;
    var $add_text = '_zoom_';
    var $quality = 85;
    // ================================================================================================
    //    Function          : UploadImage (Constructor)
    //    Version           : 1.0.0
    //    Date              : 25.06.2010
    //    Parms             : $module    / module
    //                      : $position  / position
    //                      : $path      / path to uploads files
    //                      : $table     / save general information about files to upload
    //                      : $table_spr / save name and desription of files to upload
    //                      : $max_items / max items, which can be upload to each positions
    //    Returns           : none
    //
    //    Description       : Set the variabels
    // ================================================================================================

    function UploadImage($module, $position = NULL, $path, $table, $table_spr = NULL, $max_items = NULL,$lang = NULL,$max_image_height= NULL,$max_image_width= NULL){
        parent::UploadClass($module,$position,$path,$table,$table_spr,$max_items,$lang);
        if(isset($max_image_width)) $this->max_image_width = $max_image_width;
        if(isset($max_image_height)) $this->max_image_height = $max_image_height;
        $this->valid_types =  array("gif", "GIF", "jpg", "JPG", "png", "PNG", "jpeg", "JPEG");
        $this->type = 'I';
    }

// ================================================================================================
// Function : ShowFormToUpload
// Version : 1.0.0
// Date : 29.06.2010
//
// Parms :   $max_files / max items, which can be upload to one positions
//           $position  / position
// Returns : $res / Void
// Description : Show form to upload or change files fo position
// ================================================================================================
// Programmer : Oleg MOrgalyuk
// Date : 29.06.2010
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================    
    
    function ShowFormToUpload($max_files = NULL,$position = NULL,$formId=1){
        if(is_null($position))
            if(!is_null($this->position)) $position = $this->position;

        UploadView::ImageUploadTop($this->multi_lang);
        $model =  new UploadModel();
        $array = $model->GetImagesForPosition($position,$this->module,$this->table,$this->table_spr,$this->path,$this->add_text);
        $count = count ($array);
        UploadView::UploadedImages($this->lang,$array,$this->multi_lang,$this->path,$position,$this->table,$this->table_spr,$formId);
        if(!isset($max_files)) $max_files = $this->max_items;
        $max_files -= $count;
        if($max_files<$this->empty_fields) $this->empty_fields = $max_files;
        UploadView::Uploadform($this->lang,$this->empty_fields,$this->multi_lang,$max_files,$count,$this->type,$formId);
        UploadView::ImageUploadBot();
    }//ShowFormToUpload
// ================================================================================================
// Function : SaveImages
// Version : 1.0.0
// Date : 04.07.2010
//
// Parms :   $id  / id of position 
// Returns : $res / Void
// Description : Save images
// ================================================================================================
// Programmer : Oleg Morgalyuk
// Date : 04.07.2010
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================    
    function SaveImages($id,$formId=1)
    {
         $model =  new UploadModel();
         $res = $model->SaveImages($this->max_file_size,$this->multi_lang,$this->path,$id,$this->table,$this->table_spr,$this->module,$this->lang,$this->save_file_names,$this->valid_types,$this->max_image_width,$this->max_image_height,$formId);
         if(!empty($res)) 
            UploadView::ShowError($res);
         
         $res1 = $model->UpdateFiles($this->multi_lang,$id,$this->table,$this->table_spr,$this->module,$this->lang,$this->type,$formId);
         if(!empty($res1)) 
            UploadView::ShowError($res1);
    }//SaveImages

// ================================================================================================
// Function : DeleteImages
// Version : 1.0.0
// Date : 05.07.2010
//
// Parms :   $id  / id in table 
//           $img  / image name 
//           $path  / path to file 
// Returns : $res / Void
// Description : delete images and information about the image (one only)
// ================================================================================================
// Programmer : Oleg Morgalyuk
// Date : 05.07.2010
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================       
    function DeleteImages($id,$img,$path)
    {
         $model =  new UploadModel();
         $res = $model->DeleteImages($this->multi_lang,$id,$this->table,$this->table_spr,$img,$path);
         if(!empty($res)) 
            UploadView::ShowError($res);
         else
            UploadView::ShowMessage($this->multi_lang['DEL_OK']);
    }//DeleteImages
// ================================================================================================
// Function : DeleteImagesThumbs
// Version : 1.0.0
// Date : 05.07.2010
//
// Parms :   $path  / path to file 
// Returns : $res / Void
// Description : delete image thumbs
// ================================================================================================
// Programmer : Oleg Morgalyuk
// Date : 05.07.2010
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================       
    function DeleteImagesThumbs()
    {
         $model =  new UploadModel();
         $res = $model->DeleteImagesThumb($this->multi_lang,$this->path,$this->module,$this->add_text,$this->table);
         if(!empty($res)) 
            UploadView::ShowError($res);
         else
            UploadView::ShowMessage($this->multi_lang['DEL_OK']);
    }//DeleteImagesThumbs

    // ================================================================================================
// Function : DeleteAllImagesForPosition
// Version : 1.0.0
// Date : 05.07.2010
//
// Parms :   $id_pos     / position id 
//           $module_id  / module id 
//           $path  / path to file 
// Returns : $res / Void
// Description : delete all images for position
// ================================================================================================
// Programmer : Oleg Morgalyuk
// Date : 05.07.2010
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================       
    function DeleteAllImagesForPosition($pos,$module = NULL)
    {
         $model =  new UploadModel();
          if(!isset($module)) 
            $module = $this->module;
         $res = $model->DeleteImagesAll($this->multi_lang,$pos,$module,$this->table,$this->table_spr,$this->path);
         if(!empty($res)) 
            UploadView::ShowError($res);
         elseif($res!=0)
            UploadView::ShowMessage($this->multi_lang['DEL_OK']);
    }//DeleteAllImagesForPosition

// ================================================================================================
// Function : ShowMainPicture
// Version : 1.0.0
// Date : 05.07.2010
//
// Parms :   $id_pos     / position id 
//           $module_id  / module id 
//           $path  / path to file 
// Returns : $res / Void
// Description : delete all images for position
// ================================================================================================
// Programmer : Oleg Morgalyuk
// Date : 05.07.2010
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================           
    function ShowMainPicture($idPosition,$lang,$size = NULL, $quality = NULL, $wtm = NULL ) {
         $model =  new UploadModel(); 
         $array = $model->GetImagesForPositionFront($idPosition,$this->module,$this->table,$this->table_spr,$this->path,$this->add_text,false,1,$lang,1,$size,NULL,NULL,NULL,$quality,$wtm);
         if(count($array)>0) 
             UploadView::ShowSingleImage($array, $this->multi_lang, $lang);
         else
            return false;
         return true;
    }
// ================================================================================================
// Function : GetMainPictureOfPositions
// Version : 1.0.0
// Date : 05.07.2010
//
// Parms :   $id_pos     / position id 
//           $module_id  / module id 
//           $path  / path to file 
// Returns : $res / Void
// Description : delete all images for position
// ================================================================================================
// Programmer : Oleg Morgalyuk
// Date : 05.07.2010
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================           
    function GetMainPictureOfPositions($lang,$size = NULL, $quality = NULL, $wtm = NULL ) {
         $model =  new UploadModel(); 
         $array = $model->GetImagesForPositionFront($idPosition,$this->module,$this->table,$this->table_spr,$this->path,$this->add_text,false,1,$lang,1,$size,NULL,NULL,NULL,$quality,$wtm);
         if(count($array)>0) 
             UploadView::ShowSingleImage($array, $this->multi_lang, $lang);
         else
            return false;
         return true;
    }
// ================================================================================================
// Function : GetPictureInArray
// Version : 1.0.0
// Date : 05.07.2010
//
// Parms :   $id_pos     / position id 
//           $module_id  / module id 
//           $path  / path to file 
// Returns : $res / Void
// Description : delete all images for position
// ================================================================================================
// Programmer : Oleg Morgalyuk
// Date : 05.07.2010
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================           
    function GetPictureInArray($idPosition,$lang,$size = NULL, $quality = NULL, $limit = NULL, $wtm = NULL, $size2 = NULL) {
         $model =  new UploadModel(); 
         $array = $model->GetImagesForPositionFront($idPosition,$this->module,$this->table,$this->table_spr,$this->path,$this->add_text,false,$limit,$lang,1,$size,NULL,NULL,NULL,$quality,$wtm,$size2);
         return $array;
    }
// ================================================================================================
// Function : GetPictureInArray
// Version : 1.0.0
// Date : 05.07.2010
//
// Parms :   $id_pos     / position id 
//           $module_id  / module id 
//           $path  / path to file 
// Returns : $res / Void
// Description : delete all images for position
// ================================================================================================
// Programmer : Oleg Morgalyuk
// Date : 05.07.2010
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================           
    function GetFirstPictureInArray($count,$lang,$size = NULL, $quality = NULL, $limit = NULL, $wtm = NULL ) {
         $model =  new UploadModel(); 
         $array = $model->GetFirstImagesForAllPosition($count,$this->module,$this->table,$this->table_spr,$this->path,$this->add_text,false,$limit,$lang,1,$size,NULL,NULL,NULL,$quality,$wtm);
         return $array;
    }

// ================================================================================================
// Function : GetPictureInArray
// Version : 1.0.0
// Date : 05.07.2010
//
// Parms :   $id_pos     / position id 
//           $module_id  / module id 
//           $path  / path to file 
// Returns : $res / Void
// Description : delete all images for position
// Programmer : Oleg Morgalyuk
// ================================================================================================           
    function GetFirstRandomPictureInArray($lang,$size = NULL,  $wtm = NULL ) {
         $model =  new UploadModel(); 
         $array = $model->GetFirstImagesForAllPosition(false,$this->module,$this->table,$this->table_spr,$this->path,$this->add_text,false,1,$lang,1,$size,NULL,NULL,NULL,85,$wtm,0,true);
         return $array;
    }

// ================================================================================================
// Function : GetPictureInArray
// Version : 1.0.0
// Date : 05.07.2010
//
// Parms :   $id_pos     / position id 
//           $module_id  / module id 
//           $path  / path to file 
// Returns : $res / Void
// Description : delete all images for position
// Programmer : Oleg Morgalyuk
// ================================================================================================           
    function GetFirstRandomPicture($page_id,$lang,$size = NULL,  $wtm = NULL ) {
         $model =  new UploadModel(); 
         $array = $model->GetFirstImagesForCurrentPosition($page_id,$this->module,$this->table,$this->table_spr,$this->path,$this->add_text,false,1,$lang,1,$size,NULL,NULL,NULL,85,$wtm,0,true);
         return $array;
    }                            
// ================================================================================================
// Function : GetPictureInArrayExSize
// Version : 1.0.0
// Date : 05.07.2010
//
// Parms :   $id_pos     / position id 
//           $module_id  / module id 
//           $path  / path to file 
// Returns : $res / Void
// Description : delete all images for position
// ================================================================================================
// Programmer : Oleg Morgalyuk
// Date : 05.07.2010
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================           
    function GetFirstPictureInArrayExSize($count,$lang,$width = NULL,$height=NULL,$hor_align=true,$ver_align=true, $quality = NULL, $wtm = NULL,$visible = 1, $limit = NULL ) {
         $model =  new UploadModel(); 
         $array = $model->GetFirstImagesForAllPosition($count,$this->module,$this->table,$this->table_spr,$this->path,$this->add_text,true,$limit,$lang,$visible,$width,$height,$hor_align,$ver_align,$quality,$wtm);
         return $array;
    }
// ================================================================================================
// Function : ShowMainPictureExSize
// Version : 1.0.0
// Date : 05.07.2010
//
// Parms :   $id_pos     / position id 
//           $module_id  / module id 
//           $path  / path to file 
// Returns : $res / Void
// Description : delete all images for position
// ================================================================================================
// Programmer : Oleg Morgalyuk
// Date : 05.07.2010
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================           
    function ShowMainPictureExSize($idPosition,$lang,$width = NULL,$height=NULL,$hor_align=true,$ver_align=true, $quality = NULL, $wtm = NULL,$ex_size= false ) {
         $model =  new UploadModel(); 
         $array = $model->GetImagesForPositionFront($idPosition,$this->module,$this->table,$this->table_spr,$this->path,$this->add_text,true,1,$lang,1,$width,$height,$hor_align,$ver_align,$quality,$wtm);
         return $array;
    }
// ================================================================================================
// Function : GetPictureInArrayExSize
// Version : 1.0.0
// Date : 05.07.2010
//
// Parms :   $id_pos     / position id 
//           $module_id  / module id 
//           $path  / path to file 
// Returns : $res / Void
// Description : delete all images for position
// ================================================================================================
// Programmer : Oleg Morgalyuk
// Date : 05.07.2010
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================           
    function GetPictureInArrayExSize($idPosition,$lang,$limit=1,$width = NULL,$height=NULL,$hor_align=true,$ver_align=true, $quality = NULL, $wtm = NULL,$width2 = NULL, $height2= NULL ) {
         $model =  new UploadModel(); 
         $array = $model->GetImagesForPositionFront($idPosition,$this->module,$this->table,$this->table_spr,$this->path,$this->add_text,true,$limit,$lang,1,$width,$height,$hor_align,$ver_align,$quality,$wtm,$width2, $height2);
         return $array;
    }
// ================================================================================================
// Function : GetImagesCount
// Version : 1.0.0
// Date : 05.07.2010
//
// Parms :   $id_pos     / position id 
//           $module_id  / module id 
// Returns : $res / Void
// Description : 
// Programmer : Oleg Morgalyuk
// ================================================================================================       
    function GetImagesCount ($position, $lang=_LANG_ID) {
        $model =  new UploadModel();
        $array =  $model->GetImagesForPosition($position,$this->module,$this->table,$this->table_spr, $this->path,$this->add_text,$lang);
        UploadView::ShowImagesCount($array, $this->multi_lang );
    }   // End of function GetImagesCount();
}
?>