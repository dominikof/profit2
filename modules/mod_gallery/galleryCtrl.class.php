<?
// ================================================================================================
//    System     : SEOCMS
//    Module     : Gallery
//    Date       : 01.07.2010
//    Licensed To: Yaroslav Gyryn
//    Purpose    : Class definition for Gallery - module
// ================================================================================================

 include_once( SITE_PATH.'/modules/mod_gallery/gallery.defines.php' );

// ================================================================================================
//    Class             : GalleryCtrl
//    Date              : 01.07.2010
//    Constructor       : Yes
//    Parms             :
//    Returns           : None
//    Description       : Gallery Module
//    Programmer        : Yaroslav Gyryn
// ================================================================================================

class GalleryCtrl extends Gallery{
    
    var $is_tags = NULL;
    var $UploadImages = NULL;
    
    /**
    * Class Constructor
    * Set the variabels
    * @return true/false
    * @author Yaroslav Gyryn
    * @version 1.0, 08.10.2011
    */
    function __construct($user_id = NULL, $module = NULL)
    {
        $this->user_id = $user_id;
        $this->module = $module;
        
        $this->db = DBs::getInstance(); 
        $this->Right =  &check_init("RightsFoto", "Rights", "'".$this->user_id."','".$this->module."'");
        $this->Form = &check_init('FormGallery', 'Form', "'mod_gallery'");        
        $this->ln_sys = &check_init('SysLang', 'SysLang'); 
        $this->ln_arr = $this->ln_sys->LangArray( $this->lang_id ); 
        if (empty($this->Msg)) $this->Msg = &check_init('ShowMsg', 'ShowMsg');
        if (empty($this->Spr)) $this->Spr = &check_init('SysSpr', 'SysSpr');
        $this->settings = $this->GetSettings();
        
        $this->UploadImages = new UploadImage(149, null, $this->settings['img_path'],'mod_gallery_img',NULL,NULL,$this->ln_arr, 800, 1024);
        //$this->UploadImages->CreateTables();
        
        $this->multi = &check_init_txt('TblBackMulti',TblBackMulti);
        $this->is_tags=0;
        ( defined("USE_TAGS")                  ? $this->is_tags = USE_TAGS                     : $this->is_tags=0 ); // использовать тэги
        if(defined("_LANG_ID")) $this->lang_id = _LANG_ID;
    }

       
    // ================================================================================================
    // Function : show()
    // Date : 01.07.2010
    // Parms :
    // Returns :     true,false / Void
    // Description : Show Gallery
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function show()
    {
     $db = DBs::getInstance(); 
     $frm = new Form('fltr');
     $script = 'module='.$this->module.'&display='.$this->display.'&start='.$this->start.'&sort='.$this->sort.'&fltr='.$this->fltr.'&fln='.$this->fln;
     $script = $_SERVER['PHP_SELF']."?$script";

     if( !$this->sort ) $this->sort = 'position';
     if( strstr( $this->sort, 'position' ) ) $this->sort = $this->sort.' desc';

      $q = "SELECT `".TblModGallery."`.*, `".TblModGalleryCat."`.name AS `cat_name`, `".TblModGalleryTxt."`.name, `".TblModGalleryTxt."`.short AS `short_descr`, `".TblModGalleryTxt."`.full AS `full_descr` 
              FROM `".TblModGallery."`, `".TblModGalleryCat."`, `".TblModGalleryTxt."`
              WHERE `".TblModGallery."`.category=`".TblModGalleryCat."`.cod
              AND `".TblModGalleryCat."`.lang_id='".$this->lang_id."'
              AND `".TblModGallery."`.id=`".TblModGalleryTxt."`.cod
              AND `".TblModGalleryTxt."`.lang_id='".$this->lang_id."'
             ";
     if( $this->fltr ) $q = $q." AND `".TblModGallery."`.".$this->fltr;
     $q = $q." ORDER BY `".TblModGallery."`.".$this->sort;

     $res = $this->Right->Query( $q, $this->user_id, $this->module );
     //echo '<br>'.$q.'<br/> $res='.$res;
     if( !$res )return false;
     $rows = $this->Right->db_GetNumRows();
     $a = $rows;
     $j = 0;
     $row_arr = NULL;
     for( $i = 0; $i < $rows; $i++ )
     {
       $row = $this->Right->db_FetchAssoc();
       if( $i >= $this->start && $i < ( $this->start+$this->display ) )
       {
         $row_arr[$j] = $row;
         $j = $j + 1;
       }
     }

     /* Write Form Header */
     $this->Form->WriteHeader( $script );

     /* Write Table Part */
     AdminHTML::TablePartH();

     /* Write Links on Pages */
     echo '<TR><TD COLSPAN=10>';
     $script1 = 'module='.$this->module.'&fltr='.$this->fltr;
     $script1 = $_SERVER['PHP_SELF']."?$script1";
     $this->Form->WriteLinkPages( $script1, $rows, $this->display, $this->start, $this->sort );

     echo '<TR><TD COLSPAN=5>';
     $this->Form->WriteTopPanel( $script );
     
     echo '<td colspan=3 CLASS="TR1">'.$this->multi['_TXT_FILTR'].": "; 
     $arr = NULL;
     $arr[''] = 'All';
     
     $q = "SELECT `cod`, `name` 
           FROM `".TblModGalleryCat."` 
           WHERE `lang_id`='".$this->lang_id."'
          ";
     $res = $db->db_Query( $q );
     //echo '<br>$q='.$q.' $res='.$res;
     $rows1 = $db->db_GetNumRows();
     for( $i = 0; $i < $rows1; $i++ )
     {
        $row1 = $db->db_FetchAssoc();
        $arr['category='.$row1['cod']] = stripslashes($row1['name']);
     }
     $this->Form->SelectAct( $arr, 'category', $this->fltr, "onChange=\"location='".$script."'+'&fltr='+this.value\"" );  

     echo '<td><td><td colspan=2>';
     $script2 = 'module='.$this->module.'&display='.$this->display.'&start='.$this->start.'&task=show&fltr='.$this->fltr;
     $script2 = $_SERVER['PHP_SELF']."?$script2";

    if($rows1>$this->display) $ch = $this->display;
    else $ch = $rows;
     
    ?>
     <tr>
     <td class="THead"><input value="0" id="cAll" onclick="if (this.value == '1') {unCheckAll(<?=$ch;?>); this.value = '0';} else {checkAll(<?=$ch;?>); this.value = '1';}" type="checkbox"></Th>
     <td class="THead"><A HREF=<?=$script2?>&sort=id><?=$this->multi['_FLD_ID'];?></A></Th>
     <td class="THead"><?=$this->multi['_FLD_NAME'];?></Th>
     <td class="THead"><?=$this->multi['_FLD_CATEGORY'];?></Th>
     <td class="THead"><?=$this->multi['_FLD_DATE'];?></Th>
     <td class="THead"><?=$this->multi['_FLD_STATUS'];?></Th>
     <td class="THead"><?=$this->multi['_FLD_SHORT'];/*?></Th>
     <td class="THead"><?=$this->multi['_FLD_TEXT'];*/?></Th>
     <td class="THead"><?=$this->multi['_FLD_IMG'];?></Th>
     <td class="THead"><?=$this->multi['FLD_DISPLAY'];?></Th>
     <?
     $up = 0;
     $down = 0;
     $id = 0;
     $style1 = 'TR1';
     $style2 = 'TR2';
     $n = count( $row_arr );
     for( $i = 0; $i <$n; $i++ )
     {
       $row = $row_arr[$i];

       if ( (float)$i/2 == round( $i/2 ) )
       {
        echo '<TR CLASS="'.$style1.'">';
       }
       else echo '<TR CLASS="'.$style2.'">';

       echo '<TD>';
       $this->Form->CheckBox( "id_del[]", $row['id'], null, "check".$i );

       echo '<TD>';
       $this->Form->Link( $script."&task=edit&id=".$row['id'], stripslashes( $row['id'] ), $this->multi['_TXT_EDIT_DATA'] );

       /* Name */
       echo '<TD align=center>';
       $this->Form->Link( $script."&task=edit&id=".$row['id'], stripslashes($row['name']), $this->multi['_TXT_EDIT_DATA'] );

       /* Category */
       echo '<TD align=center>';
       $this->Form->Link( $script."&task=show&fltr=category=".$row['category'], stripslashes($row['cat_name']), $this->multi['_TXT_FILTR'] );

       /* Date Time */
       echo '<TD align=center>'.$row['dttm'];

       echo '<TD align=center>';
       if( $row['status'] =='i') echo $this->multi['_FLD_INACTIVE'];
       //if( $row['status'] =='e') echo $this->multi['_FLD_EXPIRED'];
       if( $row['status'] =='a') echo $this->multi['FLD_ACTIVE'];

       echo '<TD align=center>';
       $short = trim( stripslashes($row['short_descr']) );
       if( $short ) $this->Form->ButtonCheck();

       /*echo '<TD align=center>';
       $full = trim( stripslashes($row['full_descr']) );
       if( $full ) $this->Form->ButtonCheck();*/

       echo '<TD align=center>';
          $this->UploadImages->GetImagesCount($row['id']);
         /*$img = $this->GetMainImage($row['id'], 'back');
              if ( !empty($img)) {
                ?><a href="<?=$script?>&task=showimages&id=<?=$row['id'];?>"><?=$this->ShowImage( $img, $row['id'], 'size_width=75', 100, NULL, "border=0");?>
                <br><?=$this->multi['TXT_ADD_EDIT_IMAGES'].'['.$this->GetImagesCount($row['id']).']';?></a><?
              }
              else {?><a href="<?=$script?>&task=showimages&id=<?=$row['id'];?>"><?=$this->multi['TXT_ADD_EDIT_IMAGES'].'['.$this->GetImagesCount($row['id']).']';?></a><?}*/

       echo '<TD align=center>';
       if( $up != 0 )
       {
       ?>
        <a href=<?=$script?>&task=up&move=<?=$row['position']?>>
        <?=$this->Form->ButtonUp( $row['id'] );?>
        </a>
       <?
       }

       if( $i != ( $rows - 1 ) )
       {
       ?>
         <a href=<?=$script?>&task=down&move=<?=$row['position']?>>
         <?=$this->Form->ButtonDown( $row['id'] );?>
         </a>
       <?
       }
       $up = $row['id'];
       $a = $a - 1;
     } //-- end for

     AdminHTML::TablePartF();
     $this->Form->WriteFooter();
     return true;
    }


