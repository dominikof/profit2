<?php
include_once( SITE_PATH.'/sys/classes/sysPage.class.php' );

/**
 * PageAdmin
 * 
 * @package 
 * @author SEOTM
 * @copyright 2011
 * @version 2.0.0
 * @access public
 */
class PageAdmin extends Page {

 var $module;   //--- execute module
 var $user;     //--- current user
 var $group;    //--- current users group
 static $type_action = array (''=>'','back'=>'admin','front'=>'modules');

 /**
  * PageAdmin::PageAdmin()
  * Constructor, Set the variabels
  * @return void
  */
 function PageAdmin()
 {
     if( !isset($_SESSION['session_id']) ) if( !headers_sent() ) session_start();
     
     //echo "<br>000SESSION['session_id'] = ".$_SESSION['session_id'];
     
     if( defined("MAKE_DEBUG") AND MAKE_DEBUG==1 ){  
        $this->time_start = $this->getmicrotime();
        $_SESSION['cnt_db_queries']=0; 
     }

     $this->Settings = &check_init('SysSettings', 'SysSettings');
     if (isset($_SESSION['session_id'])) $session_id = $_SESSION['session_id'];
     else $session_id=NULL;
     $this->logon = &check_init('logon','Authorization', "'$session_id'");
     //echo "<br>SESSION['session_id'] = ".$_SESSION['session_id'];  
     // проверка переключеня языка
     if(isset($_GET['lang_pg'])){ 
        //устанавливаем выбранный язык в сесию
        //$_SESSION['lang_pg'] = $_GET['lang_pg'];
        //устанавливаем выбранный язык для данного пользователя
        $this->Settings->SetLangBackend($this->logon->user_id, $_GET['lang_pg']);
     }
     //echo "<br>SESSION['session_id'] = ".$_SESSION['session_id'].'<br>$this->logon->user_id='.$this->logon->user_id; 
     //echo "<br>_GET['lang_pg'] = ".$_GET['lang_pg'];
     //echo "<br>_SESSION['lang_pg'] = ".$_SESSION['lang_pg'];

    // установка языка из базы для данного пользователя
    if(!empty($this->logon->user_id))
        $tmp_lang = $this->Settings->GetLangBackend($this->logon->user_id,true);
    else
        $tmp_lang = ''; 
    //echo '<br>$this->logon->user_id='.$this->logon->user_id.' tmp_lang ='.$tmp_lang;print_r($tmp_lang);
     
    if( !isset($tmp_lang['cod']) OR empty($tmp_lang['cod']) ){ // установка языка из базы для для всех пользователей 
        $tmp_lang = SysLang::GetDefBackLangData();
        //echo "<br>tmp_lang = ".$tmp_lang;print_r($tmp_lang);
        if(count($tmp_lang)==0 || empty($tmp_lang['cod']) ){ // установка языка втупую
            if (!defined("_LANG_ID")) define("_LANG_ID", DEBUG_LANG);
            if (!defined("_LANG_SHORT")) define("_LANG_SHORT", DEBUG_LANG_SHORT);
        }
        else{
            if (!defined("_LANG_ID")) define("_LANG_ID", $tmp_lang["cod"]);
            if (!defined("_LANG_SHORT")) define("_LANG_SHORT", $tmp_lang["short_name"]);
        }
    }
    else {
        if (!defined("_LANG_ID")) define("_LANG_ID", $tmp_lang["cod"]);
        if (!defined("_LANG_SHORT")) define("_LANG_SHORT", $tmp_lang["short"]);
    }
    //устанавливаем переменную сессии, что бы можно было подключать визуальный редактор tiny_mce на нужной языковой версии.
    $_SESSION['_LANG_SHORT']=_LANG_SHORT;
    //echo '<br>_LANG_ID='._LANG_ID.' _LANG_SHORT='._LANG_SHORT.' $_SESSION[_LANG_SHORT]='.$_SESSION['_LANG_SHORT']; 
    
        
    //======== IMPORTANT!!! Class $Lang MUST created always after define _LANG_ID, ===========
    //======== othewize will be problems with multilanguages labels in admin part. ===========  
    $Lang = &check_init('SysLang','SysLang');
    $this->page_encode = $Lang->GetDefLangEncoding(_LANG_ID);
     
    if (empty($this->db)) $this->db = DBs::getInstance();
    if (empty($this->Form)) $this->Form = new Form();
    $Langarray = $Lang->LangArray(_LANG_ID);
    //echo '<br>_LANG_ID='._LANG_ID;
    $this->title = 'Control panel'; 
    $this->msg =  &check_init_txt('TblBackMulti',TblBackMulti, _LANG_ID); 
    $this->Msg =  &check_init('ShowMsg','ShowMsg');
    
    $this->send_headers();
 }



