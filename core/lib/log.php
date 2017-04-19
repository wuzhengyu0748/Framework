<?php
namespace core\lib;

/**
 * 日志类
 * @package core\lib
 */
class log
{
    public static $class;

    public static function init()
    {
        //确定存储方式
        $drive = conf::get('DRIVE','log');
        $class = '\core\lib\drive\log\\'.$drive;
        self::$class = new $class();
    }

    public static function log($name)
    {
        self::$class->log($name);
    }
}