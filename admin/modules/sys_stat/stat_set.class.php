<?php
/**
* Class StatSet
* Class definition for all actions with settings of Statictic
* @package System Package of SEOCMS
* @author Igor Trokhymchuk  <ihor@seotm.com>
* @version 1.1, 02.04.2012
* @copyright (c) 2005+ by SEOTM
*/
 class StatSet extends Stat
 {
   var $id;         /* id */
   var $db_name;    /* Database Name for Statistic */
   var $db_user;    /* User for access */
   var $db_pass;    /* Password for access */
   var $front;      /* Set Front statistic (Yes/No) */
   var $back;       /* Set Back statistic (Yes/No) */
   var $fields;     /* Fields */


   var $dt = 0;
   var $page = 0;
   var $module_ = 0;
   var $refer = 0;
   var $time_gen = 0;
   var $ip = 0;
   var $host = 0;
   var $proxy = 0;
   var $user = 0;
   var $agent = 0;
   var $screen_res = 0;
   var $lang = 0;
   var $country = 0;

   var $db;         /* DB Access variable */

  /**
  * StatRep::__construct()
  * 
  * @param integer $user_id
  * @param integer $module_id
  * @return void
  */
  function __construct($user_id=NULL, $module=NULL)
  {
   $this->user_id = $user_id;
   $this->module = $module;
   $this->Right =  new RightsOld($this->user_id, $this->module);
   $this->db = new DB();
   /* Init Statistic Settings */
   $this->StatSetInit();
   $this->AutoInsertColumn();

  } //--- end of StatSet()



  // ================================================================================================
  //    Function          : StatSetInit()
  //    Version           : 1.0.0
  //    Date              : 14.03.2005
  //    Parms             :
  //    Returns           : true/false
  //    Description       : Init Statistic Settings
  // ================================================================================================

  function StatSetInit()
  {
    $q = "select * from ".TblModStatSet." where id=1";
    $res = $this->db->db_Query( $q );
    //echo '<br />$q='.$q.' $res='.$res;
    if( $this->db->db_GetNumRows( $res ) > 0 )
    {
     $row = $this->db->db_FetchAssoc( $res );
     $this->db_name = $row['db_name'];
     $this->db_user = $row['db_user'];
     $this->db_pass = $row['db_pass'];
     //$this->db_name = _DBNAME;
     //$this->db_user = _USER;
     //$this->db_pass = _PASSWD;

     $this->front = $row['front'];
     $this->back = $row['back'];
     $this->fields = $row['fields'];

     $arr = explode( ';', $row['fields'] );
     if( isset( $arr[0] ) ) $this->dt = $arr[0];
     else $this->dt = 1;
     $this->dt = 1;

     if( isset( $arr[1] ) ) $this->page = $arr[1];
     else $this->page = 0;

     if( isset( $arr[2] ) ) $this->module_ = $arr[2];
     else $this->module_ = 1;
     $this->module_ = 1;

     if( isset( $arr[3] ) ) $this->refer = $arr[3];
     else $this->refer = 0;

     if( isset( $arr[4] ) ) $this->time_gen = $arr[4];
     else $this->time_gen = 0;

     if( isset( $arr[5] ) ) $this->ip = $arr[5];
     else $this->ip = 1;
     $this->ip = 1;

     if( isset( $arr[6] ) ) $this->host = $arr[6];
     else $this->host = 0;

     if( isset( $arr[7] ) ) $this->proxy = $arr[7];
     else $this->proxy = 0;

     if( isset( $arr[8] ) ) $this->user = $arr[8];
     else $this->user = 1;
     $this->user = 1;

     if( isset( $arr[9] ) ) $this->agent = $arr[9];
     else $this->agent = 0;

     if( isset( $arr[10] ) ) $this->screen_res = $arr[10];
     else $this->screen_res = 0;

     if( isset( $arr[11] ) ) $this->lang = $arr[11];
     else $this->lang = 1;

     if( isset( $arr[12] ) ) $this->country = $arr[12];
     else $this->country = 1;

    }     
    
    /* 
     $this->db_name = STAT_DBNAME;
     $this->db_user = STAT_USER;
     $this->db_pass = STAT_PASSWD;
     //$this->db_name = _DBNAME;
     //$this->db_user = _USER;
     //$this->db_pass = _PASSWD;

     $this->front = FRONT_END_STAT;
     $this->back = BACK_END_STAT;
     $this->fields = FIELDS_STAT;

     $arr = explode( ';', $this->fields );
     if( isset( $arr[0] ) ) $this->dt = $arr[0];
     else $this->dt = 1;
     $this->dt = 1;

     if( isset( $arr[1] ) ) $this->page = $arr[1];
     else $this->page = 0;

     if( isset( $arr[2] ) ) $this->module_ = $arr[2];
     else $this->module_ = 1;
     $this->module_ = 1;

     if( isset( $arr[3] ) ) $this->refer = $arr[3];
     else $this->refer = 0;

     if( isset( $arr[4] ) ) $this->time_gen = $arr[4];
     else $this->time_gen = 0;

     if( isset( $arr[5] ) ) $this->ip = $arr[5];
     else $this->ip = 1;
     $this->ip = 1;

     if( isset( $arr[6] ) ) $this->host = $arr[6];
     else $this->host = 0;

     if( isset( $arr[7] ) ) $this->proxy = $arr[7];
     else $this->proxy = 0;

     if( isset( $arr[8] ) ) $this->user = $arr[8];
     else $this->user = 1;
     $this->user = 1;

     if( isset( $arr[9] ) ) $this->agent = $arr[9];
     else $this->agent = 0;

     if( isset( $arr[10] ) ) $this->screen_res = $arr[10];
     else $this->screen_res = 0;

     if( isset( $arr[11] ) ) $this->lang = $arr[11];
     else $this->lang = 1;

     if( isset( $arr[12] ) ) $this->country = $arr[12];
     else $this->country = 1;
     */
  } //--- StatSetInit()


 } // --- end of class
?>