  /**
   * PageAdmin::WriteHeader()
   * Write Header of Admin Page 
   * @param mixed $module_name
   * @return void
   */
  function WriteHeader($module_name=NULL)
  {
      $this->send_headers();
 
      if( !empty($module_name) ) $this->title = $this->msg["_TXT_TITLE"].' - '.$module_name;
      else {
          if( empty($this->logon->user_id) ) $this->title = $this->msg["_TXT_TITLE"].' - '.$this->msg["_TXT_LOGIN"];
          else $this->title = $this->msg["_TXT_TITLE"].' - '.$this->msg["_TXT_CONTROL_PANEL"].', '.$this->logon->login.'!';
      }
      
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$this->page_encode;?>"/>
<meta http-equiv="Content-Type" content="application/x-javascript; charset=<?=$this->page_encode;?>"/>
<title><?=$this->title;?></title>
<link rel="stylesheet" type="text/css" href="http://<?=NAME_SERVER?>/admin/include/css/Admin.css" />
<link rel="stylesheet" type="text/css" href="http://<?=NAME_SERVER?>/admin/include/css/AdminHTML.css" />
<link rel="stylesheet" type="text/css" href="http://<?=NAME_SERVER?>/admin/include/css/style.css" />
<script type="text/javascript" src="http://<?=NAME_SERVER?>/sys/js/jQuery/jquery.js"></script>
<script type="text/javascript" src="http://<?=NAME_SERVER?>/sys/js/jQuery/jquery.tablednd.js"></script>
<script type="text/javascript" src="http://<?=NAME_SERVER?>/sys/js/jQuery/easyTooltip.js"></script>
<script type="text/javascript" src="http://<?=NAME_SERVER?>/sys/js/jQuery/jquery.form.js"></script>
<script type="text/javascript" src="http://<?=NAME_SERVER?>/sys/js/jQuery/jquery.AjaxUpload.js"></script>
<script type="text/javascript" src="http://<?=NAME_SERVER?>/admin/include/js/funcs.js"></script>
<script type="text/javascript" src="http://<?=NAME_SERVER?>/sys/js/fancybox/jquery.fancybox-1.3.4.js"></script>
<link rel="stylesheet" type="text/css" href="http://<?=NAME_SERVER?>/sys/js/fancybox/jquery.fancybox-1.3.4.css" />
<?
$jqueryUi=new jqueryUi();
$jqueryUi->load_files();
?>
</head>
<body>

 <script type="text/javascript">
  preload('images/icons/uparrow.png');
  preload('images/icons/uparrow-1.png');
  preload('images/icons/downarrow-1.png');
  preload('images/icons/downarrow.png');
  preload('images/icons/restore.png');
  preload('images/icons/minus.png');
  preload('images/icons/plus.png');
  preload('images/icons/folder.png');
  preload('images/icons/page.png');
  preload('images/icons/categ.png');
  
  $(document).ready(function(){
         $("a[target='_blank']").fancybox({
            'transitionIn'	:	'elastic',
            'transitionOut'	:	'elastic',
            'titlePosition'  : 'over',
            'overlayColor' : '#2a2a2a'
        });
       // $("#dateField").datepicker({ dateFormat: 'yy-mm-dd' });
  });
 </script>
 
<div class="top">
  <?
    if (isset($this->user))
    {
        $Lang = &check_init('LangSys','SysLang');
        ?><img height="34" align="left" style="position: absolute;top:0px;left:5px;" src="/admin/images/design/cmsLogo.png"/>
        <div class="infoR"><b><?=$this->msg['_FLD_LANGUAGE']?>:</b>  <?=$Lang->WriteLangPanel(_LANG_ID)?></div>
        <div class="Langs">
            <?=$this->msg["_TXT_LOGINED"]?>: 
            <span><b><?=$this->user?></b></span> (<a href='<?echo $_SERVER['PHP_SELF'].'?logout=logout';?>' title='<?=$this->msg["_TXT_LOGOUT"]?>'><?=$this->msg["_TXT_LOGOUT"]?></a>)
        </div>
        <?
                        
    } 
    else
    {
       echo '<span>',$this->msg["_TXT_CONTROL_PANEL"],'</span>'; 
    }
  ?>
  <div class="clear"></div>
</div><? 
 }


