<?
// ================================================================================================
//    System     : SEOCMS
//    Module     : Gallery
//    Date       : 01.07.2010
//    Licensed To: Yaroslav Gyryn   
//    Purpose    : Class definition for Gallery - moule
// ================================================================================================


// ================================================================================================
//    Class             : Gallery
//    Date              : 01.07.2010
//    Constructor       : Yes
//    Parms             :
//    Returns           : None
//    Description       : Gallery Module
//    Programmer        :  Yaroslav Gyryn
// ================================================================================================
class Gallery {
    var $id;
    var $dttm;
    var $category;
    var $status;
    var $img;
    var $position;
    var $name;
    var $short;
    var $full;

    var $Right;
    var $Form;
    var $Msg;
    var $Spr;

    var $page;   
    var $display;
    var $sort;
    var $start;
    var $rows;

    var $user_id;
    var $use_image;
    var $module;

    var $fltr;    // filter of group Production

    var $lang_id;
    var $sel = NULL;
    var $Err = NULL;
    var $title;
    var $keywords;
    var $description;

    var $str_cat;
    var $str_art;

    var $settings = null;
 
    // ================================================================================================
    //    Function          : Gallery (Constructor)
    //    Version           : 1.0.0
    //    Date              : 01.07.2010
    //    Parms             :
    //    Returns           :
    //    Description       : Gallery
    // ================================================================================================
    function Gallery()
    {
      $this->db =  DBs::getInstance();
      $this->Right =  &check_init('Rights', 'Rights');
      $this->Form = &check_init('FormGallery', 'Form', "'mod_gallery'");         /* create Form object as a property of this class */
      if(empty($this->Msg)) $this->Msg = &check_init('ShowMsg', 'ShowMsg');
     //$this->Msg->SetShowTable(TblModNewsSprTxt);
     if (empty($this->Spr)) $this->Spr = &check_init('SysSpr', 'SysSpr');
          
        
        if( defined("_LANG_ID") ) $this->lang_id = _LANG_ID;
        //$this->Right =  new Rights;                   /* create Rights obect as a property of this class */
        //$this->Form = new Form( 'form_art' );        /* create Form object as a property of this class */
        //$this->Msg = new ShowMsg();                   /* create ShowMsg object as a property of this class */
        //$this->Msg->SetShowTable(TblModGallerySprTxt);
        $this->use_image=1;
        //$this->Spr = new SysSpr( NULL, NULL, NULL, NULL, NULL, NULL, NULL ); /* create SysSpr object as a property of this class */
     
        $this->settings = $this->GetSettings();
    }// end of Gallery (Constructor) 


    // ================================================================================================
    // Function : GetGalleryData()
    // Date : 20.09.2009
    // Returns :      true,false / Void
    // Description :  Return gallery data
    // Programmer :  Yaroslav Gyryn
    // ================================================================================================
    function GetGalleryData( $art_id = NULL )
    {
        if(!$art_id) return true; 
        //$q = "select * from ".TblModNews." where id='$news_id'";
        $q = "SELECT `".TblModGallery."`.*, 
                            `".TblModGalleryCat."`.name AS `cat_name`, 
                            `".TblModGalleryCat."`.translit AS cat_translit,
                            `".TblModGalleryTxt."`.name AS `sbj`, 
                            `".TblModGalleryTxt."`.translit, 
                            `".TblModGalleryTxt."`.short,
                            `".TblModGalleryTxt."`.full
              FROM `".TblModGallery."`, `".TblModGalleryCat."`, `".TblModGalleryTxt."`
              WHERE `".TblModGallery."`.id='".$art_id."'
              AND `".TblModGallery."`.category=`".TblModGalleryCat."`.cod
              AND `".TblModGalleryCat."`.lang_id='".$this->lang_id."'
              AND `".TblModGallery."`.id=`".TblModGalleryTxt."`.cod
              AND `".TblModGalleryTxt."`.lang_id='".$this->lang_id."'
             ";
        $res = $this->db->db_Query( $q );
        //echo '<br>'.$q.' $res='.$res.' $this->db->result='.$this->db->result; 
        if ( !$res OR !$this->db->result ) return false;
        $rows = $this->db->db_GetNumRows();
        return $rows;
    } //end of fuinction GetGalleryData() 


