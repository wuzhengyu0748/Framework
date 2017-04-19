<?php
namespace core\lib\helper;
/**
 * 文件上传类
 * User: wzy@fhxzh
 * Date: 2017/4/9
 * Time: 0:18
 */

class Upload
{
    private $fileName;      # 表单name值
    private $maxSize;       # 允许最大文件大小
    private $allowMime;     # 允许的MIME类型
    private $allowExt;      # 允许的文件扩展名
    private $path;          # 文件上传路径
    private $imgFlag;       # 是否检验真实图片
    private $fileInfo;      # 文件信息
    private $ext;           # 文件后缀名
    private $destination;   # 文件保存后的返回路径
    private $uniName;       # 上传后唯一文件名

    /**
     * 架构函数
     * 初始化信息
     * @param string $fileName
     * @param string $path
     * @param string $imgflag
     * @param string $maxSize
     * @param array $allowExt
     * @param array $allowMime
     */
    public function __construct($fileName='myFile',$path='./uploads',$imgflag='false',$maxSize='5242880',$allowExt=['jpeg','jpg','png','gif'],$allowMime=['image/jpeg','image/jpg','image/png','image/gif'])
    {
        $this->fileName = $fileName;
        $this->maxSize = $maxSize;
        $this->allowMime = $allowMime;
        $this->allowExt = $allowExt;
        $this->path = $path;
        $this->imgflag = $imgflag;
        $this->ext = strtolower(pathinfo($_FILES[$this->fileName]['name'],PATHINFO_EXTENSION));
        $this->fileInfo = $_FILES[$this->fileName];
    }

    /**
     * 文件上传
     * @return bool|string 上传后的路径
     */
    public function uploadFile()
    {
        if ( $this->checkError() && $this->checkAttr() ) {
            if( !file_exists($this->path) ) {
                mkdir($this->path,0777,true);
            }
            //开始上传
            $this->uniName = $this->getUniName();
            $this->destination = $this->path.'/'.$this->uniName.'.'.$this->ext;
            if( @move_uploaded_file($this->fileInfo['tmp_name'],$this->destination) ) {
                return $this->destination;
            }else {
                dump('文件移动失败');
                return false;
            }
        }
        else {
           dump($this->error);
           return false;
        }
    }

    /**
     * 检测文件error信息
     * @return bool
     */
    private function checkError()
    {
        if (is_null($this->fileInfo)) {
            dump('文件上传出错');
            return false;
        }
        if ($this->fileInfo['error'] > 0) {
            switch ($this->fileInfo['error']) {
                case 1:
                    dump('超过了PHP配置文件中upload_max_filesize选项的值'); return false;
                    break;
                case 2:
                    dump('超过了表单中MAX_FILE_SIZE设置的值'); return false;
                    break;
                case 3:
                    dump('文件部分被上传'); return false;
                    break;
                case 4:
                    dump('没有选择上传文件'); return false;
                    break;
                case 6:
                    dump('未找到临时目录'); return false;
                    break;
                case 7:
                    dump('文件不可写'); return false;
                    break;
                case 8:
                    dump('由于PHP扩展程序中断文件上传'); return false;
                    break;
            }
        } else {
            return true;
        }
    }

    /**
     * 检测文件属性
     * @return bool
     */
    private function checkAttr()
    {
        if ($this->fileInfo['size']>$this->maxSize) {
            dump('上传文件过大');
            return false;
        }
        if (!in_array($this->ext,$this->allowExt)) {
            dump('不允许的扩展名');
            return false;
        }
        if (!in_array($this->fileInfo['type'],$this->allowMime)) {
            dump('不允许的文件类型');
            return false;
        }
        if ($this->imgFlag) {
            if (!@getimagesize($this->fileInfo['tmp_name'])) {
                dump('不是真实图片');
                return false;
            }
        }
        if (!is_uploaded_file($this->fileInfo['tmp_name'])) {
            dump('文件不是通过HTTP_POST方式上传');
            return false;
        }

        return true;
    }

    /**
     * 生成唯一文件名
     * @return string
     */
    private function getUniName()
    {
        return md5(uniqid(microtime(true),true));
    }
}