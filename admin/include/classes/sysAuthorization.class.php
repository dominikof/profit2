<?php
// ================================================================================================
// System : PrCSM05
// Module : sysAuthorization.class.php
// Version : 1.0.0
// Date : 26.01.2005
// Licensed To:
// Igor Trokhymchuk ihoru@mail.ru
// Andriy Lykhodid las_zt@mail.ru
//
// Purpose : Class definition for user authorization to the back-end of the site
//
// ================================================================================================

// ================================================================================================
//
//    Programmer        :  Igor Trokhymchuk
//    Date              :  26.01.2005
//    Reason for change :  Creation
//    Change Request Nbr:
//
//    Function          :  Class definition for user authorization to the back-end of the site
//
//  ================================================================================================

include_once( SITE_PATH.'/admin/include/defines.inc.php' );
include_once( SITE_PATH.'/sys/classes/sysAuthorize.class.php' );

// ================================================================================================
//    Class             : Authorization
//    Version           : 1.0.0
//    Date              : 26.01.2005
//
//    Constructor       : Yes
//    Parms             : session_id / session id
//                        usre_id    / UserID
//                        user_      /
//                        user_type  / id of group of user
//    Returns           : None
//    Description       : Class definition for user authorization to the back-end of the site
// ================================================================================================
//    Programmer        :  Igor Trokhymchuk
//    Date              :  26.01.2005
//    Reason for change :  Creation
//    Change Request Nbr:  N/A
// ================================================================================================
 class Authorization extends Authorize{

   var $session_id = NULL;
   var $user_id = NULL;
   var $login = NULL;
   var $alias = NULL;
   var $user_type;
   var $db=NULL;
   var $Msg = NULL;

   // ================================================================================================
   //    Function          : Authorization (Constructor)
   //    Version           : 1.0.0
   //    Date              : 26.01.2005
   //    Parms             : session_id / session id
   //                        usre_id    / UserID
   //                        user_      /
   //                        user_type  / id of group of user
   //    Returns           : Error Indicator
   //
   //    Description       : Opens and selects a dabase
   // ================================================================================================
   function Authorization($session_id = NULL) {
        //Check if Constants are overrulled
        if (empty($this->db)) $this->db = DBs::getInstance();
        //if (empty($this->Msg))$this->Msg = new ShowMsg();
        //echo '<br> $session_id='.$session_id;
        /*
        if ( (!isset($_SESSION['session_id'])) & ($_SERVER['SCRIPT_NAME']!='/admin/index.php') ){
           $this->Msg->show_Msg( '_NOT_AUTH' );
           $this->Logout();
           echo "<script>window.location.href='/admin/index.php';</script>\n";
        } */
        if ( isset($_SESSION['session_id']) ) $session_id=$_SESSION['session_id'];
        else $session_id = NULL;
         //echo '<br> $session_id ='.$session_id;
         
        //del old session
        $this->DelOldSession(LOGOUT_TIME, 'back'); 
        $this->init_vars( $session_id );
        /*
        if ( !$this->LoginCheck() ){
           $this->Logout();
           echo "<script>window.location.href='/admin/index.php';</script>\n";
        }
        */
   } // End of Authorization Constructor
       
    /**
    * Class method isAccessToScript
    * chck has the user access to the script or not
    * @param integer $module - id of the module
    * @return true or false
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 07.01.2011
    */
    function isAccessToScript($module)
    {
        $goto = "http://".NAME_SERVER."/admin/index.php?logout=1";
        //echo '<br>$goto='.$goto;
        if ( !isset($_SESSION[ 'session_id']) OR empty($_SESSION[ 'session_id']) OR empty( $module ) ) {
             //$this->Msg->show_msg( '_NOT_AUTH' );
             //return false;
             ?><script>window.location.href="<?=$goto?>";</script><?;
             return false;
        }

        //$logon = new Authorization();
        if (!$this->LoginCheck()) {
            //return false;
            ?><script>window.location.href="<?=$goto?>";</script><?; 
            return false;
        }
        return true;

    }//end of function isAccessToScript()       

    // ================================================================================================
    // Function : LoginCheck
    // Version : 1.0.0
    // Date : 09.01.2005
    //
    // Parms :  Void
    // Returns : $res / Void
    // Description : Check if the user with his login are register in this session
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 09.01.2005
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function LoginCheck()
    {
        $SysUser = &check_init('SysUser', 'SysUser');
        //del old session
        //if (!$this->DelOldSession(LOGOUT_TIME, 'back')) return false;

        //echo '<br>$this->user_type='.$this->user_type.'  $this->session_id='.$this->session_id.' $this->user_id='.$this->user_id.' $this->login='.$this->login.' $this->alias='.$this->alias;
        //die();
        if ((!isset($this->user_type)) || (!isset($this->session_id)) || (empty($this->user_type)) || (empty($this->session_id))) {
            //$this->Msg->show_Msg('_DO_LOGIN');
            return false;
            //echo "<script>window.location.href='logout.php';</script>\n";
        }
        
        // Сравнение хэшей БД и текущего пользователя
	    $this->user_hash = $this->CreateUserHash();
	    $q="select `user_hash` from `".TblSysSession."` where session_id='".$this->session_id."'";
	    $sesrow = $this->db->db_FetchAssoc($this->db->db_Query($q));
	    //echo $sesrow['user_hash'];
	    //echo '<br />$this->session_id - '.$this->session_id.'<br />$this->user_hash - '.$this->user_hash.'<br />$q - '.$q;
	    if($sesrow['user_hash']!=$this->user_hash) return false;        
        
        $q="select `adm_menu` from `".TblSysGroupUsers."` where id='".$this->user_type."'";
        $this->db->db_Query($q);
        $row_res=$this->db->db_FetchAssoc();

        $login=$this->GetLogin_sys_session($this->session_id);
        //echo '<br>$this->login='.$this->login.' $login='.$login;
        if ($this->login == $login) {
           if ($row_res['adm_menu']!=1) {
                $this->Msg = &check_init('ShowMsg', 'ShowMsg');
                $this->Msg->show_Msg('_LOGIN_NOADMINS');
                return false;
                //echo "<script>window.location.href='logout.php';</script>\n";
           }
           else {
                 if ( !$this->sys_user_time($this->login) ) return false;
                 if ( !$SysUser->user_last_active($this->user_id) ) return false;
                 return true;
           }
        }
        else {
              return false;
        }
    } //end of function LoginCheck()

    // ================================================================================================
    // Function : Logout
    // Version : 1.0.0
    // Date : 23.02.2005
    //
    // Parms : Void
    // Returns : true/false
    // Description : delete session of user from sys_sessin table
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 23.02.2005
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function Logout()
    {
      $SysUser = &check_init('SysUser', 'SysUser');

      if ( isset($_SESSION['session_id']) ) $session_id=$_SESSION['session_id'];
      else return false;
      if (!$session_id=="") {
        if ( $this->LoginCheck() ) $SysUser->user_last_active($this->user_id);
      }
      $q= "DELETE FROM `".TblSysSession."` WHERE session_id='$session_id'";
      $res = $this->db->db_Query($q);
      // set status of user - offline
      if ( !$SysUser->set_not_active($this->user_id) );

      $session_id = "";
      session_unset();
      session_destroy();
      return true;
    } //end of function Logout()
        
    /**
    * Class method LogoutUserFromSystem
    * logout user with $user_id from the system
    * @param $user_id - id of the user for logout 
    * @return true or false
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 06.09.2011
    */     
    function LogoutUserFromSystem($user_id)
    {
      $SysUser = new SysUser();
      $q= "DELETE FROM `".TblSysSession."` WHERE `user_id`='".$user_id."'";
      $res = $this->db->db_Query($q);
      // set status of user - offline
      $SysUser->set_not_active($user_id);
      return true;
    } //end of function LogoutUserFromSystem()          

 } //end of class Authorization
?>