    // ================================================================================================
    // Function : GetGalleryCatLast()
    // Date : 01.05.2011
    // Returns :      true,false / Void
    // Description :  Get Gallery Cat Last
    // Programmer :  Yaroslav Gyryn
    // ================================================================================================
    function GetGalleryCatLast( $id_cat=1, $limit=3, $active=true)
    {
        $q = "SELECT 
            `".TblModGallery."`.id,
            `".TblModGallery."`.dttm,
            `".TblModGallery."`.category as id_category,
            `".TblModGalleryTxt."`.translit,
            `".TblModGalleryTxt."`.lang_id,
            `".TblModGalleryTxt."`.name,
            `".TblModGalleryTxt."`.short,
            `".TblModGalleryCat."`.name as category,
            `".TblModGalleryCat."`.translit as cat_translit
        FROM 
            `".TblModGallery."`, `".TblModGalleryTxt."`, `".TblModGalleryCat."`
        WHERE 
            `".TblModGallery."`.category = `".TblModGalleryCat."`.cod AND 
            `".TblModGalleryCat."`.lang_id='".$this->lang_id."' AND 
            `".TblModGallery."`.id = `".TblModGalleryTxt."`.cod and
            `".TblModGallery."`.category = '".$id_cat."' and 
            `".TblModGalleryTxt."`.lang_id ='".$this->lang_id."' and 
            `".TblModGalleryTxt."`.name !='' ";
            
        if($active==true)
           $q .= " and `".TblModGallery."`.status='a' ";
        if(isset($this->id))
            $q .= " and `".TblModGallery."`.id!= '".$this->id."' ";
        
        $q .="ORDER BY 
            `position` desc LIMIT ".$limit;
            
        $res = $this->db->db_Query( $q );
        //echo '<br>'.$q.' <br/>$res='.$res.' $this->db->result='.$this->db->result; 
        if ( !$res OR !$this->db->result ) return false;
        $rows = $this->db->db_GetNumRows();
        $array = array();
        for( $i=0; $i<$rows; $i++ )  {
            $array[$i] =  $this->db->db_FetchAssoc($res);
        }
        return $array;
    } //end of function GetGalleryCatLast() 
     
    
    // ================================================================================================
    // Function : GetGalleryLast()
    // Date : 21.05.2011
    // Returns :      true,false / Void
    // Description :  Get Gallery Last
    // Programmer :  Yaroslav Gyryn
    // ================================================================================================
    function GetGalleryLast()
    {
        $limit = 1;
        $q = "SELECT 
            `".TblModGallery."`.id,
            `".TblModGallery."`.category as id_category,
            `".TblModGalleryTxt."`.translit,
            `".TblModGalleryTxt."`.name,
            `".TblModGalleryTxt."`.short,
            `".TblModGalleryCat."`.translit as cat_translit
        FROM 
            `".TblModGallery."`, `".TblModGalleryTxt."`, `".TblModGalleryCat."`
        WHERE 
            `".TblModGallery."`.category = `".TblModGalleryCat."`.cod AND 
            `".TblModGalleryCat."`.lang_id='".$this->lang_id."' AND 
            `".TblModGallery."`.id = `".TblModGalleryTxt."`.cod and
            `".TblModGalleryTxt."`.lang_id ='".$this->lang_id."' and 
            `".TblModGalleryTxt."`.name !='' and
            `".TblModGallery."`.status='a' ";
            $q .="ORDER BY 
            `position` desc LIMIT ".$limit;
            
        $res = $this->db->db_Query( $q );
        //echo '<br>'.$q.' $res='.$res.' $this->db->result='.$this->db->result; 
        if ( !$res OR !$this->db->result ) return false;
        $rows = $this->db->db_GetNumRows();
        return $this->db->db_FetchAssoc($res);
    } //end of function GetGalleryLast() 
    

