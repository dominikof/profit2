<?php
// ================================================================================================
//    System     : PrCSM05
//    Module     : Article
//    Version    : 1.0.0
//    Date       : 27.01.2006
//    Licensed To:
//                 Igor  Trokhymchuk  ihoru@mail.ru
//                 Andriy Lykhodid    las_zt@mail.ru
//    PREVIEW ARTICLE
// ================================================================================================

include_once( SITE_PATH.'/admin/include/defines.inc.php' );

$msg = new ShowMsg();

if ( isset($_SESSION['session_id']) ) $session_id=$_SESSION['session_id'];
else $session_id = NULL;
$logon = new  Authorization();


$Lang = new SysLang();

if (empty($_SESSION["lang_pg"])) $_SESSION["lang_pg"]=3;

if(isset($_REQUEST['lang_pg'])){
   $old_lang=$_SESSION["lang_pg"];
   $_SESSION["lang_pg"] = $_REQUEST['lang_pg'];
}

if (!isset($pg)) $pg = new PageAdmin();

$mas=$Lang->LangArray($_SESSION["lang_pg"]);
if (empty($mas)){
     $_SESSION["lang_pg"]=$old_lang;
     define( "_LANG_ID", $_SESSION["lang_pg"] );
     $pg->SetLang($_SESSION["lang_pg"]);
     $msg = new ShowMsg(_LANG_ID);
     $msg->show_msg('_ERR_NO_TRANSLATE_ON_THIS_LANG');
}
else {
    if ( !defined("_LANG_ID") ) define( "_LANG_ID", $_SESSION["lang_pg"] );
}
echo '<br>_LANG_ID='._LANG_ID; 

//$pg->WriteHeader();

?>

        <script>
                var form = window.opener.document.form_news
                var subject = window.opener.document.form_news.elements['subject[<?=_LANG_ID?>]'].value;
                var short = window.opener.document.form_news.elements['short[<?=_LANG_ID?>]'].value;
                var full = window.opener.document.form_news.elements['full[<?=_LANG_ID?>]'].value;
        </script>
<table align="center" width="90%" cellspacing="2" cellpadding="2" border="0">
        <tr>
                <td class="contentheading" colspan="2" align=center><h3><script>document.write(subject);</script></h3></td>
        </tr>
        <tr>
                <script>document.write("<td valign=\"top\" height=\"90%\" colspan=\"2\"><br>" + short + "</td>");</script>
        </tr>

        <tr>
                <script>document.write("<td valign=\"top\" height=\"90%\" colspan=\"2\"><br><br>" + full + "</td>");</script>
        </tr>
        <tr>    <td> <td> <br>
        <tr>
                <td align="right"><a href="#" onClick="window.close()">Close</a>&nbsp;&nbsp;</td>
                <td align="left"><a href="javascript:;" onClick="window.print(); return false">Print</a></td>
        </tr>
</table>
<?
//$pg->WriteFooter();
?>
