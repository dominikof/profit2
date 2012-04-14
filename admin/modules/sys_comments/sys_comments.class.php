<?php
include_once( SITE_PATH.'/modules/mod_comments/comments.defines.php' );

/**
* Class CommentsCtrl
* Class definition for all actions with managment of Comments
* @package System Package of SEOCMS
* @author Igor Trokhymchuk  <ihor@seotm.com>
* @version 1.1, 02.04.2012
* @copyright (c) 2005+ by SEOTM
*/ 
class CommentsCtrl {

	   public  $user_id = NULL;
	   public  $module = NULL;
	   public  $lang_id = NULL;
	   public  $Err = NULL;

	   public  $sort = NULL;
	   public  $display = 10;
	   public  $start = 0;
	   public  $width = 500;
	   public  $fln = NULL;
	   public  $fltr = NULL;
	   public  $fltr2 = NULL; 
	   public  $srch = NULL;
	   public  $Msg = NULL;
	   public  $Rights = NULL;
	   public  $Form = NULL;
	   public  $Spr = NULL;

	   /**
       * CommentsCtrl::__construct()
       * 
       * @param integer $user_id
       * @param integer $module
       * @param integer $display
       * @param string $sort
       * @param integer $start
       * @param integer $width
       * @return void
       */
       function __construct($user_id=NULL, $module=NULL, $display=NULL, $sort=NULL, $start=NULL, $width = NULL) {
				//Check if Constants are overrulled
				( $user_id   !="" ? $this->user_id = $user_id  : $this->user_id = NULL );
				( $module   !="" ? $this->module  = $module   : $this->module  = NULL );
				( $display  !="" ? $this->display = $display  : $this->display = 10   );
				( $sort     !="" ? $this->sort    = $sort     : $this->sort    = NULL );
				( $start    !="" ? $this->start   = $start    : $this->start   = 0    );
				( $width    !="" ? $this->width   = $width    : $this->width   = 750  );

				if(defined("_LANG_ID")) $this->lang_id = _LANG_ID;
				
				if (empty($this->Rights)) $this->Rights = new Rights($this->user_id, $this->module); 
				if (empty($this->Form)) $this->Form = new Form('form_comments');
				if (empty($this->Msg)) $this->Msg = &check_init_txt('TblBackMulti',TblBackMulti); 
				if (empty($this->Spr)) $this->Spr = new  SysSpr();
				
	   } // End of CommentsCtrl Constructor

