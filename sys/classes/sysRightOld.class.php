<?php 
// ================================================================================================
//    System     : SEOCMS
//    Module     : Rights
//    Version    : 1.0.0
//    Date       : 25.01.2005
//    Licensed To:
//                 Igor  Trokhymchuk  ihoru@mail.ru
//                 Andriy Lykhodid    las_zt@mail.ru
//
//    Purpose    : Database Abstraction Layer
//
// ================================================================================================

// ================================================================================================
//    Class             : Rights
//    Version           : 1.0.0
//    Date              : 25.01.2005
//
//    Constructor       : Yes
//    Parms             : Host               HostName
//                        User               UserID to database
//                        pwd                Password to database
//                        dbName             Name of the database to connect type
//                        open               Open database (true/false)
//    Returns           : None
//    Description       : Database Abstraction Layer
// ================================================================================================
//    Programmer        :  Andriy Lykhodid
//    Date              :  25.01.2005
//    Reason for change :  Creation
//    Change Request Nbr:  N/A
// ================================================================================================

include_once( SITE_PATH.'/sys/classes/sysDatabase.class.php' );

class RightsOld extends DB {

 var $user;
 var $module;
 var $query;
 var $is = true;

 var $Msg;


// ================================================================================================
//    Function          : Rights (Constructor)
//    Version           : 1.0.0
//    Date              : 25.01.2005
//    Parms             :
//    Returns           : Error Indicator
//
//    Description       : Opens and selects a dabase
// ================================================================================================

Function RightsOld ( $user=NULL, $module=NULL)
{
 if ( !$this->db_SetConfig());
 $this->db_Open();
 $this->db_Select();
 $this->user = $user;
 $this->module = $module;

 $this->Msg = new ShowMsg();                  /* create ShowMsg object as a property of this class */
}


// ================================================================================================
// Function : Query()
// Version : 1.0.0
// Date : 25.01.2005
//
// Parms : $user   / User login   / Void
//         $module / Module read  / Void
//         $q      / query
// Returns : true,false / Void
// Description : Execute query
// ================================================================================================
// Programmer : Andriy Lykhodid
// Date : 31.01.2005
// Reason for change : add strtoupper
// Change Request Nbr:
// ================================================================================================

function Query( $q, $user=NULL, $module=NULL )
{
 if( empty( $user ) ) $user = $this->user;
 if( empty( $module ) ) $module = $this->module;
 
 $act = NULL;
 $q=trim($q);
 //$arr_tmp = str_word_count($q, 1);
 $arr_tmp = explode(" ",$q);
 //echo '<br>$arr_tmp[0]='.$arr_tmp[0];
 
 if( strstr(  strtoupper( $arr_tmp[0] ),  strtoupper( 'select' ) ) ) $act = 'select';
 if( strstr(  strtoupper( $arr_tmp[0] ),  strtoupper( 'update' ) ) ) $act = 'update';
 if( strstr(  strtoupper( $arr_tmp[0] ),  strtoupper( 'alter' ) ) ) $act = 'update'; 
 if( strstr(  strtoupper( $arr_tmp[0] ),  strtoupper( 'insert' ) ) ) $act = 'insert';
 if( strstr(  strtoupper( $arr_tmp[0] ),  strtoupper( 'delete' ) ) ) $act = 'delete';
  if( strstr(  strtoupper( $arr_tmp[0] ),  strtoupper( 'drop' ) ) ) $act = 'delete'; 
 if( strstr(  strtoupper( $arr_tmp[0] ),   strtoupper( 'create') ) ) $act = 'execute'; 

 switch( $act)
 {
   case 'select': if( $this->IsRead( $user, $module ) )
                  {
                      $this->db_FreeResult();
                      $this->db_Query( $q );

                  }else
                  {
                    $this->Msg->show_msg('SYS_RIGHTS_NOT_RIGHTS');
                  }
                  break;
   case 'update': if( $this->IsUpdate( $user, $module ) )
                  {
                      $this->db_FreeResult();
                      $this->db_Query( $q );

                  }else
                  {
                    $this->Msg->show_msg('SYS_RIGHTS_NOT_RIGHTS');
                  }
                  break;
   case 'insert': if( $this->IsWrite( $user, $module ) )
                  {
                      $this->db_FreeResult();
                      $this->db_Query( $q );

                  }else
                  {
                    $this->Msg->show_msg('SYS_RIGHTS_NOT_RIGHTS');
                  }
                  break;
   case 'delete': if( $this->IsDelete( $user, $module ) )
                  {
                      $this->db_FreeResult();
                      $this->db_Query( $q );

                  }else
                  {
                    $this->Msg->show_msg('SYS_RIGHTS_NOT_RIGHTS');
                  }
                  break;
   case 'execute':if( $this->IsExecute( $user, $module ) )
                  {
                      $this->db_FreeResult();
                      $this->db_Query( $q );

                   }else
                   {
                    $this->Msg->show_msg('SYS_RIGHTS_NOT_RIGHTS');
                   }
                   break;
   default:return false;
 }
 return true;
}

// ================================================================================================
// Function : IsRead
// Version : 1.0.0
// Date : 25.01.2005
//
// Parms : $user   / User login   / Void
//         $module / Module read  / Void
// Returns : true,false / Void
// Description : Check is Read
// ================================================================================================
// Programmer : Andriy Lykhodid
// Date : 25.01.2005
// Reason for change : Reason Description / Creation
// Change Request Nbr:
// ================================================================================================

function IsRead( $user=NULL, $module=NULL )
{
 $db = new DB;
 if( empty( $user ) ) $user = $this->user;
 if( empty( $module ) ) $module = $this->module;

 $q = "SELECT sys_group_func.id,sys_group_func.function,sys_group_func.r
 FROM sys_user,sys_func,sys_group_func
 WHERE 1 and sys_user.id='$user' and sys_func.id ='$module' AND sys_group_func.group=sys_user.group_id AND sys_group_func.function=sys_func.id";
 //$res = $this->db_Query( $q );
 $res = $db->db_Query( $q );

 if( !$res ) return false;
 $row = $db->db_FetchArray( $res );
 if( $row[2]>0 )return true;
 else return false;
}

// ================================================================================================
// Function : IsWrite
// Version : 1.0.0
// Date : 25.01.2005
//
// Parms : $user   / User login   / Void
//         $module / Module read  / Void
// Returns : true,false / Void
// Description : Check is Write
// ================================================================================================
// Programmer : Andriy Lykhodid
// Date : 25.01.2005
// Reason for change : Reason Description / Creation
// Change Request Nbr:
// ================================================================================================

function IsWrite( $user=NULL, $module=NULL )
{
 $db = new DB;
 if( empty( $user ) ) $user = $this->user;
 if( empty( $module ) ) $module = $this->module;

 $q = "SELECT sys_group_func.id,sys_group_func.function,sys_group_func.w
 FROM sys_user,sys_func,sys_group_func
 WHERE 1 and sys_user.id='$user' and sys_func.id='$module' AND sys_group_func.group=sys_user.group_id AND sys_group_func.function=sys_func.id";
 $res = $db->db_Query( $q );
 if( !$res ) return false;
 $row = $db->db_FetchArray( $res );
 if( $row[2]>0 )return true;
 else return false;
}

// ================================================================================================
// Function : IsUpdate
// Version : 1.0.0
// Date : 25.01.2005
//
// Parms : $user   / User login   / Void
//         $module / Module read  / Void
// Returns : true,false / Void
// Description : Check is Update
// ================================================================================================
// Programmer : Andriy Lykhodid
// Date : 25.01.2005
// Reason for change : Reason Description / Creation
// Change Request Nbr:
// ================================================================================================

function IsUpdate( $user=NULL, $module=NULL )
{
 $db = new DB;
 if( empty( $user ) ) $user = $this->user;
 if( empty( $module ) ) $module = $this->module;

 $q = "SELECT sys_group_func.id,sys_group_func.function,sys_group_func.u
 FROM sys_user,sys_func,sys_group_func
 WHERE 1 and sys_user.id='$user' and sys_func.id='$module' AND sys_group_func.group=sys_user.group_id AND sys_group_func.function=sys_func.id";
 $res = $db->db_Query( $q );
 if( !$res ) return false;
 $row = $db->db_FetchArray( $res );
 if( $row[2]>0 )return true;
 else return false;
}

// ================================================================================================
// Function : IsDelete
// Version : 1.0.0
// Date : 25.01.2005
//
// Parms : $user   / User login   / Void
//         $module / Module read  / Void
// Returns : true,false / Void
// Description : Check is Delete
// ================================================================================================
// Programmer : Andriy Lykhodid
// Date : 25.01.2005
// Reason for change : Reason Description / Creation
// Change Request Nbr:
// ================================================================================================

function IsDelete( $user=NULL, $module=NULL )
{
 $db = new DB;
 if( empty( $user ) ) $user = $this->user;
 if( empty( $module ) ) $module = $this->module;

 $q = "SELECT sys_group_func.id,sys_group_func.function,sys_group_func.d
 FROM sys_user,sys_func,sys_group_func
 WHERE 1 and sys_user.id='$user' and sys_func.id='$module' AND sys_group_func.group=sys_user.group_id AND sys_group_func.function=sys_func.id";
 $res = $db->db_Query( $q );
 if( !$res ) return false;
 $row = $db->db_FetchArray( $res );
 if( $row[2]>0 )return true;
 else return false;
}


// ================================================================================================
// Function : IsExecute
// Version : 1.0.0
// Date : 25.01.2005
//
// Parms : $user   / User login   / Void
//         $module / Module read  / Void
// Returns : true,false / Void
// Description : Check is Execute
// ================================================================================================
// Programmer : Andriy Lykhodid
// Date : 25.01.2005
// Reason for change : Reason Description / Creation
// Change Request Nbr:
// ================================================================================================

function IsExecute( $user=NULL, $module=NULL )
{
 $db = new DB;
 if( empty( $user ) ) $user = $this->user;
 if( empty( $module ) ) $module = $this->module;

 $q = "SELECT sys_group_func.id,sys_group_func.function,sys_group_func.e
 FROM sys_user,sys_func,sys_group_func
 WHERE 1 and sys_user.id='$user' and sys_func.id='$module' AND sys_group_func.group=sys_user.group_id AND sys_group_func.function=sys_func.id";
 $res = $db->db_Query( $q );
 if( !$res ) return false;
 $row = $db->db_FetchArray( $res );
 if( $row[2]>0 )return true;
 else return false;
}

} // End of Rights Class

?>
