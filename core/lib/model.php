<?php
namespace core\lib;

use core\lib\conf;
use core\lib\drive\db\Medoo;
/**
 * Class model
 * @package core\lib
 */
class model extends Medoo
{
    public function __construct()
    {
        //加载数据库配置
        $option = conf::all('database');
        //连接数据库
        parent::__construct($option);
    }
}