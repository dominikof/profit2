<?
// ================================================================================================
//    System     : PrCSM05
//    Module     : Article
//    Version    : 1.0.0
//    Date       : 27.01.2006
//    Licensed To:
//                 Igor  Trokhymchuk  ihoru@mail.ru
//                 Andriy Lykhodid    las_zt@mail.ru
//
//    Purpose    : Front end of Article module
//
// ================================================================================================


 include_once( SITE_PATH.'/modules/mod_article/article.defines.php' );


 if( !isset( $_REQUEST['task'] ) ) $task = 'last';
 else $task = $_REQUEST['task'];

 if( !isset( $_REQUEST['cat'] ) ) $cat = NULL;
 else $id = $_REQUEST['cat'];

 if( !isset( $_REQUEST['art'] ) ) $art = NULL;
 else $id = $_REQUEST['art'];

 if( !isset( $_REQUEST['display'] ) ) $display = 20;
 else $display = $_REQUEST['display'];

 if( !isset( $_REQUEST['start'] ) ) $start = 0;
 else $start = $_REQUEST['start'];

 if( !isset( $_REQUEST['sort'] ) ) $sort = NULL;
 else $sort = $_REQUEST['sort'];

 if( !isset( $_REQUEST['lang_id'] ) ) $lang_id = _LANG_ID;
 else $lang_id= $_REQUEST['lang_id']; 
 
  // $str_cat - for mod_rewrite
   if( !isset( $_REQUEST['str_cat'] ) ) $str_cat = NULL;
 else $str_cat = $_REQUEST['str_cat'];
  // $str_news - for mod_rewrite 
    if( !isset( $_REQUEST['str_art'] ) ) $str_art = NULL;
 else $str_art = $_REQUEST['str_art'];

 $a = new ArticleLayout($display, $sort, $start);
  if( isset( $_REQUEST['str_cat'] ) ){
  $category = $a->GetIdCatByStrCat($str_cat);
  $a->category = $category;
  $task = 'cat';
  } 
 
 if( isset( $_REQUEST['str_art'] ) ){
  $id = $a->GetIdArtByStrArt($str_art);
  $a->id = $id;
  $task = 'art';
  } 
  
  
    $rows = $a->getNRows();
 //   echo "<br> rows = ".$rows;
    $k =  round($rows/$display,0);
 //   echo "<br> k = ".$k;
    $pp  =$display*$k;
//    echo "<br> pp = ".$pp;
    $p0 = $rows-$pp;
//    echo "<br> p0 = ".$p0;
 
   if(!isset($_REQUEST['start'])) $start=$rows-$display-$p0;
  else $start=$_REQUEST['start'];
   if($start<0) $start = 0;
 if($p0>0) $display = $display+$p0;  

 $a->display = $display;
 $a->start = $start;
 $a->sort = $sort;
 $a->lang_id = $lang_id;
 $a->str_cat = $str_cat;
 $a->str_art = $str_art;
 

  $a->ShowNavigation();
 //echo '<br>$a->lang_id='.$a->lang_id.' $art='.$art;
 //echo 'task='.$task;
switch( $task )
{
 case 'last':
   $a->ShowArticles('last');
   break;

 case 'arch':
   $a->ShowArticles('arch');
   break;

 case 'all':
   $a->ShowArticles('all');
   break;

 case 'cat':
   $a->ShowArticles('cat', $a->category );
   break;

 case 'art':
   $info = $a->ShowArticleFull( $a->id );
   echo '<p>'.$info;
   break;

  default: $a->ShowArticles('last');
}

?>