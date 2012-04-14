<?php
// ================================================================================================
// System : PrCSM05
// Module : Database
// Version : 1.0.0
// Date : 12.03.2005
// Licensed To:
// Igor Trokhymchuk ihoru@mail.ru
// Andriy Lykhodid las_zt@mail.ru
// ================================================================================================

// ================================================================================================
//    Class             : Db
//    Version           : 1.0.0
//    Date              : 12.03.2005
//    Constructor       : Yes
//    Parms             : Host      Host Name
//                        User      UserID to database
//                        pwd       Password to database
//                        dbName    Name of the database to connect type
//                        open      Open database (true/false)
//    Returns           : None
//    Description       : Database Abstraction Layer
// ================================================================================================
//    Programmer        :  RAM R. Ramautar, Andriy Lykhodid
//    Date              :  12.03.2005
//    Reason for change :  Creation
//    Change Request Nbr:  N/A
// ================================================================================================

 class DB {

        var $conn = NULL;                         // Connection ID
        var $result = false;                      // ResultID of Query
        var $dbname;                              // Database Name
        var $persist = false;                     // Persist connection to the database
        var $new_link = false;
        var $open = false;                        // Open ID
        var $error = false;                       // Error Found
        var $errno = null;                        // Error Number
        var $errdet = null;                       // Error Detail
        var $charset_client = NULL;
        var $charset_result = NULL;
        var $collation_connection = NULL;
        
        // ================================================================================================
        //    Function          : DB (Constructor)
        //    Version           : 1.0.0
        //    Date              : 13.01.2005
        //    Parms             : Host          Host Name
        //                        User          UserID to database
        //                        pwd           Password to database
        //                        dbName        Name of the database to connect type
        //                        open          Open database (true/false)
        //    Returns           : Error Indicator
        //    Description       : Opens and selects a dabase
        // ================================================================================================

        function DB( $host = "", $user = "", $pwd = "", $dbname = "", $open = "", $persist= "", $new_link= "" )
        {
                //Check if Constants are overrulled
                ( $host   != "" ? $this->host   = $host   : $this->host   = _HOST );
                ( $user   != "" ? $this->user   = $user   : $this->user   = _USER );
                ( $pwd    != "" ? $this->pwd    = $pwd    : $this->pwd    = _PASSWD );
                ( $dbname != "" ? $this->dbname = $dbname : $this->dbname = _DBNAME );
                ( $open   != "" ? $this->dbopen = $open   : $this->dbopen = _DBOPEN );
                ( $persist!= "" ? $this->persist = $persist   : $this->persist = _PERSIST );
                ( $new_link!= "" ? $this->new_link = $new_link   : ( ($this->persist=='true' OR $this->persist==1) ? $this->new_link = false   : $this->new_link = true ) );

                //echo '<br>0 $this->host='.$this->host.' $this->user='.$this->user.' $this->pwd='.$this->pwd.' $this->dbname='.$this->dbname;
                
                // Open the Database
                //$Rc = $this->db_Open();
                $Rc = $this->db_Select( $this->dbname );
        } // End of DB Constructor

       // ================================================================================================
       // Function : InitCharsetValues
       // Version : 1.0.0
       // Date : 13.04.2007
       //
       // Parms :
       // Returns : true,false / Void
       // Description : set valuse of charset 
       // ================================================================================================
       // Programmer : Igor Trokhymchuk
       // Date : 13.04.2007
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function InitCharsetValues()
       {
          if ( defined("DB_CHARACTER_SET_CLIENT") ) $this->charset_client = DB_CHARACTER_SET_CLIENT;
          if ( defined("DB_CHARACTER_SET_RESULT") ) $this->charset_result = DB_CHARACTER_SET_RESULT;
          if ( defined("DB_COLLATION_CONNECTION") ) $this->collation_connection = DB_COLLATION_CONNECTION;
       } //end of function InitCharsetValues()         

        // ================================================================================================
        //    Function          : db_Open
        //    Version           : 1.0.0
        //    Date              : 13.01.2005
        //
        //    Parms             : None
        //    Returns           : ErrorID
        //    Description       : Open the Database
        // ================================================================================================
        function db_Open()
        {
                if( !$this->dbopen )
                {
                  return false;                                                                                                                                                                                                                // Do not open the database
                }
                
                // Select the type to connect;
                //echo '<br>$this->persist='.$this->persist.' $this->new_link='.$this->new_link;
                if( !empty($this->persist) AND $this->persist!='false' )
                {
                        $func = 'mysql_pconnect';
                }else
                {
                        $func = 'mysql_connect';
                }
                //echo '<br>$func='.$func; 
                
                // Reset the error flag
                $this->db_ResetError();
                // Connect to the database
                $this->conn = $func( $this->host, $this->user, $this->pwd, $this->new_link );
                //echo '<br>db_Open :: $this->host='.$this->host.' $this->user='.$this->user.' $this->pwd='.$this->pwd.' $this->dbname='.$this->dbname.'  $this->conn='.$this->conn.' $this->error='.$this->error;
                 if( !$this->conn )
                 {
                          $this->db_SetError();
                          echo '<br>db_Open :: $this->conn='.$this->conn.' $this->error='.$this->error;
                          die("Could not connect: ".$this->db_GetErrNo().' '.$this->db_GetErrDetail());
                 }else
                 {
                          $this->open = true;
                          return $this->conn;
                 }
        } // End of db_Open


        // ================================================================================================
        //    Function          : db_Select
        //    Version           : 1.0.0
        //    Date              : 12.03.2005
        //    Parms             : Database Name
        //    Returns           : ErrorID
        //    Description       : Select a database
        // ================================================================================================
        function db_Select( $dbname = "" )
        {
                //( $dbname != "" ? $this->dbname = $dbname : $this->dbname = _DBNAME );
                $res = $this->db_Open();
                $res = mysql_select_db( $this->dbname, $this->conn );

                // Reset the error flag
                $this->db_ResetError();

                if( mysql_errno() ){
                    $this->db_SetError();
                }

               $this->InitCharsetValues();
               if( !empty($this->charset_client) AND $this->charset_client==$this->charset_result AND $this->charset_client==$this->collation_connection){
                   $q = "SET NAMES '".$this->charset_client."'";
                   $res = mysql_query($q);
                   //echo '<br>$q='.$q.' $res='.$res.' $this->errdet='.$this->errdet;
                   if (!$res) {$this->db_SetError(); return false;}
               }
               else{
                   if(!empty($this->charset_client)) {
                       $q = "set character_set_client='".$this->charset_client."'";
                       $res = mysql_query($q);
                       //echo '<br>$q='.$q.' $res='.$res.' $this->errdet='.$this->errdet;
                       if (!$res) {$this->db_SetError(); return false;}
                   } 
                   if(!empty($this->charset_result)) {
                       $q = "set character_set_results='".$this->charset_result."'";
                       $res = mysql_query($q);
                       //echo '<br>$q='.$q.' $res='.$res.' $this->errdet='.$this->errdet; 
                       if (!$res) {$this->db_SetError(); return false;}
                   }
                   if(!empty($this->collation_connection)) {
                       $q = "set collation_connection='".$this->collation_connection."'";
                       $res = mysql_query($q);               
                       //echo '<br>$q='.$q.' $res='.$res.' $this->errdet='.$this->errdet; 
                       if (!$res) {$this->db_SetError(); return false;}
                   }
               } 
               return $this->error;
        } // End of db_Select

        
        // ================================================================================================
        //    Function          : db_Close
        //    Version           : 1.0.0
        //    Date              : 13.01.2005
        //
        //    Parms             : None
        //    Returns           : ErrorID
        //    Description       : Close the Database
        // ================================================================================================

        function db_Close()
        {
                // Reset the error flag
                $this->db_ResetError();

                $Rc = mysql_close( $this->conn );
                //echo $Rc;
                $this->conn = NULL;
                $this->open = false;
        }



        // ================================================================================================
        //    Function          : db_Query
        //    Version           : 1.0.0
        //    Date              : 13.01.2005
        //
        //    Parms             : sql                SQL Statement to run
        //    Returns           : ErrorID
        //    Description       : Run an SQL Statement
        // ================================================================================================

        function db_Query( $sql = '' )
        {
//                echo '<br> $this->$sql='.$sql;
                // Reset the error flag
                $this->db_ResetError();
                /*echo '
                <br>=============================================================================================================
                <br>=>db_Query :: $this->host='.$this->host.' $this->user='.$this->user.' $this->pwd='.$this->pwd.' $this->dbname='.$this->dbname.'  $this->conn='.$this->conn.' $this->error='.$this->error.'
                <br>=============================================================================================================';
                */
//                $init= microtime();
                $this->result = mysql_query( trim($sql), $this->conn );
//                 echo '<br> $this->$sql='.$sql;
                //echo '<br> $this->result='.$this->result;
                if( !$this->result )
                {
                        $this->db_SetError();
                }
//                echo '<br> $this->$sql='.$sql.'<br />time='.microtime()-$init;
                if( defined("MAKE_DEBUG") AND MAKE_DEBUG==1 ){
                    if( isset($_SESSION['cnt_db_queries']) AND !empty($_SESSION['cnt_db_queries']) ) $cnt = $_SESSION['cnt_db_queries']+1;
                    else $cnt = 1;
//                  echo '<br>QUERIES: '.$_SESSION['cnt_db_queries']; 
                    $_SESSION['cnt_db_queries'] = $cnt;
//                  echo '<br>QUERIES: '.$_SESSION['cnt_db_queries'];
                    //echo '<br>$cnt='.$cnt;
                }

                return ( $this->result != false );
        } // End of db_Query

        
        
        
        // ================================================================================================
        //    Function          : db_GetNumRows
        //    Version           : 1.0.0
        //    Date              : 13.01.2005
        //
        //    Parms             : Table                        Name of table
        //    Returns           : Number of rows
        //    Description       : Get the number of rows in a given table
        // ================================================================================================
        function db_GetNumRows()
        {
                // Reset the error flag
                $this->db_ResetError();
                return( @mysql_num_rows( $this->result ) );
         } // End of db_GetNumRows

         
         
         
        // ================================================================================================
        //    Function          : db_FreeResult
        //    Version           : 1.0.0
        //    Date              : 13.01.2005
        //
        //    Parms             : None
        //    Returns           : ErrorID
        //    Description       : Free the Result data
        // ================================================================================================

        function db_FreeResult() {
                // Reset the error flag
                $this->db_ResetError();

                $this->error = @mysql_free_result( $this->result );
                $this->result = '';
                if( !$this->error )
                {
                        $this->db_SetError();
                }

                return ( $this->error != false );
        } // End of db_FreeResult



        // ================================================================================================
        //    Function          : db_FetchAssoc
        //    Version           : 1.0.0
        //    Date              : 13.01.2005
        //
        //    Parms             : None
        //    Returns           : Array  Array containing the row of data
        //    Description       : Fetch a row of data into a associative array
        // ================================================================================================

        function db_FetchAssoc()
        {
                // Reset the error flag
                $this->db_ResetError();

                if(!$this->result)
                {
                        $this->db_SetError();
                        return false;
                }

                return @mysql_fetch_assoc( $this->result );
        } //--- end of db_FetchAssoc



        // ================================================================================================
        //    Function          : db_FetchArray
        //    Version           : 1.0.0
        //    Date              : 13.01.2005
        //
        //    Parms             : None
        //    Returns           : Array                Array containing the row of data
        //    Description       : Fetch a row of data into a indexed array
        // ================================================================================================

        function db_FetchArray()
        {
                // Reset the error flag
                $this->db_ResetError();

                if( !$this->result )
                {
                        $this->db_SetError();
                        return false;
                }

                return @mysql_fetch_array( $this->result, MYSQL_NUM );
        } //--- end of db_FetchArray



        // ================================================================================================
        //    Function          : db_FetchObject
        //    Version           : 1.0.0
        //    Date              : 13.01.2005
        //
        //    Parms             : None
        //    Returns           : Object with a row of data
        //    Description       : Fetch a row of data into an object
        // ================================================================================================

        function db_FetchObject()
        {
                // Reset the error flag
                $this->db_ResetError();

                if( !$this->result )
                {
                        $this->db_SetError();
                        return false;
                }

                return ( @mysql_fetch_object( $this->result, MYSQL_ASSOC ) );
        } //--- end of db_FetchObject

        

        // ================================================================================================
        //    Function          : db_FetchRow
        //    Version           : 1.0.0
        //    Date              : 20.04.2006
        //
        //    Parms             : None
        //    Returns           : Object with a row of data
        //    Description       : Fetch a row of data as an enumerated array
        // ================================================================================================

        function db_FetchRow()
        {
                // Reset the error flag
                $this->db_ResetError();

                if( !$this->result )
                {
                        $this->db_SetError();
                        return false;
                }

                return ( @mysql_fetch_row( $this->result ) );
        } //--- end of db_FetchRow




        // ================================================================================================
        //    Function          : db_ListTables
        //    Version           : 1.0.0
        //    Date              : 13.01.2005
        //
        //    Parms             : sql                SQL Statement to run
        //    Returns           : ErrorID
        //    Description       : Run an SQL Statement
        // ================================================================================================

        function db_ListTables( $sql = '' )
        {
                // Reset the error flag
                $this->db_ResetError();
                $this->result = mysql_query( "SHOW TABLES" );
                //echo '<br> $this->result='.$this->result;
                if( !$this->result )
                {
                        $this->db_SetError();
                }

                return ( $this->result != false );
        } // End of db_ListTables        
        
        
        // ================================================================================================
        //    Function          : db_GetInsertID
        //    Version           : 1.0.0
        //    Date              : 13.01.2005
        //
        //    Parms             : None
        //    Returns           : InsertID        Returns the last Insert ID
        //    Description       : Get the Last Insert ID
        // ================================================================================================

        function db_GetInsertID()
        {
                // Reset the error flag
                $this->db_ResetError();

                if( !$this->result )
                {
                        $this->db_SetError();
                        return false;
                }

                return ( @mysql_insert_id( $this->conn ) );
        } //--- end of db_GetInsertID




        // ================================================================================================
        //    Function          : db_DataSeek
        //    Version           : 1.0.0
        //    Date              : 13.01.2005
        //
        //    Parms             : Row                Record # within the result
        //    Returns           : Array                Array containing the row of data
        //    Description       : Position the Pointer within a Result mannually
        // ================================================================================================

        function db_DataSeek( $row )
        {
                // Reset the error flag
                $this->db_ResetError();

                if( !$this->result )
                {
                        $this->db_SetError();
                        return false;
                }

                return ( @mysql_data_seek( $this->result, $row ) );
        } //--- end of db_DataSeek



        // ================================================================================================
        //    Function          : db_ResetError
        //    Version           : 1.0.0
        //    Date              : 13.01.2005
        //
        //    Parms             : None
        //    Returns           : true
        //    Description       : Reset the error flags and information
        // ================================================================================================
        function db_ResetError()
        {
                $this->error  = null;
                $this->errno  = null;
                $this->errdet = null;
                return 0;                         // Always return OK.
        } //--- end of db_ResetError



        // ================================================================================================
        //    Function          : db_SetError
        //    Version           : 1.0.0
        //    Date              : 13.01.2005
        //
        //    Parms             : None
        //    Returns           : None
        //    Description       : Set the error flags and information
        // ================================================================================================

        function db_SetError()
        {
                       $this->error  = true;
                       $this->errno  = mysql_errno();
                       $this->errdet = mysql_error();
        } //--- end of db_SetError




        // ================================================================================================
        //    Function          : db_GetConnID
        //    Version           : 1.0.0
        //    Date              : 13.01.2005
        //
        //    Parms             : None
        //    Returns           : ConnectionID
        //    Description       : Get the connection ID to the server
        // ================================================================================================

        function db_GetConnID()
        {
                return $this->conn;
        } //--- end of db_GetConnID




        // ================================================================================================
        //    Function          : db_IsOpen
        //    Version           : 1.0.0
        //    Date              : 13.01.2005
        //
        //    Parms             : None
        //    Returns           : OpenID
        //    Description       : Returns if the database is open or not
        // ================================================================================================
        function db_IsOpen()
        {
                return $this->open;
        } //--- end of db_IsOpen




        // ================================================================================================
        //    Function          : db_IsError
        //    Version           : 1.0.0
        //    Date              : 13.01.2005
        //
        //    Parms             : None
        //    Returns           : Error Status
        //    Description       : Returns the current value for the Error Indicator
        // ================================================================================================

        function db_IsError()
        {
                return $this->error;
        } //--- end of db_IsError



        // ================================================================================================
        //    Function          : db_GetErrNo
        //    Version           : 1.0.0
        //    Date              : 13.01.2005
        //
        //    Parms             : None
        //    Returns           : Returns the Current ErrorNo
        //    Description       : Returns the ErrorNumber of the Message
        // ================================================================================================

        function db_GetErrNo()
        {
                return $this->errno;
        } //--- end of db_GetErrNo




        // ================================================================================================
        //    Function          : db_GetErrorDetail
        //    Version           : 1.0.0
        //    Date              : 13.01.2005
        //
        //    Parms             : None
        //    Returns           : Error Detail
        //    Description       : Return the current detail of the error
        // ================================================================================================

        function db_GetErrDetail()
        {
                return $this->errdet;
        } //--- end of db_GetErrorDetail




        // ================================================================================================
        //    Function          : db_SetPersist
        //    Version           : 1.0.0
        //    Date              : 13.01.2005
        //
        //    Parms             : None
        //    Returns           : None
        //    Description       : The the Persitance level of a database connection
        // ================================================================================================

        function db_SetPersist( $persist = "" )
        {
                ( $persist != "" ? $this->persist = $persist : $this->persist = _PERSIST );
        } //--- end of db_SetPersist




        // ================================================================================================
        //    Function          : getVersion
        //    Version           : 1.0.0
        //    Date              : 13.01.2005
        //
        //    Parms             : None
        //    Returns           : None
        //    Description       : The the Persitance level of a database connection
        // ================================================================================================
          function getVersion()
          {
             return "Database Layer Version 1.0.0";
          } //--- end of getVersion




        // ================================================================================================
        //    Function          : db_SetConfig
        //    Version           : 1.0.0
        //    Date              : 13.01.2005
        //
        //    Parms             : None
        //    Returns           : None
        //    Description       : The the Persitance level of a database connection
        // ================================================================================================

       function db_SetConfig( $host = "", $user = "", $pwd = "", $dbname = "", $open = "", $persist= "", $new_link= "" )
       {
                //Check if Constants are overrulled
                ( $host   != "" ? $this->host   = $host   : $this->host   = _HOST );
                ( $user   != "" ? $this->user   = $user   : $this->user   = _USER );
                ( $pwd    != "" ? $this->pwd    = $pwd    : $this->pwd    = _PASSWD );
                ( $dbname != "" ? $this->dbname = $dbname : $this->dbname = _DBNAME );
                ( $open   != "" ? $this->dbopen = $open   : $this->dbopen = _DBOPEN );
                ( $persist!= "" ? $this->persist = $persist   : $this->persist = _PERSIST );
                ( $new_link!= "" ? $this->new_link = $new_link   : ( ($this->persist=='true' OR $this->persist==1) ? $this->new_link = false   : $this->new_link = true ) );
                
                // Open the Database
                 //$Rc = $this->db_Open();
                 $Rc = $this->db_Select($this->dbname);
       } // End of db_SetConfig
       
       
       // ================================================================================================
       // Function : IsTableExist
       // Version : 1.0.0
       // Date : 09.11.2006
       //
       // Parms :   $Table  / name of table, from which will be checking
       // Returns : return 1 or 0
       // Description : return exist or not (1 or 0) table in the database
       // ================================================================================================
       // Programmer : Igor Trokhymchuk
       // Date : 09.11.2006 
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function IsTableExist($Table)
       {
           $image = NULL;
           
           $tmp_db = new DB();
           $tmp_db->db_ListTables();
           if ( !$tmp_db->result) return false;
           $rows = $tmp_db->db_GetNumRows(); 

           for($i=0; $i<$rows; $i++){
               $row = $tmp_db->db_FetchAssoc();
               foreach($row as $key=>$value){
                //echo " $value\n";
                if ($value==$Table) return true;
               }
           }
          return false;
       } //end of function IsTableExist()  
       
       // ================================================================================================
       // Function : AutoInsertColumnToTable
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
       function AutoInsertColumnToTable( $table=NULL, $column=NULL, $column_params=NULL)
       {
          $db = new DB();
          if ( !$this->IsFieldExist($table, "$column") ) {
            $q = "ALTER TABLE `".$table."` ADD `$column` $column_params";
            $res = $db->db_Query($q);
            //echo '<br>q='.$q.' res='.$res.' $db->result='.$db->result;
            if ( !$res OR !$db->result ) return false;    
          }
          return true;
       } //end of function AutoInsertColumnToTable()
       
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
       function IsFieldExist($Table, $field )
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
                   //echo '<br>$meta->name='.$meta->name; 
                   if ($meta->name==$field) return true;
                }
                $i++;
           }
           return false;
       } //end of function IsFieldExist()
       

 } // End of DB Class


?>