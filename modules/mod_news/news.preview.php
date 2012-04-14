<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
<LINK href="../../include/css/main.css" type="text/css" rel="stylesheet">
<?php
// ================================================================================================
//    System     : PrCSM05
//    Module     : News
//    Version    : 1.0.0
//    Date       : 04.02.2005
//    Licensed To:
//                 Igor  Trokhymchuk  ihoru@mail.ru
//                 Andriy Lykhodid    las_zt@mail.ru
//
//    Purpose    : Class definition for News - moule
//
// ================================================================================================
if (!defined("SITE_PATH")) define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] );  
 include_once( SITE_PATH.'/include/defines.php' ); 
 include_once( SITE_PATH.'/modules/mod_news/news.defines.php' );     
$msg = new ShowMsg();

if ( isset($_SESSION['session_id']) ) $session_id=$_SESSION['session_id'];
else $session_id = NULL;
$logon = new  Authorization();

//phpinfo();

$Lang = new SysLang();

//if (empty($_SESSION["lang_pg"])) $_SESSION["lang_pg"]=1;

//if(isset($_REQUEST['lang_pg'])){
//   $old_lang=$_SESSION["lang_pg"];
//   $_SESSION["lang_pg"] =$_REQUEST['lang_pg'];
//}

if (!isset($pg)) $pg = new PageAdmin();

if (!defined('_LANG_ID')) { 
    $mas=$Lang->LangArray($_SESSION["lang_pg"]);
    if (empty($mas)){
         $_SESSION["lang_pg"]=$old_lang;
         define( "_LANG_ID", $_SESSION["lang_pg"] );
         $pg->SetLang($_SESSION["lang_pg"]);
         $msg = new ShowMsg(_LANG_ID);
         $msg->show_msg('_ERR_NO_TRANSLATE_ON_THIS_LANG');
    }
    else define( "_LANG_ID", $_SESSION["lang_pg"] );
}
if (empty($Msg)) $Msg = new ShowMsg();
$Msg->SetShowTable(TblModNewsSprTxt);
$News = new NewsLayout(); 
$arr = NULL;


//_SERVER["HTTP_REFERER"]
//http://cms/admin/index.php?module=72&display=10&start=0&sort=&fltr=&fln=3&task=edit&id=14
if( isset($_SERVER["HTTP_REFERER"])){ 
    $st = $_SERVER["HTTP_REFERER"];
    $data = explode("&", $st);
    //echo count($data);
    $id_tmp = $data[count($data)-1]; 
    $id_tmp2 = explode("=", $id_tmp);
    $id = $id_tmp2[1];
}
//echo $id;


echo  intval($id);
if ($id==intval($id)) $arr = $News->ConvertDataToOutputArray($News->GetNewsData($id), "id", "asc", "full");
//$pg->WriteHeader();

?>

        <script>
                var form = window.opener.document.form_news
                var subject = window.opener.document.form_news.elements['subject[<?=_LANG_ID?>]'].value;
                var short = window.opener.document.form_news.elements['short[<?=_LANG_ID?>]'].value;
                var full = window.opener.document.form_news.elements['full[<?=_LANG_ID?>]'].value;
                var news_id = window.opener.document.form_news.elements['id'].value;
                var cat_id = window.opener.document.form_news.elements['id_category'].value;
                var cat = window.opener.document.form_news.elements['id_category'].options[cat_id].text;
                var start_date = window.opener.document.form_news.elements['start_date'].value;  
                var mypic = window.opener.document.form_news.elements['pic'].value; 
        </script>
 


 <table border="0" width="100%" cellspacing="0" cellpadding="0">
    <tr>
       <td><h1><script>document.write(subject);</script></h1></td>
       <td>
         <div><p class="news_cat" style="text-align:right;"><strong>
         <script>
       if(start_date!=undefined)
       {
       document.write(start_date);
       }
       else
       {
           document.write('<?=date('Y-m-d H:i:s');?>');
       }
      </script>
         </strong>&nbsp;&nbsp;</p></div>
       </td>
    </tr>
    <tr>
      <td colspan="2"><?=$Msg->show_text('TXT_CATEGORY');?>: <script>document.write("<a href=\"newscat_"+cat_id+".html\" class=\"news_cat\">");</script>
      <script>document.write(" "+cat);</script></a>
      </td>
    </tr>
    <tr>
      <td colspan="2" class="news_full" valign="top"><script>document.write(full);</script></td>
     </tr>
     <tr>
      <td rowspan="2" valign="top">
      
       <!-- display photos start -->
                 <table border="0" width="100%" cellspacing="10" cellpadding="0">
                  <tr>
                   <td align="center" valign="top">
                   <?
                   if ($arr!==NULL) {
                   foreach($arr as $key=>$value){ 
                   $class="img_main"; 
                   $params = "OnClick='window.open(\"news_print.php?item=".$value['img']['path']."&amp;news=".$value['id']."\", \"\", \"width=600, height=600\");'";               
                   if( !empty($value['img']['path'])) { 
                     ?><table><tr><td class="main_news_photo"><a href="javascript:void(0)" <?=$params;?>><?=$News->ShowImage($value['img']['path'], $value['id'], 'size_auto=200', '85', NULL, 'border="0"');?></a></td></tr></table><?
                   }
                   if( !empty($value['img']['descr'])) echo $value['img']['descr'];?>
                   </td>
                  </tr>
                  <?
                   }
                   }
                   ?> 
                 </table>
        <!-- display photos end -->
      </td>
    </tr>
    <tr>
      <td align="center">
         <table border="0">
       <?
         if ($arr==NULL) { 
       if( count($value['img_arr'])>1) {
           $count_in_row = count($value['img_arr']);
           if($count_in_row>5) $count_in_row = 5;?>
                  <tr>     
                       <?
                       $j=0;
                       $href="";
                       for($i=1;$i<count($value['img_arr']);$i++){
                           if($j==$count_in_row){
                               ?><tr><?
                               $j=0;
                           }
                           $params = "OnClick='window.open(\"news_print.php?item=".$value['img_arr'][$i]['path']."&amp;news=".$value['id']."\", \"\", \"width=620, height=620\");'";
                           ?><td  valign="top" align="center" width="150"><?if( !empty($value['img_arr'][$i]['path']) ) {?><table><tr><td class="img_others"><a href="javascript:void(0)" <?=$params;?>><?=$News->ShowImage($value['img_arr'][$i]['path'], $value['id'], 'size_auto=100', '85', NULL, 'border="0"');?></a></td></tr></table><? } 
                           if( !empty($value['img_arr'][$i]['descr'])) echo $value['img_arr'][$i]['descr'];
                           $j++;
                       }//end for
                    ?></td></tr><?
                    }//end if
         }
                    ?>
        </table>
      </td>
    </tr>
    <tr>
      <td>
       
      </td>
    </tr>
   </table> 
<?
//$pg->WriteFooter();
?>