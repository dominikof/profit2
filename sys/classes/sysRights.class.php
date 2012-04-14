<?php 
include_once( SITE_PATH.'/sys/classes/sysDatabase.single.class.php' );

/**
* Class Rights
* Database Abstraction Layer
* @package System Package of SEOCMS
* @author Igor Trokhymchuk  <ihor@seotm.com>
* @version 1.1, 02.04.2012
* @copyright (c) 2005+ by SEOTM
*/
class Rights  {
     
    public $user;
    public $module;
    public $query;
    public $is = true;
    public $result = false;
    public $Msg;
    public $userAccessRights = NULL; //array for store users access rights to system functions (modules)     
    
    /**
    * Rights::__construct()
    * 
    * @param integer $user_id
    * @param integer $module_id
    * @return void
    */
    Function __construct( $user=NULL, $module=NULL )
    {
     $this->single_db= &DBs::getInstance();
     $this->user = $user;
     $this->module = $module;
     if(!empty($this->user)) $this->LoadUserAccessRights($this->user);
    }
    
    
    ///part from DB BEGIN (overloads)
    function db_Query( $sql = '' )
            {
                $ret=$this->single_db->db_Query($sql);
                //echo '<br>$ret='.$ret.' $this->single_db->result='.$this->single_db->result;
                $this->result=$this->single_db->result;
                return $ret;
            } 
    function db_FreeResult( )
            {
                 $ret=$this->single_db->db_FreeResult();
                 $this->result=$this->single_db->result;
                 return $ret;
            } 
    function db_FetchAssoc( )
            {
                $www =  $this->single_db->db_FetchAssoc();
                $this->result=$this->single_db->result;
                return $www;
            } 
    function db_Array( )
            {
                $www =  $this->single_db->db_FetchAssoc();
                $this->result=$this->single_db->result;
                return $www;
            } 
    function db_GetNumRows( )
            {
                $www = $this->single_db->db_GetNumRows();
                $this->result=$this->single_db->result;
                return $www;
            }
    function IsFieldExist($Table, $field )
           {
                return $this->single_db->IsFieldExist($Table,$field);
           } //end of function IsFieldExist()
    function db_GetInsertID( )
           {
                return $this->single_db->db_GetInsertID( );
           } //end of function IsFieldExist()
           
           
    //part from DB END (overloads)
    

    /**
    * Class method db_QueryResult
    * Execute query
    * @param string $sql - query
    * @return data query result as array
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 02.04.2012
    */     
    function db_QueryResult( $sql = '' )
    {
         $res = $this->single_db->db_Query( $sql );
         if( !$res )return false;
         $rows = $this->single_db->db_GetNumRows();
         $this->result=$this->single_db->result;
         $results=array();
         for($i=0;$i<$rows;++$i){
            $results[$i] = $this->single_db->db_FetchAssoc();             
         }  
        return $results;
    } 

    /**
    * Class method Query
    * Execute query with checking users grands to make this
    * @param string $q - query
    * @param integer $user - id of the user
    * @param integer $module - id of the module
    * @return data query result
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 02.04.2012
    */         
    function Query( $q, $user=NULL, $module=NULL )
    {
     $this->db_FreeResult();
     if( empty( $user ) ) $user = $this->user;
     if( empty( $module ) ) $module = $this->module;
     if( empty( $this->Msg ) )  $this->Msg = new ShowMsg();
     
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
     //echo '<br>$q='.$q;
     //echo '<br>$user='.$user.' $module='.$module.' $act='.$act; 
     switch( $act)
     {
       case 'select': if( $this->IsRead( $module ) )
                      {
                          $this->db_FreeResult();
                          return $this->db_Query( $q );
    
                      }else
                      {
                          $this->Msg->show_msg('SYS_RIGHTS_NOT_RIGHTS');
                          return false;
                      }
                      break;
       case 'update': if( $this->IsUpdate( $module ) )
                      {
                          $this->db_FreeResult();
                          return $this->db_Query( $q );
    
                      }else
                      {
                        $this->Msg->show_msg('SYS_RIGHTS_NOT_RIGHTS');
                        return false;
                      }
                      break;
       case 'insert': if( $this->IsWrite( $module ) )
                      {
                          $this->db_FreeResult();
                          return $this->db_Query( $q );
    
                      }else
                      {
                        $this->Msg->show_msg('SYS_RIGHTS_NOT_RIGHTS');
                        return false;
                      }
                      break;
       case 'delete': if( $this->IsDelete( $module ) )
                      {
                          $this->db_FreeResult();
                          $res = $this->db_Query( $q );
                          if( !$res OR !$this->result) return false;
                          
                          //======================= OPTIMIZE TABLE after deleting START ================================
                          $tblArrAssoc = $this->GetTblFromQueryDelete($q); 
                          //echo '<br>$tblArrAssoc=';print_r($tblArrAssoc);
                          if( is_array($tblArrAssoc) ){
                              $keys = array_keys($tblArrAssoc);
                              $cnt = count($keys);
                              for($i=0;$i<$cnt;$i++){
                                  $q2 = 'OPTIMIZE TABLE '.$keys[$i];
                                  $res = $this->db_Query( $q2 );
                                  //echo '<br>$q2='.$q2.' $res='.$res.' $this->result='.$this->result.' $this->error='.$this->error;
                              }
                          }
                          //======================= OPTIMIZE TABLE after deleting END ================================
    
                      }else
                      {
                        $this->Msg->show_msg('SYS_RIGHTS_NOT_RIGHTS');
                        return false;
                      }
                      break;
       case 'execute':if( $this->IsExecute( $module ) )
                      {
                          $this->db_FreeResult();
                          return $this->db_Query( $q );
    
                       }else
                       {
                        $this->Msg->show_msg('SYS_RIGHTS_NOT_RIGHTS');
                        return false;
                       }
                       break;
       default:
        return false;
     }
     return true;
    }
    

