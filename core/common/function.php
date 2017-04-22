<?php
/**
 * 自定义函数库
 */

/**
 * 输出
 * @param $var
 */
function p($var)
{
    if(is_bool($var))
    {
        var_dump($var);
    }
    else if(is_null($var))
    {
        var_dump(NULL);
    }
    else
    {
        echo "<pre style='position: relative;z-index: 1000;padding: 10px;border-radius: 5px;background: #f5f5f5;border:1px solid #aaa;font-size: 14px;line-height: 18px;opacity:0.9;'>".print_r($var,true)."</pre>";
    }
}

/**
 * 递归遍历文件夹
 * @param $path
 * @return array
 */
function readDirectory($path)
{
    static $arr;
    //打开指定目录
    $handle = opendir($path);
    //读目录
    while( ($item=readdir($handle)) !== false ){//0
        //.和..
        if($item!='.' && $item!='..'){
            if(is_file($path.'/'.$item)){//是文件
                $arr[] = $item;
            }else if(is_dir($path.'/'.$item)){//是目录
                $func = __FUNCTION__;
                $func($path.'/'.$item);
            }
        }
    }
    closedir($handle);//关闭句柄
    return $arr ? $arr : array();
}

/**
 * 字节格式化 把字节数格式为 Byte K M G T P E Z Y描述的大小
 * @return string
 */
function byteFormat($size, $dec=2) {
    $a = array('Byte', 'KB', 'MB', 'GB', 'TB', 'PB','EB','ZB','YB');
    $pos = 0;
    while ($size >= 1024) {
        $size /= 1024;
        $pos++;
    }
    return round($size,$dec).$a[$pos];
}

/**
 * 把返回的数据集转换成Tree
 * @access public
 * @param array $list 要转换的数据集
 * @param string $pid parent标记字段
 * @param string $level level标记字段
 * @return array
 */
function list_to_tree($list, $pk='id',$pid = 'pid',$child = '_child',$root=0) {
    // 创建Tree
    $tree = array();
    if(is_array($list)) {
        // 创建基于主键的数组引用
        $refer = array();
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] =& $list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId = $data[$pid];
            if ($root == $parentId) {
                $tree[] =& $list[$key];
            }else{
                if (isset($refer[$parentId])) {
                    $parent =& $refer[$parentId];
                    $parent[$child][] =& $list[$key];
                }
            }
        }
    }
    return $tree;
}

/**
 * 对查询结果集进行排序
 * @access public
 * @param array $list 查询结果
 * @param string $field 排序的字段名
 * @param array $sortby 排序类型
 * asc正向排序 desc逆向排序 nat自然排序
 * @return array
 */
function list_sort_by($list,$field, $sortby='asc') {
    if(is_array($list)){
        $refer = $resultSet = array();
        foreach ($list as $i => $data)
            $refer[$i] = &$data[$field];
        switch ($sortby) {
            case 'asc': // 正向排序
                asort($refer);
                break;
            case 'desc':// 逆向排序
                arsort($refer);
                break;
            case 'nat': // 自然排序
                natcasesort($refer);
                break;
        }
        foreach ( $refer as $key=> $val)
            $resultSet[] = &$list[$key];
        return $resultSet;
    }
    return false;
}