	   // ================================================================================================
	   // Function : show
	   // Version : 1.0.0
	   // Date : 20.08.2008 
	   //
	   // Parms :
	   // Returns : true,false / Void
	   // Description : Show data from $module table
	   // ================================================================================================
	   // Programmer : Igor Trokhymchuk
	   // Date : 05.12.2006 
	   // Reason for change : Creation
	   // Change Request Nbr:
	   // ================================================================================================
	   function show()
	   {

		$db = new Rights($this->user_id, $this->module);
		$tmp_db = new Rights($this->user_id, $this->module);
		//$Spr = new SysSpr();
		
		if( !$this->sort ) $this->sort='dt';
		$q = "SELECT * FROM `".TblSysModComments."` WHERE 1";
		if( $this->fltr ) $q = $q." and `id_module`='".$this->fltr."'";
		if( $this->fltr2 ) $q = $q." and `id_item`='".$this->fltr2."'";
		$q = $q." ORDER BY `".$this->sort."` desc";
		$res = $this->Rights->Query( $q, $this->user_id, $this->module );
		//echo '<br>$q='.$q.' $res='.$res.' $this->Rights->result='.$this->Rights->result;
		if( !$res )return false;

		$rows = $this->Rights->db_GetNumRows();

		// echo '<br> this->srch ='.$this->srch.' $script='.$script;

		/* Write Form Header */
		$this->Form->WriteHeader( $this->script );

		$this->ShowContentFilters(); 
		/* Write Table Part */
		AdminHTML::TablePartH();

		/* Write Links on Pages */
		echo '<TR><TD COLSPAN=9>';
		//$script1 = 'module='.$this->module.'&fltr='.$this->fltr.'&fltr2='.$this->fltr2;
		//$script1 = $_SERVER['PHP_SELF']."?$script1";
		$this->Form->WriteLinkPages( $this->script, $rows, $this->display, $this->start, $this->sort );

		echo '<TR><TD COLSPAN=9><div class="topPanel">';
		$this->Form->WriteTopPanel( $this->script, 2 );

		?></div><td align=center><?
	   // $this->Spr->ShowActSprInCombo(TblModCommentsSprGroup, 'fltr', $this->fltr, $_SERVER["QUERY_STRING"]."&start=$this->start&display=$this->display&sort=$this->sort&srch=$this->srch&srch2=$this->srch2");

		/*
		?><td align=center><?
		$this->Spr->ShowActSprInCombo(TblModCommentsSprCity, 'fltr2', $this->fltr2, $_SERVER["QUERY_STRING"]."&start=$this->start&display=$this->display&sort=$this->sort&srch=$this->srch&srch2=$this->srch2");
		*/
		if($rows>$this->display) $ch = $this->display;
        else $ch = $rows;
	   ?>
		<TR>
		<Th class="THead"><input value="0" id="cAll" onclick="if (this.value == '1') {unCheckAll(<?=$ch;?>); this.value = '0';} else {checkAll(<?=$ch;?>); this.value = '1';}" type="checkbox"></Th>
		<Th class="THead"><?$this->Form->LinkTitle($this->script.'&sort=id', $this->Msg['FLD_ID']);?></Th>
		<Th class="THead"><?=$this->Msg['_FLD_MODULE']?></Th>
		<Th class="THead"><?=$this->Msg['_FLD_POSITION']?></Th>
		<Th class="THead"><?=$this->Msg['FLD_USER_ID']?></Th>
		<?/*
		<td class="THead"><A HREF=<?=$this->script?>&sort=city_d><?=$this->Msg['FLD_CITY'])?></A></Th>
		*/?>
		<Th class="THead"><?=$this->Msg['FLD_TEXT']?></Th>
		<Th class="THead"><?=$this->Msg['_FLD_VISIBLE']?></Th>
		<?
		$a = $rows;
		$j = 0;
		$up = 0;
		$down = 0;
		$row_arr = NULL;
		for( $i = 0; $i < $rows; $i++ )
		{
		  $row = $this->Rights->db_FetchAssoc();
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
		  $this->Form->CheckBox( "id_del[]", $row['id'], null, "check".$i );
		  echo '<TD>'.$row['id'];
		 // $this->Form->Link( $this->script."&task=edit&id=".$row['id'], stripslashes( $row['id'] ), $this->Msg['TXT_EDIT'] );

		  echo '<TD align=center>'.stripslashes($this->Spr->GetNameByCod( TblSysSprFunc, $row['id_module'] )).'</TD>';           
		  
	      //   echo "<br> row['id_module'] = ".$row['id_module'];
		  
		  switch($row['id_module']){
		      case '68': 
                $tbl = TblModCatalogPropSprName;
				$nameItem = stripslashes($this->Spr->GetNameByCod( $tbl, $row['id_item'] ));
                break;
			  case '83': 
                $tbl = TblModArticleTxt;
                $nameItem = stripslashes($this->Spr->GetNameByCod( $tbl, $row['id_item'] ));
				break;
			  case '90':
                $q = "SELECT `pname` FROM `".TblModPagesTxt."` WHERE `cod`='".$row['id_item']."'";
				$res = $db->db_Query($q);
                $row = $db->db_FetchAssoc();
                $nameItem = stripslashes($row['pname']);
                break;
			  case '72': 
                $tbl = TblModNewsSprSbj;
                $nameItem = stripslashes($this->Spr->GetNameByCod( $tbl, $row['id_item'] ));
				break;
			  default: 
                //$tbl = TblModPagesTxt;
                $nameItem = null;
				break;
		   }
		  
		  echo '<TD align=center>'.$nameItem;
		  
		  $U = new SysUser();
		  echo '<TD align=center>'.$U->GetUserLoginByUserId($row['id_user']).'</TD>';
		  /*
		  if ($row['city_d']=='0') echo '<TD align=center></TD>';
		  else echo '<TD align=center>'.$this->Spr->GetNameByCod(TblModDealerSprCity, $row['city_d']).'</TD>';
		  */
		  echo '<TD align=center>'.$row['text'];

		  echo '<TD align="center">'; 
		  if( $row['status']<2) $n_ch = $row['status']+1;
		  else $n_ch=0;
		  ?>
		  <div id="res_ch<?=$row['id'];?>">
			<a href="#" onclick="makeRequest('/admin/modules/sys_comments/sys_comments.php', 'task=ch_stat&id=<?=$row['id'];?>&status=<?=$n_ch?>', 'res_ch<?=$row['id'];?>');">
		  <?
		  if( $row['status'] == 0 ) $this->Form->Img( 'http://'.NAME_SERVER.'/admin/images/icons/publish_x.png', $this->Msg['TXT_UNVISIBLE'], 'border=0' );
		  if( $row['status'] == 1 ) $this->Form->Img( 'http://'.NAME_SERVER.'/admin/images/icons/tick.png', $this->Msg['_TXT_VISIBLE'], 'border=0' );
		  if( $row['status'] == 2 ) $this->Form->Img( 'http://'.NAME_SERVER.'/admin/images/icons/publish_g.png', $this->Msg['_FLD_MODERATION'], 'border=0' );
		  ?></a>
		  </div>
		  </td><?
		   
		   $up=$row['id'];
		   $a=$a-1;                    
	   
		} //-- end for

		AdminHTML::TablePartF();
		$this->Form->WriteFooter();
		return true;

	   } //end of fuinction show
	   
