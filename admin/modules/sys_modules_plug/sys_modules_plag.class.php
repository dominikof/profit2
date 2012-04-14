<?
// ================================================================================================
//    System     : SEOCMS
//    Module     : ModulesPlug
//    Version    : 1.0.0
//    Date       : 02.02.2005
//
//    Purpose    : Class definition for Modules Plug of System
// ================================================================================================


// ================================================================================================
//    Class             : ModulesPlug
//    Version           : 1.0.0
//    Date              : 02.02.2005
//    Constructor       : Yes
//    Parms             :
//    Returns           : None
//    Description       : ModulesPlug
// ================================================================================================
//    Date              :  02.02.2005
//    Reason for change :  Creation
//    Change Request Nbr:  N/A
// ================================================================================================

class ModulesPlug {

 var $Right;
 var $Form;
 var $Msg;
 var $Msg_text;
 var $Spr;

 var $display;
 var $sort;
 var $start;

 var $user_id;
 var $module;

// ================================================================================================
//    Function          : ModulesPlug (Constructor)
//    Version           : 1.0.0
//    Date              : 02.02.2005
//    Parms             :
//    Returns           :
//    Description       : ModulesPlug
// ================================================================================================

function ModulesPlug()
{
 if (empty($this->Right)) $this->Right = &check_init('Rights', 'Rights', '$this->user_id, $this->module');
 if (empty($this->Form)) $this->Form = &check_init('FormModulesPlug', 'Form', '"form_module_plug"');
 if (empty($this->Msg)) $this->Msg = &check_init('ShowMsg', 'ShowMsg');
 if (empty($this->Spr)) $this->Spr = &check_init('SysSpr', 'SysSpr');
 $this->Msg_text = &check_init_txt('TblBackMulti',TblBackMulti);
}

// ================================================================================================
// Function : show()
// Version : 1.0.0
// Date : 02.02.2005
//
// Parms :
// Returns : true,false / Void
// Description : Show data from TblSysModPlag table
// ================================================================================================
// Programmer :  Andriy Lykhodid
// Date : 02.02.2005
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================
function show()
{
 $script = 'module='.$this->module.'&display='.$this->display.'&start='.$this->start.'&sort='.$this->sort;
 $script = $_SERVER['PHP_SELF']."?$script";

 if( $this->sort );
 else $this->sort = 'id';
 $q = "select * from ".TblSysModPlag." order by $this->sort";
 $res = $this->Right->Query( $q, $this->user_id, $this->module );
 if( !$res )return false;

 $rows = $this->Right->db_GetNumRows();
 /* Write Form Header */
 $this->Form->WriteHeader( $script );
 /* Write Table Part */
 AdminHTML::TablePartH();
 echo '<TR><td COLSPAN=7>';
 /* Write Links on Pages */
 $this->Form->WriteLinkPages( $script, $rows, $this->display, $this->start, $this->sort );
 echo '<TR><td COLSPAN=7>';
 $this->Form->WriteTopPanel( $script );
 $script2 = 'module='.$this->module.'&display='.$this->display.'&start='.$this->start.'&task=show';
 $script2 = $_SERVER['PHP_SELF']."?$script2";

?>
 <TR>
 <td class="THead">*</Th>
 <td class="THead"><A HREF=<?=$script2?>&sort=id><?=$this->Msg_text['FLD_ID']?></A></Th>
 <td class="THead"><A HREF=<?=$script2?>&sort=sys_func><?=$this->Msg_text['_FLD_FUNCTION']?></A></Th>
 <td class="THead"><A HREF=<?=$script2?>&sort=plugin><?=$this->Msg_text['_FLD_MOD_PLUGIN']?></A></Th>
 <td class="THead"><A HREF=<?=$script2?>&sort=maintenance><?=$this->Msg_text['_FLD_MOD_MAINTENANCE']?></A></Th>
 <td class="THead"><A HREF=<?=$script2?>&sort=forlogon><?=$this->Msg_text['FLD_LOGIN']?></A></Th>
 <td class="THead"><A HREF=<?=$script2?>&sort=layout><?=$this->Msg_text['_FLD_MOD_LAYOUT']?></A></Th>
<?
 $a = $rows;
 for( $i = 0; $i < $rows; $i++ )
 {
   $row = $this->Right->db_FetchAssoc();
   if( $i >= $this->start && $i < ( $this->start+$this->display ) )
   {
	 if ( (float)$i/2 == round( $i/2 ) ) echo '<TR CLASS="TR1">';
	 else echo '<TR CLASS="TR2">';

	 echo '<TD>';
	 $this->Form->CheckBox( "id_del[]", $row['id'] );

	 echo '<TD>';
	 $this->Form->Link( $script."&task=edit&id=".$row['id'], stripslashes( $row['id'] ), $this->Msg_text['TXT_EDIT'] );

	 echo '<TD align=center><b>'.$this->Spr->GetNameByCod( TblSysSprFunc, $row['sys_func'] ).'</b>';

	 echo '<TD align="center">';
	 if( $row['plugin'] == 'on' ) $this->Form->ButtonCheck();

	 echo '<TD align="center">';
	 if( $row['maintenance'] == 'on' ) $this->Form->ButtonCheck();

	 echo '<TD align="center">';
	 if( $row['forlogon'] == 'on' ) $this->Form->ButtonCheck();

	 if ( empty($row['layout'])) $val='';
	 else $val=$this->Spr->GetNameByCod( TblSysSprPanelManage, $row['layout']);
	 echo '<td>'.$val;// $row_spr1['description'];

	 $a=$a-1;
  }
}
 AdminHTML::TablePartF();
 $this->Form->WriteFooter();
}

// ================================================================================================
// Function : edit()
// Version : 1.0.0
// Date : 02.02.2005
//
// Parms :
// Returns : true,false / Void
// Description : (Add, Edit function for TblSysModPlag) Show data from TblSysModPlag table for editing
// ================================================================================================
// Programmer : Andriy Lykhodid
// Date : 13.01.2005
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================
function edit( $id, $mas=NULL )
{
 $script = 'module='.$this->module.'&display='.$this->display.'&start='.$this->start.'&sort='.$this->sort;
 $script = $_SERVER['PHP_SELF']."?$script";

 if( $id!=NULL and ( $mas==NULL ) )
 {
  $q="select * from ".TblSysModPlag." where id='$id'";
  $res = $this->Right->Query( $q, $this->user_id, $this->module );
  if( !$res ) return false;
  $mas = $this->Right->db_FetchAssoc();
 }
 /* Write Form Header */
 $this->Form->WriteHeader( $script );

 if( $id!=NULL ) $txt = $this->Msg_text['TXT_EDIT'];
 else $txt = $this->Msg_text['_TXT_ADD_DATA'];

 AdminHTML::PanelSubH( $txt );
 AdminHTML::PanelSimpleH();
?>
 <TABLE BORDER=0 class="EditTable">
 <TR><TD><b><?=$this->Msg_text['FLD_ID']?></b>
 <TD>
<?
   if( $id!=NULL )
   {
	echo $mas['id'];
	$this->Form->Hidden( 'id', $mas['id'] );
   }else $this->Form->Hidden( 'id', '' );

?>
  <TR><TD><b><?=$this->Msg_text['_FLD_FUNCTION']?></b>
  <TD>
<?
   $mas_s = NULL;
   $mas_s[''] = '';
   $q_spr = "select * from ".TblSysFunc." where target='front'";
   $res_spr = $this->Right->Query( $q_spr, $this->user_id, $this->module );
   $rows_spr = $this->Right->db_GetNumRows();
   for( $i = 0; $i<$rows_spr; $i++ )
   {
	  $row_spr = $this->Right->db_FetchAssoc();
	  $mas_s[$row_spr['id']] = $this->Spr->GetNameByCod( TblSysSprFunc, $row_spr['id'] ).' ('.$row_spr['name'].')';
   }
   $this->Form->Select( $mas_s, 'sys_func', $mas['sys_func'] );
?>
 <TR><TD><b><?=$this->Msg_text['_FLD_MOD_PLUGIN']?></b>
	 <TD>
<?
   $mas_s = NULL;
   $mas_s['on'] = 'on';
   $mas_s['off'] = 'off';
   $this->Form->Select( $mas_s, 'plugin', $mas['plugin'] );
?>
 <TR><TD><b><?=$this->Msg_text['_FLD_MOD_MAINTENANCE']?></b>
	 <TD>
<?
   $mas_s = NULL;
   $mas_s['on'] = 'on';
   $mas_s['off'] = 'off';
   $this->Form->Select( $mas_s, 'maintenance', $mas['maintenance'] );
?>
 <TR><TD><b><?=$this->Msg_text['FLD_LOGIN']?></b>
	 <TD>
<?
   $mas_s = NULL;
   $mas_s['on'] = 'on';
   $mas_s['off'] = 'off';
   $this->Form->Select( $mas_s, 'forlogon', $mas['forlogon'] );
?>
 <TR><TD><b><?=$this->Msg_text['_FLD_MOD_LAYOUT']?></b>
	 <TD>
<?
 $q_spr1 = "select * from ".TblSysPanelManage." order by level,move";
 $res_spr1 = $this->Right->Query($q_spr1, $this->user_id, $this->module);
 $rows_spr1 = $this->Right->db_GetNumRows();
 $masp['']='';
 for($i=0; $i<$rows_spr1; $i++)
 {
 $row_spr1=$this->Right->db_FetchAssoc();
 $masp[$row_spr1['id']] = $this->Spr->GetNameByCod( TblSysSprPanelManage, $row_spr1['id'] );
 }
 $this->Form->Select( $masp, 'layout', $mas['layout'] );
 //$this->Form->TextBox( 'layout', $mas['layout'], 70 )
?>
 <TR><TD COLSPAN=2 ALIGN=left>
<?
   $this->Form->WriteSavePanel( $script );
?>
 </TABLE>
<?
 AdminHTML::PanelSimpleF();
 AdminHTML::PanelSubF();

 $this->Form->WriteFooter();

return true;
}

// ================================================================================================
// Function : save()
// Version : 1.0.0
// Date : 02.02.2005
//
// Parms :   $id, $sys_func, $plugin, $maintenance, $forlogon, $layout
// Returns : true,false / Void
// Description : Store data to the table
// ================================================================================================
// Programmer : Andriy Lykhodid
// Date : 02.02.2005
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================

function save( $id, $sys_func, $plugin, $maintenance, $forlogon, $layout )
{
 if( empty( $sys_func ) )
 {
  $this->Msg->show_msg('_EMPTY_FUNCTION_FIELD');
  $this->edit( $id, $_REQUEST );
  return false;
 }

 $q="select * from ".TblSysModPlag."  where id='$id'";
 $res = $this->Right->Query( $q, $this->user_id, $this->module );
 if( !$res ) return false;

 $rows = $this->Right->db_GetNumRows();
 if($rows>0)
 {
   $q="update ".TblSysModPlag." set
	  sys_func='$sys_func',
	  plugin='$plugin',
	  maintenance='$maintenance',
	  forlogon='$forlogon',
	  layout='$layout'
	  where id='$id'";

  $res = $this->Right->Query( $q, $this->user_id, $this->module );
  if( $res )
   return 1;
  else return 0;
 } else
 {
 $q="select * from `".TblSysModPlag."` where id='$id'";
 $res = $this->Right->Query( $q, $this->user_id, $this->module );
 $rows = $this->Right->db_GetNumRows();
 if($rows>0) return 0;

 $q="insert into `".TblSysModPlag."` values(NULL, '$sys_func', '$plugin', '$maintenance', '$forlogon', '$layout')";
 $res = $this->Right->Query( $q, $this->user_id, $this->module );
 if( !$res ) return false;

 }
 return true;
}


// ================================================================================================
// Function : del()
// Version : 1.0.0
// Date : 02.02.2005
//
// Parms :
// Returns : true,false / Void
// Description :  Remove data from the table
// ================================================================================================
// Programmer : Andriy Lykhodid
// Date : 12.01.2005
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================
function del( $id_del )
{
	$del=0;
	$kol=count( $id_del );
	for( $i=0; $i<$kol; $i++ )
	{
	 $u=$id_del[$i];
	 $q="delete from ".TblSysModPlag."  where id='$u'";
	 $res = $this->Right->Query( $q, $this->user_id, $this->module );
	 if( !$res )return false;
	 if ( $res )
	  $del=$del+1;
	 else
	  return -1;
	}
  return $del;
}

