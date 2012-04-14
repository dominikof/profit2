<?php

include_once( SITE_PATH.'/admin/include/defines.inc.php' ); 
include_once( SITE_PATH.'/sys/classes/sysSpr.class.php' );

/**
* Class SysSpr
* Class definition for all actions with reference-books
* @package System Package of SEOCMS
* @author Igor Trokhymchuk  <ihor@seotm.com>
* @version 1.1, 02.04.2012
* @copyright (c) 2005+ by SEOTM
*/
class SysSpr extends SystemSpr {

    public  $user_id = NULL;
    public  $module = NULL;
    public  $module_name = NULL;
    public  $info_msg = NULL;

    public  $sort = NULL;
    public  $display = 15;
    public  $start = 0;
    public  $fln = NULL;
    public  $spr = NULL;
    public  $srch = NULL;
    public  $Err = NULL;

    public  $field_type = NULL;
    public  $use_edit_ajax = NULL;
    public  $uselevels = 0;
    public  $level = NULL;
    public  $level_new = NULL;
    public  $usemeta = 0;
    public  $useshort = 0;
    public  $useimg = 0;
    public  $usemove = 0;
    public  $usedescr = 0;
    public  $usetranslit = 0;
    public  $usecolors = 0;
    public  $usecodpli = 0;
    public  $msg = NULL;
    public  $Rights = NULL;
    public  $Form = NULL;
    public  $script = NULL;
    public  $root_script = NULL; 
    public  $parent_script = NULL;
    public  $parent_id = NULL;
    public  $Msg_text = NULL;

    /**
    * SysSpr::__construct()
    * 
    * @param integer $user_id
    * @param integer $module_id
    * @param integer $display
    * @param string $sort
    * @param integer $start
    * @param integer $width
    * @param integer $spr
    * @return void
    */
    function __construct($user_id=NULL, $module=NULL, $display=NULL, $sort=NULL, $start=NULL, $width = NULL, $spr=NULL) {
            //Check if Constants are overrulled
            ( $user_id   !="" ? $this->user_id = $user_id  : $this->user_id = NULL );
            ( $module   !="" ? $this->module  = $module   : $this->module  = NULL );
            ( $display  !="" ? $this->display = $display  : $this->display = 20   );
            ( $sort     !="" ? $this->sort    = $sort     : $this->sort    = NULL );
            ( $start    !="" ? $this->start   = $start    : $this->start   = 0    );
            ( $spr      !="" ? $this->spr     = $spr      : $this->spr     = NULL  );

            if ( defined("_LANG_ID") ) $this->lang_id = _LANG_ID;
            
            if( defined("AJAX_RELOAD") AND AJAX_RELOAD==1){
                $this->make_encoding = 1;
                $this->encoding_from = 'utf-8';
                $this->encoding_to = 'windows-1251';        
            }   
            
            if (empty($this->Rights)) $this->Rights = new Rights($this->user_id, $this->module);
            if (empty($this->Msg)) $this->Msg = new ShowMsg();  
            if (empty($this->Form)) $this->Form = new Form('form_sys_spr');
            if (empty($this->Msg_text)) $this->Msg_text = &check_init_txt('TblBackMulti',TblBackMulti);  
        
            $this->use_edit_ajax = 0;
            
    } // End of SysSpr Constructor

    // ================================================================================================
    // Function : AddTbl
    // Version : 1.0.0
    // Date : 09.01.2005
    //
    // Parms :
    // Returns : true,false / Void
    // Description : Dynamicly modify structure of tables
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 09.01.2005
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function AddTbl()
    {
        if( $this->usemove==1 ) {
            $this->AutoInsertColumnMove( $this->spr );
            $this->AutoInsertDataIntoColumnMove( $this->spr );
        }
        
        if( $this->useimg==1 ) $this->AutoInsertColumnImg( $this->spr );
        if( $this->useshort==1 ) $this->AutoInsertColumnShortName( $this->spr );
        
        if($this->uselevels==1){
           // add field level to the table $this->spr
           if ( !$this->Rights->IsFieldExist($this->spr, "level") ) {
               $q = "ALTER TABLE `".$this->spr."` ADD `level` INT( 11 ) UNSIGNED DEFAULT '0';";
               $res = $this->Rights->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false; 
                
               $q = "ALTER TABLE `".$this->spr."` ADD INDEX ( `level` ) ;";
               $res = $this->Rights->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;                   
               if( !$res )return false;
           }
           if ( !$this->Rights->IsFieldExist($this->spr, "node") ) {               
               $q = "ALTER TABLE `".$this->spr."` ADD `node` SMALLINT( 5 ) UNSIGNED DEFAULT '0';";
               $res = $this->Rights->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;
               $this->AutoInsertColumnNode( $this->spr ); 
           } 
        }
       
       if($this->usemeta==1){
           // add field mtitle, mdescr, mkeywords to the table $this->spr for meta data
           if ( !$this->Rights->IsFieldExist($this->spr, "mtitle") ) {
               $q = "ALTER TABLE `".$this->spr."` ADD `mtitle` VARCHAR( 255 ) ,
                     ADD `mdescr` VARCHAR( 255 ) ,
                     ADD `mkeywords` VARCHAR( 255 ) ;";
               $res = $this->Rights->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false; 
           }
       }                              

       if($this->usetranslit==1){
           // add field translit
           if ( !$this->Rights->IsFieldExist($this->spr, "translit") ) {
               $q = "ALTER TABLE `".$this->spr."` ADD `translit` VARCHAR( 255 );";
               $res = $this->Rights->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false; 
           }
       }
            
       if($this->usedescr==1){
           // add field translit
           if ( !$this->Rights->IsFieldExist($this->spr, "descr") ) {
               $q = "ALTER TABLE `".$this->spr."` ADD `descr` text;";
               $res = $this->Rights->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false; 
           }
       }
       
       if($this->usecodpli==1){
           // add field cod_pli
           if ( !$this->Rights->IsFieldExist($this->spr, "cod_pli") ) {               
               $q = "ALTER TABLE `".$this->spr."` ADD `cod_pli` VARCHAR( 100 );";
               $res = $this->Rights->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false; 
                
               $q = "ALTER TABLE `".$this->spr."` ADD INDEX ( `cod_pli` ) ;";
               $res = $this->Rights->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;                   
               if( !$res )return false;
           }        
       }
    }//end of function AddTbl()       

    // ================================================================================================
    // Function : GetContent
    // Version : 1.0.0
    // Date : 19.03.2008
    //
    // Parms :
    // Returns : true,false / Void
    // Description : execute SQL query
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 19.03.2008
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function GetContent($limit='limit')
    {
        if (empty($this->Msg_text)) $this->Msg_text = &check_init_txt('TblBackMulti',TblBackMulti);         
        //echo '<br>$this->sort='.$this->sort;
        if( empty($this->sort) ) {
            $tmp_db = DBs::getInstance();
            $q = "SELECT * FROM `".$this->spr."` WHERE 1 LIMIT 1";
            $res = $tmp_db->db_Query($q);
            //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
            //print_r($tmp_db->result);
            if ( !$res OR !$tmp_db->result ) return false;
            $fields_col = mysql_num_fields($tmp_db->result);
            $this->field_type = mysql_field_type($tmp_db->result,1);
            //echo '<br> $fields_col='.$fields_col.' $this->field_type='.$this->field_type; 
            if ($fields_col>4 & $this->field_type!='string') $this->sort='move';
            else $this->sort='cod';
        }
        
        // select (R)
        $q=" SELECT `".$this->spr."`.*, srp.name as lang_name 
             FROM `".$this->spr."` 
             LEFT JOIN `".TblSysLang."` as srp ON (srp.cod=`".$this->spr."`.lang_id AND srp.lang_id='"._LANG_ID."') 
             WHERE 1";
        //if( ($this->fln!=NULL) || ($this->srch!=NULL)  ) $q = $q." WHERE 1";
        if( $this->uselevels==1 AND empty($this->srch) ) $q = $q." AND `".$this->spr."`.level='".$this->level."'";
        if( $this->srch!=NULL ) $q = $q." AND (`".$this->spr."`.cod LIKE '%".$this->srch."%' OR `".$this->spr."`.name LIKE '%".$this->srch."%')";
        if( $this->fln!=NULL ) {
             if ( $this->srch ) $q = $q." AND `".$this->spr."`.lang_id='".$this->fln."'";
             else $q = $q." AND `".$this->spr."`.lang_id='".$this->fln."'";
        }
        if( !empty($this->id_cat)) $q .= " AND `id_cat`='".$this->id_cat."'";
        if( !empty($this->id_param)) $q .= " AND `id_param`='".$this->id_param."'";
        if ($this->fln!=NULL) $q=$q." GROUP BY `".$this->spr."`.cod ORDER BY `".$this->spr."`.".$this->sort."";
        else $q=$q." ORDER BY `".$this->spr."`.".$this->sort."";
        if($limit=='limit'  AND $this->srch==NULL ) $q = $q." LIMIT ".$this->start.", ".$this->display;
        $result = $this->Rights->QueryResult($q, $this->user_id, $this->module);
        //echo '<br> $q='.$q.' $this->user_id='.$this->user_id.' $this->module='.$this->module.' $this->Rights->result='.$this->Rights->result. ' $this->spr='.$this->spr.' $res='.$res;
        if ( !$this->Rights->result ) return false;
        if( !isset($this->field_type) OR empty($this->field_type)) $this->field_type = mysql_field_type($this->Rights->result,1);
        //echo '<br> $fields_col='.$fields_col.' $this->field_type='.$this->field_type;         
        return $result;
    }//end of function GetContent()  
       
    // ================================================================================================
    // Function : show
    // Version : 1.0.0
    // Date : 09.01.2005
    //
    // Parms :         $user_id  / user ID
    //                 $module   / Module read  / Void
    //                 $display  / How many records to show / Void
    //                 $sort     / Sorting data / Void
    //                 $start    / First record for show / Void
    //                 spr       / name of the table for this module
    // Returns : true,false / Void
    // Description : Show data from $module table
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 09.01.2005
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function show()
    {
        if (empty($this->Msg_text)) $this->Msg_text = &check_init_txt('TblBackMulti',TblBackMulti);  
        ?>
        <div id="wndw0"><?$this->showList();?></div>
        <script language="JavaScript">
         function makesearch(uri, div_id){
             //document.<?=$this->Form->name?>.task.value='make_search';
             document.<?=$this->Form->name?>.srch.value=document.getElementById('srch').value; 
             //alert('task='+document.<?=$this->Form->name?>.task.value);
              $.ajax({
                    type: "POST",
                    dataType : "html",
                    data: '&srch='+document.<?=$this->Form->name?>.srch.value,
                    url: uri,
                    success: function(data){
                      $("#"+div_id).empty();
                      $("#"+div_id).append(data);
                    },
                    beforeSend: function(){
                        $("#"+div_id).html('<div style="border:0px solid #000000; padding-top:5px; padding-bottom:5px; text-align:left;" align="center"><img src="/admin/images/icons/loading_animation_liferay.gif"></div>'); 
                    }
              });
         }
        </script>
        <?       
    } //end of fuinction show

    // ================================================================================================
    // Function : showList
    // Version : 1.0.0
    // Date : 21.05.2008
    //
    // Parms :         $user_id  / user ID
    //                 $module   / Module read  / Void
    //                 $display  / How many records to show / Void
    //                 $sort     / Sorting data / Void
    //                 $start    / First record for show / Void
    //                 spr       / name of the table for this module
    // Returns : true,false / Void
    // Description : Show data from $module table
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 09.01.2005
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function showList()
    {
        //======== If don't exist $this->spr then create it! ;) ===========
        $this->CreateSpr( $this->spr );
        $this->AddTbl();
        //=================================================================
        /* Init Table  */
        $tbl = new html_table( 0, 'center', 650, 1, 5 );
        $result = $this->GetContent('nolimit');
        $rows = count ($result);
        //echo '<br> rows='.$rows;
        /* Write Form Header */
        $this->Form->WriteHeader( $this->script );

        if ( $this->module_name!=NULL ) {
            $pg = &check_init('PageAdmin', 'PageAdmin');
            $func = $pg->GetFunction( $this->parent_id );
            //echo '<br>$this->parent_id ='.$this->parent_id ;
            echo '<br /><a href="'.str_replace('_AND_', '&', $this->parent_script).'&parent_script='.$this->root_script.'">'.$this->Msg_text['FLD_BACK'].' → '.stripslashes($func['module_name']).'</a>'; 
        }
        $this->Form->Hidden( 'module_name', $this->module_name );
        $this->Form->Hidden( 'spr', $this->spr ); 
        $this->Form->Hidden( 'root_script', $this->root_script ); 
        $this->Form->Hidden( 'parent_script', $this->parent_script ); 
        $this->Form->Hidden( 'parent_id', $this->parent_id );
        /* Write Table Part */
        AdminHTML::TablePartH();
        ?>
         <tr>
          <td><?
           /* Write Links on Pages */
           $this->Form->WriteLinkPages( $this->script, $rows, $this->display, $this->start, $this->sort );?>
          </td>
         </tr>
         <tr>
          <td>
          <div class="topPanel">
            <div class="SavePanel"><?
           /* Write Top Panel (NEW,DELETE - Buttons) */
           $this->Form->WriteTopPanel( $this->script );
           ?></div><div class="SelectType"><?
           echo $this->Form->TextBox('srch', $this->srch, 30, 'id="srch"');
           $url = '/admin/modules/sys_spr/sys_spr.php?'.$this->script_ajax.'&task=make_search&uselevels='.$this->uselevels.'&level='.$this->level.'&node='.$this->node;
           ?><input type="submit" value="<?=$this->Msg_text['TXT_SEARCH'];?>" onclick="makesearch('<?=$url;?>','wndw0'); return false;"><?
           //echo "<br>fln=".$this->fln;
           $this->Form->WriteSelectLangChange( $this->script, $this->fln );?>
            </div>
           </div>
          </td>
         </tr>
         <tr>
          <td>
           <div name="load" id="load"></div>
           <div class="warning"><?=$this->info_msg?></div>
           <div id="result"></div>
           <div id="debug">
            <?
            $this->ShowContentHTML($result);
            ?>
           </div>     
          </td>
         </tr>
         <?
        AdminHTML::TablePartF();
        /* Write Form Footer */
        $this->Form->WriteFooter();
    } //end of fuinction showList()      
       
