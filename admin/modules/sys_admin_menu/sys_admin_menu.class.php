<?
include_once( SITE_PATH.'/admin/modules/sys_group/sys_group.class.php' );

/**
* Class AdminMenu
* Class for all actions with Admin Menu of Content System Management
* @package System Package of SEOCMS
* @author Igor Trokhymchuk  <ihor@seotm.com>
* @version 1.1, 02.04.2012
* @copyright (c) 2005+ by SEOTM
*/
class AdminMenu {

    public  $Right;
    public  $Form;
    public  $Msg;
    public  $Spr;
    
    public  $display;
    public  $sort;
    public  $start=0;
    public  $fltr;
    
    public  $user_id;
    public  $module_id;

    /**
    * AdminMenu::__construct()
    * 
    * @param integer $user_id
    * @param integer $module_id
    * @param integer $display
    * @param string $sort
    * @param integer $start
    * @return void
    */
    function __construct( $user_id=NULL, $module_id=NULL, $display=NULL, $start=NULL, $sort=NULL )
    {
     $this->user_id = $user_id;
     $this->module_id = $module_id;
     $this->display = $display;
     if( !empty( $sort ) ) $this->sort = $sort;
     if( !empty( $start ) ) $this->start = $start;
         
     $this->Right =  new Rights($this->user_id, $this->module_id);  
     $this->Form = new Form( 'form_sys_func' );
     $this->Msg = &check_init_txt('TblBackMulti',TblBackMulti);
     $this->Spr = new SysSpr(); 
    
    }
    
    
    // ================================================================================================
    // Function : show()
    // Version : 1.0.0
    // Date : 28.01.2005
    // Parms :
    //        $module_id  - id of this module in system
    //        $user_id    - id of current user
    //        $action     - script action
    //        $display    - rows count display on form
    //        $sort=NULL  - sort parameter
    //        $start=NULL - start row for display
    //        $level = 0  - level of menu  (0 - first level)
    // Returns : true,false / Void
    // Description : Write Form Sys Function
    // ================================================================================================
    // Programmer : Oleg Morgalyuk
    // Date : 05.02.2010
    // Reason for change : new object Right using singleton BD
    // Change Request Nbr:
    // ================================================================================================
    