    // ================================================================================================
    // Function : edit()
    // Date : 01.07.2010
    // Parms :
    // Returns : true,false / Void
    // Description : edit/add records in Gallery module
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function edit()
    {
     $Panel = new Panel();
     $calendar = new DHTML_Calendar(false, 'en', 'calendar-win2k-2', false);
     $calendar->load_files();
     
     $script = 'module='.$this->module.'&display='.$this->display.'&start='.$this->start.'&sort='.$this->sort.'&fltr='.$this->fltr;
     $script = $_SERVER['PHP_SELF']."?$script";

     if( $this->id!=NULL )
     {
       $q = "SELECT `".TblModGallery."`.*
             FROM `".TblModGallery."`
             WHERE `".TblModGallery."`.`id`='".$this->id."'
            ";
       $res = $this->Right->Query( $q, $this->user_id, $this->module );
       if( !$res ) return false;
       $mas = $this->Right->db_FetchAssoc();
     }

     /* Write Form Header */
     $this->Form->WriteHeaderFormImg( $script );
     $this->Form->textarea_editor = 'FCK'; //'tinyMCE';
     $this->Form->IncludeSpecialTextArea(); 
     
     if( $this->id!=NULL ) $txt = $this->multi['_TXT_EDIT_DATA'];
     else $txt = $this->multi['_TXT_ADD_DATA'];
     
     AdminHTML::PanelSubH( $txt );
     //-------- Show Error text for validation fields --------------
     $this->ShowErrBackEnd();
     //------------------------------------------------------------
     AdminHTML::PanelSimpleH();
    ?>
    <script type="text/javascript">
        function EditTranslit(div_id, idbtn){
            Did = "#"+div_id;
            idbtn = "#"+idbtn;
            if( !window.confirm('<?=$this->Msg->get_msg('MSG_DO_YOU_WANT_TO_EDIT_TRANSLIT');?>')) return false;
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
    <table class="EditTable"><tr><td valign=top align=left>
    <TABLE BORDER=0 class="EditTable" width="100">
     <TR><TD><?=$this->multi['_FLD_ID'];?>:&nbsp;
    <?
       if( $this->id!=NULL )
       {
        echo $mas['id'];
        $this->Form->Hidden( 'id', $mas['id'] );
       }
       else $this->Form->Hidden( 'id', '' );
    ?>
    <TR><TD><?=$this->multi['FLD_DISPLAY'];?>:&nbsp;
    <?
    if( $this->id!=NULL )
    {
      echo $mas["position"];
      echo '<input type="hidden" name=position VALUE="'.$mas["position"].'">';
    }
    else
    {
          $q="select MAX(`position`) from `".TblModGallery."`";
          $res = mysql_query( $q );
          $tmp = mysql_fetch_array( $res );
          $maxx = $tmp['MAX(`position`)'] + 1;
          echo $maxx;
          echo '<INPUT TYPE=hidden NAME=position VALUE="'.$maxx.'">';
    }
    ?>
    </table>
    
    <td width=100%>
    <table class="EditTable">
     <TR>
      <TD><b><?=$this->multi['_FLD_CATEGORY'];?>:</b>
      <TD>
       <?
       if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->category : $val=$mas['category'];
       else $this->Err!=NULL ? $val=$this->category : $val='';
       $catId = $val;
       $this->Spr->ShowInComboBox( TblModGalleryCat, 'category', stripslashes($val), 40, $this->multi['TXT_SELECT_CATEGORY'] );
       ?>
      </td>
     </tr> 
     <TR>
      <TD><b><?=$this->multi['_FLD_STATUS'];?>:</b>
      <TD>
       <?
       $arr = NULL;
       $arr['a'] = $this->multi['FLD_ACTIVE'];
       $arr['i'] = $this->multi['_FLD_INACTIVE'];
       
       if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->status : $val=$mas['status'];
       else $this->Err!=NULL ? $val=$this->status : $val='a';
       $this->Form->Select( $arr, 'status', $val, NULL );

     echo '<tr><td><b>'.$this->multi['_FLD_DATE'].':</b>';
     echo '<td>';
     if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->dttm : $val=$mas['dttm'];
     else $this->Err!=NULL ? $val=$this->dttm : $val = strftime('%Y-%m-%d %H:%M', strtotime('now'));

     $a1 = array('firstDay'       => 1, // show Monday first
                 'showsTime'      => true,
                 'showOthers'     => true,
                 'ifFormat'       => '%Y-%m-%d %H:%M',
                 'timeFormat'     => '12');
      $a2 = array('style'       => 'width: 15em; color: #840; background-color: #ff8; border: 1px solid #000; text-align: center',
                  'name'        => 'dttm',
                  'value'       => $val );
     $calendar->make_input_field( $a1, $a2 );  
    ?>
     </table>
    </table>
    <?
     if ( $this->is_tags==1 ) {  
         //echo '$this->module ='.$this->module;
        $Tags = new SystemTags($this->user_id, $this->module);
        if( $this->id!=NULL ) $this->Err!=NULL ? $id_tag=$this->id_tag : $id_tag=$Tags->GetTagsByModuleAndItem($this->module, $this->id);
        else $id_tag=$this->id_tag;
        //echo '<br>$id_tag='.$id_tag; print_r($id_tag);
        ?><div><?$Tags->ShowEditTags($id_tag);?><br /></div><?        
     } 
     
    $Panel->WritePanelHead( "SubPanel_" );
    $tmp_bd= new DB();
    $q1="select * from `".TblModGalleryTxt."` where 1";
    if( $this->id!=NULL )
        $q1 = $q1." AND `".TblModGalleryTxt."`.`cod`='".$this->id."'";
        
     $res = $tmp_bd->db_query( $q1);
     if( !$tmp_bd->result ) return false;
     $rows1 = $tmp_bd->db_GetNumRows();
     $txt= array();
     for($i=0; $i<$rows1;$i++)
     {
        $row1 = $tmp_bd->db_FetchAssoc();
        $txt[$row1['lang_id']]=$row1;
     }
        
     while( $el = each( $this->ln_arr ) )
     {
         $lang_id = $el['key'];
         $lang = $el['value'];
         $mas_s[$lang_id] = $lang;

         $Panel->WriteItemHeader( $lang );
         echo "\n <table border=0 class='EditTable' width='100%'>";
         echo "\n <tr>";
         echo "\n <td><b>".$this->multi['FLD_PAGE_URL'].":</b>";
         echo "\n <br>";
         ?><span style="font-size:10px;">../<?=$this->Spr->GetTranslitByCod(TblModGalleryCat, $catId, $lang_id);?>/<?
         if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->translit[$lang_id] : $val=$this->GetTranslitById( $this->id, $lang_id );
         else $val=$this->translit[$lang_id];
         if( $this->id ){
             $params = 'disabled';
             $this->Form->Hidden( 'translit['.$lang_id.']', stripslashes($val) );
         }
         else{
             $params="onkeyup=\"CheckTranslitField('translit".$lang_id."','tbltranslit".$lang_id."');\"";
         }
         $this->Form->TextBox( 'translit['.$lang_id.']', stripslashes($val), 50, 'id="translit'.$lang_id.'"; style="font-size:10px; "'.$params );?>.html</span><?
         $this->Form->Hidden( 'translit_old['.$lang_id.']', stripslashes($val) );
         if( $this->id ){?>&nbsp;<?$this->Form->ButtonSimple("btn", $this->multi['_TXT_EDIT_DATA'], NULL, "id='button".$lang_id."' onClick=\"EditTranslit('translit".$lang_id."','button".$lang_id."');\"");}
         ?><br><?           
        
         echo "\n <tr>";
         echo "\n <td><b>".$this->multi['_FLD_SUBJECT'].":</b>";
         echo "\n <br>";
         $row = NULL;
         if (isset($txt[$lang_id]['name']))
            $row = $txt[$lang_id]['name'];
         else $row='';    
         if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->name[$lang_id] : $val=$row;
         else $this->Err!=NULL ? $val=$this->name[$lang_id] : $val = '';
         $this->Form->TextBox( 'name['.$lang_id.']', stripslashes($val), 110 );
         echo "\n <br>";
         echo "\n <tr>";
         echo "\n <td><b>".$this->multi['_FLD_TITLE'].":</b>";
         echo "\n <br>";
         if (isset($txt[$lang_id]['title']))
            $row = $txt[$lang_id]['title'];
         else $row='';    
         if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->title[$lang_id] : $val=$row;
         else $this->Err!=NULL ? $val=$this->title[$lang_id] : $val = '';
         $this->Form->TextBox( 'title['.$lang_id.']', stripslashes($val), 110 );
         echo "\n <br>";
       
         if(isset($this->settings['set_keywrd']) AND $this->settings['set_keywrd']=='1' ) {  
            echo "\n <tr><td><b>".$this->multi['FLD_KEYWORDS'].":</b>";
            echo "\n <br>";
              if (isset($txt[$lang_id]['keywords']))
                    $row = $txt[$lang_id]['keywords'];
              else $row='';
            if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->keywords[$lang_id] : $val=$row;
            else $this->Err!=NULL ? $val=$this->keywords[$lang_id] : $val = '';
            $this->Form->TextArea( 'keywords['.$lang_id.']',  stripslashes($val), 3, 110 );
            echo "\n <br>";
         }
        
         if(isset($this->settings['set_descr']) AND $this->settings['set_descr']=='1' ) {
            echo "\n <tr><td><b>".$this->multi['FLD_DECRIPTION'].":</b>";
            echo "\n <br>";
            if (isset($txt[$lang_id]['description']))
                    $row = $txt[$lang_id]['description'];
              else $row='';
            if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->description[$lang_id] : $val=$row;
            else $this->Err!=NULL ? $val=$this->description[$lang_id] : $val = '';
            $this->Form->TextArea( 'description['.$lang_id.']',  stripslashes($val), 3, 110);
         }
        
         echo "\n <br>";
         echo "\n <tr>";
         echo "\n <td><b>".$this->multi['_FLD_SHORT'].":</b>";
         echo "\n <br>";
         if (isset($txt[$lang_id]['short']))
                    $row = $txt[$lang_id]['short'];
              else $row='';
         if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->short[$lang_id] : $val=$row;
         else $this->Err!=NULL ? $val=$this->short[$lang_id] : $val = '';
         $this->Form->SpecialTextArea(NULL, 'short['.$lang_id.']', stripslashes($val), 12, 70, NULL, $lang_id );
         echo "\n <br>";

    /*     echo "\n <tr>";
         echo "\n <td><b>".$this->multi['_FLD_TEXT'].":</b>";
         echo "\n <br>";
        
         if (isset($txt[$lang_id]['full']))
                    $row = $txt[$lang_id]['full'];
              else $row='';
         if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->full[$lang_id] : $val=$row;
         else $this->Err!=NULL ? $val=$this->full[$lang_id] : $val = '';
         $this->Form->SpecialTextArea(NULL, 'full['.$lang_id.']', stripslashes($val), 25, 70, NULL, $lang_id );
         echo "\n <br>"; */
         echo   "\n </table>";
         $Panel->WriteItemFooter();
     }

     //-------------------- Upload Files Start --------------------- 
      $this->UploadImages->ShowFormToUpload(NULL,$this->id);
      //-------------------- Upload Files End --------------------- 
          
     $Panel->WritePanelFooter();
     $this->Form->WriteSavePanel( $script );
     $this->Form->WriteCancelPanel( $script );
     //$this->Form->WritePreviewPanel( 'http://'.NAME_SERVER."/modules/mod_gallery/gallery.preview.php" );

     $this->Form->WriteFooter();
     AdminHTML::PanelSimpleF();
     AdminHTML::PanelSubF();
    }

   // ================================================================================================
   // Function : CheckFields()
   // Version : 1.0.0
   // Date : 01.07.2010
   // Parms :
   // Returns :      true,false / Void
   // Description :  Checking all fields for filling and validation
   // Programmer :  Yaroslav Gyryn
   // ================================================================================================
   function CheckFields()
   {
        $this->Err=NULL;
        if (empty( $this->category )) 
            $this->Err=$this->Err.$this->multi['MSG_FLD_CATEGORY_EMPTY'].'<br>';
               
        $i=0;
        while( $el = each( $this->ln_arr ) ){
            $lang_id = $el['key'];
            if( !empty( $this->name[$lang_id] ) ) 
                continue;
            else $i++;
        }
        if($i==count($this->name)) 
            $this->Err=$this->Err.$this->multi['MSG_FLD_NAME_EMPTY'].'<br>'; 

        //echo '<br>$this->Err='.$this->Err.' $this->Msg->table='.$this->Msg->table;
        return $this->Err;
   } //end of fuinction CheckFields() 

    // ================================================================================================
    // Function : save()
    // Description : Store Gallery ( Save )
    // Parms :
    // Returns : true,false / Void
    // Programmer : 
    // Date : 01.07.2010
    // ================================================================================================
    function save()
    {
        //print_r($_REQUEST);
        if( empty( $this->category ) )
        {
         $this->multi['MSG_FLD_CATEGORY_EMPTY'];
         $this->edit( $this->id, $_REQUEST );
         return false;
        }

       if( $this->id )
       {
         $q = "SELECT * FROM ".TblModGallery." WHERE `id`='$this->id'";
         $res = $this->Right->Query( $q, $this->user_id, $this->module );
         if( !$res ) return false;

         $rows = $this->Right->db_GetNumRows();
       }else $rows = 0;
       
       if( $rows > 0 )   //--- update
       {
          $q = "update `".TblModGallery."` set
               `category`='$this->category',
               `status`='$this->status',
               `dttm`='$this->dttm',
               `img`='$this->img',
               `position`='$this->position'
                where id='$this->id'";
         $res = $this->Right->Query( $q, $this->user_id, $this->module );
         if( !$res ) return false;
       }
       else          //--- insert
       {
         $q = "insert into `".TblModGallery."` values(NULL,'$this->dttm','$this->category','$this->status','$this->img','$this->position')";
         $res = $this->Right->Query( $q, $this->user_id, $this->module );
         if( !$res ) return false;

         $this->id = $this->Right->db_GetInsertID();
       }
      //---- save tags ----
      if ( $this->is_tags==1 ) {
           $Tags = new SystemTags();
           $res=$Tags->SaveTagsById( $this->module, $this->id, $this->id_tag );
           if( !$res ) return false;
      }  
    
    $q="select * from `".TblModGalleryTxt."` where `cod`='$this->id'";
    $res = $this->Right->Query( $q, $this->user_id, $this->module );
    if( !$this->Right->result ) return false;
    $rows = $this->Right->db_GetNumRows();
    $_txst= array();
    
     for($i=0; $i<$rows;$i++)
     {
        $row = $this->Right->db_FetchAssoc();
        $_txst[$row['lang_id']]='1';
     }
     $count = count($this->ln_arr);
     $el = array_keys($this->ln_arr);
     for( $i=0; $i < $count; $i++ )
     {
       $lang_id = $el[$i]; 
       $name_ = addslashes( strip_tags(trim($this->name[ $lang_id])) );
       $titles = addslashes( strip_tags(trim($this->title[ $lang_id ])) );
       $keywords = addslashes( strip_tags(trim($this->keywords[ $lang_id ])) );  
       $description = addslashes( strip_tags(trim($this->description[ $lang_id ])) );  
       $short_ = addslashes( $this->short[ $lang_id ] );
       $full_ = addslashes( $this->full[ $lang_id ] );
        
        //---------------- save translit of curent name START -----------------------
       $translit = $this->SaveTranslit($this->category, $this->id,$this->name, $this->translit, $this->translit_old,$lang_id);   
        //---------------- save translit of current name END  ------------------------        
       
       if (isset($_txst[$lang_id]))
       {
         $q="update `".TblModGalleryTxt."` set
                  `title`='".$titles."',
                  `name`='".$name_."',
                  `translit`='".$translit."',
                  `short`='".$short_."',
                  `full`='".$full_."',
                  `description`='".$description."',
                  `keywords`='".$keywords."'
                  WHERE `lang_id`='$lang_id' and `cod`='$this->id'";
          $res = $this->Right->Query( $q, $this->user_id, $this->module );
          //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result; 
          if( !$res ) return false; 
          if( !$this->Right->result ) return false; 
       }
       else
       {
          $q="insert into `".TblModGalleryTxt."`  set
                  `title`='".$titles."',
                  `name`='".$name_."',
                  `translit`='".$translit."',
                  `short`='".$short_."',
                  `full`='".$full_."',
                  `description`='".$description."',
                  `keywords`='".$keywords."',
                  `lang_id`='$lang_id',`cod`='$this->id'";
          $res = $this->Right->Query( $q, $this->user_id, $this->module );
          //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result; 
          if( !$this->Right->result) return false;
       }
      } //--- end while
      return true;
    }


    // ================================================================================================
    // Function : del()
    // Date : 01.07.2010
    // Parms :
    // Returns :      true,false / Void
    // Description :  Remove data from the table
    // Programmer :  Yaroslav Gyryn
    // ================================================================================================
    function del( $id_del )
    {
        $Tags = new SystemTags();
        
        $kol = count( $id_del );
        $del = 0;
        for( $i = 0; $i < $kol; $i++ )
        {
         $u = $id_del[$i];
         
         //--- delete relation between tags for current position --- 
         $res = $Tags->DelTagsByModuleItem( $this->module, $u);
         if( !$res ) return false;
         
         $q = "DELETE FROM `".TblModGallery."` WHERE id='$u'";
         $res = $this->Right->Query( $q, $this->user_id, $this->module );
         $q1 = "DELETE FROM `".TblModGalleryTxt."` WHERE cod='$u'";
         $res = $this->Right->Query( $q1, $this->user_id, $this->module );

         $this->UploadImages->DeleteAllImagesForPosition($u);

         if ( $res )
          $del = $del + 1;
         else
          return false;
        }
      return $del;
    }

    // ================================================================================================
    // Function : up()
    // Date : 01.07.2010
    // Parms :
    // Returns :      true,false / Void
    // Description :  Up
    // Programmer :  Yaroslav Gyryn
    // Date : 01.07.2010
    // ================================================================================================
    function up( $move )
    {
     $q = "select * from ".TblModGallery." where position='$move'";
     $res = $this->Right->Query( $q, $this->user_id, $this->module );
     if( !$res )return false;
     $rows = $this->Right->db_GetNumRows();
     $row = $this->Right->db_FetchAssoc();
     $move_down = $row['position'];
     $id_down = $row['id'];

     $q = "select * from ".TblModGallery." where position>'$move' order by position limit 1";
     $res = $this->Right->Query( $q, $this->user_id, $this->module );
     if( !$res )return false;
     $rows = $this->Right->db_GetNumRows();
     $row = $this->Right->db_FetchAssoc();
     $move_up = $row['position'];
     $id_up = $row['id'];

     if( $move_down!=0 AND $move_up!=0 )
     {
         $q = "update ".TblModGallery." set
             position='$move_down' where id='$id_up'";
         $res = $this->Right->Query( $q, $this->user_id, $this->module );

         $q = "update ".TblModGallery." set
             position='$move_up' where id='$id_down'";
         $res = $this->Right->Query( $q, $this->user_id, $this->module );
     }
    }



    // ================================================================================================
    // Function : down()
    // Date : 01.07.2010
    // Parms :
    // Returns :      true,false / Void
    // Description :  Down
    // Programmer :  Yaroslav Gyryn
    // ================================================================================================
    function down( $move )
    {
     $q = "select * from ".TblModGallery." where position='$move'";
     $res = $this->Right->Query( $q, $this->user_id, $this->module );
     if( !$res )return false;
     $rows = $this->Right->db_GetNumRows();
     $row = $this->Right->db_FetchAssoc();
     $move_up = $row['position'];
     $id_up = $row['id'];

     $q = "select * from ".TblModGallery." where position<'$move' order by position desc limit 1";
     $res = $this->Right->Query( $q, $this->user_id, $this->module );
     if( !$res )return false;
     $rows = $this->Right->db_GetNumRows();
     $row = $this->Right->db_FetchAssoc();
     $move_down = $row['position'];
     $id_down = $row['id'];

     if( $move_down!=0 AND $move_up!=0 )
     {
     $q = "update ".TblModGallery." set
         position='$move_down' where id='$id_up'";
     $res = $this->Right->Query( $q, $this->user_id, $this->module );

     $q = "update ".TblModGallery." set
         position='$move_up' where id='$id_down'";
     $res = $this->Right->Query( $q, $this->user_id, $this->module );
     }
    }
     
   // ================================================================================================
   // Function : ShowErrBackEnd()
   // Date : 01.07.2010
   // Parms :
   // Returns :      true,false / Void
   // Description :  Show errors
   // Programmer :  Yaroslav Gyryn
   // ================================================================================================
   function ShowErrBackEnd()
   {
     if ($this->Err){
        $title=$this->Msg->show_text('MSG_ERRORS');
        echo '
        <fieldset class="err" title="'.$title.'"> <legend>'.$title.'</legend>
        <div class="err_text">'.$this->Err.'</div>
        </fieldset>';
     }
   } //end of fuinction ShowErrBackEnd()
   

} // end of GalleryCtrl Class
?>