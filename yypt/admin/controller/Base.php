<?php

/**
 * Created by PhpStorm.
 * Description:基础类，无需验证权限。
 * User: ChenXiaoHua
 * Mail: chenxiaohua@civilizationdata.com
 */
namespace yypt\admin\controller;

use com\verify\HonrayVerify;
use com\jwt\JWT;
use yypt\common\controller\Common;
use think\Request;

class Base extends Common
{
    public function pg(){
        $goods=model('Cdata');
       // $data=$goods->getGoodsData();
        $data=$goods->getRetailOderList();
        return resultArray(['data' => $data]);
    }
    public function login()
    {

        $userModel = model('User');
        $param = $this->param;
        $username = $param['username'];
        $password = $param['password'];
        $verifyCode = !empty($param['verifyCode'])? $param['verifyCode']: '';
        $isRemember = !empty($param['isRemember'])? $param['isRemember']: '';
        $data = $userModel->login($username, $password, $verifyCode, $isRemember);
        if (!$data) {
            return resultArray(['error' => $userModel->getError()]);
        } 
        return resultArray(['data' => $data]);
    }

    public function relogin()
    {   
        $userModel = model('User');
        $param = $this->param;
        $data = decrypt($param['rememberKey']);
        $username = $data['username'];
        $password = $data['password'];

        $data = $userModel->login($username, $password, '', true, true);
        if (!$data) {
            return resultArray(['error' => $userModel->getError()]);
        } 
        return resultArray(['data' => $data]);
    }    

    public function logout()
    {
        $param = $this->param;
        cache('Auth_'.$param['authkey'], null);
        return resultArray(['data'=>'退出成功']);
    }

    public function getConfigs()
    {
        $systemConfig = cache('DB_CONFIG_DATA'); 
        if (!$systemConfig) {
            //获取所有系统配置
            $systemConfig = model('admin/SystemConfig')->getDataList();
            cache('DB_CONFIG_DATA', null);
            cache('DB_CONFIG_DATA', $systemConfig, 36000); //缓存配置
        }
        return resultArray(['data' => $systemConfig]);
    }

    public function getVerify()
    {
        $captcha = new HonrayVerify(config('captcha'));
        return $captcha->entry();
    }

    public function setInfo()
    {
        $userModel = model('User');
        $param = $this->param;
        $old_pwd = $param['old_pwd'];
        $new_pwd = $param['new_pwd'];
        $auth_key = $param['auth_key'];
        $data = $userModel->setInfo($auth_key, $old_pwd, $new_pwd);
        if (!$data) {
            return resultArray(['error' => $userModel->getError()]);
        } 
        return resultArray(['data' => $data]);
    }

    // miss 路由：处理没有匹配到的路由规则
    public function miss()
    {
        if (Request::instance()->isOptions()) {
            return ;
        } else {
            echo '轻谈运营平台接口';
        }
    }

    //加密
    public function one(){

        $key = "jfdksajfkl;dsajfkdjsaklfdajffdsafdsfdsfdsfdsklfdsafdsafdsafdsdsajlkfdsa";
        $token = array(
            'uid' => 1050,
            'username' => 'baby',
        );

        $jwt = JWT::encode($token, $key);
        echo $jwt;

    }
    //解密
    public function two(){
        $key = "jfdksajfkl;dsajfkdjsaklfdajffdsafdsfdsfdsfdsklfdsafdsafdsafdsdsajlkfdsa";
        $str = isset($_GET['str']) ? $_GET['str'] : '';
        if($str == ''){
            exit('empty');
        }
        $decoded = JWT::decode($jwt, $key, array('HS256'));
        if(!is_object($decode)){
            echo "error";
        }else{
            $arr = json_decode(json_encode($decoded), true);
            dump($arr);
            $uid = $arr['uid']; //既然能拿到uid，那么就说明是有权限的用户，并且他的uid是1050。剩下的，只要有uid，该干什么就干什么好了。
        }
    }



}
 