    // ================================================================================================
    // Function : GetAllGallerysIdCat()
    // Date :    26.05.2011
    // Returns : true/false
    // Description : Get all Id gallery for $cat
    // Programmer :  Yaroslav Gyryn
    // ================================================================================================
    function GetAllGallerysIdCat( $id_cat=1, $idModule = null)
    {
        $q = "
            SELECT 
                `".TblModGallery."`.id,
                `".TblModGallery."`.dttm as start_date,
                `".TblModGallery."`.position
            FROM 
                `".TblModGallery."`
            WHERE 
                `".TblModGallery."`.category ='".$id_cat."' and 
               `".TblModGallery."`.status='a' 
            ORDER BY 
                `position` desc 
        ";
            
        $res = $this->db->db_Query( $q );
        //echo '<br>'.$q.' <br/>$res='.$res.' $this->db->result='.$this->db->result; 
        if ( !$res OR !$this->db->result ) return false;
        $rows = $this->db->db_GetNumRows();
        $array = array();
        for( $i=0; $i<$rows; $i++ )  {
            $row =  $this->db->db_FetchAssoc($res);
            $dateId = strtotime ($row['start_date']);
            $array[$dateId]['id'] = $row['id'];
            //$array[$dateId]['start_date'] = $row['start_date'];
            $array[$dateId]['id_module'] = $idModule;
        }
        return $array;
    } //end of function GetAllGallerysIdCat()



    // ================================================================================================
    // Function : GetGallerysIdByQuickSearch()
    // Date :    26.05.2011
    // Returns : true/false
    // Description : Get all Id articles for $search_keywords
    // Programmer :  Yaroslav Gyryn
    // ================================================================================================
    function GetGallerysIdByQuickSearch( $search_keywords = null, $idModule = null )
    {
        $search_keywords = stripslashes($search_keywords);
        $sel_table = NULL;
        $str_like = NULL;
        $filter_cr = ' OR ';
        $str_like = $this->build_str_like(TblModGalleryTxt.'.name', $search_keywords);
        $str_like .= $filter_cr.$this->build_str_like(TblModGalleryTxt.'.short', $search_keywords);
        $str_like .= $filter_cr.$this->build_str_like(TblModGalleryTxt.'.full', $search_keywords); 
        $sel_table = "`".TblModGallery."`, `".TblModGalleryTxt."`, `".TblModGalleryCat."` ";
   
       $q ="SELECT  
                `".TblModGallery."`.id,
                `".TblModGallery."`.dttm as start_date,
                `".TblModGallery."`.position as display
            FROM ".$sel_table."
            WHERE (".$str_like.")
            AND `".TblModGallery."`.category = `".TblModGalleryCat."`.cod 
            AND `".TblModGalleryTxt."`.lang_id = '".$this->lang_id."'
            AND `".TblModGallery."`.id = `".TblModGalleryTxt."`.cod
            AND `".TblModGalleryCat."`.lang_id='".$this->lang_id."' 
            ORDER BY  `".TblModGallery."`.position DESC";
       
        $res = $this->db->db_Query( $q );
        //echo '<br>'.$q.' <br/>$res='.$res.' $this->db->result='.$this->db->result; 
        if ( !$res OR !$this->db->result ) return false;
        $rows = $this->db->db_GetNumRows();
        $array = array();
        for( $i=0; $i<$rows; $i++ )  {
            $row =  $this->db->db_FetchAssoc($res);
            $dateId = strtotime ($row['start_date']);
            $array[$dateId]['id'] = $row['id'];
            //$array[$dateId]['start_date'] = $row['start_date'];
            $array[$dateId]['id_module'] = $idModule;
        }
        //print_r($array);
        return $array;
    } //end of function GetGallerysIdByQuickSearch() 
            
