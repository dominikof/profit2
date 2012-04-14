<?php 
// ================================================================================================
//    System     : PrCSM05
//    Module     : Forms
//    Version    : 1.0.0
//    Date       : 28.01.2005
//    Licensed To:
//                 Igor  Trokhymchuk  ihoru@mail.ru
//                 Andriy Lykhodid    las_zt@mail.ru
//
//    Purpose    : Class definition for Forms of Content System Management
//
// ================================================================================================
//    Class             : Form
//    Version           : 1.0.0
//    Date              : 26.01.2005
//
//    Constructor       : Yes
//    Parms             :
//    Returns           : None
//    Description       : Form Designer
// ================================================================================================
//    Programmer        :  Andriy Lykhodid
//    Date              :  26.01.2005
//    Reason for change :  Creation
//    Change Request Nbr:  N/A
// ================================================================================================
include_once( SITE_PATH.'/sys/classes/sysLang.class.php' ); 
class Form {
    var $name = '';
    var $msg = NULL;
    var $msg_text = NULL;
    var $textarea_editor = NULL;

    // ================================================================================================
    //    Function          : Form (Constructor)
    //    Version           : 1.0.0
    //    Date              : 26.01.2005
    //    Parms             :
    //    Returns           :
    //    Description       : Form Designer (Show Form Header, Footer and Content)
    // ================================================================================================
    Function Form ( $nameform = 'f1' )
    {
     $this->name = $nameform;
    } //end of Constructor Form()

    // ================================================================================================
    // Function : WriteHeaderFormImg()
    // Version : 1.0.0
    // Date : 08.05.2005
    // Parms :
    // Returns : true,false / Void
    // Description : Write Header for form with images
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 26.01.2005
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function WriteHeaderFormImg( $scriptact='' )
    {
        ?>
        <form action="<?=$scriptact;?>" method="post" name="<?=$this->name;?>" id="<?=$this->name;?>" enctype='multipart/form-data'>
        <input type="hidden" name="task" id="task" value=""/>
        <?
    } // end of function WriteHeaderFormImg()

