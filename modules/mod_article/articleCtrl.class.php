<?
// ================================================================================================
//    System     : SEOCMS
//    Module     : Article
//    Version    : 1.5.0
//    Date       : 4.06.2007 
//    Licensed To:
//                 Andriy Lykhodid    las_zt@mail.ru
//
//    Purpose    : Class definition for Article - module
//
// ================================================================================================

 include_once( SITE_PATH.'/modules/mod_article/article.defines.php' );


/**
 * ArticleCtrl
 * 
 * @package 
 * @author Yaroslav
 * @copyright 2011
 * @version $Id$
 * @access public
 */
class ArticleCtrl extends Article{
    
    public $is_tags = NULL;
    public $user_id = NULL;
    public $module = NULL;
    
    /**
     * ArticleCtrl::__construct()
     * 
     * @return void
     */
    function __construct($user_id = NULL, $module = NULL)
    {
        $this->user_id = $user_id;
        $this->module = $module;
        
        $this->db = DBs::getInstance(); 
        $this->Right =  &check_init("RightsArticle", "Rights", "'".$this->user_id."','".$this->module."'");
        $this->Form = &check_init("Form", "Form", "'form_art'" );
        $this->Msg = &check_init("ShowMsg", "ShowMsg");
        $this->Spr = &check_init("SysSpr", "SysSpr");
        if(defined("_LANG_ID")) $this->lang_id = _LANG_ID;
        ( defined("USE_TAGS") ? $this->is_tags = USE_TAGS : $this->is_tags=0 ); // использовать тэги
        if(empty($this->multi)) $this->multi = &check_init_txt('TblBackMulti', TblBackMulti);
        $this->settings = $this->GetSettings(false);
    }


