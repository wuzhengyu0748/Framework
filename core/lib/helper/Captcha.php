<?php
namespace core\lib\helper;

/**
 * 验证码类
 * User: wzy@fhxzh
 * Date: 2017/4/9
 * Time: 0:18
 */

class Captcha
{
    private $image = null; //画布资源
    private $_fontfile = ''; //字体文件
    private $_width = 120; //画布宽
    private $_height = 40; //画布高
    private $_fontsize = 20; //字体大小
    private $_length = 4; //验证码长度
    private $_interfere = 1;//干扰元素类型 1：线和像素点 2：雪花

    /**
     * 初始化数据
     * @param array $config 验证码配置
     */
    public function __construct($config = array())
    {
        if(!extension_loaded('gd')) {
            throw new \Exception('未加载GD库');
        }

        if ( is_array($config) && !empty($config) )
        {
            //检测是否需要字体
            if ( isset($config['fontfile']) ){
                //判断是否有该字体且有可读权限
                if(is_file($config['fontfile']) && is_readable($config['fontfile']) ) {
                    $this->_fontfile = $config['fontfile'];
                } else {
                    return false;
                }
            }
            //检测是否设置画布宽高
            if( isset($config['width'])&&$config['width']>0 ) {
                $this->_width = $config['width'];
            }
            if( isset($config['height'])&&$config['height']>0 ) {
                $this->_height = $config['height'];
            }
            //检测是否设置字体大小
            if( isset($config['fontsize'])&&$config['fontsize']>0 ) {
                $this->_fontsize = $config['fontsize'];
            }
            //检测是否设置验证码长度
            if( isset($config['length'])&&$config['length']>0 ) {
                $this->_length = $config['length'];
            }
            //检测干扰元素
            if( isset($config['interfere'])&&in_array($config['interfere'],[1,2]) ) {
                $this->_interfere = $config['interfere'];
            }
            //创建画布资源
            $this->image = imagecreatetruecolor($this->_width,$this->_height);
            return $this->image;
        }
        else
        {
            return false;
        }
    }

    /**
     * 返回验证码图片
     */
    public function show()
    {
        //设置画布底色为白色
        $white = imagecolorallocate($this->image,255,255,255);
        //填充底色
        imagefilledrectangle($this->image,0,0,$this->_width,$this->_height,$white);
        //生成验证码
        $str = $this->generateStr($this->_length);
        if(false === $str) {
            return false;
        }
        //绘制验证码
        $this->drawCaptcha($str);
        //给验证码加干扰元素
        $this->interfere();
        //输出图像
        header('content-type:image/png');
        imagepng($this->image);
        imagedestroy($this->image);
        //返回字串供验证使用
        return strtolower($str);
    }

    /**
     * 绘制验证码
     * @param $str 验证码的随机字符串
     */
    private function drawCaptcha($str)
    {
        //判断是是否传字体配置
        if( $this->_fontfile == '' )
        {
            for ($i=0;$i<$this->_length;++$i) {
                $x = ceil($this->_width/$this->_length)*$i+mt_rand(5,10);//x轴
                $y = rand(5,10);//y轴
                $color = $this->getRandColor();//字体颜色
                $text = substr($str,$i,1);//每次从随机字串中取一个
                imagestring($this->image,$this->_fontsize,$x,$y,$text,$color);
            }
        }
        else
        {
            for ($i=0;$i<$this->_length;++$i) {
                $angle = mt_rand(-30,30);//角度
                $x = ceil($this->_width/$this->_length)*$i+mt_rand(5,10);//x轴
                $y = ceil($this->_height/1.5);//y轴
                $color = $this->getRandColor();//字体颜色
                $text = mb_substr($str,$i,1,'utf-8');//每次从随机字串中取一个
                imagettftext($this->image,$this->_fontsize,$angle,$x,$y,$color,$this->_fontfile,$text);
            }
        }
    }

    /**
     * 给验证码加干扰元素
     */
    private function interfere()
    {
        if(intval($this->_interfere) === 1)
        {//点和像素
            for ($i=1;$i<=60;++$i) {
                imagesetpixel($this->image,mt_rand(0,$this->_width),mt_rand(0,$this->_height),$this->getRandColor());
            }
            for ($i=1;$i<=7;++$i) {
                imageline($this->image,mt_rand(0,$this->_width),mt_rand(0,$this->_height),mt_rand(0,$this->_width),mt_rand(0,$this->_height),$this->getRandColor());
            }
        }
        else
        {//雪花
            for ($i=1;$i<=10;++$i) {
                imagestring($this->image, mt_rand(1, 5), mt_rand(0, $this->_width), mt_rand(0, $this->_height), '*', $this->getRandColor());
            }
        }
    }

    /**
     * 生成指定长度验证码
     * @param integer $len 验证码长度
     * @return string 随机字符
     */
    private function generateStr($len)
    {
        if ($len<1 || $len>30) {
            return false;
        }
        $chars = [
            'a','b','c','d','e','f','g','h','k','m','n','p','x','y','z',
            'A','B','C','D','E','F','G','H','K','M','N','P','X','Y','Z',
            1,2,3,4,5,6,7,8,9
        ];
        $str = join('',array_rand(array_flip($chars),$len));
        return $str;
    }

    /**
     * 获得随机颜色值
     */
    private function getRandColor()
    {
        return imagecolorallocate($this->image,mt_rand(0,255),mt_rand(0,255),mt_rand(0,255));
    }
}