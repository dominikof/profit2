<?php
// ================================================================================================
// System : SEOCMS
// Module : sysTags.class.php
// Version : 1.0.0
// Date : 06.08.2008
// Licensed To:
// Igor Trokhymchuk ihoru@mail.ru
// Andriy Lykhodid las_zt@mail.ru
//
// Purpose : Class definition for system functions with tags for all modules
//
// ================================================================================================

include_once( SITE_PATH.'/admin/include/defines.inc.php' );  

// ================================================================================================
//    Class             : SystemTags
//    Version           : 1.0.0
//    Date              : 06.08.2008
//
//    Constructor       : Yes
//    Parms             : session_id / session id
//                        user_id    / UserID

//    Returns           : None
//    Description       : Class definition for system functions with tags for all modules 
// ================================================================================================
//    Programmer        :  Igor Trokhymchuk
//    Date              :  06.08.2008
//    Reason for change :  Creation
//    Change Request Nbr:  N/A
// ================================================================================================
 class SystemTags {

   var $user_id = NULL;
   var $module = NULL;
   var $lang_id = NULL;
   var $Spr = NULL;

   // ================================================================================================
   //    Function          : SystemTags (Constructor)
   //    Version           : 1.0.0
   //    Date              : 06.08.2008
   //    Parms             : usre_id   / User ID
   //                        module    / module ID
   //    Returns           : Error Indicator
   //
   //    Description       : Opens and selects a dabase
   // ================================================================================================
   function SystemTags($user_id=NULL, $module=NULL) {
            //Check if Constants are overrulled
            ( $user_id   !="" ? $this->user_id = $user_id  : $this->user_id = NULL );
            ( $module   !="" ? $this->module  = $module   : $this->module  = NULL );

            if ( defined("_LANG_ID") ) $this->lang_id = _LANG_ID;
                         
            if (empty($this->Rights)) $this->Rights = &check_init('Rights', 'Rights', $this->user_id,$this->module);
            if (empty($this->Msg)) $this->Msg = &check_init('ShowMsg', 'ShowMsg');
            if (empty($this->Spr)) $this->Spr = &check_init('SysSpr', 'SysSpr', $this->user_id,$this->module);
            if (empty($this->Form)) $this->Form = &check_init('FormTags', 'Form', '"mod_tags"');
   } // End of SystemTags Constructor


   // ================================================================================================
   // Function : AddTbl()
   // Version : 1.0.0
   // Date : 06.08.2008
   //
   // Parms :   
   // Returns :      true,false / Void
   // Description :  Add tables
   // ================================================================================================
   // Programmer :  Igor Trokhymchuk
   // Date : 06.08.2008
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function AddTbl()
   {
      $tmp_db = DBs::getInstance();

      if( defined("DB_TABLE_CHARSET")) $this->tbl_charset = DB_TABLE_CHARSET;
      else $this->tbl_charset = 'utf8';
       
       // create table for strore individual name of category
       if ( !$tmp_db->IsTableExist(TblSysModTags) ) {
           $q = "
            CREATE TABLE `".TblSysModTags."` (
            `id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
            `id_tag` INT( 11 ) UNSIGNED NOT NULL ,
            `id_module` INT( 11 ) UNSIGNED NOT NULL ,
            `id_item` INT( 11 ) UNSIGNED NOT NULL ,
            PRIMARY KEY ( `id` ) ,
            INDEX ( `id_tag` , `id_module` , `id_item` )
            ) ENGINE=MyISAM DEFAULT CHARSET=".$this->tbl_charset.";
            ";
           $res = $tmp_db->db_Query( $q );
           //echo '<br>$q='.$q.' $res='.$res;
           if( !$res )return false;
       }     

   }     
   
   // ================================================================================================
   // Function : GetTagsByModuleAndItem()
   // Version : 1.0.0
   // Date : 06.08.2008
   // Parms :   $id_module - id of module
   //           $id_item - id of item position in module $id_module
   // Returns : true,false / Void
   // Description : get tags by id module and id of position
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 06.08.2008
   // Reason for change : Reason Description / Creation
   // Change Request Nbr:
   // ================================================================================================
   function GetTagsByModuleAndItem( $id_module=NULL, $id_item=NULL )
   {
       $db = DBs::getInstance();
       $q = "SELECT `id_tag` FROM `".TblSysModTags."` WHERE `id_module`='".$id_module."' AND `id_item`='".$id_item."'";
       $res = $db->db_Query( $q );
       //echo '<br> $q='.$q.' $res='.$res.' $db->result='.$db->result;
       if( !$res OR !$db->result ) {return false;}
       $rows = $db->db_GetNumRows();
       //echo '<br>$rows='.$rows;
       $arr = array();
       for($i=0;$i<$rows;$i++){
            $row = $db->db_FetchAssoc();
            $arr[$i] =  $row['id_tag'];
       }
       return $arr;
   } //end of function GetTagsByModuleAndItem()

   // ================================================================================================
   // Function : SaveTagsById()
   // Version : 1.0.0
   // Date : 07.08.2008
   // Parms :   $id_module - id of module
   //           $id_item - id of item position in module $id_module
   //           $arr - array with values  
   // Returns : true,false / Void
   // Description : save tags by id module and id of position
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 07.08.2008
   // Reason for change : Reason Description / Creation
   // Change Request Nbr:
   // ================================================================================================
   function SaveTagsById( $id_module, $id_item, $arr )
   {
       $db = DBs::getInstance();
       $q = "DELETE FROM `".TblSysModTags."` WHERE `id_module`='".$id_module."' AND `id_item`='".$id_item."'";
       $res = $db->db_Query( $q );
       //echo '<br> $q='.$q.' $res='.$res.' $db->result='.$db->result;
       if( !$res OR !$db->result ) {return false;} 
       for($i=0;$i<count($arr);$i++){
           $q = "INSERT INTO `".TblSysModTags."` SET 
                 `id_tag`='".$arr[$i]."',
                 `id_module`='".$id_module."',
                 `id_item`='".$id_item."'";
           $res = $db->db_Query( $q );
           //echo '<br> $q='.$q.' $res='.$res.' $db->result='.$db->result;
           if( !$res OR !$db->result ) {return false;}
       }
       return true;
   } //end of function SaveTagsById()

   // ================================================================================================
   // Function : GetInfoByIdTag()
   // Version : 1.0.0
   // Date : 06.08.2008
   // Parms :   $id_tag - id of the tag
   // Returns : true,false / Void
   // Description : get tags by id module and id of position
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 06.08.2008
   // Reason for change : Reason Description / Creation
   // Change Request Nbr:
   // ================================================================================================
   function GetInfoByIdTag( $id_tag=NULL )
   {
       $db = DBs::getInstance();
       $q = "SELECT * FROM `".TblSysModTags."` WHERE `id_tag`='".$id_tag."'";
       $res = $db->db_Query( $q );
       //echo '<br> $q='.$q.' $res='.$res.' $db->result='.$db->result;
       if( !$res OR !$db->result ) {return false;}
       $rows = $db->db_GetNumRows();
       //echo '<br>$rows='.$rows;
       $arr = array();
       for($i=0;$i<$rows;$i++){
            $row = $db->db_FetchAssoc();
            $arr[$row['id_module']][$row['id_item']] =  $row['id'];
       }
       return $arr;
   } //end of function GetInfoByIdTag()   

   // ================================================================================================
   // Function : GetSimilarItems()
   // Version : 1.0.0
   // Date : 06.08.2008
   // Parms :   $id_module - id of the module
   //           $id_item - id of the position
   // Returns : true,false / Void
   // Description : return similar items for $id_item in multy-array 
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 06.08.2008
   // Reason for change : Reason Description / Creation
   // Change Request Nbr:
   // ================================================================================================
   function GetSimilarItems( $id_module, $id_item )
   {
       $db = DBs::getInstance();

       $q = "SELECT `id`, `id_tag` FROM `".TblSysModTags."` WHERE `id_module`='".$id_module."' AND `id_item`='".$id_item."'";
       $res = $db->db_Query( $q );
       //echo '<br> $q='.$q.' $res='.$res.' $db->result='.$db->result;
       if( !$res OR !$db->result ) {return false;}
       $rows = $db->db_GetNumRows();
       //echo '<br>$rows='.$rows;
       $tags_str = NULL;
       $id_str = NULL;
       for($i=0;$i<$rows;$i++){
            $row = $db->db_FetchAssoc();
            if( empty($tags_str) ) $tags_str =  $row['id_tag'];
            else $tags_str = $tags_str.','.$row['id_tag'];
            if( empty($id_str) ) $id_str =  $row['id'];
            else $id_str = $id_str.','.$row['id'];
            
       }
       $arr = array();
       if( !empty($tags_str) ){     
           $q = "SELECT * FROM `".TblSysModTags."` WHERE `id_tag` IN(".$tags_str.") AND `id` NOT IN(".$id_str.")";
           $res = $db->db_Query( $q );
           //echo '<br> $q='.$q.' $res='.$res.' $db->result='.$db->result;
           if( !$res OR !$db->result ) {return false;}
           $rows = $db->db_GetNumRows();
           //echo '<br>$rows='.$rows;
           $arr = array();
           for($i=0;$i<$rows;$i++){
                $row = $db->db_FetchAssoc();
                //$arr[$i] =  $row['id'];
                $arr[$row['id_module']][$row['id_item']] =  $row['id'];
           }
       }
       //print_r($arr);
       return $arr;
   } //end of function GetSimilarItems() 

   // ================================================================================================
   // Function : GetCloudOfTags()
   // Version : 1.0.0
   // Date : 07.08.2008
   // Parms :  
   // Returns : true,false / Void
   // Description : get cloud of tags in array
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 07.08.2008
   // Reason for change : Reason Description / Creation
   // Change Request Nbr:
   // ================================================================================================
   function GetCloudOfTags()
   {
       $db = DBs::getInstance();
       $q = "SELECT `id_tag`, COUNT(`id_tag`) as cnt FROM `".TblSysModTags."` WHERE 1 GROUP BY `id_tag`";
       $res = $db->db_Query( $q );
       //echo '<br> $q='.$q.' $res='.$res.' $db->result='.$db->result;
       if( !$res OR !$db->result ) {return false;}
       $rows = $db->db_GetNumRows();
       //echo '<br>$rows='.$rows;
       $arr = array();
       for($i=0;$i<$rows;$i++){
            $row = $db->db_FetchAssoc();
            $arr[$row['id_tag']] =  $row['cnt'];
       }
       return $arr;
   } //end of function GetCloudOfTags()  

   // ================================================================================================
   // Function : ShowEditTags()
   // Version : 1.0.0
   // Date : 08.08.2008
   // Parms :  
   // Returns : true,false / Void
   // Description : show form for edit tags on backend
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 08.08.2008
   // Reason for change : Reason Description / Creation
   // Change Request Nbr:
   // ================================================================================================
   function ShowEditTags($id_tag)
   {
       ?>
       <table border="0" cellpadding="0" cellspacing="0" width="100%" class="EditTable">
        <tr>
         <td valign="top" width="100px">
          <div style="background-color:#E0E0E0; padding: 4px;"><b><?=$this->Msg->show_text('FLD_TAGS')?>:</b></div>
          <?
          $ModulesPlug = new ModulesPlug();
          $id_module = $ModulesPlug->GetModuleIdByPath ( '/admin/modules/sys_spr/sys_spr.php?spr=sys_modules_tags_spr_name&uselevels=1&useshort=0&useimg=0' );
          //$url = '/admin/modules/sys_spr/sys_spr.php?spr=sys_modules_tags_spr_name&uselevels=1&module='.$this->module.'&task=add_new_tags';
          $url = '/admin/index.php?module='.$id_module.'&task=new'; 
          $params = "OnClick='window.open(\"".$url."\", \"\", \"width=1024, height=600, status=0, toolbar=0, location=0, menubar=0, resizable=1, scrollbars=1\"); return false;'";
          ?>
          <?/*<a href="#" onclick="AddNewTag('<?=$url?>','tag-categories-all'); return false;"><?=$this->Msg->show_text('_TXT_ADD_TAGS', TblSysTxt)?></a>*/?>
          <br><a href="" <?=$params?>><?=$this->Msg->show_text('_TXT_ADD_TAGS')?></a>
         </td>
         <td valign="top"><div id="tag-categories-all" class="ui-tabs-panel"><?$this->Spr->ShowInCheckBox( TblSysModTagsSprName, 'id_tag', 1, $id_tag, "left", NULL, 'move', 'asc', 1, 0 );?><div></td>
        </tr>
       </table>
       
        <script language="JavaScript"> 
         function AddNewTag(url, div_id){
              //nameform = '<?=$this->Form->name?>'; 
              //document.<?=$this->Form->name?>.task.value=$task;
              //document.<?=$this->Form->name?>.name.value=document.getElementById('name').value;
              //alert('nameform='+nameform);
              //alert('task='+document.<?=$this->Form->name?>.task.value);
              //alert('name='+document.<?=$this->Form->name?>.name.value);
              JsHttpRequest.query(
                    url, // backend
                    {
                        // pass a text value
                        //'str': document.getElementById("mystr").value,
                        // path a file to be uploaded
                        //'q': document.getElementById('<?=$this->Form->name?>')
                    },
                    // Function is called when an answer arrives.
                    function(result, errors) {
                        //alert('result='+result);
                        // Write errors to the debug div.
                        document.getElementById(div_id).innerHTML = errors;
                        // Write the answer.
                        if (result) {
                            document.getElementById("result").innerHTML = result;
                        }
                    },
                    false,  // do not disable caching
                    div_id, //id div for show result content
                    div_id, //id div for loader
                    //layout for loader
                    '<div style="border:0px solid #000000; padding-top:0px; padding-bottom:0px; text-align:left;" align="center"><img src="/admin/images/icons/loading_animation_liferay.gif"></div>'
              );
         }
        </script>       
       <?       
   } //end of function GetCloudOfTags()   

   // ================================================================================================
   // Function : DelTagsByModuleItem()
   // Version : 1.0.0
   // Date : 09.10.2008
   // Parms :   $id_module  / id of the module
   //           $id_item    / id of the item position
   // Returns : true,false / Void
   // Description : delete relative between item position and tags (delete tags from item position $id_item)
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 09.10.2008
   // Reason for change : Reason Description / Creation
   // Change Request Nbr:
   // ================================================================================================
   function DelTagsByModuleItem($id_module, $id_item)
   {
       $db = new DB();
       $q = "DELETE FROM `".TblSysModTags."` WHERE `id_module`='".$id_module."' AND `id_item`='".$id_item."'";
       $res = $db->db_Query( $q );
       //echo '<br> $q='.$q.' $res='.$res.' $db->result='.$db->result;
       if( !$res OR !$db->result ) {return false;}
       return true;
   } //end of function DelTagsByModuleItem()
       
 }  //end of class SystemTags  