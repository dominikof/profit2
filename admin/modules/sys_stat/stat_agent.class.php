<?php
/**
* Class Agent
* Class definition for Statistic - moule
* @package System Package of SEOCMS
* @author Igor Trokhymchuk  <ihor@seotm.com>
* @version 1.1, 02.04.2012
* @copyright (c) 2005+ by SEOTM
*/
class Agent {
 public  $id;
 public  $name;
 public  $comments;
 public  $type;
 public  $status;

 public  $Right;
 public  $Form;
 public  $Msg;
 public  $Spr;

 public  $display;
 public  $sort;
 public  $start;

 public  $user_id;
 public  $module;

 public  $fltr;    // filter

    /**
    * Agent::__construct()
    * 
    * @param integer $user_id
    * @param integer $module_id
    * @return void
    */
    function __construct($user_id=NULL, $module=NULL)
    {
     $this->user_id = $user_id;
     $this->module = $module;
     $this->Right =  new Rights($this->user_id, $this->module);                   /* create Rights obect as a property of this class */
     $this->Form = new Form( 'form_agent' );        /* create Form object as a property of this class */
     $this->Msg = new ShowMsg();                   /* create ShowMsg object as a property of this class */
     $this->Spr = new SysSpr( NULL, NULL, NULL, NULL, NULL, NULL, NULL ); /* create SysSpr object as a property of this class */
    }


// ================================================================================================
// Function : show()
// Version : 1.0.0
// Date : 31.12.2005
//
// Parms :
// Returns :     true,false / Void
// Description : Show All Agents
// ================================================================================================
// Programmer : Andriy Lykhodid
// Date : 31.12.2005
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================

function show()
{
 $db = new Rights;
 $frm = new Form('fltr');
 $script = 'module='.$this->module.'&display='.$this->display.'&start='.$this->start.'&sort='.$this->sort.'&fltr='.$this->fltr.'&fln='.$this->fln;
 $script = $_SERVER['PHP_SELF']."?$script";

 if( !$this->sort ) $this->sort='id';
 if( strstr( $this->sort, 'id' ) )$this->sort = $this->sort.'';
 $q = "SELECT * FROM ".TblModStatAgent." where 1 ";
 if( $this->fltr ) $q = $q." and $this->fltr";
 $q = $q." order by $this->sort";

 $res = $this->Right->QueryResult( $q, $this->user_id, $this->module );
 if( !isset($res) )return false;

 $rows = count($res);

 /* Write Form Header */
 $this->Form->WriteHeader( $script );

 /* Write Table Part */
 AdminHTML::TablePartH();

 /* Write Links on Pages */
 echo '<TR><TD COLSPAN=13>';
 $script1 = 'module='.$this->module.'&fltr='.$this->fltr;
 $script1 = $_SERVER['PHP_SELF']."?$script1";
 $this->Form->WriteLinkPages( $script1, $rows, $this->display, $this->start, $this->sort );

 echo '<TR><TD COLSPAN=13><div class="topPanel"><div class="SavePanel">';
 $this->Form->WriteTopPanel( $script );

 echo '</div><div class="SelectType">';
 $arr = NULL;
 $arr[''] = 'All';
 $arr['type=user'] = 'User';
 $arr['type=index'] = 'Robots,Index';
 $this->Form->SelectAct( $arr, 'id_type', $this->fltr, "onChange=\"location='$script'+'&fltr='+this.value\"" );


 echo '</div></div><td><td><td colspan=2>';

 $script2 = 'module='.$this->module.'&display='.$this->display.'&start='.$this->start.'&task=show&fltr='.$this->fltr;
 $script2 = $_SERVER['PHP_SELF']."?$script2";
?>
 <TR>
 <td class="THead">*</Th>
 <td class="THead"><A HREF=<?=$script2?>&sort=id><?=$this->Msg->show_text('FLD_ID')?></A></Th>
 <td class="THead"><A HREF=<?=$script2?>&sort=type>Type</A></Th>
 <td class="THead">Name</Th>
 <td class="THead">Comments</Th>
 <td class="THead">Status in Statistic</Th>
 <?
 $up = 0;
 $down = 0;
 $id = 0;

 $a = $rows;
 $j = 0;
 $row_arr = NULL;
 for( $i = 0; $i < $rows; $i++ )
 {
   $row = $res[$i];
   if( $i >= $this->start && $i < ( $this->start+$this->display ) )
   {
     $row_arr[$j] = $row;
     $j = $j + 1;
   }
 }

 $style1 = 'TR1';
 $style2 = 'TR2';
 for( $i = 0; $i < count( $row_arr ); $i++ )
 {
   $row = $row_arr[$i];

   if ( (float)$i/2 == round( $i/2 ) )
   {
    echo '<TR CLASS="'.$style1.'">';
   }
   else echo '<TR CLASS="'.$style2.'">';

   echo '<TD>';
   $this->Form->CheckBox( "id_del[]", $row['id'] );

   echo '<TD>';
   $this->Form->Link( $script."&task=edit&id=".$row['id'], stripslashes( $row['id'] ), $this->Msg->show_text('TXT_EDIT') );

   echo '<TD align=center>'.$row['type'];

   echo '<TD align=left>'.$row['name'];

   echo '<TD align=left>'.$row['comments'];

   echo '<TD align=center>'.$row['status'];

 } //-- end for

 AdminHTML::TablePartF();
 $this->Form->WriteFooter();
 return true;
}


// ================================================================================================
// Function : edit()
// Version : 1.0.0
// Date : 31.12.2005
//
// Parms :
// Returns : true,false / Void
// Description : edit/add Agent records
// ================================================================================================
// Programmer : Andriy Lykhodid
// Date : 31.12.2005
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================

function edit( $mas=NULL )
{
 $Panel = new Panel();
 $ln_sys = new SysLang();

 $fl = NULL;

 if( $mas )
 {
  $fl = 1;
 }

 $script = 'module='.$this->module.'&display='.$this->display.'&start='.$this->start.'&sort='.$this->sort.'&fltr='.$this->fltr;
 $script = $_SERVER['PHP_SELF']."?$script";

 if( $this->id != NULL and ( $mas == NULL ) )
 {
   $q = "SELECT * FROM ".TblModStatAgent." where id='$this->id'";
   $res = $this->Right->Query( $q, $this->user_id, $this->module );
   if( !$res ) return false;
   $mas = $this->Right->db_FetchAssoc();
 }

 /* Write Form Header */
 $this->Form->WriteHeader( $script );
// $this->Form->IncludeHTMLTextArea();
 if( $this->id!=NULL ) $txt = $this->Msg->show_text('TXT_EDIT');
 else $txt = $this->Msg->show_text('_TXT_ADD_DATA');
 AdminHTML::PanelSubH( $txt );
 AdminHTML::PanelSimpleH();

?>
<table class="PanelSimpleL"><tr><td valign=top width=150 align=left>
<TABLE BORDER=0 class="EditTable">
 <TR><TD><?=$this->Msg->show_text('FLD_ID')?>
 <TD>
<?
   if( $this->id != NULL )
   {
    echo $mas['id'];
    $this->Form->Hidden( 'id', $mas['id'] );
   }
   else $this->Form->Hidden( 'id', '' );
?>
</table>
<tr><td width=650>
<table class="EditTable">
 <TR><TD><b>Agent Name</b>
     <TD>
<?
 if( $this->id != NULL or ( $mas != NULL ) )
 {
  $this->Form->TextArea( 'name', $mas['name'],4,60 );
 }else
  $this->Form->TextArea( 'name', '',4,60 );
?>
 <TR><TD><b>Type of Agent</b>
     <TD>
<?
 $arr = NULL;
  $arr['user'] = 'User';
  $arr['index'] = 'Robots,Index';
  if( !isset( $mas['type'] ) )  $this->Form->Select( $arr, 'type', 0, NULL );
  else $this->Form->Select( $arr, 'type', $mas['type'], NULL );

 ?>
 <TR><TD><b>Status</b>
     <TD>
<?
  $arr = NULL;
  $arr['on'] = "On";
  $arr['off'] = "Off";

  if( !isset( $mas['status'] ) ) $this->Form->Select( $arr, 'status', 'off', NULL );
  else $this->Form->Select( $arr, 'status', $mas['status'], NULL );
?>
 <TR><TD><b>Comments</b>
     <TD>
<?
 if( $this->id != NULL or ( $mas != NULL ) )
 {
  $this->Form->TextBox( 'comments', $mas['comments'], 60 );
 }else
  $this->Form->TextBox( 'comments', '', 60 );
?>

 </table>
</table>
<div class="space"></div>
<?
 $this->Form->WriteSavePanel( $script );
 $this->Form->WriteFooter();
 AdminHTML::PanelSimpleF();
 AdminHTML::PanelSubF();
 return true;
}







// ================================================================================================
// Function : save()
// Version : 1.0.0
// Date : 31.12.2005
// Parms :
// Returns : true,false / Void
// Description : Store Agent
// ================================================================================================
// Programmer : Andriy Lykhodid
// Date : 31.12.2005
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================

function save()
{
   $q = "SELECT * FROM ".TblModStatAgent." WHERE `id`='$this->id'";
   $res = $this->Right->Query( $q, $this->user_id, $this->module );
   if( !$res ) return false;
   $rows = $this->Right->db_GetNumRows();

   if( $rows > 0 )   //--- update
   {
      $q = "update `".TblModStatAgent."` set
           `type`='$this->type',
           `name`='$this->name',
           `comments`='$this->comments',
           `status`='$this->status'
            where id='$this->id'";
     $res = $this->Right->Query( $q, $this->user_id, $this->module );
     if( !$res ) return false;
   }
   else          //--- insert
   {
     $q = "insert into `".TblModStatAgent."` values(NULL, '$this->name', '$this->comments', '$this->type', '$this->status')";
     $res = $this->Right->Query( $q, $this->user_id, $this->module );
     if( !$res ) return false;
   }
 return true;
}





// ================================================================================================
// Function : del()
// Version : 1.0.0
// Date : 31.12.2005
// Parms :
// Returns :      true,false / Void
// Description :  Remove data (Agent-records) from the table
// ================================================================================================
// Programmer :  Andriy Lykhodid
// Date : 31.12.2005
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================

function del( $id_del )
{
    $kol = count( $id_del );
    $del = 0;
    for( $i=0; $i<$kol; $i++ )
    {
     $u = $id_del[$i];
     $q = "DELETE FROM `".TblModStatAgent."` WHERE id='$u'";
     $res = $this->Right->Query( $q, $this->user_id, $this->module );
     if( !$res )return false;
     if( $res )
      $del = $del + 1;
     else
      return false;
    }
  return $del;
}


} //--- end of class
