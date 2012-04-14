<?php
// ================================================================================================
// System : PrCSM05
// Module : SysLang.class.php
// Version : 2.0.0
// Date : 11.01.2006
// Licensed To:
// Igor Trokhymchuk ihoru@mail.ru
// Andriy Lykhodid las_zt@mail.ru
//
// Purpose : Class definition for all actions with languages
//
// ================================================================================================

// ================================================================================================
//
//    Programmer        :  Igor Trokhymchuk
//    Date              :  09.02.2005
//    Reason for change :  Creation
//    Change Request Nbr:
//
//    Function          :  Class definition for all actions with languages
//
//  ================================================================================================

include_once( SITE_PATH.'/admin/include/defines.inc.php' );
include_once( SITE_PATH.'/admin/modules/sys_lang/syslang.defines.php' );

// ================================================================================================
//    Class             : SysLang
//    Version           : 1.0.0
//    Date              : 09.02.2005
//
//    Constructor       : Yes
//    Parms             : session_id / session id
//                        usre_id    / UserID
//                        user_      /
//                        user_type  / id of group of user
//    Returns           : None
//    Description       : Class definition for all actions with languages
// ================================================================================================
//    Programmer        :  Igor Trokhymchuk
//    Date              :  27.01.2005
//    Reason for change :  Creation
//    Change Request Nbr:  N/A
// ================================================================================================
 class SysLang {

       var $lang_id = NULL;

       var $msg = NULL;
       var $Rights = NULL;
       var $Form = NULL;
       var $Spr = NULL;
       var $LangBackend = NULL;
       static $LangArray = NULL;

       // ================================================================================================
       //    Function          : SysLang (Constructor)
       //    Version           : 1.0.0
       //    Date              : 31.01.2005
       //    Parms             : usre_id   / User ID
       //                        module    / module ID
       //                        sort      / field by whith data will be sorted
       //                        display   / count of records for show
       //                        start     / first records for show
       //                        width     / width of the table in with all data show
       //                        spr       / name of the table for this module
       //    Returns           : Error Indicator
       //
       //    Description       : Opens and selects a dabase
       // ================================================================================================
       function SysLang( $lang_id = NULL, $back_front='back'  ) {
             //Check if Constants are overrulled
            if( empty($this->Rights ) ) $this->Rights = &check_init('Rights','Rights');
            //echo '<br>$this->Rights='.$this->Rights;
            
            //Если на вход не передавался $lang_id, то устававливем его
            if( empty( $lang_id ) ) {
                //Если есть определена глобльная константа _LANG_ID, то устанавливаем язык из этой константы,
                //если же константа не определна, то пытаемся установить текущий язык из других источников
                if(defined( "_LANG_ID")) $this->lang_id = _LANG_ID;
                else{
                    //Если язык передается в $_GET['lang_pg'], то утанваливаем его, если же нет, то пытаемся установить из значения сессии $_SESSION["lang_pg"]
                    if( isset( $_GET['lang_pg'] ) ){$this->lang_id = intval($_GET['lang_pg']);}
                    elseif( isset($_SESSION["lang_pg"]) ){$this->lang_id = $_SESSION["lang_pg"];}
                    //Если же нигде нет данных языка, то последняя инстанция это достать язык из базы даннх, кототрый установлен по-умолчанию
                    else{
                        $q = "select * from `".TblModSysLang."` WHERE 1";
                        if($back_front=='back') {$q = $q." and `def_back`='1'";}
                        if($back_front=='front') {$q = $q." and `def_front`='1'";}
                        $q = $q." order by `cod`";
                        $res = $this->Rights->db_Query($q);
                        $row = $this->Rights->db_FetchAssoc();
                        $this->lang_id = $row['cod'];
                    }
                }
            }
            else $this->lang_id = $lang_id;
            
            //Если по каким-либо причинам не удалось установить язык, то устанавливаем из глобаной константы DEBUG_LANG
            if( empty($this->lang_id) ) $this->lang_id = DEBUG_LANG; 
            
            if( empty($this->Msg ) ) $this->Msg = &check_init('ShowMsg','ShowMsg', $this->lang_id);
            if( empty($this->Form ) ) $this->Form = &check_init('Form','Form');
            //$this->Spr = &check_init('SysSpr','SysSpr');
       } // End of SysLang Constructor

       // ================================================================================================
       // Function : GetCountLang()
       // Version : 1.0.0
       // Date : 09.02.2008
       // Parms : $back_front / front or back
       // Returns : true,false / Void
       // Description : Get count of languages
       // ================================================================================================
       // Programmer : Igor Trokhymchuk
       // Date : 09.02.2008
       // Reason for change : Reason Description / Creation
       // Change Request Nbr:
       // ================================================================================================
       function GetCountLang( $back_front='back' )
       {
           $tmp_db = DBs::getInstance();
           $q = "SELECT `id` FROM `".TblModSysLang."` WHERE 1";
           if ($back_front=='back') {$q = $q." and `back`='1'";}
           if ($back_front=='front') {$q = $q." and `front`='1'";}
           $res = $tmp_db->db_Query($q);
           $rows = $tmp_db->db_GetNumRows();
           //echo '<br> $q='.$q.' res='.$res.' $rows='.$rows;
           return $rows;
       } //end of function GetCountLang()       
       
       // ================================================================================================
       // Function : LangArray()
       // Version : 1.0.0
       // Date : 26.01.2005
       // Parms : $lang_id / on which language the name of the language will be shown
       // Returns : true,false / Void
       // Description : Set Language of page
       // ================================================================================================
       // Programmer : Igor Trokhymchuk
       // Date : 11.01.2006
       // Reason for change : Reason Description / Creation
       // Change Request Nbr:
       // ================================================================================================
       function LangArray( $lang_id = NULL, $back_front='back' )
       {
        //echo '<br>$back_front='.$back_front;
        if( isset (self::$LangArray)) return self::$LangArray;
        if( $lang_id ) $this->lang_id = $lang_id;
        else $this->lang_id = $this->GetDefBackLangID();
        $q = "SELECT `".TblModSysLang."`.cod, `".TblModSysLangSpr."`.name FROM `".TblModSysLang."`, `".TblSysLang."` 
              WHERE `".TblModSysLang."`.cod=`".TblSysLang."`.cod
              AND `".TblModSysLangSpr."`.lang_id='".$this->lang_id."'";
        if ($back_front=='back') {$q = $q." and `".TblModSysLang."`.back='1'";}
        if ($back_front=='front') {$q = $q." and `".TblModSysLang."`.front='1'";}
        $q = $q." ORDER BY `".TblSysLang."`.move";
        $res = $this->Rights->db_Query($q);
        //echo '<br> $q='.$q.' res='.$res;
        $rows = $this->Rights->db_GetNumRows();

        $mas_lang = NULL;
        for( $i = 0; $i < $rows; $i++ )
        {
         $row_spr = $this->Rights->db_FetchAssoc();
         $mas_lang[ $row_spr['cod'] ] = $row_spr['name'] ;
        }
        //echo '<br>$mas_lang=';print_r($mas_lang);
        self::$LangArray = $mas_lang;
        return $mas_lang;
       } //end of function LangArray()

       // ================================================================================================
       // Function : StrLangArray()
       // Version : 1.0.0
       // Date : 26.01.2005
       // Parms : $lang_id / on which language the name of the language will be shown
       // Returns : true,false / Void
       // Description : Set Language of page
       // ================================================================================================
       // Programmer : Igor Trokhymchuk
       // Date : 11.01.2006
       // Reason for change : Reason Description / Creation
       // Change Request Nbr:
       // ================================================================================================
       function StrLangArray( $lang_id = NULL, $back_front='back' )
       {
        if( $lang_id ) $this->lang_id = $lang_id;
        $q = "SELECT `".TblModSysLang."`.*, `".TblSysLang."`.move as langmove,`".TblSysLang."`.name as namelang FROM `".TblModSysLang."`, `".TblSysLang."` WHERE 1";
        if ($back_front=='back') {$q = $q." AND  `".TblModSysLang."`.back='1'";}
        if ($back_front=='front') {$q = $q." AND  `".TblModSysLang."`.front='1'";}
        $q = $q." AND `".TblModSysLang."`.cod=`".TblSysLang."`.cod AND `".TblSysLang."`.lang_id='".$this->lang_id."' order by `langmove`";
        $res = $this->Rights->db_Query($q);
        //echo '<br> $q='.$q.' res='.$res;

        $rows = $this->Rights->db_GetNumRows();
        //echo '<br>$rows='.$rows;

        $mas_lang = NULL;
        for( $i = 0; $i < $rows; $i++ )
        {
         $row_spr = $this->Rights->db_FetchAssoc();
         $mas_lang[ $row_spr['short_name'] ] = $row_spr['namelang']; 
        }
        //print_r($mas_lang);
        return $mas_lang;
       } //end of function StrLangArray() 
              
       
       // ================================================================================================
       // Function : WriteLangPanelBox()
       // Version : 1.0.0
       // Date : 02.02.2005
       // Parms :
       // Returns : true,false / Void
       // Description : Write the language in the combobox
       // ================================================================================================
       // Programmer : Igor Trokhymchuk
       // Date : 02.02.2005
       // Reason for change : Reason Description / Creation
       // Change Request Nbr:
       // ================================================================================================

       function WriteLangPanelBox( $lang_id = NULL )
       {
        if (!empty($lang_id)) $this->lang_id = $lang_id;
        $this->Form->Select( $this->LangArray($this->lang_id), 'lang_id', '' );
       }

       // ================================================================================================
       // Function : WriteLangPanel()
       // Version : 1.0.0
       // Date : 02.02.2005
       // Parms :
       // Returns : true,false / Void
       // Description : Write the language in the text string
       // ================================================================================================
       // Programmer : Igor Trokhymchuk
       // Date : 02.02.2005
       // Reason for change : Reason Description / Creation
       // Change Request Nbr:
       // ================================================================================================
       function WriteLangPanel($lang_id=NULL, $back_front='back')
       {
        //echo '<br> $back_front='.$back_front;
       if (!empty($lang_id)) $this->lang_id = $lang_id;
       else $this->lang_id = $this->GetDefBackLangID(); 

       $mas=$this->LangArray($this->lang_id, $back_front);

       if (empty($mas)){
            $_SESSION["lang_pg"]=1;
            $msg = new ShowMsg(1);
            //echo '<br> LANG='.$pg->lang.' $_SESSION["lang_pg"]='.$_SESSION["lang_pg"].' _LANG_ID='._LANG_ID;
            $msg->show_msg('_ERR_NO_TRANSLATE_ON_THIS_LANG');
            $mas=$this->LangArray($_SESSION["lang_pg"], $back_front);
        }
        if ( empty($mas) ) return false;
        $script = NULL;
        $tmp = $_SERVER["REQUEST_URI"];
        $s1 = explode( "lang_pg=", $tmp );
        $script = $s1[0];
        if( isset( $s1[1] ) ) {
         $s2 = explode( '&', $s1[1] );
         if( !empty( $s2[1] ) ) $script = $script.'&amp;'.$s2[1];
        }
        if (strstr($script,"?")){
          $script = trim( $script );
          if ( substr( $script, (strlen($script)-1), strlen($script) ) == '&' ) $strlink=$script."lang_pg=";
          else $strlink=$script."&amp;lang_pg=";
        }
        else $strlink=$script."?lang_pg=";
        $list_lang='';
//        print_r($mas);
        //echo '<br> _LANG_ID='._LANG_ID;
        $count=count($mas);
        $i=1;
        while( $el = each( $mas ) )
        {
            $strs='';
            if($count!=$i)  $strs='&nbsp;|&nbsp;';
             if( _LANG_ID != $el['key'] ) $list_lang = $list_lang.$this->Form->Link2($strlink.$el['key'], $el['value']).$strs;
             else $list_lang = $list_lang.$el['value'].$strs;
         $i++;
        }
        //echo '<br>$list_lang='.$list_lang;
        return $list_lang;
       } // end of function WriteLangPanel();

       // ================================================================================================
       // Function : WriteLangPanelFront()
       // Version : 1.0.0
       // Date : 02.02.2005
       // Parms :
       // Returns : true,false / Void
       // Description : Write the language in the text string
       // ================================================================================================
       // Programmer : Igor Trokhymchuk
       // Date : 02.02.2005
       // Reason for change : Reason Description / Creation
       // Change Request Nbr:
       // ================================================================================================
       function WriteLangPanelFront($lang_id=NULL)
       {
        $back_front='front';
        if (!empty($lang_id)) $this->lang_id = $lang_id;

        $mas=$this->LangArray($this->lang_id, $back_front);
        if (empty($mas)){
            $_SESSION["lang_pg"]=1;
            $msg = new ShowMsg(1);
            //echo '<br> LANG='.$pg->lang.' $_SESSION["lang_pg"]='.$_SESSION["lang_pg"].' _LANG_ID='._LANG_ID;
            $msg->show_msg('_ERR_NO_TRANSLATE_ON_THIS_LANG');
            $mas=$this->LangArray($_SESSION["lang_pg"], $back_front);
        }
        if ( empty($mas) ) return false;
        $script = NULL;
        $tmp = $_SERVER["REQUEST_URI"];
        $s1 = explode( "lang_pg=", $tmp );
        
        $script = $s1[0];
        if( isset( $s1[1] ) ) {
         $s2 = explode( '&', $s1[1] );
         if( !empty( $s2[1] ) ) $script = $script.'&amp;'.$s2[1];
        }
        if (strstr($script,"?")){
          $script = trim( $script );
          if ( substr( $script, (strlen($script)-1), strlen($script) ) == '&' ) $strlink=$script."lang_pg=";
          else $strlink=$script."&amp;lang_pg=";
        }
        else $strlink=$script."?lang_pg=";
        $list_lang='';
        //print_r($mas);
        //echo '<br> _LANG_ID='._LANG_ID;
        while( $el = each( $mas ) )
        {
         if( _LANG_ID != $el['key'] ) $list_lang = $list_lang.$this->Form->Link($strlink.$el['key'], $el['value']);
         else $list_lang = $list_lang.$el['value'];
        }
        //echo '<br>$list_lang='.$list_lang;
        return $list_lang;
       } // end of function WriteLangPanelFront();       
       // ================================================================================================
       // Function : WriteLangPanelFrontHtAccess()
       // Version : 1.0.0
       // Date : 02.02.2005
       // Parms :
       // Returns : true,false / Void
       // Description : Write the language in the text string
       // ================================================================================================
       // Programmer : Igor Trokhymchuk
       // Date : 02.02.2005
       // Reason for change : Reason Description / Creation
       // Change Request Nbr:
       // ================================================================================================
       function WriteLangPanelFrontHtAccess($lang_id=NULL, $back_front='back')
       {
        //echo '<br> $back_front='.$back_front;
       if (!empty($lang_id)) $this->lang_id = $lang_id;
        $mas=$this->LangArray($this->lang_id, $back_front);
        if (empty($mas)){
            $_SESSION["lang_pg"]=1;
            $msg = new ShowMsg(1);
            //echo '<br> LANG='.$pg->lang.' $_SESSION["lang_pg"]='.$_SESSION["lang_pg"].' _LANG_ID='._LANG_ID;
            $msg->show_msg('_ERR_NO_TRANSLATE_ON_THIS_LANG');
            $mas=$this->LangArray($_SESSION["lang_pg"], $back_front);
        }
        if ( empty($mas) ) return false;
        $script = NULL;
        $tmp = $_SERVER["REQUEST_URI"];
        $s1 = explode( "lang_pg=", $tmp );
        $script = $s1[0];
        if( isset( $s1[1] ) ) {
         $s2 = explode( '&', $s1[1] );
         if( !empty( $s2[1] ) ) $script = $script.'&'.$s2[1];
        }
        if (strstr($script,"?")){
          $script = trim( $script );
          if ( substr( $script, (strlen($script)-1), strlen($script) ) == '&' ) $strlink=$script."lang_pg=";
          else $strlink=$script."&lang_pg=";
        }
        else $strlink=$script."?lang_pg=";
        $list_lang='';
        //print_r($mas);
        //echo '<br> _LANG_ID='._LANG_ID;
        $length = NULL;
        $ppp = $_SERVER['REQUEST_URI'];
        $aaa = explode(".", $ppp);
        $length = strlen($aaa[0]);
        
        $page_ = substr($aaa[0], 1, $length-2);
        
        $arr = explode("_", $ppp);
        for( $j=0; $j<count($arr); $j++ )
        {
            $page = $arr[$j];
        }
        $tmp_arr = explode(".", $page);
        $lang = $tmp_arr[0];
        $lang_ = _LANG_ID;
        $lang = _LANG_ID;
        if($lang==2)
        {$lang_ = 3;}
        if($lang==3)
        {$lang_ = 2;}
        $url = $page_.$lang_.".html";
        
        if($_SERVER['PHP_SELF']=="/index.php")
        $url = $_SERVER['PHP_SELF']."?&lang_pg=".$lang_;
        
        while( $el = each( $mas ) )
        {
        //echo $strlink.$el['key'];
         if( _LANG_ID != $el['key'] ) $list_lang = $list_lang.$this->Form->Link($url, $el['value']);
         else $list_lang = $list_lang.$el['value'];
        }
        //echo '<br>$list_lang='.$list_lang;
        return $list_lang;
       } // end of function WriteLangPanelFrontHtAccess()

       // ================================================================================================
       // Function : WriteLangPanelFrontByFolders()
       // Version : 1.0.0
       // Date : 02.02.2005
       // Parms :
       // Returns : true,false / Void
       // Description : Write the language in the text string
       // ================================================================================================
       // Programmer : Igor Trokhymchuk
       // Date : 02.02.2005
       // Reason for change : Reason Description / Creation
       // Change Request Nbr:
       // ================================================================================================
       function WriteLangPanelFrontByFolders($lang_id=NULL, $is_img = false)
       {
        $back_front='front';
        if (!empty($lang_id)) $this->lang_id = $lang_id;
        if (empty($this->Spr))  $this->Spr = &check_init('SysSpr', 'SysSpr'); 
        $mas=$this->LangArray($this->lang_id, $back_front);
        $strmas=$this->StrLangArray($this->lang_id, $back_front); 
        //print_r($mas);
        //print_r($strmas);
        if (empty($mas)){
            $_SESSION["lang_pg"]=1;
            $msg = new ShowMsg(1);
            //echo '<br> LANG='.$pg->lang.' $_SESSION["lang_pg"]='.$_SESSION["lang_pg"].' _LANG_ID='._LANG_ID;
            $msg->show_msg('_ERR_NO_TRANSLATE_ON_THIS_LANG');
            $mas=$this->LangArray($_SESSION["lang_pg"], $back_front);
        }
        if ( empty($mas) ) return false;
        $script = NULL;
        //phpinfo();
        $tmp = $_SERVER["REQUEST_URI"];
        $tmp = substr($tmp, 1, 255);
        $m = explode( "/", $tmp );
        if (isset($m[0])) {
            $def_lang = $this->GetDefFrontLangID();
            $link=NULL;
            foreach($m as $key=>$value){
               if( ($key==0 AND !array_key_exists($value, $strmas)) OR $key>0){    
                    if( empty($link) ) $link = $value;
                    else $link = $link.'/'.$value;
                    //echo '<br>$link='.$link;
               }
            }
            $list_lang='';
            while( $el = each( $strmas ) )
            {
                if( !isset($m[1]) ) $m[1]=NULL;
                //echo '<br>$el[key]='.$el['key'];
                $lang_cod = $this->GetLangCodByShortName($el['key']);
                if($is_img){
                    //echo '<br>00000000';
                    //echo '<br>111='.$this->Spr->GetImageByCodOnLang(TblSysLang, $this->GetLangCodByShortName($el['key']), $this->lang_id);
                    $img_path = $this->Spr->GetImageByCodOnLang(TblSysLang, $lang_cod, $this->lang_id);
                    if( !empty($img_path) ) $str='<img src="'.$img_path.'" border="0" alt="'.$el['value'].'" title="'.$el['value'].'"/>';
                    else $str=$el['key'];
                }
                else { $str = $el['value']; } 
                if( $lang_cod!=$def_lang ) $new_lang = "/".$el['key']."/";
                else $new_lang = "/";
                //echo '<br>_LANG_ID='._LANG_ID.' $this->GetLangCodByShortName($el[key])='.$this->GetLangCodByShortName($el['key']);
                //if( _LINK != $new_lang ) $list_lang = $list_lang.'<a class="lang" href="'.$new_lang.$link.'">'.$str.'</a>';
                if( _LANG_ID != $lang_cod ) $list_lang = $list_lang.'<a class="lang" href="'.$new_lang.$link.'">'.$str.'</a>';
                else $list_lang = $list_lang.'<span class="lang">'.$str.'</span>';
                $list_lang = $list_lang.' ';
            }
        } 
        return $list_lang;
       } // end of function WriteLangPanelFrontByFolders();
       
       /**
        * SysLang::GetLangData()
        * 
        * @param mixed $lang_id
        * @param string $back_front
        * @return array with langs data
        */
       function GetLangData( $lang_id = NULL, $back_front='back' )
       {
        //echo '<br>$back_front='.$back_front;
        if( $lang_id ) $this->lang_id = $lang_id;
        else $this->lang_id = $this->GetDefBackLangID();
        $q = "SELECT 
              `".TblModSysLang."`.*, 
              `".TblModSysLangSpr."`.name,
              `".TblModSysLangSpr."`.img,
              `".TblModSysLangSpr."`.short 
              FROM `".TblModSysLang."`, `".TblSysLang."` 
              WHERE `".TblModSysLang."`.cod=`".TblSysLang."`.cod
              AND `".TblModSysLangSpr."`.lang_id='".$this->lang_id."'";
        if ($back_front=='back') {$q = $q." and `".TblModSysLang."`.back='1'";}
        if ($back_front=='front') {$q = $q." and `".TblModSysLang."`.front='1'";}
        $q = $q." ORDER BY `".TblModSysLangSpr."`.move";
        $res = $this->Rights->db_Query($q);
        //echo '<br> $q='.$q.' res='.$res;
        $rows = $this->Rights->db_GetNumRows();

        $mas_lang = NULL;
        for( $i = 0; $i < $rows; $i++ )
        {
         $row_spr = $this->Rights->db_FetchAssoc();
         $mas_lang[ $row_spr['cod'] ] = $row_spr;
        }
        //echo '<br>$mas_lang=';print_r($mas_lang);
        return $mas_lang;
       } //end of function GetLangData()
       
       /**
        * SysLang::WriteLangPanelShort()
        * 
        * @param mixed $lang_id
        * @param bool $is_img
        * @return
        */
       function WriteLangPanelShort($lang_id=NULL, $is_img = false)
       {
        $back_front='front';
        $list_lang = NULL;
        $def_lang = NULL;
        if (!empty($lang_id)) $this->lang_id = $lang_id;
        
        $mas = $this->GetLangData($this->lang_id, $back_front);
        //print_R($mas);
        $keys_mas = array_keys($mas);
        $cnt_mas = count($mas);
        $strmas = array();
        $strmas_lower = array();
        for($i=0;$i<$cnt_mas;$i++){
            $row = $mas[$keys_mas[$i]];
            $strmas[$row['short_name']]=$row['name'];
            $strmas_lower[strtolower($row['short_name'])] = $row['name'];
            if($row['def_front']==1) $def_lang = $row['cod'];
        }
        
        if (empty($mas)){
            $_SESSION["lang_pg"]=1;
            $this->Msg->show_msg('_ERR_NO_TRANSLATE_ON_THIS_LANG');
            $mas=$this->LangArray($_SESSION["lang_pg"], $back_front);
            if ( empty($mas) ) return false;
        }
        
        $tmp = $_SERVER["REQUEST_URI"];
        $tmp = substr($tmp, 1, 255);
        $m = explode( "/", $tmp );

        if (isset($m[0])) {
            $link=NULL;
            foreach($m as $key=>$value){
               if( ($key==0 AND !array_key_exists($value, $strmas_lower)) OR $key>0){    
                    if( empty($link) ) 
                        $link = $value;
                    else 
                        $link = $link.'/'.$value;
               }
            }
            
            $list_lang='<div id="langNavBox"><ul>';
            for($i=0;$i<$cnt_mas;$i++){
                $row = $mas[$keys_mas[$i]];
                $lang_cod = $row['cod'];
                $img_path = stripslashes($row['img']);
                $name = stripslashes($row['name']);
                $short_name = stripslashes($row['short_name']);
                $new_lang = "/";
                if( !isset($m[1]) ) $m[1]=NULL;
                if($is_img AND !empty($img_path)) $str='<img src="'.$img_path.'" border="0" alt="'.htmlspecialchars($name).'" title="'.htmlspecialchars($name).'"/>';
                else $str = $name;
                
                if( $lang_cod != $def_lang ) 
                    $new_lang = "/".strtolower($short_name)."/";
                    
                if( _LANG_ID != $lang_cod ) 
                    $list_lang .= '<li><a href="'.$new_lang.$link.'">'.$str.'</a></li>';
                else 
                    $list_lang .= '<li><span class="lang">'.$str.'</span></li>';
                $list_lang .= ' ';
            }
            $list_lang .= '</ul></div>';
        }
        return $list_lang;
       } // end of function WriteLangPanelShort();      

        // ================================================================================================
        // Function : GetDefBackLangID()
        // Version : 1.0.0
        // Date : 26.07.2011
        // Parms :
        // Returns : true,false / Void
        // Description : return default language data on the Back-End
        // ================================================================================================
        // Programmer : Igor Trokhymchuk
        // Date : 26.07.2011
        // Reason for change : Reason Description / Creation
        // Change Request Nbr:
        // ================================================================================================
        function GetDefBackLangData()
        {
            $tmp_db = DBs::getInstance();
            $q = "select * from `".TblModSysLang."` where `def_back`='1'";
            $res = $tmp_db->db_Query($q);
            //$rows = $tmp_db->db_GetNumRows();
            $row = $tmp_db->db_FetchAssoc();
            return $row;
        } // end function GetDefBackLangID()
               
        // ================================================================================================
        // Function : GetDefBackLangID()
        // Version : 1.0.0
        // Date : 15.12.2006
        // Parms :
        // Returns : true,false / Void
        // Description : return default language on the Back-End
        // ================================================================================================
        // Programmer : Igor Trokhymchuk
        // Date : 15.12.2006
        // Reason for change : Reason Description / Creation
        // Change Request Nbr:
        // ================================================================================================
        function GetDefBackLangID()
        {
            $tmp_db = DBs::getInstance();
            $q = "select * from `".TblModSysLang."` where `def_back`='1'";
            $res = $tmp_db->db_Query($q);
            //$rows = $tmp_db->db_GetNumRows();
            $row = $tmp_db->db_FetchAssoc();
            return $row['cod'];
        } // end function GetDefBackLangID()

        // ================================================================================================
        // Function : GetDefFrontLangID()
        // Version : 1.0.0
        // Date : 15.12.2006 
        // Parms :
        // Returns : true,false / Void
        // Description : return default language on the Front-End
        // ================================================================================================
        // Programmer : Igor Trokhymchuk
        // Date : 15.12.2006 
        // Reason for change : Reason Description / Creation
        // Change Request Nbr:
        // ================================================================================================
        function GetDefFrontLangID()
        {
            $tmp_db = DBs::getInstance();
            $q = "select * from `".TblModSysLang."` where `def_front`='1'";
            $res = $tmp_db->db_Query($q);
            //$rows = $tmp_db->db_GetNumRows();
            $row = $tmp_db->db_FetchAssoc();
            return $row['cod'];
        } // end function GetDefFrontLangID()
        
        // ================================================================================================
        // Function : GetDefLangEncoding()
        // Version : 1.0.0
        // Date : 22.08.2007 
        // Parms : $lang_id - cod of the language
        // Returns : true,false / Void
        // Description : return encoding for language $lang_id
        // ================================================================================================
        // Programmer : Igor Trokhymchuk
        // Date : 22.08.2007 
        // Reason for change : Reason Description / Creation
        // Change Request Nbr:
        // ================================================================================================
        function GetDefLangEncoding( $lang_id = NULL )
        {
            $tmp_db = DBs::getInstance();
            $q = "SELECT `encoding` FROM `".TblModSysLang."` WHERE `cod`='$lang_id'";
            $res = $tmp_db->db_Query($q);
            $row = $tmp_db->db_FetchAssoc();
            return $row['encoding'];
        } // end function GetDefLangEncoding()
        
        // ================================================================================================
        // Function : GetLangShortName()
        // Version : 1.0.0
        // Date : 29.08.2007 
        // Parms : $lang_id - cod of the language
        // Returns : true,false / Void
        // Description : return short name of language $lang_id
        // ================================================================================================
        // Programmer : Igor Trokhymchuk
        // Date : 29.08.2007 
        // Reason for change : Reason Description / Creation
        // Change Request Nbr:
        // ================================================================================================
        function GetLangShortName( $lang_id )
        {
            $tmp_db = DBs::getInstance(); 
            $q = "SELECT `short_name` FROM `".TblModSysLang."` WHERE `cod`='$lang_id'";
            $res = $tmp_db->db_Query($q);
            $row = $tmp_db->db_FetchAssoc();
            return $row['short_name'];
        } // end function GetLangShortName()                     

        // ================================================================================================
        // Function : GetLangCodByShortName()
        // Version : 1.0.0
        // Date : 29.08.2007 
        // Parms : $name - short name of the language
        // Returns : true,false / Void
        // Description : return short name of language $lang_id
        // ================================================================================================
        // Programmer : Igor Trokhymchuk
        // Date : 29.08.2007 
        // Reason for change : Reason Description / Creation
        // Change Request Nbr:
        // ================================================================================================
        function GetLangCodByShortName( $name )
        {
            $tmp_db = DBs::getInstance();
            $q = "SELECT `cod` FROM `".TblModSysLang."` WHERE `short_name`='$name'";
            $res = $tmp_db->db_Query($q);
            $row = $tmp_db->db_FetchAssoc();
            return $row['cod'];
        } // end function GetLangCodByShortName()         
 }//--- enf of class
?>