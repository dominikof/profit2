<?php
// ================================================================================================
// System : SEOCMS
// Module : upload.php
// Version : 1.0.0
// Date : 01.07.2010
// Licensed To:
// Oleg Morgalyuk oleg4444@bk.ru
//
// Purpose : script for all actions with reference-books
//
// ================================================================================================
if (!defined("SITE_PATH")) define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] );
include_once( SITE_PATH.'/admin/include/defines.inc.php' );
$page = new PageAdmin();

if( !isset($_REQUEST['id']) ) $id=NULL;
else $id = $_REQUEST['id'];

if( !isset($_REQUEST['path']) ) $path=NULL;
else $path = $_REQUEST['path'];

if( !isset($_REQUEST['img']) ) $img=NULL;
else $img = $_REQUEST['img'];

if( !isset($_REQUEST['t1']) ) $t1=NULL;
else $t1 = $_REQUEST['t1'];

if( !isset($_REQUEST['t2']) ) $t2=NULL;
else $t2 = $_REQUEST['t2'];

if( !isset($_REQUEST['task']) ) $task=NULL;
else $task = $_REQUEST['task'];
 switch ($task)
{
    case 'delsl':
            $upload = new UploadClass(NULL,NULL,NULL,$t1,$t2);
            $upload->DeleteFiles($id, $path);
    break;
    case 'delslImages':
            $upload = new UploadImage(NULL,NULL,NULL,$t1,$t2);
            $upload->DeleteImages($id,$img,$path);
    break;
}

?>