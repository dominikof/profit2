<?php
include_once( SITE_PATH.'/admin/include/defines.inc.php' ); 

/**
* Class SysSettingsAdm
* Class definition for all actions with system settings on the back-end
* @package System Package of SEOCMS
* @author Igor Trokhymchuk  <ihor@seotm.com>
* @version 1.1, 02.04.2012
* @copyright (c) 2005+ by SEOTM
*/
class SysSettingsAdm extends SysSettings {

       /**
       * SysSettingsAdm::__construct()
       * 
       * @param integer $user_id
       * @param integer $module_id
       * @param integer $display
       * @param string $sort
       * @param integer $start
       * @param integer $width
       * @param integer $spr
       * @return void
       */
       function __construct($user_id=NULL, $module=NULL, $display=NULL, $sort=NULL, $start=NULL, $width = NULL, $spr=NULL) {
                //Check if Constants are overrulled
                ( $user_id   !="" ? $this->user_id = $user_id  : $this->user_id = NULL );
                ( $module   !="" ? $this->module  = $module   : $this->module  = NULL );
                ( $display  !="" ? $this->display = $display  : $this->display = 20   );
                ( $sort     !="" ? $this->sort    = $sort     : $this->sort    = NULL );
                ( $start    !="" ? $this->start   = $start    : $this->start   = 0    );
                ( $width    !="" ? $this->width   = $width    : $this->width   = 750  );
                ( $spr      !="" ? $this->spr     = $spr      : $this->spr     = NULL  );

                if ( defined("_LANG_ID") ) $this->lang_id = _LANG_ID;
                
                if (empty($this->db)) $this->db = DBs::getInstance();
                if (empty($this->Right)) $this->Right = new Rights($this->user_id, $this->module);
                if (empty($this->Msg)) $this->Msg = &check_init_txt('TblBackMulti',TblBackMulti);
                if (empty($this->Form)) $this->Form = new Form('form_sys_set');
                if (empty($this->Spr)) $this->Spr = new SysSpr($this->user_id, $this->module);
                
       } // End of SysSettingsAdm Constructor

  // ================================================================================================
  //    Function          : Show()
  //    Version           : 1.0.0
  //    Date              : 14.03.2005
  //    Parms             :
  //    Returns           : true/false
  //    Description       : Show Statistic Log
  // ================================================================================================

