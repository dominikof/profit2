<?php
// ================================================================================================
// Module : upload.class.php
// Version : 1.0.0
// Date : 25.06.2010
// Licensed To:
// Oleg Morgalyuk oleg@seotm.com
//
// Purpose : Class definition(controller) for working with uploads (files, images, videos)
//
// ================================================================================================

// ================================================================================================
//
//    Programmer        :  Oleg Morgalyuk
//    Date              :  25.06.2010
//    Reason for change :  Creation
//    Change Request Nbr:
//
//    Function          :  Class definition(controller) for working with uploads (files, images, videos) 
//
//  ================================================================================================

include_once( SITE_PATH.'/admin/include/defines.inc.php' );
include_once( SITE_PATH.'/sys/classes/upload/upload.view.php' ); 
include_once( SITE_PATH.'/sys/classes/upload/upload.model.php' ); 
class UploadClass {
    
    var $module = NULL;
    var $path = NULL;
    var $position = NULL;
    var $table = NULL;
    var $table_spr = NULL;
    var $max_items = 100;
    var $empty_fields = 1;
    var $max_file_size = 104857600; //100Mb
    static $lan = NULL;
    var $lang = NULL;
    var $type = 'F';
    var $save_file_names = true;
    static $multi = NULL;
    var $multi_lang = NULL;
    
    // ================================================================================================
    //    Function          : UploadClass (Constructor)
    //    Version           : 1.0.0
    //    Date              : 25.06.2010
    //    Parms             : $module    / module
    //                      : $position  / position
    //                      : $path      / path to uploads files
    //                      : $table     / save general information about files to upload
    //                      : $table_spr / save name and desription of files to upload
    //                      : $max_items / max items, which can be upload to each positions
    //                      : $lang      / array of languages
    //    Returns           : none
    //    Description       : Set the variabels
    // ================================================================================================

    function UploadClass($module,$position = NULL,$path,$table,$table_spr = NULL,$max_items = NULL,$lang = NULL){
        $this->module =  $module;
        $this->position =  $position;
        $this->path =  $path;
        $this->table =  $table;
        if(isset($max_items)) 
            $this->max_items = $max_items;
        if(!isset($table_spr)) 
            $this->table_spr = $this->table.'_spr';
        else 
            $this->table_spr = $table_spr;
        if(!isset(UploadClass::$lan)){
            if(isset($lang)) 
                UploadClass::$lan = $lang;
            else{
                $ln_sys = new SysLang();
                UploadClass::$lan = $ln_sys->LangArray( _LANG_ID );
            }
        }
        $this->lang = &UploadClass::$lan;  
       if(!isset(UploadClass::$multi)){
            //$spr = &check_init('SystemSpr', 'SystemSpr');
            UploadClass::$multi  = &check_init_txt('TblBackMulti',TblBackMulti);
        }
        $this->multi_lang = UploadClass::$multi;
    }//UploadClass
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
            
        UploadView::FileUploadTop($this->multi_lang);
        $model =  new UploadModel();
        $array = $model->GetFilesForPosition($position,$this->module,$this->table,$this->table_spr);
        $count = count ($array);
        UploadView::UploadedFiles($this->lang,$array,$this->multi_lang,$this->path,$position,$this->table,$this->table_spr,$formId);
        if(!isset($max_files)) $max_files = $this->max_items;
        $max_files -= $count;
        if($this->empty_fields > $max_files) $this->empty_fields = $max_files;
        UploadView::Uploadform($this->lang,$this->empty_fields,$this->multi_lang,$max_files,$count,$this->type,$formId);
        UploadView::FileUploadBot();
    }//ShowFormToUpload
// ================================================================================================
// Function : CreateTables
// Version : 1.0.0
// Date : 29.06.2010
//
// Parms :   null
// Returns : $res / Void
// Description : Create tables, which we need to use this module
// ================================================================================================
// Programmer : Oleg MOrgalyuk
// Date : 29.06.2010
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================    
    function CreateTables()
    {
         $model =  new UploadModel();
         $res = $model->CreateTables($this->table, $this->table_spr);
         if(!$res) 
            UploadView::ShowError($this->multi_lang['FLD_ERROR']);
    }//CreateTables
// ================================================================================================
// Function : SaveFiles
// Version : 1.0.0
// Date : 01.07.2010
//
// Parms :   $id  / id of position 
// Returns : $res / Void
// Description : Save files
// ================================================================================================
// Programmer : Oleg Morgalyuk
// Date : 01.07.2010
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================    
    function SaveFiles($id,$formId=1)
    {
         $model =  new UploadModel();
         $res = $model->SaveFiles($this->max_file_size,$this->multi_lang,$this->path,$id,$this->table,$this->table_spr,$this->module,$this->lang,$this->save_file_names,$formId);
         if(!empty($res)) 
            UploadView::ShowError($res);
            
         $res1 = $model->UpdateFiles($this->multi_lang,$id,$this->table,$this->table_spr,$this->module,$this->lang,$this->type,$formId);
         if(!empty($res1)) 
            UploadView::ShowError($res1);
                        
    }//SaveFiles