	   function change_stat(){
		 $q = "update `".TblSysModComments."` set
				  `status`='$this->status'
					WHERE `id` = '$this->id'";
			 $res = $this->Rights->Query( $q, $this->user_id, $this->module );
			 //echo '<br>$q='.$q.' $res='.$res.' $this->Rights->result='.$this->Rights->result;
			 if( !$res OR !$this->Rights->result) return false;
			 
		 if( $this->status<2) $n_ch =$this->status+1;
		  else $n_ch=0;    
			 ?>
			 <a href="#" onclick="makeRequest('/admin/modules/sys_comments/sys_comments.php', 'task=ch_stat&id=<?=$this->id;?>&status=<?=$n_ch?>', 'res_ch<?=$this->id;?>');">
		  <?
		  if($this->status == 0 ) $this->Form->Img( 'http://'.NAME_SERVER.'/admin/images/icons//publish_x.png', $this->Msg['TXT_UNVISIBLE'], 'border=0' );
		  if($this->status == 1 ) $this->Form->Img( 'http://'.NAME_SERVER.'/admin/images/icons/tick.png', $this->Msg['_TXT_VISIBLE'], 'border=0' );
		  if($this->status == 2 ) $this->Form->Img( 'http://'.NAME_SERVER.'/admin/images/icons/publish_g.png', $this->Msg['_FLD_MODERATION'], 'border=0' );
		  ?>
		  </a>
		  <?
	   } // end of function change_stat


