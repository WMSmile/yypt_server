<?php
/**
 * Created by PhpStorm.
 * Description:微信接口
 * User: ChenXiaoHua
 * Mail: chenxiaohua@civilizationdata.com
 */

namespace yypt\wechat\controller;

use com\weChat\weChatMenu;
use com\weChat\weChatOauth;
use com\weChat\weChatUser;
use yypt\common\controller\ApiCommon;
use yypt\common\controller\Common;

class WeChatApi extends Common
{
    /**
     * 获取公众号access_token
     */
    public function getAccessToken()
    {
        $wechatOauth = new weChatOauth();
        $accessToken = $wechatOauth->get_access_token();
        echo $accessToken;
        echo "<br>";
        echo cache('DB_WC_DATA');
    }

    /**
     * 生成公众号菜单
     */
    public function createMenu()
    {
        $data = //测试数据
            '{
            "button":[{
           "name":"易儿安睡",
           "sub_button":[
                   {	
                       "type":"view",
                       "name":"公司官网",
                       "url":"http://www.gxyec.com/"
                    },
                    {
                       "type":"view",
                       "name":"产品介绍",
                       "url":"http://mp.weixin.qq.com/s?__biz=MzI5MTgwMzQxNw==&mid=100000412&idx=1&sn=ef0971f75ece27d391a73c0919e5a9e8&chksm=6c0a5aea5b7dd3fcf259adf399141244faa6b60d1580f70c1c5b27a0ca9ca24a71d0214d0ffe&scene=18#wechat_redirect"
                    },
                    {
                        "type":"view",
                       "name":"运营平台",
                       "url":"http://113.204.47.108/"
                    }
            ]
             },
           {	
               "type":"view",
               "name":"运营平台",
               "url":"http://http://113.204.47.108:8009"
            },
           
            {
               "type":"view",
               "name":"个人中心",
               "url":"http://http://www.baidu.com"
             }]
             }';
        $wechatMenu = new weChatMenu();
        $result = $wechatMenu->createMenu($data);
        echo $result;
    }
    /**
     * 批量获取关注粉丝列表
     */
    public function getUserLists()
    {
        $wechatUser = new weChatUser();
        $result = $wechatUser->getUserList();
        dump($result);
    }
//
//    public function location()
//    {
//        $wechatUser = new  WechatUser();
//        $result = $wechatUser->location('113.323916', '23.089716');
//        $result = json_decode($result,true);
//        dump($result['regeocode']['addressComponent']['city']);
//    }

}