 /**
  * PageAdmin::WriteFooter()
  * Write Footer of admin page
  * @return void
  */
 function WriteFooter()
 {
if( defined("MAKE_DEBUG") AND MAKE_DEBUG==1 ){
    $this->time_end = $this->getmicrotime();
    //echo '<br/>LOADING TIME: '.($this->time_end - $this->time_start);
    printf ("<br/>TIME:%2.3f", $this->time_end - $this->time_start);
    if( isset($_SESSION['cnt_db_queries'])) echo '<br>QUERIES: '.$_SESSION['cnt_db_queries'];
}
?> 
</body>
</html>
 <?
 }


 /**
  * PageAdmin::WriteContentH()
  * Write Header of Content Admin Page
  * @return void
  */
 function WriteContentH()
 {
  $Lang = &check_init('LangSys','SysLang');
 ?>
 <div class="Page">
    <div class="menu">
    <?$this->WriteMenu( );?>
    </div>
    <div class="Content">
        <div class="container"><?
 }


 /**
  * PageAdmin::WriteContentF()
  * Write Footer of Content of admin page 
  * @return void
  */
 function WriteContentF()
 {
    ?>
        </div>
    </div>
    <div class="clear"></div>
  </div>
  <div class="copyright">© 2005-<?=date("Y");?> Content Management System SEOCMS,  SEOTM.COM все права защищены</div>
  <?
 }


/**
 * PageAdmin::LoginMenu()
 * Write Menu of admin page
 * @param mixed $width
 * @return void
 */
function LoginMenu( $width )
{
 //echo "<br>_SERVER['HTTP_REFERER']=".$_SERVER['HTTP_REFERER']." NAME_SERVER=".NAME_SERVER;
 if( isset($_SERVER['REQUEST_URI']) AND !empty($_SERVER['REQUEST_URI']) AND (strstr($_SERVER['REQUEST_URI'], '/admin')) ) $referer_page=$_SERVER['REQUEST_URI'];
 else $referer_page=NULL;
 ?>
 <script type="text/javascript"> 
 $(document).ready(function(){
   if(window.innerWidth){
    width = window.innerWidth;
    height = window.innerHeight;
  }   else if(document.documentElement && document.documentElement.clientWidth){
    width = document.documentElement.clientWidth;
    height = document.documentElement.clientHeight;
  }   else if(document.body && document.body.clientWidth){
    width = document.body.clientWidth;
    height = document.body.clientHeight;
  }
  
  var h=height-$(".top").height()-$(".copyright").height()-40;
  $(".loginForm").css("padding-top",((h-$(".loginForm").height())/2)+"px");
  if(h>$(".loginPage").height())
       $(".loginPage").height(h+"px");
  });
  </script>
 
 <div class="loginPage">
 <div class="loginForm">
    <div class="Inherit"><div class="head"><?=$this->msg['_FIRST_WELCOME'];?></div>
        
        <div class="info"><img width="50" align="left" style="margin-left: 36px;" src="/admin/images/design/cmsLogo.png"/><div class="welcomeText"><?=$this->msg['_FIRST_WELCOME2'];?></div></div>
        <form method='POST' action='/admin/index.php'>
        <input type="hidden" name="referer_page" value="<?=$referer_page;?>" /> 
        <table class="loginTable" cellpadding="4" cellpadding="4" align="center">
        <tr>
         <td><b><?=$this->msg['FLD_USERNAME']?></b></td>
         <td><input size='15' type='text' class='textbox' name='enter_login'/></td>
        </tr>
        <tr>
         <td><b><?=$this->msg['FLD_PASSWORD'];?></b></td>
         <td><input size='15' type='password' class='textbox' name='enter_pass'/></td>
        </tr>
        <tr>
         <td align='center' colspan="2"><input type='submit' class='submit_button' style="display: inline;margin-top: 10px" value='<?=$this->msg['_TXT_LOGIN'];?>'/></td>
        </tr>
        </table>
  </form></div>
 </div>
</div>
<div class="copyright" align="center">© <?=date("Y");?> Content Management System SEOCMS,  <a href="http://www.seotm.com/" title="Веб студия SEOTM">seotm.com</a>. Все права защищены.</div>
 <?
}



// ================================================================================================
// Function : WriteMenu()
// Version : 1.0.0
// Date : 26.01.2005
// Parms :
// Returns : true,false / Void
// Description : Write Menu of admin page
// ================================================================================================
// Programmer : Andriy Lykhodid
// Date : 26.01.2005
// Reason for change : Reason Description / Creation
// Change Request Nbr:
// ================================================================================================
function WriteMenuOld( $width = 150 )
{
 $db = new DB;   
 ?>
 <table width='100%' CLASS='LMenuTable' border=0 cellpadding="0" cellspacing="0">
  <tr>
   <td width='$width' height='520' VALIGN='top'>
 <?

 $q = "SELECT `".TblSysMenuAdm."`.*, `".TblSysSprMenuAdm."`.`name` FROM `".TblSysMenuAdm."`, `".TblSysSprMenuAdm."` 
       WHERE `".TblSysMenuAdm."`.`level`='0'
       AND `".TblSysMenuAdm."`.`group`='".$this->group."'
       AND `".TblSysSprMenuAdm."`.`cod`=".TblSysMenuAdm.".`id`
       AND `".TblSysSprMenuAdm."`.`lang_id`='"._LANG_ID."'
       ORDER BY `".TblSysMenuAdm."`.`move`
      ";
 $res = $db->db_Query( $q );
 //echo '<br>WriteMenu:: $q='.$q.' $res='.$res;
 if( !$res ) return false;

 $rows = $db->db_GetNumRows( $res );
 for( $i = 0; $i < $rows; $i++ )
 {
  $row = $db->db_FetchAssoc( $res );
  $this->InsertMenu( $row['name'], "level_".$row['id'] );
  $this->AdminMenu2( "level_".$row['id'], $row['id'] );
 }
 ?>
  <tr>
   <td width="200" height="1"><img src="images/spacer.gif" width="200" height="1" alt="" title="" border="0"/></td>
  </tr>
 </table> 
 <?
}

