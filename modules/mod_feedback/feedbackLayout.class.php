<?php
include_once( SITE_PATH.'/modules/mod_feedback/feedback.defines.php' );

/**
* Class FeedbackLayout
* class for display interface of Feedback module.
* @author Igor Trokhymchuk  <ihor@seotm.com>
* @version 1.1, 22.12.2010
*/ 
class FeedbackLayout extends Feedback {
               
    /**
    * Class Constructor FeedbackLayout
    * Init variables for module feedback.
    * @param varchar $session_id - id of the session
    * @param integer $user_id - id of the user
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.1, 22.12.2010
    */    
    function __construct( $session_id=NULL, $user_id=NULL) {
        ( $session_id   !="" ? $this->session_id  = $session_id   : $this->session_id  = NULL );
        ( $user_id      !="" ? $this->user_id     = $user_id      : $this->user_id     = NULL );

        if( DEFINED("_LANG_ID") ) $this->lang_id = _LANG_ID;
        $this->lang_id_for_send_emails = $this->lang_id;
        
        if (empty($this->db)) $this->db = DBs::getInstance();
        if (empty($this->Form)) $this->Form = &check_init('FormCatalog', 'FrontForm', '"form_mod_feedback"');
        $this->multi = &check_init_txt('TblFrontMulti', TblFrontMulti);

        if(defined("FeedbackUseFiles")) $this->is_files = FeedbackUseFiles;
        $this->AddTblFld();

    } // End of FeedbackLayout Constructor    
    
    /**
    * Class method show_form
    * show form for feedback 
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 22.12.2010
    */
    function show_form()
    {
       ?>
       <div class="subBody" id="feedback">
           <form method="post" action="<?=_LINK;?>contacts/send/" name="form_mod_feedback" id="form_mod_feedback" enctype="multipart/form-data">
               <?$this->ShowErr();?>
               <div class="floatContainer">
                   <div class="width25 floatToLeft"><?=$this->multi['_TXT_NAME'];?>: <span class="red">*</span></div>
                   <div class="width75 floatToRight"><?$this->Form->TextBox('name', stripslashes($this->name));?></div>
               </div>
               <div class="floatContainer">
                   <div class="width25 floatToLeft"><?=$this->multi['_TXT_E_MAIL'];?>: <span class="red">*</span></div>
                   <div class="width75 floatToRight"><?$this->Form->TextBox('e_mail', stripslashes($this->e_mail));?></div>
               </div>
               <div class="floatContainer">
                   <div class="width25 floatToLeft"><?=$this->multi['_TXT_TEL'];?>: <span class="red">*</span></div>
                   <div class="width75 floatToRight"><?$this->Form->TextBox('tel', stripslashes($this->tel));?></div>
               </div>
               <div class="floatContainer">
                   <div class="width25 floatToLeft"><?=$this->multi['_TXT_FAX'];?>:</div>
                   <div class="width75 floatToRight"><?$this->Form->TextBox('fax', stripslashes($this->fax));?></div>
               </div>
               
               <div class="floatContainer">
                   <div class="width25 floatToLeft"><?=$this->multi['_TXT_MESSAGE'];?>: <span class="red">*</span></div>
                   <div class="width75 floatToRight"><?$this->Form->TextArea('question', stripslashes($this->question), 6, 38);?></div>
               </div>
               <?
               if($this->is_files==1){
                   ?>
                   <div class="floatContainer">
                       <div class="width25 floatToLeft"><?=$this->multi['ATTACH_FILE'];?>:</div>
                       <div class="width75 floatToRight">
                         <input type="file" name="filename" />
                         <br /><span style="font-size: 10px;"><?=$this->multi['ATTACH_FILE_DESCR'];?></span>
                       </div>
                   </div>
                   <?
               }
               include_once(SITE_PATH.'/include/kcaptcha/kcaptcha.php');
               ?>
               <div class="floatContainer">
                    <div class="width25 floatToLeft"><img src="/include/kcaptcha/index.php?<?=session_name()?>=<?=session_id()?>" alt="" /></div>
                    <div class="width75 floatToRight">
                        <div style="font-size:10px;"><?=$this->multi['_TXT_CAPTCHA'];?></div>
                        <input type="text" name="captchacodestr" class="captchacode"/>
                    </div>
               </div>
               
               <div class="floatContainer">
                    <div class="width75 floatToRight">
                    <input type="submit" name="submit" value="<?=$this->multi['_TXT_SEND']?>" class="btnSubmit" onclick="return verify();"/>
                    <?//$this->Form->Button('submit',$this->multi['_TXT_SEND'], 'onclick="return verify();"');?></div>
               </div>
           </form>
       </div>
       <?
       $this->show_JS();
    } //end of fucntion show_form()
    
