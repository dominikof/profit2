<?php
/**
* news.php
* script for all actions with news on front-end
* @package News Package of SEOCMS
* @author Yaroslav Gyryn  <yaroslav@seotm.com>
* @version 1.1, 22.07.2011
* @copyright (c) 2010+ by SEOTM
*/
include_once( $_SERVER['DOCUMENT_ROOT'].'/include/defines.php' );
include_once( SITE_PATH.'/modules/mod_news/news.defines.php' );

$Page = &check_init('PageUser', 'PageUser');
$ModulesPlug = &check_init('ModulesPlug', 'ModulesPlug');
$id_module = $ModulesPlug->GetModuleIdByPath( 'mod_news/news.backend.php' );
$News = &check_init('NewsLayout', 'NewsLayout', "NULL, NULL, NULL, '$id_module'");
$Page->module =  $id_module;

if( !isset( $_REQUEST['task'] ) ) $News->task = '';
else $News->task = $News->Form->GetRequestTxtData($_REQUEST['task'], 1);

if(!isset($_REQUEST['sort'])) $News->sort=NULL;
else $News->sort=$News->Form->GetRequestTxtData($_REQUEST['sort'], 1);

if(!isset($_REQUEST['start'])) $News->start=0;
else $News->start=$News->Form->GetRequestTxtData($_REQUEST['start'], 1);

if(!isset($_REQUEST['display'])) $News->display=10;
else $News->display=$News->Form->GetRequestTxtData($_REQUEST['display'], 1);

if(!isset($_REQUEST['page'])) $News->page=1;
else $News->page = $News->Form->GetRequestTxtData($_REQUEST['page'], 1);
//echo '<br />$News->display='.$News->display;
if($News->page>1) $News->start = ($News->page-1)*$News->display;
if(strval($News->page)=='all') {
    $News->start = 0;
    $News->display = 999999;
}

if( !isset( $_REQUEST['day'] ) )$News->day = NULL;
else $News->day = $_REQUEST['day'];

if( !isset( $_REQUEST['category'] ) ) $News->category = NULL;
else {
    $News->category = $News->Form->GetRequestTxtData($_REQUEST['category'], 1);
    $News->fltr .= " AND `".TblModNews."`.`id_category`=".$News->category;
} 

if( !isset ( $_REQUEST['id'] ) ) $News->id = NULL;
else {
    $News->id = $News->Form->GetRequestTxtData($_REQUEST['id'], 1);
    $News->fltr .= " AND `".TblModNews."`.`id`=".$News->id;
}

// $str_cat - for mod_rewrite
if( !isset( $_REQUEST['str_cat'] ) ) $News->str_cat = NULL;
else{
    $News->str_cat = $News->Form->GetRequestTxtData($_REQUEST['str_cat'], 1);
    $News->category = $News->Spr->GetCodByTranslit(TblModNewsCat, $News->str_cat, $News->lang_id);
    if( empty($News->category) ) $Page->Set_404();
    else $News->fltr .= " AND `".TblModNews."`.`id_category`=".$News->category;
}
//echo '<br>$News->str_cat='.$News->str_cat.' $News->category='.$News->category;

// $str_news - for mod_rewrite 
if( !isset( $_REQUEST['str_news'] ) ) $News->str_news = NULL;
else{
    $News->str_news = $News->Form->GetRequestTxtData($_REQUEST['str_news'], 1);
    $News->id = $News->GetIdNewsByStrNews($News->str_news);
    if(empty($News->id)) $Page->Set_404();
    else $News->fltr .= " AND `".TblModNews."`.`id`=".$News->id;
}

if( !isset ( $_REQUEST['categories'] ) ) $News->categories = NULL;
else $News->categories = $_REQUEST['categories'];

if( !isset( $_REQUEST['s_keywords'] ) ) $News->s_keywords = NULL;
else $News->s_keywords = $News->Form->GetRequestTxtData($_REQUEST['s_keywords'], 1); 

if( !isset ( $_REQUEST['subscriber'] ) ) $News->subscriber = NULL;
else $News->subscriber = $News->Form->GetRequestTxtData($_REQUEST['subscriber'], 1);

if( !isset ( $_REQUEST['subscr_pass'] ) ) $News->subscr_pass = NULL;
else $News->subscr_pass = strip_tags(trim($_REQUEST['subscr_pass']));


if(isset ($Page->FrontendPages)) 
    $FrontendPages = &$Page->FrontendPages;
else
    $FrontendPages = &check_init('FrontendPages', 'FrontendPages');
      
