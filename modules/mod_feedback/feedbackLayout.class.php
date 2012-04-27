<?php
include_once( SITE_PATH.'/modules/mod_feedback/feedback.defines.php' );

/**
* Class FeedbackLayout
* class for display interface of Feedback module.
* @author Igor Trokhymchuk  <ihor@seotm.com>
* @version 1.1, 22.12.2010
*/ 
class FeedbackLayout extends Feedback{
               
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
    function show_form($showMap=true)
    {
        
       ?>
       <div class="subBody user-text" id="feedback">
	   <?if($showMap):?>
	    <div class="mapBox">
		<h2 class="map-header"><?=$this->multi['TXT_IN_MAP']?>:</h2>
		<?echo $this->pageTxt['short']?>
	    </div>
	   <?endif;?>
	   <div id="idFeedbackFormBox" class="<?if($showMap) echo 'feedbackFormBox';else echo "feedbackFormBoxSideBar";?>" style="<?if(!$showMap) echo "margin-Left:0px;"?>">
	       <div id="ajaxLoaderId" class="ajax-loader"></div>
	       <script type="text/javascript">
		   function checkName(field, rules, i, options){
		      if (field.val() == "<?=$this->multi['_TXT_NAME']?>") {
			    // this allows to use i18 for the error msgs
			    return options.allrules.required.alertText;
			}
 
		   }
		   function checkQuestion(field, rules, i, options){
		      if (field.val() == "<?=$this->multi['_TXT_MESSAGE']?>") {
			    // this allows to use i18 for the error msgs
			    return options.allrules.required.alertText;
			}
 
		   }
	       </script>
	   <?if($showMap):?>
	   <h2 class="feddaback-title"><?=$this->multi['TXT_FEEDBACK']?></h2>
	   <?endif;?>
           <form method="post" action="#" name="form_mod_feedback" id="form_mod_feedback" enctype="multipart/form-data">
               <?$this->ShowErr();?>
               <div class="floatContainer ">
		       <input type="text" class="request_text   validate[required,funcCall[checkName]]" id="request1111" placeholder="<?=$this->multi['_TXT_NAME']?>"  value="<?if(empty($this->name)) echo $this->multi['_TXT_NAME']; else echo $this->name;?>" name="name" onclick="if(this.value=='<?=$this->multi['_TXT_NAME'];?>') this.value='';" onblur="if(this.value=='') this.value='<?=$this->multi['_TXT_NAME'];?>';"/>
               </div>
               <div class="floatContainer ">
		       <input type="text" class="request_text   validate[required,custom[phone]]" id="requestPhone" placeholder="<?=$this->multi['_TXT_TEL']?>" value="<?if(empty($this->tel)) echo $this->multi['_TXT_TEL']; else echo $this->tel;?>" name="tel" onclick="if(this.value=='<?=$this->multi['_TXT_TEL'];?>') this.value='';" onblur="if(this.value=='') this.value='<?=$this->multi['_TXT_TEL'];?>';"/>
               </div>
               <div class="floatContainer ">
		       <input type="text" class="request_text   validate[required,custom[email]]" id="request3" placeholder="<?=$this->multi['_TXT_E_MAIL']?>" value="<?if(empty($this->e_mail)) echo $this->multi['_TXT_E_MAIL']; else echo $this->e_mail;?>" name="e_mail" onclick="if(this.value=='<?=$this->multi['_TXT_E_MAIL'];?>') this.value='';" onblur="if(this.value=='') this.value='<?=$this->multi['_TXT_E_MAIL'];?>';"/>
               </div>
               <div class="floatContainer ">
		       <textarea id="request5" name="question" class="request_areaAsk   validate[required,funcCall[checkQuestion]]" placeholder="<?=$this->multi['_TXT_MESSAGE']?>" onclick="if(this.value=='<?=$this->multi['_TXT_MESSAGE']?>') this.value='';" onblur="if(this.value=='') this.value='<?=$this->multi['_TXT_MESSAGE']?>';"><?if(empty($this->question)) echo $this->multi['_TXT_MESSAGE']; else echo $this->question;?></textarea>
               </div>
               
               <div class="widht100procentov ">
                    <input id="sendFeedback" type="button" name="submit" value="<?=$this->multi['_TXT_SEND']?>" class="btn" onclick="return check();"/>
                    <?//$this->Form->Button('submit',$this->multi['_TXT_SEND'], 'onclick="return verify();"');?></div>
               </div>
	   <div id="container_feedback"></div>
           </form>
	   <script type="text/javascript">
	       $(document).ready(function(){
		  $("#form_mod_feedback").validationEngine(); 
	       });
	       
	   </script>
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
	   
            function check(){
		   if($("#form_mod_feedback").validationEngine("validate"))
		       save_order(); 
	    }
	    
            function save_order(){
            $.ajax({
                    type: "POST",
                    data: $("#form_mod_feedback").serialize() ,
                    url: "<?=_LINK;?>feedback_ajax/",
                    success: function(msg){
                    //alert(msg);
		    $("#ajaxLoaderId").fadeOut('fast', function(){
			$("#form_mod_feedback").validationEngine('showPrompt', msg, 'pass');
		    });
                    },
                    beforeSend : function(){
                        //$("#sss").html("");
			$("#ajaxLoaderId").width($("#idFeedbackFormBox").width()+15).height($("#idFeedbackFormBox").height()-26).fadeTo("fast",0.6);
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
        <tr><td width="100">'.$this->multi['_TXT_TEL'].':</td><td>'.stripslashes($this->tel).'</td></tr>
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
