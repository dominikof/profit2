<?php
 // ================================================================================================
 // System : CMS
 // Module : Database
 // Version : 1.0.0
 // Date : 15.03.2005
 // Licensed To:
 // Igor Trokhymchuk ihoru@mail.ru
 // ================================================================================================

 // ================================================================================================
 //    Class             : FrontForm
 //    Date              : 13.02.2005
 //    Constructor       : Yes
 //    Returns           : None
 //    Description       : Class definition for describe input fields on front-end
 //    Programmer        :  Igor Trokhymchuk, Andriy Lykhodid
 // ================================================================================================
 class FrontForm extends Form
 {
    // ================================================================================================
    //    Function          : FrontForm (Constructor)
    //    Date              : 26.01.2005
    //    Description       : FrontForm Designer (Show Form Header, Footer and Content)
    // ================================================================================================
    Function FrontForm ( $nameform = 'f1' )
    {
        $this->name = $nameform;
        $this->multi = &check_init_txt('TblFrontMulti', TblFrontMulti);
    } //end of Constructor FrontForm() 
        
    // ================================================================================================
    // Function : WriteFrontHeader()
    // Date : 15.04.2005
    // Returns : true,false / Void
    // Description : Write Form Header
    // Programmer : Andriy Lykhodid
    // ================================================================================================
    function WriteFrontHeader( $name = '', $script = '', $task = '', $params = '' )
    {
      if(empty($name)) $name = $this->name;
      ?>
      <form id="<?=$name;?>" name="<?=$name;?>" action="<?=$script;?>" method="post" enctype="multipart/form-data" <?=$params;?>>
      <input type="hidden" name="task" value="<?=$task;?>"/>
      <?
    } //--- end of WriteFrontHeader()    

    // ================================================================================================
    // Function : WriteFrontFooter()
    // Date : 15.04.2005
    // Returns : true,false / Void
    // Description : Write Form Footer
    // Programmer : Andriy Lykhodid
    // ================================================================================================
    function WriteFrontFooter()
    {
    ?>
    </form>
    <?
    } //--- end of WriteFrontFooter()


    // ================================================================================================
    // Function : Radio()
    // Date : 15.04.2005
    // Parms :       $arr, $value, $property, $javascript
    // Description : Write Radio
    // Programmer : Andriy Lykhodid
    // ================================================================================================

    function Radio( $name = NULL, $value = NULL, $valuech = NULL,  $txt = NULL, $params=NULL )
    {
    ?><input type="radio" class="radio" name="<?=$name;?>" value="<?=$value;?>" <?if( $valuech == $value )echo 'checked';?> <?if(!empty($params)) echo $params;?> ><?=$txt;?><?
    } //--- end of Radio()


    // ================================================================================================
    // Function : TextBox()
    // Date : 26.01.2005
    // Description : Write Text Box
    // Programmer : Andriy Lykhodid
    // ================================================================================================
    function TextBox( $name = '', $value = '', $params = '' )
    {
        echo '<input type="text" class="CatinputFromForm" name="'.$name.'" value="'.htmlspecialchars($value).'" ';
        if (!empty($params)) echo $params;
        echo "/>";
    }

    // ================================================================================================
    // Function : CheckBox()
    // Date : 26.01.2005
    // Description : Write CheckBox
    // Programmer : Andriy Lykhodid
    // ================================================================================================
    function CheckBox( $name = 'id_del[]', $value = '', $sel = NULL, $params = NULL )
    {
    ?>
    <INPUT TYPE="checkbox" name="<?=$name;?>" value="<?=$value;?>" <?if( $sel )echo ' checked';?> <?if(!empty($params)) echo $params;?> />
    <?
    }

    // ================================================================================================
    // Function : TextArea()
    // Date : 26.01.2005
    // Description : Write Text Area
    // Programmer : Andriy Lykhodid
    // ================================================================================================
    function TextArea( $name = '', $value = '', $rows = 4, $cols = 70, $params = NULL )
    {
    ?>
    <textarea name=<?=$name;?> rows="<?=$rows;?>" cols="<?=$cols;?>" <?if(!empty($params)) echo $params;?> class="textarea"><?=htmlspecialchars($value);?></textarea>
    <?
    }
	  
    // ================================================================================================
    // Function : Password()
    // Date : 29.01.2005
    // Description : Write input type=hidden
    // Programmer : Andriy Lykhodid
    // ================================================================================================
    function Password( $name = '', $value = '', $size = '', $param=NULL )
    {
    if(!$size){$size = 10;}
    ?>
    <input type="password" <?=$param?> class="CatinputFromForm" name="<?=$name;?>" value="<?=$value;?>" size="<?=$size;?>"/>
    <?
    }      

    // ================================================================================================
    // Function : Hidden()
    // Date : 27.01.2005
    // Description : Write input type=hidden
    // Programmer : Andriy Lykhodid
    // Change Request Nbr:
    // ================================================================================================
    function Hidden( $name = '', $value = '', $params=NULL )
    {
    ?>
    <input type="hidden" name="<?=$name;?>" value="<?=htmlspecialchars($value);?>"/>
    <?
    }

    // ================================================================================================
    // Function : Button()
    // Date : 27.01.2005
    // Description : Write input type=hidden
    // Programmer : Andriy Lykhodid
    // ================================================================================================
    function Button( $name = '', $value = '', $params = NULL )
    {
    ?>
    <input type="submit" name="<?=$name?>" value="<?=$value?>" class="button" <?if($params) echo $params;?>/>
    <?
    }

    // ================================================================================================
    // Function : Select()
    // Date : 26.01.2005
    // Description : Write Select Box
    // Programmer : Andriy Lykhodid
    // ================================================================================================
    function Select( $arr, $name = '', $value = NULL, $width = NULL ,$param)
    {
    ?>
    <select <?=$param?> name="<?=$name;?>" width="<?=$width?>">
    <?
    while( $el=each( $arr ) )
    {
   
    if( $el['key']==$value ) echo '<option value="'.$el['key'].'" selected>'.$el['value'].'</option>';
    else echo '<option value="'.$el['key'].'">'.$el['value'].'</option>';
    }
    ?>
    </select>
    <?
    }

    // ================================================================================================
    // Function : spr_get
    // Date : 12.01.2005
    // Parms : $Table    - table, from whith data will be shown
    //         $name_fld - name of field
    //         $val      - value of field
    //         $width    - width of field
    // Returns : true
    // Description : show the list of the records from table to combobox
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function spr_get( $Table, $name_fld, $val, $width )
    {
    $Rights = new Rights();
    if (empty($name_fld)) $name_fld=$Table;
    if ($width==0) $width=250;

     $q = "select * from `".$Table."` where lang_id='"._LANG_ID."'";
     $res = $Rights->db_Query( $q );
     if (!$Rights->result) return false;
     $rows1=$Rights->db_GetNumRows();
     /*
     for( $i = 0; $i < $rows1; $i++ )
     {
      $row1 = $Rights->db_FetchAssoc();
      $mas_sel[$row1['cod']]=$row1['name'];
     }
       */
     //$this->Select($mas_sel, $name_fld, $val, $width);

     echo '<select style="width:'.$width.'" name='.$name_fld.'>';
     echo '<option value="">';
     for( $i = 0; $i < $rows1; $i++ )
     {
      $row1 = $Rights->db_FetchAssoc();
      //echo ' '.$i.' '.$val. ' = '.$row1['id'];
      echo '<option value="'.$row1['cod'].'"';
      if (!empty($val) and ($val == $row1['cod'])) echo ' selected>'.$row1['name'];
      else echo '">'.$row1['name'];
     }

     /*
     if ( (empty($val)) & ($Table!='sex') & ($Table!='sys_spr_day') & ($Table!='sys_spr_mounth') & ($Table!='sys_spr_years')) {
	     echo '<OPTION value="0" SELECTED>'; echo show_text('_value_NOT_SET');
     }
     */
     echo '</select>';
    }

    // ================================================================================================
    // Function : checkbox_spr_get
    // Date : 13.01.2005
    // Parms :   $Table    - table, from whith data will be shown
    //           $name_fld - name of field
    //           $cols     - count of checkboxes in one row
    //           $val      - value of checkbox
    // Returns : true
    // Description : show the list of the records from table to checkbox
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function checkbox_spr_get( $Table, $name_fld, $cols, $val )
    {
    $Rights = new Rights();
    $row1 = NULL;
    if (empty($name_fld)) $name_fld=$Table;
    $q = "select * from `".$Table."` where lang_id='"._LANG_ID."'";
    $res = $Rights->db_Query( $q );
    if (!$Rights->result) return false;
    $rows1=$Rights->db_GetNumRows();
    $col_check=1;
    echo '<table border=1><tr>';
    for( $i = 0; $i < $rows1; $i++ )
    {
       $row1 = $Rights->db_FetchAssoc();
       if ($col_check > $cols) {
	      echo '</tr><tr>';
	      $col_check=1;
       }

       echo "\n<td align='right' width='140'>".$row1['name']."<input class='checkbox' type='checkbox' name=".$name_fld."[] value='".$row1['cod']."'";
       //echo '<br>count='.count($val);
       for( $j = 0; $j < count($val); $j++ )
       {
	      //echo '<br>i='.$j.' val['.$j.']='.$val[$j].' $row1[cod]='.$row1['cod'];
	      if (isset($val[$j]) and ($val[$j]==$row1['cod'])) echo " checked";
       }
       echo "/></td></td>";
       $col_check++;
    }
    echo '</tr></table>';
    echo "<input type='hidden' name='$name_fld'/>";
    }

    // ================================================================================================
    // Function : Link()
    // Date : 25.04.2005
    // Description : Write Link
    // Programmer : Andriy Lykhodid
    // ================================================================================================
    function Link( $script = '', $name = 'link', $hint = NULL )
    {
    ?>
    <a href="<?=$script;?>" <? if( $hint )echo "onmouseover=\"return overlib('$hint',WRAP);\" onmouseout=\"nd();\""; ?> class="arch_polls"><?=$name?></a>
    <?
    } //--- end of Link
	  
    // ================================================================================================
    // Function : ShowMenuBtn
    // Date :     29.11.2006 
    // Returns :  true,false / Void
    // Description : Show menu button
    // Programmer : Igor Trohkymchuk
    // ================================================================================================
    function ShowMenuBtn( $text, $pos=NULL, $href=NULL )
    {
      ?>
      <table border="0" cellpadding="0" cellspacing="0">
       <tr>
	    <td>
	     <?
	     if ($pos=='first') { ?><img src="images/design/btn_left_first.gif" border="0" alt="<?=$text;?>" title="<?=$text;?>"/><?}
	     else { ?><img src="images/design/btn_left.gif" border="0" alt="<?=$text;?>" title="<?=$text;?>"/><?}
	     ?> 
	    </td>
	    <td class="btn_menu" width="96"><a href="<?=$href;?>" class="menu" title="<?=$text;?>"><?=$text;?></a></td>
	    <td>
	     <?
	     if ($pos=='last') { ?><img src="images/design/btn_right_last.gif" border="0" alt="" title=""/><?}
	     else { ?><img src="images/design/btn_right.gif" border="0" alt="" title=""/><?}
	     ?> 
	    </td>
       </tr>
      </table>
      <?
    } // end of function ShowMenuBtn()


    // ================================================================================================
    // Function : ShowButton
    // Date :     29.11.2006 
    // Returns :  true,false / Void
    // Description : Show menu button
    // Programmer : Igor Trohkymchuk
    // ================================================================================================
    function ShowButton( $text=NULL, $href=NULL, $params = NULL, $onClick=NULL )
    {
      /*  
      ?>
      <div id="btn1"><img src="images/design/button_left.png" border=0 alt="<?=$text;?>" title="<?=$text;?>"></div>  
      <div id="btn2"><a href="<?=$href;?>" class="button" title="<?=$text;?>"><?=$text;?></a></div>
      <div id="btn1"><img src="images/design/button_right.png" border=0 alt="<?=$text;?>" title="<?=$text;?>"></div>  
      <?
      */
      if (!strstr($params,"width")) $width="width='50'";
      else $width = NULL;
      ?> 
      <table border="0" cellpadding="0" cellspacing="0" <?=$width.' '.$params;?>>
       <tr>
	    <td><img src="images/design/button_left.png" border="0" alt="<?=$text;?>" title="<?=$text;?>"/></td>
	    <td class="button_all" width="100%"><a href="<?=$href;?>" onClick="<?=$onClick;?>" class="button" title="<?=$text;?>"><?=$text;?></a></td>
	    <td><img src="images/design/button_right.png" border="0" alt="" title=""/></td>
       </tr>
      </table>
      <?
      
    } // end of function ShowButton() 

    // ================================================================================================
    // Function : ShowTextBlock
    // Date :     29.11.2006 
    // Returns :  true,false / Void
    // Description : Show menu button
    // Programmer : Igor Trohkymchuk
    // ================================================================================================
    function ShowTextBlock( $text, $width = '50', $height = '50' )
    {
	    $width_column = $width-41;
	    ?>
      <table border="0" cellpadding="0" cellspacing="0" width="<?=$width;?>">
       <tr>
	    <td><img src="images/design/spacer.gif" border="0" alt="" title=""/></td>
	    <td><img src="images/design/block_head_0_2.jpg" border="0" alt="" title=""/></td>
	    <td></td>
	    <td></td>
       </tr>   
       <tr>
	    <td><img src="images/design/block_head_1_1.jpg" border="0" alt="" title=""/></td>
	    <td><img src="images/design/block_head_1_2.jpg" border="0" alt="" title=""/></td>
	    <td class="bg_text_block_head_1" width="<?=$width;?>"><img src="images/design/spacer.gif" width="<?=$width;?>" height="26" border="0" alt="" title=""/></td>
	    <td><img src="images/design/block_head_1_4.jpg" border="0" alt="" title=""/></td>
       </tr>
       <tr>
	    <td class="bg_text_block_left"><img src="images/design/spacer.gif" border="0" alt="" title=""/></td>
	    <?//<td class="bg_text_block_content"></td>?>
	    <td class="bg_text_block_content" colspan="2" height="<?=$height;?>"><?=$text;?></td>
	    <td class="bg_text_block_right"><img src="images/design/spacer.gif" border="0" alt="" title=""/></td>
       </tr>
       <tr>
	    <td><img src="images/design/block_footer_1.jpg" border="0" alt="" title=""/></td>
	    <td class="bg_text_block_footer" colspan="2"><img src="images/design/spacer.gif" border="0" alt="" title=""/></td>
	    <td><img src="images/design/block_footer_3.jpg" border="0" alt="" title=""/></td>
       </tr>
      </table>
      <?
    } // end of function ShowTextBlock()     

    // ================================================================================================
    // Function : ShowButtonProfile
    // Date :     29.11.2006 
    // Returns :  true,false / Void
    // Description : Show menu button
    // Programmer : Igor Trohkymchuk
    // ================================================================================================
    function ShowButtonProfile( $text=NULL, $href=NULL, $params = NULL )
    {
      if (!strstr($params,"width")) $width="width='50'";
      else $width = NULL; 
       
      ?>
      <table border="0" cellpadding="0" cellspacing="0" <?=$width.' '.$params;?> align="center">
       <tr>
	    <td class="left_column"><img src="images/design/button_profile_left.gif" border="0" alt="<?=$text;?>" title="<?=$text;?>"/></td>
	    <td class="button_profile" width="100%"><a href="<?=$href;?>" class="a_button_profile" title="<?=$text;?>">&nbsp;<?=$text;?></a></td>
	    <td class="left_column"><img src="images/design/button_profile_right.gif" border="0" alt="" title=""/></td>
       </tr>
      </table>
      <?
    } // end of function ShowButtonProfile() 

    // ================================================================================================
    // Function : ShowButtonProfileMenu
    // Date :     30.01.2007 
    // Returns :  true,false / Void
    // Description : Show menu button
    // Programmer : Igor Trohkymchuk
    // ================================================================================================
    function ShowButtonProfileMenu( $text=NULL, $href=NULL, $params=NULL )
    {
      if (!strstr($params,"width")) $width="width=152";
      else $width = NULL; 
      
      /*
      ?>
      <div id="button_profile_menu" <?=$params;?>><a href="<?=$href;?>"><?=$text;?></a></div> 
      <?
      */
      $class="";
      ?>
      <table border="0" cellpadding="0" cellspacing="0" <?=$width.' '.$params;?> align="center" class="button_profile_menu">
       <tr>
	    <td><a href="<?=$href;?>" class="<?=$class;?>"><?=$text;?></a></td>
       </tr>
      </table>   
      <?
      
    } // end of function ShowButtonProfileMenu() 


    // ================================================================================================
    // Function : WriteLinkPagesFront()
    // Date : 26.10.2007
    // Returns : true,false / Void
    // Description : Write Link Pages
    // Programmer : Alex Kerest
    // ================================================================================================
    function WriteLinkPagesFront( $scriptact, $rows, $display, $start, $sort )
    {               
     $scriptD = $scriptact."&amp;start=".$start."&amp;sort=".$sort;
     if(strpos($scriptD, "?")!==false) $sin = '';
     else $sin = '?';
     $sh = 1;
     $na = 2;
     $flag1 = 1;
     $flag2 = 1;
     $p0 = 0;
     $end = round($rows/$display, 2);
     //echo "<br> rows/display = ".$end; 
     if($end<=1) return;
     ?>
     <table border="0" cellpading="2" cellspacing="0" width="100%">
      <tr>
       <td align=left style="padding:5px;">
       <?
    //   echo "<br> rows = ".$rows;
    //   echo "<br> display = ".$display;
      // echo "<br> start = ".$start;
       
     //  echo "<br> rows/display = ".$end;
       $curr = round($start/$display, 0) +1;
      // echo "<br> current page = ".$curr;
     //  echo "<br>";
       
        if(!$start || $start==''){
            echo '<span class="lnk_page">Пред.</span> ';     
        }
        else{
            echo '<a href="'.$scriptact.$sin.'&amp;start='.($start-$display).'&amp;display='.$display.'" class="lnk_page">Пред.</a> ';
        }
        $start_=0;
        $end_=0;
        $t_end = round($rows/$display, 0);
        for( $i=0; $i<($rows/$display); $i++){
         $p = $i+1;
       // echo "<br> i = ".$i;
      //  echo " p0 = ".$p0;
       // echo " p = ".$p;
               
                if($p0==$p) { continue; }
                $start_=$end_;
                 if( ( $end_+$display) > $rows ) $end_=$rows;
                else $end_=$end_+$display;
                $script = $scriptact.$sin."&display=$display&start=$start_&sort=$sort";
                
                if($p<=$na+$sh) {
                 
                 if($p==$curr) { echo '<b class="LinkPagesSel">'.$p.'</b>  '; 
                                if($end<=$sh) continue;
                                }
                  else {
                   echo '<a href="'.$script.'" title="Перейти на страницу '.$p.'" class="lnk_page">'.$p.'</a> ';
                   if($end<=$sh) continue; 
                   }
                   
                } else {
                    if($flag1==1 and $na+$sh<=$curr-$sh-$sh-1 and $p>$sh+2 and $p<$end-$sh) { echo " ... "; $flag1 =0; }
                }
               // echo "<br> sh = ".$sh;
              //  echo "<br> na = ".$na;
               // echo "<br> curr = ".$curr;
                if($p>=$curr-$sh and $p<=$curr+$sh and $p>$na+$sh and $p<$end-$sh and $p>($sh+1)){
                  if($p==$curr) { echo '<b class="LinkPagesSel">'.$p.'</b>  ';  }
                  else {
                      echo '<a href="'.$script.'" title="Перейти на страницу '.$p.'" class="lnk_page">'.$p.'</a>  ';
                   }
                  if($p>=$curr+$sh and $flag2==1 and $end-$sh>=$curr+$sh+$sh+2) { echo " ... "; $flag2 =0; }
                } else {
                 if($curr<$sh+$na and $flag2==1 and $p>=$curr+$sh and $p>$sh+2) { echo " ... "; $flag2 =0; }
                }
                
                
                                                                 
                if($p>=$end-$sh and $p>$sh+2) {
                 if($p==$curr) echo '<b class="LinkPagesSel">'.$p.'</b> '; 
                  else {
                     echo '<a href="'.$script.'" title="Перейти на страницу '.$p.'" class="lnk_page">'.$p.'</a> ';
                   }
                }
          $p0=$p;
        }
        ?>&nbsp;<?
        if(($display+$start)>=$rows){
            echo ' <span class="lnk_page"> След.</span>';
        }
        else{
            ?>&nbsp;<a href="<?=$scriptact.$sin;?>&amp;start=<?=($start+$display);?>&amp;display=<?=$display;?>" class="lnk_page">След.</a><?
        }
       ?>
       </td>
      <? // <td align="right" width="80">по:&nbsp;<?$this->WriteSelectCountRows( $display, "?".$scriptD, $rows, 'class="select"' ); ?>
      </tr>
     </table>
     <?
    }//end of function WriteLinkPagesFront()


    /**
     * FrontForm::WriteLinkPagesStatic()
     * @author Yaroslav
     * @param mixed $scriptact
     * @param mixed $rows
     * @param mixed $display
     * @param mixed $start
     * @param mixed $sort
     * @param mixed $page
     * @param mixed $param_url
     * @return
     */
    function WriteLinkPagesStatic( $scriptact, $rows, $display, $start, $sort, $page, $param_url=null )
    {
     $sh = 1;
     $na = 2;
     $flag1 = 1;
     $flag2 = 1;
     $p0 = 0;
     //echo "<br> page = ".$page;
     //echo '<br/>rows ='.$rows;
     //echo '<br/>$display ='.$display;
     $end = round($rows/$display, 2);
     if($end<=1) return;
     $goToPage = $this->multi['TXT_FRONT_GO_PAGE'];
     ?>
     <table border="0" cellpadding="2" cellspacing="0" align="center" class="pagesTable">
      <tr>
          <td>
              <?=$this->multi['FLD_PAGE'];?>:
          </td>
       <td align="left">
           <?
           $curr = round($start/$display, 0) +1;
           if(!$start || $start==''){
                ?><?
           }
           else{
                if($page-1==1) $link_prevpage= $scriptact;
                else $link_prevpage= $scriptact.'page'.($page-1).'/';
                if(!empty($param_url)) $link_prevpage .=  $param_url;
                ?><a href="<?=$link_prevpage;?>" class="lnk_page">←&nbsp;<?=$this->multi['TXT_FRONT_PREVIOUS'];?></a>&nbsp;<?
           }
           $start_=0;
           $end_=0;
           $t_end = round($rows/$display, 0);
           for( $i=0; $i<($rows/$display); $i++){
             $p = $i+1;
             if($p0==$p) { continue; }
             $start_=$end_;
             if( ( $end_+$display) > $rows ) $end_=$rows;
             else $end_=$end_+$display;
             
             if($p==1) $script = $scriptact;
             else $script = $scriptact."page".$p.'/';
             if(!empty($param_url)) $script = $script.$param_url;
             if($p<=$na+$sh){
                 if($p==$curr){
                     ?><b class="LinkPagesSel"><?=$p;?></b>&nbsp;<?
                     if($end<=$sh) continue;
                 }
                 else {
                     ?><a href="<?=$script;?>" title="<?=$goToPage;?> <?=$p;?>" class="lnk_page"><?=$p;?></a>&nbsp;<?
                     if($end<=$sh) continue; 
                 }
               
             }
             else{
                if($flag1==1 and $na+$sh<=$curr-$sh-$sh-1 and $p>$sh+2 and $p<$end-$sh) { echo '<div class="pagesPoints"> ... </div>'; $flag1 =0; }
             }
             if($p>=$curr-$sh and $p<=$curr+$sh and $p>$na+$sh and $p<$end-$sh and $p>($sh+1)){
                 if($p==$curr){?><b class="LinkPagesSel"><?=$p;?></b><?}
                 else {?><a href="<?=$script;?>" title="<?=$goToPage;?> <?=$p;?>" class="lnk_page"><?=$p;?></a>&nbsp;<?}
                 if($p>=$curr+$sh and $flag2==1 and $end-$sh>=$curr+$sh+$sh+2) { echo '<div class="pagesPoints"> ... </div>'; $flag2 =0; }
             }
             else{
                 if($curr<$sh+$na and $flag2==1 and $p>=$curr+$sh and $p>$sh+2) { echo '<div class="pagesPoints"> ... </div>'; $flag2 =0; }
             }
                                                 
             if($p>=$end-$sh and $p>$sh+2){
                 if($p==$curr) {?><b class="LinkPagesSel"><?=$p;?></b>&nbsp;<?}
                 else{?><a href="<?=$script;?>" title="<?=$goToPage;?> <?=$p;?>" class="lnk_page"><?=$p;?></a>&nbsp;<?}
             }
             $p0=$p;
           }
           
           if(!empty($param_url)) $script = $scriptact.'page'.($page+1).'/'.$param_url;
           else $script = $scriptact.'page'.($page+1).'/';
           
           if(($display+$start)>=$rows){?><?}
           else{?><a href="<?=$script;?>" class="lnk_page"><?=$this->multi['TXT_FRONT_NEXT'];?>&nbsp;→</a><?}
           ?>
         </td>
         
      </tr>
     </table>
     <div class="clear"></div>
     <?
    }//end of function WriteLinkPagesStatic()

    // ================================================================================================
    // Function : ShowTextMessages()
    // Date : 06.06.2007
    // Returns :      true,false / Void
    // Description :  Show text messages
    // Programmer :  Igor Trokhymchuk
    // ================================================================================================
    function ShowTextMessages($txt=NULL)
    {
        if ($txt){
            ?>
            <div align="center">
             <div class="msg_box">
              <table border="0" cellspacing="0" cellpadding="0" align="center">
               <tr><td class="msg_text"><?=$txt;?></td></tr>
              </table>
             </div>
            </div>
            <?
        }
    } //end of fuinction ShowTextMessages()

    // ================================================================================================
    // Function : ShowTextWarnings()
    // Date : 16.05.2008
    // Returns :      true,false / Void
    // Description :  Show text warnings
    // Programmer :  Igor Trokhymchuk
    // ================================================================================================
    function ShowTextWarnings($txt=NULL)
    {
        if( !empty($txt) ){
           ?>
           <div align="center"> 
            <div class="wrn_box">
             <table border="0" cellspacing="0" cellpadding="0" align="center">
              <tr><td class="wrn_text"><?=$txt;?></td></tr>
             </table>
            </div>
           </div>
           <?            
        }
    } //end of fuinction ShowTextWarnings()
    
    // ================================================================================================
    // Function : ShowErr()
    // Date : 10.01.2006
    // Returns :      true,false / Void
    // Description :  Show errors
    // Programmer :  Igor Trokhymchuk
    // ================================================================================================
    function ShowErr($txt=NULL)
    {
     if ($txt){
       ?>
       <div align="center">
        <div class="err_box">
         <table border="0" cellspacing="0" cellpadding="0" align="center">
          <tr><td class="err_title"><?=$this->multi['MSG_PAY_ATTENTION'];?></td></tr>
          <tr><td class="err_text"><?=$txt;?></td></tr>
         </table>
        </div>
       </div>
       <?
     }
    } //end of fuinction ShowErr()

    
    // ================================================================================================
    // Function : WriteContentHeader()
    // Date : 15.04.2011
    // Returns : true,false / Void
    // Description : Write Content  Header
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function WriteContentHeader( $h1 = null, $title = null, $path=null)
    {
        ?><div id="content2Box"><?
        if($h1) {
            ?><h1><?=$h1;?></h1><?
        }
        if($title) {
            ?><div class="title"><?=$title;?></div><?
        }
        if($path) {
            ?><div class="path"><?=$path;?></div><?
        }
        
    } //--- end of WriteContentHeader()   

    // ================================================================================================
    // Function : WriteContentHeader()
    // Date : 15.04.2011
    // Returns : true,false / Void
    // Description : Write Content  Header
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function WriteContentFooter()
    {
        ?></div><?
    } //--- end of WriteContentHeader()      
 } //end of claas FrontForm
?>