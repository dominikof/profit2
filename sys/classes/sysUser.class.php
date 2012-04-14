<?php
// ================================================================================================
// System : SEOCMS
// Module : SysUser.class.php
// Version : 1.0.0
// Date : 24.02.2005
// Licensed To:
// Igor Trokhymchuk ihoru@mail.ru
// Andriy Lykhodid las_zt@mail.ru
//
// Purpose : Class definition for all action with user
//
// ================================================================================================

// ================================================================================================
//
//    Programmer        :  Igor Trokhymchuk
//    Date              :  24.02.2005
//    Reason for change :  Creation
//    Change Request Nbr:
//
//    Function          :  Class definition for all action with user
//
//  ================================================================================================

// ================================================================================================
//    Class             : SysUser
//    Version           : 1.0.0
//    Date              : 26.01.2005
//
//    Constructor       : Yes
//    Parms             :
//    Returns           : None
//    Description       : Class definition for all action with user
// ================================================================================================
//    Programmer        :  Igor Trokhymchuk
//    Date              :  26.01.2005
//    Reason for change :  Creation
//    Change Request Nbr:  N/A
// ================================================================================================
 class SysUser{
       var $user_id = NULL;
       var $login = NULL;
       var $oldpass = NULL;
       var $password = NULL;
       var $password2 = NULL;
       var $users_access_to_admin_part = NULL;

       var $db = NULL;
       var $db_host = NULL;
       var $db_name = NULL;
       var $db_user = NULL;
       var $db_pass = NULL;
       var $db_open = NULL;
       // ================================================================================================
       //    Function          : SysUser (Constructor)
       //    Version           : 1.0.0
       //    Date              : 26.01.2005
       //    Parms :             usre_id   / User ID
       //    Returns           : Error Indicator
       //
       //    Description       : Opens and selects a dabase
       // ================================================================================================
       function SysUser($user_id=NULL) {
                //Check if Constants are overrulled
                ( $user_id   !="" ? $this->user_id = $user_id  : $this->user_id = NULL );
                if (empty($this->db)) $this->db = DBs::getInstance();
                //$this->AddTbl();
       } // End of SysUser Constructor

       // ================================================================================================
       // Function : AddTbl()
       // Version : 1.0.0
       // Date : 17.04.2007
       //
       // Parms :   
       // Returns :      true,false / Void
       // Description :  Add tables
       // ================================================================================================
       // Programmer :  Igor Trokhymchuk
       // Date : 17.04.2007
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function AddTbl()
       {
           $tmp_db = new DB();
           
           // create table for strore statistic by users
           if ( !$tmp_db->IsTableExist(TblSysUserStat) ) {
               $q = "
                CREATE TABLE `".TblSysUserStat."` (
                  `id` int(11) unsigned NOT NULL auto_increment,
                  `user_id` int(11) unsigned default NULL,
                  `dt` date default NULL,
                  `tm` time default NULL,
                  `ip_user` varchar(255) default NULL,
                  `ip_remote_server` varchar(255) default NULL,
                  `hostname` varchar(255) default NULL,
                  `agent` text,
                  PRIMARY KEY  (`id`),
                  KEY `user_id` (`user_id`,`dt`)
                ) ENGINE=MyISAM DEFAULT CHARSET=cp1251;                
                ";
               $res = $tmp_db->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;
           }
           //$tmp_db->db_Close(); 
       }//end of function AddTbl()       
       
       // ================================================================================================
       // Function : save_user
       // Version : 1.0.0
       // Date : 14.01.2005
       //
       // Parms :  $group - alias of user
       //          $login
       //          $pass
       //          $enrol_date
       //          $login_multi_use 
       //          $last_active_counter
       //          $used_counter
       //          $active
       //          $alias - alias of the user 
       // Returns : id of the created user - if all data saved to table sys_user sucsesfuly, another false
       // Description : Save data about user to the table sys_user
       // ================================================================================================
       // Programmer : Igor Trokhymchuk
       // Date : 14.01.2005
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function save_sys_user($group, $login, $pass, $enrol_date, $login_multi_use, $last_active_counter, $used_counter, $active, $alias)
       {
           $q="INSERT INTO `".TblSysUser."` SET
              `group_id`='".$group."',
              `login`='".$login."',
              `pass`='".$pass."',
              `enrol_date`='".$enrol_date."',
              `login_multi_use`='".$login_multi_use."',
              `last_active_counter`='".$last_active_counter."',
              `used_counter`='".$used_counter."',
              `active`='".$active."',
              `alias`='".$alias."'
              ";       
           $res = $this->db->db_Query($q);
           //echo '<br>$q='.$q.' $res='.$res.' $this->db->result='.$this->db->result;
           if (!$this->db->result) return false;
           $inserted_id = $this->db->db_GetInsertID();
           /*
           $q="SELECT id FROM `".TblSysUser."` WHERE login='$login'";
           $res = $this->db->db_Query($q);
           if (!$this->db->result) return false;
           $rows = $this->db->db_FetchAssoc();
           */
           //$inserted_id = $rows['id'];
           return $inserted_id;
       }//end of function save_sys_user()



       // ================================================================================================
       // Function : update_user
       // Version : 1.0.0
       // Date : 15.01.2005
       //
       // Parms :  $user_id id of the user in the table sys_user
       //          $group - group of user
       //          $alias - alias of the user
       // Returns : id of the user - if all data saved to table sys_user sucsesfuly, another false
       // Description : Update data about user to the table sys_user
       // ================================================================================================
       // Programmer : Igor Trokhymchuk
       // Date : 15.01.2005
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function update_user($user_id, $group, $alias, $enrol_date=NULL, $login_multi_use=NULL)
       {
           $q = "UPDATE `".TblSysUser."` SET
                 `group_id`='".$group."',
                 `alias`='".$alias."'";
           if( !empty($enrol_date) ) $q = $q.", `enrol_date`='".$enrol_date."'";
           if( !empty($login_multi_use) ) $q = $q.", `login_multi_use`='".$login_multi_use."'";
           $q = $q." WHERE `id`='".$user_id."'";
           $res = $this->db->db_Query($q);
           //echo '<br>q='.$q.' res='.$res.' $this->db->result='.$this->db->result;
           if (!$this->db->result) return false;
           return $user_id;
       }

       // ================================================================================================
       // Function : ChangeUserLogin()
       // Version : 1.0.0
       // Date : 29.11.2006
       //
       // Parms :   $old_login  / old login of the user
       //           $new_login  / new login of the user
       // Returns :      true,false / Void
       // Description :  Change login for External user in the table sys_user
       // ================================================================================================
       // Programmer :  Igor Trokhymchuk
       // Date : 29.11.2006 
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function ChangeUserLogin( $old_login = NULL, $new_login = NULL)
       {
           $q = "UPDATE `".TblSysUser."` set `login`='$new_login' WHERE `login`='$old_login'";
           $res = $this->db->db_Query($q);
           //echo '<br>$q='.$q.' $res='.$res.' $this->db->result='.$this->db->result;
           if ( !$res OR !$this->db->result) return false;
           return true;
       } //end of fuinction ChangeUserLogin()          
       
       // ================================================================================================
       // Function : unique_login
       // Version : 1.0.0
       // Date : 12.01.2005
       //
       // Parms : $alias - alias of user
       // Returns : true - if alias exist in system ; else - false
       // Description : check to unique the login in the table sys_user
       // ================================================================================================
       // Programmer : Igor Trokhymchuk
       // Date : 12.01.2005
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function unique_login($login)
       {
         $q="SELECT * FROM `".TblSysUser."` WHERE `login`='".$login."'";
         $res = $this->db->db_Query($q);
         //echo '<br>q='.$q.' res='.$res.'<br>';
         if (!$this->db->result) return false;
         $rows=$this->db->db_GetNumRows($res);
         if ($rows>0)return false;
         return true;
       }

       // ================================================================================================
       // Function : unique_alias
       // Version : 1.0.0
       // Date : 23.04.2008
       //
       // Parms : $alias - alias of user
       // Returns : true - if alias exist in system ; else - false
       // Description : check to unique the alias in the table sys_user
       // ================================================================================================
       // Programmer : Igor Trokhymchuk
       // Date : 23.04.2008
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function unique_alias($alias)
       {
         $q="SELECT * FROM `".TblSysUser."` WHERE `alias`='".$alias."'";
         $res = $this->db->db_Query($q);
         //echo '<br>q='.$q.' res='.$res.'<br>';
         if (!$this->db->result) return false;
         $rows=$this->db->db_GetNumRows($res);
         if ($rows>0)return false;
         return true;
       }// end of function unique_alias()       
       
       // ================================================================================================
       // Function : GetUserPassword()
       // Version : 1.0.0
       // Date : 11.04.2007
       //
       // Parms :       $login - login of the user
       // Returns :      true,false / Void
       // Description :  get current password of the user
       // ================================================================================================
       // Programmer :  Igor Trokhymchuk
       // Date : 11.04.2007
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function GetUserPassword( $login )
       {
           $q="SELECT * FROM `".TblSysUser."` WHERE `login`='$login'";
           $res = $this->db->db_Query($q);
           //echo '<br>$q='.$q.' $res='.$res.' $this->db->result='.$this->db->result;
           if ( !$res OR !$this->db->result) return false;
           $res = $this->db->db_FetchAssoc();
           return $res['pass'];
       } //end of fuinction GetUserPassword()       
       
       // ================================================================================================
       // Function : GetUserGroupAccessToBackEnd
       // Version : 1.0.0
       // Date : 11.04.2007
       //
       // Parms : $user_group - group of user for the user
       // Returns : 1 - if user has access to the back-end ; else - 0
       // Description : check if user group have access to the back-end of the site
       // ================================================================================================
       // Programmer : Igor Trokhymchuk
       // Date : 11.04.2007
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function GetUserGroupAccessToBackEnd($user_group)
       {       
            $q="select * from `".TblSysGroupUsers."` where id='".$user_group."'";
            $this->db->db_Query($q);
            $row_res=$this->db->db_FetchAssoc();
            $this->users_access_to_admin_part = $row_res['adm_menu'];
            return $this->users_access_to_admin_part;
       } // end of function GetUserGroupAccessToBackEnd()      
       
       // ================================================================================================
       // Function : IsEncodePass
       // Version : 1.0.0
       // Date : 11.04.2007
       //
       // Parms :   $login - login of the user
       //           $user_group - user group for the $user
       // Returns : true,false / Void
       // Description : check settings for users groups to encode passwors or not
       // ================================================================================================
       // Programmer : Igor Trokhymchuk
       // Date : 11.04.2007
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function IsEncodePass($login, $user_group = NULL )
       {
         if ( empty($user_group)) $user_group = $this->GetUserGroupByUserLogin($login); 
         //echo '<br>$login='.$login.' $user_group='.$user_group;
         
         // if user have access to adin part
         if( $this->GetUserGroupAccessToBackEnd($user_group)==1 ){
            if(defined(ENCODE_PASSWORD_BACKEND)) {
                if (ENCODE_PASSWORD_BACKEND=='false') return false;
            }
            
         }
         // if user haven't access to admin-part
         else {
            if(defined(ENCODE_PASSWORD_FRONTEND)) {
                if (ENCODE_PASSWORD_FRONTEND=='false') return false;
            }             
         }
         return true;
       } //end of function IsEncodePass()        
       
       // ================================================================================================
       // Function : EncodePass
       // Version : 1.0.0
       // Date : 17.01.2005
       //
       // Parms :   $login - login of the user
       //           $pass - password of the user / Void
       // Returns : true,false / Void
       // Description : return password of the user (encoded if ithis is needed)
       // ================================================================================================
       // Programmer : Igor Trokhymchuk
       // Date : 17.01.2005
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function EncodePass($login, $pass, $user_group=NULL)
       {
         $Crypt = new Crypt();
         //echo '<br>$pass='.$pass.' $encode_pass='.$encode_pass.' $login='.$login.' $user_group='.$user_group;
         
         // if it is needed to encode password then encode password, else use $pass password
         if ( $this->IsEncodePass($login, $user_group) ) $encode_pass = $Crypt->CryptStr($pass); 
         else $encode_pass = $pass;
         //echo '<br>=$encode_pass='.$encode_pass;         
         return $encode_pass;
       } //end of function EncodePass()       
       
       // ================================================================================================
       // Function : change_pass
       // Version : 1.0.0
       // Date : 17.01.2005
       //
       // Parms :   $login - ogin og the user  / Void
       //           $pass - passwor og the user / Void
       // Returns : true,false / Void
       // Description : Check the login znd password for existing in database.
       // ================================================================================================
       // Programmer : Igor Trokhymchuk
       // Date : 17.01.2005
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function change_pass($login, $pass)
       {
         $encode_pass = $this->EncodePass($login, $pass);
         $q="UPDATE `".TblSysUser."` set `pass`='".$encode_pass."' WHERE `login`='".$login."'";
         $res = $this->db->db_Query($q);
         //echo '<br>q='.$q.' res='.$res.'<br>';
         if (!$this->db->result) return false;
         return true;
       }

       // ================================================================================================
       // Function : CheckPassword
       // Version : 1.0.0
       // Date : 17.01.2005
       //
       // Parms :   $login - ogin og the user  / Void
       //           $pass - passwor og the user / Void
       // Returns : true,false / Void
       // Description : Check the login and password for existing in database.
       // ================================================================================================
       // Programmer : Igor Trokhymchuk
       // Date : 17.01.2005
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function CheckPassword($login, $pass)
       {
           if( !$this->IsEncodePass($login) ){
                $encode_pass = $this->EncodePass($login, $pass);
                $q="SELECT * FROM `".TblSysUser."` WHERE `login`='".$login."'";
                $res = $this->db->db_Query($q);
                //echo '<br>q='.$q.' res='.$res.'<br>';
                if ( !$res OR !$this->db->result ) return false;
         
                $row = $this->db->db_FetchAssoc();
                if ($row['pass']!=$encode_pass) return false;
           }
           return true;
       }       
       
       // ================================================================================================
       // Function : user_last_active
       // Version : 1.0.0
       // Date : 11.01.2005
       //
       // Parms :   $user_id / id of user from the sys_user table  / Void
       // Returns : true,false / Void
       // Description : Save the time, when user click on menu (his last active time)
       // ================================================================================================
       // Programmer : Igor Trokhymchuk
       // Date : 11.01.2005
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function user_last_active($user_id)
       {
         $tmp_db = DBs::getInstance();
         $currentDate = date("Y-m-d\ H:i:s");
         $q="UPDATE `".TblSysUser."` SET `last_active_counter`='$currentDate' WHERE id='".$user_id."'";
         $res = $tmp_db->db_Query($q);
         //echo '<br>q='.$q.' res='.$res.'<br>';
         if (!$res OR !$tmp_db->result) {$tmp_db->db_Close(); return false;}
         //$tmp_db->db_Close();
         return true;
       }
       
       // ================================================================================================
       // Function : GetLastActiveByUserLogin()
       // Version : 1.0.0
       // Date : 15.11.2006
       //
       // Parms :
       // Returns :      true,false / Void
       // Description :  Return User last active
       // ================================================================================================
       // Programmer :  Igor Trokhymchuk
       // Date : 15.11.2006
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function GetLastActiveByUserLogin( $user_login )
       {
         $tmp_db = new DB();
         $q = "select `last_active_counter` from ".TblSysUser." where `login`='$user_login'";
         $res = $tmp_db->db_Query( $q );
         //echo '<br>'.$q.' $res='.$res.' $tmp_db->result='.$tmp_db->result; 
         if ( !$res OR !$tmp_db->result) {$tmp_db->db_Close(); return false;}
         $row = $tmp_db->db_FetchAssoc();
         //$tmp_db->db_Close();
         //echo '<br> $row[last_active]='.$row['last_active'];
         return $row['last_active_counter'];
       } //end of fuinction GetLastActiveByUserLogin()         

       // ================================================================================================
       // Function : user_used_counter
       // Version : 1.0.0
       // Date : 16.02.2005
       //
       // Parms :   $user_id / id of user from the sys_user table  / Void
       // Returns : true,false / Void
       // Description : Calculate the quantity of times the user logon to the system
       // ================================================================================================
       // Programmer : Igor Trokhymchuk
       // Date : 16.02.2005
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function user_used_counter($user_id)
       {
         $tmp_db = new DB();
         $q="SELECT * FROM `".TblSysUser."` WHERE id='".$user_id."'";
         $res = $tmp_db->db_Query($q);
         //echo '<br>q='.$q.' res='.$res.'<br>';
         if (!$res OR !$tmp_db->result) return false;
         $rows = $tmp_db->db_FetchAssoc();

         $q="UPDATE `".TblSysUser."` SET `used_counter`='".($rows['used_counter']+1)."' WHERE id='".$user_id."'";
         $res = $tmp_db->db_Query($q);
         //echo '<br>q='.$q.' res='.$res.'<br>';
         if (!$res OR !$tmp_db->result) {$tmp_db->db_Close();return false;}
         //$tmp_db->db_Close();
         return true;
       }
       
       // ================================================================================================
       // Function : GetUsedCounterByUserLogin()
       // Version : 1.0.0
       // Date : 15.11.2006
       //
       // Parms :
       // Returns :      true,false / Void
       // Description :  Return User used counter 
       // ================================================================================================
       // Programmer :  Igor Trokhymchuk
       // Date : 15.11.2006
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function GetUsedCounterByUserLogin( $user_login )
       {
         $tmp_db = new DB();
         $q = "select `used_counter` from ".TblSysUser." where `login`='$user_login'";
         $res = $tmp_db->db_Query( $q );
         //echo '<br>'.$q.' $res='.$res.' $tmp_db->result='.$tmp_db->result; 
         if ( !$res OR !$tmp_db->result) {$tmp_db->db_Close();return false;}
         $row = $tmp_db->db_FetchAssoc();
         //$tmp_db->db_Close();
         //echo '<br> $row[active]='.$row['active'];
         return $row['used_counter'];
       } //end of fuinction GetUsedCounterByUserLogin()        

       // ================================================================================================
       // Function : set_active
       // Version : 1.0.0
       // Date : 16.02.2005
       //
       // Parms :   $user_id / id of user from the sys_user table  / Void
       // Returns : true,false / Void
       // Description : Set the flag that the user acive (online)
       // ================================================================================================
       // Programmer : Igor Trokhymchuk
       // Date : 16.02.2005
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function set_active($user_id)
       {
         $tmp_db = new DB();
         $q="UPDATE `".TblSysUser."` SET `active`='1' WHERE id='".$user_id."'";
         $res = $tmp_db->db_Query($q);
         //echo '<br>q='.$q.' res='.$res.'<br>';
         if (!$res OR !$tmp_db->result) {$tmp_db->db_Close();return false;}
         //$tmp_db->db_Close();
         return true;
       }

       // ================================================================================================
       // Function : set_not_active
       // Version : 1.0.0
       // Date : 16.02.2005
       //
       // Parms :   $user_id / id of user from the sys_user table  / Void
       // Returns : true,false / Void
       // Description : Set the flag that the user not acive (offline)
       // ================================================================================================
       // Programmer : Igor Trokhymchuk
       // Date : 16.02.2005
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function set_not_active($user_id=NULL)
       {
         $tmp_db = new DB();
         $q="UPDATE `".TblSysUser."` SET `active`='0' WHERE id='".$user_id."'";
         $res = $tmp_db->db_Query($q);
         //echo '<br>q='.$q.' res='.$res.'<br>';
         if (!$res OR !$tmp_db->result) {$tmp_db->db_Close();return false;}
         //$tmp_db->db_Close();
         return true; 
       }
       
       // ================================================================================================
       // Function : GetStatusByUserLogin()
       // Version : 1.0.0
       // Date : 15.11.2006
       //
       // Parms :
       // Returns :      true,false / Void
       // Description :  Return User status (online or offline) 
       // ================================================================================================
       // Programmer :  Igor Trokhymchuk
       // Date : 15.11.2006
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function GetStatusByUserLogin( $user_login )
       {
         $tmp_db = new DB();
         $q = "select `active` from ".TblSysUser." where `login`='$user_login'";
         $res =$tmp_db->db_Query( $q );
         //echo '<br>'.$q.' $res='.$res.' $tmp_db->result='.$tmp_db->result; 
         if ( !$res OR !$tmp_db->result) {$tmp_db->db_Close();return false;}
         $row = $tmp_db->db_FetchAssoc();
         //$tmp_db->db_Close();
         //echo '<br> $row[active]='.$row['active'];
         return $row['active'];
       } //end of fuinction GetStatusByUserLogin()        
       
       
       // ================================================================================================
       // Function : GetRegDateByUserLogin()
       // Version : 1.0.0
       // Date : 15.11.2006
       //
       // Parms :
       // Returns :      true,false / Void
       // Description :  Return User registration date
       // ================================================================================================
       // Programmer :  Igor Trokhymchuk
       // Date : 15.11.2006
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function GetRegDateByUserLogin( $user_login )
       {
         $tmp_db = new DB();
         $q = "select `enrol_date` from ".TblSysUser." where `login`='$user_login'";
         $res = $tmp_db->db_Query( $q );
         //echo '<br>'.$q.' $res='.$res.' $tmp_db->result='.$tmp_db->result; 
         if ( !$res OR !$tmp_db->result) {$tmp_db->db_Close();return false;}
         $row = $tmp_db->db_FetchAssoc();
         //$tmp_db->db_Close();
         //echo '<br> $row[active]='.$row['active'];
         return $row['enrol_date'];
       } //end of fuinction GetRegDateByUserLogin()        
           
       // ================================================================================================
       // Function : GetUserName()
       // Version : 1.0.0
       // Date : 21.01.2006
       //
       // Parms :
       // Returns :      true,false / Void
       // Description :  Get the name of the user from module External users!!!
       // ================================================================================================
       // Programmer :  Igor Trokhymchuk
       // Date : 21.01.2006
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function GetUserName($user_login)
       {
         include_once( SITE_PATH.'/modules/mod_user/user.defines.php' );

         $tmp_db = new DB(); 
         $q = "SELECT * FROM `".TblModUser."` WHERE `email`='".$user_login."'";
         $res = $tmp_db->db_Query( $q );
         //echo '<br>$q='.$q.' $res='.$res.' $tmp_db->result='.$tmp_db->result;
         if( !$res OR !$tmp_db->result ) {$tmp_db->db_Close(); return false;}
         $row = $tmp_db->db_FetchAssoc();
         //$tmp_db->db_Close();
         //echo '<br>$row[nickname]='.$row['nickname'];
         if ( isset($row['nickname']) ) return $row['nickname'];
         else return $row['name'];
       } //end of fuinction GetUserName()
       
       // ================================================================================================
       // Function : GetUserName()
       // Version : 1.0.0
       // Date : 21.01.2006
       //
       // Parms :
       // Returns :      true,false / Void
       // Description :  Get the name of the user from module External users!!!
       // ================================================================================================
       // Programmer :  Igor Trokhymchuk
       // Date : 21.01.2006
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function GetUserNameByModUserId($id_user)
       {
         include_once( SITE_PATH.'/modules/mod_user/user.defines.php' );

         $tmp_db = new DB(); 
         $q = "SELECT * FROM `".TblModUser."` WHERE `id`='".$id_user."'";
         $res = $tmp_db->db_Query( $q );
         //echo '<br>$q='.$q.' $res='.$res.' $tmp_db->result='.$tmp_db->result;
         if( !$res OR !$tmp_db->result ) {$tmp_db->db_Close();return false;}
         $row = $tmp_db->db_FetchAssoc();
         //$tmp_db->db_Close();
         //echo '<br>$row[nickname]='.$row['nickname'];
         return $row['nickname'];
       } //end of fuinction GetUserName()       
       
       // ================================================================================================
       // Function : GetUserLoginByUserId()
       // Version : 1.0.0
       // Date : 06.01.2006
       //
       // Parms :
       // Returns :      true,false / Void
       // Description :  Return User login 
       // ================================================================================================
       // Programmer :  Igor Trokhymchuk
       // Date : 06.01.2006
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function GetUserLoginByUserId( $user_id )
       {
         $tmp_db = new DB(); 
         $q = "select `login` from ".TblSysUser." where `id`='".$user_id."'";
         $res = $tmp_db->db_Query( $q );
         //echo '<br>'.$q.' $res='.$res.' $tmp_db->result='.$tmp_db->result; 
         if ( !$res OR !$tmp_db->result) {$tmp_db->db_Close(); return false;}
         $row = $tmp_db->db_FetchAssoc();
         //$tmp_db->db_Close();
         $login = $row['login'];
         //echo '<br> $login='.$login;
         return $login;
       } //end of fuinction GetUserLoginByUserId()

       // ================================================================================================
       // Function : GetUserAliasByUserId()
       // Version : 1.0.0
       // Date : 05.05.2008
       //
       // Parms :
       // Returns :      true,false / Void
       // Description :  Return User alias 
       // ================================================================================================
       // Programmer :  Igor Trokhymchuk
       // Date : 05.05.2008
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function GetUserAliasByUserId( $user_id )
       {
         $tmp_db = new DB(); 
         $q = "select `alias` from ".TblSysUser." where `id`='".$user_id."'";
         $res = $tmp_db->db_Query( $q );
         //echo '<br>'.$q.' $res='.$res.' $tmp_db->result='.$tmp_db->result; 
         if ( !$res OR !$tmp_db->result) {$tmp_db->db_Close(); return false;}
         $row = $tmp_db->db_FetchAssoc();
         //$tmp_db->db_Close();
         $login = $row['alias'];
         //echo '<br> $login='.$login;
         return $login;
       } //end of fuinction GetUserAliasByUserId()       

       // ================================================================================================
       // Function : GetUserAliasByUserLogin()
       // Version : 1.0.0
       // Date : 05.05.2008
       //
       // Parms :
       // Returns :      true,false / Void
       // Description :  Return User alias 
       // ================================================================================================
       // Programmer :  Igor Trokhymchuk
       // Date : 05.05.2008
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function GetUserAliasByUserLogin( $login )
       {
         $tmp_db = new DB(); 
         $q = "select `alias` from ".TblSysUser." where `login`='".$login."'";
         $res = $tmp_db->db_Query( $q );
         //echo '<br>'.$q.' $res='.$res.' $tmp_db->result='.$tmp_db->result; 
         if ( !$res OR !$tmp_db->result) {$tmp_db->db_Close(); return false;}
         $row = $tmp_db->db_FetchAssoc();
         //$tmp_db->db_Close();
         $login = $row['alias'];
         //echo '<br> $login='.$login;
         return $login;
       } //end of fuinction GetUserAliasByUserLogin() 
       
       // ================================================================================================
       // Function : GetUserGroupByUserLogin()
       // Version : 1.0.0
       // Date : 15.11.2006
       //
       // Parms :
       // Returns :      true,false / Void
       // Description :  Return User Group 
       // ================================================================================================
       // Programmer :  Igor Trokhymchuk
       // Date : 15.11.2006
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function GetUserGroupByUserLogin( $user_login )
       {
         $tmp_db = new DB();
         $q = "SELECT `group_id` FROM `".TblSysUser."` WHERE `login`='$user_login'";
         $res = $tmp_db->db_Query( $q );
         //echo '<br>'.$q.' $res='.$res.' $tmp_db->result='.$tmp_db->result; 
         if ( !$res OR !$tmp_db->result) {$tmp_db->db_Close(); return false;}
         $row = $tmp_db->db_FetchAssoc();
         //$tmp_db->db_Close();
         $group = $row['group_id'];
         //echo '<br> $group='.$group;
         return $group;
       } //end of fuinction GetUserGroupByUserLogin() 
       
       // ================================================================================================
       // Function : SetUserGroupByLogin
       // Version : 1.0.0
       // Date : 18.12.2006
       //
       // Parms :   $login / id of user from the sys_user table  
       //           $group_id / new group of the user
       // Returns : true,false / Void
       // Description : Set user group
       // ================================================================================================
       // Programmer : Igor Trokhymchuk
       // Date : 18.12.2006 
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function SetUserGroupByLogin($login=NULL, $group_id = NULL)
       {
         $tmp_db = new DB();
         $q="UPDATE `".TblSysUser."` SET `group_id`='$group_id' WHERE `login`='".$login."'";
         $res = $tmp_db->db_Query($q);
         //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
         if (!$res OR !$tmp_db->result) {$tmp_db->db_Close(); return false;}
         //$tmp_db->db_Close();
         return true;
       } // end of function SetUserGroupByLogin()        
       
       // ================================================================================================
       // Function : SetUserGroup
       // Version : 1.0.0
       // Date : 18.12.2006
       //
       // Parms :   $user_id / id of user from the sys_user table  
       //           $group_id / new group of the user
       // Returns : true,false / Void
       // Description : Set user group
       // ================================================================================================
       // Programmer : Igor Trokhymchuk
       // Date : 18.12.2006 
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function SetUserGroup($user_id=NULL, $group_id = NULL)
       {
         $tmp_db = new DB();
         $q="UPDATE `".TblSysUser."` SET `group_id`='$group_id' WHERE `id`='".$user_id."'";
         $res = $tmp_db->db_Query($q);
         echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
         if (!$res OR !$tmp_db->result) {$tmp_db->db_Close(); return false;}
         //$tmp_db->db_Close();
         return true;
       } // end of function SetUserGroup()                        
       
       
       // ================================================================================================
       // Function : GetNameUserGrp
       // Version : 1.0.0
       // Date : 25.01.2006
       // Parms : $user_id
       // Returns : 
       // Description : Return name of user group
       // ================================================================================================
       // Programmer : Igor Trokhimchuk
       // Date : 25.01.2006
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================

       function GetNameUserGrp( $user_grp = NULL )
       {       
         $tmp_db = new DB();
         $q = "SELECT * FROM `".TblSysGroupUsers."` WHERE id='".$user_grp."'";
         $res = $tmp_db->db_Query( $q );
         //echo '<br>$q='.$q.' $res='.$res.' $tmp_db->result='.$tmp_db->result;
         if( !$res OR !$tmp_db->result ) {$tmp_db->db_Close(); return false;}
         $row = $tmp_db->db_FetchAssoc();
         //$tmp_db->db_Close();
         //echo '<br>$row[name]='.$row['name'];
         return $row['name']; 
       } //end of function GetNameUserGrp()
       
       // ================================================================================================
       // Function : GetSysUserIdByUserLogin()
       // Version : 1.0.0
       // Date : 18.12.2006
       //
       // Parms : $login - login (email) of the user
       // Returns :      true,false / Void
       // Description :  Return User id by user email (login) 
       // ================================================================================================
       // Programmer :  Igor Trokhymchuk
       // Date : 18.12.2006
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function GetSysUserIdByUserLogin( $login = NULL )
       {
         $tmp_db = new DB();
         $q = "select `id` from `".TblSysUser."` where `login`='$login'";
         $res = $tmp_db->db_Query( $q );
         //echo '<br>'.$q.' $res='.$res.' $tmp_db->result='.$$tmp_db->result; 
         if ( !$res OR !$tmp_db->result ) {$tmp_db->db_Close(); return false;}
         $row = $tmp_db->db_FetchAssoc();
         //$tmp_db->db_Close();
         $id = $row['id'];
         //echo '<br> $email='.$email;
         return $id;
       } //end of fuinction GetSysUserIdByUserLogin() 
       
       // ================================================================================================
       // Function : GetSysUserIdByUserId()
       // Version : 1.0.0
       // Date : 27.11.2007
       //
       // Parms : $id - external user id 
       // Returns :      true,false / Void
       // Description :  Return system user id by external user id 
       // ================================================================================================
       // Programmer :  Igor Trokhymchuk
       // Date : 27.11.2007
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function GetSysUserIdByUserId( $id = NULL )
       {
         $tmp_db = new DB();
         $q = "SELECT `email` FROM `".TblModUser."` WHERE `id`='".$id."'";
         $res = $tmp_db->db_Query( $q );
         //echo '<br>$q='.$q.' $res='.$res.' $tmp_db->result='.$tmp_db->result;
         if ( !$res OR !$tmp_db->result ){$tmp_db->db_Close(); return false;}
         $row = $tmp_db->db_FetchAssoc();
         $q = "SELECT `id` FROM `".TblSysUser."` WHERE `login`='".$row['email']."'";
         $res = $tmp_db->db_Query( $q );
         //echo '<br>'.$q.' $res='.$res.' $tmp_db->result='.$tmp_db->result; 
         if ( !$res OR !$tmp_db->result ){$tmp_db->db_Close(); return false;}
         $row = $tmp_db->db_FetchAssoc();
         //$tmp_db->db_Close();
         $id = $row['id'];
         //echo '<br> $email='.$email;
         return $id;
       } //end of fuinction GetSysUserIdByUserId()       
       
        // ================================================================================================
        // Function : IsLoginMultiUse
        // Version : 1.0.0
        // Date : 15.01.2007
        //
        // Parms :         $sec / Module read  / Void
        // Returns : true,false / Void
        // Description : Check the login on the multi use
        // ================================================================================================
        // Programmer : Igor Trokhymchuk
        // Date : 15.01.2007
        // Reason for change : Creation
        // Change Request Nbr:
        // ================================================================================================
        function IsLoginMultiUse($login)
        {
         $q = "SELECT `login_multi_use` FROM ".TblSysUser." WHERE `login`='$login'";
         $res = $this->db->db_Query( $q );
         //echo '<br>'.$q.' $res='.$res.' $this->db->result='.$this->db->result; 
         if ( !$res ) return false;
         $row = $this->db->db_FetchAssoc();
         $login_multi_use= $row['login_multi_use'];
         //echo '<br> $login_multi_use='.$login_multi_use;
         return $login_multi_use;
        }  // End of IsLoginMultiUse()
        
                              
        // ================================================================================================
        // Function : SaveStat
        // Version : 1.0.0
        // Date : 15.01.2007
        //
        // Parms :         $sec / Module read  / Void
        // Returns : true,false / Void
        // Description : save statistic of succesfull log on to the site
        // ================================================================================================
        // Programmer : Igor Trokhymchuk
        // Date : 15.06.2007
        // Reason for change : Creation
        // Change Request Nbr:
        // ================================================================================================
        function SaveStat($user_id)
        {
         $dt = date('Y-m-d');
         $tm = date('H:i:s');
         if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) $ip_user = $_SERVER['HTTP_X_FORWARDED_FOR'];
         else $ip_user = NULL;
         
         if (isset($_SERVER['REMOTE_ADDR'])) $ip_remote_server = $_SERVER['REMOTE_ADDR'];
         else $ip_remote_server = NULL;
         
         if( isset($_SERVER["REMOTE_HOST"]) ) $hostname = $_SERVER["REMOTE_HOST"];
         else $hostname = NULL;
         
         if( isset($_SERVER["HTTP_USER_AGENT"]) )$agent = $_SERVER["HTTP_USER_AGENT"];
         else $agent = NULL;  
         $q = "INSERT INTO `".TblSysUserStat."` VALUES(NULL, '$user_id', '$dt', '$tm', '$ip_user', '$ip_remote_server', '$hostname', '$agent')";
         $res = $this->db->db_Query( $q );
         //echo '<br>'.$q.' $res='.$res.' $this->db->result='.$this->db->result; 
         if ( !$res OR !$this->db->result ) return false;
         return true;
        }  // End of SaveStat()
        
 } //end of class SysUser
?>