<?php
// ================================================================================================
// System : SEOCMS
// Module : sys_currencies.class.php
// Version : 1.0.0
// Date : 26.09.2007
// Licensed To:
// Igor Trokhymchuk ihoru@mail.ru
// Andriy Lykhodid las_zt@mail.ru
//
// Purpose : Class definition for all actions with currencies on the back-end
//
// ================================================================================================

include_once( SITE_PATH.'/admin/include/defines.inc.php' ); 

// ================================================================================================
//    Class             : SysSpr
//    Version           : 1.0.0
//    Date              : 26.09.2007
//
//    Constructor       : Yes
//    Parms             : session_id / session id
//                        usre_id    / UserID
//                        user_      /
//                        user_type  / id of group of user
//    Returns           : None
//    Description       : Class definition for all actions with currencies on the back-end
// ================================================================================================
//    Programmer        :  Igor Trokhymchuk
//    Date              :  26.09.2007
//    Reason for change :  Creation
//    Change Request Nbr:  N/A
// ================================================================================================
 class SysCurrencies extends SystemCurrencies {
		var $use_non_cash = false;
	   // ================================================================================================
	   //    Function          : SysCurrencies (Constructor)
	   //    Version           : 1.0.0
	   //    Date              : 26.09.2007
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
	   function SysCurrencies($user_id=NULL, $module=NULL, $display=NULL, $sort=NULL, $start=NULL, $width = NULL, $spr=NULL, $use_cash=false) {
				//Check if Constants are overrulled
				( $user_id   !="" ? $this->user_id = $user_id  : $this->user_id = NULL );
				( $module   !="" ? $this->module  = $module   : $this->module  = NULL );
				( $display  !="" ? $this->display = $display  : $this->display = 20   );
				( $sort     !="" ? $this->sort    = $sort     : $this->sort    = NULL );
				( $start    !="" ? $this->start   = $start    : $this->start   = 0    );
				( $spr      !="" ? $this->spr     = $spr      : $this->spr     = NULL  );
				( $use_cash !="" ? $this->use_non_cash= $use_cash : $this->use_non_cash  = false  );
				if ( defined("_LANG_ID") ) $this->lang_id = _LANG_ID;
				if (empty($this->db)) $this->db = &DBs::getInstance();
				if (empty($this->Right)) $this->Right = new Rights($this->user_id, $this->module);
				if (empty($this->Msg)) $this->Msg = &check_init_txt('TblBackMulti',TblBackMulti);
				if (empty($this->Form)) $this->Form = new Form('form_mod_catalog_params');
				if (empty($this->Spr)) $this->Spr = new  SysSpr($this->user_id, $this->module);
				//$this->def_currency = $this->GetDefaultCurrency();
				$this->defaultData = $this->GetDefaultData();
			   // print_r($this->defaultData);
	   } // End of SysCurrencies Constructor

   // ================================================================================================
   // Function : Show
   // Version : 1.0.0
   // Date : 04.02.2010
   // Parms :
   // Returns : true,false / Void
   // Description : Show data from $module table
   // ================================================================================================
   // Programmer : Yaroslav Gyryn
   // Date : 04.02.2010
   // ================================================================================================
   function Show()
   {
	if( empty($this->sort) ) {
		$this->sort='move';
	}
	$q = "SELECT 
				`".TblSysCurrencies."`.id,
				`".TblSysCurrencies."`.value,
				`".TblSysCurrencies."`.cashless,
				`".TblSysCurrencies."`.is_default,
				`".TblSysCurrencies."`.move,
				`".TblSysCurrencies."`.visible,
				 `".TblSysCurrenciesSprName."`.name,
				 `".TblSysCurrenciesSprName."`.pref,
				 `".TblSysCurrenciesSprName."`.suf,
				 `".TblSysCurrenciesSprName."`.short
			FROM `".TblSysCurrencies."` , `".TblSysCurrenciesSprName."`
			WHERE `".TblSysCurrencies."`.id = `".TblSysCurrenciesSprName."`.cod
			AND `".TblSysCurrenciesSprName."`.lang_id='".$this->lang_id."'
			";
	$q = $q." ORDER BY `$this->sort` $this->asc_desc";
	$result = $this->Right->QueryResult( $q, $this->user_id, $this->module );
	//echo '<br>$q='.$q.' $res='.$res.' $this->Right->result='.$this->Right->result.' $this->user_id='.$this->user_id;
//    if( !$res )return false;
	$rows =  count($result);
	/* Write Form Header */
	$this->Form->WriteHeader( $this->script );
	/* Write Table Part */
	AdminHTML::TablePartH();
	?><tr><td colspan="12">
   <div><?
	/* Write Links on Pages */
	$this->Form->WriteLinkPages( $this->script, $rows, $this->display, $this->start, $this->sort );

	?></div><div class="topPanel"><div class="SavePanel"><? 
	/* Write Top Panel (NEW,DELETE - Buttons) */
	$this->Form->WriteTopPanel( $this->script );

	$linkEdit = $this->Msg['TXT_EDIT'];     
	$txtUnvisible =$this->Msg['TXT_UNVISIBLE'];
	$txtVisibleBackend = $this->Msg['TXT_VISIBLE_ONLY_ON_BACKEND'];
	$txtVisible = $this->Msg['_TXT_VISIBLE'];
	?></div>
	<TR>
	<Th class="THead">*</Th>
	<Th class="THead"><?$this->Form->Link($this->script."&sort=id", $this->Msg['FLD_ID']);?></Th>
	<Th class="THead"><?=$this->Msg['_FLD_NAME'];?></Th>
	<Th class="THead"><?=$this->Msg['_FLD_SHORT_NAME'];?></Th>
	<Th class="THead"><?=$this->Msg['FLD_PREFIX'];?></Th>
	<Th class="THead"><?=$this->Msg['FLD_VALUE'];?></Th>
	<? if($this->use_non_cash){?> 
	<Th class="THead"><?=$this->Msg['FLD_VALUE_CASHLESS'];?></Th> <?}?>
	<Th class="THead"><?=$this->Msg['FLD_SUFIX'];?></Th>
	<Th class="THead"><?=$this->Msg['FLD_EXAMPLE_OUTPUT'];?></Th>
	<Th class="THead"><?=$this->Msg['FLD_DEFAULT'];?></Th>
	<Th class="THead"><?$this->Form->Link($this->script."&sort=visible", $this->Msg['_FLD_VISIBLE']);?></Th>
	<Th class="THead"><?$this->Form->Link($this->script."&sort=move", $this->Msg['FLD_DISPLAY']);?></Th>
	<?
	$a=$rows;
	$j = 0;
	$up = 0;
	$down = 0;   
	for( $i = 0; $i < $rows; ++$i )
	{
	   $row = $result[$i];
	   if( $i >=$this->start && $i < ( $this->start+$this->display ) )
	   {
		  if ( (float)$i/2 == round( $i/2 ) ){
			 ?><tr class="TR1"><?
		  }
		  else{
			 ?><tr class="TR2"><?
		  }
		  ?>
		  <td align="center"><?=$this->Form->CheckBox( "id_del[]", $row['id'] );?></td>
		  <td align="center"><?=$this->Form->Link( $this->script."&task=edit&id=".$row['id'], stripslashes($row['id']), $linkEdit );?></td>
		  <td align="center"><?=$row['name'] //$this->Spr->GetNameByCod(TblSysCurrenciesSprName, $row['id'], $this->lang_id, 1);?></td>
		  <td align="center"><?=$row['short'] //$this->Spr->GetNameByCod(TblSysCurrenciesSprShort, $row['id'], $this->lang_id, 1);?></td>
		  <td align="center"><?=$row['pref']//  $this->Spr->GetNameByCod(TblSysCurrenciesSprPrefix, $row['id'], $this->lang_id, 1);?></td> 
		  <td align="center"><?=stripslashes($row['value']);?></td>
		   <? if($this->use_non_cash){?>
		  <td align="center"><?=stripslashes($row['cashless']);?></td>
		  <?}?>
		  <td align="center"><?=$row['suf']//$this->Spr->GetNameByCod(TblSysCurrenciesSprSufix, $row['id'], $this->lang_id, 1);?></td>
		  <td align="left">&nbsp;
			<?=$this->defaultData['pref'];//=$this->Spr->GetNameByCod(TblSysCurrenciesSprPrefix, $this->def_currency, $this->lang_id, 1);?>
			100
			<?=$this->defaultData['suf']; //=$this->Spr->GetNameByCod(TblSysCurrenciesSprSufix, $this->def_currency, $this->lang_id, 1);?> 
			= 
			<?=$row['pref']//$this->Spr->GetNameByCod(TblSysCurrenciesSprPrefix, $row['id'], $this->lang_id, 1);?>
			<?=$this->Converting( $this->defaultData['cod'], $row['id'], 100, 2);?>
			<?=$row['suf']//$this->Spr->GetNameByCod(TblSysCurrenciesSprSufix, $row['id'], $this->lang_id, 1);?>
			</td>
		  <td align="center">
		   <?
		   if( $row['is_default']==1) $this->Form->ButtonCheck(); 
		   ?>
		  </td>
		  <td align="center">
		   <?
		   if( $row['visible'] == 0 ) $this->Form->Img( 'http://'.NAME_SERVER.'/admin/images/icons/publish_x.png', $txtUnvisible, 'border=0' );
		   if( $row['visible'] == 1 ) $this->Form->Img( 'http://'.NAME_SERVER.'/admin/images/icons/publish_r.png', $txtVisibleBackend, 'border=0' );
		   if( $row['visible'] == 2 ) $this->Form->Img( 'http://'.NAME_SERVER.'/admin/images/icons/publish_g.png', $txtVisible, 'border=0' );
		   ?>
		  </td>
		  <td align="center">
		   <?
		   if( $up!=0 )
		   {
		   ?>
			<a href="<?=$this->script?>&task=up&move=<?=$row['move']?>"><?=$this->Form->ButtonUp( $row['id'] );?></a>
		   <?
		   }
		   if( $i!=($rows-1) )
		   {
		   ?>
			 <a href="<?=$this->script?>&task=down&move=<?=$row['move']?>"><?=$this->Form->ButtonDown( $row['id'] );?></a>
		   <?
		   }
		   $up=$row['id'];
		   $a=$a-1;               
		   ?>
		  </td>
		 </tr> 
		 <?
	   }
	}
	AdminHTML::TablePartF();
	/* Write Form Footer */
	$this->Form->WriteFooter();
	return true;
   } //end of fuinction Show()

   // ================================================================================================
   // Function : Edit
   // Version : 1.0.0
   // Returns : true,false / Void
   // Description : Show data from $spr table for editing
   // Programmer : Yaroslav Gyryn
   // Date : 04.02.2010
   // ================================================================================================
   function Edit()
   {
	$Panel = new Panel();
	$ln_sys = new SysLang();
	$mas=NULL;
   
   //echo '<br/>$this->id= '.$this->id;
	if( $this->id!=NULL and ( $mas==NULL ) )
	{
   //  $q="SELECT * FROM `".TblSysCurrencies."` WHERE `id`='$this->id'";
	 $q="SELECT 
			`".TblSysCurrencies."`.id,
			`".TblSysCurrencies."`.value,
			`".TblSysCurrencies."`.cashless,
			`".TblSysCurrencies."`.is_default,
			`".TblSysCurrencies."`.visible,
			`".TblSysCurrencies."`.move,
			`".TblSysCurrenciesSprName."`.lang_id,
			 `".TblSysCurrenciesSprName."`.name,
			 `".TblSysCurrenciesSprName."`.pref,
			 `".TblSysCurrenciesSprName."`.suf,
			 `".TblSysCurrenciesSprName."`.short          
			FROM `".TblSysCurrencies."` , `".TblSysCurrenciesSprName."`
			WHERE 
			`".TblSysCurrencies."`.id ='$this->id' AND
			`".TblSysCurrencies."`.id = `".TblSysCurrenciesSprName."`.cod
	 ";  
	 $res = $this->Right->Query( $q, $this->user_id, $this->module );
	 //echo '<br>$q='.$q.' $res='.$res.' $this->Right->result='.$this->Right->result.' $this->user_id='.$this->user_id;
	 if( !$this->Right->result ) return false;
	 $rows = $this->Right->db_GetNumRows();
	 for( $i = 0; $i < $rows; $i++ )
	 {
		$row = $this->Right->db_FetchAssoc();
		$mas[$row['lang_id']] = $row;
	 }
	 //$mas = $this->Right->db_FetchAssoc();
	 //print_r($mas);
	 //echo '<br/>';
	}
	
	/* Write Form Header */
	$this->Form->WriteHeader( $this->script );
	
	if( $this->id!=NULL ) $txt = $this->Msg['TXT_EDIT'];
	else $txt = $this->Msg['_TXT_ADD_DATA'];

	AdminHTML::PanelSubH( $txt );
	//-------- Show Error text for validation fields --------------
	$this->ShowErrBackEnd();
	//-------------------------------------------------------------        
	AdminHTML::PanelSimpleH();
   ?>
   <table border="0" class="PanelSimpleL" cellspacing="3" cellpadding="4" width="100%">
	<tr>
	 <td><b><?echo $this->Msg['FLD_ID']?>:</b></td>
	 <td width="90%">
	  <?
	  if( $this->id!=NULL ){
		  echo $mas[_LANG_ID]['id']; 
	   //echo $mas['id'];
	   $this->Form->Hidden( 'id',$mas[_LANG_ID]['id']/* $mas['id']*/ );
	  }
	  else $this->Form->Hidden( 'id', '' );
	  $this->Form->Hidden( 'move', $this->move );
	  ?>
	 </td>
	</tr>
	<tr>
	 <td colspan="2">
	  <?
	  $Panel->WritePanelHead( "SubPanel_" );

	  $ln_arr = $ln_sys->LangArray( _LANG_ID );
	  //print_r($ln_arr);
	  $arr_v[0]=$this->Msg['TXT_UNVISIBLE'];
	  //$arr_v[1]=$this->Msg['TXT_VISIBLE_ONLY_ON_BACKEND'];
	  $arr_v[2]=$this->Msg['_TXT_VISIBLE'];    
	  $fldName = $this->Msg['_FLD_NAME']; 
	  $fldShortName = $this->Msg['_FLD_SHORT_NAME'];
	  $fldPrefix = $this->Msg['FLD_PREFIX'];
	  $fldSufix = $this->Msg['FLD_SUFIX'];
	  while( $el = each( $ln_arr ) ){
		  $lang_id = $el['key'];
		  $lang = $el['value'];
		  $mas_s[$lang_id] = $lang;

		  $Panel->WriteItemHeader( $lang );
		  echo "\n <table border=0 class='EditTable'>";

		  echo "\n <tr>\n <td><b>",$fldName,":</b>\n <td>";
		  //$row = $this->Spr->GetByCod( TblSysCurrenciesSprName, $mas['id'], $lang_id );
		  if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->name[$lang_id] : $val=$mas[$lang_id]['name'];//$row[$lang_id];
		  else $val=$this->name[$lang_id];              
		  $this->Form->TextBox( 'name['.$lang_id.']', stripslashes($val), 20 );

		  echo "\n <tr>\n <td><b>",$fldShortName,":</b>\n <td>";
		  //$row = $this->Spr->GetByCod( TblSysCurrenciesSprShort, $mas['id'], $lang_id );
		  if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->short[$lang_id] : $val=$mas[$lang_id]['short'];//$row[$lang_id];
		  else $val=$this->short[$lang_id];              
		  $this->Form->TextBox( 'short['.$lang_id.']', stripslashes($val), 20 );
		  
		  echo "\n <tr>\n <td><b>",$fldPrefix,":</b>\n <td>";
		  //$row = $this->Spr->GetByCod( TblSysCurrenciesSprPrefix, $mas['id'], $lang_id );
		  if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->prefix[$lang_id] : $val=$mas[$lang_id]['pref'];//$row[$lang_id];
		  else $val=$this->prefix[$lang_id];              
		  $this->Form->TextBox( 'prefix['.$lang_id.']', stripslashes($val), 20 );

		  echo "\n <tr>\n <td><b>",$fldSufix,":</b>\n <td>";
		  //$row = $this->Spr->GetByCod( TblSysCurrenciesSprSufix, $mas['id'], $lang_id );
		  if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->sufix[$lang_id] : $val=$mas[$lang_id]['suf'];//$row[$lang_id];
		  else $val=$this->sufix[$lang_id];              
		  $this->Form->TextBox( 'sufix['.$lang_id.']', stripslashes($val), 20 );
		  
		  echo   "\n </table>";
		  $Panel->WriteItemFooter();
	  }//end while
	  $Panel->WritePanelFooter();
	  ?>
	 </td>
	</tr>
	<tr>
	 <td></td>
	 <td valign="middle">
	  <?
	  if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->is_default : $val=$mas[$lang_id]['is_default'];
	  else $val=$this->is_default;
	  if( $this->GetDefaultCurrency()==0) $val = 1;
	  $this->Form->CheckBox( 'is_default', $val, $val ); echo $this->Msg['FLD_DEFAULT'];
	  ?>
	 </td>
	</tr>
	<tr>
	 <td><b><?=$this->Msg['FLD_VALUE'];?>:</b></td>
	 <td>
	  <?
	  if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->value : $val=$mas[$lang_id]['value'];
	  else $val=$this->value;         
	  $this->Form->TextBox( 'value', stripslashes($val), 20 );
	  ?>
	 </td>
	</tr>
	<? if($this->use_non_cash) {?>
	<tr>
	 <td><b><?=$this->Msg['FLD_VALUE_CASHLESS'];?>:</b></td>
	 <td>
	  <?
	  if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->cashless : $val=$mas[$lang_id]['cashless'];
	  else $val=$this->cashless;         
	  $this->Form->TextBox( 'cashless', stripslashes($val), 20 );
	  ?>
	 </td>
	</tr>
	<?}
	else 
		$this->Form->Hidden( 'cashless','' );
	?>
	<tr>
	 <td><b><?=$this->Msg['_FLD_VISIBLE'];?>:</b></td>
	 <td>
	  <?
	  if( $this->id!=NULL ) $this->Err!=NULL ? $visible=$this->visible : $visible=$mas[$lang_id]['visible'];
	  else $visible=$this->visible; 
	  $this->Form->Select( $arr_v, 'visible', $visible );
	  ?>
	 </td>
	</tr>
	</table>
	<div class="space"></div>
	  <?
	  $this->Form->WriteSavePanel( $this->script );
	  $this->Form->WriteCancelPanel( $this->script );
	AdminHTML::PanelSimpleF();
	AdminHTML::PanelSubF();
	if ($this->id==NULL) $move = $this->GetMaxValueOfFieldMove()+1;
	else $move=$mas[_LANG_ID]['move'];
	$this->Form->Hidden( 'move', $move );
	$this->Form->WriteFooter();
	return true;
   }  //end of fuinction Edit()

   // ================================================================================================
   // Function : Save
   // Version : 1.0.0
   // Returns : true,false / Void
   // Description : Store data to the table
   // ================================================================================================
   // Programmer : Yaroslav Gyryn
   // Date : 03.02.2010
   // ================================================================================================
   function Save()
   {
	$q="SELECT `id` FROM `".TblSysCurrencies."` WHERE `id`='$this->id'";
	$res = $this->Right->Query( $q, $this->user_id, $this->module );
	if( !$this->Right->result ) return false;
	$rows = $this->Right->db_GetNumRows();
	
	if( $this->is_default == 1 ){
	   $q = "UPDATE `".TblSysCurrencies."` SET `is_default`='0'";
	   $res = $this->Right->Query( $q, $this->user_id, $this->module );
	   if( !$res ) return false; 
	   //echo '<br>$q='.$q.' $res='.$res; 
	}

   $ln_sys = new SysLang();
   $ln_arr = $ln_sys->LangArray( _LANG_ID );
	if($rows>0)
	{
	  $q="UPDATE `".TblSysCurrencies."` SET
		  `value`='$this->value',
		  `cashless`='$this->cashless',
		  `is_default`='$this->is_default',
		  `move`='$this->move',
		  `visible`='$this->visible'";
	  $q=$q." WHERE `id`='$this->id'";
	  $res = $this->Right->Query( $q, $this->user_id, $this->module );
	  //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result; 
	  if( !$res  or !$this->Right->result ) 
			return false;
	  
	  while( $el = each( $ln_arr ) )
	  {
		  $lang_id = $el['key']; 
		  $name = $this->name[$lang_id];
		  $prefix = $this->prefix[$lang_id];
		  $sufix = $this->sufix[$lang_id];
		  $short = $this->short[$lang_id];
		  $q="UPDATE `".TblSysCurrenciesSprName."` SET
				  `cod`='$this->id',
				  `lang_id`='$lang_id',
				  `name`='$name',
				  `pref`='$prefix',
				  `suf`='$sufix',
				  `short`='$short'
				WHERE `cod`='".$this->id."' AND `lang_id`='".$lang_id."'
		  ";
		  $res = $this->Right->Query( $q, $this->user_id, $this->module );
		  //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result; 
		  if( !$res) 
			return false;
	  } //--- end while  
	}
	else
	{ 
	  $maxx = $this->GetMaxValueOfFieldMove()+1;
	  $q="INSERT INTO `".TblSysCurrencies."` VALUES(NULL,'$this->value','$this->cashless','$this->is_default','$this->move','$this->visible')";
	  $res = $this->Right->Query( $q, $this->user_id, $this->module );
	  //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;
	  if( !$this->Right->result) return false;
	  if ( empty($this->id) )
			  $this->id = $this->Right->db_GetInsertID();

	  while( $el = each( $ln_arr ) )
	  {
		  $lang_id = $el['key']; 
		  $name = $this->name[$lang_id];
		  $prefix = $this->prefix[$lang_id];
		  $sufix = $this->sufix[$lang_id];
		  $short = $this->short[$lang_id];
		  $q="INSERT INTO `".TblSysCurrenciesSprName."` VALUES(NULL,'$this->id','$lang_id','$name','$prefix','$sufix','$short')";
		  $res = $this->Right->Query( $q, $this->user_id, $this->module );
					if( !$res ) return false;
	  } //--- end while          
	}
	//---- Save fields on different languages ----
	/*$res=$this->Spr->SaveNameArr( $this->id, $this->name, TblSysCurrenciesSprName );
	if( !$res ) return false;

	$res=$this->Spr->SaveNameArr( $this->id, $this->short, TblSysCurrenciesSprShort );
	if( !$res ) return false;        

	$res=$this->Spr->SaveNameArr( $this->id, $this->prefix, TblSysCurrenciesSprPrefix );
	if( !$res ) return false;  
	
	$res=$this->Spr->SaveNameArr( $this->id, $this->sufix, TblSysCurrenciesSprSufix );
	if( !$res ) return false;  */
	return true;
   } //end of fuinction Save()

	   // ================================================================================================
	   // Function : Del
	   // Version : 1.0.0
	   // Date : 26.09.2007
	   //
	   // Parms : $id_del   / array of the records which must be deleted / Void
	   // Returns : true,false / Void
	   // Description :  Remove data from the table
	   // ================================================================================================
	   // Programmer : Igor Trokhymchuk
	   // Date : 26.09.2007
	   // Reason for change : Creation
	   // Change Request Nbr:
	   // ================================================================================================
	   function Del($id_del)
	   {

		   $this->Form->Hidden( 'sort', $this->sort );
		   $this->Form->Hidden( 'fln', $this->fln );
		   $this->Form->Hidden( 'display', $this->display );
		   $this->Form->Hidden( 'start', $this->start );
		   $this->Form->Hidden( 'asc_desc', $this->asc_desc );

		   $kol=count( $id_del );
		   $del=0;
		   for( $i=0; $i<$kol; $i++ )
		   {
			$u=$id_del[$i];
			$q = "SELECT * FROM `".TblSysCurrencies."` WHERE `id`='$u'";
			$res = $this->Right->Query( $q, $this->user_id, $this->module );
			if( !$this->Right->result ) return false;
			$row = $this->Right->db_FetchAssoc();

			$q = "DELETE FROM `".TblSysCurrencies."` WHERE `id`='".$row['id']."'";
			$res = $this->Right->Query( $q, $this->user_id, $this->module );
			if( !$this->Right->result ) return false;
			$q = "DELETE FROM `".TblSysCurrenciesSprName."` WHERE `cod`='".$row['id']."'";
			$res = $this->Right->Query( $q, $this->user_id, $this->module );
			if( !$this->Right->result ) return false;
			if( !$res ) return false;
			$del=$del+1;
		   }
		   return $del;
	   } //end of fuinction Del()
		   
		// ================================================================================================
		// Function : up()
		// Version : 1.0.0
		// Date : 26.09.2007
		// Parms :
		// Returns :      true,false / Void
		// Description :  Up position
		// ================================================================================================
		// Programmer :  Andriy Lykhodid
		// Date : 26.09.2007
		// Reason for change : Creation
		// Change Request Nbr:
		// ================================================================================================
		function up($table)
		{
//            echo '$this->move= '.$this->move;
		 $q="select `id`,`move` from `$table` where `move`='$this->move'";
		 $res = $this->Right->Query( $q, $this->user_id, $this->module );
		 //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;
		 if( !$res )return false;
		 $rows = $this->Right->db_GetNumRows();
		 $row = $this->Right->db_FetchAssoc();
		 $move_down = $row['move'];
		 $id_down = $row['id'];

		 $q="select `id`,`move` from `$table` where `move`<'$this->move' order by `move` desc";
		 $res = $this->Right->Query( $q, $this->user_id, $this->module );
		 //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;
		 if( !$res )return false;
		 $rows = $this->Right->db_GetNumRows();
		 $row = $this->Right->db_FetchAssoc();
		 $move_up = $row['move'];
		 $id_up = $row['id'];

		 //echo '<br> $move_down='.$move_down.' $move_up ='.$move_up;
		 if( $move_down!=0 AND $move_up!=0 )
		 {
		 $q="update `$table` set
			 `move`='$move_down' where `id`='$id_up'";
		 $res = $this->Right->Query( $q, $this->user_id, $this->module );
		 //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result; 
		 
		 $q="update `$table` set
			 `move`='$move_up' where `id`='$id_down'";
		 $res = $this->Right->Query( $q, $this->user_id, $this->module );
		 //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result; 
		 
		 }
		} // end of function up()

		// ================================================================================================
		// Function : down()
		// Version : 1.0.0
		// Date : 26.09.2007
		// Parms :
		// Returns :      true,false / Void
		// Description :  Down position
		// ================================================================================================
		// Programmer :  Andriy Lykhodid
		// Date : 26.09.2007
		// Reason for change : Creation
		// Change Request Nbr:
		// ================================================================================================
		function down($table)
		{
		 $q="select `id`,`move` from `$table` where `move`='$this->move'";
		 $res = $this->Right->Query( $q, $this->user_id, $this->module );
		 //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;
		 if( !$res )return false;
		 $rows = $this->Right->db_GetNumRows();
		 $row = $this->Right->db_FetchAssoc();
		 $move_up = $row['move'];
		 $id_up = $row['id'];


		 $q="select `id`,`move` from `$table` where `move`>'$this->move' order by `move` asc";
		 $res = $this->Right->Query( $q, $this->user_id, $this->module );
		 //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;
		 if( !$res )return false;
		 $rows = $this->Right->db_GetNumRows();
		 $row = $this->Right->db_FetchAssoc();
		 $move_down = $row['move'];
		 $id_down = $row['id'];

		 if( $move_down!=0 AND $move_up!=0 )
		 {
		 $q="update `$table` set
			 `move`='$move_down' where `id`='$id_up'";
		 $res = $this->Right->Query( $q, $this->user_id, $this->module );
		 //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;

		 $q="update `$table` set
			 `move`='$move_up' where `id`='$id_down'";
		 $res = $this->Right->Query( $q, $this->user_id, $this->module );
		 //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;
		 }
		} // end of function down()         

		// ================================================================================================
		// Function : CheckFields
		// Version : 1.0.0
		// Date : 26.09.2007
		//
		// Parms :  $table  - name of the table  
		// Returns : value
		// Description : return the biggest value
		// ================================================================================================
		// Programmer : Igor Trokhymchuk
		// Date : 26.09.2007 
		// Reason for change : Creation
		// Change Request Nbr:
		// ================================================================================================
		function CheckFields()
		{
		  $this->Err = NULL;
		  if (empty( $this->name[_LANG_ID] )) {
				$this->Err=$this->Err.$this->Msg['MSG_FLD_CURRENCY_NAME_EMPTY'].'<br />';
		  }
		  if( empty($this->value) ){
			$this->Err = $this->Err.$this->Msg['MSG_ERR_CURRENCY_VALUE'].'<br />';
		  }
		  return $this->Err;
		} // end of function CheckFields()          
		
		// ================================================================================================
		// Function : GetMaxValueOfFieldMove
		// Version : 1.0.0
		// Date : 26.09.2007
		//
		// Parms :  $table  - name of the table  
		// Returns : value
		// Description : return the biggest value
		// ================================================================================================
		// Programmer : Igor Trokhymchuk
		// Date : 26.09.2007 
		// Reason for change : Creation
		// Change Request Nbr:
		// ================================================================================================
		function GetMaxValueOfFieldMove()
		{
		   $tmp_db = &DBs::getInstance();
		   
		   $q = "SELECT MAX(`move`) FROM `".TblSysCurrencies."` WHERE 1";
		   $res = $tmp_db->db_Query( $q );
		   //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;  
		   if( !$res OR !$tmp_db->result ) return false;
		   //$rows = $tmp_db->db_GetNumRows();
		   $row = $tmp_db->db_FetchAssoc();
		   return $row['MAX(`move`)'];
		} // end of function GetMaxValueOfFieldMove();         
		
	   // ================================================================================================
	   // Function : ShowErrBackEnd()
	   // Version : 1.0.0
	   // Date : 26.09.2007
	   //
	   // Parms :
	   // Returns :      true,false / Void
	   // Description :  Show errors
	   // ================================================================================================
	   // Programmer :  Igor Trokhymchuk
	   // Date : 26.09.2007
	   // Reason for change : Creation
	   // Change Request Nbr:
	   // ================================================================================================
	   function ShowErrBackEnd()
	   {
		 if ($this->Err){
		   echo '
			<table border=0 cellspacing=4 cellpadding=5 class="err" align="center">
			 <tr><td align="left">'.$this->Err.'</td></tr>
			</table>';
		 }
	   } //end of fuinction ShowErrBackEnd()
							 
 }  //end of class SysCurrencies?>