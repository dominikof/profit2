<?php
// ================================================================================================
// Module : upload.view.php
// Version : 1.0.0
// Date : 25.06.2010
// Licensed To:
// Oleg Morgalyuk oleg@seotm.com
//
// Purpose : Class definition(view) for working with uploads (files, images, videos)
//
// ================================================================================================
//
//    Programmer        :  Oleg Morgalyuk
//    Date              :  25.06.2010
//    Reason for change :  Creation
//    Change Request Nbr:
//
//    Function          :  Class definition(view) for working with uploads (files, images, videos) 
//
//  ================================================================================================
class UploadView{
    // ================================================================================================
    //    Function          : UploadView (Constructor)
    //    Version           : 1.0.0
    //    Date              : 25.06.2010
    //    Parms             : none
    //    Returns           : none
    //    Description       : not implemented yet
    // ================================================================================================    
    function UploadView(){
        
    }
// ================================================================================================
// Function : FileUploadTop
// Version : 1.0.0
// Date : 02.07.2010
//
// Parms :   $text  / array of multilanguage captions 
// Returns : $res   / Void
// Description : Show top part of form uploading files
// ================================================================================================
// Programmer : Oleg Morgalyuk
// Date : 02.07.2010
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================     
    static function FileUploadTop($text){
        ?>
        <fieldset title="<?=$text['FLD_FILES'];?>">
            <legend><img src="images/icons/files.png" alt="<?=$text['FLD_FILES'];?>" title="<?=$text['FLD_FILES'];?>" border="0"><?=$text['FLD_FILES'];?></legend>
        <?
        
    }
// ================================================================================================
// Function : FileUploadBot
// Version : 1.0.0
// Date : 02.07.2010
//
// Parms :   null 
// Returns : $res       / Void
// Description : Show bottom part of form uploading files
// ================================================================================================
// Programmer : Oleg Morgalyuk
// Date : 02.07.2010
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================  
    static function FileUploadBot(){
        ?>
        </fieldset>
        <?
    }
// ================================================================================================
// Function : ImageUploadTop
// Version : 1.0.0
// Date : 02.07.2010
//
// Parms :   $text  / array of multilanguage captions 
// Returns : $res   / Void
// Description : Show top part of form uploading images
// ================================================================================================
// Programmer : Oleg Morgalyuk
// Date : 02.07.2010
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================ 
    static function ImageUploadTop($text){
        ?>
        <fieldset title="<?=$text['IMAGE'];?>">
            <legend><img src="images/icons/pictures.png" alt="<?=$text['IMAGE'];?>" title="<?=$text['IMAGE'];?>" border="0"><?=$text['IMAGE'];?></legend>
        <?
        
    }
// ================================================================================================
// Function : ImageUploadBot
// Version : 1.0.0
// Date : 02.07.2010
//
// Parms :   null 
// Returns : $res       / Void
// Description : Show bottom part of form uploading images
// ================================================================================================
// Programmer : Oleg Morgalyuk
// Date : 02.07.2010
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================ 
    static function ImageUploadBot(){
        ?>
        </fieldset>
        <?
        
    }
// ================================================================================================
// Function : VideoUploadTop
// Version : 1.0.0
// Date : 05.07.2010
//
// Parms :   $text  / array of multilanguage captions 
// Returns : $res   / Void
// Description : Show top part of form uploading videos
// ================================================================================================
// Programmer : Oleg Morgalyuk
// Date : 02.07.2010
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================ 
    static function VideoUploadTop($text){
        ?>
        <fieldset title="<?=$text['VIDEO'];?>">
            <legend><img src="images/icons/video.png" alt="<?=$text['VIDEO'];?>" title="<?=$text['VIDEO'];?>" border="0"><?=$text['VIDEO'];?></legend>
        <?
        
    }//VideoUploadTop
// ================================================================================================
// Function : VideoUploadBot
// Version : 1.0.0
// Date : 05.07.2010
//
// Parms :   null 
// Returns : $res       / Void
// Description : Show bottom part of form uploading videos
// ================================================================================================
// Programmer : Oleg Morgalyuk
// Date : 02.07.2010
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================ 
    static function VideoUploadBot(){
        ?>
        </fieldset>
        <?
        
    }//VideoUploadBot
// ================================================================================================
// Function : Uploadform
// Version : 1.0.0
// Date : 02.07.2010
//
// Parms :   $lang   / array of languages
//           $items  / empty fileds to show 
//           $text   / array of multilanguage captions 
//           $max    / max elements which can be loaded for each position 
//           $count  / number of files, which already loaded for position 
// Returns : $res   / Void
// Description : Show form for uploadnig files
// ================================================================================================
// Programmer : Oleg Morgalyuk
// Date : 02.07.2010
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================     
    
