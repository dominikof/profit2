<?
/**
* NewsCtrl.class.php
* Class definition for News module
* @package Package of SEOCMS
* @author Igor Trokhymchuk  <ihor@seotm.com>
* @version 1.1, 08.10.2011
* @copyright (c) 2010+ by SEOTM
*/

 include_once( SITE_PATH.'/modules/mod_news/news.defines.php' );

/**
* Class PageUser
* Class definition for News module
* @author Igor Trokhymchuk  <ihor@seotm.com>
* @version 1.1, 08.10.2011
*/
class NewsCtrl extends News{

    var $Right;
    var $Form;
    var $Msg;
    var $Spr;

    var $display;
    var $sort;
    var $start;

    var $user_id;
    var $module;

    var $id_category;
    var $subj_;
    var $short_;
    var $full_;
    var $status;
    var $start_date;
    var $end_date;
    var $display1;
    var $id_relart = NULL;

    var $fltr;    // filter of group news

    var $width;

    var $img = NULL;
    var $search_keywords =NULL;
    var $category = NULL;
    var $sel = NULL;
    var $is_tags = NULL;


    /**
    * Class Constructor
    * Set the variabels
    * @return true/false
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 08.10.2011
    */
    function __construct($user_id = NULL, $module = NULL)
    {
        if(defined("_LANG_ID")) $this->lang_id = _LANG_ID;

        $this->user_id = $user_id;
        $this->module = $module;

        $this->db =  DBs::getInstance();
        $this->Right =  &check_init('RightsNews', 'Rights', "'".$this->user_id."','".$this->module."'");
        $this->Form = &check_init('FormNews', 'Form', "'mod_pages'");
        $this->ln_sys = &check_init('SysLang', 'SysLang');
        $this->ln_arr = $this->ln_sys->LangArray( $this->lang_id );
        if (empty($this->Msg)) $this->Msg = &check_init('ShowMsg', 'ShowMsg');
        if (empty($this->Spr)) $this->Spr = &check_init('SysSpr', 'SysSpr');

        $this->width = '850';

        //echo '$this->lang_id ='.$this->lang_id;
        ( defined("USE_TAGS")                  ? $this->is_tags = USE_TAGS                     : $this->is_tags=0 ); // использовать тэги

        $this->multi = &check_init_txt('TblBackMulti',TblBackMulti);
        $this->settings = $this->GetSettings(false);

        $this->AddTable();

    } //end of constructor NewsCtrl

    /**
    * Class method AddTable
    * function for dynamic modifications structure of tables
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.1, 08.10.2011
    * @return true or false
    */
    function AddTable()
    {
        $tmp_db = DBs::getInstance();

        if( defined("DB_TABLE_CHARSET")) $this->tbl_charset = DB_TABLE_CHARSET;
        else $this->tbl_charset = 'utf8';

       // add field id_group to the table settings
       if ( !$this->db->IsFieldExist(TblModNews, "source") ) {
           $q = "ALTER TABLE `".TblModNews."` ADD source char(255) NOT NULL default '';";
           $res = $this->db->db_Query( $q );
         //  echo '<br>$q='.$q.' $res='.$res;
           if( !$res )return false;
       }

       // create table for strore individual name of category
       $q = "
        CREATE TABLE IF NOT EXISTS `".TblModNewsRelatProd."` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `id_news` int(10) unsigned NOT NULL DEFAULT '0',
          `id_prod` int(10) unsigned NOT NULL DEFAULT '0',
          PRIMARY KEY (`id`),
          KEY `id_news` (`id_news`,`id_prod`)
        ) ENGINE=MyISAM DEFAULT CHARSET=".$this->tbl_charset.";
        ";
       $res = $tmp_db->db_Query( $q );
       //echo '<br>$q='.$q.' $res='.$res;
       if( !$res )return false;
    } // end  of function AddTable

    /**
    * Class method show
    * function for show list of data
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.1, 08.10.2011
    * @return true or false
    */
    function show()
    {
        //$this->perevod();
        $frm = new Form('fltr');
        $script = 'module='.$this->module.'&display='.$this->display.'&start='.$this->start.'&sort='.$this->sort.'&fltr='.$this->fltr.'&fltr2='.$this->fltr2.'&fln='.$this->fln;
        $script = $_SERVER['PHP_SELF']."?$script";

        if( !$this->sort ) $this->sort='display';
        //if( strstr( $this->sort, 'display' ) )$this->sort = $this->sort.' desc';
        $q = "SELECT * FROM ".TblModNews." where 1 ";
        if( $this->fltr=='id_category' )  $this->fltr = 'id_category='.$this->id_category;
        if( $this->fltr ) $q = $q." and $this->fltr";
        if( $this->fltr2 ) $q = $q." and `status`='".$this->fltr2."'";
        $q = $q." order by $this->sort desc";

        $res = $this->Right->Query( $q, $this->user_id, $this->module );
        //echo "<br /> q = ".$q;
        if( !$res )return false;

        $rows = $this->Right->db_GetNumRows();
        $up = 0;
        $down = 0;
        $id = 0;
        $a = $rows;
        $j = 0;
        $row_arr = NULL;
        $arr_visible_news = '';
        for( $i = 0; $i < $rows; $i++ )
        {
           $row = $this->Right->db_FetchAssoc();
           if( $i >= $this->start && $i < ( $this->start+$this->display ) )
           {
             $row_arr[$j] = $row;
             if(empty($arr_visible_news)) $arr_visible_news = $row['id'];
             else $arr_visible_news .= ','.$row['id'];
             $j = $j + 1;
           }
        }

        if ( isset($this->settings['relat_prod']) AND $this->settings['relat_prod']=='1' ) {
            $arrRelatProd = array();
            if( !empty($arr_visible_news)){
                $q00 = "SELECT
                      `".TblModNewsRelatProd."`.*,
                      `".TblModCatalogPropSprName."`.`name`
                      FROM `".TblModNewsRelatProd."`, `".TblModCatalogPropSprName."`
                      WHERE `id_news` IN (".$arr_visible_news.")
                      AND `".TblModNewsRelatProd."`.`id_prod`=`".TblModCatalogPropSprName."`.`cod`";
                $res00 = $this->db->db_Query( $q00 );
                //echo '<br>$q00='.$q00.' $res00='.$res00;
                $rows00 = $this->db->db_GetNumRows();
                for( $i = 0; $i < $rows00; $i++ ){
                    $row00 = $this->Right->db_FetchAssoc();
                    $arrRelatProd[$row00['id_news']][] = stripslashes($row00['name']);
                }
            }
            //echo '<br>$arrRelatProd=';print_r($arrRelatProd);
        }

        /* Write Form Header */
        $this->Form->WriteHeader( $script );

        /* Write Table Part */
        AdminHTML::TablePartH();

        /* Write Links on Pages */
        echo '<TR><TD COLSPAN=13>';
        $script1 = 'module='.$this->module.'&fltr='.$this->fltr.'&fltr2='.$this->fltr2;
        $script1 = $_SERVER['PHP_SELF']."?$script1";

        $script2 = 'module='.$this->module.'&display='.$this->display.'&start='.$this->start.'&task=show&fltr='.$this->fltr.'&fltr2='.$this->fltr2;
        $script2 = $_SERVER['PHP_SELF']."?$script2";

        $this->Form->WriteLinkPages( $script1, $rows, $this->display, $this->start, $this->sort );

        if ( isset($this->settings['subscr']) AND $this->settings['subscr']=='1' ) {
        ?>
         <tr>
          <td colspan="4"></td>
          <td colspan="6" style="padding:5px; background-color:#D0D0D0;">
           <h3 style="margin:0px; paddinf:0px;"><?=$this->Msg->show_text('TXT_DISPATCH_SETTINGS');?></h4>
             <?
             $tmp_db = DBs::getInstance();
             $q = "SELECT * FROM `".TblModNewsDispatch."` WHERE 1 AND `is_partner`='0'";
             $res = $tmp_db->db_Query($q);
             $cnt_news_to_dispatch = $tmp_db->db_GetNumRows();
             if($cnt_news_to_dispatch>0){
                 $q = "SELECT * FROM `".TblModNewsSubscr."` WHERE 1 AND `user_status`='1'";
                 $res = $tmp_db->db_Query($q);
                 $cnt_subscr = $tmp_db->db_GetNumRows();
                 $q = "SELECT * FROM `".TblModNewsSubscr."` WHERE 1 AND `user_status`='1' AND `is_send`='0'";
                 $res = $tmp_db->db_Query($q);
                 $cnt_not_send = $tmp_db->db_GetNumRows();
                 ?>
                 <span class="not_href"><?=$this->Msg->show_text('TXT_DISPATCH_NOW_SENT_TO_1');?> <?=($cnt_subscr-$cnt_not_send);?> <?=$this->Msg->show_text('TXT_DISPATCH_NOW_SENT_TO_2');?> <?=$cnt_subscr;?>&nbsp;<?=$this->Msg->show_text('TXT_DISPATCH_NOW_SENT_TO_3');?></span>
                 <br/><input type="button" value="<?=$this->Msg->show_text('TXT_DISPATCH_STOP');?>" onclick="if( !window.confirm('<?=$this->Msg->show_text('TXT_DISPATCH_WARNING2');?>') ) return false; else <?=$this->Form->name;?>.task.value='stop_dispatch';<?=$this->Form->name;?>.submit();">
                 </div>
                 <?
             }
             if($cnt_news_to_dispatch==0) {
                 ?>
                 <span class="not_href"><?=$this->Msg->show_text('TXT_DISPATCH_TITLE');?>:</span><input type="text" name="dispatch_sbj" value="<?=stripslashes($this->dispatch_sbj);?>" size="40">
                 <br/><input type="button" value="<?=$this->Msg->show_text('TXT_DISPATCH_CREATE_IT');?>" onclick="if( !window.confirm('<?=$this->Msg->show_text('TXT_DISPATCH_WARNING1');?>') ) return false; else <?=$this->Form->name;?>.task.value='news_posting_arr';<?=$this->Form->name;?>.submit();">
                 <?
             }
             ?>
             <br/>
          </td>
         </tr>

         <?/*<li><a href="javascript:<?=$this->Form->name;?>.task.value='subscr_send';<?=$this->Form->name;?>.submit();" onclick="if( !window.confirm('Вы точно хотите запустить рассылку отмеченных новостей?') ) return false;"><?=$this->Msg->show_text('TXT_SUBSCRIBE_SEND');?></a></li>*/?>
        <?
        } // end if settings

        echo '<TR><TD COLSPAN=3>';
        $this->Form->WriteTopPanel( $script );
        echo '<td CLASS="TR1" align="center">'.$this->Msg->show_text('_FLD_NEWS_CATEGORY_FLTR')." ";
        $arr = NULL;
        $arr[''] = $this->multi['TXT_NEWS_ALL_CATEGORIES'];
        $q = "select * from ".TblModNewsCat." where 1 and lang_id="._LANG_ID;
        $res = $this->Right->Query( $q, $this->user_id, $this->module );
        //echo '<br>$q='.$q.' $res='.$res;
        $rows1 = $this->Right->db_GetNumRows();
        for( $i = 0; $i < $rows1; $i++ )
        {
            $row1 = $this->Right->db_FetchAssoc();
            $arr['id_category='.$row1['cod']] = stripslashes($row1['name']); //$this->Spr->GetNameById( TblModNewsCat, $row1['id'] );
        }
        $this->Form->SelectAct( $arr, 'id_category', $this->fltr, "onChange=\"location='$script'+'&fltr='+this.value\"" );
        echo '<TD align="left">'; //<a href="'.$script1.'&task=subscr_send">'.$this->Msg->show_text('TXT_SUBSCRIBE_SEND').'</a>'; ;
         // set rss import
        if ( isset($this->settings['rss_import']) AND $this->settings['rss_import']=='1' ) {
          ?>
          <li><a href="javascript:<?=$this->Form->name;?>.task.value='read_rss';<?=$this->Form->name;?>.submit();"><?=$this->Msg->show_text('TXT_RSS_IMPORT');?></a></li>
          <?
        } // end if settings rss inport

        if ( isset($this->settings['top_news']) AND $this->settings['top_news']=='1' ) {
            ?><td></td><?
        }
        if ( isset($this->settings['newsline']) AND $this->settings['newsline']=='1' ) {
            ?><td></td><?
        }

        echo '<td CLASS="TR1" align="center">';
        $arr = '';
        $arr[''] = $this->multi['TXT_NEWS_ALL_STATUSES'];
        $arr['a'] = $this->multi['TXT_STATUS_ACTIVE'];
        $arr['e'] = $this->multi['TXT_STATUS_EXPIRED'];
        $arr['i'] = $this->multi['TXT_STATUS_INACTIVE'];
        $this->Form->SelectAct( $arr, 'status', $this->fltr2, "onChange=\"location='$script'+'&fltr2='+this.value\"" );
        echo '</td>';
        echo '<td colspan=2>';
        if($rows>$this->display) $ch = $this->display;
        else $ch = $rows;

    ?>
     <TR>
     <th class="THead"><input value="0" id="cAll" onclick="if (this.value == '1') {unCheckAll(<?=$ch;?>); this.value = '0';} else {checkAll(<?=$ch;?>); this.value = '1';}" type="checkbox"></Th>
     <th class="THead"><?$this->Form->LinkTitle($script2.'&sort=id', $this->Msg->show_text('_FLD_ID'));?></Th>
     <th class="THead"><?$this->Form->LinkTitle($script2.'&sort=subject', $this->Msg->show_text('_FLD_NEWS_SUBJECT'));?></Th>
     <th class="THead"><?$this->Form->LinkTitle($script2.'&sort=id_category', $this->Msg->show_text('_FLD_NEWS_CATEGORY'));?></Th>
     <?if( isset($this->settings['img']) AND $this->settings['img']==1){?>
     <th class="THead"><?=$this->Msg->show_text('_FLD_IMG')?></Th>
     <?}?>
     <?if( isset($this->settings['top_news']) AND $this->settings['top_news']==1){?>
     <th class="THead"><?=$this->Msg->show_text('TXT_TOP')?></Th>
     <?}?>
     <?if( isset($this->settings['newsline']) AND $this->settings['newsline']==1){?>
     <th class="THead"><?=$this->Msg->show_text('TXT_NEWS_LINE')?></Th>
     <?}?>
     <th class="THead"><?$this->Form->LinkTitle($script2.'&sort=status', $this->Msg->show_text('_FLD_NEWS_STATUS'));?></Th>
     <?if ( isset($this->settings['relat_prod']) AND $this->settings['relat_prod']=='1' ) {?>
     <th class="THead"><?=$this->Msg->show_text('TXT_PRODUCTS_TO_NEWS');?></Th>
     <?}?>
     <?if( isset($this->settings['dt']) AND $this->settings['dt']==1){?>
     <th class="THead"><?$this->Form->LinkTitle($script2.'&sort=start_date', $this->Msg->show_text('_FLD_NEWS_STARTDATE'));?></Th>
     <th class="THead"><?$this->Form->LinkTitle($script2.'&sort=end_date', $this->Msg->show_text('_FLD_NEWS_ENDDATE'));?></Th>
     <?}?>
     <td class="THead"><?$this->Form->LinkTitle($script2.'&sort=display', $this->Msg->show_text('_FLD_NEWS_DISPLAY'));?></Th>
    <?

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
       $this->Form->Link( $this->script."&task=edit&id=".$row['id'], stripslashes( $row['id'] ), $this->multi['TXT_EDIT'] );

       echo '<TD align=center>';
       $this->Form->Link( $script."&task=edit&id=".$row['id'], stripslashes($this->Spr->GetNameByCod( TblModNewsSprSbj, $row['id'] )), $this->Msg->show_text('_TXT_EDIT_DATA') );
       //echo '<TD align=center>';
       //if( trim( $this->Spr->GetNameByCod( TblModNewsSprShrt, $row['id'] ) )!='' ) $this->Form->ButtonCheck();

       //echo '<TD align=center>';
       //if( trim( $row['full'] )!='' ) $this->Form->ButtonCheck();

       echo '<TD align=center>';
       $id_category = $row['id_category'];
       //$category = $this->Category->GetCategoryNameById($row['id_category']);
       $category = $this->Spr->GetNameByCod( TblModNewsCat, $id_category );
       $this->Form->Link( $script."&task=show&fltr=id_category=$id_category", $category, $this->Msg->show_text('_TXT_FLT_DATA') );

        if( isset($this->settings['img']) AND $this->settings['img']==1){
        echo '<td align="center">';
        $img = $this->GetMainImage($row['id'], 'back');
              if ( !empty($img)) {
                ?><a href="<?=$script?>&task=showimages&id=<?=$row['id'];?>"><?=$this->ShowImage( $img, $row['id'], 'size_width=75', 100, NULL, "border=0");?>
                <br><?=$this->Msg->show_text('TXT_ADD_EDIT_IMAGES').'['.$this->GetImagesCount($row['id']).']';?></a><?
              }
              else {?><a href="<?=$script?>&task=showimages&id=<?=$row['id'];?>"><?=$this->Msg->show_text('TXT_ADD_EDIT_IMAGES').'['.$this->GetImagesCount($row['id']).']';?></a><?}


	    echo '</td>';
        }
        if( isset($this->settings['top_news']) AND $this->settings['top_news']==1){
        echo '<td align="center">';
        switch($row['top']){
            case '1':
                echo '<img src="images/icons/tick.png">';
                if($row['top_main']) {
                    ?><br/><b><?=$this->Msg->show_text('TXT_NEWS_TOP_MAIN')?></b><?
                }
                break;
            case '0':
                echo '<img src="images/icons/publish_x.png">';
                break;
        }

        echo '</td>';}
        if( isset($this->settings['newsline']) AND $this->settings['newsline']==1){
        echo '<td align="center">';
        switch($row['line']){
            case '1':
                echo '<img src="images/icons/tick.png">';
                break;
            case '0':
                echo '<img src="images/icons/publish_x.png">';
                break;
        }
        echo '</td>';
        }
       echo '<TD align=center>';
       if( $row['status'] =='i') echo $this->multi['TXT_STATUS_INACTIVE'];
       if( $row['status'] =='e') echo $this->multi['TXT_STATUS_EXPIRED'];
       if( $row['status'] =='a') echo $this->multi['TXT_STATUS_ACTIVE'];

       if ( isset($this->settings['relat_prod']) AND $this->settings['relat_prod']=='1' ) {
           ?><td align="center"><?
           if(isset($arrRelatProd[$row['id']])){
               $cnt_prod = count($arrRelatProd[$row['id']]);
               //print_r($arrRelatProd[$row['id']]);
               for($i_prod=0;$i_prod<$cnt_prod;$i_prod++){
                    echo $arrRelatProd[$row['id']][$i_prod].'<br/>';
               }
           }
           ?></td><?
       }
       if( isset($this->settings['dt']) AND $this->settings['dt']==1){
        echo '<TD align=center>'.$row['start_date'].'</TD>';
        echo '<TD align=center>'.$row['end_date'].'</TD>';
       }
       echo '<TD align=center>';
       if( $up!=0 )
       {
       ?>
        <a href=<?=$script?>&task=up&move=<?=$row['display']?> ><?=$this->Form->ButtonUp( $row['id'] );?></a>
       <?
       }

       if( $i!=($rows-1) )
       {
       ?>
         <a href=<?=$script?>&task=down&move=<?=$row['display']?>><?=$this->Form->ButtonDown( $row['id'] );?></a>
       <?
       }

       $up=$row['id'];
       $a=$a-1;
     } //-- end for

     AdminHTML::TablePartF();
     $this->Form->WriteFooter();
     return true;
    }