    /**
    * Class method show_JS
    * javascript functions
    * @return true or false
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.1, 23.12.2010
    */    
    function show_JS()
    {
       ?>
       <script type="text/javascript">
            function emailCheck (emailStr) {
                if (emailStr=="") return true;
                var emailPat=/^(.+)@(.+)$/;
                var matchArray=emailStr.match(emailPat);
                if (matchArray==null) 
                {
                    return false;
                }
                return true;
            }

            function verify() {
                var themessage = "<?=$this->multi['_TXT_CHECK'].'\n';?>";
                if (document.forms.form_mod_feedback.name.value=="") {
                    themessage = themessage + " - <?=$this->multi['_TXT_CHECK_FIO'].'\n';?>";
                }
                if ((!emailCheck(document.forms.form_mod_feedback.e_mail.value))||(document.forms.form_mod_feedback.e_mail.value=='')) {
                    themessage = themessage + " - <?=$this->multi['_TXT_CHECK_EMAIL'].'\n';?>";
                }
                if (document.forms.form_mod_feedback.tel.value=="") {
                    themessage = themessage + " - <?=$this->multi['_TXT_CHECK_TEL'].'\n';?>";
                }
                if (document.forms.form_mod_feedback.question.value=="") {
                    themessage = themessage + " - <?=$this->multi['MSG_EMPTY_QUESTION'].'\n';?>";
                }

                if (themessage == "<?=$this->multi['_TXT_CHECK'].'\n';?>")
                {
                    //save_order();
                    return true;
                }
                else 
                    alert(themessage);
                return false;
            }
            
            function verify2() {
                var themessage = "<?=$this->multi['_TXT_CHECK'].'\n';?>";
                if (document.forms.feedback.name.value=="") {
                    themessage = themessage + " - <?=$this->multi['_TXT_CHECK_FIO'].'\n';?>";
                }
                if ((!emailCheck(document.forms.feedback.e_mail.value))||(document.forms.feedback.e_mail.value=='')) {
                    themessage = themessage + " - <?=$this->multi['_TXT_CHECK_EMAIL'].'\n';?>";
                }
                if (document.forms.feedback.tel.value=="") {
                    themessage = themessage + " - <?=$this->multi['_TXT_CHECK_TEL'].'\n';?>";
                }
                if (document.forms.feedback.question.value=="") {
                    themessage = themessage + " - <?=$this->multi['MSG_EMPTY_QUESTION'].'\n';?>";
                }
                
                if (themessage == "<?=$this->multi['_TXT_CHECK'].'\n';?>")
                {
                    save_order();
                    return true;
                }
                else 
                    alert(themessage);
                return false;
            }
            
            function save_order(){
            $.ajax({
                    type: "POST",
                    data: $("#feedback").serialize() ,
                    url: "<?=_LINK;?>feedback_ajax/",
                    success: function(msg){
                    //alert(msg);
                    $("#container_feedback").html( msg );
                    },
                    beforeSend : function(){
                        //$("#sss").html("");
                        $("#rez").html('<div style="text-align:center;"><img src="/images/style/ajax-load.gif" alt="" title="" /></div>');
                    }  
                    });
            }
            
            function sh_kcaptcha() {
                //$("#seokcaptcha").css("display", "block");
            }   
       </script>
       <?
    } //end of function showJS()    


