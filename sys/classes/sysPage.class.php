<?php
// ================================================================================================
// System : SEOCMS
// Module : SysLang.class.php
// Version : 2.0.0
// Date : 11.01.2006
// Licensed To:
// Igor Trokhymchuk ihoru@mail.ru
// Andriy Lykhodid las_zt@mail.ru
//
// Purpose :  Class definition for all page actions
//
// ================================================================================================
// ================================================================================================
//
//    Programmer        :  Andriy Lykhodid
//    Date              :  26.01.2005
//    Reason for change :  Creation
//    Change Request Nbr:
//
//    Function          :  Class definition for all page actions
//
//  ================================================================================================
// ================================================================================================
//    Class             : Page
//    Version           : 1.0.0
//    Date              : 26.01.2005
//
//    Constructor       : Yes
//    Parms             :
//    Returns           : None
//    Description       : Page - base class
// ================================================================================================
//    Programmer        :  Andriy Lykhodid
//    Date              :  26.01.2005
//    Reason for change :  Creation
//    Change Request Nbr:  N/A
// ================================================================================================

class Page {

     var $body = NULL;
     var $title = NULL;
     var $Description = NULL; 
     var $Keywords = NULL; 
     var $head = NULL;
     var $debug = NULL;
     var $lang_id = NULL;
     var $is_404 = false;

     var $Lang = NULL;
     var $page_encode = NULL;
     var $time_start = NULL;
     var $time_end = NULL;
 
    // ================================================================================================
    //    Function          : Page (Constructor)
    //    Version           : 1.0.0
    //    Date              : 26.01.2005
    //    Parms             :
    //    Returns           :
    //    Description       : Form Designer (Show Form Header, Footer and Content)
    // ================================================================================================
    Function Page ( )
    {    
        if( empty($this->Msg ) ) $this->Msg = &check_init('ShowMsg','ShowMsg', $this->lang_id);
    }
    // ================================================================================================
    // Function : SetBody()
    // Version : 1.0.0
    // Date : 26.01.2005
    // Parms :
    // Returns : true,false / Void
    // Description : Set BODY
    // ================================================================================================
    // Programmer : Andriy Lykhodid
    // Date : 26.01.2005
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function SetBody( $body='' )
    {
     $this->body = $body;
    }
    // ================================================================================================
    // Function : SetTitle()
    // Version : 1.0.0
    // Date : 26.01.2005
    // Parms :
    // Returns : true,false / Void
    // Description : Set TITLE
    // ================================================================================================
    // Programmer : Andriy Lykhodid
    // Date : 26.01.2005
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function SetTitle( $title='' )
    {
     if( $this->Is_404() ) {
         if( empty($this->Msg ) ) $this->Msg = &check_init('ShowMsg','ShowMsg', $this->lang_id);
         $txt = $this->Msg->show_text('MSG_404_PAGE_NOT_FOUND');
         if( empty($txt) ) $txt = 'Error 404 - Page Not Found';
         $this->title = $txt;
     }
     else $this->title = $title;
    }
    // ================================================================================================
    // Function : SetDescription()
    // Version : 1.0.0
    // Date : 30.11.2006  
    // Parms :
    // Returns : true,false / Void
    // Description : Set Description for page
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 30.11.2006  
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function SetDescription( $descr='' )
    {
        if( $this->Is_404() ) $this->Description = '';
        $this->Description = $descr;
    }
    // ================================================================================================
    // Function : SetKeywords()
    // Version : 1.0.0
    // Date : 30.11.2006
    // Parms :
    // Returns : true,false / Void
    // Description : Set Keywords for page
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 30.11.2006  
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function SetKeywords( $keywords='' )
    {
        if( $this->Is_404() ) $this->Keywords = '';
        $this->Keywords = $keywords;
    }
    // ================================================================================================
    // Function : SetDebug()
    // Version : 1.0.0
    // Date : 26.01.2005
    // Parms :
    // Returns : true,false / Void
    // Description : Set debug
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 26.01.2005
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function SetDebug( $debug='' )
    {
     $this->debug = $debug;
    }
    // ================================================================================================
    // Function : GetDebug()
    // Version : 1.0.0
    // Date : 26.01.2005
    // Parms :
    // Returns : true,false / Void
    // Description : Get debug
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 26.01.2005
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function GetDebug( )
    {
     return $this->debug;
    }
    // ================================================================================================
    // Function : SetHead()
    // Version : 1.0.0
    // Date : 26.01.2005
    // Parms :
    // Returns : true,false / Void
    // Description : Set HEAD
    // ================================================================================================
    // Programmer : Andriy Lykhodid
    // Date : 26.01.2005
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function SetHead( $head='' )
    {
     $this->head = $head;
    }
    // ================================================================================================
    // Function : SetLang()
    // Version : 1.0.0
    // Date : 26.01.2005
    // Parms :
    // Returns : true,false / Void
    // Description : Set Language of page
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 02.02.2005
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function SetLang( $lang_id='' )
    {
     $this->lang_id = $lang_id;
    }
    // ================================================================================================
    // Function : GetLang()
    // Version : 1.0.0
    // Date : 26.01.2005
    // Parms :
    // Returns : true,false / Void
    // Description : return the current Language of page
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 02.02.2005
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function GetLang()
    {
     return $this->lang_id;
    }
    // ================================================================================================
    // Function : GetFunction()
    // Version : 1.0.0
    // Date : 26.01.2005
    // Parms :   $function - id function
    // Returns : true,false / Void
    // Description : Get Function for menu
    // ================================================================================================
    // Programmer : Andriy Lykhodid
    // Date : 26.01.2005
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function GetFunction( $function, $lang_id=_LANG_ID )
    {
     $db = &DBs::getInstance();

      $q = "select `".TblSysFunc."`.name, `".TblSysFunc."`.target, `".TblSysSprFunc."`.name as module_name from `".TblSysFunc."`,`".TblSysSprFunc."` 
      where `".TblSysFunc."`.id='$function'
      and `".TblSysFunc."`.id=`".TblSysSprFunc."`.cod 
      and `".TblSysSprFunc."`.lang_id='$lang_id' ";
      $r = $db->db_Query( $q );
      //echo '<br>$q='.$q.' $r='.$r;
      if( !$r ) return false;
      $rkol = $db->db_GetNumRows( $r );
      $mas_menu=NULL;
      $mas = $db->db_FetchAssoc( $r );
      return $mas;
    }
    // ================================================================================================
    // Function : Set_404()
    // Version : 1.0.0
    // Date : 28.05.2008
    // Parms : 
    // Returns : true,false / Void
    // Description : set error 404
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 28.05.2008
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================    
    function Set_404() {
        $this->is_404 = true;  
        //@header("HTTP/1.1 404 Not Found");
        //@header("Status: 404 Not Found");        
        //echo '<br>$this->is_404='.$this->is_404;
    }//end of function Set_404()

