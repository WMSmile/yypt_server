<?php
// bsf管理模板函数文件


/**
 * cookies加密函数
 * @param string 加密后字符串
 */
function encrypt($data, $key = 'kls8in1e') 
{ 
    $prep_code = serialize($data); 
    $block = mcrypt_get_block_size('des', 'ecb'); 
    if (($pad = $block - (strlen($prep_code) % $block)) < $block) { 
        $prep_code .= str_repeat(chr($pad), $pad); 
    } 
    $encrypt = mcrypt_encrypt(MCRYPT_DES, $key, $prep_code, MCRYPT_MODE_ECB); 
    return base64_encode($encrypt); 
} 

/**
 * cookies 解密密函数
 * @param array 解密后数组
 */
function decrypt($str, $key = 'kls8in1e') 
{ 
    $str = base64_decode($str); 
    $str = mcrypt_decrypt(MCRYPT_DES, $key, $str, MCRYPT_MODE_ECB); 
    $block = mcrypt_get_block_size('des', 'ecb'); 
    $pad = ord($str[($len = strlen($str)) - 1]); 
    if ($pad && $pad < $block && preg_match('/' . chr($pad) . '{' . $pad . '}$/', $str)) { 
        $str = substr($str, 0, strlen($str) - $pad); 
    } 
    return unserialize($str); 
}

/**
 * 获取客户端IP地址
 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @param boolean $adv 是否进行高级模式获取（有可能被伪装）
 * @return mixed
 */
function get_client_ip($type = 0,$adv=false) {
    $type       =  $type ? 1 : 0;
    static $ip  =   NULL;
    if ($ip !== NULL) return $ip[$type];
    if($adv){
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos    =   array_search('unknown',$arr);
            if(false !== $pos) unset($arr[$pos]);
            $ip     =   trim($arr[0]);
        }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip     =   $_SERVER['HTTP_CLIENT_IP'];
        }elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip     =   $_SERVER['REMOTE_ADDR'];
        }
    }elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip     =   $_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $long = sprintf("%u",ip2long($ip));
    $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
    return $ip[$type];
}


/**
 * 保存用户行为，前台用户和后台用户
 * @param int $uid 用户id
 * @param string $name 用户名
 * @param string $type config('FRONTEND_USER')/config('BACKEND_USER')
 * @param string $info 行为描述
 */
function storage_user_action($uid,$name,$type,$info){
    if(config('storage_user_action')==true){
        Db::name('user_action')->insert([
            'type'=>$type,
            'user_id'=>$uid,
            'uname'=>$name,
            'add_time'=>time(),
            'info'=>$info]);
    }
}



/**
 * 设置、获取公众号类型
 * @return string
 */

function getMpType($type = '')
{
    switch ($type) {
        case '1':
            return '普通订阅号';
            break;
        case '2':
            return '认证订阅号';
            break;
        case '3':
            return '普通服务号';
            break;
        case '4':
            return '认证服务号(媒体、政府)';
            break;
    }

}