    /**
    * Class method CheckFields
    * Checking all fields for filling and validation 
    * @return varchar string with errors
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.1, 23.12.2010
    */       
    function CheckFields()
    {
        $this->Err=NULL;
        
        if(empty($this->name)) $this->Err = $this->Err.$this->multi['MSG_EMPTY_NAME'].'<br>';
        if(empty($this->e_mail)) $this->Err = $this->Err.$this->multi['MSG_EMPTY_EMAIL'].'<br>';
        if(empty($this->question)) $this->Err = $this->Err.$this->multi['MSG_EMPTY_QUESTION'].'<br>';         
        //echo '<br>$_SESSION[captcha_keystring]='.$_SESSION['captcha_keystring'].' $this->captchacodestr='.$this->captchacodestr;
        //echo '<br>$this->quick_form='.$this->quick_form;
        if($this->quick_form!=1){
            if( !isset($_SESSION['captcha_keystring']) ) {$this->Err = $this->Err.$this->multi['MSG_ERROR_SESSION'].'<br>';}
            else{
                if( empty($this->captchacodestr) OR $_SESSION['captcha_keystring'] != $this->captchacodestr ){
                    $this->Err = $this->Err.$this->multi['MSG_ERROR_CODE'].'<br>';
                }       
                unset($_SESSION['captcha_keystring']);
            }
        }
        if($this->is_files==1){
            //echo '$_FILES["filename"]='.$_FILES["filename"].' is_uploaded_file($_FILES["filename"]["tmp_name"])='.is_uploaded_file($_FILES["filename"]["tmp_name"]).' $_FILES["filename"]["size"]='.$_FILES["filename"]["size"];
            if ( isset($_FILES["filename"]) && is_uploaded_file($_FILES["filename"]["tmp_name"]) && $_FILES["filename"]["size"] ){
                //echo '$_FILES["filename"]["size"]='.$_FILES["filename"]["size"].' Feedback_MAX_FILE_SIZE='.Feedback_MAX_FILE_SIZE;
                if( $_FILES["filename"]["size"] > Feedback_MAX_FILE_SIZE){
                    $this->Err = $this->Err.$this->multi['MSG_ERR_FILE_SIZE'].' '.floor(Feedback_MAX_FILE_SIZE/1024/1024).'Mb<br>';
                }
            }
        }        
        //echo '<br>$this->Err='.$this->Err.' $this->Msg->table='.$this->Msg->table;
        return $this->Err;
    } //end of fuinction CheckFields()         
    
    /**
    * Class method print_all
    * print all contact.
    * @return none
    * @author Bogdan Iglinsky <bi@seotm.com>
    * @version 1.0, 9.4.2012
    */
    function print_all($id_del){
        $data=explode(';', trim($id_del));
        //print_r($data);
        $array=$this->GetContentFoId_del($data);
        
        ?>
        <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru">
        <head>
        
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv='Content-Type' content="application/x-javascript; charset=utf-8" /> 
        <meta http-equiv="Content-Language" content="ru" />
        <title>Заказ обратного звонка</title>
        <meta name="Description" content="Заказ обратного звонка" />
        <meta name="Keywords" content="Заказ обратного звонка" />       
                
        <link rel="icon" type="image/vnd.microsoft.icon"  href="/images/design/favicon.ico" />
        <link rel="SHORTCUT ICON" href="/images/design/favicon.ico" />
        
        <link href="/include/css/compare.css" type="text/css" rel="stylesheet" />
        </head>
        <body style="background:white;">
            <div style="overflow: hidden;">
             <div style="float:left;width: 251px;height: 99px;padding-right: 10px;"><img src="/images/design/logo.png"></div>
             <div style="padding: 50px 10px 50px 275px; font-size:18px;">
              Заказ обратного звонка
             </div>
            </div>

             <div style="width:600px;">
                 <table border="1" cellspacing="0" cellpadding="5" width="100%">
                  <tbody>
                  <tr align="center" style="font-weight:bold;"> 
                   <td align="cetter">Имя</td>
                   <td align="center">Телефон</td>
                   <td align="center">Коментарий</td>
                   <td align="center">Дата</td>
                  </tr>
                  <?$count=count($array);
                  for($i=0;$i<$count;$i++){
                    $arr=$array[$i];
                    ?>
                    <tr align="center">
                    <td align="center"><?=$arr['f_name']?></td>
                    <td align="center"><?=$arr['tel']?></td>
                    <td align="center"><?=$arr['message']?></td>
                    <td align="center"><?=$arr['date']?></td>
                    </tr> 
                    <?
                  }?> 
                  
                 </tbody>
                 </table>
             </div>
             <br />
              <div align="center" style="margin-top: 15px;width: 600px;"> 
                 <div style="width:200px;" align="center" onclick="this.style.visibility='hidden';">
                    <input type="submit" name="submit" value="Распечатать" onclick="this.style.visibility='hidden'; window.print();" />
                 </div>
             </div>
        
        
        </body></html>
        <?
    }