$FrontendPages->lang_id = $News->lang_id;  
$FrontendPages->page = PAGE_NEWS;

$News->SetMetaData($FrontendPages->page);

if ( empty($News->title) ) $Title = $News->multi["_TXT_NEWS_TITLE"];
else $Title = $News->title;
if ( empty($News->description) ) $Description =  $News->multi["_TXT_META_DESCRIPTION"];
else $Description = $News->description;
if ( empty($News->keywords) ) $Keywords = $News->multi["_TXT_META_KEYWORDS"];
else $Keywords = $News->keywords;   

$Page->SetTitle( $Title );
$Page->SetDescription( $Description );
$Page->SetKeywords( $Keywords );   

if( isset( $_REQUEST['id'] ) ) $News->fltr = ' AND `id`='.$_REQUEST['id'];


if($News->task=='showfull'){
    $title_content = $News->Spr->GetNameByCod(TblModNewsSprSbj, $News->id, $News->lang_id, 1);
}
elseif($News->task=='showa'){
    $title_content = $News->multi['MOD_NEWS_LATEST'];    
}
elseif($News->task=='arch'){
    $title_content = $News->multi['MOD_NEWS_ARCH'];    
}
elseif($News->task=='showall'){
    if( !empty($News->category)) $title_content = $News->Spr->GetNameByCod(TblModNewsCat, $News->category, $News->lang_id, 1);
    else $title_content = $News->multi['MOD_NEWS_ALL'];    
}
elseif($News->task=='new_subscriber' OR $News->task=='save_reg_data'){
    $title_content = $News->multi['TXT_SUBSCRIBE'];    
}
else{
    $title_content = $News->multi['_FLD_NEWS'];    
}
//$News->ShowNewsPath();
$Page->WriteHeader(); 
$Page->Form->WriteContentHeader($title_content,false,false);

//echo  '<br />$News->task='.$News->task;
switch( $News->task ){
    case 'showsm':
        $News->fltr = $News->fltr." and `".TblModNews."`.status='a'";
        $News->ShowNewsByPages();
        break;
        
    case 'showa':
        $News->fltr = $News->fltr." and `".TblModNews."`.status='a'";
        $News->ShowNewsByPages();
        break;

    case 'showall':
        $News->fltr = $News->fltr." and `".TblModNews."`.status!='i'";
        $News->ShowNewsByPages();
        break;
        
    case 'arch':
        $News->fltr = $News->fltr." and `".TblModNews."`.status='e'";
        $News->ShowNewsByPages();
        break;

    case 'showfull':
        $News->fltr = $News->fltr." and `".TblModNews."`.status!='i'";
        $News->NewsShowFull();
        break;

    case 'showrelart':
        $News->NewsRelartDisplay( $News->Form->GetRequestTxtData($_REQUEST['relart'], 1) );
        break;

    case 'news_by_date':
        $News->ShowNewsByDate($day);
        break;

    case 'search_result':
        $News->ShowSearchResult();
        break;

    case 'last_news':
        $News->NewsLast();
        break;
        
    case 'new_subscriber':
        $News->SubscrRegForm();
        break;
        
    case 'save_reg_data':  
        if ( $News->CheckFields()!=NULL ){
            $News->SubscrRegForm();
            break;
        }
        if($News->SubscrSave()){
            if ( !$News->SendHTML() ) {
                $News->ShowTextMessages($News->multi['MSG_SUBSCRIBER_NOT_SENT']);
            }
            else { 
                $News->ShowTextMessages($News->multi['MSG_SUBSCRIBER_SAVED']);   
                $News->ShowTextMessages($News->multi['MSG_SUBSCRIBER_SENT_OK']);
            }
        }
        else { 
            $News->ShowTextMessages($News->multi['MSG_SUBSCRIBER_NOT_SAVED']);   
        }  
        break;

    case 'activate': 
        $News->ActivateUser($News->subscriber);
        break;
        
    case 'subscr_del':
        if($News->SubscrDel()) { $News->ShowTextMessages($News->multi['MSG_SUBSCRIBER_DEL_OK']); }
        else { $News->ShowTextMessages($News->multi['MSG_SUBSCRIBER_NOT_DEL']); }
        break;

    default:
        $News->fltr = " and `".TblModNews."`.status='a'";
        $News->ShowNewsByPages();
        break;
}
 
//$News->ShowNewsLinks();

$Page->Form->WriteContentFooter();
$Page->WriteFooter();
?>