    // ================================================================================================
    // Function : ShowContentHTML
    // Version : 1.0.0
    // Date : 21.05.2008
    //
    // Parms :
    // Returns : true,false / Void
    // Description : Show content
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 21.05.2008
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function ShowContentHTML($result=NULL)
    { 
        if(!isset($result)) {
            $result = $this->GetContent();
        } 
        $rows = count ($result);
        if($rows>($this->display+$this->start)) $ch = $this->start + $this->display;
        else $ch = $rows;
        //$this->field_type = mysql_field_type($this->Rights->result,1);
        /*
        $top_lev = $this->GetTopLevel( $this->spr, $this->level );
        if( $top_lev ){
            if( $top_lev['level']!=0 ){
                $tmp = $top_lev['name'];
            }
            else{
                $tmp = ' '.$this->Msg_text['TXT_ROOT_LEVEL'];
            }
            ?>
            <div>
             <??>
             <a class="toolbar" href=<?=$this->script."&task=show&level=".$top_lev['level'];?>>&nbsp;<?=$tmp?></a>
            </div>
        <?}?>
        <?*/
        ?><div class="path"><?
        if( !empty($this->srch)){ echo $this->Msg_text['_TXT_SEARCH_RESULT'];?>:<?}
        elseif($this->uselevels==1){$this->ShowPathToLevel($this->spr, $this->level, $this->script, NULL, NULL);}
        ?></div>
        <table border="0" cellpadding="0" cellspacing="1" width="100%">
         <tr>
          <Th class="THead"><input value="0" id="cAll" onclick="if (this.value == '1') {unCheckAll(<?=$ch;?>); this.value = '0';} else {checkAll(<?=$ch;?>); this.value = '1';}" type="checkbox"/></Th>
          <?/*<Th class="THead"><? $this->Form->Link($this->script."&sort=id", $this->Msg_text['FLD_ID']);?></Th>*/?>
          <Th class="THead"><? $this->Form->LinkTitle($this->script."&sort=cod", $this->Msg_text['_FLD_CODE']);?></Th>
          <Th class="THead"><? $this->Form->LinkTitle($this->script."&sort=name", $this->Msg_text['_FLD_NAME']);?></Th>
          <?
          if( $this->useshort==1 ){?>
          <Th class="THead"><? $this->Form->LinkTitle($this->script."&sort=short", $this->Msg_text['_FLD_SHORT_NAME']);?></Th>
          <?}
          if( $this->usedescr==1 ){?>
          <Th class="THead"><?=$this->Msg_text['FLD_DESCRIPTION'];?></Th>
          <?}
          if( $this->usetranslit==1 ){?>
          <Th class="THead"><? $this->Form->LinkTitle($this->script."&sort=translit", $this->Msg_text['FLD_PAGE_URL']);?></Th>
          <?}
          if( $this->useimg==1 ){?>
          <Th class="THead"><? $this->Form->LinkTitle($this->script."&sort=img", $this->Msg_text['_FLD_IMAGE']);?></Th> 
          <?}
          if ($this->field_type!='string' AND $this->uselevels==1) {?> 
          <Th class="THead"><?=$this->Msg_text['FLD_SUBLEVEL'];?></Th>
          <?
          }
          if( $this->usemeta==1 ){?>
          <Th class="THead"><?=$this->Msg_text['_TXT_META_DATA'];?></Th> 
          <?}?>           
          <Th class="THead"><? $this->Form->LinkTitle($this->script."&sort=lang_id", $this->Msg_text['_FLD_LANGUAGE']);?></Th>
          <?if ($this->field_type!='string') {?>
          <Th class="THead"><?=$this->Msg_text['FLD_DISPLAY']?></Th>
          <?}?>
         </tr>       
         <?
         $a=$rows;
         $j = 0;
         $up = 0;
         $down = 0;
         $style1 = 'TR1';
         $style2 = 'TR2';
         //echo '<br>$rows='.$rows.' $this->field_type='.$this->field_type;
         for( $i = $this->start; $i < $ch; ++$i )
         {
             $row = $result[$i];
             //echo '<br>$i='.$i.' $this->start='.$this->start.' $this->display='.$this->display.' $this->start+$this->display='.($this->start+$this->display);
             //if( $i >=$this->start && $i < ( $this->start+$this->display ) )
             //{
                if( (float)$i/2 == round( $i/2 ) ) $class='TR1';
                else $class='TR2';
                ?>
                <tr class="<?=$class;?>">
                 <td align="center"><?=$this->Form->CheckBox( "id_del[]", $row['cod'], null, "check".$i );?>
                 <?/*<td align="center"><?=$this->Form->Link( "$this->script&task=edit&id=".$row['id'], stripslashes($row['id']), $this->Msg_text['TXT_EDIT'] );?>*/?>
                 
                 <td <?if($this->field_type!='string') {?>align="center"<?} else{?>align="left"<?}?> style="padding-left:2px; padding-right:2px;"><?=$this->Form->Link( "$this->script&task=edit&id=".$row['id'], stripslashes($row['cod']), $this->Msg_text['TXT_EDIT'] );?></td>
                 <?//echo '<TD align=left>'; $this->Form->Link( "$this->script&task=edit&id=".$row['id'], stripslashes($row['name']), $this->Msg_text['TXT_EDIT'] ); echo '</TD>';?>
                 <td align="left" style="padding-left:2px; padding-right:2px;"><?=stripslashes($row['name']);?></td>
                 <?
                 if( $this->useshort==1 ){?>
                 <td align="center"><?=$row['short'];?></td>
                 <?}
                 if( $this->usedescr==1 ){?>
                 <td align="center"><?if( !empty($row['descr'])) $this->Form->ButtonCheck();?></td>
                 <?}
                 if( $this->usetranslit==1 ){?>
                 <td align="center"><?=$row['translit'];?></td>
                 <?}
                 if( $this->useimg==1 ){?>
                 <td align="center"><?
                  if ( !empty($row['img']) ){
                      ?><a href="<?=Spr_Img_Path_Small.$this->spr.'/'.$this->fln.'/'.$row['img'];?>" target="_blank" alt="<?=$this->Msg_text['TXT_ZOOM_IMG'];?>" title="<?=$this->Msg_text['TXT_ZOOM_IMG'];?>"><?
                      echo $this->ShowImage($this->spr, $this->fln, $row['img'], 'size_width=75', 100, NULL, "border=0");
                      ?></a><br /><?
                      echo $row['img'];
                  }else{
                      if($this->usecolors==1){
                          ?><div style='width: 30px; height: 30px; background-color: #<?=$row['colorsBit']?>'></div><?
                      }
                  }
                  ?>
                 </td>
                 <?
                 }
                 if( $this->field_type!='string' AND $this->uselevels==1 ){?>
                 <td>
                  <?
                  $sbl = $this->IsSubLevels($this->spr, $row['cod']);
                  if( $sbl==0 ) $txt_tmp = $this->Msg_text['TXT_CREATE_SUBLEVEL'];
                  else $txt_tmp = $this->Msg_text['FLD_SUBLEVEL'];
//                  echo $row['cod'].'<br />node'.$row['node'];
                  $url = '/admin/modules/sys_spr/sys_spr.php?'.$this->script_ajax.'&task=show&uselevels='.$this->uselevels.'&level='.$row['cod'].'&node='.$row['node'].'&srch=';
                  ?>
                  <a href="<?=$this->script;?>&level=<?=$row['cod'];?>&node=<?=$row['node'];?>" onclick="GoToSubLevel('<?=$url;?>', 'wndw0' ); return false;"><?=$txt_tmp;?></a> <?if($sbl>0) { ?><span class="simple_text"><?=' ['.$sbl.']';?></span><?}?>
                 </td>
                 <?
                 }
                 if( $this->usemeta==1 ){?>
                   <td align="left" style="padding:5px; font-weight:normal;" nowrap="nowrap">
                    <?if( !empty($row['mtitle'])){?><div><?=$this->Form->ButtonCheck(); echo ' ',$this->Msg_text['FLD_PAGES_TITLE']; ?></div><?}?>
                    <?if( !empty($row['mdescr'])){?><div><?=$this->Form->ButtonCheck(); echo ' ',$this->Msg_text['FLD_PAGES_DESCR'];?></div><?}?>
                    <?if( !empty($row['mkeywords'])){?><div><?=$this->Form->ButtonCheck(); echo ' ',$this->Msg_text['FLD_KEYWORDS'];?></div><?}?>
                   </td>
                 <?
                 }           
                 ?>
                 <td align="center" style="padding:0px 2px 0px 2px;"><?=$row['lang_name'];?></td><?
                 //echo '<br> $this->field_type='.$this->field_type;
                 if ($this->field_type!='string') {
                 ?><td align="center" nowrap><?
                   $url = '/admin/modules/sys_spr/sys_spr.php?'.$this->script_ajax.'&uselevels='.$this->uselevels;
                   if( $up!=0 )
                   {
                       $this->Form->ButtonUpAjax($this->script, $row['id'], $url, 'debug', 'move', $row['move']);
                       /*?><a href="<?=$this->script?>&task=up&move=<?=$row['move']?>"><?=$this->Form->ButtonUp( $row['id'] );?></a><?*/
                   }
                   else{?><img src="images/spacer.gif" width="12"/><?}
                   //for replace
                   ?>&nbsp;<?$this->Form->TextBoxReplace($url, 'debug', 'move', $row['move'], $row['cod']);?>&nbsp;<?
                   if( $i!=($rows-1) )
                   {
                       $this->Form->ButtonDownAjax($this->script, $row['id'], $url, 'debug', 'move', $row['move']);
                       /*?><a href="<?=$this->script?>&task=down&move=<?=$row['move']?>"><?=$this->Form->ButtonDown( $row['id'] );?></a><?*/
                   }
                   else{?><img src="images/spacer.gif" width="12"/><?}
                   $up=$row['id'];
                   $a=$a-1;
                 ?></td><?               
                 }  
                 //echo '<TD>'; $this->Form->Link("$scriplink&task=add_lang&id=".$row['cod'], '&nbsp;&nbsp;'.$this->Msg_text['_LNK_OTHER_LANGUAGE'].'&nbsp;&nbsp');
                 ?>
                </tr><?
             //}//end if
         }//end for
         ?>
        </table>
        <script language="JavaScript">
         function GoToSubLevel(uri, div_id){
              $.ajax({
                    type: "POST",
                    dataType : "html",
                    data: '&level='+document.getElementById("mystr").value,
                    url: uri,
                    success: function(data){
                      $("#"+div_id).empty();
                      $("#"+div_id).append(data);
                    },
                    beforeSend: function(){
                        $("#"+div_id).html('<div style="border:0px solid #000000; padding-top:5px; padding-bottom:5px; text-align:left;" align="center"><img src="/admin/images/icons/loading_animation_liferay.gif"></div>'); 
                    }
              });
         }         
        </script>        
        <?
    }//end of function ShowContentHTML()       
       
   // ================================================================================================
   // Function : GetRowByCODandLANGID()
   // Version : 1.0.0
   // Date : 09.02.2005
   //
   // Parms :
   //                 $id   / id of editing record / Void
   //                 $mas  / array of form values
   // Returns : true,false / Void
   // Description : edit/add records in News module
   // ================================================================================================
   // Programmer : Andriy Lykhodid
   // Date : 09.02.2005
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================

   function GetRowByCODandLANGID( $cod, $ln )
   {
    $db = new Rights($this->user_id, $this->module);
    $Row = NULL;
    $q = "SELECT * FROM `".$this->spr."` WHERE `cod`='".addslashes($cod)."' AND `lang_id`='".$ln."'";
    $res = $db->Query( $q, $this->user_id, $this->module );
    //echo '<br>$q='.$q.' $res='.$res.' $db->result='.$db->result;
    if( !$res OR !$db->result ) return $Row;
    $Row = $db->db_FetchAssoc();
    return $Row;
   }

