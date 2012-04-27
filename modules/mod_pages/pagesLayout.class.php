<?php
/**
* pagesLayout.class.php
* class for display interface of Dynamic Front-end Pages
* @package Dynamic Pages Package of SEOCMS
* @author Igor Trokhymchuk  <ihor@seotm.com>
* @version 1.1, 05.08.2011
* @copyright (c) 2010+ by SEOTM
*/

include_once( SITE_PATH.'/modules/mod_pages/pages.defines.php' );

/**
* Class FrontendPages
* class for display interface of Dynamic Front-end Pages.
* @author Igor Trokhymchuk  <ihor@seotm.com>
* @version 1.1, 05.08.2011
* @property CatalogLayout $Catalog
* @property FrontSpr $Spr
* @property FrontForm $Form 
* @property db $db      
* @property UploadImage $UploadImages      
* @property UploadClass $UploadFile      
*/ 
class FrontendPages extends DynamicPages{
    public $page = NULL;
    public $module = NULL;
    public $is_tags = NULL;
    public $is_comments = NULL;
    public $main_page = NULL; // Главная страница сайта
    public $mod_rewrite = 1;
    public $Spr = NULL;
    public $Form = NULL;
    public $db = NULL;
    
    public $treePageList = NULL; //array $this->treePageList[]=$id_cat
    public $treePageLevels = NULL; //array $this->treePageLevels[level][id_cat]=''
    public $treePageData = NULL; //array treePageData[id_cat]=array with category data    
 
    /**
    * Class Constructor
    * 
    * @param $module - id of the module
    * @return true/false
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.1, 05.04.2011
    */      
    function __construct($module=NULL)
    {
        ( $module   !="" ? $this->module  = $module   : $this->module  = NULL );
        
        if(defined("_LANG_ID")) $this->lang_id = _LANG_ID;
        
        if(empty($this->db)) $this->db = DBs::getInstance();
        //if(empty($this->Spr)) $this->Spr = &check_init('FrontSpr', 'FrontSpr');
        if(empty($this->Form)) $this->Form = &check_init('FrontForm', 'FrontForm');
        
        $this->UploadImages = &check_init('UploadImage', 'UploadImage', "'90', 'null', 'uploads/images/pages', 'mod_page_file_img'");
        $this->UploadFile = &check_init('UploadClass', 'UploadClass', '90, null, "uploads/files/pages","mod_page_file"');
        //$this->UploadVideo = &check_init('UploadVideo', 'UploadVideo', '90, null, "uploads/video/pages","mod_page_file_video"');

        // for folders links
        if( !isset($this->mod_rewrite) OR empty($this->mod_rewrite) ) $this->mod_rewrite = 1;
        
        ( defined("USE_TAGS")                  ? $this->is_tags = USE_TAGS                     : $this->is_tags=0 ); // использовать тэги
        ( defined("USE_COMMENTS")              ? $this->is_comments = USE_COMMENTS             : $this->is_comments=0 ); // возможность оставлять комментарии
        ( defined("PAGES_USE_SHORT_DESCR")     ? $this->is_short_descr = PAGES_USE_SHORT_DESCR : $this->is_short_descr=0 ); // Краткое оисание страницы
        ( defined("PAGES_USE_SPECIAL_POS")     ? $this->is_special_pos = PAGES_USE_SPECIAL_POS : $this->is_special_pos=0 ); // специальное размещение страницы
        ( defined("PAGES_USE_IMAGE")           ? $this->is_image = PAGES_USE_IMAGE             : $this->is_image=0 ); // изображение к странице
        ( defined("PAGES_USE_IS_MAIN")         ? $this->is_main_page = PAGES_USE_IS_MAIN       : $this->is_main_page=0 ); // главная страница сайта
        
        if(empty ($this->multi)) $this->multi = &check_init_txt('TblFrontMulti', TblFrontMulti);
        
        $this->loadTree();
        //echo '<br />treePageList=';print_r($this->treePageList);
        //echo '<br />treePageLevels=';print_r($this->treePageLevels);
        //echo '<br />treePageData=';print_r($this->treePageData);         
         
     } // end of constructor FrontendPages()  
   
    /**
    * Class method loadTree
    * load all data of catalog categories to arrays
    * @return true/false or arrays:
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.1, 05.04.2011
    */
    function loadTree()
    {
        if( is_array($this->GetTreePageLevelAll()) AND is_array($this->GetTreePageDataAll()) ) return true;
         
        $q = "SELECT `".TblModPages."`.*, `".TblModPagesTxt."`.`pname` FROM `".TblModPages."`, `".TblModPagesTxt."` 
              WHERE  `".TblModPagesTxt."`.cod=`".TblModPages."`.id
              AND `".TblModPagesTxt."`.lang_id='".$this->lang_id."'
              ORDER BY `move` asc";        

        $res = $this->db->db_Query($q);
        //echo $q.' <br/>$res = '.$res.' $this->db->result='.$this->db->result;
        if(!$res OR !$this->db->result) return false;
        $rows = $this->db->db_GetNUmRows($res);   
        if($rows==0) 
            return false;
            
        $tree = array();

        for($i = 0; $i < $rows; $i++){
            $row = $this->db->db_FetchAssoc($res);
            
            // Установка главной страницы сайта
            if($row['main_page']==1)
                $this->main_page = $row['id'];

            if(empty($tree[$row['level']])) {
                $tree[$row['level']] = array();
            }
            $this->SetTreeCatLevel($row['level'], $row['id']);
            //$this->treePageLevels[$row['level']][$row['id']]='';
            $this->SettreePageData($row);
            //$this->treePageData[$row['id']]=$row;
        }
        //build category translit path for all categories and subcategories
        //exit();
        $this->makeCatPath();
        return true;
    } //end of function loadTree()
    
