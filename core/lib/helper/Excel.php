<?php
namespace core\lib\helper;
/**
 * Excel操作类
 * User: wzy@fhxzh
 * Date: 2017/4/10
 * Time: 23:51
 */

class Excel
{
    private static $objPHPExcel;//PHPExcel对象

    public function __construct()
    {
        self::$objPHPExcel = new \PHPExcel();//初始化Excel表格
        //设置缓存方法
        $this->setCache();
    }

    /**
     * 导出
     * @param array $title 标题
     * @param array $data 数据 每行数据的列必须与标题对应
     * @param string $type 导出类型
     * @param string $filename 导出文件名
     * @param bool $browser 是否输出浏览器
     * @throws \PHPExcel_Reader_Exception
     */
    public function export($title,$data,$type='Excel5',$filename='excel',$browser=true)
    {
        //获取当前活动sheet的操作对象
        $objSheet = self::$objPHPExcel->getActiveSheet();
        //设置excel样式
        $this->style($objSheet);
        //给当前活动sheet设置名称
        $objSheet->setTitle("demo");
        //循环设置标题
        foreach($title as $key => $val){
            $objSheet->setCellValue($key.'1',$val);
        }
        //填充数据
        $j = 2;//起始行
        foreach($data as $key => $val){//循环行
            foreach($title as $k => $v){//循环列
                $objSheet->setCellValue($k.$j,array_shift($val));
            }
            ++$j;
        }
        //按指定格式生成excel
        $objWriter = \PHPExcel_IOFactory::createWriter(self::$objPHPExcel,$type);

        if($browser)
        {//输出到浏览器
            $this->browser_export($type,$filename);
            $objWriter->save("php://output");
        }
        else
        {//保存到服务器
            if($type == 'Excel2007'){
                $objWriter->save(WZY.'/'.$filename.'.xlsx');
            }else {
                $objWriter->save(WZY.'/'.$filename.'.xls');
            }
        }
    }

    /**
     * 输出到浏览器
     * @param string $type
     * @param $filename
     */
    private function browser_export($type = 'Excel5',$filename)
    {
        if($type == 'Excel5'){
            header('Content-Type:application/vnd.ms-excel');//excel03
        }else{
            header('Content-Type:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');//excel07
        }
        //输出文件名
        if($type == 'Excel2007'){
            header('Content-Disposition:attachment;filename="'.$filename.'.xlsx"');
        }else {
            header('Content-Disposition:attachment;filename="'.$filename.'.xls"');
        }
        //禁止浏览器缓存
        header('Cache-Control:max-age=0');
    }

    /**
     * 样式控制
     * @param $obj 活动sheet操作对象
     */
    private function style($obj)
    {
        //设置excel文件默认水平垂直居中
        $obj->getDefaultStyle()
            ->getAlignment()
            ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER)
            ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //设置默认全局字体和大小
        $obj->getDefaultStyle()->getFont()->setName('微软雅黑')->setSize('14');
        //设置标题字体和大小
        $obj->getStyle("A1:Z1")->getFont()->setSize('16')->setBold(true);
    }

    /**
     * 读取Excel 导入操作用
     * @param $filename excel文件路径
     * @return array
     */
    public function toArray($filename)
    {
        //引入PHPExcel的IO对象
        require PHPEXCEL_ROOT."PHPExcel/IOFactory.php";
        //加载Excel文件
        $ExcelObj = \PHPExcel_IOFactory::load($filename);

        //分批处理数据
        foreach($ExcelObj->getWorksheetIterator() as $sheet){//循环sheet读取数据
            foreach($sheet->getRowIterator() as $row){//逐行处理
                if($row->getRowIndex() < 2){//标题不读
                    continue;
                }
                $row_data = array();
                foreach($row->getCellIterator() as $cell){//逐列处理
                    $res = $cell->getValue();
                    if(empty($res)){
                        break;
                    }
                    $row_data[] = $res;
                }
                $data[] = $row_data;
            }
        }
        return $data ? $data : array();
    }

    /**
     * 设置缓存方式
     * @param $type string [memcache][gzip]
     */
    private function setCache($type='gzip')
    {
        if($type == 'gzip')
        {
            $cacheMethod = \PHPExcel_CachedObjectStorageFactory::cache_in_memory_gzip;//缓存方式
            $cacheSettings = array();//缓存设置
        }
        else
        {
            $cacheMethod = \PHPExcel_CachedObjectStorageFactory::cache_to_memcache;
            $cacheSettings = array(
                'memcacheServer'=>'localhost',
                'memcachePort'=>11211,
                'cacheTime'=>600
            );
        }

        \PHPExcel_Settings::setCacheStorageMethod($cacheMethod,$cacheSettings);
    }


}