   // ================================================================================================
   // Function : edit
   // Version : 1.0.0
   // Date : 09.01.2005
   //
   // Parms :         $user_id  / user ID
   //                 $module   / Module read  / Void
   //                 $id       / id of editing record / Void
   //                 $mas      / mas with value from $_REQUEST
   //                 spr       / name of the table for this module
   // Returns : true,false / Void
   // Description : Show data from $spr table for editing
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 09.01.2005
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function edit()
   {

    if (empty($this->Msg_text)) $this->Msg_text = &check_init_txt('TblBackMulti',TblBackMulti);
    $Panel = new Panel();
    $ln_sys = &check_init('LangSys','SysLang'); 

    //$scriptact = $_SERVER['PHP_SELF'].'?module='.$module.'&display='.$_REQUEST['display'].'&start='.$_REQUEST['start'].'&sort='.$_REQUEST['sort'];

    if( $this->id!=NULL )
    {
     $q="SELECT * FROM `".$this->spr."` WHERE id='".$this->id."'";
     // edit (U)
     $result = $this->Rights->QueryResult($q, $this->user_id, $this->module);
     if( !is_array($result)) return false;
     $mas = $result[0];
    }
    /* Write Form Header */
    $this->Form->WriteHeaderFormImg( $this->script );        
    
    $this->Form->Hidden( 'module_name', $this->module_name );
    $this->Form->Hidden( 'spr', $this->spr );
    $this->Form->Hidden( 'root_script', $this->root_script );
    $this->Form->Hidden( 'parent_script', $this->parent_script ); 
    $this->Form->Hidden( 'parent_id', $this->parent_id ); 
    $this->Form->Hidden( 'sort', $this->sort );
    $this->Form->Hidden( 'display', $this->display );
    $this->Form->Hidden( 'start', $this->start );
    $this->Form->Hidden( 'fln', $this->fln );
    $this->Form->Hidden( 'srch', $this->srch );
    
    $this->Form->Hidden( 'item_img', "" );  
    $this->Form->Hidden( 'lang_id', "" );
    $this->Form->Hidden( 'edit_lang', "" );
    $this->Form->Hidden( 'usemeta', $this->usemeta );
    $this->Form->Hidden( 'useshort', $this->useshort ); 
    $this->Form->Hidden( 'useimg', $this->useimg ); 
    $this->Form->Hidden( 'uselevels', $this->uselevels );
    $this->Form->Hidden( 'usecolors', $this->usecolors );
    $this->Form->Hidden( 'usecodpli', $this->usecodpli );
    $this->Form->Hidden( 'usetranslit', $this->usetranslit );
    $this->Form->Hidden( 'usedescr', $this->usedescr );

    if( $this->id!=NULL ) $txt = $this->Msg_text['TXT_EDIT'];
    else $txt = $this->Msg_text['_TXT_ADD_DATA'];

    AdminHTML::PanelSubH( $txt );
    
    //-------- Show Error text for validation fields --------------
    $this->ShowErrBackEnd();
    //-------------------------------------------------------------          
    
    /* Write Simple Panel*/
    AdminHTML::PanelSimpleH();
    
    $q="SELECT * FROM `".$this->spr."` ORDER BY `cod` desc LIMIT 1";
    $res = $this->Rights->Query($q, $this->user_id, $this->module);
    //echo '<br> $q='.$q.' $res='.$res.' $this->Rights->result='.$this->Rights->result;
    $tmp = $this->Rights->db_FetchAssoc();
    $this->field_type = mysql_field_type($this->Rights->result,1);
 
     if( isset($mas['id']) ) $this->Form->Hidden( 'id', $mas['id'] );
     else $this->Form->Hidden( 'id', '' );
     ?>
     <tr>
      <td><b><?echo $this->Msg_text['_FLD_CODE']?>:</b></td>
      <td>
       <?
       if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->cod : $val=$mas['cod'];
       else $val=$this->cod;
       
       if ( $this->id ){
           $this->Form->TextBox( 'cod', $val, 50 );
           $this->Form->Hidden( 'cod_old', $val );
       }
       else{
           if ($this->field_type=='int'){
               $new_cod = $tmp['cod']+1;
               $this->Form->TextBox( 'cod', $new_cod, 50, 'readonly="readonly"' );
           }
           else{
               $this->Form->TextBox( 'cod', $val, 50 );
           }
       }
       ?>       
      </td>
     </tr>
     <?
       if($this->usecodpli){
       ?>
     <tr>
         <td><b><?echo $this->Msg_text['FLD_COD_PLI']?>:</b></td>
         <td>
            <?
            if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->cod_pli : $val=$mas['cod_pli'];
             else $val=$this->cod_pli;
             $this->Form->TextBox( 'cod_pli', $val, 50 );
            ?>
         </td>
     </tr>
         <?              
     }
     if($this->uselevels==1){?>
     <tr>
      <td><b><?echo $this->Msg_text['_FLD_LEVEL']?>:</b></td>
      <td>
       <?
       $arr_levels = $this->GetStructureInArray($this->spr, 0, NULL, $this->Msg_text['_TXT_ROOT_LEVEL'], '&nbsp;',$this->useshort);
//       echo '<br>mas=<pre>'; print_r($arr_levels);echo '</pre>';
       $this->Form->Select( $arr_levels, 'level_new', $this->level );
       ?>
      </td>
     </tr>
     <?}?>
     <?
     if($this->usecolors==1){
         if( $this->id!=NULL ) $this->Err!=NULL ? $colorsBit=$this->colorsBit : $colorsBit=$mas['colorsBit'];
         else $colorsBit="ffffff";
         ?>
     <tr>
      <td><b><?echo $this->Msg_text['_FLD_COLORS_FIELD']?>:</b></td>
      <td>
        <link rel="stylesheet" href="/sys/js/colorpicker/css/colorpicker.css" type="text/css" />
        <link rel="stylesheet" media="screen" type="text/css" href="/sys/js/colorpicker/css/layout.css" />
	<script type="text/javascript" src="/sys/js/colorpicker/js/colorpicker.js"></script>
        <script type="text/javascript">
            $(document).ready(function(){
               $('#colorSelector').ColorPicker({
                    color: '#<?=$colorsBit?>',
                    onShow: function (colpkr) {
                            $(colpkr).fadeIn(500);
                            return false;
                    },
                    onHide: function (colpkr) {
                            $(colpkr).fadeOut(500);
                            return false;
                    },
                    onChange: function (hsb, hex, rgb) {
                            $('#colorSelector div').css('backgroundColor', '#' + hex);
                             $('#colorBitField').val(hex);
                            
                    }
                   
            }); 
            });
        </script>
          <div id="colorSelector" style="float: left;"><div style="background-color: #<?=$colorsBit?>"></div></div>
          <input type="hidden" id="colorBitField" name="colorBit" value="<?=$colorsBit?>"/>
       <?
       //$this->Form->TextBox(  'level_new',$tmp['colorsBit'], 10 );
       ?>
      </td>
     </tr>
     <?}
   
     ?>
     <tr>
      <td colspan="2">
       <?
       $q="SELECT `name` FROM `".$this->spr."` ORDER BY `name` desc LIMIT 1";
       $res = $this->Rights->Query($q, $this->user_id, $this->module);
       //echo '<br> $q='.$q.' $res='.$res.' $this->Rights->result='.$this->Rights->result;
       $tmp = $this->Rights->db_FetchAssoc();
       $name_type = mysql_field_type($this->Rights->result,0);
       //echo '<br>$name_type='.$name_type;
       if($name_type=='blob' OR $this->usedescr==1){
           $settings=SysSettings::GetGlobalSettings();
           $this->textarea_editor = $settings['editer']; //'tinyMCE'; 
           $this->Form->IncludeSpecialTextArea( $settings['editer']); 
       }
    
       $Panel->WritePanelHead( "SubPanel_" );
       
       if( $this->usetranslit==1) {$this->ShowJS();}
       
       $ln_arr = $ln_sys->LangArray( _LANG_ID );
       if ( empty($ln_arr) )  $ln_arr[1]='';
       while( $el = each( $ln_arr ) )
       {
             $lang_id = $el['key'];
             $lang = $el['value'];
             $mas_s[$lang_id] = $lang;

             $Panel->WriteItemHeader( $lang );
                if ($this->id) $row = $this->GetRowByCODandLANGID( $mas['cod'], $lang_id ); 
                echo "\n <table border=0 class='EditTable'>";
                echo "\n <tr>";
                echo "\n <td><b>".$this->Msg_text['_FLD_NAME'].":</b>";
                echo "\n <td>";
                if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->name[$lang_id] : $val = $row['name'];
                else $val=$this->name[$lang_id];
                //$this->Form->TextBox( 'name['.$lang_id.']', stripslashes($name), 80 );
                if($name_type=='blob' AND $this->spr!='sys_spr_txt'){
                    $this->Form->SpecialTextArea( $this->textarea_editor, 'name['.$lang_id.']', stripslashes($val), 15, 70, 'style="width:100%;"', $lang_id, 'name'.$lang_id  );
                    //$this->Form->TextArea( 'name['.$lang_id.']', stripslashes($val), 10, 70 );
                }
                else $this->Form->TextArea( 'name['.$lang_id.']', stripslashes($val), 10, 70 );
                
                if( $this->useshort==1) {
                    echo "\n <tr>";
                    echo "\n <td><b>".$this->Msg_text['_FLD_SHORT_NAME'].":</b>";
                    echo "\n <td>";
                    if( $this->id!=NULL ) $this->Err!=NULL ? $short=$this->short[$lang_id] : $short = $row['short'];
                    else $short=$this->short[$lang_id];                
                    $this->Form->TextBox( 'short['.$lang_id.']', stripslashes($short), 40 );
                }
                
                if( $this->usetranslit==1) {
                    echo "\n <tr>";
                    echo "\n <td valign=top><b>".$this->Msg_text['FLD_PAGE_URL'].":</b>";
                    echo "\n <td>";
                    if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->translit[$lang_id] : $val = $row['translit'];
                    else $val=$this->translit[$lang_id];
                    if( $this->id ){
                        $params = 'disabled';
                        $this->Form->Hidden( 'translit['.$lang_id.']', stripslashes($val) ); 
                    }
                    else {
                        $params="onkeyup=\"CheckTranslitField('translit".$lang_id."','tbltranslit".$lang_id."');\"";
                        //$params='';
                    }
                    $this->Form->TextBox( 'translit['.$lang_id.']', stripslashes($val), 40, 'id="translit'.$lang_id.'"; style="font-size:10px;" '.$params );
                    if( $this->id ){?>&nbsp;<?$this->Form->ButtonSimple("btn", $this->Msg_text['TXT_EDIT'], NULL, "id='button".$lang_id."' onClick=\"EditTranslit('translit".$lang_id."','button".$lang_id."');\"");}
                    else{ 
                        ?><br>
                        <table class='EditTable' id="tbltranslit<?=$lang_id;?>" width="600">
                         <tr>
                          <td><img src='images/icons/info.png' alt='' title='' border='0' /></td>
                          <td class='info'><?=$this->Msg_text['HELP_FLD_PAGE_URL'];?></td>
                         </tr>
                         <tr>
                          <td></td>
                          <td>
                           <?$this->Form->Radio( 'translit_from['.$lang_id.']', $this->Msg_text['_FLD_NAME'], '1', 1 );?>
                           <br/><?$this->Form->Radio( 'translit_from['.$lang_id.']', $this->Msg_text['_FLD_SHORT_NAME'], '2', 1 );?>
                           <br/><?$this->Form->Radio( 'translit_from['.$lang_id.']', $this->Msg_text['TXT_NO_AUTO_TRANSLIT'], '0', 1 );?>
                          </td>
                        </table><?
                        ?><br/><?
                    }
                }

                if( $this->usedescr==1) {
                    echo "\n <tr>";
                    echo "\n <td><b>".$this->Msg_text['FLD_DESCRIPTION'].":</b>";
                    echo "\n <td>";
                    if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->descr[$lang_id] : $val = $row['descr'];
                    else $val=$this->descr[$lang_id];                
                    //$this->Form->TextBox( 'descr['.$lang_id.']', stripslashes($val), 40 );
                    $this->Form->SpecialTextArea( $this->textarea_editor, 'descr['.$lang_id.']', stripslashes($val), 15, 70, 'style="width:100%;"', $lang_id  );
                }
                
                if( $this->useimg==1) { 
                    echo "\n <tr>\n <td><b>".$this->Msg_text['_FLD_IMAGE'].":</b>";
                    echo "\n <td>";
                    if ( !isset($this->img[$lang_id]) ) $this->img[$lang_id]=NULL;
                    if( $this->id!=NULL ) $this->Err!=NULL ? $img=$this->img[$lang_id] : $img = $row['img'];
                    else $img=$this->img[$lang_id];                 
                    if( !empty($img) ) {
                        ?><table border=0 cellpadding=0 cellspacing=5>
                           <tr>
                            <td><?
                        $this->Form->Hidden( 'img['.$lang_id.']', $img );
                        //$this->Form->Hidden( 'item_img', NULL );
                        ?><?
                        echo $this->ShowImage($this->spr, $lang_id, $img, 'size_width=150', 85, NULL, NULL);
                            ?><td class='EditTable'><?
                        echo '<br>'.$this->GetImgFullPath($img, $this->spr, $lang_id).'<br>';
                        ?><a href="javascript:form_sys_spr.edit_lang.value='<?=$lang_id;?>';form_sys_spr.item_img.value='<?=$img;?>';form_sys_spr.submit();"><?=$this->Msg_text['_TXT_DELETE_IMG'];?></a><?
                       ?></table><?
                       echo '<b>'.$this->Msg_text['_TXT_REPLACE_IMG'].':</b>';
                    }
                    //else {                    
                        ?>
                        <INPUT TYPE="file" NAME="image[<?=$lang_id;?>]" size="40" VALUE="<?=$img?>"> 
                        <?
                        //echo $mas['img'][$lang_id];
                    //}
                } 
                echo   "\n </table>";
                
                if( $this->usemeta==1) {
                    echo "\n<fieldset title='".$this->Msg_text['_TXT_META_DATA']."'> <legend><span style='vetical-align:middle; font-size:15px;'><img src='images/icons/meta.png' alt='".$this->Msg_text['_TXT_META_DATA']."' title='".$this->Msg_text['_TXT_META_DATA']."' border='0' /> ".$this->Msg_text['_TXT_META_DATA']."</span></legend>";
                    echo "\n <table border=0 class='EditTable'>";
                    echo "\n <tr>";
                    echo "\n <td><b>".$this->Msg_text['FLD_PAGES_TITLE'].":</b>";
                    echo "\n <br>";
                    echo '<span class="help">'.$this->Msg_text['HELP_MSG_PAGE_TITLE'].'</span>';
                    echo "\n <br>";
                    if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->mtitle[$lang_id] : $val=$row['mtitle'];
                    else $val=$this->mtitle[$lang_id];
                    $this->Form->TextBox( 'mtitle['.$lang_id.']', stripslashes($val), 70 );
                    echo "<hr width='70%' align='left' size='1'>";
                
                    echo "\n <tr>";
                    echo "\n <td><b>".$this->Msg_text['FLD_PAGES_DESCR'].":</b>";
                    echo "\n <br>";
                    echo '<span class="help">'.$this->Msg_text['HELP_MSG_PAGE_DESCRIPTION'].'</span>';
                    echo "\n <br>";
                    if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->mdescr[$lang_id] : $val=$row['mdescr'];
                    else $val=$this->mdescr[$lang_id];
                    $this->Form->TextArea( 'mdescr['.$lang_id.']', stripslashes($val), 3, 70 );
                    echo "<hr width='70%' align='left' size='1'>";
                
                    echo "\n <tr>";
                    echo "\n <td><b>".$this->Msg_text['FLD_KEYWORDS'].":</b>";
                    echo "\n <br>";
                    echo '<span class="help">'.$this->Msg_text['_HELP_MSG_PAGE_KEYWORDS'].'</span>';
                    echo "\n <br>";
                    if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->mkeywords[$lang_id] : $val=$row['mkeywords'];
                    else $val=$this->mkeywords[$lang_id];
                    $this->Form->TextArea( 'mkeywords['.$lang_id.']', stripslashes($val),3, 70 );
                    echo "\n </table>";
                    echo "</fieldset><br>"; 
                }
                               
             $Panel->WriteItemFooter();
       }
       $Panel->WritePanelFooter();
    AdminHTML::PanelSimpleF();
    $this->Form->WriteSavePanel( $this->script );
    $this->Form->WriteCancelPanel( $this->script );
     
    AdminHTML::PanelSubF();
    $this->Form->WriteFooter();
   
    return true;
   }  //end of fuinction edit