    function show( $user_id=NULL, $module_id=NULL, $display=NULL, $start=NULL, $sort=NULL, $level=0 )
    {
     $id = AntiHacker::AntiHackRequest('id');
     if( $module_id ) $this->module_id = $module_id;
     if( $user_id ) $this->user_id = $user_id;
     if( $display ) $this->display = $display;
     if( $sort ) $this->sort = $sort;
     if( $start ) $this->start = $start;
     $scriptact = 'module='.$this->module_id;              /* set action page-adress with parameters */
     $scriplink = $_SERVER['PHP_SELF']."?$scriptact&fltr=$this->fltr";
    
     if( !$sort ) $sort='move';
     //if( !$sort ) $sort='group_menu';
     //$q = "select `".TblSysMenuAdm."`.*,`".TblSysSprMenuAdm."`.name from `".TblSysMenuAdm."`,`".TblSysSprMenuAdm."` where level='$level' and `".TblSysMenuAdm."`.id=`".TblSysSprMenuAdm."`.cod and `".TblSysSprMenuAdm."`.lang_id='"._LANG_ID."'";
     
      
     $q = "select `".TblSysMenuAdm."`.*,`".TblSysSprMenuAdm."`.name 
            from `".TblSysMenuAdm."` LEFT JOIN `".TblSysSprMenuAdm."` 
                on (`".TblSysMenuAdm."`.id=`".TblSysSprMenuAdm."`.cod) 
            where level='$level' and  `".TblSysSprMenuAdm."`.lang_id='"._LANG_ID."'";
     if( $this->fltr ) $q = $q." and `group`='$this->fltr'";
     $q = $q." order by $sort";
     //echo $q; 
     $result = $this->Right->db_QueryResult( $q, $this->user_id, $this->module_id );
     //echo '<br />$result='.$result;print_r($result);
     //if( !$result )return false;
     $rows = count($result);
     //echo '<br />$rows='.$rows;
     
     /* Write Form Header */
     $this->Form->WriteHeader( $scriplink."&level=$level".'&display='.$this->display.'&start='.$this->start.'&sort='.$this->sort );
     /* Write Table Part */
     /* Write Links on Pages */
     echo '<div>';
     $this->Form->WriteLinkPages( $scriplink."&level=$level", $rows, $this->display, $this->start, $this->sort );
     echo '</div><div class="topPanel"><div class="SavePanel">';
    
     $scriplink = $scriplink.'&display='.$this->display.'&start='.$this->start.'&sort='.$this->sort;
     /* Write Top Panel (NEW,DELETE - Buttons) */
     $this->Form->WriteTopPanel( $scriplink."&level=$level".'&display='.$this->display.'&start='.$this->start.'&sort='.$this->sort );
    
     //echo '<TR><TD COLSPAN=7>';
     $top_lev = $this->get_top_level( $level );
     if( $top_lev )
     {
       $top = $this->get_level_name( $top_lev['level'] );
       if( $top_lev['level']!=0 )
       {
         $tmp = $this->Spr->GetNameByCod( TblSysSprMenuAdm, $top['id'] );
       }
       else
       {
         $tmp = $this->Msg['_LNK_UP_LEVEL'];
       }
       ?>
        <a class="r-button" href=<?=$_SERVER['PHP_SELF']."?$scriptact"."&task=show&level=".$top_lev['level'].'&fltr='.$this->fltr;?>>
        <span><span><IMG src='images/icons/restore.png' alt="Go to:" align="middle" border="0" name="restore"><?=$tmp?></span></span></a>
       <?
     }
    
     echo '</div><div class="SelectType">';
    
     $arr = SysGroup::GetGrpToArr( $this->user_id, $this->module_id, NULL );
     $grp = NULL;
     $max = count( $arr ); 
     for( $i = 0; $i < $max; ++$i )
     {
       $grp[$arr[$i]['id']] = $arr[$i]['name'];
     }
     $this->Form->SelectAct( $grp, 'group', $this->fltr, "onChange=\"location='$scriplink'+'&fltr='+this.value\"" );
    ?>
    </div>
    </div>
    <?if( $top_lev )
     {?>
    <div class="SpecialCaption"><b class="LinkPagesSel"><?=$this->Spr->GetNameByCod( TblSysSprMenuAdm, $top_lev['id'] )?></b></div>
    <?}
    AdminHTML::TablePartH(); ?>
     <tr>
     <td class="THead"> *</td>
     <td class="THead"> <?=$this->Msg['FLD_ID']?></td>
     <td class="THead"> <?=$this->Msg['FLD_DESCRIPTION']?></td>
     <td class="THead"> <?=$this->Msg['_FLD_FUNCTION']?></td>
     <td class="THead"> <?=$this->Msg['FLD_SUBLEVEL']?></td>
     <td class="THead"> <?=$this->Msg['_FLD_UP']?></td>
     <td class="THead"> <?=$this->Msg['_FLD_DOWN']?></td>
     <td class="THead"> <?=$this->Msg['FLD_GROUP']?></td>
    <?
    $a=$rows;
    $up=0;
    $down=0;
    for( $i = 0; $i < $rows; ++$i )
    {
     
        $row = &$result[$i];  
     if( $i >=$this->start && $i < ( $this->start+$this->display ) )
     {
       if ( (float)$i/2 == round( $i/2 ) )
               echo '<TR CLASS="TR1">';
       else
               echo '<TR CLASS="TR2">';
       $down=$row['id'];
       echo '<TD>';
       $this->Form->CheckBox( "id_del[]", $row['id'] );
       echo '<TD>';
       $this->Form->Link( $scriplink."&task=edit&id=".$row['id']."&level=".$row['level'], stripslashes( $row['id'] ), $this->Msg['TXT_EDIT'] );
    
       echo '<TD> ';
       $function_id = $row['function'];
       if( $function_id )
        $this->Form->Link( $_SERVER['PHP_SELF']."?module=".$function_id, $row['name'] );
       else
        echo $row['name'] ;
       echo ' </TD><TD>',$this->Spr->GetNameByCod( TblSysSprFunc, $row['function'] ),'</TD><TD>';
       $this->Form->Link( $scriplink."&task=show&level=".$row['id'], $this->Msg['FLD_SUBLEVEL'] );
       echo '<TD align=center>';
       if( $up!=0 )
       {
       ?>
        <a href=<?=$scriplink?>&level=<?=$level?>&task=up&move=<?=$row['move']?>>
        <?=$this->Form->ButtonUp( $row['id'] );?>
        </a>
       <?
       }
       echo '<TD>';
       if( $i!=($rows-1) )
       {
       ?>
         <a href=<?=$scriplink?>&level=<?=$level?>&task=down&move=<?=$row['move']?>>
         <?=$this->Form->ButtonDown( $row['id'] );?>
         </a>
       <?
       }
       $up=$row['id'];
    //   echo '<td>';
    //   $arr = SysGroup::GetGrpToArr( $this->user_id, $this->module_id,  $row['group'] );
    //   echo $arr[0]['name']; 
       echo '<td>',$grp[$row['group']],'</TR>';
       $a=$a-1;
      }
    }
    AdminHTML::TablePartF();
    $this->Form->WriteFooter();
    }
    
    
    