    function showSideBarContacts(){
	$q="SELECT * FROM `mod_spr_diff` WHERE `cod` IN ('5','6','3') AND `lang_id`='$this->lang_id' ORDER BY `cod` DESC";
	   $res=$this->db->db_Query($q);
	   if(!$res) return false;
	   $rows=$this->db->db_GetNumRows();
	    ?><div class="side-title foto-gallery-side-title">Контакты</div><div class="devider"></div><?
	    for($i = 0; $i < $rows; $i++)
	    {
		$row=$this->db->db_FetchAssoc();
		 if($row['id']==6){
			$title= $this->multi['FLD_ADR'];
			$ico='/images/ico/home.png';
		    }elseif($row['id']==5){	 
			$title= $this->multi['_TXT_TEL'];
			$ico='/images/ico/phone.png';
		    }elseif($row['id']==3){
			$ico='/images/ico/email.png';
			$title= $this->multi['TXT_ELECTR_MAIL'];
		    }
		?><div class="ico"><img src="<?=$ico?>" title="<?=$title?>" alt="<?=$title?>"/></div><?
		?><div class="side-bar-contacy-text">
		    <h2><?php
		   echo $title;
			?>:
		    </h2>
		    <?php 
		    if($row['id']==3)
			echo '<a href="mailto:'.strip_tags($row['descr']).'" title="'.strip_tags($row['descr']).'">'.strip_tags($row['descr'])."</a>";
		    else
			echo $row['descr'];?>
		</div><?
	    }
	
    }
    
    /**
    * Class method SetTreeCatLevel
    * set new vlaue to property $this->treePageLevels. It build array $this->treePageLevels[level][id_cat]='' 
    * @param integer $level - id of the parent category
    * @param integer $id - id of the category
    * @return none
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 17.05.2011
    */
    function SetTreeCatLevel($level, $id)
    {
        $this->treePageLevels[$level][$id]='';
    } //end of function SetTreeCatLevel()
    
    /**
    * Class method GetTreePageLevelAll
    * get array $this->treePageLevels
    * @return array $this->treePageLevels
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 17.05.2011
    */
    function GetTreePageLevelAll()
    {
        return $this->treePageLevels;
    } //end of function GetTreePageLevelAll()
    
    /**
    * Class method GetTreePageLevel
    * get node of array $this->treePageLevels where store array with sublevels
    * @param integer $item - id of the category as node in array
    * @return node of array $this->treePageLevels[$item]
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 17.05.2011
    */
    function GetTreePageLevel($item=0)
    {
        if(!isset($this->treePageLevels[$item])) return false;
        return $this->treePageLevels[$item];
    } //end of function GetTreePageLevel()  
    
    /**
    * Class method SettreePageData
    * set new vlaue to property $this->treePageData. It build array $this->treePageData[id_cat]=array with category data 
    * @param array $row - assoc array with data of category
    * @return true
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 17.05.2011
    */
    function SetTreePageData($row)
    {
        $this->treePageData[$row['id']]=$row;
        return true;
    } //end of function SettreePageData()

    /**
    * Class method SettreePageDataAddNew
    * set new vlaue to property $this->treePageData. It build array $this->treePageData[id_cat]=array with category data 
    * @param integer $id_cat - id of the category
    * @param varchar $key - name of new key
    * @param varchar $val - value for key $key 
    * @return true
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 17.05.2011
    */
    function SetTreePageDataAddNew($id_cat, $key, $val)
    {
        $this->treePageData[$id_cat][$key]=$val;
        return true;
    } //end of function SettreePageDataAddNew()

    
    /**
    * Class method GettreePageDataAll
    * get array $this->treePageData
    * @return array $this->treePageData
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 17.05.2011
    */
    function GetTreePageDataAll()
    {
        return $this->treePageData;
    } //end of function GettreePageDataAll()  
    
    /**
    * Class method GettreePageData
    * get node of array $this->treePageData where store array with data about category
    * @param integer $item - id of the category as node in array
    * @return node of array $this->treePageData[$item]
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 17.05.2011
    */
    function GettreePageData($item)
    {
        if(!isset($this->treePageData[$item])) return false;
        return $this->treePageData[$item];
    } //end of function GettreePageData()  
    
    /**
    * Class method SettreePageList
    * set new vlaue to property $this->treePageList. It build array $this->treePageList[counter]=id of the category 
    * @param integer $counter - counter for array
    * @param integer $id_cat - id of the category
    * @return true
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 17.05.2011
    */
    function SettreePageList($counter, $id_cat)
    {
        $this->treePageList[$counter] = $id_cat;
        return true;
    } //end of function SettreePageList() 
    
    /**
    * Class method GetTreeListAll
    * get array $this->treePageList
    * @return array $this->treePageList
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 17.05.2011
    */
    function GetTreeListAll()
    {
        return $this->treePageList;
    } //end of function GetTreeListAll()                     
    
