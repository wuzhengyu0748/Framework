<?php
namespace app\ctrl;

use core\kernel;
use core\lib\model;
use core\lib\helper\Excel;

class indexCtrl extends kernel
{
    public function index()
    {
        $objPHPExcel = new \PHPExcel();
        $array = array();
        for($i=1;$i<=3;$i++)
        {
            //这里默认会创建一个sheet 所以跳过1
            if($i>1){
                $objPHPExcel->createSheet();//创建新的活动sheet
            }
            $objPHPExcel->setActiveSheetIndex($i-1);//把新创建的sheet设定为当前活动sheet
            $objSheet = $objPHPExcel->getActiveSheet();//获取当前活动sheet
            $objSheet->setTitle($i."年级");//设置sheet标题
            $data = $array;//查询的二维数组
            $objSheet->setCellValue('A1','姓名')->setCellValue('B1','班级')->setCellValue('C1','分数');
//            $arr = [
//                [],
//                ['','姓名','分数'],
//                ['','张三','60'],
//                ['','李四','65']
//            ];
//            $objSheet->fromArray($arr);//直接加载数据块
            $j = 2;
            foreach($data as $key => $val){
                $objSheet->setCellValue('A'.$j,$val['username'])->setCellValue('B'.$j,$val['class'])->setCellValue('C'.$j,$val['score']);
                $j++;
            }
            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel5');//生成Excel文件
            $objWriter->save('./export.xls');//保存文件
        }

        $this->display('index/index');
    }
}