    // ================================================================================================
    // Function : ConvertDate()
    // Date : 01.07.2010
    // Parms :
    // Returns :      true,false / Void
    // Description :  Convert Date to nidle format
    // ================================================================================================
    function ConvertDate($date_to_convert, $showTime = false){
    //print_r($tmp = explode("-", $date_to_convert));
    $tmp = explode("-", $date_to_convert);
    if(!empty($tmp[2] )) {
        $tmp2 = explode(" ", $tmp[2]);
        $month = NULL;
        $day = NULL;
        $year = NULL;
        $month =  $tmp[1];
        $day = intval($tmp2[0]);
        $year = $tmp[0];
        if($showTime) {
            $time = $tmp2[1];
            $tmp3 = explode(":", $time);
            return $tmp3[0].':'.$tmp3[1];      //18:30
        }
        else
            return $day.".".$month.".".$year;
        //$month =  $this->Spr->GetShortNameByCod(TblSysSprMonth, intval($tmp[1]), $this->lang_id, 1);
    }
    return false;
} // end of function ConvertDate



    // ================================================================================================
    // Function : Link()
    // Date : 12.01.2010
    // Parms :
    // Returns :
    // Description : Return Link 
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function Link( $cat , $id = NULL)
    {
        if( !defined("_LINK")) {
            $Lang = new SysLang(NULL, "front");
            $tmp_lang = $Lang->GetDefFrontLangID();
            if( ($Lang->GetCountLang('front')>1 OR isset($_GET['lang_st'])) AND $this->lang_id!=$tmp_lang) {
                define("_LINK", "/".$Lang->GetLangShortName($this->lang_id)."/");
            }
            else {
                define("_LINK", "/");
            }
        }
        if($cat!=NULL and $id==NULL) 
            return _LINK.'gallery/'.$cat.'/';
        elseif($id!=NULL)
            return _LINK.'gallery/'.$cat.'/'.$id.'.html';
        else
            return _LINK.'gallery/';
    } // end of function Link()


    // ================================================================================================
    // Function : GetIdCatByIdArt()
    // Date : 01.07.2010
    // Parms :
    // Returns :
    // Description : 
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function GetIdCatByIdArt($id){
      $q = "select * from ".TblModGallery." where 1 and `id`='".$id."'";
      $res = $this->db->db_Query( $q );
      $rows = $this->db->db_GetNumRows();
    //echo "<br> q=".$q." res=".$res." rows=".$rows;
      $row = $this->db->db_FetchAssoc();    
    return $row['category']; 
    } // end of function GetIdCatByIdArt

    // ================================================================================================
    // Function : GetIdArtByStrArt()
    // Date : 01.07.2010
    // Parms :
    // Returns :
    // Description : 
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function GetIdArtByStrArt($str_art)
    {
        $q = "select * from ".TblModGalleryLinks." where 1 and `link`='".$str_art."'";
        $res = $this->db->db_Query( $q );
        $rows = $this->db->db_GetNumRows();
        //echo "<br>GetIdArtByStrArt  q=".$q." res=".$res." rows=".$rows;
        $row = $this->db->db_FetchAssoc();
        //echo "<br>ART q=".$q." res=".$res.' rows='.$rows.' cod='.$row['cod']; 
        return $row['cod']; 
    } // end of function GetIdArtByStrArt

    
    
    //------------------------------------------------------------------------------------------------------------
    //---------------------------- FUNCTION FOR SETTINGS OF OFFERS START ---------------------------------------       
    //------------------------------------------------------------------------------------------------------------
        