   // ================================================================================================
   // =======================             FRONT END           ========================================
   // ================================================================================================
   // ================================================================================================
   // Function : IsPlug()
   // Version : 1.0.0
   // Date : 07.02.2005
   // Parms :
   // Returns : true,false / Void
   // Description :  Check the module is Plug
   // ================================================================================================
   // Programmer : Andriy Lykhodid
   // Date : 07.02.2005
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================

   function IsPlug( $id )
   {
	 $q = "select * from ".TblSysModPlag." where sys_func='$id'";
	 $res = $this->Right->db_Query( $q );
	 if( !$res ) return false;
	 $mas = $this->Right->db_FetchAssoc();
	 if( $mas['plugin'] == 'on' ) return true;
	 else return false;
   }

   // ================================================================================================
   // Function : IsMaintenance()
   // Version : 1.0.0
   // Date : 07.02.2005
   // Parms :
   // Returns : true,false / Void
   // Description :  Check the module is Maintenance - mode
   // ================================================================================================
   // Programmer : Andriy Lykhodid
   // Date : 07.02.2005
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================

   function IsMaintenance( $id )
   {
	 $q = "select * from ".TblSysModPlag." where sys_func='$id'";
	 $res = $this->Right->db_Query( $q );
	 if( !$res ) return false;
	 $mas = $this->Right->db_FetchAssoc();
	 if( $mas['maintenance'] == 'on' ) return true;
	 else return false;
   }
   // ================================================================================================
   // Function : IsLogon()
   // Version : 1.0.0
   // Date : 07.02.2005
   // Parms :
   // Returns : true,false / Void
   // Description :  Check the module is Logon - mode
   // ================================================================================================
   // Programmer : Andriy Lykhodid
   // Date : 07.02.2005
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================

