<?php
 // ================================================================================================
 // System : SEOCMS
 // Module : Database
 // Version : 1.0.0
 // Date : 08.08.2008
 // Licensed To:
 // Igor Trokhymchuk ihoru@mail.ru
 // Andriy Lykhodid las_zt@mail.ru
 // ================================================================================================

 // ================================================================================================
 //    Class             : FrontTags
 //    Version           : 1.0.0
 //    Date              : 08.08.2008
 //    Constructor       : Yes
 //    Parms             :
 //    Returns           : None
 //    Description       : Class definition for describe input fields on front-end
 // ================================================================================================
 //    Programmer        :  Igor Trokhymchuk, Andriy Lykhodid
 //    Date              :  08.08.2008
 //    Reason for change :  Creation
 //    Change Request Nbr:  N/A
 // ================================================================================================

 class FrontTags extends SystemTags
 {
   // ================================================================================================
   //    Function          : FrontTags (Constructor)
   //    Version           : 1.0.0
   //    Date              : 06.08.2008
   //    Parms             : usre_id   / User ID
   //                        module    / module ID
   //    Returns           : Error Indicator
   //
   //    Description       : Opens and selects a dabase
   // ================================================================================================
   function FrontTags($user_id=NULL, $module=NULL) {
            //Check if Constants are overrulled
            ( $user_id   !="" ? $this->user_id = $user_id  : $this->user_id = NULL );
            ( $module   !="" ? $this->module  = $module   : $this->module  = NULL );

            if ( defined("_LANG_ID") ) $this->lang_id = _LANG_ID;
                         
            if (empty($this->Rights)) $this->Rights = new Rights($this->user_id, $this->module);
            if (empty($this->Spr)) $this->Spr = new SystemSpr($this->user_id, $this->module);
            if (empty($this->Msg)) $this->Msg = new ShowMsg();
            $this->Msg->SetShowTable(TblModPagesSprTxt);
   } // End of FrontTags Constructor     

   // ================================================================================================
   // Function : ShowUsingTags()
   // Version : 1.0.0
   // Date : 20.09.2008
   // Parms :   $id_module - id of the module
   //           $id_item - id of the position
   // Returns : true,false / Void
   // Description : show using tsgs for $id_item and module $id_module
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 20.09.2008 
   // Reason for change : Reason Description / Creation
   // Change Request Nbr:
   // ================================================================================================
   function ShowUsingTags( $id_module, $id_item )
   {
       //$arr = $this->GetUsingTags( $id_module, $id_item );
       //print_r($arr);
       $db = new DB();

       $q = "SELECT `id_tag` FROM `".TblSysModTags."` WHERE `id_module`='".$id_module."' AND `id_item`='".$id_item."'";
       $res = $db->db_Query( $q );
       //echo '<br> $q='.$q.' $res='.$res.' $db->result='.$db->result;
       if( !$res OR !$db->result ) {return false;}
       $rows = $db->db_GetNumRows();
       //echo '<br>$rows='.$rows;
       $tags_str = NULL;
       $id_str = NULL;
       ?><div><?
       for($i=0;$i<$rows;$i++){
            $row = $db->db_FetchAssoc();
            $tag_name = $this->Spr->GetNameByCod(TblSysModTagsSprName, $row['id_tag'], $this->lang_id, 1);
            if( $i==0 ) { ?><a href="<?=_LINK?>tags/<?=urlencode($tag_name);?>"><?=$tag_name;?></a><?}
            else {?>, <a href="<?=_LINK?>tags/<?=urlencode($tag_name);?>"><?=$tag_name?></a><?}
       }//end for
       ?></div><?
   }//end of function ShowUsingTags
     
   // ================================================================================================
   // Function : ShowSimilarItems()
   // Version : 1.0.0
   // Date : 08.08.2008
   // Parms :   $id_module - id of the module
   //           $id_item - id of the position
   // Returns : true,false / Void
   // Description : show similar items for $id_item and module $id_module
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 08.08.2008
   // Reason for change : Reason Description / Creation
   // Change Request Nbr:
   // ================================================================================================
   function ShowSimilarItems( $id_module, $id_item )
   {
       $arr = $this->GetSimilarItems( $id_module, $id_item );
       if(!is_array($arr)) return false;
       foreach($arr as $id_module=>$arr_items){
           //echo '<br>$id_module='.$id_module;
           foreach($arr_items as $id_item=>$id){
               //echo '<br>$id_item='.$id_item;
               switch($id_module){
                   //Catalog
                   case '68':
                    $Obj = new CatalogLayout();
                    $name = $this->Spr->GetNameByCod(TblModCatalogPropSprName, $id_item, $this->lang_id, 1);
                    //echo '<br>$Obj->GetMainTopLevel($id_item)='.$Obj->GetMainTopLevel($id_item).' $id_item='.$id_item;
                    $lnk = $Obj->Link($Obj->GetCategory($id_item), $id_item);
                    break;
                    //Dynamic Pages
                   case '90':
                    $Obj = new FrontendPages();
                    $name = $this->Spr->GetNameByCod(TblModPagesSprName, $id_item, $this->lang_id, 1);
                    //echo '<br>$Obj->GetMainTopLevel($id_item)='.$Obj->GetMainTopLevel($id_item).' $id_item='.$id_item;
                    $lnk = $Obj->Link($id_item);
                    break;
                    //News
                   case '72':
                    $Obj = new NewsLayout();
                    $name = $this->Spr->GetNameByCod(TblModNewsSprSbj, $id_item, $this->lang_id, 1);
                    //echo '<br>$Obj->GetMainTopLevel($id_item)='.$Obj->GetMainTopLevel($id_item).' $id_item='.$id_item;
                    $lnk = $Obj->GetLink(NULL, NULL, $id_item);
                    break;
                   default:
                    $name = NULL;
                    $lnk = NULL;
               }
               if( empty($name) ) continue;
               ?><a href="<?=$lnk;?>"><?=$name;?></a><br/><?
           }// end foreach
       }// end foreach
       
   } //end of function ShowSimilarItems()

   // ================================================================================================
   // Function : ShowItems()
   // Version : 1.0.0
   // Date : 08.08.2008
   // Parms :   $id_module - id of the module
   //           $id_item - id of the position
   // Returns : true,false / Void
   // Description : show similar items for $id_item and module $id_module
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 08.08.2008
   // Reason for change : Reason Description / Creation
   // Change Request Nbr:
   // ================================================================================================
   function ShowItems( $id_tag )
   {
       $arr = $this->GetInfoByIdTag($id_tag);
       foreach($arr as $id_module=>$arr_items){
           //echo '<br>$id_module='.$id_module;
           foreach($arr_items as $id_item=>$id){
               //echo '<br>$id_item='.$id_item;
               switch($id_module){
                   //Catalog
                   case '68':
                    $Obj = new CatalogLayout();
                    $name = $this->Spr->GetNameByCod(TblModCatalogPropSprName, $id_item, $this->lang_id, 1);
                    $id_cat = $Obj->GetCategory($id_item);
                    $path_str = $Obj->GetPathToLevel($id_cat);
                    $lnk = $Obj->Link($id_cat, $id_item);
                    break;
                    //Dynamic Pages
                   case '90':
                    $Obj = new FrontendPages();
                    $name = $this->Spr->GetNameByCod(TblModPagesSprName, $id_item, $this->lang_id, 1);
                    $path_str = $Obj->ShowPath($id_item);
                    $lnk = $Obj->Link($id_item);
                    break;
                   default:
                    $path_str = NULL;
                    $name = NULL;
                    $lnk = NULL;
               }
               if( empty($name) ) continue;
               //echo '<br>$path_str='.$path_str;
               if( !empty($path_str) ) echo $path_str.' > ';
               ?><a href="<?=$lnk;?>"><?=$name;?></a><br/><?
           }// end foreach
       }// end foreach
       
   } //end of function ShowItems()
   
   // ================================================================================================
   // Function : ShowCloudOfTags()
   // Version : 1.0.0
   // Date : 07.08.2008
   // Parms :  
   // Returns : true,false / Void
   // Description : show cloud of tags
   // ================================================================================================                                                   
   // Programmer : Igor Trokhymchuk
   // Date : 07.08.2008
   // Reason for change : Reason Description / Creation
   // Change Request Nbr:
   // ================================================================================================
   function ShowCloudOfTags()
   {
       $tags_array = $this->GetCloudOfTags();
       //echo '<br>$tags_array=';print_r($tags_array);
       asort($tags_array);
       //echo '<br>$tags_array=';print_r($tags_array);
       $tags_set = NULL;
       $i = 1; $dif = count ($tags_array);
       foreach ( $tags_array as $tag=>$count ) {
           $prc = ceil(($i * 100) / $dif);
           $size = ceil($prc/2) + 100;
           $tag_name = $this->Spr->GetNameByCod(TblSysModTagsSprName, $tag, $this->lang_id, 1);
           if( empty($tag_name) ) continue;
           //echo '<br>$tag='.$tag.' $size='.$size;
           //$color = $func->grad (34,131,204,252,0,140,$prc);
           $tags_set[$tag_name] = '<a style="font-size: '.$size.'%; " '.
           'href="'._LINK.'tags/'.urlencode($tag_name).'">'.$tag_name.'</a> ('.$count.')';
           $i++;
       }
       //echo '<br>$tags_set=';print_r($tags_set);
       if( is_array($tags_set)){
        ksort ($tags_set);
        //echo '<br>$tags_set=';print_r($tags_set);
        print implode(', ', $tags_set);       
       }
   } //end of function ShowCloudOfTags()    
 
 }//end of class FrontTags    