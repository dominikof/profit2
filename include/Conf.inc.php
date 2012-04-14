<?php
if( strstr($_SERVER['SERVER_NAME'], 'seotm.biz') ){
    define("_HOST", "localhost"); // Hostname of the server 
    define("_DBNAME", "seocms2011"); // Database Name
    define("_USER","seocms"); // User to access the database
    define("_PASSWD", "ctjwvc2011"); // Password to access the database
}
else{
    define("_HOST", "localhost"); // Hostname of the server 
    define("_DBNAME", "seocms2011"); // Database Name
    define("_USER","root"); // User to access the database
    define("_PASSWD", ""); // Password to access the database
}

define("_DBOPEN", "true"); // Open Database
define("_PERSIST", "false"); // Type of connection

define( "LOGOUT_TIME", "7200" );      // Time to wait for automatic logout on the back-end
define( "LOGOUT_USER_TIME", "3600" ); // Time to wait for automatic logout on the front-end

define( "ENCODE_PASSWORD_BACKEND", "true" );    // encode or not password of users for the back-end
define( "ENCODE_PASSWORD_FRONTEND", "false" );   // encode or not password of users for the front-end

define( "DB_CHARACTER_SET_CLIENT", "utf8" );   // charset for client  
define( "DB_CHARACTER_SET_RESULT", "utf8" );   // charset for results 
define( "DB_COLLATION_CONNECTION", "utf8" );   // charset for collation connection  
//define( "DB_COLLATION_CONNECTION", "utf8_general_ci" );   // charset for collation connection
define( "DB_CHARSET", "utf8" );   // charset for database
define( "DB_TABLE_CHARSET", "utf8" );   // charset for tables

date_default_timezone_set('Europe/Kiev'); // set local timezone
?>