 // ================================================================================================
// Function : WriteMenuNew()
// Version : 1.0.0
// Date : 26.01.2005
// Parms :
// Returns : true,false / Void
// Description : Write Menu of admin page
// ================================================================================================
// Programmer : Andriy Lykhodid
// Date : 26.01.2005
// Reason for change : Reason Description / Creation
// Change Request Nbr:
// ================================================================================================
function WriteMenu()
{
 $db = DBs::getInstance();
   $q = "SELECT `".TblSysMenuAdm."`.*, `".TblSysSprMenuAdm."`.`name` FROM `".TblSysMenuAdm."`, `".TblSysSprMenuAdm."` 
       WHERE `".TblSysMenuAdm."`.group='".$this->group."'
       AND `".TblSysSprMenuAdm."`.cod=".TblSysMenuAdm.".id
       AND `".TblSysSprMenuAdm."`.lang_id='"._LANG_ID."'
       ORDER BY `".TblSysMenuAdm."`.level,`".TblSysMenuAdm."`.move
      ";
  //(`".TblSysMenuAdm."`.`level`='0' OR `".TblSysMenuAdm."`.`level`='1' )
 $res = $db->db_Query( $q );
 //echo '<br>WriteMenu:: $q='.$q.' $res='.$res;
 if( !$res ) return false;
 $rows = $db->db_GetNumRows( $res );
 //echo '<br>$rows='.$rows;
 if($rows==0){
    echo '<br>Not found menu.';
    return false;
 }
 $menu_array= array();
 $level_count= array();
 $fr_level=0;
 $counter=0;
 for( $i = 0; $i < $rows; $i++ )
 {
  $row = $db->db_FetchAssoc( $res );
  if ($row['level']!=$fr_level)
  {
      $level_count[$fr_level]=$counter;
      $fr_level= $row['level'];
      $counter=0; 
  }
  
  $menu_array[$row['level']][$counter]['id']=$row['id'];
  $menu_array[$row['level']][$counter]['function']=$row['function'];
  $menu_array[$row['level']][$counter]['name']=$row['name'];
  $menu_array[$row['level']][$counter]['move']=$row['move'];
  if($i==($rows-1))
     $level_count[$row['level']]=$counter+1;
  $counter++;
 }
// print_r($menu_array);
// print_r($level_count);
 ?>
 <ul>
 <?
 for( $i = 0; $i < $level_count[0]; $i++ )
 {
  if (isset($level_count[$menu_array[0][$i]['id']]))
    $this->InsertMenu( $menu_array[0][$i]['name'], "level_".$menu_array[0][$i]['id'],$level_count[$menu_array[0][$i]['id']]);
  else
    $this->InsertMenu( $menu_array[0][$i]['name'], "level_".$menu_array[0][$i]['id'],NULL);
    $this->AdminMenuNew( "level_".$menu_array[0][$i]['id'], $menu_array[0][$i]['id'],$menu_array,$level_count );
 }
 ?>
  </ul> 
 <?
}
function WriteMenuNotSoOld()
{
 $db = DBs::getInstance();
   $q = "SELECT `".TblSysMenuAdm."`.*, `".TblSysSprMenuAdm."`.`name` FROM `".TblSysMenuAdm."`, `".TblSysSprMenuAdm."` 
       WHERE `".TblSysMenuAdm."`.group='".$this->group."'
       AND `".TblSysSprMenuAdm."`.cod=".TblSysMenuAdm.".id
       AND `".TblSysSprMenuAdm."`.lang_id='"._LANG_ID."'
       ORDER BY `".TblSysMenuAdm."`.level,`".TblSysMenuAdm."`.move
      ";
  //(`".TblSysMenuAdm."`.`level`='0' OR `".TblSysMenuAdm."`.`level`='1' )
 $res = $db->db_Query( $q );
 //echo '<br>WriteMenu:: $q='.$q.' $res='.$res;
 if( !$res ) return false;
 $rows = $db->db_GetNumRows( $res );
 $menu_array= array();
 $level_count= array();
 $fr_level=0;
 $counter=0;
 for( $i = 0; $i < $rows; $i++ )
 {
  $row = $db->db_FetchAssoc( $res );
  if ($row['level']!=$fr_level)
  {
      $level_count[$fr_level]=$counter;
      $fr_level= $row['level'];
      $counter=0; 
  }
  
  $menu_array[$row['level']][$counter]['id']=$row['id'];
  $menu_array[$row['level']][$counter]['function']=$row['function'];
  $menu_array[$row['level']][$counter]['name']=$row['name'];
  $menu_array[$row['level']][$counter]['move']=$row['move'];
  if($i==($rows-1))
     $level_count[$row['level']]=$counter+1;
  $counter++;
 }
// print_r($menu_array);
// print_r($level_count);
 ?>
 <ul>
 <?
 for( $i = 0; $i < $level_count[0]; $i++ )
 {
  if (isset($level_count[$menu_array[0][$i]['id']]))
    $this->InsertMenu( $menu_array[0][$i]['name'], "level_".$menu_array[0][$i]['id'],$level_count[$menu_array[0][$i]['id']]);
  else
    $this->InsertMenu( $menu_array[0][$i]['name'], "level_".$menu_array[0][$i]['id'],NULL);
    $this->AdminMenuNew( "level_".$menu_array[0][$i]['id'], $menu_array[0][$i]['id'],$menu_array,$level_count );
 }
 ?>
  </ul> 
 <?
}
// ================================================================================================
// Function : AdminMenuNew()
// Version : 1.0.0
// Date : 26.01.2005
// Parms :
// Returns : true,false / Void
// Description : Write Menu of admin page
// ================================================================================================
// Programmer : Andriy Lykhodid
// Date : 26.01.2005
// Reason for change : Reason Description / Creation
// Change Request Nbr:
// ================================================================================================
function AdminMenuNew( $group_menu, $level,$menu_array,$level_count )
{
  $mas_menu=NULL;
  if (isset($level_count[$level]))
  {
  for( $j = 0; $j < $level_count[$level]; $j++ )
  {
   $function = $menu_array[$level][$j]['function'];
   $mas_menu[$j] = array( $menu_array[$level][$j]['name'], $function, "level_".$menu_array[$level][$j]['id'], $menu_array[$level][$j]['id'] );
   //   $level=$mas['level'];  

  } 
  //echo '<br />level'.$level;
  //  print_r($mas_menu) ;
  if( count( $mas_menu )>0 )
    $this->InsertSubMenu( $mas_menu, $group_menu, $menu_array ,$level_count);
  return true;
  }
}
// ================================================================================================
// Function : AdminMenu2()
// Version : 1.0.0
// Date : 26.01.2005
// Parms :
// Returns : true,false / Void
// Description : Write Menu of admin page
// ================================================================================================
// Programmer : Andriy Lykhodid
// Date : 26.01.2005
// Reason for change : Reason Description / Creation
// Change Request Nbr:
// ================================================================================================
function AdminMenu2( $group_menu, $level )
{
  $db = new DB;
  $spr = new SysSpr();

  //$q = "select * from ".TblSysMenuAdm." where level='".$level."'";
  //$q = $q." and `group`='$this->group'";
  //$q = $q." order by move";
  
 $q = "SELECT `".TblSysMenuAdm."`.*, `".TblSysSprMenuAdm."`.`name` FROM `".TblSysMenuAdm."`, `".TblSysSprMenuAdm."` 
       WHERE `".TblSysMenuAdm."`.`level`='".$level."'
       AND `".TblSysMenuAdm."`.`group`='".$this->group."'
       AND `".TblSysSprMenuAdm."`.`cod`=".TblSysMenuAdm.".`id`
       AND `".TblSysSprMenuAdm."`.`lang_id`='"._LANG_ID."'
       ORDER BY `".TblSysMenuAdm."`.`move`
      ";  
  $r = $db->db_Query( $q );
//  echo '<br>AdminMenu2:: $q='.$q.' $r='.$r; 
  if( !$r ) return false;

  $rkol = $db->db_GetNumRows( $r );

  $mas_menu=NULL;
  for( $j = 0; $j < $rkol; $j++ )
  {
   $mas = $db->db_FetchAssoc( $r );
   $function = $mas['function'];
   $mas_menu[$j] = array( $mas['name'], $function, "level_".$mas['id'], $mas['id'] );
   $level=$mas['level'];  
  }

  if( count( $mas_menu )>0 )
    $this->InsertSubMenu( $mas_menu, $group_menu );
  return true;
}


// ================================================================================================
// Function : InsertMenu()
// Version : 1.0.0
// Date : 26.01.2005
// Parms :
// Returns : true,false / Void
// Description : Write Menu 1st level
// ================================================================================================
// Programmer : Andriy Lykhodid
// Date : 26.01.2005
// Reason for change : Reason Description / Creation
// Change Request Nbr:
// ================================================================================================

function InsertMenu( $text, $id ,$count=NULL)
{
        $hidden = false;
        if ( isset( $_COOKIE[ $id ] ) && $_COOKIE[ $id ]=='1' )
        {
            $hidden = true;
        }
        ?>
        <li class="ParentMenuHeader" onclick="flip_div('<?=$id?>'); flip_arrow(img_<?=$id?>);">
        <a href="javascript:void(0)">
         <img src="<?if ($hidden) echo "http://".NAME_SERVER."/admin/images/design/item_a.gif";else echo "http://".NAME_SERVER."/admin/images/design/item_n.gif";?>" id="img_<?=$id?>" />
        <?$l = explode( '_', $id );
//        $count = $this->LevelCountItem( $l[1] );
        if (!isset($count)) $count=0;
        echo "&nbsp; $text [$count]</a></li>";
}


// ================================================================================================
// Function : InsertSubMenu()
// Version : 1.0.0
// Date : 26.01.2005
// Parms :
// Returns : true,false / Void
// Description : Write  Sub Menu
// ================================================================================================
// Programmer : Andriy Lykhodid
// Date : 26.01.2005
// Reason for change : Reason Description / Creation
// Change Request Nbr:
// ================================================================================================

function InsertSubMenu( $arr_items, $id, $menu_array,$level_count )
{
        //echo '$arr_items=';print_r($arr_items);
        $hidden = false;
        if ( isset( $_COOKIE[ $id ] ) && $_COOKIE[ $id ]=='1' )
                $hidden = true;
//          echo $HTTP_COOKIE_VARS['level_142'];
        ?>
         <li><ul id="<?=$id?>" class="LMenuSubLevel" style="display: <?if($hidden) echo "none"; else "block";?>;">
        <?
        for ( $i = 0; $i < count( $arr_items ); $i++ )
        {
         if (isset($level_count[$arr_items[ $i ][ 3 ]])) $count=$level_count[$arr_items[ $i ][ 3 ]];
         else $count=0; 
//         $count = $this->LevelCountItem( $arr_items[ $i ][ 3 ] );
         if( $count >0 )
         {
             $hidden = false; 
             if ( isset( $_COOKIE[ $arr_items[ $i ][ 2 ] ] ) && $_COOKIE[ $arr_items[ $i ][ 2 ] ]=='1' )
                $hidden = true;
           ?><li class="subMenu"><a href="javascript:void(0)" onclick="flip_div('<?=$arr_items[ $i ][ 2 ]?>'); flip_arrow( img_<?=$arr_items[ $i ][ 2 ]?>);"> 
           <img id="img_<?=$arr_items[ $i ][ 2 ]?>" 
           src="<?if ($hidden) echo "http://".NAME_SERVER."/admin/images/design/item_a.gif";else echo "http://".NAME_SERVER."/admin/images/design/item_n.gif";?>" style="cursor: hand;" />
           <?='&nbsp;'.stripcslashes($arr_items[ $i ][ 0 ])?></a></li><?
         }
         else
         {
           if( $this->module == $arr_items[ $i ][ 1 ] ) 
           {
              ?><li><a class="active" href="?module=<?=$arr_items[ $i ][ 1 ]?>">> <?=stripcslashes($arr_items[ $i ][ 0 ])?></a></li><?
           }
           else 
           {
                ?><li><a class="item" href="?module=<?=$arr_items[ $i ][ 1 ]?>"><?=stripcslashes($arr_items[ $i ][ 0 ])?></a></li><?
           }
         }
           $this->AdminMenuNew( $arr_items[ $i ][ 2 ], $arr_items[ $i ][ 3 ],$menu_array,$level_count );
        }
        echo "</ul></li>";
}


// ================================================================================================
// Function : IsLevel()
// Version : 1.0.0
// Date : 26.01.2005
// Parms :   $level - level of menu
// Returns : true,false / Void
// Description : Check Sub Level
// ================================================================================================
// Programmer : Andriy Lykhodid
// Date : 26.01.2005
// Reason for change : Reason Description / Creation
// Change Request Nbr:
// ================================================================================================
function IsLevel( $level )
{
 $db = new DB;

  $q = "select * from ".TblSysMenuAdm." where level='".$level."' order by move";
  $r = $db->db_Query( $q );
  if( !$r ) return false;

  $rkol = $db->db_GetNumRows( $r );
  if( $rkol<1 ) return false;

 return true;
} 


// ================================================================================================
// Function : LevelCountItem()
// Version : 1.0.0
// Date : 02.02.2005
// Parms :   $level - id level
// Returns : count of items / Void
// Description : Get Function for menu
// ================================================================================================
// Programmer : Yaroslav Gyryn
// Date : 15.10.2009
// Reason for change : Reason Description / Creation
// Change Request Nbr:
// ================================================================================================
function LevelCountItem( $level )
{
  $db = new DB;
  $q = 'SELECT COUNT(*) as count '
        . ' FROM `'.TblSysMenuAdm.'` '
        . ' WHERE LEVEL ='.$level;
  $r = $db->db_Query( $q );
  if( !$r ) return false;
  $row = $db->db_FetchAssoc();
        return $row['count'];
}

} // end of class
?>