    /**
    * Class method makeCatPath
    * build relative url to category using category translit for all categories and subcategories 
    * @param integer $level - id of the category
    * @param string $path - path to category
    * @return true/false
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 14.01.2011
    */
    function makeCatPath($level = 0, $path = NULL)
    {
        if( !$this->GetTreePageLevel($level) ) return;
        $n = count($this->GetTreePageLevel($level));
        $keys = array_keys($this->GetTreePageLevel($level));
        for($i = 0; $i < $n; $i++) {
            //echo '<br />$keys[$i]='.$keys[$i];
            $row = $this->GettreePageData($keys[$i]);
            //if(!$path) $full_path = '/'.$row['name'];
            //else $full_path = $path.$row['name'];
            if($row['ctrlscript']==1) $full_path = $path.$row['name'];
            else $full_path = $row['name'];
            //$this->treePageData[$keys[$i]]['path'] = $full_path;
            $this->SettreePageDataAddNew($keys[$i], 'path', $full_path);
            //$this->treePageList[]=$row['id'];
            $this->SettreePageList($i, $row['id']);
            $this->makeCatPath($row['id'], $full_path);
        }
    }//end of function makeCatPath()
    
    /**
    * Class method isPageASubcatOfLevel
    * Checking if the page $id_page is a subcategory of $item at any dept start from $arr[$item]  
    * @param integer $id_page - id of the page
    * @param integer $item - as index for array $arr
    * @return array with index as counter
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.1, 05.04.2012
    */
    function isPageASubcatOfLevel($id_page, $item)
    {
       if($id_page==$item) return true;
       $a_tree = $this->GetTreePageLevel($item);
       if( !$a_tree ) return false;
       $keys = array_keys($a_tree);
       $rows = count($keys);
       if(array_key_exists($id_page, $a_tree)) return true;
       for ($i=0;$i<$rows;$i++) {
            $id = $keys[$i];
            //echo '<br />$id='.$id;
            if( $this->GetTreePageLevel($id) AND is_array($this->GetTreePageLevel($id)) ) {
                $res = $this->isCatASubcatOfLevel($id_page, $id);
                if($res) return true;
            }
        }
        return false;
    } // end of function isPageASubcatOfLevel()

    /**
    * Class method isSubLevels()
    * Checking exist or not sublevels for page $id_page
    * @param integer $id_page - id of the page
    * @return true or false
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.1, 05.04.2012
    */
    function isSubLevels($id_page)
    {
       if( !$this->GetTreePageLevel($id_page) ) return false;
       return true;
    } // end of function isSubLevels()
    
    /**
    * Class method getSubLevels
    * return string with sublevels for page $id_page
    * @param integer $id_page - id of the page
    * @return sting with id of categories like (1,13,15,164? 222)
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.1, 05.04.2012
    */
    function getSubLevels( $id_page )
    {
       if( !$this->GetTreePageLevel($id_page) ) return false;
       $a_tree = $this->GetTreePageLevel($id_page);     
       $keys = array_keys($a_tree);
       $rows = count($keys);
       for ($i=0;$i<$rows;$i++) {
            $id = $keys[$i];
            //echo '<br />$id='.$id;
            if( empty($arr_row)) $arr_row = $id;
            else $arr_row = $arr_row.','.$id;
            if(  $this->GetTreePageLevel($id) AND is_array($this->GetTreePageLevel($id)) ) {
                $arr_row .= ','.$this->getSubLevels($id);
            }
        }
        return $arr_row;
    } // end of function getSubLevels()         
    
    /**
    * Class method getTopLevel
    * get the top level of pages for page $id_page
    * @param integer $id_page - id of the page  
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 05.04.2012
    */
    function getTopLevel($id_page)
    {
        $cat_data = $this->GetTreePageData($id_page);
        if(!$cat_data) return false;
        if($cat_data['level']==0) return $id_cat;
        return $this->getTopLevel($cat_data['level']);
    } // end of function getTopLevel() 
    

    /**
    * Class method getUrlByTranslit
    * build reletive URL link to page $id_page
    * @param string $translit - string with tranlsit to the page
    * @return string $link with reletive URL link to page $id_page
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 05.04.2012
    */
    function getUrlByTranslit($translit)
    {      
        if( !defined("_LINK")) {
            $Lang = &check_init('SysLang', 'SysLang', 'NULL, "front"');
            if( _LANG_ID!=$Lang->GetDefFrontLangID() ) define("_LINK", "/".$Lang->GetLangShortName(_LANG_ID)."/");
            else define("_LINK", "/");
        }
        
        $link = _LINK.$translit;
        return $link;
    } //end of function getUrlByTranslit()  

