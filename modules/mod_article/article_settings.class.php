<?php
// ================================================================================================
// System : SEOCMS
// Module : article_settings.class.php
// Version : 1.0.0
// Date : 23.05.2007
// Licensed To:
// Igor Trokhymchuk ihoru@mail.ru
// Andriy Lykhodid las_zt@mail.ru
//
// Purpose : Class definition for all actions with settings of Article
//
// ================================================================================================

include_once( SITE_PATH.'/modules/mod_article/article.defines.php' );

// ================================================================================================
//    Class             : Article_settings
//    Version           : 1.0.0
//    Date              : 21.03.2006
//
//    Constructor       : Yes
//    Parms             : session_id / session id
//                        usre_id    / UserID
//                        user_      /
//                        user_type  / id of group of user
//    Returns           : None                                               
//    Description       : Class definition for all actions with managment of Article
// ================================================================================================
//    Programmer        :  ALex Kerest
//    Date              :  23.05.2007
//    Reason for change :  Creation
//    Change Request Nbr:  N/A
// ================================================================================================
 class Article_settings extends Article {
    var $set_keywrd;
    var $set_descr;
    var $rss;

    // ================================================================================================
    //    Function          : Article_settings (Constructor)
    //    Version           : 1.0.0
    //    Date              : 23.05.2007
    //    Parms             : usre_id   / User ID
    //                        module    / module ID
    //                        sort      / field by whith data will be sorted
    //                        display   / count of records for show
    //                        start     / first records for show
    //                        width     / width of the table in with all data show
    //    Returns           : Error Indicator
    //
    //    Description       : Opens and selects a dabase
    // ================================================================================================
    function __construct($user_id=NULL, $module=NULL, $display=NULL, $sort=NULL, $start=NULL, $width = NULL) {
            //Check if Constants are overrulled
            ( $user_id  !="" ? $this->user_id = $user_id  : $this->user_id = NULL );
            ( $module   !="" ? $this->module  = $module   : $this->module  = NULL );
            ( $display  !="" ? $this->display = $display  : $this->display = 20   );
            ( $sort     !="" ? $this->sort    = $sort     : $this->sort    = NULL );
            ( $start    !="" ? $this->start   = $start    : $this->start   = 0    );
            ( $width    !="" ? $this->width   = $width    : $this->width   = 750  );

            if (empty($this->db)) $this->db = new DB();
            if (empty($this->Right)) $this->Right = new Rights($this->user_id, $this->module);
            if (empty($this->Msg)) $this->Msg = new ShowMsg();
            $this->Msg->SetShowTable(TblModArticleSprTxt);
            if (empty($this->Form)) $this->Form = new Form('form_mod_article');
            if (empty($this->Spr)) $this->Spr = new  SysSpr($this->user_id, $this->module);
    } // End of Article_settings Constructor

    // ================================================================================================
    // Function : ShowSettings()
    // Version : 1.0.0
    // Date : 27.03.2006
    // Parms :
    // Returns : true,false / Void
    // Description : show setting of Articleue
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 27.03.2006
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function ShowSettings()
    {  
     $Panel = new Panel();
     $ln_sys = new SysLang(); 

    $script = $_SERVER['PHP_SELF'].'?module='.$this->module;

     $q="select * from `".TblModArticleSet."` where 1";
     $res = $this->Right->Query( $q, $this->user_id, $this->module );
     if( !$this->Right->result ) return false;
     $row = $this->Right->db_FetchAssoc();
     $tmp_bd= new DB();
      $q1="select * from `".TblModArticleSetSprMeta."` where 1";
     $res = $tmp_bd->db_query( $q1);
     if( !$tmp_bd->result ) return false;
     $rows1 = $tmp_bd->db_GetNumRows();
     $meta= array();
     for($i=0; $i<$rows1;$i++)
     {
        $row1 = $tmp_bd->db_FetchAssoc();
        $meta[$row1['lang_id']]=$row1;
     }
//     print_r($meta);
    /* Write Form Header */
    $this->Form->WriteHeader( $script );
    AdminHTML::PanelSimpleH(); 

    if ($this->use_image==1)
    {
    ?>

    <TABLE BORDER=0 class="EditTable">
     <TR valign="top">
      <TD>         
       <?=AdminHTML::PanelSimpleH();?>
       <table border=0 cellspacing=1 cellpading=0 class="EditTable">
        <tr>
         <td><b><?=$this->Msg->show_text('TXT_IMG_PATH')?>:</b></td>
         <?$this->Err!=NULL ? $val=$this->img_path : $val=$row['img_path'];
           if ( trim($val)=='' ) $val = Img_Path;?>
         <td align="left" width="80%"><?echo SITE_PATH; echo $this->Form->TextBox( 'img_path', $val, 40 )?></td>
        </tr>
       </table>
       <?=AdminHTML::PanelSimpleF();?>
      </TD>
     </TR>
    </TABLE>        
<?}?>
    <TABLE BORDER=0 class="EditTable" cellpadding="0" cellspacing="1">
     <TR valign="top">
      <TD>
       <?=AdminHTML::PanelSimpleH();?>
       <table border=0 cellspacing=1 cellpading=0 width="200" class="EditTable">
        <tr>
         <td colspan=2><b><?=$this->Msg->show_text('TXT_USED_PROPS')?>:</b></td>
        </tr>
        <tr class=tr1>
         <td align="left"><?=$this->Msg->show_text('FLD_IMG2');?>
         <td><?$this->Form->CheckBox( "img", '', $row['img'] );?>
        </tr>
        <tr class=tr2>
         <td align="left"><?=$this->Msg->show_text('SET_KEYWORDS');?>
         <td><?$this->Form->CheckBox( "set_keywrd", '', $row['set_keywrd'] );?>
        </tr>
        <tr class=tr1>               
         <td align="left"><?=$this->Msg->show_text('SET_DESCR');?>
         <td><?$this->Form->CheckBox( "set_descr", '', $row['set_descr'] );?>
        </tr>
        <tr class=tr2>
         <td align="left">Mod_rewrite
         <td><?$this->Form->CheckBox( "rewrite", '', $row['rewrite'] );?>
        </tr>                                                           
        <tr class=tr1>               
         <td align="left"><?=$this->Msg->show_text('FLD_DATE');?>
         <td><?$this->Form->CheckBox( "dt", '', $row['dt'] );?>
        </tr>
        <tr class=tr2>
         <td align="left"><?=$this->Msg->show_text('TXT_RSS');?>
         <td><?$this->Form->CheckBox( "rss", '', $row['rss'] );?>
        </tr>               
       </table>
       <?=AdminHTML::PanelSimpleF();?>
      </TD>
      <TD></TD>                       
      <TD>
      <?=AdminHTML::PanelSimpleH();?>
       <table border=0 cellspacing=1 cellpading=0 width="200" class="EditTable">
        <tr>
         <td colspan=2><b><?=$this->Msg->show_text('TXT_META_DATA')?>:</b></td>
        </tr>
        <tr>
         <td>
          <?
            $Panel->WritePanelHead( "SubPanel_" );               
            $ln_arr = $ln_sys->LangArray( _LANG_ID );
            while( $el = each( $ln_arr ) )
            {
              $lang_id = $el['key'];
              $lang = $el['value'];
              $mas_s[$lang_id] = $lang;

              $Panel->WriteItemHeader( $lang );
              echo "\n <table border=0 class='EditTable'>";
              
              echo "\n<tr><td><b>".$this->Msg->show_text('FLD_TITLE').":</b></td>";
              echo "\n<td>";
              $name = $meta[$lang_id]['title'];
              $this->Err!=NULL ? $val=$this->title[$lang_id] : $val=$name;
              //else $val=$this->title[$lang_id];              
               $this->Form->TextBox( 'title['.$lang_id.']', stripslashes($val),58 );
              
              echo "\n<tr><td><b>".$this->Msg->show_text('SET_DESCR').":</b></td>"; 
              echo "\n<td>";                                 
              $name = $meta[$lang_id]['description'];
              $this->Err!=NULL ? $val=$this->description[$lang_id] : $val=$name;
              //else $val=$this->description[$lang_id];              
              $this->Form->TextArea( 'description['.$lang_id.']', stripslashes($val), 4, 50 );
              
              echo "\n<tr><td><b>".$this->Msg->show_text('SET_KEYWORDS').":</b></td>"; 
              echo "\n<td>";                                 
              $name = $meta[$lang_id]['keywords'];
              $this->Err!=NULL ? $val=$this->keywords[$lang_id] : $val=$name;
              //else $val=$this->keywords[$lang_id];              
              $this->Form->TextArea( 'keywords['.$lang_id.']', stripslashes($val), 4, 50 );   
              echo "\n</table>";
              $Panel->WriteItemFooter();                   
            }
            $Panel->WritePanelFooter();
            ?>             
         </td>
        </tr>            
       </table>          
       <?=AdminHTML::PanelSimpleF();?>
      </TD>
     </TR>
    </TABLE> 
    <?

    $this->Form->WriteSavePanel( $script );
    //$this->Form->WriteCancelPanel( $script );
    AdminHTML::PanelSimpleF();
    //AdminHTML::PanelSubF();

    $this->Form->WriteFooter();
    return true;            
    } //end of function ShowSettings()
      
    // ================================================================================================
    // Function : SaveSettings()
    // Version : 1.0.0
    // Date : 27.03.2006
    // Parms :
    // Returns : true,false / Void
    // Description : show setting of Articleue
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 27.03.2006
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function SaveSettings()
    {      
    $q="select * from `".TblModArticleSet."` where 1";
    $res = $this->Right->Query( $q, $this->user_id, $this->module );
    if( !$this->Right->result ) return false;
    $rows = $this->Right->db_GetNumRows();

    $uploaddir =SITE_PATH.$this->img_path;
    if ( !file_exists ($uploaddir) ) mkdir($uploaddir,0777); 
    //else chmod($uploaddir,0777);
    chmod($uploaddir,0755);

    if($rows>0)
    {
      $q="update `".TblModArticleSet."` set
          `img`='$this->img',
          `set_keywrd`='$this->set_keywrd',
          `set_descr`='$this->set_descr',
          `rewrite`='$this->rewrite',
          `dt`='$this->dt',
          `img_path`='$this->img_path',
          `rss`='$this->rss'";
      $res = $this->Right->Query( $q, $this->user_id, $this->module );
      //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result; 
      if( !$res ) return false; 
      if( !$this->Right->result ) return false;
    }
    else
    {
      $q="select * from `".TblModArticleSet."` where 1";
      $res = $this->Right->Query( $q, $this->user_id, $this->module );
      $rows = $this->Right->db_GetNumRows();
      //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result; 
      if($rows>0) return false;

      $q="insert into `".TblModArticleSet."` values('$this->img', '$this->set_keywrd', '$this->set_descr', '$this->rewrite', '$this->dt', '$this->img_path', '$this->rss')";
      $res = $this->Right->Query( $q, $this->user_id, $this->module );
      //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result; 
      if( !$this->Right->result) return false;
    }
////////////////////////////////////////////////////////////    
    $q="select * from `".TblModArticleSetSprMeta."` where 1";
    $res = $this->Right->Query( $q, $this->user_id, $this->module );
    if( !$this->Right->result ) return false;
    $rows = $this->Right->db_GetNumRows();
    $meta= array();
     for($i=0; $i<$rows;$i++)
     {
        $row = $this->Right->db_FetchAssoc();
        $meta[$row['lang_id']]='1';
     }
     $ln_sys = new SysLang();
            $ln_arr = $ln_sys->LangArray( _LANG_ID );
            while( $el = each( $ln_arr ) )
            {
              $lang_id = $el['key'];
              if (isset($meta[$lang_id]))
              {
                  
                 $q="update `".TblModArticleSetSprMeta."` set
                  `title`='".$this->title[$lang_id]."',
                  `description`='".$this->description[$lang_id]."',
                  `keywords`='".$this->keywords[$lang_id]."'
                  WHERE `lang_id`='$lang_id'";
                  $res = $this->Right->Query( $q, $this->user_id, $this->module );
//                  echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result; 
                  if( !$res ) return false; 
                  if( !$this->Right->result ) return false; 
              }
        else
        {
          $q="insert into `".TblModArticleSetSprMeta."`  set
                  `title`='".$this->title[$lang_id]."',
                  `description`='".$this->description[$lang_id]."',
                  `keywords`='".$this->keywords[$lang_id]."',
                  `lang_id`='$lang_id'";
          $res = $this->Right->Query( $q, $this->user_id, $this->module );
//          echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result; 
          if( !$this->Right->result) return false;
          }
            }
    //---- Save fields on different languages ----
                    
    return true;             
    } // end of function SaveSettings()
 } //end of class Article_settings
