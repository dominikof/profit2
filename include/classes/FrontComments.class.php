<?php
 // ================================================================================================
 // System : SEOCMS
 // Module : FrontComments.class.php
 // Version : 1.0.0
 // Date : 20.08.2008
 // Licensed To:
 // Igor Trokhymchuk ihoru@mail.ru
 // Andriy Lykhodid las_zt@mail.ru
 // ================================================================================================

 // ================================================================================================
 //    Class             : FrontComments
 //    Version           : 1.0.0
 //    Date              : 20.08.2008
 //    Constructor       : Yes
 //    Parms             :
 //    Returns           : None
 //    Description       : Class definition for describe input fields on front-end
 // ================================================================================================
 //    Programmer        :  Igor Trokhymchuk, Andriy Lykhodid
 //    Date              :  20.08.2008
 //    Reason for change :  Creation
 //    Change Request Nbr:  N/A
 // ================================================================================================

 class FrontComments extends SystemComments
 {
     var $uselogon = NULL;
     var $login = NULL;
     var $password = NULL;     
     var $script=NULL;
     var $task = NULL;
     var $Err = NULL;
     
     public $db = NULL;
     public $Spr = NULL;
     public $Form = NULL;
     public $Logon = NULL;
     
     
     // ================================================================================================
   //    Function          : FrontComments (Constructor)
   //    Version           : 1.0.0
   //    Date              : 20.08.2008
   //    Parms             : usre_id   / User ID
   //                        module    / module ID
   //    Returns           : Error Indicator
   //
   //    Description       : Opens and selects a dabase
   // ================================================================================================
   function FrontComments($module=NULL, $id_item=NULL) {
            //Check if Constants are overrulled
            ( $module   !="" ? $this->module  = $module   : $this->module  = NULL );
            ( $id_item  !="" ? $this->id_item = $id_item  : $this->id_item = NULL ); 
            
            if ( defined("_LANG_ID") ) $this->lang_id = _LANG_ID;
                         
            if (empty($this->db)) $this->db = DBs::getInstance();
            if (empty($this->Spr)) $this->Spr = check_init('SystemSpr', 'SystemSpr', "'', '$this->module'");
            if (empty($this->Form)) $this->Form = check_init('FrontFormCommnetns', 'FrontForm', "'form_comments'");
            if (empty($this->Logon)) $this->Logon = check_init('UserAuthorize', 'UserAuthorize'); 
            $this->multi = &check_init_txt('TblFrontMulti', TblFrontMulti);
            
            $this->uselogon=1;
            
   } // End of FrontComments Constructor     

   
   // ================================================================================================
   // Function : FacebookComments()
   // Date : 20.05.2011
   // Returns : true,false / Void
   // Description : show list of Facebook comments
   // Programmer : Yaroslav Gyryn
   // ================================================================================================
   function FacebookComments($n = 7, $width = 686 ) 
   {
        if(isset($_SERVER['REQUEST_URI'])) 
            $uri = 'http://'.NAME_SERVER.$_SERVER['REQUEST_URI'];
       else $uri = 'http://'.NAME_SERVER;
        ?>
        <div id="fb-root"></div>
        <script src="http://connect.facebook.net/ru_RU/all.js#xfbml=1"></script>
        <fb:comments href="<?=$uri;?>" num_posts="<?=$n;?>" width="<?=$width;?>"></fb:comments><?
   } //end of function FacebookComments()   
   
   
   // ================================================================================================
   // Function : VkontakteComments()
   // Date : 20.06.2011
   // Returns : true,false / Void
   // Description : show list of Vkontakte comments
   // Programmer : Yaroslav Gyryn
   // ================================================================================================
   function VkontakteComments() 
   {
       /*?>
       <!-- В тег <head> на странице Вашего сайта необходимо добавить следующий код: -->
       <script src="http://userapi.com/js/api/openapi.js" type="text/javascript" charset="windows-1251"></script>*/?>
       <script type="text/javascript">
          VK.init({
            apiId: 2385265,
            onlyWidgets: true
          });
        </script>
        <?/*
        В тело страницы необходимо добавить элемент DIV, 
        в котором будут отображаться комментарии, 
        задать ему уникальный id и добавить в него код инициализации виджета
        */?>
        <!-- Put this div tag to the place, where the Comments block will be -->
        <div id="vk_comments"></div>
        <script type="text/javascript">
            VK.Widgets.Comments("vk_comments", {limit: 5, width: "686", attach: false});
            //VK.Widgets.Comments("vk_comments");
        </script>
       <?
   } //end of function VkontakteComments() 
   
   // ================================================================================================
   // Function : ShowCommentsCountLink()
   // Date : 01.06.2011
   // Returns : true,false / Void
   // Description : Show Comment Count Link 
   // Programmer : Yaroslav Gyryn
   // ================================================================================================
   function ShowCommentsCountLink($link = '#commentsBlock') 
   {
       ?>
       <div class="news_colum1_1_footer">
            <div class="news_colum1_1_footer_text"><img src="/images/design/oblako.png" alt="" /><a href="<?=$link;?>">Коментарів - <?=$this->GetCommentsCountItem();?></a></div>
            
            <div class="right"><script src="http://connect.facebook.net/ru_RU/all.js#xfbml=1"></script><fb:like href="" layout="button_count" show_faces="true"  action="recommend" font="arial"></fb:like></div>
            <div class="right"><g:plusone size="medium"></g:plusone></div>
            <?/*<div class="right">
                <div id="vk_like"></div>
                <script type="text/javascript">
                    VK.Widgets.Like("vk_like", {type: "button", verb: 1});
                </script>
            </div>*/?>
       </div>
       <br/>
       <?
   }   
   
   // ================================================================================================
   // Function : ShowCommentsByModuleAndItem()
   // Date : 01.06.2011
   // Returns : true,false / Void
   // Description : Show Comments By Module And Item
   // Programmer : Yaroslav Gyryn
   // ================================================================================================
   function ShowCommentsByModuleAndItem() 
   {
       $this->ShowJS(); 
       ?>
        <div id="commentsBlock">
            <h3><?=$this->multi['TXT_FRONT_COMMENTS'];?></h3>
            <div id="commentsList">
            <div class="regTxt">
             <?/*<a href="#" onclick="hideMainCommentForm(); return false;" title="Сховати">Сховати</a>
             &nbsp;&nbsp;*/
             if($this->uselogon==1 AND !$this->Logon->user_id){
                 ?>
                    <div class="txtRegister"><?=$this->multi['TXT_COMMENTS_REG0'];?></div>
                    <div class="butRegister"><a href="/registration/"><?=$this->multi['TXT_COMMENTS_REG1'];?></a></div>
                    <br/><br/>
                 <?
             }
             ?>
             
            </div>
            <?
             //$this->ShowCommentsList();  // Old Variant
             $this->ShowCommentsTree(10);
             ?>
             <a id="commentLink" style="display: none;" class="addComment" href="#" onclick="showMainCommentForm(); return false;" title="<?=$this->multi['TXT_FRONT_LEAVE_RESPONSES'];?>"><?=$this->multi['TXT_FRONT_LEAVE_RESPONSES'];?> &raquo;</a>
             <div id="resp_form">
                <?$this->ShowCommentsFormHTML();?>
                <?//$this->ShowCommentsForm();?>
            </div>
             </div>
        </div>
        <?
   } //end of function ShowCommentsByModuleAndItem()
   
    // ================================================================================================
    // Programmer : Yaroslav Gyryn
    // Date : 01.06.2011 
    // Reason for change : Reason Description / Creation
    // ================================================================================================
    function GetUserCommentsTree($limit, $idUser=null) 
    {                                    
        $this->idUser = $idUser;
        $tree = $this->LoadCommentsUserTreeList($limit);
        
        if(isset($this->moduleId['24'])) {
            if(empty($this->News)) $this->News = &check_init('NewsLayout', 'NewsLayout');
            $this->arrNews = $this->News->GetNewsNameLinkForId($this->moduleId['24']);
        }
        
        if(isset($this->moduleId['32'])) {
            if(empty($this->Article)) $this->Article = &check_init('ArticleLayout', 'ArticleLayout');
            $this->arrArticles = $this->Article->GetArticlesNameLinkForId($this->moduleId['32']);
        }
        if(isset($this->moduleId['37'])) {
            if(empty($this->FrontendPages)) $this->FrontendPages = &check_init('FrontendPages', 'FrontendPages');
            $this->arrArticles = $this->Article->GetSharesNameLinkForId($this->moduleId['37']);
        }
        /*
        if(isset($this->moduleId['153'])) {
            if(empty($this->Video)) $this->Video = Singleton::getInstance('VideoLayout');
            $this->arrVideos = $this->Video->GetVideosNameLinkForId($this->moduleId['153']);
        }
        
        if(isset($this->moduleId['156'])) {
            if(empty($this->Gallery)) $this->Gallery = Singleton::getInstance('GalleryLayout');
            $this->arrGallerys = $this->Gallery->GetGallerysNameLinkForId($this->moduleId['156']);
        }
        */
        
        $this->ShowJS();
        $this->showTreeUser($tree);
        //$keys = array_keys($tree);
        //print_r($tree);
        //$n = count($tree);
        /*for($i = 0; $i < $n; $i++) {
            $this->showTreeUser($tree[$keys[$i]]);      
        }*/
        //$this->showTreeUser($tree);      
    }  
    
    // ------------------------------------------------------------------------------------------------  
    function LoadCommentsUserTreeList($limit)
    {
        $q = "SELECT 
                `".TblSysModComments."`.id,
                `".TblSysModComments."`.id_module,
                `".TblSysModComments."`.id_item,
                `".TblSysModComments."`.id_user,
                `".TblSysModComments."`.dt,
                `".TblSysModComments."`.level,
                `".TblSysModComments."`.text,
                `".TblSysModComments."`.rating,
                `".TblModUser."`.discount as img,
                `".TblModUser."`.name as first_name,
                `".TblModUser."`.country as second_name
            FROM 
                `".TblSysModComments."`
            LEFT JOIN `".TblModUser."` ON 
            (
                `".TblSysModComments."`.`id_user`=`".TblModUser."`.`sys_user_id` 
            )
            WHERE 
                `".TblSysModComments."`.status = 1
            AND  
                `".TblSysModComments."`.id_user = ".$this->idUser."
            ORDER BY 
                `".TblSysModComments."`.dt desc";
         
         // $q = $q." limit ".$limit;
         // $q = $q."  limit ".$this->start.",".$this->display."";
        $res = $this->db->db_Query($q);
        //echo '<br/>'.$q.' <br/><br/>$res = '.$res.'<br/>';
        if(!$res OR !$this->db->result) 
            return false;
        
        $rows = $this->db->db_GetNUmRows($res);   
        if($rows==0)   
            return false;
            
        $tree = array();
        $this->moduleId = array();
        $idArray = array();
        
        for($i = 0; $i < $rows; $i++){
            $row = $this->db->db_FetchAssoc($res);
            
            //Список всіх ID-шок
            if(empty($idArray))
                $idArray = $row['id'];
            else
                $idArray .= ','.$row['id'];
            
            if(empty($this->moduleId[$row['id_module']]))
                $this->moduleId[$row['id_module']] = $row['id_item'];
            else
                $this->moduleId[$row['id_module']] .= ','.$row['id_item'];
            
            $tree[$row['id']] = $row;
            /*if(empty( $tree[$row['id']][$row['level']] ))
                $tree[$row['id']][$row['level']]= array();
            $tree[$row['id']][$row['level']]= $row;*/
            /*if(empty($tree[$row['id']])) 
                $tree[$row['id']] = array();
            $tree[$row['id'][$row['level']]] = $row;*/
        }

        $q = "SELECT 
                `".TblSysModComments."`.id,
                `".TblSysModComments."`.id_module,
                `".TblSysModComments."`.id_item,
                `".TblSysModComments."`.id_user,
                `".TblSysModComments."`.dt,
                `".TblSysModComments."`.level,
                `".TblSysModComments."`.text,
                `".TblSysModComments."`.rating,
                `".TblModUser."`.discount as img,
                `".TblModUser."`.name as first_name,
                `".TblModUser."`.country as second_name
            FROM 
                `".TblSysModComments."`
            LEFT JOIN `".TblModUser."` ON 
            (
                `".TblSysModComments."`.`id_user`=`".TblModUser."`.`sys_user_id` 
            )
            WHERE 
                `".TblSysModComments."`.status = 1
            AND
                `".TblSysModComments."`.level IN ( ".$idArray.")
            AND
                `".TblSysModComments."`.id NOT IN ( ".$idArray.")
            ORDER BY 
                `".TblSysModComments."`.dt desc";
         
        $res = $this->db->db_Query($q);
        //echo '<br/>'.$q.' <br/><br/>$res = '.$res.'<br/>';
        if(!$res OR !$this->db->result) 
            return false;
        
        $rows = $this->db->db_GetNUmRows($res);   
        for($i = 0; $i < $rows; $i++){
            $row = $this->db->db_FetchAssoc($res);
            
            if(empty($this->moduleId[$row['id_module']]))
                $this->moduleId[$row['id_module']] = $row['id_item'];
            else
                $this->moduleId[$row['id_module']] .= ','.$row['id_item'];
            
            /*if(empty( $tree[$row['id']][$row['level']] ))
                $tree[$row['id']][$row['level']]= array();
            $tree[$row['id']][$row['level']]= $row;*/
            
            $tree[$row['id']] = $row;
        }
        
        krsort($tree);

        /*$keys = array_keys($tree);
        $n = count($tree);
        $this->arr2 = array();
        $this->makeUserTree($tree, 0, $limit);
        //for($i = 0; $i < $n; $i++) {
            //$union[] = $this->makeUserTree($tree, $keys[$i], $limit);
        //}*/
        return $tree;  
    }
    

    /*// ------------------------------------------------------------------------------------------------  
    function GetSubLevel($tree, $id ) {
        //echo '<br/>id ='.$id;
        if(isset($tree[$id])) {
            $path = $id;
            $keys2 = array_keys($tree[$id]);
            $n2 = count($tree[$id]);
            for($j = 0; $j < $n2; $j++) {
                    if(!isset($tree[$id][$keys2[$j]]['show'])) {
                        //echo '<br/>$keys2[$j] ='.$keys2[$j];
                        $tree[$id][$keys2[$j]]['show'] =  1;
                        //$this->arr2[$id][$keys2[$j]] = 1;
                        echo '<br/>$res = '.$res = $this->GetSubLevel($tree, $keys2[$j]);
                        if($res)
                            $path = $res.','.$id;
                    }
            }
            return $path;
        }
        return $id;
    }*/
    
    
    // ------------------------------------------------------------------------------------------------  
    /*function makeUserTree(&$tree, $k_item = 0, $limit=9999)
    {
        $keys = array_keys($tree);
        $n = count($tree);
        for($i = 0; $i < $n; $i++) {
             $keys2 = array_keys($tree[$keys[$i]]);
             $n2 = count($tree[$keys[$i]]);
             for($j = 0; $j < $n2; $j++) {
                 $this->arr2[$keys[$i]] = $this->GetSubLevel($tree, $keys2[$j]);
             }
             echo '<br/>';
        }
        
        
        //echo '<br/><br/>$k_item = '.$k_item;
        //print_r($tree[$k_item]);
        /*if(empty($tree[$k_item])) 
            return array();
            
        $a_tree = array();
        $n = count($tree[$k_item]);
        for($i = 0; $i < $n; $i++) {
            if($i==$limit) 
                break;
            $row = $tree[$k_item][$i];
            //echo '<br/>$tree[$k_item][$i] ='.print_r($tree[$k_item][$i]);
            $row['a_tree'] = $this->makeUserTree($tree, $tree[$k_item][$i]['id']);
            $a_tree[] = $row;
        }
        //print_r($a_tree);
        return $a_tree;   
    }*/
    
    // ------------------------------------------------------------------------------------------------  
    function showTreeUser(&$a_tree, $level = 0)
    {
        if(empty($a_tree))
            return;
        
        $n = count($a_tree);
        $keys = array_keys($a_tree);
        $margin = 30*$level;

        for($j = 0; $j <$n; $j++) {
            $i  = $keys[$j];
                                            
            $id= $a_tree[$i]['id'];
            $idUser = $a_tree[$i]['id_user'];
            $date = date("Y.m.d H:i", $a_tree[$i]['dt']);
            $comment =  $a_tree[$i]['text'];
            $rating = $a_tree[$i]['rating'];
            $image =  $a_tree[$i]['img'];
            $module  = $a_tree[$i]['id_module'];
            $item = $a_tree[$i]['id_item'];
            $itemName = '';
            $itemLink ='#';
            switch ($module) {
               case '72': 
                        if(isset($this->arrNews[$item])) {
                            $itemName = $this->arrNews[$item]['name'];
                            $itemLink = $this->arrNews[$item]['link'];
                        }
                        break;
               
               case '83': 
                        if(isset($this->arrArticles[$item])) {
                            $itemName = $this->arrArticles[$item]['name'];
                            $itemLink = $this->arrArticles[$item]['link'];
                        }
                        break;
               
               case '153': 
                        //echo '<br/>Video';
                        if(isset($this->arrVideos[$item])) {
                            $itemName = $this->arrVideos[$item]['name'];
                            $itemLink = $this->arrVideos[$item]['link'];
                        }
                        break;

               case '156': 
                        //echo '<br/>Gallery';
                        if(isset($this->arrGallerys[$item])) {
                            $itemName = $this->arrGallerys[$item]['name'];
                            $itemLink = $this->arrGallerys[$item]['link'];
                        }
                        break;
            } 
            if( !empty($a_tree[$i]['name']) ){
                 $username = stripslashes($a_tree[$i]['name']);
             }
             elseif( !empty($a_tree[$i]['second_name']) or !empty($a_tree[$i]['first_name'] )) {
                 $username = stripslashes($a_tree[$i]['second_name']).' '.stripslashes($a_tree[$i]['first_name']);
             }
             else {
                 if (empty($this->User)) 
                    $this->User = &check_init('User', 'User');
                 $username = $this->User->GetUserLoginByUserId($idUser);
             }
            ?>
            
            <div id="div_<?=$id;?>" class="comment" style="margin-left:<?=$margin;?>px;">
                <div class="userPhoto left">
                    <?if($image != null) {?>
                        <img width="80" height="80" src="/images/mod_blog/<?=$idUser;?>/<?=$image;?>"/>
                    <?}
                        else {?>
                        <img width="80" height="80" src="/images/design/noAvatar.gif"/>
                        <?}
                    ?>
                </div>
                <div class="content">
                    <div style="overflow: hidden;">
                        <div class="name2 left"><?=$username;?></div>
                        <div class="date2 left"><?=$date?></div>
                        <div class="left">&nbsp;до&nbsp;</div><br/>
                        <div class="left"> <a class="item" href="<?=$itemLink?>#commentsBlock"><?=$itemName?></a>&nbsp;</div>
                        
                     </div>
                    <div class="data">
                        <?=str_replace("\n", "<br/>",stripslashes($comment));?><br/>
                    </div>
                    <?
                    //$params = 'onclick="AddComment('."'/add_comments.php'".', '."'div_'".'); return false;"';
                    //$link="javascript:showForm('".$id."')";
                    if($idUser == $this->idUser) {
                        ?><a class="reply"  onclick="DeleteComment('/add_comments.php?task=del_comments&idComment=<?=$id?>', 'div_', '<?=$id?>' ); return false;" href="#" ><?=$this->multi['TXT_COMMENTS_DELETE_MY_ITEM'];?> &raquo;</a><?
                    }
                    else {
                        ?><a class="reply" href="<?=$itemLink?>#commentsBlock"><?=$this->multi['TXT_COMMENTS_REPLY_TO_ITEM'];?> &raquo;</a><?
                    }
                    $class='gray';
                    if($rating>0)
                        $class='blue';
                    elseif($rating<0)
                        $class='red';
                    ?>
                    <div id="rating_<?=$id?>" class="ratingBlock right">
                        
                        <div title="<?=$this->multi['TXT_COMMENTS_RATING'];?>"  class="rating left <?=$class?>"><?=$rating;?></div>
                        <?/*if( $this->Logon->user_id){?>
                            <a title="+" href="#" href="#" onclick="Vote('/add_comments.php?task=vote&idComment=<?=$id;?>&idUser=<?=$this->Logon->user_id?>&vote=1',  'rating_<?=$id?>'); return false;" ><img src="/images/design/icoPlus.gif"></a>
                            <a title="-" href="#" onclick="Vote('/add_comments.php?task=vote&idComment=<?=$id;?>&idUser=<?=$this->Logon->user_id?>&vote=-1',  'rating_<?=$id?>'); return false;" ><img src="/images/design/icoMinus.gif"></a>
                        <?}
                        else */{
                            ?><img title="<?=$this->multi['TXT_COMMENTS_RATING'];?>" src="/images/design/icoVote.png"><?
                        }
                        ?>
                    </div>
                </div>
            </div>
            <?//$this->showTreeUser($a_tree[$i]['a_tree'], ++$level);
        }
    }   

    // ================================================================================================
    // Programmer : Yaroslav Gyryn
    // Date : 01.06.2011 
    // Reason for change : Reason Description / Delete Comments
    // ================================================================================================
    function DeleteComments () {
        
        $q = "SELECT 
                 COUNT(`".TblSysModComments."`.id) as count
            FROM 
                `".TblSysModComments."` 
            WHERE 
                `level`=".$this->idComment." 
            ";
        $res = $this->db->db_Query($q);
        //echo '$q ='.$q.' <br/>$res = '.$res.'<br/>';
        if(!$res OR !$this->db->result) 
            return 0;
        $row = $this->db->db_FetchAssoc($res);
        if($row['count']==0) {
            $q="DELETE FROM `".TblSysModComments."` WHERE id=".$this->idComment.""; 
            $res = $this->db->db_Query($q);
            //echo '$q ='.$q.' <br/>$res = '.$res.'<br/>';
            if(!$res OR !$this->db->result) 
                return 0;
            
            $q="DELETE FROM `".TblSysModComments."` WHERE cod=".$this->idComment.""; 
            $res = $this->db->db_Query($q);
            //echo '$q ='.$q.' <br/>$res = '.$res.'<br/>';
            /*if(!$res OR !$this->db->result) 
                return 0;*/
            
            return 1;
        }
        else 
            return -1;
}
    
       
    // ================================================================================================
    // Programmer : Yaroslav Gyryn
    // Date : 01.06.2011 
    // Reason for change : Reason Description / Creation
    // ================================================================================================
    function ShowCommentsTree($limit) 
    {                                    
        $tree = $this->LoadCommentsTreeList($limit);
        $this->showTree($tree);      
    }     

    // ------------------------------------------------------------------------------------------------  
    function LoadCommentsTreeList($limit)
    {
        $q = "SELECT 
                `".TblSysModComments."`.id,
                `".TblSysModComments."`.id_user,
                `".TblSysModComments."`.dt,
                `".TblSysModComments."`.level,
                `".TblSysModComments."`.text,
                `".TblSysModComments."`.rating,
                `".TblModUser."`.discount as img,
                `".TblModUser."`.name as first_name,
                `".TblModUser."`.country as second_name
            FROM 
                `".TblSysModComments."` 
            LEFT JOIN `".TblModUser."` ON (`".TblSysModComments."`.`id_user`=`".TblModUser."`.`sys_user_id`) 
            WHERE 
                `id_module`=".$this->module." 
            AND 
                `id_item`=".$this->id_item." 
            AND 
                `status`=1 
            ORDER BY 
                `dt` desc";
         /*if($limit) 
                $q = $q." limit ".$limit;*/
        $res = $this->db->db_Query($q);
        if(!$res OR !$this->db->result) 
            return false;
        //echo '$q ='.$q.' <br/>$res = '.$res.'<br/>';
        $rows = $this->db->db_GetNUmRows($res);   
        if($rows==0)   {
            return false;
        }
            
        $tree = array();
        for($i = 0; $i < $rows; $i++){
            $row = $this->db->db_FetchAssoc($res);
            if(empty($tree[$row['level']])) 
                $tree[$row['level']] = array();
            $tree[$row['level']][] = $row;
        }
       
       return $this->makeTree($tree, 0, $limit);   
    }

    // ------------------------------------------------------------------------------------------------  
    function makeTree(&$tree, $k_item = 0, $limit=9999)
    {
        if(empty($tree[$k_item])) 
            return array();
            
        $a_tree = array();
        $n = count($tree[$k_item]);
        for($i = 0; $i < $n; $i++) {
            if($i==$limit) 
                break;
            $row = $tree[$k_item][$i];
            $row['a_tree'] = $this->makeTree($tree, $tree[$k_item][$i]['id']);
            $a_tree[] = $row;
        }
        //print_r($a_tree);
        return $a_tree;
    }

    // ------------------------------------------------------------------------------------------------  
    function showTree(&$a_tree, $level = 0)
    {
        if(empty($a_tree)) 
            return;        
        
        $n = count($a_tree);
        $margin = 30*$level;

        for($i = 0; $i <$n; $i++) {
            $id= $a_tree[$i]['id'];
            $idUser = $a_tree[$i]['id_user'];
            $date = date("Y.m.d H:i", $a_tree[$i]['dt']);
            $comment =  $a_tree[$i]['text'];
            $rating = $a_tree[$i]['rating'];
            $image =  $a_tree[$i]['img'];
            if( !empty($a_tree[$i]['name']) ){
                 $username = stripslashes($a_tree[$i]['name']);
             }
             elseif( !empty($a_tree[$i]['second_name']) or !empty($a_tree[$i]['first_name'] )) {
                 $username = stripslashes($a_tree[$i]['second_name']).' '.stripslashes($a_tree[$i]['first_name']);
             }
             else {
                 if (empty($this->User)) 
                    $this->User = Singleton::getInstance('User');
                $username = $this->User->GetUserLoginByUserId($idUser);
             }
            ?>
            
            <div class="comment" style="margin-left:<?=$margin;?>px;">
                <div class="userPhoto left">
                    <?if($image != null) {?>
                        <img width="80" height="80" src="/images/mod_blog/<?=$idUser;?>/<?=$image;?>"/>
                    <?}
                        else {?>
                        <img width="80" height="80" src="/images/design/noAvatar.gif"/>
                        <?}
                    ?>
                </div>
                <div class="content">
                    <div style="overflow: hidden;">
                        <div class="name left"><?=$username;?></div>
                        <div class="date left"><?=$date?></div>
                     </div>
                    <div class="data">
                        <?=str_replace("\n", "<br/>",stripslashes($comment));?><br/>
                    </div>
                    <?$link="javascript:showForm('".$id."')";?>
                    <a class="reply" href="<?=$link?>" >Відповісти  &raquo;</a>
                    <?
                    $class='gray';
                    if($rating>0)
                        $class='blue';
                    elseif($rating<0)
                        $class='red';
                    ?>
                    <div id="rating_<?=$id?>" class="ratingBlock right">
                        <div  class="rating left <?=$class?>"><?=$rating;?></div>
                        <?if( $this->Logon->user_id){?>
                            <a title="+" href="#" href="#" onclick="Vote('/add_comments.php?task=vote&idComment=<?=$id;?>&idUser=<?=$this->Logon->user_id?>&vote=1',  'rating_<?=$id?>'); return false;" ><img src="/images/design/icoPlus.gif"></a>
                            <a title="-" href="#" onclick="Vote('/add_comments.php?task=vote&idComment=<?=$id;?>&idUser=<?=$this->Logon->user_id?>&vote=-1',  'rating_<?=$id?>'); return false;" ><img src="/images/design/icoMinus.gif"></a>
                        <?}
                        else {
                            ?><a class="icoVote" href="/registration/" title="Голосувати можуть лише зареєстровані користувачі"><img src="/images/design/icoVote.png"></a><?
                        }
                        ?>
                    </div>
                </div>
                
                <!-- Контейнер для виводу форми-->
                <div id="div_<?=$id;?>"></div>
            </div>
            <?$this->showTree($a_tree[$i]['a_tree'], ++$level);
        }
    }   

    
   // ================================================================================================
   // Function : PopularItems()
   // Date : 20.06.2011
   // Returns : true,false / Void
   // Description : Show form to leave Comments
   // Programmer : Yaroslav Gyryn
   // ================================================================================================
   function PopularItems($module=null, $limit = 10)    {
        if($module == null)
            $module = $this->module;
        $q="SELECT 
                    `id_item`,
                    count(id_item) as `count`
                FROM 
                    `".TblSysModComments."`
                WHERE 
                    `id_module`='".$module."'
                GROUP BY 
                    id_item
                ORDER BY 
                    `count` DESC 
                LIMIT ".$limit;
                
        $res = $this->db->db_Query($q);                
        if(!$res OR !$this->db->result) 
            return false;
        //echo '$q ='.$q.' <br/>$res = '.$res.'<br/>';
        $rows = $this->db->db_GetNUmRows($res);   
        if($rows==0)   
            return false;
            
        $arr = array();
        for($i = 0; $i < $rows; $i++){
            $arr[] = $this->db->db_FetchAssoc($res);
        }
        $str = null;
        for($i = 0; $i < $rows; $i++){
            if(empty($str))
                $str = $arr[$i]['id_item'];
            else
                $str = $str.','.$arr[$i]['id_item'];
        }
        return $str;
   }

   // ================================================================================================
   // Function : ShowCommentsFormHTML()
   // Date : 20.06.2011
   // Returns : true,false / Void
   // Description : Show form to leave Comments
   // Programmer : Yaroslav Gyryn
   // ================================================================================================
   function ShowCommentsFormHTML() 
   {
       $this->Form->WriteFrontHeader( NULL, '#', $task = 'save_response');
       $this->Form->Hidden('id_module', $this->module);
       $this->Form->Hidden('id_item', $this->id_item);
       /*if($this->task==NULL) $params = 'style="display:none;"';
       else $params = 'style="display:block;"'; */
     ?>
     <?/*<div id="commentsFormDtl" <?=$params;?>>*/?>
     <div id="commentsFormDtl">
       <?
       if($this->Err) {
       ?><div id="err" class="err"><?
        $this->Form->ShowErr($this->Err);
        ?></div><?
       }
       ?>
       <table border="0" cellpadding="2" cellspacing="2" style="font-size: 12px;">
        <?
        //echo '<br> $this->uselogon='.$this->uselogon.' $this->Logon->user_id='.$this->Logon->user_id;
        if( $this->uselogon==1){
         //if no user authorized
         if( !$this->Logon->user_id){
        ?>
        <tr>
         <td><?=$this->multi['FLD_LOGIN'];?>:&nbsp;<span class="inputRequirement">*</span></td>
         <td><?$this->Form->TextBox( 'login',$this->login, 'size="40"' );?></td>
        </tr>
        <tr>
         <td><?=$this->multi['FLD_PASSWORD'];?>:&nbsp;<span class="inputRequirement">*</span></td>
         <td><?$this->Form->Password( 'password',$this->password, "40" );?></td> 
        </tr>
        <?
         }
         else{
        ?>
        <tr>
         <td><?=$this->multi['TXT_FRONT_USER_NAME'];?>:&nbsp;<?$this->name = $this->Logon->login; ?><strong><?=$this->name;?></strong></td>
        </tr>
        <?
         }
        }
        else {
        ?> 
        <tr>
         <td><?=$this->multi['TXT_FRONT_USER_NAME'];?>:&nbsp;<span class="inputRequirement">*</span></td>
         <td align="left">
          <?$this->Form->TextBox( 'name', $this->name, 'size="40"' );?>
         </td>
        </tr>
        <tr>
         <td><?=$this->multi['TXT_FRONT_USER_EMAIL'];?>:&nbsp;<span class="inputRequirement">*</span></td>
         <td><?$this->Form->TextBox( 'email',$this->email, 'size="40"' );?></td> 
        </tr>
        <?}?>
        <tr>
         <td colspan="2">
          <?=$this->multi['TXT_FRONT_USER_COMMENT'];?>:&nbsp;<span class="inputRequirement">*</span><br/>
          <?$this->Form->TextArea( 'text', $this->text, 9, 55, NULL );?>
         </td>
        </tr>
        <?/*
        <tr>
         <td colspan="2"><b><?=$this->multi['TXT_FRONT_SPAM_PROTECTION'];?>:&nbsp;<span class="inputRequirement">*</span></b> <b><?=$this->multi['TXT_FRONT_SPAM_PROTECTION_SPECIFY_SUM'];?>&nbsp;<?=$v1;?>+<?=$v2;?>?</b> <?$this->Form->TextBox( 'usr_v', NULL, 'size="2"' );?></td>
        </tr>
        */?>
        <tr>
         <td colspan="2" align="left"><span class="inputRequirement">*</span> 
            <span class="needFileds"><?=$this->multi['TXT_FRONT_REQUIREMENT_FIELDS'];?></span>
         </td>
        </tr>
        <tr>
         <td colspan="2" align="left">
         <?$link="javascript:hideForm()";?>
         <a class="cancel" href="<?=$link?>">Відмінити</a>
         <?
         $params = 'onclick="AddComment('."'/add_comments.php'".', '."'div_'".'); return false;"';
         $this->Form->Button( 'save_response', $this->multi['TXT_FRONT_ADD_COMMENT'], $params );
         /*?><input type="submit" onclick="return verify();" class="button"  name="submit"><?*/
         if(!isset ($this->level))
            $this->level = 0;
         ?>
         <input type="hidden" name="level" id="cur_comment" value="<?=$this->level?>">
         </td>  
        </tr>         
       </table>
       <br/>
     </div>
        <?
        $this->Form->WriteFrontFooter();
   }//end of function ShowCommentsFormHTML()  

       
   
   // ================================================================================================
   // Function : ShowJS()
   // Date : 08.06.2011
   // Returns : true,false / Void
   // Description : show form with rating from users about goods
   // Programmer : Yaroslav Gyryn
   // ================================================================================================
   function ShowJS() 
   {
   ?><script type="text/javascript">
   function Vote(uri, div_id){
        idResp = div_id;
        Did = "#"+div_id;
        $.ajax
        ({
            url:uri,
            type: 'POST',
            dataType: 'html',
            success: function (img_upload, status)
            {                  
                //alert(img_upload);
                $(Did).html(img_upload).animate({
                  opacity: 'show'
                }, "slow", "easein");
            },
            error: function (img_upload, status)
            {
                $(Did).html(status).animate({
                  opacity: 'show'
                }, "slow", "easein");
            }
        });
    } // end of function AddComment   
    

   function showMainCommentForm() {
        var id_t =$("#cur_comment").val();
        if(id_t==0)
        {
            $("#resp_form").show();
        }
        else
        {
            $("#resp_form").html( $("#div_"+id_t).html() ); 
            $("#div_"+id_t).html( '' ); 
            $("#resp_form").show(); 
            $("#cur_comment").val(0); 
        }
    }  

    /*function hideMainCommentForm() {
        var id_t =$("#cur_comment").val();
        if(id_t==0) {
            $("#resp_form").hide();
            $("#commentLink").show();
        }
        else {
            $("#div_"+id_t).hide(); 
        }
    } */
    
    function showForm (id) {
        var id_t =$("#cur_comment").val();
        
        if(id_t==0)
        {
            $("#div_"+id).html( $("#resp_form").html() ); 
            $("#resp_form").html('');
            $("#div_"+id).show(  ); 
        }
        else
        {
            if(id!=id_t){
                $("#div_"+id).html( $("#div_"+id_t).html() ); 
                $("#div_"+id_t).html( '' ); 
            }
            $("#div_"+id).show(  ); 
        }
        //$("#err").html('');
        //$("#commentLink").hide();
        $("#cur_comment").val(id);
    }

    function hideForm () {
        var id_t =$("#cur_comment").val();
        if(id_t==0) 
            $("#resp_form").hide();
        else
            $("#div_"+id_t).hide(); 
        $("#commentLink").show();
            
    } 
    </script>
    
    <script type="text/javascript">
    var idResp;
    var dta;

    function ShowCommentsForm(uri, div_id){
        Did = "#"+div_id;
        $(Did).show("slow",function(){
            $(Did).css("display","block");
        });

    } // end of function ShowCommentsForm     
    
    function AddComment(uri, div_id){
        idResp = div_id;
        idForm = '<?=$this->Form->name?>';
        document.<?=$this->Form->name?>.task.value='add_comment';
        var id_t =$("#cur_comment").val();
        //alert(div_id + id_t );
        if(id_t==0)
            Did = "#resp_form";
        else
            Did = "#"+div_id+id_t;
        $.ajax
        ({
            url:uri,
            type: 'POST',
            dataType: 'html',
            data:$('#<?=$this->Form->name?>').serialize(),
            success: function (img_upload, status)
            {                  
                //alert(img_upload);
                $(Did).html(img_upload).animate({
                  opacity: 'show'
                }, "slow", "easein");
            },
            error: function (img_upload, status)
            {
                $(Did).html(status).animate({
                  opacity: 'show'
                }, "slow", "easein");
            }
        });
    } // end of function AddComment

    function DeleteComment(uri, div_id, id_comment){
        //alert(uri);
        Did = "#"+div_id+id_comment;
        $.ajax
        ({
            url:uri,
            type: 'POST',
            dataType: 'html',
            success: function (img_upload, status)
            {                  
                //alert(img_upload);
                $(Did).html(img_upload).animate({
                  opacity: 'show'
                }, "slow", "easein");
            },
            error: function (img_upload, status)
            {
                $(Did).html(status).animate({
                  opacity: 'show'
                }, "slow", "easein");
            }
        });
    } // end of function AddComment
        
    function ReloadComments(uri, div_id){
        uri = uri+'&task=show_comments';
        Did = "#"+div_id;
       
        $.ajax
        ({
            url:uri,
            type: 'POST',
            dataType: 'html',
            success: function (img_upload, status)
            {
                //$("#commentsList").empty();
                $(Did).hide("slow",function(){
                    //$(Did).empty();
                    $(Did).css("display","block");
                    $(Did).html(img_upload).animate({
                      height: 'show'
                    }, "slow", "easein");
                });
            },
            error: function (img_upload, status)
            {
                $(Did).html(status).animate({
                  opacity: 'show'
                }, "slow", "easein");
            }
        });
    } // end of function ReloadComments 
    </script> 
    <?
   } // end of functin ShowJS()

   

   // ================================================================================================
   // Function : GetCommentsCountItem()
   // Date : 08.08.2011
   // Returns : true,false / Void
   // Description : 
   // Programmer : Yaroslav Gyryn
   // ================================================================================================
    function GetCommentsCountItem()
    {
        $q = "SELECT 
                    COUNT(`".TblSysModComments."`.id) as count
                FROM 
                    `".TblSysModComments."` 
                WHERE 
                    `id_module`=".$this->module." 
                AND 
                    `id_item`=".$this->id_item." 
                AND
                    `status` = '1'
                ";
        $res = $this->db->db_Query($q);
        if(!$res OR !$this->db->result) 
            return false;
        //echo '$q ='.$q.' <br/>$res = '.$res.'<br/>';
        $row = $this->db->db_FetchAssoc($res);
        $count = 0;
        $count = $row['count'];
        return $count;
        }
        

   // ================================================================================================
   // Function : GetCommentsCountItem()
   // Date : 08.08.2011
   // Returns : true,false / Void
   // Programmer : Yaroslav Gyryn
   // ================================================================================================
    function GetCommentsCount($str = null)
    {
        $q = "SELECT 
                id_item,
            COUNT(`".TblSysModComments."`.id) as count
            FROM 
                `".TblSysModComments."` 
            WHERE 
                `id_module`=".$this->module."
            AND
                `status` = '1'
                ";
        
        if( $str != null ) {
            $q .= " AND id_item IN (".$str.") ";
        }
        $q .= "GROUP BY
                `id_item` DESC
        ";
        $res = $this->db->db_Query($q);
        //echo $q.' <br/>$res = '.$res.'<br/>';
        
        if(!$res or !$this->db->result) 
            return false;
        
        $rows = $this->db->db_GetNumRows();
        $arr = array();
        for( $i=0; $i<$rows; $i++ )
        {
            $row = $this->db->db_FetchAssoc($res);
            $arr[$row['id_item']] = $row['count'];
        }
        return $arr;
    }
                
           
   // ================================================================================================
   // Function : AddVote()
   // Date : 08.08.2011
   // Returns : true,false / Void
   // Description : Add Vote
   // Programmer : Yaroslav Gyryn
   // ================================================================================================
   function AddVote() {
      // Вибірка  к-сті голосів для коментаря
        $q = "SELECT  
                `".TblSysModComments."`.rating
            FROM 
                `".TblSysModComments."` 
            WHERE 
                `id`=".$this->idComment." 
        ";
        $res = $this->db->db_Query($q);
        //echo '<br>'.$q.' <br>$res='.$res.' $this->db->result='.$this->db->result;
        if( !$res OR !$this->db->result ) return false;
        $row = $this->db->db_FetchAssoc($res);
        $rating =  intval($row['rating']);
            
       // Перевірка чи користувач ще не голосував
       $q = "SELECT 
                    COUNT(`".TblSysModCommentsRating."`.id) as count
                FROM 
                    `".TblSysModCommentsRating."` 
                WHERE 
                    `sys_user_id`=".$this->idUser." 
                AND 
                    `cod`=".$this->idComment." 
            ";
        $res = $this->db->db_Query($q);
        //echo '<br>'.$q.' <br>$res='.$res.' $this->db->result='.$this->db->result;
        if( !$res OR !$this->db->result ) 
            return false;
        //$rows = $this->db->db_GetNUmRows($res);   
        $row = $this->db->db_FetchAssoc($res);
        if($row['count']==0)   {
            // Запис голоса в таблицю для користувача
            //echo '$this->vote = '.$this->vote;
            $q = "INSERT INTO 
                        `".TblSysModCommentsRating."` 
                    SET
                      `cod`='".$this->idComment."',
                      `sys_user_id`='".$this->idUser."',
                      `vote`='".$this->vote."'
            ";
            $res = $this->db->db_Query($q);
            //echo '<br>'.$q.' <br>$res='.$res.' $this->db->result='.$this->db->result;
            if( !$res OR !$this->db->result ) return false;
         
            $rating += $this->vote;
            
            // Оновлення рейтингу для коментаря
            $q = "UPDATE 
                        `".TblSysModComments."` 
                    SET
                        `rating` = '".$rating."'
                    WHERE 
                        `id` = '".$this->idComment."'
            ";
            $res = $this->db->db_Query($q);
            //echo '<br>'.$q.' <br>$res='.$res.' $this->db->result='.$this->db->result;
            if( !$res OR !$this->db->result ) return false;
        }
       
        return $rating;       
   }
   
   // ================================================================================================
   // Function : CheckFields()
   // Date : 08.08.2011
   // Returns : true,false / Void
   // Description : show form with rating from users about goods
   // Programmer : Yaroslav Gyryn
   // ================================================================================================
   function CheckFields() 
   {
        $this->Err = NULL;
        //echo '<br>$this->uselogon='.$this->uselogon.' $this->text='.$this->text;
        if($this->uselogon==1){
            //echo '<br>$this->Logon->user_id='.$this->Logon->user_id;
            if( !$this->Logon->user_id){
                if( empty($this->login) ){
                    $this->Err = $this->Err.$this->multi['MSG_FRONT_ERR_EMPTY_LOGIN_FIELD'].'<br/>';
                }
                if( empty($this->password) ){
                    $this->Err = $this->Err.$this->multi['MSG_FRONT_ERR_EMPTY_PASSWORD_FIELD'].'<br/>';
                }
                if( !empty($this->login) ANd !empty($this->password) ){
                    $this->Logon->user_valid( $this->login, $this->password, 1 );
                    $this->Err = $this->Err.$this->Logon->Err;
                    //$this->Logon->user_valid( $this->login, $this->password, 0 );
                }
            }
        }
        else{
            if( empty($this->name) ){
                $this->Err = $this->Err.$this->multi['MSG_FRONT_ERR_SPECIFY_YOUR_NAME'].'<br/>';
            }
            if( empty($this->email) ){
                $this->Err = $this->Err.$this->multi['MSG_FRONT_ERR_SPECIFY_YOUR_EMAIL'].'<br/>';
            }
        }
        if( $this->text==NULL ){
            $this->Err = $this->Err.$this->multi['MSG_FRONT_ERR_EMPTY_TEXT_FIELD'].'<br/>';
        }
        return $this->Err;
   }//end of function CheckFields()
   
    // ================================================================================================
    // Function : SaveComments()
    // Date : 26.08.2011
    // Returns :      true,false / Void
    // Description :  Save data to database
    // Programmer :  Yaroslav Gyryn
    // ================================================================================================    
    function SaveComments()
    {
        $this->dt = time();
        $this->status = 1;
        $q = "INSERT INTO `".TblSysModComments."` SET
              `id_module`='".$this->module."',
              `id_item`='".$this->id_item."',
              `level`='".$this->level."',
              `dt`='".$this->dt."',
              `status`='".$this->status."',
              `text`='".$this->text."',
              `id_user`='".$this->Logon->user_id."',
              `name`='".$this->name."',
              `email`='".$this->email."'
             ";
        $res = $this->db->db_Query($q);
        //echo '<br>$q='.$q.' $res='.$res.' $this->db->result='.$this->db->result;
        if( !$res OR !$this->db->result ) return false;
        return true;    
    }//end of function SaveComments()       
 

         // ================================================================================================
        // Function : SendResponseEmail()
        // Date : 13.06.2011
        // Returns : true,false / Void
        // Description : Send Response to Email
        // Programmer : Igor Trokhymchuk
        // ================================================================================================
        function SendResponseEmail(){
            $q = "SELECT 
                        `".TblModUser."`.email,
                        `".TblModUser."`.sys_user_id,
                        `".TblModUser."`.name as first_name,
                        `".TblModUser."`.country as second_name
                    FROM 
                        `".TblModUser."`, `".TblSysModComments."`
                    WHERE 
                        `".TblModUser."`.sys_user_id = `".TblSysModComments."`.id_user
                    AND
                        `".TblSysModComments."`.id_module = '".$this->module."' 
                    AND
                        `".TblSysModComments."`.id_item = '".$this->id_item."' 
                    AND
                        `".TblSysModComments."`.id = '".$this->level."' 
            ";
            $res = $this->db->db_Query($q);
            if(!$res OR !$this->db->result) 
                return false;
            //echo '<br/>'.$q.' <br/>$res = '.$res.'<br/>'; 

            $row = $this->db->db_FetchAssoc();
            $username ='';
            $email = $row['email'];
            $idUser = $row['sys_user_id'];
            if( !empty($row['second_name']) or !empty($row['first_name'] )) {
                 $username = stripslashes($row['second_name']).' '.stripslashes($row['first_name']);
            }
            else {
                if (empty($this->User)) 
                    $this->User = Singleton::getInstance('User');
                $username = $this->User->GetUserLoginByUserId($idUser);                 
            }
                 
            if(empty($email)) {
                if(empty($SysUser)) 
                    $SysUser = Singleton::getInstance('SysUser');
                $email = $SysUser->GetUserAliasByUserId($idUser);
            }
            
                 
            if(!empty($email)) {
                $link = $this->referer_page;
                $subject = '"Вам відповіли на залишений коментар"';
                $body = 'Доброго дня, '.$username.'! <br/>Вам відповіли на залишений коментар: <br/><a href='.$link.'>'.$link.'</a>';
                
                //================ send by class Mail START =========================
                $mail = new Mail();
                $mail->AddAddress($email);
                $mail->WordWrap = 500;
                $mail->IsHTML( true );
                //$mail->FromName = $name;
                $mail->Subject = $subject;
                $mail->Body = $body;
                if( !$mail->SendMail() )  {
                    echo "<h2 class='err'>Повідомлення не відправлено!</h2>";
                    return false;
                }
            }
            else 
                return false;
            return true;
        } //end of function SendResponseEmail()
        
   // ================================================================================================
   // Function : ShowCommentsForm()
   // Date : 20.08.2011
   // Returns : true,false / Void
   // Description : show form to leave comments
   // Programmer : Yaroslav Gyryn
   // ================================================================================================
   /*function ShowCommentsForm() 
   {
       $v1 = rand(1,9);
       $v2 = rand(1,9);
       $sum = $v1+$v2;            
       $url = '/add_comments.php?id_module'.$this->module.'&id_item='.$this->id_item; ; 
       //$this->ShowJS();
       //echo '$url  ='.$url;
       //$this->Form->WriteFrontHeader( NULL, $link, $task = 'save_response', 'onsubmit="return check_form_response(this, this.my_gen_v.value, '."'".$this->uselogon."'".', '."'".$this->Logon->user_id."'".' ); "' )
       $this->Form->WriteFrontHeader( NULL, '#', $task = 'save_response');
       $this->Form->Hidden('id_module', $this->module);
       $this->Form->Hidden('id_item', $this->id_item);
       //$this->Form->Hidden('my_gen_v', $sum);
       ?>
       <div id="commentsForm">
        <h2><a href="#" onclick="ShowCommentsForm('<?=$url;?>', 'commentsFormDtl');return false;"><?=$this->multi['TXT_FRONT_USER_COMMENT'];?></a></h2>
        <?$this->ShowCommentsFormHTML();?>
       </div>
       <?
       $this->Form->WriteFrontFooter();
   } //end of function ShowCommentsForm()   */


   // ================================================================================================
   // Function : ShowCommentsList()
   // Date : 26.08.2011
   // Returns : true,false / Void
   // Description : show list of comments
   // Programmer : Yaroslav Gyryn
   // ================================================================================================
   /*function ShowCommentsList() 
   {
        $q = "SELECT 
                    `".TblSysModComments."`. *,
                    `".TblModUser."`.discount as img,
                    `".TblModUser."`.name as first_name,
                    `".TblModUser."`.name as second_name
                FROM 
                    `".TblSysModComments."` 
                LEFT JOIN `".TblModUser."` ON (`".TblSysModComments."`.`id_user`=`".TblModUser."`.`sys_user_id`) 
                WHERE 
                    `id_module`=".$this->module." 
                AND 
                    `id_item`=".$this->id_item." 
                AND 
                    `status`=1 
                ORDER BY 
                    `dt` desc";
        $res = $this->db->db_Query($q);
        //echo '<br>'.$q.'<br/> $res='.$res.' $this->db->result='.$this->db->result;
        if( !$res OR !$this->db->result ) return false;
        $rows = $this->db->db_GetNumRows();
        ?><div id="commentsList" style="display:block;"><?
         if($rows>0){
          for($i=0;$i<$rows;$i++){
             $row = $this->db->db_FetchAssoc();
             //echo '$row[id_user] = '.$row['id_user'];
             $dt = date("Y.m.d H:i", $row['dt']);
             if( !empty($row['name'])){
                 $username = stripslashes($row['name']);
             }
             else{
                 //$username = $User->GetUserLoginByUserId($row['id_user']);                 
                 $username = stripslashes($row['second_name']).' '.stripslashes($row['first_name']);
             }
             ?>
             <div class="comment">
                <div class="userPhoto left">
                    <?if($row['img']!= null) {?>
                        <img width="80" height="80" src="/images/mod_blog/<?=$row['id_user']?>/<?=$row['img']?>"/>
                    <?}?>
                </div>
                <div class="content">
                    <div style="overflow: hidden;">
                        <div class="name left"><?=$username;?></div>
                        <div class="date left"><?=$dt?></div>
                     </div>
                    <div class="data">
                        <?=str_replace("\n", "<br/>",stripslashes($row['text']));?><br/>
                    </div>
                    <a class="reply" href="#">Відповісти  &raquo;</a>
                    <?
                    $class='gray';
                    if($row['rating']>0)
                        $class=='blue';
                    elseif($row['rating']<0)
                        $class=='red';
                        ?>
                        <div class="ratingBlock right">
                            <a title="-" href="#"><img src="/images/design/icoMinus.gif"></a>
                            <a title="+" href="#"><img src="/images/design/icoPlus.gif"></a>
                            <div  class="rating right <?=$class?>"><?=$row['rating']?></div>
                        </div>
                </div>
             </div>
             <?
          }//end for
         }//end if
         else{
             echo $this->multi['TXT_FRONT_COMMENTS_NULL'];
         }
         ?></div><?
        
   } //end of function ShowCommentsList()       */
   
   
   
   
   
   
   
   
   
   
   /*
   // ================================================================================================
   // Function : ShowCommentsByModuleAndItem()
   // Version : 1.0.0
   // Date : 26.08.2008
   // Parms :  
   // Returns : true,false / Void
   // Description : show list of comments
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 26.08.2008 
   // Reason for change : Reason Description / Creation
   // Change Request Nbr:
   // ================================================================================================
   function ShowCommentsByModuleAndItem() 
   {
        ?>
        <div id="commentsBlock"><?
         $this->ShowCommentsForm();
         $this->ShowCommentsList();?>
        </div>
        <?
   } //end of function ShowCommentsByModuleAndItem()         
   
   // ================================================================================================
   // Function : ShowCommentsList()
   // Version : 1.0.0
   // Date : 26.08.2008
   // Parms :  
   // Returns : true,false / Void
   // Description : show list of comments
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 26.08.2008 
   // Reason for change : Reason Description / Creation
   // Change Request Nbr:
   // ================================================================================================
   function ShowCommentsList() 
   {
        $q = "SELECT * FROM `".TblSysModComments."` WHERE `id_module`=".$this->module." AND `id_item`=".$this->id_item." AND `status`='1' ORDER BY `dt` desc";
        $res = $this->db->db_Query($q);
        //echo '<br>$q='.$q.' $res='.$res.' $this->db->result='.$this->db->result;
        if( !$res OR !$this->db->result ) return false;
        $rows = $this->db->db_GetNumRows();
        //echo '<br>$rows='.$rows;
        for($i=0;$i<$rows;$i++){
             $arr_data[$i] = $this->db->db_FetchAssoc();
        }
        ?>
        <div id="commentsList" style="display:block;">
         <?
         //echo '<br>$rows='.$rows;
         if($rows>0){?>
         <h2><?=$this->multi['TXT_FRONT_COMMENTS'];?>:</h2>
         <?
          for($i=0;$i<$rows;$i++){
             $row = $arr_data[$i];
             $dt = date("Y.m.d H:i", $row['dt']);
             if( !empty($row['name'])){
                 $username = stripslashes($row['name']);
             }
             else{
                 $User = new User();
                 $username = $User->GetUserLoginByUserId($row['id_user']);                 
             }
             ?>
             <div><?=$dt?>, <strong><?=$username;?></strong></div>
             <div><?=str_replace("\n", "<br/>",stripslashes($row['text']));?></div>
             <?
          }//end for
         }//end if
         else{
             echo $this->multi['TXT_FRONT_COMMENTS_NULL'];
         }
         ?>
        </div>
        <?
        
   } //end of function ShowCommentsList()
     
   // ================================================================================================
   // Function : ShowCommentsForm()
   // Version : 1.0.0
   // Date : 20.08.2008
   // Parms :  
   // Returns : true,false / Void
   // Description : show form to leave comments
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 20.08.2008 
   // Reason for change : Reason Description / Creation
   // Change Request Nbr:
   // ================================================================================================
   function ShowCommentsForm() 
   {
       $v1 = rand(1,9);
       $v2 = rand(1,9);
       $sum = $v1+$v2;            
       $url = '/add_comments.php?id_module'.$this->module.'&id_item='.$this->id_item; ; 
       $this->ShowJS();
       //$this->Form->WriteFrontHeader( NULL, $link, $task = 'save_response', 'onsubmit="return check_form_response(this, this.my_gen_v.value, '."'".$this->uselogon."'".', '."'".$Logon->user_id."'".' ); "' )
       $this->Form->WriteFrontHeader( NULL, '#', $task = 'save_response');
       $this->Form->Hidden('id_module', $this->module);
       $this->Form->Hidden('id_item', $this->id_item);
       $this->Form->Hidden('my_gen_v', $sum);
       ?>
       <div id="commentsForm">
       
        <h2><a href="#" onclick="ShowCommentsForm('<?=$url;?>', 'commentsFormDtl');return false;"><?=$this->multi['TXT_FRONT_USER_COMMENT'];?></a></h2>
        <?$this->ShowCommentsFormHTML();?>
       </div>
       <?
       $this->Form->WriteFrontFooter();
   } //end of function ShowCommentsForm()   

   // ================================================================================================
   // Function : ShowCommentsFormHTML()
   // Version : 1.0.0
   // Date : 20.08.2008
   // Parms :  
   // Returns : true,false / Void
   // Description : show form to leave comments
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 20.08.2008 
   // Reason for change : Reason Description / Creation
   // Change Request Nbr:
   // ================================================================================================
   function ShowCommentsFormHTML() 
   {
       $Logon = new UserAuthorize();
       if($this->task==NULL) $params = 'style="display:none;"';
       else $params = 'style="display:block;"'; 
     ?>
     <div id="commentsFormDtl" <?=$params;?>>
       <div style="width:100%;"><?$this->Form->ShowErr($this->Err);?></div>
       <table border="0" cellpadding="2" cellspacing="2">
        <?
        //echo '<br> $this->uselogon='.$this->uselogon.' $Logon->user_id='.$Logon->user_id;
        if( $this->uselogon==1){
         //if no user authorized
         if( !$Logon->user_id){
        ?>
        <tr>
         <td><?=$this->multi['FLD_LOGIN'];?>:&nbsp;<span class="inputRequirement">*</span></td>
         <td><?$this->Form->TextBox( 'login',$this->login, 'size="40"' );?></td>
        </tr>
        <tr>
         <td><?=$this->multi['FLD_PASSWORD'];?>:&nbsp;<span class="inputRequirement">*</span></td>
         <td><?$this->Form->Password( 'password',$this->password, "40" );?></td> 
        </tr>
        <?
         }
         else{
        ?>
        <tr>
         <td><?=$this->multi['TXT_FRONT_USER_NAME'];?>:&nbsp;<?$this->name = $Logon->login; ?><strong><?=$this->name;?></strong></td>
        </tr>
        <?
         }
        }
        else {
        ?> 
        <tr>
         <td><?=$this->multi['TXT_FRONT_USER_NAME'];?>:&nbsp;<span class="inputRequirement">*</span></td>
         <td align="left">
          <?$this->Form->TextBox( 'name', $this->name, 'size="40"' );?>
         </td>
        </tr>
        <tr>
         <td><?=$this->multi['TXT_FRONT_USER_EMAIL'];?>:&nbsp;<span class="inputRequirement">*</span></td>
         <td><?$this->Form->TextBox( 'email',$this->email, 'size="40"' );?></td> 
        </tr>
        <?}?>
        <tr>
         <td colspan="2">
          <?=$this->multi['TXT_FRONT_USER_COMMENT'];?>:&nbsp;<span class="inputRequirement">*</span><br/>
          <?$this->Form->TextArea( 'text', $this->text, 9, 60, NULL );?>
         </td>
        </tr>
        <?
        //<tr>
        // <td colspan="2"><b><?=$this->Msg->show_text('TXT_FRONT_SPAM_PROTECTION');?>:&nbsp;<span class="inputRequirement">*</span></b> <b><?=$this->Msg->show_text('TXT_FRONT_SPAM_PROTECTION_SPECIFY_SUM');?>&nbsp;<?=$v1;?>+<?=$v2;?>?</b> <?$this->Form->TextBox( 'usr_v', NULL, 'size="2"' );?></td>
        //</tr>
        ?>
        <tr>
         <td colspan="2" align="left"><span class="inputRequirement">*</span> <?=$this->multi['TXT_FRONT_REQUIREMENT_FIELDS'];?></td>
        </tr>
        <tr>
         <td colspan="2" align="left"><?
         $params = 'onclick="AddComment('."'/add_comments.php'".', '."'commentsFormDtl'".'); return false;"';
         $this->Form->Button( 'save_response', $this->multi['TXT_FRONT_ADD_COMMENT'], $params );?>
         </td>  
        </tr>         
       </table>
     </div>
        <?
   }//end of function ShowCommentsFormHTML()  
   
   
   // ================================================================================================
   // Function : ShowJS()
   // Version : 1.0.0
   // Date : 08.08.2007
   // Parms :  
   // Returns : true,false / Void
   // Description : show form with rating from users about goods
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 08.08.2007 
   // Reason for change : Reason Description / Creation
   // Change Request Nbr:
   // ================================================================================================
   function ShowJS() 
   {
       ?>
    <script type="text/javascript">
    var idResp;
    var dta;

    function ShowCommentsForm(uri, div_id){
        Did = "#"+div_id;
        $(Did).show("slow",function(){
            $(Did).css("display","block");
        });

    } // end of function ReloadComments     
    
    function AddComment(uri, div_id){
        idResp = div_id;
        idForm = '<?=$this->Form->name?>';
        document.<?=$this->Form->name?>.task.value='add_comment';
        Did = "#"+div_id;
        dta = $('#<?=$this->Form->name?>').formSerialize();
        
        $.ajax
        ({
            url:uri,
            type: 'POST',
            dataType: 'html',
            success: function (img_upload, status)
            {
                $(Did).html(img_upload).animate({
                  opacity: 'show'
                }, "slow", "easein");
            },
            error: function (img_upload, status)
            {
                $(Did).html(status).animate({
                  opacity: 'show'
                }, "slow", "easein");
            },
            data: dta
        });
    } // end of function AddComment
    
    function ReloadComments(uri, div_id){
        uri = uri+'&task=show_comments';
        Did = "#"+div_id;
       
        $.ajax
        ({
            url:uri,
            type: 'POST',
            dataType: 'html',
            success: function (img_upload, status)
            {
                //$("#commentsList").empty();
                $(Did).hide("slow",function(){
                    //$(Did).empty();
                    $(Did).css("display","block");
                    $(Did).html(img_upload).animate({
                      height: 'show'
                    }, "slow", "easein");
                });
            },
            error: function (img_upload, status)
            {
                $(Did).html(status).animate({
                  opacity: 'show'
                }, "slow", "easein");
            }
        });
    } // end of function ReloadComments 
    </script> 
    <?
   } // end of functin ShowJS()

   // ================================================================================================
   // Function : CheckFields()
   // Version : 1.0.0
   // Date : 08.08.2007
   // Parms :  
   // Returns : true,false / Void
   // Description : show form with rating from users about goods
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 08.08.2007 
   // Reason for change : Reason Description / Creation
   // Change Request Nbr:
   // ================================================================================================
   function CheckFields() 
   {
        $this->Err = NULL;
        //echo '<br>$this->uselogon='.$this->uselogon.' $this->text='.$this->text;
        if($this->uselogon==1){
            $Logon = new UserAuthorize(); 
            //echo '<br>$Logon->user_id='.$Logon->user_id;
            if( !$Logon->user_id){
                if( empty($this->login) ){
                    $this->Err = $this->Err.$this->multi['MSG_FRONT_ERR_EMPTY_LOGIN_FIELD'].'<br/>';
                }
                if( empty($this->password) ){
                    $this->Err = $this->Err.$this->multi['MSG_FRONT_ERR_EMPTY_PASSWORD_FIELD'].'<br/>';
                }
                if( !empty($this->login) ANd !empty($this->password) ){
                    $Logon->user_valid( $this->login, $this->password, 1 );
                    $this->Err = $this->Err.$Logon->Err;
                    //$Logon->user_valid( $this->login, $this->password, 0 );
                }
            }
        }
        else{
            if( empty($this->name) ){
                $this->Err = $this->Err.$this->multi['MSG_FRONT_ERR_SPECIFY_YOUR_NAME'].'<br/>';
            }
            if( empty($this->email) ){
                $this->Err = $this->Err.$this->multi['MSG_FRONT_ERR_SPECIFY_YOUR_EMAIL'].'<br/>';
            }
        }
        if( $this->text==NULL ){
            $this->Err = $this->Err.$this->multi['MSG_FRONT_ERR_EMPTY_TEXT_FIELD'].'<br/>';
        }
        return $this->Err;
   }//end of function CheckFields()
   
    // ================================================================================================
    // Function : SaveComments()
    // Version : 1.0.0
    // Date : 26.08.2008
    //
    // Parms :
    // Returns :      true,false / Void
    // Description :  Save data to database
    // ================================================================================================
    // Programmer :  Igor Trokhymchuk
    // Date : 26.08.2008
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================    
    function SaveComments()
    {
        $Logon = new UserAuthorize();
        $this->dt = time();
        $this->status = 1;
        $q = "INSERT INTO `".TblSysModComments."` SET
              `id_module`='".$this->module."',
              `id_item`='".$this->id_item."',
              `dt`='".$this->dt."',
              `status`='".$this->status."',
              `text`='".$this->text."',
              `id_user`='".$Logon->user_id."',
              `name`='".$this->name."',
              `email`='".$this->email."'
             ";
        $res = $this->db->db_Query($q);
        //echo '<br>$q='.$q.' $res='.$res.' $this->db->result='.$this->db->result;
        if( !$res OR !$this->db->result ) return false;
        return true;    
    }//end of function SaveComments() 
    */      
 
 }//end of class FrontComments    