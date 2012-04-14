<?
// ================================================================================================
//    Class             : Mail
//    Version           : 1.0.0
//    Date              : 04.03.2005
//    Constructor       : Yes
//    Parms             :
//    Returns           : None
//    Description       : Send Mail Class
// ================================================================================================
//    Programmer        :  Andriy Lykhodid
//    Date              :  04.03.2005
//    Reason for change :  Creation
//    Change Request Nbr:  N/A
// ================================================================================================

include_once( SITE_PATH.'/include/mail/class.phpmailer.php' );

 class Mail extends PHPMailer
 {

  var $insert_header = 1;
  var $insert_footer = 1;
  var $BodyHeader;   //--- Header of HTML BODY
  var $BodyFooter;   //--- Footer if HTML BODY
  var $lang_id;

 // ================================================================================================
 // Function : Mail()
 // Version : 1.0.0
 // Date : 04.03.2005
 // Returns : true,false     / Void
 // Description : Constructor
 // ================================================================================================
 // Programmer : Andriy Lykhodid
 // Date : 04.03.2005
 // Reason for change : Reason Description / Creation
 // Change Request Nbr:
 // ================================================================================================
 function Mail($lang_id=NULL)
 {
  ( !empty($lang_id) ? $this->lang_id = $lang_id : $this->lang_id = _LANG_ID );
  //$Spr = new SystemSpr();
  $SysSet = new SysSettings();
  $SysSet->lang_id = $lang_id;
  $sett = $SysSet->GetGlobalSettings();
  $sett['mail_from_name'] = $sett['txt'][$this->lang_id]['from'];
  $sett['mail_header'] = $sett['txt'][$this->lang_id]['head'];
  $sett['mail_footer'] = $sett['txt'][$this->lang_id]['foot'];  
  if( !empty($sett['mail_mailer']) )    $this->Mailer     = stripslashes($sett['mail_mailer']);
  if( !empty($sett['mail_host']) )      $this->Host       = stripslashes($sett['mail_host']);         // SMTP servers
  if( !empty($sett['mail_port']) )      $this->Port       = stripslashes($sett['mail_port']);
  if( !empty($sett['mail_smtp_auth']) ) $this->SMTPAuth   = stripslashes($sett['mail_smtp_auth']);    // turn on SMTP authentication
  if( !empty($sett['mail_username']) )  $this->Username   = stripslashes($sett['mail_username']);     // SMTP username
  if( !empty($sett['mail_password']) )  $this->Password   = stripslashes($sett['mail_password']);     // SMTP password
  if( !empty($sett['mail_from']) )      $this->From       = stripslashes($sett['mail_from']);
  if( !empty($sett['mail_from_name']) ) $this->FromName   = $sett['mail_from_name'];
  if( !empty($sett['mail_word_wrap']) ) $this->WordWrap   = stripslashes($sett['mail_word_wrap']);
  $this->IsHTML($sett['mail_is_html']);
  if( !empty($sett['mail_priority']) )  $this->Priority   = stripslashes($sett['mail_priority']);
  if( !empty($sett['mail_charset']) )   $this->CharSet    = stripslashes($sett['mail_charset']);
  if( !empty($sett['mail_encoding']) )  $this->Encoding   = stripslashes($sett['mail_encoding']);
  if( !empty($sett['mail_header']) ){
      $arr_html_img = $this->ConvertHtmlWithImagesForSend($sett['mail_header']);
      foreach($arr_html_img as $key=>$value){
          //echo '<br>$key='.$key;
          if( $key!='content') $this->AddAttachment($key);
      }
      $this->BodyHeader = $arr_html_img['content'];
  }
  if( !empty($sett['mail_footer']) ){
      $arr_html_img = $this->ConvertHtmlWithImagesForSend($sett['mail_footer']);
      foreach($arr_html_img as $key=>$value){
          //echo '<br>$key='.$key;
          if( $key!='content') $this->AddAttachment($key);
      }
      $this->BodyFooter = $arr_html_img['content'];      
  }
  /*
  if($this->ContentType = "text/html"){
     $this->BodyHeader = "
     <html>
     <head>
     <title></title>
     </head>
     <body>".$this->BodyHeader;
     $this->BodyFooter = $this->BodyFooter."
     </body>
     </html>";
  } 
  */ 
  /*
  $this->IsSMTP();                              // send via SMTP
  $this->Host     = "mail.ltw-tech.com";           // SMTP servers
  $this->SMTPAuth = true;                       // turn on SMTP authentication
  $this->Username = "andrey";           // SMTP username
  $this->Password = "andrey123";                     // SMTP password
  $this->From     = "andrey@ltw-tech.com";
  $this->FromName = $Msg->show_text('TXT_FRONT_SITE_TITLE');
  */
  
 }


 // ================================================================================================
 // Function : SendMail()
 // Version : 1.0.0
 // Date : 04.03.2005
 // Returns : true,false     / Void
 // Description : Send Mail Method
 // ================================================================================================
 // Programmer : Andriy Lykhodid
 // Date : 04.03.2005
 // Reason for change : Reason Description / Creation
 // Change Request Nbr:
 // ================================================================================================
 function SendMail()
 {
    if( $this->insert_header ) $this->Body = $this->BodyHeader.$this->Body;
    if( $this->insert_footer ) $this->Body = $this->Body.$this->BodyFooter;
    //echo $this->Body;
    if( $this->Send() ) return true;
    else return false;
 }
 
    // ================================================================================================
    // Function : ConvertHtmlWithImagesForSend()
    // Version : 1.0.0
    // Date : 27.09.2005
    //
    // Parms :        $model / id of the model of product
    // Returns :      true,false / Void
    // Description :  show product an news column
    // ================================================================================================
    // Programmer :  Igor Trokhymchuk
    // Date : 27.09.2005
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================

    function ConvertHtmlWithImagesForSend($str_source = NULL)
    {
       //echo '<br>$_SERVER[DOCUMENT_ROOT]='.SITE_PATH;
       //echo '<br>$_SERVER[SERVER_NAME]='.NAME_SERVER;
       
       if ( strstr(strtoupper($str_source),'IMG') ){
           $str_source = str_replace('<img', '<IMG', $str_source);
           $tmp_str_source = $str_source;
           $arr_html = explode("<IMG", $tmp_str_source);
           for($i=1; $i<=count($arr_html)-1; $i++){
            $html = $arr_html[$i];
            //echo '<br>$arr_html['.$i.']='.$arr_html[$i]; 
            $tmp_pos0 = strpos(strtoupper($html),"SRC");
            $tmp_new_str = substr($html, $tmp_pos0);
            $tmp_pos1 = strpos($tmp_new_str,'"');
            $tmp_new_str = substr($tmp_new_str, $tmp_pos1+1);
            //echo '<br>$tmp_new_str='.$tmp_new_str; 
            $tmp_pos2 = strpos($tmp_new_str,'"');
            //echo '<br>$tmp_pos1='.$tmp_pos1.' $tmp_pos2='.$tmp_pos2;
            $tmp_str_from = substr($tmp_new_str, 0, $tmp_pos2);
            //echo '<br>$tmp_str_from='.$tmp_str_from;
            
            $tmp_pos1 = strrpos($tmp_str_from,"/");
            $tmp_str_to = substr($tmp_str_from,$tmp_pos1+1);
            //$tmp_img_path_pos = strpos($tmp_str_from, NAME_SERVER);
            //echo '<br>$tmp_img_path_pos='.$tmp_img_path_pos;
            $tmp_img_path0 = substr($tmp_str_from, strpos($tmp_str_from, NAME_SERVER));
            //echo '<br><br>$tmp_img_path0='.$tmp_img_path0;
            $tmp_img_path = substr($tmp_img_path0,strpos($tmp_img_path0,"/") );
            //echo '<br>$tmp_img_path='.$tmp_img_path;
            //$tmp_img_path = substr($tmp_str_from,);
            $img_path = SITE_PATH.$tmp_img_path;
            //echo '<br>$img_path='.$img_path;
            $arr_html_img[$img_path] = "";                
            
            //echo'<br>$tmp_str_to='.$tmp_str_to; 
            $tmp_str_source = str_replace($tmp_str_from, $tmp_str_to, $tmp_str_source); 
           }
           //echo '<br>$tmp_str_source='.$tmp_str_source; 
           $str_source = $tmp_str_source;
       }
       $arr_html_img['content'] = $str_source;
       return $arr_html_img;
    } //end f function ConvertHtmlWithImagesForSend();  

 } //--- end of class
 /*_______________________   Real Example  _______________________________________*/
 /*-------------------------------------------------------------------------------

$mail = new Mail();
$mail->IsSMTP();                                   // send via SMTP
$mail->Host     = "mail.ltw-tech.com";             // SMTP servers
$mail->SMTPAuth = true;                            // turn on SMTP authentication
$mail->Username = "andrey";                        // SMTP username
$mail->Password = "erhfbyf2005";                   // SMTP password
$mail->From     = "andrey@ltw-tech.com";
$mail->FromName = "Andriy Lykhodid";
$mail->AddAddress("las_zt@mail.ru","Andiy Lykhodyd");
$mail->AddAddress("las@polesye.net");               // optional name
$mail->AddReplyTo("andrey@ltw-tech.com","Andiy Lykhodyd");
$mail->WordWrap = 50;                              // set word wrap
$mail->AddAttachment("/var/tmp/file.tar.gz");      // attachment
$mail->AddAttachment("/tmp/image.jpg", "new.jpg");
$mail->IsHTML(true);                               // send as HTML
$mail->Subject  =  "Here is the subject";
$mail->Body     =  "This is the <b>HTML body</b>";
$mail->AltBody  =  "This is the text-only body";
if(!$mail->Send())
{
   echo "Message was not sent <p>";
   echo "Mailer Error: " . $mail->ErrorInfo;
   exit;
}
echo "Message has been sent";
 -----------------------------------------------------------------------------------*/
?>