   // ================================================================================================
   // Function : ShowJS()
   // Version : 1.0.0
   // Date : 08.08.2007
   // Parms :  
   // Returns : true,false / Void
   // Description : show form with rating from users about goods
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 08.08.2007 
   // Reason for change : Reason Description / Creation
   // Change Request Nbr:
   // ================================================================================================
   function ShowJS() 
   {
       ?>
        <script type="text/javascript">
        function EditTranslit(div_id, idbtn){
            Did = "#"+div_id;
            idbtn = "#"+idbtn;
            if( !window.confirm('<?=$this->Msg_text['MSG_DO_YOU_WANT_TO_EDIT_TRANSLIT'];?>')) return false;
            else{
              $(Did).removeAttr("disabled")
                     .focus();
              $(idbtn).css("display", "none");
            }
        } // end of function EditTranslit
        function CheckTranslitField(div_id, idtbl){
            Did = "#"+div_id;
            idtbl = "#"+idtbl;
            //alert('val='+(Did).val());
            if( $(Did).val()!='') $(idtbl).css("display", "none");
            else $(idtbl).css("display", "block");
        } // end of function EditTranslit
        </script>
        <?       
   }//end of function ShowJS()


   // ================================================================================================
   // Function : EditWithAjax
   // Version : 1.0.0
   // Date : 11.08.2008
   //
   // Parms :
   // Returns : true,false / Void
   // Description : Show data from $spr table for editing using Ajax
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 11.08.2008
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function EditWithAjax()
   {
    //$Panel = new Panel();
    //$ln_sys = new SysLang();
    if( $this->id!=NULL )
    {
     $q="SELECT * FROM `".$this->spr."` WHERE `id`='".$this->id."'";
     // edit (U)
     $res = $this->Rights->Query($q, $this->user_id, $this->module);
     if( !$res ) return false;
     $mas = $this->Rights->db_FetchAssoc();
    }
    
    /* Write Form Header */
    $this->Form->WriteHeader( $this->script, 'target="_parent"' );        
    
    $this->Form->Hidden( 'module_name', $this->module_name );
    $this->Form->Hidden( 'spr', $this->spr );
    $this->Form->Hidden( 'root_script', $this->root_script );
    $this->Form->Hidden( 'parent_script', $this->parent_script ); 
    $this->Form->Hidden( 'parent_id', $this->parent_id ); 
    $this->Form->Hidden( 'sort', $this->sort );
    $this->Form->Hidden( 'display', $this->display );
    $this->Form->Hidden( 'start', $this->start );

    $this->Form->Hidden( 'fln', $this->fln );
    $this->Form->Hidden( 'srch', $this->srch );
    
    $this->Form->Hidden( 'item_img', "" );  
    $this->Form->Hidden( 'lang_id', "" );
    $this->Form->Hidden( 'usemeta', $this->usemeta );
    $this->Form->Hidden( 'useshort', $this->useshort );
    $this->Form->Hidden( 'useimg', $this->useimg );
    $this->Form->Hidden( 'uselevels', $this->uselevels );

    if( $this->id!=NULL ) $txt = $this->Msg_text['TXT_EDIT'];
    else $txt = $this->Msg_text['_TXT_ADD_DATA'];

    AdminHTML::PanelSubH( $txt );
    
    //-------- Show Error text for validation fields --------------
    $this->ShowErrBackEnd();
    //-------------------------------------------------------------          
    
   /* Write Simple Panel*/
    AdminHTML::PanelSimpleH();
    
    $q="SELECT * FROM `".$this->spr."` ORDER BY `cod` desc LIMIT 1";
    $res = $this->Rights->Query($q, $this->user_id, $this->module);
    //echo '<br> $q='.$q.' $res='.$res.' $this->Rights->result='.$this->Rights->result;
    $tmp = $this->Rights->db_FetchAssoc();
    $this->field_type = mysql_field_type($this->Rights->result,1);
    $fields_col = mysql_num_fields($this->Rights->result);
    
    ?>                   
    <table border="0" width="100%" align="center" class="EditTable">
     <tr>
      <td><b><?echo $this->Msg_text['FLD_ID']?>:</b></td>
      <td width="95%">
       <?
       if( $this->id ){
           echo $mas['id'];
           $this->Form->Hidden( 'id', $mas['id'] );
       }
       ?>
      </td>
     </tr>
     <tr>
      <td><b><?echo $this->Msg_text['_FLD_CODE']?>:</b></td>
      <td>
       <?
       if( $this->id ){
          $this->Form->TextBox( 'cod', $mas['cod'], 50, 'id="cod"' );
          $this->cod = $mas['cod'];
          $this->Form->Hidden('cod', $this->cod); 
       }
       else{
          if ($this->field_type=='int') $new_cod = $tmp['cod']+1;
          else $new_cod='';
          $this->Form->TextBox( 'cod', $new_cod, 50, 'id="cod"' );
       }
      ?>
      </td>
     </tr>
     <?
     if($this->uselevels==1){?>
     <tr>
      <td><b><?echo $this->Msg_text['_FLD_LEVEL']?>:</b></td>
      <td>
       <?
       $arr_levels = $this->GetStructureInArray($this->spr, 0, NULL, $this->Msg_text['_TXT_ROOT_LEVEL'], '&nbsp;', $this->useshort);
       $this->Form->Select( $arr_levels, 'level_new', $this->level );
       ?>
      </td>
     </tr>
     <?}?>
     <tr>
      <td colspan="2">
        <div id="edlngpanel">
         <?=$this->EditLngPanel();?>
        </div> 
        <script language="JavaScript">
        var idResp;
        function onAjaxSuccess(data)
        {
         // Здесь мы получаем данные, отправленные сервером
           $("#"+idResp).empty();
           $("#"+idResp).append(data);
        }

        function makeRequest(script, idForm, idRes){
           idResp = idRes;
           document.<?=$this->Form->name?>.task.value='edit_lng_panel';
           $.post(script, $('#'+idForm).formSerialize(), onAjaxSuccess);
        } // end of function makeRequest

        function ChangeTabStyle(lang, on_off)
        {
           if(on_off=='on') {
               $("#tabpanel"+lang).toggleClass("tab-panel-passive");
               $("#tabpanel"+lang).addClass("tab-panel-hover");
           }
           if(on_off=='off') {
               $("#tabpanel"+lang).toggleClass("tab-panel-hover");
               $("#tabpanel"+lang).addClass("tab-panel-passive");
           }
        }
        
        function upl_file(uri, div_id, form) {
            document.<?=$this->Form->name?>.task.value='add_img_on_lang';
            Did = "#"+div_id;
            $.ajaxUpload
            ({
                url:uri,
                secureuri:false,
                uploadform: form,
                type: 'POST',
                dataType: 'html',
                success: function (img_upload, status)
                {
                    $(Did).html(img_upload).animate({
                      opacity: 'show'
                    }, "slow", "easein");
                },
                error: function (img_upload, status)
                {
                    $(Did).html(status).animate({
                      opacity: 'show'
                    }, "slow", "easein");
                }

            });
        }
        
        function DelItemImg(uri, idForm, div_id){
            document.<?=$this->Form->name?>.task.value='delitemimg';
            Did = "#"+div_id;
            $.ajax
            ({
                url:uri,
                type: 'POST',
                dataType: 'html',
                success: function (img_upload, status)
                {
                    $(Did).html(img_upload).animate({
                      opacity: 'show'
                    }, "slow", "easein");
                },
                error: function (img_upload, status)
                {
                    $(Did).html(status).animate({
                      opacity: 'show'
                    }, "slow", "easein");
                }

            });
        } // end of function DelItemImg         
        </script>
      </td>
     </tr> 
     <?
     /*
     $tmp_db = new DB();
     $q = "SELECT * FROM `".$this->spr."` WHERE 1 LIMIT 1";
     $res = $tmp_db->db_Query($q);
     //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
     if ( !$res ) return false;
     if ( !$tmp_db->result ) return false;
     $fields_col = mysql_num_fields($tmp_db->result);
     */
     if($fields_col>4){         
      if($this->id==NULL){
         $tmp_db = new DB();
         $tmp_q = "select MAX(`move`) as maxx from `".$this->spr."` order by `move` desc";
         $res = $tmp_db->db_Query( $tmp_q );
         if( !$res )return false;
         $tmp_row = $tmp_db->db_FetchAssoc();
         $move = $tmp_row['maxx'];
         $move=$move+1;
      }
      else $move=$mas['move'];
      $this->Form->Hidden( 'move', $move );         
     }         
     ?>
     <tr>
      <td colspan="2" align="left">  
       <?
       $this->Form->WriteSavePanelNew( $this->script, 'save' );
       //$this->Form->WriteSavePanel( $this->script, 'save', 'checkCod();' ); 
       $this->Form->WriteCancelPanel( $this->script );
       ?>
      </td>
     </tr>
    </table>
    <?
    AdminHTML::PanelSimpleF();
    AdminHTML::PanelSubF();
    $this->Form->WriteFooter();
    return true;
   }  //end of fuinction EditWithAjax
   
   // ================================================================================================
   // Function : EditLngPanel
   // Version : 1.0.0
   // Date : 11.08.2008
   //
   // Parms :
   // Returns : true,false / Void
   // Description : edit data on selected language
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 11.08.2008
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function EditLngPanel()
   {
       $ln_sys = &check_init('LangSys','SysLang'); 
       $ln_arr = $ln_sys->LangArray( _LANG_ID );
       if ( empty($ln_arr) )  $ln_arr[1]='';
       ?>
       <table border="0" cellpadding="0" cellspacing="0">
        <tr>
         <td>
       <div style="height:25px; border:0px solid #000000;"><?
       while( $el = each( $ln_arr ) )
       {
         $lang_id = $el['key'];
         $lang = $el['value']; 
         if ($this->id) {
             $row = $this->GetRowByCODandLANGID( $this->cod, $lang_id );
         
             if( !isset($this->name[$lang_id])) $this->Form->Hidden('name['.$lang_id.']', $row['name']);
             else $this->Form->Hidden('name['.$lang_id.']', $this->name[$lang_id] ); 
         
             if($this->useshort==1){
                if( !isset($this->short[$lang_id])) $this->Form->Hidden('short['.$lang_id.']', $row['short']);
                else $this->Form->Hidden('short['.$lang_id.']', $this->short[$lang_id] );
             }
             
             if($this->useimg==1){
                if( !isset($this->img[$lang_id])) $this->Form->Hidden('img['.$lang_id.']', $row['img']);
                else $this->Form->Hidden('img['.$lang_id.']', $this->img[$lang_id] );
             }
             
             if($this->usemeta==1){
                if( !isset($this->mtitle[$lang_id])) $this->Form->Hidden('mtitle['.$lang_id.']', $row['mtitle']);
                else $this->Form->Hidden('mtitle['.$lang_id.']', $this->mtitle[$lang_id] );
                if( !isset($this->mdescr[$lang_id])) $this->Form->Hidden('mdescr['.$lang_id.']', $row['mdescr']);
                else $this->Form->Hidden('mdescr['.$lang_id.']', $this->mdescr[$lang_id] );
                if( !isset($this->mkeywords[$lang_id])) $this->Form->Hidden('mkeywords['.$lang_id.']', $row['mkeywords']);
                else $this->Form->Hidden('mkeywords['.$lang_id.']', $this->mkeywords[$lang_id] );
             }
         }
         else {
             if( isset($this->name[$lang_id])) $this->Form->Hidden('name['.$lang_id.']', $this->name[$lang_id] );
             if($this->useshort==1){
                 if( isset($this->short[$lang_id])) $this->Form->Hidden('short['.$lang_id.']', $this->short[$lang_id] );
             }
             if($this->useimg==1){
                if( isset($this->img[$lang_id])) $this->Form->Hidden('img['.$lang_id.']', $this->img[$lang_id] );
             }
             if($this->usemeta==1){
                if( isset($this->mtitle[$lang_id])) $this->Form->Hidden('mtitle['.$lang_id.']', $this->mtitle[$lang_id] );
                if( isset($this->mdescr[$lang_id])) $this->Form->Hidden('mdescr['.$lang_id.']', $this->mdescr[$lang_id] );
                if( isset($this->mkeywords[$lang_id])) $this->Form->Hidden('mkeywords['.$lang_id.']', $this->mkeywords[$lang_id] );
             }
         }
         $url = '/admin/modules/sys_spr/sys_spr.php?'.$this->script_ajax.'&edit_lang='.$lang_id;
         if($lang_id==$this->edit_lang) $class="tab-panel-active";
         else $class="tab-panel-passive";
         ?><div id="tabpanel<?=$lang_id;?>" class="<?=$class;?>" <?if($lang_id!=$this->edit_lang) {?>onmouseover="ChangeTabStyle(<?=$lang_id;?>, 'on');" onmouseout="ChangeTabStyle(<?=$lang_id;?>, 'off');"<?}?> onclick="makeRequest('<?=$url;?>', '<?=$this->Form->name;?>', 'edlngpanel'); return false;"><div style="padding-top:5px;"><?=$lang;?></div></div><?         
       }
       ?><div style="float:none;">&nbsp;</div></div>
         </td>
        </tr>
        <tr>
         <td> 
       <div class="editlngpanel">
       <?
        if ($this->id) $row = $this->GetRowByCODandLANGID( $this->cod, $this->edit_lang ); 
        echo "\n <table border='0' class='EditTable'>";
        echo "\n <tr>";
        echo "\n <td valign='top'><b>".$this->Msg_text['_FLD_NAME'].":</b>";
        echo "\n <td>";
        if( isset($this->name[$this->edit_lang]) ) $val=$this->name[$this->edit_lang];
        else{
            if( isset($row['name']) ) $val = $row['name'];
            else $val = NULL;
        } 
        $this->Form->TextArea( 'name['.$this->edit_lang.']', stripslashes($val), 10, 70 );

        if($this->useshort==1){
            echo "\n <tr>";
            echo "\n <td><b>".$this->Msg_text['_FLD_SHORT_NAME'].":</b>";
            echo "\n <td>";
            if( isset($this->short[$this->edit_lang]) ) $val=$this->short[$this->edit_lang];
            else{
                if( isset($row['short']) ) $val = $row['short'];
                else $val = NULL;
            }        
            $this->Form->TextBox( 'short['.$this->edit_lang.']', stripslashes($val), 40 );
        }
        if($this->useimg==1){
            echo "\n <tr>";
            echo "\n <td><b>".$this->Msg_text['_FLD_IMAGE'].":</b>";
            echo "\n <td>";
            if( isset($this->img[$this->edit_lang]) ) $img=$this->img[$this->edit_lang];
            else{
                if( isset($row['img']) ) $img = $row['img'];
                else $img = NULL;
            }
            ?><div id="editlngpanel_img"><?$this->EditLngPanelImg($img);?></div><?  
            $url = '/admin/modules/sys_spr/sys_spr.php?'.$this->script_ajax.'&edit_lang='.$this->edit_lang;                    
            ?>
            <input type="file" name="image[<?=$this->edit_lang;?>]" size="40" value="<?=$img?>">
            <input type="submit" value="Загрузить" onclick="upl_file('<?=$url;?>', 'editlngpanel_img', this.form);return false;">                    
            <?
            echo   "\n </table>";
        }
        if( $this->usemeta==1) {
            echo "\n<fieldset title='".$this->Msg_text['_TXT_META_DATA']."' style='width:70%'><legend><span style='vetical-align:middle; font-size:15px;'><img src='images/icons/meta.png' alt='".$this->Msg_text['_TXT_META_DATA']."' title='".$this->Msg_text['_TXT_META_DATA']."' border='0' /> ".$this->Msg_text['_TXT_META_DATA']."</span></legend>";
            echo "\n <table border=0 class='EditTable'>";
            echo "\n <tr>";
            echo "\n <td><b>".$this->Msg_text['FLD_PAGES_TITLE'].":</b>";
            echo "\n <br>";
            echo '<span class="help">'.$this->Msg_text['HELP_MSG_PAGE_TITLE'].'</span>';
            echo "\n <br>";
            //if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->mtitle[$this->edit_lang] : $val=$row['mtitle'];
            //else $val=$this->mtitle[$this->edit_lang];
            if( isset($this->mtitle[$this->edit_lang]) ) $val=$this->mtitle[$this->edit_lang];
            else{
                if( isset($row['mtitle']) ) $val = $row['mtitle'];
                else $val = NULL;
            }              
            $this->Form->TextBox( 'mtitle['.$this->edit_lang.']', stripslashes($val), 70 );
            echo "<hr width='70%' align='left' size='1'>";
        
            echo "\n <tr>";
            echo "\n <td><b>".$this->Msg_text['FLD_PAGES_DESCR'].":</b>";
            echo "\n <br>";
            echo '<span class="help">'.$this->Msg_text['HELP_MSG_PAGE_DESCRIPTION'].'</span>';
            echo "\n <br>";
            //if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->mdescr[$this->edit_lang] : $val=$row['mdescr'];
            //else $val=$this->mdescr[$this->edit_lang];
            if( isset($this->mdescr[$this->edit_lang]) ) $val=$this->mdescr[$this->edit_lang];
            else{
                if( isset($row['mdescr']) ) $val = $row['mdescr'];
                else $val = NULL;
            }             
            $this->Form->TextArea( 'mdescr['.$this->edit_lang.']', stripslashes($val), 3, 70 );
            echo "<hr width='70%' align='left' size='1'>";
        
            echo "\n <tr>";
            echo "\n <td><b>".$this->Msg_text['FLD_KEYWORDS'].":</b>";
            echo "\n <br>";
            echo '<span class="help">'.$this->Msg_text['_HELP_MSG_PAGE_KEYWORDS'].'</span>';
            echo "\n <br>";
            //if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->mkeywords[$this->edit_lang] : $val=$row['mkeywords'];
            //else $val=$this->mkeywords[$this->edit_lang];
            if( isset($this->mkeywords[$this->edit_lang]) ) $val=$this->mkeywords[$this->edit_lang];
            else{
                if( isset($row['mkeywords']) ) $val = $row['mkeywords'];
                else $val = NULL;
            }             
            $this->Form->TextArea( 'mkeywords['.$this->edit_lang.']', stripslashes($val),3, 70 );
            echo "\n </table>";
            echo "</fieldset><br>"; 
        }
        ?></div>
          </td>
         </tr>
        </table><?
   }//end of function EditLngPanel()