	   // ================================================================================================
	   // Function : ShowContentFilters
	   // Version : 1.0.0
	   // Date : 20.08.2008 
	   //
	   // Parms :
	   // Returns : true,false / Void
	   // Description : Show content of the catalogue
	   // ================================================================================================
	   // Programmer : Igor Trokhymchuk
	   // Date : 05.12.2006 
	   // Reason for change : Creation
	   // Change Request Nbr:
	   // ================================================================================================
	   function ShowContentFilters()
	   { 
		 /* Write Table Part */
		 AdminHTML::TablePartH();
		   //phpinfo();
		 ?>
		 <table border=0 cellpadding=0 cellspacing=0>
		  <tr valign=top>
		   <?/*
		   <td>
			 <table border=0 cellpadding=2 cellspacing=1>
			  <tr><td><h4><?=$this->Msg['TXT_FILTERS'];?></h4></td></tr>
			  <tr class=tr1>
			   <td align=left><?=$this->Msg['FLD_GROUP'];?></td>
			   <td align=left><?$this->Spr->ShowActSprInCombo(TblModDealerSprGroup, 'fltr', $this->fltr, $_SERVER["QUERY_STRING"]);?></td>
			  </tr>
			  <tr class=tr2>
			   <td align=left><?=$this->Msg['FLD_CITY'];?></td>
			   <td align=left><?$this->Spr->ShowActSprInCombo(TblModDealerSprCity, 'fltr2', $this->fltr2, $_SERVER["QUERY_STRING"]);?></td>
			  </tr> 
			 </table>
		   </td>
		   <td width=30></td>
		   */?>
		   <td>
			 <table border="0" cellpadding="3" cellspacing="4">
			  <tr><td><b><?=$this->Msg['_FLD_LEGEND'];?></b></td></tr>
			  <tr class=tr1>
			   <td align="center"><img src="http://<?=NAME_SERVER;?>/admin/images/icons/publish_x.png"></td>
			   <td><?=$this->Msg['TXT_UNVISIBLE']?></td>
			   <tr class=tr2>
			   <td align="center"><img src="http://<?=NAME_SERVER;?>/admin/images/icons/tick.png"></td>
			   <td><?=$this->Msg['_TXT_VISIBLE']?></td>
			  <tr class=tr1>
			   <td align="center"><img src="http://<?=NAME_SERVER;?>/admin/images/icons/publish_g.png"></td>
			   <td><?=$this->Msg['_FLD_MODERATION']?></td>
			  </tr>
			 </table>
		   </td>
		  </tr>
		 </table>
		 <?
		 AdminHTML::TablePartF();
			 
	   } //end of fuinction ShowContentFilters()       
	   

	   // ================================================================================================
	   // Function : edit()
	   // Version : 1.0.0
	   // Date : 05.12.2006 
	   //
	   // Parms :
	   // Returns : true,false / Void
	   // Description : edit/add records in Comments module
	   // ================================================================================================
	   // Programmer : Igor Trokhymchuk
	   // Date : 05.12.2006  
	   // Reason for change : Creation
	   // Change Request Nbr:
	   // ================================================================================================

