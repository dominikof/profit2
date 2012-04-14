<?
// ================================================================================================
//    System     : SEOCMS
//    Module     : Sys Lang Control
//    Version    : 1.0.0
//    Date       : 11.01.2006
//    Licensed To:
//                 Andriy Lykhodid    las_zt@mail.ru
//    Purpose    : Class definition for System Langouges - moule
//
// ================================================================================================


// ================================================================================================
//    Class             : SysLangCtrl
//    Version           : 1.0.0
//    Date              : 11.01.2006
//    Constructor       : Yes
//    Parms             :
//    Returns           : None
//    Description       : System Languages
// ================================================================================================
//    Programmer        :  Andriy Lykhodid
//    Date              :  11.01.2006
//    Reason for change :  Creation
//    Change Request Nbr:  N/A
// ================================================================================================

/**
* Class SysLangCtrl
* Class for all actions with languages in SEOCMS
* @package System Package of SEOCMS
* @author Igor Trokhymchuk  <ihor@seotm.com>
* @version 1.1, 02.04.2012
* @copyright (c) 2005+ by SEOTM
*/
class SysLangCtrl {
     public   $id;
     public   $cod;
     public   $short_name;
     public   $front;
     public   $back;
     public   $def_front;
     public   $def_back;
     public   $encoding;
     public   $lang_img;
    
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
    * SysLangCtrl::__construct()
    * 
    * @param integer $user_id
    * @param integer $module_id
    * @param integer $display
    * @param string $sort
    * @param integer $start
    * @return void
    */

