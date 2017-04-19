<?php

/**
 * 入口文件
 * 1.定义常量
 * 2.加载函数库
 * 3.启动框架
 */
define('WZY',realpath('./'));//定义当前框架所在根目录
define('CORE',WZY.'/core');//框架核心文件目录
define('APP',WZY.'/app');//项目文件所处目录
define('MODULE','app');

define('DEBUG',true);//是否开启调试模式

include "vendor/autoload.php";

if( DEBUG ) {
    $whoops = new \Whoops\Run;
    $errorTitle = '框架出错了';
    $option = new \Whoops\Handler\PrettyPageHandler;
    $option->setPageTitle($errorTitle);
    $whoops->pushHandler($option);
    $whoops->register();
    ini_set('display_error','On');
} else{
    ini_set('display_error','Off');
}
//加载函数库
include CORE.'/common/function.php';
//加载核心类
include CORE.'/kernel.php';
//注册自动加载函数
spl_autoload_register('\core\kernel::load');
//启动框架
\core\kernel::run();