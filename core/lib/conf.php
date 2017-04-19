<?php
namespace core\lib;


/**
 * 配置类
 */
class conf
{
    public static $conf = array();

    /**
     * 加载单个配置项
     * @param $name
     * @param $file
     * @return mixed
     * @throws \Exception
     */
    public static function get($name,$file)
    {
        //如果缓存中已经有配置就不再加载
        if( isset(self::$conf[$file]) ) {
            return self::$conf[$file][$name];
        }
        else {
            //判断配置文件是否存在
            $path = WZY . '/core/config/' . $file . '.php';
            if (is_file($path)) {
                $conf = include $path;
                //判断配置项是否存在
                if (isset($conf[$name])) {
                    //缓存配置
                    self::$conf[$file] = $conf;
                    return $conf[$name];
                } else {
                    throw new \Exception('没有这个配置项' . $name);
                }
            } else {
                throw new \Exception('找不到配置文件' . $file);
            }
        }
    }

    /**
     * 加载整个配置文件
     * @param $file
     * @return mixed
     * @throws \Exception
     */
    public static function all($file)
    {
        //如果缓存中已经有配置就不再加载
        if( isset(self::$conf[$file]) ) {
            return self::$conf[$file];
        }
        else {
            //判断配置文件是否存在
            $path = WZY . '/core/config/' . $file . '.php';
            if (is_file($path)) {
                $conf = include $path;
                self::$conf[$file] = $conf;
                return $conf;
            } else {
                throw new \Exception('找不到配置文件' . $file);
            }
        }
    }
}