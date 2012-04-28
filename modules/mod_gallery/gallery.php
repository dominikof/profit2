<?
if (!defined("SITE_PATH")) define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] );
include_once( SITE_PATH.'/include/defines.php' );
include_once( SITE_PATH.'/modules/mod_gallery/gallery.defines.php' );

$Page = &check_init('PageUser', 'PageUser');
if(empty($Page->FrontendPages))
    $Page->FrontendPages = &check_init('FrontendPages', 'FrontendPages');

$ModulesPlug = new ModulesPlug();
$id_module = $ModulesPlug->GetModuleIdByPath( '/modules/mod_gallery/gallery.backend.php' );


if(!isset ($Page->Gallery))
    $Page->Gallery = &check_init('GalleryLayout', 'GalleryLayout');
$Page->Gallery->module =  $id_module;    // 156
$Page->module=$id_module;
$Gallery = &$Page->Gallery;

if( !isset( $_REQUEST['task'] ) ) $task = '';
else $task = $_REQUEST['task'];

if( !isset( $_REQUEST['start'] ) ) $start = 0;
else $start = $_REQUEST['start'];

if( !isset( $_REQUEST['sort'] ) ) $sort = NULL;
else $sort = $_REQUEST['sort'];

if( !isset( $_REQUEST['display'] ) ) $display = 8;
else $display = $_REQUEST['display'];

if(!isset($_REQUEST['page'])) $page=1;
else $page=$_REQUEST['page'];
if($page>1) $start = ($page-1)*$display;
if($page=='all') {
   $start = 0;
   $display = 999999;
}

if( !isset( $_REQUEST['cat'] ) ) $cat = NULL; // category cod
else {
    $cat = $_REQUEST['cat'];
    $idCat= $Gallery->GetCategoryIdByTranslit($cat);

    if(!$idCat)
        $Page->Set_404();
    $Gallery->fltr = ' AND `category`='.$idCat;
    $Gallery->cat  = $idCat;
}

if( !isset( $_REQUEST['position'] ) ) $position = NULL;
else {
    $position = $_REQUEST['position'];
    $idPosition= $Gallery->GetPositionIdByTranslit($position, $idCat);

    if(!$idPosition)
        $Page->Set_404();
    $Gallery->id = $idPosition;
}

$Gallery->task = $task;
$Gallery->display = $display;
$Gallery->page = $page;
$Gallery->start = $start;
$Gallery->sort = $sort;

$Page->FrontendPages->lang_id = _LANG_ID;
$Page->FrontendPages->page = PAGE_GALLERY;

$Gallery->SetMetaData($Page->FrontendPages->page);

if ( empty($Gallery->title) ) $Title = $Gallery->multi['TXT_GALLERY_TITLE'];
else $Title = $Gallery->title;
if ( empty($Gallery->description) ) $Description = $Gallery->multi['TXT_GALLERY_TITLE'];
else $Description = $Gallery->description;
if ( empty($Gallery->keywords) ) $Keywords = $Gallery->multi['TXT_GALLERY_TITLE'];
else $Keywords = $Gallery->keywords;

$Page->SetTitle( $Title );
$Page->SetDescription( $Description );
$Page->SetKeywords( $Keywords );

$Page->WriteHeader();

/*$title_content = null;
if($Gallery->task=='position'){
    $title_content = $Gallery->Spr->GetNameByCod(TblModGalleryTxt, $Gallery->id, $Gallery->lang_id, 1);
}
elseif($Gallery->task=='last'){
    $title_content = $Gallery->multi['TXT_FRONT_TITLE_LATEST'];
}
elseif($Gallery->task=='showall'){
    if( !empty($Gallery->category)) $title_content = $Gallery->Spr->GetNameByCod(TblModGalleryCat, $Gallery->cat, $Gallery->lang_id, 1);
    else $title_content = $Gallery->multi['TXT_ALL_GALLERY'];
}
else*/{
    $title_content = $Gallery->multi['TXT_GALLERY_TITLE'];
}
//$Gallery->ShowGalleryNavigation();

$Page->Form->WriteContentHeader($title_content,false,false);
switch( $Gallery->task ){
    case 'last':
    case 'showall':
        $Gallery->fltr = $Gallery->fltr." AND `status`='a'";
        $Gallery->ShowGallerysByPages();
        break;

    case 'cat':
        if($cat!=NULL) {
            $Gallery->fltr = $Gallery->fltr." AND `status`='a'";
            $Gallery->ShowGallerysByPages();
        }
        else
            $Gallery->ShowGalleryCat();
        break;

    case 'position':
        $Gallery->ShowGalleryFull();
        /*?><div class="banner"><?$Page->Banner->GetBanner(15,1); //Детально відео, фото 1?></div><?
        $Gallery->GalleryCatLast($Gallery->cat, 4);

        if(!isset($Gallery->Comments))
            $Gallery->Comments = new FrontComments($Gallery->module, $Gallery->id);
        $Gallery->Comments->ShowCommentsByModuleAndItem();
        $Gallery->Comments->VkontakteComments();
        $Gallery->Comments->FacebookComments();
        ?><div class="banner"><?$Page->Banner->GetBanner(16,1); //Детально відео, фото 2?></div><?*/
        break;

    case 'add':
        $Gallery->ShowAddForm();
        break;

    /*case 'save_data':
        print_r($_REQUEST);
        $Gallery->CheckNewData();
        if($Gallery->Err!='') $Gallery->ShowAddForm();
        else {
            //$res = $Gallery->SaveData();
            if(!$Gallery->SaveData()) echo "<p class=err> Данные НЕ сохранены!</p>";
            else echo "<p>Предложение добавлено. После проверки модератором станет доступным.</p>";
        }
        break;*/

    default:
        $Gallery->fltr = $Gallery->fltr." AND `status`='a'";
        $Gallery->ShowGallerysByPages();

}
//$Gallery->ShowGalleryTask();
$Page->Form->WriteContentFooter();
$Page->WriteFooter();
?>