// ================================================================================================
// Function : edit()
// Date : 04.02.2005
// Parms :
//                 $id   / id of editing record / Void
//                 $mas  / array of form values
// Returns : true,false / Void
// Description : edit/add records in News module
// Programmer : Yaroslav Gyryn
// ================================================================================================
function edit( $id, $mas=NULL )
{
 $this->settings = $this->GetSettings(false);
 $Panel = new Panel();
 $ln_sys = new SysLang();
 $calendar = new DHTML_Calendar(false, 'en', 'calendar-win2k-2', false);
 $calendar->load_files();
 $this->id = $id;
 $fl = NULL;
 if( $mas )
    $fl = 1;

 $script = 'module='.$this->module.'&display='.$this->display.'&start='.$this->start.'&sort='.$this->sort.'&fltr='.$this->fltr;
 $script = $_SERVER['PHP_SELF']."?$script";

 if( $id!=NULL and ( $mas==NULL ) )
 {
   $q = "SELECT * FROM ".TblModNews." where id='$id'";
   $res = $this->Right->Query( $q, $this->user_id, $this->module );
   //echo '<br/>',$q.'<br/>'.$res;
   if( !$res ) return false;
   $mas = $this->Right->db_FetchAssoc();
 }

 /* Write Form Header */
 $this->Form->WriteHeaderFormImg( $script );
 $settings=SysSettings::GetGlobalSettings();
 $this->Form->textarea_editor = $settings['editer']; //'tinyMCE';
 $this->Form->IncludeSpecialTextArea( $this->Form->textarea_editor );
 if( $id!=NULL ) $txt = $this->Msg->show_text('_TXT_EDIT_DATA');
 else $txt = $this->Msg->show_text('_TXT_ADD_DATA');

 AdminHTML::PanelSubH( $txt );
 $this->Form->ShowErrBackEnd($this->Err);
 AdminHTML::PanelSimpleH();
 ?>
 <tr>
  <td>
   <table>
    <tr>
      <td valign="top" align="left" width="170">
         <?=$this->Msg->show_text('_FLD_ID')?>: <?
           if( $id!=NULL )
           {
            echo $mas['id'];
            $this->Form->Hidden( 'id', $mas['id'] );
           }
           else $this->Form->Hidden( 'id', '' );

          if($id!=NULL ) $this->Err!=NULL ? $top = $this->top : $top=$mas['top'];
           else $top=0;
           if($top==1) $checked='checked';
           else $checked='';
           ?><br/>
           <br/>
           <script type="text/javascript">
           // Функция для отображения блока топ новости
            function show(id)
            {
                if (document.getElementById(id).style.display == "none") {
                    document.getElementById(id).style.display = "block";
                    if(document.getElementById('top1')!=null) document.getElementById('top1').style.display = "block";
                    if(document.getElementById('top2')!=null) document.getElementById('top2').style.display = "block";
                    if(document.getElementById('top3')!=null) document.getElementById('top3').style.display = "block";
                }
                else {
                    document.getElementById(id).style.display = "none";
                    if(document.getElementById('top1')!=null) document.getElementById('top1').style.display = "none";
                    if(document.getElementById('top2')!=null) document.getElementById('top2').style.display = "none";
                    if(document.getElementById('top3')!=null) document.getElementById('top3').style.display = "none";
                }
            }
           </script>
           <?
           if( isset($this->settings['top_news']) AND $this->settings['top_news']==1){?>
           <input type="checkbox" onchange="show('top');" name="top" align="left" <?=$checked;?>> <b><?=$this->Msg->show_text('TXT_TOP')?></b><br/>

           <?if($id!=NULL ) $this->Err!=NULL ? $topMain = $this->topMain : $topMain=$mas['top_main'];
           else $topMain=0;
           if($topMain==1) $checked='checked';
           else $checked='';
           // ТОП
            if($top==1)
                $style ='block;';
            else
                $style ='none;';
            ?>
           <div id="top" style="display: <?=$style;?>">
                <input type="checkbox" name="topMain" align="left" <?=$checked;?>>  <b><?=$this->Msg->show_text('TXT_NEWS_TOP_MAIN')?>
           </div>
           <?if($id!=NULL ) $this->Err!=NULL ? $line = $this->line : $line=$mas['line'];
           else $line=0;
           if($line==1) $checked='checked';
           else $checked='';
           }

           if( isset($this->settings['newsline']) AND $this->settings['newsline']==1){?>
           <input type="checkbox" name="line" align="left" <?=$checked;?> />  <b><?=$this->Msg->show_text('TXT_NEWS_LINE')?></b>
           <br/><br/><?}?>
           <?if( isset($this->settings['img']) AND $this->settings['img']==1){?>
         <div align="center">
          <?
          $img = $this->GetMainImage($mas['id'], 'back');
          if ( !empty($img) ) {
             $this->Form->Hidden( 'pic', $img );
             $arr = $this->GetImagesToShow($mas['id']);
             ?><a href="<?=$script?>&task=showimages&id=<?=$mas['id'];?>"><?=$this->ShowImage( $img, $mas['id'], 'size_width=100', 100, NULL, "border=0");?>
             <br><?=$this->Msg->show_text('TXT_ADD_EDIT_IMAGES2').'['.$this->GetImagesCount($mas['id']).']';?></a>
             <br><a href="<?=$script?>&task=qdelimg&id_img_del=<?=$arr[0]['id'];?>&id=<?=$mas['id']?>"><?=$this->Msg->show_text('TXT_DELETE')?></a>
             <?/*<a href="<?=$script?>&task=showimages&id=<?=$mas['id'];?>"><?=$this->Msg->show_text('TXT_ADD_EDIT_IMAGES2').'['.$this-> GetImagesCount($mas['id']).']';?></a>*/
          }
          ?>
         </div><?}?>
      </td>
      <td  valign="top">
       <table class="EditTable" width="100%">
        <tr>
         <td width="120"><b><?=$this->Msg->show_text('_FLD_NEWS_CATEGORY')?></b></td>
         <td>
          <?
          if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->id_category : $val=$mas['id_category'];
          else $val=$this->id_category;
          //echo '$this->id_category='.$this->id_category;
          //printf($val);
          $this->Spr->ShowInComboBox( TblModNewsCat, 'id_category',$val, 0 );
          ?>
         </td>
        </tr>
        <?if( isset($this->settings['ukraine_news']) AND $this->settings['ukraine_news']==1){?>
        <tr>
          <td><strong><?=$this->Msg->show_text('TXT_UKRAINE_NEWS');?></strong></td>
          <td><input type="checkbox" name="property" <? if($mas['property']) echo 'CHECKED';?> /></td>
        </tr><?}?>
        <tr>
         <td><b><?=$this->Msg->show_text('_FLD_NEWS_STATUS')?></b></td>
         <td valign="top">
          <?
          $arr = NULL;
          $arr['a'] = $this->Msg->show_text('TXT_STATUS_ACTIVE');
          $arr['e'] = $this->Msg->show_text('TXT_STATUS_EXPIRED');
          $arr['i'] = $this->Msg->show_text('TXT_STATUS_INACTIVE');
          if( !$mas['status'] ) $mas['status'] = 'a';
          if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->status : $val=$mas['status'];
          else $val=$this->status;
          $this->Form->Select( $arr, 'status', $val, NULL );
          ?>
          </td>
          </tr><?if( isset($this->settings['dt']) AND $this->settings['dt']==1){?>
          <tr>
            <td><b><?=$this->Msg->show_text('_FLD_NEWS_STARTDATE')?></b></td>
            <td><?
              if( $this->id!=NULL ) $this->Err!=NULL ? $start_date_val=$this->start_date : $start_date_val=$mas['start_date'];
              else $start_date_val=strftime('%Y-%m-%d %H:%M', strtotime('now'));
              //if( empty($start_date_val) ) $start_date_val = strftime('%Y-%m-%d %H:%M', strtotime('now'));
              $a1 = array('firstDay'       => 1, // show Monday first
                         'showsTime'      => true,
                         'showOthers'     => true,
                         'ifFormat'       => '%Y-%m-%d %H:%M',
                         'timeFormat'     => '12');
              $a2 = array('style'       => 'width: 15em; color: #840; background-color: #ff8; border: 1px solid #000; text-align: center',
                          'name'        => 'start_date',
                          'value'       => $start_date_val );
              //echo '<br>$a1='.$a1.' $a2='.$a2.' $start_date_val='.$start_date_val;
              $calendar->make_input_field( $a1, $a2 );?>
              <br/><?=$this->Msg->show_text('HLP_FLD_NEWS_DATE')?>
          </td>
          </tr>
          <tr>
            <td><b><?=$this->Msg->show_text('_FLD_NEWS_ENDDATE')?></b></td>
          <td><?
          if( $this->id!=NULL ) $this->Err!=NULL ? $end_date_val=$this->end_date : $end_date_val=$mas['end_date'];
          else $end_date_val=strftime('%Y-%m-%d %H:%M', strtotime('+30 days'));
          //echo '<br>$end_date_val='.$end_date_val;
          //if( empty( $end_date_val ) ) $end_date_val = strftime('%Y-%m-%d %H:%M', strtotime('+30 days'));
          $a1 = array('firstDay'       => 1, // show Monday first
                     'showsTime'      => true,
                     'showOthers'     => true,
                     'ifFormat'       => '%Y-%m-%d %H:%M',
                     'timeFormat'     => '12');
          $a2 = array('style'       => 'width: 15em; color: #840; background-color: #ff8; border: 1px solid #000; text-align: center',
                      'name'        => 'end_date',
                      'value'       => $end_date_val );
          $calendar->make_input_field( $a1, $a2 );?>
          <br/><?=$this->Msg->show_text('HLP_FLD_NEWS_DATE2')?>
        </td>
        </tr>
        <?}if( isset($this->settings['img']) AND $this->settings['img']==1){?>
        <tr>
         <td><b><?=$this->Msg->show_text('_FLD_IMG')?>:</b></td>
         <td><INPUT TYPE="file" NAME="filename[]" size="40" value="<?=$this->img;?>"></td>
        </tr><?}?>
     </table>
     </td>
     </tr>
     <?if ( isset($this->settings['relat_prod']) AND $this->settings['relat_prod']=='1' ) {?>
     <tr>
        <td valign="middle"><?=$this->multi['TXT_PRODUCTS_TO_NEWS'];?></td>
        <td>
        <?
         if( $this->id!=NULL ) $arr_relat_prop = $this->GetRelatProdToNews($this->id);
         else $arr_relat_prop = NULL;
         //print_r($arr_relat_prop);
         //else $prod_id = NULL;
         $Catalog = &check_init('Catalog', 'Catalog');
         $arr_categs = $Catalog->PrepareCatalogForSelect(0, NULL, NULL, 'back', true, true, false, false, NULL, NULL);
         $arr_props = $Catalog->PreparePositionsTreeForSelect('all', 'back', 'move', 'asc', NULL);
         $arr_prod['']='Выберите модель';
         $arr_prod = $Catalog->Spr->GetListName( TblModCatalogPropSprName, $this->lang_id, 'array', 'cod', 'asc', $return_data='all' );
         $cnt_prods = 5;
         for($i=0;$i<$cnt_prods;$i++){
             if( isset($arr_relat_prop[$i])) $val = 'curcod='.$arr_relat_prop[$i];
             else $val=NULL;
             //$this->Form->Select($arr_prod, 'prod_id['.$i.']', $val);
             $Catalog->ShowCatalogInSelect($arr_categs, $arr_props, '--- '.$this->multi['TXT_SELECT_POSITIONS'].' ---', 'arr_relat_prop[]', $val,'');
            ?><br/><?
         }
        ?>
        </td>
     </tr>
     <?}?>
    </table>
   </td>
  </tr>

    <?
     if ( $this->is_tags==1 ) {
        $Tags = new SystemTags($this->user_id, $this->module);
        if( $this->id!=NULL ) $this->Err!=NULL ? $id_tag=$this->id_tag : $id_tag=$Tags->GetTagsByModuleAndItem($this->module, $this->id);
        else $id_tag=$this->id_tag;
        //echo '<br>$id_tag='.$id_tag; print_r($id_tag);
        ?><tr><td valign="top"><?$Tags->ShowEditTags($id_tag);?></td></tr><?
     }
    ?>

 <tr>
  <td>
    <?
    $Panel->WritePanelHead( "SubPanel_" );

    $ln_arr = $ln_sys->LangArray( _LANG_ID );
    if( $id!=NULL ) {
        $q = "SELECT
                        `".TblModNewsTop."`. lang_id,
                        `".TblModNewsTop."`. name,
                        `".TblModNewsTop."`. short,
                        `".TblModNewsTop."`. image
                FROM
                        `".TblModNewsTop."`
                WHERE
                     ".TblModNewsTop.".cod = '$id'
       ";

       $res = $this->Right->Query( $q, $this->user_id, $this->module );
       $rows = $this->Right->db_GetNumRows();
       //echo '<br/>',$q.'<br/>'.$res;
       if( !$res ) return false;
       $topArr = array();
       for($i=0; $i<$rows; $i++) {
            $row = $this->Right->db_FetchAssoc();
            $topArr[$row['lang_id']]=$row;
        }
    }
    while( $el = each( $ln_arr ) ){
         $lang_id = $el['key'];
         $lang = $el['value'];
         $mas_s[$lang_id] = $lang;
         $Panel->WriteItemHeader( $lang );
         if( isset($this->settings['top_news']) AND $this->settings['top_news']==1){
             // ТОП
            ?>
            <div id="top<?=$lang_id;?>" style="display: <?=$style;?>">
                <fieldset style="border: 1px solid #4682B4; padding: 5px;">
                    <legend><b><?=$this->Msg->show_text('TXT_TOP')?></b ></legend>
                    <table  class="EditTable"  border="0" align="left">
                    <tr>
                    <?
                       if( $id!=NULL ) {
                            //$imgTop = $this->GetTopImage($mas['id']);
                            if ( !empty($topArr[$lang_id]['image']) ) {
                                ?><td width="260"><?
                                 echo $this->ShowImage( $topArr[$lang_id]['image'], $mas['id'], 'size_width=250', 85, NULL, "border=0");
                                 ?></td><?
                            }
                       }
                    ?>
                    <td>
                    <b><?=$this->Msg->show_text('FLD_TOP_SUBJECT')?>:</b><br><?
                        isset($topArr[$lang_id]['name']) ? $row = $topArr[$lang_id]['name'] :$row='' ;
                        if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->topSubject[$lang_id] : $val = $row;
                        else $val=$this->topSubject[$lang_id];
                        $this->Form->TextBox( 'topSubject['.$lang_id.']',  stripslashes($val), 60 );
                        echo "\n <br/>";

                        echo "\n <b>".$this->Msg->show_text('FLD_TOP_SHORT').":</b>";
                        echo "\n <br>";
                        isset($topArr[$lang_id]['name']) ? $row = $topArr[$lang_id]['short'] :$row='' ;
                        if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->topShort[$lang_id] : $val = $row;
                        else $val=$this->topShort[$lang_id];
                        $this->Form->TextArea( 'topShort['.$lang_id.']',  stripslashes($val), 6, 60 );
                        echo "\n <br>";
                       ?>
                       <b><?=$this->Msg->show_text('FLD_IMAGE_TOP')?>:</b><br/>
                       <INPUT TYPE="file" NAME="topImage[]" size="40" value="<?=$this->topImage;?>">
                       <br/><?=$this->Msg->show_text('FLD_IMAGE_TOP_HELP')?>
                        </td>
                        </tr>
                        </table>
                </fieldset>
                <br>
            </div>
            <?
            // End TOP
         }

        echo "\n <table border=0 class='EditTable' width='100%'>";
        echo "\n <tr>";
        echo "\n <td><b>".$this->Msg->show_text('_FLD_NEWS_SUBJECT').":</b>";
        echo "\n <br>";

        $row = NULL;
        if( isset( $mas['id'] ) ) $row = $this->Spr->GetByCod( TblModNewsSprSbj, $mas['id'], $lang_id );
        if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->subj_[$lang_id] : $val=$row[$lang_id];
        else $val=$this->subj_[$lang_id];
        $this->Form->TextBox( 'subject['.$lang_id.']',  stripslashes($val), 110 );
        echo "\n <br/>";
        echo "\n </tr>";

        echo "\n <tr><td><b>".$this->Msg->show_text('FLD_DECRIPTION').":</b>";
        echo "\n <br>";
        if( isset( $mas['id'] ) ) $row = $this->Spr->GetByCod( TblModNewsSprDescription, $mas['id'], $lang_id );
        if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->description[$lang_id] : $val=$row[$lang_id];
        else $val=$this->description[$lang_id];
        $this->Form->TextArea( 'description['.$lang_id.']',  stripslashes($val), 2, 110 );
        echo "\n <br>";

        echo "\n <tr><td><b>".$this->Msg->show_text('FLD_KEYWORDS').":</b>";
        echo "\n <br>";
        if( isset( $mas['id'] ) ) $row = $this->Spr->GetByCod( TblModNewsSprKeywords, $mas['id'], $lang_id );
        if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->keywords[$lang_id] : $val=$row[$lang_id];
        else $val=$this->keywords[$lang_id];
        $this->Form->TextArea( 'keywords['.$lang_id.']',  stripslashes($val), 2, 110 );
        echo "\n <br>";
        if( isset($this->settings['main_thing']) AND $this->settings['main_thing']==1){

        echo "\n <tr><td><b>".$this->Msg->show_text('FLD_MAIN_IN_NEWS').":</b>";
        echo "\n <br>";
        if( isset( $mas['id'] ) ) $row = $this->Spr->GetByCod( TblModNewsSprMain, $mas['id'], $lang_id );
        if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->main[$lang_id] : $val=$row[$lang_id];
        else $val=$this->main[$lang_id];
        $this->Form->TextArea( 'main['.$lang_id.']',  stripslashes($val), 7, 110 );
        echo "\n <br>";
            }
        if( isset($this->settings['short_descr']) AND $this->settings['short_descr']==1){

        echo "\n <tr>";
        echo "\n <td><br><b>".$this->Msg->show_text('_FLD_NEWS_SHORT').":</b>";
        echo "\n <br>";
        if( isset( $mas['id'] ) ) $row = $this->Spr->GetByCod( TblModNewsSprShrt, $mas['id'], $lang_id );
        if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->short_[$lang_id] : $val=$row[$lang_id];
        else $val=$this->short_[$lang_id];
        $this->Form->SpecialTextArea( $this->Form->textarea_editor, 'short['.$lang_id.']', stripslashes($val), 20, 80, 'class="contentInput"', $lang_id, 'short' );
        echo "\n <br>";
        }
        if( isset($this->settings['full_descr']) AND $this->settings['full_descr']==1){
        echo "\n <tr>";
        echo "\n <td><b>".$this->Msg->show_text('_FLD_NEWS_FULL').":</b>";
        echo "\n <br>";

        if( isset( $mas['id'] ) ) $row = $this->Spr->GetByCod( TblModNewsSprFull, $mas['id'], $lang_id );
        if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->full_[$lang_id] : $val=$row[$lang_id];
        else $val=$this->full_[$lang_id];
        $this->Form->SpecialTextArea( $this->Form->textarea_editor, 'full['.$lang_id.']',  stripslashes($val), 60, 80,  'class="contentInput"', $lang_id, 'full' );
        echo "\n <br>";
        }
        echo   "\n </table>";
        $Panel->WriteItemFooter();
    }
    $Panel->WritePanelFooter();

    if ( isset($this->settings['source']) AND $this->settings['source']=='1' ) {
        echo "\n <table border=0 class='EditTable'>";
        echo "\n <tr>";
        echo "\n <td><b>".$this->Msg->show_text('_TXT_SOURCE').":</b>";
        echo "\n <br>";
        if(isset($mas['source'])) $this->Form->TextBox( 'source',  stripslashes($mas['source']), 60 );
        else  $this->Form->TextBox( 'source',  "", 60 );
        echo "\n <br>";
        echo "\n </tr>";
        echo   "\n </table>";
    }

    ?>
  </td>
 </tr>

 <?
 AdminHTML::PanelSimpleF();
 $this->Form->WriteSaveAndReturnPanel( $script );?>&nbsp;<?
 $this->Form->WriteSavePanel( $script );?>&nbsp;<?
 $this->Form->WriteCancelPanel( $script );?>&nbsp;<?
 //$this->Form->WritePreviewPanel( 'http://'.NAME_SERVER."/modules/mod_news/news.preview.php" );
 $this->Form->WritePreviewPanelNewWindow( "http://".NAME_SERVER.$this->GetLink(NULL, NULL, $this->id) );
 AdminHTML::PanelSubF();
 $this->Form->WriteFooter();

 return true;
}

   // ================================================================================================
   // Function : CheckFields()
   // Date : 19.08.2008
   // Parms :        $id - id of the record in the table
   // Returns :      true,false / Void
   // Description :  Checking all fields for filling and validation
   // Programmer :  Igor Trokhymchuk
   // ================================================================================================
   function CheckFields()
   {
    $this->Err=NULL;
    if( isset($this->settings['top_news']) AND $this->settings['top_news']==1){
    if($this->top) {
        if(empty( $this->topSubject[_LANG_ID] ))
            $this->Err=$this->Err.$this->Msg->show_text('NEWS_TOP_SUBJECT_EMPTY').'<br>';
        if(empty( $this->topShort[_LANG_ID] ))
            $this->Err=$this->Err.$this->Msg->show_text('NEWS_TOP_SHORT_EMPTY').'<br>';
    }
     }
     if(empty($this->id_category)) {
         $this->Err=$this->Err.$this->Msg->show_text('NEWS_CATEGORY_EMPTY').'<br>';
     }
     if( isset($this->settings['dt']) AND $this->settings['dt']==1){
     if(empty($this->start_date)) {
         $this->Err=$this->Err.$this->Msg->show_text('MSG_NEWS_START_DATE_EMPTY').'<br>';
     }
     if(empty($this->end_date)) {
         $this->Err=$this->Err.$this->Msg->show_text('MSG_NEWS_END_DATE_EMPTY').'<br>';
     }
     /*
     if($this->start_date >= $this->end_date) {
         $this->Err=$this->Err.$this->Msg->show_text('MSG_NEWS_END_DATE_MUST_BE_OLDER').'<br>';
     }
     */
     }

     $tmp = explode( '-', $this->start_date );
     $tmp1 = explode( ' ', $tmp[2] );
     $tmp2 = explode( ':', $tmp1[1] );
     $start_d = $tmp[0].$tmp[1].$tmp1[0].$tmp2[0].$tmp2[1];

     $tmp = explode( '-', $this->end_date );
     $tmp1 = explode( ' ', $tmp[2] );
     $tmp2 = explode( ':', $tmp1[1] );
     $end_d = $tmp[0].$tmp[1].$tmp1[0].$tmp2[0].$tmp2[1];

     //echo '<br>$start_d='.$start_d.' $end_d='.$end_d.' $this->start_date='.$this->start_date.' $this->end_date='.$this->end_date;
     if( $start_d >= $end_d ){
         $this->Err=$this->Err.$this->Msg->show_text('NEWS_STARTDATE_ENDDATE_WRONG');
     }

     if(empty( $this->subj_[_LANG_ID] )) {
         $this->Err=$this->Err.$this->Msg->show_text('NEWS_SUBJECT_EMPTY').'<br>';
     }
     if( isset($this->settings['short_descr']) AND $this->settings['short_descr']==1){
     if(empty( $this->short_[_LANG_ID] )) {
         $this->Err=$this->Err.$this->Msg->show_text('NEWS_SHORT_EMPTY').'<br>';
     }}
     /*
     if( isset($this->settings['full_descr']) AND $this->settings['full_descr']==1){
     if (empty( $this->full_[_LANG_ID] )) {
         $this->Err=$this->Err.$this->Msg->show_text('NEWS_FULL_EMPTY').'<br>';
     }}
     */
     return $this->Err;
   }//end of function CheckFields()


