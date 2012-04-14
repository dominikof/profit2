<?
class AntiHacker
{
	public static function AntiHack( $str )
	{
		//echo '<br>000$str='.$str;
        $str = AntiHacker::StripEmptyChars( $str );
		$str = AntiHacker::AntiXSS( $str );
		$str = AntiHacker::AntiSQLInjection( $str );	
		return $str;
	}
    public static function AntiHackRequest( $str, $default_value=NULL )
    {    
        //echo '<br>111$str='.$str;
        if(!isset($_REQUEST[$str])) return $default_value;
        $str = AntiHacker::StripEmptyChars( $_REQUEST[$str] );
        //$str = AntiHacker::AntiXSS( $str );
        $str = AntiHacker::AntiSQLInjection( $str );    
        return $str;
    }
     public static function AntiHackRequestPass( $str, $default_value=NULL )
    {
        if(!isset($_REQUEST[$str])) return $default_value;
        $str = AntiHacker::StripEmptyChars( $_REQUEST[$str] );
        $str = AntiHacker::AntiSQLInjection( $str );    
        return $str;
    }
     public static function AntiHackArrayRequest( $str, $default_value=NULL )
    {
        if(!isset($_REQUEST[$str])) return $default_value;
        return $_REQUEST[$str];
    }
    
	public static function StripEmptyChars( $str )
	{
		//echo '<br>$str='.$str.' print_r()=';
        //print_r($str);
        $str = trim( $str );
		
		return $str;
	}
	public static function AntiXSS( $str )
	{
		$str = htmlspecialchars( $str );
		
		return $str;
	}
	public  static function AntiSQLInjection( $str )
	{
		if( !get_magic_quotes_gpc() )
			$str = mysql_escape_string( $str );
		
		return $str;
	}
}
function AntiHackRequest( $str, $default_value=NULL )
    {
        if(!isset($_REQUEST[$str])) return $default_value;
        $str = StripEmptyChars( $_REQUEST[$str] );
//        $str = AntiHacker::AntiXSS( $str );
        $str = AntiSQLInjection( $str );    
        return $str;
    }
 function StripEmptyChars( $str )
    {
        $str = trim( $str );
        
        return $str;
    }
 function AntiXSS( $str )
    {
        $str = htmlspecialchars( $str );
        
        return $str;
    }
 function AntiSQLInjection( $str )
    {
        if( !get_magic_quotes_gpc() )
            $str = mysql_escape_string( $str );
        
        return $str;
    }
?>