<?php
/**
* Class SysGroup
* Class definition for all actions with group of users
* @package System Package of SEOCMS
* @author Igor Trokhymchuk  <ihor@seotm.com>
* @version 1.1, 02.04.2012
* @copyright (c) 2005+ by SEOTM
*/
class SysGroup {

	   public  $user_id = NULL;
	   public  $module = NULL;
	   public  $sort = NULL;
	   public  $display = 10;
	   public  $start = 0;
	   public  $msg = NULL;
	   public  $msg_text = NULL;
	   public  $Rights = NULL;
	   public  $Form = NULL;

       /**
       * SysGroup::__construct()
       * 
       * @param integer $user_id
       * @param integer $module_id
       * @param integer $display
       * @param string $sort
       * @param integer $start
       * @return void
       */
	   function __construct($user_id=NULL, $module=NULL, $display=NULL, $sort=NULL, $start=NULL) {
				//Check if Constants are overrulled
				( $user_id  !="" ? $this->user_id = $user_id  : $this->user_id = NULL );
				( $module   !="" ? $this->module  = $module   : $this->module  = NULL );
				( $display  !="" ? $this->display = $display  : $this->display = 10   );
				( $sort     !="" ? $this->sort    = $sort     : $this->sort    = NULL );
				( $start    !="" ? $this->start   = $start    : $this->start   = 0    );
				if (empty($this->Rights)) $this->Rights = new Rights($this->user_id, $this->module);
				if (empty($this->msg)) $this->msg = new ShowMsg();
				if (empty($this->msg_text)) $this->msg_text =  &check_init_txt('TblBackMulti',TblBackMulti);
				if (empty($this->Form)) $this->Form = new Form('form_sys_group');
	   } // End of SysGroup Constructor