/**
* Class method save
* function for Store data to the table
* @author Igor Trokhymchuk  <ihor@seotm.com>
* @version 1.1, 08.10.2011
* @return true or false
*/
function save()
{
    $ln_sys = &check_init('SysLang','SysLang');

    if( isset($this->settings['top_news']) AND $this->settings['top_news']==1){
        // Обнуление признака основной топ новости, если задана новая
        if($this->topMain==1) {
            $this->ClearTopMainArticles($this->id);
        }
    }

    $q = "SELECT * FROM ".TblModNews." WHERE `id`='".$this->id."'";
    $res = $this->Right->Query( $q, $this->user_id, $this->module );
    if( !$res ) return false;
    $rows = $this->Right->db_GetNumRows();
    //echo 'rows='.$rows;

    if( $rows>0 )   //--- update
    {
        $q = "UPDATE `".TblModNews."` SET
              `id_category` = '".$this->id_category."',
              `id_relart` = '".$this->id_relart."',
              `status` = '".$this->status."'
             ";

        if( isset($this->settings['top_news']) AND $this->settings['top_news']==1){
            $q.=",`top` = '".$this->top."',
                 `top_main` = '".$this->topMain."'
                ";
        }
        if( isset($this->settings['newsline']) AND $this->settings['newsline']==1){
            $q.=",`line` = '".$this->line."'";
        }
        if( isset($this->settings['dt']) AND $this->settings['dt']==1){
            $q.=",`start_date` = '".$this->start_date."'
                 ,`end_date` = '".$this->end_date."'";
        }
        if( isset($this->settings['source']) AND $this->settings['source']==1){
            $q.=",`source` = '".$this->source."'";
        }
        if( isset($this->settings['ukraine_news']) AND $this->settings['ukraine_news']==1){
            $q.=",`property` = '".$this->property."'";
        }
        $q.= " WHERE `id`='".$this->id."'";
        $res = $this->Right->Query( $q, $this->user_id, $this->module );
        //echo "<br / > q = ".$q." res = ".$res;
        if( !$res ) return false;
        //else return true;
     }
     else          //--- insert
     {
        $display = $this->GetMaxValueOfField(TblModNews, 'display')+1;

        $q = "INSERT INTO `".TblModNews."` SET
              `id_category` = '".$this->id_category."',
              `id_relart` = '".$this->id_relart."',
              `status` = '".$this->status."',
              `display` = '".$display."'
             ";

        if( isset($this->settings['top_news']) AND $this->settings['top_news']==1){
            $q.=",`top` = '".$this->top."',
                 `top_main` = '".$this->topMain."'
                ";
        }
        if( isset($this->settings['newsline']) AND $this->settings['newsline']==1){
            $q.=",`line` = '".$this->line."'";
        }
        if( isset($this->settings['dt']) AND $this->settings['dt']==1){
            $q.=",`start_date` = '".$this->start_date."'
                 ,`end_date` = '".$this->end_date."'";
        }
        if( isset($this->settings['source']) AND $this->settings['source']==1){
            $q.=",`source` = '".$this->source."'";
        }
        if( isset($this->settings['ukraine_news']) AND $this->settings['ukraine_news']==1){
            $q.=",`property` = '".$this->property."'";
        }

        $res = $this->Right->Query( $q, $this->user_id, $this->module );
        //echo "<br / > q = ".$q." res = ".$res;
        if( !$res ) return false;

        $this->id = $this->Right->db_GetInsertID();
     }

     //save related products to current news
     if ( isset($this->settings['relat_prod']) AND $this->settings['relat_prod']=='1' ) {
         $res = $this->SaveRelatProdToNews($this->id, $this->arr_relat_prop);
         if( !$res ) return false;
     }

     //---- save tags ----
     if ( $this->is_tags==1 ) {
        $Tags = new SystemTags();
        $res=$Tags->SaveTagsById( $this->module, $this->id, $this->id_tag );
        if( !$res ) return false;
     }

     $ln_arr = $ln_sys->LangArray( _LANG_ID );
     while( $el = each( $ln_arr ) )
     {
        $lang_id = $el['key'];
        $subject = addslashes(strip_tags(trim($this->subj_[ $lang_id ])));
        $keywords = addslashes(strip_tags(trim($this->keywords[ $lang_id ])));
        $description = addslashes(strip_tags(trim($this->description[ $lang_id ])));

        $res = $this->Spr->SaveToSpr( TblModNewsSprSbj, $this->id, $lang_id, $subject );
        //echo '<br />TblModNewsSprSbj $res='.$res;
        if( !$res ) return false;

        $res = $this->Spr->SaveToSpr( TblModNewsSprKeywords, $this->id, $lang_id, $keywords );
        //echo '<br />TblModNewsSprKeywords $res='.$res;
        if( !$res ) return false;

        $res = $this->Spr->SaveToSpr( TblModNewsSprDescription, $this->id, $lang_id, $description );
        //echo '<br />TblModNewsSprDescription $res='.$res;
        if( !$res ) return false;

        if( isset($this->settings['main_thing']) AND $this->settings['main_thing']==1){
           $main = addslashes(trim($this->main[ $lang_id ]));
           $res = $this->Spr->SaveToSpr( TblModNewsSprMain, $this->id, $lang_id, $main );
           //echo '<br />TblModNewsSprSbj $res='.$res;
           if( !$res ) return false;
        }

        if( isset($this->settings['short_descr']) AND $this->settings['short_descr']==1){
            $short = addslashes(trim($this->short_[$lang_id]));
            $res = $this->Spr->SaveToSpr( TblModNewsSprShrt, $this->id, $lang_id, $short );
            //echo '<br />TblModNewsSprShrt $res='.$res;
            if( !$res ) return false;
        }

        if( isset($this->settings['full_descr']) AND $this->settings['full_descr']==1){
            $full = addslashes(trim($this->full_[ $lang_id ]));
            $res = $this->Spr->SaveToSpr( TblModNewsSprFull, $this->id, $lang_id, $full );
            //echo '<br />TblModNewsSprFull $res='.$res;
            if( !$res ) return false;
        }
     } //--- end while

     $l_res = $this->Link($this->id_category, $this->id);

     if( isset($this->settings['top_news']) AND $this->settings['top_news']==1){
        //--- save topNews ---
        if($this->top == 1 )
            $res = $this->SaveTopNews($this->id);
        else {
            $res = $this->DelTopPicture($this->id);
            $res = $this->DeleteTopNews($this->id);
        }
        if( !$res ) return false;
     }

     $res = $this->SavePicture();
     // if( !$res ) return false;
     if( isset($this->settings['rss_import']) AND $this->settings['rss_import']==1){
        $res = $this->GenerateRSSNews();
     }

     //$uploaddir = NewsImg_Path;
     //$Uploads = new Uploads( $this->user_id , $this->module , $uploaddir, 200, $this->module );
     //$Uploads->saveCurentImages($this->id, $this->module);

     return true;
}



