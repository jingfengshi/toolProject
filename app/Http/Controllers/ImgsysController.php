<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ImgsysController extends Controller
{
    public function getImg($name)
    {
        $path = storage_path() . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'public'.DIRECTORY_SEPARATOR . $name;
        if (!file_exists($path)) {
            //报404错误
            header("HTTP/1.1 404 Not Found");
            header("Status: 404 Not Found");
            exit;
        }
        //输出图片
        header('Content-type: image/jpg');
        echo file_get_contents($path);
        exit;
    }
}
