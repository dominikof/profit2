<?php
// ================================================================================================
// System : SEOCMS
// Module : sysSpr.class.php
// Version : 1.0.0
// Date : 25.05.2006
// Licensed To:
// Igor Trokhymchuk ihoru@mail.ru
// Andriy Lykhodid las_zt@mail.ru
//
// Purpose : Class definition for system functions with reference-books
//
// ================================================================================================

include_once( SITE_PATH.'/admin/include/defines.inc.php' );  

// ================================================================================================
//    Class             : SystemSpr
//    Version           : 1.0.0
//    Date              : 25.05.2006
//
//    Constructor       : Yes
//    Parms             : session_id / session id
//                        usre_id    / UserID
//                        user_      /
//                        user_type  / id of group of user
//    Returns           : None
//    Description       : Class definition for system functions with reference-books
// ================================================================================================
//    Programmer        :  Igor Trokhymchuk
//    Date              :  25.05.2006
//    Reason for change :  Creation
//    Change Request Nbr:  N/A
// ================================================================================================
 class SystemSpr {

   var $user_id = NULL;
   var $module = NULL;
   var $module_name = NULL;

   var $sort = NULL;
   var $display = 10;
   var $start = 0;
   var $fln = NULL;
   var $width = 500;
   var $spr = NULL;
   var $srch = NULL;

   var $msg = NULL;
   var $Rights = NULL;
   var $Form = NULL;
   var $script = NULL;
   var $root_script = NULL; 
   var $parent_script = NULL;
   var $parent_id = NULL;
   var $Err = NULL;
   
   var $id = NULL;
   var $cod = NULL;
   var $lang_id = NULL;
   var $name = NULL;
   var $short = NULL;
   var $img = NULL;
   var $make_encoding = NULL;
   
   var $mtitle = NULL;
   var $mdescr = NULL;
   var $mkeywords = NULL;

   // ================================================================================================
   //    Function          : SystemSpr (Constructor)
   //    Version           : 1.0.0
   //    Date              : 25.05.2006
   //    Parms             : usre_id   / User ID
   //                        module    / module ID
   //                        sort      / field by whith data will be sorted
   //                        display   / count of records for show
   //                        start     / first records for show
   //                        width     / width of the table in with all data show
   //                        spr       / name of the table for this module
   //    Returns           : Error Indicator
   //
   //    Description       : Opens and selects a dabase
   // ================================================================================================
   function SystemSpr($user_id=NULL, $module=NULL, $display=NULL, $sort=NULL, $start=NULL, $width = NULL, $spr=NULL) {
            //Check if Constants are overrulled
            ( $user_id   !="" ? $this->user_id = $user_id  : $this->user_id = NULL );
            ( $module   !="" ? $this->module  = $module   : $this->module  = NULL );
            ( $display  !="" ? $this->display = $display  : $this->display = 20   );
            ( $sort     !="" ? $this->sort    = $sort     : $this->sort    = NULL );
            ( $start    !="" ? $this->start   = $start    : $this->start   = 0    );
            ( $width    !="" ? $this->width   = $width    : $this->width   = 750  );
            ( $spr      !="" ? $this->spr     = $spr      : $this->spr     = NULL  );

            if ( defined("_LANG_ID") ) $this->lang_id = _LANG_ID;
            
            if( defined("AJAX_RELOAD") AND AJAX_RELOAD==1){
                $this->make_encoding = 1;
                $this->encoding_from = 'utf-8';
                $this->encoding_to = 'windows-1251';        
            }                 
            
            if (empty($this->Rights)) $this->Rights = new Rights($this->user_id, $this->module);
            if (empty($this->Msg)) $this->Msg = new ShowMsg();
            if (empty($this->Form)) $this->Form = new Form('form_sysSpr');
            
            //$this->multi =  $this->getAllmsg();
   } // End of SystemSpr Constructor
   
   
   // ================================================================================================
   // Function : getAllmsg
   // Version : 1.0.0
   // Date : 22.03.2010
   // Parms : $Table        / table, from which data will be shown
   //         $lang_id     / id of language
   // Returns : true
   // Description : get all records from table to array
   // ================================================================================================
   // Programmer : Oleg Morgalyuk
   // Date : 22.03.2010
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   static function getAllmsg($table='',$lang_id=_LANG_ID)
       {
           if ($table==='') $table=TblBackMulti;
           $rezult= array();
           $db= &DBs::getInstance();
           $q = "SELECT `name`,`cod` FROM `".$table."` WHERE lang_id='".$lang_id."' ";
           $res = $db->db_Query($q);
           //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$db->result;
           if ( !$res OR !$db->result ) return false;
           $rows = $db->db_GetNumRows();
           for($i=0; $i<$rows; $i++)
           {
               $row_spr=$db->db_FetchAssoc();
               $rezult[$row_spr['cod']]=stripslashes($row_spr['name']);
           }
           return $rezult;
      }
   // ================================================================================================
   // Function : ShowInComboBox
   // Version : 1.0.0
   // Date : 04.02.2005
   //
   // Parms : $Table        / table, from which data will be shown
   //         $name_fld     / name of field
   //         $val          / value seleced by default
   //         $width        / width of SELECT-field
   //         $default_val  / default value
   //         $sort_name    / sortation of a list by which field
   //         $asc_desc     / type of sirtation - asc or desc
   // Returns : true
   // Description : show the list of the records from table to combobox
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 04.02.2005
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function ShowInComboBox( $Table, $name_fld, $val, $width=40, $default_val = '&nbsp;', $sort_name = 'move', $asc_desc = 'asc', $params=NULL )
   {
      if (empty($name_fld)) $name_fld=$Table;
      if ($width==0) $width=250;
    
      $tmp_db = &DBs::getInstance();
      $q = "SELECT * FROM `".$Table."` WHERE 1 LIMIT 1";
      $res = $tmp_db->db_Query($q);
      //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
      if ( !$res ) return false;
      if ( !$tmp_db->result ) return false;
      $fields_col = mysql_num_fields($tmp_db->result);
      
      if ($fields_col>4) $q = "SELECT * FROM `".$Table."` WHERE `lang_id`='"._LANG_ID."' order by `$sort_name` $asc_desc"; 
      else $q = "SELECT * FROM `".$Table."` WHERE `lang_id`='"._LANG_ID."'";
      
      $res = $tmp_db->db_Query($q);
      //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
      if (!$res) return false;
      $rows = $tmp_db->db_GetNumRows();

      $mas_spr['']=$default_val;
      for($i=0; $i<$rows; $i++)
      {
           $row_spr=$tmp_db->db_FetchAssoc();
           $mas_spr[$row_spr['cod']]=stripslashes($row_spr['name']);
      }
      $this->Form->Select( $mas_spr, $name_fld, $val,  $width, $params );
   }  //end of fuinction ShowInComboBox
   
   // ================================================================================================
   // Function : ShowShortNameInComboBox
   // Version : 1.0.0
   // Date : 04.02.2005
   //
   // Parms : $Table        / table, from which data will be shown
   //         $name_fld     / name of field
   //         $val          / value seleced by default
   //         $width        / width of SELECT-field
   //         $default_val  / default value
   //         $sort_name    / sortation of a list by which field
   //         $asc_desc     / type of sirtation - asc or desc
   // Returns : true
   // Description : show the list of the short names from table to combobox
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 04.02.2005
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function ShowShortNameInComboBox( $Table, $name_fld, $val, $width=40, $default_val = '&nbsp;', $sort_name = 'move', $asc_desc = 'asc', $params=NULL )
   {
      if (empty($name_fld)) $name_fld=$Table;
      if ($width==0) $width=250;

      $tmp_db = &DBs::getInstance();
      $q = "SELECT * FROM `".$Table."` WHERE 1 LIMIT 1";
      $res = $tmp_db->db_Query($q);
      //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
      if ( !$res ) return false;
      if ( !$tmp_db->result ) return false;
      $fields_col = mysql_num_fields($tmp_db->result);
      
      if ($fields_col>4) $q = "SELECT * FROM `".$Table."` WHERE `lang_id`='"._LANG_ID."' order by `$sort_name` $asc_desc"; 
      else $q = "SELECT * FROM `".$Table."` WHERE `lang_id`='"._LANG_ID."'";
      
      $res = $tmp_db->db_Query($q);
      //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
      if (!$res) return false;
      $rows = $tmp_db->db_GetNumRows();

      $mas_spr['']=$default_val;
      for($i=0; $i<$rows; $i++)
      {
           $row_spr=$tmp_db->db_FetchAssoc();
           $mas_spr[$row_spr['cod']]=stripslashes($row_spr['short']);
      }
      $this->Form->Select( $mas_spr, $name_fld, $val,  $width, $params );
   }  //end of fuinction ShowShortNameInComboBox 
   
   // ================================================================================================
   // Function : ShowInComboBoxWithShortName
   // Version : 1.0.0
   // Date : 04.02.2005
   //
   // Parms : $Table        / table, from which data will be shown
   //         $name_fld     / name of field
   //         $val          / value seleced by default
   //         $width        / width of SELECT-field
   //         $default_val  / default value
   //         $sort_name    / sortation of a list by which field
   //         $asc_desc     / type of sirtation - asc or desc
   //         $short_name_position  / position of short name. It can be: left or right
   //         $divider      / divider between short name and full name
   // Returns : true
   // Description : show the list of the records from table to combobox
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 04.02.2005
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function ShowInComboBoxWithShortName( $Table, $name_fld, $val, $width = '250', $default_val = '&nbsp;', $sort_name = 'move', $asc_desc = 'asc', $short_name_position='left', $divider=' ', $params=NULL )
   {
      if (empty($name_fld)) $name_fld=$Table;

      $tmp_db = &DBs::getInstance();
      $q = "SELECT * FROM `".$Table."` WHERE 1 LIMIT 1";
      $res = $tmp_db->db_Query($q);
      //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
      if ( !$res ) return false;
      if ( !$tmp_db->result ) return false;
      $fields_col = mysql_num_fields($tmp_db->result);
      
      if ($fields_col>4) $q = "SELECT * FROM `".$Table."` WHERE `lang_id`='"._LANG_ID."' order by `$sort_name` $asc_desc"; 
      else $q = "SELECT * FROM `".$Table."` WHERE `lang_id`='"._LANG_ID."'";
      
      $res = $tmp_db->db_Query($q);
      //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
      if (!$res) return false;
      $rows = $tmp_db->db_GetNumRows();

      $mas_spr['']=$default_val;
      for($i=0; $i<$rows; $i++)
      {
           $row_spr=$tmp_db->db_FetchAssoc();
           if( !empty($row_spr['short']) ){
             if($short_name_position=='left') $mas_spr[$row_spr['cod']] = stripslashes($row_spr['short']).$divider.stripslashes($row_spr['name']);
             else $mas_spr[$row_spr['cod']] = stripslashes($row_spr['name']).$divider.stripslashes($row_spr['short']);
           }
           else $mas_spr[$row_spr['cod']] = stripslashes($row_spr['name']);
      }
      $this->Form->Select( $mas_spr, $name_fld, $val,  $width, $params );
   }  //end of fuinction ShowInComboBoxWithShortName()       
  

   // ================================================================================================
   // Function : ShowActSprInCombo
   // Version : 1.0.0
   // Date : 09.03.2005
   //
   // Parms : table     / name of the table from with the data will be shown
   //         fld_name  / name of the field
   //         val       / value for the field
   // Returns : true,false / Void
   // Description : Show the Combobox with data from $table with action after selections
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 09.03.2005
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function ShowActSprInCombo($Table, $name_fld, $val = NULL, $scriptact = NULL, $default_val = NULL)
   {
      if ( empty($scriptact) ) $scriptact = 'module='.$this->module;
      //echo '<br>$this->module='.$this->module;
      $scriplink = $_SERVER['PHP_SELF']."?$scriptact";

      if (empty($name_fld)) $name_fld=$Table;

      $tmp_db = &DBs::getInstance();
      $q = "SELECT * FROM `".$Table."` WHERE 1 LIMIT 1";
      $res = $tmp_db->db_Query($q);
      //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
      if ( !$res ) return false;
      if ( !$tmp_db->result ) return false;
      $fields_col = mysql_num_fields($tmp_db->result);
      
      if ($fields_col>4) $q = "select * from `".$Table."` where lang_id='"._LANG_ID."' order by `move`"; 
      else $q = "select * from `".$Table."` where lang_id='"._LANG_ID."'";          

      $res = $tmp_db->db_Query($q);
      //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result; 
      if (!$res) return false;
      $rows = $tmp_db->db_GetNumRows();

      $mas_spr['']=$default_val;
      for($i=0; $i<$rows; $i++)
      {
           $row_spr=$tmp_db->db_FetchAssoc();
           $mas_spr[$row_spr['cod']]=stripslashes($row_spr['name']);
      }
      $this->Form->SelectAct( $mas_spr, $name_fld, $val, "onChange=\"location='$scriplink&$name_fld='+this.value\"" ); 
   } //end of function ShowActSprInCombo()

   // ================================================================================================
   // Function : ShowActSprInComboWithScript
   // Version : 1.0.0
   // Date : 09.03.2005
   //
   // Parms : $Table     / name of the table from with the data will be shown
   //         $name_fld  / name of the field
   //         $val       / value for the field
   //         $scriptact / script  
   //         $default_val / default value
   // Returns : true,false / Void
   // Description : Show the Combobox with data from $table with action after selections
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 09.03.2005
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function ShowActSprInComboWithScript($Table, $name_fld, $val = NULL, $scriptact = NULL, $default_val = NULL)
   {
      $scriplink = $scriptact;

      if (empty($name_fld)) $name_fld=$Table;

      $tmp_db = &DBs::getInstance();
      $q = "SELECT * FROM `".$Table."` WHERE 1 LIMIT 1";
      $res = $tmp_db->db_Query($q);
      //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
      if ( !$res ) return false;
      if ( !$tmp_db->result ) return false;
      $fields_col = mysql_num_fields($tmp_db->result);
      
      if ($fields_col>4) $q = "select * from `".$Table."` where lang_id='"._LANG_ID."' order by `move`"; 
      else $q = "select * from `".$Table."` where lang_id='"._LANG_ID."'";          

      $res = $tmp_db->db_Query($q);
      //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result; 
      if (!$res) return false;
      $rows = $tmp_db->db_GetNumRows();

      $mas_spr['']=$default_val;
      for($i=0; $i<$rows; $i++)
      {
           $row_spr=$tmp_db->db_FetchAssoc();
           $mas_spr[$row_spr['cod']]=stripslashes($row_spr['name']);
      }
      $this->Form->SelectAct( $mas_spr, $name_fld, $val, "onChange=\"location='$scriplink&$name_fld='+this.value\"" ); 
   } //end of function ShowActSprInComboWithScript()        
   
   
   // ================================================================================================
   // Function : ShowInCheckBox
   // Version : 1.0.0
   // Date : 04.02.2005
   //
   // Parms : $Table      / table, from which data will be shown
   //         $name_fld   / name of field
   //         $cols       / count of checkboxes in one line
   //         $val        / value seleced by default
   //         $position   / position of the combo box ( "right" - right from tite, "left" - left from title)
   // Returns : true
   // Description : show the list of the records from table to checkbox
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 04.02.2005
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function ShowInCheckBox( $Table, $name_fld, $cols, $val, $position = "left", $disabled = NULL, $sort_name = 'move', $asc_desc = 'asc', $show_sublevels=0, $level=NULL )
   {
      //$Tbl = new html_table(1);

      $row1 = NULL;
      if (empty($name_fld)) $name_fld=$Table;

      $tmp_db = &DBs::getInstance();
      $q = "SELECT * FROM `".$Table."` WHERE 1 LIMIT 1";
      $res = $tmp_db->db_Query($q);
      //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
      if ( !$res ) return false;
      if ( !$tmp_db->result ) return false;
      $fields_col = mysql_num_fields($tmp_db->result);
      
      $q = "SELECT * FROM `".$Table."` WHERE `lang_id`='"._LANG_ID."'";
      //echo '<br>$level='.$level;
      if( $tmp_db->IsFieldExist($Table, 'level') ) $q = $q." AND `level`='".$level."'";
      if ($fields_col>4) $q = $q." ORDER BY `$sort_name` $asc_desc"; 
      //echo '<br>$q='.$q;          

      $res = $tmp_db->db_Query($q);
      if (!$res) return false;
      $rows = $tmp_db->db_GetNumRows();
      $arr_data = array();
      for( $i = 0; $i < $rows; $i++ )
      {
           $row000 = $tmp_db->db_FetchAssoc();
           $arr_data[$i] = $row000;
      }

      $col_check=1;
      ?>
      <table border="0" cellpadding="1" cellspacing="1" align="left" class="checkbox_tbl">
       <tr>
       <?
       for( $i = 0; $i < $rows; $i++ )
       {
           $row1 = $arr_data[$i];
           if ($col_check > $cols) {
               ?></tr><tr><?
               $col_check=1;
           }
           
           $checked ='>';
           if (is_array($val)) {
               if (isset($val))
               foreach($val as $k=>$v)
               {
                if (isset($k) and ($v==$row1['cod'])) $checked = " checked".$checked;
                //echo '<br>$k='.$k.' $v='.$v.' $row1[cod]='.$row1['cod']; 
               }
           }
           if ( $position == "left" ) $align= 'left';
           else $align= 'right';               
           ?><td align="<?=$align?>" valign="top" class="checkbox"><?
           
           if ( $position == "left" ) {
              //echo "<table border='0' cellpadding='1' cellspacing='0'><tr><td><input class='checkbox' type='checkbox' name='".$name_fld."[]' value='".$row1['cod']."' ".$disabled." ".$checked.'</td><td>'.stripslashes($row1['name']).'</td></tr></table>'; 
              ?>
              <table border="0" cellpadding="1" cellspacing="0">
               <tr>
                <td valign="top"><input class="checkbox" type="checkbox" name="<?=$name_fld;?>[]" value="<?=$row1['cod'];?>" <?=$disabled;?> <?=$checked;?> </td>
                <td class="checkbox_td"><?=stripslashes($row1['name']);?></td>
               </tr>
              </table>
              <?
           }
           else {
               //echo stripslashes($row1['name'])."<input class='checkbox' type='checkbox' name='".$name_fld."[]' value='".$row1['cod']."' ".$disabled." ".$checked;
               ?>
               <table border="0" cellpadding="1" cellspacing="0">
                <tr>
                 <td valign="top">
                 <td class="checkbox_td"><?=stripslashes($row1['name']);?></td>
                 <td><input class="checkbox" type="checkbox" name="<?=$name_fld;?>[]" value="<?=$row1['cod'];?>" <?=$disabled;?> <?=$checked;?> </td>
                </tr>
               </table>
               <?
           }
           
           
           //======= show sublevels START ===========
           if( $show_sublevels==1){
           ?>
            <table border="0" cellpadding="1" cellspacing="0">
             <tr>
              <td style="padding:0px 0px 0px 20px;"><?
               $this->ShowInCheckBox( $Table, $name_fld, 1, $val, $position, $disabled, $sort_name, $asc_desc, $show_sublevels, $row1['cod']);
              ?>
              </td>
             </tr>
            </table>
           <?
           }
           //======= show sublevels END ===========
           ?></td><?
           $col_check++;
       }
       ?>
       </tr>
      </table>
      <?
   }  //end of fuinction ShowInCheckBox
   
   
   // ================================================================================================
   // Function : ShowInRadioBox
   // Version : 1.0.0
   // Date : 04.02.2005
   //
   // Parms : $Table      / table, from which data will be shown
   //         $name_fld   / name of field
   //         $cols       / count of checkboxes in one line
   //         $val        / value seleced by default
   //         $position   / position of the combo box ( "right" - right from tite, "left" - left from title)
   // Returns : true
   // Description : show the list of the records from table to checkbox
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 04.02.2005
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function ShowInRadioBox( $Table, $name_fld, $cols, $val, $position = "left", $disabled = NULL, $sort_name = 'move', $asc_desc = 'asc' )
   {
      //$Tbl = new html_table(1);

      $row1 = NULL;
      if (empty($name_fld)) $name_fld=$Table;

      $tmp_db = &DBs::getInstance();
      $q = "SELECT * FROM `".$Table."` WHERE 1 LIMIT 1";
      $res = $tmp_db->db_Query($q);
      //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
      if ( !$res ) return false;
      if ( !$tmp_db->result ) return false;
      $fields_col = mysql_num_fields($tmp_db->result);
      
      if ($fields_col>4) $q = "select * from `".$Table."` where lang_id='"._LANG_ID."' order by `$sort_name` $asc_desc"; 
      else $q = "select * from `".$Table."` where lang_id='"._LANG_ID."'";          

      $res = $tmp_db->db_Query($q);
      if (!$res) return false;
      $rows = $tmp_db->db_GetNumRows();

      $col_check=1;
      //$Tbl->table_header();
      //$Tbl->tr();
      echo '<table border="0" cellpadding="1" cellspacing="1" align="left" ><tr>';
      for( $i = 0; $i < $rows; $i++ )
      {
           $row1 = $tmp_db->db_FetchAssoc();
           if ($col_check > $cols) {
               //$Tbl->tr();
               echo '</tr><tr>';
               $col_check=1;
           }
               
           $checked ='>';
           if ($val==$row1['cod']) $checked = " checked".$checked;

           if ( $position == "left" ) $align= 'left';
           else $align= 'right';               
           echo "\n<td align=".$align." valign='middle' class='checkbox'>";
           
           if ( $position == "left" ) echo "<input class='checkbox' type='radio' name='".$name_fld."' value='".$row1['cod']."' ".$disabled." ".$checked.' '.stripslashes($row1['name']); 
           else echo stripslashes($row1['name'])."<input class='checkbox' type='checkbox' name=".$name_fld." value='".$row1['cod']."' ".$disabled." ".$checked;
           echo "</td>";
            $col_check++;
      }
      echo '</tr></table>';
      //$Tbl->table_footer();
   }  //end of fuinction ShowInRadioBox       

   // ================================================================================================
   // Function : GetNameByCod
   // Version : 1.0.0
   // Date : 04.02.2005
   //
   // Parms :   $Table - name of table, from which will be select data
   //           $cod - cod of the record in the table where the name is searched
   //           $lang_id - id of the language
   //           $my_ret_val - parameter for returned value .( 1- return '' for empty records) 
   // Returns : $res / Void
   // Description : Get the name from table by its cod on needed language
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 04.02.2005
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function GetNameByCod($Table, $cod, $lang_id = _LANG_ID, $my_ret_val = NULL)
   {
       if ( empty($cod) ) {
           if (!empty($my_ret_val)) return '';
           else return $this->Msg->show_text('_VALUE_NOT_SET');
       }
       $tmp_db = &DBs::getInstance();
       $q="SELECT * FROM `".$Table."` WHERE `cod`='".addslashes($cod)."' AND `lang_id`='".$lang_id."'";
       $res = $tmp_db->db_Query($q);
       //echo '<br> $q='.$q.'  $tmp_db->result='.$tmp_db->result;
       if ( !$res OR !$tmp_db->result ) return false;
       $row_res = $tmp_db->db_FetchAssoc();
//       echo '<br> $row_res[name]='.$row_res['name'];
       
       $field_type = mysql_field_type($tmp_db->result,3);
       //echo '<br>$field_type='.$field_type;
       //echo '<br> $my_ret_val='.$my_ret_val;
       $retval = stripslashes( $row_res['name'] );
       
       if ( empty($retval) ){
            if ( $field_type!='blob' ){
                if ( !empty($my_ret_val)) $retval = '';
                else $retval = $this->Msg->show_text('_VALUE_NOT_SET');
            }
       }
       //echo '<br>$this->make_encoding='.$this->make_encoding;
       if( $this->make_encoding==1 AND !empty($this->encoding_from) AND !empty($this->encoding_to) ) {
           $retval = iconv($this->encoding_to, $this->encoding_from, $retval);
       }              
       return $retval;
   }  //end of fuinction GetNameByCod
   
   // ================================================================================================
   // Function : GetDataByCod
   // Version : 1.0.0
   // Date : 28.05.2010
   //
   // Parms :   $Table - name of table, from which will be select data
   //           $cod - cod of the record in the table where the name is searched
   //           $lang_id - id of the language
   //           $my_ret_val - parameter for returned value .( 1- return '' for empty records) 
   // Returns : $res / Void
   // Description : Get the name from table by its cod on needed language
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 28.05.2010
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function GetDataByCod($Table, $cod, $lang_id = _LANG_ID, $my_ret_val = NULL)
   {
       if ( empty($cod) ) {
           if (!empty($my_ret_val)) return '';
           else return $this->Msg->show_text('_VALUE_NOT_SET');
       }
       $tmp_db = new DB();
       $q="SELECT * FROM `".$Table."` WHERE `cod`='".addslashes($cod)."' AND `lang_id`='".$lang_id."'";
       $res = $tmp_db->db_Query($q);
       //echo '<br> $q='.$q.'  $tmp_db->result='.$tmp_db->result;
       if ( !$res OR !$tmp_db->result ) return false;
       $row_res = $tmp_db->db_FetchAssoc();
       return $row_res;
   }  //end of fuinction GetDataByCod     
   
   // ================================================================================================
   // Function : GetArrNameByCodLike
   // Version : 1.0.0
   // Date : 04.02.2005
   //
   // Parms :   $Table - name of table, from which will be select data
   //           $cod - array with codes of the record in the table where the name is searched
   //           $lang_id - id of the language
   //           $my_ret_val - parameter for returned value .( 1- return '' for empty records) 
   // Returns : $res / Void
   // Description :Get the array of names from table by its codes on needed language
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 04.02.2005
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function GetArrNameByCodLike($Table, $cod, $lang_id = _LANG_ID, $my_ret_val = NULL)
   {
       $this->Msg = new ShowMsg();
       $rezult = array(); 
       if ( empty($cod) ) {
           if (!empty($my_ret_val)) return '';
           else $rezult;
       }
       $tmp_db = &DBs::getInstance();
       $q="SELECT * FROM `".$Table."` WHERE `cod` like '".addslashes($cod)."%' AND `lang_id`='".$lang_id."'";
       $res = $tmp_db->db_Query($q);
       //echo '<br> $q='.$q.'  $tmp_db->result='.$tmp_db->result;
       if ( !$res OR !$tmp_db->result ) return false;
       $row_num = $tmp_db->db_GetNumRows();
       for ($i=0;$i<$row_num;$i++)
       {
           $row_res = $tmp_db->db_FetchAssoc();
           //echo '<br> $row_res[name]='.$row_res['name'];
           $field_type = mysql_field_type($tmp_db->result,3);
           //echo '<br>$field_type='.$field_type;
           //echo '<br> $my_ret_val='.$my_ret_val;
           $retval = stripslashes( $row_res['name'] );
           
           if ( empty($retval) ){
                if ( $field_type!='blob' ){
                    if ( !empty($my_ret_val)) $retval = '';
                    else $retval = $this->Msg->show_text('_VALUE_NOT_SET');
                }
           }
           //echo '<br>$this->make_encoding='.$this->make_encoding;
           if( $this->make_encoding==1 AND !empty($this->encoding_from) AND !empty($this->encoding_to) ) {
               $retval = iconv($this->encoding_to, $this->encoding_from, $retval);
           }
            $rezult[$row_res['cod']]=$retval;
       }              
       return $rezult;
   }  //end of fuinction GetArrNameByCodLike()
   
   // ================================================================================================
   // Function : GetArrNameByCodLike
   // Version : 1.0.0
   // Date : 04.02.2005
   //
   // Parms :   $Table - name of table, from which will be select data
   //           $cod - array with codes of the record in the table where the name is searched
   //           $lang_id - id of the language
   //           $my_ret_val - parameter for returned value .( 1- return '' for empty records) 
   // Returns : array / Void
   // Description : Get the array of names from table by its codes on needed language
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 04.02.2005
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function GetArrNameByArrayCod($Table, $cod, $lang_id = _LANG_ID, $my_ret_val = NULL)
   {
       $this->Msg = new ShowMsg();
       $rezult = array(); 
       if ( empty($cod) ) {
           if (!empty($my_ret_val)) return '';
           else $rezult;
       }
       $str=implode(',',$cod);
       $tmp_db = &DBs::getInstance();
       $q="SELECT * FROM `".$Table."` WHERE `cod` in (".$str.") AND `lang_id`='".$lang_id."'";
       $res = $tmp_db->db_Query($q);
       //echo '<br> $q='.$q.'  $tmp_db->result='.$tmp_db->result;
       if ( !$res OR !$tmp_db->result ) return false;
       $row_num = $tmp_db->db_GetNumRows();
       for ($i=0;$i<$row_num;$i++)
       {
           $row_res = $tmp_db->db_FetchAssoc();
           //echo '<br> $row_res[name]='.$row_res['name'];
           $field_type = mysql_field_type($tmp_db->result,3);
           //echo '<br>$field_type='.$field_type;
           //echo '<br> $my_ret_val='.$my_ret_val;
           $retval = stripslashes( $row_res['name'] );
           
           if ( empty($retval) ){
                if ( $field_type!='blob' ){
                    if ( !empty($my_ret_val)) $retval = '';
                    else $retval = $this->Msg->show_text('_VALUE_NOT_SET');
                }
           }
           //echo '<br>$this->make_encoding='.$this->make_encoding;
           if( $this->make_encoding==1 AND !empty($this->encoding_from) AND !empty($this->encoding_to) ) {
               $retval = iconv($this->encoding_to, $this->encoding_from, $retval);
           }
            $rezult[$row_res['cod']]=$retval;
       }              
       return $rezult;
   }  //end of fuinction GetArrNameByArrayCod()       
   
   // ================================================================================================
   // Function : GetShortNameByCod
   // Version : 1.0.0
   // Date : 14.05.2007
   //
   // Parms :   $Table - name of table, from which will be select data
   //           $cod - cod of the record in the table where the name is searched
   //           $lang_id - id of the language
   //           $my_ret_val - parameter for returned value .( 1- return '' for empty records) 
   // Returns : $res / Void
   // Description : Get the short name from table by its cod on needed language
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 04.02.2005
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function GetShortNameByCod($Table, $cod, $lang_id = _LANG_ID, $my_ret_val = NULL)
   {
       if ( empty($cod) ) {
           if (!empty($my_ret_val)) return '';
           else return $this->Msg->show_text('_VALUE_NOT_SET');
       }
       $tmp_db = &DBs::getInstance();
       $q="SELECT * FROM `".$Table."` WHERE `cod`='".addslashes($cod)."' AND `lang_id`='".$lang_id."'";
       $res = $tmp_db->db_Query($q);
       //echo '<br> $q='.$q.'  $tmp_db->result='.$tmp_db->result;
       if ( !$tmp_db->result ) return false;
       $row_res = $tmp_db->db_FetchAssoc();
       //echo '<br> $row_res[name]='.$row_res['name'];
       
       $field_type = mysql_field_type($tmp_db->result,3);
       //echo '<br>$field_type='.$field_type;
       //echo '<br> $my_ret_val='.$my_ret_val;
       $retval = stripslashes( $row_res['short'] );
       
       if ( empty($retval) ){
            if ( $field_type!='blob' ){
                if ( !empty($my_ret_val)) $retval = '';
                else $retval = $this->Msg->show_text('_VALUE_NOT_SET');
            }
       }
       
       if( $this->make_encoding==1 AND !empty($this->encoding_from) AND !empty($this->encoding_to) ) {
           $retval = iconv($this->encoding_to, $this->encoding_from, $retval);
       }
       return $retval;
   }  //end of fuinction GetShortNameByCod
   
   
   // ================================================================================================
   // Function : GetShortNameAndNameByCod
   // Version : 1.0.0
   // Date : 14.05.2007
   //
   // Parms :   $Table - name of table, from which will be select data
   //           $cod - cod of the record in the table where the name is searched
   //           $lang_id - id of the language
   //           $my_ret_val - parameter for returned value .( 1- return '' for empty records)
   //           $devider    - devider between short name and full name 
   // Returns : $res / Void
   // Description : Get the short name from table by its cod on needed language
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 04.02.2005
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function GetShortNameAndNameByCod($Table, $cod, $lang_id = _LANG_ID, $my_ret_val = NULL, $devider=' ')
   {
       if ( empty($cod) ) {
           if (!empty($my_ret_val)) return '';
           else return $this->Msg->show_text('_VALUE_NOT_SET');
       }
       $tmp_db = &DBs::getInstance();
       $q="SELECT * FROM `".$Table."` WHERE `cod`='".addslashes($cod)."' AND `lang_id`='".$lang_id."'";
       $res = $tmp_db->db_Query($q);
       //echo '<br> $q='.$q.'  $tmp_db->result='.$tmp_db->result;
       if ( !$tmp_db->result ) return false;
       $row_res = $tmp_db->db_FetchAssoc();
       //echo '<br> $row_res[name]='.$row_res['name'];
       
       $field_type = mysql_field_type($tmp_db->result,3);
       //echo '<br>$field_type='.$field_type;
       //echo '<br> $my_ret_val='.$my_ret_val;
       $retval = stripslashes( $row_res['short'] ).$devider.stripslashes( $row_res['short'] );
       
       if ( empty($retval) ){
            if ( $field_type!='blob' ){
                if ( !empty($my_ret_val)) $retval = '';
                else $retval = $this->Msg->show_text('_VALUE_NOT_SET');
            }
       }

       if( $this->make_encoding==1 AND !empty($this->encoding_from) AND !empty($this->encoding_to) ) {
          $retval = iconv($this->encoding_to, $this->encoding_from, $retval);
       }            

       return $retval;
   }  //end of fuinction GetShortNameAndNameByCod
   
   // ================================================================================================
   // Function : GetMetaTitleByCod
   // Version : 1.0.0
   // Date : 14.05.2007
   //
   // Parms :   $Table - name of table, from which will be select data
   //           $cod - cod of the record in the table where the name is searched
   //           $lang_id - id of the language
   //           $my_ret_val - parameter for returned value .( 1- return '' for empty records) 
   // Returns : $res / Void
   // Description : Get meta title from table by its cod on needed language
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 04.02.2005
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function GetMetaTitleByCod($Table, $cod, $lang_id = _LANG_ID, $my_ret_val = NULL)
   {
       if ( empty($cod) ) {
           if (!empty($my_ret_val)) return '';
           else return $this->Msg->show_text('_VALUE_NOT_SET');
       }
       $tmp_db = &DBs::getInstance();
       $q="SELECT `mtitle` FROM `".$Table."` WHERE `cod`='".addslashes($cod)."' AND `lang_id`='".$lang_id."'";
       $res = $tmp_db->db_Query($q);
       //echo '<br> $q='.$q.'  $tmp_db->result='.$tmp_db->result;
       if ( !$tmp_db->result ) return false;
       $row_res = $tmp_db->db_FetchAssoc();
       //echo '<br> $row_res[name]='.$row_res['name'];
       
       $field_type = mysql_field_type($tmp_db->result,0);
       //echo '<br>$field_type='.$field_type;
       //echo '<br> $my_ret_val='.$my_ret_val;
       $retval = stripslashes( $row_res['mtitle'] );
       
       if ( empty($retval) ){
            if ( $field_type!='blob' ){
                if ( !empty($my_ret_val)) $retval = '';
                else $retval = $this->Msg->show_text('_VALUE_NOT_SET');
            }
       }
       
       if( $this->make_encoding==1 AND !empty($this->encoding_from) AND !empty($this->encoding_to) ) {
           $retval = iconv($this->encoding_to, $this->encoding_from, $retval);
       }
       return $retval;
   }  //end of fuinction GetMetaTitleByCod                  

   // ================================================================================================
   // Function : GetMetaDescrByCod
   // Version : 1.0.0
   // Date : 14.05.2007
   //
   // Parms :   $Table - name of table, from which will be select data
   //           $cod - cod of the record in the table where the name is searched
   //           $lang_id - id of the language
   //           $my_ret_val - parameter for returned value .( 1- return '' for empty records) 
   // Returns : $res / Void
   // Description : Get meta description from table by its cod on needed language
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 04.02.2005
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function GetMetaDescrByCod($Table, $cod, $lang_id = _LANG_ID, $my_ret_val = NULL)
   {
       if ( empty($cod) ) {
           if (!empty($my_ret_val)) return '';
           else return $this->Msg->show_text('_VALUE_NOT_SET');
       }
       $tmp_db = &DBs::getInstance();
       $q="SELECT `mdescr` FROM `".$Table."` WHERE `cod`='".addslashes($cod)."' AND `lang_id`='".$lang_id."'";
       $res = $tmp_db->db_Query($q);
       //echo '<br> $q='.$q.'  $tmp_db->result='.$tmp_db->result;
       if ( !$tmp_db->result ) return false;
       $row_res = $tmp_db->db_FetchAssoc();
       //echo '<br> $row_res[name]='.$row_res['name'];
       
       $field_type = mysql_field_type($tmp_db->result,0);
       //echo '<br>$field_type='.$field_type;
       //echo '<br> $my_ret_val='.$my_ret_val;
       $retval = stripslashes( $row_res['mdescr'] );
       
       if ( empty($retval) ){
            if ( $field_type!='blob' ){
                if ( !empty($my_ret_val)) $retval = '';
                else $retval = $this->Msg->show_text('_VALUE_NOT_SET');
            }
       }
       
       if( $this->make_encoding==1 AND !empty($this->encoding_from) AND !empty($this->encoding_to) ) {
           $retval = iconv($this->encoding_to, $this->encoding_from, $retval);
       }
       return $retval;
   }  //end of fuinction GetMetaDescrByCod 

   // ================================================================================================
   // Function : GetMetaKeywordsByCod
   // Version : 1.0.0
   // Date : 14.05.2007
   //
   // Parms :   $Table - name of table, from which will be select data
   //           $cod - cod of the record in the table where the name is searched
   //           $lang_id - id of the language
   //           $my_ret_val - parameter for returned value .( 1- return '' for empty records) 
   // Returns : $res / Void
   // Description : Get meta keywords from table by its cod on needed language
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 04.02.2005
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function GetMetaKeywordsByCod($Table, $cod, $lang_id = _LANG_ID, $my_ret_val = NULL)
   {
       if ( empty($cod) ) {
           if (!empty($my_ret_val)) return '';
           else return $this->Msg->show_text('_VALUE_NOT_SET');
       }
       $tmp_db = &DBs::getInstance();
       $q="SELECT `mkeywords` FROM `".$Table."` WHERE `cod`='".addslashes($cod)."' AND `lang_id`='".$lang_id."'";
       $res = $tmp_db->db_Query($q);
       //echo '<br> $q='.$q.'  $tmp_db->result='.$tmp_db->result;
       if ( !$tmp_db->result ) return false;
       $row_res = $tmp_db->db_FetchAssoc();
       //echo '<br> $row_res[name]='.$row_res['name'];
       
       $field_type = mysql_field_type($tmp_db->result,0);
       //echo '<br>$field_type='.$field_type;
       //echo '<br> $my_ret_val='.$my_ret_val;
       $retval = stripslashes( $row_res['mkeywords'] );
       
       if ( empty($retval) ){
            if ( $field_type!='blob' ){
                if ( !empty($my_ret_val)) $retval = '';
                else $retval = $this->Msg->show_text('_VALUE_NOT_SET');
            }
       }
       
       if( $this->make_encoding==1 AND !empty($this->encoding_from) AND !empty($this->encoding_to) ) {
           $retval = iconv($this->encoding_to, $this->encoding_from, $retval);
       }
       return $retval;
   }  //end of fuinction GetMetaKeywordsByCod
   
   // ================================================================================================
   // Function : GetMetaDataByCod
   // Version : 1.0.0
   // Date : 21.07.2009
   //
   // Parms :   $Table - name of table, from which will be select data
   //           $cod - cod of the record in the table where the name is searched
   //           $lang_id - id of the language

   // Returns : $res / Void
   // Description : Get all meta data from table by its cod on needed language
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 21.07.2009
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function GetMetaDataByCod($Table, $cod, $lang_id = _LANG_ID)
   {
       if ( empty($cod) ) return false;
       $tmp_db = &DBs::getInstance();
       $q="SELECT `mtitle`, `mdescr`, `mkeywords` FROM `".$Table."` WHERE `cod`='".addslashes($cod)."' AND `lang_id`='".$lang_id."'";
       $res = $tmp_db->db_Query($q);
       //echo '<br> $q='.$q.'  $tmp_db->result='.$tmp_db->result;
       if ( !$tmp_db->result ) return false;
       $row_res = $tmp_db->db_FetchAssoc();
       //echo '<br> $row_res[name]='.$row_res['name'];
       return $row_res;
   }  //end of fuinction GetMetaDataByCod       
   
   // ================================================================================================
   // Function : GetTranslitByCod
   // Version : 1.0.0
   // Date : 21.07.2009
   //
   // Parms :   $Table - name of table, from which will be select data
   //           $cod - cod of the record in the table where the name is searched
   //           $lang_id - id of the language
   // Returns : $res / Void
   // Description : Get the translit from table by its cod on needed language
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 21.07.2009
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function GetTranslitByCod($Table, $cod, $lang_id = _LANG_ID)
   {
       if ( empty($cod) ) return false;
       $tmp_db = &DBs::getInstance();
       $q="SELECT  `".$Table."`.translit FROM `".$Table."` WHERE `cod`='".addslashes($cod)."' AND `lang_id`='".$lang_id."'";
       $res = $tmp_db->db_Query($q);
       //echo '<br> $q='.$q.'  $tmp_db->result='.$tmp_db->result;
       if ( !$tmp_db->result ) return false;
       $row_res = $tmp_db->db_FetchAssoc();
       $retval = stripslashes( $row_res['translit'] );
       return $retval;
   }  //end of fuinction GetTranslitByCod       
   
   // ================================================================================================
   // Function : GetCodByTranslit
   // Version : 1.0.0
   // Date : 21.07.2009
   //
   // Parms :   $Table - name of table, from which will be select data
   //           $cod - cod of the record in the table where the name is searched
   //           $lang_id - id of the language
   // Returns : $res / Void
   // Description : Get cod from table by its translit on needed language
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 21.07.2009
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function GetCodByTranslit($Table, $translit, $lang_id = _LANG_ID)
   {
       if ( empty($translit) ) return false;
       $tmp_db = &DBs::getInstance();
       $q="SELECT  `".$Table."`.cod FROM `".$Table."` WHERE `translit`='".addslashes($translit)."' AND `lang_id`='".$lang_id."'";
       $res = $tmp_db->db_Query($q);
       //echo '<br> $q='.$q.'  $tmp_db->result='.$tmp_db->result;
       if ( !$tmp_db->result ) return false;
       $row_res = $tmp_db->db_FetchAssoc();
       $retval = stripslashes( $row_res['cod'] );
       return $retval;
   }  //end of fuinction GetCodByTranslit        
   
   // ================================================================================================
   // Function : GetCodByName
   // Version : 1.0.0
   // Date : 15.11.2006
   //
   // Parms :   $Table - name of table, from which will be select data
   //           $name - cod of the record in the table where the name is searched
   //           $lang_id - id of the language
   // Returns : $res / Void
   // Description : Get cod of the record from table by its name on needed language
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 15.11.2006
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function GetCodByName($Table, $name)
   {
       $tmp_db = &DBs::getInstance();
       $q="SELECT * FROM `".$Table."` WHERE `name`='".addslashes($name)."'";
       $res = $tmp_db->db_Query($q);
       //echo '<br> $q='.$q.'  $tmp_db->result='.$tmp_db->result;
       if ( !$tmp_db->result ) return false;
       $row_res = $tmp_db->db_FetchAssoc();
       $retval = stripslashes( $row_res['cod'] );
       return $retval;
   }  //end of fuinction GetCodByName       
   
   // ================================================================================================
   // Function : GetCodByShortName
   // Version : 1.0.0
   // Date : 15.11.2006
   //
   // Parms :   $Table - name of table, from which will be select data
   //           $name - cod of the record in the table where the name is searched
   // Returns : $res / Void
   // Description : Get cod of the record from table by its short name on needed language
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 15.11.2006
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function GetCodByShortName($Table, $name)
   {
       $tmp_db = &DBs::getInstance();
       $q="SELECT * FROM `".$Table."` WHERE `short`='".addslashes($name)."'";
       $res = $tmp_db->db_Query($q);
       //echo '<br> $q='.$q.'  $tmp_db->result='.$tmp_db->result;
       if ( !$tmp_db->result ) return false;
       $row_res = $tmp_db->db_FetchAssoc();
       $retval = stripslashes( $row_res['cod'] );
       return $retval;
   }  //end of fuinction GetCodByShortName() 
   
   // ================================================================================================
   // Function : GetListName
   // Version : 1.0.0
   // Date : 17.04.2006
   //
   // Parms :   $Table - name of table, from which will be select data
   //           $lang_id - id of the language on which you want to get names
   //           $return_type - type of return value. Can be 'str' or 'array';
   //           $sort - name of field for sorting
   //           $asc_desc - type f sorting: asc or desc
   //           $return_data - what data to return: can be name of field or 'All', then return multi-array with all fields
   // Returns : $res / Void
   // Description : Get the list of names from table on needed language
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 17.04.2006 
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function GetListName( $Table, $lang_id = NULL, $return_type = 'str', $sort='cod', $asc_desc='asc', $return_data='all' )
   {
       if (!$lang_id) $lang_id = _LANG_ID;
      
      $tmp_db = &DBs::getInstance();
      $q = "SELECT * FROM `".$Table."` WHERE 1 LIMIT 1";
      $res = $tmp_db->db_Query($q);
      //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
      if ( !$res OR !$tmp_db->result ) return false;
      $fields_col = mysql_num_fields($tmp_db->result);
      
      if ($fields_col>4) {
        $q = "SELECT * FROM `".$Table."` 
              WHERE `lang_id`='".$lang_id."' 
              ORDER BY `move`";
      } 
      else $q = "SELECT * FROM `".$Table."` WHERE `lang_id`='".$lang_id."'";           
      
       $DB_tmp = new DB();            
       $res = $DB_tmp->db_Query($q);
       //echo '<br> $q='.$q.'  $DB_tmp->result='.$DB_tmp->result;
       if ( !$DB_tmp->result ) return false;
       $rows = $DB_tmp->db_GetNumRows();
       //echo '<br> rows='.$rows;
       if (!$rows) return $this->Msg->show_text('_VALUE_NOT_SET');
       $retstr = NULL;
       for ($i=0; $i<$rows; $i++){
           $row = $DB_tmp->db_FetchAssoc();
           if ($return_type=='str'){
              if (!$retstr) $retstr=stripslashes($row['name']).'<br>';
              else $retstr=$retstr.stripslashes($row['name']).'<br>';
           }
           else {
               switch($return_data){
                   case 'cod':
                    $retstr[$row[$sort]] = stripslashes($row['cod']);
                    break;
                   case 'name':
                    $retstr[$row[$sort]] = stripslashes($row['name']);
                    break;
                   case 'short':
                    $retstr[$row[$sort]] = stripslashes($row['short']);
                    break;
                   case 'img':
                    $retstr[$row[$sort]] = $this->GetImageByCodOnLang($Table, $row['cod'], $lang_id);
                    break;
                   case 'move':
                    $retstr[$row[$sort]] = $row['move'];
                    break;
                   default:
                    $retstr[$row[$sort]]['cod'] =stripslashes($row['cod']);
                    $retstr[$row[$sort]]['name'] = stripslashes($row['name']);
                    if(isset($row['short']))
                        $retstr[$row[$sort]]['short'] = stripslashes($row['short']);
                      else 
                      $retstr[$row[$sort]]['short']='';
                    $retstr[$row[$sort]]['img'] = $this->GetImageByCodOnLang($Table, $row['cod'], $lang_id);
                    break;                      
               }
           }
       }
       if ($return_type=='array'){
           if( $asc_desc=='asc') ksort($retstr);
           if( $asc_desc=='desc') krsort($retstr);
       }
       return $retstr;
   } // end of function GetListName()
   
   // ================================================================================================
   // Function : GetNamesInStr
   // Version : 1.0.0
   // Date : 17.04.2006
   //
   // Parms :   $Table - name of table, from which will be select data
   //           $lang_id - id of the language on which you want to get names
   //           $val - values, which will be getting to  the string
   //           $divider - divider for string 
   // Returns : $res / Void
   // Description : Get the list of names from table on needed language in string like (html, javascript, css, php, mysql))
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 17.04.2006 
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function GetNamesInStr( $Table, $lang_id = NULL, $val=NULL, $divider=',' )
   {
       if (!$lang_id) $lang_id = _LANG_ID;
      
      $tmp_db = &DBs::getInstance();
      $q = "SELECT * FROM `".$Table."` WHERE 1 LIMIT 1";
      $res = $tmp_db->db_Query($q);
      //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
      if ( !$res ) return false;
      if ( !$tmp_db->result ) return false;
      $fields_col = mysql_num_fields($tmp_db->result);
      
      if ($fields_col>4) $q = "select * from `".$Table."` where lang_id='".$lang_id."' order by `move`"; 
      else $q = "select * from `".$Table."` where lang_id='".$lang_id."'";           
      
       $DB_tmp = &DBs::getInstance();            
       $res = $DB_tmp->db_Query($q);
       //echo '<br> $q='.$q.'  $DB_tmp->result='.$DB_tmp->result;
       if ( !$DB_tmp->result ) return false;
       $rows = $DB_tmp->db_GetNumRows();
       //echo '<br> rows='.$rows;
       if (!$rows) return $this->Msg->show_text('_VALUE_NOT_SET');
       $retstr = NULL;
       for ($i=0; $i<$rows; $i++){
           $row = $DB_tmp->db_FetchAssoc();
           if (is_array($val)) {
               foreach($val as $k=>$v)
               {
                if (isset($k) and ($v==stripslashes($row['cod']))) 
                 if (!$retstr) $retstr = stripslashes($row['name']);
                 else $retstr = $retstr.$divider.' '.stripslashes($row['name']);                    
                //echo '<br>$k='.$k.' $v='.$v.' $row1[cod]='.$row1['cod']; 
               }
           }
           else {
            if(!empty($val)){
                if (!$retstr) $retstr = stripslashes($row['name']);
                else $retstr = $retstr.$divider.' '.stripslashes($row['name']);                    
            }
           }               
       }
       return $retstr;
   } // end of function GetNamesInStr()       
   
   
   // ================================================================================================
   // Function : GetShortNamesInStr
   // Version : 1.0.0
   // Date : 17.04.2006
   //
   // Parms :   $Table - name of table, from which will be select data
   //           $lang_id - id of the language on which you want to get names
   //           $val - values, which will be getting to  the string
   //           $divider - divider for string 
   // Returns : $res / Void
   // Description : Get the list of names from table on needed language in string like (html, javascript, css, php, mysql))
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 17.04.2006 
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function GetShortNamesInStr( $Table, $lang_id = NULL, $val=NULL, $divider=',' )
   {
       if (!$lang_id) $lang_id = _LANG_ID;
      
      $tmp_db = &DBs::getInstance();
      $q = "SELECT * FROM `".$Table."` WHERE 1 LIMIT 1";
      $res = $tmp_db->db_Query($q);
      //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
      if ( !$res ) return false;
      if ( !$tmp_db->result ) return false;
      $fields_col = mysql_num_fields($tmp_db->result);
      
      if ($fields_col>4) $q = "select * from `".$Table."` where lang_id='".$lang_id."' order by `move`"; 
      else $q = "select * from `".$Table."` where lang_id='".$lang_id."'";           
      
       $DB_tmp = &DBs::getInstance();            
       $res = $DB_tmp->db_Query($q);
       //echo '<br> $q='.$q.'  $DB_tmp->result='.$DB_tmp->result;
       if ( !$DB_tmp->result ) return false;
       $rows = $DB_tmp->db_GetNumRows();
       //echo '<br> rows='.$rows;
       if (!$rows) return $this->Msg->show_text('_VALUE_NOT_SET');
       $retstr = NULL;
       for ($i=0; $i<$rows; $i++){
           $row = $DB_tmp->db_FetchAssoc();
           if (is_array($val)) {
               foreach($val as $k=>$v)
               {
                if (isset($k) and ($v==stripslashes($row['cod']))) 
                 if (!$retstr) $retstr = stripslashes($row['short']);
                 else $retstr = $retstr.$divider.' '.stripslashes($row['short']);                    
                //echo '<br>$k='.$k.' $v='.$v.' $row1[cod]='.$row1['cod']; 
               }
           }
           else {
            if (!$retstr) $retstr = stripslashes($row['short']);
            else $retstr = $retstr.$divider.' '.stripslashes($row['short']);                    
           }               
       }
       return $retstr;
   } // end of function GetShortNamesInStr()        
   
   
   // ================================================================================================
   // Function : GetNamesInList
   // Version : 1.0.0
   // Date : 23.05.2006
   //
   // Parms :   $Table - name of table, from which will be select data
   //           $lang_id - id of the language on which you want to get names
   //           $val - values, which will be getting to  the string
   //           $divider - divider for string 
   // Returns : $res / Void
   // Description : Get the list of names from table on needed language in list like 
   //               (html;
   //                javascript;
   //                css;
   //                php;
   //                mysql;)
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 23.05.2006
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function GetNamesInList( $Table, $lang_id = NULL, $val=NULL, $divider=NULL )
   {
       if (!$lang_id) $lang_id = _LANG_ID;
      
      $tmp_db = &DBs::getInstance();
      $q = "SELECT * FROM `".$Table."` WHERE 1 LIMIT 1";
      $res = $tmp_db->db_Query($q);
      //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
      if ( !$res ) return false;
      if ( !$tmp_db->result ) return false;
      $fields_col = mysql_num_fields($tmp_db->result);
      
      if ($fields_col>4) $q = "select * from `".$Table."` where lang_id='".$lang_id."' order by `move`"; 
      else $q = "select * from `".$Table."` where lang_id='".$lang_id."'";           
      
       $DB_tmp = &DBs::getInstance();            
       $res = $DB_tmp->db_Query($q);
       //echo '<br> $q='.$q.'  $DB_tmp->result='.$DB_tmp->result;
       if ( !$DB_tmp->result ) return false;
       $rows = $DB_tmp->db_GetNumRows();
       //echo '<br> rows='.$rows;
       if (!$rows) return $this->Msg->show_text('_VALUE_NOT_SET');
       $retstr = NULL;
       for ($i=0; $i<$rows; $i++){
           $row = $DB_tmp->db_FetchAssoc();
           if (count($val)>0) {
               foreach($val as $k=>$v)
               {
                if (isset($k) and ($v==stripslashes($row['cod']))) 
                 if (!$retstr) $retstr = stripslashes($row['name']).$divider;
                 else $retstr = $retstr.'<br>'.stripslashes($row['name']).$divider;                    
                //echo '<br>$k='.$k.' $v='.$v.' $row1[cod]='.$row1['cod']; 
               }
           }               
       }
       return $retstr;
   } // end of function GetNamesInList()        
   
   // ================================================================================================
   // Function : GetNameById
   // Version : 1.0.0
   // Date : 04.02.2005
   //
   // Parms :   $Table - name of table, from which will be select data
   //           $id - id of the record in the table where the name is searched
   // Returns : $res / Void
   // Description : Get the name from table by its id on needed language
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 04.02.2005
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function GetNameById($Table, $id)
   {
       if (empty($id)) return $this->Msg->show_text('_VALUE_NOT_SET');
       $tmp_db = &DBs::getInstance(); 
       $q="select name FROM `".$Table."` WHERE id='".$id."'";
       $res = $tmp_db->db_Query($q);
       if (!$res) return false;
       if( !$tmp_db->result ) return false;
       $row_res=$tmp_db->db_FetchAssoc();
       //echo '<br> $id='.$id.' $row_res[cod]='.$row_res['cod'].' $row_ress[name]='. $row_ress['name'];
       return stripslashes($row_res['name']);
   }  //end of fuinction GetNameById

   // ================================================================================================
   // Function : GetById
   // Version : 1.0.0
   // Date : 25.02.2005
   //
   // Parms : $spr    / name of table, from which will be select data
   //         $id     / id of the record
   // Returns : $row['name'] - field 'name' from the record
   // Description : Get the name from table by its id
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 25.02.2005
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function GetById( $spr, $id )
   {
      $tmp_db = &DBs::getInstance();
      $q = "Select * from `".$spr."` WHERE `id`='".$id."'";
      $res = $tmp_db->db_Query($q);
      if ( !$tmp_db->result ) return false;
      $row = $tmp_db->db_FetchAssoc();
      return stripslashes($row['name']);
   } //end of fuinction ShowInCheckBox

   // ================================================================================================
   // Function : GetByCod
   // Version : 1.0.0
   // Date : 25.02.2005
   //
   // Parms : $spr     / name of table, from which will be select data
   //         $cod     / cod of the record
   //         $lang_id / id of the language
   // Returns : array $res[lang_id]=name - description on lang_id language.
   // Description : Get the name from table by its cod and language. If lang_id isn't set, it will be
   //               return all of the record with such cod.
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 25.02.2005
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function GetByCod( $spr, $cod, $lang_id = NULL )
   {
      $tmp_db = &DBs::getInstance();
      $q = "Select * from `".$spr."` WHERE `cod`='".$cod."'";
      if (!empty($lang_id)) $q = $q." AND lang_id='".$lang_id."'";
      $res = $tmp_db->db_Query($q);
      if ( !$tmp_db->result ) return false;
      $rows = $tmp_db->db_GetNumRows();
      $ret[$lang_id]='';
      for ($i=0; $i<$rows; $i++){
          $row = $tmp_db->db_FetchAssoc();
          $ret[$row['lang_id']] = stripslashes($row['name']);
      }
      return $ret;
   }
   
   // ================================================================================================
   // Function : GetCountValuesInSprOnLang
   // Version : 1.0.0
   // Date : 25.02.2005
   //
   // Parms : $spr     / name of table, from which will be select data
   //         $cod     / cod of the record
   //         $lang_id / id of the language
   // Returns : array $res[lang_id]=name - description on lang_id language.
   // Description : Get the name from table by its cod and language. If lang_id isn't set, it will be
   //               return all of the record with such cod.
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 25.02.2005
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function GetCountValuesInSprOnLang( $spr, $lang_id = NULL )
   {
      if (!$lang_id) $lang_id=_LANG_ID;
      $DB_tmp = &DBs::getInstance();
      
      $q = "Select * from `".$spr."` WHERE `lang_id`='".$lang_id."'";
      $res = $DB_tmp->db_Query($q);
      if ( !$DB_tmp->result ) return false;
      $rows = $DB_tmp->db_GetNumRows();
      return $rows;
   }// end of function GetCountValuesInSprOnLang()
  
   
   // ================================================================================================
   // Function : IsFieldExist
   // Version : 1.0.0
   // Date : 13.10.2006
   //
   // Parms :   $Table  / name of table, from which will be checking
   //           $field  / name of the field whitch will be checking
   // Returns : return 1 or 0
   // Description : return exist or not (1 or 0) field in this table
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 13.10.2006
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function IsFieldExist($Table, $field = 'img')
   {
       $image = NULL;
       
       $tmp_db = &DBs::getInstance();;
       $q = "SELECT * FROM `".$Table."` WHERE 1 LIMIT 1";
       $res = $tmp_db->db_Query($q);
       //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
       if ( !$res ) return false;
       if ( !$tmp_db->result ) return false;
       
       $i = 0;
       while ($i < mysql_num_fields($tmp_db->result)) {
            $meta = mysql_fetch_field($tmp_db->result, $i);
            if ($meta) {
               if ($meta->name==$field) return true;
            }
            $i++;
       }
       return false;
   } //end of function IsFieldExist() 
  
   // ================================================================================================
   // Function : GetImageByCod
   // Version : 1.0.0
   // Date : 13.10.2006
   //
   // Parms : $Table   / name of table, from which will be select data
   //         $cod     / cod of the record
   // Returns : return $image for current value with cod=$cod
   // Description : return image for current value with cod=$cod, if it is exist 
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 13.10.2006
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function GetImageByCodOnLang($Table, $cod, $lang_id, $return_path=1)
   {
       $image = NULL;
       $tmp_db = &DBs::getInstance();
       
       if( $this->IsFieldExist($Table, 'img') ) {
           $q = "SELECT `img` FROM `".$Table."` WHERE 1 AND `cod`='".addslashes($cod)."' AND `lang_id`='$lang_id'";
           $res = $tmp_db->db_Query($q);
           //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
           if ( !$res or !$tmp_db->result ) return false;
           $row = $tmp_db->db_FetchAssoc();
           if ( !empty($row['img'])) {
               if( $return_path==1 ) $image = Spr_Img_Path_Small.$Table.'/'.$lang_id.'/'.$row['img'];
               else $image = $row['img'];
           }
       }
       else $this->AutoInsertColumnImg( $Table );
       //echo '<br>$image='.$image;
       return $image;
   } //end of function GetImageByCod()      
  
    // ================================================================================================
    // Function : GetImgFullPath
    // Version : 1.0.0
    // Date : 06.11.2006
    //
    // Parms :  $spr - name of  the table of spr
    //          $lang_id - id of the language
    //          $img - name of the picture
    // Returns : $res / Void
    // Description : return full path to the image  like z:/home/unior/www/images/spr/mod_catalog_spr_manufac/2/1162648375_2.jpg
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 06.11.2006  
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function GetImgFullPath($img = NULL, $spr = NULL, $lang_id = NULL )
    {
        if ( empty($spr) ) $spr = $this->spr;
        if ( empty($lang_id) ) $lang_id = $this->lang_id; 
        if ( empty($img) ) $img = $this->img; 
        
        return Spr_Img_Path.$spr.'/'.$lang_id.'/'.$img;
    } //end of function GetImgFullPath()
    
    // ================================================================================================
    // Function : GetImgPath
    // Version : 1.0.0
    // Date : 06.11.2006
    //
    // Parms :  $spr - name of  the table of spr
    //          $lang_id - id of the language
    //          $img - name of the picture
    // Returns : $res / Void
    // Description : return path to the image like /images/spr/mod_catalog_spr_manufac/2/1162648375_2.jpg 
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 06.11.2006  
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function GetImgPath($img = NULL, $spr = NULL, $lang_id = NULL )
    {
        if ( empty($spr) ) $spr = $this->spr;
        if ( empty($lang_id) ) $lang_id = $this->lang_id; 
        if ( empty($img) ) $img = $this->img; 
        
        return Spr_Img_Path_Small.$spr.'/'.$lang_id.'/'.$img;
    } //end of function GetImgPath()        
        
    // ================================================================================================
    // Function : GetImgTitle
    // Version : 1.0.0
    // Date : 06.11.2006
    //
    // Parms :  $spr - name of  the table of spr
    //          $img - name of the picture
    //          $lang_id - id of the language 
    // Returns : $res / Void
    // Description : return full path to the image 
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 06.11.2006  
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function GetImgTitle( $spr, $img, $lang_id=NULL )
    {
        $tmp_db = &DBs::getInstance();
        $q = "SELECT * FROM `".$spr."` WHERE `img`='".$img."'";
        if( !empty($lang_id) ) $q = $q ." AND `lang_id`='".$lang_id."'";
        $res = $tmp_db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
        if ( !$res OR !$tmp_db->result ) return false;
        $row = $tmp_db->db_FetchAssoc();
        $retval = $row['name'];
        if( $this->make_encoding==1 AND !empty($this->encoding_from) AND !empty($this->encoding_to) ) {
           $retval = iconv($this->encoding_to, $this->encoding_from, $retval);
        }            
        return strip_tags($retval);
    } //end of function GetImgTitle()   
      
     // ================================================================================================
    // Function : ShowImage
    // Version : 1.0.0
    // Date : 06.11.2006
    //
    // Parms :  $spr - name of spr table
    //          $lang_id - id of the language
    //          $img - path of the picture
    //          $size -  Can be "size_auto" or  "size_width" or "size_height"
    //          $quality - quality of the image
    //          $wtm - make watermark or not. Can be "txt" or "img"
    //          $parameters - other parameters for TAG <img> like border
    //          $ret_img_or_path - if 0 or NULL then image with tags <img>. If 1 the return only path to new image
    // Returns : $res / Void
    // Description : Show images from catalogue
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 06.11.2006  
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function ShowImage($spr = NULL, $lang_id = NULL, $img = NULL, $size = NULL, $quality = 85, $wtm = NULL, $parameters = NULL, $ret_img_or_path=null)
    {
         $size_auto = NULL;
         $size_width = NULL;
         $size_height = NULL;
         $alt = NULL;
         $title = NULL;
         $str = NULL;
         
         $img_with_path = $this->GetImgPath($img, $spr, $lang_id);
         $img_full_path = $this->GetImgFullPath($img, $spr, $lang_id);   
         
         $mas_img_name=explode(".",$img_with_path);

         if ( strstr($size,'size_width') ){ 
            $size_width = substr( $size, strrpos($size,'=')+1, strlen($size) );
            $img_name_new = $mas_img_name[0].SPR_ADDITIONAL_FILES_TEXT.'width_'.$size_width.'.'.$mas_img_name[1];
         }
         elseif ( strstr($size,'size_auto') ) {
            $size_auto = substr( $size, strrpos($size,'=')+1, strlen($size) );
            $img_name_new = $mas_img_name[0].SPR_ADDITIONAL_FILES_TEXT.'auto_'.$size_auto.'.'.$mas_img_name[1];
         }
         elseif ( strstr($size,'size_height') ) {
            $size_height = substr( $size, strrpos($size,'=')+1, strlen($size) );
            $img_name_new = $mas_img_name[0].SPR_ADDITIONAL_FILES_TEXT.'height_'.$size_height.'.'.$mas_img_name[1];
         }
         elseif(empty($size)) $img_name_new = $mas_img_name[0].'.'.$mas_img_name[1];
         //echo '$img_name_new='.$img_name_new;
         $img_full_path_new = SITE_PATH.$img_name_new; 
         //if exist local small version of the image then use it
         if( file_exists($img_full_path_new)){
            //echo 'exist';
            if ( !strstr($parameters, 'alt') ) $alt = $this->GetImgTitle($spr, $img, $lang_id);
            if ( !strstr($parameters, 'title') ) $title = $this->GetImgTitle($spr, $img, $lang_id);
            if ( !strstr($parameters, 'alt') )  $parameters = $parameters.' alt="'.$alt.'"';
            if ( !strstr($parameters, 'title') ) $parameters = $parameters.' title=" '.$title.' "';        
            if($ret_img_or_path==1) $str = $img_name_new;
            else $str = '<img src="'.$img_name_new.'" '.$parameters.' />';
         }
        //else use original image on the server SITE_PATH and make small version on local server
        else {         
            //echo 'Not  exist';
            //echo '<br> $img_full_path='.$img_full_path.'<br> $size_auto='.$size_auto;
            if ( !file_exists($img_full_path) ) return false;

            $thumb = new Thumbnail($img_full_path);
            //echo '<br>$thumb->img[x_thumb]='.$thumb->img['x_thumb'].' $thumb->img[y_thumb]='.$thumb->img['y_thumb'];
            $src_x = $thumb->img['x_thumb'];
            $src_y = $thumb->img['y_thumb'];
            if ( !empty($size_width ) and empty($size_height) ) $thumb->size_width($size_width);
            if ( !empty($size_height) and empty($size_width) ) $thumb->size_height($size_height);
            if ( !empty($size_width) and !empty($size_height) ) $thumb->size($size_width,$size_height); 
            if ( !$size_width and !$size_height and $size_auto ) $thumb->size_auto($size_auto);
            //echo '<br>$thumb->img[x_thumb]='.$thumb->img['x_thumb'].' $thumb->img[y_thumb]='.$thumb->img['y_thumb'];
            
            //if original image smaller than thumbnail then use original image and don't create thumbnail
            if($thumb->img['x_thumb']>=$src_x OR $thumb->img['y_thumb']>=$src_y){
                if ( !strstr($parameters, 'alt') ) $alt = $this->GetImgTitle($spr, $img, $lang_id);
                if ( !strstr($parameters, 'title') ) $title = $this->GetImgTitle($spr, $img, $lang_id);
                if ( !strstr($parameters, 'alt') ) $parameters = $parameters.' alt="'.$alt.'"';
                if ( !strstr($parameters, 'title') ) $parameters = $parameters.' title=" '.$title.' "';        
                if($ret_img_or_path==1) $str = $img_with_path;
                else $str = '<img src="'.$img_with_path.'" '.$parameters.' />';
            }
            else{
                $thumb->quality=$quality;                  //default 75 , only for JPG format  
                //echo '<br>$wtm='.$wtm;
                if ( $wtm == 'img' ) {
                    $thumb->img_watermark = SITE_PATH.'/images/design/m01.png';        // [OPTIONAL] set watermark source file, only PNG format [RECOMENDED ONLY WITH GD 2 ]
                    $thumb->img_watermark_Valing='CENTER';           // [OPTIONAL] set watermark vertical position, TOP | CENTER | BOTTOM
                    $thumb->img_watermark_Haling='CENTER';           // [OPTIONAL] set watermark horizonatal position, LEFT | CENTER | RIGHT
                }
                if ( $wtm == 'txt' ) {
                    if ( defined('WATERMARK_TEXT') ) $thumb->txt_watermark=SPR_WATERMARK_TEXT;        // [OPTIONAL] set watermark text [RECOMENDED ONLY WITH GD 2 ]
                    else $thumb->txt_watermark='';
                    $thumb->txt_watermark_color='000000';        // [OPTIONAL] set watermark text color , RGB Hexadecimal[RECOMENDED ONLY WITH GD 2 ]
                    $thumb->txt_watermark_font=5;                // [OPTIONAL] set watermark text font: 1,2,3,4,5
                    $thumb->txt_watermark_Valing='TOP';           // [OPTIONAL] set watermark text vertical position, TOP | CENTER | BOTTOM
                    $thumb->txt_watermark_Haling='LEFT';       // [OPTIONAL] set watermark text horizonatal position, LEFT | CENTER | RIGHT
                    $thumb->txt_watermark_Hmargin=10;          // [OPTIONAL] set watermark text horizonatal margin in pixels
                    $thumb->txt_watermark_Vmargin=10;           // [OPTIONAL] set watermark text vertical margin in pixels     
                }

                $mas_img_name=explode(".",$img_with_path);
                //$img_name_new = $mas_img_name[0].SPR_ADDITIONAL_FILES_TEXT.intval($thumb->img['x_thumb']).'x'.intval($thumb->img['y_thumb']).'.'.$mas_img_name[1];
                if(!empty($size_width )) 
                   $img_name_new = $mas_img_name[0].SPR_ADDITIONAL_FILES_TEXT.'width_'.$size_width.'.'.$mas_img_name[1];
                elseif(!empty($size_auto )) 
                   $img_name_new = $mas_img_name[0].SPR_ADDITIONAL_FILES_TEXT.'auto_'.$size_auto.'.'.$mas_img_name[1];
                elseif(!empty($size_height )) 
                   $img_name_new = $mas_img_name[0].SPR_ADDITIONAL_FILES_TEXT.'height_'.$size_height.'.'.$mas_img_name[1];
                $img_src = $img_name_new; 
                $img_full_path_new = SITE_PATH.$img_name_new; 
                $uploaddir = SITE_PATH.substr($img_with_path, 0, strrpos($img_with_path,'/')).'/';                      

                //echo '<br>$img_name_new='.$img_name_new;  
                //echo '<br>$img_full_path_new='.$img_full_path_new;
                //echo '<br>$img_src='.$img_src;
                //echo '<br>$uploaddir='.$uploaddir;                
         
                $alt = $this->GetImgTitle( $spr, $img, $lang_id );
                $title = $this->GetImgTitle( $spr, $img, $lang_id ); 

                if ( !strstr($parameters, 'alt') )  $parameters = $parameters.' alt="'.$alt.'"';
                if ( !strstr($parameters, 'title') ) $parameters = $parameters.' title=" '.$title.' "';
         
                if ( !file_exists($img_full_path_new) ){
                    //echo '<br>$uploaddir='.$uploaddir.'<br>$img_full_path_new='.$img_full_path_new;
                    if( !file_exists ($uploaddir) ) mkdir($uploaddir,0777);
                    if( file_exists($uploaddir) ) @chmod($uploaddir,0777);
                    $thumb->process();       // generate image
                    //make new image like R1800TII_big.jpg -> R1800TII_big_autozoom_100x84.jpg 
                    $thumb->save($img_full_path_new);
                    @chmod($uploaddir,0755); 
                    $params = "img=$img&amp;spr=$spr&amp;lang_id=$lang_id&amp;$size&amp;quality=$quality";
                    //echo '<br> $params='.$params;
                }
                if($ret_img_or_path==1) $str = $img_src;
                else $str = '<img src="'.$img_src.'" '.$parameters.' />';
            }//end else
         }//end else
         //echo '<br>$str='.$str;   
         return $str;  
    } // end of function ShowImage()

    // ================================================================================================
    // Function : ShowImageByPath
    // Version : 1.0.0
    // Date : 06.11.2006
    //
    // Parms :  $img - path of the picture
    //          $size -  Can be "size_auto" or  "size_width" or "size_height"
    //          $quality - quality of the image
    //          $wtm - make watermark or not. Can be "txt" or "img"
    //          $parameters - other parameters for TAG <img> like border
    // Returns : $res / Void
    // Description : Show images from catalogue
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 06.11.2006  
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function ShowImageByPath($img_with_path = NULL, $size = NULL, $quality = NULL, $wtm = NULL, $parameters = NULL)
    {
     $size_auto = NULL;
     $size_width = NULL;
     $size_height = NULL;
     if ( strstr($size,'size_auto') ) $size_auto = substr( $size, strrpos($size,'=')+1, strlen($size) );
     if ( strstr($size,'size_width') ) $size_width = substr( $size, strrpos($size,'=')+1, strlen($size) );
     if ( strstr($size,'size_height') ) $size_height = substr( $size, strrpos($size,'=')+1, strlen($size) );
     
     if (empty($quality)) $quality=85;
     
     $img = substr($img_with_path, strrpos($img_with_path,'/')+1);
     
     $tmp_for_lang_id = substr($img_with_path, 0, strrpos($img_with_path,'/'));
     $lang_id = substr($tmp_for_lang_id, strrpos($tmp_for_lang_id,'/')+1);
     
     $tmp_for_spr = substr($tmp_for_lang_id, 0, strrpos($tmp_for_lang_id,'/')); 
     $spr = substr($tmp_for_spr, strrpos($tmp_for_spr,'/')+1); 
     
     $img_full_path = SITE_PATH.$img_with_path;   
       
     //$img_full_path = Spr_Img_Path.$spr.'/'.$img_with_path; // like z:/home/speakers/www/uploads/45/R1800TII_big.jpg
     //echo '<br> $img_with_path='.$img_with_path.'<br>$img_full_path='.$img_full_path.'<br>$img='.$img.'<br>$lang_id='.$lang_id.'<br>$spr='.$spr;
     //if ( !file_exists($img_full_path) ) return false;

     $thumb = new Thumbnail($img_full_path);
     //echo '<br>$thumb->img[x_thumb]='.$thumb->img['x_thumb'].' $thumb->img[y_thumb]='.$thumb->img['y_thumb']; 
     $src_x = $thumb->img['x_thumb'];
     $src_y = $thumb->img['y_thumb'];
     if ( !empty($size_width ) and empty($size_height) ) $thumb->size_width($size_width);
     if ( !empty($size_height) and empty($size_width) ) $thumb->size_height($size_height);
     if ( !empty($size_width) and !empty($size_height) ) $thumb->size($size_width,$size_height); 
     if ( !$size_width and !$size_height and $size_auto ) $thumb->size_auto($size_auto);		            // [OPTIONAL] set the biggest width and height for thumbnail
     //echo '<br>$thumb->img[x_thumb]='.$thumb->img['x_thumb'].' $thumb->img[y_thumb]='.$thumb->img['y_thumb']; 
     
     //if original image smaller than thumbnail then use original image and don't create thumbnail
     if($thumb->img['x_thumb']>=$src_x OR $thumb->img['y_thumb']>=$src_y){
        //$img_full_path = $settings_img_path.'/'.$img_name;
        $img_full_path = $img_with_path;
        //echo '<br>$settings_img_path='.$settings_img_path.' $img_full_path='.$img_full_path;
        if ( !strstr($parameters, 'alt') ) $alt = '';//$this->GetPictureAlt($img);
        if ( !strstr($parameters, 'title') ) $title = '';//$this->GetPictureTitle($img);
        if ( !strstr($parameters, 'alt') )  $parameters = $parameters.' alt="'.$alt.'"';
        if ( !strstr($parameters, 'title') ) $parameters = $parameters.' title=" '.$title.' "';        
        //$str = '<img src="'.$img_full_path.'" '.$parameters.' />';
        ?><img src="<?=$img_full_path;?>" <?=$parameters?>><?
        return;
     }
     else{
         $thumb->quality=$quality;                  //default 75 , only for JPG format
         //echo '<br>$thumb->quality='.$thumb->quality; 
           
         //echo '<br>$wtm='.$wtm;
         if ( $wtm == 'img' ) {
            $thumb->img_watermark = SITE_PATH.'/images/design/m01.png';	    // [OPTIONAL] set watermark source file, only PNG format [RECOMENDED ONLY WITH GD 2 ]
            $thumb->img_watermark_Valing='CENTER';   	    // [OPTIONAL] set watermark vertical position, TOP | CENTER | BOTTOM
            $thumb->img_watermark_Haling='CENTER';   	    // [OPTIONAL] set watermark horizonatal position, LEFT | CENTER | RIGHT
         }
         if ( $wtm == 'txt' ) {
             if ( defined('WATERMARK_TEXT') ) $thumb->txt_watermark=SPR_WATERMARK_TEXT;	    // [OPTIONAL] set watermark text [RECOMENDED ONLY WITH GD 2 ]
             else $thumb->txt_watermark='';
             $thumb->txt_watermark_color='000000';	    // [OPTIONAL] set watermark text color , RGB Hexadecimal[RECOMENDED ONLY WITH GD 2 ]
             $thumb->txt_watermark_font=5;	            // [OPTIONAL] set watermark text font: 1,2,3,4,5
             $thumb->txt_watermark_Valing='TOP';   	    // [OPTIONAL] set watermark text vertical position, TOP | CENTER | BOTTOM
             $thumb->txt_watermark_Haling='LEFT';       // [OPTIONAL] set watermark text horizonatal position, LEFT | CENTER | RIGHT
             $thumb->txt_watermark_Hmargin=10;          // [OPTIONAL] set watermark text horizonatal margin in pixels
             $thumb->txt_watermark_Vmargin=10;           // [OPTIONAL] set watermark text vertical margin in pixels     
         }

         $thumb->process();   	// generate image  

         //make new image like R1800TII_big.jpg -> R1800TII_big_autozoom_100x84.jpg  
         $mas_img_name=explode(".",$img_with_path);
         $img_name_new = $mas_img_name[0].SPR_ADDITIONAL_FILES_TEXT.intval($thumb->img['x_thumb']).'x'.intval($thumb->img['y_thumb']).'.'.$mas_img_name[1];
         $img_src = $img_name_new; 
         $img_full_path_new = SITE_PATH.$img_name_new; 
         $uploaddir = SITE_PATH.substr($img_with_path, 0, strrpos($img_with_path,'/')).'/';                      

         
         $alt = ''; //$this->GetImgTitle( $spr, $img);
         $title = ''; //$this->GetImgTitle( $spr, $img);
         //echo '<br>$alt='.$alt.' $title='.$title; 
         //echo '<br>$img_name_new='.$img_name_new;  
         //echo '<br>$img_full_path_new='.$img_full_path_new;
         //echo '<br>$img_src='.$img_src;
         
         if ( !strstr($parameters, 'alt') )  $parameters = $parameters.' alt="'.$alt.'"';
         if ( !strstr($parameters, 'title') ) $parameters = $parameters.' title=" '.$title.' "';
         
         if ( !file_exists($img_full_path_new) ){
             //echo '<br>$uploaddir='.$uploaddir.'<br>$img_full_path_new='.$img_full_path_new;
             @chmod($uploaddir,0777);
             $thumb->save($img_full_path_new);
             @chmod($uploaddir,0755);
             $params = "img=$img&amp;spr=$spr&amp;lang_id=$lang_id&amp;$size&amp;quality=$quality";
             //echo '<br> $params='.$params;
             ?><img src="<?=$img_src;?>" <?=$parameters?>><?
         }
         else {
             ?><img src="<?=$img_src;?>" <?=$parameters?>><?
         }
     }//end else
     return;  
    } // end of function ShowImageByPath()

    
   // ================================================================================================
   // Function : GetFirstValue
   // Version : 1.0.0
   // Date : 25.02.2005
   //
   // Parms : $spr     / name of table, from which will be select data
   //         $lang_id / id of the language
   // Returns :  return array with data of the record from spr
   // Description : Get the array with data of first record from table 
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 25.02.2005
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function GetFirstValue( $spr, $lang_id = NULL )
   {
      if (!$lang_id) $lang_id = $this->lang_id;
      $DB_tmp = &DBs::getInstance();
      
      $q = "SELECT * FROM `".$spr."` WHERE `lang_id`='".$lang_id."' ORDER BY `move` asc LIMIT 1";
      $res = $DB_tmp->db_Query($q);
      //echo '<br>$q='.$q.' $res='.$res.' $DB_tmp->result='.$DB_tmp->result;
      if ( !$res OR !$DB_tmp->result ) return false;
      //$rows = $DB_tmp->db_GetNumRows();
      $row = $DB_tmp->db_FetchAssoc();
      return $row;
   }// end of function GetFirstValue()        
   
   // ================================================================================================
   // Function : GetLastValue
   // Version : 1.0.0
   // Date : 25.02.2005
   //
   // Parms : $spr     / name of table, from which will be select data
   //         $lang_id / id of the language
   // Returns : return array with data of the record from spr
   // Description : Get the array with data of last record from table
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 25.02.2005
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function GetLastValue( $spr, $lang_id = NULL )
   {
      if (!$lang_id) $lang_id = $this->lang_id;
      $DB_tmp = &DBs::getInstance();
      
      $q = "SELECT * FROM `".$spr."` WHERE `lang_id`='".$lang_id."' ORDER BY `move` desc LIMIT 1";
      $res = $DB_tmp->db_Query($q);
      //echo '<br>$q='.$q.' $res='.$res.' $DB_tmp->result='.$DB_tmp->result;
      if ( !$res OR !$DB_tmp->result ) return false;
      //$rows = $DB_tmp->db_GetNumRows();
      $row = $DB_tmp->db_FetchAssoc();
      return $row;
   }// end of function GetLastValue()
   
   // ================================================================================================
   // Function : GetTopLevel
   // Version : 1.0.0
   // Date : 22.05.2008
   //
   // Parms : $spr     / name of table, from which will be select data
   //         $level   / level
   //         $lang_id / id of the language
   // Returns : data of row
   // Description : return upper level of current category 
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 22.05.2008
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function GetTopLevel( $spr, $level, $lang_id=NULL )
   {
       $db = &DBs::getInstance();
       if( empty($lang_id) ) $lang_id = $this->lang_id;
       $q = "SELECT * FROM `".$spr."` WHERE `cod`='".$level."'";
       if( !empty($lang_id) ) $q = $q." AND `lang_id`='".$lang_id."'";
       $q = $q." GROUP BY `cod`";
       $res = $db->db_Query( $q );
       //echo '<br> $q='.$q.' $res='.$res.' $db->result='.$db->result;  
       if( !$res OR !$db->result )return false;
       $row = $db->db_FetchAssoc();      
       return $row;              
   }//end of function GetTopLevel()
       
   // ================================================================================================
   // Function : GetNodeForPosition
   // Version : 1.0.0
   // Date : 22.05.2008
   //
   // Parms : $spr     / name of table, from which will be select data
   //         $level   / level
   //         $lang_id / id of the language
   // Returns : data of row
   // Description : return upper node of current category 
   // ================================================================================================
   // Programmer : Oleg Moragalyuk
   // Date : 18.03.2010
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function GetNodeForPosition( $spr, $level, $lang_id=NULL )
   {
       $db = &DBs::getInstance();
       if( empty($lang_id) ) $lang_id = $this->lang_id;
       $q = "SELECT `node` FROM `".$spr."` WHERE `cod`='".$level."'";
       if( !empty($lang_id) ) $q = $q." AND `lang_id`='".$lang_id."'";
       $q = $q." GROUP BY `cod`";
       $res = $db->db_Query( $q );
//           echo '<br> $q='.$q.' $res='.$res.' $db->result='.$db->result;  
       if( !$res OR !$db->result )return 0;
       $rows = $db->db_GetNumRows();
       if($rows == 0) return 0;
       $row = $db->db_FetchAssoc();
       return ($row['node']+1);              
   }//end of function GetNodeForPosition()
       
   // ================================================================================================
   // Function : GetSubLevelsInStr
   // Version : 1.0.0
   // Date : 22.05.2008
   //
   // Parms : $spr     / name of table, from which will be select data
   //         $level   / level  
   // Returns : return array with data of the record from spr
   // Description : Get the array with data of last record from table
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 22.05.2008
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function GetSubLevelsInStr( $spr, $level, $lang_id=NULL )
   {
      $db = &DBs::getInstance();
      if( empty($lang_id) ) $lang_id = $this->lang_id;    
      if($level!=0)
        {
            $q = "SELECT `node` FROM `".$spr."` WHERE `level`='$level' group by level";
            $res = $db->db_Query( $q );
            if( !$res )return false;  
            $row=$db->db_FetchAssoc();
            $curr_node = $row['node'];
        }
        else
            $curr_node = 0;
        $q = "SELECT MAX(`node`) as max FROM `".$spr."`";
        $res = $db->db_Query( $q );
        if( !$res )return false;  
        $row = $db->db_FetchAssoc();
        $max_node = $row['max'];
        $rows = $max_node - $curr_node; 
     $q='SELECT t0.cod as cod0 ';
     for($i=1; $i<=$rows;$i++)
     {
         $q.=', '.'t'.$i.'.cod as cod'.$i;
     }
     $q.= ' FROM '.$spr.' AS t0 ';
     for($i=1; $i<=$rows;$i++)
     {
         $q.='LEFT JOIN '.$spr.' AS t'.$i.' ON ( t'.$i.'.level = t'.($i-1).'.cod AND t'.$i.'.lang_id = t'.($i-1).'.lang_id) ';
     }
     $q.=' WHERE 1 ';   
     $q = $q." AND t0.level = $level AND t0.lang_id='".$lang_id."' ORDER BY t0.move ";
     $res = $db->db_Query( $q );
//         echo '<br>q='.$q.' res='.$res.' $db->result='.$db->result;
     if( !$res )return false;
     $rows_count = $db->db_GetNumRows();
    //echo '<br> $rows='.$rows;
    $mas = array(); 
    for( $i = 0; $i < $rows_count; $i++ )
    {
        $row=$db->db_FetchAssoc();
        for($j=0;$j<=$rows;$j++){
            if(!isset($mas[$row['cod'.$j]]) && !is_null($row['cod'.$j]))
            {
                $mas[$row['cod'.$j]] = $row['cod'.$j];
            }
        }
    }  
    return implode(",",$mas);
   }
   
   // ================================================================================================
   // Function : GetStructureInArray
   // Version : 1.0.0
   // Date : 22.05.2008
   //
   // Parms : $spr     / name of table, from which will be select data
   //         $level   / level  
   // Returns : return array with data of the record from spr
   // Description : Get the array with data of last record from table
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 22.05.2008
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function GetStructureInArray( $spr, $level_start = 0, $lang_id = NULL, $default_val = NULL, $spacer = NULL, $show_shortname = 1, $show_name = 1, $show_sublevels = 1, $front_back = 'back', $mas=NULL )
   {
      $db = &DBs::getInstance();
      if( empty($lang_id) ) $lang_id = $this->lang_id;    
      if( $show_sublevels==0){
         $rows = 0;
      }
      else
      {
          if($level_start!=0)
            {
                $q = "SELECT `node` FROM `".$spr."` WHERE `level`='$level_start' group by level";
                $res = $db->db_Query( $q );
                if( !$res )return false;  
                $row=$db->db_FetchAssoc();
                $curr_node = $row['node'];
            }
            else
                $curr_node = 0;
            $q = "SELECT MAX(`node`) as max FROM `".$spr."`";
            $res = $db->db_Query( $q );
            if( !$res )return false;  
            $row = $db->db_FetchAssoc();
            $max_node = $row['max'];
            $rows = $max_node - $curr_node; 
      }
     $q='SELECT t0.node as node0, t0.cod as cod0 ';
     if ($show_shortname==1) $q.=', t0.short as short0';
     if ($show_name==1) $q.=', t0.name as name0';
     for($i=1; $i<=$rows;$i++)
     {
         $q.=', '.'t'.$i.'.node as node'.$i.', '.'t'.$i.'.cod as cod'.$i;
         if ($show_shortname==1) $q.=', t'.$i.'.short as short'.$i;
         if ($show_name==1) $q.=', t'.$i.'.name as name'.$i;
     }
     $q.= ' FROM '.$spr.' AS t0 ';
     for($i=1; $i<=$rows;$i++)
     {
         $q.='LEFT JOIN '.$spr.' AS t'.$i.' ON ( t'.$i.'.level = t'.($i-1).'.cod AND t'.$i.'.lang_id = t'.($i-1).'.lang_id) ';
     }
     $q.=' WHERE 1 ';   
     $q = $q." AND t0.level = '".$level_start."' AND t0.lang_id='".$lang_id."' ORDER BY t0.move ";
     $res = $db->db_Query( $q );
     //echo '<br>q='.$q.' res='.$res.' $db->result='.$db->result;
     if( !$res )return false;
     $rows_count = $db->db_GetNumRows();
     //echo '<br> $rows='.$rows;
    $mas[''] = $default_val; 
    for( $i = 0; $i < $rows_count; $i++ )
    {
        $row=$db->db_FetchAssoc();
        for($j=0;$j<=$rows;$j++){
            if(!isset($mas[$row['cod'.$j]]) && !is_null($row['cod'.$j]))
            {
                $output_str = $spacer;
                for($k=1;$k<=$row['node'.$j];$k++){
                    $output_str.= $spacer;
                }
                if( $show_shortname ) $output_str = $output_str.' '.stripslashes($row['short'.$j]);
                if( $show_name ) $output_str = $output_str.' '.stripslashes($row['name'.$j]);
                
                $mas[$row['cod'.$j]] = $output_str;
            }
        }
    }  
    return $mas;           
 }// end of function GetStructureInArray()
   
   // ================================================================================================
   // Function : IsSubLevels()
   // Version : 1.0.0
   // Date : 22.05.2008
   // Parms :   $spr - name of the table
   //           $level - level
   // Returns : true,false / Void
   // Description : check exist or not sub levels of current category
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 22.05.2008
   // Reason for change : Reason Description / Creation
   // Change Request Nbr:
   // ================================================================================================
   function IsSubLevels( $spr, $level )
   {
       $db = &DBs::getInstance();
       $q = "SELECT `cod` FROM `".$spr."` WHERE `level`='".$level."' GROUP BY `cod`";
       $res = $db->db_Query( $q );
       //echo '<br> $q='.$q.' $res='.$res.' $db->result='.$db->result;
       if( !$res OR !$db->result ) {return false;}
       $rows = $db->db_GetNumRows();
       //echo '<br>$rows='.$rows;
       return $rows;
   } //end of function IsSubLevels()
   
    // ================================================================================================
    // Function : GetMulti()
    // Date : 23.12.2010
    // Parms : $table
    // Returns :     $arr / false
    // Description :  retutn array with all multilangues for $table
    // Programmer :  Yaroslav Gyryn
    // ================================================================================================
    function GetMulti($table)
    {
       $dbr = &DBs::getInstance();
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
       $arr = array(); 
       for( $i = 0; $i < $rows; $i++ ){
           $row=$dbr->db_FetchAssoc();
           $arr[$row['cod']] = $row['name'];
       }
       return $arr;                      
    } // End of function GetMulti()   
        
   // ================================================================================================
   // Function : GetNameArr
   // Version : 1.0.0
   // Date : 06.03.2005
   //
   // Parms : $table_for_show   / name of the table for save
   //         $id               / code of the description
   // Returns : array $res[lang_id]=name - description on lang_id language.
   // Description : Return the array description_arr on different languages
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 06.03.2005
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
    function GetNameArr( $table_for_show, $id )
    {
      $ln_sys = &check_init('LangSys','SysLang'); 

      $description_arr='';

      $ln_arr = $ln_sys->LangArray( _LANG_ID );
      while( $el = each( $ln_arr ) )
      {
         $lang_id = $el['key'];
         $row = $this->GetByCod( $table_for_show, $id, $lang_id );
         $description_arr[$lang_id]=$row[$lang_id];
      }
      return $description_arr;
    }
   // ================================================================================================
   // Function : SaveNameArr
   // Version : 1.0.0
   // Date : 06.03.2005
   //
   // Parms : $id               / code of the description
   //         $description_arr  / array with desctiptions on different languages
   //         $table_for_save   / name of the table for save
   // Returns : array $res[lang_id]=name - description on lang_id language.
   // Description : Save Description on different languages to the table $table_for_save
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 06.03.2005
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
    function SaveNameArr( $id, $description_arr, $table_for_save, $short_arr=NULL, $img_arr=NULL  )
    {
       $ln_sys = &check_init('LangSys','SysLang'); 
        //print_r($description_arr);
       $ln_arr = $ln_sys->LangArray( _LANG_ID );
       while( $el = each( $ln_arr ) )
       {
           $lang_id = $el['key']; 
           if( !isset($description_arr[ $lang_id ])) continue;
           $description = $this->Form->GetRequestTxtData($description_arr[ $lang_id ]);
           //if (empty($description)) continue;
           $short = $this->Form->GetRequestTxtData($short_arr[ $lang_id ]);
           if( isset($img_arr[ $lang_id ]) ) $img = $this->Form->GetRequestTxtData($img_arr[ $lang_id ]);
           else $img = NULL;

           $res = $this->SaveToSpr( $table_for_save, $id, $lang_id, $description, $short, $img  );
           //echo '<br> $table_for_save='.$table_for_save.' $id='.$id.' $lang_id='.$lang_id.' $description='.$description.' res='.$res;
           if( !$res ) return false;
       } //--- end while
       return true;
    } // end of function SaveNameArr()

   // ================================================================================================
   // Function : SaveToSpr
   // Version : 1.0.0
   // Date : 25.02.2005
   //
   // Parms : $spr     / name of table, from which will be select data
   //         $cod     / cod of the record
   //         $lang_id / id of the language
   //         $name    / name
   //         $short   / short name
   //         $img     / image name
   // Returns : array $res[lang_id]=name - description on lang_id language.
   // Description : Get the name from table by its cod and language. If lang_id isn't set, it will be
   //               return all of the record with such cod.
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 25.02.2005
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function SaveToSpr( $spr, $cod, $lang_id, $name, $short=NULL, $img=NULL ){
      $q = "SELECT * FROM `".$spr."` WHERE `cod`='".$cod."' AND `lang_id`='".$lang_id."'";
      $res = $this->Rights->db_Query($q);
      //echo '<br>00 $q='.$q.' $res='.$res.' $$this->Rights->result='.$this->Rights->result;
      if ( !$this->Rights->result ) return false;
      $row = $this->Rights->db_FetchAssoc();
      $rows = $this->Rights->db_GetNumRows();
      //echo '<br> $row='.print_r($row);
      //echo '<br> $rows='.$rows;
      
      $tmp_db = DBs::getInstance();
      $q = "SELECT * FROM `".$spr."` WHERE 1 LIMIT 1";
      $res = $tmp_db->db_Query($q);
      //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
      if ( !$res OR !$tmp_db->result ) return false;
      $fields_col = mysql_num_fields($tmp_db->result);
      
      if( $rows>0 )   //--- update
      {
          $q="UPDATE `".$spr."` SET 
              `cod`='".$cod."',
              `lang_id`='".$lang_id."',
              `name`='".$name."'";
          if( isset($row['short'])) $q .= ", `short`='".$short."'";
          if( isset($row['img'])) $q .= ", `img`='".$img."'";
          $q .= " WHERE `cod`='".$cod."' AND `lang_id`='".$lang_id."'";
          $res = $this->Rights->db_Query($q);
          //echo '<br>$q='.$q.' $res='.$res.' $$this->Rights->result='.$this->Rights->result;
          if( !$this->Rights->result ) return false;
      }
      else          //--- insert
      {
          $q = "INSERT INTO `".$spr."` SET 
                `cod`='".$cod."',
                `lang_id`= '".$lang_id."',
                `name`='".$name."'
               ";
          if( isset($row['short'])) $q .= ", `short`='".$short."'";
          if( isset($row['img'])) $q .= ", `img`='".$img."'";
          $res = $this->Rights->db_Query($q);
          //echo '<br>$q='.$q.' $res='.$res.' $$this->Rights->result='.$this->Rights->result;
          if( !$this->Rights->result ) return false;
         
          if( isset($row['move']) ){       
            $q="SELECT MAX(`move`) AS maxx FROM `".$spr."` WHERE `lang_id`='"._LANG_ID."'";
            $res = $this->Rights->db_Query( $q );
            //$rows = $this->Rights->db_GetNumRows();
            $my = $this->Rights->db_FetchAssoc();
            $maxx=$my['maxx']+1;  //add link with position auto_incremental

            $q="UPDATE `".$spr."` SET `move`='".$maxx."' WHERE `cod`='".$cod."'";
            $res = $this->Rights->db_Query( $q );
            //echo '<br>333 $q='.$q.' $res='.$res.' $this->Rights->result='.$this->Rights->result;
            if( !$res OR !$this->Rights->result ) return false; 
          }               
      }
      return true;
   }   
   
   // ================================================================================================
   // Function : DelFromSpr
   // Version : 1.0.0
   // Date : 25.02.2005
   //
   // Parms :         $spr      / name of table, from which will be select data
   //                 $cod      / cod of the record
   //                 $lang_id  / id of the language
   // Returns : true,false / Void
   // Description :  Remove data from the table
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 25.02.2005
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function DelFromSpr( $spr, $cod, $lang_id = NULL ){
      $q = "DELETE FROM `".$spr."` WHERE cod='".$cod."'";
      if( !empty($lang_id) ) $q = $q." AND lang_id='".$lang_id."'";
      $res = $this->Rights->db_Query($q);
      //echo '<br>$q='.$q.' $res='.$res.' $this->Rights->result='.$this->Rights->result;
      if ( !$this->Rights->result ) return false;
      return true;
   }            
       
 }  //end of class SystemSpr