// ================================================================================================
// Function : DeleteFiles
// Version : 1.0.0
// Date : 02.07.2010
//
// Parms :   $id  / id in table 
//           $path  / full path to file 
// Returns : $res / Void
// Description : delete files and information about the file
// ================================================================================================
// Programmer : Oleg Morgalyuk
// Date : 02.07.2010
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================       
    function DeleteFiles($id,$path)
    {
         $model =  new UploadModel();
         $res = $model->DeleteFiles($this->multi_lang,$id,$this->table,$this->table_spr,$path);
         if(!empty($res)) 
            UploadView::ShowError($res);
         else
            UploadView::ShowMessage($this->multi_lang['DEL_OK']);
    }//DeleteFiles


// ================================================================================================
// Function : ShowListOfFilesFrontend
// Version : 1.0.0
// Date : 02.07.2010
//
// Parms :   $position  / item position 
//           $lang      / id of language 
// Returns : $res       / Void
// Description : Show list of files in frontend
// ================================================================================================
// Programmer : Oleg Morgalyuk
// Date : 02.07.2010
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================ 
    function GetListOfFilesFrontend($position,$lang = _LANG_ID){
        $model =  new UploadModel();
        return  $model->GetFilesForPosition($position,$this->module,$this->table,$this->table_spr,$lang,1);
    }//ShowListOfFilesFrontend
        
// ================================================================================================
// Function : ShowListOfFilesFrontend
// Version : 1.0.0
// Date : 02.07.2010
//
// Parms :   $position  / item position 
//           $lang      / id of language 
// Returns : $res       / Void
// Description : Show list of files in frontend
// ================================================================================================
// Programmer : Oleg Morgalyuk
// Date : 02.07.2010
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================ 
    function ShowListOfFilesFrontend($array, $position,$lang = _LANG_ID){
        UploadView::ListFilesFrontend($lang,$array,$this->path.'/'.$position, $this->multi_lang);
    }//ShowListOfFilesFrontend
    
    
// ================================================================================================
// Function : DownloadCatalogFrontend
// Version : 1.0.0
// Date : 02.07.2010
//
// Parms :   $position  / item position 
//           $lang      / id of language 
// Returns : $res       / Void
// Description : Show list of files in frontend
// Programmer : Oleg Morgalyuk
// ================================================================================================ 
    function DownloadCatalogFrontend($array, $position,$lang = _LANG_ID){
        UploadView::DownloadCatalogFrontend($lang,$array,$this->path.'/'.$position, $this->multi_lang);
    }//ShowListOfFilesFrontend
        
// ================================================================================================
// Function : DeleteAllFilesForPosition
// Version : 1.0.0
// Date : 05.07.2010
//
// Parms :   $id_pos     / position id 
//           $module_id  / module id 
//           $path  / path to file 
// Returns : $res / Void
// Description : delete all files for position
// ================================================================================================
// Programmer : Oleg Morgalyuk
// Date : 05.07.2010
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================       
    function DeleteAllFilesForPosition($pos,$module = NULL)
    {
         $model =  new UploadModel();
         if(!isset($module)) 
            $module = $this->module;
         $res = $model->DeleteFilesAll($this->multi_lang,$pos,$module,$this->table,$this->table_spr,$this->path);
         if(!empty($res)) 
            UploadView::ShowError($res);
         elseif($res!=0)
            UploadView::ShowMessage($this->multi_lang['DEL_OK']);
    }//DeleteAllFilesForPosition
// ================================================================================================
// Function : GetFilesCount
// Version : 1.0.0
// Date : 05.07.2010
//
// Parms :   $id_pos     / position id 
//           $module_id  / module id 
// Returns : $res / Void
// Description : 
// Programmer : Oleg Morgalyuk
// ================================================================================================       
    function GetFilesCount ($position, $lang=_LANG_ID) {
        $model =  new UploadModel();
        $count =  $model->GetFilesCountForPosition($position,$this->module,$this->table,$this->table_spr,$lang);
        UploadView::ShowFilesCount($count, $this->multi_lang );
    }   // End of function GetImagesCount();
// ================================================================================================
// Function : GetFilesCount
// Version : 1.0.0
// Date : 05.07.2010
//
// Parms :   $id_pos     / position id 
//           $module_id  / module id 
// Returns : $res / Void
// Description : 
// Programmer : Oleg Morgalyuk
// ================================================================================================       
    function GetFilesCountForModule ($lang=_LANG_ID) {
        $model =  new UploadModel();
        $array =  $model->GetFilesCountForPosition(NULL,$this->module,$this->table,$this->table_spr,$lang);
        return $array;
    }   // End of function GetImagesCount();
}
?>