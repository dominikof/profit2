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
//    Date              :  05.07.2010
//    Reason for change :  Creation
//    Change Request Nbr:
//
//    Function          :  Class definition(controller) for working with videos 
//
//  ================================================================================================

include_once( SITE_PATH.'/admin/include/defines.inc.php' );
include_once( SITE_PATH.'/sys/classes/upload/upload.view.php' ); 
include_once( SITE_PATH.'/sys/classes/upload/upload.model.php' ); 
class UploadVideo extends UploadClass {
    var $valid_types = NULL;
    // ================================================================================================
    //    Function          : UploadVideo (Constructor)
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

    function UploadVideo($module,$position = NULL,$path,$table,$table_spr = NULL,$max_items = NULL,$lang = NULL,$max_image_height= NULL,$max_image_width= NULL){
        parent::UploadClass($module,$position,$path,$table,$table_spr,$max_items,$lang);
        $this->valid_types =  array("flv");
        $this->type = 'V';
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
    
    function ShowFormToUpload($max_files = NULL,$position = NULL){
        if(is_null($position))
            if(!is_null($this->position)) $position = $this->position;

        UploadView::VideoUploadTop($this->multi_lang);
        $model =  new UploadModel();
        $array = $model->GetFilesForPosition($position,$this->module,$this->table,$this->table_spr);
        $count = count ($array);
        UploadView::UploadedVideos($this->lang,$array,$this->multi_lang,$this->path,$position,$this->table,$this->table_spr);
        if(!isset($max_files)) $max_files = $this->max_items;
        $max_files -= $count;
        UploadView::Uploadform($this->lang,$this->empty_fields,$this->multi_lang,$max_files,$count,$this->type);
        UploadView::VideoUploadBot();
    }//ShowFormToUpload
// ================================================================================================
// Function : SaveVideos
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
    function SaveVideos($id)
    {
         $model =  new UploadModel();
         
         $res = $model->SaveVideos($this->max_file_size,$this->multi_lang,$this->path,$id,$this->table,$this->table_spr,$this->module,$this->lang,$this->save_file_names,$this->valid_types);
         if(!empty($res)) 
            UploadView::ShowError($res);
         
         $res1 = $model->UpdateFiles($this->multi_lang,$id,$this->table,$this->table_spr,$this->module,$this->lang,$this->type);
         if(!empty($res1)) 
            UploadView::ShowError($res1);
    }//SaveImages
    
    
// ================================================================================================
// Function : GetListOfVideoFrontend
// Date : 02.07.2010
// Parms :   $position  / item position 
//           $lang      / id of language 
// Returns : $res       / Void
// Description : Show list of files in frontend
// Programmer : Yaroslav Gyryn
// ================================================================================================ 
    function GetListOfVideoFrontend($position,$lang = _LANG_ID){
        $model =  new UploadModel();
        return  $model->GetFilesForPosition($position,$this->module,$this->table,$this->table_spr,$lang,1);
    }//GetListOfVideoFrontend
        
// ================================================================================================
// Function : ShowListOfVideoFrontend
// Date : 02.07.2010
// Parms :   $position  / item position 
//           $lang      / id of language 
// Returns : $res       / Void
// Description : Show list of video files in frontend
// Programmer : Yaroslav Gyryn
// ================================================================================================ 
    function ShowListOfVideoFrontend($array, $position,$lang = _LANG_ID){
        UploadView::ListVideoFrontend($lang,$array,$this->path.'/'.$position, $this->multi_lang);
    }//ShowListOfVideoFrontend
        
}
?>