    static function Uploadform($lang, $items, $text,$max,$count,$type,$formId=1){
        $lang_count = count($lang);
        $lang_keys = array_keys($lang);
        $str_lang_key_JS ="";
        $name_input = '';
        if($type=="F") $name_input='file'.$formId;  
        elseif($type=="I") $name_input='image'.$formId;    
        elseif($type=="V") $name_input='video';
	$type=$type.$formId;
        if($items>=0){
        ?>
        <div class="header"><?=$text['DOWNLOAD'];?></div>
        <table cellpadding="3" cellspacing="1" border="0" class="itemUpload add">
            <tr>
                <td class="path"></td>
                <td class="heads"></td>
                <?
                    for($j=0; $j<$lang_count;$j++){
                        ?>
                        <td class="lang"><?=$lang[$lang_keys[$j]]?></td>
                        <?
                        if ($j!=0)
                        {
                            $str_lang_key_JS.=', ';
                        }
                        $str_lang_key_JS.="'". $lang_keys[$j]."'";
                    }
                    ?>
            </tr>
        </table>
        <input type="hidden" name="countLoad" id="countLoad<?=$type?>" value="<?=($items)?>" />
        <input type="hidden" name="maxcountLoad" id="maxcountLoad<?=$type?>" value="<?=($max)?>" />
        <table cellpadding="1" cellspacing="1" border="0" class="mainUpload" id="ItemToUpload<?=$type?>"><?
        for($i=0;$i<$items;$i++){
            ?><tr><td>
               <table cellpadding="3" cellspacing="1" border="0" class="itemUpload">
                <tr>
                    <td rowspan="2" class="path">
                    <input type="file" name="<?=$name_input?>[<?=$i?>]" /></td>
                    <td class="heads"><?=$text['FLD_TITLE'];?></td>
                    <?
                    for($j=0;$j<$lang_count;$j++){
                        ?>
                        <td class="lang"><input name="title<?=$type?>[<?=$lang_keys[$j]?>][<?=$i?>]" size="22" value="" /></td>
                        <?
                    }
                    ?>
                </tr>
                <tr>
                    <td class="heads"><?=$text['FLD_DESCRIPTION'];?></td>
                    <?
                    for($j=0;$j<$lang_count;$j++){
                        ?>
                        <td class="lang"><textarea cols="15" rows="2" name="description<?=$type?>[<?=$lang_keys[$j]?>][<?=$i?>]"></textarea></td>
                        <?
                    }
                    ?>
                </tr>
               </table>
            </td></tr><?
        }
        ?></table>
        <div id="addBut<?=$type?>" class="addBut"><input type="button" value="<?=$text['ADD']?>" onclick="add_el<?=$type?>()"></div>
        <script language='JavaScript'>
        var langf = new Array(<?=$str_lang_key_JS?>);
         function add_el<?=$type?>()
         {
            item = parseInt($("#countLoad<?=$type?>").val());
            max = parseInt($("#maxcountLoad<?=$type?>").val());
            //item = parseInt(document.getElementById('countLoad<?=$type?>').value);
            //max = parseInt(document.getElementById('maxcountLoad<?=$type?>').value);
            var load = <?=$count?>;
            if(item < max)
            {
            var t = parseInt(item) + 1;
            $("#countLoad<?=$type?>").val(t);
            var str = '<tr><td><table cellpadding="3" cellspacing="1" border="0" class="itemUpload"><tr><td rowspan="2" class="path">';
            str += '<input type="file" name="<?=$name_input?>['+item+']" /></td><td class="heads"><?=$text['FLD_TITLE'];?></td>';
                    
                    for(j=0;j<langf.length;j++){
                        str += '<td class="lang"><input name="title<?=$type?>['+langf[j]+']['+item+']" size="22" value="" /></td>';
                    }
                  
                str +='</tr><tr><td class="heads"><?=$text['FLD_DESCRIPTION'];?></td>';
                    for(j=0;j<langf.length;j++){
                        str += '<td class="lang"><textarea cols="15" rows="2" name="description<?=$type?>['+langf[j]+']['+item+']"></textarea></td>';
                    }
                 str += '</tr>';
            $("#ItemToUpload<?=$type?>").append(str);
            }
            else
            {
                alert('<?=$text['MAX']?>: '+ (parseInt(item)+parseInt(load)));
            }
            if(item==max){$("#addBut<?=$type?>").hide(); }
         }
          </script>
        <?
        }        
    }
/*// ================================================================================================
// Function : UploadformVideo
// Version : 1.0.0
// Date : 02.07.2010
//
// Parms :   $lang   / array of languages
//           $items  / empty fileds to show 
//           $text   / array of multilanguage captions 
//           $max    / max elements which can be loaded for each position 
//           $count  / number of files, which already loaded for position 
// Returns : $res   / Void
// Description : Show form for uploadnig video
// ================================================================================================
// Programmer : Oleg Morgalyuk
// Date : 02.07.2010
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================     
    
    static function UploadformVideo($lang, $items, $text,$max,$count){
        $lang_count = count($lang);
        $lang_keys = array_keys($lang);
        $str_lang_key_JS ="";
        if($items>0){
        ?>
        <div class="header"><?=$text['DOWNLOAD'];?></div>
        <table cellpadding="3" cellspacing="1" border="0" class="itemUpload add">
            <tr>
                <td class="path"></td>
                <td class="heads"></td>
                <?
                    for($j=0;$j<$lang_count;$j++){
                        ?>
                        <td class="lang"><?=$lang[$lang_keys[$j]]?></td>
                        <?
                        if ($j!=0)
                        {
                            $str_lang_key_JS.=', ';
                        }
                        $str_lang_key_JS.="'". $lang_keys[$j]."'";
                    }
                    ?>
            </tr>
        </table>
        <input type="hidden" name="countLoad" id="countLoadV" value="<?=($items)?>" />
        <input type="hidden" name="maxcountLoad" id="maxcountLoadV" value="<?=($max)?>" />
        <table cellpadding="1" cellspacing="1" border="0" class="mainUpload" id="ItemToUploadV"><?
        for($i=0;$i<$items;$i++){
            ?><tr><td>
               <table cellpadding="3" cellspacing="1" border="0" class="itemUpload">
                <tr>
                    <td rowspan="2" class="path"><input type="file" name="video[<?=$i?>]" /></td>
                    <td class="heads"><?=$text['FLD_TITLE'];?></td>
                    <?
                    for($j=0;$j<$lang_count;$j++){
                        ?>
                        <td class="lang"><input name="titleV[<?=$lang_keys[$j]?>][<?=$i?>]" size="22" value="" /></td>
                        <?
                    }
                    ?>
                </tr>
                <tr>
                    <td class="heads"><?=$text['FLD_DESCRIPTION'];?></td>
                    <?
                    for($j=0;$j<$lang_count;$j++){
                        ?>
                        <td class="lang"><textarea cols="15" rows="2" name="descriptionV[<?=$lang_keys[$j]?>][<?=$i?>]"></textarea></td>
                        <?
                    }
                    ?>
                </tr>
               </table>
            </td></tr><?
        }
        ?></table>
        <div id="addButV"><input type="button" value="<?=$text['ADD']?>" onclick="add_elV()"></div>
        <script language='JavaScript'>
        var lang = new Array(<?=$str_lang_key_JS?>);
         function add_elV()
         {
            item = $("#countLoadV").val();
            max = $("#maxcountLoadV").val();
            var load = <?=$count?>;
            if(item < max)
            {
            var t = parseInt(item) + 1;
            $("#countLoadV").val(t);
            var str = '<tr><td><table cellpadding="3" cellspacing="1" border="0" class="itemUpload"><tr><td rowspan="2" class="path"><input type="video" name="file['+item+']" /></td><td class="heads"><?=$text['FLD_TITLE'];?></td>';
                    
                    for(j=0;j<lang.length;j++){
                        str += '<td class="lang"><input name="titleV['+lang[j]+']['+item+']" size="22" value="" /></td>';
                    }
                  
                str +='</tr><tr><td class="heads"><?=$text['FLD_DESCRIPTION'];?></td>';
                    for(j=0;j<lang.length;j++){
                        str += '<td class="lang"><textarea cols="15" rows="2" name="descriptionV['+lang[j]+']['+item+']"></textarea></td>';
                    }
                 str += '</tr>';
            $("#ItemToUploadV").append(str);
            }
            else
            {
                alert('<?=$text['MAX']?>: '+ (parseInt(item)+parseInt(load)));
            }
            if(item==max){$("#addBut").hide(); }
         }
          </script>
        <?
        }        
    }
// ================================================================================================
// Function : UploadformImage
// Version : 1.0.0
// Date : 05.07.2010
//
// Parms :   $lang   / array of languages
//           $items  / empty fileds to show 
//           $text   / array of multilanguage captions 
//           $max    / max elements which can be loaded for each position 
//           $count  / number of files, which already loaded for position 
// Returns : $res   / Void
// Description : Show form for uploadnig images
// ================================================================================================
// Programmer : Oleg Morgalyuk
// Date : 05.07.2010
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================     
    
    static function UploadformImage($lang, $items, $text,$max,$count){
        $lang_count = count($lang);
        $lang_keys = array_keys($lang);
        $str_lang_key_JS ="";
        if($items>0){
        ?>
        <div class="header"><?=$text['DOWNLOAD'];?></div>
        <table cellpadding="3" cellspacing="1" border="0" class="itemUpload add">
            <tr>
                <td class="path"></td>
                <td class="heads"></td>
                <?
                    for($j=0;$j<$lang_count;$j++){
                        ?>
                        <td class="lang"><?=$lang[$lang_keys[$j]]?></td>
                        <?
                        if ($j!=0)
                        {
                            $str_lang_key_JS.=', ';
                        }
                        $str_lang_key_JS.="'". $lang_keys[$j]."'";
                    }
                    ?>
            </tr>
        </table>
        <input type="hidden" name="countLoad" id="countLoadI" value="<?=($items)?>" />
        <input type="hidden" name="maxcountLoad" id="maxcountLoadI" value="<?=($max)?>" />
        <table cellpadding="1" cellspacing="1" border="0" class="mainUpload" id="ItemToUploadI"><?
        for($i=0;$i<$items;$i++){
            ?><tr><td>
               <table cellpadding="3" cellspacing="1" border="0" class="itemUpload">
                <tr>
                    <td rowspan="2" class="path"><input type="file" name="image[<?=$i?>]" /></td>
                    <td class="heads"><?=$text['FLD_TITLE'];?></td>
                    <?
                    for($j=0;$j<$lang_count;$j++){
                        ?>
                        <td class="lang"><input name="titleI[<?=$lang_keys[$j]?>][<?=$i?>]" size="22" value="" /></td>
                        <?
                    }
                    ?>
                </tr>
                <tr>
                    <td class="heads"><?=$text['FLD_DESCRIPTION'];?></td>
                    <?
                    for($j=0;$j<$lang_count;$j++){
                        ?>
                        <td class="lang"><textarea cols="15" rows="2" name="descriptionI[<?=$lang_keys[$j]?>][<?=$i?>]"></textarea></td>
                        <?
                    }
                    ?>
                </tr>
               </table>
            </td></tr><?
        }
        ?></table>
        <div id="addBut"><input type="button" value="<?=$text['ADD']?>" onclick="add_el_image()"></div>
        <script language='JavaScript'>
        var lang = new Array(<?=$str_lang_key_JS?>);
         function add_el_image()
         {
            item = $("#countLoadI").val();
            max = $("#maxcountLoadI").val();
            var load = <?=$count?>;
            if(item < max)
            {
            var t = parseInt(item) + 1;
            $("#countLoadI").val(t);
            var str = '<tr><td><table cellpadding="3" cellspacing="1" border="0" class="itemUpload"><tr><td rowspan="2" class="path"><input type="file" name="image['+item+']" /></td><td class="heads"><?=$text['FLD_TITLE'];?></td>';
                    
                    for(j=0;j<lang.length;j++){
                        str += '<td class="lang"><input name="titleI['+lang[j]+']['+item+']" size="22" value="" /></td>';
                    }
                  
                str +='</tr><tr><td class="heads"><?=$text['FLD_DESCRIPTION'];?></td>';
                    for(j=0;j<lang.length;j++){
                        str += '<td class="lang"><textarea cols="15" rows="2" name="descriptionI['+lang[j]+']['+item+']"></textarea></td>';
                    }
                 str += '</tr>';
            $("#ItemToUploadI").append(str);
            }
            else
            {
                alert('<?=$text['MAX']?>: '+ (parseInt(item)+parseInt(load)));
            }
            if(item==max){$("#addBut").hide(); }
         }
          </script>
        <?
        }        
    }  */
// ================================================================================================
// Function : UploadedFiles
// Version : 1.0.0
// Date : 02.07.2010
//
// Parms :   $lang   / array of languages
//           $items  / array of items 
//           $text   / array of multilanguage captions 
//           $path   / path to download files 
//           $id_pos / id of position 
//           $table  / table to store general inormation about files 
//           $count1 / number of files, which already loaded for position 
// Returns : $res   / Void
// Description : Show form for uploadnig files
// ================================================================================================
// Programmer : Oleg Morgalyuk
// Date : 02.07.2010
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================     
    static function UploadedFiles($lang, $items, $text, $path, $id_pos, $table, $table1,$formId=1){
        $lang_count = count($lang);
        $lang_keys = array_keys($lang);
        $items_keys = array_keys($items);
        $items_count = count($items);
        if($items_count>0){
        ?>
        <div class="header"><?=$text['DOWNLOADED'];?></div>
        <table cellpadding="3" cellspacing="1" border="0" class="itemUpload add">
            <tr>
                <td class="path"></td>
                <td class="heads"></td>
                <?
                    for($j=0;$j<$lang_count;$j++){
                        ?>
                        <td class="lang"><?=$lang[$lang_keys[$j]]?></td>
                        <?
                    }
                    ?>
                <td class="see"><?=$text['_FLD_VISIBLE']?></td>
                <td class="del"><?=$text['TXT_DELETE']?></td>
            </tr>
        </table>
        
        <table cellpadding="1" cellspacing="1" border="0" class="mainUploaded" id="mainUploadedF"><?
        for($i=0;$i<$items_count;$i++){
            $file = '/'.$path.'/'.$id_pos.'/'.$items[$items_keys[$i]]['path'];
            ?><tr id="f<?=$i?><?=$formId?>"><td>
               <table cellpadding="3" cellspacing="1" border="0" class="itemUpload" width="100%">
                <tr class="nodrag">
                    <td rowspan="2" class="path">
                        <a href="<?=$file?>"><?=$items[$items_keys[$i]]['path']?></a>
                        <input type="hidden" name="moveF<?=$formId?>[]" value="<?=$items_keys[$i]?>"><br /><br />
                        <?=$text['_FLD_PATH'].': '."http://".NAME_SERVER.$file?>
                    </td>
                    <td class="heads"><?=$text['FLD_TITLE'];?></td>
                    <?
                    for($j=0;$j<$lang_count;$j++){
                        ?>
                        <td class="lang">
                            <input name="titleF<?=$formId?>s[<?=$items_keys[$i]?>][<?=$lang_keys[$j]?>]" size="22" value="<?=$items[$items_keys[$i]]['name'][$lang_keys[$j]]?>" />
                        </td>
                        <?
                    }
                    ?>
                    <td rowspan="2" class="see"><input type="checkbox" value="1" name="visibleF<?=$formId?>[<?=$items_keys[$i]?>]"<?if($items[$items_keys[$i]]['visible']==1) echo ' checked="checked"';?> /></td>
                    <td rowspan="2" class="del"><a href="#" onclick="delete_files<?=$formId?>( '<?=$table?>' ,'<?=$table1?>' ,'<?=$items_keys[$i]?>' ,'/<?=$path?>/<?=$id_pos?>/<?=$items[$items_keys[$i]]['path']?>','<?=$i?>');return false;"><img src="/admin/images/cancel.png" alt="<?=$text['TXT_DELETE']?>" title="<?=$text['TXT_DELETE']?>" /></a></td>                    
                </tr>
                <tr class="nodrag">
                    <td class="heads"><?=$text['FLD_DESCRIPTION'];?></td>
                    <?
                    for($j=0;$j<$lang_count;$j++){
                        ?>
                        <td class="lang"><textarea cols="15" rows="2" name="descriptionF<?=$formId?>s[<?=$items_keys[$i]?>][<?=$lang_keys[$j]?>]"><?=$items[$items_keys[$i]]['text'][$lang_keys[$j]]?></textarea></td>
                        <?
                    }
                    ?>
                </tr>
               </table>
            </td>
            </tr><?
            ?><?
        }
        ?></table>
        <script language='JavaScript'>
         $(document).ready(function() {
            $("#mainUploadedF").tableDnD({onDragClass: "myDragClass"});
         });
         function delete_files<?=$formId?>(t1,t2,id,path,pos)
         {
            $.ajax({
                type: "POST",
                data: 'task=delsl&id='+id+'&path='+path+'&t1='+t1+'&t2='+t2,
                url: "/sys/classes/upload/upload.php",
            success:function(msg){
                $('#f'+pos+<?=$formId?>).html('<td>'+msg+'</td> ');
            },
            beforeSend: function() {
                $('#f'+pos+<?=$formId?>).html('<td clas="imageLoad"><img src="/admin/images/ajax-loader.gif" /></td>');
            }
          });  
         }
          </script>
        <?
        }        
    }
// ================================================================================================
// Function : UploadedVideos
// Version : 1.0.0
// Date : 02.07.2010
//
// Parms :   $lang   / array of languages
//           $items  / array of items 
//           $text   / array of multilanguage captions 
//           $path   / path to download files 
//           $id_pos / id of position 
//           $table  / table to store general inormation about files 
//           $count1 / number of files, which already loaded for position 
// Returns : $res   / Void
// Description : Show form for uploadnig videos
// ================================================================================================
// Programmer : Oleg Morgalyuk
// Date : 02.07.2010
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================     
    static function UploadedVideos($lang, $items, $text, $path, $id_pos, $table, $table1){
        $lang_count = count($lang);
        $lang_keys = array_keys($lang);
        $items_keys = array_keys($items);
        $items_count = count($items);
        if($items_count>0){
        ?>
        <div class="header"><?=$text['DOWNLOADED'];?></div>
        <table cellpadding="3" cellspacing="1" border="0" class="itemUpload add">
            <tr>
                <td class="path"></td>
                <td class="heads"></td>
                <?
                    for($j=0;$j<$lang_count;$j++){
                        ?>
                        <td class="lang"><?=$lang[$lang_keys[$j]]?></td>
                        <?
                    }
                    ?>
                <td class="see"><?=$text['_FLD_VISIBLE']?></td>
                <td class="del"><?=$text['TXT_DELETE']?></td>
            </tr>
        </table>
        
        <table cellpadding="1" cellspacing="1" border="0" class="mainUploaded" id="mainUploadedV"><?
        for($i=0;$i<$items_count;$i++){
            $file = '/'.$path.'/'.$id_pos.'/'.$items[$items_keys[$i]]['path'];
            ?><tr id="v<?=$i?>"><td>
               <table cellpadding="3" cellspacing="1" border="0" class="itemUpload" width="100%">
                <tr class="nodrag">
                    <td rowspan="2" class="path">
                        <a href="<?=$file?>"><?=$items[$items_keys[$i]]['path']?></a>
                        <input type="hidden" name="moveV[]" value="<?=$items_keys[$i]?>"><br /><br />
                        <?=$text['_FLD_PATH'].': '."http://".NAME_SERVER.$file?>
                    </td>
                    <td class="heads"><?=$text['FLD_TITLE'];?></td>
                    <?
                    for($j=0;$j<$lang_count;$j++){
                        ?>
                        <td class="lang">
                            <input name="titleVs[<?=$items_keys[$i]?>][<?=$lang_keys[$j]?>]" size="22" value="<?=$items[$items_keys[$i]]['name'][$lang_keys[$j]]?>" />
                        </td>
                        <?
                    }
                    ?>
                    <td rowspan="2" class="see"><input type="checkbox" value="1" name="visibleV[<?=$items_keys[$i]?>]"<?if($items[$items_keys[$i]]['visible']==1) echo ' checked="checked"';?> /></td>
                    <td rowspan="2" class="del"><a href="#" onclick="delete_videos( '<?=$table?>' ,'<?=$table1?>' ,'<?=$items_keys[$i]?>' ,'/<?=$path?>/<?=$id_pos?>/<?=$items[$items_keys[$i]]['path']?>','<?=$i?>');return false;"><img src="/admin/images/cancel.png" alt="<?=$text['TXT_DELETE']?>" title="<?=$text['TXT_DELETE']?>" /></a></td>
                </tr>
                <tr class="nodrag">
                    <td class="heads"><?=$text['FLD_DESCRIPTION'];?></td>
                    <?
                    for($j=0;$j<$lang_count;$j++){
                        ?>
                        <td class="lang"><textarea cols="15" rows="2" name="descriptionVs[<?=$items_keys[$i]?>][<?=$lang_keys[$j]?>]"><?=$items[$items_keys[$i]]['text'][$lang_keys[$j]]?></textarea></td>
                        <?
                    }
                    ?>
                </tr>
               </table>
            </td>
            </tr><?
            ?><?
        }
        ?></table>
        <script language='JavaScript'>
         $(document).ready(function() {
            $("#mainUploadedV").tableDnD({onDragClass: "myDragClass"});
         });
         function delete_videos(t1,t2,id,path,pos)
         {
            $.ajax({
                type: "POST",
                data: 'task=delsl&id='+id+'&path='+path+'&t1='+t1+'&t2='+t2,
                url: "/sys/classes/upload/upload.php",
            success:function(msg){
                $('#v'+pos).html('<td>'+msg+'</td> ');
            },
            beforeSend: function() {
                $('#v'+pos).html('<td clas="imageLoad"><img src="/admin/images/ajax-loader.gif" /></td>');
            }
          });  
         }
          </script>
        <?
        }        
    }
// ================================================================================================
// Function : UploadedImages
// Version : 1.0.0
// Date : 05.07.2010
//
// Parms :   $lang   / array of languages
//           $items  / array of items 
//           $text   / array of multilanguage captions 
//           $path   / path to download files 
//           $id_pos / id of position 
//           $table  / table to store general inormation about files 
//           $count1 / number of files, which already loaded for position 
// Returns : $res   / Void
// Description : Show form for uploadnig images
// ================================================================================================
// Programmer : Oleg Morgalyuk
// Date : 05.07.2010
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================     
    static function UploadedImages($lang, $items, $text, $path, $id_pos, $table, $table1,$formId=1){
        $lang_count = count($lang);
        $lang_keys = array_keys($lang);
        $items_keys = array_keys($items);
        $items_count = count($items);
        if($items_count>0){
        ?>
        <div class="header"><?=$text['DOWNLOADED'];?></div>
        <table cellpadding="3" cellspacing="1" border="0" class="itemUpload add">
            <tr>
                <td class="path"></td>
                <td class="heads"></td>
                <?
                    for($j=0;$j<$lang_count;$j++){
                        ?>
                        <td class="lang"><?=$lang[$lang_keys[$j]]?></td>
                        <?
                    }
                    ?>
                <td class="see"><?=$text['_FLD_VISIBLE']?></td>
                <td class="del"><?=$text['TXT_DELETE']?></td>
            </tr>
        </table>
        
        <table cellpadding="1" cellspacing="1" border="0" class="mainUploaded" id="mainUploadedI"><?
        for($i=0;$i<$items_count;$i++){
            $file = $items[$items_keys[$i]]['path'];
            ?><tr id="I<?=$i?><?=$formId?>"><td>
               <table cellpadding="3" cellspacing="1" border="0" class="itemUpload" width="100%">
                <tr class="nodrag">
                    <td rowspan="2" class="path">
                        <img src="<?=$file?>" />
                        <input type="hidden" name="moveI<?=$formId?>[]" value="<?=$items_keys[$i]?>">
                        <br />
                        <?=$text['_FLD_PATH'].': '."http://".NAME_SERVER.'/'.$path.'/'.$id_pos.'/'.$items[$items_keys[$i]]['path_original']?>
                    </td>
                    <td class="heads"><?=$text['FLD_TITLE'];?></td>
                    <?
                    for($j=0;$j<$lang_count;$j++){
                        ?>
                        <td class="lang">
                            <input name="titleI<?=$formId?>s[<?=$items_keys[$i]?>][<?=$lang_keys[$j]?>]" size="22" value="<?=$items[$items_keys[$i]]['name'][$lang_keys[$j]]?>" />
                        </td>
                        <?
                    }
                    ?>
                    <td rowspan="2" class="see"><input type="checkbox" value="1" name="visibleI<?=$formId?>[<?=$items_keys[$i]?>]"<?if($items[$items_keys[$i]]['visible']==1) echo ' checked="checked"';?> /></td>
                    <td rowspan="2" class="del"><a href="#" onclick="delete_images<?=$formId?>( '<?=$table?>' ,'<?=$table1?>' ,'<?=$items_keys[$i]?>' ,'/<?=$path?>/<?=$id_pos?>','<?=$items[$items_keys[$i]]['path_original']?>','<?=$i?>');return false;"><img src="/admin/images/cancel.png" alt="<?=$text['TXT_DELETE']?>" title="<?=$text['TXT_DELETE']?>" /></a></td>                    
                </tr>
                <tr class="nodrag">
                    <td class="heads"><?=$text['FLD_DESCRIPTION'];?></td>
                    <?
                    for($j=0;$j<$lang_count;$j++){
                        ?>
                        <td class="lang"><textarea cols="15" rows="2" name="descriptionI<?=$formId?>s[<?=$items_keys[$i]?>][<?=$lang_keys[$j]?>]"><?=$items[$items_keys[$i]]['text'][$lang_keys[$j]]?></textarea></td>
                        <?
                    }
                    ?>
                </tr>
               </table>
            </td>
            </tr><?
            ?><?
        }
        ?></table>
        <script language='JavaScript'>
         $(document).ready(function() {
            $("#mainUploadedI").tableDnD({onDragClass: "myDragClass"});
         });
         function delete_images<?=$formId?>(t1,t2,id,path,img,pos)
         {
            $.ajax({
                type: "POST",
                data: 'task=delslImages&id='+id+'&path='+path+'&t1='+t1+'&t2='+t2+'&img='+img,
                url: "/sys/classes/upload/upload.php",
            success:function(msg){
                $('#I'+pos+<?=$formId?>).html('<td>'+msg+'</td> ');
            },
            beforeSend: function() {
                $('#I'+pos+<?=$formId?>).html('<td clas="imageLoad"><img src="/admin/images/ajax-loader.gif" /></td>');
            }
          });  
         }
          </script>
        <?
        }        
    }
    
    
    
