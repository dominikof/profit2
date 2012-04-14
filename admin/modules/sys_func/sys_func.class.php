<?php
/**
* Class SysFunc
* Class definition for system function
* @package System Package of SEOCMS
* @author Igor Trokhymchuk  <ihor@seotm.com>
* @version 1.1, 02.04.2012
* @copyright (c) 2005+ by SEOTM
*/
class SysFunc {

     public  $Right;
     public  $Form;
     public  $Msg;
     public  $Msg_text;
     public  $Spr;
    
     public  $display;
     public  $sort;
     public  $start=0;
     public  $fltr;
     public  $Err = NULL;
    
     public  $user_id;
     public  $module_id;
     public  $width;
    
    /**
     * SysFunc::__construct()
     * 
     * @param integer $user_id
     * @param integer $module_id
     * @param integer $display
     * @param string $sort
     * @param integer $start
     * @return void
     */
    Function __construct( $user_id=NULL, $module_id=NULL, $display=NULL, $sort=NULL, $start=NULL )
    {
     $this->user_id = $user_id;
     $this->module_id = $module_id;
     $this->display = $display;
     if( !empty( $sort ) ) $this->sort = $sort;
     if( !empty( $start ) ) $this->start = $start;
     $this->width = '100%';
     
     $this->Right =  new Rights($this->user_id, $this->module_id);                  /* create Rights obect as a property of this class */
     $this->Form = new Form( 'form_sys_func' );   /* create Form object as a property of this class */
     $this->Msg = new ShowMsg(); 
     $this->Msg_text = &check_init_txt('TblBackMulti',TblBackMulti);                 /* create ShowMsg object as a property of this class */
     $this->Spr = new SysSpr();                   /* create SysSpr object as a property of this class */
    }
    
    
    // ================================================================================================
    // Function : show()
    // Version : 1.0.0
    // Date : 27.01.2005
    // Parms :
    //        $module_id  - id of this module in system
    //        $user_id    - id of current user
    //        $action     - script action
    //        $display    - rows count display on form
    //        $sort=NULL  - sort parameter
    //        $start=NULL - start row for display
    // Returns : true,false / Void
    // Description : Write Form Sys Function
    // ================================================================================================
    // Programmer : Andriy Lykhodid
    // Date : 27.01.2005
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    
    function show( $user_id=NULL, $module_id=NULL, $display=NULL, $sort=NULL, $start=NULL )
    {
     if( $module_id ) $this->module_id = $module_id;
     if( $user_id ) $this->user_id = $user_id;
     if( $display ) $this->display = $display;
     if( $sort ) $this->sort = $sort;
     if( $start ) $this->start = $start;
    
     $scriptact = 'module='.$this->module_id;                            /* set action page-adress with parameters */
     if( empty($this->sort) ) $this->sort='id';                          /* set sort variable */
    
     $scriplink = $_SERVER['PHP_SELF']."?$scriptact";
    
    // $q = "SELECT `".TblSysFunc."`.*,`".TblSysSprFunc."`.name as names FROM  `".TblSysFunc."` ,`".TblSysSprFunc."` WHERE `".TblSysFunc."`.id = `".TblSysSprFunc."`.cod  AND `".TblSysSprFunc."`.lang_id='"._LANG_ID."'";
     $q = "SELECT `".TblSysFunc."`.*,`".TblSysSprFunc."`.name as names FROM  `".TblSysFunc."` LEFT JOIN `".TblSysSprFunc."` on (`".TblSysFunc."`.id = `".TblSysSprFunc."`.cod) WHERE  `".TblSysSprFunc."`.lang_id='"._LANG_ID."'";
     if( $this->fltr ) $q = $q." AND `target`='$this->fltr'";
     $q = $q." ORDER BY $this->sort";
    // echo '<br>$q='.$q.' $this->Right->result='.$this->Right->result.' $this->user_id='.$this->user_id;
     /*$res = $this->Right->Query( $q, $this->user_id, $this->module_id );
     if( !$res ) return false;
     $rows = $this->Right->db_GetNumRows();*/
     
     $result = $this->Right->QueryResult( $q, $this->user_id, $this->module_id );
       // if( !$result )return false;
       $rows = count($result);
     /* Write Form Header */
     $this->Form->WriteHeader( $scriplink );
    
     /* Write Table Part */
     AdminHTML::TablePartH();
    
     echo "<TR><TD COLSPAN=5>";
     /* Write Links on Pages */
     $this->Form->WriteLinkPages( $scriplink.'&fltr='.$this->fltr, $rows, $this->display, $this->start, $this->sort );
     echo '<TR><TD COLSPAN=5><div class="topPanel"><div class="SavePanel">';
    
     $scriplink = $scriplink.'&display='.$this->display.'&start='.$this->start.'&sort='.$this->sort;
    
     /* Write Top Panel (NEW,DELETE - Buttons) */
     $this->Form->WriteTopPanel( $scriplink.'&fltr='.$this->fltr );
    
     echo '</div><div class="SelectType">';
    
     $this->Form->SelectAct( PageAdmin::$type_action, 'fltr', $this->fltr, "onChange=\"location='$scriplink&start=0&fltr='+this.value\"" );
     $scriplink = $scriplink.'&fltr='.$this->fltr;
     ?>
     </div>
     </div>
     <tr>
     <Th class="THead">*</Th>
     <Th class="THead"> <?=$this->Form->Link( $scriplink."&sort=id", $this->Msg_text['FLD_ID'] );?>     </Th>
     <Th class="THead"> <?=$this->Msg_text['FLD_DESCRIPTION'];?> </Th>
     <Th class="THead"> <?=$this->Form->Link( $scriplink."&sort=name", $this->Msg_text['_FLD_PATH'] );?> </Th>
     <Th class="THead"> <?=$this->Form->Link( $scriplink."&sort=target", $this->Msg_text['_FLD_SYS_FUNC_TARGET'] );?> </Th>
     <?
     $a=$rows;
     $arr_s = array();
     //$this->Right->db_DataSeek( $this->start );
     for( $i = 0; $i < $rows; $i++ )
     {
      $row = $result[$i];
      if( $i >=$this->start && $i < ( $this->start+$this->display ) )
      {
       if ( (float)$i/2 == round( $i/2 ) )
    		   echo '<TR CLASS="TR1">';
       else
    		   echo '<TR CLASS="TR2">';
       $arr_s[$i+1] = $row['id'] ;
       ?>
       <td><?$this->Form->CheckBox( "id_del[]", $row['id'] );?></td>
       <td><?=$this->Form->Link( $scriplink."&task=edit&id=".$row['id'], stripslashes( $row['id'] ), $this->Msg_text['TXT_EDIT'] );?></td>
       <td align="left"><?=$row['names'];?></td>
       <td align="left"><?=$row['name'];?></td>
       <td align="center"><?=PageAdmin::$type_action[$row['target']];?></td>
       </tr>
       <?
       $a=$a-1;
      }
     }
     ?>
     </table>
     <?
    // print_r($arr_s);
    // echo implode(", ",$arr_s);
    /*$keys_a = array_keys($arr_s);
    print_r($arr_s);
    print_r($keys_a);
    
    for( $i = 1; $i <=$rows ; $i++ )
    {
        if($keys_a[$i-1]==$arr_s[$i]) continue;
    //    $q="update ".TblSysFunc." set id='".$keys_a[$i-1]."' where id='".$arr_s[$i]."';";
    //    echo '<br />',$q;
        $q="UPDATE `".TblSysAccess."` SET `function` = '".$keys_a[$i-1]."' where `function`='".$arr_s[$i]."';";
        echo '<br />',$q;
        $q="UPDATE `sys_spr_func` SET `cod` = '".$keys_a[$i-1]."' where `cod`='".$arr_s[$i]."';";
        echo '<br />',$q;
        //$res = $this->Right->Query( $q, $user_id, $module_id );
    }*/
     AdminHTML::TablePartF();
     /* Write Form Footer */
     $this->Form->WriteFooter();
    }
    