   // ================================================================================================
   // Function : EditLngPanelImg
   // Version : 1.0.0
   // Date : 12.08.2008
   //
   // Parms :
   // Returns : true,false / Void
   // Description : edit image on selected language
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 12.08.2008
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function EditLngPanelImg($img)
   {
        if( !empty($img) ) {
            ?>
            <table border="0" cellpadding="0" cellspacing="5">
             <tr>
              <td>
               <?
               $this->Form->Hidden( 'img['.$this->edit_lang.']', $img );
               //$this->Form->Hidden( 'item_img', NULL );
               $file_with_path = Spr_Img_Path.$this->spr.'/'.$this->edit_lang.'/'.$img;
               $link = 'http://'.NAME_SERVER.Spr_Img_Path_Small.$this->spr.'/'.$this->edit_lang.'/'.$img; 
               //echo '<br/>$file_with_path='.$file_with_path;
               //echo '<br/>$link='.$link;
               $Uploads = new Uploads();
               $koef = NULL;
               if($Uploads->IsImage($img)){
                    $size = @GetImageSize($file_with_path);
                    //echo '<br>$size[0]='.$size[0].' $size[1]='.$size[1];
                    if($size[1]) $koef = $size[0]/$size[1];
                    $width=100;
                    if($koef)   $height = $width/$koef;
                    $new_w = $size[0]+20;
                    $news_h = $size[1]+20;
                    //$img_path ="<img src=http://".NAME_SERVER.$dir_path_to_img.$this->IConvert($row['name'])." width=".$width." height=".$height." border=0>";
               }
               else{ 
                    $new_w = 800;
                    $news_h = 600;
                    $img='';
               }
               $params = "OnClick='window.open(\"".$link."\", \"\", \"width=".$new_w.", height=".$news_h.", status=0, toolbar=0, location=0, menubar=0, resizable=0, scrollbars=0\");'";
               ?><a href="javascript:void(0);" <?=$params;?>><?=$this->ShowImage($this->spr, $this->edit_lang, $img, 'size_width=150', 85, NULL, NULL);?></a>
              </td>
              <td class='EditTable'>
               <?
               echo '<br>'.$this->GetImgFullPath($img, $this->spr, $this->edit_lang).'<br>';
               $url = '/admin/modules/sys_spr/sys_spr.php?'.$this->script_ajax.'&item_img='.$img.'&edit_lang='.$this->edit_lang
               /*?><a href="javascript:<?=$this->Form->name;?>.item_img.value='<?=$img;?>';<?=$this->Form->name;?>.submit();" onClick="DelItemImg('<?=$url;?>', '<?=$this->Form->name;?>', 'editlngpanel_img'); return false;"><?=$this->Msg_text['_TXT_DELETE_IMG'];?></a><?*/
               ?><a href="#" onClick="DelItemImg('<?=$url;?>', '<?=$this->Form->name;?>', 'editlngpanel_img'); return false;"><?=$this->Msg_text['_TXT_DELETE_IMG'];?></a>
              </td>
             </tr> 
            </table>
            <?
           echo '<b>'.$this->Msg_text['_TXT_REPLACE_IMG'].':</b>';
        }
   }//end of function EditLngPanelImg()       
   
   
   // ================================================================================================
   // Function : CheckFields
   // Version : 1.0.0
   // Date : 18.04.2010
   //
   // Parms :
   // Returns : true,false / Void
   // Description : check fields
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 18.04.2010 
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================   
   function CheckFields()
   {
       $this->Err = NULL;
       
       if(empty($this->cod)) $this->Err .= $this->Msg_text['MSG_ERR_SPR_EMPTY_CODE'].'<br />';  
       
       $q = "SELECT * FROM `".$this->spr."` WHERE `cod`='".$this->cod."'";
       $res = $this->Rights->Query($q, $this->user_id, $this->module);
       //echo '<br> $q='.$q.' $res='.$res.' $this->Rights->result='.$this->Rights->result;
       if( !$res OR !$this->Rights->result ) return false;
       $rows_by_cod = $this->Rights->db_GetNumRows();
       //echo '<br />$rows_by_cod='.$rows_by_cod.' $this->id='.$this->id;
       //if already exist record with same cod when we want create new one, then show error
       if($rows_by_cod>0 AND empty($this->id) ) $this->Err .= $this->Msg_text['MSG_ERR_SPR_CODE_ALREADY_EXIST'].'<br />';
       
   }//end of function CheckFields()   
   
   // ================================================================================================
   // Function : save
   // Version : 1.0.0
   // Date : 09.01.2005
   //
   // Parms :
   // Returns : true,false / Void
   // Description : Store data to the table
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 09.01.2005
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function save()
   {
    $ln_sys = &check_init('LangSys','SysLang'); 
    $tmp_db = &DBs::getInstance();
    //$this->cod = $this->Form->GetRequestTxtData( $this->cod );
    
    $q = "SELECT * FROM `".$this->spr."` WHERE `cod`='".$this->cod."'";
    $res = $this->Rights->Query($q, $this->user_id, $this->module);
    //echo '<br> $q='.$q.' $res='.$res.' $this->Rights->result='.$this->Rights->result;
    if( !$res OR !$this->Rights->result ) return false;
    $rows_by_cod = $this->Rights->db_GetNumRows();
    $num_fields = mysql_num_fields($this->Rights->result);        
    
    $ln_arr = $ln_sys->LangArray( _LANG_ID );
    if ( empty($ln_arr) ) $ln_arr[1]='';
     if( $this->uselevels==1 ) 
          {
              $node = $this->GetNodeForPosition($this->spr,$this->level_new);
          }
    while( $el = each( $ln_arr ) )
    {
       //echo '<br> $el[key]='.$el['key'].' $name[ '.$el['key'].' ]='.$name[ $el['key'] ];
       $lang_id = $el['key']; 
       $name = $this->Form->GetRequestTxtData($this->name[$lang_id]);
       $short = $this->Form->GetRequestTxtData($this->short[$lang_id]);
       isset($this->img[$lang_id]) ?  $img = $this->img[$lang_id] : $img = NULL;
       $mtitle = $this->Form->GetRequestTxtData($this->mtitle[$lang_id]); 
       $mdescr = $this->Form->GetRequestTxtData($this->mdescr[$lang_id]); 
       $mkeywords = $this->Form->GetRequestTxtData($this->mkeywords[$lang_id]); 
       $translit = $this->Form->GetRequestTxtData($this->translit[$lang_id]);
       $descr = $this->Form->GetRequestTxtData($this->descr[$lang_id]);
       if( empty($translit) AND !$this->id ) {
           $Crypt = new Crypt();
           if( $this->translit_from[$lang_id]==1 ) $translit = $Crypt->GetTranslitStr(strip_tags($name));
           elseif( $this->translit_from[$lang_id]==2 ) $translit = $Crypt->GetTranslitStr(strip_tags($short));
       }
       //echo '<br>$this->img=';
       //print_r($this->img);
       

       $q = "SELECT * FROM `".$this->spr."` WHERE `cod`='".$this->cod_old."' AND `lang_id`='".$lang_id."'";
       $res = $this->Rights->Query($q, $this->user_id, $this->module);
       //echo '<br> $q='.$q.' $res='.$res.' $$this->Rights->result='.$this->Rights->result;
       if( !$res OR !$this->Rights->result ) return false;
       $rows = $this->Rights->db_GetNumRows();

       if( $rows>0 )   //--- update
       {
          $row = $this->Rights->db_FetchAssoc();
          //echo '<br>$row[img]='.$row['img'].' $img='.$img;
          //Del old image Image
          if ( !empty($row['img']) AND $row['img']!=$img) {
            $this->DelItemImage($row['img'], $lang_id);
          } 
         
          $q="UPDATE `".$this->spr."` SET 
              `cod`='".$this->cod."',
              `lang_id`='".$lang_id."',
              `name`='".$name."'";
          if( $this->useshort==1 ) $q = $q.", `short`='".$short."'";
          if( $this->useimg==1 ) $q = $q.", `img`='".$img."'";
          if( $this->uselevels==1 ) $q = $q.", `level`='".$this->level_new."', `node`='".$node."'";
          if( $this->usemeta==1 ) $q = $q.", `mtitle`='".$mtitle."', `mdescr`='".$mdescr."', `mkeywords`='".$mkeywords."'";
          if( $this->usetranslit==1 ) $q = $q.", `translit`='".$translit."'";
          if( $this->usedescr==1 ) $q = $q.", `descr`='".$descr."'";
          if( $this->usecolors==1 ) $q = $q.", `colorsBit`='".$this->colorBit."'";
          if( $this->usecodpli==1 ) $q = $q.", `cod_pli`='".$this->cod_pli."'";
          if( !empty($this->id_cat) ) $q = $q.", `id_cat`='".$this->id_cat."'";
          if( !empty($this->id_param) ) $q = $q.", `id_param`='".$this->id_param."'";
          $q = $q." WHERE `cod`='".$this->cod_old."' AND `lang_id`='".$lang_id."'";
          
       }
       else          //--- insert
       {
          $q="INSERT INTO `".$this->spr."` SET
              `cod`='".$this->cod."',
              `lang_id`='".$lang_id."',
              `name`='".$name."'";
          if( $this->useshort==1 ) $q = $q.", `short`='".$short."'";
          if( $this->useimg==1 ) $q = $q.", `img`='".$img."'";
          if( $this->uselevels==1 ) $q = $q.", `level`='".$this->level_new."', `node`='".$node."'";
          if( $this->usemeta==1 ) $q = $q.", `mtitle`='".$mtitle."', `mdescr`='".$mdescr."', `mkeywords`='".$mkeywords."'";
          if( $this->usetranslit==1 ) $q = $q.", `translit`='".$translit."'";
          if( $this->usedescr==1 ) $q = $q.", `descr`='".$descr."'";
          if( $this->usecolors==1 ) $q = $q.", `colorsBit`='".$this->colorBit."'";
          if( $this->usecodpli==1 ) $q = $q.", `cod_pli`='".$this->cod_pli."'";
          if( !empty($this->id_cat) ) $q = $q.", `id_cat`='".$this->id_cat."'";
          if( !empty($this->id_param) ) $q = $q.", `id_param`='".$this->id_param."'";
       }
     
       $res = $this->Rights->Query( $q, $this->user_id, $this->module );
       //echo '<br> $q='.$q.' $res='.$res.' $this->Rights->result='.$this->Rights->result;
       if( !$res OR !$this->Rights->result ) return false;
    } //--- end while

    //echo '<br> $rows_by_cod='.$rows_by_cod.' $num_fields='.$num_fields;
    if ($rows_by_cod==0 AND $num_fields>4) {       
        $q="SELECT MAX(`move`) as maxx FROM `".$this->spr."` WHERE `lang_id`='"._LANG_ID."'";
        $res = $this->Rights->Query( $q, $this->user_id, $this->module );
        $rows = $this->Rights->db_GetNumRows();
        $my = $this->Rights->db_FetchAssoc(); 
        $maxx=$my['maxx']+1;  //add link with position auto_incremental
        $q="UPDATE `".$this->spr."` SET `move`='".$maxx."' WHERE `cod`='".$this->cod."'";
        $res = $this->Rights->Query( $q, $this->user_id, $this->module );
//        echo '<br> $q='.$q.' $res='.$res.' $this->Rights->result='.$this->Rights->result;
        if( !$res OR !$this->Rights->result ) return false; 
    }     
    return true;
   } //end of fuinction save
   
