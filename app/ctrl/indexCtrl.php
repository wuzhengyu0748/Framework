<?php
namespace app\ctrl;

use core\kernel;
use core\lib\model;
use core\lib\helper\Image;

class indexCtrl extends kernel
{
    public function index()
    {
        //$model = new model();
        dump(readDirectory('D:\framework'));
        $this->display('index/index');
    }
}