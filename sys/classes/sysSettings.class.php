<?php
// ================================================================================================
//    System     : SEOCMS
//    Module     : sysSettings.class.php
//    Version    : 1.0.0
//    Date       : 17.02.2007
//
//    Purpose    : Class definition for all actions with users settings
//
//    Called by  : *ANY
//
// ================================================================================================
// ================================================================================================
//    Class             : SysSettings
//    Version           : 1.0.0
//    Date              : 17.02.2007
//
//    Constructor       : Yes
//    Parms             :
//    Returns           : None
//    Description       : Page - base class
// ================================================================================================
//    Programmer        :  Igor Trokhymchuk
//    Date              :  17.02.2007
//    Reason for change :  Creation
//    Change Request Nbr:  N/A
// ================================================================================================
class SysSettings {
    
    var $Rights = NULL;
    var $lang = NULL;
    var $debug = NULL;
    var $Err = NULL;
// ================================================================================================
//    Function          : SysSettings (Constructor)
//    Version           : 1.0.0
//    Date              : 17.02.2007
//    Parms             :
//    Returns           :
//    Description       : 
// ================================================================================================

Function SysSettings ( )
{
   if (empty($this->Rights)) $this->Rights = new Rights(); 
   if (empty($this->db)) $this->db = DBs::getInstance();
}// end of Constructor
// ================================================================================================
// Function : SetLangBackend()
// Version : 1.0.0
// Date : 17.02.2007 
// Parms :
// Returns : true,false / Void
// Description : Set language for back-end for currect user 
// ================================================================================================
// Programmer : Igor Trokhymchuk
// Date : 17.02.2007 
// Reason for change : Reason Description / Creation
// Change Request Nbr:
// ================================================================================================
function SetLangBackend( $user_id=NULL, $lang_id=NULL )
{
     $q = "SELECT `id_user` FROM `".TblSysSettings."` WHERE `id_user`='$user_id'";
     $res = $this->Rights->db_Query($q);
//     echo '<br /><br /><br /><br> $q='.$q.' $res='.$res.' $this->Rights->result='.$this->Rights->result;
     if ( !$res OR ! $this->Rights->result) return false;
     $rows = $this->Rights->db_GetNumRows();
//     echo '<br />$rows='.$rows;
     if ($rows>0){
         $q = "UPDATE `".TblSysSettings."` SET `curr_lang_backend`='$lang_id' WHERE `id_user`='$user_id'";
         $res = $this->Rights->db_Query($q);
         //echo '<br> $q='.$q.' $res='.$res.' $this->Rights->result='.$this->Rights->result;
         if ( !$res OR ! $this->Rights->result) return false;
     }
     else{
         $q = "INSERT INTO `".TblSysSettings."` SET
         `id_user`='$user_id',
         `curr_lang_backend`='$lang_id'";
         $res = $this->Rights->db_Query($q);
         //echo '<br> $q='.$q.' $res='.$res.' $this->Rights->result='.$this->Rights->result;
         if ( !$res OR ! $this->Rights->result) return false;         
     }
     return true;
} //end of function SetLangBackend()

// ================================================================================================
// Function : GetLangBackend()
// Version : 1.0.0
// Date : 17.02.2007 
// Parms :
// Returns : true,false / Void
// Description : Return language for back-end for currect user
// ================================================================================================
// Programmer : Igor Trokhymchuk
// Date : 17.02.2007 
// Reason for change : Reason Description / Creation
// Change Request Nbr:
// ================================================================================================
function GetLangBackend( $user_id=NULL ,$flag=false)
{
    if(!$flag)
     $q = "SELECT `curr_lang_backend` FROM `".TblSysSettings."` WHERE `id_user`='$user_id'";
    else
     $q = "SELECT `".TblSysSettings."`.`curr_lang_backend`,`".TblModSysLang."`.`short_name` FROM `".TblSysSettings."`,`".TblModSysLang."` WHERE `id_user`='$user_id' AND `".TblModSysLang."`.`cod`=`".TblSysSettings."`.`curr_lang_backend`" ;
     $res = $this->db->db_Query($q);
     //echo '<br> $q='.$q.' $res='.$res.' $this->Rights->result='.$this->Rights->result;
     if ( !$res OR ! $this->db->result) return false;
     $row =$this->db->db_FetchAssoc();
     if(!$flag)
      return $row['curr_lang_backend'];
     else{
         $langArr=array();
         $langArr['cod']=$row['curr_lang_backend'];
         $langArr['short']=$row['short_name'];
      return $langArr;
     }
} //end of function GetLangBackend()

// ================================================================================================
// Function : SetLangFrontend()
// Version : 1.0.0
// Date : 17.02.2007 
// Parms :
// Returns : true,false / Void
// Description : Set language for front-end for currect user 
// ================================================================================================
// Programmer : Igor Trokhymchuk
// Date : 17.02.2007 
// Reason for change : Reason Description / Creation
// Change Request Nbr:
// ================================================================================================
function SetLangFrontend( $user_id=NULL, $lang_id=NULL )
{
     $q = "SELECT `id_user` FROM `".TblSysSettings."` WHERE `id_user`='$user_id'";
     $res = $this->Rights->db_Query($q);
     //echo '<br> $q='.$q.' $res='.$res.' $this->Rights->result='.$this->Rights->result;
     if ( !$res OR ! $this->Rights->result) return false;
     $rows = $this->Rights->db_GetNumRows();
     
     if ($rows>0){
         $q = "UPDATE `".TblSysSettings."` SET `curr_lang_frontend`='$lang_id' WHERE `id_user`='$user_id'";
         $res = $this->Rights->db_Query($q);
         //echo '<br> $q='.$q.' $res='.$res.' $this->Rights->result='.$this->Rights->result;
         if ( !$res OR ! $this->Rights->result) return false;
     }
     else{
         $q = "INSERT INTO `".TblSysSettings."` SET
         `id_user`='$user_id',
         `curr_lang_frontend`='$lang_id'";
         $res = $this->Rights->db_Query($q);
         //echo '<br> $q='.$q.' $res='.$res.' $this->Rights->result='.$this->Rights->result;
         if ( !$res OR ! $this->Rights->result) return false;         
     }
     return true;
} //end of function SetLangFrontend()

// ================================================================================================
// Function : GetLangFrontend()
// Version : 1.0.0
// Date : 17.02.2007 
// Parms :
// Returns : true,false / Void
// Description : return language for front-end for currect user
// ================================================================================================
// Programmer : Igor Trokhymchuk
// Date : 17.02.2007 
// Reason for change : Reason Description / Creation
// Change Request Nbr:
// ================================================================================================
function GetLangFrontend( $user_id=NULL )
{
     $q = "SELECT `curr_lang_frontend` FROM `".TblSysSettings."` WHERE `id_user`='$user_id'";
     $res = $this->Rights->db_Query($q);
     //echo '<br> $q='.$q.' $res='.$res.' $this->Rights->result='.$this->Rights->result;
     if ( !$res OR ! $this->Rights->result) return false;
     $row = $this->Rights->db_FetchAssoc();
     return $row['curr_lang_frontend'];
} //end of function GetLangFrontend()

// ================================================================================================
// Function : GetGlobalSettings()
// Version : 1.0.0
// Date : 19.12.2007
// Parms :
// Returns : true,false / Void
// Description : Get mail settings
// ================================================================================================
// Programmer : Igor Trokhymchuk
// Date : 19.12.2007
// Reason for change : Reason Description / Creation
// Change Request Nbr:
// ================================================================================================
static function GetGlobalSettings()
{   $tmpdb=&DBs::getInstance();
   $q = "SELECT * FROM ".TblSysSetGlobal." WHERE `id`='1'";
   $res = $tmpdb->db_Query( $q );
   if( !$res ) return false;
   $mas = $tmpdb->db_FetchAssoc();
   $mas['txt'] = SysSettings::GetMailTxtData();
   return $mas;
}//end of function GetGlobalSettings()

static function GetMailTxtData()
{
    $tmpdb=&DBs::getInstance();
    $result = array();
    $q = 'SELECT * from `'.TblSysSetGlobalSprMail.'` ';
    $res = $tmpdb->db_Query( $q);
    if (!$res) return false;
    $rows = $tmpdb->db_GetNumRows();
    for($i=0;$i<$rows;$i++){
        $row = $tmpdb->db_FetchAssoc();
        $result[$row['lang_id']]['head'] = $row['head'];
        $result[$row['lang_id']]['from'] = $row['from'];
        $result[$row['lang_id']]['foot'] = $row['foot'];
    }
//    print_r($result);
    return $result;
}

// ================================================================================================
// Function : SetDebug()
// Version : 1.0.0
// Date : 26.01.2005
// Parms :
// Returns : true,false / Void
// Description : Set debug
// ================================================================================================
// Programmer : Igor Trokhymchuk
// Date : 26.01.2005
// Reason for change : Reason Description / Creation
// Change Request Nbr:
// ================================================================================================
function SetDebug( $debug='' )
{
 $this->debug = $debug;
}
// ================================================================================================
// Function : GetDebug()
// Version : 1.0.0
// Date : 26.01.2005
// Parms :
// Returns : true,false / Void
// Description : Get debug
// ================================================================================================
// Programmer : Igor Trokhymchuk
// Date : 26.01.2005
// Reason for change : Reason Description / Creation
// Change Request Nbr:
// ================================================================================================
function GetDebug(  )
{
 return $this->debug;
}

}// End of Class SysSettings
?>