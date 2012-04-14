<?php
include_once( SITE_PATH.'/admin/modules/sys_group/sys_group.class.php' );

/**
* Class SysGroupFunc
* Class definition for all actions with Grand rights of the groups of users
* @package System Package of SEOCMS
* @author Igor Trokhymchuk  <ihor@seotm.com>
* @version 1.1, 02.04.2012
* @copyright (c) 2005+ by SEOTM
*/
 class SysGroupFunc {

	   public  $user_id = NULL;
	   public  $module = NULL;
	   public  $sort = NULL;
	   public  $fltr = NULL;
	   public  $display = 20;
	   public  $start = 0;
	   public  $msg = NULL;
	   public  $Rights = NULL;
	   public  $Form = NULL;
	   public  $Spr = NULL;

	   /**
       * SysGroupFunc::__construct()
       * 
       * @param integer $user_id
       * @param integer $module_id
       * @param integer $display
       * @param string $sort
       * @param integer $start
       * @return void
       */
       function __construct($user_id=NULL, $module=NULL, $display=NULL, $sort=NULL, $start=NULL, $width=NULL)
       {
    		//Check if Constants are overrulled
    		( $user_id  !="" ? $this->user_id = $user_id  : $this->user_id = NULL );
    		( $module   !="" ? $this->module  = $module   : $this->module  = NULL );
    		( $sort     !="" ? $this->sort    = $sort     : $this->sort    = NULL );
    		( $display  !="" ? $this->display = $display  : $this->display = 10   );
    		( $start    !="" ? $this->start   = $start    : $this->start   = 0    );
    		if (empty($this->Rights)) $this->Rights = new Rights($this->user_id, $this->module);
    		if (empty($this->msg)) $this->msg = new ShowMsg();
    		if (empty($this->Msg_text)) $this->Msg_text = &check_init_txt('TblBackMulti',TblBackMulti); 
    		if (empty($this->Form)) $this->Form = new Form('form_TblSysAccess');
    
    		$this->Spr = new SysSpr();         /* create SysSpr object as a property of this class */
            
	   } // End of SysGroupFunc Constructor
       
       
	   // ================================================================================================
	   // Function : show
	   // Version : 1.0.0
	   // Date : 08.01.2005
	   //
	   // Parms :         $user_id  / user ID
	   //                 $module   / Module read  / Void
	   //                 $display  / How many records to show / Void
	   //                 $sort     / Sorting data / Void
	   //                 $start    / First record for show / Void
	   // Returns : true,false / Void
	   // Description : Show groups grands rights into the table
	   // ================================================================================================
	   // Programmer : Igor Trokhymchuk
	   // Date : 08.01.2005
	   // Reason for change : Creation
	   // Change Request Nbr:
	   // ================================================================================================
	   function show( $user_id=NULL, $module=NULL, $display=NULL, $sort=NULL, $start=NULL )
	   {
		if( $user_id ) $this->user_id = $user_id;
		if( $module ) $this->module = $module;
		if( $display ) $this->display = $display;
		if( $sort ) $this->sort = $sort;
		if( $start ) $this->start = $start;

		$scriptact = 'module='.$this->module;
		$scriplink = $_SERVER['PHP_SELF']."?$scriptact";

		if( empty($this->sort) ) $this->sort='id';
	   // $q = "SELECT `".TblSysAccess."`.*, `".TblSysSprFunc."`.name as function_name, `".TblSysGroupUsers."`.name as user_name FROM `".TblSysAccess."` LEFT JOIN `".TblSysSprFunc."` on
		//(`".TblSysSprFunc."`.cod=`".TblSysAccess."`.function ) LEFT JOIN `".TblSysGroupUsers."` on (`".TblSysGroupUsers."`.id=`".TblSysAccess."`.group) WHERE  
		//`".TblSysSprFunc."`.lang_id='"._LANG_ID."'";
		 $q = "SELECT `".TblSysAccess."`.*, `".TblSysSprFunc."`.name as function_name, `".TblSysGroupUsers."`.name as user_name FROM `".TblSysAccess."`,`".TblSysSprFunc."`,`".TblSysGroupUsers."` WHERE  
		`".TblSysSprFunc."`.cod=`".TblSysAccess."`.function 
		AND `".TblSysGroupUsers."`.id=`".TblSysAccess."`.group 
		AND `".TblSysSprFunc."`.lang_id='"._LANG_ID."'";
		if( $this->fltr ) $q = $q." AND `group`='$this->fltr'";
		$q = $q." order by $this->sort";

		/*$res = $this->Rights->Query( $q, $this->user_id, $this->module );
		if( !$res ) return false;
		$rows = $this->Rights->db_GetNumRows();*/
		$result = $this->Rights->QueryResult( $q, $this->user_id, $this->module );
//        if( !$result )return false;
//        echo $q;
		$rows = count($result);
		/* Write Form Header */
		$this->Form->WriteHeader( $scriplink.'&display='.$this->display.'&start='.$this->start.'&sort='.$this->sort."&fltr=$this->fltr" );
		/* Write Table Part */
		AdminHTML::TablePartH();
		echo '<TR><td colspan="9">';
		/* Write Links on Pages */
		$this->Form->WriteLinkPages( $scriplink."&fltr=$this->fltr", $rows, $this->display, $this->start, $this->sort );
		echo ' <TR><TD colspan="9"><div class="topPanel"><div class="SavePanel">';
		$scriplink = $scriplink.'&display='.$this->display.'&start='.$this->start.'&sort='.$this->sort;
	   /* Write Top Panel (NEW,DELETE - Buttons) */
		$this->Form->WriteTopPanel( $scriplink."&fltr=$this->fltr" );
		echo '</div><div class="SelectType">';
		$arr = SysGroup::GetGrpToArr( $this->user_id, $this->module, NULL );
		$grp = NULL;
		$max =count( $arr ) ;
		for( $i = 0; $i <$max ; ++$i)
		{
		 $grp[$arr[$i]['id']] = $arr[$i]['name'];
		}
		$grp[''] = '';
		$this->Form->SelectAct( $grp, 'group', $this->fltr, "onChange=\"location='$scriplink'+'&fltr='+this.value\"" );
		if( $this->fltr ) $scriplink = $scriplink."&fltr=$this->fltr";

		  ?>
		  </div></div>
		 <TR>
		 <Th class="THead">*</Th>
		 <Th class="THead"><? $this->Form->Link($scriplink."&sort=id", $this->Msg_text['FLD_ID']);?></Th>
		 <Th class="THead"><? $this->Form->Link($scriplink."&sort=function", $this->Msg_text['_FLD_FUNCTION']);?></Th>
		 <Th class="THead"><? $this->Form->Link($scriplink."&sort=group", $this->Msg_text['FLD_GROUP']);?></Th>
		 <Th class="THead"><? $this->Form->Link($scriplink."&sort=mask", $this->Msg_text['_FLD_READ']);?></Th>
		 <Th class="THead"><? $this->Form->Link($scriplink."&sort=mask", $this->Msg_text['_FLD_WRITE']);?></Th>
		 <Th class="THead"><? $this->Form->Link($scriplink."&sort=mask", $this->Msg_text['_FLD_UPDATE']);?></Th>
		 <Th class="THead"><? $this->Form->Link($scriplink."&sort=mask", $this->Msg_text['_FLD_DELETE']);?></Th>
		 <Th class="THead"><? $this->Form->Link($scriplink."&sort=mask", $this->Msg_text['_FLD_EXECUTE']);?></Th>
		  <?
		   $a=$rows;
		   for( $i = 0; $i < $rows; $i++ )
		   {
				 $row = $result[$i];
				 if( $i >=$this->start && $i < ( $this->start+$this->display ) )
				 {
					 if ( (float)$i/2 == round( $i/2 ) )
						 echo '<TR CLASS="TR1">';
					 else
						 echo '<TR CLASS="TR2">';
					 echo '<TD>'; $this->Form->CheckBox( "id_del[]", $row['id'] );
					 echo '<TD align=center>';
					 $this->Form->Link( $scriplink.'&task=edit&id='.$row['id'], stripslashes( $row['id'] ), $this->Msg_text['TXT_EDIT'] );

					 echo '<TD>',$row['function_name'],'<TD>',$row['user_name'],'</TD><TD>';
					 if( $row['r'] >0 ) $this->Form->ButtonCheck();
					 echo '</TD><TD>';
					 if( $row['w'] >0 ) $this->Form->ButtonCheck();
					 echo '</TD><TD>';
					 if( $row['u'] >0 ) $this->Form->ButtonCheck();
					 echo '</TD><TD>';
					 if( $row['d'] >0 ) $this->Form->ButtonCheck();
					 echo '</TD><TD>';
					 if( $row['e'] >0 ) $this->Form->ButtonCheck();
					 echo '</TD></TR>';
					 $a=$a-1;
				 }
		   }
		AdminHTML::TablePartF();
		/* Write Form Footer */
		$this->Form->WriteFooter();
		return true;
	   } //end of fuinction show
	   // ================================================================================================
	   // Function : edit
	   // Version : 1.0.0
	   // Date : 08.01.2005
	   //
	   // Parms :         $user_id  / user ID
	   //                 $module   / Module read  / Void
	   //                 $id       / id of editing record / Void
	   //                 $row      / mas with value from $_REQUEST
	   // Returns : true,false / Void
	   // Description : Show data for editing
	   // ================================================================================================
	   // Programmer : Igor Trokhymchuk
	   // Date : 08.01.2005
	   // Reason for change : Creation
	   // Change Request Nbr:
	   // ================================================================================================
	   function edit( $user_id=NULL, $module=NULL, $id=NULL, $row=NULL )
	   {
		if( $user_id ) $this->user_id = $user_id;
		if( $module ) $this->module = $module;

		/* set action page-adress with parameters */
		$scriptact = $_SERVER['PHP_SELF'].'?module='.$module.'&display='.$_REQUEST['display'].'&start='.$_REQUEST['start'];

		if( $id AND (!isset($row)) )
		{
		 $q="select * from ".TblSysAccess." where id='$id'";
		 // edit (U)
		 $res = $this->Rights->Query($q, $this->user_id, $this->module);
		 if( !$res ) return false;
		 $row = $this->Rights->db_FetchAssoc();
		}
		 /* Write Form Header */
		 $this->Form->WriteHeader( $scriptact );

		 $this->Form->Hidden( 'sort', $this->sort );
		 $this->Form->Hidden( 'fltr', $this->fltr );
		 $this->Form->Hidden( 'display', $this->display );
		 $this->Form->Hidden( 'start', $this->start );

		 if( $id!=NULL ) $txt = $this->Msg_text['TXT_EDIT'];
		 else $txt = $this->Msg_text['_TXT_ADD_DATA'];

		 AdminHTML::PanelSubH( $txt );
		 AdminHTML::PanelSimpleH();
?>
		 <TR><TD><b><?echo $this->Msg_text['FLD_ID']?></b>
			 <TD><?
			   if( $id )
			   {
				 echo $row['id'];
				 $this->Form->Hidden( 'id', $row['id'] );
			   }
			  ?>
		 <TR><TD><b><?echo $this->Msg_text['FLD_GROUP'];?></b>
			 <TD> <?
				   $q_spr = "select `id`,`name` from ".TblSysGroupUsers;
				   $res_spr = $this->Rights->db_QueryResult($q_spr, $this->user_id, $this->module);
				   $rows_spr = count($res_spr);
				   $mas['']='';
				   for($i=0; $i<$rows_spr; $i++)
				   {
						$mas[$res_spr[$i]['id']]=$res_spr[$i]['name'];
				   }
				   $this->Form->Select( $mas, 'group', $row['group'] );
			 ?>
		 <TR><TD><b><?echo $this->Msg_text['_FLD_FUNCTION'];?></b>
			 <TD><?
				   $q_spr1 = "select `".TblSysFunc."`.*,`".TblSysSprFunc."`.name as r_name from `".TblSysFunc."` LEFT JOIN `".TblSysSprFunc."` on ( `".TblSysFunc."`.id=`".TblSysSprFunc."`.cod) WHERE `".TblSysSprFunc."`.lang_id='"._LANG_ID."' order by r_name asc";
//                   echo $q_spr1;
				   $res_spr1 = $this->Rights->db_QueryResult($q_spr1, $this->user_id, $this->module);
				   $rows_spr1 = count($res_spr1);
				   $mas1['']='';
				   for($i=0; $i<$rows_spr1; $i++)
				   {
						if (!empty($res_spr1[$i]['name'])) $mas1[$res_spr1[$i]['id']]=$res_spr1[$i]['r_name'].' ('.$res_spr1[$i]['name'].')';
				   }
				   $this->Form->Select( $mas1, 'function', $row['function'] );
			 ?>
		 <TR><TD><b><?echo $this->Msg_text['_FLD_MASK'];?></b>
			 <TD>
					<table class="EditTable">
					<tr>
					 <td align=center><?echo $this->Msg_text['_FLD_READ']?></td>
					 <td align=center><?echo $this->Msg_text['_FLD_WRITE']?></td>
					 <td align=center><?echo $this->Msg_text['_FLD_UPDATE']?></td>
					 <td align=center><?echo $this->Msg_text['_FLD_DELETE']?></td>
					 <td align=center><?echo $this->Msg_text['_FLD_EXECUTE']?></td>
					</tr>
					<tr>
					 <td><?if( !isset( $row['r'] ) ) $row['r'] = ''; $this->Form->CheckBox( "r", '', $row['r'] );?>
					 <td><?if( !isset( $row['w'] ) ) $row['w'] = ''; $this->Form->CheckBox( "w", '', $row['w'] );?>
					 <td><?if( !isset( $row['u'] ) ) $row['u'] = ''; $this->Form->CheckBox( "u", '', $row['u'] );?>
					 <td><?if( !isset( $row['d'] ) ) $row['d'] = ''; $this->Form->CheckBox( "d", '', $row['d'] );?>
					 <td><?if( !isset( $row['e'] ) ) $row['e'] = ''; $this->Form->CheckBox( "e", '', $row['e'] );?>
					</tr>
					</table>
		 <tr><td colspan="2" align="left">
<?
		AdminHTML::PanelSimpleF();
		$this->Form->WriteSavePanel( $scriptact );
		AdminHTML::PanelSubF();
		return true;
	   }

	   // ================================================================================================
	   // Function : save
	   // Version : 1.0.0
	   // Date : 08.01.2005
	   //
	   // Parms :         $user_id  / user ID
	   //                 $module   / Module read  / Void
	   //                 $id       / id of editing record / Void
	   //                 $group    / name of the group / Void
	   //                 $function / name of the function /Void
	   //                 $r        / rights for read / Void
	   //                 $w        / rights for write / Void
	   //                 $u        / rights for update / Void
	   //                 $d        / rights for delete / Void
	   //                 $e        / rights for execute / Void
	   // Returns : true,false / Void
	   // Description : Store data to the table
	   // ================================================================================================
	   // Programmer : Igor Trokhymchuk
	   // Date : 08.01.2005
	   // Reason for change : Creation
	   // Change Request Nbr:
	   // ================================================================================================
	   function save( $user_id, $module, $id, $group, $function, $r, $w, $u, $d, $e )
	   {
		 if( $user_id ) $this->user_id = $user_id;
		 if( $module ) $this->module = $module;
		 if (empty($group)) {
			 $this->msg->show_msg('_EMPTY_GROUP_FIELD');
			 $this->edit( $user_id, $module, $id, $_REQUEST );
			 return false;
		 }
		 if (empty($function)) {
			 $this->msg->show_msg('_EMPTY_FUNCTION_FIELD');
			 $this->edit( $user_id, $module, $id, $_REQUEST );
			 return false;
		 }

		 $q = "select `id` from ".TblSysAccess." where `group`='$group' and `function`='$function'";
		 $res = $this->Rights->Query( $q, $this->user_id, $this->module );
		 if( !$res ) return false;
		 $rows = $this->Rights->db_GetNumRows();
		 $mas = $this->Rights->db_FetchAssoc();
		 if( $rows>0 and ( !$id ) )
		 {
			 $this->msg->show_msg('SYS_GROUP_ACCESS_DBL_FUNCTION');
			 $this->edit( $user_id, $module, $id, $_REQUEST );
			 return false;
		 }

		 $q="select `id` from ".TblSysAccess." where id='$id'";
		 //save (W)
		 $res = $this->Rights->Query($q, $this->user_id, $this->module);
		 if( !$res ) return false;
		 $rows = $this->Rights->db_GetNumRows();
		 if($rows>0)
		 {
		   $q = "UPDATE `".TblSysAccess."` SET `group` = '$group',
				`function` = '$function',
				`r` = '$r',
				`w` = '$w',
				`u` = '$u',
				`d` = '$d',
				`e` = '$e' WHERE `id` = '$id'";
		   $res = $this->Rights->Query($q, $this->user_id, $this->module);
		   if( !$res ) return false;
		   else return true;
		 }
		 else
		 {
		   $q="select `id` from ".TblSysAccess." where id='$id'";
		   $res = $this->Rights->Query($q, $this->user_id, $this->module);
		   if( !$res ) return false;
		   if($rows>0) return 0;

		  $q="insert into ".TblSysAccess." values(NULL,'$group','$function', '$r', '$w', '$u', '$d', '$e')";
		  $res = $this->Rights->Query($q, $this->user_id, $this->module);
		  if( !$res ) return false;
		  else return true;
		 }
	   }

	   // ================================================================================================
	   // Function : del
	   // Version : 1.0.0
	   // Date : 08.01.2005
	   //
	   // Parms :         $user_id  / user ID
	   //                 $module   / Module read  / Void
	   //                 $id_del   / array of the records which must be deleted / Void
	   // Returns : true,false / Void
	   // Description : Remove data from the table
	   // ================================================================================================
	   // Programmer : Igor Trokhymchuk
	   // Date : 08.01.2005
	   // Reason for change : Creation
	   // Change Request Nbr:
	   // ================================================================================================
	   function del( $user_id, $module, $id_del )
	   {
			$del=0;
			$kol=count( $id_del );
			for( $i=0; $i<$kol; $i++ )
			{
			 $u=$id_del[$i];
			 $q="delete from ".TblSysAccess." where id='$u'";
			 // delete (D)
			 $res = $this->Rights->Query($q, $user_id, $module);
			 if( !$this->Rights->result ) return false;
			 if ( $res )
			  $del=$del+1;
			 else
			  return -1;
			}
		  return $del;
	   }

 } // End of class SysGroupFunc
?>
