<?php 

class sysBase {

    private static $instance;

    public function __construct()
    {
        self::$instance =& $this;
    }

    public static function &get_instance()
    {
        return self::$instance;
    }
}

function &get_instance()
{
    return sysBase::get_instance();
}
function check_init($name,$class,$params=NULL)
{   
    $base= &get_instance();
    if(isset($base->$name)){
        //echo '<br>Exist';
        return $base->$name;
    }
    else{
        if(!empty($params)) $str_to_eval = ' $base->'.$name.' = new '.$class.'('.$params.');';
        else $str_to_eval = ' $base->'.$name.' = new '.$class.'();';
        //echo '<br>$str_to_eval='.$str_to_eval;
        eval($str_to_eval);
        //$base->$name = new $class($params);
        return $base->$name;
    }
}

function check_init_item($name,$value)
{   
    $base= &get_instance();
    if(isset($base->$name)) return $base->$name;
    else
    {
        $base->$name = $value;
        return $base->$name;
    }
}
function get_init_item($name)
{   
    $base= &get_instance();
    if(isset($base->$name)) return $base->$name;
    else
        return NULL;
}
function check_init_txt($name,$table_name,$lang_id=NULL)
{   
    $base= &get_instance();
    if(isset($base->$name)) return $base->$name;
    else
    {
        if(!empty($lang_id)) $base->$name = SystemSpr::getAllmsg($table_name, $lang_id);
        else $base->$name = SystemSpr::getAllmsg($table_name);
        return $base->$name;
    }
}
?>