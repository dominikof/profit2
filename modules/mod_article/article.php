<?
if (!defined("SITE_PATH")) define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] ); 
include_once( SITE_PATH.'/include/defines.php' );
include_once( SITE_PATH.'/modules/mod_article/article.defines.php' );

//========= FIRST DEFINE PAGE LANGUAGE  BEGIN ===========
$Page = new PageUser();
$Msg = new ShowMsg();
$Msg->SetShowTable(TblModArticleSprTxt);
//========= FIRST DEFINE PAGE LANGUAGE BEGIN  ===========

$ModulesPlug = new ModulesPlug();
$id_module = $ModulesPlug->GetModuleIdByPath( 'mod_article/article.backend.php' );
$Page->module = $id_module;

if( !empty($Page->Article) )
$Article = &check_init('ArticleLayout', 'ArticleLayout');

if( !isset( $_REQUEST['task'] ) ) $task = '';
else $task = $Article->Form->GetRequestTxtData($_REQUEST['task'], 1);

if( !isset( $_REQUEST['start'] ) ) $start = 0;
else $start = $Article->Form->GetRequestTxtData($_REQUEST['start'], 1);

if( !isset( $_REQUEST['sort'] ) ) $sort = NULL;
else $sort = $Article->Form->GetRequestTxtData($_REQUEST['sort'], 1);

if( !isset( $_REQUEST['display'] ) ) $display = 10;
else $display = $Article->Form->GetRequestTxtData($_REQUEST['display'], 1);

if(!isset($_REQUEST['page'])) $page=1;
else $page = $Article->Form->GetRequestTxtData($_REQUEST['page'], 1);
if($page>1) $start = ($page-1)*$display;
if($page=='all') {
   $start = 0;
   $display = 999999;
}

if( !isset( $_REQUEST['cat'] ) ) $cat = NULL;
else $id = $Article->Form->GetRequestTxtData($_REQUEST['cat'], 1);

if( !isset( $_REQUEST['art'] ) ) $art = NULL;
else $id = $Article->Form->GetRequestTxtData($_REQUEST['art'], 1);

// $str_cat - for mod_rewrite
if( isset( $_REQUEST['str_cat'] ) ){ 
    $Article->category = $Article->Spr->GetCodByTranslit(TblModArticleCat, $Article->Form->GetRequestTxtData($_REQUEST['str_cat'], 1), $Article->lang_id);
    $Article->category_name = $Article->Spr->GetNameByCod('mod_article_spr_category',$Article->category,$Article->lang_id);
    if( empty($Article->category) ) $Page->Set_404(); 
    $Article->fltr .= " AND `".TblModArticle."`.`category`='".$Article->category."'";
}

// $str_news - for mod_rewrite 
if( isset( $_REQUEST['str_art'] ) ){
    $Article->id = $Article->GetIdArtByStrArt($Article->Form->GetRequestTxtData($_REQUEST['str_art'], 1));
    if(empty($Article->id)) $Page->Set_404(); 
    $Article->fltr .= " AND `".TblModArticle."`.`id`='".$Article->id."'"; 
}

if( !isset( $_REQUEST['name'] ) ) $name = NULL;
else $name = $Article->Form->GetRequestTxtData($_REQUEST['name'], 1);

if( !isset( $_REQUEST['url'] ) ) $url = NULL;
else $url = $Article->Form->GetRequestTxtData($_REQUEST['url'], 1);

if( !isset( $_REQUEST['short_descr'] ) ) $short_descr = NULL;
else $short_descr = $Article->Form->GetRequestTxtData($_REQUEST['short_descr'], 0);

if( !isset( $_REQUEST['full_descr'] ) ) $full_descr = NULL;
else $full_descr = $Article->Form->GetRequestTxtData($_REQUEST['full_descr'], 0);

if( !isset( $_REQUEST['email'] ) ) $email = NULL;
else $email = $Article->Form->GetRequestTxtData($_REQUEST['email'], 1);

if( !isset( $_REQUEST['a_keywords'] ) ) $a_keywords = NULL;
else $a_keywords = $Article->Form->GetRequestTxtData($_REQUEST['a_keywords'], 1);

$Article->task = $task; 
$Article->display = $display;
$Article->page = $page;
$Article->start = $start;
$Article->sort = $sort;

$Article->name = $name;
$Article->url = addslashes($url);
$Article->short_descr = $short_descr;
$Article->full_descr = $full_descr;
$Article->email = $email;
$Article->a_keywords = $a_keywords;

 
if(isset ($Page->FrontendPages)) 
    $FrontendPages = &$Page->FrontendPages;
else
    $FrontendPages = &check_init('FrontendPages', 'FrontendPages');
    
$FrontendPages->lang_id = _LANG_ID; 
$FrontendPages->page = PAGE_ARTICLE;
if($Article->category==1)
$FrontendPages->page = 73;
if($Article->category==2)
$FrontendPages->page = 90;


$Article->SetMetaData(PAGE_ARTICLE);  

if ( empty($Article->title) ) $Title = 'Статьи';
else $Title = $Article->title;
if ( empty($Article->description) ) $Description = 'Статьи';
else $Description = $Article->description;
if ( empty($Article->keywords) ) $Keywords = 'cтатьи';
else $Keywords = $Article->keywords; 

$Page->SetTitle( $Title );
$Page->SetDescription( $Description );
$Page->SetKeywords( $Keywords );   

$Page->WriteHeader();

/*if($Article->task=='art'){
    $title_content = $Article->Spr->GetNameByCod(TblModArticleTxt, $Article->id, $Article->lang_id, 1);
}
elseif($Article->task=='last'){
    $title_content = $Article->multi['TXT_FRONT_TITLE_LATEST'];    
}
elseif($Article->task=='arch'){
    $title_content = $Article->multi['TXT_FRONT_TITLE_ARCH'];    
}
elseif($Article->task=='showall'){
    if( !empty($Article->category)) 
        $title_content = $Article->Spr->GetNameByCod(TblModArticleCat, $Article->category, $Article->lang_id, 1);
    else 
        $title_content = $Article->multi['TXT_ALL_ARTICLES'];    
}
else*/
if(isset($Article->category_name ) && !empty($Article->category_name ))
{
    $title_content=$Article->category_name;
}else	 $title_content = $Article->multi['TXT_ARTICLE_TITLE'];    

$Page->Form->WriteContentHeader($title_content, false,false);
?><div id="articles"><?
switch( $Article->task ){

    case 'arch':
        $Article->fltr = $Article->fltr." AND `".TblModArticle."`.`status`='e'";
        $Article->ShowArticlesByPages();
        break;

    case 'showall':
        $Article->fltr = $Article->fltr." AND `".TblModArticle."`.`status`!='i'";
        $Article->ShowArticlesByPages();
        break;

    case 'art':
        $Article->fltr = $Article->fltr." AND `".TblModArticle."`.`status`!='i'";
        $info = $Article->ShowArticleFull( $Article->id );
        echo '<p>'.$info;
        break;
        

    case 'last':
        $Article->fltr = $Article->fltr." AND `".TblModArticle."`.`status`='a'";
        $Article->ShowArticlesByPages();
        break;
        
    default: 
        $Article->fltr = $Article->fltr." AND `".TblModArticle."`.`status`='a'";
        $Article->ShowArticlesByPages();
}
?></div><?
//$Article->ShowArticleTask(); 
$Page->Form->WriteContentFooter();
$Page->WriteFooter();
?>