    /**
    * Class method Link
    * build reletive|absolute URL link to page $id
    * @param integer $id - id of the page
    * @param boolean $add_domen_name If true then add domen name before page url (like http://www.seotm.com/news/)
    * @param string $lang id of the lang for build link
    * @return string $link - link to page
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 12.04.2012
    */
    function Link($id, $add_domen_name=true, $lang = NULL)
    {
        $link=NULL;
        if( !empty($lang) ){
            //$Lang = new SysLang(NULL, "front");
            $Lang = &check_init('SysLang', 'SysLang', 'NULL, "front"');
            $tmp_lang = $Lang->GetDefFrontLangID();
            if( ($Lang->GetCountLang('front')>1 OR isset($_GET['lang_st'])) AND $lang!=$tmp_lang) $lang_prefix =  "/".$Lang->GetLangShortName($lang)."/";
            else $lang_prefix = "/"; 
        }
        else{
            if( !defined("_LINK")){
                //define("_LINK", "/");
                //$Lang = new SysLang(NULL, "front");
                $Lang = &check_init('SysLang', 'SysLang', 'NULL, "front"');
                $tmp_lang = $Lang->GetDefFrontLangID();
                if( ($Lang->GetCountLang('front')>1 OR isset($_GET['lang_st'])) AND _LANG_ID!=$tmp_lang) {
                    define("_LINK", "/".$Lang->GetLangShortName(_LANG_ID)."/");
                    $lang_prefix =  "/".$Lang->GetLangShortName(_LANG_ID)."/";
                }
                else {
                    define("_LINK", "/");
                    $lang_prefix = "/";
                }
            }
            else $lang_prefix = _LINK;
        }
        
        
        // echo '<br>$this->mod_rewrite='.$this->mod_rewrite;
        if($this->mod_rewrite==1){
           //$link = $this->GetNameById($id);
           $link = $this->treePageData[$id]['path'];
           //echo '<br>$link='.$link;
           
           if( !empty($link)){
               //echo '<br>_LINK='._LINK.' strlen(_LINK)='.strlen(_LINK);
               if( strlen($lang_prefix)>1 AND $this->treePageData[$id]['ctrlscript']==1 ){
                   if($add_domen_name) $link = 'http://'.$_SERVER['SERVER_NAME'].$lang_prefix.$link;
                   else $link = $lang_prefix.$link;
               }
               else {
                   //if page is not dynamic page and this is not link to the page of other site then show path to this site
                   if( !strstr($link, "http://") ){
                       $pos = strpos($link, '/');
                       if($pos==0) $link = substr($link, 1);
                       if($add_domen_name) $link = 'http://'.$_SERVER['SERVER_NAME'].$lang_prefix.$link;
                       else $link = $lang_prefix.$link;
                   }
                   else{
                       if( $this->is_main_page){
                           if( $this->main_page==$id ) $link=$link.$lang_prefix;
                       }
                   }
               }
           }
           else{
               //$link = $this->SetPath($id);
           }
           $link = $this->PrepareLink($link);
        }
        if( empty($link) ){
            if($this->main_page==$id) $link=$lang_prefix;
            else $link = $lang_prefix."index.php?page=".$id;
        }
        //echo '<br>$link='.$link;
        return $link;
    } //end of function Link()

    /**
    * Class method ShowPath
    * eturn path of names to the page
    * @param string $id_page - id of the page
    * @param string $path string with path for recursive execute
    * @param boolean $make_link - make link for last page in path or not
    * @return string path of names to the page
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 05.04.2012
    */     
    function ShowPath($id_page, $path=NULL, $make_link = false )
    {
        $res = NULL;
        $devider = '→';
        if($id_page>0){
            $row = $this->treePageData[$id_page];
            $name = stripslashes($row['pname']);
            $link = $this->Link($row['id']);
            
            if( !empty($path) ){
                $path = '<a href="'.$link.'">'.$name.'</a> '.$devider.' '.$path;
            }
            else{
                if( $make_link==1 ) {
                    $path = '<a href="'.$link.'">'.$name.'</a>';
                }
                else $path = $name;
                
            }
            if( $row['level']>0 ){
                $path = $this->ShowPath($row['level'], $path, $make_link);
            }
            else $path = '<a href="'._LINK.'">'.$this->multi['TXT_FRONT_HOME_PAGE'].'</a> '.$devider.' <span class="spanShareName">'.$path."</span>";
        }
        else{
            $path = '<a href="'._LINK.'">'.$this->multi['TXT_FRONT_HOME_PAGE'].'</a> '.$devider.' ';
        }
        return $path;
    }//end of function ShowPath()

    
    /**
     * FrontendPages::ShowHorizontalMenu()
     *
     * @author Yaroslav Gyryn 13.03.2012 
     * @param integer $level
     * @return void
     */
    /*
    function ShowHorizontalMenu($level = 0)
    {
        if(!isset($this->treePageLevels[$level])) return false;
        $keys = array_keys($this->treePageLevels[$level]);
        $rows = count($keys);
        $this->topLevel = $this->treePageData[$this->page]['level']; //$this->GetLevel($this->page);
        ?>
        <div class="menu">
            <ul class="main-menu">
                <?
                $j = 0;
                for( $i = 0; $i < $rows; $i++ )
                {
                    $row = $this->treePageData[$keys[$i]];
                    if($row['visible']==0 OR empty($row['pname']) ) continue;
                    if ($this->main_page == $row['id']) 
                        $href=_LINK;
                    else 
                        $href = $this->Link($row['id']);
                    
                    if($this->page == $row['id'] OR $row['id']==$this->topLevel)
                        $classli = 'Active';
                    else
                        $classli = '';
                    ?>
                    
                    <li class="<?=$classli;?>">
                        <a href="<?=$href;?>"><?=stripslashes($row['pname']);?></a>
                        <?
                        if($this->isSubLevels($row['id'])){
                            //$this->ShowSubLevelsInList($row['id'], true);
                        }
                        ?>
                    </li>
                    
                    <?
                }// end for
                ?>
            </ul>
        </div>
        <?
    }//end of function ShowHorizontalMenu()
    
     * 
     */
     /**
     * FrontendPages::ShowSubLevelsInList()
     * Show sublevels of the page $level
     * @author Yaroslav Gyryn 13.03.2012
     * @param mixed $key
     * @param bool $list
     * @return void
     */
    function ShowSubLevelsInList($key,$list=true)
    {
        if(!isset($this->treePageLevels[$key])) return false;
        $keys = array_keys($this->treePageLevels[$key]);
        $cnt = count($keys);
        if($cnt>0){
            if($list){
                ?><div class="subNav" id="subNav<?=$key?>" style="_display:none; _width:150px;"><ul><?
                for($i=0; $i<$cnt; $i++)
                {
                    $row = $this->treePageData[$keys[$i]];
                    if($row['visible']==0 OR empty($row['pname']) ) continue;
                    $href = $this->Link($row['id']);
                    ?><li><a href="<?=$href;?>"><?=stripslashes($row['pname']);?></a><?
                    /*if (isset($menu_array[$menu_array[$key][$i]['id']]))
                    $this->ShowSubLevelsInList($menu_array[$key][$i]['id'], $menu_array[$key][$i]['pname'], $menu_array, $links);?></li><?*/
                }
                ?></ul></div><?
            }
        }
    }// end of function ShowSubLevelsInList()

    
    