    function __construct($user_id = NULL, $module = NULL)
    {
        $this->user_id = $user_id;
        $this->module = $module;
        $this->Right =  new Rights($this->user_id, $this->module);                   /* create Rights obect as a property of this class */
        $this->Form = new Form( 'form_syslang' );        /* create Form object as a property of this class */
        $this->Msg = &check_init_txt('TblBackMulti',TblBackMulti);                   /* create ShowMsg object as a property of this class */
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
     $db = new Rights($this->user_id, $this->module);
     $frm = new Form('fltr');
    
     /* Write Table Part */
     AdminHTML::PanelSimpleH();
    if( !$this->sort ) $this->sort='id';
      echo '<table class="EditTable">';
      echo '<tr><td valign=top>';
    
      echo '<table class="EditTable" width=350>';
      echo '<tr><td><b>';
      echo $this->Msg['sys_lang_def_front'].': ';
      $q = "SELECT `".TblModSysLang."`.*,`".TblModSysLangSpr."`.name FROM `".TblModSysLang."`,`".TblModSysLangSpr."` where `".TblModSysLang."`.cod= `".TblModSysLangSpr."`.cod and `".TblModSysLangSpr."`.lang_id = '"._LANG_ID."' ";
       $q = $q." order by $this->sort";
    //   echo $q;
      $result = $this->Right->QueryResult( $q, $this->user_id, $this->module );
      if( !$result )return false;
      $rows = count ($result);
      $default_fr = '';
      $default_back = '';
      $used_fr = '';
      $used_back = '';
      
      for( $i=0; $i<$rows; ++$i)
      {
    	  if($result[$i]['def_front']==1) $default_fr.=$result[$i]['cod'].'-'.$result[$i]['name'].'</b>';
    	  if($result[$i]['front']==1) $used_fr.='<tr><td>'.$result[$i]['cod'].'-'.$result[$i]['name'];
    	  if($result[$i]['def_back']==1) $default_back.=$result[$i]['cod'].'-'.$result[$i]['name'].'</b>';
    	  if($result[$i]['back']==1) $used_back.='<tr><td>'.$result[$i]['cod'].'-'.$result[$i]['name'];
    	  
      }
      echo $default_fr;
      echo '<tr><td ALIGN=center><u>'.$this->Msg['sys_lang_front'].'</u>';
      /*$q = "SELECT * FROM ".TblModSysLang." where front='1'";
      $res = $this->Right->Query( $q, $this->user_id, $this->module );
      $rows = $this->Right->db_GetNumRows();
      for( $i = 0; $i < $rows; $i++ )
      {
    	$row = $this->Right->db_FetchAssoc();
    	echo '<tr><td>'.$row['cod'].'-'.$this->Spr->GetNameByCod( TblModSysLangSpr, $row['cod'] );
      }*/
      echo $used_fr,'</table>';
    
      echo '<td valign=top>';
      echo '<table class="EditTable" width=350>';
      echo '<tr><td><b>';
     /* $q = "SELECT * FROM ".TblModSysLang." where def_back='1'";
      $res = $this->Right->Query( $q, $this->user_id, $this->module );
      $row = $this->Right->db_FetchAssoc();
      if( !$res )return false;*/
      echo $this->Msg['sys_lang_def_back'].': ';
      echo $default_back,'</b>';
      echo '<tr><td ALIGN=center><u>',$this->Msg['sys_lang_back'],'</u>',$used_back;
      /*$q = "SELECT * FROM ".TblModSysLang." where back='1'";
      $res = $this->Right->Query( $q, $this->user_id, $this->module );
      $rows = $this->Right->db_GetNumRows();
      for( $i = 0; $i < $rows; $i++ )
      {
    	$row = $this->Right->db_FetchAssoc();
    	echo '<tr><td>'.$row['cod'].'-'.$this->Spr->GetNameByCod( TblModSysLangSpr, $row['cod'] );
      }*/
      echo '</table>';
      echo '</table>';
    
     AdminHTML::PanelSimpleF();
     $script = 'module='.$this->module.'&display='.$this->display.'&start='.$this->start.'&sort='.$this->sort.'&fltr='.$this->fltr;
     $script = $_SERVER['PHP_SELF']."?$script";
    
     if( $this->fltr ) $q = $q." and $this->fltr"; 
     /*$q = "SELECT * FROM ".TblModSysLang." where 1 ";
    */
    /* $res = $this->Right->Query( $q, $this->user_id, $this->module );
     if( !$res )return false;
    
     $rows = $this->Right->db_GetNumRows();
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
    
     echo '</div></div><td><td><td colspan=2>';
     $script2 = 'module='.$this->module.'&display='.$this->display.'&start='.$this->start.'&task=show&fltr='.$this->fltr;
     $script2 = $_SERVER['PHP_SELF']."?$script2";
    ?>
     <TR>
     <Th class="THead">*</Th>
     <Th class="THead"><A HREF=<?=$script2?>&sort=id><?=$this->Msg['FLD_ID']?></A></Th>
     <Th class="THead"><A HREF=<?=$script2?>&sort=cod><?=$this->Msg['sys_lang_cod']?></A></Th>
     <Th class="THead"><?=$this->Msg['_FLD_LANGUAGE']?></Th>
     <Th class="THead"><A HREF=<?=$script2?>&sort=short_name><?=$this->Msg['sys_lang_short']?></A></Th>
     <Th class="THead"><A HREF=<?=$script2?>&sort=lang_img><?=$this->Msg['_FLD_IMAGE']?></A></Th>
     <Th class="THead"><A HREF=<?=$script2?>&sort=front><?=$this->Msg['sys_lang_front']?></A></Th>
     <Th class="THead"><A HREF=<?=$script2?>&sort=back><?=$this->Msg['sys_lang_back']?></A></Th>
     <Th class="THead"><A HREF=<?=$script2?>&sort=def_front><?=$this->Msg['sys_lang_def_front']?></A></Th>
     <Th class="THead"><A HREF=<?=$script2?>&sort=def_back><?=$this->Msg['sys_lang_def_back']?></A></Th>
     <Th class="THead"><A HREF=<?=$script2?>&sort=encoding><?=$this->Msg['sys_lang_encoding']?></A></Th>
     <?
     $a = $rows;
     $j = 0;
     $row_arr = NULL;
     for( $i = 0; $i < $rows; $i++ )
     {
       $row = $result[$i];
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
       $this->Form->Link( $script."&task=edit&id=".$row['id'], stripslashes( $row['id'] ), $this->Msg['TXT_EDIT'] );
    
       echo '<TD align=center>'.$row['cod'];
    
       echo '<TD align=left>'.$this->Spr->GetNameByCod( TblModSysLangSpr, $row['cod'] );
       echo '<TD align=center>'.$row['short_name'];
       echo '<TD align=center>';
       if( $row['lang_img'] )
       {
    	$this->Form->ButtonCheck();
       }
    
       echo '<TD align=center>';
       if( $row['front'] == 1 )
       {
    	$this->Form->ButtonCheck();
       }
    
       echo '<TD align=center>';
       if( $row['back'] == 1 )
       {
    	$this->Form->ButtonCheck();
       }
    
       echo '<TD align=center>';
       if( $row['def_front'] == 1 )
       {
    	$this->Form->ButtonCheck();
       }
    
       echo '<TD align=center>';
       if( $row['def_back'] == 1 )
       {
    	$this->Form->ButtonCheck();
       }
    
       echo '<TD align=center>'.$row['encoding'];
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
       $q = "SELECT * FROM ".TblModSysLang." where id='$this->id'";
       $res = $this->Right->Query( $q, $this->user_id, $this->module );
       if( !$res ) return false;
       $mas = $this->Right->db_FetchAssoc();
     }
    
     /* Write Form Header */
     $this->Form->WriteHeader( $script );
     if( $this->id!=NULL ) $txt = $this->Msg['TXT_EDIT'];
     else $txt = $this->Msg['_TXT_ADD_DATA'];
     AdminHTML::PanelSubH( $txt );
     AdminHTML::PanelSimpleH();
    
    ?>
    <tr><td valign=top width=150 align=left>
    <tr><td width=650>
    <table class="EditTable">
     <TR><TD align=right><?=$this->Msg['FLD_ID']?>
     <TD align=left>
    <?
       if( $this->id != NULL )
       {
    	echo $mas['id'];
    	$this->Form->Hidden( 'id', $mas['id'] );
    	$this->Form->Hidden( 'cod', $mas['cod'] );
       }
       else $this->Form->Hidden( 'id', '' );
    ?>
     <tr><td align=right><?=$this->Msg['_FLD_LANGUAGE']?>
     <td align=left><?=$this->Spr->ShowInComboBox( TblModSysLangSpr, 'cod', $mas['cod'], '120' );?>
     <tr><td align=right><?=$this->Msg['sys_lang_short']?>
     <td>
    <?if( $this->id != NULL or ( $mas != NULL ) )
     {
      $this->Form->TextBox( 'short_name', $mas['short_name'], 4 );
     }else
      $this->Form->TextBox( 'short_name', '', 4 );
    ?>
    
     <tr><td align=right><?=$this->Msg['sys_lang_front']?>
     <td align=left>
    <? $arr = NULL;
      $arr['1'] = 'Yes';
      $arr['0'] = 'No';
      if( !isset( $mas['front'] ) )  $this->Form->Select( $arr, 'front', 0, NULL );
      else $this->Form->Select( $arr, 'front', $mas['front'], NULL );?>
    
     <tr><td align=right><?=$this->Msg['sys_lang_back']?>
     <td align=left>
    <? $arr = NULL;
      $arr['1'] = 'Yes';
      $arr['0'] = 'No';
      if( !isset( $mas['back'] ) )  $this->Form->Select( $arr, 'back', 0, NULL );
      else $this->Form->Select( $arr, 'back', $mas['back'], NULL );?>
    
     <tr><td align=right><?=$this->Msg['sys_lang_def_front']?>
     <td align=left>
    <? $arr = NULL;
      $arr['1'] = 'Yes';
      $arr['0'] = 'No';
      if( !isset( $mas['def_front'] ) )  $this->Form->Select( $arr, 'def_front', 0, NULL );
      else $this->Form->Select( $arr, 'def_front', $mas['def_front'], NULL );?>
    
     <tr><td align=right><?=$this->Msg['sys_lang_def_back']?>
     <td align=left>
    <? $arr = NULL;
      $arr['1'] = 'Yes';
      $arr['0'] = 'No';
      if( !isset( $mas['def_back'] ) )  $this->Form->Select( $arr, 'def_back', 0, NULL );
      else $this->Form->Select( $arr, 'def_back', $mas['def_back'], NULL );?>
    
    
     <tr><td align=right><?=$this->Msg['sys_lang_encoding']?>
     <td>
    <?if( $this->id != NULL or ( $mas != NULL ) )
     {
      $this->Form->TextBox( 'encoding', $mas['encoding'], 20 );
     }else
      $this->Form->TextBox( 'encoding', '', 20 );
    ?>
     </table>
    </table>
    <?
    AdminHTML::PanelSimpleF();
     $this->Form->WriteSavePanel( $script );
     $this->Form->WriteFooter();
     
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
       $q = "SELECT * FROM ".TblModSysLang." WHERE `id`='$this->id'";
       $res = $this->Right->Query( $q, $this->user_id, $this->module );
       //echo '<br>$q='.$q.' $res='.$res;
       if( !$res ) return false;
       $rows = $this->Right->db_GetNumRows();
       //echo '<br>$rows='.$rows.' $this->def_front='.$this->def_front.' $this->def_back='.$this->def_back.' $this->cod='.$this->cod;
       
     if( $this->def_front == '1' )
     {
       $q = "update `".TblModSysLang."` set `def_front`='0'";
       $res = $this->Right->Query( $q, $this->user_id, $this->module );
       //echo '<br>$q='.$q.' $res='.$res; 
     }
     if( $this->def_back == '1' )
     {
       $q = "update `".TblModSysLang."` set `def_back`='0'";
       $res = $this->Right->Query( $q, $this->user_id, $this->module );
       //echo '<br>$q='.$q.' $res='.$res; 
     }
     if( $this->cod )
     {
       $q = "SELECT * FROM ".TblModSysLang." WHERE `cod`='$this->cod' and id!='$this->id'";
       $res = $this->Right->Query( $q, $this->user_id, $this->module );
       //echo '<br>$q='.$q.' $res='.$res;
       if( !$res ) return false;
       $rows2 = $this->Right->db_GetNumRows();
       if( $rows2 > 0 ) return false;
     }
     else return false;
    
       if( $rows > 0 )   //--- update
       {
    	  $q = "update `".TblModSysLang."` set
    		   `cod`='$this->cod',
    		   `short_name`='$this->short_name',
    		   `front`='$this->front',
    		   `back`='$this->back',
    		   `def_front`='$this->def_front',
    		   `def_back`='$this->def_back',
    		   `encoding`='$this->encoding',
    		   `lang_img`='$this->lang_img'
    			where id='$this->id'";
    	 $res = $this->Right->Query( $q, $this->user_id, $this->module );
    	 //echo '<br>$q='.$q.' $res='.$res; 
    	 if( !$res ) return false;
       }
       else          //--- insert
       {
    	 $q = "insert into `".TblModSysLang."` values(NULL, '$this->cod', '$this->short_name', '$this->front', '$this->back', '$this->def_front', '$this->def_back', '$this->encoding', '$this->lang_img')";
    	 $res = $this->Right->Query( $q, $this->user_id, $this->module );
    	 //echo '<br>$q='.$q.' $res='.$res;
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
    	 $q = "DELETE FROM `".TblModSysLang."` WHERE id='$u'";
    	 $res = $this->Right->Query( $q, $this->user_id, $this->module );
    	 if( !$res )return false;
    	 if( $res )
    	  $del = $del + 1;
    	 else
    	  return false;
    	}
      return $del;
    }
    
    function GetShortNameByCod( $cod )
    {
     $q = "SELECT short_name FROM `".TblModSysLang."` WHERE cod='$cod' AND lang_id='"._LANG_ID."'";
     $res = $this->Right->db_Query( $q );
     if ( !$this->Right->result ) return false;
     $row_res = $this->Right->db_FetchAssoc();
     if( empty( $row_res['name'] ) ) return 0;
     else return $row_res['name'];
    }
} //--- end of class?>