    // ================================================================================================
    // Function : WriteHeader()
    // Version : 1.0.0
    // Date : 26.01.2005
    // Parms :
    // Returns : true,false / Void
    // Description : Write Form Header
    // ================================================================================================
    // Programmer : Andriy Lykhodid
    // Date : 26.01.2005
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function WriteHeader( $scriptact='', $params=NULL )
    {
        ?>
        <form action="<?=$scriptact;?>" method="post" name="<?=$this->name;?>" id="<?=$this->name;?>" enctype='multipart/form-data' <?=$params;?> >
        <input type="hidden" name="task" id="task" value=""/>
        <input type="hidden" name="id" value=""/>
        <input type="hidden" name="replace_to" value=""/>
        <input type="hidden" name="move" value=""/>

        <script language="JavaScript">
         var nameform = '';  
         function keypress(uri, div_id, id){
            k = (document.all)?event.keyCode : arguments.callee.caller.arguments[0].which;
//            alert('k='+k);
            if(k==13){
              nameform = '<?=$this->name?>'; 
              document.<?=$this->name?>.task.value='replace';
              document.<?=$this->name?>.id.value=id;
              document.<?=$this->name?>.replace_to.value = document.getElementById(id).value;
              //alert('replace_to='+document.nameform.replace_to.value);
              $.ajax({
                    type: "POST",
                    dataType : "html",
                    data: '&task=replace&replace_to=' + document.getElementById(id).value + '&id=' + id,
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
         }
         function up_down(uri, div_id, task, field, val){
            document.<?=$this->name?>.task.value=task;
            //str = 'document.<?=$this->name?>.'+field+'.value = '+val;
            //alert('str='+str);
            //eval(str);
            //document.<?=$this->name?>.move.value = val;
            //alert('task='+document.<?=$this->name?>.task.value);             
            //alert('move='+document.<?=$this->name?>.move.value);
            $.ajax({
                    type: "POST",
                    dataType : "html",
                    data: '&task='+document.<?=$this->name?>.task.value + '&'+ field + '=' + val,
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
    } //end of function WriteHeader()

    // ================================================================================================
    // Function : WriteFooter()
    // Version : 1.0.0
    // Date : 26.01.2005
    // Parms :
    // Returns : true,false / Void
    // Description : Write Form Footer
    // ================================================================================================
    // Programmer : Andriy Lykhodid
    // Date : 26.01.2005
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function WriteFooter()
    {
    ?>
     </form>
    <?
    } //end of function WriteFooter()


    // ================================================================================================
    // Function : WriteLinkPages()
    // Version : 1.0.0
    // Date : 26.01.2005
    //
    // Parms :
    // Returns : true,false / Void
    // Description : Write Link Pages
    // ================================================================================================
    // Programmer : Andriy Lykhodid
    // Date : 26.01.2005
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function WriteLinkPages( $scriptact, $rows, $display, $start, $sort ,$ajax=false)
    {
        if(!isset($this->msg_text)) $this->msg_text=&check_init_txt('TblBackMulti',TblBackMulti);
     $scriptD = "$scriptact&start=$start&sort=$sort";
     echo '<table class="LinksPanel" cellpading="0" cellspacing="0" width="100%"><tr><td align="center">';
     $this->WriteSelectCountRows( $display, $scriptD, $rows,$ajax);
     if( empty( $display ) ) $display = 10;
     echo ' of (',$rows,')';

     echo '<tr><td align="center">'.$this->msg_text['_TXT_PAGES'].': ';
      $start_=0;
      $end_=0;
      $way=ceil($rows/$display);
      for( $i=1; $i<=$way; ++$i)
      {
       $start_=$end_;
       if( ( $end_+$display) > $rows )
         $end_=$rows;
       else
         $end_=$end_+$display;
       if( $start==$start_ AND $end_<=($start+$display) )
          echo '<b class="LinkPagesSel">'.$i.'</b>';
       else{
       $script = "$scriptact&display=$display&start=$start_&sort=$sort";
       
       if(!$ajax){
        ?>
            <a href=<?=$script;?> class="LinkPages">
        <?}else{?>
                <a href="#" onclick="reloadCatalogInner('','<?=$script?>'); return false;" class="LinkPages">    
        <?}?>
      &nbsp;<?echo $i;?>&nbsp;</a>
      <?}
      }
      if(!$ajax)
         echo ' | &nbsp;<a class="LinkPages" href='.$scriptact.'&start=0&display='.$rows; 
      else echo ' | &nbsp;<a class="LinkPages" onclick='."reloadCatalogInner('','".$scriptact.'&start=0&display='.$rows."'); return false;".' href="#"'; 
      if(isset($sort))echo '&sort=',$sort;
      echo '>'.$this->msg_text['_TXT_ALL_PAGES'].'</a><br/>';
     echo '</table>';
    } //end of function WriteLinkPages()


    // ================================================================================================
    // Function : WriteSelectCountRows()
    // Version : 1.0.0
    // Date : 27.01.2005
    //
    // Parms :
    // Returns : true,false / Void
    // Description : Write Link Pages
    // ================================================================================================
    // Programmer : Andriy Lykhodid
    // Date : 27.01.2005
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function WriteSelectCountRows( $display, $script, $rows=NULL,$ajax=false)
    {

     if( $display>0 AND $display<10 ) $arr[$display] = $display;
     $arr[10]=10;
     $arr[20]=20;
     $arr[30]=30;
     $arr[50]=50;
     $arr[60]=60;
     $arr[70]=70;
     $arr[80]=80;
     $arr[90]=90;
     $arr[100]=100;
     if( $rows ) $arr[$rows] = $rows;

     //echo '$arr='; print_r($arr);
     //echo '<br> $rows='.$rows.' $display='.$display; 
    ?>
    <?if(!$ajax){?>
        <select name="display" onChange='location="<?=$script?>"+"&start=0&display="+this.value'>
    <?}else{?>
        <select name="display" onChange='reloadCatalogInner("","<?=$script?>"+"&start=0&display="+this.value);'>
    <?}
    $key = array_keys($arr);
    $size = sizeOf($key);
    for ($i=0; $i<$size; ++$i) 
//     while( $el=each( $arr ) )
     {
         echo '$el[key]=',$key[$i],' $rows=',$rows,' $display=',$display;
         if( $key[$i] <= $rows ){
            if( $key[$i]==$display ) $selected = 'selected'; //echo '<option value="'.$el['key'].'" selected>'.$el['value'].'</option>';
            elseif( $display>$rows AND $key[$i]== $rows ) $selected = 'selected';
            else $selected = '';
            echo '<option value="',$key[$i],'" ',$selected,'>',$arr[$key[$i]],'</option>';
         }
     }
    ?>
    </select>
    <?
    } //end of function WriteSelectCountRows()


    // ================================================================================================
    // Function : WriteSelectLangChange()
    // Version : 1.0.0
    // Date : 01.02.2005
    //
    // Parms :
    // Returns : true,false / Void
    // Description : Write Select Lang Change
    // ================================================================================================
    // Programmer : Andriy Lykhodid
    // Date : 01.02.2005
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function WriteSelectLangChange( $script, $display='', $all = NULL )
    {
     $db = DBs::getinstance();
     if( $all == 1 ) $arr = NULL;
     else $arr['']='All';
     $q = "select `cod`,`name` from `".TblSysLang."` where lang_id="._LANG_ID." order by cod";
     $res = $db->db_Query( $q );
//     echo '<br>q='.$q.' $res='.$res;
     $rows = $db->db_GetNumRows();
     for( $i=0; $i<$rows; ++$i )
     {
       $row = $db->db_FetchAssoc();
       $arr[$row['cod']] = $row['name'];
     }
     //print_r($arr);
    ?>
    <select name="fln" onChange="location='<?=$script?>'+'&fln='+this.value">
    <?
     foreach($arr as $k=>$v){
         if( $k==$display ) echo '<option value="',$k,'" selected>',$v;
         else echo '<option value="',$k,'">',$v;
     }
     //$key = array_keys($arr);
//    $size = sizeOf($key);
//    for ($i=0; $i<$size; $i++) {
//         if( $key[$i]==$display ) echo '<option value="',$key[$i],'" selected>',$arr[$key[$i]];
//         else echo '<option value="',$key[$i].'">',$arr[$key[$i]];
//     }
    
    ?>
    </select>
    <?
    } //end of function WriteSelectLangChange()


    // ================================================================================================
    // Function : WritePanelForm()
    // Version : 1.0.0
    // Date : 05.04.2005
    //
    // Parms :   $scriptact - script
    //           $par ( 0 - All Buttons, 1 - New Only, 2 - Delete Only )
    // Returns : true,false / Void
    // Description : Write Panel
    // ================================================================================================
    // Programmer : Andriy Lykhodid
    // Date : 26.01.2005
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function WriteTopPanel( $scriptact, $par = 0 )
    {
        if(!isset($this->msg_text)) $this->msg_text=&check_init_txt('TblBackMulti',TblBackMulti);
     if( $par == 0 OR $par == 1 )
     {
        ?>
         <a class="r-button" href="<?=$scriptact?>&task=new">
         <span><span><img src="images/icons/new.png" alt="<?=$this->msg_text['_BUTTON_NEW'];?>" title="<?=$this->msg_text['_BUTTON_NEW'];?>" align="center" name="new" /><?=$this->msg_text['_BUTTON_NEW'];?></span></span>
         </a>
        <?
     }

     if( $par == 0 OR $par == 2 )
     {
        ?>
         <a class="r-button" href="javascript:$('#task').val('delete');$('#<?=$this->name?>').submit();" onClick="if( !window.confirm('<?=$this->msg_text['_SYS_QUESTION_IS_DELETE'];?>')) return false;">
         <span><span><img src="images/icons/delete.png" alt="<?=$this->msg_text['TXT_DELETE'];?>" title="<?=$this->msg_text['TXT_DELETE'];?>" align="center" name="delete" /><?=$this->msg_text['TXT_DELETE'];?></span></span>
         </a>
        <?
     }
    } //end of function WriteTopPanel()

    // ================================================================================================
    // Function : WriteTopPanel2()
    // Version : 1.0.0
    // Date : 04.02.2005
    //
    // Parms :
    // Returns : true,false / Void
    // Description : Write Panel
    // ================================================================================================
    // Programmer : Andriy Lykhodid
    // Date : 04.02.2005
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function WriteTopPanel2( $scriptact, $newtask = NULL, $deltask = NULL )
    {
        if(!isset($this->msg_text)) $this->msg_text=&check_init_txt('TblBackMulti',TblBackMulti);
     if ( !empty($newtask) ){
        ?>
         <a class="r-button" href="<?=$scriptact?>&task=<?=$newtask;?>">
         <span><span><img src="images/icons/new.png" alt="<?=$this->msg_text['_BUTTON_NEW'];?>" title="<?=$this->msg_text['_BUTTON_NEW'];?>" align="center" name="new" /><?=$this->msg_text['_BUTTON_NEW'];?></span></span>
         </a>
        <?
     }
     if ( !empty($deltask) ) {
        ?>
         <a class="r-button" href="javascript:$('#task').val('delete');$('#<?=$this->name?>').submit();" onClick="if( !window.confirm('<?=$this->msg_text['_SYS_QUESTION_IS_DELETE'];?>')) return false;">
         <span><span><img src="images/icons/delete.png" alt="<?=$this->msg_text['TXT_DELETE'];?>" title="<?=$this->msg_text['TXT_DELETE'];?>" align="center" name="delete" /><?=$this->msg_text['TXT_DELETE'];?></span></span>
         </a>
        <?
     }
    } //end of function WriteTopPanel2()


    // ================================================================================================
    // Function : WriteSavePanelNew()
    // Version : 1.0.0
    // Date : 27.01.2005
    //
    // Parms :
    // Returns : true,false / Void
    // Description : Write Panel with Button Save
    // ================================================================================================
    // Programmer : Andriy Lykhodid
    // Date : 27.01.2005
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function WriteSavePanelNew( $scriptact, $task='save' )
    {
     if(!isset($this->msg_text)) $this->msg_text=&check_init_txt('TblBackMulti',TblBackMulti);   
    /*?>
    <input type="button" name="button_save" value="<?=$this->msg_text['TXT_SAVE'];?>" onClick="SaveData(); return false;"/>
    */?>
    <a class="r-button" href="#" onClick="SaveData(); return false;">
    <span><span>
     <img src="images/icons/save.png" alt="<?=$this->msg_text['TXT_SAVE'];?>" title="<?=$this->msg_text['TXT_SAVE'];?>" align="center" name="save" /><?=$this->msg_text['TXT_SAVE'];?></span></span>
    </a>
    <script language="JavaScript">
        function SaveData()
        {
           document.<?=$this->name;?>.task.value='<?=$task;?>';
           $("#<?=$this->name;?>").attr("target","_parent");
           $("#<?=$this->name;?>").attr("action","<?=$scriptact;?>");
           document.<?=$this->name;?>.submit();
        }
    </script>
    <?

    } //end of function WriteSavePanelNew()

    // ================================================================================================
    // Function : WriteSavePanel()
    // Version : 1.0.0
    // Date : 27.01.2005
    //
    // Parms :
    // Returns : true,false / Void
    // Description : Write Panel with Button Save
    // ================================================================================================
    // Programmer : Andriy Lykhodid
    // Date : 27.01.2005
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function WriteSavePanel( $scriptact, $task='save', $func=NULL )
    {
        if(!isset($this->msg_text)) $this->msg_text=&check_init_txt('TblBackMulti',TblBackMulti);
    ?>
    <a class="r-button" href="javascript:$('#task').val('<?=$task;?>');$('#<?=$this->name?>').submit();" <?if( !empty($func)) {?>onClick="<?=$func;?>"<?}?>>
    <span><span>
     <img src="images/icons/save.png" alt="<?=$this->msg_text['TXT_SAVE'];?>" title="<?=$this->msg_text['TXT_SAVE'];?>" align="center" name="save" /><?=$this->msg_text['TXT_SAVE'];?></span></span>
    </a>
    <?
    } //end of function WriteSavePanel()    
    
    // ================================================================================================
    // Function : WriteSaveAndReturnPanel()
    // Version : 1.0.0
    // Date : 20.02.2008
    //
    // Parms :
    // Returns : true,false / Void
    // Description : Write Panel with Button Save
    // ================================================================================================
    // Programmer : Ihor Trokhymchuk
    // Date : 20.02.2008
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function WriteSaveAndReturnPanel( $scriptact, $task='savereturn' )
    {
        if(!isset($this->msg_text)) $this->msg_text=&check_init_txt('TblBackMulti',TblBackMulti);
    ?>
    <a class="r-button" href="javascript:$('#task').val('<?=$task;?>');$('#<?=$this->name?>').submit();">
    <span><span>
     <img src="images/icons/save.png" alt="<?=$this->msg_text['_BUTTON_SAVE_AND_RETURN'];?>" title="<?=$this->msg_text['_BUTTON_SAVE_AND_RETURN'];?>" align="center" name="savereturn" /><?=$this->msg_text['_BUTTON_SAVE_AND_RETURN'];?>
    </span></span>
    </a>
    <?
    } //end of function WriteSaveAndReturnPanel()

    // ================================================================================================
    // Function : WriteCancelPanel()
    // Version : 1.0.0
    // Date : 27.01.2005
    //
    // Parms :
    // Returns : true,false / Void
    // Description : Write Panel with Button Cancel
    // ================================================================================================
    // Programmer : Ihor Trokhymchuk
    // Date : 27.01.2005
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function WriteCancelPanel( $scriptact )
    {
        if(!isset($this->msg_text)) $this->msg_text=&check_init_txt('TblBackMulti',TblBackMulti);
    ?>
    <a class="r-button" href="javascript:$('#task').val('cancel');$('#<?=$this->name?>').submit();">
    <span><span>
     <img src="images/icons/cancel.png" alt="<?=$this->msg_text['_BUTTON_CANCEL'];?>" title="<?=$this->msg_text['_BUTTON_CANCEL'];?>" align="center" name="cancel" /><?=$this->msg_text['_BUTTON_CANCEL'];?>
    </span></span> 
    </a>
    <?
    } //end of function WriteCancelPanel()


    // ================================================================================================
    // Function : WriteReturnPanel()
    // Version : 1.0.0
    // Date : 15.01.2007
    //
    // Parms :
    // Returns : true,false / Void
    // Description : Write Panel with Button return (back)
    // ================================================================================================
    // Programmer : Ihor Trokhymchuk
    // Date : 15.01.2007
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function WriteReturnPanel()
    {
        if(!isset($this->msg_text)) $this->msg_text=&check_init_txt('TblBackMulti',TblBackMulti);
    ?>
    <a class="r-button" href="javascript:window.history.go(-1);">
     <span><span><img src='images/icons/restore.png' alt="<?=$this->msg_text['FLD_BACK'];?>" title="<?=$this->msg_text['FLD_BACK'];?>" align="middle" name="restore" /><?=$this->msg_text['FLD_BACK'];?></span></span>
    </a>
    <?
    } //end of function WriteReturnPanel()


    // ================================================================================================
    // Function : WritePreviewPanel()
    // Version : 1.0.0
    // Date : 02.02.2005
    // Parms :
    // Returns :
    // Description : WritePreviewPanel
    // ================================================================================================
    // Programmer : Andriy Lykhodid
    // Date : 05.02.2005
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function WritePreviewPanel( $script )
    {
        if(!isset($this->msg_text)) $this->msg_text=&check_init_txt('TblBackMulti',TblBackMulti);
    ?>
     <a class="r-button" href="javascript:void(0)" onClick="window.open('<?=$script?>', 'win1', 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no');">
     <span><span><img src="images/icons/preview.png" alt="<?=$this->msg_text['_BUTTON_PREVIEW'];?>" title="<?=$this->msg_text['_BUTTON_PREVIEW'];?>" align="center" name="preview" /><?=$this->msg_text['_BUTTON_PREVIEW'];?></span></span>
     </a>
    <?
    } //end of function WritePreviewPanel()


    // ================================================================================================
    // Function : WritePreviewPanelNewWindow()
    // Version : 1.0.0
    // Date : 02.02.2005
    // Parms :
    // Returns :
    // Description : WritePreviewPanel
    // ================================================================================================
    // Programmer : Andriy Lykhodid
    // Date : 05.02.2005
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function WritePreviewPanelNewWindow( $script, $width=800, $height=600 )
    {
        if(!isset($this->msg_text)) $this->msg_text=&check_init_txt('TblBackMulti',TblBackMulti);
        $params = "OnClick='window.open(\"".$script."\", \"\", \"width=".$width.", height=".$height.", status=0, toolbar=0, location=1, menubar=0, resizable=1, scrollbars=1\");'";
        ?>
        <a class="r-button" href="javascript:void(0)" <?=$params;?>>
        <span><span><img src="images/icons/preview.png" alt="<?=$this->msg_text['_BUTTON_PREVIEW'];?>" title="<?=$this->msg_text['_BUTTON_PREVIEW'];?>" align="center" name="preview" /><?=$this->msg_text['_BUTTON_PREVIEW'];?></span></span>
        </a>
        <?
    } //end of function WritePreviewPanelNewWindow()


    // ================================================================================================
    // Function : WriteUpLoadPanel()
    // Version : 1.0.0
    // Date : 12.02.2005
    // Description : WriteUpLoadPanel
    // ================================================================================================
    // Programmer : Andriy Lykhodid
    // Date : 12.02.2005
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function WriteUpLoadPanel( $script )
    {
        if(!isset($this->msg_text)) $this->msg_text=&check_init_txt('TblBackMulti',TblBackMulti);
        ?>
        <a class="r-button" href="javascript:void(0)" onClick="window.open('<?=$script?>', 'win1', 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=300,height=80,directories=no,location=no');">
        <span><span><img src="images/icons/upload.png" alt="<?=$this->msg_text['_BUTTON_UPLOAD'];?>" title="<?=$this->msg_text['_BUTTON_UPLOAD'];?>" align="center" name="upload" /><?=$this->msg_text['_BUTTON_UPLOAD'];?></span></span>
        </a>
        <?
    } //end of function WriteUpLoadPanel()

    // ================================================================================================
    // Function : WritePublishPanel()
    // Version : 1.0.0
    // Date : 20.02.2008
    // Description : WritePublishPanel
    // ================================================================================================
    // Programmer : Andriy Lykhodid
    // Date : 20.02.2008
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function WritePublishPanel( $script )
    {
        if(!isset($this->msg_text)) $this->msg_text=&check_init_txt('TblBackMulti',TblBackMulti);
        ?>
        <a class="r-button" href="javascript:<?=$this->name?>.task.value='to_publish';<?=$this->name?>.submit();">
        <span><span><img src="images/icons/upload.png" alt="<?=$this->msg_text['_BUTTON_PUBLISH'];?>" title="<?=$this->msg_text['_BUTTON_PUBLISH'];?>" align="center" name="publish" /><?=$this->msg_text['_BUTTON_PUBLISH'];?></span></span>
        </a>
        <?
    } //end of function WritePublishPanel()

    // ================================================================================================
    // Function : Link()
    // Version : 1.0.0
    // Date : 26.01.2005
    // Parms :
    // Returns :
    // Description : Write Link
    // ================================================================================================
    // Programmer : Andriy Lykhodid
    // Date : 26.01.2005
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function Link2( $script = '', $name = 'link' )
    {
        return '<a href="'.$script.'">'.$name.'</a>';
    } //end of function Link()

    // ================================================================================================
    // Function : Link()
    // Version : 1.0.0
    // Date : 26.01.2005
    // Parms :
    // Returns :
    // Description : Write Link
    // ================================================================================================
    // Programmer : Andriy Lykhodid
    // Date : 26.01.2005
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function Link( $script = '', $name = 'link',$hint=NULL )
    {
        if(isset($hint))
            echo '<a href="'.$script.'" alt="'.$hint.'" title="'.$hint.'" class="tip">'.$name.'</a>';
        else
            echo '<a href="'.$script.'">'.$name.'</a>';
    } //end of function Link()

    // ================================================================================================
    // Function : LinkTitle()
    // Version : 1.0.0
    // Date : 19.08.2008
    // Parms :
    // Returns :
    // Description : Write Link
    // ================================================================================================
    // Programmer : Andriy Lykhodid
    // Date : 26.01.2005
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function LinkTitle( $script = '', $name = 'link', $hint = NULL )
    {
        ?>
        <a href="<?=$script;?>" class="aTHead tip" <?if( $hint ) echo " title=\"$hint\" "; ?>><?=$name?></a>
        <?
    } //end of function LinkTitle()    
    

    // ================================================================================================
    // Function : LinkSort()
    // Version : 1.0.0
    // Date : 29.08.2007
    // Parms :  $sort_now           - sortation of data result now
    //          $sort_field_name    - new sortation for link
    //          $script             - script for link
    //          $asc_desc           - type of sortation now
    //          $name               - text for link
    //          $hint               - hint for link
    // Returns :
    // Description : Write Link for sortation
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 29.08.2007
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function LinkSort( $sort_now = NULL, $sort_field_name = NULL, $script = '', $asc_desc = NULL, $name = 'link', $hint = NULL )
    {
        if(!isset($this->msg_text)) $this->msg_text=&check_init_txt('TblBackMulti',TblBackMulti);
        if($asc_desc=='asc') {$img1='c_asc.gif'; $img2='c_desc.gif';}
        else {$img1='c_desc.gif'; $img2='c_asc.gif'; }
        //echo '<br>$asc_desc='.$asc_desc.' $img1='.$img1.' $img2='.$img2;
        ?>
        <div NOWRAP><a href="<?=$script;?>&sort=<?=$sort_field_name;?>" class="aTHead" onmouseover="if(document.getElementById('soimg1')){ document.getElementById('soimg1').src='images/icons/<?=$img2;?>'; } " onmouseout="if(document.getElementById('soimg1')){ document.getElementById('soimg1').src='images/icons/<?=$img1;?>'; }" <? if( $hint ){ ?>title="<?=$hint;?>" <?}?> ><?=$name?></a>
        <?if($sort_now==$sort_field_name) { ?>&nbsp;<img src="images/icons/<?=$img1;?>" alt="" title="" id="soimg1" /> <?}?>
        </div>
        <?
    }//end of function LinkSort()


    // ================================================================================================
    // Function : TextBox()
    // Version : 1.0.0
    // Date : 26.01.2005
    // Parms :
    // Returns :
    // Description : Write Text Box
    // ================================================================================================
    // Programmer : Andriy Lykhodid
    // Date : 26.01.2005
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function TextBox( $name = '', $value = '', $size=50, $params = NULL )
    {
        ?><input type="text" name="<?=$name;?>" size="<?=$size;?>" value="<?=htmlspecialchars($value);?>" class="txt0" <?=$params;?> /><?
    } //end of function TextBox()

    // ================================================================================================
    // Function : Button()
    // Version : 1.0.0
    // Date : 26.01.2006
    // Parms :
    // Returns :
    // Description : Write Text Box
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 26.01.2006
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function Button( $name = '', $value = '', $size=50, $params=NULL )
    {
        ?><input type="submit" class="btn0" name="<?=$name;?>" value="<?=$value;?>" size="<?=$size;?>" <?=$params;?> /><?
    } //end of function Button()

    // ================================================================================================
    // Function : ButtonSimple()
    // Version : 1.0.0
    // Date : 23.04.2008
    // Parms :
    // Returns :
    // Description : Write Text Box
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 23.04.2008 
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function ButtonSimple( $name = '', $value = '', $size=50, $params=NULL )
    {
        ?><input type="button" class="btn0" name="<?=$name;?>" value="<?=$value;?>" size="<?=$size;?>" <?=$params;?> /><?
    } //end of function ButtonSimple()    
    
    // ================================================================================================
    // Function : TextArea()
    // Version : 1.0.0
    // Date : 26.01.2005
    // Parms :
    // Returns :
    // Description : Write Text Area
    // ================================================================================================
    // Programmer : Andriy Lykhodid
    // Date : 26.01.2005
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function TextArea( $name = '', $value = '', $rows = 4, $cols = 70, $params = NULL )
    {
        ?><textarea name="<?=$name;?>" rows="<?=$rows;?>" cols="<?=$cols;?>" class="area0" <?if(!empty($params)) echo $params;?>><?=htmlspecialchars($value);?></textarea><?
    } //end of function TextArea()

    // ================================================================================================
    // Function : IncludeSpecialTextArea()
    // Date : 10.03.2005
    // Description : Include HTML Text Area
    // Programmer : Andriy Lykhodid
    // ================================================================================================
    function IncludeSpecialTextArea($editor=NULL)
    {
        if( empty($editor) ) $editor = $this->textarea_editor;
        switch($editor){
            case 'TinyMCE':
                $this->IncludeTinyMCE();
                break;
            case 'FCK':
                $this->IncludeFCK();
                break;
            case 'CK':
                $this->IncludeCK();
                break;
            case 'wysiwyg':
                $this->Includewysiwyg();
                break;
             case 'elrte':
                $this->IncludeElrte();
                break;
            default:
                $this->IncludeFCK();
                break;
        }
    }// end of function IncludeSpecialTextArea()
    
    // ================================================================================================
    // Function : SpecialTextArea()
    // Date : 03.04.2008
    // Description : Write Special Editor for TextArea
    // Programmer : Ihor Trokhymchuk
    // ================================================================================================
    function SpecialTextArea( $editor=NULL, $name, $value, $rows, $cols, $params=NULL, $lang_id=NULL, $id=NULL )
    {
        //echo '<br>$editor='.$editor.' $this->textarea_editor='.$this->textarea_editor;
        if( empty($editor) ) $editor = $this->textarea_editor;
        switch($editor){
            case 'TinyMCE_OLD':
                $this->TinyMCEArea($name, $value, $rows, $cols, $params, $lang_id, $id);
                break;

            case 'TinyMCE':
                $this->TinyMCEArea($name, $value, $rows, $cols, $params, $lang_id, $id);
                break;
            case 'FCK': 
                $width = $cols*10;
                $height = $rows*20;
                //$name=$name."[".$lang_id."]";
                $this->HTMLAreaFCK($name, $value,$width,$height);
                break;
            case 'CK': 
                $width = $cols*10;
                $height = $rows*20;
                $this->HTMLAreaCK($name, $value,$width,$height);
                break;
             case 'elrte':
                 $width = $cols*10;
                $height = $rows*20;
                $this->elrteArea($name, $value, $width,$height,"elrte");
                break;
            case 'wysiwyg': 
                $width = $cols*35;
                $height = $rows*20;
                $this->HTMLAreawysiwyg($name, $value,$width,$height,$params);
                break;
            default:
                $this->HTMLTextArea($name, $value, $rows, $cols, $params, $lang_id);
                break;
        }        
    }//end of function SpecialTextArea()    
    // ================================================================================================
    // Function : HTMLTextArea()
    // Version : 1.0.0
    // Date : 09.02.2005
    // Parms :
    // Returns :
    // Description : Write HTML Text Area
    // ================================================================================================
    // Programmer : Andriy Lykhodid
    // Date : 09.02.2005
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function HTMLTextArea( $name = '', $value = '', $rows = 4, $cols = 70, $params=NULL )
    {
    //============= for HTML Area START =================
    ?>
    <textarea id="<?=$name;?>" name="<?=$name;?>" rows="<?=$rows;?>" cols="<?=$cols;?>" class="area0" <?=$params;?>><?=$value;?></textarea>
    <?
    //============= for HTML Area END =================
    } //end of function HTMLTextArea()
    
    
    // ================================================================================================
    // Function : IncludeTinyMCE()
    // Version : 1.0.0
    // Date : 03.04.2008
    // Parms :
    // Returns :
    // Description : Include TinyMCE editor for textarea
    // ================================================================================================
    // Programmer : Ihor Trokhymchuk
    // Date : 03.04.2008
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function IncludeTinyMCE()
    {
    ?>
    <script type="text/javascript" src="/sys/js/tinymce/jquery.tinymce.js"></script>
    <script type="text/javascript" src="/sys/js/tinymce/tiny_mce.js"></script>
    <?
    } //end of function IncludeTinyMCE()    
    
    // ================================================================================================
    // Function : TinyMCEArea()
    // Date : 03.04.2008
    // Description : Write TinyMCEA Area
    // Programmer : Ihor Trokhymchuk
    // ================================================================================================
    function TinyMCEArea( $name = '', $value = '', $rows = 4, $cols = 70, $params=NULL, $lang_id=1, $id=NULL )
    {
    ?>
        <!-- Load TinyMCE -->
        <script type="text/javascript">
        	$(document).ready(function() {
        		$('#<?=$id?>tinymce<?=$lang_id?>').tinymce({
        			// Location of TinyMCE script
        			//script_url : '/sys/js/tinymce/tiny_mce.js',
                         mode : "textareas",
                        theme : "advanced",
                        language:"<?if( strtolower(_LANG_SHORT)=="ua") echo "uk"; else echo strtolower(_LANG_SHORT);?>",
                        plugins : "pagebreak,style,contextmenu,table,advhr,advimage,advlink,inlinepopups,media,searchreplace,paste,fullscreen,noneditable,visualchars,nonbreaking,template,imagemanager,filemanager",
                        //plugins : "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,imagemanager,filemanager",
                        // Theme options
                        theme_advanced_buttons1 : "code,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,formatselect,fontselect,fontsizeselect",
                        theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,forecolor,backcolor",
                        theme_advanced_buttons3 : "tablecontrols,hr,removeformat,visualaid,|,charmap,iespell,media,advhr,|,fullscreen,|,styleprops,|,visualchars,nonbreaking,pagebreak,insertimage,insertfile",
                        theme_advanced_toolbar_location : "top",
                        theme_advanced_toolbar_align : "left",
                        theme_advanced_statusbar_location : "bottom",
                        theme_advanced_resizing : true,
                        skin : "o2k7",
                        skin_variant : "silver",
                        convert_urls : false,
                        content_css : "/include/css/TinyMCE.css"

                               
        		});
        	});
        </script>
        <!-- /TinyMCE -->    
        <textarea id="<?=$id?>tinymce<?=$lang_id?>" name="<?=$name;?>" rows="<?=$rows?>" cols="<?=$cols?>" <?=$params?> class="tinymce"><?=$value?></textarea>
    <?
    } //end of function TinyMCEArea()    
    

    // ================================================================================================
    // Function : IncludeFCK()
    // Version : 1.0.0
    // Date : 02.02.2009
    // Parms :
    // Returns :
    // Description : 
    // ================================================================================================
    // Programmer : Alex Kerest
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function IncludeFCK(){
    include_once(SITE_PATH."/sys/js/fckeditor/fckeditor.php") ;
    }// end of IncludeFCK

    // ================================================================================================
    // Function : IncludeCK()
    // Version : 1.0.0
    // Date : 22.03.2010
    // Parms :
    // Returns :
    // Description : 
    // ================================================================================================
    // Programmer : Oleg Morgalyuk
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function IncludeCK(){
        ?><script type="text/javascript" src="/sys/js/ckeditor/ckeditor.js"></script>
    <script type="text/javascript" src="/sys/js/AjexFileManager/ajex.js"></script>
        <?
    }// end of IncludeCK

    // ================================================================================================
    // Function : IncludeElrte()
    // Version : 1.0.0
    // Date : 22.03.2010
    // Parms :
    // Returns :
    // Description : 
    // ================================================================================================
    // Programmer : Oleg Morgalyuk
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function IncludeElrte(){
        ?>
        <?/*<link rel="stylesheet" href="/sys/js/elrte/js/ui-themes/base/ui.all.css" type="text/css" media="screen" charset="utf-8" />*/?>
        <link rel="stylesheet" href="/sys/js/elrte/css/elrte.full.css" type="text/css" media="screen" charset="utf-8" />

        <?/*<script src="/sys/js/elrte/js/jquery-ui-1.8.13.custom.min.js" type="text/javascript" charset="utf-8"></script>*/?>
        <script src="/sys/js/elrte/js/elrte.min.js" type="text/javascript" charset="utf-8"></script>
        <script src="/sys/js/elfinder/js/elfinder.min.js" type="text/javascript" charset="utf-8"></script>
        <script src="/sys/js/elrte/js/i18n/elrte.ru.js" type="text/javascript" charset="utf-8"></script>
        <link rel="stylesheet" href="/sys/js/elfinder/css/elfinder.css" type="text/css" media="screen" title="no title" charset="utf-8" /> 
        <script src="sys/js/elrte/js/i18n/elfinder.ru.js" type="text/javascript" charset="utf-8"></script> 
      <?  
    }// end of IncludeElrte


// ================================================================================================
    // Function : Includewysiwyg()
    // Version : 1.0.0
    // Date : 22.03.2010
    // Parms :
    // Returns :
    // Description : 
    // ================================================================================================
    // Programmer : Oleg Morgalyuk
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function Includewysiwyg(){
        ?><script type="text/javascript" src="/sys/js/jquery.js"></script>
            <script type="text/javascript" src="/sys/js/jwysiwyg/jquery.wysiwyg.js"></script>
            <link rel="stylesheet" href="/sys/js/jwysiwyg/jquery.wysiwyg.css" type="text/css" /><?
    }// end of Includewysiwyg



    // ================================================================================================
    // Function : HTMLAreaFCK()
    // Version : 1.0.0
    // Date : 02.02.2009
    // Parms :
    // Returns :
    // Description : 
    // ================================================================================================
    // Programmer : Alex Kerest
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function HTMLAreaFCK($name='FCKeditor1', $value='', $width='100%', $height='200' ){

    //echo "<br /> s_name = ".$_SERVER['SERVER_NAME'];

    $sBasePath = 'http://'.$_SERVER['SERVER_NAME'].'/sys/js/fckeditor/';
    //$sBasePath = substr( $sBasePath, 0, strpos( $sBasePath, "/" ) ) ;

    $oFCKeditor = new FCKeditor($name);
    $oFCKeditor->BasePath    = $sBasePath;

    $oFCKeditor->Width    = $width ;
    $oFCKeditor->Height    = $height ;

    $oFCKeditor->ToolbarSet = 'Default'; // Default | Basic

    $oFCKeditor->Config['AutoDetectLanguage']    = false ; //true
    $oFCKeditor->Config['DefaultLanguage']        = 'ru' ; // en

    $oFCKeditor->Config['SkinPath'] = $sBasePath . 'editor/skins/office2003/' ; //default  | office2003 | silver

    //if($value=='') $value = '';
    $oFCKeditor->Value        = $value;
    $oFCKeditor->Create() ;
    } // end of HTMLAreaFCK
// ================================================================================================
    // Function : HTMLAreaCK()
    // Version : 1.0.0
    // Date : 02.02.2009
    // Parms :
    // Returns :
    // Description : 
    // ================================================================================================
    // Programmer : Alex Kerest
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function HTMLAreaCK($name='FCKeditor1', $value='', $width='100%', $height='200' ){
     ?><textarea style="width:<?=$width?>px; height:<?=$height?>px;" id="<?=$name?>" name="<?=$name?>"><?=$value;?></textarea>
        <script type="text/javascript">
        var ckeditor = CKEDITOR.replace('<?=$name?>');
        AjexFileManager.init({
            returnTo: 'ckeditor',
            editor: ckeditor
        });
        </script><?
    } // end of HTMLAreaCK
// ================================================================================================
    // Function : elrteArea()
    // Version : 1.0.0
    // Date : 02.02.2009
    // Parms :
    // Returns :
    // Description : 
    // ================================================================================================
    // Programmer : Alex Kerest
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
   function elrteArea($name='editor1', $value='', $width='100%', $height='200' ){
     ?>
     <script type="text/javascript" charset="utf-8">
      $().ready(function() {
          var opts = {
              lang : 'ru',
              styleWithCss : false,
              height  : 400,
              toolbar : 'maxi',
              fmAllow  : true,
              fmOpen   : function(callback) {
                 $('<div />').elfinder({
                     url : '/sys/js/elfinder/connectors/php/connector.php',
                     lang : 'ru',
                     dialog : { width : 900, modal : true },
                     editorCallback : callback
                 })
             }
         };
          var editor = new elRTE(document.getElementById('<?=$name?>'), opts);
          
     });
 </script>
 
 <textarea style="width:<?=$width?>px; height:<?=$height?>px;" id="<?=$name?>" name="<?=$name?>"><?=$value;?></textarea>
        <?
    } // end of elrteArea
// ================================================================================================
    // Function : HTMLAreaFCK()
    // Version : 1.0.0
    // Date : 02.02.2009
    // Parms :
    // Returns :
    // Description : 
    // ================================================================================================
    // Programmer : Alex Kerest
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function HTMLAreawysiwyg($name='wysiwyg1', $value='', $width='530', $height='200',$params ){

    //echo "<br /> s_name = ".$_SERVER['SERVER_NAME'];

      ?>
      <textarea id="<?=$params?>" name="<?=$name?>" style="width:<?=$width?>px; height:<?=$height?>px;"><?=$value;?></textarea> 
        <script type="text/javascript">
          $(function(){
               $('#<?=$params?>').wysiwyg(
               {
                   controls : {
                    cut : { visible : true },
                    paste : { visible : true },
                    copy : { visible : true },
                    html : { visible : true },
                    undo  : { visible : false },
                    redo  : { visible : false }
               }
             });
          });
        </script>
      <?
    } // end of HTMLAreaFCK

    // ================================================================================================
    // Function : Select()
    // Version : 1.0.0
    // Date : 26.01.2005
    // Parms :
    // Returns :
    // Description : Write Select Box
    // ================================================================================================
    // Programmer : Andriy Lykhodid
    // Date : 26.01.2005
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function Select( $arr, $name = '', $value = NULL, $width = NULL, $params = NULL )
    {
        ?>
        <select name="<?=$name;?>" <?if( !empty($width)) {?>width="<?=$width;?>"<?}?> <?if( !strstr($params, "class")){?>class="slct0"<?}?> <?=$params;?>>
        <?
        /*$key = array_keys($arr);
        $size = sizeOf($key);
        for ($i=0; $i<$size; $i++) {
             if( $key[$i]==$display ) echo '<option value="',$key[$i],'" selected>',$arr[$key[$i]];
         else echo '<option value="',$key[$i].'">',$arr[$key[$i]];
         } */
        while( $el=each( $arr ) )
         {
          if (count($arr)==1){
          if($el['key']) echo '<option value="'.$el['key'].'" selected>'.$el['value'].'</option>'; 
          else  echo '<option value="'.$el['key'].'">'.$el['value'].'</option>'; 
          }
          else   
          if( $el['key']==$value   ) echo '<option value="'.$el['key'].'" selected>'.$el['value'].'</option>';
          else echo '<option value="'.$el['key'].'">'.$el['value'].'</option>';
         }
        ?>
        </select>
        <?
    } //end of function Select()
    
    //correct for polls
    function Select2( $arr, $name = '', $value = NULL, $width = NULL, $params = NULL )
    {
        ?>
        <select name="<?=$name;?>" <?if( !empty($width)) {?>width="<?=$width;?>"<?}?> <?if( !strstr($params, "class")){?>class="slct0"<?}?> <?=$params;?>>
        <?
        /*$key = array_keys($arr);
        $size = sizeOf($key);
        for ($i=0; $i<$size; $i++) {
             if( $key[$i]==$display ) echo '<option value="',$key[$i],'" selected>',$arr[$key[$i]];
         else echo '<option value="',$key[$i].'">',$arr[$key[$i]];
         } */
        while( $el=each( $arr ) )
         {
          if (count($arr)==2){
            if($el['key']==$value) echo '<option value="'.$el['key'].'" selected>'.$el['value'].'</option>'; 
            else  echo '<option value="'.$el['key'].'">'.$el['value'].'</option>'; 
          }else{   
            if( $el['key']==$value   ) echo '<option value="'.$el['key'].'" selected>'.$el['value'].'</option>';
            else echo '<option value="'.$el['key'].'">'.$el['value'].'</option>';
          }
         }
        ?>
        </select>
        <?
    } //end of function Select()


    // ================================================================================================
    // Function : SelectAct()
    // Version : 1.0.0
    // Date : 28.01.2005
    // Parms :
    // Returns :
    // Description : Write Select Box with Action
    // ================================================================================================
    // Programmer : Andriy Lykhodid
    // Date : 28.01.2005
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function SelectAct( $arr, $name = '', $value = NULL, $action='', $params = NULL )
    {
        ?>
        <select name="<?=$name;?>" <?=$action?> <?if( !strstr($params, "class")){?>class="slct0"<?}?> <?=$params;?>>
        <?
         while( $el=each( $arr ) )
         {
          if( $el['key']==$value ) echo '<option value="'.$el['key'].'" selected>'.$el['value'].'</option>';
          else echo '<option value="'.$el['key'].'">'.$el['value'].'</option>';
         }
        ?>
        </select>
        <?
    } //end of function SelectAct()


    // ================================================================================================
    // Function : Sel()
    // Version : 1.0.0
    // Date : 12.01.2005
    // Parms :       $arr, $value, $property, $javascript
    // Returns :
    // Description : Write Universal Select Box
    // ================================================================================================
    // Programmer : Andriy Lykhodid
    // Date : 28.01.2005
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function Sel( $arr, $value = NULL, $property = '', $javascript = '', $params = NULL )
    {
        ?>
        <select <?=$property;?> <?=$javascript?> <?if( !strstr($params, "class")){?>class="slct0"<?}?> <?=$params;?>>
        <?
         while( $el=each( $arr ) )
         {
          if( $el['key']==$value ) echo '<option value="'.$el['key'].'" selected>'.$el['value'].'</option>';
          else echo '<option value="'.$el['key'].'">'.$el['value'].'</option>';
         }
        ?>
        </select>
        <?
    } //end of function Sel()

    // ================================================================================================
    // Function : CheckBox()
    // Version : 1.0.0
    // Date : 26.01.2005
    // Parms :
    // Returns :
    // Description : Write CheckBox
    // ================================================================================================
    // Programmer : Andriy Lykhodid
    // Date : 26.01.2005
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function CheckBox( $name = 'id_del[]', $value = '', $sel = NULL, $id='', $params='', $label=NULL )
    {
        if(!empty($label)){
            ?><label><?
        }
        ?><input type="checkbox" id="<?=$id;?>" name="<?=$name;?>" value="<?=$value;?>" class="check0" <? if($sel==1) echo 'checked'; ?> <?=$params;?> />&nbsp;<?=$label;
        if(!empty($label)){
            ?></label><?
        }
    } //end of function CheckBox()     
    
    // ================================================================================================
    // Function : Radio()
    // Version : 1.0.0
    // Date : 02.04.2005
    // Parms :       $arr, $value, $property, $javascript
    // Returns :
    // Description : Write Universal Select Box
    // ================================================================================================
    // Programmer : Andriy Lykhodid
    // Date : 28.01.2005
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function Radio( $name = NULL, $txt = NULL, $value = NULL, $valuech = NULL )
    {
        ?><input type="radio" name="<?=$name;?>" value="<?=$value;?>" class="check0" <?if( $valuech == $value )echo 'checked';?> /> <?=$txt;?><?
    } //end of function Radio()


    // ================================================================================================
    // Function : Hidden()
    // Version : 1.0.0
    // Date : 27.01.2005
    // Parms :
    // Returns :
    // Description : Write input type=hidden
    // ================================================================================================
    // Programmer : Andriy Lykhodid
    // Date : 27.01.2005
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function Hidden( $name = '', $value = '' )
    {
        ?><input type="hidden" name="<?=$name;?>" value="<?=$value;?>"/><?
    } //end of function Hidden()


    // ================================================================================================
    // Function : Password()
    // Version : 1.0.0
    // Date : 29.01.2005
    // Parms :
    // Returns :
    // Description : Write input type=hidden
    // ================================================================================================
    // Programmer : Andriy Lykhodid
    // Date : 29.01.2005
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function Password( $name = '', $value = '', $size = '' )
    {
        if(!$size){$size = 10;}
        ?>
        <input type="password" name="<?=$name;?>" value="<?=$value;?>" size="<?=$size;?>"/>
        <?
    } //end of function Password()


    // ================================================================================================
    // Function : Img()
    // Version : 1.0.0
    // Date : 02.02.2005
    // Parms :
    // Returns :
    // Description : Write Img
    // ================================================================================================
    // Programmer : Andriy Lykhodid
    // Date : 02.02.2005
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function Img( $src, $alt, $parameters=NULL )
    {
        ?><img src="<?=$src;?>" alt="<?=$alt;?>" <?if( $parameters )echo $parameters;?> /><?
    } //end of function Img()



    // ================================================================================================
    // Function : ButtonCheck()
    // Version : 1.0.0
    // Date : 25.02.2005
    // Description : ButtonCheck()
    // ================================================================================================
    // Programmer : Andriy Lykhodid
    // Date : 25.02.2005
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function ButtonCheck()
    {
        ?><img src="images/icons/tick.png" /><?
    } //end of function ButtonCheck()


    // ================================================================================================
    // Function : ButtonUp()
    // Version : 1.0.0
    // Date : 25.02.2005
    // Description : Button Up
    // ================================================================================================
    // Programmer : Andriy Lykhodid
    // Date : 25.02.2005
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function ButtonUp( $id_img=NULL )
    {
        ?><img src='images/icons/uparrow.png' name="up<?=$id_img;?>" onmouseout="MM_swapImgRestore();" onmouseover="MM_swapImage('up<?=$id_img?>','','images/icons/uparrow-1.png',1);"/><?
    } //end of function ButtonUp()

    // ================================================================================================
    // Function : ButtonUpAjax()
    // Version : 1.0.0
    // Date : 07.05.2008
    // Description : Button Up
    // ================================================================================================
    // Programmer : Ihor Trokhymchuk 
    // Date : 07.05.2008
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function ButtonUpAjax( $script, $id_img=NULL, $url, $div_id, $field, $field_value )
    {
        ?><a href=<?=$script;?>&task=up&move=<?=$field_value;?> title="UP" onClick="up_down('<?=$url;?>', '<?=$div_id;?>', 'up', '<?=$field;?>', '<?=$field_value;?>'); return false;"><?=$this->ButtonUp($id_img);?></a><?
    }//end of function ButtonUpAjax()
    
    // ================================================================================================
    // Function : ButtonDown()
    // Version : 1.0.0
    // Date : 25.02.2005
    // Description : Button Up
    // ================================================================================================
    // Programmer : Andriy Lykhodid
    // Date : 25.02.2005
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function ButtonDown( $id_img=NULL )
    {
        ?><img src='images/icons/downarrow.png' name='down<?=$id_img;?>' onmouseout="MM_swapImgRestore();" onmouseover="MM_swapImage('down<?=$id_img;?>','','images/icons/downarrow-1.png',1);"/><?
    } //end of function ButtonDown()

    // ================================================================================================
    // Function : ButtonDownAjax()
    // Version : 1.0.0
    // Date : 07.05.2008
    // Description : Button Up
    // ================================================================================================
    // Programmer : Ihor Trokhymchuk 
    // Date : 25.02.2005
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function ButtonDownAjax( $script, $id_img=NULL, $url, $div_id, $field, $field_value )
    {
        ?><a href=<?=$script;?>&task=up&move=<?=$field_value;?> title="DOWN" onClick="up_down('<?=$url;?>', '<?=$div_id;?>', 'down', '<?=$field;?>', '<?=$field_value;?>'); return false;"><?=$this->ButtonDown( $id_img );?></a><?
    }//end of function ButtonDownAjax()    
    
    // ================================================================================================
    // Function : TextBoxReplace()
    // Version : 1.0.0
    // Date : 29.02.2008
    // Parms :
    // Returns :
    // Description : Write Text Box
    // ================================================================================================
    // Programmer : Ihor Trokhymchuk 
    // Date : 29.02.2008
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function TextBoxReplace( $url=NULL, $div_id=NULL, $name = '', $value = '', $id, $size=3)
    {
        ?><input type="text" name="<?=$name;?>[<?=$id;?>]" id="<?=$id;?>" value="<?=htmlspecialchars($value);?>" size="<?=$size;?>" class="txt0" style="font-size:10px;font-weight:normal;" onKeyPress="keypress('<?=$url;?>', '<?=$div_id;?>', <?=$id;?>)"/><?
    } //end of function TextBoxReplace()

    // ================================================================================================
    // Function : Replace()
    // Version : 1.0.0
    // Date : 29.02.2008
    // Description : replace position by id
    // ================================================================================================
    // Programmer : Ihor Trokhymchuk
    // Date : 29.02.2008
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function Replace( $tbl=NULL, $fld, $id, $pos )
    {
        $db = new DB();
        $db2 = new DB();
        $q = "SELECT `".$fld."` FROM `".$tbl."` WHERE `id`='".$id."'";
        $res = $db->db_Query( $q );
        //echo '<br>$q='.$q.' $res='.$res;
        if( !$res )return false;
        $row = $db->db_FetchAssoc();
        $old_val = $row["$fld"];
        if($old_val==$pos) return false;

        $q = "SELECT * FROM `".$tbl."` WHERE `id`!='".$id."'";
        if( $old_val>$pos ) $q = $q." AND `".$fld."`>='".$pos."'";
        else $q = $q." AND `".$fld."`>='".$pos."'";
        $res = $db->db_Query( $q );
        //echo '<br>$q='.$q.' $res='.$res;
        if( !$res )return false;
        $rows = $db->db_GetNumRows();

        $q = "UPDATE `".$tbl."` SET `".$fld."`='".$pos."' WHERE `id`='".$id."'";
        $res = $db2->db_Query( $q );
        //echo '<br>$q='.$q.' $res='.$res;
        if( !$res )return false;

        for($i=0;$i<$rows;$i++){
            $row = $db->db_FetchAssoc();
            $new_val = $row["$fld"]+1;
            $q = "UPDATE `".$tbl."` SET `".$fld."`='".$new_val."' WHERE `id`='".$row['id']."'";
            $res = $db2->db_Query( $q );
            //echo '<br>$q='.$q.' $res='.$res;
            if( !$res )return false;
        }
    } //end of function Replace()
    
    // ================================================================================================
    // Function : ReplaceByCod()
    // Version : 1.0.0
    // Date : 21.05.2008
    // Description : replace position by cod
    // ================================================================================================
    // Programmer : Ihor Trokhymchuk
    // Date : 21.05.2008
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function ReplaceByCod( $tbl=NULL, $fld, $id, $pos )
    {
        $db = new DB();
        $db2 = new DB();
        $q = "SELECT `".$fld."` FROM `".$tbl."` WHERE `cod`='".$id."'";
        $res = $db->db_Query( $q );
        //echo '<br>$q='.$q.' $res='.$res;
        if( !$res )return false;
        $row = $db->db_FetchAssoc();
        $old_val = $row["$fld"];
        if($old_val==$pos) return false;

        $q = "SELECT * FROM `".$tbl."` WHERE `cod`!='".$id."'";
        if( $old_val>$pos ) $q = $q." AND `".$fld."`>='".$pos."'";
        else $q = $q." AND `".$fld."`>='".$pos."'";
        $q = $q." GROUP BY `cod`";
        $res = $db->db_Query( $q );
        //echo '<br>$q='.$q.' $res='.$res;
        if( !$res )return false;
        $rows = $db->db_GetNumRows();

        $q = "UPDATE `".$tbl."` SET `".$fld."`='".$pos."' WHERE `cod`='".$id."'";
        $res = $db2->db_Query( $q );
        //echo '<br>$q='.$q.' $res='.$res;
        if( !$res )return false;

        for($i=0;$i<$rows;$i++){
            $row = $db->db_FetchAssoc();
            $new_val = $row[$fld]+1;
            $q = "UPDATE `".$tbl."` SET `".$fld."`='".$new_val."' WHERE `cod`='".$row['cod']."'";
            $res = $db2->db_Query( $q );
            //echo '<br>$q='.$q.' $res='.$res;
            if( !$res )return false;
        }
    } //end of function ReplaceByCod()    

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
    function ShowErrBackEnd($Err=NULL)
    {
     if ($Err){
       $Msg = new ShowMsg();
       echo '
        <fieldset class="err" title="'.$Msg->show_text('MSG_ERRORS').'"> <legend>'.$Msg->show_text('MSG_ERRORS').'</legend>
        <div class="err_text">'.$Err.'</div>
        </fieldset>';            
     }
    } //end of fuinction ShowErrBackEnd()
       
   // ================================================================================================
   // Function : GetRequestTxtData()
   // Version : 1.0.0
   // Date : 28.01.2009
   //
   // Parms :   $str - text string
   //           $use_strip_tags - 0 - not use function strip_tags, 1 - use function strip_tags for string $str
   // Returns : true,false / formated string
   // Description : get request text data
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 28.01.2009 
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function GetRequestTxtData($str=NULL, $use_strip_tags=0)
   {
       //echo '<br />$str='.$str; print_r($str);
       if($use_strip_tags==1) $str = strip_tags(trim($str));
       else $str = strip_tags(trim($str), '<iframe>,<a>,<abbr>,<acronym>,<address>,<applet>,<area>,<b>,<base>,<basefont>,<bdo>,<big>,<blockquote>,<br>,<caption>,<center>,<cite>,<code>,<col>,<colgroup>,<dd>,<del>,<dfn>,<dir>,<div>,<dl>,<dt>,<em>,<embed>,<font>,<h1>,<h2>,<h3>,<h4>,<h5>,<h6>,<head>,<hr>,<i>,<img>,<ins>,<isindex>,<kbd>,<label>,<legend>,<li>,<link>,<map>,<menu>,<meta>,<noframes>,<noscript>,<object>,<ol>,<optgroup>,<option>,<p>,<param>,<pre>,<q>,<s>,<samp>,<script>,<select>,<small>,<span>,<strike>,<strong>,<style>,<sub>,<sup>,<table>,<tbody>,<td>,<tfoot>,<th>,<thead>,<title>,<tr>,<tt>,<u>,<ul>,<script>,<i>');
       if( !get_magic_quotes_gpc() ) {
            $str = addslashes($str); 
       }
       return $str;       
   } //end of function GetRequestData()  
   
   // ================================================================================================
   // Function : GetRequestNumData()
   // Version : 1.0.0
   // Date : 28.01.2009
   //
   // Parms :   $str - text string
   // Returns : integer value
   // Description : get request number data
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 28.01.2009 
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function GetRequestNumData($str=NULL)
   {
       return intval($str);
   } //end of function GetRequestNumData() 
   
   function ShowMessage($id, $err) {
        if(isset($err[$id])) echo $err[$id];
    }
    
} // End of Page Class

?>