    /**
     * FrontendPages::ShowHorizontalMenu()
     * 
     * @param integer $level
     * @param integer $cnt_sublevels
     * @param integer $cnt
     * @return
     */
    function ShowHorizontalMenu($level=0, $cnt_sublevels=10, $cnt=0)
    {
        if(!isset($this->treePageLevels[$level])) return false;
        $rows=count($this->treePageLevels[$level]);
        $keys=array_keys($this->treePageLevels[$level]);
        if($rows==0) return false;
        if($level==0){
            ?><ul class="horizontalMenu"><?
        }
        else{
            ?><ul><?
        }
        $hidden=0;
	$arr=array();
	for($i = 0; $i < $rows; $i++)
	{
	    $row = $this->treePageData[$keys[$i]];
	    if($row['visible']==0 or empty($row['pname']) or $row['publish']==0){
                continue;
	    }
	    $arr[]=$row;
	}
	$count=count($arr);
        for($i=0;$i<$count;$i++){
           // $row = $arr_data[$i];
            $row = $arr[$i];
            
            $href = $this->Link($row['id']);
            $s="";
            if($this->page==$row['id']) $s="selected-menu-punkt";
            $name = stripslashes($row['pname']);
            //echo '<br>$name='.$name.' $row[id]='.$row['id'];
            ?>
            <li>
                <a href="<?=$href;?>" class="<?=$s?>"><?=$name;?></a>
                <?
                //echo '<br>$cnt_sublevels='.$cnt_sublevels.' $cnt='.$cnt;
                if($this->IsSubLevels($row['id'], 'front')){
                    $this->ShowHorizontalMenu($row['id'], $cnt_sublevels, $cnt);
                }
            ?></li>
		    <?
		    
	    if($i!=$count-1):
		    ?> <li class="menu-devider"></li>
			    <?
	    endif;
        }
        ?></ul><?
    }// end of function ShowHorizontalMenu()

    
    
    /**
    *  FrontendPages::ShowVerticalMenu()
    * @return true,false / Void
    * @author Yaroslav Gyryn 12.04.2012
    */
    function ShowVerticalMenu($level=0, $cnt_sublevels=10, $cnt=0)
    {
        if(!isset($this->treePageLevels[$level])) return false;
        $rows=count($this->treePageLevels[$level]);
        $keys=array_keys($this->treePageLevels[$level]);
        if($rows==0) return false;
        ?><ul><?
        for($i=0;$i<$rows;$i++){
            $row = $this->treePageData[$keys[$i]];
            if($row['visible']==0 or empty($row['pname'])) continue;
            if ($this->main_page==$row['id']) $href=_LINK;
            else $href = $this->Link($row['id']);
            $s="";
            if($this->page==$row['id']){$s="current";}
            $name = stripslashes($row['pname']);
            ?><li><a href="<?=$href;?>" class="<?=$s;?>"><?=$name;?></a></li><?
            if($this->IsSubLevels($row['id'], 'front')){
                $cnt=$cnt+1;
                if($cnt<$cnt_sublevels){
                    $this->ShowVerticalMenu($row['id'], $cnt_sublevels, $cnt);
                    $cnt=0;
                }
            }
        }
        ?></ul><?
    }// end of function ShowVerticalMenu()

    
    
    /**
     * FrontendPages::ShowFooterMenu()
     * @author Yaroslav Gyryn 21.10.2011  
     * @return void
     */
    function ShowFooterMenu($level = 0)
    {
        if(!isset($this->treePageLevels[$level])) return false;
        $rows=count($this->treePageLevels[$level]);
        $keys=array_keys($this->treePageLevels[$level]);
        if($rows==0) return false;
        ?>
        <div id="footerNavBox">
            <ul>
                <?
                for( $i = 0; $i < $rows; $i++ )
                {
                    $row = $this->treePageData[$keys[$i]];
                    if($row['visible']==0 OR empty($row['pname']) ) continue;
                    if ($this->main_page == $row['id'])
                        $href=_LINK;
                    else 
                        $href = $this->Link($row['id']);
                    ?>
                    <li><a <?
                    if($this->page == $row['id']) 
                    {
                        echo ' class="current"';
                    }
                    ?> href="<?=$href;?>"><?=stripslashes($row['pname']);?></a>
                    </li>
                    <?
                }// end for
                ?>
            </ul>
        </div>
        <?
    }//end of function ShowFooterMenu()    