    // ================================================================================================
    // Function : show()
    // Version : 1.0.0
    // Date : 27.01.2006
    //
    // Parms :
    // Returns :     true,false / Void
    // Description : Show Article
    // ================================================================================================
    // Programmer : Ihor Trokhymchuk
    // Date : 27.01.2006
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function show()
    {
     $db = DBs::getInstance();
     $frm = new Form('fltr');
     $script = 'module='.$this->module.'&display='.$this->display.'&start='.$this->start.'&sort='.$this->sort.'&fltr='.$this->fltr.'&fln='.$this->fln;
     $script = $_SERVER['PHP_SELF']."?$script";

     if( !$this->sort ) $this->sort = 'position';
        $q = "SELECT `".TblModArticle."`.*, `".TblModArticleCat."`.name AS `cat_name`, `".TblModArticleTxt."`.name, `".TblModArticleTxt."`.short AS `short_descr`, `".TblModArticleTxt."`.full AS `full_descr` 
              FROM `".TblModArticle."`
		 LEFT JOIN `".TblModArticleCat."` ON (`".TblModArticleCat."`.lang_id='".$this->lang_id."' AND `".TblModArticle."`.category=`".TblModArticleCat."`.cod)
		     , `".TblModArticleTxt."`
              WHERE  `".TblModArticle."`.id=`".TblModArticleTxt."`.cod
              AND `".TblModArticleTxt."`.lang_id='".$this->lang_id."'
             ";
     if( $this->fltr ) $q = $q." AND `".TblModArticle."`.".$this->fltr;
     $q = $q." ORDER BY `".TblModArticle."`.".$this->sort." desc";

     $res = $this->Right->Query( $q, $this->user_id, $this->module );
     //echo '<br>$q='.$q.' $res='.$res;
     if( !$res )return false;
     $rows = $this->Right->db_GetNumRows();
     
     $artArr = null;
     for( $i = 0; $i < $rows; $i++ )
     {
        $rowA = $this->Right->db_FetchAssoc();
        $artArr[$i] = $rowA;
     }

     /* Write Form Header */
     $this->Form->WriteHeader( $script );

     /* Write Table Part */
     AdminHTML::TablePartH();

     /* Write Links on Pages */
     echo '<TR><TD COLSPAN=13>';
     $script1 = 'module='.$this->module.'&fltr='.$this->fltr;
     $script1 = $_SERVER['PHP_SELF']."?$script1";
     $this->Form->WriteLinkPages( $script1, $rows, $this->display, $this->start, $this->sort );

     echo '<TR><TD COLSPAN=3>';
     $this->Form->WriteTopPanel( $script );
     
     echo '<td CLASS="TR1" align="center">'.$this->multi['_TXT_FILTR'].":<br/>"; 
     $arr = NULL;
     $arr[''] = $this->multi['TXT_NEWS_ALL_CATEGORIES'];
     
     $q = "SELECT `cod`, `name` 
           FROM `".TblModArticleCat."` 
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
     <TR>
     <td class="THead"><input value="0" id="cAll" onclick="if (this.value == '1') {unCheckAll(<?=$ch;?>); this.value = '0';} else {checkAll(<?=$ch;?>); this.value = '1';}" type="checkbox"></Th>
     <td class="THead"><A HREF=<?=$script2?>&sort=id><?=$this->multi['_FLD_ID'];?></A></Th>
     <td class="THead"><?=$this->multi['_FLD_NAME'];?></Th>
     <td class="THead"><?=$this->multi['_FLD_CATEGORY'];?></Th>
     <td class="THead"><?=$this->multi['_FLD_IMG'];?></Th>
     <td class="THead"><?=$this->multi['_FLD_DATE'];?></Th>
     <td class="THead"><?=$this->multi['FLD_STATUS'];?></Th>
     <td class="THead"><?=$this->multi['_FLD_SHORT'];?></Th>
     <td class="THead"><?=$this->multi['FLD_TEXT'];?></Th>
     <td class="THead"><?=$this->multi['FLD_DISPLAY'];?></Th>
     <?
     $up = 0;
     $down = 0;
     $id = 0;

     $a = $rows;
     $j = 0;
     $row_arr = NULL;
     for( $i = 0; $i < $rows; $i++ )
     {
       $row = $artArr[$i];//$this->Right->db_FetchAssoc();
       if( $i >= $this->start && $i < ( $this->start+$this->display ) )
       {
         $row_arr[$j] = $row;
         $j = $j + 1;
       }
     }

     $style1 = 'TR1';
     $style2 = 'TR2';
     //echo count( $row_arr );
     for( $i = 0; $i < count( $row_arr ); $i++ )
     {
       $row = $row_arr[$i];
       //print_r($row_arr[$i]);

       if ( (float)$i/2 == round( $i/2 ) )
       {
        echo '<TR CLASS="'.$style1.'">';
       }
       else echo '<TR CLASS="'.$style2.'">';

       echo '<TD align="center">';
       $this->Form->CheckBox( "id_del[]", $row['id'], null, "check".$i );

       echo '<TD>';
       $this->Form->Link( $script."&task=edit&id=".$row['id'], stripslashes( $row['id'] ), $this->multi['_TXT_EDIT_DATA'] );

       /* Name */
       echo '<TD align=center>';
       $this->Form->Link( $script."&task=edit&id=".$row['id'], stripslashes($row['name']), $this->multi['_TXT_EDIT_DATA'] );

       /* Category */
       echo '<TD align=center>';
       $this->Form->Link( $script."&task=show&fltr=category=".$row['category'], stripslashes($row['cat_name']), $this->multi['_TXT_FILTR'] );

       echo '<TD align=center>';
           
       $img = $this->GetMainImage($row['id'], 'back');
       if ( !empty($img)) {
        ?><a href="<?=$script?>&task=showimages&id=<?=$row['id'];?>"><?=$this->ShowImage( $img, $row['id'], 'size_width=75', 100, NULL, "border=0");?><br><?=$this->multi['TXT_ADD_EDIT_IMAGES'].'['.$this->GetImagesCount($row['id']).']';?></a><?
       }
       else {?><a href="<?=$script?>&task=showimages&id=<?=$row['id'];?>"><?=$this->multi['TXT_ADD_EDIT_IMAGES'].'['.$this->GetImagesCount($row['id']).']';?></a><?}


       /* Date Time */
       echo '<TD align=center>'.$row['dttm'];

       echo '<TD align=center>';
       if( $row['status'] =='i') echo $this->multi['TXT_STATUS_INACTIVE'];
       if( $row['status'] =='e') echo $this->multi['TXT_STATUS_EXPIRED'];
       if( $row['status'] =='a') echo $this->multi['TXT_STATUS_ACTIVE'];

       echo '<TD align=center>';
       $short = trim( stripslashes($row['short_descr']) );
       if( $short ) $this->Form->ButtonCheck();

       echo '<TD align=center>';
       $full = trim( stripslashes($row['full_descr']) );
       if( $full ) $this->Form->ButtonCheck();

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



    /**
      * ArticleCtrl::edit()
      * Edit/add records in Article module
      * @author Yaroslav 
      * @return
      */
    function edit()
    {
        $Panel = new Panel();
        $ln_sys = new SysLang();
        $calendar = new DHTML_Calendar(false, 'en', 'calendar-win2k-2', false);
        $calendar->load_files();
        //$settings = $this->GetSettings(); 
     
        $script = 'module='.$this->module.'&display='.$this->display.'&start='.$this->start.'&sort='.$this->sort.'&fltr='.$this->fltr;
        $script = $_SERVER['PHP_SELF']."?$script";
    
        if( $this->id!=NULL )
        {
           $q = "SELECT `".TblModArticle."`.*
                 FROM `".TblModArticle."`
                 WHERE `".TblModArticle."`.`id`='".$this->id."'
                ";
           $res = $this->Right->Query( $q, $this->user_id, $this->module );
           if( !$res ) return false;
           $mas = $this->Right->db_FetchAssoc();
        }
     
        /* Write Form Header */
        $this->Form->WriteHeaderFormImg( $script );
        $settings=SysSettings::GetGlobalSettings();
        $this->Form->textarea_editor = $settings['editer']; //'tinyMCE'; 
        $this->Form->IncludeSpecialTextArea( $settings['editer']); 
     
        if( $this->id!=NULL ) $txt = $this->Msg->show_text('_TXT_EDIT_DATA');
        else $txt = $this->Msg->show_text('_TXT_ADD_DATA');
     
        AdminHTML::PanelSubH( $txt );
        //-------- Show Error text for validation fields --------------
        $this->ShowErrBackEnd();
        //------------------------------------------------------------
         AdminHTML::PanelSimpleH();

        ?>
        <tr>
          <td>
            <table class="EditTable">
              <tr valign="top">
               <td width="150"><?=$this->multi['_FLD_ID'];?>:&nbsp;
                <?
               if( $this->id!=NULL )
               {
                echo $mas['id'];
                $this->Form->Hidden( 'id', $mas['id'] );
               }
               else $this->Form->Hidden( 'id', '' );
                ?>
                <br/>
              <?=$this->multi['FLD_DISPLAY'];?>:&nbsp;<?
            if( $this->id!=NULL )
            {
              echo $mas["position"];
              echo '<input type="hidden" name=position VALUE="'.$mas["position"].'">';
            }
            else
            {
              $q="select MAX(`position`) from `".TblModArticle."`";
              $res = mysql_query( $q );
              $tmp = mysql_fetch_array( $res );
              $maxx = $tmp['MAX(`position`)'] + 1;
              echo $maxx;
              echo '<INPUT TYPE=hidden NAME=position VALUE="'.$maxx.'">';
            }
            
            $img = $this->GetMainImage($this->id, 'back');
            if ( !empty($img) ) {
            $this->Form->Hidden( 'pic', $img );
            //echo '<INPUT TYPE=text NAME=pic SIZE=50 VALUE="'.$img.'">';
            }                
        
            if ( !empty($img)) {
                $arr = $this->GetImagesToShow($this->id); 
               ?>
               <br/>
               <a href="<?=$script?>&task=showimages&id=<?=$mas['id'];?>"><?=$this->ShowImage( $img, $this->id, 'size_width=100', 100, NULL, "border=0");?>
              <br/><?=$this->multi['TXT_ADD_EDIT_IMAGES2'].'['.$this->GetImagesCount($this->id).']';?></a>
              <br/><a href="<?=$script?>&task=qdelimg&id_img_del=<?=$arr[0]['id'];?>&id=<?=$this->id?>">
              <?=$this->multi['TXT_DELETE'];?></a><?
            }
            /*
            else {
                ?><a href="<?=$script?>&task=showimages&id=<?=$this->id;?>"><?=$this->multi['TXT_ADD_EDIT_IMAGES2'].'['.$this-> GetImagesCount($this->id).']';?></a> <?
            }
            */
            ?>
            <td>
            <table>
             <tr>
              <td width="110"><b><?=$this->multi['_FLD_CATEGORY'];?>:</b></td>
              <td>
               <?
               if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->category : $val=$mas['category'];
               else $this->Err!=NULL ? $val=$this->category : $val='';
               $this->Spr->ShowInComboBox( TblModArticleCat, 'category', stripslashes($val), 40, $this->multi['TXT_SELECT_CATEGORY'] );
               ?>
              </td>
             </tr> 
             <tr>
              <td><b><?=$this->multi['_FLD_STATUS'];?>:</b></td>
              <td>
               <?
               $arr = NULL;
               $arr['a'] = $this->multi['TXT_STATUS_ACTIVE'];
               $arr['e'] = $this->multi['TXT_STATUS_EXPIRED'];
               $arr['i'] = $this->multi['TXT_STATUS_INACTIVE'];
               
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
              //echo '<br>$a1='.$a1.' $a2='.$a2.' $val='.$val;
              $calendar->make_input_field( $a2, $a1 );     
            ?>
            <tr>
                <td><b><?=$this->multi['_FLD_IMG'];?>:</b></td>
                    <?
                   // echo $img;
                    ?>
                <td><input type="file" name="filename[]" size="40" value="<?=$this->img;?>"/></td>
            </tr>
        </table>
      </td>
     </tr>
    </table>
     <?
     /*if ( $this->is_tags==1 ) { 
        $Tags = new SystemTags($this->user_id, $this->module);
        if( $this->id!=NULL ) $this->Err!=NULL ? $id_tag=$this->id_tag : $id_tag=$Tags->GetTagsByModuleAndItem($this->module, $this->id);
        else $id_tag=$this->id_tag;
        //echo '<br>$id_tag='.$id_tag; print_r($id_tag);
        ?><div><?$Tags->ShowEditTags($id_tag);?><br /></div><?        
     } */
     $Panel->WritePanelHead( "SubPanel_" );
     $tmp_bd= DBs::getInstance();
     $q1="select * from `".TblModArticleTxt."` where `cod`='".$this->id."'";
     $res = $tmp_bd->db_query( $q1);
     if( !$tmp_bd->result ) return false;
     $rows1 = $tmp_bd->db_GetNumRows();
     $txt= array();
     for($i=0; $i<$rows1;$i++)
     {
        $row1 = $tmp_bd->db_FetchAssoc();
        $txt[$row1['lang_id']]=$row1;
     }
        $ln_arr = $ln_sys->LangArray( _LANG_ID );
        
     while( $el = each( $ln_arr ) )
     {
         $lang_id = $el['key'];
         $lang = $el['value'];
         $mas_s[$lang_id] = $lang;

         $Panel->WriteItemHeader( $lang );
         echo "\n <table border=0 class='EditTable' width='100%'>";
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
         echo "\n</table><br>";
            
         echo "\n<fieldset title='".$this->multi['_TXT_META_DATA']."'> <legend><span style='vetical-align:middle; font-size:15px;'><img src='images/icons/meta.png' alt='".$this->multi['_TXT_META_DATA']."' title='".$this->multi['_TXT_META_DATA']."' border='0' /> ".$this->multi['_TXT_META_DATA']."</span></legend>";
         echo "\n <table border=0 class='EditTable'>";
         echo "\n <tr>";
         echo "\n <td><b>".$this->multi['_FLD_TITLE'].":</b>";
         echo "\n <br>";
         echo '<span class="help">'.$this->multi['HELP_MSG_PAGE_TITLE'].'</span>';
         echo "\n <br>";
         if (isset($txt[$lang_id]['title']))
            $row = $txt[$lang_id]['title'];
         else $row='';    
         if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->title[$lang_id] : $val=$row;
         else $this->Err!=NULL ? $val=$this->title[$lang_id] : $val = '';
         $this->Form->TextBox( 'title['.$lang_id.']', stripslashes($val), 110 );
         echo "<hr width='80%' align='left' size='1'>";

        //if(isset($settings['set_descr']) AND $settings['set_descr']=='1' ) {
        echo "\n <tr><td><b>".$this->multi['FLD_DECRIPTION'].":</b>";
        echo "\n <br>";
        echo '<span class="help">'.$this->multi['HELP_MSG_PAGE_DESCRIPTION'].'</span>';
        echo "\n <br>";
        if (isset($txt[$lang_id]['description']))
            $row = $txt[$lang_id]['description'];
        else $row='';
        if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->description[$lang_id] : $val=$row;
        else $this->Err!=NULL ? $val=$this->description[$lang_id] : $val = '';
        $this->Form->TextArea( 'description['.$lang_id.']',  stripslashes($val), 2, 110);
        echo "<hr width='100%' align='left' size='1'>";
        //}        
        //if(isset($settings['set_keywrd']) AND $settings['set_keywrd']=='1' ) {
        echo "\n <tr><td><b>".$this->multi['FLD_KEYWORDS'].":</b>";
        echo "\n <br>";
        echo '<span class="help">'.$this->multi['_HELP_MSG_PAGE_KEYWORDS'].'</span>';
        echo "\n <br>";
        if (isset($txt[$lang_id]['keywords']))
            $row = $txt[$lang_id]['keywords'];
        else $row='';
        if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->keywords[$lang_id] : $val=$row;
        else $this->Err!=NULL ? $val=$this->keywords[$lang_id] : $val = '';
        $this->Form->TextArea( 'keywords['.$lang_id.']',  stripslashes($val), 2, 110 );
        echo "<hr width='100%' align='left' size='1'>";
        //}
        echo "\n </table>";
        echo "</fieldset><br>";
        
        echo "\n <table border=0 class='EditTable'>";
        echo "\n <tr>";
        echo "\n <td><b>".$this->multi['_FLD_SHORT'].":</b>";
        echo "\n <br>";
        if (isset($txt[$lang_id]['short']))
                    $row = $txt[$lang_id]['short'];
              else $row='';
        if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->short[$lang_id] : $val=$row;
        else $this->Err!=NULL ? $val=$this->short[$lang_id] : $val = '';
        $this->Form->SpecialTextArea(NULL, 'short['.$lang_id.']', stripslashes($val), 12, 70, 'class="contentInput"', $lang_id );
        echo "\n <br>";

        echo "\n <tr>";
        echo "\n <td><b>".$this->multi['FLD_TEXT'].":</b>";
        echo "\n <br>";
        
         if (isset($txt[$lang_id]['full']))
                    $row = $txt[$lang_id]['full'];
              else $row='';
        if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->full[$lang_id] : $val=$row;
        else $this->Err!=NULL ? $val=$this->full[$lang_id] : $val = '';
        $this->Form->SpecialTextArea(NULL, 'full['.$lang_id.']', stripslashes($val), 25, 70, 'class="contentInput"', $lang_id ,'descr' );
        echo "\n <br>"; 
        echo   "\n </table>";
        $Panel->WriteItemFooter();
    }

          $Panel->WritePanelFooter();
    ?>
        </td>
    </tr>
    <?

     AdminHTML::PanelSimpleF();
     $this->Form->WriteSaveAndReturnPanel( $script );
     $this->Form->WriteSavePanel( $script );
     $this->Form->WriteCancelPanel( $script );
     //$this->Form->WritePreviewPanel( 'http://'.NAME_SERVER."/modules/mod_article/article.preview.php" );

     $this->Form->WriteFooter();

     AdminHTML::PanelSubF();
    }

   // ================================================================================================
   // Function : CheckFields()
   // Version : 1.0.0
   // Date : 08.10.2009
   //
   // Parms :
   // Returns :      true,false / Void
   // Description :  Checking all fields for filling and validation
   // ================================================================================================
   // Programmer :  Igor Trokhymchuk
   // Date : 10.01.2006
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function CheckFields()
   {
        $this->Err=NULL;

        if (empty( $this->category )) {
            $this->Err=$this->Err.$this->Msg->show_text('MSG_FLD_CATEGORY_EMPTY').'<br>';
        }       
        
        $ln_sys = new SysLang();
        $ln_arr = $ln_sys->LangArray( _LANG_ID );
        $i=0;
        while( $el = each( $ln_arr ) ){
            $lang_id = $el['key'];
            if( !empty( $this->name[$lang_id] ) ) continue;
            else $i++;
        }
        if($i==count($this->name)) $this->Err=$this->Err.$this->Msg->show_text('MSG_FLD_NAME_EMPTY').'<br>'; 

        //echo '<br>$this->Err='.$this->Err.' $this->Msg->table='.$this->Msg->table;
        return $this->Err;
   } //end of fuinction CheckFields() 

    // ================================================================================================
    // Function : save()
    // Version : 1.0.0
    // Date : 27.01.2006
    // Parms :
    // Returns : true,false / Void
    // Description : Store Article ( Save )
    // ================================================================================================
    // Programmer : Andriy Lykhodid
    // Date : 27.01.2006
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function save()
    {
        $ln_sys = new SysLang();

        //print_r($_REQUEST);
        if( empty( $this->category ) )
        {
         $this->Msg->show_msg('NEWS_CATEGORY_EMPTY');
         $this->edit( $this->id, $_REQUEST );
         return false;
        }

     
       if( $this->id )
       {
         $q = "SELECT * FROM ".TblModArticle." WHERE `id`='$this->id'";
         $res = $this->Right->Query( $q, $this->user_id, $this->module );
         if( !$res ) return false;

         $rows = $this->Right->db_GetNumRows();
       }else $rows = 0;

       if( $rows > 0 )   //--- update
       {
          $q = "UPDATE `".TblModArticle."` SET
               `id_department`='".$this->id_department."',
               `category`='".$this->category."',
               `status`='".$this->status."',
               `dttm`='".$this->dttm."',
               `img`='".$this->img."',
               `position`='".$this->position."'
                WHERE `id`='".$this->id."'";
       }
       else          //--- insert
       {
         $q = "INSERT INTO `".TblModArticle."` SET
               `id_department`='".$this->id_department."',
               `category`='".$this->category."',
               `status`='".$this->status."',
               `dttm`='".$this->dttm."',
               `img`='".$this->img."',
               `position`='".$this->position."'         
              ";
       }
       $res = $this->Right->Query( $q, $this->user_id, $this->module );
       //echo '<br>$q='.$q.' $res='.$res;
       if( !$res ) return false;
       
       if( $rows == 0 ) $this->id = $this->Right->db_GetInsertID(); 
       
      //---- save tags ----
      if ( $this->is_tags==1 ) {
           $Tags = new SystemTags();
           $res=$Tags->SaveTagsById( $this->module, $this->id, $this->is_tags );
           if( !$res ) return false;
      } 
     // die; 
      
     $q="select * from `".TblModArticleTxt."` where `cod`='".$this->id."'";
     $res = $this->Right->Query( $q, $this->user_id, $this->module );
     if( !$this->Right->result ) return false;
     $rows = $this->Right->db_GetNumRows();
     $_txst= array();
     for($i=0; $i<$rows;$i++)
     {
        $row = $this->Right->db_FetchAssoc();
        $_txst[$row['lang_id']]='1';
     }
     $ln_arr = $ln_sys->LangArray( _LANG_ID );
     while( $el = each( $ln_arr ) )
     {
        //print_r($el);
        //print_r($this->short);
        //echo '</br>';
       if(isset($this->name[ $el['key'] ])) $name_ = addslashes( strip_tags(trim($this->name[ $el['key'] ])) );
       else $name_= NULL;
       
       if(isset($this->title[ $el['key'] ])) $titles = addslashes( strip_tags(trim($this->title[ $el['key'] ])) );
       else $titles= NULL;
       
       if(isset($this->keywords[ $el['key'] ])) $keywords = addslashes( strip_tags(trim($this->keywords[ $el['key'] ])) );
       else $keywords= NULL;
       
       if(isset($this->description[ $el['key'] ])) $description = addslashes( strip_tags(trim($this->description[ $el['key'] ])) );
       else $description= NULL;
       
       if(isset($this->short[ $el['key'] ])) $short_ = addslashes( /*strip_tags(*/trim($this->short[ $el['key'] ])/*)*/ );
       else $short_= NULL;
       
       //echo ' short='.$short_.'<br/>';
       
       if(isset($this->full[ $el['key'] ])) $full_ = addslashes( /*strip_tags(*/trim($this->full[ $el['key'] ])/*)*/ );
       else $full_= NULL;
       
      // $titles = addslashes( strip_tags(trim($this->title[ $el['key'] ])) );
       //$keywords = addslashes( strip_tags(trim($this->keywords[ $el['key'] ])) );  
       //$description = addslashes( strip_tags(trim($this->description[ $el['key'] ])) );  
       //$short_ = addslashes( $this->short[ $el['key'] ] );
       //$full_ = addslashes( $this->full[ $el['key'] ] );
       $lang_id = $el['key'];
       if (isset($_txst[$lang_id]))
              {
                  
                 $q="update `".TblModArticleTxt."` set
                  `title`='".$titles."',
                  `name`='".$name_."',
                  `short`='".$short_."',
                  `full`='".$full_."',
                  `description`='".$description."',
                  `keywords`='".$keywords."'
                  WHERE `lang_id`='$lang_id' and `cod`='$this->id'";
                  $res = $this->Right->Query( $q, $this->user_id, $this->module );
//                  echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result; 
                  if( !$res ) return false; 
                  if( !$this->Right->result ) return false; 
              }
        else
        {
          $q="insert into `".TblModArticleTxt."`  set
                  `title`='".$titles."',
                  `name`='".$name_."',
                  `short`='".$short_."',
                  `full`='".$full_."',
                  `description`='".$description."',
                  `keywords`='".$keywords."',
                  `lang_id`='$lang_id',`cod`='$this->id'";
          $res = $this->Right->Query( $q, $this->user_id, $this->module );
//          echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result; 
          if( !$this->Right->result) return false;
          }
      

     } //--- end while
      
     //$link = $this->Link($this->category, $this->id); 
     $link = $this->GetLink($this->id);
     if( empty($link) ) $link = $this->SetLink($this->id, true);
      
     $res = $this->SavePicture(); 
     
     //$uploaddir = ArticleImg_Path;
     //$Uploads = new Uploads( $this->user_id , $this->module , $uploaddir, 200, $this->module );
     //$Uploads->saveCurentImages($this->id, $this->module);
     
     return true;
    }



    // ================================================================================================
    // Function : del()
    // Date : 27.01.2006
    // Returns :      true,false / Void
    // Description :  Remove data from the table
    // Programmer :  Andriy Lykhodid
    // ================================================================================================
    function del( $id_del )
    {
        $kol = count( $id_del );
        $del = 0;
        for( $i = 0; $i < $kol; $i++ )
        {
         $u = $id_del[$i];
         $q = "DELETE FROM `".TblModArticle."` WHERE id='$u'";
         $res = $this->Right->Query( $q, $this->user_id, $this->module );
         $q1 = "DELETE FROM `".TblModArticleTxt."` WHERE cod='$u'";
         $res = $this->Right->Query( $q1, $this->user_id, $this->module );

         // del links
         $q = "DELETE FROM `".TblModArticleLinks."` WHERE cod='$u'";
         $res = $this->Right->Query( $q, $this->user_id, $this->module );
         
         $q = "select * from ".TblModArticleImg." where id_art='$u'";
         $res = $this->Right->db_Query( $q );
         $rows = $this->Right->db_GetNumRows(); 
         $id_img=NULL;
         for($j=0;$j<$rows;$j++){
                    $row = $this->Right->db_FetchAssoc();
                    $id_img[$j] = $row['id'];
         } 
         if(count($id_img)>0) {
         $res = $this->DelPicture($id_img, $u);
         if (!$res) return false; 
         }

         if ( $res )
          $del = $del + 1;
         else
          return false;
        }
      return $del;
    }



    // ================================================================================================
    // Function : up()
    // Version : 1.0.0
    // Date : 27.01.2006
    // Parms :
    // Returns :      true,false / Void
    // Description :  Up
    // ================================================================================================
    // Programmer :  Andriy Lykhodid
    // Date : 27.01.2006
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================

    function up( $move )
    {
     $q = "select * from ".TblModArticle." where position='$move'";
     $res = $this->Right->Query( $q, $this->user_id, $this->module );
     if( !$res )return false;
     $rows = $this->Right->db_GetNumRows();
     $row = $this->Right->db_FetchAssoc();
     $move_down = $row['position'];
     $id_down = $row['id'];


     $q = "select * from ".TblModArticle." where position>'$move' order by position limit 1";
     $res = $this->Right->Query( $q, $this->user_id, $this->module );
     if( !$res )return false;
     $rows = $this->Right->db_GetNumRows();
     $row = $this->Right->db_FetchAssoc();
     $move_up = $row['position'];
     $id_up = $row['id'];

     if( $move_down!=0 AND $move_up!=0 )
     {
     $q = "update ".TblModArticle." set
         position='$move_down' where id='$id_up'";
     $res = $this->Right->Query( $q, $this->user_id, $this->module );

     $q = "update ".TblModArticle." set
         position='$move_up' where id='$id_down'";
     $res = $this->Right->Query( $q, $this->user_id, $this->module );
     }
    }




    // ================================================================================================
    // Function : down()
    // Version : 1.0.0
    // Date : 27.01.2006
    // Parms :
    // Returns :      true,false / Void
    // Description :  Down
    // ================================================================================================
    // Programmer :  Andriy Lykhodid
    // Date : 27.01.2006
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================

    function down( $move )
    {
     $q = "select * from ".TblModArticle." where position='$move'";
     $res = $this->Right->Query( $q, $this->user_id, $this->module );
     if( !$res )return false;
     $rows = $this->Right->db_GetNumRows();
     $row = $this->Right->db_FetchAssoc();
     $move_up = $row['position'];
     $id_up = $row['id'];


     $q = "select * from ".TblModArticle." where position<'$move' order by position desc limit 1";
     $res = $this->Right->Query( $q, $this->user_id, $this->module );
     if( !$res )return false;
     $rows = $this->Right->db_GetNumRows();
     $row = $this->Right->db_FetchAssoc();
     $move_down = $row['position'];
     $id_down = $row['id'];

     if( $move_down!=0 AND $move_up!=0 )
     {
     $q = "update ".TblModArticle." set
         position='$move_down' where id='$id_up'";
     $res = $this->Right->Query( $q, $this->user_id, $this->module );

     $q = "update ".TblModArticle." set
         position='$move_up' where id='$id_down'";
     $res = $this->Right->Query( $q, $this->user_id, $this->module );
     }
    }


    // ================================================================================================
    // Function : CheckImages
    // Version : 1.0.0
    // Date : 17.11.2006
    //
    // Parms :
    // Returns : $res / Void
    // Description : check uploaded images for size, type and other.
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 17.11.2006
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function CheckImages()
    {
     $this->Err = NULL;
     $max_image_width= ARTICLE_MAX_IMAGE_WIDTH;
     $max_image_height= ARTICLE_MAX_IMAGE_HEIGHT;
     $max_image_size= ARTICLE_MAX_IMAGE_SIZE;
     $valid_types =  array("gif", "GIF", "jpg", "JPG", "png", "PNG", "jpeg", "JPEG");
     //print_r($_FILES["image"]);
     if (!isset($_FILES["image"])) return false;
     $cols = count($_FILES["image"]);

     //echo '<br><br>$cols='.$cols;
     for ($i=0; $i<$cols; $i++) {
         //echo '<br>$_FILES["image"]='.$_FILES["image"].' $_FILES["image"]["tmp_name"]["'.$i.'"]='.$_FILES["image"]["tmp_name"]["$i"].' $_FILES["image"]["size"]["'.$i.'"]='.$_FILES["image"]["size"]["$i"];
         //echo '<br>$_FILES["image"]["name"][$i]='.$_FILES["image"]["name"][$i];
         if ( !empty($_FILES["image"]["name"][$i]) ) {
           if ( isset($_FILES["image"]) && is_uploaded_file($_FILES["image"]["tmp_name"][$i]) && $_FILES["image"]["size"][$i] ){
            $filename = $_FILES['image']['tmp_name'][$i];
            $ext = substr($_FILES['image']['name'][$i],1 + strrpos($_FILES['image']['name'][$i], "."));
            //echo '<br>filesize($filename)='.filesize($filename).' $max_image_size='.$max_image_size;
            if (filesize($filename) > $max_image_size) {
                $this->Err = $this->Err.$this->Msg->show_text('MSG_ERR_FILE_SIZE').' ('.$_FILES['image']['name']["$i"].')<br>';
                continue;
            }
            if (!in_array($ext, $valid_types)) {
                $this->Err = $this->Err.$this->Msg->show_text('MSG_ERR_FILE_TYPE').' ('.$_FILES['image']['name']["$i"].')<br>';  
            }
            else {
              $size = GetImageSize($filename);
              //echo '<br>$size='.$size.'$size[0]='.$size[0].' $max_image_width='.$max_image_width.' $size[1]='.$size[1].' $max_image_height='.$max_image_height;
              if (($size) && ($size[0] < $max_image_width) && ($size[1] < $max_image_height)) {
                 //$alias = $this->Spr->GetNameByCod( TblModCatalogPropSprName, $this->id );
              }
              else {
                 $this->Err = $this->Err.$this->Msg->show_text('MSG_ERR_FILE_PROPERTIES').' ['.$max_image_width.'x'.$max_image_height.'] ('.$_FILES['image']['name']["$i"].')<br>'; 
              }
            }
           }
           else $this->Err = $this->Err.$this->Msg->show_text('MSG_ERR_FILE').' ('.$_FILES['image']['name']["$i"].')<br>';
         } 
         //echo '<br>$i='.$i;
     } // end for
     return $this->Err;
   }  // end of function CheckImages() 

   
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
     $tmp_db = DBs::getInstance();
     $this->Err = NULL;
     $max_image_width= ARTICLE_MAX_IMAGE_WIDTH;
     $max_image_height= ARTICLE_MAX_IMAGE_HEIGHT;
     $max_image_size= ARTICLE_MAX_IMAGE_SIZE;
     $valid_types =  array("gif", "GIF", "jpg", "JPG", "png", "PNG", "jpeg", "JPEG");
     //print_r($_FILES["filename"]);
     //echo $this->img; 
     if (!isset($_FILES["filename"])) return false; 
     $cols = count($_FILES["filename"]);

     //echo '<br><br>$cols='.$cols;
     for ($i=0; $i<$cols; $i++) {
         //echo '<br>$_FILES["image"]='.$_FILES["image"].' $_FILES["image"]["tmp_name"]["'.$i.'"]='.$_FILES["image"]["tmp_name"]["$i"].' $_FILES["image"]["size"]["'.$i.'"]='.$_FILES["image"]["size"]["$i"];
         //echo '<br>$_FILES["image"]["name"][$i]='.$_FILES["image"]["name"][$i];
         if ( !empty($_FILES["filename"]["name"][$i]) ) {
           if ( isset($_FILES["filename"]) && is_uploaded_file($_FILES["filename"]["tmp_name"][$i]) && $_FILES["filename"]["size"][$i] ){
            $filename = $_FILES['filename']['tmp_name'][$i];
            $ext = substr($_FILES['filename']['name'][$i],1 + strrpos($_FILES['filename']['name'][$i], "."));
            $name_no_ext = substr($_FILES['filename']['name'][$i], 0, strrpos($_FILES['filename']['name'][$i], "."));
            //echo '<br>filesize($filename)='.filesize($filename).' $max_image_size='.$max_image_size;
            if (filesize($filename) > $max_image_size) {
                $this->Err = $this->Err.$this->Msg->show_text('MSG_ERR_FILE_SIZE').' ('.$_FILES['filename']['name']["$i"].')<br>';
                continue;
            }
            if (!in_array($ext, $valid_types)) {
                $this->Err = $this->Err.$this->Msg->show_text('MSG_ERR_FILE_TYPE').' ('.$_FILES['filename']['name']["$i"].')<br>';  
            }
            else {
              $size = GetImageSize($filename);
              //echo '<br>$size='.$size.'$size[0]='.$size[0].' $max_image_width='.$max_image_width.' $size[1]='.$size[1].' $max_image_height='.$max_image_height;
              //if (($size) && ($size[0] < $max_image_width) && ($size[1] < $max_image_height)) {
                 $settings = $this->GetSettings();
                // $uploaddir = NewsImg_Full_Path.$this->id;
                 $alias = $this->id;
                 $uploaddir = SITE_PATH.$settings['img_path'].'/'.$alias;
                 if ( !file_exists ($uploaddir) ) mkdir($uploaddir,0777);  
                // if ( !file_exists ($uploaddir) ) mkdir($uploaddir,0777); 
                 else @chmod($uploaddir,0777);
                 
                 $uploaddir2 = $name_no_ext.'_'.time().$i.'.'.$ext;
                 $uploaddir = $uploaddir."/".$uploaddir2;
              
                 //echo '<br>$filename='.$filename.'<br> $uploaddir='.$uploaddir.'<br> $uploaddir2='.$uploaddir2;
                 //if (@move_uploaded_file($filename, $uploaddir)) {
                 if ( copy($filename,$uploaddir) ) {
                     $q="select MAX(`move`) from `".TblModArticleImg."` where 1";
                     $res = $tmp_db->db_Query( $q );
                     //$rows = $tmp_db->db_GetNumRows();
                     $my = $tmp_db->db_FetchAssoc();
                     $maxx=$my['MAX(`move`)']+1;  //add link with position auto_incremental

                     $q="INSERT INTO `".TblModArticleImg."` SET
                         `id_art`='".$this->id."',
                         `path`='".$uploaddir2."',
                         `show`='1', 
                         `move`='".$maxx."'
                        ";
                     $res = $tmp_db->db_Query( $q );
                     if( !$res OR !$tmp_db->result ) $this->Err = $this->Err.$this->Msg->show_text('MSG_ERR_SAVE_FILE_TO_DB').' ('.$_FILES['image']['name']["$i"].')<br>';
                     //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
                     if (($size) AND (($size[0] > $max_image_width) OR ($size[1] > $max_image_height)) ){
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
                 else {
                     $this->Err = $this->Err.$this->Msg->show_text('MSG_ERR_FILE_MOVE').' ('.$_FILES['filename']['name']["$i"].')<br>';
                 }
              //}
              //else {
              //   $this->Err = $this->Err.$this->Msg->show_text('MSG_ERR_FILE_PROPERTIES').' ['.$max_image_width.'x'.$max_image_height.'] ('.$_FILES['filename']['name']["$i"].')<br>'; 
              //}
            }
           }
           else $this->Err = $this->Err.$this->Msg->show_text('MSG_ERR_FILE').' ('.$_FILES['filename']['name']["$i"].')<br>';
         } 
         //echo '<br>$i='.$i;
     } // end for
     return $this->Err;

    }  // end of function SavePicture() 
    
    
    // ================================================================================================
    // Function : UpdatePicture
    // Version : 1.0.0
    // Date : 28.11.2006
    //
    // Parms :
    // Returns : $err / error string
    // Description : Save comments of the image to the table
    // ================================================================================================
    // Programmer : Alex Kerest
    // Date : 8.05.2007
    // Reason for change : Modernization
    // Change Request Nbr:
    // ================================================================================================
     function UpdatePicture(){
     $this->Err = NULL;
     //print_r($this->img_descr);
     for($i=0; $i<count($this->id_img); $i++){
        //if ( isset($this->img_show[$i]) ) $img_show = 1;
        //else $img_show = 0;
        $key = array_search($this->id_img[$i], $this->img_show);
        if ($key!==false) $img_show = 1;
        else $img_show = 0; 
        
        $q="update `".TblModArticleImg."` set `show`='".$img_show."' where `id`='".$this->id_img[$i]."'";
        $res = $this->Right->Query( $q, $this->user_id, $this->module );
        //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;  
        if( !$this->Right->result ) $this->Err = $this->Err.$this->Msg->show_text('MSG_ERR_SAVE_FILE_TO_DB').'<br>';
       
     //print_r($this->img_title)  ;
    $meta= $this->get($this->id_img[$i]);
     $ln_sys = new SysLang();
            $ln_arr = $ln_sys->LangArray( _LANG_ID );
            while( $el = each( $ln_arr ) )
            {
              $lang_id = $el['key'];
              if (isset($meta[$lang_id]))
              {
                  
                 $q="update `".TblModArticleImgSpr."` set
                  `name`='".$this->img_title[$this->id_img[$i]][$lang_id]."',
                  `descr`='".$this->img_descr[$this->id_img[$i]][$lang_id]."'
                  WHERE `lang_id`='$lang_id' and `cod`='".$this->id_img[$i]."'" ;
                  $res = $this->Right->Query( $q, $this->user_id, $this->module );
                  //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result; 
                  if( !$res ) return false; 
                  if( !$this->Right->result ) return false; 
              }
        else
        {
          $q="insert into `".TblModArticleImgSpr."`  set
          `name`='".$this->img_title[$this->id_img[$i]][$lang_id]."',
                  `descr`='".$this->img_descr[$this->id_img[$i]][$lang_id]."',
                  `lang_id`='$lang_id', `cod`='".$this->id_img[$i]."'";
          $res = $this->Right->Query( $q, $this->user_id, $this->module );
//          echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result; 
          if( !$this->Right->result) return false;
          }
            }
       
           /*$res=$this->Spr->SaveNameArr( $this->id_img[$i], $this->img_title[$this->id_img[$i]], TblModArticleImgSprName );
            //echo "<br>res1=".$res;
            if( !$res ) return false;
        
            $res=$this->Spr->SaveNameArr( $this->id_img[$i], $this->img_descr[$this->id_img[$i]], TblModArticleImgSprDescr );
            //echo " <br>res2=".$res;
            if( !$res ) return false;*/
      
     }
     return $this->Err;
    }  // end of function UpdatePicture()
    
    // ================================================================================================
    // Function : DelPicture
    // Version : 1.0.0
    // Date : 03.04.2006
    //
    // Parms :  $id_img_del - file for upload
    // Returns : $res / Void
    // Description : Remove images from table
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 03.04.2006 
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function DelPicture($id_img_del, $id_art = NULL)
    {
     $tmp_db = DBs::getInstance();
     $del=0;
     if(empty($id_art)) $id_art = $this->GetArticleIdByImgId($id_img_del[0]);
     for($i=0; $i<count($id_img_del); $i++){
       $u=$id_img_del[$i];
       
       $q="SELECT * FROM `".TblModArticleImg."` WHERE `id`='".$u."'";
       $res = $tmp_db->db_Query( $q );
       //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;  
       if( !$res OR !$tmp_db->result ) return false;
       $row = $tmp_db->db_FetchAssoc();
       $path = ArticleImg_Full_Path.'/'.$row['id_art'].'/'.$row['path'];
       // delete file which store in the database
       if (file_exists($path)) {
          $res = unlink ($path);
          if( !$res ) return false;
       }
       
       $q="DELETE FROM `".TblModArticleImg."` WHERE `id`='".$u."'";
       $res = $tmp_db->db_Query( $q );
       //echo '<br>2q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;  
       if( !$res OR !$tmp_db->result ) return false;
        $q="DELETE FROM `".TblModArticleImgSpr."` WHERE `id`='".$u."'";
       $res = $tmp_db->db_Query( $q ); 
       if( !$res )return false;           
       
       $del=$del+1;
     
       $path = ArticleImg_Full_Path.'/'.$row['id_art'];
       //echo '<br> $path='.$path;
       if( is_dir($path) ){
           $handle = @opendir($path);
           //echo '<br> $handle='.$handle;
           $cols_files = 0;
           while ( ($file = readdir($handle)) !==false ) {
               //echo '<br> $file='.$file;
               $mas_file=explode(".",$file);
               $mas_img_name=explode(".",$row['path']);
               if ( strstr($mas_file[0], $mas_img_name[0].ARTICLE_ADDITIONAL_FILES_TEXT) and $mas_file[1]==$mas_img_name[1] ) {
                  $res = unlink ($path.'/'.$file);
                  if( !$res ) return false;                    
               }
               if ($file == "." || $file == ".." ) {
                   $cols_files++;
               }
           }
           closedir($handle);
       }
     }
     $n = $this->GetImagesCount($id_news);
     if( $n==0 AND is_dir($path) ) $this->full_rmdir($path);          
     return $del;
    } // end of function DelPicture()
    
     // ================================================================================================
     // Function : full_rmdir
     // Date : 07.04.2011
     // Parms :  $dirname - directory for full del
     // Returns : $res / Void
     // Description : Full remove directory from disk (all files and subdirectory)
     // Programmer : Yaroslav Gyryn
     // ================================================================================================     
     function full_rmdir($dirname)
     {
        if ($dirHandle = opendir($dirname)){
            $old_cwd = getcwd();
            chdir($dirname);

            while ($file = readdir($dirHandle)){
                if ($file == '.' || $file == '..') continue;

                if (is_dir($file)){
                    if (!full_rmdir($file)) return false;
                }else{
                    if (!unlink($file)) return false;
                }
            }

            closedir($dirHandle);
            chdir($old_cwd);
            if (!rmdir($dirname)) return false;

            return true;
        }else{
            return false;
        }
     }    
     
    // ================================================================================================
    // Function : GetArticleIdByImgId
    // Date : 22.06.2007 
    // Parms :  $img - name of the picture
    // Returns : $res / Void
    // Description : return title for image 
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function GetArticleIdByImgId( $img )
    {
        $tmp_db = new DB();
        $q = "SELECT * FROM `".TblModArticleImg."` WHERE 1 AND `id`='".$img."'";
        $res = $tmp_db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
        if ( !$res or !$tmp_db->result ) return false;
        //$rows = $tmp_db->db_GetNumRows();
        // echo '<br>$rows='.$rows;
        $row = $tmp_db->db_FetchAssoc();
        $id = $row['id_news']; 
        return $id;            
    } //end of function GetArticleIdByImgId()       
    
     // ================================================================================================
    // Function : ShowImagesBackEnd
    // Version : 1.0.0
    // Date : 03.04.2006
    //
    // Parms :
    // Returns : $res / Void
    // Description : Show the immages of item product
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 03.04.2006
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function ShowImagesBackEnd()
    {
     $this->Rights =  new Rights;   
     $Panel = new Panel();
     $ln_sys = new SysLang(); 
     $settings = $this->GetSettings(); 
   
     $q="SELECT * FROM `".TblModArticleImg."` WHERE `id_art`='$this->id' order by `move`";
     $res = $this->Right->Query( $q, $this->user_id, $this->module );
     if( !$this->Right->result ) return false;
     $rows = $this->Right->db_GetNumRows(); 
     
     $img_arr = null;
     for ($i=0; $i<$rows; $i++) {
        $row = $this->Rights->db_FetchAssoc();
        $img_arr[$i] = $row;
     }

     $q="SELECT * FROM `".TblModArticleTxt."` WHERE `cod`='$this->id' and `lang_id`="._LANG_ID."";
     $res2 = $this->Rights->Query( $q, $this->user_id, $this->module );
     if( !$this->Rights->result ) return false;
     $rows2 = $this->Rights->db_GetNumRows();
     $row2 = $this->Rights->db_FetchAssoc();
     //$this->Form->IncludeHTMLTextArea();          
     
     $txt = $this->Msg->show_text('TXT_ADDITING_IMAGES');
     AdminHTML::PanelSubH( $txt );
      $script2 = 'module='.$this->module.'&display='.$this->display.'&start='.$this->start.'&task=show&fltr='.$this->fltr;
      $this->script = $_SERVER['PHP_SELF']."?$script2";
    //   echo  $this->script;
    //-------- Show Error text for validation fields --------------
    $this->ShowErrBackEnd();
    //-------------------------------------------------------------          

     AdminHTML::PanelSimpleH();
     
     ?>
     <Table border=0 class="EditTable">
      <TR>
       <FORM ACTION="<?=$_SERVER['PHP_SELF']?>" name="AddImg" enctype="multipart/form-data" method="post">
       <input type="hidden" name="id" value="<?=$this->id;?>">
       <TD><b><?=$this->Msg->show_text('_FLD_ARTICLE')?>:</b></TD>
       <TD width="99%"><?=stripcslashes($row2['name']);?></TD>
      </TR>
     <?

     $a = $rows;
     $up = 0;
     $down = 0;         
//     echo $rows;
     for ($i=0; $i<$rows; $i++) {
       $row = $img_arr[$i];//$this->Right->db_FetchAssoc();
       $img_path = $this->GetImgPath($row['path'], $this->id);
       //echo 'row_path='.$row['path'].' $img_path='.$img_path;
       ?>
      <TR>
       <TD colspan=2>
       <?=AdminHTML::PanelSimpleH();?>
        <TABLE border=0 cellpadding=2 cellspacing=0 class="EditTable">
         <TR>
          <TD align=right><INPUT class='checkbox' TYPE=checkbox NAME='id_img_del[]' VALUE="<?=$row['id'];?>"></TD>
          <TD align=center valign="middle" width="255">
           <?/*<a href="http://<?=$_SERVER['SERVER_NAME']?>/img.php?item=<?=$row['path']?>&amp;art=<?=$this->id?>" target=_blank><?=$this->ShowImage($row['path'], $this->id, 'size_auto=250', 100, NULL, "border=0");?></a>*/?>
           <a href="http://<?=$_SERVER['SERVER_NAME'].$img_path;?>" target=_blank><?=$this->ShowImage($row['path'], $this->id, 'size_auto=250', 100, NULL, "border=0");?></a>
          </TD>
          <TD valign="top">
           <table border=0 cellpadding=0 cellspacing=2 class="EditTable">
            <tr>             
             <TD><b><?=$this->Msg->show_text('_FLD_ID')?>:</b><?=$row['id']; $this->Form->Hidden( 'id_img[]', $row['id'] );?></TD>
            </tr>
            <tr>
             <TD><b><?=$this->Msg->show_text('FLD_IMG')?>:</b> <?=SITE_PATH.$img_path;?></TD> 
            </tr>
            <tr><td>
                          <?
            $Panel->WritePanelHead( "SubPanel_" );   
            $meta = $this->get($row['id'] );
            $ln_arr = $ln_sys->LangArray( _LANG_ID );
            while( $el = each( $ln_arr ) )
            {
              $lang_id = $el['key'];
              $lang = $el['value'];
              $mas_s[$lang_id] = $lang;

              $Panel->WriteItemHeader( $lang );
              echo "\n <table border=0 class='EditTable'>";
              echo "\n<tr><td><b>".$this->Msg->show_text('FLD_IMG_ALT').":</b></td>";
              echo "\n<tr><td>";
              if(is_array($meta) AND isset($meta[$lang_id]['name']))
                $name = $meta[$lang_id]['name'];
              else
                $name='';
              if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->img_title[$lang_id] : $val=$name;
              else $val=$this->img_title[$lang_id];              
              $this->Form->TextBox( 'img_title['.$row['id'].']['.$lang_id.']', stripslashes($val), 60 );
              
              echo "\n<tr><td><b>".$this->Msg->show_text('FLD_IMG_TITLE').":</b></td>"; 
              echo "\n<tr><td>";                                 
              if(is_array($meta) AND isset($meta[$lang_id]['descr']))
                $name = $meta[$lang_id]['descr'];
              else
                $name='';
              if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->img_descr[$lang_id] : $val=$name;
              else $val=$this->img_descr[$lang_id];              
              //$this->Form->HTMLTextArea( 'img_descr['.$row['id'].']['.$lang_id.']', stripslashes($val), 5, 50  ); 
              $this->Form->TextArea( 'img_descr['.$row['id'].']['.$lang_id.']', stripslashes($val), 4, 50  );
              echo "\n</table>";
              $Panel->WriteItemFooter();                     
            }
            $Panel->WritePanelFooter();
            ?>          
            <tr>
             <td align=left><b><?=$this->multi['FLD_SHOW']?>:</b><INPUT class='checkbox' TYPE=checkbox NAME='id_img_show[]' VALUE="<?=$row['id']?>" <?if ($row['show']=='1') echo 'CHECKED';?>></TD>
            </tr>
           <? 
             
            ?>       
            <tr>
             <td><b>
             <? if( $i!=($rows-1) or $up!=0 )  {
              echo $this->multi['FLD_DISPLAY'];?>:</b>
             <? }
              if( $up!=0 )
              {
               ?>
                <a href=<?=$this->script?>&task=up_img&move=<?=$row['move']?>&id=<?=$this->id?>>
                <?=$this->Form->ButtonUp( $row['id'] );?>
                </a>
               <?
              }
              if( $i!=($rows-1) )
              {
               ?>
                 <a href=<?=$this->script?>&task=down_img&move=<?=$row['move']?>&id=<?=$this->id?>>
                 <?=$this->Form->ButtonDown( $row['id'] );?>
                 </a>
               <?
              }
              $up=$row['id'];
              $a=$a-1;
              ?>   
             </td>
            </tr>                
           </table>
          </TD>
         </TR>
        </TABLE>
        <?=AdminHTML::PanelSimpleF();?>
       </TD>
       <?
     }
     ?>
      </TR>
      <TR>
       <TD colspan=2>
        <?if ( $rows>0 ){
          ?><?=$this->Form->Button('updimg',$this->Msg->show_text('TXT_SAVE'));?>
            <?=$this->Form->Button('delimg',$this->Msg->show_text('TXT_DELETE'));
          }?>
          <?=$this->Form->Button('cancel',$this->multi['TXT_CANCEL']);?> 
       </TD>
      </TR>
      <TR>
       <TD colspan=2>
       <br /><br />
        <input type="hidden" name="MAX_FILE_SIZE" value="2000000">
        <INPUT TYPE="file" NAME="filename[]" " size="80" VALUE="<?=$this->img['name']['0']?>">
        <br>
        <INPUT TYPE="file" NAME="filename[]" " size="80" VALUE="<?=$this->img['name']['1']?>">
        <br>
        <INPUT TYPE="file" NAME="filename[]" " size="80" VALUE="<?=$this->img['name']['2']?>">
        <br>
        <INPUT TYPE="file" NAME="filename[]" " size="80" VALUE="<?=$this->img['name']['3']?>">            
        <br>
        <INPUT TYPE="file" NAME="filename[]" " size="80" VALUE="<?=$this->img['name']['4']?>">
        <br/><br/><?=$this->Form->Button('saveimg',$this->Msg->show_text('TXT_ADD_IMAGES'));?>
        
       </TD>
      </TR>
      <?
       echo "<input type=hidden name='task' value=''>";
       echo "<input type=hidden name='id' value='".$this->id."'>";
       echo "<input type=hidden name='module' value='".$this->module."'>";
       echo "<input type=hidden name='display' value='".$this->display."'>";
       echo "<input type=hidden name='start' value='".$this->start."'>";
       echo "<input type=hidden name='sort' value='".$this->sort."'>";
       echo "<input type=hidden name='fltr' value='".$this->fltr."'>";
       //echo "<input type=hidden name='fltr2' value='".$this->fltr2."'>";


      ?>          
      </FORM>
     </Table>
     <?
     AdminHTML::PanelSimpleF();
     AdminHTML::PanelSubF();
     return true;
    } // end of function ShowImagesBackEnd()
    
             
   function get($cod)
   {
       $tmp_bd1= DBs::getInstance();
      $q11="select * from `".TblModArticleImgSpr."` where `cod`='".$cod."'";
     $res1 = $tmp_bd1->db_query( $q11);
//     echo $q11;
     if( !$tmp_bd1->result ) return false;
     $rows11 = $tmp_bd1->db_GetNumRows();
     $meta= '';
//     echo '<br />rows='.$rows11;
     for($i=0; $i<$rows11;$i++)
     {
        $row11 = $tmp_bd1->db_FetchAssoc();
        $meta[$row11['lang_id']]=$row11;
     }    
     return $meta;
   }
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
        $title=$this->Msg->show_text('MSG_ERRORS');
        echo '
        <fieldset class="err" title="'.$title.'"> <legend>'.$title.'</legend>
        <div class="err_text">'.$this->Err.'</div>
        </fieldset>';
     }
   } //end of fuinction ShowErrBackEnd()


    /**
    * Class method ImportEdifierArticles
    * function for import data from old Edifier News to new
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 08.10.2011
    * @return true or false
    */
    function ImportEdifierArticles()
    {
        define("TblModArticleEd", "mod_article_ed");
        define("TblModArticleEdSprSbj", "mod_article_ed_name");
        define("TblModArticleEdSprShort", "mod_article_ed_short");
        define("TblModArticleEdSprFull", "mod_article_ed_full");

        
        
        // достаю все старые статьи
        $q = "SELECT 
              `".TblModArticleEd."`.*,
              `".TblModArticleEdSprSbj."`.`name` AS `name`,
              `".TblModArticleEdSprShort."`.`name` AS `short`,
              `".TblModArticleEdSprFull."`.`name` AS `full` 
              FROM `".TblModArticleEd."`
              LEFT JOIN `".TblModArticleEdSprSbj."` ON (`".TblModArticleEd."`.`id`=`".TblModArticleEdSprSbj."`.`cod` AND `".TblModArticleEdSprSbj."`.`lang_id`='3')
              LEFT JOIN `".TblModArticleEdSprShort."` ON (`".TblModArticleEd."`.`id`=`".TblModArticleEdSprShort."`.`cod` AND `".TblModArticleEdSprShort."`.`lang_id`='3')
              LEFT JOIN `".TblModArticleEdSprFull."` ON (`".TblModArticleEd."`.`id`=`".TblModArticleEdSprFull."`.`cod` AND `".TblModArticleEdSprFull."`.`lang_id`='3') 
              WHERE 1
              ORDER BY `".TblModArticleEd."`.`position` asc";
        $res = $this->db->db_Query( $q );
        echo '<br />$q='.$q.' $this->db->result='.$this->db->result;
        if( !$res OR !$this->db->result) return false;
        $rows = $this->db->db_GetNumRows();
        $arr_prod = array();
        for($i=0;$i<$rows;$i++){
            $arr_prod[$i] = $this->db->db_FetchAssoc();

        }
        
        $cnt_prod = count($arr_prod);
        echo '<br>$cnt_prod=',$cnt_prod;
        for($i=0;$i<$cnt_prod;$i++){
            echo '<br>===== START OF '.$i.' =====';
            $row = $arr_prod[$i];
            
            $q = "SELECT 
                  `".TblModArticle."`.*,
                  `".TblModArticleTxt."`.`name`
                  FROM `".TblModArticle."`
                  LEFT JOIN `".TblModArticleTxt."` ON (`".TblModArticle."`.`id`=`".TblModArticleTxt."`.`cod` AND `".TblModArticleTxt."`.`lang_id`='3')
                  WHERE `dttm`='".$row['dttm']."' AND `".TblModArticleTxt."`.`name`='".addslashes($row['name'])."'
                 "; 
            $res = $this->db->db_Query( $q );
            echo '<br />$q='.$q.' $this->db->result='.$this->db->result;
            if( !$res OR !$this->db->result) return false;
            $rows = $this->db->db_GetNumRows();
            echo '<br>$rows='.$rows;
            if($rows>0) continue;
            
            //=== Prepare input data Start ===
            $old_id_article = $row['id'];
            $this->id = NULL;
            $this->category = $row['category'];
            $this->status = $row['status'];
            $this->dttm = $row['dttm'];
            $this->img = $row['img'];
            $this->position = $row['position'];
            
            $this->name[3] = $row['name'];
            $this->title[3] = '';
            $this->keywords[3] = '';
            $this->description[3] = '';
            $this->short[3] = str_replace("http://edifier.com.ua/images/", "/images/", $row['short']);
            $this->full[3] = str_replace("http://edifier.com.ua/images/", "/images/", $row['full']);
            //=== Prepare input data End ===
            
            $res = $this->save();
            echo '<br>SAVE = '.$res;
            if(!$res) continue;
            
            if(strstr( strtolower($this->short[3]), "<img")){
                $img_big = trim(strip_tags(stripslashes($this->short[3]),'<img>'));
                echo '<br>$img_big2='.$img_big;
                $tmp1 = substr($img_big, (strpos($img_big, 'src')+5) );
                $img_rel_path = substr($tmp1, 0, strpos($tmp1, '"') );
            }
            else $img_rel_path = '';
            echo '<br>$img_rel_path='.$img_rel_path;
            
            if(!empty($img_rel_path)){
                $ext = substr($img_rel_path,1 + strrpos($img_rel_path, "."));
                $imgNameNoExt = substr($img_rel_path, 1 + strrpos($img_rel_path, "/"));
                $imgNameNoExt = substr($imgNameNoExt, 0, strrpos($imgNameNoExt, "."));
                $filename = SITE_PATH.$img_rel_path;
                $alias = $this->id;
                $uploaddir = SITE_PATH.$this->settings['img_path'].'/'.$alias;
                $uploaddir_0 =$uploaddir; 
                if ( !file_exists ($uploaddir) ) mkdir($uploaddir,0777); 
                else @chmod($uploaddir,0777);
    			//$uploaddir2 = time().$i.'.'.$ext;
                $Crypt = &check_init('Crypt', 'Crypt');
                $uploaddir2 = $Crypt->GetTranslitStr($imgNameNoExt).'.'.$ext;
    			$uploaddir = $uploaddir."/".$uploaddir2; 
                //$uploaddir = $uploaddir."/".$_FILES['image']['name']["$i"];
                //$uploaddir2 = $_FILES['image']['name']["$i"];
                 
                echo '<br>$filename='.$filename.'<br> $uploaddir='.$uploaddir.'<br> $uploaddir2='.$uploaddir2;
                
                if ( @copy($filename,$uploaddir) ) {
                    //====== set next max value for move START ============
                    $maxx = NULL; 
                    $q = "SELECT MAX(`move`) AS `maxx` FROM `".TblModArticleImg."` WHERE 1";
                    $res = $this->db->db_Query( $q );
                    $row = $this->db->db_FetchAssoc();
                    $maxx = $row['maxx']+1;     
                    //====== set next max value for move END ==============
                     
                     $q="INSERT INTO `".TblModArticleImg."` values(NULL,'".$this->id."','".$uploaddir2."','1', '".$maxx."', NULL)";
                     $res = $this->db->db_Query( $q );
                     if( !$this->db->result ) $this->Err = $this->Err.$this->multi['MSG_ERR_SAVE_FILE_TO_DB'].' ('.$img_rel_path.')<br>';
                     echo '<br>q='.$q.' res='.$res.' $this->db->result='.$this->db->result;
                }
                
                @chmod($uploaddir_0,0755);
            }
            echo '<br>===== END OF '.$i.' =====';                        
        }
    }//end of function ImportEdifierArticles()       

} // end of ArticleCtrl Class
?>