	   function edit()
	   {
		$Panel = new Panel();
		$ln_sys = new SysLang();
		$Spr = new SysSpr();
		$mas=NULL; 
		if( $this->id!=NULL)
		{
		  $q = "SELECT * FROM ".TblModComments." where id='$this->id'";
		  $res = $this->Rights->Query( $q, $this->user_id, $this->module );
		  if( !$res ) return false;
		  $mas = $this->Rights->db_FetchAssoc();
		}
		 
		/* Write Form Header */
		$this->Form->WriteHeaderFormImg( $this->script ); 
		
		$this->Form->Hidden( 'display', $this->display );
		$this->Form->Hidden( 'start', $this->start );
		$this->Form->Hidden( 'sort', $this->sort );
		$this->Form->Hidden( 'srch', $this->srch ); 
		$this->Form->Hidden( 'srch2', $this->srch2 );
		$this->Form->Hidden( 'fltr', $this->fltr );
		$this->Form->Hidden( 'fltr2', $this->fltr2 );
		$this->Form->Hidden( 'fln', $this->fln );
		$this->Form->Hidden( 'delimg', "" );        
		
		$this->Form->IncludeHTMLTextArea();
		
		if( $this->id!=NULL ) $txt = $this->Msg['TXT_EDIT'];
		else $txt = $this->Msg['_TXT_ADD_DATA'];
		
		AdminHTML::PanelSubH( $txt );
		//-------- Show Error text for validation fields --------------
		$this->ShowErrBackEnd();
		//-------------------------------------------------------------          
		AdminHTML::PanelSimpleH();
	   ?>
		<TABLE BORDER=0 class="EditTable">
		<TR><TD><b><?echo $this->Msg['FLD_ID']?>:</b>
		<TD width="90%">
	   <?
		  if( $this->id!=NULL )
		  {
		   echo $mas['id'];
		   $this->Form->Hidden( 'id', $mas['id'] );
		  }
		  else $this->Form->Hidden( 'id', '' );
		  
	   ?>
		<TR><TD><b><?echo $this->Msg['FLD_CATEGORY']?>:</b>
			<TD>
			<?
		if( $this->id!=NULL ) $this->Err!=NULL ? $group_d=$this->group_d : $group_d=$mas['group_d'];
		else $group_d=$this->group_d; 
		$this->Spr->ShowInComboBox( TblModCommentsSprGroup, 'group_d', $group_d, 40 );
	   
	   /*
	   ?>
		<TR><TD><b><?echo $this->Msg['FLD_CITY']?>:</b>
			<TD>
			<?
		if( $this->id!=NULL ) $this->Err!=NULL ? $city_d=$this->city_d : $city_d=$mas['city_d'];
		else $city_d=$this->city_d; 
		$this->Spr->ShowInComboBox( TblModDealerSprCity, 'city_d', $city_d, 40 );
		*/
		?>              
		<TR><TD colspan=2>
		<?
		$Panel->WritePanelHead( "SubPanel_" );

		$ln_arr = $ln_sys->LangArray( $this->lang_id );
		while( $el = each( $ln_arr ) )
		{
			 $lang_id = $el['key'];
			 $lang = $el['value'];
			 $mas_s[$lang_id] = $lang;

			 $Panel->WriteItemHeader( $lang );
			 echo "\n <table border=0 class='EditTable'>";
			 echo "\n <tr>";
			 echo "\n <td><b>".$this->Msg['FLD_NAME'].":</b>";
			 echo "\n <tr><td>";
			 $row = $this->Spr->GetByCod( TblModCommentsSprName, $mas['id'], $lang_id );
			 if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->name[$lang_id] : $val=$row[$lang_id];
			 else $val=$this->name[$lang_id];              
			 $this->Form->TextBox( 'name['.$lang_id.']', stripslashes($val), 80 );
			 echo "\n <tr>";
			 echo "\n <td><b>".$this->Msg['FLD_DESCR'].":</b>";
			 echo "\n <tr><td>";
			 $row = $this->Spr->GetByCod( TblModCommentsSprDescr, $mas['id'], $lang_id );
			 if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->content[$lang_id] : $val=$row[$lang_id];
			 else $val=$this->content[$lang_id];              
			 $this->Form->HTMLTextArea( 'content['.$lang_id.']', stripslashes($val), 7, 70 );             
			 
			 echo "\n <td rowspan=3>";
			 echo   "\n </table>";
			 $Panel->WriteItemFooter();
		}
		$Panel->WritePanelFooter();

		?>
		<TR><TD><b><?echo $this->Msg['FLD_VISIBLE']?>:</b>
			<TD>
			<?
			$arr_v[0]=$this->Msg['TXT_UNVISIBLE'];
			$arr_v[1]=$this->Msg['TXT_VISIBLE'];
			
		if( $this->id!=NULL ) $this->Err!=NULL ? $visible=$this->visible : $visible=$mas['visible'];
		else $visible=$this->visible; 
		$this->Form->Select( $arr_v, 'visible', $visible );

		?>
		<TR><TD><b><?echo $this->Msg['_FLD_IMAGE']?>:</b>
			<TD>
			 <table border=0 cellpadding=0 cellspacing=1 class="EditTable">
			  <tr>
			   <td><?
			   if( $this->id!=NULL ) $this->Err!=NULL ? $img=$this->img : $img=$mas['img'];
			   else $img=$this->img;                 
				if( !empty($img) ) {
					?><table border=0 cellpadding=0 cellspacing=5>
					   <tr>
						<td class='EditTable'><?
					$this->Form->Hidden( 'img', $img);
					?><a href="<?=Comments_Img_Path.$img;?>" target="_blank" onmouseover="return overlib('<?=$this->Msg['TXT_ZOOM_IMG'];?>',WRAP);" onmouseout="nd();" alt="<?=$this->Msg['TXT_ZOOM_IMG'];?>" title="<?=$this->Msg['TXT_ZOOM_IMG'];?>"><?
					$this->ShowImage($img, 'size_width=150', 100, NULL, "border=0");
					?></a><br><?                    
					/*
					<img src="http://<?=NAME_SERVER?>/thumb.php?img=<?=Dealer_Img_Path.$img?>&size_auto=100" border=0 alt="<?=$this->Spr->GetNameByCod( TblModDealerSprName, $mas['id'], $this->lang_id ); ?>">
					*/
					?>
					<td class='EditTable'><?
					echo Comments_Img_Full_Path.$img.'<br>';
					?><a href="javascript:form_comments.delimg.value='<?=$mas['img'];?>';form_comments.submit();"><?=$this->Msg['_TXT_DELETE_IMG'];?></a><?
				   ?></table><?
				   echo '<tr><td><b>'.$this->Msg['_TXT_REPLACE_IMG'].':</b>';
				}
				  
				?>
				<INPUT TYPE="file" NAME="filename" size="40" VALUE="" />                    
				</td>
			  </tr>
			 </table>
		<?
		if ($this->id==NULL) {
		 $arr = NULL;
		 $arr['']='';
		 $tmp_db = new DB();
		 $tmp_q = "select * from `".TblModComments."` order by move desc";
		 $res = $tmp_db->db_Query( $tmp_q );
		 if( !$res )return false;
		 $tmp_row = $tmp_db->db_FetchAssoc();
		 $move = $tmp_row['move'];
		 $move=$move+1;
		 $this->Form->Hidden( 'move', $move );
		}
		else $move=$mas['move'];
		$this->Form->Hidden( 'move', $move );

		echo '<TR><TD COLSPAN=2 ALIGN=left>';
		$this->Form->WriteSavePanel( $this->script );
		$this->Form->WriteCancelPanel( $this->script );
		echo '</table>';
		AdminHTML::PanelSimpleF();
		AdminHTML::PanelSubF();

		$this->Form->WriteFooter();
		return true;
	   } //end of fuinction edit

