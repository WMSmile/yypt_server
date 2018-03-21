<?php

/**
 * Created by PhpStorm.
 * Description: 图片上传
 * User: ChenXiaoHua
 * Mail: chenxiaohua@civilizationdata.com
 */
namespace yypt\admin\controller;

use think\Request;
use think\Controller;

class Upload extends Controller
{   
    public function index()
    {	

        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST');
        header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
        $file = request()->file('file');
        if (!$file) {
        	return resultArray(['error' => '请上传文件']);
        }
        
        $info = $file->validate(['ext'=>'jpg,png,gif'])->move(ROOT_PATH . DS . 'public/uploads');
        if ($info) {
            return resultArray(['data' =>  'public/uploads'. DS .$info->getSaveName()]);
        }
        
        return resultArray(['error' =>  $file->getError()]);
    }
    
    public function remove() {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST');
        header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, authKey, sessionId");
        $file = $this->request->param('path');
        $data = [];
        
        if(is_file($file) == false) {
            $data = ['error'=> "文件不存在!"];
        }else{
            unlink($file);
            $data = ['data'=>true];
        }
        return resultArray($data);
    }
}
 