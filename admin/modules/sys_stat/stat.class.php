<?
/**
* Class Stat
* Statistic Base Class
* @package System Package of SEOCMS
* @author Igor Trokhymchuk  <ihor@seotm.com>
* @version 1.1, 02.04.2012
* @copyright (c) 2005+ by SEOTM
*/
 class Stat
 {
   public  $id;         /* id */
   public  $target;     /* Target (0-Back End, 1-Front End) */
   public  $dt;         /* date */
   public  $tm;         /* time */
   public  $page;       /* page */
   public  $module;     /* module, id-module of sysstem */
   public  $cnt;        /* count */
   public  $refer;      /* referral page */
   public  $time_gen;   /* time needed to generate the page */
   public  $ip;         /* remote IP address */
   public  $host;       /* remote host name */
   public  $proxy;      /* proxy server */
   public  $user;       /* user, id-user in system or 0 */
   public  $agent;      /* the client the user is using I.E., FireFox etc */
   public  $screen_res; /* the screen resolution of user */
   public  $lang;       /* lang id */
   public  $country;    /* country */
   public  $page_url = NULL;

   public  $db;

  /**
  * Stat::__construct()
  * 
  * @param integer $target
  * @return void
  */
  function __construct($target=1)
  {
    /* Get Statistic Settings */
    $this->Set = new StatSet();
    $this->target = $target;
    if( ($target==1 AND $this->Set->front==1) OR ($target==0 AND $this->Set->back==1) ){
        $this->db = new DB( "", $this->Set->db_user, $this->Set->db_pass, $this->Set->db_name, "" );
        $res = $this->db->db_Select( $this->Set->db_name );
    }
 } //--- end of Stat()



  // ================================================================================================
  // Function : Set()
  // Version : 1.0.0
  // Date : 11.03.2005
  // Parms :
  // Returns : true,false / Void
  // Description : Set Statistic Property's
  // ================================================================================================
  // Programmer : Andriy Lykhodid
  // Date : 11.03.2005
  // Reason for change : Reason Description / Creation
  // Change Request Nbr:
  // ================================================================================================

  function Set()
  {
    
   $set = new StatSet();
   $arr = explode( ';', $set->fields );

   $this->id = '';

   if( $set->dt == 1 ) $this->dt = date( "Y-m-d" );
   else $this->dt = '';
   $this->tm = date("H:i:s");

   if( $set->page == 1 )
   {
    $this->page = getenv( "SCRIPT_NAME" );
    if( isset($_SERVER["REQUEST_URI"])) $this->page_url = $_SERVER["REQUEST_URI"];
    else $this->page_url = NULL;
   }
   else $this->page = '';

   if( $set->module_ == 1 )
   {
    if( isset( $_REQUEST['module'] ) ) $this->module = $_REQUEST['module'];
    else $this->module = '';
   }else $this->module = '';

   if( $set->refer == 1 )
   {
    $this->refer = '';
   //ECHO '<BR>$set->refer:'.$set->refer;
   //$_SERVER['HTTP_REFERER'] = "http://www.meta.ua/search.asp?q=%EA%EE%EC%EF%FC%FE%F2%E5%F0%ED%FB%E9+%F1%F2%EE%EB&m=";
   //ECHO '<BR>$_SERVER[HTTP_REFERER]:'.$_SERVER['HTTP_REFERER'];
    if( isset( $_SERVER['HTTP_REFERER'] ) )
    {
     if( !strstr( $_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'] ) )
     $this->refer = $_SERVER['HTTP_REFERER'];
    }
   }else $this->refer = '';


   if( $set->time_gen == 1 ) {
       if( isset($_SERVER["REQUEST_TIME"]) ) {
           list($usec, $sec) = explode(" ", microtime()); 
           $microtime = ((float)$usec + (float)$sec);
           $this->time_gen = $microtime - $_SERVER["REQUEST_TIME"];
       }
       else $this->time_gen = '';
       //echo '<br>$_SERVER["REQUEST_TIME"]='.$_SERVER["REQUEST_TIME"].' time()='.time().' microtime='.$microtime.' $this->time_gen='.$this->time_gen;
   }
   else $this->time_gen = '';

   if( $set->ip == 1 ) $this->ip = sprintf( "%u", ip2long( $_SERVER['REMOTE_ADDR'] ) ); //3562199730;
   else $this->ip = '';

   if( $set->host == 1 )
   {
    if( isset( $_SERVER['REMOTE_HOST'] ) ) $this->host = $_SERVER['REMOTE_HOST'];
    else $this->host = '';
   }else $this->host = '';

   if( $set->proxy == 1 )
   {
    $this->proxy = '';
   }else $this->proxy = '';

   if( $set->agent == 1 )
   {
    $this->agent = $this->Get_Agent_Id( $_SERVER['HTTP_USER_AGENT'] );
    if( isset( $_SERVER['HTTP_USER_AGENT'] ) ) $this->agent = $this->Get_Agent_Id( $_SERVER['HTTP_USER_AGENT'] );
    else $this->agent = '';
   }
   else $this->agent = '';

   if( $set->screen_res == 1 ) $this->screen_res = '';
   else $this->screen_res = '';

   if( $set->lang == 1 )
   {
    if( isset( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ) $this->lang = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
    else $this->lang = '';
   }else $this->lang = '';

   if( $set->country == 1 ) $this->country = $this->GetCountryByIP( $this->ip );
   else $this->country = '';

   /* Check this data in database*/
   if( $this->Check() )
   {
    /* Update statistic */
    $this->Update();
   }else
   {
    /* Save statistic */
    $this->Save();
   }
  } //--- end of Set



  // ================================================================================================
  // Function : Check()
  // Version : 1.0.0
  // Date : 11.03.2005
  // Parms :
  // Returns : true,false / Void
  // Description : Check Statistic data in database
  // ================================================================================================
  // Programmer : Andriy Lykhodid
  // Date : 11.03.2005
  // Reason for change : Reason Description / Creation
  // Change Request Nbr:
  // ================================================================================================

  function Check()
  {
   $q = "select * from ".TblModStatLog."
         where `dt`='".$this->dt."'
           AND `ip`='".$this->ip."'
           AND `module`='".$this->module."'
           AND `page`='".$this->page."'
           AND `refer`='".$this->refer."'
           AND `user`='".$this->user."'";
   if ( $this->IsFieldExist(TblModStatLog, 'page_url')==true ) $q = $q." AND `page_url`='".$this->page_url."'";        
   $q = $q." order by dt";
   $res = $this->db->db_Query( $q );
   $rows = $this->db->db_GetNumRows( $res );
   //echo '<br>'.$q.'<br> res='.$res.'<br> $this->db->result='.$this->db->result.' $rows='.$rows; //.'<br> $this->db->conn='.$this->db->conn.'<br> $this->db->error='.$this->db->error.'<br> $this->db->db_GetErrNo()='.$this->db->db_GetErrNo().'<br> $this->db->db_GetErrDetail()='.$this->db->db_GetErrDetail().'<br> mysql_errno()='.mysql_errno().' mysql_error()='.mysql_error() ;
   if( $rows > 0 )
   {
     $row = $this->db->db_FetchAssoc( $res );
     $this->cnt = $row['cnt'];
     $this->id = $row['id'];

     return true;
   }else return false;
  } //--- end of Check()



  // ================================================================================================
  // Function : Save()
  // Version : 1.0.0
  // Date : 11.03.2005
  // Parms :
  // Returns : true,false / Void
  // Description : Save Statistic record in database
  // ================================================================================================
  // Programmer : Andriy Lykhodid
  // Date : 11.03.2005
  // Reason for change : Reason Description / Creation
  // Change Request Nbr:
  // ================================================================================================

  function Save()
  {
    $this->cnt = 1;
    $q = "insert into ".TblModStatLog." values(
    NULL,
    '".$this->target."',
    '".$this->dt."',
    '".$this->page."',
    '".$this->module."',
    '".$this->cnt."',
    '".$this->refer."',
    '".$this->time_gen."',
    '".$this->ip."',
    '".$this->host."',
    '".$this->proxy."',
    '".$this->user."',
    '".$this->agent."',
    '".$this->screen_res."',
    '".$this->lang."',
    '".$this->country."',
    '".$this->tm."',
    '".$this->page_url."'";
    $q = $q.")";

    $res = $this->db->db_Query( $q );
    //echo '<br>'.$q.'<br> res='.$res.'<br> $this->db='.$this->db.'<br> $this->db->result='.$this->db->result.'<br> $this->db->conn='.$this->db->conn.'<br> $this->db->error='.$this->db->error.'<br> $this->db->db_GetErrNo()='.$this->db->db_GetErrNo().'<br> $this->db->db_GetErrDetail()='.$this->db->db_GetErrDetail().'<br> mysql_errno()='.mysql_errno().' mysql_error()='.mysql_error() ;
    //echo phpinfo();
    if( $res ) return true;
    else return false;
  } //--- end of Save



  // ================================================================================================
  // Function : Update()
  // Version : 1.0.0
  // Date : 11.03.2005
  // Parms :
  // Returns : true,false / Void
  // Description : Update Statistic record in database
  // ================================================================================================
  // Programmer : Andriy Lykhodid
  // Date : 11.03.2005
  // Reason for change : Reason Description / Creation
  // Change Request Nbr:
  // ================================================================================================
  function Update()
  {
    $q = "update ".TblModStatLog." set
    `cnt`='".( $this->cnt + 1 )."',
    `tm`='".$this->tm."'";
    $q = $q." where `id`='".$this->id."'";
    $res = $this->db->db_Query( $q );
    //echo '<br>'.$q.'<br> res='.$res.'<br> $this->db='.$this->db.'<br> $this->db->result='.$this->db->result.'<br> $this->db->conn='.$this->db->conn.'<br> $this->db->error='.$this->db->error.'<br> $this->db->db_GetErrNo()='.$this->db->db_GetErrNo().'<br> $this->db->db_GetErrDetail()='.$this->db->db_GetErrDetail().'<br> mysql_errno()='.mysql_errno().' mysql_error()='.mysql_error() ;
    if( $res ) return true;
    else return false;
  } //--- end of Update




  // ================================================================================================
  // Function : DelAll()
  // Version : 1.0.0
  // Date : 13.03.2005
  // Parms :
  // Returns : true,false / Void
  // Description : Delete All Statistic record's in Log-table
  // ================================================================================================
  // Programmer : Andriy Lykhodid
  // Date : 13.03.2005
  // Reason for change : Reason Description / Creation
  // Change Request Nbr:
  // ================================================================================================

  function DelAll()
  {

  } //--- end of Del




  // ================================================================================================
  // Function : GetCountryByIP()
  // Version : 1.0.0
  // Date : 11.03.2005
  // Parms :   $ip - IP address
  // Returns : true,false / Void
  // Description : Get Country By IP
  // ================================================================================================
  // Programmer : Andriy Lykhodid
  // Date : 11.03.2005
  // Reason for change : Reason Description / Creation
  // Change Request Nbr:
  // ================================================================================================
  function GetCountryByIP( $ip )
  {
   $cn = NULL;

   $q = "SELECT ctry FROM ".TblModStatIP." WHERE ip_from<=".$ip." AND ip_to>=".$ip;
   $res = $this->db->db_Query( $q );

   if( $this->db->db_GetNumRows( $res ) ==0 ) $cn = '';
   if( $this->db->db_GetNumRows( $res ) != 0 )
   {
    $row = $this->db->db_FetchAssoc( $res );
    $cn = $row['ctry'];
   }
   return $cn;
  } //--- end of GetCountryByIP




  // ================================================================================================
  // Function : Get_Agent_Id()
  // Version : 1.0.0
  // Date : 09.12.2005
  // Parms :   $agent name
  // Returns : agent Id
  // Description : Get Id by Agent Name
  // ================================================================================================
  // Programmer : Andriy Lykhodid
  // Date : 09.12.2005
  // Reason for change : Reason Description / Creation
  // Change Request Nbr:
  // ================================================================================================

  function Get_Agent_Id( $agent )
  {
    $agent = addslashes( $agent );
    $q = "SELECT id, name FROM ".TblModStatAgent." WHERE `name`='".$agent."'";
    $res = $this->db->db_Query( $q );

    if( $this->db->db_GetNumRows( $res ) > 0 )
    {

     $row = $this->db->db_FetchAssoc( $res );
     //echo '<br>'.$q.'<br>'.$res.'<br>id='.$row['id'];
     return $row['id'];
    }else
    {
     $q = "insert into ".TblModStatAgent." values(NULL,'$agent','','user','on')";
     $res = $this->db->db_Query( $q );

     $id = mysql_insert_id();

     return $id;
    }
  } //---- end
  
   // ================================================================================================
   // Function : AutoInsertColumn
   // Version : 1.0.0
   // Date : 12.12.2006
   //
   // Parms :
   // Returns : true,false / Void
   // Description :  
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 12.12.2006
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function AutoInsertColumn()
   {
      $db = new DB();
      if ( !$this->IsFieldExist(TblModStatLog, 'tm') ) {
        $q = "ALTER TABLE `".TblModStatLog."` ADD `tm` time";
        $res = $db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $db->result='.$db->result;
        if ( !$res OR !$db->result ) return false;    
      }

      if ( !$this->IsFieldExist(TblModStatLog, 'page_url') ) {
        $q = "ALTER TABLE `".TblModStatLog."` ADD `page_url` text";
        $res = $db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $db->result='.$db->result;
        if ( !$res OR !$db->result ) return false;    
      }
      return true;
   } //end of function AutoInsertColumn() 
   
   // ================================================================================================
   // Function : IsFieldExist
   // Version : 1.0.0
   // Date : 13.10.2006
   //
   // Parms :   $Table  / name of table, from which will be checking
   //           $field  / name of the field whitch will be checking
   // Returns : return 1 or 0
   // Description : return exist or not (1 or 0) field in this table
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 13.10.2006
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function IsFieldExist($Table, $field ='tm')
   {
       $tmp_db = new DB();
       $q = "SELECT * FROM `".$Table."` WHERE 1 LIMIT 1";
       $res = $tmp_db->db_Query($q);
       //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
       if ( !$res ) return false;
       if ( !$tmp_db->result ) return false;
       
       $i = 0;
       while ($i < mysql_num_fields($tmp_db->result)) {
            $meta = mysql_fetch_field($tmp_db->result, $i);
            if ($meta) {
               if ($meta->name==$field) return true;
            }
            $i++;
       }
       return false;
   } //end of function IsFieldExist()  
   
   // ================================================================================================
   // Function : GetCounterAll
   // Version : 1.0.0
   // Date : 19.03.2007
   //
   // Parms :   
   // Returns : 
   // Description : return count of all visits on the site  
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 19.03.2007
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function GetCounterAll()
   {
       $tmp_db = new DB();
       $q = "select 1 as cnt,dt,ip from ".TblModStatLog." group by ip,dt";
       $res = $tmp_db->db_Query( $q );
       //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
       if ( !$res OR !$tmp_db->result ) return false;
       $rows = $tmp_db->db_GetNumRows();            
       return $rows;
   } //end of function GetCounterAll()                   

   // ================================================================================================
   // Function : GetCounterByDay
   // Version : 1.0.0
   // Date : 19.03.2007
   //
   // Parms :   
   // Returns : 
   // Description : return count of visits on the site by day 
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 19.03.2007
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function GetCounterByDay()
   {
       $tmp_db = new DB();
       $Date_Calc = new Date_Calc();
       $days = $Date_Calc->dateToDays( $Date_Calc->getDay(), $Date_Calc->getMonth(), $Date_Calc->getYear() );
       $dt = $Date_Calc->daysToDate( $days, "%Y-%m-%d" );
       $q = "select 1 as cnt,dt,ip from ".TblModStatLog." where dt='$dt' group by ip,dt";
       $res = $tmp_db->db_Query( $q );
       //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
       if ( !$res OR !$tmp_db->result ) return false;
       $host_today = $tmp_db->db_GetNumRows();            
       return $host_today;
   } //end of function GetCounterByDay() 
   
   // ================================================================================================
   // Function : GetCounterHitsByDay
   // Version : 1.0.0
   // Date : 19.03.2007
   //
   // Parms :   
   // Returns : 
   // Description : return count of views pages on the site  by day
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 19.03.2007
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function GetCounterHitsByDay()
   {
       $tmp_db = new DB();
       $Date_Calc = new Date_Calc();
       $days = $Date_Calc->dateToDays( $Date_Calc->getDay(), $Date_Calc->getMonth(), $Date_Calc->getYear() );
       $dt = $Date_Calc->daysToDate( $days, "%Y-%m-%d" );
       $q = "select sum(cnt) as cnt from ".TblModStatLog." where dt='$dt' ";
       $res = $tmp_db->db_Query( $q );
       //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
       if ( !$res OR !$tmp_db->result ) return false;
       $row = $tmp_db->db_FetchAssoc();
       $hit_today = $row['cnt'];
       return intval( $hit_today );
   } //end of function GetCounterHitsByDay()        

 } //--- end of class Stat

?>