    // ================================================================================================
    // Function : edit()
    // Version : 1.0.0
    // Date : 28.01.2005
    //
    // Parms :   $user_id=NULL, $module_id=NULL, $id=NULL, $mas=NULL, $level=0
    // Returns : true,false / Void
    // Description : Show data from $spr table for editing
    // ================================================================================================
    // Programmer : Andriy Lykhodid
    // Date : 31.01.2005
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    
    function edit( $user_id=NULL, $module_id=NULL, $id=NULL, $level=0, $mas=NULL )
    {
     if( $module_id ) $this->module_id = $module_id;
     if( $user_id ) $this->user_id = $user_id;
    
     $Panel = new Panel();
     $ln_sys = &check_init('LangSys','SysLang');
    // $ln_sys = &check_init('LangSys','SysLang');
    
     $fl = NULL;
     if( $mas )
     {
       $fl = 1;
     }
    
     /* set action page-adress with parameters */
     $scriptact = $_SERVER['PHP_SELF'].'?module='.$this->module_id.'&display='.$_REQUEST['display'].'&start='.$_REQUEST['start'].'&sort'.$_REQUEST['sort'];
    
     if( $id!=NULL and ( $mas==NULL ) )
     {
      $q="select `".TblSysMenuAdm."`.*,`".TblSysSprMenuAdm."`.name from `".TblSysMenuAdm."` LEFT JOIN `".TblSysSprMenuAdm."` ON
       (`".TblSysSprMenuAdm."`.cod = `".TblSysMenuAdm."`.level AND `".TblSysSprMenuAdm."`.lang_id = '"._LANG_ID."' )
        where `".TblSysMenuAdm."`.id='$id'";
    //  echo $q;  
    //  exit();
      $res = $this->Right->Query( $q, $this->user_id, $this->module_id );
      if( !$res )return false;
      $mas = $this->Right->db_FetchAssoc();
     }
     /* Write Form Header */
     $this->Form->WriteHeader( $scriptact );
    ?>
    <?
       if( $id!=NULL ) $txt = $this->Msg['TXT_EDIT'];
       else $txt = $this->Msg['_TXT_ADD_DATA'];
       AdminHTML::PanelSubH( $txt );
       AdminHTML::PanelSimpleH();
    ?>
     <TR><TD><b><?echo $this->Msg['FLD_ID'];?></b>
     <TD>
    <?
     if( $id!=NULL )
     {
       echo $mas['id'];
       $this->Form->Hidden( 'id', $mas['id'] );
     }else $this->Form->Hidden( 'id', '' );
     $this->Form->Hidden( 'level', $level );
     $level_name = $this->get_level_name( $mas['level']);
    
     echo '<TR><TD><b>',$this->Msg['FLD_GROUP'],'</b><TD>';
    
     if( $id!=NULL or ( $mas!=NULL ) )
     {
      $arr = SysGroup::GetGrpToArr( $this->user_id, $this->module_id,  $mas['group'] );
      echo $arr[0]['id'],' - ',$arr[0]['name'];
      $this->Form->Hidden( 'group', $mas['group'] );
      $this->Form->Hidden( 'fltr', $mas['group'] );
      $this->fltr = $mas['group'];
     }else
     {
      $arr = SysGroup::GetGrpToArr( $this->user_id, $this->module_id,  $this->fltr );
      echo $arr[0]['id'],' - ',$arr[0]['name'];
      $this->Form->Hidden( 'group', $this->fltr );
      $this->Form->Hidden( 'fltr', $this->fltr );
     }
     if($mas['level']>0){
    ?>
     <TR><TD><b><?echo $this->Msg['_FLD_LEVEL']?></b>
         <TD><b><?=$mas['level'].' - '.$mas['name'];?></b>
    <?
     }
     ?>
     <tr><td colspan=2>
    <?
     $Panel->WritePanelHead( "SubPanel_" );
     $ln_arr = $ln_sys->LangArray( _LANG_ID );
      while( $el = each( $ln_arr ) )
     {
          $lang_id = $el['key'];
          $lang = $el['value'];
          $mas_s[$lang_id] = $lang;
    
          $Panel->WriteItemHeader( $lang );
          echo "\n <table border=0 class='EditTable'> \n <tr><td><b>",$this->Msg['FLD_DESCRIPTION'],":</b>\n <td>";
          $row = $this->Spr->GetByCod( TblSysSprMenuAdm, $mas['id'], $lang_id );
          if( $fl )
            $this->Form->TextBox( 'description['.$lang_id.']', $mas['description'][$lang_id], 80 );
          else 
            $this->Form->TextBox( 'description['.$lang_id.']', $row[$lang_id], 80 );
          echo "\n <td rowspan=3>\n </table>";
          $Panel->WriteItemFooter();
     }
     $Panel->WritePanelFooter();
    
    ?>
    
     <TR><TD><b><?echo $this->Msg['_FLD_FUNCTION']?></b>
         <TD>
    <?
     $arr = NULL;
     $arr['']='';
     $tmp_db = new Rights($this->user_id, $this->module_id);
     $tmp_q = "select `".TblSysFunc."`.id, `".TblSysFunc."`.name, `".TblSysSprFunc."`.name as tdesc 
     from `".TblSysAccess."`, `".TblSysFunc."` LEFT JOIN `".TblSysSprFunc."` ON
       (`".TblSysSprFunc."`.cod = `".TblSysFunc."`.id AND `".TblSysSprFunc."`.lang_id = '"._LANG_ID."' )
               where `".TblSysAccess."`.group = $this->fltr
                 and `".TblSysAccess."`.function = `".TblSysFunc."`.id Order BY tdesc";
    //echo $tmp_q;
     $tmp_db->Query( $tmp_q, $this->user_id, $this->module_id );
     $tmp_rows = $tmp_db->db_GetNumRows();
     for( $i=0; $i<$tmp_rows; $i++ )
     {
      $tmp_row = $tmp_db->db_FetchAssoc();
    //  $arr[$tmp_row['id']] = $this->Spr->GetNameByCod( TblSysSprFunc, $tmp_row['id'] ).'  ('.$tmp_row['name'].')';
      $arr[$tmp_row['id']] = $tmp_row['tdesc'].'  ('.$tmp_row['name'].')';
     }
     $this->Form->Select( $arr, $name = 'function', $mas['function'] );
    
     if( $id!=NULL or ( $mas!=NULL ) )
     {
      $this->Form->Hidden( 'move', $mas['move'] );
     }
     else
     {
      $tmp_q = "select * from ".TblSysMenuAdm." order by move desc";
      $res = $tmp_db->Query( $tmp_q, $this->user_id, $this->module_id );
      if( !$res )return false;
      $tmp_row = $tmp_db->db_FetchAssoc();
      $move = $tmp_row['move'];
      $this->Form->Hidden( 'move', ($move+1) );
     }
     AdminHTML::PanelSimpleF();
     $this->Form->WriteSavePanel( $scriptact );
     AdminHTML::PanelSubF();
     $this->Form->WriteFooter();
    return true;
    }
    
    
    // ================================================================================================
    // Function : save()
    // Version : 1.0.0
    // Date : 31.01.2005
    //
    // Parms :   $user_id, $module_id, $id, $group_menu, $level, $description, $function, $move
    // Returns : true,false / Void
    // Description : Store data to the table TblSysMenuAdm
    // ================================================================================================
    // Programmer : Andriy Lykhodid
    // Date : 31.01.2005
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    
    function save( $user_id, $module_id, $id, $group, $level, $description, $function, $move )
    {
     if( $module_id ) $this->module_id = $module_id;
     if( $user_id ) $this->user_id = $user_id;
    
     if ( empty($description[_LANG_ID]) ) {
        $this->Msg['_EMPTY_DESCRIPTION_FIELD'];
        $this->edit( $user_id, $module_id, $id, $level, $_REQUEST );
        return false;
     }
     $ln_sys = &check_init('LangSys','SysLang');
    
     $q="select `id` from ".TblSysMenuAdm." where id='$id'";
     $res = $this->Right->Query( $q, $this->user_id, $this->module_id );
     if( !$res ) return false;
     $rows = $this->Right->db_GetNumRows();
     if($rows>0)
     {
       $q="update ".TblSysMenuAdm." set
          `group`='$group', level='$level', function='$function', move='$move'";
          $q=$q." where id='$id'";
      $res = $this->Right->Query( $q, $this->user_id, $this->module_id );
      //if( $res ) return true;
      //else return false;
     }
     else
     {
      $q = "select `id` from ".TblSysMenuAdm." order by id desc";
      $res = $this->Right->Query( $q, $this->user_id, $this->module_id );
      $row = $this->Right->db_FetchAssoc();
      $id = $row['id']+1;
    
      $q = "insert into ".TblSysMenuAdm." values('$id','$group','$level','$function', '$move')";
      $res = $this->Right->Query( $q, $this->user_id, $this->module_id );
      if( !$res ) return false;
     }
    
     // Save Description on different languages
     $ln_arr = $ln_sys->LangArray( _LANG_ID );
     while( $el = each( $ln_arr ) )
     {
          $description1 = addslashes($description[ $el['key'] ]);
          //if (empty($description1)) continue;
          $lang_id = $el['key'];
          $res = $this->Spr->SaveToSpr( TblSysSprMenuAdm, $id, $lang_id, $description1 );
          if( !$res ) return false;
     } //--- end while
    
     return true;
    }
    
    
    // ================================================================================================
    // Function : del()
    // Version : 1.0.0
    // Date : 28.01.2005
    // Parms :   $user_id, $module_id, $id_del
    // Returns : true,false / Void
    // Description :  Remove data from the table
    // ================================================================================================
    // Programmer : Andriy Lykhodid
    // Date : 28.01.2005
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    
    function del( $user_id, $module_id, $id_del )
    {
     if( $module_id ) $this->module_id = $module_id;
     if( $user_id ) $this->user_id = $user_id;
    
        $del = 0;
        $kol = count( $id_del );
        for( $i=0; $i<$kol; $i++ )
        {
         $u=$id_del[$i];
    
         $q="select * from ".TblSysMenuAdm." where level='$u'";
         $res = $this->Right->Query( $q, $this->user_id, $this->module_id );
         $rows = $this->Right->db_GetNumRows();
         for( $i_ = 0; $i_ < $rows; $i_++ )
         {
          $row = $this->Right->db_FetchAssoc();
          $id_del_l[$i_] = $row['id'];
         }
         if( $rows>0 )$this->del( $user_id, $module_id, $id_del_l );
    
          $q = "delete from ".TblSysMenuAdm." where id='$u'";
          $res = $this->Right->Query( $q, $this->user_id, $this->module_id );
          $res = $this->Spr->DelFromSpr( TblSysSprMenuAdm, $u );
          if( !$res )return false;
          if ( $res )
           $del=$del+1;
          else
           return false;
        }
      return $del;
    }
    
    
    // ================================================================================================
    // Function : up_menu()
    // Version : 1.0.0
    // Date : 31.01.2005
    // Parms :
    //           $user_id=NULL
    //           $module_id=NULL
    //           $display=NULL
    //           $start=NULL
    //           $sort=NULL
    //           $level = 0  - level of menu  (0 - first level)
    //           $move  -  move field..
    // Returns : true,false / Void
    // Description : Get Top Level of menu
    // ================================================================================================
    // Programmer : Andriy Lykhodid
    // Date : 31.01.2005
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    
    function up_menu( $user_id=NULL, $module_id=NULL, $display=NULL, $start=NULL, $sort=NULL, $level, $move )
    {
     if( $module_id ) $this->module_id = $module_id;
     if( $user_id ) $this->user_id = $user_id;
     if( $display ) $this->display = $display;
     if( $sort ) $this->sort = $sort;
     if( $start ) $this->start = $start;
    
     $scriptact = 'module='.$this->module_id;              /* set action page-adress with parameters */
     $scriplink = $_SERVER['PHP_SELF']."?$scriptact";
    
     $q="select * from ".TblSysMenuAdm." where level='$level' AND move='$move'";
     $res = $this->Right->Query( $q, $this->user_id, $this->module_id );
     if( !$res )return false;
    
     $rows = $this->Right->db_GetNumRows();
     $row = $this->Right->db_FetchAssoc();
     $move_down = $row['move'];
     $id_down = $row['id'];
    
     $q="select * from ".TblSysMenuAdm." where level='$level' AND move<'$move' order by move desc";
     $res = $this->Right->Query( $q, $this->user_id, $this->module_id );
     if( !$res )return false;
    
     $rows = $this->Right->db_GetNumRows();
     $row = $this->Right->db_FetchAssoc();
     $move_up = $row['move'];
     $id_up = $row['id'];
    
     $q="update ".TblSysMenuAdm." set
         move='$move_down' where id='$id_up'";
     $res = $this->Right->Query( $q, $this->user_id, $this->module_id );
    
     $q="update ".TblSysMenuAdm." set
         move='$move_up' where id='$id_down'";
     $res = $this->Right->Query( $q, $this->user_id, $this->module_id );
    
    }
    
    
    // ================================================================================================
    // Function : down_menu()
    // Version : 1.0.0
    // Date : 31.01.2005
    // Parms :
    //           $user_id=NULL
    //           $module_id=NULL
    //           $display=NULL
    //           $start=NULL
    //           $sort=NULL
    //           $level = 0  - level of menu  (0 - first level)
    //           $move  -  move field..
    // Returns : true,false / Void
    // Description : Down  menu item
    // ================================================================================================
    // Programmer : Andriy Lykhodid
    // Date : 31.01.2005
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    
    function down_menu( $user_id=NULL, $module_id=NULL, $display=NULL, $start=NULL, $sort=NULL, $level, $move )
    {
     if( $module_id ) $this->module_id = $module_id;
     if( $user_id ) $this->user_id = $user_id;
     if( $display ) $this->display = $display;
     if( $sort ) $this->sort = $sort;
     if( $start ) $this->start = $start;
    
     $q="select * from ".TblSysMenuAdm." where level='$level' AND move='$move'";
     $res = $this->Right->Query( $q, $this->user_id, $this->module_id );
     if( !$res )return false;
    
     $rows = $this->Right->db_GetNumRows();
     $row = $this->Right->db_FetchAssoc();
     $move_up = $row['move'];
     $id_up = $row['id'];
    
    
     $q="select * from ".TblSysMenuAdm." where level='$level' AND move>'$move' order by move";
     $res = $this->Right->Query( $q, $this->user_id, $this->module_id );
     if( !$res )return false;
    
     $rows = $this->Right->db_GetNumRows();
     $row = $this->Right->db_FetchAssoc();
     $move_down = $row['move'];
     $id_down = $row['id'];
    
     $q="update ".TblSysMenuAdm." set
         move='$move_down' where id='$id_up'";
     $res = $this->Right->Query( $q, $this->user_id, $this->module_id );
    
     $q="update ".TblSysMenuAdm." set
         move='$move_up' where id='$id_down'";
     $res = $this->Right->Query( $q, $this->user_id, $this->module_id );
    }
    
    
    // ================================================================================================
    // Function : get_top_level()
    // Version : 1.0.0
    // Date : 30.01.2005
    // Parms :
    //           $level = 0  - level of menu  (0 - first level)
    // Returns : true,false / Void
    // Description : Get Top Level of menu
    // ================================================================================================
    // Programmer : Andriy Lykhodid
    // Date : 30.01.2005
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    
    function get_top_level( $level )
    {
     $q = "select * from ".TblSysMenuAdm." where id='$level'";
     $res = $this->Right->Query( $q, $this->user_id, $this->module_id );
    
     if( !$res )return false;
     $rows = $this->Right->db_GetNumRows();
     $row = $this->Right->db_FetchAssoc();
     return $row;
    }
    
    // ================================================================================================
    // Function : get_level_name()
    // Version : 1.0.0
    // Date : 30.01.2005
    // Parms :
    //           $id = 0  - level of menu  (0 - first level)
    // Returns : true,false / Void
    // Description : Get Level Name
    // ================================================================================================
    // Programmer : Andriy Lykhodid
    // Date : 30.01.2005
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    
    function get_level_name( $id )
    {
     //$db = new Rights($this->user_id, $this->module_id);
     $q = "select * from ".TblSysMenuAdm." where id='$id'";
     $res = $this->Right->Query( $q, $this->user_id, $this->module_id );
    
     if( !$res )return false;
     $rows = $this->Right->db_GetNumRows();
     $row = $this->Right->db_FetchAssoc();
     return $row;
    }


} // end of class
?>