     function headerPicturesOthers(){
//        echo $_SERVER["REQUEST_URI"];
        $q="SELECT * 
            FROM `mod_catalog_spr_slider_main` 
            WHERE 
            `lang_id`='$this->lang_id' 
             ORDER BY `id`   ";
        $res=$this->db->db_Query($q); 
	$rows=$this->db->db_GetNumRows();
         
//        if(!$res || $rows==0){
//            $q="SELECT * FROM `mod_catalog_spr_slider_main` WHERE `lang_id`='$this->lang_id' AND `cod`='1' ORDER BY `id`";
//            $res=$this->db->db_Query($q);
//            $rows=$this->db->db_GetNumRows();
////            $row=$this->db->db_FetchAssoc();
//        }
        ?>
        <ul  id="caruselOtherPages"><?
        for ($i = 0; $i < $rows; $i++) {
            $row=$this->db->db_FetchAssoc();

            $img="/images/spr/mod_catalog_spr_slider_main/".$this->lang_id."/".$row['img'];
//            $img=$this->Catalog->ShowCurrentImageExSize($img, 960, 349, true, true, 86, NULL, NULL, "alt='".$row['name']."'",true);
            ?>
            <li>
            <img class="big-photo-pages" src="<?=$img?>">
            </li>
                <?
        }
        ?></ul>
            <div class="clearfix"></div>
	    <div class="pagination" id="mainPaginationid"></div>
        <script type="text/javascript">
            $("#caruselOtherPages").carouFredSel({
            auto    : {
			duration        : 800,
			fx          : "fade",
			pauseDuration   : 4500

	    },
	    items:{
		    visible:1
	    },
            pagination  : {
		container   : "#mainPaginationid",
		duration    : 800,
		fx          : "fade"
	    }
            });
        </script>    
            
            <?
        
    }

    /**
    * Class method ShowContent
    * show content of the dynamic page
    * @return content of the page
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 12.04.2012
    */
    function ShowContent($sprecialClass=NULL)
    {
        $name = stripslashes($this->page_txt['pname']);
        if($this->page!=$this->main_page) $this->Form->WriteContentHeader($name, false,false,$sprecialClass);
        else $this->Form->WriteContentHeader($this->page_txt['short'], false,false);
        
         if( !$this->IsPublish($this->page) AND !$this->preview ){
            echo $this->multi['_MSG_CONTENT_NOT_PUBLISH'];
         }
         else{
            $body = stripslashes($this->page_txt['content']);
            if(empty($body)){
                if($this->ShowSubLevelsInContent($this->page)==false)
                echo $this->multi['_MSG_CONTENT_EMPTY'];
            }
            else{
                echo $body;
                $this->ShowSubLevelsInContent($this->page);
            }
         }
//         $this->ShowUploadFileList($this->page);
         $this->ShowUploadImagesList($this->page);
            
        
         /*?>
         <!-- AddThis Button BEGIN -->
         <div class="addthis_toolbox addthis_default_style">
            <a href="http://addthis.com/bookmark.php?v=250&amp;username=xa-4c559bfc5d7d23e8" class="addthis_button_compact">Share</a>
            <span class="addthis_separator">|</span>
            <a class="addthis_button_facebook"></a>
            <a class="addthis_button_myspace"></a>
            <a class="addthis_button_google"></a>
            <a class="addthis_button_twitter"></a>
         </div>
         <script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#username=xa-4c559bfc5d7d23e8"></script>
         <!-- AddThis Button END -->
         </div>
         <?*/         
         $this->Form->WriteContentFooter();
    }// end of function ShowContent

    /**
    * Class method ShowSubLevelsInContent
    * show sublevels of the page $level in content part
    * @param integer $level - id of the page
    * @return sublevels of this page
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 12.04.2012
    */    
    function ShowSubLevelsInContent($level)
    {
        if(!isset($this->treePageLevels[$level])) return false;
        $rows=count($this->treePageLevels[$level]);
        $keys=array_keys($this->treePageLevels[$level]);
        if($rows==0) return false;
        ?>
        <ul>
            <?
            for($i=0; $i<$rows; $i++){
                $row = $this->treePageData[$keys[$i]];
                if($row['visible']==0 OR empty($row['pname']) ) continue;
                $href = $this->Link($row['id']);
                ?><li>&nbsp;<a href="<?=$href;?>" class="sub_levels"><?=stripslashes($row['pname']);?></a>&nbsp;</li><?
            }
            ?>
        </ul>
        <?
    }// end of function ShowSubLevelsInContent()

