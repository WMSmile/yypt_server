<?php
/**
 * Created by PhpStorm.
 * Description: 解决跨域问题
 * User: ChenXiaoHua
 * Mail: chenxiaohua@civilizationdata.com
 */

namespace yypt\common\controller;

use think\Controller;
use think\Request;

class Common extends Controller
{
    public $param;
    public $userInfo;
    public function _initialize()
    {
        parent::_initialize();
        /*防止跨域*/      
        header('Access-Control-Allow-Origin: '.$_SERVER['HTTP_ORIGIN']);
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, authKey, sessionId");
        $param =  Request::instance()->param();
        $this->param = $param;

    }

    public function object_array($array) 
    {  
        if (is_object($array)) {  
            $array = (array)$array;  
        } 
        if (is_array($array)) {  
            foreach ($array as $key=>$value) {  
                $array[$key] = $this->object_array($value);  
            }  
        }  
        return $array;  
    }
    /**
     * 写入操作日志
     * @param string $action
     * @param string $content
     * @return bool
     */
    public  function write($action = '行为', $content = "内容描述")
    {
        $request = Request::instance();
        $node = strtolower(join('/', [$request->module(), $request->controller(), $request->action()]));
        $data = ['ip' => $request->ip(), 'node' => $node, 'username' => $this->userInfo['username'] . '', 'action' => $action, 'content' => $content];
        $systemLog=model('SystemLog');
        return $systemLog->add($data) !== false;
    }
}
 