<?php
if (!defined("SITE_PATH")) define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] );  
include_once( SITE_PATH.'/admin/include/defines.inc.php' );
define('BASEPATH', $_SERVER['DOCUMENT_ROOT'].'/');

//========= FIRST DEFINE PAGE LANGUAGE  BEGIN ===========
$pg = &check_init('PageAdmin', 'PageAdmin');
//========= FIRST DEFINE PAGE LANGUAGE BEGIN  =========== 

//echo "<br>_SESSION['session_id'] = ".$_SESSION['session_id'];
//phpinfo(); 
$ajax=AntiHacker::AntiHackRequest('ajax');
$enter_login = AntiHacker::AntiHackRequest('enter_login');
$enter_pass = AntiHacker::AntiHackRequestPass('enter_pass');  
$module = AntiHacker::AntiHackRequest('module');  
$module_name = AntiHacker::AntiHackRequest('module_name');  

$module_tmp=explode("?",$module);
$module = $module_tmp[0];
//echo '<br><br><br> $module_name='.$module_name;
$logout = AntiHacker::AntiHackRequest('logout');
$referer_page = AntiHacker::AntiHackRequest('referer_page');
//echo '<br>$referer_page='.$referer_page;
if( !strstr($referer_page, '/admin') ) $referer_page=$_SERVER['PHP_SELF']; 
if( strstr($referer_page, 'logout') ) $referer_page=$_SERVER['PHP_SELF'];
//echo '<br>$referer_page='.$referer_page;

//print_r($msg);
$pg->module=$module;

//------------------ Authorization settings -------------------------
$logon = &check_init('logon','Authorization');
//echo '<br />_LAN_ID='._LANG_ID; 
//$logon = new Authorization();

if (!empty($enter_login) || !empty($enter_pass)) {

    $res = $logon->user_valid( $enter_login, $enter_pass, 1 );
    if( $res ){
        //================= save succesfull logon to user statistic start ====================
        $SysUser = new SysUser($logon->user_id);
        $SysUser->SaveStat($logon->user_id);
        //================= save succesfull logon to user statistic end ======================  
    }
    else
    {
        ?>
        <script type="text/javascript">alert('<?=$logon->Err?>');</script>
        <?
    }  
    //echo '<br>$referer_page='.$referer_page;
    //restart page to set users default language
    Header( "HTTP/1.1 301 Moved Permanently" ); 
    Header( "Location: ".$referer_page );
    //echo '<br>$referer_page='.$referer_page;
    echo "<script>window.location.href='".$referer_page."';</script>\n";
    
}
if (isset($logout)) $logon->Logout();

if (!$logon->LoginCheck()) {
    if(!$ajax){
        $pg->WriteHeader(); 
        $pg->LoginMenu(450);
    }else echo "<script>top.window.location.href='/admin/';</script>";
}
else {
   $pg->module = $module;
   $pg->user = $logon->login;
   $pg->group = $logon->user_type;
   /* Write Header of Admin Page*/
   
   if( $module ) {
       $mdl_info=$pg->GetFunction( $module );
       if(!$ajax){
        $pg->WriteHeader($mdl_info['module_name']);  
        $pg->WriteContentH();
       }
     if (!isset($module_name) OR (empty($module_name) ) ) $module_name = $mdl_info['module_name']; 
     //echo '<br>$module='.$module.' $module_name='.$module_name;
     if(!$ajax)
     AdminHTML::PanelMainH( NULL, $module_name );
     $target = PageAdmin::$type_action[$mdl_info['target']];
     $mdl= '/'.$target.'/'.$mdl_info['name'];
     //echo '<br>$mdl='.$mdl.' $module='.$module;
     $mas_module=explode("?",$mdl);
     $params='';
     if (isset($mas_module[1])) {
         $inc_module=trim($mas_module[0]); 
         $module=$module.'?'.$mas_module[1];
          $mas_params=explode("&",$mas_module[1]);
          $count = count($mas_params); 
          for ( $a1 = 0; $a1 < $count; $a1++)
          {
            $name_val_par = explode("=",$mas_params[$a1]);
            if ( isset($_REQUEST[$name_val_par[0]]) ) $value_par =  $_REQUEST[$name_val_par[0]];
            else $value_par = $name_val_par[1];
            $params = $params.'$_REQUEST["'.$name_val_par[0].'"]'.'="'.$value_par.'";';
          }          
          
     }
     else $inc_module=trim($mdl);
     eval($params);
//     echo '<br> $mdl='.$mdl.' $inc_module='.$inc_module.' <br>$module='.$module.' <br/>SITE_PATH='.SITE_PATH;
     include( SITE_PATH.$inc_module);
     unset( $_REQUEST['task'] );
     AdminHTML::PanelMainF();
   }
   else
   {
       $pg->WriteHeader(); 
       $pg->WriteContentH();
   }
   /* Write Footer of Admin Page*/
   if(!$ajax)
     $pg->WriteContentF();
}
//echo '<pre>';
//print_r(get_instance());
//echo '</pre>';
if(!$ajax)
    $pg->WriteFooter();

//print_r($_REQUEST);
/* Statistic module */
$st = new Stat(0);             //--- create Statistic-Object
//if set to save back-end statistic then do it.
if($st->Set->back){
    $st->user = $logon->user_id;  //--- set cuurrent user id
    $res = $st->Set();            //--- set all property's for log and save in database
}
?>