  function Show($res = '' )
  {
   //$this->AddTbl();
   $maildata= $this->GetMailTxtData();  
   $Panel = new Panel();
   $script = 'module='.$this->module;
   $script = $_SERVER['PHP_SELF']."?$script";
   $q = "SELECT * FROM ".TblSysSetGlobal." WHERE `id`='1'";
   $res = $this->Right->Query( $q, $this->user_id, $this->module );
   if( !$res ) return false;
   $mas = $this->Right->db_FetchAssoc();
   $txt = $this->Msg['TXT_EDIT'];
   AdminHTML::PanelSubH( $txt );
   // Write Form Header
   $this->Form->WriteHeader( $script );
   /*========================== PAGE 1 START ================================*/  
   $Panel->WritePanelHead( "SubPanel_" );
   $Panel->WriteItemHeader( $this->Msg['SYS_SET_MAIL'] );
   // Write Simple Panel
   AdminHTML::PanelSimpleH();

   $this->Form->Hidden( 'id', $mas['id'] );
  
   ?>
   <table class="ContentTable" border="0" width="100%">
   <?/*
    <tr>
     <td><?=$this->Msg['FLD_ID'];?></td>
     <td><?=$mas['id']; ?></td>
    </tr>
    */?>
    <tr class="tr1">
     <td align="left" height="25"><?=$this->Msg['SYS_SET_MAIL_MAILER'];?>:</td>
     <?( !empty($this->Err) ? $val = $this->mail_mailer : ( !empty($mas['mail_mailer']) ? $val = $mas['mail_mailer'] : $val = 'smtp' ) );?>
     <td align="left"><?=$this->Form->TextBox( 'mail_mailer', stripslashes($val), 30 );?></td>
     <td align="left"><?=$this->Msg['SYS_SET_MAIL_MAILER_HELP'];?></td>
    </tr>
    <tr class="tr2">
     <td align="left" width="150" height="25"><?=$this->Msg['SYS_SET_MAIL_HOST'];?>:</td>
     <?( !empty($this->Err) ? $val = $this->mail_host : $val = $mas['mail_host'] );?>
     <td align="left"><?=$this->Form->TextBox( 'mail_host', stripslashes($val), 30 );?></td>
     <td align="left"><?=$this->Msg['SYS_SET_MAIL_HOST_HELP'];?></td>
    </tr>
    <tr class="tr1">
     <td align="left" height="25"><?=$this->Msg['SYS_SET_MAIL_PORT'];?>:</td>
     <?( !empty($this->Err) ? $val = $this->mail_port : ( !empty($mas['mail_port']) ? $val = $mas['mail_port'] : $val = 25 ) );?>
     <td align="left"><?=$this->Form->TextBox( 'mail_port', stripslashes($val), 3 );?></td>
     <td align="left"></td>
    </tr>
    <tr class="tr2">
     <td align="left" height="25"><?=$this->Msg['SYS_SET_MAIL_SMTP_AUTH'];?>:</td>
     <?( !empty($this->Err) ? $val = $this->mail_smtp_auth : $val = $mas['mail_smtp_auth'] );?>
     <td align="left"><?=$this->Form->CheckBox( "mail_smtp_auth", '1', $val );?></td>
     <td align="left"></td>
    </tr>
    <tr class="tr1">
     <td align="left" height="25"><?=$this->Msg['SYS_SET_MAIL_USERNAME'];?>:</td>
     <?( !empty($this->Err) ? $val = $this->mail_username : $val = $mas['mail_username'] );?>
     <td align="left"><?=$this->Form->TextBox( 'mail_username', stripslashes($val), 30 );?></td>
     <td align="left"></td>
    </tr>
    <tr class="tr2">
     <td align="left" height="25"><?=$this->Msg['SYS_SET_MAIL_PASSWORD'];?>:</td>
     <?( !empty($this->Err) ? $val = $this->mail_password : $val = $mas['mail_password'] );?>
     <td align="left"><?=$this->Form->TextBox( 'mail_password', stripslashes($val), 30 );?></td>
     <td align="left"></td>
    </tr>
    <tr class="tr1">
     <td align="left" height="25"><?=$this->Msg['SYS_SET_MAIL_FROM'];?>:</td>
     <?( !empty($this->Err) ? $val = $this->mail_from : $val = $mas['mail_from'] );?>
     <td align="left"><?=$this->Form->TextBox( 'mail_from', stripslashes($val), 30 );?></td>
     <td align="left"></td>
    </tr>
    <tr class="tr1">
     <td align="left" colspan="3">
      <?
      $Panel->WritePanelHead( 'mail_data' );
      $ln_arr = SysLang::$LangArray;
      while( $el = each( $ln_arr ) ){
        $lang_id = $el['key'];
        $lang = $el['value'];
        $mas_s[$lang_id] = $lang;
        $Panel->WriteItemHeader( $lang );
        ?>
        <table border="0" class="EditTable">
         <tr>
          <td><b><?=$this->Msg['SYS_SET_MAIL_FROM_NAME'];?>:</b></td>
         </tr>
         <tr>
          <td>
           <?
           ( !empty($this->Err) ? $val = $this->mail_from_name[$lang_id] : $val = $maildata[$lang_id]['from'] );
           $this->Form->TextBox( 'mail_from_name['.$lang_id.']', $val, 80 );
           ?>
          </td>
         </tr>
         <tr>
         <tr>
          <td><b><?=$this->Msg['SYS_SET_MAIL_HEADER'];?>:</b></td>
         </tr>
         <tr>
          <td>
           <?
           ( !empty($this->Err) ? $val = $this->mail_header[$lang_id] : $val = $maildata[$lang_id]['head'] );
           $this->Form->HTMLTextArea( 'mail_header['.$lang_id.']', $val, 7, 80 );
           ?>
          </td>
         </tr>
         <tr>
          <td><b><?=$this->Msg['SYS_SET_MAIL_FOOTER'];?>:</b></td>
         </tr>
         <tr>
          <td>
           <?
           ( !empty($this->Err) ? $val = $this->mail_footer[$lang_id] : $val = $maildata[$lang_id]['foot'] );
           $this->Form->HTMLTextArea( 'mail_footer['.$lang_id.']', $val, 7, 80 );
           ?>
          </td>
         </tr>
        </table>
        <?
        $Panel->WriteItemFooter();
      }
      $Panel->WritePanelFooter();
      ?>
     </td>
    </tr>
    <tr class="tr1">
     <td align="left" height="25"><?=$this->Msg['SYS_SET_MAIL_WORD_WRAP'];?>:</td>
     <?( !empty($this->Err) ? $val = $this->mail_word_wrap : ( !empty($mas['mail_word_wrap']) ? $val = $mas['mail_word_wrap'] : $val = 50 ) );?>
     <td align="left"><?=$this->Form->TextBox( 'mail_word_wrap', stripslashes($val), 3 );?></td>
     <td align="left"><?=$this->Msg['SYS_SET_MAIL_WORD_WRAP_HELP'];?></td>
    </tr>
    <tr class="tr2">
     <td align="left" height="25"><?=$this->Msg['SYS_SET_MAIL_IS_HTML'];?>:</td>
     <?( !empty($this->Err) ? $val = $this->mail_is_html : $val = $mas['mail_is_html'] );?>
     <td align="left"><?=$this->Form->CheckBox( "mail_is_html", '1', $val );?></td>
     <td align="left"></td>
    </tr>    
    <tr class="tr1">
     <td align="left" height="25"><?=$this->Msg['SYS_SET_MAIL_PRIORITY'];?>:</td>
     <?( !empty($this->Err) ? $val = $this->mail_priority : ( !empty($mas['mail_priority']) ? $val = $mas['mail_priority'] : $val = 3 ) );?>
     <td align="left"><?=$this->Form->TextBox( 'mail_priority', stripslashes($val), 3 );?></td>
     <td align="left"><?=$this->Msg['SYS_SET_MAIL_PRIORITY_HELP'];?></td>
    </tr>    
    <tr class="tr2">
     <td align="left" height="25"><?=$this->Msg['SYS_SET_MAIL_CHARSET'];?>:</td>
     <?( !empty($this->Err) ? $val = $this->mail_charset : ( !empty($mas['mail_charset']) ? $val = $mas['mail_charset'] : $val = 'utf-8' ) );?>
     <td align="left"><?=$this->Form->TextBox( 'mail_charset', stripslashes($val), 30 );?></td>
     <td align="left"></td>
    </tr>
    <tr class="tr1">
     <td align="left" height="25"><?=$this->Msg['SYS_SET_MAIL_ENCODING'];?>:</td>
     <?( !empty($this->Err) ? $val = $this->mail_encoding : ( !empty($mas['mail_encoding']) ? $val = $mas['mail_encoding'] : $val = '8bit' ) );?>
     <td align="left"><?=$this->Form->TextBox( 'mail_encoding', stripslashes($val), 30 );?></td>
     <td align="left"><?=$this->Msg['SYS_SET_MAIL_ENCODING_HELP'];?></td>
    </tr>
    <tr class="tr2">
     <td align="left" height="25"><?=$this->Msg['SYS_SET_AUTO_EMAILS'];?></td>
     <?( !empty($this->Err) ? $val = $this->mail_auto_emails : $val = $mas['mail_auto_emails'] );?>
     <td align="left"><?=$this->Form->Textarea( 'mail_auto_emails', stripslashes($val), 3, 30 );?></td>
     <td align="left"><?=$this->Msg['SYS_SET_AUTO_EMAILS_HELP'];?></td> 
    </tr>
    <tr class="tr1">
     <td align="left" height="25"><?=$this->Msg['SYS_SET_ADMIN_EMAIL'];?>:</td>
     <?( !empty($this->Err) ? $val = $this->mail_admin_email : $val = $mas['mail_admin_email'] );?>
     <td align="left"><?=$this->Form->TextBox( 'mail_admin_email', stripslashes($val), 30 );?></td>
     <td align="left"></td>
    </tr>
   </table>
   <?
   AdminHTML::PanelSimpleF();
   
   $Panel->WriteItemFooter();
   
   //$Panel->WritePanelHead( "SubPanel_" );
   $Panel->WriteItemHeader( $this->Msg['SYS_SET_REDACTORS'] );
   ?>
    <table class="ContentTable" border="0" width="100%">
      <tr class="tr1">
         <td align="left" width="150" height="25"><?=$this->Msg['SYS_SET_EDITER_SELECT'];?>:</td>
         <td align="left"><select name="editer">
                 <option <?if($mas['editer']=="TinyMCE") echo "selected "?> value="TinyMCE">TinyMCE</option>
                 <option <?if($mas['editer']=="FCK") echo "selected "?> value="FCK">FCK</option>                 
                 <option <?if($mas['editer']=="elrte") echo "selected "?> value="elrte">elrte</option>
             </select></td>
      </tr>
     
    </table>
   
   <?
   $Panel->WriteItemFooter();
   $Panel->WritePanelFooter();
   /*========================== PAGE 1 END ================================*/
   
   /*========================== PAGE 2 START ================================*/
   /*
   $Panel->WriteItemHeader( $this->Msg['SYS_STAT_DATA'] );
   // Write Simple Panel
   AdminHTML::PanelSimpleH();
   echo '<table class="EditTable" width=400>';
   echo '<TR class=tr1><TD height=25>'.$this->Msg['FLD_DT'];
   echo '<td>';
   $this->Form->ButtonCheck();
   if( isset( $arr[0] ) ) $this->dt = $arr[0];
   else $this->dt = 1;
   $this->dt = 1;
   $this->Form->Hidden( 'dt', $this->dt );

   echo '<TR class=tr2><TD height=25>'.$this->Msg['_FLD_PAGE'];
   if( isset( $arr[1] ) ) $this->page = $arr[1];
   else $this->page = 0;
   echo '<td>'; $this->Form->CheckBox( "page", '1', $this->page );

   echo '<TR class=tr1><TD height=25>'.$this->Msg['_FLD_MODULE'];
   echo '<td>';
   $this->Form->ButtonCheck();
   if( isset( $arr[2] ) ) $this->module_ = $arr[2];
   else $this->module_ = 1;
   $this->module_ = 1;
   $this->Form->Hidden( 'module_', $this->module_ );

   echo '<TR class=tr2><TD height=25>'.$this->Msg['SYS_STAT_REFER'];
   if( isset( $arr[3] ) ) $this->refer = $arr[3];
   else $this->refer = 0;
   echo '<td>'; $this->Form->CheckBox( "refer", '1', $this->refer );

   echo '<TR class=tr1><TD height=25>'.$this->Msg['SYS_STAT_TIME_GEN'];
   if( isset( $arr[4] ) ) $this->time_gen = $arr[4];
   else $this->time_gen = 0;
   echo '<td>'; $this->Form->CheckBox( "time_gen", '1', $this->time_gen );

   echo '<TR class=tr2><TD height=25>'.$this->Msg['SYS_STAT_IP'];
   echo '<td>';
   $this->Form->ButtonCheck();
   if( isset( $arr[5] ) ) $this->ip = $arr[5];
   else $this->ip = 1;
   $this->ip = 1;
   $this->Form->Hidden( 'ip', $this->ip );

   echo '<TR class=tr1><TD height=25>'.$this->Msg['SYS_STAT_HOST'];
   if( isset( $arr[6] ) ) $this->host = $arr[6];
   else $this->host = 0;
   echo '<td>'; $this->Form->CheckBox( "host", '1', $this->host );

   echo '<TR class=tr2><TD height=25>'.$this->Msg['SYS_STAT_PROXY'];
   if( isset( $arr[7] ) ) $this->proxy = $arr[7];
   else $this->proxy = 0;
   echo '<td>'; $this->Form->CheckBox( "proxy", '1', $this->proxy );

   echo '<TR class=tr1><TD height=25>'.$this->Msg['FLD_USER_ID';
   echo '<td>';
   $this->Form->ButtonCheck();
   if( isset( $arr[8] ) ) $this->user = $arr[8];
   else $this->user = 1;
   $this->user = 1;
   $this->Form->Hidden( 'user', $this->user );

   echo '<TR class=tr2><TD height=25>'.$this->Msg['SYS_STAT_USER_AGENT'];
   if( isset( $arr[9] ) ) $this->agent = $arr[9];
   else $this->agent = 0;
   echo '<td>'; $this->Form->CheckBox( "agent", '1', $this->agent );

   echo '<TR class=tr1><TD height=25>'.$this->Msg['SYS_STAT_SCREEN_RES'];
   if( isset( $arr[10] ) ) $this->screen_res = $arr[10];
   else $this->screen_res = 0;
   echo '<td>'; $this->Form->CheckBox( "screen_res", '1', $this->screen_res );

   echo '<TR class=tr2><TD height=25>'.$this->Msg['_FLD_LANGUAGE'];
   if( isset( $arr[11] ) ) $this->lang = $arr[11];
   else $this->lang = 1;
   echo '<td>'; $this->Form->CheckBox( "lang", '1', $this->lang );

   echo '<TR class=tr1><TD height=25>'.$this->Msg['FLD_COUNTRY'];
   if( isset( $arr[12] ) ) $this->country = $arr[12];
   else $this->country = 1;
   echo '<td>'; $this->Form->CheckBox( "country", '1', $this->country );
   echo '</table>';
   AdminHTML::PanelSimpleF();
   $Panel->WriteItemFooter();
   $Panel->WritePanelFooter();
   */
   /*========================== PAGE 2 END ================================*/
    ?><div class="space"></div><?
   if($this->Right->IsUpdate($this->module)) $this->Form->WriteSavePanel( $script );
   $this->Form->WriteFooter();
   AdminHTML::PanelSubF();
  } //--- end of Show()


// ================================================================================================
// Function : Save
// Version : 1.0.0
// Date : 19.12.2007
//
// Parms :
// Returns : true,false / Void
// Description : Store data to the table
// ================================================================================================
// Programmer : Igor Trokhymchuk
// Date : 19.12.2007
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================
function Save()
{
   $q = "SELECT * FROM ".TblSysSetGlobal." WHERE `id`='1'";
   $res = $this->Right->Query( $q, $this->user_id, $this->module );
   if( !$res OR !$this->Right->result ) return false;
   $rows = $this->Right->db_GetNumRows();
   if( $rows > 0 ){
    $q = "UPDATE ".TblSysSetGlobal." SET
         `mail_host`='".$this->mail_host."',
         `mail_port`='".$this->mail_port."',
         `mail_mailer`='".$this->mail_mailer."',
         `mail_smtp_auth`='".$this->mail_smtp_auth."',
         `mail_username`='".$this->mail_username."',
         `mail_password`='".$this->mail_password."',
         `mail_from`='".$this->mail_from."',
         `mail_word_wrap`='".$this->mail_word_wrap."',
         `mail_is_html`='".$this->mail_is_html."',
         `mail_priority`='".$this->mail_priority."',
         `mail_charset`='".$this->mail_charset."',
         `mail_encoding`='".$this->mail_encoding."',
         `mail_auto_emails`='".$this->mail_auto_emails."',
         `mail_admin_email`='".$this->mail_admin_email."',
         `editer`='".$this->editer."'
          WHERE `id`='1'";
    $res = $this->Right->Query( $q, $this->user_id, $this->module );
    //echo '<br>$q='.$q.' $res='.$res.' $this->Right->result='.$this->Right->result;
    if( !$res OR !$this->Right->result ) return false;
   }
   else{
    $q = "INSERT INTO ".TblSysSetGlobal." SET 
         `id`='1',
         `mail_host`='".$this->mail_host."',
         `mail_port`='".$this->mail_port."',
         `mail_mailer`='".$this->mail_mailer."',
         `mail_smtp_auth`='".$this->mail_smtp_auth."',
         `mail_username`='".$this->mail_username."',
         `mail_password`='".$this->mail_password."',
         `mail_from`='".$this->mail_from."',
         `mail_word_wrap`='".$this->mail_word_wrap."',
         `mail_is_html`='".$this->mail_is_html."',
         `mail_priority`='".$this->mail_priority."',
         `mail_charset`='".$this->mail_charset."',
         `mail_encoding`='".$this->mail_encoding."',
         `mail_auto_emails`='".$this->mail_auto_emails."',
         `mail_admin_email`='".$this->mail_admin_email."',
         `editer`='".$this->editer."'";
    $res = $this->Right->Query( $q, $this->user_id, $this->module );
    //echo '<br>$q='.$q.' $res='.$res.' $this->Right->result='.$this->Right->result;
    if( !$res OR !$this->Right->result ) return false;
   }
   
   $ln_sys = &check_init('LangSys','SysLang');
        //print_r($description_arr);
       $ln_arr = $ln_sys->LangArray( _LANG_ID );
       while( $el = each( $ln_arr ) )
       {
           $lang_id = $el['key']; 
            $q="UPDATE `".TblSysSetGlobalSprMail."` SET 
              `from`='".$this->mail_from_name[$lang_id]."',
              `head`='".$this->mail_header[$lang_id]."',
              `foot`='".$this->mail_footer[$lang_id]."'
           WHERE `lang_id`='".$lang_id."'";
          $res = $this->db->db_Query($q);
//          echo '<br>$q='.$q.' $res='.$res.' $$this->Rights->result='.$this->db->result;
          if( !$this->db->result ) return false;
       } //--- end while
   // Save Description on different languages
/*  
    $id = 1;
     
    $res=$this->Spr->SaveNameArr( $id, $this->mail_from_name, TblSysSetGlobalSprMailFrom );
   if( !$res ) return false;   

   $res=$this->Spr->SaveNameArr( $id, $this->mail_header, TblSysSetGlobalSprMailHeader );
   if( !$res ) return false; 
   
   $res=$this->Spr->SaveNameArr( $id, $this->mail_footer, TblSysSetGlobalSprMailFooter );
   if( !$res ) return false;    */
   return true;  
} //end of fuinction Save()

                             
 }  //end of class SysSettingsAdm