    static function ListFilesFrontend($lang, $items, $path, $text){
        $items_keys = array_keys($items);
        $items_count = count($items);
        if($items_count>0){
        ?>
        <ul id="uploaded_files"><?
        for($i=0;$i<$items_count;$i++){
            $name = $items[$items_keys[$i]]['path'];
            $ext = explode('.', $name);
            $ext =  strtolower($ext[count($ext)-1]);
            $filesExt = array('doc','docx', 'xls', 'xlsx', 'ppt', 'pptx', 'bmp', 'jpg', 'jpeg', 'png', 'gif', 'psd', 'mp3', 'wav', 'ogg', 'avi', 'wmv', 'flv', 'pdf', 'exe', 'txt', 'swf', 'rar', 'zip');
            if (in_array($ext, $filesExt)) 
                $class = $ext;
            else 
                $class = 'other';

             $filesize = filesize(SITE_PATH.'/'.$path.'/'.$name);
             //echo $row['file_path'].$row['name'] ;
             $cor = intval($filesize/1024);
                
            ?>
            <li>
             <div class="file <?=$class?>"></div>
                <div class="file_title">
                    <a href="/<?=$path?>/<?=$name?>">
                    <?
                    if (!empty($items[$items_keys[$i]]['name'][$lang])){
                       ?><span><?=$items[$items_keys[$i]]['name'][$lang]?></span><?
                    }
                    else {
                      ?><span><?=$name?></span><?
                    }
                    ?>
                    </a>
                    &nbsp;-&nbsp;<?=$cor?> <?=$text['KB']?>
                </div> 
                </li><?
        }
        ?>
        </ul>
        <?
        }        
    }


