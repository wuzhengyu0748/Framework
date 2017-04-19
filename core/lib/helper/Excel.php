<?php
namespace core\lib\helper;
/**
 * Excel操作类
 * User: Administrator
 * Date: 2017/4/10
 * Time: 23:51
 */

class Excel
{
    private $objPHPExcel;

    public function __construct()
    {
        $this->objPHPExcel = new \PHPExcel();//初始化Excel表格
        return $this->objPHPExcel;
    }
}