	   // ================================================================================================
	   // Function : show
	   // Version : 1.0.0
	   // Date : 08.01.2005
	   //
	   // Parms :         $module / Module read  / Void
	   //                 $sort / Sorting data / Void
	   //                 $display / Count of record for show / Void
	   //                 $start / First record for show / Void
	   //                 $end / Last record for show / Void
	   // Returns : true,false / Void
	   // Description : Show groups of users into the table
	   // ================================================================================================
	   // Programmer : Igor Trokhymchuk
	   // Date : 08.01.2005
	   // Reason for change : Creation
	   // Change Request Nbr:
	   // ================================================================================================
	   function show($user_id=NULL, $module=NULL, $display=NULL, $sort=NULL, $start=NULL)
	   {
		if( $user_id ) $this->user_id = $user_id;
		if( $module ) $this->module = $module;
		if( $display ) $this->display = $display;
		if( $sort ) $this->sort = $sort;
		if( $start ) $this->start = $start;

		$scriptact = 'module='.$this->module;
		$scriplink = $_SERVER['PHP_SELF'].'?'.$scriptact;
		$scriplink2 = $_SERVER['PHP_SELF']."?module=1".'&display='.$this->display.'&start='.$this->start.'&sort='.$this->sort;

		if( empty($this->sort) ) $this->sort='id';
		$q = "select `id`,`name`,`adm_menu` FROM `".TblSysGroupUsers."` order by $this->sort asc";
		// select (R)
		 $result = $this->Rights->QueryResult( $q, $this->user_id, $this->module );
		 if( !$result )return false;
		 $rows = count($result);
		/* Write Form Header */
		$this->Form->WriteHeader( $scriplink.'&display='.$this->display.'&start='.$this->start.'&sort='.$this->sort );

		/* Write Table Part */
		AdminHTML::TablePartH();
		echo '<TR><TD COLSPAN=6>';
		/* Write Links on Pages */
		$this->Form->WriteLinkPages( $scriplink, $rows, $this->display, $this->start, $this->sort );

		echo '<TR><TD COLSPAN=6><div class="topPanel">';
		$scriplink = $scriplink.'&display='.$this->display.'&start='.$this->start.'&sort='.$this->sort;
		$this->Form->WriteTopPanel( $scriplink );
			?>
		   </div> 
		 <TR>
		  <Td class="THead">*</Td>
		  <Td class="THead"><?=$this->msg_text['TXT_EDIT'];?></Td>
		  <Td class="THead"><? $this->Form->Link($scriplink."&sort=id", $this->msg_text['FLD_ID']);?></Td>
		  <Td class="THead"><? $this->Form->Link($scriplink."&sort=name", $this->msg_text['_FLD_NAME']);?></Td>
		  <Td class="THead"><? $this->Form->Link($scriplink."&sort=adm_menu", $this->msg_text['_FLD_BACK_OFFICE']);?></Td>
		  <Td class="THead"><?=$this->msg_text['_LNK_MENU'];?></Td>
		   <?
		   $a=$rows;
		   for( $i = 0; $i < $rows; $i++ )
		   {
			$row =$result[$i];
			if( $i >=$this->start && $i < ( $this->start+$this->display ) )
			{
			 if ( (float)$i/2 == round( $i/2 ) )
				 echo '<tr class="tr1">';
			 else
				 echo '<tr class="tr2">';
			 echo '<td class="td_center">'; $this->Form->CheckBox( "id_del[]", $row['id'] );

			 echo '<td>';
			 ?>
			  <a class="toolbar tip" title="<?=$this->msg_text['TXT_EDIT'];?>" href="<?=$scriplink.'&task=edit&id='.$row['id'];?>"> <?=$this->msg_text['TXT_EDIT'];?> </a>
			 <?
			 echo '</td><td>',$row['id'],'<td>',$row['name'],'</td>','<td class="td_center">';
			 if( $row['adm_menu'] >0 ) $this->Form->ButtonCheck();
			 echo '</td><td>';
			 ?>
			  <a class="toolbar tip" href="<?=$scriplink2.'&fltr='.$row['id'];?>" title="<?=$this->msg_text['_LNK_MENU'];?>" > <?=$this->msg_text['_LNK_MENU'];?> </a>
			 <?
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
	   // Parms :         $module / Module read  / Void
	   //                 $id / id of the record in table / Void
	   //                 $name / value of the field 'name'
	   //                 $adm_menu / value of the field 'adm_menu'
	   // Returns : true,false / Void
	   // Description : Show data for editing
	   // ================================================================================================
	   // Programmer : Igor Trokhymchuk
	   // Date : 08.01.2005
	   // Reason for change : Creation
	   // Change Request Nbr:
	   // ================================================================================================
	   function edit( $user_id, $module=NULL, $id=NULL, $row=NULL )
	   {
		if( $user_id ) $this->user_id = $user_id;
		if( $module ) $this->module = $module;
		$scriptact = $_SERVER['PHP_SELF'].'?module='.$module.'&display='.$_REQUEST['display'].'&start='.$_REQUEST['start'].'&sort='.$_REQUEST['sort'];
		if($id AND (!isset($row['id'])) )
		{
		 $q="select `id`,`name`,`adm_menu` from `".TblSysGroupUsers."` where id='$id'";
		 // edit (U)
		 $res = $this->Rights->Query($q, $this->user_id, $this->module);
		 if( !$res ) return false;
		 $row = $this->Rights->db_FetchAssoc();
		}

		$this->Form->WriteHeader( $scriptact );

		if( $id!=NULL ) $txt = $this->msg_text['TXT_EDIT'];
		else $txt = $this->msg_text['_TXT_ADD_DATA'];

		AdminHTML::PanelSubH( $txt );

		AdminHTML::PanelSimpleH();
		echo '<TR><TD width=200><b>',$this->msg_text['FLD_ID'],'</b><TD>';
		if( $id )
		{
		 echo $row['id'];
		 $this->Form->Hidden( 'id', $row['id'] );
		}
		echo '<TR><TD><b>',$this->msg_text['_FLD_NAME'],'</b><TD>';
		$this->Form->TextBox( 'name', $row['name'], 50 );
		echo '<TR><TD><b>',$this->msg_text['_FLD_BACK_OFFICE'],'</b><TD>';
		$this->Form->CheckBox( "adm_menu", '', $row['adm_menu'] );
		AdminHTML::PanelSimpleF();
		$this->Form->WriteSavePanel( $scriptact );
		AdminHTML::PanelSimpleF();
		AdminHTML::PanelSubF();
		$this->Form->WriteFooter();
		return true;
	   }  //end of fuinction edit

	   // ================================================================================================
	   // Function : save
	   // Version : 1.0.0
	   // Date : 08.01.2005
	   //
	   // Parms :         $user_id  / user ID
	   //                 $module   / Module read  / Void
	   //                 $id       / id of editing record / Void
	   //                 $name     / name of the group / Void
	   //                 $adm_menu / value for access to the back-office
	   // Returns : true,false / Void
	   // Description : Store data to the table
	   // ================================================================================================
	   // Programmer : Igor Trokhymchuk
	   // Date : 08.01.2005
	   // Reason for change : Creation
	   // Change Request Nbr:
	   // ================================================================================================
	   function save( $user_id, $module, $id, $name, $adm_menu)
	   {
		if( $user_id ) $this->user_id = $user_id;
		if( $module ) $this->module = $module;
		if ( empty($name) ) {
		   $this->msg->show_msg('_EMPTY_NAME_FIELD');
		   $this->edit($user_id, $module, $id, $_REQUEST );
		   return false;
		}
		$name = addslashes( $name );

		$q = "select `id` from `".TblSysGroupUsers."` where id='$id'";
		//save (W)
		$res = $this->Rights->Query($q, $this->user_id, $this->module);
		if( !$res ) return false;
		$rows = $this->Rights->db_GetNumRows();
		if($rows>0)
		{
		  $q="update `".TblSysGroupUsers."` set name='$name', adm_menu='$adm_menu'";
		  $q=$q." where id='$id'";
		  $res=$this->Rights->Query($q, $this->user_id, $this->module);
		  if( !$res ) return false;
		  else return true;
		}
		else
		{
		  $q="insert into `".TblSysGroupUsers."` values(NULL,'$name','$adm_menu')";
		  $res=$this->Rights->Query($q, $this->user_id, $this->module);
		  if( !$res ) return false;
		  else return true;
		}
		return true;
	   }  //end of fuinction edit

	   // ================================================================================================
	   // Function : sys_group_del
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
	   function del( $user_id, $module, $id_del)
	   {
		   $del=0;
		   $kol=count( $id_del );
		   for( $i=0; $i<$kol; $i++ )
		   {
			$u=$id_del[$i];
			$q="delete from `".TblSysGroupUsers."` where id='$u'";
			// delete (D)
			$res = $this->Rights->Query($q, $user_id, $module);
			if( !$this->Rights->result ) return false;
			if ( $res )
			 $del=$del+1;
		   }
		   return $del;
	   } //end of fuinction del

	   // ================================================================================================
	   // Function : GetGrpToArr
	   // Version : 1.0.0
	   // Date :    25.02.2005
	   // Parms :       $user_id, $module, $id=NULL
	   // Returns :     $arr
	   // Description : Return Array of Groups
	   // ================================================================================================
	   // Programmer : Andriy Lykhodid
	   // Date : 25.02.2005
	   // Reason for change : Creation
	   // Change Request Nbr:
	   // ================================================================================================
	   function GetGrpToArr( $user_id, $module_id, $id=NULL )
	   {
		$db = new Rights($user_id, $module_id);
        $arr = NULL;
		$q = "select `id`,`name` from `".TblSysGroupUsers."` where 1";
		if( $id ) $q = $q." and id='$id'";
		$result = $db->QueryResult( $q, $user_id, $module_id );
		if( !$result )return $arr;
		return $result;
	   }
	   // ================================================================================================
	   // Function : GetGrpNameToArr
	   // Version : 1.0.0
	   // Date :    26.011.2007
	   // Parms :       $access = 'front', 'back' or NULL        
	   // Returns :     $arr
	   // Description : Return Array of Groups
	   // ================================================================================================
	   // Programmer : Igor Trokhymchuk
	   // Date : 26.011.2007
	   // Reason for change : Creation
	   // Change Request Nbr:
	   // ================================================================================================
	   function GetGrpNameToArr($access=NULL)
	   {
		$dbr = DBs::getInstance();
		$arr = NULL;
		$q = "SELECT * FROM `".TblSysGroupUsers."` WHERE 1";
		if($access=='front') $q = $q." AND `adm_menu`='0'"; 
		if($access=='back') $q = $q." AND `adm_menu`='1'";
		$res = $dbr->db_Query( $q);
		if( !$res ) return $arr;

		$rows = $dbr->db_GetNumRows();
		for( $i = 0; $i < $rows; ++$i )
		{
			$row = $dbr->db_FetchAssoc();
			$arr[$row['id']] = $row['name'];
		}
		return $arr;
	   }         
	   
 }  //end of class SysGroup
?>