    // ================================================================================================
    // Function : edit()
    // Version : 1.0.0
    // Date : 27.01.2005
    //
    // Parms :
    //           $module_id - id of current module
    //           $user_id   - id of current user
    //           $id        - id of record
    // Returns : true,false / Void
    // Description : Show data from $spr table for editing
    // ================================================================================================
    // Programmer : Andriy Lykhodid
    // Date : 27.01.2005
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function edit( $user_id=NULL, $module_id=NULL, $id=NULL, $mas=NULL )
    {
     if( $module_id ) $this->module_id = $module_id;
     if( $user_id ) $this->user_id = $user_id;
    
     $Panel = new Panel();
     $ln_sys = new SysLang();
    
     $fl = false;
     if( is_array($mas) )
     {
       $fl = true;
     }
    
     /* set action page-adress with parameters */
     $scriptact = $_SERVER['PHP_SELF'].'?module='.$this->module_id.'&display='.$_REQUEST['display'].'&start='.$_REQUEST['start'].'&sort='.$_REQUEST['sort'];
    
     $Panel->WritePanelHead( "EditPanel_" );
    
     if( $id AND (!isset($mas['id'])) )
     {
      $q="select * from ".TblSysFunc." where id='$id' order by id";
      $res = $this->Right->Query( $q, $user_id, $module_id );
      if( !$res ) return false;
      $mas = $this->Right->db_FetchAssoc();
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
    
     //-------- Show Error text for validation fields --------------
     $this->ShowErrBackEnd();
     //-------------------------------------------------------------
    
     /* Write Simple Panel*/
     AdminHTML::PanelSimpleH();
    
     ?>
      <tr>
      <td><b><?echo $this->Msg_text['FLD_ID']?></b></td>
      <TD> <?
    		if( $id )
    		{
    		  echo $mas['id'];
    		  $this->Form->Hidden( 'id', $mas['id'] );
    		}
    		$this->Form->Hidden( 'fltr', $this->fltr );
    		
    		if( $id!=NULL ) $this->Err!=NULL ? $val=$this->name : $val=$mas['name'];
    		else $val=$this->name;         
    		?>
    		<TR><TD><b><?=$this->Msg_text['_FLD_PATH']?></b>
    			<TD>/ <?$this->Form->Select( PageAdmin::$type_action, 'target', $mas['target'], 70 );?> / <?$this->Form->TextBox( 'name', $val, 70 )?>
    		<TR><TD><b><?=$this->Msg_text['_FLD_SYS_FUNC_TARGET']?> </b>
    			<TD>
    		<?
    		
     ?>
     <tr><td colspan=2>
     <?
    
     $Panel->WritePanelHead( "SubPanel_" );
     $ln_arr = $ln_sys->LangArray( _LANG_ID );
     if( isset($mas['id']) ) $row = $this->Spr->GetByCod( TblSysSprFunc, $mas['id'] );
     while( $el = each( $ln_arr ) )
     {
    	  $lang_id = $el['key'];
    	  $lang = $el['value'];
    	  $mas_s[$lang_id] = $lang;
    
    	  $Panel->WriteItemHeader( $lang );
    	  echo "\n <table border=0 class='EditTable'>";
    	  echo "\n <tr>";
    	  echo "\n <td><b>".$this->Msg_text['FLD_DESCRIPTION'].":</b>";
    	  echo "\n <td>";
    	  if( !isset($mas['id']) ) $this->Form->TextBox( 'description['.$lang_id.']', '', 80 );
    //      if( $fl ) $this->Form->TextBox( 'description['.$lang_id.']', $mas['description'][$lang_id], 80 );
    	  else $this->Form->TextBox( 'description['.$lang_id.']', $row[$lang_id], 80 );
    	  echo "\n <td rowspan=3>";
    	  echo   "\n </table>";
    	  $Panel->WriteItemFooter();
     }
     $Panel->WritePanelFooter();
     AdminHTML::PanelSimpleF();
     $this->Form->WriteSavePanel( $scriptact );
     $this->Form->WriteCancelPanel( $scriptact );
     AdminHTML::PanelSubF();
     $this->Form->WriteFooter();
     return true;
    }
    
    // ================================================================================================
    // Function : save()
    // Version : 1.0.0
    // Date : 28.01.2005
    //
    // Parms :   $user_id, $module_id, $id, $name, $description
    // Returns : true,false / Void
    // Description : Store data to the table
    // ================================================================================================
    // Programmer : Andriy Lykhodid
    // Date : 27.01.2005
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function save( $user_id, $module_id, $id, $name, $description, $target )
    {
     if ( empty($name) ) {
    	$this->Msg->show_msg('_EMPTY_NAME_FIELD');
    	$this->edit( $user_id, $module_id, $id, $_REQUEST );
    	return false;
     }
     if ( empty($description[_LANG_ID]) ) {
    	$this->Msg->show_msg('_EMPTY_DESCRIPTION_FIELD');
    	$this->edit( $user_id, $module_id, $id, $_REQUEST );
    	return false;
     }
     $ln_sys = new SysLang();
    
     $name = addslashes( trim($name) );
    
     $q="select `id` from ".TblSysFunc." where id='$id'";
     $res = $this->Right->Query( $q, $user_id, $module_id );
     if( !$this->Right->result ) return false;
     $rows = $this->Right->db_GetNumRows();
     if($rows>0) //Update
     {
       $q="update ".TblSysFunc." set name='$name', target='$target'";
       $q=$q." where id='$id'";
       $res = $this->Right->Query( $q, $user_id, $module_id );
       //echo '<br>1 $q='.$q.' $this->Right->result='.$this->Right->result.' $res='.$res;
       if( !$this->Right->result ) return false;
     }
     else  //Insert
     {
      $q = "select `id` from ".TblSysFunc." where id='$id'";
      $res = $this->Right->Query( $q, $user_id, $module_id );
      //echo '<br>2 $q='.$q.' $this->Right->result='.$this->Right->result.' $res='.$res;
      if( !$this->Right->result ) return false;
      $rows = $this->Right->db_GetNumRows();
      if( $rows>0 ) return false;
    
      $q="insert into ".TblSysFunc." values(NULL,'$name','$target')";
      $res = $this->Right->Query( $q, $user_id, $module_id );
      //echo '<br>3 $q='.$q.' $this->Right->result='.$this->Right->result.' $res='.$res;
      if( !$this->Right->result ) return false;
     }
    
     if ( empty($id) ){
      $q = "select `id` from ".TblSysFunc." where name='$name' AND target='$target'";
      $res = $this->Right->Query( $q, $user_id, $module_id );
      if( !$this->Right->result ) return false;
      $row=$this->Right->db_FetchAssoc();
      $id = $row['id'];
     }
     //echo '<br>$id='.$id;
     // Save Description on different languages
     $res=$this->Spr->SaveNameArr( $id, $description, TblSysSprFunc );
     if( !$res ) return false;
    
     return true;
    }
    
    // ================================================================================================
    // Function : del()
    // Version : 1.0.0
    // Date : 27.01.2005
    //
    // Parms :   $user_id, $module_id, $id_del
    // Returns : true,false / Void
    // Description :  Remove data from the table
    // ================================================================================================
    // Programmer : Andriy Lykhodid
    // Date : 27.01.2005
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function del( $user_id, $module_id, $id_del )
    {
    	$del=0;
    	$kol=count( $id_del );
    	for( $i=0; $i<$kol; $i++ )
    	{
    	 $u=$id_del[$i];
    	 $q="delete from ".TblSysFunc." where id='$u'";
    	 $res = $this->Right->Query( $q, $user_id, $module_id );
    	 if( !$this->Right->result )return false;
    	 $res = $this->Spr->DelFromSpr( TblSysSprFunc, $u );
    	 if( !$res )return false;
    	 $del=$del+1;
    	}
      return $del;
    }
    
    // ================================================================================================
    // Function : ExistFunc()
    // Version : 1.0.0
    // Date : 06.09.2007
    //
    // Parms :   $name
    // Returns : true,false / Void
    // Description :  check exist or not system function already
    // ================================================================================================
    // Programmer : Ihor Trokhymchuk
    // Date : 06.09.2007
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function ExistFunc( $name )
    {
    	$tmp_db = DBs::getInstance();
    	$q="SELECT `id` FROM ".TblSysFunc." WHERE `name`='$name'";
    	$res = $tmp_db->db_Query( $q );
    	//echo '<br>$q='.$q.' $res='.$res.' $tmp_db->result='.$tmp_db->result;
    	if( !$res OR !$tmp_db->result ) return false;
    	$rows = $tmp_db->db_GetNumRows();
    	return $rows;
    } //end of function ExistFunc()
    
    
    // ================================================================================================
    // Function : ShowErrBackEnd()
    // Version : 1.0.0
    // Date : 10.01.2006
    //
    // Parms :
    // Returns :      true,false / Void
    // Description :  Show errors
    // ================================================================================================
    // Programmer :  Igor Trokhymchuk
    // Date : 10.01.2006
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function ShowErrBackEnd( $err = NULL)
    {
       if ($this->Err) $err = $this->Err;
       if( !empty($err) ) {
    	   ?>
    	   <table border="0" cellspacing="0" cellpadding="0" class="err" align="center" width="100%">
    		<tr><td align="center"><?=$err;?></td></tr>
    	   </table>               
    	   <?
       }
    } //end of fuinction ShowErrBackEnd() 
} // end of class