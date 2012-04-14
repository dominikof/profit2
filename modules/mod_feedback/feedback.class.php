<?php
/**
* feedback.class.php   
* parent class of Feedback module
* @package Feedback Package of SEOCMS
* @author Igor Trokhymchuk  <ihor@seotm.com>
* @version 1.1, 22.12.2010
* @copyright (c) 2010+ by SEOTM
*/
include_once( SITE_PATH.'/modules/mod_feedback/feedback.defines.php' );


/**
* Class Feedback
* parent class of Feedback module
* @author Igor Trokhymchuk  <ihor@seotm.com>
* @version 1.1, 22.12.2010
*/ 
class Feedback {
    
    public $user_id = NULL;
    public $module = NULL;
    public $lang_id = NULL;
    public $Err = NULL;
   
    public $Msg = NULL;
    public $Rights = NULL;
    public $db = NULL;
    public $Form = NULL;
    public $Spr = NULL;
    
    public $lang_id_for_send_emails = NULL;
    public $name=NULL;
    public $fax = NULL;
    public $e_mail = NULL;
    public $tel = NULL;
    public $question = NULL;
    public $quick_form = NULL;
    public $refpage = NULL;
    public $fpath = NULL;
    public $to_addr = NULL;
    public $cookie_serfing = NULL;
    public $serfing = NULL;    
   
    public $is_files = 0;

    /**
    * Class Constructor Feedback
    * Init variables for module feedback.
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.1, 22.12.2010
    */    
    function __construct() {
        if(defined("_LANG_ID")) $this->lang_id = _LANG_ID;
    } // End of Feedback Constructor


    /**
    * Class method AddTblFld
    * function for addition fields or tables for module feedback.
    * @return true or false
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 22.12.2010
    */
    function AddTblFld(){
        if( defined("DB_TABLE_CHARSET")) $this->tbl_charset = DB_TABLE_CHARSET;
        else $this->tbl_charset = 'utf8'; 
        
        // add field refpage to the table TblModfeedback
        if ( !$this->db->IsFieldExist(TblModfeedback, "refpage") ) {
            $q = "ALTER TABLE `".TblModfeedback."` ADD `refpage` varchar( 255 );";
            $res = $this->db->db_Query( $q );
            //echo '<br>$q='.$q.' $res='.$res;
            if( !$res OR !$this->db->result ) return false; 
        }

        // add field fpath to the table TblModfeedback
        if ( !$this->db->IsFieldExist(TblModfeedback, "fpath") ) {
            $q = "ALTER TABLE `".TblModfeedback."` ADD `fpath` varchar( 255 );";
            $res = $this->db->db_Query( $q );
            //echo '<br>$q='.$q.' $res='.$res;
            if( !$res OR !$this->db->result ) return false; 
        }
        
        // create table for strore serfing by pages of site
        $q = "
              CREATE TABLE IF NOT EXISTS `".TblModFeedbackSerfing."` (
              `id_feedback` INT( 4 ) NOT NULL ,
              `uri` VARCHAR( 255 ) NOT NULL ,
              `tstart` INT( 4 ) UNSIGNED NOT NULL ,
              `tstay` TIME NOT NULL ,
              INDEX (  `id_feedback` )
              ) ENGINE=MyISAM DEFAULT CHARSET=".$this->tbl_charset.";
             ";
        $res = $this->db->db_Query( $q );
        //echo '<br>$q='.$q.' $res='.$res;
        if( !$res OR !$this->db->result ) return false;
        
    } //end of function AddTblFld();
    
    /**
    * Class method SaveContact
    * store feedback data to database
    * @return true or false
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 07.01.2011
    */
    function SaveContact(){
        $q="INSERT INTO `".TblModfeedback."` SET
            `to_addr`='".$this->to_addr."',
            `f_name`='".$this->name."',
            `tel`='".$this->tel."',
            `fax`='".$this->fax."',
            `e_mail`='".$this->e_mail."',
            `message`='".$this->question."',
            `date`=NOW( ),
            `refpage`='".$this->refpage."',
            `fpath`='".$this->fpath."'";    
        $res = $this->db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res;
        if( !$res OR !$this->db->result ) return false;
        $this->id = $this->db->db_GetInsertID(); 
        
        if( is_array($this->serfing) ){
            $rows = count($this->serfing);
            for($i=0;$i<$rows;$i++){
                $q = "INSERT INTO `".TblModFeedbackSerfing."` SET
                      `id_feedback`='".$this->id."',
                      `uri`='".$this->serfing[$i]['uri']."',
                      `tstart`='".$this->serfing[$i]['tstart']."',
                      `tstay`='".$this->serfing[$i]['tstay_dt']."'  
                ";
                $res = $this->db->db_Query($q);
                //echo '<br>q='.$q.' res='.$res;
                if( !$res OR !$this->db->result ) return false;
            }
            //echo '<br />$serfing=';print_r($serfing);
        }
        return true;
    }//end of function SaveContact()         
    
    /**
    * Class method GetContentFoId_del
    * get contebt fo id_del
    * @return array fo content
    * @author Bogdan Iglinsky <bi@seotm.com>
    * @version 1.0, 9.04.2012
    */
    function GetContentFoId_del($id_del=NULL){
        if(!is_array($id_del) | empty($id_del)) return false;
        $count=count($id_del);
        $q="SELECT `".TblModfeedback."`.*
        FROM `".TblModfeedback."`
        WHERE
        `".TblModfeedback."`.id in(";
        for($i=0;$i<$count;$i++){
            $q.=$id_del[$i];
            if($i+1<$count) $q.=",";
        }
        $q.=") ORDER BY date";
        //echo '$q='.$q;
        $res = $this->db->db_Query($q);
        $rows = $this->db->db_GetNumRows();
        $array=array();
        for($i=0;$i<$rows;$i++){
            $array[] = $this->db->db_FetchAssoc();  
        }
        return $array;
    } 
} // End of class Feedback

