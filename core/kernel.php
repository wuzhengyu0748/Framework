<?php
namespace core;

class kernel
{
    public static $classMap = array();
    public $assign = array();

    public static function run()
    {
        header("Content-Type:text/html;charset=utf-8");
        //实例化路由类
        $route = new \core\lib\route();
        //控制器名
        $ctrlClass = $route->ctrl;
        //方法名
        $action = $route->action;
        //控制器文件路径
        $ctrlfile = APP.'/ctrl/'.$ctrlClass.'Ctrl.php';
        //控制器类名
        $ctrlClass = '\\'.MODULE.'\ctrl\\'.$ctrlClass.'Ctrl';
        if( is_file($ctrlfile) ) {
            //加载控制器
            include $ctrlfile;
            //路由分发
            $ctrl = new $ctrlClass();
            $ctrl -> $action();
        } else {
            throw new \Exception('找不到控制器'.$ctrlClass);
        }
    }

    /**
     * 类自动加载
     */
    public static function load($class)
    {
        //判断是否已经引入
        if( isset($classMap[$class]) ){
            return true;
        }
        else
        {
            $class = str_replace('\\', '/', $class);
            $file = WZY.'/'. $class . '.php';
            if (is_file($file)) {
                include $file;
                self::$classMap[$class] = $class;
            } else {
                return false;
            }
        }
    }

    public function assign($name,$value)
    {
        $this->assign[$name] = $value;
    }

    public function display($tpl)
    {
        $file = APP.'/views/'.$tpl.'.html';
        if( is_file($file) ) {
            extract($this->assign);
            include $file;
        }
    }
}