   // ================================================================================================
   // Function : GetSettings()
   // Date : 01.07.2010
   // Parms :
   // Returns : true,false / Void
   // Description : return all settings of Gatalogue
   // Programmer : Yaroslav Gyryn
   // ================================================================================================
   function GetSettings()
   {       
    $db = new DB();
    $q="select * from `".TblModGallerySet."` where 1";
    $res = $db->db_Query( $q );
    //echo "<br /> q = ".$q." res = ".$res;
    if( !$db->result ) return false;
    $row = $db->db_FetchAssoc();
    $db1 = new DB();
    $q1="select * from `".TblModGallerySetSprMeta."` where `lang_id`='$this->lang_id' ";
    $res1 = $db1->db_Query( $q1 );
    //echo "<br /> q = ".$q." res = ".$res;
    if( !$db1->result ) 
        return false;
    $row1 = $db1->db_FetchAssoc();
    $row['title']=$row1['title'];
    $row['keywords']=$row1['keywords'];
    $row['description']=$row1['description'];
    return $row;         
   } // end of function GetSettings() 

   // ================================================================================================
   // Function : SetMetaData()
   // Date : 01.07.2010
   // Parms :
   // Returns : true,false / Void
   // Description : set title, description and keywords for this module or for current news or category
   //               of news
   // Programmer : Yaroslav Gyryn
   // ================================================================================================
  function SetMetaData($page)
   {
      $db1 = new DB();
    $q1="select * from `".TblModGallerySetSprMeta."` where `lang_id`='$this->lang_id' ";
    $res1 = $db1->db_Query( $q1 );
    //echo "<br /> q = ".$q." res = ".$res;
    if( !$db1->result ) return false;
    $row1 = $db1->db_FetchAssoc();

    $title=$row1['title'];
    $keywords=$row1['keywords'];
    $description=$row1['description'];
    
    // Установка через динамічні сторінки
    if(empty($this->id)) {
        if(!isset ($this->FrontendPages)) 
            $this->FrontendPages = Singleton::getInstance('FrontendPages');
        $this->FrontendPages->page_txt = $this->FrontendPages->GetPageTxt($page);
        
        $this->title = $this->FrontendPages->GetTitle();
        if(empty($this->title) )
            $this->title = $title;
        
        $this->description = $this->FrontendPages->GetDescription();
        if(empty($this->description))
            $this->description = $description;
        
        $this->keywords = $this->FrontendPages->GetKeywords();
        if(empty($this->keywords))
            $this->keywords = $keywords;
        return;
    }
    
    $this->title=$title;
    $this->keywords=$keywords;
    $this->description=$description;
    
   //echo " META SET: id=".$this->id." cat=".$this->category;
    $q = "SELECT `name`, `title`, `keywords`, `description` 
          FROM `".TblModGalleryTxt."`
          WHERE `".TblModGalleryTxt."`.cod='".$this->id."'
          AND `".TblModGalleryTxt."`.lang_id='".$this->lang_id."'
         ";
    $res = $this->db->db_Query( $q );
//        echo '<br>'.$q.' $res='.$res.' $this->db->result='.$this->db->result; 
    if ( !$res OR !$this->db->result ) return false;
    $rows = $this->db->db_GetNumRows();
    $row=$this->db->db_FetchAssoc();
    if( $this->id ) {$title =  $row['title'];
    if( empty($title) ) $title =  $row['name'];}
    else {
    if( $this->category ) $title = $this->Spr->GetNameByCod( TblModGalleryCat, $this->category, _LANG_ID, 1);
    }
    if( !empty($title) ) $this->title = $title.' | '.$this->title;
    else {
   // echo "<br>task=".$this->task;
    switch($this->task){
    case 'all':  $title = $this->Msg->show_text('TXT_META_TITLE_ALL');
                break;
    case 'last':  $title = $this->Msg->show_text('TXT_META_TITLE_LAST');
                break;
    case 'arch':  $title = $this->Msg->show_text('TXT_META_TITLE_ARCH');
                break;
    }
     if( !empty($title) ) $this->title = $title.' | '.$this->title;   
    }
    
  //  echo "<br> start = ".$this->start;
  //  echo "<br> display = ".$this->display;
  //  echo "<br> rows = ".$this->rows;
     $curr = round($this->start/$this->display, 0);
     $end = round($this->rows/$this->display, 0);
     
   //  echo "<br> curr = ".$curr;
   //  echo "<br> end = ".$end;
                                                    
     $page = $end-$curr;
     if($page>1) $this->title = $this->title." | ". $this->Msg->show_text('TXT_GALLERY_TITLE').' '.($this->start+1)."...".($this->start+$this->display);
       
   if( $this->id ) $descr = $row['description'];
    else {
    if( $this->category ) $descr = $this->Spr->GetNameByCod( TblModGalleryCat, $this->category, _LANG_ID, 1);
    }
    
    if( !empty($descr) ) $this->description = $descr.'. '.$this->description;
    else {
        if( !empty($title) ) $this->description = $title.'. '.$this->description;
    }

   if( $this->id ) $keywrds = $row['keywords'];
    else {
   if( $this->category ) $keywrds = $this->Spr->GetNameByCod( TblModGalleryCat, $this->category, _LANG_ID, 1);
   }
   
   if( !empty($keywrds) ) $this->keywords = $keywrds.', '.$this->keywords;
   //else $this->keywords = $title .', '.$this->keywords; 
   
   } //end of function  SetMetaData()  


// ================================================================================================
 // Function : QuickSearch()
 // Date : 01.07.2010
 // Parms : 
 // Returns : true,false / Void
 // Description :
 // Programmer : Yaroslav Gyryn
 // ================================================================================================    
 function QuickSearch($search_keywords){
   $search_keywords = stripslashes($search_keywords);
   
   $sel_table = NULL;
   $str_like = NULL;
   $filter_cr = ' OR ';

    $str_like = $this->build_str_like(TblModGalleryTxt.'.name', $search_keywords);
    $str_like .= $filter_cr.$this->build_str_like(TblModGalleryTxt.'.short', $search_keywords);
    $str_like .= $filter_cr.$this->build_str_like(TblModGalleryTxt.'.full', $search_keywords); 
    $sel_table = "`".TblModGallery."`, `".TblModGalleryTxt."`, `".TblModGalleryCat."` ";
   
   $q ="SELECT  
                `".TblModGallery."`.id, 
                `".TblModGallery."`.category,
                `".TblModGallery."`.status,
                `".TblModGalleryTxt."`.name as sbj,
                `".TblModGallery."`.position,
                `".TblModGalleryTxt."`.translit,
                `".TblModGalleryCat."`.name as category,
                `".TblModGalleryCat."`.translit as cat_translit
        FROM ".$sel_table."
        WHERE (".$str_like.")
        AND `".TblModGallery."`.category = `".TblModGalleryCat."`.cod 
        AND `".TblModGalleryTxt."`.lang_id = '".$this->lang_id."'
        AND `".TblModGallery."`.id = `".TblModGalleryTxt."`.cod
        AND `".TblModGalleryCat."`.lang_id='".$this->lang_id."' 
        ORDER BY  `".TblModGallery."`.position DESC";

   $res = $this->db->db_Query( $q );
   //echo '<br>'.$q.'<br/> res='.$res.' $tmp_db->result='.$this->db->result;
   if ( !$res) return false;
   if( !$this->db->result ) return false;  
   $rows = $this->db->db_GetNumRows();
   $array = array();
   for( $i = 0; $i <$rows; $i++ ){
        $array[] = $this->db->db_FetchAssoc();
   }
   return $array;
 } // end of function QuickSearch
 
 
// ================================================================================================
// Function : build_str_like
// Date : 01.07.2010 
//
// Parms : $find_field_name - name of the field by which we want to do search
//         $field_value - value of the field
// Returns : str_like_filter - builded string with special format;
// Description : create the string for SQL-command SELECT for search in the text field by any word
// Programmer : Yaroslav Gyryn
// ================================================================================================
function build_str_like($find_field_name, $field_value)
{
    $str_like_filter=NULL;
    // cut unnormal symbols
    $field_value=preg_replace("/[^\w\x7F-\xFF\s]/", " ", $field_value);
    // delete double spacebars
    $field_value=str_replace(" +", " ", $field_value);
    $wordmas=explode(" ", $field_value);

    for ($i=0; $i<count($wordmas); $i++){
          $wordmas[$i] = trim($wordmas[$i]);
          if (EMPTY($wordmas[$i])) continue;
          if (!EMPTY($str_like_filter)) $str_like_filter=$str_like_filter." AND ".$find_field_name." LIKE '%".$wordmas[$i]."%'";
          else $str_like_filter=$find_field_name." LIKE '%".$wordmas[$i]."%'";
    }
    if ($i>1) $str_like_filter="(".$str_like_filter.")";
    //echo '<br>$str_like_filter='.$str_like_filter;
    return $str_like_filter;
} //end offunction build_str_like()


// ================================================================================================
// Function : GetTranslitById()
// Version : 1.0.0
// Date : 01.07.2010 
// Parms :  $id    - id of the category
// Returns : true,false / Void
// Description :  return translit for category or current position
// Programmer : Yaroslav Gyryn
// ================================================================================================
function GetTranslitById($id = NULL, $lang_id = NULL)
{      
    $db = new DB();
    $q = "SELECT translit FROM `".TblModGalleryTxt."` WHERE `cod`='".$id."'";
    if( !empty($lang_id) ) $q = $q." AND `lang_id`='".$lang_id."'";
    $res =$db->db_Query( $q );
    //echo '<br>$q='.$q.' $res='.$res.' $db->result='.$db->result;
    if( !$res OR !$db->result ) return false;
    $row = $db->db_FetchAssoc();
    //echo '<br>$row[translit]='.$row['translit'];
    return $row['translit'];
}// end of function GetTranslitById()       


// ================================================================================================
// Function : SaveTranslit()
// Date : 01.07.2010 
// Parms :  $id_cat - if of the category
//          $id - id of the current position
//          $name_ind - translit name of the position
//          $name -  name of the current position
//          $translit_old - old values of translit field 
// Returns : true,false / Void
// Description :  save translit name of current position
// Programmer : Yaroslav Gyryn
// ================================================================================================
function SaveTranslit($id_cat, $id, $name = NULL, $translit, $translit_old, $lang_id)
{
    $db = new DB();
    $Crypt = new Crypt();
    $translitNew = NULL;
    $translit[$lang_id] = $this->Form->GetRequestTxtData($translit[$lang_id], 1);
    $translit_old[$lang_id] = $this->Form->GetRequestTxtData($translit_old[$lang_id], 1);
    $name[$lang_id] = $this->Form->GetRequestTxtData($name[$lang_id], 1);
    //echo '<br/>$translit_old[$lang_id]='.$translit_old[$lang_id].' $name_ind[$lang_id]='.$name_ind[$lang_id];
    
    //if exist old translit $translit_old[$lang_id] and it = current translit $name_ind[$lang_id] then no needs to save translit. 
    //Old translit must not to change automaticaly, only manualy!
    if( (!empty($translit_old[$lang_id]) AND $translit_old[$lang_id]==$translit[$lang_id]) ) 
        return $translit[$lang_id];
    
    //generate translit only for new position of catalog
    if( empty($translit_old[$lang_id]) ){
        //First check translit field and make transliteration of it 
        if( isset($translit[$lang_id]) AND !empty($translit[$lang_id])){
            $translitNew = $Crypt->GetTranslitStr(stripslashes($translit[$lang_id]));
            $translitNew = $this->GetTranslit($translitNew, $id_cat, $id, $lang_id);
        }
        //else check other field for generate translit and make transliteration of it
        elseif( isset($name[$lang_id]) AND !empty($name[$lang_id]) ) {
            $translitNew = $Crypt->GetTranslitStr(stripslashes($name[$lang_id]));
            $translitNew = $this->GetTranslit($translitNew, $id_cat, $id, $lang_id);
        }
    }
    else{
        $translitNew = stripslashes($translit[$lang_id]);
    }
    
    return $translitNew;
}

// ================================================================================================
// Function : GetTranslit()
// Date : 01.07.2010
// Parms :  $str        - string for checking
//          $id_cat     - id of the category
//          $id_prop    - id of the current position
//          $lang_id
// Returns : true,false / Void
// Description :  check the name for exist in translit
// Programmer : Yaroslav Gyryn
// ================================================================================================
function GetTranslit($str = NULL, $id_cat = NULL, $id_prop = NULL, $lang_id=NULL)
{      
    $db = new DB();
    
    $q = "SELECT 
                    `".TblModGalleryTxt."`.translit, 
                    `".TblModGallery."` .id as id_prop 
            FROM `".TblModGalleryTxt."` ,`".TblModGallery."` 
            WHERE 
                BINARY `translit` = BINARY '".$str."'
                AND
                    `".TblModGallery."`.id = `".TblModGalleryTxt."`.cod
                ";
    if( !empty($lang_id) ) 
            $q = $q." AND `".TblModGalleryTxt."`.lang_id='".$lang_id."'";
    if( $id_cat!=NULL ) 
            $q = $q." AND `".TblModGallery."`.category='".$id_cat."'";
    //-------- если проверяется конкретная позиция,а не категория -------
    $res =$db->db_Query( $q );
    //echo '<br>$q='.$q.' $res='.$res.' $db->result='.$db->result;
    if( !$res OR !$db->result ) return false;
    $rows = $db->db_GetNumRows();
    //echo '<br>$rows='.$rows;
    for($i=0;$i<$rows;$i++){
        $row = $db->db_FetchAssoc();
        //echo '<br>$id_prop='.$id_prop.' $row[id_prop]='.$row['id_prop'].' $row[translit]='.$row['translit'];
        
        // проверка конкретной позиции, если найденный транслит не пренадлежит данной позиции $id_prop, то значит уже есть такой транслит
        // у другой позиции $row['id_prop'], поэтому возвращаем его.
        if( $id_prop!=$row['id_prop'] ){
            return $row['translit'].$id_prop;
        }
    }// end for
    //echo '<br>$return='.$return;
    return $str;
}// end of function GetTranslit() 