    static function DownloadCatalogFrontend($lang, $items, $path, $text){
        $items_keys = array_keys($items);
        $items_count = count($items);
        if($items_count>0){
            for($i=0;$i<$items_count;$i++) {
                $name = $items[$items_keys[$i]]['path'];
                 $filesize = filesize(SITE_PATH.'/'.$path.'/'.$name);
                 //echo $row['file_path'].$row['name'] ;
                 $cor = intval($filesize/1024);

                if (!empty($items[$items_keys[$i]]['name'][$lang]))
                   $title = $items[$items_keys[$i]]['name'][$lang];
                else 
                  $title = $name;
                ?>
                <div style="padding-bottom: 5px;">
                    <a href="/<?=$path?>/<?=$name?>" title="<?=$title?>&nbsp;-&nbsp;<?=$cor?> <?=$text['KB']?>"><img src="/images/design/btnDownloadCatalog<?=_LANG_ID?>.gif"></a>
                </div>
                <?
            }
        }        
    }
        
    static function ListVideoFrontend($lang, $items, $path, $text){
        $items_keys = array_keys($items);
        $items_count = count($items);
        for($i=0; $i<$items_count; $i++){
            $filename = $items[$items_keys[$i]]['path'];
            $fullpath ='/'.$path.'/'.$filename;
             
            if (!empty($items[$items_keys[$i]]['name'][$lang]))
                $name = $items[$items_keys[$i]]['name'][$lang];
            else 
                $name = $filename;
            $title= $items[$items_keys[$i]]['text'][$lang]; // Описание                 
            /*?><a href="<?=$fullpath?>"><?=$name;?></a>*/?>
            <a href="#" onclick="SetNewHref('<?=$fullpath;?>','player'); return false;" title="<?=$title;?>"><?=$name;?></a><br/><?
        }
    }
    
