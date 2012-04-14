<?php
/**
* feedbackCtrl.class.php   
* Class definition for all actions with managment of feddback on back-end
* @package Feedback Package of SEOCMS
* @author Igor Trokhymchuk  <ihor@seotm.com>
* @version 1.1, 22.12.2010
* @copyright (c) 2010+ by SEOTM
*/

/**
* Class FeedbackCtrl
* parent class of Feedback module
* @author Igor Trokhymchuk  <ihor@seotm.com>
* @version 1.1, 22.12.2010
*/ 
 class FeedbackCtrl extends Feedback {
    public $sort = NULL;
    public $sort_old = NULL;
    public $display = 50;
    public $start = 0;
    public $width = 500;
    public $fltr = NULL;

    /**
    * Class Constructor FeedbackCtrl
    * Init variables
    * @param integer $user_id - id of the user
    * @param integer $module - id of the module
    * @param integer $display - count to dispalw rows
    * @param integer $sort - fiels for sorting
    * @param integer $start - start position for display
    * @param integer $width - width of the form
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.1, 22.12.2010
    */      
    function FeedbackCtrl($user_id=NULL, $module=NULL, $display=NULL, $sort=NULL, $start=NULL, $width = NULL) {
            //Check if Constants are overrulled
            ( $user_id   !="" ? $this->user_id = $user_id  : $this->user_id = NULL );
            ( $module   !="" ? $this->module  = $module   : $this->module  = NULL );
            ( $display  !="" ? $this->display = $display  : $this->display = 10   );
            ( $sort     !="" ? $this->sort    = $sort     : $this->sort    = NULL );
            ( $start    !="" ? $this->start   = $start    : $this->start   = 0    );
            ( $width    !="" ? $this->width   = $width    : $this->width   = 750  );

            if(defined("_LANG_ID")) $this->lang_id = _LANG_ID;
            if(defined("FeedbackUseFiles")) $this->is_files = FeedbackUseFiles;
            
            if (empty($this->Rights)) $this->Rights = new Rights($this->user_id, $this->module);
            //$this->Msg->SetShowTable(TblModFeedbackSprTxt);
            if (empty($this->Form)) $this->Form = new Form('form_feedback');
            
            $this->msg =  &check_init_txt('TblBackMulti',TblBackMulti);

    } // End of Feedback Constructor

   
    /**
    * Class method GetCoutRows
    * function for select count of rows in table TblModfeedback
    * @return integer count of rows
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 22.12.2010
    */
    function GetCoutRows(){
        $q = "SELECT COUNT(`id`) AS `cnt` FROM `".TblModfeedback."` WHERE 1 ";
        $res = $this->Rights->Query( $q, $this->user_id, $this->module );
        if( !$res OR !$this->Rights->result ) return false;
        $row = $this->Rights->db_FetchAssoc();
        return intval($row['cnt']);
    }//end of function GetCoutRows();   
   
   
    /**
    * Class method GetContent
    * function for select data of feedback table
    * @param varchar $limit - select with limit or not 
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 22.12.2010
    * @return array or false
    */
    function GetContent($limit='limit'){
        if( !$this->sort ) $this->sort='id';
        $q = "SELECT * FROM `".TblModfeedback."` WHERE 1 ";
        $q .= " ORDER BY `$this->sort` $this->asc_desc";
        if($limit=='limit') $q .= " LIMIT ".$this->start.", ".$this->display;
        $res = $this->Rights->Query( $q, $this->user_id, $this->module );
        //echo '<br />$q='.$q.' $this->Rights->result='.$this->Rights->result;
        if( !$res OR !$this->Rights->result) return false;
        $rows = $this->Rights->db_GetNumRows();
        $arr = array();
        for($i=0;$i<$rows;$i++){
            $arr[$i] = $this->Rights->db_FetchAssoc();
        }
        
        return $arr;
    }//end of function GetContent();
   
    /**
    * Class method GetSerfingByContact
    * function for select serfing data for contact
    * @param integer $id - id of the contatc from mod_feedback
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 30.12.2010
    * @return array or false
    */
    function GetSerfingByContact($id){
        $q = "SELECT * FROM `".TblModFeedbackSerfing."` WHERE `id_feedback`='".$id."'";
        $res = $this->Rights->Query( $q, $this->user_id, $this->module );
        //echo '<br />$q='.$q.' $this->Rights->result='.$this->Rights->result;
        if( !$res OR !$this->Rights->result) return false;
        $rows = $this->Rights->db_GetNumRows();
        $arr = array();
        for($i=0;$i<$rows;$i++){
            $arr[$i] = $this->Rights->db_FetchAssoc();
        }
        
        return $arr;
    }//end of function GetSerfingByContact();   
   

    /**
    * Class method show
    * Show list of data
    * @return true of false
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.1, 22.12.2010
    */   
    function show(){
        
        $row_arr = $this->GetContent();
        $rows = count( $row_arr );
        
        /* Write Form Header */
        $this->Form->WriteHeader( $this->script );

        /* Write Table Part */
        AdminHTML::TablePartH();

        /* Write Links on Pages */
        echo '<TR><TD COLSPAN=9>';
        $this->Form->WriteLinkPages( $this->script, $this->GetCoutRows(), $this->display, $this->start, $this->sort );

        echo '<TR><TD COLSPAN=6>';
        $this->Form->WriteTopPanel( $this->script, 2);
        ?>
        <script type="text/javascript">
        function send_print(){
            //alert($(".check0").val());
            //$(".check0").val();
            var x = $('.check0');
            str='';
            len=x.length;
            flag=0;
            for (i=0; i<len; i++)
            {
                //alert(x[i].checked);
                if(x[i].checked==true) {
                    if(flag==0) flag=1;
                    else str=str+';';
                    str=str+x[i].value;
                }
            }
            //alert(str);
            if(str=='') {
                alert('<?=$this->msg['TXT_NOTHING_SELECTED']?>');
                return false;
            }
            window.open("/modules/mod_feedback/feedback_ajax.php?task=print_all&id_zvon=1&id_del="+str+"", "", "width=620px, height=400px, status=0, toolbar=0, location=100, menubar=0, resizable=0, scrollbars=1");
        }
        </script>
        <a class="r-button"  href="#" onclick="send_print(); return false;">
        <span><span>
         <img src="images/icons/save.png" alt="<?=$this->msg['TXT_PRINT_FEEDBACK']?>" title="<?=$this->msg['TXT_PRINT_FEEDBACK']?>" align="center" name="save" /><?=$this->msg['TXT_PRINT_FEEDBACK']?>
        </span></span>
        </a>
        <?
        //echo '<br>$this->asc_desc='.$this->asc_desc;
        $script2 = 'module='.$this->module.'&display='.$this->display.'&start='.$this->start.'&task=show&fltr='.$this->fltr.'&sort_old='.$this->sort.'&asc_desc='.$this->asc_desc;
        $script2 = $_SERVER['PHP_SELF']."?$script2";
        
        if($rows>$this->display) $ch = $this->display;
        else $ch = $rows;
        //$txtSortData = $this->Msg->show_text('_TXT_SORT_DATA', TblModFeedbackSprTxt);
       ?>
        <TR>
        <td class="THead"><input value="0" id="cAll" onclick="if (this.value == '1') {unCheckAll(<?=$ch;?>); this.value = '0';} else {checkAll(<?=$ch;?>); this.value = '1';}" type="checkbox"></Th>
        <td class="THead"><?=$this->Form->LinkSort( $this->sort, 'id', $script2, $this->asc_desc, $this->msg['FLD_ID']);?></Th>
        <?if($this->is_files==1){?>
            <td class="THead"><?=$this->Form->LinkSort( $this->sort, 'fpath', $script2, $this->asc_desc, $this->msg['ATTACHED_FILE']);?></Th>
        <?}?>
        <td class="THead"><?=$this->Form->LinkSort( $this->sort, 'f_name', $script2, $this->asc_desc, $this->msg['_FLD_F_NAME']);?></Th>
        <td class="THead"><?=$this->Form->LinkSort( $this->sort, 'e_mail', $script2, $this->asc_desc, $this->msg['_FLD_E_MAIL']);?></Th>
        <Th class="THead"><?=$this->msg['_FLD_MESSAGE'];?></Th>         
        <td class="THead" ><?=$this->Form->LinkSort( $this->sort, 'date', $script2, $this->asc_desc, $this->msg['_FLD_DATE']);?></Th>
        <td class="THead"><?=$this->Form->LinkSort( $this->sort, 'refpage', $script2, $this->asc_desc, $this->msg['TRACK_CONTACT']);?></Th>
        <?
        $style1 = 'TR1';
        $style2 = 'TR2';
        for( $i = 0; $i < $rows; $i++ )
        {
          $row = $row_arr[$i];
          if ( (float)$i/2 == round( $i/2 ) ) $class_tr = $style1;
          else $class_tr = $style2;
          ?>
          <tr class="<?=$class_tr;?>">
            <td><?=$this->Form->CheckBox( "id_del[]", $row['id'], null, "check".$i );?></td>
            <td><?=$this->Form->Link( $this->script."&task=edit&id=".$row['id'], stripslashes( $row['id'] ), $this->msg['_TXT_LISTEN_DATA'] );?></td>
            <?if($this->is_files==1){?>
                <td align="center"><?
                if( !empty($row['fpath'])){
                    $path = FeedbackUploadFilesPath.$row['fpath'];
                    ?><a href="<?=$path;?>"><img src="images/icons/files.png" alt="" title="<?=$row['fpath'];?>" border="0" /></a><?
                }
                ?>
                </td>
            <?}?>
            <td align="left"><?=stripslashes($row['f_name']);?></td>
            <td align="left"><?=stripslashes($row['e_mail'])?></td>
            <td align="left" ><?
                $msg = stripslashes($row['message']);
                if( strlen($msg)>300 ){echo substr($msg, 0, 300).'...'; $this->Form->Link( $this->script."&task=edit&id=".$row['id'], $this->msg['TXT_DETAILS'], $this->msg['TXT_DETAILS'] );}
                else echo $msg;
                ?>
            </td>
            <td align="left" width="70"><?=stripslashes($row['date']);?></td>
            <td align="left"><?=urldecode(stripslashes($row['refpage']));?></td>
          </tr>
          <?
        } //-- end for

        AdminHTML::TablePartF();
        $this->Form->WriteFooter();
        return true;

    } //end of fuinction show


    /**
    * Class method edit
    * Show data for edit
    * @return true of false
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.1, 22.12.2010
    */    
    function edit(){
        $mas=NULL; 
        if( $this->id!=NULL){
            $q = "SELECT * FROM ".TblModfeedback." where id='$this->id'";
            $res = $this->Rights->Query( $q, $this->user_id, $this->module );
            if( !$res  OR !$this->Rights->result ) return false;
            $mas = $this->Rights->db_FetchAssoc();
        }
        else return false;
        /* Write Form Header */
        $this->Form->WriteHeader( $this->script );
    
        $this->Form->Hidden( 'id', $this->id );
        $this->Form->Hidden( 'display', $this->display );
        $this->Form->Hidden( 'start', $this->start );
        $this->Form->Hidden( 'sort', $this->sort );
            
        if( $this->id!=NULL ) $txt = $this->msg['_TXT_LISTEN_DATA'];
        else $txt = $this->msg['_TXT_ADD_DATA'];
    
        AdminHTML::PanelSubH( $txt );
        //-------- Show Error text for validation fields --------------
        $this->ShowErrBackEnd();
        //-------------------------------------------------------------          
        AdminHTML::PanelSimpleH();
        $scriptact = 'module='.$this->module;
        $scriplink = '&display='.$this->display.'&start='.$this->start.'&sort='.$this->sort;
        ?>
        <table border="0" cellpadding="0" cellspacing="5" width="100%">
            <tr>
                <td valign="top">
                    <table border="0" cellpadding="5" cellspacing="1" width="400">
                        <tr  CLASS="TR2"><td align="left"><?=$this->msg['_FLD_F_NAME'];?>:</td><td align="left" width="85%"><?=$mas['f_name'];?></td></tr>
                        <tr  CLASS="TR1"><td align="left"><?=$this->msg['COMPANY'];?>:</td><td align="left"><?=$mas['fax'];?></td></tr>
                        <tr  CLASS="TR2"><td align="left"><?=$this->msg['FLD_PHONE'];?>:</td><td align="left"><?=$mas['tel'];?></td></tr>
                        <tr  CLASS="TR1"><td align="left"><?=$this->msg['_FLD_E_MAIL'];?>:</td><td align="left"><a href="mailto:<?=$mas['e_mail'];?>"><?=$mas['e_mail'];?></a></td></tr>
                        <tr  CLASS="TR2"><td align="left"><?=$this->msg['_FLD_DATE'];?>:</td><td align="left"><?=$mas['date'];?></td></tr>
                        <?
                        if( !empty($mas['fpath']) ) {?><tr  CLASS="TR1"><td align="left"><?=$this->msg['ATTACHED_FILE'];?>:</td><td align="left"><a href="<?=FeedbackUploadFilesPath.$mas['fpath'];?>"><?=$mas['fpath'];?></a></td></tr><?}?>
                        <tr  CLASS="TR2"><td colspan="2" align="left"><?=$this->msg['_FLD_MESSAGE'];?></td></tr>
                        <tr  CLASS="TR1"><td colspan="2" align="left"><textarea style="width:100%; height:400px;" readonly="readonly"><?=stripslashes($mas['message']);?></textarea></td></tr>        
                    </table>
                </td>
                <td valign="top">
                    <h4><?=$this->msg['TRACK_CONTACT'];?></h4>
                    <?
                    if(!empty($mas['refpage'])){
                        $txt = urldecode($mas['refpage']);
                    }
                    else $txt = $this->msg['HTTP_REFERER_NONE'];
                    ?>
                    <div class="TR1" style="text-align:left;"><?=$this->msg['HTTP_REFERER'];?>:&nbsp;<?=$txt;?></div>

                    <table border="0" cellpadding="5" cellspacing="1" width="100%">
                        <tr CLASS="TR2"> 
                            <td colspan="3" align="left"><?=$this->msg['PAGE_SERFING'];?></td>
                        </tr>
                        <tr CLASS="TR1">
                            <td align="left"><?=$this->msg['PAGE_SERFING_URL'];?></td><td width="122"><?=$this->msg['PAGE_SERFING_TIME_START'];?></td><td width="70"><?=$this->msg['PAGE_SERFING_TIME'];?></td>
                        </tr>
                        <?
                        $arr_rows = $this->GetSerfingByContact($this->id);
                        $rows = count($arr_rows);
                        $style1 = 'TR1';
                        $style2 = 'TR2';
                        for($i=0;$i<$rows;$i++){
                            if ( (float)$i/2 == round( $i/2 ) ) $class_tr = $style2;
                            else $class_tr = $style1;
                            ?>
                            <tr class="<?=$class_tr;?>">
                                <td align="left"><?=stripslashes($arr_rows[$i]['uri']);?></td>
                                <td nowrap="nowrap"><?=strftime("%Y-%m-%d %H:%M:%S",$arr_rows[$i]['tstart']);?></td>
                                <td nowrap="nowrap"><?=$arr_rows[$i]['tstay'];?></td>
                            </tr>
                            <?
                        }
                        ?>
                    </table>
                </td>
            </tr>
        </table>
        <hr style="color:#666666" size="1px"; />
        <? 
        $this->Form->Hidden( "id_del[0]", $this->id );
        $this->Form->WriteTopPanel( $this->script, 2);
        ?>
        <a class="r-button" href="javascript:window.history.go(-1);" onmouseout="MM_swapImgRestore();" onmouseover="MM_swapImage('restore','','images/icons/restore_f2.png',1);">
            <span><span><IMG src='images/icons/restore.png' width="23" height="23" alt="Go to:" align="middle" border="0" name="restore" />&nbsp;&nbsp;<?=$this->msg['BTN_BACK'];?></span></span></a>
        <?

        echo '<TR><TD COLSPAN=2 ALIGN=left>';
       // $this->Form->WriteSavePanel( $this->script );
       //$this->Form->WriteCancelPanel( $this->script );
        ?></table><?;
        AdminHTML::PanelSimpleF();
        AdminHTML::PanelSubF();

        $this->Form->WriteFooter();
        return true;
    } //end of fuinction edit


    /**
    * Class method del
    * Remove data from the table 
    * @return true of false
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.1, 22.12.2010
    */      
    function del( $id_del )
    {
        $kol = count( $id_del );
        $del = 0;
        for( $i=0; $i<$kol; $i++ ){
            $u = $id_del[$i];
            
            $q="SELECT * FROM `".TblModfeedback."` WHERE `id`='".$u."'";
            $res = $this->Rights->Query( $q, $this->user_id, $this->module );
            if (!$res OR !$this->Rights->result) return false;
            $row = $this->Rights->db_FetchAssoc();
            if ( !empty($row['fpath']) ){
                $path = $_SERVER['DOCUMENT_ROOT'].FeedbackUploadFilesPath.$row['fpath'];
                //echo '<br>$path='.$path;
                if ( file_exists($path) ) {
                    $res = unlink ($path);
                    //if( !$res ) return false;
                }
            }         
         
            $q="DELETE FROM `".TblModfeedback."` WHERE `id`='".$u."'";
            $res = $this->Rights->Query( $q, $this->user_id, $this->module );
            if (!$res OR !$this->Rights->result) return false;
            $del=$del+1;
         }
         return $del;
    } //end of fuinction del()
    

    /**
    * Class method ShowErrBackEnd
    * show errors 
    * @return true of false
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.1, 22.12.2010
    */     
    function ShowErrBackEnd()
    {
      if ($this->Err){
        echo '
         <table border=0 cellspacing=0 cellpadding=0 class="err" align="center">
          <tr><td align="left">'.$this->Err.'</td></tr>
         </table>';
      }
    } //end of fuinction ShowErrBackEnd()    

 } // End of class Feedback


?>