     /**
     * FrontendPages::MAP()
     * Show map of dynamic pages
     * @author Yaroslav
     * @param integer $level
     * @return
     */
    function MAP($level=0)
    {
        if(!isset($this->treePageLevels[$level])) 
            return false;
        $rows=count($this->treePageLevels[$level]);
        if($rows==0) 
            return false;
        $keys=array_keys($this->treePageLevels[$level]);
        ?><ul><?
        for($i=0;$i<$rows;$i++){
            $row = $this->treePageData[$keys[$i]];
            $id = $row['id'];
            $name = $row['pname']; 
            if ($this->MainPage() == $id ) 
                $href="/";
            else 
                $href = $this->Link($id);
            
            ?><li><a href="<?=$href;?>"><?=$name;?></a></li><? 
            $this->MAP($id);
            
            if($id == PAGE_NEWS)   { //News
                $News = &check_init('NewsLayout', 'NewsLayout');
                $News->GetMap();
            }
            
            if($id == PAGE_ARTICLE)   { //Articles
                $Article = &check_init('ArticleLayout', 'ArticleLayout');
                $Article->GetMap();
            }
            
            if($id ==PAGE_CATALOG)   { //Catalog
                if(!isset($this->Catalog)) $this->Catalog = &check_init('CatalogLayout', 'CatalogLayout');
                $this->Catalog->MAP();
            }

            if($id == PAGE_GALLERY)   { //Gallery
                $Gallery = &check_init('GalleryLayout', 'GalleryLayout');
                $Gallery->GetMap();
            }
            
            if($id == PAGE_VIDEO)   { //Video
                $Video = &check_init('VideoLayout', 'VideoLayout');
                $Video->GetMap();
            }
            
            if($id == PAGE_DICTIONARY)   { //Dictionary
                if(!isset($this->Dictionary)) $this->Dictionary  = &check_init('Dictionary', 'Dictionary');
                $this->Dictionary->MAP();
            }
            
            if($id ==PAGE_COMMENT ) { //Комментарий     
                if(!isset($this->Comments))  $this->Comments = &check_init('CommentsLayout', 'CommentsLayout');
                $this->Comments->GetMap();
            }
        } //end for
        ?></ul><?
    }// end of function MAP()
    
   
    // ================================================================================================
    // Function : GetTitle()
    // Date : 18.08.2006
    // Returns : true,false / Void
    // Description :  return titleiption of the page 
    // Programmer : Ihor Trohymchuk 
    // ================================================================================================
    function GetTitle()
    {
        if(empty($this->page_txt['mtitle'])) return stripslashes($this->page_txt['pname']);
        else return stripslashes($this->page_txt['mtitle']);
    } //end of function GetTitle()


    // ================================================================================================
    // Function : GetDescription()
    // Date : 18.08.2006
    // Returns : true,false / Void
    // Description :  return description of the page 
    // Programmer : Ihor Trohymchuk 
    // ================================================================================================
    function GetDescription()
    {
        return stripslashes($this->page_txt['mdescr']);
    } //end of function GetDescription()

    // ================================================================================================
    // Function : GetKeywords()
    // Date : 18.08.2006
    // Returns : true,false / Void
    // Description :  return kyewords of the page
    // Programmer : Ihor Trohymchuk 
    // ================================================================================================
    function GetKeywords()
    {
        return stripslashes($this->page_txt['mkeywords']);
    } //end of function GetKeywords()



    
     // ================================================================================================
     // Function : ShowPagesSpecialPos()
     // Date : 10.10.2008
     // Returns : true,false / Void
     // Description : show pages in special position
     // Programmer : Ihor Trokhymchuk
     // ================================================================================================
    /*function ShowPagesSpecialPos()
    {
        $db = DBs::getInstance();
        $q = "SELECT * FROM `".TblModPages."` WHERE `special_pos`='1' ORDER BY `move`";
        $res = $db->db_Query($q);
        //echo '<br>$q='.$q.' $res='.$res;
        $rows = $db->db_GetNumRows($res);
        if(!$rows) return false;
        //echo '<br>$rows='.$rows;
        ?>
        <table border="0" cellpadding="1" cellspacing="0" align="center">
         <tr>
         <?
         for($i=0; $i<$rows; $i++)
         {
            $row = $db->db_FetchAssoc();
            $href = $this->Link($row['id']);
            ?><td>&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?=$href;?>" class="special"><?=trim($this->Spr->GetNameByCod( TblModPagesSprName, $row['id'], $this->lang_id ));?></a>&nbsp;&nbsp;&nbsp;&nbsp;</td><?
         }
         ?>
         </tr>
        </table>
        <?
    }// end of function ShowPagesSpecialPos()*/
    
    // ================================================================================================
    // Function : ShowSearchRes()
    // Date : 31.03.2008
    // Returns : true,false / Void
    // Description : Show Add form on fontend
    // Programmer : Ihor Trohymchuk 
    // ================================================================================================     
    function ShowSearchRes($arr_res)
    {
        $rows = count($arr_res);
        if($rows>0){
           ?><ul><?
           for($i=0;$i<$rows;$i++){
               $row = $arr_res[$i];
               ?> 
               <li><a href="<?=$this->Link($row['id']);?>" class="map"><?=stripslashes($row['pname']);?></a></li>
               <?        
           }
           ?></ul><?
        }
        else{
            echo $this->multi['SEARCH_NO_RES'];
        }
    } // end of function ShowSearchRes()


    // ================================================================================================
    // Function : ShowSearchResHead()
    // Date : 31.03.2008
    // Returns : true,false / Void
    // Description : Show Add form on fontend
    // Programmer : Ihor Trohymchuk
    // ================================================================================================       
    function ShowSearchResHead($str)
    {
        ?>
        <div><?=$str;?></div>
        <?
    } // end of function ShowSearchResHead()
    
    
        // ================================================================================================
    // Function : UploadFileList()
    // Date : 30.05.2010
    // Parms : $pageId - id of the page
    // Returns : true,false / Void
    // Description : Show list of files attached to page with $pageId
    // Programmer : Yaroslav Gyryn
    // ================================================================================================         
    function ShowUploadFileList($pageId)
    {
        $array = $this->UploadFile->GetListOfFilesFrontend($pageId, $this->lang_id);
        if(count($array)>0) {
         ?><div class="leftBlockHead"><?=$this->multi['_TXT_FILES_TO_PAGE']?>:</div><?   
         $this->UploadFile->ShowListOfFilesFrontend($array, $pageId );
         }
    }