	   // ================================================================================================
	   // Function : save()
	   // Version : 1.0.0
	   // Date : 05.12.2006 
	   //
	   // Parms :
	   // Returns : true,false / Void
	   // Description : Store data to the table
	   // ================================================================================================
	   // Programmer : Igor Trokhymchuk
	   // Date : 05.12.2006 
	   // Reason for change : Creation
	   // Change Request Nbr:
	   // ================================================================================================
	   function save()
	   {
		$this->Form->Hidden( 'display', $this->display );
		$this->Form->Hidden( 'start', $this->start );
		$this->Form->Hidden( 'sort', $this->sort );
		$this->Form->Hidden( 'srch', $this->srch ); 
		$this->Form->Hidden( 'srch', $this->srch2 ); 
		$this->Form->Hidden( 'fltr', $this->fltr );
		$this->Form->Hidden( 'fltr2', $this->fltr2 ); 
		$this->Form->Hidden( 'fln', $this->fln );       
	   
		$q = "SELECT * FROM ".TblModComments." WHERE `id`='$this->id'";
		$res = $this->Rights->Query( $q, $this->user_id, $this->module );
		if( !$res OR !$this->Rights->result) return false;
		$rows = $this->Rights->db_GetNumRows();
		//echo '<br>$q='.$q.'$rows='.$rows;
		if( $rows>0 )   //--- update
		{
			 $row = $this->Rights->db_FetchAssoc();
			 //Delete old image
			 //echo '<br>$row[img]='.$row['img'].' $this->img='.$this->img;
			 if ( !empty($row['img']) AND $row['img']!=$this->img) {
				$this->DelItemImage($row['img']);
			 }
			
			 $q = "update `".TblModComments."` set
				  `group_d`='$this->group_d',
				  `city_d`='$this->city_d',
				  `img` = '$this->img',
				  `visible`='$this->visible',
				  `move`='$this->move' WHERE `id` = '$this->id'";
			 $res = $this->Rights->Query( $q, $this->user_id, $this->module );
			 //echo '<br>$q='.$q.' $res='.$res.' $this->Rights->result='.$this->Rights->result;
			 if( !$res OR !$this->Rights->result) return false;
		}
		else   //--- insert
		{
			/*
			$q="select * from `".TblModDealers."` where 1";
			$res = $this->Rights->Query( $q, $this->user_id, $this->module );
			$rows = $this->Rights->db_GetNumRows();
			$maxx=0;  //add link with position auto_incremental
			for($i=0;$i<$rows;$i++)
			{
			  $my = $this->Rights->db_FetchAssoc();
			  if($maxx < $my['move'])
			  $maxx=$my['move'];
			}
			$maxx=$maxx+1;             
			*/
			
			$q = "insert into `".TblModComments."` values(NULL, '$this->group_d', '$this->city_d', '$this->img', '$this->visible', '$this->move')";
			$res = $this->Rights->Query( $q, $this->user_id, $this->module );
			//echo '<br>'.$q.' $res='.$res.' $this->Rights->result='.$this->Rights->result;
			if( !$res OR !$this->Rights->result) return false;
		}
		
		if ( empty($this->id) ){
		  $this->id = $this->Rights->db_GetInsertID();
		}

		// Save Description on different languages
		$res=$this->Spr->SaveNameArr( $this->id, $this->name, TblModCommentsSprName );
		if( !$res ) return false;
		$res=$this->Spr->SaveNameArr( $this->id, $this->content, TblModCommentsSprDescr );
		if( !$res ) return false;        
		return true;
	   } //end of fuinction save()