     // ================================================================================================
    // Function : SavePicture
    // Version : 1.0.0
    // Date : 03.04.2006
    //
    // Parms :
    // Returns : $res / Void
    // Description : Save the file (image) to the folder  and save path in the database (table user_images)
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 03.04.2006
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function SavePicture()
    {
     
     $ln_sys = &check_init('LangSys','SysLang');     
     
     $this->Err = NULL;
     $max_image_width = SPR_MAX_IMAGE_WIDTH;
     $max_image_height = SPR_MAX_IMAGE_HEIGHT;
     $max_image_size = SPR_MAX_IMAGE_SIZE;
     $max_image_quantity = 85;
     $valid_types =  array("gif", "GIF", "jpg", "JPG", "png", "PNG", "jpeg", "JPEG");
     //print_r($_FILES["image"]);
     //$cols = count($_FILES["image"]);
     //echo '<br><br>$cols='.$cols;
     
     $ln_arr = $ln_sys->LangArray( _LANG_ID );
     if ( empty($ln_arr) ) $ln_arr[1]='';
     while( $el = each( $ln_arr ) )
     {         
         $lang_id = $el['key'];
         
         //echo '<br>$_FILES["image"]='.$_FILES["image"].' $_FILES["image"]["tmp_name"]["'.$lang_id.'"]='.$_FILES["image"]["tmp_name"]["$lang_id"].' $_FILES["image"]["size"]["'.$lang_id.'"]='.$_FILES["image"]["size"]["$lang_id"];
         //echo '<br>$_FILES["image"]["name"][$lang_id]='.$_FILES["image"]["name"][$lang_id];
         //$this->img[$lang_id] = $_FILES["image"]["name"][$lang_id]; 
         if ( !empty($_FILES["image"]["name"][$lang_id]) ) {
           if ( isset($_FILES["image"]) && is_uploaded_file($_FILES["image"]["tmp_name"][$lang_id]) && $_FILES["image"]["size"][$lang_id] ){
            $filename = $_FILES['image']['tmp_name'][$lang_id];
            $ext = substr($_FILES['image']['name'][$lang_id],1 + strrpos($_FILES['image']['name'][$lang_id], "."));
            $name_no_ext = substr($_FILES['image']['name'][$lang_id], 0, strrpos($_FILES['image']['name'][$lang_id], "."));
//            echo '<br>$ext='.$ext.' $filename='.$filename;
//            echo '<br>filesize($filename)='.filesize($filename).' $max_image_size='.$max_image_size;
            if (filesize($filename) > $max_image_size) {
                $this->Err = $this->Err.$this->Msg->show_text('MSG_ERR_FILE_SIZE').' ('.$_FILES['image']['name']["$lang_id"].')<br>';
                continue;
            }
            if (!in_array($ext, $valid_types)) {
                $this->Err = $this->Err.$this->Msg->show_text('MSG_ERR_FILE_TYPE').' ('.$_FILES['image']['name']["$lang_id"].')<br>';  
            }
            else {
              $size = GetImageSize($filename);
              //echo '<br>$size='.$size.'$size[0]='.$size[0].' $max_image_width='.$max_image_width.' $size[1]='.$size[1].' $max_image_height='.$max_image_height;
              //if (($size) && ($size[0] < $max_image_width) && ($size[1] < $max_image_height)) {
                 $uploaddir0 = Spr_Img_Path;
                 if ( !file_exists ($uploaddir0) ) mkdir($uploaddir0,0777);
                 $uploaddir1 = Spr_Img_Path.$this->spr;
                 if ( !file_exists ($uploaddir1) ) mkdir($uploaddir1,0777); 
                 $uploaddir2 = $uploaddir1.'/'.$lang_id;
                 if ( !file_exists ($uploaddir2) ) mkdir($uploaddir2,0777);
                 else @chmod($uploaddir2,0777);
                 
                 // Формирую новое название файла, которе будет храниться на сервере  
                 $this->img[$lang_id] = $name_no_ext.'_'.time().$lang_id.'.'.$ext;
                 
                 //$this->img[$lang_id] = time().'_'.$lang_id.'.'.$ext; 
                 $uploaddir = $uploaddir2."/".$this->img[$lang_id];
                 //echo '<br>$filename='.$filename.'<br> $uploaddir='.$uploaddir.'<br> $uploaddir2='.$uploaddir2;
                 //if (@move_uploaded_file($filename, $uploaddir)) {
                 if ( copy($filename,$uploaddir) ) {
                     if (($size) AND (($size[0] > $max_image_width) OR ($size[1] > $max_image_height)) ){
                         //ini_set("memory_limit","128M");
                         //============= resize original image to size from settings =============
                         $thumb = new Thumbnail($uploaddir);
                         
                         if($max_image_width==$max_image_height) $thumb->size_auto($max_image_width);
                         else{ 
                            $thumb->size_width($max_image_width);
                            $thumb->size_height($max_image_height);
                         }
                         $thumb->quality = $max_image_quantity;
                         $thumb->process();       // generate image
                         $thumb->save($uploaddir); //make new image
                         //=======================================================================
                         
                     }                         
                 }
                 else{    
                     $this->Err = $this->Err.$this->Msg->show_text('MSG_ERR_FILE_MOVE').' ('.$_FILES['image']['name']["$lang_id"].')<br>';
                 }
                 @chmod($uploaddir2,0755);
                 @chmod($uploaddir1,0755);
                 @chmod($uploaddir0,0755);
              //}
              //else {
              //   $this->Err = $this->Err.$this->Msg->show_text('MSG_ERR_FILE_PROPERTIES').' ['.$max_image_width.'x'.$max_image_height.'] ('.$_FILES['image']['name']["$lang_id"].')<br>'; 
              //}
            }
           }
           else $this->Err = $this->Err.$this->Msg->show_text('MSG_ERR_FILE').' ('.$_FILES['image']['name']["$lang_id"].')<br>';
         } 
         //echo '<br>$lang_id='.$lang_id;
     } // end while
     return $this->Err;
    }  // end of function SavePicture()
           
   // ================================================================================================
   // Function : del
   // Version : 1.0.0
   // Date : 09.01.2005
   //
   // Parms :         $user_id  / user ID
   //                 $module   / Module read  / Void
   //                 $id_del   / array of the records which must be deleted / Void
   // Returns : true,false / Void
   // Description :  Remove data from the table
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 09.01.2005
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function del($id_del)
   {
       $this->Form->Hidden( 'sort', $this->sort );
       $this->Form->Hidden( 'fln', $this->fln );
       $this->Form->Hidden( 'display', $this->display );
       $this->Form->Hidden( 'start', $this->start );
       $kol=count( $id_del );
       //echo '<br>$kol='.$kol;
       $del=0;
       for( $i=0; $i<$kol; $i++ )
       {
        $u=$id_del[$i];
        /*
        $q = "SELECT * FROM `".$this->spr."` WHERE `cod`='".$u."' GROUP BY `cod`";
        $res = $this->Rights->Query( $q, $this->user_id, $this->module );
        echo '<br>$q='.$q.' $res='.$res.' $this->Rights->result='.$this->Rights->result;
        if( !$res OR !$this->Rights->result ) return false;
        $row = $this->Rights->db_FetchAssoc();            
        */
        if ($this->uselevels == 1)
        {
        //--- select sublevels of curent category ---
        $q="SELECT * FROM `".$this->spr."` WHERE `level`='".$u."' GROUP BY `cod`";
        $res_tmp = $this->Rights->Query( $q, $this->user_id, $this->module );
        //if ($res_tmp) 
        $rows_tmp = $this->Rights->db_GetNumRows();
        //else $rows_tmp = 0;
        //echo '<br>$q='.$q.' $res_tmp='.$res_tmp.' $this->Rights->result='.$this->Rights->result.' $rows_tmp='.$rows_tmp;
        $id_del_l=NULL;
        for( $i_ = 0; $i_ < $rows_tmp; $i_++ )
        {
          $row_tmp = $this->Rights->db_FetchAssoc();
          $id_del_l[$i_] = $row_tmp['cod'];
        }
        //echo '<br>$id_del_l=';print_r($id_del_l);
        //--- delete sublevels ---
        if( $rows_tmp>0 )$this->del( $id_del_l );
        }
        //delete image
        if ( !$this->DelImageByCod($u) ) return false;
        //delete current level 
        $q = "DELETE FROM `".$this->spr."` WHERE `cod`='".addslashes($u)."'";
        if( !empty($this->id_cat) ) $q = $q." AND `id_cat`='".$this->id_cat."'";
        if( !empty($this->id_param) ) $q = $q." AND `id_param`='".$this->id_param."'";        
        $res = $this->Rights->Query( $q, $this->user_id, $this->module );
        //echo '<br>$q='.$q.' $res='.$res.' $this->Rights->result='.$this->Rights->result;
        if( !$res OR !$this->Rights->result ) return false;
        
         $del=$del+1;
       }
       return $del;
   } //end of fuinction del
       
    // ================================================================================================
    // Function : up()
    // Version : 1.0.0
    // Date : 11.02.2005
    // Parms :
    // Returns :      true,false / Void
    // Description :  Up position
    // ================================================================================================
    // Programmer :  Andriy Lykhodid
    // Date : 11.02.2005
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function up($table, $level = 0)
    {
     $q = "SELECT * FROM `".$table."` WHERE `move`='".$this->move."'";
     if($this->uselevels==1) $q = $q." AND `level`='".$level."'";
     $q = $q." GROUP BY `cod`";
     $res = $this->Rights->Query( $q, $this->user_id, $this->module );
     //echo '<br>q='.$q.' res='.$res.' $this->Rights->result='.$this->Rights->result;
     if( !$res )return false;
     $rows = $this->Rights->db_GetNumRows();
     $row = $this->Rights->db_FetchAssoc();
     $move_down = $row['move'];
     $id_down = $row['cod'];

     $q="SELECT * FROM `".$table."` WHERE `move`<'".$this->move."'";
     if($this->uselevels==1) $q=$q." AND `level`='".$level."'";
     $q = $q." GROUP BY `cod` ORDER BY `move` desc";
     $res = $this->Rights->Query( $q, $this->user_id, $this->module );
     //echo '<br>q='.$q.' res='.$res.' $this->Rights->result='.$this->Rights->result;
     if( !$res )return false;
     $rows = $this->Rights->db_GetNumRows();
     $row = $this->Rights->db_FetchAssoc();
     $move_up = $row['move'];
     $id_up = $row['cod'];

     //echo '<br> $move_down='.$move_down.' $move_up ='.$move_up;
     if( $move_down!=0 AND $move_up!=0 )
     {
     $q="UPDATE `".$table."` SET `move`='".$move_down."' WHERE `cod`='".$id_up."'";
     $res = $this->Rights->Query( $q, $this->user_id, $this->module );
     //echo '<br>q='.$q.' res='.$res.' $this->Rights->result='.$this->Rights->result; 
     $q="UPDATE `".$table."` SET `move`='".$move_up."' WHERE `cod`='".$id_down."'";
     $res = $this->Rights->Query( $q, $this->user_id, $this->module );
     //echo '<br>q='.$q.' res='.$res.' $this->Rights->result='.$this->Rights->result; 
     }
    } // end of function up()

    // ================================================================================================
    // Function : down()
    // Version : 1.0.0
    // Date : 11.02.2005
    // Parms :
    // Returns :      true,false / Void
    // Description :  Down position
    // ================================================================================================
    // Programmer :  Andriy Lykhodid
    // Date : 11.02.2005
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function down($table, $level = 0)
    {
     $q="SELECT * FROM `".$table."` WHERE `move`='".$this->move."'";
     if($this->uselevels==1) $q = $q." AND `level`='".$level."'";
     $q = $q." GROUP BY `cod`";         
     $res = $this->Rights->Query( $q, $this->user_id, $this->module );
     //echo '<br>q='.$q.' res='.$res.' $this->Rights->result='.$this->Rights->result;
     if( !$res )return false;
     $rows = $this->Rights->db_GetNumRows();
     $row = $this->Rights->db_FetchAssoc();
     $move_up = $row['move'];
     $id_up = $row['cod'];
     $q="SELECT * FROM `".$table."` WHERE `move`>'".$this->move."'";
     if($this->uselevels==1) $q=$q." AND `level`='".$level."'";
     $q = $q." GROUP BY `cod` ORDER BY `move` asc";         
     $res = $this->Rights->Query( $q, $this->user_id, $this->module );
     //echo '<br>q='.$q.' res='.$res.' $this->Rights->result='.$this->Rights->result;
     if( !$res )return false;
     $rows = $this->Rights->db_GetNumRows();
     $row = $this->Rights->db_FetchAssoc();
     $move_down = $row['move'];
     $id_down = $row['cod'];

     if( $move_down!=0 AND $move_up!=0 )
     {
     $q="UPDATE `".$table."` SET `move`='".$move_down."' WHERE `cod`='".$id_up."'";
     $res = $this->Rights->Query( $q, $this->user_id, $this->module );
     //echo '<br>q='.$q.' res='.$res.' $this->Rights->result='.$this->Rights->result;

     $q="UPDATE `$table` SET `move`='".$move_up."' WHERE `cod`='".$id_down."'";
     $res = $this->Rights->Query( $q, $this->user_id, $this->module );
     //echo '<br>q='.$q.' res='.$res.' $this->Rights->result='.$this->Rights->result;
     }
    } // end of function down()         

