<?php
namespace core\lib\helper;
/**
 * 客户端通信接口类
 * User: wzy@fhxzh
 * Date: 2017/4/9
 * Time: 0:18
 */

class Response
{
    /**
     * 综合方式返回数据
     * @param $code 返回的状态码
     * @param string $message 返回的状态消息
     * @param array $data 返回的数据
     * @param $string 返回类型 json xml array
     */
    public static function show($code, $message = '', $data = array(), $type = 'json')
    {
        //如果状态码不是数字返回空
        if( !is_numeric($code) ) {
            return '';
        }

        $result = [
            'code' => $code,
            'message' => $message,
            'data' => $data
        ];

        if ( $type == 'json' )
        {
            self::json($code,$message,$data);
        }
        else if( $type == 'array' ) // 调试用
        {
            echo '<pre>';
            var_dump($result);
            exit();
        }
        else if ( $type == 'xml' )
        {
            self::xml($code,$message,$data);
        }
    }

    /**
     * JSON方式返回数据
     * @param $code 返回的状态码
     * @param string $message 返回的状态消息
     * @param array $data 返回的数据
     */
    public static function json($code, $message = '', $data = array())
    {
        //如果状态码不是数字返回空
        if( !is_numeric($code) ) {
            return '';
        }

        $result = [
            'code' => $code,
            'message' => $message,
            'data' => $data
        ];

        echo json_encode($result); # 此函数仅仅支持utf8编码 否则返回null
        exit();
    }

    /**
     * XML方式返回数据
     * @param $code 返回的状态码
     * @param string $message 返回的状态消息
     * @param array $data 返回的数据
     */
    public static function xml($code, $message, $data)
    {
        //如果状态码不是数字返回空
        if ( !is_numeric($code) ) {
            return '';
        }

        $result = [
            'code' => $code,
            'message' => $message,
            'data' => $data,
        ];
        //告知浏览器按xml格式输出
        header("Content-Type:text/xml");
        $xml = "<?xml version='1.0' encoding='UTF-8'?>\n";
        $xml .="<root>\n";

        $xml .= self::xmlEncode($result);

        $xml .="</root>";
        echo $xml;
        exit();
    }

    /**
     * 将数据组装成xml子节点
     * @param $data 数据
     * @return string
     * 注:xml节点不能为数字
     * 如果key是数字 则将如<0>1</0> 组装成<item id="0">1</item>
     */
    public static function xmlEncode($data)
    {
        $xml = $attr = '';
        foreach ($data as $key => $value) {
            //节点是否为数字
            if ( is_numeric($key) ) {
                $attr = " id='{$key}'";
                $key = 'item';
            }
            $xml .= "<{$key}{$attr}>";
            //判断该值是数组则递归
            $xml .= is_array($value) ? self::xmlEncode($value) : $value;
            $xml .= "</{$key}>\n";
        }
        return $xml;
    }
}