	   // ================================================================================================
	   // Function : del()
	   // Version : 1.0.0
	   // Date : 05.12.2006 
	   //
	   // Parms :
	   // Returns :      true,false / Void
	   // Description :  Remove data from the table
	   // ================================================================================================
	   // Programmer :  Igor Trokhymchuk
	   // Date : 05.12.2006 
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
			$q="DELETE FROM `".TblSysModComments."` WHERE id='$u'";
			$res = $this->Rights->Query( $q, $this->user_id, $this->module );
			if (!$res) return false;
			if ( $res )
			 $del=$del+1;
			else
			 return false;
		   }
		 return $del;
	   } //end of fuinction del()
	   
	   // ================================================================================================
	   // Function : CheckFields()
	   // Version : 1.0.0
	   // Date : 05.12.2006 
	   //
	   // Parms :        $id - id of the record in the table
	   // Returns :      true,false / Void
	   // Description :  Checking all fields for filling and validation
	   // ================================================================================================
	   // Programmer :  Igor Trokhymchuk
	   // Date : 05.12.2006 
	   // Reason for change : Creation
	   // Change Request Nbr:
	   // ================================================================================================
	   function CheckFields($id = NULL)
	   {
		$this->Err=NULL;
		/*
		if (empty( $this->group_d)) {
			$this->Err = $this->Err.$this->Msg['MSG_FLD_GROUP_EMPTY'].'<br>';
		}
		
		if (empty( $this->city_d)) {
			$this->Err = $this->Err.$this->Msg['MSG_FLD_CITY_EMPTY'].'<br>';
		}                  
		*/
		if (empty( $this->name[$this->lang_id] )) {
			$this->Err = $this->Err.$this->Msg['MSG_FLD_NAME_EMPTY'].'<br>';
		}

		//echo '<br>$this->Err='.$this->Err.' $this->Msg->table='.$this->Msg->table;
		return $this->Err;
	   } //end of fuinction CheckFields()         
   
	   
 } // End of class CommentsCtrl?>