    // ================================================================================================
    // Function : DownloadCatalog()
    // Date : 30.05.2010
    // Parms : $pageId - id of the page
    // Returns : true,false / Void
    // Description : 
    // Programmer : Yaroslav Gyryn
    // ================================================================================================         
    function DownloadCatalog($pageId)
    {
        $array = $this->UploadFile->GetListOfFilesFrontend($pageId, $this->lang_id);
        if(count($array)>0) {
         $this->UploadFile->DownloadCatalogFrontend($array, $pageId );
         }
    }
        
    // ================================================================================================
    // Function : ShowUploadImageList()
    // Date : 30.05.2010
    // Parms : $pageId - id of the page
    // Returns : true,false / Void
    // Description : Show Upload Images List
    // Programmer : Yaroslav Gyryn
    // ================================================================================================         
    function ShowUploadImagesList($pageId)
    {
        $items = $this->UploadImages->GetPictureInArrayExSize($pageId, $this->lang_id,NULL,175,135,true,true,85);
        $items_keys = array_keys($items);
        $items_count = count($items);
        if($items_count>0) {
        ?><div class="leftBlockHead"><?= $this->multi['SYS_IMAGE_GALLERY'];?></div>
            <div class="imageBlock " align="center">
                <ul id="carouselLeft" class="vhidden jcarousel-skin-menu"><?
                for($j=0; $j<$items_count; $j++){   
                    $alt= $items[$items_keys[$j]]['name'][$this->lang_id];  // Заголовок
                    $title= $items[$items_keys[$j]]['text'][$this->lang_id]; // Описание 
                    $path = $items[$items_keys[$j]]['path'];                 // Путь уменьшенной копии
                    $path_org = $items[$items_keys[$j]]['path_original'];    // Путь оригинального изображения
                    ?><li>                            
                            <a href="<?=$path_org;?>" class="highslide" onclick="return hs.expand(this);">
                                <img src="<?=$path;?>" alt="<?=$alt?>" title="<?=$title;?>"/>
                             </a>
                             <div class="highslide-caption"><?=$title;?></div>
                     </li><?                
                }
                ?></ul>
            </div><?
         }        
        //$this->UploadImages->ShowMainPicture($pageId,$this->lang_id,'size_width=175 ', 85 ) ;
    }

    function showSideBarSertificats($pageId=91){
//	$items = $this->UploadImages->GetPictureInArrayExSize($pageId, $this->lang_id,3,175,135,true,true,85);
	$items = $this->UploadImages->GetPictureInArray($pageId, $this->lang_id, "size_height=170", 85, 3, NULL);
        $items_keys = array_keys($items);
        $items_count = count($items);
        if($items_count==0) return false;
        ?>
	<div class="side-title sertificats-side-title"><?=$this->multi['TXT_SIDEBAR_TITLE_SERTIFICATS'];?></div>
		<div class="devider"></div>
<!--		<div class="sertificats-sidebar">-->
            <?
                for($j=0; $j<$items_count; $j++){   
                    $alt= $items[$items_keys[$j]]['name'][$this->lang_id];  // Заголовок
                    $title= $items[$items_keys[$j]]['text'][$this->lang_id]; // Описание 
                    $path = $items[$items_keys[$j]]['path'];                 // Путь уменьшенной копии
                    $path_org = $items[$items_keys[$j]]['path_original'];    // Путь оригинального изображения
                    ?>                            
			<img class="sidebar-sertificats sidebar-sertificats<?=$j?>" src="<?=$path;?>" alt="<?=$alt?>" title="<?=$title;?>"/>
                    <?                
                }
		?>
<!--		</div>-->
		<a href="/sertifikati/" class="btn all-sertificats-btn" title="<?=$this->multi['TXT_WATCH_ALL']?>">
		    <?=$this->multi['TXT_WATCH_ALL']?>
		</a>    
		<?
               
    }
    
    // ================================================================================================
    // Function : ShowRandomImage()
    // Date : 30.09.2010
    // Parms : $pageId - id of the page
    // Returns: void
    // Description :  Show Random Image
    // Programmer : Yaroslav Gyryn
    // ================================================================================================         
    function ShowRandomImage($pageId)
    {
        $page_txt = $this->GetPageData($pageId, $lang_id=NULL); 
        $name = stripslashes($page_txt['pname']);
        
       ?>
       <div class="leftMenuHead">
            <h3><?=$name?></h3>
       </div>
         <div class="imageBlock">
            <?
            $link = $this->Link($pageId);
            $items = $this->UploadImages->GetFirstRandomPicture($pageId, $this->lang_id, 'size_width= 232', null);
            $items_keys = array_keys($items);
            $items_count = count($items);
            if($items_count>0) {
                    /*$alt= $items[$items_keys]['name'][$this->lang_id];  // Заголовок
                    $title= $items[$items_keys]['text'][$this->lang_id];  // Описание */
                    $path = $items[$items_keys[0]]['path'];                    // Путь уменьшенной копии
                    //$path_org = $items[$items_keys['path_original'];   // Путь оригинального изображения
                    ?><a href="<?=$link;?>" title="<?=$name?>"><img src="<?=$path;?>" alt="<?=$name?>" title="<?=$name?>"/></a><?
            }                        
            /*?>
            <a href="<?=$link?>" title="<?=$this->multi['TXT_GALLERY_TITLE'];?>"><img src="/images/design/videoSmall.jpg"></a>*/?>
         </div>
         <?
       }
       
}// end of class FrontendPages
?>