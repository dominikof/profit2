<?php
/** 
 * Реализация паттерна Singleton 
 * @name Singleton 
 * @author Yaroslav Gyryn
 */ 
class Singleton {
    /** 
     * Статический массив обьектов, в котором мы 
     * будем хранить экземпляры классов
     * @var $objects 
     */ 
    protected static $objects = null; 
 
    /**
     * Защищаем от создания через new Singleton
     * @return Singleton
     */
    private function __construct() { /* ... */ }
 
    /**
     * Защищаем от создания через клонирование
     * @return Singleton
     */
    private function __clone() { /* ... */ }
 
    /**
     * Возвращает единственный экземпляр класса $class
     * @return $objects[$class]
     */
    public static function getInstance($class, $p1=NULL, $p2=NULL, $p3=NULL, $p4=NULL, $p5=NULL, $p6=NULL, $p7=NULL, $p8=NULL, $p9=NULL, $p10=NULL) {
        //echo '<br>'.$class.'  p1='.$p1;
        if(!isset(self::$objects[$class])) {
              self::$objects[$class] = new $class($p1, $p2, $p3, $p4, $p5, $p6, $p7, $p8, $p9, $p10);   
        }
        return self::$objects[$class];         
    }

    /**
     * Возвращает все созданные обьекты
     * @return $objects
     */    
    public static function getAllOblects() {
        return self::$objects;         
    } 

    /**
     * Возвращает количество созданных обьектов
     * @return count
     */    
    public static function getCountAllOblects() {
        return count(self::$objects);         
    } 
        
}
?>