    static function ShowImagesCount($items,$text){
        $count = count($items);
        $items_keys = array_keys($items);
        if($count>0)
        {
             $file = $items[$items_keys[0]]['path']; 
            ?><img src="<?=$file?>" /><?
        }
        ?><div class="count"><?=$text['TXT_COUNT'].': <b>'.$count?></b></div><?
        
    }
     static function ShowFilesCount($count,$text){
        ?><div class="count"><?=$text['TXT_COUNT'].': <b>'.$count?></b></div><?
        
    }
    static function ShowMessage($text){
        ?><div class="msg"><?=$text?></div><?
    }
    
    static function ShowError($text){
        ?><div class="err"><?=$text?></div><?
    }

// ================================================================================================
// Function : ShowSingleImage
// Version : 1.0.0
// Date : 05.07.2010
// Parms :$items  / array of items 
//           $text   / array of multilanguage captions 
//           $lang   / array of languages
// Returns : $res   / Void
// Description : Show Single Image
// Programmer : Yaroslav Gyryn
// ================================================================================================     
static function ShowSingleImage($items, $text, $lang = _LANG_ID) {
    $items_keys = array_keys($items);
    $items_count = count($items);
    if($items_count>0) {
         ?><div class="leftBlockHead"><?=$text['SYS_IMAGE_GALLERY']?></div>
         <div class="imageBlock " align="center"><?
         for($i=0;$i<$items_count;$i++){   
                $alt= $items[$items_keys[$i]]['name'][$lang];  // Заголовок
                $title= $items[$items_keys[$i]]['text'][$lang];  // Описание 
                $path = $items[$items_keys[$i]]['path'];         //  Путь уменьшенной копии
                $path_org = $items[$items_keys[$i]]['path_original']; 
                ?>
                <a href="<?=$path_org;?>" class="highslide" onclick="return hs.expand(this);">
                   <img src="<?=$path;?>" alt="<?=$alt?>" title="<?=$title;?>">
                 </a><?                    
         }
         ?></div><?
     }
}  // End of ShowSingleImage();  
// ===============================================================================================         

}
?>
