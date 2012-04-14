<?php
// ================================================================================================
// System : SEOCMS
// Module : UserAuthorize.class.php
// Version : 1.0.0
// Date : 14.03.2005
// Licensed To:
// Igor Trokhymchuk ihoru@mail.ru
// Andriy Lykhodid las_zt@mail.ru
//
// Purpose : Class definition for user authorization to the front-end of the site
//
// ================================================================================================

// ================================================================================================
//
//    Programmer        :  Igor Trokhymchuk
//    Date              :  14.03.2005
//    Reason for change :  Creation
//    Change Request Nbr:
//
//    Function          :  Class definition for user authorization to the front-end of the site
//
//  ================================================================================================

  include_once( SITE_PATH.'/include/defines.php' );
  //include_once( SITE_PATH.'/admin/include/defines.inc.php' );

// ================================================================================================
//    Class             : UserAuthorize
//    Version           : 1.0.0
//    Date              : 14.03.2005
//
//    Constructor       : Yes
//    Parms             : session_id / session id
//                        usre_id    / UserID
//                        user_      /
//                        user_type  / id of group of user
//    Returns           : None
//    Description       : Class definition for user authorization to the front-end of the site
// ================================================================================================
//    Programmer        :  Igor Trokhymchuk
//    Date              :  14.03.2005
//    Reason for change :  Creation
//    Change Request Nbr:  N/A
// ================================================================================================
 class UserAuthorize extends Authorize{

       var $session_id = NULL;
       var $user_id = NULL;
       var $login = NULL;
       var $alias = NULL;
       var $user_type = NULL;

       var $db = NULL;
       var $Msg = NULL;
       var $Form = NULL;

       // ================================================================================================
       //    Function          : UserAuthorize (Constructor)
       //    Version           : 1.0.0
       //    Date              : 14.03.2005
       //    Parms             : session_id / session id
       //                        usre_id    / UserID
       //                        user_      /
       //                        user_type  / id of group of user
       //    Returns           : Error Indicator
       //
       //    Description       : Opens and selects a dabase
       // ================================================================================================
       function UserAuthorize($session_id = NULL) {
	    //Check if Constants are overrulled
	    if (empty($this->db)) $this->db = DBs::getInstance();
	    if (empty($this->Msg))$this->Msg = &check_init('ShowMsg', 'ShowMsg');
	    if (empty($this->Spr)) $this->Spr = &check_init('SysSpr', 'SysSpr');
	    $this->multi = &check_init_txt('TblFrontMulti',TblFrontMulti);
	    if (empty($this->Form)) $this->Form = &check_init('FrontForm', 'FrontForm');

	    if ( $session_id==NULL ){
	      if ( !isset($_SESSION['session_id']) || empty($_SESSION['session_id']) ){
		  if ( isset($_COOKIE[SEOCMS_SESSNAME]) ) $session_id = $_COOKIE[SEOCMS_SESSNAME];
		  else $session_id = session_id();
	      }
	      else $session_id = $_SESSION['session_id'];
	    }

	    //echo '<br> UserAuthorize :: <br>$session_id='.$session_id.'<br>$_COOKIE[SEOCMS_SESSNAME]='.$_COOKIE[SEOCMS_SESSNAME].'<br>$_SESSION[session_id]='.$_SESSION['session_id'];

	    //del old session hash 
	    $this->DelOldSessionHash();	    
	    
	    $this->user_hash = $this->CreateUserHash();
	    $ses_hash = $this->GetSessionHash($session_id);
	    // echo '<br>$this->user_hash='.$this->user_hash.' $ses_hash='.$ses_hash;
	    
	    if($ses_hash!='-1' AND $this->user_hash!=$ses_hash){
		setcookie(SEOCMS_SESSNAME, "", time()-60*60*24*31, '/');
		//$session_id = session_id();
		//$_SESSION['session_id'] = $session_id;
		echo "<script>window.location.href='"._LINK."';</script>\n";
		die();
	    }
	    
	    $this->SaveSessionHash($session_id, $this->user_hash);
	    
	    //del old session 
	    $this->DelOldSession(LOGOUT_USER_TIME, 'front');

	    $this->init_vars( $session_id );
	    if (!empty($session_id)) {
	      //echo '<br>UserAuthorize Constructor $session_id='.$session_id; 
	      if (!$this->LoginCheck()) {
		  if(empty($_SESSION['session_id']) AND $this->user_id)
		      $this->Logout();
		/*echo "<script>window.location.href='index.php';</script>\n";*/
	      }
	    }

       } // End of UserAuthorize Constructor
    
    
       function GetUserEmail(){
           $q="SELECT `email` FROM `".TblModUser."` WHERE `sys_user_id`='".$this->user_id."'";
           //echo $q;
           $this->db->db_Query($q);
           $user=$this->db->db_FetchAssoc();
           return $user['email'];
       }
       
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
            $SysUser = new SysUser();
            //del old session
            //if (!$this->DelOldSession(LOGOUT_USER_TIME, 'front')) return false;
	    //echo '<br>$this->user_id='.$this->user_id;
	    if($this->user_id){
		/*
		if (isset($_SERVER['REMOTE_ADDR'])) $remAd = $_SERVER['REMOTE_ADDR'];
		else $remAd = NULL;
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) $forvFor = $_SERVER['HTTP_X_FORWARDED_FOR'];
		else $forvFor = NULL;
		if (isset($_SERVER['HTTP_USER_AGENT'])) $userAgent = $_SERVER['HTTP_USER_AGENT'];
		else $userAgent = NULL;
		if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) $accLang = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
		else $accLang = NULL;
		//echo '<br>$remAd='.$remAd.' $forvFor='.$forvFor.' $userAgent='.$userAgent.' $accLang='.$accLang;
		$this->user_hash = md5($remAd.$forvFor.$userAgent.$accLang);
		*/
		$q="select * from `".TblSysSession."` where session_id='".$this->session_id."'";
		$sesrow = $this->db->db_FetchAssoc($this->db->db_Query($q));
		//echo '<br />$this->session_id - '.$this->session_id.'<br />$this->user_hash - '.$this->user_hash.'<br />$q - '.$q;
		//echo '<br>$sesrow[user_hash]='.$sesrow['user_hash'];
		
		if($sesrow['user_hash']!=$this->user_hash){
		    //echo '<br>Hash Error!';
		    $this->user_id='';
		    $this->login='';
		    $this->alias='';
		    $this->user_type='';
		    setcookie(SEOCMS_SESSNAME, "", time()-60*60*24*31, '/');
		    $this->session_id = session_id();
		    //echo '<br>$this->session_id='.$this->session_id;
		    $_SESSION['session_id'] = $this->session_id;
		    echo "<script>window.location.href='"._LINK."';</script>\n";
		    die();
		    //return false;
		}

	    }
	
            //echo '<br> $this->user_type='.$this->user_type.' $this->session_id='.$this->session_id;
            if ((!isset($this->user_type)) || (!isset($this->session_id)) || (empty($this->user_type)) || (empty($this->session_id))) {
               return false;
            }

            $login=$this->GetLogin_sys_session($this->session_id);

            if ($this->login == $login) {
               if ( !$this->sys_user_time($login) ) return false;
               if ( !$SysUser->user_last_active($this->user_id) ) return false;
               return true;
            }
            else return false;
        }  // End of function LoginCheck

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
          
          //if ( isset($_SESSION['session_id']) ) $session_id=$_SESSION['session_id'];
          //else return false;
          
          
          $SysUser = new SysUser();
          if (!$this->session_id=="") {
            if ( $this->LoginCheck() ) $SysUser->user_last_active($this->user_id);
          }
          $q= "DELETE FROM `".TblSysSession."` WHERE `session_id`='$this->session_id'";
          $res = $this->db->db_Query($q);
          //echo '<br>$q='.$q.' $res='.$res.' $this->db->result='.$this->db->result;

          // set status of user - offline
          if ( !$SysUser->set_not_active($this->user_id) );

          $this->session_id = "";
          session_unset();
          //session_destroy();
          return true;
        } // End of function Logout()

       // ================================================================================================
       // Function : LoginForm
       // Version : 1.0.0
       // Date : 19.01.2006
       //
       // Parms :
       // Returns : true,false / Void
       // Description : Show form for logon of the user on the front-end
       // ================================================================================================
       // Programmer : Yaroslav Gyryn
       // Date : 19.10.2009
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function LoginForm($login = NULL, $pass = NULL, $logout=NULL )
       {
         //echo '<br> $this->session_id='.$this->session_id;
         if ( !$this->login ){
             ?>
                <div id="enterLA">
                    <?
                    $this->Form->WriteFrontHeader( 'Login',_LINK.'login.html', NULL, NULL );
                    $this->Form->Hidden('referer_page', $_SERVER['REQUEST_URI']);
                    $this->Form->Hidden('whattodo', 2);                    
                    ?>
                        <div class="line left">
			    <input type="text" id="login" class="login right" name="login" autocomplete="off" value="<?=$login?>" />
			    <label class="labelLA tah12 right" for="login"><?=$this->multi['FLD_LOGIN'];?>:</label>
                        </div>
                        <div class="line left">
			    <input type="password" id="pass" class="pass right" autocomplete="off" name="pass" value=""/>
			    <label class="labelPA tah12 right" for=""><?=$this->multi['FLD_PASSWORD'];?>:</label>
                        </div>
			<input class="butA" type="submit" name="button" value="<?=$this->multi['TXT_FRONT_NEXT'];?>" class="tah12 orange logBut" />
                    <?
                    $this->Form->WriteFrontFooter();
                    ?>
                </div>
             <?
             
         }
         else {
           $this->alias = $this->GetUserAlias();
           ?>
                <div id="enterOA">
		    <span class="LLA tah12 right"><?=$this->multi['TXT_WELCOME_USER']?>, <span class="orange"><?=$this->alias;?></span></span>
                    <a class="LPA right orange" href="<?=_LINK;?>logout.html" title="<?=$this->multi['TXT_LOGOUT'];?>"><?=$this->multi['TXT_LOGOUT'];?></a>
                </div>
           <? 
         }
       } //end of function LoginForm()

       
       // ================================================================================================
       // Function : LoginForm2
       // Version : 1.0.0
       // Date : 21.12.2006
       //
       // Parms :
       // Returns : true,false / Void
       // Description : Show form for logon of the user on the front-end
       // ================================================================================================
       // Programmer : Igor Trokhymchuk
       // Date : 21.12.2006
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function LoginForm2($login = NULL, $pass = NULL, $logout=NULL )
       {
         //echo '<br> $this->session_id='.$this->session_id;
         if ( !$this->login ){
         $this->Form->WriteFrontHeader( 'Login', 'login.php', NULL, NULL );
         $this->Form->Hidden('referer_page', $_SERVER['REQUEST_URI']);
         $this->Form->Hidden('whattodo', 2);
         ?>
          <h4><?=$this->Msg->show_text('TXT_FRONT_LOGIN_FORM');?></h4>
          <table border="0" cellpadding="0" cellspacing="0" align="center" class="left_column">
           <tr>
            <td class="left_column"><b><?=$this->Msg->show_text('FLD_LOGIN');?>:</b></td>
            <td class="left_column"><?=$this->Form->TextBox( 'login', $login, 'size="20"' );?></td>
           </tr>
           <tr>
            <td class="left_column"><b><?=$this->Msg->show_text('FLD_PASSWORD');?>:</b></td>
            <td class="left_column"><?=$this->Form->Password(  'pass', NULL, '20' );?></td>
            
           </tr>
           <tr>          
            <td colspan="2" class="left_column"><?=$this->Form->ShowButton($this->Msg->show_text('_TXT_LOGIN'), "#", 'class="login"', "Login.submit();");?>
            <br>
            <div id="login2"><a href="login.php?task=reg" class="a01"><?=$this->Msg->show_text('TXT_FRONT_REGISTRATION');?></a> 
            <br><a href="forgot_pass.php" class="a01"><?=$this->Msg->show_text('TXT_FRONT_FORGOT_PASS');?></a>
            </div>
            </td>
           </tr>
          </table>
          <input type="image" src="/images/design/spacer.gif"/>
          <div class="line_grey"></div>
         <?
         $this->Form->WriteFrontFooter();
         }
         else {
           $this->alias = $this->GetUserAlias();
           $User = new User();
           $id_user = $User->GetUserIdByEmail($this->login);
         ?>
         <h4><?=$this->Msg->show_text('TXT_FRONT_MEMBER_PROFILE');?></h4> 
           <?=$this->Form->ShowButtonProfile( $this->Msg->show_text('TXT_FRONT_MY_DATINGBOX'), $href="box.php", "width=220" )?>  
           <?=$this->Form->ShowButtonProfile( $this->Msg->show_text('TXT_FRONT_EDIT_MY_FAVORITES'), $href="myfavorites.html", "width=220" )?>
           <?=$this->Form->ShowButtonProfile( $this->Msg->show_text('TXT_FRONT_SHOW_MY_PROFILE'), $href="profile.php?task=show_profile&amp;profile=$id_user", "width=220" )?>
           <?=$this->Form->ShowButtonProfile( $this->Msg->show_text('TXT_FRONT_EDIT_MY_PROFILE'), $href="profile.php?task=profile", "width=220" )?>
           <?=$this->Form->ShowButtonProfile( $this->Msg->show_text('TXT_FRONT_TRANSLATE_TEXT'), $href="translate.html", "width=220" )?>
        <table border="0" cellpadding="10" cellspacing="0" width="100%" align="center">
         <tr>
          <td class="left_column">
          <?
          $User= new UserShow();
          $id_user = $User->GetUserIdByEmail($this->login);
          
          $img = $User->GetMainImage($id_user, 'front');
          if (!empty($img)) {
              $class="img_main_man";
              ?><div id="<?=$class;?>"><?$User->ShowImage($img, $id_user, 'size_auto=100', '100', NULL, 'border=0');?></div><?
          }
          ?>
          </td>
          <td width="100%" class="left_column">
           <b><?=$User->GetNameUserGrp($this->user_type);?></b>
           <?if (!empty($this->alias)) {
                ?><br/><b><?=$this->alias;?></b><?
             }
           ?>
          <? 
          $this->Form->ShowButton($this->Msg->show_text('_TXT_LOGOUT'), "login.php?logout=1", 'class="login"');
          ?>
         </td>
        </tr>
       </table>
       <div class="line_grey"></div>
       
         <?
         }
         return true;
       } //end of function LoginForm2()       
       
       // ================================================================================================
       // Function : GetUserAlias
       // Version : 1.0.0
       // Date : 10.04.2006
       //
       // Parms :
       // Returns : true,false / Void
       // Description : return name of the user
       // ================================================================================================
       // Programmer : Igor Trokhymchuk
       // Date : 10.04.2006
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function GetUserAlias()
       {
           $SysUser = new SysUser();
           $this->alias = $SysUser->GetUserName($this->login);
           if ( empty($this->alias) ) $this->alias = $this->login;
           return $this->alias;           
       } //end of function GetUserAlias        
             
 } //end of class UserAuthorize
?>