   function IsLogon( $id )
   {
	 $q = "select * from ".TblSysModPlag." where sys_func='$id'";
	 $res = $this->Right->db_Query( $q );
	 if( !$res ) return false;
	 $mas = $this->Right->db_FetchAssoc();
	 if( $mas['forlogon'] == 'on' ) return true;
	 else return false;
   }

   // ================================================================================================
   // Function : GetLayout()
   // Version : 1.0.0
   // Date : 07.02.2005
   // Parms :
   // Returns : true,false / Void
   // Description :  Return the id of panel where this modul need to show
   // ================================================================================================
   // Programmer : Andriy Lykhodid
   // Date : 07.02.2005
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function GetLayout( $id )
   {
	 $q = "select * from ".TblSysModPlag." where sys_func='$id'";
	 $res = $this->Right->db_Query( $q );
	 if( !$res ) return false;
	 $mas = $this->Right->db_FetchAssoc();
	 return $mas['layout'];
   }

   // ================================================================================================
   // Function : GetModuleIdByPath()
   // Version : 1.0.0
   // Date : 09.02.2005
   // Parms :  $mod_path / path of the module. In table sys_func this is the name.
   // Returns : true,false / Void
   // Description :  Return the id of the module
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 09.02.2005
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function GetModuleIdByPath ( $mod_path )
   {
     $q = "select * from sys_func where name='$mod_path'";
	 $res = $this->Right->db_Query( $q );
     //echo '<br>$q='.$q.' $res='.$res;
	 if( !$res ) return false;
	 $mas = $this->Right->db_FetchAssoc();
	 return $mas['id'];
   }

   // ================================================================================================
   // Function : GetModuleNameById()
   // Version : 1.0.0
   // Date : 09.04.2009
   // Parms :  $module / if of the module.
   // Returns : true,false / Void
   // Description :  Return name of the module on selectel language
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 09.04.2009 
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function GetModuleNameById ( $module )
   {
	 return $this->Spr->GetNameByCod(TblSysSprFunc, $module, _LANG_ID, 1); 
   }//end of function GetModuleNameById()    
   
} //-- end of class
?>