    // ================================================================================================
    // Function : Is_404()
    // Version : 1.0.0
    // Date : 28.05.2008
    // Parms : 
    // Returns : true,false / Void
    // Description : set error 404
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 28.05.2008
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================    
    function Is_404() {
        return $this->is_404;
    }//end of function Is_404()

    
    // ================================================================================================
    // Function : get_status_header_desc()
    // Version : 1.0.0
    // Date : 28.05.2008
    // Parms : 
    // Returns : true,false / Void
    // Description : set error 404
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 28.05.2008
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================    
    function get_status_header_desc( $code ) {
        global $wp_header_to_desc;

        $code = intval( $code );

        if ( !isset( $wp_header_to_desc ) ) {
            $wp_header_to_desc = array(
                100 => 'Continue',
                101 => 'Switching Protocols',

                200 => 'OK',
                201 => 'Created',
                202 => 'Accepted',
                203 => 'Non-Authoritative Information',
                204 => 'No Content',
                205 => 'Reset Content',
                206 => 'Partial Content',

                300 => 'Multiple Choices',
                301 => 'Moved Permanently',
                302 => 'Found',
                303 => 'See Other',
                304 => 'Not Modified',
                305 => 'Use Proxy',
                307 => 'Temporary Redirect',

                400 => 'Bad Request',
                401 => 'Unauthorized',
                403 => 'Forbidden',
                404 => 'Not Found',
                405 => 'Method Not Allowed',
                406 => 'Not Acceptable',
                407 => 'Proxy Authentication Required',
                408 => 'Request Timeout',
                409 => 'Conflict',
                410 => 'Gone',
                411 => 'Length Required',
                412 => 'Precondition Failed',
                413 => 'Request Entity Too Large',
                414 => 'Request-URI Too Long',
                415 => 'Unsupported Media Type',
                416 => 'Requested Range Not Satisfiable',
                417 => 'Expectation Failed',

                500 => 'Internal Server Error',
                501 => 'Not Implemented',
                502 => 'Bad Gateway',
                503 => 'Service Unavailable',
                504 => 'Gateway Timeout',
                505 => 'HTTP Version Not Supported'
            );
        }

        if ( isset( $wp_header_to_desc[$code] ) )
            return $wp_header_to_desc[$code];
        else
            return '';
    }
    
    // ================================================================================================
    // Function : status_header()
    // Version : 1.0.0
    // Date : 28.05.2008
    // Parms : 
    // Returns : true,false / Void
    // Description :
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 28.05.2008
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================     
    function status_header( $header ) {
        $text = $this->get_status_header_desc( $header );
        if ( empty( $text ) )
            return false;

        $protocol = $_SERVER["SERVER_PROTOCOL"];
        if ( 'HTTP/1.1' != $protocol && 'HTTP/1.0' != $protocol )
            $protocol = 'HTTP/1.0';
        $status_header = "$protocol $header $text";
        //if ( function_exists( 'apply_filters' ) ) $status_header = apply_filters( 'status_header', $status_header, $header, $text, $protocol );

        if ( version_compare( phpversion(), '4.3.0', '>=' ) )
            return @header( $status_header, true, $header );
        else
            return @header( $status_header );
    }//end of function status_header()      

    
    // ================================================================================================
    // Function : send_headers()
    // Version : 1.0.0
    // Date : 28.05.2008
    // Parms : 
    // Returns : true,false / Void
    // Description :
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 28.05.2008
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================     
    function send_headers() {
        if( $this->Is_404() ) $this->status_header('404');
        else $this->status_header('200');
        header('Content-type: text/html; charset=utf-8');
    }//end of function send_headers()
    
    // ================================================================================================
    // Function : getmicrotime()
    // Version : 1.0.0
    // Date : 15.10.2009
    // Parms : 
    // Returns : true,false / Void
    // Description : return microseconds
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 15.10.2009
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function getmicrotime() 
    { 
        list($usec, $sec) = explode(" ", microtime()); 
        return ((float)$usec + (float)$sec); 
    }
    
}// End of Page Class
?>
