<?php
 
 /**
  * DBPDO
  * 
  * @package SEOCMS
  * @author seotm
  * @copyright 2012
  * @version $Id$
  * @access public
  */
 class DBPDO {
        private $host;
        private $user;
        private $pwd;
        private $dbname;
        private $dbopen;
        private $persist = false;                     // Persist connection to the database
        private $new_link = false;
        private $charset_client = NULL;
        private $charset_result = NULL;
        private $collation_connection = NULL;
        private static $instance;
        private $DBH = NULL;
        private $STH = NULL;
 
        protected function __construct($config=NULL) {
             ( isset($config['host']) ? $this->host   = $config['host']   : $this->host   = _HOST );
             ( isset($config['user']) ? $this->user   = $config['user']   : $this->user   = _USER );
             ( isset($config['pwd']) ? $this->pwd    = $config['pwd']    : $this->pwd    = _PASSWD );
             ( isset($config['dbname']) ? $this->dbname = $config['dbname'] : $this->dbname = _DBNAME );
             ( isset($config['open']) ? $this->dbopen = $config['open']   : $this->dbopen = _DBOPEN );
             ( isset($config['persist']) ? $this->persist = $config['persist']   : $this->persist = _PERSIST );
             ( isset($config['new_link']) ? $this->new_link = $config['new_link']   : ( ($this->persist=='true' OR $this->persist==1) ? $this->new_link = false   : $this->new_link = true ) );
             //echo '<br>0 $this->host='.$this->host.' $this->user='.$this->user.' $this->pwd='.$this->pwd.' $this->dbname='.$this->dbname;  
             
             try {  
              # MySQL через PDO_MYSQL  
              $this->DBH = new PDO("mysql:host=$this->host;dbname=$this->dbname", $this->user, $this->pwd);
              $this->DBH->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
              
              $this->InitCharsetValues();
              if( !empty($this->charset_client) AND $this->charset_client==$this->charset_result AND $this->charset_client==$this->collation_connection){
                   $q = "SET NAMES '".$this->charset_client."'";
                   $this->DBH->exec($q);
               }
               else{
                   if(!empty($this->charset_client)) {
                       $q = "set character_set_client='".$this->charset_client."'";
                       $this->DBH->exec($q);
                   } 
                   if(!empty($this->charset_result)) {
                       $q = "set character_set_results='".$this->charset_result."'";
                       $this->DBH->exec($q);
                   }
                   if(!empty($this->collation_connection)) {
                       $q = "set collation_connection='".$this->collation_connection."'";
                       $this->DBH->exec($q);               
                   }
               }                
             }  
             catch(PDOException $e) {  
                echo $e->getMessage();  
             }
        }

        private function __clone() {}
 
        public static function getInstance($param=NULL) {
            if (self::$instance === null) {
                self::$instance = new self($param);
                }
                return self::$instance;
        }

       /**
        * DBPDO::InitCharsetValues()
        * set valuse of charset
        * @return void
        */
       function InitCharsetValues()
       {
          if ( defined("DB_CHARACTER_SET_CLIENT") ) $this->charset_client = DB_CHARACTER_SET_CLIENT;
          if ( defined("DB_CHARACTER_SET_RESULT") ) $this->charset_result = DB_CHARACTER_SET_RESULT;
          if ( defined("DB_COLLATION_CONNECTION") ) $this->collation_connection = DB_COLLATION_CONNECTION;
       } //end of function InitCharsetValues()
       
       function Prepare($q){
            $this->STH = $this->DBH->prepare($q);
       }
       
       function Bind($index, $name){
            $this->STH->bindParam($index, $name);
       }
       
       function Execute($data = NULL){
             $this->BeginTransaction();
             try {
                $this->STH->execute($data);
                if( defined("MAKE_DEBUG") AND MAKE_DEBUG==1 ){
                    if( isset($_SESSION['cnt_db_queries']) AND !empty($_SESSION['cnt_db_queries']) ) $cnt = intval($_SESSION['cnt_db_queries'])+1;
                    else $cnt = 1; 
                    $_SESSION['cnt_db_queries'] = $cnt;
                }                
             }  
             catch(PDOException $e) {  
                echo $e->getMessage();
                $this->RollBack();
                return false;  
             }
             $this->Commit();
             return true;
       }
       
       function ExecuteArray($data){
             $keys = array_keys($data);
             if( isset($keys[0]) AND is_array($data[$keys[0]])){
                $cnt = count($keys);
                $this->BeginTransaction();
                try {
                    for($i=0;$i<$cnt;$i++){
                        $this->STH->execute($data[$keys[$i]]);
                        if( defined("MAKE_DEBUG") AND MAKE_DEBUG==1 ){
                            if( isset($_SESSION['cnt_db_queries']) AND !empty($_SESSION['cnt_db_queries']) ) $cnt = intval($_SESSION['cnt_db_queries'])+1;
                            else $cnt = 1; 
                            $_SESSION['cnt_db_queries'] = $cnt;
                        }                        
                    }
                }  
                catch(PDOException $e) {  
                    echo $e->getMessage();
                    $this->RollBack();
                    return false;  
                }
                $this->Commit();
                return true;
             }
             else return false;
       } 
       
       function BeginTransaction(){
            $this->DBH->beginTransaction();
       } 
       
       function Commit(){
            $this->DBH->commit();
       }
       
       function RollBack(){
            $this->DBH->rollBack();
       }         

       function GetInsertID(){ 
            return $this->DBH->lastInsertId();
       }
       
       function FetchAssoc(){
            return $this->STH->fetch(PDO::FETCH_ASSOC);
       }
       
       function FetchAssocAll(){
            return $this->STH->fetchAll(PDO::FETCH_ASSOC);
       }
       
       function RowCount(){
            return $this->STH->rowCount();
       } 

 } // End of DBPDO Class


?>