    // ================================================================================================
     // Function : GetGallerysNameLinkForId()
     // Date : 15.06.2011
     // Returns :      true,false / Void
     // Description :  Get News Name Link For Id()  
     // Programmer : Yaroslav Gyryn
     // ================================================================================================
     function GetGallerysNameLinkForId($str = null) {
          $q = "SELECT 
                `".TblModGallery."`.id,
                `".TblModGallery."`.category as id_category,
                `".TblModGalleryTxt."`.translit as link,
                `".TblModGalleryTxt."`.name
            FROM 
                `".TblModGallery."`, `".TblModGalleryTxt."`
            WHERE 
                `".TblModGalleryTxt."`.cod = `".TblModGallery."`.id
            AND 
                `".TblModGalleryTxt."`.lang_id='".$this->lang_id."'
            AND
                `".TblModGallery."`.id in (".$str.")
            ";
            $res = $this->db->db_Query( $q );
            //echo "<br> ".$q." <br/> res = ".$res;
            $rows = $this->db->db_GetNumRows($res);
            if (empty($this->Category))  $this->Category = Singleton::getInstance('Category');

            $arrNews = array();
            for( $i=0; $i<$rows; $i++ ) {
                $row = $this->db->db_FetchAssoc($res);
                $id = $row['id'];
                if(!isset($arrNews[$id])) {
                    $arrNews[$id]['name'] = $row['name'];
                    $str_cat =  $this->Category->GetCategoryTranslitById($row['id_category']);
                    $arrNews[$id]['link'] = $this->Link($str_cat, $row['link']);
                }
            }
            return  $arrNews;
     }
                                    
} //--- end of class