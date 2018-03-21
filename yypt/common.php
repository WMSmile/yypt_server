<?php

/**
 * 行为绑定
 */
\think\Hook::add('app_init','yypt\\common\\behavior\\InitConfigBehavior');

/**
 * 返回对象
 * @param $array 响应数据
 */
function resultArray($array)
{
    if(isset($array['data'])) {
        $array['error'] = '';
        $code = 200;
    } elseif (isset($array['error'])) {
        $code = 400;
        $array['data'] = '';
    }
    return [
        'code'  => $code,
        'data'  => $array['data'],
        'error' => $array['error']
    ];
}

/**
 * 调试方法
 * @param  array   $data  [description]
 */
function p($data,$die=1)
{
    echo "<pre>";
    print_r($data);
    echo "</pre>";
    if ($die) die;
}

/**
 * 用户密码加密方法
 * @param  string $str      加密的字符串
 * @param  [type] $auth_key 加密符
 * @return string           加密后长度为32的字符串
 */
function user_md5($str, $auth_key = '')
{
    return '' === $str ? '' : md5(sha1($str) . $auth_key);
}

/**
 * 获取 HTTPS协议类型
 * @return string
 */
function getHttpType()
{
    return $type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
}

/**
 * 获取当前域名
 * @return string
 */

function getHostDomain()
{
    // return $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'];

    $host=getHttpType();
    if($_SERVER["SERVER_PORT"]!=80){
            $host=$host. $_SERVER['SERVER_NAME'].':'.$_SERVER["SERVER_PORT"];
    }else{
            $host=$host. $_SERVER['SERVER_NAME'];
    }
    return $host;
}

/**
 * 重写Url生成
 * @param string $url 路由地址
 * @param string|array $vars 变量
 * @param bool|string $suffix 生成的URL后缀
 * @param bool|string $domain 域名
 * @return string
 */
function setUrl($url = '', $vars = '', $suffix = true, $domain = false)
{
    if (!empty($mid = input('mid'))) {
        if (is_array($vars)) {
            if (isset($vars['mid'])) {
                $mid = $vars['mid'];
            }
            $vars = array_merge($vars, ['mid' => $mid]);
        } elseif ($vars != '' && !is_array($vars)) {
            $vars = $vars . '&' . 'mid=' . $mid;
        } else {
            $vars = ['mid' => $mid];
        }
    }
    return \think\Url::build($url, $vars, $suffix, $domain);
}

/**
 * 生成随机字符串
 * @param $length int 字符串长度
 * @return $str string 随机字符串
 */
function getRandChar($length)
{
    $str = null;
    $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
    $max = strlen($strPol) - 1;

    for ($i = 0; $i < $length; $i++) {
        $str .= $strPol[rand(0, $max)];//rand($min,$max)生成介于min和max两个数之间的一个随机整数
    }

    return $str;
}

/**
 * Emoji原形转换为String
 * @param string $content
 * @return string
 */
function emojiEncode($content)
{
    return json_decode(preg_replace_callback("/(\\\u[ed][0-9a-f]{3})/i", function ($str) {
        return addslashes($str[0]);
    }, json_encode($content)));
}

/**
 * Emoji字符串转换为原形
 * @param string $content
 * @return string
 */
function emojiDecode($content)
{
    return json_decode(preg_replace_callback('/\\\\\\\\/i', function () {
        return '\\';
    }, json_encode($content)));
}