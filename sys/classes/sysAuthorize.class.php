<?php
// ================================================================================================
// System : PrCSM05
// Module : sysAuthorize.class.php
// Version : 1.0.0
// Date : 14.02.2005
// Licensed To:
// Igor Trokhymchuk ihoru@mail.ru
// Andriy Lykhodid las_zt@mail.ru
//
// Purpose : Class definition for user authorization to the site (Parent class)
//
// ================================================================================================

// ================================================================================================
//
//    Programmer        :  Igor Trokhymchuk
//    Date              :  14.02.2005
//    Reason for change :  Creation
//    Change Request Nbr:
//
//    Function          :  Class definition for user authorization to the site (Parent class)
//
//  ================================================================================================

include_once( SITE_PATH.'/sys/define.php' );

// ================================================================================================
//    Class             : Authorize
//    Version           : 1.0.0
//    Date              : 14.02.2005
//
//    Constructor       : Yes
//    Parms             : session_id / session id
//                        usre_id    / UserID
//                        user_      /
//                        user_type  / id of group of user
//    Returns           : None
//    Description       : Class definition for user authorization to the site (Parent class)
// ================================================================================================
//    Programmer        :  Igor Trokhymchuk
//    Date              :  14.02.2005
//    Reason for change :  Creation
//    Change Request Nbr:  N/A
// ================================================================================================
 class Authorize {

    var $session_id = NULL;
    var $user_id = NULL;
    var $login = NULL;
    var $alias = NULL;
    var $user_type = NULL;
    var $Err = NULL;
    var $ip = NULL;
    var $ip2 = NULL;
    var $session_type =NULL;
    var $user_hash =NULL;
    
    var $db=NULL;
    var $Msg = NULL;
    
    // ================================================================================================
    //    Function          : Authorize (Constructor)
    //    Version           : 1.0.0
    //    Date              : 14.02.2005
    //    Parms             : session_id / session id
    //                        usre_id    / UserID
    //                        user_      /
    //                        user_type  / id of group of user
    //    Returns           : Error Indicator
    //
    //    Description       : Opens and selects a dabase
    // ================================================================================================
    function Authorize($config = NULL) {
            //Check if Constants are overrulled
            if (empty($this->db)) $this->db = &DBs::getInstance();
            if(empty($this->Msg)) $this->Msg = &check_init('ShowMsg', 'ShowMsg');
    } // End of Authorize Constructor


    /**
    * Class method CreateUserHash
    * create user hash by its personal settings
    * @return true/false or arrays:
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 20.01.2012
    */
    function CreateUserHash()
    {
    	if (isset($_SERVER['REMOTE_ADDR'])) $remAd = $_SERVER['REMOTE_ADDR'];
    	else $remAd = NULL;
    	if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) $forvFor = $_SERVER['HTTP_X_FORWARDED_FOR'];
    	else $forvFor = NULL;
    	if (isset($_SERVER['HTTP_USER_AGENT'])) $userAgent = $_SERVER['HTTP_USER_AGENT'];
    	else $userAgent = NULL;
    	if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) $accLang = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
    	else $accLang = NULL;
    	//echo '<br>$remAd='.$remAd.' $forvFor='.$forvFor.' $userAgent='.$userAgent.' $accLang='.$accLang;
    	return md5($remAd.$forvFor.$userAgent.$accLang);
    }//end of function CreateUserHash()       

    /**
    * Class method SaveSessionHash
    * create user hash by its personal settings
    * @return true/false or arrays:
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 20.01.2012
    */
    function SaveSessionHash($session_id, $user_hash)
    {
    	$q = "SELECT * FROM `".TblSysSessionHash."` WHERE `session_id`='".$session_id."' AND `hash`='".$user_hash."'";
    	$res = $this->db->db_Query($q);
    	$rows = $this->db->db_GetNumRows();
    	if($rows==0){
    	    $q = "INSERT INTO `".TblSysSessionHash."` SET
    		 `session_id`='".$session_id."',
    		 `hash`='".$user_hash."',
    		 `ses_tm`='".time()."' 
    		 ";
    	}
    	else{
    	    $q = "UPDATE `".TblSysSessionHash."` SET `ses_tm`='".time()."' WHERE `session_id`='".$session_id."' AND `hash`='".$user_hash."'";
    	}
    	$res = $this->db->db_Query($q);
	//echo '<br>$q='.$q;
	
    }//end of function CreateUserHash()        
    
    /**
    * Class method GetSessionHash
    * get user hash by its sesstion id
    * @return true/false or arrays:
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 20.01.2012
    */
    function GetSessionHash($session_id)
    {
    	$q = "SELECT `hash` FROM `".TblSysSessionHash."` WHERE `session_id`='".$session_id."'";
    	$res = $this->db->db_Query($q);
    	$rows = $this->db->db_GetNumRows();
    	$row = $this->db->db_FetchAssoc();
    	//echo '<br>$q='.$q;
    	if($rows>0) return $row['hash'];
    	else return -1;
    }//end of function GetSessionHash()        
       
    /**
    * Class method DelOldSessionHash
    * del old sesstion  hash
    * @return true/false or arrays:
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 20.01.2012
    */
    function DelOldSessionHash()
    {
    	$past = time()-(60*60*24);
    	$q = "DELETE FROM `".TblSysSessionHash."` WHERE `ses_tm`<'".$past."'";
    	$res = $this->db->db_Query($q);
	//echo '<br>$q='.$q;	
    }//end of function DelOldSessionHash()
    
    
        // ================================================================================================
        // Function : init_vars
        // Version : 1.0.0
        // Date : 14.02.2005
        //
        // Parms :   $session_id - ogin og the user  / Void
        // Returns : true,false / Void
        // Description : Check the login znd password for existing in database.
        //               If exist, than will start the session, save encrypted session_id and
        //               encrypted user_ to the session array and to the table.
        // ================================================================================================
        // Programmer : Igor Trokhymchuk
        // Date : 11.01.2005
        // Reason for change : Creation
        // Change Request Nbr:
        // ================================================================================================
        function init_vars( $session_id )
        {
             $this->session_id = $session_id;
//             echo '<br>init_vars1 : $this->session_id='.$this->session_id.' $_SESSION[session_id]='.$_SESSION['session_id'];
             
             if (isset($_SERVER['REMOTE_ADDR'])) $this->ip = $_SERVER['REMOTE_ADDR'];
             else $this->ip = NULL;
             if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) $this->ip2 = $_SERVER['HTTP_X_FORWARDED_FOR'];
             else $this->ip2 = NULL;
             
             if ( defined("SESSION_TYPE") ) $this->session_type = SESSION_TYPE;
             
             if ( $this->session_type=='session_by_ip'){
                 $q = "SELECT `session_id` FROM `".TblSysSession."` WHERE 1 AND `ip`='$this->ip' AND `ip2`='$this->ip2'";
                 $res = $this->db->db_Query($q);
                 //echo '<br>$q='.$q.' $res='.$res.' $this->db->result='.$this->db->result;
                 if ( !$res OR !$this->db->result) return false;
                 $row = $this->db->db_FetchAssoc();
                 if( !empty($row['session_id']) ) {
                     $this->session_id = $row['session_id'];
                 }
                 
                 //$this->Logout();
                 $_SESSION['session_id'] = $this->session_id; 
             }
             // если после всех инициализаций $_SESSION['session_id'] оказался все равно пустым, то запихиваю туда значение из
             // $this->session_id - обычно это системный session_id, а не программно згенерированный нашей SEOCMS.
             // след. строчка была закомментированна, значит с этим есть проблеммы, но пока не выяснил какие именно проблеммы.
             if ( empty($_SESSION['session_id']) ) $_SESSION['session_id'] = $this->session_id;
              
             //echo '<br>init_vars2 : $this->session_id='.$this->session_id.' $_SESSION[session_id]='.$_SESSION['session_id']; 
                     
             if ($this->ExistsSession($this->session_id)){
                $info=$this->GetUserInfo_sys_session($this->session_id);
                $this->user_id=$info['user_id'];
                $this->login=$info['login'];
                $this->alias=$info['alias'];
                $this->user_type=$info['user_type'];
             }
        }
         
        // ================================================================================================
        // Function : user_valid
        // Version : 1.0.1
        // Date : 11.01.2005
        //
        // Parms :   $login - ogin og the user  / Void
        //           $pass - passwor og the user / Void
        // Returns : true,false / Void
        // Description : Check the login znd password for existing in database.
        //               If exist, than will start the session, save encrypted session_id and
        //               encrypted user_ to the session array and to the table.
        // ================================================================================================
        // Programmer : Igor Trokhymchuk
        // Date : 11.01.2005
        // Reason for change : Creation
        // Change Request Nbr:
        // ================================================================================================
        function user_valid( $login, $pass, $save_err_to_var = NULL )
        {
         $this->Err = NULL;
         if(empty($this->Msg)) $this->Msg = new ShowMsg();
         $SysUser = new SysUser();
         //$Crypt = new Crypt();
         
         if ((!$login)) {
                if ( $save_err_to_var==1 ) $this->Err = $this->Err.$this->Msg->get_msg("_NO_LOGIN").'<br>'; 
                else $this->Msg->show_msg("_NO_LOGIN");
                return false;
         }

         if ((!$pass)) {
             if ( $save_err_to_var==1 ) $this->Err = $this->Err.$this->Msg->get_msg("_NO_PASS").'<br>';
             else $this->Msg->show_msg('_NO_PASS');
             return false;
         }
         // check how the user logon -  by his login (profile) or by alias
         $this->db->db_Query("SELECT * FROM `".TblSysUser."` WHERE `login`='".$login."'");
         // if no login then search the alias
         if ( !$this->db->db_GetNumRows() ){
             $q = "SELECT * FROM `".TblSysUser."` WHERE `alias`='".$login."'";
             $this->db->db_Query($q);
             //echo '<br>$q='.$q.' $this->db->result='.$this->db->result;
             //echo '<br>$this->db->db_GetNumRows()='.$this->db->db_GetNumRows();
             if ( !$this->db->db_GetNumRows() ){
                if ( $save_err_to_var==1 ) $this->Err = $this->Err.$this->Msg->get_msg("_LOGIN_INCORRECT").'<br>'; 
                else $this->Msg->show_msg('_LOGIN_INCORRECT');
                return false;
             }
             else $line =  $this->db->db_FetchAssoc();
         }
         else $line =  $this->db->db_FetchAssoc();
         
         $sys_login = $line['login'];
         $sys_alias = $line['alias'];
         $passwd = $line['pass'];
         //================ Check for Encode password ================= 
         $encode_pass = $SysUser->EncodePass($sys_login, $pass);
         //echo '<br>$passwd='.$passwd.' $encode_pass='.$encode_pass; 
         if ( $passwd != $encode_pass ) {
            if ( $save_err_to_var==1 ) $this->Err = $this->Err.$this->Msg->show_text("_LOGIN_INCORRECT").'<br>'; 
            else $this->Msg->show_msg('_LOGIN_INCORRECT');
            return FALSE;
         }
         else {
             //echo '<br>$sys_login='.$sys_login;
             if ($this->IsSession($sys_login)) {
                 // if this login is unique use then delete other sessions with this login
                 if ( !$SysUser->IsLoginMultiUse($sys_login) ) {
                    $this->db->db_Query("DELETE FROM `".TblSysSession."` WHERE `login`='".$sys_login."'" );
                    if (!$this->db->result) return false;
                  //$Msg->show_msg('_LOGIN_IN_SESSION');
                  //return false;
                 }
             } 
             else {
                 // delete all sessions from current ip-address
                  if ( $this->session_type=='session_by_ip') {
                    $q = "DELETE FROM `".TblSysSession."` WHERE `ip`='".$this->ip."' AND `ip2`='".$this->ip2."'";
                    $res = $this->db->db_Query($q);
                    //echo '<br>$q='.$q.' $res='.$res.' $this->db->result='.$this->db->result;
                    if (!$res OR !$this->db->result) return false;
                 }             
             }

            $this->user_id=$line['id'];
            $this->login=$sys_login;
            $this->alias=$sys_alias;
            $this->user_type=$line['group_id'];
            $this->logintime=time();
            if (isset($_SERVER['REMOTE_ADDR'])) $this->ip = $_SERVER['REMOTE_ADDR'];
            else $this->ip = NULL;
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) $this->ip2 = $_SERVER['HTTP_X_FORWARDED_FOR'];
            else $this->ip2 = NULL;
            //echo '<br>$this->session_id='.$this->session_id;
            if ( empty($this->session_id) ) $this->session_id = md5( $this->user_id.$this->login.$this->user_type.$this->logintime );
            //$this->login = md5( $this->login.$this->session_id );

            $this->user_hash = $this->CreateUserHash();
            //echo $this->user_hash; die();
	        //echo '<br> Save to Session';
            //save user data to sys_session table
            if ( !$this->SaveSession() ) return false;
            //set status of user - online
            if ( !$SysUser->set_active($this->user_id) ) return false;
            //set count of Logon
            if ( !$SysUser->user_used_counter($this->user_id) ) return false;

            $_SESSION['session_id'] = $this->session_id;
            //echo '<br>SEOCMS_SESSNAME='.SEOCMS_SESSNAME;
            //setcookie(SEOCMS_SESSNAME, $this->session_id, time()-60*60*24*31, '/');
            //echo '<br>$this->session_id='.$this->session_id.'$_SESSION[session_id]='.$_SESSION['session_id'];
            return TRUE;
         }
         return true;
        } // End of user_valid function

        // ================================================================================================
        // Function : SaveSession
        // Version : 1.0.0
        // Date : 06.01.2005
        //
        // Parms : Void
        // Returns : true/false
        // Description : save login, session_id and time of logon to the table sys_session
        // ================================================================================================
        // Programmer : Igor Trokhymchuk
        // Date : 06.01.2005
        // Reason for change : Creation
        // Change Request Nbr:
        // ================================================================================================
        function SaveSession()
        {
//          $q = "INSERT INTO `".TblSysSession."` SET
//                `user_type`='".$this->user_type."',
//                `user_id`='".$this->user_id."',
//                `login`='".$this->login."',
//                `alias`='".$this->alias."',
//                `time`='".$this->logintime."',
//                `session_id`='".$this->session_id."',
//                `ip`='".$this->ip."',
//                `ip2`='".$this->ip2."'
//                ";
          $q = "INSERT INTO `".TblSysSession."` SET
                `user_type`='".$this->user_type."',
                `user_id`='".$this->user_id."',
                `login`='".$this->login."',
                `alias`='".$this->alias."',
                `time`='".$this->logintime."',
                `session_id`='".$this->session_id."',
                `ip`='".$this->ip."',
                `ip2`='".$this->ip2."',
                `user_hash`='".$this->user_hash."'
                ";
          $this->db->db_Query($q);
          //echo '<br> $q='.$q.' $this->db->result='.$this->db->result;
          if ( !$this->db->result ) return false;
          return true;
        } // End of function SaveSession()

        // ================================================================================================
        // Function : UpdateSessionAlias
        // Version : 1.0.0
        // Date : 06.01.2005
        //
        // Parms : Void
        // Returns : true/false
        // Description : save login, session_id and time of logon to the table sys_session
        // ================================================================================================
        // Programmer : Igor Trokhymchuk
        // Date : 06.01.2005
        // Reason for change : Creation
        // Change Request Nbr:
        // ================================================================================================
        function UpdateSessionAlias($alias = NULL)
        {
          if ( !empty($alias) ) $this->alias = $alias;
          $q = "UPDATE `".TblSysSession."` SET `alias`='".$this->alias."' WHERE `login`='".$this->login."'";
          $this->db->db_Query($q);
          //echo '<br> $q='.$q.' $this->db->result='.$this->db->result;
          if ( !$this->db->result ) return false;
          return true;
        } // End of function UpdateSessionAlias()
        
        // ================================================================================================
        // Function : UpdateSessionUserType
        // Version : 1.0.0
        // Date : 06.01.2005
        //
        // Parms : Void
        // Returns : true/false
        // Description : save login, session_id and time of logon to the table sys_session
        // ================================================================================================
        // Programmer : Igor Trokhymchuk
        // Date : 06.01.2005
        // Reason for change : Creation
        // Change Request Nbr:
        // ================================================================================================
        function UpdateSessionUserType($user_type = NULL)
        {
          if ( !empty($user_type) ) $this->user_type = $user_type;
          $q = "UPDATE `".TblSysSession."` SET `user_type`='".$this->user_type."' WHERE `login`='".$this->login."'";
          $this->db->db_Query($q);
          //echo '<br> $q='.$q.' $this->db->result='.$this->db->result;
          if ( !$this->db->result ) return false;
          return true;
        } // End of function UpdateSessionUserType()        

        // ================================================================================================
        // Function : DelOldSession
        // Version : 1.0.0
        // Date : 11.01.2005
        //
        // Parms :         $sec / Module read  / Void
        // Returns : true,false / Void
        // Description : Remove old session from the sys_session table if it is timeout
        // ================================================================================================
        // Programmer : Igor Trokhymchuk
        // Date : 11.01.2005
        // Reason for change : Creation
        // Change Request Nbr:
        // ================================================================================================
        function DelOldSession($sec, $front_back='back')
        {
            $SysUser = new SysUser();
            // timeout old sessions
            $past = time()-$sec;
                
            $q="select `id` from `".TblSysGroupUsers."` where `adm_menu`='1'";
            $this->db->db_Query($q);
            $rows = $this->db->db_GetNumRows();  
            $str=NULL;              
            for ($i=0; $i<$rows; $i++){
               $row = $this->db->db_FetchAssoc();
               if (empty($str)) $str = $row['id'];
               else $str = $str.','.$row['id'];
            }               
            $q = "SELECT `user_id` FROM `".TblSysSession."` WHERE `time` < '$past'";
            if ($front_back=='back') $q = $q." AND `user_type` IN($str)";
            else $q = $q." AND `user_type` NOT IN($str)";
            $this->db->db_Query($q);
            //echo '<br>$q='.$q.' $this->db->result='.$this->db->result;
            if (!$this->db->result) return false;
            $rows = $this->db->db_GetNumRows();
            for ($i=0; $i<$rows; $i++){
               $row= $this->db->db_FetchAssoc();
               $SysUser->set_not_active($row['user_id']);
            }
            $q = "DELETE FROM `".TblSysSession."` WHERE `time` < '$past'";
            if ($front_back=='back') $q = $q." AND `user_type` IN($str)";
            else $q = $q." AND `user_type` NOT IN($str)";            
            $this->db->db_Query($q);
            //echo '<br>$q='.$q.' $this->db->result='.$this->db->result;
            if (!$this->db->result) return false;

            return true;
        }  // End of DelOldSession

        // ================================================================================================
        // Function : ExistsSession
        // Version : 1.0.0
        // Date : 11.01.2005
        //
        // Parms :         $sec / Module read  / Void
        // Returns : true,false / Void
        // Description : Check the login in session table sys_session. If it is exist so the session was started
        // ================================================================================================
        // Programmer : Igor Trokhymchuk
        // Date : 11.01.2005
        // Reason for change : Creation
        // Change Request Nbr:
        // ================================================================================================
        function ExistsSession($sid)
        {
            if (empty ($sid) ) return false;
            $q = "SELECT `id` FROM `".TblSysSession."` where `session_id`='$sid'";
            $this->db->db_Query($q);
            //echo '<br> $q='.$q.' $this->db->result='.$this->db->result;
	    $rows = $this->db->db_GetNumRows();
	    //echo '<br>$rows='.$rows;
            if (!$this->db->db_GetNumRows()) return false;
            return true;
        }  // End of IsSession

        // ================================================================================================
        // Function : IsSession
        // Version : 1.0.0
        // Date : 11.01.2005
        //
        // Parms :         $sec / Module read  / Void
        // Returns : true,false / Void
        // Description : Check the login in session table sys_session. If it is exist so the session was started
        // ================================================================================================
        // Programmer : Igor Trokhymchuk
        // Date : 11.01.2005
        // Reason for change : Creation
        // Change Request Nbr:
        // ================================================================================================
        function IsSession($login)
        {
            $this->db->db_Query("SELECT * FROM `".TblSysSession."` where login='$login'");
            if (!$this->db->db_GetNumRows()) return false;
            return true;
        }  // End of IsSession

        // ================================================================================================
        // Function : sys_user_time
        // Version : 1.0.0
        // Date : 11.01.2005
        //
        // Parms :   $login / user's login from the sys_system table  / Void
        // Returns : true,false / Void
        // Description : Save the time, when user click on menu (his last active)
        // ================================================================================================
        // Programmer : Igor Trokhymchuk
        // Date : 11.01.2005
        // Reason for change : Creation
        // Change Request Nbr:
        // ================================================================================================
        function sys_user_time($login)
        {
                $this->db->db_Query("UPDATE `".TblSysSession."` SET `time`='".time()."' WHERE login='".$login."'");
                if (!$this->db->result) return false;
                return true;
        }  // End of sys_user_time
        // ================================================================================================
        // Function : GetLogin_sys_session
        // Version : 1.0.0
        // Date : 06.01.2005
        //
        // Parms : $sid - session_id
        // Returns : $res - login from table sys_session with such session_id
        // Description : get login from table sys_session
        // ================================================================================================
        // Programmer : Igor Trokhymchuk
        // Date : 06.01.2005
        // Reason for change : Creation
        // Change Request Nbr:
        // ================================================================================================
        Function GetLogin_sys_session($sid)
        {
                $this->db->db_Query("SELECT `login` FROM `".TblSysSession."` WHERE `session_id`='$sid'");
                if (!$this->db->result) {
                      //show_error('_ERROR_NORESULT_SQL_QUERY');
                      return false;
                }
                if ( !$this->db->db_GetNumRows() ){
                      //show_error('_ERROR_NORESULT_SQL_QUERY');
                      return false;
                }
                $line = $this->db->db_FetchAssoc();
                $res = $line['login'];
                return $res;
        }  // End of GetLogin_sys_session
        
        // ================================================================================================
        // Function : GetAlias_sys_session
        // Version : 1.0.0
        // Date : 17.01.2005
        //
        // Parms : $sid - session_id
        // Returns : $res - login from table sys_session with such session_id
        // Description : get alias from table sys_session
        // ================================================================================================
        // Programmer : Igor Trokhymchuk
        // Date : 17.01.2005
        // Reason for change : Creation
        // Change Request Nbr:
        // ================================================================================================
        Function GetAlias_sys_session($sid)
        {
                $this->db->db_Query("SELECT `alias` FROM `".TblSysSession."` WHERE `session_id`='$sid'");
                if (!$this->db->result) {
                      //show_error('_ERROR_NORESULT_SQL_QUERY');
                      return false;
                }
                if ( !$this->db->db_GetNumRows() ){
                      //show_error('_ERROR_NORESULT_SQL_QUERY');
                      return false;
                }
                $line = $this->db->db_FetchAssoc();
                $res = $line['alias'];
                return $res;
        }  // End of GetAlias_sys_session
                
        // ================================================================================================
        // Function : GetUserInfo_sys_session
        // Version : 1.0.0
        // Date : 17.01.2005
        //
        // Parms : $sid - session_id
        // Returns : $res - login from table sys_session with such session_id
        // Description : get the user-id from table sys_session
        // ================================================================================================
        // Programmer : Igor Trokhymchuk
        // Date : 17.01.2005
        // Reason for change : Creation
        // Change Request Nbr:
        // ================================================================================================
        Function GetUserInfo_sys_session($sid)
        {
            $q = "SELECT `user_id`,`user_type`,`login`,`alias` FROM `".TblSysSession."` WHERE `session_id`='$sid'";    
	    $this->db->db_Query($q);
		//echo '<br>$q='.$q;
                if (!$this->db->result) {
                      //show_error('_ERROR_NORESULT_SQL_QUERY');
                      return false;
                }
                if ( !$this->db->db_GetNumRows() ){
                      //show_error('_ERROR_NORESULT_SQL_QUERY');
                      return false;
                }
                $line = $this->db->db_FetchAssoc();
                $res['user_id'] = $line['user_id'];
                $res['user_type'] = $line['user_type'];
                $res['login'] = $line['login'];
                $res['alias'] = $line['alias'];
                //echo '<br>$res='.$res;
                return $res;
        }  // End of GetUserInfo_sys_session

        // ================================================================================================
        // Function : GetUserType_sys_session
        // Version : 1.0.0
        // Date : 17.01.2005
        //
        // Parms : $sid - session_id
        // Returns : $res - login from table sys_session with such session_id
        // Description : get the type of user (group) from table sys_session
        // ================================================================================================
        // Programmer : Igor Trokhymchuk
        // Date : 17.01.2005
        // Reason for change : Creation
        // Change Request Nbr:
        // ================================================================================================
        Function GetUserType_sys_session($sid)
        {
                $this->db->db_Query("SELECT `user_type` FROM `".TblSysSession."` WHERE `session_id`='$sid'");
                if (!$this->db->result) {
                      //show_error('_ERROR_NORESULT_SQL_QUERY');
                      return false;
                }
                if ( !$this->db->db_GetNumRows() ){
                      //show_error('_ERROR_NORESULT_SQL_QUERY');
                      return false;
                }
                $line = $this->db->db_FetchAssoc();
                $res = $line['user_type'];
                return $res;
        }  // End of GetUserType_sys_session

        // ================================================================================================
        // Function : GetId_sys_session
        // Version : 1.0.0
        // Date : 06.01.2005
        //
        // Parms :         $sid - session_id / void
        //                        $login - login of user / void
        // Returns : $res - id of the record in the table sys_session
        // Description : get id from table sys_session
        // ================================================================================================
        // Programmer : Igor Trokhymchuk
        // Date : 06.01.2005
        // Reason for change : Creation
        // Change Request Nbr:
        // ================================================================================================
        Function GetId_sys_session($sid,$login)
        {
                $this->db->db_Query("SELECT `id` FROM `".TblSysSession."` WHERE `session_id`='$sid' AND `login`='$login' ");
                if (!$this->db->result) {
                      //show_error('_ERROR_NORESULT_SQL_QUERY');
                      return false;
                }
                if ( !$this->db->db_GetNumRows() ){
                      //show_error('_ERROR_NORESULT_SQL_QUERY');
                      return false;
                }
                $line = $this->db->db_FetchAssoc();
                $res = $line['id'];
                return $res;
        }  // End of GetId_sys_session

 } //end of class Authorize
?>