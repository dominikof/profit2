<?php
// ================================================================================================
// System : SEOCMS
// Module : sysShowMsg.class.php
// Version : 1.0.0
// Date : 26.01.2005
// Licensed To:
// Igor Trokhymchuk ihoru@mail.ru
// Andriy Lykhodid las_zt@mail.ru
//
// Purpose : Class definition for show all messages and static text on selected language
//
// ================================================================================================

//  ================================================================================================
//
//    Programmer        :  Igor Trokhymchuk
//    Date              :  26.01.2005
//    Reason for change :  Creation
//    Change Request Nbr:
//
//    Function          :   Class definition for show all messages and static text on selected language
//
//  ================================================================================================

// ================================================================================================
//    Class             : ShowMsg
//    Version           : 1.0.0
//    Date              : 26.01.2005
//
//    Constructor       : Yes
//    Parms             : Host                Host Name
//                                                  User                UserID to database
//                                                   pwd                Password to database
//                        dbName        Name of the database to connect type
//                                                   open                Open database (true/false)
//    Returns           : None
//    Description       : Show or rerutn Messages
// ================================================================================================
//    Programmer        :  Igor Trokhymchuk
//    Date              :  26.01.2005
//    Reason for change :  Creation
//    Change Request Nbr:  N/A
// ================================================================================================
 class ShowMsg {
       var $db=NULL;
       var $lang_id;
       var $table = NULL;
       var $make_encoding = NULL;
       static $msg = NULL;
       // ================================================================================================
       //    Function          : ShowMsg (Constructor)
       //    Version           : 1.0.0
       //    Date              : 26.01.2005
       //    Parms
       //    Returns           : Error Indicator
       //
       //    Description       :
       // ================================================================================================
       function ShowMsg( $lang_id = NULL )
       {
            if( empty( $this->db ) ) $this->db = DBs::getInstance();
            if( empty( $lang_id ) )
            {
                if( defined( '_LANG_ID' ) ) $this->lang_id = _LANG_ID;
                elseif( isset( $_SESSION["lang_pg"] ) ) $this->lang_id = $_SESSION["lang_pg"];
            }
            else $this->lang_id = $lang_id;
            //Если по каким-либо причинам не удалось установить язык, то устанавливаем из глобаной константы DEBUG_LANG
            if( empty($this->lang_id) ) $this->lang_id = DEBUG_LANG;
            //$this->SetShowTable();
            
            if( defined("AJAX_RELOAD") AND AJAX_RELOAD==1){
                $this->make_encoding = 1;
                $this->encoding_from = 'utf-8';
                $this->encoding_to = 'windows-1251';        
            } 
            if(empty (self::$msg))  self::$msg =  &check_init_txt('TblBackMulti',TblBackMulti, $this->lang_id);
 
       } // End of Authorization Constructor
       
       // ================================================================================================
       // Function : GetMultiTxtInArr()
       // Version : 1.0.0
       // Date : 14.01.2010
       //
       // Parms :
       // Returns :      true,false / Void
       // Description :  retutn array with all multilangues for $table
       // ================================================================================================
       // Programmer :  Igor Trokhymchuk
       // Date : 14.01.2010 
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function GetMultiTxtInArr($table)
       {
           $dbr = new DB();
           $q = "SELECT `".$table."`.*
                 FROM `".$table."`
                 WHERE `".$table."`.`name`!=''
                 AND `".$table."`.`lang_id`='".$this->lang_id."'
                ";   
           $res = $dbr->db_Query( $q );
           //echo '<br>$q='.$q.' $res='.$res.' $dbr->result='.$dbr->result;
           if ( !$res or !$dbr->result) return false;
           $rows = $dbr->db_GetNumRows();
           //echo '<br>rows='.$rows;
           $arr = NULL;
           for( $i = 0; $i < $rows; $i++ ){
               $row=$dbr->db_FetchAssoc();
               $arr[$row['cod']] = $row['name'];
           }
           return $arr;                      
       }//end of function GetMultiTxtInArr()       
       
        // ================================================================================================
        // Function : find_txt
        // Version : 1.0.0
        // Date : 12.01.2005
        //
        // Parms : $txt - cod to search
        // Returns : $res - laguage depends error message
        // Description : get name of static text on needed language from table sys_static_text
        // ================================================================================================
        // Programmer : Igor Trokhymchuk
        // Date : 12.01.2005
        // Reason for change : Creation
        // Change Request Nbr:
        // ================================================================================================
        function find_txt($txt)
        {
            $q = "SELECT `name` FROM `".$this->GetShowTable()."` WHERE cod='".$txt."' AND lang_id='".$this->lang_id."' ";
            $this->db->db_Query($q);
            $row=$this->db->db_FetchAssoc();
            //echo '<br>q='.$q.' this->db->result='.$this->db->result;
            return $row['name'];
        }

        // ================================================================================================
        // Function : find_img
        // Version : 1.0.0
        // Date : 12.01.2005
        //
        // Parms : $txt - cod to search
        // Returns : $res - laguage depends error message
        // Description : get img of static text on needed language from table sys_static_text
        // ================================================================================================
        // Programmer : Igor Trokhymchuk
        // Date : 12.01.2005
        // Reason for change : Creation
        // Change Request Nbr:
        // ================================================================================================
        function find_img($txt)
        {
            $q = "SELECT `img` FROM `".$this->GetShowTable()."` WHERE cod='".$txt."' AND lang_id='".$this->lang_id."' ";
            $this->db->db_Query($q);
            $row=$this->db->db_FetchAssoc();
            //echo '<br>q='.$q.' this->db->result='.$this->db->result.' row='.$row.' $row->name='.$row->name;
            return $row['img'];
        } 
        // ================================================================================================
        // Function : find_txt_short
        // Version : 1.0.0
        // Date : 12.01.2005
        //
        // Parms : $txt - cod to search
        // Returns : $res - laguage depends error message
        // Description : get short name of static text on needed language from table sys_static_text
        // ================================================================================================
        // Programmer : Igor Trokhymchuk
        // Date : 12.01.2005
        // Reason for change : Creation
        // Change Request Nbr:
        // ================================================================================================
        function find_txt_short($txt)
        {
            $q = "SELECT `short` FROM `".$this->GetShowTable()."` WHERE cod='".$txt."' AND lang_id='".$this->lang_id."' ";
            $this->db->db_Query($q);
            $row=$this->db->db_FetchAssoc();
            //echo '<br>q='.$q.' this->db->result='.$this->db->result.' row='.$row.' $row->name='.$row->name;
            return $row['short'];
        } // end of function find_txt_short()               
        
        // ================================================================================================
        // Function : show_txt
        // Version : 1.0.0
        // Date : 12.01.2005
        //
        // Parms :  $txt - cod to search
        //          $instant_table - table where search
        // Returns : true
        // Description : return the static text from the table sys_static_text
        // ================================================================================================
        // Programmer : Igor Trokhymchuk
        // Date : 12.01.2005
        // Reason for change : Creation
        // Change Request Nbr:
        // ================================================================================================
        function show_text($txt, $instant_table=NULL, $make_encoding=0, $encoding_from = NULL, $encoding_to = NULL, $strip_tags='')
        {
            return self::$msg[$txt];
        }
        
        // ================================================================================================
        // Function : show_img
        // Version : 1.0.0
        // Date : 12.01.2005
        //
        // Parms :  $txt - cod to search
        //          $instant_table - table where search
        // Returns : true
        // Description : return the static text from the table sys_static_text
        // ================================================================================================
        // Programmer : Igor Trokhymchuk
        // Date : 12.01.2005
        // Reason for change : Creation
        // Change Request Nbr:
        // ================================================================================================
        function show_img($txt, $instant_table=NULL)
        {
            
            $Spr = new SystemSpr();             
            $curent_table=NULL;
            if (!empty($instant_table)) {
                $curent_table=$this->GetShowTable();
                $this->SetShowTable($instant_table);
            }
            if ( !$this->GetShowTable() ) $this->SetShowTable(TblBackMulti);

            $img = $this->find_img($txt);
            $name = $this->find_txt($txt);
            //echo '<br>$img='.$img;
            //$Spr->ShowImage($this->GetShowTable(), $this->lang_id, $img, NULL, '100', NULL, 'border=0');
            $img_path = $Spr->GetImgPath($img, $this->GetShowTable(), $this->lang_id ) 
            ?><img src="<?=$img_path;?>" border="0" alt="<?=$name;?>" title="<?=$name;?>"/><?
            if (!empty($curent_table)) $this->SetShowTable($curent_table);
            //echo '<br> $nam='.$nam;
            //return $nam;
        } // edn of function show_img()  
        
        // ================================================================================================
        // Function : show_text_short
        // Version : 1.0.0
        // Date : 04.05.2007
        //
        // Parms :  $txt - cod to search
        //          $instant_table - table where search
        // Returns : true
        // Description : return the short static text from the table sys_static_text
        // ================================================================================================
        // Programmer : Igor Trokhymchuk
        // Date : 04.05.2007
        // Reason for change : Creation
        // Change Request Nbr:
        // ================================================================================================
        function show_text_short($txt, $instant_table=NULL)
        {
            $Spr = new SystemSpr();             
            $curent_table=NULL;
            if (!empty($instant_table)) {
                $curent_table=$this->GetShowTable();
                $this->SetShowTable($instant_table);
            }
            if ( !$this->GetShowTable() ) $this->SetShowTable(TblBackMulti);

            $name = $this->find_txt_short($txt);
            if( empty($name)) $name = $this->find_txt($txt);
            
            if (!empty($curent_table)) $this->SetShowTable($curent_table);
            //echo '<br> $name='.$name;
            return stripslashes($name);
        } // edn of function show_text_short()              

        // ================================================================================================
        // Function : find_err_txt
        // Version : 1.0.0
        // Date : 07.01.2005
        //
        // Parms : $err - definition of the error
        // Returns : $res - laguage depends error message
        // Description : get description of the error or message on needed language from table sys_errorspr
        // ================================================================================================
        // Programmer : Igor Trokhymchuk
        // Date : 07.01.2005
        // Reason for change : Creation
        // Change Request Nbr:
        // ================================================================================================
        function find_err_txt($err)
        {
            $q = "SELECT `name` FROM `".$this->GetShowTable()."` WHERE cod='".$err."' and lang_id='".$this->lang_id."'";
            $this->db->db_Query($q);
            $row=$this->db->db_FetchAssoc();
            //echo '<br>q='.$q.' this->db->result='.$this->db->result.' row='.$row;
            return $row['name'];
        }
        // ================================================================================================
        // Function : show_msg
        // Version : 1.0.0
        // Date : 07.01.2005
        //
        // Parms : $err - definition of the error
        // Returns : true
        // Description : show the error message in messagebox to the browser
        // ================================================================================================
        // Programmer : Igor Trokhymchuk
        // Date : 07.01.2005
        // Reason for change : Creation
        // Change Request Nbr:
        // ================================================================================================
        function show_msg($err, $instant_table=NULL)
        {
            echo "<script>window.alert(\"".self::$msg[$err]."\")</script>";
            return true;
        }

        // ================================================================================================
        // Function : get_msg
        // Version : 1.0.0
        // Date : 12.01.2005
        //
        // Parms : $err - definition of the error
        // Returns : true
        // Description : return the error message as string
        // ================================================================================================
        // Programmer : Igor Trokhymchuk
        // Date : 12.01.2005
        // Reason for change : Creation
        // Change Request Nbr:
        // ================================================================================================
        function get_msg($err, $instant_table=NULL)
        {
            $curent_table=NULL;
            if (!empty($instant_table)) {
                $curent_table=$this->GetShowTable();
                $this->SetShowTable($instant_table);
            }
            if ( !$this->GetShowTable() ) $this->SetShowTable(TblBackMulti);
            //echo '<br> $this->table='.$this->table;
            $msg_txt=$this->find_err_txt($err);
            if (!empty($curent_table)) $this->SetShowTable($curent_table);
            return $msg_txt;
        }

        // ================================================================================================
        // Function : SetShowTable
        // Version : 1.0.0
        // Date : 06.01.2006
        //
        // Parms : $err - definition of the error
        // Returns : true
        // Description : set the name of the table for show signatures and messages
        // ================================================================================================
        // Programmer : Igor Trokhymchuk
        // Date : 06.01.2006
        // Reason for change : Creation
        // Change Request Nbr:
        // ================================================================================================
        function SetShowTable($table=NULL)
        {
            if( empty($table)) $this->table = $this->GetShowTable();
            else $this->table=$table;
            //echo '<br>$this->table='.$this->table;
            return true;
        }  //End of function SetShowTable()

        // ================================================================================================
        // Function : GetShowTable
        // Version : 1.0.0
        // Date : 06.01.2006
        //
        // Parms : $err - definition of the error
        // Returns : true
        // Description : return the name of the table for show signatures and messages
        // ================================================================================================
        // Programmer : Igor Trokhymchuk
        // Date : 06.01.2006
        // Reason for change : Creation
        // Change Request Nbr:
        // ================================================================================================
        function GetShowTable()
        {
            return $this->table;
        }  //End of function GetShowTable()
 }
?>