// ================================================================================================
// Function : SaveTopNews()
// Date : 22.03.2011
// Parms :   $id - id of TopNews
// Returns : true,false / Void
// Description : Store Top News data to the DB
// Programmer : Yaroslav Gyryn
// ================================================================================================
function SaveTopNews($id)
{
    $ln_sys = new SysLang();
    $ln_arr = $ln_sys->LangArray( _LANG_ID );

    $q = "SELECT * FROM ".TblModNewsTop." WHERE `cod`='".$id."'";
    $res = $this->Right->Query( $q, $this->user_id, $this->module );
    if( !$res ) return false;
    $rows = $this->Right->db_GetNumRows();
    //echo 'rows='.$rows;
    while( $el = each( $ln_arr ) )
    {
        $name = addslashes(strip_tags(trim($this->topSubject[ $el['key'] ])));
        $short = addslashes(strip_tags(trim($this->topShort[ $el['key'] ])));
        if( $rows>0 )   //--- update
        {
           $q = "UPDATE
                    `".TblModNewsTop."`
                  SET
                   `name`='".$name."',
                   `short`='".$short."'
                WHERE
                    cod ='".$this->id."'
                AND
                    lang_id ='".$el['key']."'
                    ";
        }
        else {   // --insert
           $q = "INSERT INTO `".TblModNewsTop."` values(NULL, '".$id."','".$el['key']."','".$name."','".$short."',  '' )";
        }
        $res = $this->Right->Query( $q, $this->user_id, $this->module );
            //echo "<br/>".$q."<br/> $res = ".$res;
        if( !$res ) return false;
    }
   $res = $this->SavePictureTop();
    return true;
}

// ================================================================================================
// Function : DeleteTopNews()
// Date : 04.02.2011
// Returns :      true,false / Void
// Description :  Remove TopNews from the DB
// Programmer :  Yaroslav Gyryn
// ================================================================================================
function DeleteTopNews( $id )
{
    $q = "DELETE FROM `".TblModNewsTop."` WHERE cod='$id'";
    $res = $this->Right->Query( $q, $this->user_id, $this->module );
    //echo '<br/>'.$q.'<br/>$res='.$res;
    if (!$res)
         return false;
    return true;
}

// ================================================================================================
// Function : ClearTopMainArticles()
// Date : 30.03.2011
// Returns :      true,false / Void
// Description :  Clear Top Main Articles
// Programmer :  Yaroslav Gyryn
// ================================================================================================
function ClearTopMainArticles($id=null)
{
   $db = new DB();
   $q = "UPDATE  `".TblModArticle."`  SET `top_main`='0'  WHERE   top_main ='1' ";
   $res = $this->Right->Query( $q, $this->user_id, $this->module );
    //echo '<br/>'.$q.'<br/>$res='.$res;
    if (!$res)
         return false;
   $q = "UPDATE  `".TblModNews."`  SET `top_main`='0'  WHERE   top_main ='1' AND id != '".$id."'  ";
   $res = $this->Right->Query( $q, $this->user_id, $this->module );
    //echo '<br/>'.$q.'<br/>$res='.$res;
    if (!$res)
         return false;
    return true;
}


// ================================================================================================
// Function : del()
// Date : 04.02.2011
// Returns :      true,false / Void
// Description :  Remove data from the table
// Programmer :  Yaroslav Gyryn
// ================================================================================================
function del( $id_del )
{
    $Tags = check_init('SystemTags', 'SystemTags');
    $del = 0;
    $kol = count($id_del);
    for( $i=0; $i<$kol; $i++ )
    {
       $u = $id_del[$i];

      //--- delete relation between tags for current position ---
      $res = $Tags->DelTagsByModuleItem( $this->module, $u);
      if( !$res ) return false;

	 $q = "DELETE FROM `".TblModNews."` WHERE id='$u'";
     $res = $this->Right->Query( $q, $this->user_id, $this->module );
     //echo "<br>q=".$q." res=".$res;
     $res = $this->Spr->DelFromSpr( TblModNewsSprSbj, $u );
     $res = $this->Spr->DelFromSpr( TblModNewsSprShrt, $u );
     $res = $this->Spr->DelFromSpr( TblModNewsSprFull, $u );
     $res = $this->Spr->DelFromSpr( TblModNewsSprMain, $u );
     $res = $this->Spr->DelFromSpr( TblModNewsSprKeywords, $u );
     $res = $this->Spr->DelFromSpr( TblModNewsSprDescription, $u );
	 // del links
     $q = "DELETE FROM `".TblModNewsLinks."` WHERE cod='$u'";
     $res = $this->Right->Query( $q, $this->user_id, $this->module );
	   // delete news images
     $q = "SELECT * FROM `".TblModNewsImg."` where `id_news`='$u'";
     $res = $this->Right->Query( $q, $this->user_id, $this->module );
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
     /*$q="DELETE FROM `".TblModNewsRel."` WHERE id_news='$u'";
     $res = $this->Right->Query( $q, $this->user_id, $this->module );
     if( !$res )return false;     */
     $this->DelTopPicture($u);
     $this->DeleteTopNews($u);

     if ( $res ){
      $del=$del+1;
      $id_img = NULL;
     }
     else
      return false;
    }

  return $del;
}



// ================================================================================================
// Function : up()
// Date : 04.02.2005
// Returns :      true,false / Void
// Description :  Up news
// Programmer :  Yaroslav Gyryn
// ================================================================================================
function up( $move )
{
 $q="select * from ".TblModNews." where display='$move'";
 $res = $this->Right->Query( $q, $this->user_id, $this->module );
 if( !$res )return false;
 $rows = $this->Right->db_GetNumRows();
 $row = $this->Right->db_FetchAssoc();
 $move_down = $row['display'];
 $id_down = $row['id'];


 $q="select * from ".TblModNews." where display>'$move' order by display";
 $res = $this->Right->Query( $q, $this->user_id, $this->module );
 if( !$res )return false;
 $rows = $this->Right->db_GetNumRows();
 $row = $this->Right->db_FetchAssoc();
 $move_up = $row['display'];
 $id_up = $row['id'];

 if( $move_down!=0 AND $move_up!=0 )
 {
 $q="update ".TblModNews." set
     display='$move_down' where id='$id_up'";
 //echo '<br>'.$q;
 $res = $this->Right->Query( $q, $this->user_id, $this->module );

 $q="update ".TblModNews." set
     display='$move_up' where id='$id_down'";
 //echo '<br>'.$q;
 $res = $this->Right->Query( $q, $this->user_id, $this->module );
 }
}


// ================================================================================================
// Function : preview()
// Date : 04.02.2005
// Returns :      true,false / Void
// Description :  preview news
// Programmer :  Yaroslav Gyryn
// ================================================================================================
function preview( $subject, $short, $full )
{
?>
<table border=0 width=99% height=99%>
<tr><td align=center valign=top>
 <table border=0>
 <tr><td align=center><H4><?=$subject;?></H4>
 <tr><td><i><?=$short;?></i><br>
 <tr><td><?=$full;?>
 <tr><td><a href="javascript:window.close()">Close</a>
</table>
</table>
<?
}

