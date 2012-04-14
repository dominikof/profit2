<?php
/**
* Class StatCtrl
* Class definition for control Statistic
* @package System Package of SEOCMS
* @author Igor Trokhymchuk  <ihor@seotm.com>
* @version 1.1, 02.04.2012
* @copyright (c) 2005+ by SEOTM
*/
 class StatCtrl extends Stat
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

  public  $fltr;
  public  $fltr_dtfrom;
  public  $fltr_dtto;

  /**
  * StatCtrl::__construct()
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
   $this->Form = &check_init('Form', 'Form', 'form_stat');
   $this->Msg = &check_init_txt('TblBackMulti',TblBackMulti);
   $this->Spr = &check_init('SysSpr', 'SysSpr');

    /* Get Statistic Settings */
   $this->Set = &check_init('StatSet', 'StatSet');
   $this->Right->db_SetConfig( "", $this->Set->db_user, $this->Set->db_pass, $this->Set->db_name, "" );
   $res = $this->Right->db_Select( $this->Set->db_name );
   
   $this->target_name[0]='Back-end';
   $this->target_name[1]='Front-end';
  } //--- end of StatSetCtrl()



  // ================================================================================================
  //    Function          : StatShow()
  //    Version           : 1.0.0
  //    Date              : 14.03.2005
  //    Parms             :
  //    Returns           : true/false
  //    Description       : Show Statistic Log
  // ================================================================================================

  function StatShow()
  {

   $script = 'module='.$this->module.'&display='.$this->display.'&start='.$this->start.'&sort='.$this->sort.'&fltr='.$this->fltr;
   $script = $_SERVER['PHP_SELF']."?$script";


   if( $this->fltr_dtfrom == NULL ) $this->fltr_dtfrom = $this->GetStartDate();
   if( $this->fltr_dtto == NULL ) $this->fltr_dtto = $this->GetEndDate();

   $q = "SELECT `".TblModStatLog."`.*, `".TblSysSprFunc."`.`name` AS `mod_name`, `".TblSysUser."`.`login`, `".TblModStatAgent."`.`name` AS `agent_name`  
         FROM `".TblModStatLog."`
         LEFT JOIN `".TblSysSprFunc."` ON (`".TblModStatLog."`.`module`=`".TblSysSprFunc."`.`cod` AND `".TblSysSprFunc."`.`lang_id`='"._LANG_ID."')  
         LEFT JOIN `".TblSysUser."` ON (`".TblModStatLog."`.`user`=`".TblSysUser."`.`id`)
         LEFT JOIN `".TblModStatAgent."` ON (`".TblModStatLog."`.`agent`=`".TblModStatAgent."`.`id`)
         WHERE 1 ";
   if( !empty($this->fltr)) $q .= " AND `".TblModStatLog."`.`target`='".$this->fltr."'";
   if( !empty($this->fltr_dtfrom) ) $q = $q." AND `".TblModStatLog."`.`dt`>='".$this->fltr_dtfrom."'";
   if( !empty($this->fltr_dtto) ) $q = $q." AND `".TblModStatLog."`.`dt`<='".$this->fltr_dtto."'";
   if( !empty($this->fltr_user) ) $q = $q." AND `".TblModStatLog."`.`user`='".$this->fltr_user."'";
   

   if( $this->sort )
   {
    $q = $q." order by `".TblModStatLog."`.`$this->sort`";
    if( $this->sort == 'dt' ) $q = $q." desc";
    $q = $q.",cnt desc";
   }
   else{
     $q = $q." order by `".TblModStatLog."`.`dt` desc, `".TblModStatLog."`.`tm` desc, `".TblModStatLog."`.`cnt` desc";
   }

   $res = $this->Right->Query( $q, $this->user_id, $this->module );
   //echo '<br>$q='.$q.' $res='.$res;
   if( !$res ) return false;
   $rows = $this->Right->db_GetNumRows();
   //echo '<br>$rows='.$rows;

   $a = $rows;
   $j = 0;
   $row_arr = NULL;
   for( $i = 0; $i < $rows; $i++ )
   {
     $row = $this->Right->db_FetchAssoc();
     if( $i >= $this->start && $i < ( $this->start+$this->display ) )
     {
      $row_arr[$j] = $row;
      $j = $j + 1;
     }
   }

   $script1 = 'module='.$this->module;
   $script1 = $_SERVER['PHP_SELF']."?$script1";

   $this->Form->WriteHeader( $script1 );
   /* Write Table Part */
   AdminHTML::TablePartH();

   echo '<TR><TD>';
   $arr = NULL;
   $arr['']='All';
   $arr['back'] = $this->target_name[0];
   $arr['front'] = $this->target_name[1];

   echo '<table class="EditTable" border=0 align=center>';
   echo '<tr><td>';
    echo '<table class="EditTable" border=0 align=center>';
    echo '<tr><td>';
     $script000 = $script."&fltr_dtfrom=".$this->fltr_dtfrom."&fltr_dtto=".$this->fltr_dtto;
     //$this->Form->SelectAct( $arr, 'fltr', $this->fltr, "onChange=\"location='".$script000."&fltr='+this.value\"" );
     $this->Form->Select($arr, 'fltr', $this->fltr);
    echo '</table>';
   echo '<TD>';
    echo '<table class="EditTable" border=0 align=center>';
    echo '<tr><td>';
     echo $this->Msg['FLD_START_DATE'];
    echo '<td>';
     $this->Form->TextBox( 'fltr_dtfrom', $this->fltr_dtfrom, 10 );
    echo '<tr><td>';
     echo $this->Msg['FLD_END_DATE'];
    echo '<td>';
     $this->Form->TextBox( 'fltr_dtto', $this->fltr_dtto, 10 );
    echo '</table>';
   echo '<td align=center>';
   echo '<INPUT TYPE=submit class="button" VALUE="'.$this->Msg['SYS_STAT_GET_ST'].'">';
   echo '</table>';
   AdminHTML::TablePartF();

   /* Write Table Part */
   AdminHTML::TablePartH();

   echo '<TR><TD COLSPAN=19>';

   $script = $script.'&fltr='.$this->fltr.'&fltr_dtfrom='.$this->fltr_dtfrom.'&fltr_dtto='.$this->fltr_dtto;
   /* Write Links on Pages */
   $this->Form->WriteLinkPages( $script1.'&fltr='.$this->fltr.'&fltr_dtfrom='.$this->fltr_dtfrom.'&fltr_dtto='.$this->fltr_dtto, $rows, $this->display, $this->start, $this->sort );

   echo '<TR><TD COLSPAN=19><div class="topPanel"><div class="SavePanel">';

   $this->Form->WriteTopPanel( $script, 2 );

   $script2 = 'module='.$this->module.'&display='.$this->display.'&start='.$this->start.'&task=show&fltr='.$this->fltr.'&fltr_dtfrom='.$this->fltr_dtfrom.'&fltr_dtto='.$this->fltr_dtto;
   $script2 = $_SERVER['PHP_SELF']."?$script2";
   
   if($rows>$this->display) 
    $ch = $this->display;
   else 
    $ch = $rows;   
?>
</div>
</div>
 <TR>
 <td class="THead"><input value="0" id="cAll" onclick="if (this.value == '1') {unCheckAll(<?=$ch;?>); this.value = '0';} else {checkAll(<?=$ch;?>); this.value = '1';}" type="checkbox"></Th>
 <td class="THead"><A HREF=<?=$script2?>&sort=id><?=$this->Msg['FLD_ID']?></A></Th>
 <td class="THead"><A HREF=<?=$script2?>&sort=target><?=$this->Msg['_FLD_SYS_FUNC_TARGET']?></A></Th>
 <td class="THead"><A HREF=<?=$script2?>&sort=dt><?=$this->Msg['FLD_DT']?></A></Th>
 <td class="THead"><A HREF=<?=$script2?>&sort=tm>[time]</A></Th>
 <td class="THead"><A HREF=<?=$script2?>&sort=page><?=$this->Msg['_FLD_PAGE']?></A></Th>
 <td class="THead"><A HREF=<?=$script2?>&sort=page_url><?=$this->Msg['SYS_STAT_TXT_PAGES_URL']?></A></Th>
 <td class="THead"><A HREF=<?=$script2?>&sort=module><?=$this->Msg['_FLD_MODULE']?></A></Th>
 <td class="THead"><A HREF=<?=$script2?>&sort=cnt><?=$this->Msg['FLD_QUANTITY']?></A></Th>
 <td class="THead"><A HREF=<?=$script2?>&sort=refer><?=$this->Msg['SYS_STAT_REFER']?></A></Th>
 <td class="THead"><A HREF=<?=$script2?>&sort=time_gen><?=$this->Msg['SYS_STAT_TIME_GEN']?></A></Th>
 <td class="THead"><A HREF=<?=$script2?>&sort=ip><?=$this->Msg['SYS_STAT_IP']?></A></Th>
 <td class="THead"><A HREF=<?=$script2?>&sort=host><?=$this->Msg['SYS_STAT_HOST']?></A></Th>
 <td class="THead"><A HREF=<?=$script2?>&sort=proxy><?=$this->Msg['SYS_STAT_PROXY']?></A></Th>
 <td class="THead"><A HREF=<?=$script2?>&sort=user><?=$this->Msg['FLD_USER_ID']?></A></Th>
 <td class="THead"><A HREF=<?=$script2?>&sort=agent><?=$this->Msg['SYS_STAT_USER_AGENT']?></A></Th>
 <td class="THead"><A HREF=<?=$script2?>&sort=screen_res><?=$this->Msg['SYS_STAT_SCREEN_RES']?></A></Th>
 <td class="THead"><A HREF=<?=$script2?>&sort=lang><?=$this->Msg['_FLD_LANGUAGE']?></A></Th>
 <td class="THead"><A HREF=<?=$script2?>&sort=country><?=$this->Msg['FLD_COUNTRY']?></A></Th>

<?


   $style1 = 'TR1';
   $style2 = 'TR2';
   for( $i = 0; $i < count( $row_arr ); $i++ )
   {
    $row = $row_arr[$i];
    if( (float)$i/2 == round( $i/2 ) )
    {
     echo '<TR CLASS="'.$style1.'">';
    }
    else echo '<TR CLASS="'.$style2.'">';

    echo '<TD>';
    $this->Form->CheckBox( "id_del[]", $row['id'], NULL, "check".$i );

    echo '<TD>';
    echo ''.$row['id'];
    //$this->Form->Link( $script."&task=edit&id=".$row['id'], stripslashes( $row['id'] ), $this->Msg['TXT_EDIT'] );

    echo '<td>';
    echo $this->target_name[$row['target']];

    echo '<td>';
    echo $row['dt'];

    echo '<td>';
    echo $row['tm'];    
    
    echo '<td align=left>';
    echo $row['page'];

    echo '<td align=left>';
    echo $row['page_url'];    
    
    echo '<td>';
    echo 'ID '.$row['module'].' <a href="'.$row['page_url'].'">'.stripslashes($row['mod_name']).'</a>';

    echo '<td>';
    echo $row['cnt'];

    echo '<td align=left>';
    $tmp = explode( '?', $row['refer'] );
    echo $tmp[0];

    echo '<td>';
    echo $row['time_gen'];

    echo '<td>';
    echo long2ip($row['ip']);

    echo '<td>';
    echo $row['host'];

    echo '<td>';
    echo $row['proxy'];

    echo '<td>';
    echo $row['user'].'<br>'.$row['login'];

    echo '<td>';
    echo $row['agent_name'];

    echo '<td>';
    echo $row['screen_res'];

    echo '<td>';
    echo $row['lang'];

    echo '<td>';
    echo $row['country'];

   }
   $this->Form->WriteFooter();
   AdminHTML::TablePartF();
  } //--- end of StatShow()



  // ================================================================================================
  //    Function          : StatSave()
  //    Version           : 1.0.0
  //    Date              : 15.03.2005
  //    Parms             :
  //    Returns           : true/false
  //    Description       : Save Statistic Log
  // ================================================================================================

  function StatSave( $module )
  {

  } //--- end of StatSave()



  // ================================================================================================
  // Function : Del()
  // Version : 1.0.0
  // Date : 13.03.2005
  // Parms :   $id - Id Of Record
  // Returns : true,false / Void
  // Description : Del Statistic record in Log-table
  // ================================================================================================
  // Programmer : Andriy Lykhodid
  // Date : 13.03.2005
  // Reason for change : Reason Description / Creation
  // Change Request Nbr:
  // ================================================================================================

  function Del( $id_del = NULL )
  {
    $del = 0;
    $kol = count( $id_del );
    for( $i = 0; $i < $kol; $i++ )
    {
     $u = $id_del[$i];
     $q = "delete from ".TblModStatLog." where id='$u'";
     $res = $this->Right->Query( $q, $this->user_id, $this->module );
     if( !$res )return false;
     if ( $res )
       $del = $del + 1;
    }
    return $del;
  } //--- end of Del


  // ================================================================================================
  //    Function          : TypeOfModule()
  //    Version           : 1.0.0
  //    Date              : 17.03.2005
  //    Parms             :
  //    Returns           : true/false
  //    Description       : Get Type Of Module
  // ================================================================================================

  function TypeOfModule()
  {
    $db = new DB;
  } //--- end of StatSave()


  // ================================================================================================
  //    Function          : GetStartDate()
  //    Version           : 1.0.0
  //    Date              : 05.04.2005
  //    Parms             :
  //    Returns           : true/false
  //    Description       : Return Head Name  Of Report
  // ================================================================================================

 function GetStartDate()
 {
  $q = "SELECT MIN(dt) as dt FROM ".TblModStatLog." where 1 ";
  $res = $this->Right->Query( $q, $this->user_id, $this->module );
  if( !$res ) return false;
  $rows = $this->Right->db_GetNumRows();
  $row = $this->Right->db_FetchAssoc();
  return $row['dt'];
 }


  // ================================================================================================
  //    Function          : GetEndDate()
  //    Version           : 1.0.0
  //    Date              : 05.04.2005
  //    Parms             :
  //    Returns           : true/false
  //    Description       : Return Head Name  Of Report
  // ================================================================================================

 function GetEndDate()
 {
  $q = "SELECT MAX(dt) as dt FROM ".TblModStatLog." where 1 ";
  $res = $this->Right->Query( $q, $this->user_id, $this->module );
  if( !$res ) return false;
  $rows = $this->Right->db_GetNumRows();
  $row = $this->Right->db_FetchAssoc();
  return $row['dt'];
 }

 } // --- end of class


?>
