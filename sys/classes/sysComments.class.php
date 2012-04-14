<?php
// ================================================================================================
// System : PrCSM05
// Module : sysComments.class.php
// Version : 1.0.0
// Date : 20.08.2008
// Licensed To:
// Igor Trokhymchuk ihoru@mail.ru
// Andriy Lykhodid las_zt@mail.ru
//
// Purpose : Class definition for system functions with comments for all modules
//
// ================================================================================================

include_once( SITE_PATH.'/admin/include/defines.inc.php' );  

// ================================================================================================
//    Class             : SystemComments
//    Version           : 1.0.0
//    Date              : 20.08.2008
//
//    Constructor       : Yes
//    Parms             : session_id / session id
//                        user_id    / UserID

//    Returns           : None
//    Description       : Class definition for system functions with comments for all modules 
// ================================================================================================
//    Programmer        :  Igor Trokhymchuk
//    Date              :  20.08.2008
//    Reason for change :  Creation
//    Change Request Nbr:  N/A
// ================================================================================================
 class SystemComments {

   var $user_id = NULL;
   var $module = NULL;
   var $id_item = NULL;
   var $dt = NULL; 
   var $status = NULL;
   var $text = NULL;
   var $id_user = NULL;
   var $name = NULL;
   var $email = NULL;
   
   var $Spr = NULL;
   var $lang_id = NULL;
   
   // ================================================================================================
   //    Function          : SystemComments (Constructor)
   //    Version           : 1.0.0
   //    Date              : 20.08.2008
   //    Parms             : usre_id   / User ID
   //                        module    / module ID
   //    Returns           : Error Indicator
   //
   //    Description       : Opens and selects a dabase
   // ================================================================================================
   function SystemComments($user_id=NULL, $module=NULL) {
            //Check if Constants are overrulled
            ( $user_id   !="" ? $this->user_id = $user_id  : $this->user_id = NULL );
            ( $module   !="" ? $this->module  = $module   : $this->module  = NULL );

            if ( defined("_LANG_ID") ) $this->lang_id = _LANG_ID;
                         
            if (empty($this->Rights)) $this->Rights = new Rights($this->user_id, $this->module);
            if (empty($this->Spr)) $this->Spr = new SystemSpr($this->user_id, $this->module);
            if (empty($this->Msg)) $this->Msg = new ShowMsg();
            $this->Msg->SetShowTable(TblSysTxt);
            if (empty($this->Form)) $this->Form = new Form('sys_comments');
   } // End of SystemComments Constructor
 }?>