// ================================================================================================
// Function : down()
// Date : 04.02.2005
// Returns :      true,false / Void
// Description :  Down news
// Programmer :  Yaroslav Gyryn
// ================================================================================================
function down( $move )
{
 $q="select * from ".TblModNews." where display='$move'";
 $res = $this->Right->Query( $q, $this->user_id, $this->module );
 if( !$res )return false;
 $rows = $this->Right->db_GetNumRows();
 $row = $this->Right->db_FetchAssoc();
 $move_up = $row['display'];
 $id_up = $row['id'];


 $q="select * from ".TblModNews." where display<'$move' order by display desc";
 $res = $this->Right->Query( $q, $this->user_id, $this->module );
 if( !$res )return false;
 $rows = $this->Right->db_GetNumRows();
 $row = $this->Right->db_FetchAssoc();
 $move_down = $row['display'];
 $id_down = $row['id'];

 if( $move_down!=0 AND $move_up!=0 )
 {
 $q="update ".TblModNews." set
     display='$move_down' where id='$id_up'";
 //echo '<br>'.$q;
 $res = $this->Right->Query( $q, $this->user_id, $this->module );

 $q="update ".TblModNews." set
     display='$move_up' where id='$id_down'";
 //echo '<br>'.$q;
 $res = $this->Right->Query( $q, $this->user_id, $this->module );
 }
}



// ================================================================================================
// Function : relart()
// Date : 04.02.2005
// Returns :      true,false / Void
// Description :  relart news
// Programmer :  Yaroslav Gyryn
// ================================================================================================
function relart( $id = NULL )
{
 $db = new Rights;

 $script = 'module='.$this->module.'&display='.$this->display.'&start='.$this->start.'&sort='.$this->sort;
 $script = $_SERVER['PHP_SELF']."?$script&id=$id";

 if( !$this->sort ) $this->sort = 'id_news';
 $q = "SELECT * FROM ".TblModNewsRel;
 if( $id ) $q = $q." where id_news='$id'";
 $q = $q." order by `$this->sort`";

 $res = $this->Right->Query( $q, $this->user_id, $this->module ); if( !$res )return false;
 $rows = $this->Right->db_GetNumRows();

 /* Write Form Header */
 $this->Form->name = 'relart_form';
 $this->Form->WriteHeader( $script );
 $this->Form->Hidden( 'id', $id );
 $this->Form->Hidden( 'dorel', '1' );

 /* Write Table Part */
 AdminHTML::TablePartH();
 echo '<TR><td COLSPAN=8 width=100%>';

 /* Write Links on Pages */
 $this->Form->WriteLinkPages( $script, $rows, $this->display, $this->start, $this->sort );
 echo '<TR><td COLSPAN=4>';

 $this->Form->WriteTopPanel2( $script, 'newnews_relart' );

 $script2 = 'module='.$this->module.'&display='.$this->display.'&start='.$this->start.'&task=shownews_relart';
 $script2 = $_SERVER['PHP_SELF']."?$script2&dorel=1";

 echo '<td colspan=4 class="EditTable">';
 if( $id )
 {
   $q = "SELECT * FROM ".TblModNews." where 1 ";
   $q = $q." and id=$id ";
   $q = $q." group by id order by start_date ";
   $res = $db->Query( $q, $this->user_id, $this->module );
   $row = $db->db_FetchAssoc();
   echo '<b>'.$row['id'].'-'.$this->Spr->GetNameByCod( TblModNewsSprSbj, $row['id'] ).' ['.$row['start_date'].']</b>';
 }

?>
 <TR>
 <td class="THead">*</Th>
 <td class="THead"><A HREF=<?=$script?>&sort=id><?=$this->Msg->show_text('_FLD_ID')?></A></Th>
 <td class="THead"><A HREF=<?=$script?>&sort=id_category><?=$this->Msg->show_text('_FLD_NEWS_CATEGORY')?></A></Th>
 <td class="THead"><?=$this->Msg->show_text('_FLD_NEWS_SUBJECT')?></Th>
 <td class="THead"><A HREF=<?=$script?>&sort=status><?=$this->Msg->show_text('_FLD_NEWS_STATUS')?></A></Th>
 <td class="THead"><A HREF=<?=$script?>&sort=start_date><?=$this->Msg->show_text('_FLD_NEWS_STARTDATE')?></A></Th>
 <td class="THead"><A HREF=<?=$script?>&sort=end_date><?=$this->Msg->show_text('_FLD_NEWS_ENDDATE')?></A></Th>
 <?
 $a = $rows;
 for( $i = 0; $i < $rows; $i++ )
 {
   $row = $this->Right->db_FetchAssoc();
   if( $i >= $this->start && $i < ( $this->start+$this->display ) )
   {
     if ( (float)$i/2 == round( $i/2 ) ) echo '<TR CLASS="TR1">';
     else echo '<TR CLASS="TR2">';

   echo '<TD>';
   $this->Form->CheckBox( "id_del[]", $row['id'] );

   echo '<TD>';
   $this->Form->Link( $script."&task=editnews_relart&id_r=".$row['id'].'&id='.$row['id_news'], $this->Msg->show_text('_LNK_EDIT') );

   $q = "select * from ".TblModNews." where id='".$row['id_relart']."'";
   $res1 = $db->Query( $q, $this->user_id, $this->module );
   if( !$res1 ) return false;
   $mas_f = $db->db_FetchAssoc();

   $category = $this->Spr->GetNameByCod( TblModNewsCat, $mas_f['id_category'] );
   echo '<TD align=center>'.$category.'</TD>';
   echo '<td>'.$this->Spr->GetNameByCod( TblModNewsSprSbj, $mas_f['id'] ).'</td>';

   echo '<TD align=center>';
    if( $mas_f['status'] =='i')echo 'Inactive';
    if( $mas_f['status'] =='e')echo 'Expired';
    if( $mas_f['status'] =='a')echo 'Active';
   echo '</TD>';
   echo '<TD align=center>'.$mas_f['start_date'].'</TD>';
   echo '<TD align=center>'.$mas_f['end_date'].'</TD>';
 }
}

 AdminHTML::TablePartF();
 $this->Form->WriteFooter();
}


// ================================================================================================
// Function : relart_edit()
// Date : 04.02.2005
// Parms :        $id, $mas
// Returns :      true,false / Void
// Description :  relart_edit news
// Programmer :  Yaroslav Gyryn
// ================================================================================================
function relart_edit( $id, $id_r, $mas = NULL )
{

 $script = 'module='.$this->module.'&display='.$this->display.'&start='.$this->start.'&sort='.$this->sort;
 $script = $_SERVER['PHP_SELF']."?$script&dorel=1";

 if( $id != NULL and ( $mas == NULL ) )
 {
  $q="SELECT * FROM ".TblModNewsRel." where id='$id_r'";
  $res = $this->Right->Query( $q, $this->user_id, $this->module );
  if( !$res ) return false;
  $mas = $this->Right->db_FetchAssoc();
 }

 /* Write Form Header */
 $this->Form->WriteHeader( $script );

 if( $id!=NULL ) $txt = $this->Msg->show_text('_TXT_EDIT_DATA');
 else $txt = $this->Msg->show_text('_TXT_ADD_DATA');

 AdminHTML::PanelSubH( $txt );
 AdminHTML::PanelSimpleH();

?>
 <TABLE BORDER=0 class="EditTable">
 <TR><TD><b><?=$this->Msg->show_text('_FLD_ID')?></b>
 <TD>
<?
   echo ''.$mas['id'].'';
   $this->Form->Hidden( 'id', $mas['id'] );
?>
 <TR><TD><b><?=$this->Msg->show_text('_FLD_NEWS')?></b>
 <TD>
<?
 if( $id )
 {
   $q = "SELECT * FROM ".TblModNews." where 1 ";
   $q = $q." and id=$id ";
   $q = $q." order by start_date ";
   $res = $this->Right->Query( $q, $this->user_id, $this->module );
   $row = $this->Right->db_FetchAssoc();
   echo $row['id'].'-'.$this->Spr->GetNameByCod( TblModNewsSprSbj, $row['id'] ).' ['.$row['start_date'].']';
 }
 $this->Form->Hidden( 'id_news', $id );
?>
 <TR><TD><b><?=$this->Msg->show_text('_FLD_NEWS_RELART')?></b>
 <TD><?
 $arr = NULL;
 $arr[''] = '';
 $q = "SELECT * FROM ".TblModNews." where 1 ";
 if( $id ) $q = $q." and id!=$id ";
 $q = $q." order by start_date ";

 $res = $this->Right->Query( $q, $this->user_id, $this->module );
 if( !$res )return false;
 $rows = $this->Right->db_GetNumRows();

 for( $i = 0; $i < $rows; $i++ )
 {
  $row = $this->Right->db_FetchAssoc();
  $arr[$row['id']] = $row['id'].'-'.$this->Spr->GetNameByCod( TblModNewsSprSbj, $row['id'] ).' ['.$row['start_date'].']';
 }
 $this->Form->Select( $arr, 'id_relart', $mas['id_relart'] );
?>
 <TR><TD COLSPAN=2 ALIGN=left>
<?
   $this->Form->WriteSavePanel( $script );
?>
</TABLE>
<?
 AdminHTML::PanelSimpleF();
 AdminHTML::PanelSubF();
 $this->Form->WriteFooter();
 return true;
}


// ================================================================================================
// Function : relart_save()
// Date : 05.02.2005
// Returns :      true,false / Void
// Description :  relart_save news
// Programmer :  Yaroslav Gyryn
// ================================================================================================
function relart_save( $id, $id_news, $id_relart )
{
 if( empty( $id_relart ) )
 {
     $this->Msg->show_msg('_NEWS_RELART_EMPTY');
     $this->relart_edit( $id, $id_news, $_REQUEST );
     return false;
 }

 $q = "SELECT * FROM ".TblModNewsRel." WHERE id_news='$id_news' AND id_relart='$id_relart' ";
 $res = $this->Right->Query( $q, $this->user_id, $this->module );
 if( !$res ) return false;
echo $q;
 $rows = $this->Right->db_GetNumRows();
 if( $rows>0 )return true;


 $q="SELECT * FROM ".TblModNewsRel." WHERE id='$id'";

 $res = $this->Right->Query( $q, $this->user_id, $this->module );
 if( !$res ) return false;

 $rows = $this->Right->db_GetNumRows();
 if( $rows>0 )
 {
  $q="update `".TblModNewsRel."` set `id_news`='$id_news',
   `id_relart`='$id_relart'
   where `id`='$id'";
  $res = $this->Right->Query( $q, $this->user_id, $this->module );
  if( !$res ) return false;
  else return true;
 }
 else
 {
  $q = "insert into `".TblModNewsRel."` values(NULL, '$id_news', '$id_relart')";
  $res = $this->Right->Query( $q, $this->user_id, $this->module );
  if( !$res ) return false;
  else return true;
 }

}


// ================================================================================================
// Function : relart_del()
// Date : 05.02.2005
// Returns :      true,false / Void
// Description :  relart_del news
// Programmer :  Yaroslav Gyryn
// ================================================================================================
function relart_del( $id_del )
{
    $kol=count( $id_del );
    $del=0;
    for( $i=0; $i<$kol; $i++ )
    {
     $u = $id_del[$i];
     $q = "DELETE FROM `".TblModNewsRel."` WHERE id='$u'";
     $res = $this->Right->Query( $q, $this->user_id, $this->module );
     if( $res ) $del = $del + 1;
     else return false;
    }
  return $del;
}

