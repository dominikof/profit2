<?php
 // ================================================================================================
 // System : SEOCMS
 // Module : Database
 // Version : 1.0.0
 // Date : 15.03.2005
 // Licensed To:
 // Igor Trokhymchuk ihoru@mail.ru
 // Andriy Lykhodid las_zt@mail.ru
 // ================================================================================================

// Include needed for the script
//include_once( SITE_PATH.'/include/defines.php' );


 // ================================================================================================
 //    Class             : FrontForm
 //    Version           : 1.0.0
 //    Date              : 13.02.2005
 //    Constructor       : Yes
 //    Parms             :
 //    Returns           : None
 //    Description       : Class definition for describe input fields on front-end
 // ================================================================================================
 //    Programmer        :  Igor Trokhymchuk, Andriy Lykhodid
 //    Date              :  04.02.2005
 //    Reason for change :  Creation
 //    Change Request Nbr:  N/A
 // ================================================================================================

 class FrontForm extends Form
 {
    // ================================================================================================
    //    Function          : FrontForm (Constructor)
    //    Version           : 1.0.0
    //    Date              : 26.01.2005
    //    Parms             :
    //    Returns           :
    //    Description       : FrontForm Designer (Show Form Header, Footer and Content)
    // ================================================================================================
    Function FrontForm ( $nameform = 'f1' )
    {
        $this->name = $nameform;
        $this->Msg = new ShowMsg();
        $this->db = new DB();
    } //end of Constructor FrontForm() 
        
    // ================================================================================================
    // Function : WriteFrontHeader()
    // Version : 1.0.0
    // Date : 15.04.2005
    // Returns : true,false / Void
    // Description : Write Form Header
    // ================================================================================================
    // Programmer : Andriy Lykhodid
    // Date : 15.04.2005
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
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
    // Version : 1.0.0
    // Date : 15.04.2005
    // Returns : true,false / Void
    // Description : Write Form Footer
    // ================================================================================================
    // Programmer : Andriy Lykhodid
    // Date : 15.04.2005
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================

    function WriteFrontFooter()
    {
    ?>
    </form>
    <?
    } //--- end of WriteFrontFooter()


    // ================================================================================================
    // Function : Radio()
    // Version : 1.0.0
    // Date : 15.04.2005
    // Parms :       $arr, $value, $property, $javascript
    // Description : Write Radio
    // ================================================================================================
    // Programmer : Andriy Lykhodid
    // Date : 15.04.2005
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================

    function Radio( $name = NULL, $value = NULL, $valuech = NULL,  $txt = NULL, $params=NULL )
    {
    ?><input type="radio" class="radio" name="<?=$name;?>" value="<?=$value;?>" <?if( $valuech == $value )echo 'checked';?> <?if(!empty($params)) echo $params;?> ><?=$txt;?><?
    } //--- end of Radio()


    // ================================================================================================
    // Function : TextBox()
    // Version : 1.0.0
    // Date : 26.01.2005
    // Parms :
    // Returns :
    // Description : Write Text Box
    // ================================================================================================
    // Programmer : Andriy Lykhodid
    // Date : 26.01.2005
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function TextBox( $name = '', $value = '', $params = '' )
    {
        echo '<input type="text" class="textbox" name="'.$name.'" value="'.htmlspecialchars($value).'" ';
        if (!empty($params)) echo $params;
        echo "/>";
    }

    // ================================================================================================
    // Function : CheckBox()
    // Version : 1.0.0
    // Date : 26.01.2005
    // Parms :
    // Returns :
    // Description : Write CheckBox
    // ================================================================================================
    // Programmer : Andriy Lykhodid
    // Date : 26.01.2005
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function CheckBox( $name = 'id_del[]', $value = '', $sel = NULL, $params = NULL )
    {
    ?>
    <INPUT TYPE="checkbox" NAME="<?=$name;?>" VALUE="<?=$value;?>" <?if( $sel )echo ' CHECKED';?> <?if(!empty($params)) echo $params;?> />
    <?
    }

    // ================================================================================================
    // Function : TextArea()
    // Version : 1.0.0
    // Date : 26.01.2005
    // Parms :
    // Returns :
    // Description : Write Text Area
    // ================================================================================================
    // Programmer : Andriy Lykhodid
    // Date : 26.01.2005
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function TextArea( $name = '', $value = '', $rows = 4, $cols = 70, $params = NULL )
    {
    ?>
    <textarea name=<?=$name;?> ROWS=<?=$rows;?> COLS=<?=$cols;?> <?if(!empty($params)) echo $params;?> class="textarea"><?=htmlspecialchars($value);?></textarea>
    <?
    }
	  
    // ================================================================================================
    // Function : Password()
    // Version : 1.0.0
    // Date : 29.01.2005
    // Parms :
    // Returns :
    // Description : Write input type=hidden
    // ================================================================================================
    // Programmer : Andriy Lykhodid
    // Date : 29.01.2005
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function Password( $name = '', $value = '', $size = '' )
    {
    if(!$size){$size = 10;}
    ?>
    <input type="password" class="textbox" name="<?=$name;?>" value="<?=$value;?>" size="<?=$size;?>"/>
    <?
    }      

    // ================================================================================================
    // Function : Hidden()
    // Version : 1.0.0
    // Date : 27.01.2005
    // Parms :
    // Returns :
    // Description : Write input type=hidden
    // ================================================================================================
    // Programmer : Andriy Lykhodid
    // Date : 27.01.2005
    // Reason for change : Reason Description / Creation
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
    // Version : 1.0.0
    // Date : 27.01.2005
    // Parms :
    // Returns :
    // Description : Write input type=hidden
    // ================================================================================================
    // Programmer : Andriy Lykhodid
    // Date : 27.01.2005
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function Button( $name = '', $value = '', $params = NULL )
    {
    ?>
    <input type="submit" name="<?=$name?>" value="<?=$value?>" class="button" <?if($params) echo $params;?>/>
    <?
    }

    // ================================================================================================
    // Function : Select()
    // Version : 1.0.0
    // Date : 26.01.2005
    // Parms :
    // Returns :
    // Description : Write Select Box
    // ================================================================================================
    // Programmer : Andriy Lykhodid
    // Date : 26.01.2005
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function Select( $arr, $name = '', $value = NULL, $width = NULL )
    {
    ?>
    <select name="<?=$name;?>" width="<?=$width?>">
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
    // Version : 1.0.0
    // Date : 12.01.2005
    //
    // Parms : $Table    - table, from whith data will be shown
    //         $name_fld - name of field
    //         $val      - value of field
    //         $width    - width of field
    // Returns : true
    // Description : show the list of the records from table to combobox
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 12.01.2005
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 14.02.2005
    // Reason for change : use function from this class not HTML
    // Change Request Nbr: 1
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

     echo '<SELECT style="width:'.$width.'" NAME='.$name_fld.'>';
     echo '<OPTION VALUE="">';
     for( $i = 0; $i < $rows1; $i++ )
     {
      $row1 = $Rights->db_FetchAssoc();
      //echo ' '.$i.' '.$val. ' = '.$row1['id'];
      echo '<OPTION VALUE="'.$row1['cod'].'"';
      if (!empty($val) and ($val == $row1['cod'])) echo ' SELECTED>'.$row1['name'];
      else echo '">'.$row1['name'];
     }

     /*
     if ( (empty($val)) & ($Table!='sex') & ($Table!='sys_spr_day') & ($Table!='sys_spr_mounth') & ($Table!='sys_spr_years')) {
	     echo '<OPTION VALUE="0" SELECTED>'; echo show_text('_VALUE_NOT_SET');
     }
     */
     echo '</SELECT>';
    }

    // ================================================================================================
    // Function : checkbox_spr_get
    // Version : 1.0.0
    // Date : 13.01.2005
    //
    // Parms :   $Table    - table, from whith data will be shown
    //           $name_fld - name of field
    //           $cols     - count of checkboxes in one row
    //           $val      - value of checkbox
    // Returns : true
    // Description : show the list of the records from table to checkbox
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 13.01.2005
    // Reason for change : Creation
    // Change Request Nbr:
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
    // Version : 1.0.0
    // Date : 25.04.2005
    // Parms :
    // Returns :
    // Description : Write Link
    // ================================================================================================
    // Programmer : Andriy Lykhodid
    // Date : 26.01.2005
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function Link( $script = '', $name = 'link', $hint = NULL )
    {
    ?>
    <a href="<?=$script;?>" <? if( $hint )echo "onmouseover=\"return overlib('$hint',WRAP);\" onmouseout=\"nd();\""; ?> class="arch_polls"><?=$name?></a>
    <?
    } //--- end of Link
	  
    // ================================================================================================
    // Function : WriteLinkPagesFront()
    // Version : 1.0.0
    // Date : 26.10.2007
    //
    // Parms :
    // Returns : true,false / Void
    // Description : Write Link Pages
    // ================================================================================================
    // Programmer : Alex Kerest
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
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


    // ================================================================================================
    // Function : WriteLinkPagesStatic()
    // Version : 1.0.0
    // Date : 03.03.2009
    // Parms :
    // Returns : true,false / Void
    // Description : Write Link Pages with static url
    // ================================================================================================
    // Programmer : Alex Kerest, Ihor Trokhymchuk
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function WriteLinkPagesStatic( $scriptact, $rows, $display, $start, $sort, $page, $param_url=null )
    {
     $sh = 1;
     $na = 2;
     $flag1 = 1;
     $flag2 = 1;
     $p0 = 0;
    // echo "<br> page = ".$page;
     $end = round($rows/$display, 2);
     if($end<=1) return;
     ?>
 <table border="0" cellpading="2" cellspacing="0" align="right">
  <tr>
   <td align=left style="padding:5px;">
       <?
       $curr = round($start/$display, 0) +1;
       if(!$start || $start==''){
            ?><?
       }
       else{
            if($page-1==1) $link_prevpage= $scriptact;
            else $link_prevpage= $scriptact.'page'.($page-1).'/';
            if(!empty($param_url)) $link_prevpage .=  $param_url;
            ?><a href="<?=$link_prevpage;?>" class="lnk_page">← предыдущая</a>&nbsp;<?
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
                 ?><a href="<?=$script;?>" title="Перейти на страницу <?=$p;?>" class="lnk_page"><?=$p;?></a>&nbsp;<?
                 if($end<=$sh) continue; 
             }
           
         }
         else{
            if($flag1==1 and $na+$sh<=$curr-$sh-$sh-1 and $p>$sh+2 and $p<$end-$sh) { echo " ... "; $flag1 =0; }
         }
         if($p>=$curr-$sh and $p<=$curr+$sh and $p>$na+$sh and $p<$end-$sh and $p>($sh+1)){
             if($p==$curr){?><b class="LinkPagesSel"><?=$p;?></b><?}
             else {?><a href="<?=$script;?>" title="Перейти на страницу <?=$p;?>" class="lnk_page"><?=$p;?></a>&nbsp;<?}
             if($p>=$curr+$sh and $flag2==1 and $end-$sh>=$curr+$sh+$sh+2) { echo " ... "; $flag2 =0; }
         }
         else{
             if($curr<$sh+$na and $flag2==1 and $p>=$curr+$sh and $p>$sh+2) { echo " ... "; $flag2 =0; }
         }
                                             
         if($p>=$end-$sh and $p>$sh+2){
             if($p==$curr) {?><b class="LinkPagesSel"><?=$p;?></b>&nbsp;<?}
             else{?><a href="<?=$script;?>" title="Перейти на страницу <?=$p;?>" class="lnk_page"><?=$p;?></a>&nbsp;<?}
         }
         $p0=$p;
       }
       
       if(!empty($param_url)) $script = $scriptact.'page'.($page+1).'/'.$param_url;
       else $script = $scriptact.'page'.($page+1).'/';
       
       if(($display+$start)>=$rows){?><?}
       else{?><a href="<?=$script;?>" class="lnk_page">следующая →</a><?}
       ?>
       </td>
       <td align=left style="padding: 5px 0px 5px 5px;" >
       <?
       // if(!empty($param_url)) $script = $scriptact.'page'.($page+1).'/'.$param_url;
       // else $script = $scriptact.'page'.($page+1).'/';
       //$script = $scriptact.$param_url.'?&amp;start=0&amp;display='.$rows;
       $script = $scriptact.'showall/'.$param_url;
       ?>
          <a href="<?=$script;?>" class="lnk_page">Все</a>
       </td>
      </tr>
     </table>
     <?
    }//end of function WriteLinkPagesStatic()

    // ================================================================================================
    // Function : ShowTextMessages()
    // Version : 1.0.0
    // Date : 06.06.2007
    //
    // Parms :
    // Returns :      true,false / Void
    // Description :  Show text messages
    // ================================================================================================
    // Programmer :  Igor Trokhymchuk
    // Date : 06.06.2007
    // Reason for change : Creation
    // Change Request Nbr:
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
    // Version : 1.0.0
    // Date : 16.05.2008
    //
    // Parms :
    // Returns :      true,false / Void
    // Description :  Show text warnings
    // ================================================================================================
    // Programmer :  Igor Trokhymchuk
    // Date : 16.05.2008
    // Reason for change : Creation
    // Change Request Nbr:
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
    // Version : 1.0.0
    // Date : 10.01.2006
    //
    // Parms :
    // Returns :      true,false / Void
    // Description :  Show errors
    // ================================================================================================
    // Programmer :  Igor Trokhymchuk
    // Date : 10.01.2006
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function ShowErr($txt=NULL)
    {
     if ($txt){
       ?>
       <div align="center">
        <div class="err_box">
         <table border="0" cellspacing="0" cellpadding="0" align="center">
          <tr><td class="err_title"><?=$this->Msg->show_text('MSG_PAY_ATTENTION', TblModUserSprTxt);?></td></tr>
          <tr><td class="err_text"><?=$txt;?></td></tr>
         </table>
        </div>
       </div>
       <?
     }
    } //end of fuinction ShowErr()

 } //end of claas FrontForm
?>