    /**
    * Class method send_form
    * send form of feddback on admins e-mials and save data to the database.
    * @return true or false
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.1, 23.12.2010
    */
    function send_form()
    {
        if( is_array($this->cookie_serfing) ) {
            $keys = array_keys($this->cookie_serfing);
            $rows = count($keys);
            //echo '<br />$rows='.$rows;
            for($i=0;$i<$rows;$i++){
                $this->serfing[$i]['tstart'] = $keys[$i];
                $this->serfing[$i]['tstart_dt'] = strftime("%Y-%m-%d %H:%M:%S", $this->serfing[$i]['tstart']);
                //all records exepts first item
                if($i>0){
                    //for all items exepts last
                    $this->serfing[$i-1]['tstay'] = $keys[$i]-$this->serfing[$i-1]['tstart'];
                    $this->serfing[$i-1]['tstay_dt'] = Date_Calc::DateDiffInTime($this->serfing[$i-1]['tstart'], $keys[$i]);
                    if($i==($rows-1)) {
                        $this->serfing[$i]['tstay'] = time()-$this->serfing[$i-1]['tstart'];
                        $this->serfing[$i]['tstay_dt'] = Date_Calc::DateDiffInTime($this->serfing[$i]['tstart'], time());
                    }
                }
                else{
                    $this->serfing[$i]['tstay'] = '';
                    $this->serfing[$i]['tstay_dt'] = '';
                }
                $this->serfing[$i]['uri']=$this->cookie_serfing[$keys[$i]];
            }
        }//end if
        //echo '<br />$serfing=';print_r($serfing);
        
        if($this->quick_form==1) $subject = $this->multi['QUICK_FEEDBACK'].' :: '.$_SERVER['SERVER_NAME'].', '.$this->multi['_TXT_NAME'].': '.$this->name;
        else $subject = $this->multi['_TXT_FORM_NAME'].' :: '.$_SERVER['SERVER_NAME'].', '.$this->multi['_TXT_NAME'].': '.$this->name;       
        
        $question = str_replace("\n", "<br/>", stripslashes($this->question));
        $body = $this->multi['_TXT_FORM_NAME'].':
        <style>
         td{ font-family:Arial,Verdana,sans-serif; font-size:11px;}
        </style>
        <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr><td width="100">'.$this->multi['_TXT_NAME'].':</td><td>'.stripslashes($this->name).'</td></tr>
        <tr><td>'.$this->multi['_TXT_TEL'].':</td><td>'.stripslashes($this->tel).'</td></tr>
        <tr><td>'.$this->multi['COMPANY'].':</td><td>'.stripslashes($this->fax).'</td></tr> 
        <tr><td>'.$this->multi['_TXT_E_MAIL'].':</td><td><a href="mailto:'.stripslashes($this->e_mail).'">'.stripslashes($this->e_mail).'</a></td></tr>
        <tr><td colspan="2" align="left">'.$this->multi['_TXT_MESSAGE'].':</td></tr>
        <tr><td colspan="2">'.$question.'</td></tr>';
        if( !empty($this->refpage)) $body .= '<tr><td>'.$this->multi['HTTP_REFERER'].':</td><td>'.urldecode(stripslashes($this->refpage)).'</td></tr>';;
        $body .= '</table>';
        
        //save contact to database
        $res = $this->SaveContact();

        //================ send by class Mail START =========================            
        $massage = $body;
        $mail = new Mail($this->lang_id_for_send_emails);
        
        $SysSet = new SysSettings();
	    $sett = $SysSet->GetGlobalSettings();
	    if( !empty($sett['mail_auto_emails'])){
	        $hosts = explode(";", $sett['mail_auto_emails']);
	        for($i=0;$i<count($hosts);$i++){
	            //$arr_emails[$i]=$hosts[$i];
	            $mail->AddAddress($hosts[$i]);
	        }//end for
	    }
        if( !empty($this->fpath) ){
            $fpath = $this->uploaddir.$this->fpath;
            $mail->AddAttachment($fpath);
        }
        $mail->Subject = $subject;
        $mail->Body = $massage;
        $mail->From = stripslashes($this->e_mail);
        $mail->FromName = stripslashes($this->name);
        if( !$mail->SendMail() ) return false;
        //================ send by class Mail END =========================
        return true;
    } //end of function send_form()
       
   
    /**
    * Class method show_form_left
    * show form for quick feedback
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.1, 24.12.2010
    */     
    function show_form_left()
    {
        $this->show_JS();
        ?> 
        <div class="box rb2" style="overflow: hidden">
         <ins class="center top"></ins>
         <h2><?=$this->multi['QUICK_FEEDBACK'];?></h2>
         <div class="container" id="container_feedback">
          <div id="rez"><?$this->ShowErrLeft();?></div> 
          <form method="post" name="feedback" id="feedback" action="#">
           <input type="hidden" name="quick_form" value="1" />
           <input type="hidden" name="task" value="send" />
           <ul class="request">
            <li>
             <div class="form-label"><label for="request1"><?=$this->multi['_TXT_NAME'];?>:</label>&nbsp;<span class="asterics">*</span></div>
             <div class="form-input"><input type="text" onfocus="sh_kcaptcha();" class="request_text" id="request1" value="<?=stripslashes($this->name);?>" name="name" /></div>                 
            </li>
            <li>
             <div class="form-label"><label for="request2"><?=$this->multi['COMPANY']?></label></div>
             <div class="form-input"><input type="text" class="request_text" id="request2" value="<?=stripslashes($this->fax);?>" name="fax" /></div>
            </li>
            <li>
             <div class="form-label"><label for="request3"><?=$this->multi['_TXT_E_MAIL']?></label>&nbsp;<span class="asterics">*</span></div>
             <div class="form-input"><input type="text" class="request_text" id="request3" value="<?=stripslashes($this->e_mail);?>" name="e_mail" /></div>
            </li>                                                 
            <li>
             <div class="form-label"><label for="request4"><?=$this->multi['_TXT_TEL']?></label>&nbsp;<span class="asterics">*</span></div>
             <div class="form-input"><input type="text" class="request_text" id="request4" value="<?=stripslashes($this->tel);?>" name="tel" /></div>
            </li>
            <li>
             <label for="request5"><?=$this->multi['_TXT_MESSAGE']?></label>&nbsp;<span class="asterics">*</span>
            </li>                                
            <li>
             <textarea id="request5" name="question" class="textarea_small"><?=stripslashes($this->question);?></textarea>
            </li>
            <?
            /*
            if( empty($this->Err) ) $disp="none";
            else $disp="block";
            ?>
            <li id="seokcaptcha" style="display:<?=$disp;?>;">
             <?include_once(SITE_PATH.'/include/kcaptcha/kcaptcha.php');?>
             <div style="float:left" id="sss"><img src="/include/kcaptcha/index.php?<?=session_name();?>=fqrpcicbqfg7se785e72j36ukdco6lm1"></div>
             <div style="padding: 20px 0px 5px 85px; float:none;"><input type="text" name="captchacodestr" size="10" /></div>
             <div style="font-size:10px; float:none; height:18px;"><?=$multis['_TXT_CAPTCHA'];?></div>
            </li>
            */?>        
            <li>
             <input type="submit" value="<?=$this->multi['_TXT_SEND']?>" class="button21"  onclick="verify2(); return false;" />
            </li>                
           </ul> 
           <div class="clear"></div>
          </form>
         </div>
         <ins class="center bottom"></ins>
         <ins class="round tl"></ins>
         <ins class="round tr"></ins>
         <ins class="round bl"></ins>
         <ins class="round br"></ins>
        </div> 
        
        <?
    } //end of fucntion show_form_left()
    
    
    /**
    * Class method ShowErr
    * show form with errors
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.1, 24.12.2010
    */    
    function ShowErr()
    {
       if ( !empty($this->Err) ){
       ?>
        <div class="err">
         <h3><?=$this->multi['MSG_ERR'];?>:</h3>
         <p><?=$this->Err;?></p><br /><br />
        </div>
       <?
       }
    } //end of fuinction ShowErr()

    /**
    * Class method ShowErrLeft
    * show form with errors for quick feedback
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.1, 24.12.2010
    */       
    function ShowErrLeft()
    {
       if ( !empty($this->Err_characters) ) $this->Err=" ";
       if ( !empty($this->Err) ){
       ?>
        <div class="err"><?=$this->Err;?></div>
       <?
       }
    } //end of fuinction ShowErrLeft()       
       
} //end of class FeedbackLayout