function news_update( $module )
{
 if(!db_connect()) {
   show_error('_ERROR_CONNECT_TO_DB');
   return false;
 }

 $date = date('Y-m-d G:i:s');
 $q = "update `".TblModNews."` set `status`='e' where end_date<'$date' and `status`!='i'";
 $res1 = mysql_query( $q );

 }

    // ================================================================================================
    // Function : ShowImagesBackEnd
    // Date : 03.04.2006
    // Returns : $res / Void
    // Description : Show the immages of item product
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function ShowImagesBackEnd()
    {
         //$this->Rights =  new Rights;
         $Panel = new Panel();
         $ln_sys = new SysLang();

         $q="SELECT * FROM `".TblModNewsImg."` WHERE `id_news`='".$this->id."' order by `move`";
         $res = $this->Right->Query( $q, $this->user_id, $this->module );
         if( !$this->Right->result ) return false;
         $rows = $this->Right->db_GetNumRows();
         $arr = array();
         for ($i=0; $i<$rows; $i++) {
           $arr[] = $this->Right->db_FetchAssoc();
         }


         $q="SELECT * FROM `".TblModNewsSprSbj."` WHERE `cod`='".$this->id."' and `lang_id`=".$this->lang_id."";
         $res2 = $this->Right->Query( $q, $this->user_id, $this->module );
         if( !$this->Right->result ) return false;
         $rows2 = $this->Right->db_GetNumRows();
         $row2 = $this->Right->db_FetchAssoc();
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
           <TD><b><?=$this->Msg->show_text('FLD_NEWS')?>:</b></TD>
           <TD width="100%"><?=stripcslashes($row2['name']);?></TD>
          </TR>
         <?

         $a = $rows;
         $up = 0;
         $down = 0;
         for ($i=0; $i<$rows; $i++) {
           $row = $arr[$i]; //$this->Right->db_FetchAssoc();
           ?>
          <TR>
           <TD colspan=2>
           <?=AdminHTML::PanelSimpleH();?>
            <TABLE border=0 cellpadding=2 cellspacing=0 class="EditTable">
             <TR>
              <TD align=right><INPUT class='checkbox' TYPE=checkbox NAME='id_img_del[]' VALUE="<?=$row['id'];?>"></TD>
              <TD align=center valign="middle" width="255">
               <a href="http://<?=$_SERVER['SERVER_NAME']?>/thumb_news.php?img=<?=$row['path']?>&amp;id_news=<?=$this->id?>" target=_blank><?=$this->ShowImage($row['path'], $this->id, 'size_auto=250', 100, NULL, "border=0");?></a>
              </TD>
              <TD valign="top">
               <table border=0 cellpadding=0 cellspacing=2 class="EditTable">
                <tr>
                 <TD><b><?=$this->Msg->show_text('FLD_ID')?>:</b><?=$row['id']; $this->Form->Hidden( 'id_img[]', $row['id'] );?></TD>
                </tr>
                <tr>
                 <TD><b><?=$this->Msg->show_text('FLD_IMG')?>:</b> <?=$this->GetImgFullPath($row['path'], $this->id);?></TD>
                </tr>
                <tr><td>
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
                  echo "\n<tr><td><b>".$this->Msg->show_text('FLD_IMG_ALT').":</b></td>";
                  echo "\n<tr><td>";
                  $name = $this->Spr->GetByCod( TblModNewsImgSprName, $row['id'], $lang_id );
                  if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->img_title[$lang_id] : $val=$name[$lang_id];
                  else $val=$this->img_title[$lang_id];
                  $this->Form->TextBox( 'img_title['.$row['id'].']['.$lang_id.']', stripslashes($val), 60 );

                  echo "\n<tr><td><b>".$this->Msg->show_text('FLD_IMG_TITLE').":</b></td>";
                  echo "\n<tr><td>";
                  $name = $this->Spr->GetByCod( TblModNewsImgSprDescr, $row['id'], $lang_id );
                  if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->img_descr[$lang_id] : $val=$name[$lang_id];
                  else $val=$this->img_descr[$lang_id];
                  //$this->Form->HTMLTextArea( 'img_descr['.$row['id'].']['.$lang_id.']', stripslashes($val), 5, 50  );
                  $this->Form->TextArea( 'img_descr['.$row['id'].']['.$lang_id.']', stripslashes($val), 4, 50  );
                  echo "\n</table>";
                  $Panel->WriteItemFooter();
                }
                $Panel->WritePanelFooter();
                ?>
                <tr>
                 <td align=left><b><?=$this->Msg->show_text('FLD_SHOW')?>:</b><INPUT class='checkbox' TYPE=checkbox NAME='id_img_show[]' VALUE="<?=$row['id']?>" <?if ($row['show']=='1') echo 'CHECKED';?>></TD>
                </tr>
               <?

                ?>
                <tr>
                 <td><b>
                 <? if( $i!=($rows-1) or $up!=0 )  {
                  echo $this->Msg->show_text('FLD_DISPLAY');?>:</b>
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
              <?=$this->Form->Button('cancel',$this->Msg->show_text('TXT_CANCEL'));?>
           </TD>
          </TR>
          <TR>
           <TD colspan=2>
            <input type="hidden" name="MAX_FILE_SIZE" value="2000000">
            <br /><br />
            <INPUT TYPE="file" NAME="filename[]" size="80" VALUE="<?=$this->img['name']['0']?>">
            <br>
            <INPUT TYPE="file" NAME="filename[]" size="80" VALUE="<?=$this->img['name']['1']?>">
            <br>
            <INPUT TYPE="file" NAME="filename[]" size="80" VALUE="<?=$this->img['name']['2']?>">
            <br>
            <INPUT TYPE="file" NAME="filename[]" size="80" VALUE="<?=$this->img['name']['3']?>">
            <br>
            <INPUT TYPE="file" NAME="filename[]" size="80" VALUE="<?=$this->img['name']['4']?>">
            <br><?=$this->Form->Button('saveimg',$this->Msg->show_text('TXT_ADD_IMAGES'));?>

           </TD>
          </TR>
          <?
           //echo "<input type=hidden name='task' value='saveimg'>";
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




    // ================================================================================================
    // Function : ShowSubscribe()
    // Date : 04.01.2005
    // Returns :     true,false / Void
    // Description : Show News
    // Programmer : Ihor Trokhymchuk
    // ================================================================================================
    function ShowSubscribe()
    {
     $db = new Rights;
     $frm = new Form('fltr');
     $script = 'module='.$this->module.'&display='.$this->display.'&start='.$this->start.'&sort='.$this->sort.'&fltr='.$this->fltr.'&fln='.$this->fln;
     $script = $_SERVER['PHP_SELF']."?$script";

     if( !$this->sort ) $this->sort='dt';
     //if( strstr( $this->sort, 'display' ) )$this->sort = $this->sort.' desc';
     $q = "SELECT * FROM ".TblModNewsSubscr." WHERE 1";
     if( $this->fltr ) $q = $q." AND $this->fltr";
     $q = $q." ORDER BY `".$this->sort."` desc, `id` desc ";
     //echo '<br>$q='.$q;
     $res = $this->Right->Query( $q, $this->user_id, $this->module );
     if( !$res )return false;
     $rows = $this->Right->db_GetNumRows();

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

     $this->settings = $this->GetSettings(false);

     /* Write Form Header */
     $this->Form->WriteHeader( $script );

     /* Write Table Part */
     AdminHTML::TablePartH();

     /* Write Links on Pages */
     echo '<TR><TD COLSPAN=7>';
     $script1 = 'module='.$this->module.'&fltr='.$this->fltr;
     $script1 = $_SERVER['PHP_SELF']."?$script1";
     $this->Form->WriteLinkPages( $script1, $rows, $this->display, $this->start, $this->sort );

     ?><tr><td colspan="4"><?
     $this->Form->WriteTopPanel2( $script, 'edit_subscr', NULL );
     ?>
     <a CLASS="toolbar" href="javascript:<?=$this->Form->name;?>.task.value='del_subscr';<?=$this->Form->name;?>.submit();" onclick="if( !window.confirm('<?=$this->Msg->show_text('MSG_DEL_SUBSCR');?>') ) return false; else <?=$this->Form->name;?>.task.value='del_subscr';<?=$this->Form->name;?>.submit();" onmouseout="MM_swapImgRestore();"  onmouseover="MM_swapImage('delete','','images/icons/delete_f2.png',1);">
     <img src="images/icons/delete.png" alt="Delete" title="Delete" align="center" name="delete" border="0" />Удалить из рассылки
     </a>
     <?

     $script2 = 'module='.$this->module.'&display='.$this->display.'&start='.$this->start.'&task=show&fltr='.$this->fltr.'&task=show_subscr';
     $script2 = $_SERVER['PHP_SELF']."?$script2";
    ?>
     <TR>
     <td class="THead">*</Th>
     <td class="THead"><A class="aTHead" HREF=<?=$script2?>&sort=id><?=$this->Msg->show_text('_FLD_ID')?></A></Th>
     <td class="THead"><A class="aTHead" class="aTHead" HREF=<?=$script2?>&sort=email><?=$this->Msg->show_text('FLD_NEWS_SUBSCR_EMAIL');?></A></Th>
     <td class="THead"><A class="aTHead" HREF=<?=$script2?>&sort=status><?=$this->Msg->show_text('FLD_NEWS_SUBSCR_STATUS');?></A></Th>
     <td class="THead"><A class="aTHead" HREF=<?=$script2?>&sort=dt><?=$this->Msg->show_text('FLD_NEWS_SUBSCR_DT_REG');?></A></Th>
     <td class="THead"><A class="aTHead" HREF=<?=$script2?>&sort=dt><?=$this->Msg->show_text('FLD_NEWS_SUBSCR_CAT');?></A></Th>
     <?
     if ( isset($this->settings['subscr']) AND $this->settings['subscr']=='1' ) {
         ?><td class="THead"><A class="aTHead" HREF=<?=$script2?>&sort=is_send><?=$this->Msg->show_text('FLD_NEWS_SUBSCR_IS_SEND');?></A></Th><?
     }


     $str_yes = $this->Spr->GetNAmeByCod(TblSysLogic, 1);
     $str_no = $this->Spr->GetNAmeByCod(TblSysLogic, 2);

     $str_status_a = $this->Msg->show_text('TXT_ACTIVE_OK');
     $str_status_ina = $this->Msg->show_text('TXT_ACTIVE_NOT_OK');

     $style1 = 'TR1';
     $style2 = 'TR2';
     for( $i = 0; $i < count( $row_arr ); $i++ )
     {
       $row = $row_arr[$i];

       if ( (float)$i/2 == round( $i/2 ) ){
           echo '<TR CLASS="'.$style1.'">';
       }
       else echo '<TR CLASS="'.$style2.'">';

       echo '<TD>';
       $this->Form->CheckBox( "id_del[]", $row['id'] );

       echo '<TD>';
       $this->Form->Link( $script."&task=edit_subscr&id=".$row['id'], stripslashes( $row['id'] ), $this->Msg->show_text('_TXT_EDIT_DATA') );

       echo '<TD align=center>'.stripslashes($row['login']);

       ?><td align="center"><?
        if( $row['user_status']==0 ) echo $str_status_ina;
        else echo $str_status_a;
       ?></td><?

       ?><td align="center"><?=stripslashes($row['dt']);?></td><?

       ?><td><?
         $q2 = "SELECT `".TblModNewsCat."`.`name`
                FROM `".TblModNewsCat."`, `".TblModNewsSubscrCat."`
                WHERE `".TblModNewsSubscrCat."`.`subscr_id`='".$row['id']."'
                AND `".TblModNewsSubscrCat."`.`cat_id`=`".TblModNewsCat."`.`cod`
                AND `".TblModNewsCat."`.`lang_id`='".$this->lang_id."'";
         $res2 = $db->db_Query($q2);
         //echo '<br>$q2='.$q2." res2=".$res2;
         $rows2 = $db->db_GetNumRows();
         //echo '<br>$rows2='.$rows2;
         for($j=0;$j<$rows2;$j++){
             $row2 = $db->db_FetchAssoc();
             echo $row2['name'].'<br/>';
         }
       ?></td><?

       if ( isset($this->settings['subscr']) AND $this->settings['subscr']=='1' ) {
        ?><td><?
        if($row['is_send']==0) echo $str_no;
        else echo $str_yes;
        ?></td><?
       }
     } //-- end for

     AdminHTML::TablePartF();
     $this->Form->WriteFooter();
     return true;
    } // end of function ShowSubscribe()

    // ================================================================================================
    // Function : EditSubscribe()
    // Date : 31.05.2008
    // Returns :      true,false / Void
    // Description :  edit subscriber
    // Programmer :  Ihor Trokhymchuk
    // ================================================================================================
    function EditSubscribe()
    {
        $q = "SELECT * FROM `".TblModNewsSubscr."` where `id`='".$this->id."'";
        $res = $this->Right->db_Query( $q );
        //echo '<br>$q='.$q.' $res='.$res.' $this->Right->result='.$this->Right->result;
        if( !$res ) return false;
        $mas = $this->Right->db_FetchAssoc();

        $this->settings = $this->GetSettings();

        /* Write Form Header */
        $this->Form->WriteHeader( $this->script );
        if( $this->id!=NULL ) $txt = $this->Msg->show_text('_TXT_EDIT_DATA');
        else $txt = $this->Msg->show_text('_TXT_ADD_DATA');
        AdminHTML::PanelSubH( $txt );

        $this->ShowErrBackEnd();

        AdminHTML::PanelSimpleH();
        ?>
        <tr>
            <td width="400" valign="top">
                <table border="0" >
                 <tr>
                  <td><strong><?=$this->Msg->show_text('_FLD_ID')?>:</strong>
                   <?
                   if( $this->id!=NULL )
                   {
                    echo $mas['id'];
                    $this->Form->Hidden( 'id', $mas['id'] );
                   }
                   else $this->Form->Hidden( 'id', '' );
                   ?>
                  </td>
                  <td><strong><?=$this->Msg->show_text('FLD_NEWS_SUBSCR_STATUS');?></strong>&nbsp;
                   <?
                   if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->user_status : $val=$mas['user_status'];
                   else $val=$this->user_status;

                   $arr_v[0] = $this->Msg->show_text('TXT_ACTIVE_NOT_OK');
                   $arr_v[1] = $this->Msg->show_text('TXT_ACTIVE_OK');
                   $this->Form->Select( $arr_v, 'user_status', $val );
                   ?>
                  </td>
                 </tr>
                 </tr>
                 <tr>
                  <td><strong><?=$this->Msg->show_text('FLD_NEWS_SUBSCR_EMAIL')?>:</strong>
                  <td>
                   <?
                   if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->login : $val=$mas['login'];
                   else $val=$this->login;
                   $this->Form->TextBox( 'login', stripslashes($val), 50 );
                   ?>
                  </td>
                 </tr>
                 <tr>
                  <td><strong><?=$this->Msg->show_text('FLD_NEWS_SUBSCR_PASSWORD')?>:</strong>
                  <td>
                   <?
                   if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->pass : $val=$mas['pass'];
                   else $val=$this->pass;
                   $this->Form->TextBox( 'pass', stripslashes($val), 50 );
                   ?>
                  </td>
                 </tr>
                 <tr>
                  <td><strong><?=$this->Msg->show_text('FLD_NEWS_SUBSCR_DT_REG')?>:</strong>
                  <td>
                   <?
                   if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->dt : $val=$mas['dt'];
                   else $val=$this->dt;
                   if(empty($val)) $val = date("Y-m-d");
                   $this->Form->TextBox( 'dt', stripslashes($val), 10 );
                   ?>
                  </td>
                 </tr>
                 <?if ( isset($this->settings['subscr']) AND $this->settings['subscr']=='1' ) {?>
                 <tr>
                  <td><strong><?=$this->Msg->show_text('FLD_NEWS_SUBSCR_IS_SEND')?>:</strong>
                  <td>
                   <?
                   if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->is_send : $val=$mas['is_send'];
                   else $val=$this->is_send;
                   if(empty($val)) $val = 0;
                   $arr_v[0] = $this->Spr->GetNameByCod(TblSysLogic, 2);
                   $arr_v[1] = $this->Spr->GetNameByCod(TblSysLogic, 1);
                   $this->Form->Select( $arr_v, 'is_send', $val );
                   ?>
                  </td>
                 </tr>
                 <?}?>
                </table>
            </td>
            <?if ( isset($this->settings['subscr']) AND $this->settings['subscr']=='1' ) {?>
            <td valign="top">
                <div><strong><?=$this->Msg->show_text('FLD_NEWS_SUBSCR_CAT');?>:</strong></div>
                <div>
                <?
                if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->categories : $val = $this->GetSubscrCat($mas['id']);
                else $this->Err!=NULL ? $val=$this->categories : $val = $this->Spr->GetListName( TblModNewsCat, $this->lang_id, 'arr', 'cod', 'asc', 'cod' );
                //print_r($val);
                $cnt_cat_in_row = 4;
                $cnt_rows = ceil($this->Spr->GetCountValuesInSprOnLang( TblModNewsCat, $this->lang_id )/$cnt_cat_in_row);
                //echo '<br>$cnt_rows='.$cnt_rows.' $this->Spr->GetCountValuesInSprOnLang( TblModNewsCat, $this->lang_id ))='.$this->Spr->GetCountValuesInSprOnLang( TblModNewsCat, $this->lang_id );
                $this->Spr->ShowInCheckBox( TblModNewsCat, 'categories', $cnt_rows, $val, 'left' );
                ?>
                </div>
            </td>
            <?}?>
         </tr>



        <?
        AdminHTML::PanelSimpleF();
       $this->Form->WriteSavePanel( $this->script, 'save_subscr');
       /*
        ?>
        <a class="toolbar" href="javascript:<?=$this->Form->name?>.task.value='save_subscr';<?=$this->Form->name?>.submit();" onmouseout="MM_swapImgRestore();"  onmouseover="MM_swapImage('save','','images/icons/save_f2.png',1);">
         <img src="images/icons/save.png" alt="Save" title="Save" align="center" name="save" border="0" /> <?=$this->Msg->show_text('TXT_SAVE') ?>
        </a>
        <?
        */
        $this->Form->WriteCancelPanel( $this->script );
        $this->Form->WriteFooter();

        AdminHTML::PanelSubF();
    }//end of function EditSubscribe()

    // ================================================================================================
    // Function : GetSubscrCat()
    // Date : 15.09.2009
    // Parms :        $id - if of the subscriber
    // Returns :      true,false / Void
    // Description :  retur array of categories for current subscriber
    // Programmer :  Igor Trokhymchuk
    // ================================================================================================
    function GetSubscrCat($id)
    {
        if(!$id) return false;
        $db = new DB();
        $q="SELECT `cat_id` FROM `".TblModNewsSubscrCat."` WHERE `subscr_id`='".$id."'";
        $res = $db->db_Query( $q );
        //echo '<br>$q='.$q.' $res='.$res;
        if( !$res OR !$db->result ) return false;
        $rows = $db->db_GetNumRows();
        $arr = NULL;
        for($i=0;$i<$rows;$i++){
            $row = $db->db_FetchAssoc();
            $arr[$i]=$row['cat_id'];
        }
        return $arr;
    } //end of fuinction GetSubscrCat()

    // ================================================================================================
    // Function : GetCountSubscrCat()
    // Date : 15.09.2009
    // Parms :        $id - if of the subscriber
    // Returns :      true,false / Void
    // Description :  retur count of categories for current subscriber
    // Programmer :  Igor Trokhymchuk
    // ================================================================================================
    function GetCountSubscrCat($id)
    {
        if(!$id) return false;
        $db = new DB();
        $q="SELECT COUNT(`cat_id`) FROM `".TblModNewsSubscrCat."` WHERE `subscr_id`='".$id."'";
        $res = $db->db_Query( $q );
        //echo '<br>$q='.$q.' $res='.$res;
        if( !$res OR !$db->result ) return false;
        $row = $db->db_FetchAssoc();
        return $row['COUNT(`cat_id`)'];
    } //end of fuinction GetCountSubscrCat()

    // ================================================================================================
    // Function : CheckSubscr()
    // Date : 31.05.2008
    // Returns :      true,false / Void
    // Description :  Checking all fields for filling and validation
    // Programmer :  Igor Trokhymchuk
    // ================================================================================================
    function CheckSubscr()
    {
        $this->Err=NULL;

        if (empty( $this->login )) {
            $this->Err=$this->Err.$this->Msg->show_text('MSG_FLD_EMAIL_EMPTY').'<br>';
        }
        /*
        if (empty( $this->pass )) {
            $this->Err=$this->Err.$this->Msg->show_text('MSG_FLD_PASS_EMPTY').'<br>';
        }
        */
        else{
            if (!ereg("^[a-zA-Z0-9_.\-]+@[a-zA-Z0-9.\-].[a-zA-Z0-9.\-]+$", $this->login)) $this->Err = $this->Err.$this->Msg->show_text('MSG_NOT_VALID_EMAIL').'<br>';
            else{
                $q="SELECT `id`,`login` FROM `".TblModNewsSubscr."` WHERE `login`='".$this->login."'";
                $res = $this->Right->db_Query( $q );
                if( !$res OR !$this->Right->result ) return false;
                $rows = $this->Right->db_GetNumRows();
                $row = $this->Right->db_FetchAssoc();
                if($rows>0 AND $row['id']!=$this->id) $this->Err = $this->Err.$this->Msg->show_text('MSG_EMAIL_ALREADY_EXIST').'<br>';
            }
        }
        if(count($this->categories)==0) $this->Err = $this->Err.$this->Msg->show_text('MSG_SELECT_NEWS_CATEGORIES').'<br>';
        //echo '<br>$this->Err='.$this->Err.' $this->Msg->table='.$this->Msg->table;
        return $this->Err;
    } //end of fuinction CheckSubscr()


    // ================================================================================================
    // Function : SaveSubscribe()
    // Date : 231.05.2008
    // Parms :   $user_id, $module, $id, $group_menu, $level, $description, $function, $move
    // Returns : true,false / Void
    // Description : Store data to the table
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function SaveSubscribe()
    {
        $q="SELECT * FROM `".TblModNewsSubscr."` WHERE `id`='".$this->id."'";
        $res = $this->Right->db_Query( $q );
        if( !$res OR !$this->Right->result ) return false;
        $rows = $this->Right->db_GetNumRows();

        if($rows>0)
        {
            $q = "UPDATE `".TblModNewsSubscr."` SET
                 `login`='".$this->login."',
                 `pass`='".$this->pass."',
                 `dt`='".$this->dt."',
                 `user_status`='".$this->user_status."',
                 `is_send`='".$this->is_send."'
                 WHERE `id`='".$this->id."'";
        }
        else{
            $q = "INSERT INTO `".TblModNewsSubscr."` SET
                 `login`='".$this->login."',
                 `pass`='".$this->pass."',
                 `dt`='".$this->dt."',
                 `user_status`='".$this->user_status."',
                 `is_send`='".$this->is_send."'
                 ";
        }
        $res = $this->Right->db_Query( $q );
        //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;
        if( !$res OR !$this->Right->result ) return false;

        if ( empty($this->id)) $this->id = $this->Right->db_GetInsertID();

        $cnt = count($this->categories);
        if($cnt>0){
            $q = "DELETE FROM `".TblModNewsSubscrCat."` WHERE `subscr_id`='".$this->id."'";
            $res = $this->db->db_Query( $q );
            //echo "<br>q=".$q." res=".$res;
            if( !$res ) return false;

        }
        for($i=0;$i<$cnt;$i++){
            $q = "INSERT `".TblModNewsSubscrCat."` SET
                  `subscr_id`='".$this->id."',
                  `cat_id`='".$this->categories[$i]."'
                 ";
            $res = $this->db->db_Query( $q );
            //echo "<br>q=".$q." res=".$res;
            if( !$res ) return false;
        }
    }//end of fuinction SaveSubscribe()

    // ================================================================================================
    // Function : ShowErrBackEnd()
    // Date : 31.05.2008
    // Returns :      true,false / Void
    // Description :  Show errors
    // Programmer :  Igor Trokhymchuk
    // ================================================================================================
    function ShowErrBackEnd()
    {
     if ($this->Err){
       echo '
        <fieldset class="err" title="ОШИБКИ"> <legend>ОШИБКИ</legend>
        <div class="err_text">'.$this->Err.'</div>
        </fieldset>';
     }
    } //end of fuinction ShowErrBackEnd()

    // ================================================================================================
    // Function : DelSubscribe()
    // Date : 12.11.2006
    // Returns :      true,false / Void
    // Description :  Remove data from the table
    // Programmer :  Ihor Trokhymchuk
    // ================================================================================================
    function DelSubscribe( $id_del )
    {
        $kol = count( $id_del );
        $del = 0;
        for( $i=0; $i<$kol; $i++ )
        {
         $u = $id_del[$i];
         $q = "DELETE FROM `".TblModNewsSubscr."` WHERE `id`='".$u."'";
         $res = $this->Right->Query( $q, $this->user_id, $this->module );

         $q = "DELETE FROM `".TblModNewsSubscrCat."` WHERE `subscr_id`='".$u."'";
         $res = $this->Right->Query( $q, $this->user_id, $this->module );
         if ( $res )
          $del=$del+1;
         else
          return false;
        }
      return $del;
    }

     // ================================================================================================
     // Function : NewsPostingArr()
     // Date :03.05.2008
     // Parms :     $arr - array with id of the news
     // Returns :      true,false / Void
     // Description :  show preview news titles beforesend to users
     // Programmer :  Igor Trokhymchuk
     // ================================================================================================
    function NewsPostingArr($arr)
    {
        $db1 = new DB();
        $script = 'module='.$this->module.'&display='.$this->display.'&start='.$this->start.'&sort='.$this->sort.'&fltr='.$this->fltr.'&fln='.$this->fln;
        $script = $_SERVER['PHP_SELF']."?$script";
        $this->Form->WriteHeader( $script );
        for($i=0;$i<count($arr);$i++){
            $this->Form->Hidden('id_del[]', $arr[$i]);
        }
        AdminHTML::PanelSubH( $this->Msg->show_text('TXT_DISPATCH_TITLE').': '.stripcslashes($this->dispatch_sbj) );

        $body = $this->GetEmailBody($arr);
        echo $body;
        $res = $this->CreateDispatch($arr);

        ?><br/>
        <h3><?if($res){echo $this->Msg->show_text('TXT_DISPATCH_CREATED');} else{echo $this->Msg->show_text('TXT_DISPATCH_NOT_CREATED');}?></h3>
        <br/><a href="<?=$this->script;?>"><?=$this->Msg->show_text('TXT_DISPATCH_BACK_TO_NEWS');?></a><?
        AdminHTML::PanelSubF();
        $this->Form->WriteFooter();
    } // end of NewsPostingArr

     // ================================================================================================
     // Function : GetEmailBody()
     // Date :03.05.2008
     // Parms :     $arr - array with id of the news
     // Returns :      true,false / Void
     // Description :  rerurn email bosy with news
     // Programmer :  Igor Trokhymchuk
     // ================================================================================================
    function GetEmailBody($arr)
    {
        $tmp_db = new DB();
        $db1 = new DB();

        $body='<table border="0" cellpadding="0" cellspacing="5" style="font-family: Tahoma;">';
        for($j=0;$j<count($arr);$j++){
            $q = "SELECT * FROM `".TblModNews."` WHERE `id`='".$arr[$j]."' ORDER BY `id` desc";
            $res = $tmp_db->db_Query($q);
            //echo '<br>$q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
            if (!$res or !$tmp_db->result) return false;
            //$rows = $tmp_db->db_GetNumRows();
            $row = $tmp_db->db_FetchAssoc();

            $txt = html_entity_decode( stripslashes( $this->Spr->GetNameByCod( TblModNewsSprSbj, $row['id'] ) ) );
            $short = html_entity_decode( strip_tags( stripslashes( $this->Spr->GetNameByCod( TblModNewsSprShrt, $row['id'] ) ), '<img>,<a>,<b>,<strong>,<li>,<ul>,<br>' ) );
            //$full = stripslashes( $this->Spr->GetNameByCod( TblModNewsSprFull, $row['id'] ) );
            //$categ = stripslashes( $this->Spr->GetNameByCod( TblModNewsCat, $row['id_category'] ) );
            //$sart_date = $row['start_date'];

            $link = $this->Link(NULL, $row['id']);
            $body = $body.'
            <tr><td style="border:0px solid black;">
            <a href="http://www.'.$_SERVER['SERVER_NAME'].$link.'" style="padding-top:20px; text-decoration:none; font-size:20px; font-weight:bold;">'.$txt.'</a>';
            //$body = $body.'<br/><span style="font-size:12px;">'.$categ.' / '.$this->ConvertDate($sart_date).'</span>';
            $body = $body.'<br/><p>'.$short.'</p>';
            $body = $body.'
            </td></tr>
            <tr><td><a href="http://www.'.$_SERVER['SERVER_NAME'].$link.'">'.$this->Msg->show_text('MOD_NEWS_READ_MORE').'</a></td></tr>
            <tr><td><hr></td></tr>
            ';
        }
        $body=$body.'</table>';

        return $body;
    } // end of GetEmailBody

     // ================================================================================================
     // Function : CreateDispatch()
     // Date : 05.05.2008
     // Parms :        $email - email address
     // Returns :      true,false / Void
     // Description :  create dispatch
     // Programmer :  Igor Trokhymchuk
     // ================================================================================================
     function CreateDispatch($arr)
     {
         $tmp_db = new DB();

         $q = "DELETE FROM `".TblModNewsDispatchSet."` WHERE 1";
         $res = $tmp_db->db_Query($q);
         $q = "INSERT INTO `".TblModNewsDispatchSet."` SET
               `id`='1',
               `sbj`='".$this->dispatch_sbj."'
              ";
         $res = $tmp_db->db_Query($q);
         //echo '<br>$q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
         if (!$res or !$tmp_db->result) return false;

         for($j=0;$j<count($arr);$j++){
            $q = "SELECT * FROM `".TblModNewsDispatch."` WHERE `id_news`='".$arr[$j]."'";
            $res = $tmp_db->db_Query($q);
            //echo '<br>$q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
            if (!$res or !$tmp_db->result) return false;
            $rows = $tmp_db->db_GetNumRows();
            //echo '<br>$rows='.$rows;
            if($rows==0){
                $q = "INSERT INTO `".TblModNewsDispatch."` SET
                      `id_news`='".$arr[$j]."',
                      `is_partner`='".$this->is_partner."'
                     ";
                $res = $tmp_db->db_Query($q);
                //echo '<br>$q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
                if (!$res or !$tmp_db->result) return false;
            }
         }
         return true;
     }//end of function CreateDispatch()

     // ================================================================================================
     // Function : MakeDispatch()
     // Date : 05.05.2008
     // Returns :      true,false / Void
     // Description :  create dispatch
     // Programmer :  Igor Trokhymchuk
     // ================================================================================================
     function MakeDispatch()
     {
         $tmp_db = new DB();
         //====== send to all subscribers start ==========
         $q = "SELECT * FROM `".TblModNewsDispatch."` WHERE 1 AND `is_partner`='0'";
         $res = $tmp_db->db_Query($q);
         //echo '<br>$q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
         if (!$res OR !$tmp_db->result) return false;
         $rows = $tmp_db->db_GetNumRows();
         //echo '<br>$rows='.$rows;
         //if no news to make dispatch then go out.
         if($rows>0){
             $arr=array();
             for($j=0;$j<$rows;$j++){
                 $row = $tmp_db->db_FetchAssoc();
                 $arr[$j]=$row['id_news'];
             }
             $this->MakeDispatchArr($arr, 0);
         }
         //====== send to all subscribers end ==========

         //====== send to Partners start ==========
         $q = "SELECT * FROM `".TblModNewsDispatch."` WHERE 1 AND `is_partner`='1'";
         $res = $tmp_db->db_Query($q);
         //echo '<br>$q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
         if (!$res OR !$tmp_db->result) return false;
         $rows = $tmp_db->db_GetNumRows();
         //echo '<br>$rows='.$rows;
         //if no news to make dispatch then go out.
         if($rows>0){
             $arr=array();
             for($j=0;$j<$rows;$j++){
                 $row = $tmp_db->db_FetchAssoc();
                 $arr[$j]=$row['id_news'];
             }
             $this->MakeDispatchArr($arr, 1);
         }
         //====== send to Partners end ==========
         return true;
     }//end of function MakeDispatch()

     // ================================================================================================
     // Function : MakeDispatchArr()
     // Date : 05.05.2008
     // Parms :        $arr
     //                $is_partner - send news to partners
     // Returns :      true,false / Void
     // Description :  make dispatch
     // Programmer :  Igor Trokhymchuk
     // ================================================================================================
     function MakeDispatchArr($arr, $is_partner=0)
     {
        ini_set('max_execution_time', 10000);
        $tmp_db = new DB();
        $db1 = new DB();
        $mail = new Mail();

        $q = "SELECT * FROM `".TblModNewsDispatchSet."` WHERE 1";
        $res = $db1->db_Query($q);
        $row_sbj = $db1->db_FetchAssoc();
        $subject = stripslashes($row_sbj['sbj']);

        $body = $this->GetEmailBody($arr, $is_partner);
        //echo $body;
        $arr_html_img = array();
        $arr_html_img = array_merge($arr_html_img, $mail->ConvertHtmlWithImagesForSend($body));
        //print_r($arr_html_img);
        $body = $arr_html_img['content'];

        //use table for send news to subscribers or to partners.
        if($is_partner==1) {
            $tbl_name = TblModPartners;
        }
        else $tbl_name = TblModNewsSubscr;

        $this->subscr_cnt=45;
        //$this->subscr_start = $this->subscr_start+1;
        $q = "SELECT * FROM `".$tbl_name."` WHERE 1 AND `status`='1'";
        $res = $db1->db_Query($q);
        $this->subcsr = $db1->db_GetNumRows();

        $q = "SELECT * FROM `".$tbl_name."` WHERE `is_send`='0' AND `user_status`='1' ORDER BY `id` asc LIMIT ".$this->subscr_cnt;
        $res = $db1->db_Query($q);
        //echo '<br>$q='.$q.' res='.$res.' $db1->result='.$db1->result;
        if (!$res or !$db1->result) return false;
        $rows = $db1->db_GetNumRows();
        //$rows=1;
        for($i=0;$i<$rows;$i++){
            $row = $db1->db_FetchAssoc();
            echo '<br>'.($this->subscr_start+$i+1).'. '.$row['login'];
            $mail = new Mail();
            foreach($arr_html_img as $key=>$value){
                //echo '<br>$key='.$key;
                if( $key!='content') $mail->AddAttachment($key);
            }

            //$row['email'] = 'ihor@seotm.com';
            $tmp_arr_emails = explode(',',$row['login']);
            //print_r($tmp_arr_emails); echo '<br>count($tmp_arr_emails)='.count($tmp_arr_emails);

            $cnt_emails = count($tmp_arr_emails);
            if( $cnt_emails>1){
                for($tmp_i=0;$tmp_i<$cnt_emails;$tmp_i++){
                    $email = addslashes(trim($tmp_arr_emails[$tmp_i]));
                    echo '<br>$email='.$email;
                    $mail->AddAddress($email);
                }
            }
            else {
                echo '<br>$email='.$row['login'];
                $mail->AddAddress(addslashes($row['login']));
            }

            //$mail->AddAddress('ihor@seotm.com');

            $mail->WordWrap = 500;
            //$mail->IsMail();
            $mail->IsHTML( true );
            $mail->Subject = $subject;
            $mail->Body = $body;
            $res = $mail->SendMail();
            if( $res ) {
                $q = "UPDATE `".$tbl_name."` SET `is_send`='1' WHERE `id`='".$row['id']."'";
                //echo '<br>$q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
                $res = $tmp_db->db_Query($q);
                echo ' - OK!';
            }
            else echo ' - ОШИБКА!';
        }//end for
        //check if email send to all subscribers
        $q = "SELECT * FROM `".$tbl_name."` WHERE `user_status`='1' AND `is_send`='0'";
        $res = $db1->db_Query($q);
        $rows = $db1->db_GetNumRows();
        if($rows==0){
            $q = "DELETE FROM `".TblModNewsDispatchSet."` WHERE 1";
            $res = $db1->db_Query($q);

            $q = "DELETE FROM `".TblModNewsDispatch."` WHERE 1";
            $res = $db1->db_Query($q);

            $q = "UPDATE `".$tbl_name."` SET `is_send`='0'";
            $res = $db1->db_Query($q);
        }
        return true;
     }//end of function MakeDispatchArr()

     // ================================================================================================
     // Function : StopDispatch()
     // Date : 11.09.2009
     // Returns :      true,false / Void
     // Description :  stop dispatch
     // Programmer :  Ihor Trokhymchuk
     // ================================================================================================
     function StopDispatch()
     {
         $db1 = new DB();
         $q = "DELETE FROM `".TblModNewsDispatchSet."` WHERE 1";
         $res = $db1->db_Query($q);

         $q = "DELETE FROM `".TblModNewsDispatch."` WHERE 1";
         $res = $db1->db_Query($q);

         $q = "UPDATE `".TblModNewsSubscr."` SET `is_send`='0'";
         $res = $db1->db_Query($q);

         if( defined("TblModPartners")){
            $q = "UPDATE `".TblModPartners."` SET `is_send`='0'";
            $res = $db1->db_Query($q);
         }
     }


     // ================================================================================================
     // Function : SaveRelatProdToNews()
     // Version : 1.0.0
     // Date : 28.11.2008
     // Parms : $id_news - id of the news
     // Returns :      true,false / Void
     // Description :  save realit products tho current news
     // ================================================================================================
     // Programmer :  Igor Trokhymchuk
     // Date : 28.11.2008
     // Reason for change : Creation
     // Change Request Nbr:
     // ================================================================================================
     function SaveRelatProdToNews($id_news, $arr_relat_prop)
     {
        $db1 = new DB();
        $arr = array();
        $q = "DELETE FROM `".TblModNewsRelatProd."` WHERE `id_news`='".$id_news."'";
        $res = $db1->db_Query($q);
        //echo '<br>$q='.$q.' $res='.$res.' $db1->result='.$db1->result;
        if( !$res OR ! $db1->result) return false;

        $rows = count($arr_relat_prop);
        for($i=0;$i<$rows;$i++){
            if( !isset($arr_relat_prop[$i]) OR empty($arr_relat_prop[$i]) ) continue;
            $q = "INSERT INTO `".TblModNewsRelatProd."` SET
                  `id_news`='".$id_news."',
                  `id_prod`='".$arr_relat_prop[$i]."'
                  ";
            $res = $db1->db_Query($q);
            //echo '<br>$q='.$q.' $res='.$res.' $db1->result='.$db1->result;
            if( !$res OR ! $db1->result) return false;
        }
        return true;
     }//end of function SaveRelatProdToNews()


    /**
    * Class method ImportEdifierNews
    * function for import data from old Edifier News to new
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 08.10.2011
    * @return true or false
    */
    function ImportEdifierNews()
    {
        define("TblModProd","mod_products");
        define("TblModNewsEd", "mod_news_ed");
        define("TblModNewsEdSprSbj", "mod_news_ed_spr_subject");
        define("TblModNewsEdSprShort", "mod_news_ed_spr_short");
        define("TblModNewsEdSprFull", "mod_news_ed_spr_full");
        define("TblModNewsEdRelatProd","mod_news_ed_relat_prod");
        define("TblModNewsEdSubscr","mod_news_ed_subscr");


        //формирую соответствие старого id товара к новому
        $q = "SELECT
              `".TblModCatalogPropSprName."`.`cod`,
              `".TblModProd."`.`id`
              FROM `".TblModCatalogPropSprName."`, `".TblModProd."`
              WHERE `".TblModProd."`.`model`=`".TblModCatalogPropSprName."`.`name`";
        $res = $this->db->db_Query( $q );
        echo '<br />$q='.$q.' $this->db->result='.$this->db->result;
        if( !$res OR !$this->db->result) return false;
        $rows = $this->db->db_GetNumRows();
        $prod_old_new = array();
        for($i=0;$i<$rows;$i++){
            $sss = $this->db->db_FetchAssoc();
            $prod_old_new[$sss['id']] = $sss['cod'];

        }

        // достаю все старые новости
        $q = "SELECT
              `".TblModNewsEd."`.*,
              `".TblModNewsEdSprSbj."`.`name` AS `sbj`,
              `".TblModNewsEdSprShort."`.`name` AS `short`,
              `".TblModNewsEdSprFull."`.`name` AS `full`
              FROM `".TblModNewsEd."`
              LEFT JOIN `".TblModNewsEdSprSbj."` ON (`".TblModNewsEd."`.`id`=`".TblModNewsEdSprSbj."`.`cod` AND `".TblModNewsEdSprSbj."`.`lang_id`='3')
              LEFT JOIN `".TblModNewsEdSprShort."` ON (`".TblModNewsEd."`.`id`=`".TblModNewsEdSprShort."`.`cod` AND `".TblModNewsEdSprShort."`.`lang_id`='3')
              LEFT JOIN `".TblModNewsEdSprFull."` ON (`".TblModNewsEd."`.`id`=`".TblModNewsEdSprFull."`.`cod` AND `".TblModNewsEdSprFull."`.`lang_id`='3')
              WHERE 1
              ORDER BY `".TblModNewsEd."`.`display` asc";
        $res = $this->db->db_Query( $q );
        echo '<br />$q='.$q.' $this->db->result='.$this->db->result;
        if( !$res OR !$this->db->result) return false;
        $rows = $this->db->db_GetNumRows();
        $arr_prod = array();
        for($i=0;$i<$rows;$i++){
            $arr_prod[$i] = $this->db->db_FetchAssoc();

        }

        //формирую спписок подвязаннх товаров к новости, при чем на id старой новости вешаю новый id товара
        $q = "SELECT * FROM `".TblModNewsEdRelatProd."` WHERE 1";
        $res = $this->db->db_Query( $q );
        echo '<br />$q='.$q.' $this->db->result='.$this->db->result;
        if( !$res OR !$this->db->result) return false;
        $rows = $this->db->db_GetNumRows();
        $arr_relat_prod = array();
        for($i=0;$i<$rows;$i++){
            $rrr = $this->db->db_FetchAssoc();
            $arr_relat_prod[$rrr['id_news']][] = $prod_old_new[$rrr['id_prod']];

        }
        $cnt_prod = count($arr_prod);
        echo '<br>$cnt_prod=',$cnt_prod;
        for($i=0;$i<$cnt_prod;$i++){
            echo '<br>===== START OF '.$i.' =====';
            $row = $arr_prod[$i];

            $q = "SELECT
                  `".TblModNews."`.*,
                  `".TblModNewsSprSbj."`.`name` AS `sbj`
                  FROM `".TblModNews."`
                  LEFT JOIN `".TblModNewsSprSbj."` ON (`".TblModNews."`.`id`=`".TblModNewsSprSbj."`.`cod` AND `".TblModNewsSprSbj."`.`lang_id`='3')
                  WHERE `start_date`='".$row['start_date']."' AND `".TblModNewsSprSbj."`.`name`='".addslashes($row['sbj'])."'
                 ";
            $res = $this->db->db_Query( $q );
            echo '<br />$q='.$q.' $this->db->result='.$this->db->result;
            if( !$res OR !$this->db->result) return false;
            $rows = $this->db->db_GetNumRows();
            echo '<br>$rows='.$rows;
            if($rows>0) continue;

            //=== Prepare input data Start ===
            $old_id_news = $row['id'];
            $this->id = NULL;
            $this->id_category = $row['id_category'];
            $this->id_relart = NULL;
            $this->status = $row['status'];
            $this->start_date = $row['start_date'];
            $this->end_date = $row['end_date'];

            $this->subj_[3] = $row['sbj'];
            $this->keywords[3] = '';
            $this->description[3] = '';
            $this->short_[3] = str_replace("http://edifier.com.ua/images/", "/images/", $row['short']);
            $this->full_[3] = str_replace("http://edifier.com.ua/images/", "/images/", $row['full']);
            //=== Prepare input data End ===

            $res = $this->save();
            echo '<br>SAVE = '.$res;
            if(!$res) continue;

            if(strstr( strtolower($row['short']), "<img")){
                $img_big = trim(strip_tags(stripslashes($row['short']),'<img>'));
                //echo '<br>$img_big2='.$img_big;
                $tmp1 = substr($img_big, (strpos($img_big, 'src')+26) );
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

                //echo '<br>$filename='.$filename.'<br> $uploaddir='.$uploaddir.'<br> $uploaddir2='.$uploaddir2;

                if ( @copy($filename,$uploaddir) ) {
                    //====== set next max value for move START ============
                    $maxx = NULL;
                    $q = "SELECT MAX(`move`) AS `maxx` FROM `".TblModNewsImg."` WHERE 1";
                    $res = $this->db->db_Query( $q );
                    $row = $this->db->db_FetchAssoc();
                    $maxx = $row['maxx']+1;
                    //====== set next max value for move END ==============

                     $q="INSERT INTO `".TblModNewsImg."` values(NULL,'".$this->id."','".$uploaddir2."','1', '".$maxx."', NULL)";
                     $res = $this->db->db_Query( $q );
                     if( !$this->db->result ) $this->Err = $this->Err.$this->multi['MSG_ERR_SAVE_FILE_TO_DB'].' ('.$img_rel_path.')<br>';
                     echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;
                }

                @chmod($uploaddir_0,0755);
            }

            //СОХРАНЯЮ ПРИВЯЗАННЫЕ ТОВАРЫ К НОВОСТИ
            if (isset($arr_relat_prod[$old_id_news]) AND count($arr_relat_prod[$old_id_news])>0){
                $cnt00 = count($arr_relat_prod[$old_id_news]);
                for($i_prod=0;$i_prod<$cnt00;$i_prod++){
                    $q="INSERT INTO `".TblModNewsRelatProd."` SET
                        `id_news`='".$this->id."',
                        `id_prod`='".$arr_relat_prod[$old_id_news][$i_prod]."'
                       ";
                    $res = $this->db->db_Query( $q );
                    echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;
                }
            }

            echo '<br>===== END OF '.$i.' =====';
        }
    }//end of function ImportEdifierNews()

} // end of newsCtrl Class
?>