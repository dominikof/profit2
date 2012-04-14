<?php
/**
* Class StatRep
* Class definition for see Statistic reports
* @package System Package of SEOCMS
* @author Igor Trokhymchuk  <ihor@seotm.com>
* @version 1.1, 02.04.2012
* @copyright (c) 2005+ by SEOTM
*/
 class StatRep
 {
  public  $Right;
  public  $Form;
  public  $Msg;
  public  $Spr;

  public  $user_id;
  public  $module;

  public  $display;
  public  $sort;
  public  $start;

  public  $fltr_dtfrom;
  public  $fltr_dtto;

  public  $type1;       /* Hit Host */
  public  $type2;       /* Day, Week, Month */
  public  $type3;       /* Modules, Users.. etc */
  public  $field;       /* name of field in database */

  public  $mas;
  public  $masHead;
  public  $masDays;
  public  $RowItog;
  public  $ColItog;
  public  $_utf8win1251 = array(
"\xD0\x90"=>"\xC0","\xD0\x91"=>"\xC1","\xD0\x92"=>"\xC2","\xD0\x93"=>"\xC3","\xD0\x94"=>"\xC4",
"\xD0\x95"=>"\xC5","\xD0\x81"=>"\xA8","\xD0\x96"=>"\xC6","\xD0\x97"=>"\xC7","\xD0\x98"=>"\xC8",
"\xD0\x99"=>"\xC9","\xD0\x9A"=>"\xCA","\xD0\x9B"=>"\xCB","\xD0\x9C"=>"\xCC","\xD0\x9D"=>"\xCD",
"\xD0\x9E"=>"\xCE","\xD0\x9F"=>"\xCF","\xD0\x20"=>"\xD0","\xD0\xA1"=>"\xD1","\xD0\xA2"=>"\xD2",
"\xD0\xA3"=>"\xD3","\xD0\xA4"=>"\xD4","\xD0\xA5"=>"\xD5","\xD0\xA6"=>"\xD6","\xD0\xA7"=>"\xD7",
"\xD0\xA8"=>"\xD8","\xD0\xA9"=>"\xD9","\xD0\xAA"=>"\xDA","\xD0\xAB"=>"\xDB","\xD0\xAC"=>"\xDC",
"\xD0\xAD"=>"\xDD","\xD0\xAE"=>"\xDE","\xD0\xAF"=>"\xDF","\xD0\x87"=>"\xAF","\xD0\x86"=>"\xB2",
"\xD0\x84"=>"\xAA","\xD0\x8E"=>"\xA1","\xD0\xB0"=>"\xE0","\xD0\xB1"=>"\xE1","\xD0\xB2"=>"\xE2",
"\xD0\xB3"=>"\xE3","\xD0\xB4"=>"\xE4","\xD0\xB5"=>"\xE5","\xD1\x91"=>"\xB8","\xD0\xB6"=>"\xE6",
"\xD0\xB7"=>"\xE7","\xD0\xB8"=>"\xE8","\xD0\xB9"=>"\xE9","\xD0\xBA"=>"\xEA","\xD0\xBB"=>"\xEB",
"\xD0\xBC"=>"\xEC","\xD0\xBD"=>"\xED","\xD0\xBE"=>"\xEE","\xD0\xBF"=>"\xEF","\xD1\x80"=>"\xF0",
"\xD1\x81"=>"\xF1","\xD1\x82"=>"\xF2","\xD1\x83"=>"\xF3","\xD1\x84"=>"\xF4","\xD1\x85"=>"\xF5",
"\xD1\x86"=>"\xF6","\xD1\x87"=>"\xF7","\xD1\x88"=>"\xF8","\xD1\x89"=>"\xF9","\xD1\x8A"=>"\xFA",
"\xD1\x8B"=>"\xFB","\xD1\x8C"=>"\xFC","\xD1\x8D"=>"\xFD","\xD1\x8E"=>"\xFE","\xD1\x8F"=>"\xFF",
"\xD1\x96"=>"\xB3","\xD1\x97"=>"\xBF","\xD1\x94"=>"\xBA","\xD1\x9E"=>"\xA2");

  public  $_win1251utf8 = array(
"\xC0"=>"\xD0\x90","\xC1"=>"\xD0\x91","\xC2"=>"\xD0\x92","\xC3"=>"\xD0\x93","\xC4"=>"\xD0\x94",
"\xC5"=>"\xD0\x95","\xA8"=>"\xD0\x81","\xC6"=>"\xD0\x96","\xC7"=>"\xD0\x97","\xC8"=>"\xD0\x98",
"\xC9"=>"\xD0\x99","\xCA"=>"\xD0\x9A","\xCB"=>"\xD0\x9B","\xCC"=>"\xD0\x9C","\xCD"=>"\xD0\x9D",
"\xCE"=>"\xD0\x9E","\xCF"=>"\xD0\x9F","\xD0"=>"\xD0\x20","\xD1"=>"\xD0\xA1","\xD2"=>"\xD0\xA2",
"\xD3"=>"\xD0\xA3","\xD4"=>"\xD0\xA4","\xD5"=>"\xD0\xA5","\xD6"=>"\xD0\xA6","\xD7"=>"\xD0\xA7",
"\xD8"=>"\xD0\xA8","\xD9"=>"\xD0\xA9","\xDA"=>"\xD0\xAA","\xDB"=>"\xD0\xAB","\xDC"=>"\xD0\xAC",
"\xDD"=>"\xD0\xAD","\xDE"=>"\xD0\xAE","\xDF"=>"\xD0\xAF","\xAF"=>"\xD0\x87","\xB2"=>"\xD0\x86",
"\xAA"=>"\xD0\x84","\xA1"=>"\xD0\x8E","\xE0"=>"\xD0\xB0","\xE1"=>"\xD0\xB1","\xE2"=>"\xD0\xB2",
"\xE3"=>"\xD0\xB3","\xE4"=>"\xD0\xB4","\xE5"=>"\xD0\xB5","\xB8"=>"\xD1\x91","\xE6"=>"\xD0\xB6",
"\xE7"=>"\xD0\xB7","\xE8"=>"\xD0\xB8","\xE9"=>"\xD0\xB9","\xEA"=>"\xD0\xBA","\xEB"=>"\xD0\xBB",
"\xEC"=>"\xD0\xBC","\xED"=>"\xD0\xBD","\xEE"=>"\xD0\xBE","\xEF"=>"\xD0\xBF","\xF0"=>"\xD1\x80",
"\xF1"=>"\xD1\x81","\xF2"=>"\xD1\x82","\xF3"=>"\xD1\x83","\xF4"=>"\xD1\x84","\xF5"=>"\xD1\x85",
"\xF6"=>"\xD1\x86","\xF7"=>"\xD1\x87","\xF8"=>"\xD1\x88","\xF9"=>"\xD1\x89","\xFA"=>"\xD1\x8A",
"\xFB"=>"\xD1\x8B","\xFC"=>"\xD1\x8C","\xFD"=>"\xD1\x8D","\xFE"=>"\xD1\x8E","\xFF"=>"\xD1\x8F",
"\xB3"=>"\xD1\x96","\xBF"=>"\xD1\x97","\xBA"=>"\xD1\x94","\xA2"=>"\xD1\x9E");


  /**
  * StatRep::__construct()
  * 
  * @param integer $user_id
  * @param integer $module_id
  * @return void
  */
  function __construct($user_id=NULL, $module=NULL)
  {
   $this->user_id = $user_id;
   $this->module = $module;
   $this->Right =  new RightsOld($this->user_id, $this->module);
   $this->Form = new Form( 'form_stat' );        /* create Form object as a property of this class */
   $this->Msg = &check_init_txt('TblBackMulti',TblBackMulti);                   /* create ShowMsg object as a property of this class */
   $this->Spr = new SysSpr( NULL,NULL,NULL,NULL,NULL,NULL,NULL ); /* create SysSpr object as a property of this class */

    /* Get Statistic Settings */
   $set = new StatSet();
   $this->Right->db_SetConfig( "", $set->db_user, $set->db_pass, $set->db_name, "" );
   $res = $this->Right->db_Select( $set->db_name );
  } //--- end of StatSetCtrl()



  // ================================================================================================
  //    Function          : Form()
  //    Version           : 1.0.0
  //    Date              : 19.03.2005
  //    Parms             :
  //    Returns           : true/false
  //    Description       : Show Report Form
  // ================================================================================================

  function Form()
  {
   $script = 'module='.$this->module;
   $script = $_SERVER['PHP_SELF']."?$script";
   
   $calendar = new DHTML_Calendar(false, 'en', 'calendar-win2k-2', false);
   $calendar->load_files();

   /* Write Table Part */
   AdminHTML::TablePartH();

   $this->Form->WriteHeader( $script );
   echo '<table class="EditTable" border=0 align=center>';
   echo '<tr><td><a href="'.$script.'&task=stat">'. $this->Msg['SYS_STAT_TXT_ATTENDANCE'].'</a>';

   echo '<tr><td>';
    echo '<table class="EditTable" border=0 align=center>';
    echo '<tr><td>';
     echo $this->Msg['FLD_START_DATE'];
    echo '<td>';
     //$this->Form->TextBox( 'fltr_dtfrom', $this->fltr_dtfrom, 10 );
     if( $this->fltr_dtfrom!=NULL ) $start_date_val=$this->fltr_dtfrom;
     else $start_date_val=strftime('%Y-%m-%d %H:%M', strtotime('now'));      
     //if( empty($start_date_val) ) $start_date_val = strftime('%Y-%m-%d %H:%M', strtotime('now'));
     $a1 = array('firstDay'       => 1, // show Monday first
                 'showsTime'      => false,
                 'showOthers'     => true,
                 'ifFormat'       => '%Y-%m-%d',
                 'timeFormat'     => '24');
     $a2 = array('style'       => 'width: 15em; border: 1px solid #000; text-align: center',
                 'name'        => 'fltr_dtfrom',
                 'value'       => $start_date_val );
     $calendar->make_input_field( $a1, $a2 );
           
    echo '<tr><td>';
     echo $this->Msg['FLD_END_DATE'];
    echo '<td>';
     //$this->Form->TextBox( 'fltr_dtto', $this->fltr_dtto, 10 );
     if( $this->fltr_dtto!=NULL ) $end_date_val=$this->fltr_dtto;
     else $end_date_val=strftime('%Y-%m-%d %H:%M', strtotime('+30 days'));
     //if( empty( $end_date_val ) ) $end_date_val = strftime('%Y-%m-%d %H:%M', strtotime('+30 days'));
     $a1 = array('firstDay'       => 1, // show Monday first
                 'showsTime'      => false,
                 'showOthers'     => true,
                 'ifFormat'       => '%Y-%m-%d',
                 'timeFormat'     => '24');
     $a2 = array('style'       => 'width: 15em; border: 1px solid #000; text-align: center',
                 'name'        => 'fltr_dtto',
                 'value'       => $end_date_val );
     $calendar->make_input_field( $a1, $a2 );
    echo '<tr><td colspan=2 align=center>';
    echo '</table>';

   echo '<td>';
   $this->Form->Radio( 'type1', $this->Msg['SYS_STAT_HIT'], "hit", $this->type1 );
   echo '<br>';
   $this->Form->Radio( 'type1', $this->Msg['SYS_STAT_HOST'], "host", $this->type1 );
echo '<td>';
   $this->Form->Radio( 'type2', $this->Msg['SYS_STAT_DAY'], "d", $this->type2 );
   echo '<br>';
   $this->Form->Radio( 'type2', $this->Msg['SYS_STAT_WEEK'], "w", $this->type2 );
   echo '<br>';
   $this->Form->Radio( 'type2', $this->Msg['SYS_STAT_MONTH'], "m", $this->type2 );
   echo '<br>';
   $this->Form->Radio( 'type2', $this->Msg['SYS_STAT_YEAR'], "y", $this->type2 );
echo '<td>';
   $arr['page_url'] = $this->Msg['SYS_STAT_TXT_PAGES_URL']; 
   $arr['module'] = $this->Msg['SYS_STAT_TXT_MODULES'];
   $arr['refer'] = $this->Msg['SYS_STAT_REFER'];
   $arr['refer_server'] = $this->Msg['SYS_STAT_REFER_SERVER'];
   $arr['user'] = $this->Msg['SYS_STAT_TXT_USERS'];
   $arr['cntr'] = $this->Msg['FLD_COUNTRY'];
   $arr['lng'] = $this->Msg['_FLD_LANGUAGE'];
   $arr['agent'] = $this->Msg['SYS_STAT_USER_AGENT'];
   $arr['search_words'] = $this->Msg['FLD_KEYWORDS'];

   $this->Form->Select( $arr, 'type3', $this->type3 );
echo '<td>';
   echo '<INPUT TYPE=hidden NAME="task" VALUE="show">';
   echo '<INPUT TYPE=hidden NAME="display" VALUE="'.$this->display.'">';
   echo '<INPUT TYPE=hidden NAME="start" VALUE="'.$this->start.'">';
   echo '<INPUT TYPE=hidden NAME="sort" VALUE="'.$this->sort.'">';
echo '<td align=center>';
   echo '<INPUT TYPE=submit class="button" VALUE="'.$this->Msg['SYS_STAT_GET_ST'].'">';
   echo '</table>';

   $this->Form->WriteFooter();
   AdminHTML::TablePartF();
  } //--- ReportForm()



  // ================================================================================================
  //    Function          : Select()
  //    Version           : 1.0.0
  //    Date              : 22.03.2005
  //    Parms             :
  //    Returns           : true/false
  //    Description       : Select Data form Database
  // ================================================================================================

  function Select()
  {
   $q = "select * from ".TblModStatLog." where 1 ";

   $field = $this->type3;
   if( $this->type3 == 'page_url' ) $field = 'page_url';
   if( $this->type3 == 'module' ) $field = 'page';
   if( $this->type3 == 'cntr' ) $field = 'country';
   if( $this->type3 == 'lng' ) $field = 'lang';
   if( $this->type3 == 'refer_server' ) $field = 'refer';
   if( $this->type3 == 'search_words' ) $field = 'refer';


   if( $this->type1 == 'hit' )
   {
    $q = "select ".$field.",cnt,dt from ".TblModStatLog." where 1 ";
   }
   if( $this->type1 == 'host' )
   {
    $q = "select ".$field.",1 as cnt,dt,ip from ".TblModStatLog." where 1 ";
   }

   $q = $q." and dt>='".$this->fltr_dtfrom."'";
   $q = $q." and dt<='".$this->fltr_dtto."'";

   //if( $this->type1 == 'host' ) $q = $q.' group by ip,dt,'.$field;
   if( $this->type1 == 'host' ) $q = $q.' group by ip,dt';

   $q = $q." order by ".$field.",dt";


   //if( $this->task1 ) $q = $q." and group by ";
//echo $q;
   return $q;
  } //--- end of Select()




  // ================================================================================================
  //    Function          : GetCountDays()
  //    Version           : 1.0.0
  //    Date              : 23.03.2005
  //    Parms             :
  //    Returns           : true/false
  //    Description       : Get Count Days
  // ================================================================================================

  function GetCountDays()
  {
   $d1 = explode( '-', $this->fltr_dtfrom );
   $d1_time = mktime( 0, 0, 0, $d1[1], $d1[2], $d1[0] );
   $m1 = getdate( $d1_time );
   //echo '<br>'.$d1[2].'-'.$d1[1].'-'.$d1[0];

   $d2 = explode( '-', $this->fltr_dtto );
   $d2_time = mktime( 0, 0, 0, $d2[1], $d2[2], $d2[0] );
   $m2 = getdate( $d2_time );

   //echo '<br>'.$m1['yday'];
   //echo '<br>'.$m2['yday'];

   $days = $m2['yday'] - $m1['yday'];

   return $days + 1;
  } //--- end of GetCountDays()



  // ================================================================================================
  //    Function          : GetName()
  //    Version           : 1.0.0
  //    Date              : 23.03.2005
  //    Parms             :
  //    Returns           : true/false
  //    Description       :
  // ================================================================================================

  function GetName( $id )
  {
    $db = new DB();
    if( $this->type3 == 'page_url' )
    {
     $name = $id;
    }
    
    if( $this->type3 == 'module' )
    {
     $name = substr( $id, 1 );
    }

    if( $this->type3 == 'refer' )
    {
     $name = '';
    }

    if( $this->type3 == 'refer_server' )
    {
     $name = '';
    }

    if( $this->type3 == 'search_words' )
    {
     $name = '';
    }


    if( $this->type3 == 'user' )
    {
     $q = "select login from sys_user where id='$id'";
     $db->db_Query( $q );
     $row = $db->db_FetchAssoc();
     $name = $row['login'];
    }

    if( $this->type3 == 'cntr' )
    {
     $q = "select ctry,country from mod_stat_ip where ctry='$id'";
     $res = $this->Right->Query( $q, $this->user_id, $this->module );
     if( !$res ) return false;
     $rows = $this->Right->db_GetNumRows();
     $row = $this->Right->db_FetchAssoc();
     $name = $row['country'];
    }

    if( $this->type3 == 'lng' )
    {
     $q = "select cod,name from sys_spr_languages where cod='$id' and lang_id='1'";
     $res = $db->db_Query( $q );
     if( !$res ) return false;
     $rows = $db->db_GetNumRows();
     $row = $db->db_FetchAssoc();
     $name = $row['name'];
     if( $name == '' ) $name = $id;
    }

    if( $this->type3 == 'agent' )
    {
     $q = "select name from ".TblModStatAgent." where id='$id'";
     $res = $db->db_Query( $q );
     if( !$res ) return false;
     $rows = $db->db_GetNumRows();
     $row = $db->db_FetchAssoc();
     $name = $row['name'];
     if( $name == '' ) $name = $id;
    }

    if( trim( $name ) == '' ) $name = '-';

    return $name;
  } //--- end GetName



  // ================================================================================================
  //    Function          : Show()
  //    Version           : 1.0.0
  //    Date              : 22.03.2005
  //    Parms             :
  //    Returns           : true/false
  //    Description       : Show Data Of Report
  // ================================================================================================

  function Show()
  {
   $this->mas = NULL;
   $this->masHead = NULL;
   $this->masDays = NULL;
   $this->RowItog = NULL;
   $this->ColItog = NULL;

   $param = "&fltr_dtfrom=".$this->fltr_dtfrom."&fltr_dtto=".$this->fltr_dtto."&type1=".$this->type1."&type2=".$this->type2."&type3=".$this->type3;
   $script = 'module='.$this->module.'&display='.$this->display.'&start='.$this->start.'&sort='.$this->sort;
   $script = $_SERVER['PHP_SELF']."?$script";
   $script_ = $_SERVER['PHP_SELF'].'?module='.$this->module.'&task=show'.$param;



   $q = $this->Select();
   $res = $this->Right->Query( $q, $this->user_id, $this->module );
   //echo '<br>$q='.$q.' $res='.$res;

   if( !$res ) return false;
   $rows = $this->Right->db_GetNumRows();
   //echo '<br>$rows='.$rows;

   $this->field = $this->type3;
   if( $this->type3 == 'page_url' ) $this->field = 'page_url';
   if( $this->type3 == 'module' ) $this->field = 'page';
   if( $this->type3 == 'cntr' ) $this->field = 'country';
   if( $this->type3 == 'lng' ) $this->field = 'lang';
   if( $this->type3 == 'refer_server' ) $this->field = 'refer';
   if( $this->type3 == 'search_words' ) $this->field = 'refer';

   //--- START Day Type of Report
   if( $this->type2 == 'd' )
   {

    switch( $this->type3 )
    {
     case 'refer_server':
            $this->FromServer_Rep( $rows, 'day' );
            break;

     case 'search_words':
            $this->SearchWords_Rep( $rows, 'day' );
            break;

     default: $this->_Day_Rep( $rows );
    }
   //print_r( $ColItog );
   //sort( $mas );
   if( count( $this->masDays ) )sort( $this->masDays );
   $days = $this->GetCountDays();


       $this->masHead[0] = '*';
       $this->masHead[1] = $this->GetHeadName();

   
   $i = 2;
   if( count( $this->masDays ) )
   while( $el = each( $this->masDays ) )
   {
     $tmp = explode( '-', $el['value'] );
     $this->masHead[$i] = $tmp[2];
     $i = $i + 1;
   }
   }//--- END Day Type of Report


   //--- START WEEK Type of Report

  if( $this->type2 == 'w' )
  {

    switch( $this->type3 )
    {
     case 'refer_server':
            $this->FromServer_Rep( $rows, 'week' );
            break;
     case 'search_words':
            $this->SearchWords_Rep( $rows, 'week' );
            break;

     default: $this->_Week_Rep( $rows );
    }

   //sort( $mas );
   if( count( $this->masDays ) )sort( $this->masDays );
   $days = $this->GetCountDays();

   $this->masHead[0] = '*';
   $this->masHead[1] = $this->GetHeadName();


   $i = 2;
   if( count( $this->masDays ) )
   while( $el = each( $this->masDays ) )
   {
     $this->masHead[$i] = $el['value'];
     $i = $i + 1;
   }
   }//--- END WEEK Type of Report

   //--- START MONTH Type of Report
   if( $this->type2 == 'm' )
   {
    switch( $this->type3 )
    {
     case 'refer_server':
            $this->FromServer_Rep( $rows, 'month' );
            break;
     case 'search_words':
            $this->SearchWords_Rep( $rows, 'month' );
            break;

     default: $this->_Month_Rep( $rows );
    }

   //sort( $mas );
   if( count( $this->masDays ) )sort( $this->masDays );
   $days = $this->GetCountDays();

   $this->masHead[0] = '*';
   $this->masHead[1] = $this->GetHeadName();

   $i = 2;
   if( count( $this->masDays ) )
   while( $el = each( $this->masDays ) )
   {
     $this->masHead[$i] = $el['value'];
     $i = $i + 1;
   }
   }//--- END MONTH Type of Report


   //--- START YEAR Type of Report
   if( $this->type2 == 'y' )
   {
    switch( $this->type3 )
    {
     case 'refer_server':
            $this->FromServer_Rep( $rows, 'year' );
            break;
     case 'search_words':
            $this->SearchWords_Rep( $rows, 'year' );
            break;

     default: $this->_Year_Rep( $rows );
    }

   //print_r( $ColItog );
   //print_r( $RowItog );
   //sort( $mas );
   if( count( $this->masDays ) ) sort( $this->masDays );

   $this->masHead[0] = '*';
   $this->masHead[1] = $this->GetHeadName();

   $i = 2;
   if( count( $this->masDays ) )
   while( $el = each( $this->masDays ) )
   {
     $this->masHead[$i] = $el['value'];
     $i = $i + 1;
   }
   }//--- END YEAR Type of Report

   //print_r( $this->mas );
   //echo '<br>------------';

   //print_r( $this->RowItog );

   /* Write Table Part */
   AdminHTML::TablePartH();
   
   if( !is_array($this->RowItog) ){ 
       ?><div class="err"><?=$this->Msg['SYS_STAT_TXT_ERR_NO_DATA']?> <?=$this->fltr_dtfrom;?> - <?=$this->fltr_dtto;?></div><?
   } 
   else{
       echo '<TR><TD COLSPAN=11>';
       /* Write Links on Pages */
       $this->Form->WriteLinkPages( $script_, count( $this->mas ), $this->display, $this->start, $this->sort );


       /* Write Report Head */
       echo '<TR>';
       echo '<td class="THead" width="12">';
       if( count( $this->masHead ) )
       while( $el = each( $this->masHead ) )
       {
         echo '<td class="THead">'.$el['value'];
       }
       echo '<td class="THead">'.$this->Msg['FLD_SUMA'];

       /* Write Report */
       
       arsort( $this->RowItog );
       $j = 0;
       if( count( $this->mas ) )
       while( $el = each( $this->RowItog ) )
       {
         $tmp = $this->mas[$el['key']];
         if( $j >= $this->start && $j < ( $this->start+$this->display ) )
         {
          if( (float)$j/2 == round( $j/2 ) )
          {
           echo '<TR CLASS="TR1">';
          }else echo '<TR CLASS="TR2">';
          echo '<td>'.($j+1);
          if($this->type3=='page_url'){echo '<td align=left width=5></td><td align=left width=450 height=20>'.$this->GetName( $this->masColumn[$el['key']] );}
          else echo '<td align=left>'.urldecode( $this->masColumn[$el['key']] ).'<td width=150 height=20>'.$this->GetName( $this->masColumn[$el['key']] );
          reset( $this->masDays );
          while( $el1 = each( $this->masDays ) )
          {
           echo '<td align=center>';
           if( isset( $tmp[$el1['value']] )) echo $tmp[$el1['value']];
           else echo '-';
          }
          /* Sum */
          echo '<td align=center>';
          if( isset( $this->RowItog[$el['key']] )) echo $this->RowItog[$el['key']];
          else echo '-';
         }
         $j = $j + 1;
       } // end while


       if( count( $this->ColItog ) ) ksort( $this->ColItog );
       $SUM = 0;
       echo '<TR><td class="THead"> &nbsp;<td class="THead"> &nbsp;<td class="THead"> &nbsp;';
       if( count( $this->ColItog ) )
       while( $el = each( $this->ColItog ) )
       {
         echo '<td class="THead">'.$el['value'];
         $SUM = $SUM + $el['value'];
       }
       echo '<td class="THead">'.$SUM;
   }

   AdminHTML::TablePartF();
  } //--- end of Select()



  // ================================================================================================
  //    Function          : GetHeadName()
  //    Version           : 1.0.0
  //    Date              : 05.04.2005
  //    Parms             :
  //    Returns           : true/false
  //    Description       : Return Head Name  Of Report
  // ================================================================================================

 function GetHeadName()
 {
   switch( $this->type3 )
   {
    case 'page_url':
                    $Head = $this->Msg['SYS_STAT_TXT_PAGES_URL'];
                    break;
    case 'module':
                    $Head = $this->Msg['SYS_STAT_TXT_MODULES'];
                    break;

    case 'user':
                    $Head = $this->Msg['SYS_STAT_TXT_USERS'];
                    break;

    case 'cntr':
                    $Head = $this->Msg['FLD_COUNTRY'];
                    break;

    case 'lng':
                    $Head = $this->Msg['_FLD_LANGUAGE'];
                    break;

    case 'agent':
                    $Head = $this->Msg['SYS_STAT_USER_AGENT'];
                    break;

    default: $Head = $this->field;
   } //--- end of switch

   return $Head;
 } //--- end of GetHeadName();




function utf8_win1251( $a )
{
  if( is_array( $a ) )
  {
        foreach ( $a as $k => $v )
        {
            if( is_array( $v ) )
            {
                $a[$k] = utf8_win1251( $v );
            } else {
                $a[$k] = strtr( $v, $this->_utf8win1251 );
            }
        }
        return $a;
    }
    else
    {
        return strtr( $a, $this->_utf8win1251 );
    }
}

function win1251_utf8( $a )
{
   if( is_array( $a ) )
   {
        foreach( $a as $k=>$v )
        {
            if( is_array( $v ) )
            {
                $a[$k] = utf8_win1251( $v );
            } else {
                $a[$k] = strtr( $v, $this->_win1251utf8 );
            }
        }
        return $a;
    }
    else
    {
        return strtr( $a, $this->_win1251utf8 );
    }
}




   /*
  This seems to decode correctly between most browsers and charater coding configurations.
  Specially indicated for direct parsing of URL as it comes on environment variables:
  */

 function crossUrlDecode( $source )
 {
   $decodedStr = '';
   $pos = 0;
   $len = strlen($source);

   while ($pos < $len) {
       $charAt = substr ($source, $pos, 1);
       if ($charAt == '?') {
           $char2 = substr($source, $pos, 2);
           $decodedStr .= htmlentities(utf8_decode($char2),ENT_QUOTES,'ISO-8859-1');
           $pos += 2;
       }
       elseif(ord($charAt) > 127) {
           $decodedStr .= "&#".ord($charAt).";";
           $pos++;
       }
       elseif($charAt == '%') {
           $pos++;
           $hex2 = substr($source, $pos, 2);
           $dechex = chr(hexdec($hex2));
           if($dechex == '?') {
               $pos += 2;
               if(substr($source, $pos, 1) == '%') {
                   $pos++;
                   $char2a = chr(hexdec(substr($source, $pos, 2)));
                   $decodedStr .= htmlentities(utf8_decode($dechex . $char2a),ENT_QUOTES,'ISO-8859-1');
               }
               else {
                   $decodedStr .= htmlentities(utf8_decode($dechex));
               }
           }
           else {
               $decodedStr .= $dechex;
           }
           $pos += 2;
       }
       else {
           $decodedStr .= $charAt;
           $pos++;
       }
   }

   return $decodedStr;
 } //--- end of function






 function _Day_Rep( $rows )
 {
   $this->mas = NULL;
   $this->masHead = NULL;
   $this->masDays = NULL;
   $this->RowItog = NULL;
   $this->ColItog = NULL;

   for( $i = 0; $i < $rows; $i++ )
   {
     $row = $this->Right->db_FetchAssoc();
     $index = $row['dt'];

     //--- standart report
     $row[$this->field] = $this->setEncoding( $row[$this->field] );

     if( !isset( $this->mas[$row[$this->field]][$index] ) )
        $this->mas[$row[$this->field]][$index] = $row['cnt'];
     else $this->mas[$row[$this->field]][$index] = $this->mas[$row[$this->field]][$index] + $row['cnt'];

     $this->masDays[$index] = $row['dt'];
     $this->masColumn[$row[$this->field]] = $row[$this->field];

     if( !isset( $this->ColItog[$index] ) ) $this->ColItog[$index] = $row['cnt'];
     else $this->ColItog[$index] = $this->ColItog[$index] + $row['cnt'];

     if( !isset( $this->RowItog[$row[$this->field]] ) ) $this->RowItog[$row[$this->field]] = $row['cnt'];
     else $this->RowItog[$row[$this->field]] = $this->RowItog[$row[$this->field]] + $row['cnt'];


   } //--- end of _Day_Rep
 }



 function _Week_Rep( $rows )
 {
   $this->mas = NULL;
   $this->masHead = NULL;
   $this->masDays = NULL;
   $this->RowItog = NULL;
   $this->ColItog = NULL;

   for( $i = 0; $i < $rows; $i++ )
   {
     $row = $this->Right->db_FetchAssoc();
     $dtarr = $row['dt'];
     $dtarr = explode( '-', $dtarr );

     $week = Date_Calc::getCalendarWeek( $dtarr[2], $dtarr[1], $dtarr[0], "%Y.%m.%d" );
     $index = $week[0].' - '.$week[ ( count( $week ) - 1 ) ];

     if( !isset( $this->mas[$row[$this->field]][$index] ) )
        $this->mas[$row[$this->field]][$index] = $row['cnt'];
     else $this->mas[$row[$this->field]][$index] = $this->mas[$row[$this->field]][$index] + $row['cnt'];

     $this->masDays[$index] = $index;
     $this->masColumn[$row[$this->field]] = $row[$this->field];

      if( !isset( $this->ColItog[$index] ) ) $this->ColItog[$index] = $row['cnt'];
      else $this->ColItog[$index] = $this->ColItog[$index] + $row['cnt'];

      if( !isset( $this->RowItog[$row[$this->field]] ) ) $this->RowItog[$row[$this->field]] = $row['cnt'];
      else $this->RowItog[$row[$this->field]] = $this->RowItog[$row[$this->field]] + $row['cnt'];

   }
 } //---  End of _Week_Rep


 function _Month_Rep( $rows )
 {
   $this->mas = NULL;
   $this->masHead = NULL;
   $this->masDays = NULL;
   $this->RowItog = NULL;
   $this->ColItog = NULL;

   for( $i = 0; $i < $rows; $i++ )
   {
     $row = $this->Right->db_FetchAssoc();
     $dtarr = $row['dt'];
     $dtarr = explode( '-', $dtarr );
     $month = NULL;
     $index = $dtarr[1].'-'.$dtarr[0];

      if( !isset( $this->mas[$row[$this->field]][$index] ) )
        $this->mas[$row[$this->field]][$index] = $row['cnt'];
      else $this->mas[$row[$this->field]][$index] = $this->mas[$row[$this->field]][$index] + $row['cnt'];

      $this->masDays[$index] = $index;
      $this->masColumn[$row[$this->field]] = $row[$this->field];

      if( !isset( $this->ColItog[$index] ) ) $this->ColItog[$index] = $row['cnt'];
      else $this->ColItog[$index] = $this->ColItog[$index] + $row['cnt'];

      if( !isset( $this->RowItog[$row[$this->field]] ) ) $this->RowItog[$row[$this->field]] = $row['cnt'];
      else $this->RowItog[$row[$this->field]] = $this->RowItog[$row[$this->field]] + $row['cnt'];
   }
 } //--- end of  _Month_Rep



 function _Year_Rep( $rows )
 {
   $this->mas = NULL;
   $this->masHead = NULL;
   $this->masDays = NULL;
   $this->RowItog = NULL;
   $this->ColItog = NULL;

   for( $i = 0; $i < $rows; $i++ )
   {
     $row = $this->Right->db_FetchAssoc();
     $dtarr = $row['dt'];
     $dtarr = explode( '-', $dtarr );
     $index = $dtarr[0];

      if( !isset( $this->mas[$row[$this->field]][$index] ) )
        $this->mas[$row[$this->field]][$index] = $row['cnt'];
      else $this->mas[$row[$this->field]][$index] = $this->mas[$row[$this->field]][$index] + $row['cnt'];

      $this->masDays[$index] = $index;
      $this->masColumn[$row[$this->field]] = $row[$this->field];

      if( !isset( $this->ColItog[$index] ) ) $this->ColItog[$index] = $row['cnt'];
      else $this->ColItog[$index] = $this->ColItog[$index] + $row['cnt'];

      if( !isset( $this->RowItog[$row[$this->field]] ) ) $this->RowItog[$row[$this->field]] = $row['cnt'];
      else $this->RowItog[$row[$this->field]] = $this->RowItog[$row[$this->field]] + $row['cnt'];

   }
 } //--- end of _Year_Rep



  // ================================================================================================
  //    Function          : FromServer_Rep()
  //    Version           : 1.0.0
  //    Date              : 07.11.2005
  //    Parms             :
  //    Returns           : true/false
  //    Description       : From Server Report
  // ================================================================================================

 function FromServer_Rep( $rows, $tp = 'day' )
 {
   $this->mas = NULL;
   $this->masHead = NULL;
   $this->masDays = NULL;
   $this->RowItog = NULL;
   $this->ColItog = NULL;

   for( $i = 0; $i < $rows; $i++ )
   {
     $row = $this->Right->db_FetchAssoc();
     if( $row[$this->field] )
     {
      $tmp = explode( '//', $row[$this->field] );
      if( isset( $tmp[1] ) )
      {
       $tmp2 = explode( '/', $tmp[1] );
       $serv = $tmp2[0];
       //echo '<br>'.$tmp2[0];
       if( strstr( $tmp2[0], 'www.' ) )
       {
        $tmp3 = explode( 'www.', $tmp2[0] );
        $serv = $tmp3[1];
       }
       $row[$this->field] = $serv;
      }


    if( $tp == 'day' )
    {
     $index = $row['dt'];
     if( !isset( $this->mas[$row[$this->field]][$index] ) )
        $this->mas[$row[$this->field]][$index] = $row['cnt'];
     else $this->mas[$row[$this->field]][$index] = $this->mas[$row[$this->field]][$index] + $row['cnt'];

     $this->masDays[$index] = $row['dt'];
     $this->masColumn[$row[$this->field]] = $row[$this->field];

     if( !isset( $this->ColItog[$index] ) ) $this->ColItog[$index] = $row['cnt'];
     else $this->ColItog[$index] = $this->ColItog[$index] + $row['cnt'];

     if( !isset( $this->RowItog[$row[$this->field]] ) ) $this->RowItog[$row[$this->field]] = $row['cnt'];
     else $this->RowItog[$row[$this->field]] = $this->RowItog[$row[$this->field]] + $row['cnt'];
    }


    if( $tp == 'week' )
    {
     $dtarr = $row['dt'];
     $dtarr = explode( '-', $dtarr );

     $week = Date_Calc::getCalendarWeek( $dtarr[2], $dtarr[1], $dtarr[0], "%Y.%m.%d" );
     $index = $week[0].' - '.$week[ ( count( $week ) - 1 ) ];

     if( !isset( $this->mas[$row[$this->field]][$index] ) )
        $this->mas[$row[$this->field]][$index] = $row['cnt'];
     else $this->mas[$row[$this->field]][$index] = $this->mas[$row[$this->field]][$index] + $row['cnt'];

     $this->masDays[$index] = $index;
     $this->masColumn[$row[$this->field]] = $row[$this->field];

      if( !isset( $this->ColItog[$index] ) ) $this->ColItog[$index] = $row['cnt'];
      else $this->ColItog[$index] = $this->ColItog[$index] + $row['cnt'];

      if( !isset( $this->RowItog[$row[$this->field]] ) ) $this->RowItog[$row[$this->field]] = $row['cnt'];
      else $this->RowItog[$row[$this->field]] = $this->RowItog[$row[$this->field]] + $row['cnt'];
    }

    if( $tp == 'month' )
    {
     $dtarr = $row['dt'];
     $dtarr = explode( '-', $dtarr );
     $month = NULL;
     $index = $dtarr[1].'-'.$dtarr[0];

      if( !isset( $this->mas[$row[$this->field]][$index] ) )
        $this->mas[$row[$this->field]][$index] = $row['cnt'];
      else $this->mas[$row[$this->field]][$index] = $this->mas[$row[$this->field]][$index] + $row['cnt'];

      $this->masDays[$index] = $index;
      $this->masColumn[$row[$this->field]] = $row[$this->field];

      if( !isset( $this->ColItog[$index] ) ) $this->ColItog[$index] = $row['cnt'];
      else $this->ColItog[$index] = $this->ColItog[$index] + $row['cnt'];

      if( !isset( $this->RowItog[$row[$this->field]] ) ) $this->RowItog[$row[$this->field]] = $row['cnt'];
      else $this->RowItog[$row[$this->field]] = $this->RowItog[$row[$this->field]] + $row['cnt'];
    }

    if( $tp == 'year' )
    {
     $dtarr = $row['dt'];
     $dtarr = explode( '-', $dtarr );
     $index = $dtarr[0];

      if( !isset( $this->mas[$row[$this->field]][$index] ) )
        $this->mas[$row[$this->field]][$index] = $row['cnt'];
      else $this->mas[$row[$this->field]][$index] = $this->mas[$row[$this->field]][$index] + $row['cnt'];

      $this->masDays[$index] = $index;
      $this->masColumn[$row[$this->field]] = $row[$this->field];

      if( !isset( $this->ColItog[$index] ) ) $this->ColItog[$index] = $row['cnt'];
      else $this->ColItog[$index] = $this->ColItog[$index] + $row['cnt'];

      if( !isset( $this->RowItog[$row[$this->field]] ) ) $this->RowItog[$row[$this->field]] = $row['cnt'];
      else $this->RowItog[$row[$this->field]] = $this->RowItog[$row[$this->field]] + $row['cnt'];
    }

   } //--- end if
  } //--- end for
 } //--- end of FromServerRep




  // ================================================================================================
  //    Function          : SearchWords_Rep()
  //    Version           : 1.0.0
  //    Date              : 07.11.2005
  //    Parms             :
  //    Returns           : true/false
  //    Description       : Search Words Report
  // ================================================================================================

 function SearchWords_Rep( $rows, $tp = 'day' )
 {
   $this->mas = NULL;
   $this->masHead = NULL;
   $this->masDays = NULL;
   $this->RowItog = NULL;
   $this->ColItog = NULL;

   $host_q['google'] = 'q=';
   $host_q['rambler'] = 'words=';
   $host_q['yandex'] = 'text=';
   $host_q['meta'] = 'q=';
   $host_q['msn'] = 'q=';
   $host_q['mysearch-in'] = 'search=';
   $host_q['aport'] = 'r=';
   $host_q['mail'] = 'q=';
   $host_q['uaportal'] = 'keywords=';
   $host_q['find-it'] = 'REQ=';

   $serv = NULL;
   $str = NULL;
   $s = NULL;
   for( $i = 0; $i < $rows; $i++ )
   {
     $row = $this->Right->db_FetchAssoc();
     if( $row[$this->field] )
     {
      $row[$this->field] = urldecode( urldecode( $row[$this->field] ) );
      $row[$this->field] = $this->setEncoding( $row[$this->field] );

      reset( $host_q );
      $serv = NULL;
      $str = NULL;
      $s = NULL;
      while( $el = each( $host_q ) )
      {
       if( strstr( $row[$this->field], $el['key'] ) )
       {
         $serv = explode( $el['value'], $row[$this->field] );
         if( isset( $serv[1] ) )
         {
          $s = explode( '&', $serv[1] );
          if( $s[0] ) $str = trim( $s[0] );
         }
       }
      }
     }

   if( trim( $str )!='' )
   {
    $row[$this->field] = trim( $str );
    if( $tp == 'day' )
    {
     $index = $row['dt'];
     if( !isset( $this->mas[$row[$this->field]][$index] ) )
        $this->mas[$row[$this->field]][$index] = $row['cnt'];
     else $this->mas[$row[$this->field]][$index] = $this->mas[$row[$this->field]][$index] + $row['cnt'];

     $this->masDays[$index] = $row['dt'];
     $this->masColumn[$row[$this->field]] = $row[$this->field];

     if( !isset( $this->ColItog[$index] ) ) $this->ColItog[$index] = $row['cnt'];
     else $this->ColItog[$index] = $this->ColItog[$index] + $row['cnt'];

     if( !isset( $this->RowItog[$row[$this->field]] ) ) $this->RowItog[$row[$this->field]] = $row['cnt'];
     else $this->RowItog[$row[$this->field]] = $this->RowItog[$row[$this->field]] + $row['cnt'];
    }


    if( $tp == 'week' )
    {
     $dtarr = $row['dt'];
     $dtarr = explode( '-', $dtarr );

     $week = Date_Calc::getCalendarWeek( $dtarr[2], $dtarr[1], $dtarr[0], "%Y.%m.%d" );
     $index = $week[0].' - '.$week[ ( count( $week ) - 1 ) ];

     if( !isset( $this->mas[$row[$this->field]][$index] ) )
        $this->mas[$row[$this->field]][$index] = $row['cnt'];
     else $this->mas[$row[$this->field]][$index] = $this->mas[$row[$this->field]][$index] + $row['cnt'];

     $this->masDays[$index] = $index;
     $this->masColumn[$row[$this->field]] = $row[$this->field];

      if( !isset( $this->ColItog[$index] ) ) $this->ColItog[$index] = $row['cnt'];
      else $this->ColItog[$index] = $this->ColItog[$index] + $row['cnt'];

      if( !isset( $this->RowItog[$row[$this->field]] ) ) $this->RowItog[$row[$this->field]] = $row['cnt'];
      else $this->RowItog[$row[$this->field]] = $this->RowItog[$row[$this->field]] + $row['cnt'];
    }

    if( $tp == 'month' )
    {
     $dtarr = $row['dt'];
     $dtarr = explode( '-', $dtarr );
     $month = NULL;
     $index = $dtarr[1].'-'.$dtarr[0];

      if( !isset( $this->mas[$row[$this->field]][$index] ) )
        $this->mas[$row[$this->field]][$index] = $row['cnt'];
      else $this->mas[$row[$this->field]][$index] = $this->mas[$row[$this->field]][$index] + $row['cnt'];

      $this->masDays[$index] = $index;
      $this->masColumn[$row[$this->field]] = $row[$this->field];

      if( !isset( $this->ColItog[$index] ) ) $this->ColItog[$index] = $row['cnt'];
      else $this->ColItog[$index] = $this->ColItog[$index] + $row['cnt'];

      if( !isset( $this->RowItog[$row[$this->field]] ) ) $this->RowItog[$row[$this->field]] = $row['cnt'];
      else $this->RowItog[$row[$this->field]] = $this->RowItog[$row[$this->field]] + $row['cnt'];
    }

    if( $tp == 'year' )
    {
     $dtarr = $row['dt'];
     $dtarr = explode( '-', $dtarr );
     $index = $dtarr[0];

      if( !isset( $this->mas[$row[$this->field]][$index] ) )
        $this->mas[$row[$this->field]][$index] = $row['cnt'];
      else $this->mas[$row[$this->field]][$index] = $this->mas[$row[$this->field]][$index] + $row['cnt'];

      $this->masDays[$index] = $index;
      $this->masColumn[$row[$this->field]] = $row[$this->field];

      if( !isset( $this->ColItog[$index] ) ) $this->ColItog[$index] = $row['cnt'];
      else $this->ColItog[$index] = $this->ColItog[$index] + $row['cnt'];

      if( !isset( $this->RowItog[$row[$this->field]] ) ) $this->RowItog[$row[$this->field]] = $row['cnt'];
      else $this->RowItog[$row[$this->field]] = $this->RowItog[$row[$this->field]] + $row['cnt'];
    }

   } //--- end if
  } //--- end for

 } //--- end of SearchWords_Rep








 function setEncoding( $row )
 {
     if( strstr( $row, 'msn.' ) )
     {
      $row = $this->utf8_win1251( urldecode ( $row ) );
     }

     if( strstr( $row, 'google.' ) )
     {
      $row = $this->utf8_win1251( urldecode ( $row ) );
     }

     if( strstr( $row, 'find-it.' ) )
     {
      $row = $this->utf8_win1251( urldecode ( $row ) );
     }

     return $row;
 }



  // ================================================================================================
  //    Function          : Statistic()
  //    Version           : 1.0.0
  //    Date              : 20.12.2005
  //    Parms             :
  //    Returns           : true/false
  //    Description       : Statistic Report
  // ================================================================================================

 function Statistic()
 {
   $db = new DB();
   $q = "select sum(cnt) as cnt from ".TblModStatLog." where 1 ";
   $res = $this->Right->Query( $q, $this->user_id, $this->module );
   $row = $this->Right->db_FetchAssoc();
   /* Write Table Part */
   AdminHTML::TablePartH();

   echo '<TR><TD>';

   echo '<h2>'.$this->Msg['SYS_STAT_TXT_ATTENDANCE'].'</h2>';
   echo '<table border=0 cellpadding=5>';
   echo '<tr class="TR1"><td align=left><b>'.$this->Msg['SYS_STAT_TXT_SITE_STATISTIC_ON'].' <td align=right>'.Date_Calc::dateNow("%Y-%m-%d").'</b>';
   echo '<tr class="TR2"><td align=left>'.$this->Msg['SYS_STAT_TXT_TOTAL_REQUESTS'].':<td align=right>'.$row['cnt'];

   $q = "select 1 as cnt,dt,ip from ".TblModStatLog." group by ip,dt";
   $res = $this->Right->Query( $q, $this->user_id, $this->module );
   $rows = $this->Right->db_GetNumRows();
   echo '<tr class="TR1"><td align=left>'.$this->Msg['SYS_STAT_TXT_TOTAL_VISITORS'].':<td align=right>'.$rows;

   $q = "select dt, tm from ".TblModStatLog." WHERE `target`='1' ORDER BY `id` desc LIMIT 1";
   $res = $this->Right->Query( $q, $this->user_id, $this->module );
   $row = $this->Right->db_FetchAssoc();
   echo '<tr class="TR2"><td align=left>'.$this->Msg['SYS_STAT_TXT_LAST_TIME_SITE_PAGE_DOWNLOAD'].':<td align=right>'.$row['dt'].' '.$row['tm'];
   echo '</table>';

   echo '<h3>'.$this->Msg['TXT_DETAIL_STATISTIC'].'</h3>';
   echo '<table border=0 cellpadding=5>';
   echo '<tr class="THead"><td>'.$this->Msg['SYS_STAT_TXT_INDICATOR'].'<td>'.$this->Msg['SYS_STAT_TXT_STATS_TODAY'].'<td>'.$this->Msg['SYS_STAT_TXT_STATS_YESTERDAY'].'<td>'.$this->Msg['SYS_STAT_TXT_STATS_BY_7_DAYS'].'<td>'.$this->Msg['SYS_STAT_TXT_STATS_BY_30_DAYS'].'<td>'.$this->Msg['SYS_STAT_TXT_STATS_ALL'];


   echo '<tr class="TR1"><td>'.$this->Msg['SYS_STAT_TXT_VISITORS'];
   echo '<td>';
   $days = Date_Calc::dateToDays( Date_Calc::getDay(), Date_Calc::getMonth(), Date_Calc::getYear() );
   $dt = Date_Calc::daysToDate( $days, "%Y-%m-%d" );
   $q = "select 1 as cnt,dt,ip from ".TblModStatLog." where dt='$dt' group by ip,dt";
   //echo '<br>$q='.$q.' $res='.$res; 
   $res = $this->Right->Query( $q, $this->user_id, $this->module );
   $host_today = $this->Right->db_GetNumRows();
   //echo '<br>$host_today='.$host_today;
   echo $host_today;

   echo '<td>';
   $days = Date_Calc::dateToDays( Date_Calc::getDay(), Date_Calc::getMonth(), Date_Calc::getYear() );
   $dt = Date_Calc::daysToDate( $days-1, "%Y-%m-%d" );
   $q = "select 1 as cnt,dt,ip from ".TblModStatLog." where dt='$dt' group by ip,dt";
   $res = $this->Right->Query( $q, $this->user_id, $this->module );
   $host_1 = $this->Right->db_GetNumRows();
   echo $host_1;

   echo '<td>';
   $days = Date_Calc::dateToDays( Date_Calc::getDay(), Date_Calc::getMonth(), Date_Calc::getYear() );
   $dt = Date_Calc::daysToDate( $days-7, "%Y-%m-%d" );
   $q = "select 1 as cnt,dt,ip from ".TblModStatLog." where dt>='$dt' group by ip,dt";
   $res = $this->Right->Query( $q, $this->user_id, $this->module );
   $host_7 = $this->Right->db_GetNumRows();
   echo $host_7;

   echo '<td>';
   $days = Date_Calc::dateToDays( Date_Calc::getDay(), Date_Calc::getMonth(), Date_Calc::getYear() );
   $dt = Date_Calc::daysToDate( $days-30, "%Y-%m-%d" );
   $q = "select 1 as cnt,dt,ip from ".TblModStatLog." where dt>='$dt' group by ip,dt";
   $res = $this->Right->Query( $q, $this->user_id, $this->module );
   $host_30 = $this->Right->db_GetNumRows();
   echo $host_30;

   echo '<td>';
   $q = "select 1 as cnt,dt,ip from ".TblModStatLog." group by ip,dt";
   $res = $this->Right->Query( $q, $this->user_id, $this->module );
   $host = $this->Right->db_GetNumRows();
   echo $host;

   echo '<tr class="TR2"><td>'.$this->Msg['SYS_STAT_TXT_HITS'];
   echo '<td>';
   $days = Date_Calc::dateToDays( Date_Calc::getDay(), Date_Calc::getMonth(), Date_Calc::getYear() );
   $dt = Date_Calc::daysToDate( $days, "%Y-%m-%d" );
   $q = "select sum(cnt) as cnt from ".TblModStatLog." where dt='$dt' ";
   $res = $this->Right->Query( $q, $this->user_id, $this->module );
   $row = $this->Right->db_FetchAssoc();
   $hit_today = $row['cnt'];
   echo intval( $hit_today );

   echo '<td>';
   $days = Date_Calc::dateToDays( Date_Calc::getDay(), Date_Calc::getMonth(), Date_Calc::getYear() );
   $dt = Date_Calc::daysToDate( $days-1, "%Y-%m-%d" );
   $q = "select sum(cnt) as cnt from ".TblModStatLog." where dt='$dt' ";
   $res = $this->Right->Query( $q, $this->user_id, $this->module );
   $row = $this->Right->db_FetchAssoc();
   $hit_1 = intval( $row['cnt'] );
   echo  $hit_1;

   echo '<td>';
   $days = Date_Calc::dateToDays( Date_Calc::getDay(), Date_Calc::getMonth(), Date_Calc::getYear() );
   $dt = Date_Calc::daysToDate( $days-7, "%Y-%m-%d" );
   $q = "select sum(cnt) as cnt from ".TblModStatLog." where dt>='$dt' ";
   $res = $this->Right->Query( $q, $this->user_id, $this->module );
   $row = $this->Right->db_FetchAssoc();
   $hit_7 = intval( $row['cnt'] );
   echo  $hit_7;

   echo '<td>';
   $days = Date_Calc::dateToDays( Date_Calc::getDay(), Date_Calc::getMonth(), Date_Calc::getYear() );
   $dt = Date_Calc::daysToDate( $days-30, "%Y-%m-%d" );
   $q = "select sum(cnt) as cnt from ".TblModStatLog." where dt>='$dt' ";
   $res = $this->Right->Query( $q, $this->user_id, $this->module );
   $row = $this->Right->db_FetchAssoc();
   $hit_30 = intval( $row['cnt'] );
   echo  $hit_30;

   echo '<td>';
   $q = "select sum(cnt) as cnt from ".TblModStatLog." ";
   $res = $this->Right->Query( $q, $this->user_id, $this->module );
   $row = $this->Right->db_FetchAssoc();
   $hit = intval( $row['cnt'] );
   echo  $hit;

   echo '<tr class="TR1"><td>'.$this->Msg['SYS_STAT_TXT_HITS_HOSTS'];
   echo '<td>';
   if( $host_today != 0 ) echo number_format( $hit_today/$host_today, 2 );
   echo '<td>';
   if( $host_1 != 0 ) echo number_format( $hit_1/$host_1, 2 );
   echo '<td>';
   if( $host_7 != 0 ) echo number_format( $hit_7/$host_7, 2 );
   echo '<td>';
   if( $host_30 != 0 ) echo number_format( $hit_30/$host_30, 2 );
   echo '<td>';
   if( $host != 0 ) echo number_format( $hit/$host, 2 );

   echo '<tr class="TR2"><td>'.$this->Msg['SYS_STAT_TXT_REFERRALS'];
   echo '<td>';
   $days = Date_Calc::dateToDays( Date_Calc::getDay(), Date_Calc::getMonth(), Date_Calc::getYear() );
   $dt = Date_Calc::daysToDate( $days, "%Y-%m-%d" );
   $q = "select count(refer) as refer from ".TblModStatLog." where refer!='' and dt='$dt' ";
   $res = $this->Right->Query( $q, $this->user_id, $this->module );
   $row = $this->Right->db_FetchAssoc();
   echo  $row['refer'];

   echo '<td>';
   $days = Date_Calc::dateToDays( Date_Calc::getDay(), Date_Calc::getMonth(), Date_Calc::getYear() );
   $dt = Date_Calc::daysToDate( $days-1, "%Y-%m-%d" );
   $q = "select count(refer) as refer from ".TblModStatLog." where refer!='' and dt='$dt' ";
   $res = $this->Right->Query( $q, $this->user_id, $this->module );
   $row = $this->Right->db_FetchAssoc();
   echo  $row['refer'];

   echo '<td>';
   $days = Date_Calc::dateToDays( Date_Calc::getDay(), Date_Calc::getMonth(), Date_Calc::getYear() );
   $dt = Date_Calc::daysToDate( $days-7, "%Y-%m-%d" );
   $q = "select count(refer) as refer from ".TblModStatLog." where refer!='' and dt>='$dt' ";
   $res = $this->Right->Query( $q, $this->user_id, $this->module );
   $row = $this->Right->db_FetchAssoc();
   echo  $row['refer'];

   echo '<td>';
   $days = Date_Calc::dateToDays( Date_Calc::getDay(), Date_Calc::getMonth(), Date_Calc::getYear() );
   $dt = Date_Calc::daysToDate( $days-30, "%Y-%m-%d" );
   $q = "select count(refer) as refer from ".TblModStatLog." where refer!='' and dt>='$dt' ";
   $res = $this->Right->Query( $q, $this->user_id, $this->module );
   $row = $this->Right->db_FetchAssoc();
   echo  $row['refer'];

   echo '<td>';
   $q = "select count(refer) as refer from ".TblModStatLog." where refer!=''";
   $res = $this->Right->Query( $q, $this->user_id, $this->module );
   $row = $this->Right->db_FetchAssoc();
   echo  $row['refer'];

   echo '</table>';

   AdminHTML::TablePartF();
 } //--- end of function


 } //--- end of class


  function compare( $x, $y )
  {
   echo '<br>------------';
   //echo '<br>x='.$x[0];
   print_r( $x );
   //echo '<br>y='.$y[0];
   if( $x[0] == $y[0] )
    return 0;
   else if( $x[0] < $y[0] )
    return -1;
   else
   return 1;
  }



?>