    /**
    * Class method QueryResult
    * Execute query with checking users grands to make this
    * @param string $q - query
    * @param integer $user - id of the user
    * @param integer $module - id of the module
    * @return data query result as array
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 02.04.2012
    */     
    function QueryResult( $q, $user=NULL, $module=NULL )
    {
     if( empty( $user ) ) $user = $this->user;
     if( empty( $module ) ) $module = $this->module;
     if( empty( $this->Msg ) )  $this->Msg = new ShowMsg();                  /* create ShowMsg object as a property of this class */  ;
      
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
                          return $this->db_QueryResult( $q );
                      }else
                      {
                          $this->Msg->show_msg('SYS_RIGHTS_NOT_RIGHTS');
                      }
                      break;
       case 'update': if( $this->IsUpdate( $module ) )
                      {
                          $this->db_FreeResult();
                          return $this->db_QueryResult( $q );
    
                      }else
                      {
                        $this->Msg->show_msg('SYS_RIGHTS_NOT_RIGHTS');
                      }
                      break;
       case 'insert': if( $this->IsWrite( $module ) )
                      {
                          $this->db_FreeResult();
                          return $this->db_QueryResult( $q );
                      }else
                      {
                        $this->Msg->show_msg('SYS_RIGHTS_NOT_RIGHTS');
                      }
                      break;
       case 'delete': if( $this->IsDelete( $module ) )
                      {
                          $this->db_FreeResult();
                          return $this->db_QueryResult( $q );
                      }else
                      {
                        $this->Msg->show_msg('SYS_RIGHTS_NOT_RIGHTS');
                      }
                      break;
       case 'execute':if( $this->IsExecute( $module ) )
                      {
                          $this->db_FreeResult();
                          return $this->db_QueryResult( $q );
                       }else
                       {
                        $this->Msg->show_msg('SYS_RIGHTS_NOT_RIGHTS');
                       }
                       break;
       default:{return false;}
     }
     return true;
    }
    
    
    /**
    * Class method GetTblFromQueryDelete
    * Get name of tables from query DELETE
    * @param string $q - query
    * @return assoc array $tblArrAssoc with indexes as table names.
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 14.02.2011
    */ 
    function GetTblFromQueryDelete($q){
        $tblArrAssoc = '';
          //$q = 'DELETE FROM `tblname`';
          //$q = 'DELETE FROM `tblname` WHERE 1';
          //$q = 'DELETE a1, db2.a2 FROM db1.t1 AS a1 INNER JOIN db2.t2 AS a2 WHERE a1.id=a2.id';
          //$q = 'DELETE t1 FROM t1 LEFT JOIN t2 ON t1.id=t2.id WHERE t2.id IS NULL';
          //$q = 'DELETE FROM t1, t2 USING t1 INNER JOIN t2 INNER JOIN t3 WHERE t1.id=t2.id AND t2.id=t3.id';
          $q = strtolower($q);
          $arr = explode('from', $q);
          if(strstr($q, 'where')) {
              $arr = explode('where', trim($arr[1]));
              $tmpstr = trim($arr[0]);
          }
          else{ $tmpstr = trim($arr[1]);}
          //$tmpstr = trim( substr($q, strpos($q,'FROM')+4, (strpos($q,'WHERE')-(strpos($q,'FROM')+4)) ) );
          //echo '<br>$q='.$q.'<br>$tmpstr='.$tmpstr.' strpos($q,FROM)+4='.(strpos($q,'FROM')+4).' strpos($q,WHERE)='.strpos($q,'WHERE');
          if(strstr($tmpstr, 'using')){
              $tmpArr = explode('using', $tmpstr);
              $tblArr = explode(',', $tmpArr[0]);
              //echo '<br>$tblArr=';print_r($tblArr);
              $cnt = count($tblArr);
              for($i=0;$i<$cnt;$i++){
                  $tblArrAssoc[trim($tblArr[$i])]='';
              }
              if(strstr($tmpstr, 'join')){
                  $tmpArr2 = explode('join', $tmpArr[1]);
                  $cnt = count($tmpArr2);
                  for($i=1;$i<$cnt;$i++){
                      $tmpArr3 = explode(' ', trim($tmpArr2[$i]));
                      $tblArrAssoc[trim($tmpArr3[0])]='';
                  }
              }
              //echo '<br>$tblArrAssoc=';print_r($tblArrAssoc);
          }
          elseif(strstr($tmpstr, 'join')){
              $tmpArr2 = explode('join', $tmpstr);
              $cnt = count($tmpArr2);
              for($i=0;$i<$cnt;$i++){
                  $tmpArr3 = explode(' ', trim($tmpArr2[$i]));
                  $tblArrAssoc[trim($tmpArr3[0])]='';
              }
          }
          else{
              $tblArr = explode(',', $tmpstr);
              $cnt = count($tblArr);
              for($i=0;$i<$cnt;$i++){
                  $tblArrAssoc[trim($tblArr[$i])]='';
              }
          }    
        return $tblArrAssoc;
    } //end of function GetTblFromQueryDelete()
    
        
    /**
    * Class method LoadUserAccessRights
    * Get access rigthts for $user 
    * @param integer $user - id of the user
    * @return assoc array $tblArrAssoc with indexes as table names.
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 30.03.2012
    */ 
    function LoadUserAccessRights($user = NULL){
        if(empty($user)) $user = $this->user;
        $q = "SELECT 
              `sys_group_func`.`function`,
              `sys_group_func`.`r`,
              `sys_group_func`.`w`,
              `sys_group_func`.`u`,
              `sys_group_func`.`d`,
              `sys_group_func`.`e`
              FROM 
              `sys_user`,
              `sys_group_func`
              WHERE `sys_user`.`id`='".$user."'  
              AND `sys_group_func`.`group`=`sys_user`.`group_id` 
             ";
         $res = $this->db_Query( $q );
         //echo '<br>$q='.$q.' $res='.$res;        
         if( !$res ) return false;
         $rows = $this->db_GetNumRows();
         for($i=0;$i<$rows;$i++){
            $row = $this->db_FetchAssoc( $res );
            $this->userAccessRights[$row['function']] = $row;
         }
         //print_r($this->userAccessRights);
         return true;         
    }//end of function LoadUserAccessRights()
    
    /**
    * Class method IsRead
    * return access for read (SELECT) data for user $this->user to the module with id $module
    * @param integer $module - id of the module
    * @return true or false
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 30.03.2012
    */ 
    function IsRead($module=NULL){
        if( empty($module)) $module = $this->module;
        //echo '<br> $this->userAccessRights['.$module.'][r]='. $this->userAccessRights[$module]['r'];
        if( isset($this->userAccessRights[$module]['r'])) return $this->userAccessRights[$module]['r'];
        else return false;
    }//end of function IsRead()

    /**
    * Class method IsWrite
    * return access for write (INSERT) data for user $this->user to the module with id $module
    * @param integer $module - id of the module
    * @return true or false
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 30.03.2012
    */ 
    function IsWrite($module=NULL){
        if( empty($module)) $module = $this->module;
        if( isset($this->userAccessRights[$module]['w'])) return $this->userAccessRights[$module]['w'];
        else return false;
    }//end of function IsWrite()
    
    /**
    * Class method IsUpdate
    * return access for update (UPDATE) data for user $this->user to the module with id $module
    * @param integer $module - id of the module
    * @return true or false
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 30.03.2012
    */ 
    function IsUpdate($module=NULL){
        if( empty($module)) $module = $this->module;
        if( isset($this->userAccessRights[$module]['u'])) return $this->userAccessRights[$module]['u'];
        else return false;
    }//end of function IsUpdate()
    
    /**
    * Class method IsDelete
    * return access for delete (DELETE) data for user $this->user to the module with id $module
    * @param integer $module - id of the module
    * @return true or false
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 30.03.2012
    */ 
    function IsDelete($module=NULL){
        if( empty($module)) $module = $this->module;
        if( isset($this->userAccessRights[$module]['d'])) return $this->userAccessRights[$module]['d'];
        else return false;
    }//end of function IsDelete()
    
    /**
    * Class method IsExecute
    * return access for execute (CREATE) data for user $this->user to the module with id $module
    * @param integer $module - id of the module
    * @return true or false
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 30.03.2012
    */ 
    function IsExecute($module=NULL){
        if( empty($module)) $module = $this->module;
        if( isset($this->userAccessRights[$module]['e'])) return $this->userAccessRights[$module]['e'];
        else return false;
    }//end of function IsExecute()            

} // End of Rights Class
?>
