<?php
/**
* Class StatSetCtrl
* Class definition for all actions with system settings on the back-end
* @package System Package of SEOCMS
* @author Igor Trokhymchuk  <ihor@seotm.com>
* @version 1.1, 02.04.2012
* @copyright (c) 2005+ by SEOTM
*/
 class StatSetCtrl extends StatSet
 {
  var $Right;
  var $Form;
  var $Msg;
  var $Spr;

  var $user_id;
  var $module;



  // ================================================================================================
  //    Function          : StatSetCtrl (Constructor)
  //    Version           : 1.0.0
  //    Date              : 14.03.2005
  //    Returns           :
  //    Description       : StatSetCtrl
  // ================================================================================================
  /**
  * StatSetCtrl::__construct()
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
  function __construct( $user_id=NULL, $module_id=NULL )
  {
   $this->user_id = $user_id;
   $this->module_id = $module_id;
         
   $this->Right =  new Rights($this->user_id, $this->module_id);
   $this->Form = new Form( 'form_stat' );        /* create Form object as a property of this class */
   $this->Msg = new ShowMsg();                   /* create ShowMsg object as a property of this class */
   $this->Spr = new SysSpr( NULL,NULL,NULL,NULL,NULL,NULL,NULL ); /* create SysSpr object as a property of this class */
  } //--- end of StatSetCtrl()



  // ================================================================================================
  //    Function          : StatSetShow()
  //    Version           : 1.0.0
  //    Date              : 14.03.2005
  //    Parms             :
  //    Returns           : true/false
  //    Description       : Show Statistic Settings
  // ================================================================================================

  function StatSetShow()
  {
   $Panel = new Panel();

   $script = 'module='.$this->module;
   $script = $_SERVER['PHP_SELF']."?$script";

   $q = "SELECT * FROM ".TblModStatSet." where id=1";
   $res = $this->Right->Query( $q, $this->user_id, $this->module );
   //echo '<br>$res='.$res;
   if( !$res ) return false;
   $mas = $this->Right->db_FetchAssoc();
   $arr = explode( ';', $mas['fields'] );

   $txt = $this->Msg->show_text('TXT_EDIT');
   AdminHTML::PanelSubH( $txt );
   /* Write Form Header */
   $this->Form->WriteHeader( $script );
   $Panel->WritePanelHead( "SubPanel_" );
   $Panel->WriteItemHeader( $this->Msg->show_text('SYS_STAT_ACCESS') );
   /* Write Simple Panel*/
   AdminHTML::PanelSimpleH();

   echo '<table class="EditTable" width=400>';

   echo '<TR><TD>'.$this->Msg->show_text('FLD_ID');
   echo '<TD>';
   echo $mas['id'];
   $this->Form->Hidden( 'id', $mas['id'] );

   echo '<tr><td>'.$this->Msg->show_text('SYS_STAT_DB_NAME');
   echo '<td>';
   $this->Form->TextBox( 'db_name', $mas['db_name'], 14 );
   echo '<tr><td>'.$this->Msg->show_text('SYS_STAT_DB_USER');
   echo '<td>';
   $this->Form->TextBox( 'db_user', $mas['db_user'], 14 );
   echo '<tr><td>'.$this->Msg->show_text('SYS_STAT_DB_PASS');
   echo '<td>';
   $this->Form->TextBox( 'db_pass', $mas['db_pass'], 14 );
   echo '<tr><td>'.$this->Msg->show_text('SYS_STAT_FRONT');;
   echo '<td>';
   $this->Form->CheckBox( "front", '1', $mas['front'] );
   echo '<tr><td>'.$this->Msg->show_text('SYS_STAT_BACK');;
   echo '<td>';
   $this->Form->CheckBox( "back", '1', $mas['back'] );
   //echo '<tr><td colspan=2>';
   //$this->Form->WriteSavePanel( $script );
   echo '</table>';
   AdminHTML::PanelSimpleF();
   $Panel->WriteItemFooter();

   /*          PAGE 2             */
   $Panel->WriteItemHeader( $this->Msg->show_text('SYS_STAT_DATA') );
   /* Write Simple Panel*/
   AdminHTML::PanelSimpleH();
   echo '<table class="EditTable" width=400>';
   echo '<TR class=tr1><TD height=25>'.$this->Msg->show_text('FLD_DT');
   echo '<td>';
   $this->Form->ButtonCheck();
   if( isset( $arr[0] ) ) $this->dt = $arr[0];
   else $this->dt = 1;
   $this->dt = 1;
   $this->Form->Hidden( 'dt', $this->dt );

   echo '<TR class=tr2><TD height=25>'.$this->Msg->show_text('_FLD_PAGE');
   if( isset( $arr[1] ) ) $this->page = $arr[1];
   else $this->page = 0;
   echo '<td>'; $this->Form->CheckBox( "page", '1', $this->page );

   echo '<TR class=tr1><TD height=25>'.$this->Msg->show_text('_FLD_MODULE');
   echo '<td>';
   $this->Form->ButtonCheck();
   if( isset( $arr[2] ) ) $this->module_ = $arr[2];
   else $this->module_ = 1;
   $this->module_ = 1;
   $this->Form->Hidden( 'module_', $this->module_ );

   echo '<TR class=tr2><TD height=25>'.$this->Msg->show_text('SYS_STAT_REFER');
   if( isset( $arr[3] ) ) $this->refer = $arr[3];
   else $this->refer = 0;
   echo '<td>'; $this->Form->CheckBox( "refer", '1', $this->refer );

   echo '<TR class=tr1><TD height=25>'.$this->Msg->show_text('SYS_STAT_TIME_GEN');
   if( isset( $arr[4] ) ) $this->time_gen = $arr[4];
   else $this->time_gen = 0;
   echo '<td>'; $this->Form->CheckBox( "time_gen", '1', $this->time_gen );

   echo '<TR class=tr2><TD height=25>'.$this->Msg->show_text('SYS_STAT_IP');
   echo '<td>';
   $this->Form->ButtonCheck();
   if( isset( $arr[5] ) ) $this->ip = $arr[5];
   else $this->ip = 1;
   $this->ip = 1;
   $this->Form->Hidden( 'ip', $this->ip );

   echo '<TR class=tr1><TD height=25>'.$this->Msg->show_text('SYS_STAT_HOST');
   if( isset( $arr[6] ) ) $this->host = $arr[6];
   else $this->host = 0;
   echo '<td>'; $this->Form->CheckBox( "host", '1', $this->host );

   echo '<TR class=tr2><TD height=25>'.$this->Msg->show_text('SYS_STAT_PROXY');
   if( isset( $arr[7] ) ) $this->proxy = $arr[7];
   else $this->proxy = 0;
   echo '<td>'; $this->Form->CheckBox( "proxy", '1', $this->proxy );

   echo '<TR class=tr1><TD height=25>'.$this->Msg->show_text('FLD_USER_ID');
   echo '<td>';
   $this->Form->ButtonCheck();
   if( isset( $arr[8] ) ) $this->user = $arr[8];
   else $this->user = 1;
   $this->user = 1;
   $this->Form->Hidden( 'user', $this->user );

   echo '<TR class=tr2><TD height=25>'.$this->Msg->show_text('SYS_STAT_USER_AGENT');
   if( isset( $arr[9] ) ) $this->agent = $arr[9];
   else $this->agent = 0;
   echo '<td>'; $this->Form->CheckBox( "agent", '1', $this->agent );

   echo '<TR class=tr1><TD height=25>'.$this->Msg->show_text('SYS_STAT_SCREEN_RES');
   if( isset( $arr[10] ) ) $this->screen_res = $arr[10];
   else $this->screen_res = 0;
   echo '<td>'; $this->Form->CheckBox( "screen_res", '1', $this->screen_res );

   echo '<TR class=tr2><TD height=25>'.$this->Msg->show_text('_FLD_LANGUAGE');
   if( isset( $arr[11] ) ) $this->lang = $arr[11];
   else $this->lang = 1;
   echo '<td>'; $this->Form->CheckBox( "lang", '1', $this->lang );

   echo '<TR class=tr1><TD height=25>'.$this->Msg->show_text('FLD_COUNTRY');
   if( isset( $arr[12] ) ) $this->country = $arr[12];
   else $this->country = 1;
   echo '<td>'; $this->Form->CheckBox( "country", '1', $this->country );
   echo '</table>';
   AdminHTML::PanelSimpleF();
   $Panel->WriteItemFooter();
   $Panel->WritePanelFooter();

   $this->Form->WriteSavePanel( $script );
   $this->Form->WriteFooter();
   AdminHTML::PanelSubF();

  } //--- end of StatSetShow()



  // ================================================================================================
  //    Function          : StatSetSave()
  //    Version           : 1.0.0
  //    Date              : 15.03.2005
  //    Parms             :
  //    Returns           : true/false
  //    Description       : Save Statistic Settings
  // ================================================================================================

  function StatSetSave()
  {
   $this->StatSetFields();

   $q = "SELECT * FROM ".TblModStatSet." where id=1";
   $res = $this->Right->Query( $q, $this->user_id, $this->module );
   if( !$res ) return false;
   $rows = $this->Right->db_GetNumRows();
   if( $rows > 0 )
   {
    $q = "update ".TblModStatSet." set
         `db_name`='$this->db_name',
         `db_user`='$this->db_user',
         `db_pass`='$this->db_pass',
         `front`='$this->front',
         `back`='$this->back',
         `fields`='$this->fields'";
    $q = $q." where id=1";
    $res = $this->Right->Query( $q, $this->user_id, $this->module );
    if( !$res ) return false;
   }else
   {
    $q = "insert into ".TblModStatSet." values('1','$this->db_name','$this->db_user','$this->db_pass','$this->front','$this->back','$this->fields')";
    $res = $this->Right->Query( $q, $this->user_id, $this->module );
    if( !$res ) return false;
   }
   return true;
  } //--- end of StatSetSave()


  // ================================================================================================
  //    Function          : StatSetFields()
  //    Version           : 1.0.0
  //    Date              : 15.03.2005
  //    Parms             :
  //    Returns           : true/false
  //    Description       : Save Statistic Settings Fields
  // ================================================================================================

  function StatSetFields()
  {
    if( isset( $_REQUEST['dt'] ) ) $this->dt = $_REQUEST['dt'];
    else $this->dt = 0;
    if( isset( $_REQUEST['page'] ) ) $this->page = $_REQUEST['page'];
    else $this->page = 0;
    if( isset( $_REQUEST['module_'] ) ) $this->module_ = $_REQUEST['module_'];
    else $this->module_ = 0;
    if( isset( $_REQUEST['refer'] ) ) $this->refer = $_REQUEST['refer'];
    else $this->refer = 0;
    if( isset( $_REQUEST['time_gen'] ) ) $this->time_gen = $_REQUEST['time_gen'];
    else $this->time_gen = 0;
    if( isset( $_REQUEST['ip'] ) ) $this->ip = $_REQUEST['ip'];
    else $this->ip = 0;
    if( isset( $_REQUEST['host'] ) ) $this->host = $_REQUEST['host'];
    else $this->host = 0;
    if( isset( $_REQUEST['proxy'] ) ) $this->proxy = $_REQUEST['proxy'];
    else $this->proxy = 0;
    if( isset( $_REQUEST['user'] ) ) $this->user = $_REQUEST['user'];
    else $this->user = 0;
    if( isset( $_REQUEST['agent'] ) ) $this->agent = $_REQUEST['agent'];
    else $this->agent = 0;
    if( isset( $_REQUEST['screen_res'] ) ) $this->screen_res = $_REQUEST['screen_res'];
    else $this->screen_res = 0;
    if( isset( $_REQUEST['lang'] ) ) $this->lang = $_REQUEST['lang'];
    else $this->lang = 0;
    if( isset( $_REQUEST['country'] ) ) $this->country = $_REQUEST['country'];
    else $this->country = 0;
    $this->fields = $this->dt.';'.$this->page.';'.$this->module_.';'.$this->refer.';'.$this->time_gen.';'.$this->ip.';'.$this->host.';'.$this->proxy.';'.$this->user.';'.$this->agent.';'.$this->screen_res.';'.$this->lang.';'.$this->country;
  } //--- end of StatSetFields()

 } //--- end of class

?>
