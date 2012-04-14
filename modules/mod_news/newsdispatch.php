<?
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
include_once( SITE_PATH.'/modules/mod_news/news.defines.php' );

$Page = new PageAdmin();

if( !isset( $_REQUEST['task'] ) ) $task = NULL;
else $task = $_REQUEST['task'];

if( !isset( $_REQUEST['subscr_start'] ) ) $subscr_start=0;
else $subscr_start = $_REQUEST['subscr_start'];

if( !isset( $_REQUEST['subscr_cnt'] ) ) $subscr_cnt=0;
else $subscr_cnt = $_REQUEST['subscr_cnt'];

$m = new NewsCtrl();
$m->subscr_start = $subscr_start;   
$m->subscr_cnt = $subscr_cnt;  
//echo '<br>$task='.$task;
switch( $task ) {
    case 'send':
        $m->MakeDispatch();
        break;
    default:
        break;
} 
 ?> 