   // ================================================================================================
   // Function : ShowNameArr
   // Version : 1.0.0
   // Date : 06.03.2005
   //
   // Parms : $id               / code of the description
   //         $description_arr  / array with desctiptions on different languages
   //         $table_for_show   / name of the table for save
   //         $fld_name         / name which will be show as description for this field
   // Returns : array $res[lang_id]=name - description on lang_id language.
   // Description : Show Description on different languages from the table $table_for_show
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 06.03.2005
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
    function ShowNameArr( $id, $description_arr, $table_for_show, $fld_name )
    {
      $Panel = new Panel();
      $ln_sys = &check_init('LangSys','SysLang'); 

      $Panel->WritePanelHead( "SubPanel_" );

      $ln_arr = $ln_sys->LangArray( _LANG_ID );
      while( $el = each( $ln_arr ) )
      {
         $lang_id = $el['key'];
         $lang = $el['value'];
         $mas_s[$lang_id] = $lang;

         $Panel->WriteItemHeader( $lang );
         echo "\n <table border=0 class='EditTable'>";
         echo "\n <tr>";
         echo "\n <td><b>$fld_name:</b>";
         echo "\n <td>";
         $row = $this->GetByCod( $table_for_show, $id, $lang_id );
         if( $description_arr ) $this->Form->TextBox( 'description['.$lang_id.']', $description_arr[$lang_id], 80 );
         else $this->Form->TextBox( 'description['.$lang_id.']', $row[$lang_id], 80 );
         echo "\n <td rowspan=3>";
         echo   "\n </table>";
         $Panel->WriteItemFooter();
      } //--- end while

      $Panel->WritePanelFooter();
      return true;
    } // end of function ShowNameArr()

   
   // ================================================================================================
   // Function : AutoInsertColumnMove
   // Version : 1.0.0
   // Date : 18.04.2006
   //
   // Parms :         $spr      / name of table, from which will be select data
   // Returns : true,false / Void
   // Description :  
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 18.04.2006
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function AutoInsertColumnMove( $spr )
   {
      $tmp_db = new DB();
      $q = "SELECT * FROM `".$spr."` WHERE 1 LIMIT 1";
      $res = $tmp_db->db_Query($q);
      //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
      if ( !$res ) return false;
      if ( !$tmp_db->result ) return false;
      $fields_col = mysql_num_fields($tmp_db->result);
      if ($fields_col==4) {
        $q = "ALTER TABLE `".$spr."` ADD `move` INT( 11 ) UNSIGNED NULL";
        $res = $tmp_db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
        if ( !$res ) return false;
        if ( !$tmp_db->result ) return false;    

        $q = "ALTER TABLE `".$spr."` ADD INDEX ( `move` )";
        $res = $tmp_db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
        if ( !$res ) return false;
        if ( !$tmp_db->result ) return false;

        $res = $this->AutoInsertDataIntoColumnMove( $spr );
        if ( !$res ) return false;
      }
      return true;
   } //end of function AutoInsertColumnMove()       

   // ================================================================================================
   // Function : AutoInsertColumnNode
   // Version : 1.0.0
   // Date : 18.04.2006
   //
   // Parms :         $spr      / name of table, from which will be select data
   // Returns : true,false / Void
   // Description :  
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 18.04.2006
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function AutoInsertColumnNode( $spr )
   {
      $tmp_db = new DB();
      $tmp_db2 = new DB();        
      $node = 0;
      $str = '0';
      while(1)
      {
      $q = "SELECT `cod` FROM `".$spr."` WHERE `level`=".$str;
      $res = $tmp_db->db_Query($q);
//      echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
      if ( !$res ) return false;
      if ( !$tmp_db->result ) return false; 
      $rows = $tmp_db->db_GetNumRows();
      if ($rows == 0) return true;
      $row = $tmp_db->db_FetchAssoc();
      $str = $row['cod'];
      for ($i=1;$i<$rows;$i++){
        $row = $tmp_db->db_FetchAssoc();
        $str .= ', '.$row['cod'];
      }
      $q = "UPDATE `".$spr."` SET 
             `node`='".$node."'
             WHERE `cod` in (".$str.")";
             $res = $tmp_db->db_Query($q);
//        echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
      if ( !$res ) return false;
      $node++;
      }
      return true;
   } //end of function AutoInsertColumnNode()       

   // ================================================================================================
   // Function : AutoInsertDataIntoColumnMove
   // Version : 1.0.0
   // Date : 18.04.2006
   //
   // Parms :         $spr      / name of table, from which will be select data
   // Returns : true,false / Void
   // Description :  
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 18.04.2006
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function AutoInsertDataIntoColumnMove( $spr )
   {
      $tmp_db = new DB();
      $tmp_db2 = new DB();        
      $q = "SELECT * FROM `".$spr."` WHERE 1 AND `lang_id`='"._LANG_ID."'";
      $res = $tmp_db->db_Query($q);
      //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
      if ( !$res ) return false;
      if ( !$tmp_db->result ) return false; 
      $rows = $tmp_db->db_GetNumRows();
      for ($i=0;$i<$rows;$i++){
        $row = $tmp_db->db_FetchAssoc();
        $q = "UPDATE `".$spr."` SET 
             `move`='".$row['cod']."'
             WHERE `cod`='".$row['cod']."'";
        $res = $tmp_db2->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
        if ( !$res ) return false;
        if ( !$tmp_db2->result ) return false;                
      } 
      return true;
   } //end of function AutoInsertDataIntoColumnMove()      
   
   // ================================================================================================
   // Function : CreateSpr
   // Version : 1.0.0
   // Date : 09.11.2006
   // Parms :   $spr      / name of table, where will be adding field `img`
   // Returns : true,false / Void
   // Description : create spr with name $this->spr id it is not exist. This is for Automaticly creation of spr
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 09.11.2006 
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function CreateSpr( $spr )
   {
      $tmp_db = new DB();
      if ( !$tmp_db->IsTableExist($spr) ) {
        $q = "CREATE TABLE `".$spr."` (
              `id` int(4) unsigned NOT NULL auto_increment";
        if(strstr($spr, 'spr_txt')) 
            $q = $q.",`cod` varchar(100) default NULL";
        else 
            $q = $q.",`cod` int(4) unsigned NOT NULL default '0'";
        $q = $q.",`lang_id` int(4) unsigned NOT NULL default '0'";
        
        /* закомментировал 14.12.2009 до выяснения обстоятельств. По-идее необходимо, что бы поле name по-умолчанию было varchar(255) 
        if(strstr($spr, 'spr_txt'))
            $q = $q.",`name` varchar(255) default NULL";
        else 
            $q = $q.",`name` text default NULL";
        */
        $q = $q.",`name` varchar(255) default NULL";
        
        $q = $q.",`move` int(11) unsigned default NULL";
        if($this->useimg==1) 
            $q = $q.",`img` varchar(255) default NULL";
        if($this->useshort==1) 
            $q = $q.",`short` varchar(255) default NULL";
        if($this->usecolors==1) 
            $q = $q.",`colorsBit` varchar(10) default 'ffffff'";
        if($this->usecodpli==1) 
            $q = $q.",`cod_pli` varchar(100) default NULL";
        if($this->uselevels==1) 
            $q = $q.",`level` int(11) unsigned default '0',`node` smallint(5) unsigned default '0'";
        $q = $q."
              ,PRIMARY KEY  (`id`)
              ,KEY `cod` (`cod`,`lang_id`)
              ,KEY `move` (`move`)";
        if($this->uselevels==1) 
              $q = $q.",KEY `level` (`level`)";
        $q = $q.")";            
        
        $res = $tmp_db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
        if ( !$res OR !$tmp_db->result ) return false;    
      }
      return true;
   } //end of function CreateSpr()       
   
   // ================================================================================================
   // Function : AutoInsertColumnImg
   // Version : 1.0.0
   // Date : 03.11.2006
   // Parms :   $spr      / name of table, where will be adding field `img`
   // Returns : true,false / Void
   // Description :  
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 03.11.2006
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function AutoInsertColumnImg( $spr )
   {
      $tmp_db = new DB();
      if ( !$this->IsFieldExist($spr, 'img') ) {
        $q = "ALTER TABLE `".$spr."` ADD `img` varchar(255)";
        $res = $tmp_db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
        if ( !$res ) return false;
        if ( !$tmp_db->result ) return false;    
      }
      return true;
   } //end of function AutoInsertColumnImg()
   
   // ================================================================================================
   // Function : AutoInsertColumnShortName
   // Version : 1.0.0
   // Date : 03.11.2006
   // Parms :   $spr      / name of table, where will be adding field `img`
   // Returns : true,false / Void
   // Description :  
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 03.11.2006
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function AutoInsertColumnShortName( $spr )
   {
      $tmp_db = new DB();
      if ( !$this->IsFieldExist($spr, 'short') ) {
        $q = "ALTER TABLE `".$spr."` ADD `short` varchar(255)";
        $res = $tmp_db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
        if ( !$res ) return false;
        if ( !$tmp_db->result ) return false;    
      }
      return true;
   } //end of function AutoInsertColumnShortName()
   
   // ================================================================================================
   // Function : DelItemImage
   // Version : 1.0.0
   // Date : 06.11.2006
   //
   // Parms :   $img   / name of the image
   // Returns : true,false / Void
   // Description :  Remove iamge from table and from the disk
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 06.11.2006   
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function DelItemImage($img, $lang_id=NULL)
   {       
       $q = "SELECT * FROM `".$this->spr."` WHERE `img`='".$img."' AND `lang_id`='".$lang_id."'";
       $res = $this->Rights->Query( $q, $this->user_id, $this->module );
       //echo '<br>$q='.$q.' $res='.$res.' $this->Rights->result='.$this->Rights->result;
       if( !$this->Rights->result ) return false;
       $rows = $this->Rights->db_GetNumRows();
       //if ($rows == 0) return false;
       $row = $this->Rights->db_FetchAssoc(); 
       //echo '<br>$row'; print_r($row);
       if( isset($row['lang_id'])) $lang_id = $row['lang_id'];
       $path = Spr_Img_Path.$this->spr.'/'.$lang_id;
       $path_file = $path.'/'.$img;
       //echo '<br>$path='.$path.'<br>$path_file='.$path_file;
       // delete file which store in the database
       if (file_exists($path_file)) {
          $res = unlink ($path_file);
          //if( !$res ) return false;
       }

       //echo '<br> $path='.$path;
       $handle = @opendir($path);
       //echo '<br> $handle='.$handle; 
       $cols_files = 0;
       while ( ($file = readdir($handle)) !==false ) {
           //echo '<br> $file='.$file;
           $mas_file=explode(".",$file);
           $mas_img_name=explode(".",$img);
           if ( strstr($mas_file[0], $mas_img_name[0].SPR_ADDITIONAL_FILES_TEXT) and $mas_file[1]==$mas_img_name[1] ) {
              $res = unlink ($path.'/'.$file);
              //if( !$res ) return false;                    
           }
           if ($file == "." || $file == ".." ) {
               $cols_files++;
           }
       }
       //if ($cols_files==2) rmdir($path);
       closedir($handle);           

       $q = "UPDATE `".$this->spr."` SET `img`=NULL WHERE `img`='".$img."' AND `lang_id`='".$lang_id."'";
       $res = $this->Rights->Query( $q, $this->user_id, $this->module );
       //echo '<br>$q='.$q.' $res='.$res.' $this->Rights->result='.$this->Rights->result;
       if( !$this->Rights->result ) return false;
       $this->img[$lang_id]='';
       return true;                        
  } //end of function DelItemImage() 
  
   // ================================================================================================
   // Function : DelImageByCod
   // Version : 1.0.0
   // Date : 07.11.2006
   //
   // Parms :   $cod   / cod of the item record
   // Returns : true,false / Void
   // Description :  Remove image by cod from table and from the disk
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 06.11.2006   
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function DelImageByCod($cod)
   {       
       $tmp_db = new DB();
       
       $q = "SELECT * FROM `".$this->spr."` WHERE `cod`='$cod'";
       $res = $tmp_db->db_Query( $q, $this->user_id, $this->module );
       //echo '<br>$q='.$q.' $res='.$res.' $this->Rights->result='.$tmp_db->result;
       if( !$tmp_db->result ) return false;
       $rows = $tmp_db->db_GetNumRows();
       //echo '<br>$rows='.$rows;
       for($i=0;$i<$rows;$i++){
         $row = $tmp_db->db_FetchAssoc();
         if ( !empty($row['img']) ) {
            if ( !$this->DelItemImage($row['img']) ) return false;
         }
       }
       return true;                        
  } //end of function DelImageByCod()                
   
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
   function ShowErrBackEnd()
   {
     if ($this->Err){
       echo '
        <table border=0 cellspacing=0 cellpadding=0 class="err" align="center">
         <tr><td align="left">'.$this->Err.'</td></tr>
        </table>';
     }
   } //end of fuinction ShowErrBackEnd()
   
   // ================================================================================================
   // Function : ShowPathToLevel()
   // Version : 1.0.0
   // Date : 22.05.2008
   //
   // Parms :        $id - id of the record in the table 
   // Returns :      $str / string with name of the categoties to current level of catalogue
   // Description :  Return as links path of the categories to selected level of catalogue
   // ================================================================================================
   // Programmer :  Igor Trokhymchuk
   // Date : 22.05.2008 
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function ShowPathToLevel( $spr, $level, $script, $lang_id=NULL, $str = NULL )
   {
     /*$db = DBs::getinstance();
     echo $this->node;
     echo $level;
     if( empty($lang_id) ) $lang_id = $this->lang_id;
     $q="SELECT * FROM ".$spr." WHERE `cod`='".$level."'";
     if( !empty($lang_id) ) $q = $q." AND `lang_id`='".$lang_id."'";
     $q = $q." GROUP BY `cod`";
     $res = $db->db_Query( $q );
     //echo '<br>q='.$q.' res='.$res.' $db->result='.$db->result;
     if( !$res )return false;
     $row = $db->db_FetchAssoc(); 
     
     $url = '/admin/modules/sys_spr/sys_spr.php?'.$this->script_ajax.'&task=show_sublevel&uselevels='.$this->uselevels.'&level='.$row['cod'].'&node='.$row['node'].'&srch=';
     if ( !empty($str) ) $str = '<a href="'.$script.'&level='.$level.'" onclick="GoToSubLevel('."'".$url."'".', '."'wndw0'".' ); return false;">'.strip_tags($row['name']).'</a> <span class="not_href">></span> '.$str;
     else $str = '<span style="color:#000000; font-size:8pt; font-weight:bold;">'.strip_tags($row['name']).'</span>'.$str;
     if ( $row['level']>0 ) {
         $this->ShowPathToLevel($spr, $row['level'], $script, $lang_id, $str );
         return true;
     }
     $url0 = '/admin/modules/sys_spr/sys_spr.php?'.$this->script_ajax.'&task=show_sublevel&uselevels='.$this->uselevels.'&level=0&srch=';
     if($level>0) $str = '<a href="'.$script.'&level=0" onclick="GoToSubLevel('."'".$url0."'".', '."'wndw0'".' ); return false;">'.$this->Msg_text['_TXT_ROOT_LEVEL'].'</a> <span class="not_href">></span> '.$str;
     echo $str;
     return true;*/
     if($level!=0)
     {
         $db = DBs::getinstance();
         if( empty($lang_id) ) $lang_id = $this->lang_id;
         $q="SELECT ";
         $q.='t0.name as name0, t0.level as level0, t0.node as node0, t0.cod as cod0 ';
         for($i=1; $i<=$this->node;$i++)
         {
             $q.=', t'.$i.'.name as name'.$i.', '.'t'.$i.'.level as level'.$i.', '.'t'.$i.'.node as node'.$i.', '.'t'.$i.'.cod as cod'.$i;
         }
         $q.= ' FROM '.$spr.' AS t0 ';
         for($i=1; $i<=$this->node;$i++)
         {
             $q.='LEFT JOIN '.$spr.' AS t'.$i.' ON t'.$i.'.level = t'.($i-1).'.cod ';
         }
         $q.=' WHERE 1 ';   
         if( !empty($lang_id) ) 
         for($i=0; $i<=$this->node;$i++)
         {
             //$q.='LEFT JOIN '.$spr.' AS t'.$i.' ON t'.$i.'.level = t'.($i-1).'.cod ';
             $q = $q.' AND t'.$i.'.lang_id="'.$lang_id.'"';
         }
        
         $q = $q." AND t".($i-1).".cod='".$level."' GROUP BY t0.cod ";
         $res = $db->db_Query( $q );
    //     echo '<br>q='.$q.' res='.$res.' $db->result='.$db->result;
         if( !$res )return false;
         $row = $db->db_FetchAssoc(); 
         $url0 = '/admin/modules/sys_spr/sys_spr.php?'.$this->script_ajax.'&task=show_sublevel&uselevels='.$this->uselevels.'&level=0&srch=';
         echo '<a href="'.$script.'&level=0" onclick="GoToSubLevel('."'".$url0."'".', '."'wndw0'".' ); return false;">'.$this->Msg_text['_TXT_ROOT_LEVEL'].'</a> <span class="not_href">></span>';
         for($i=0; $i<=$this->node;$i++)
         {
             $url='/admin/modules/sys_spr/sys_spr.php?'.$this->script_ajax.'&task=show_sublevel&uselevels='.$this->uselevels.'&level='.$row['cod'.$i].'&node='.$row['node'.$i].'&srch=';
             if($i!=$this->node)
                echo '<a href="'.$script.'&level='.$row['cod'.$i].'&node='.$row['node'.$i].'" onclick="GoToSubLevel('."'".$url."'".', '."'wndw0'".' ); return false;">'.strip_tags($row['name'.$i]).'</a> <span class="not_href">></span> ';
             else 
                echo '<b>'.strip_tags($row['name'.$i]).'</b>';
         }
     }
     return true;
   }//end of function ShowPathToLevel()                            


   // ================================================================================================
   // Function : AddNewTags
   // Version : 1.0.0
   // Date : 09.01.2005
   //
   // Parms :         $user_id  / user ID
   //                 $module   / Module read  / Void
   //                 $id       / id of editing record / Void
   //                 $mas      / mas with value from $_REQUEST
   //                 spr       / name of the table for this module
   // Returns : true,false / Void
   // Description : Show data from $spr table for editing
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 09.01.2005
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function AddNewTags()
   {

    $Panel = new Panel();
    $ln_sys = &check_init('LangSys','SysLang'); 
    //$scriptact = $_SERVER['PHP_SELF'].'?module='.$module.'&display='.$_REQUEST['display'].'&start='.$_REQUEST['start'].'&sort='.$_REQUEST['sort'];
    if( $this->id!=NULL )
    {
     $q="select * FROM `".$this->spr."` where id='".$this->id."'";
     // edit (U)
     $res = $this->Rights->Query($q, $this->user_id, $this->module);
     if( !$res ) return false;
     $mas = $this->Rights->db_FetchAssoc();
    }
    
    /* Write Form Header */
    $this->Form->WriteHeaderFormImg( $this->script );        
    
    $this->Form->Hidden( 'module_name', $this->module_name );
    $this->Form->Hidden( 'spr', $this->spr );
    $this->Form->Hidden( 'root_script', $this->root_script );
    $this->Form->Hidden( 'parent_script', $this->parent_script ); 
    $this->Form->Hidden( 'parent_id', $this->parent_id ); 
    $this->Form->Hidden( 'sort', $this->sort );
    $this->Form->Hidden( 'display', $this->display );
    $this->Form->Hidden( 'start', $this->start );

    $this->Form->Hidden( 'fln', $this->fln );
    $this->Form->Hidden( 'srch', $this->srch );
    
    $this->Form->Hidden( 'item_img', "" );  
    $this->Form->Hidden( 'lang_id', "" );
    $this->Form->Hidden( 'usemeta', $this->usemeta );

    if( $this->id!=NULL ) $txt = $this->Msg_text['TXT_EDIT'];
    else $txt = $this->Msg_text['_TXT_ADD_DATA'];

    AdminHTML::PanelSubH( $txt );
    
    //-------- Show Error text for validation fields --------------
    $this->Form->ShowErrBackEnd($this->Err);
    //-------------------------------------------------------------          
    
   /* Write Simple Panel*/
    AdminHTML::PanelSimpleH();
    
    $q="SELECT * FROM `".$this->spr."` ORDER BY `cod` desc LIMIT 1";
    $res = $this->Rights->Query($q, $this->user_id, $this->module);
    //echo '<br> $q='.$q.' $res='.$res.' $this->Rights->result='.$this->Rights->result;
    $tmp = $this->Rights->db_FetchAssoc();
    $this->field_type = mysql_field_type($this->Rights->result,1);
    
    ?>                   
    <table border="0" width="100%" align="center" class="EditTable">
     <?/* 
     <tr>
      <td><b><?echo $this->Msg_text['FLD_ID']?>:</b></td>
      <td width="95%"> <?
          if ( $this->id )
          {
             echo $mas['id'];
             $this->Form->Hidden( 'id', $mas['id'] );
          }
         ?>
      </td>
     </tr>
     <tr>
      <td><b><?echo $this->Msg_text['_FLD_CODE']?>:</b></td>
      <td><?
          if ( $this->id )
          {
                   $this->Form->TextBox( 'cod', $mas['cod'], 50 );
          }
          else
          {
               if ($this->field_type=='int') $new_cod = $tmp['cod']+1;
               else $new_cod='';
               $this->Form->TextBox( 'cod', $new_cod, 50 );
          }
      ?>
      </td>
     </tr>
     <?
     */
     if($this->uselevels==1){?>
     <tr>
      <td><b><?echo $this->Msg_text['_FLD_LEVEL']?>:</b></td>
      <td>
       <?
       $arr_levels = $this->GetStructureInArray($this->spr, 0, NULL, $this->Msg_text['_TXT_ROOT_LEVEL'], '&nbsp;');
       $this->Form->Select( $arr_levels, 'level_new', $this->level );
       ?>
      </td>
     </tr>
     <?}?>
     <tr>
      <td colspan="2">
    <?
    $Panel->WritePanelHead( "SubPanel_" );

    $ln_arr = $ln_sys->LangArray( _LANG_ID );
    if ( empty($ln_arr) )  $ln_arr[1]='';
    while( $el = each( $ln_arr ) )
    {
         $lang_id = $el['key'];
         $lang = $el['value'];
         $mas_s[$lang_id] = $lang;

         $Panel->WriteItemHeader( $lang );
            if ($this->id) $row = $this->GetRowByCODandLANGID( $mas['cod'], $lang_id ); 
            echo "\n <table border=0 class='EditTable'>";
            echo "\n <tr>";
            echo "\n <td><b>".$this->Msg_text['_FLD_NAME'].":</b>";
            echo "\n <td>";
            if( $this->id!=NULL ) $this->Err!=NULL ? $name=$this->name[$lang_id] : $name = $row['name'];
            else $name=$this->name[$lang_id];                
            //$this->Form->TextBox( 'name['.$lang_id.']', stripslashes($name), 80 );
            $this->Form->TextArea( 'name['.$lang_id.']', stripslashes($name), 10, 70 );

            
            echo "\n <tr>";
            echo "\n <td><b>".$this->Msg_text['_FLD_SHORT_NAME'].":</b>";
            echo "\n <td>";
            if( $this->id!=NULL ) $this->Err!=NULL ? $short=$this->short[$lang_id] : $short = $row['short'];
            else $short=$this->short[$lang_id];                
            $this->Form->TextBox( 'short['.$lang_id.']', stripslashes($short), 40 );


            echo "\n <tr>";
            echo "\n <td><b>".$this->Msg_text['_FLD_IMAGE'].":</b>";
            echo "\n <td>";
            if ( !isset($this->img[$lang_id]) ) $this->img[$lang_id]=NULL;
            if( $this->id!=NULL ) $this->Err!=NULL ? $img=$this->img[$lang_id] : $img = $row['img'];
            else $img=$this->img[$lang_id];                 
            if( !empty($img) ) {
                ?><table border=0 cellpadding=0 cellspacing=5>
                   <tr>
                    <td><?
                $this->Form->Hidden( 'img['.$lang_id.']', $img );
                //$this->Form->Hidden( 'item_img', NULL );
                ?><?
                echo $this->ShowImage($this->spr, $lang_id, $img, 'size_width=150', 85, NULL, NULL);
                    ?><td class='EditTable'><?
                echo '<br>'.$this->GetImgFullPath($img, $this->spr, $lang_id).'<br>';
                ?><a href="javascript:form_sys_spr.item_img.value='<?=$img;?>';form_sys_spr.submit();"><?=$this->Msg_text['_TXT_DELETE_IMG'];?></a><?
               ?></table><?
               echo '<b>'.$this->Msg_text['_TXT_REPLACE_IMG'].':</b>';
            }
            //else {                    
                ?>
                <INPUT TYPE="file" NAME="image[<?=$lang_id;?>]" " size="40" VALUE="<?=$img?>">                    
                <?
                //echo $mas['img'][$lang_id];
            //}
            echo   "\n </table>";
            
            if( $this->usemeta==1) {
                echo "\n<fieldset title='".$this->Msg_text['_TXT_META_DATA']."'> <legend><span style='vetical-align:middle; font-size:15px;'><img src='images/icons/meta.png' alt='".$this->Msg_text['_TXT_META_DATA']."' title='".$this->Msg_text['_TXT_META_DATA']."' border='0' /> ".$this->Msg_text['_TXT_META_DATA']."</span></legend>";
                echo "\n <table border=0 class='EditTable'>";
                echo "\n <tr>";
                echo "\n <td><b>".$this->Msg_text['FLD_PAGES_TITLE'].":</b>";
                echo "\n <br>";
                echo '<span class="help">'.$this->Msg_text['HELP_MSG_PAGE_TITLE'].'</span>';
                echo "\n <br>";
                if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->mtitle[$lang_id] : $val=$row['mtitle'];
                else $val=$this->mtitle[$lang_id];
                $this->Form->TextBox( 'mtitle['.$lang_id.']', stripslashes($val), 70 );
                echo "<hr width='70%' align='left' size='1'>";
            
                echo "\n <tr>";
                echo "\n <td><b>".$this->Msg_text['FLD_PAGES_DESCR'].":</b>";
                echo "\n <br>";
                echo '<span class="help">'.$this->Msg_text['HELP_MSG_PAGE_DESCRIPTION'].'</span>';
                echo "\n <br>";
                if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->mdescr[$lang_id] : $val=$row['mdescr'];
                else $val=$this->mdescr[$lang_id];
                $this->Form->TextArea( 'mdescr['.$lang_id.']', stripslashes($val), 3, 70 );
                echo "<hr width='70%' align='left' size='1'>";
            
                echo "\n <tr>\n <td><b>".$this->Msg_text['FLD_KEYWORDS'].":</b>\n <br />";
                echo '<span class="help">'.$this->Msg_text['_HELP_MSG_PAGE_KEYWORDS'].'</span>';
                echo "\n <br>";
                if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->mkeywords[$lang_id] : $val=$row['mkeywords'];
                else $val=$this->mkeywords[$lang_id];
                $this->Form->TextArea( 'mkeywords['.$lang_id.']', stripslashes($val),3, 70 );
                echo "\n </table>";
                echo "</fieldset><br>"; 
            }
                           
         $Panel->WriteItemFooter();
    }
    $Panel->WritePanelFooter();

    $tmp_db = new DB();
    $q = "SELECT * FROM `".$this->spr."` WHERE 1 LIMIT 1";
    $res = $tmp_db->db_Query($q);
    //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
    if ( !$res ) return false;
    if ( !$tmp_db->result ) return false;
    $fields_col = mysql_num_fields($tmp_db->result);
    if ($fields_col>4) {         
      if ($this->id==NULL) {
         $arr = NULL;
         $arr['']='';
         $tmp_db = new DB();
         $tmp_q = "select MAX(`move`) as maxx from `$this->spr` order by `move` desc";
         $res = $tmp_db->db_Query( $tmp_q );
         if( !$res )return false;
         $tmp_row = $tmp_db->db_FetchAssoc();
         $move = $tmp_row['maxx'];
         $move=$move+1;
         $this->Form->Hidden( 'move', $move );
      }
      else $move=$mas['move'];
      $this->Form->Hidden( 'move', $move );         
    }         
    echo '<TR><TD COLSPAN=2 ALIGN=left>';
    $this->Form->WriteSavePanel( $this->script );
    $this->Form->WriteCancelPanel( $this->script );
    echo '</table>';
    AdminHTML::PanelSimpleF();

    AdminHTML::PanelSubF();
    $this->Form->WriteFooter();
    return true;
   }  //end of fuinction AddNewTags
                      
 }  //end of class SysSpr
