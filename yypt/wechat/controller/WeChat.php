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
use yypt\common\controller\ApiCommon;

class WeChat extends ApiCommon
{
    /**
     * 获取公众号access_token
     */
    public function getAccessToken()
    {
        $wechatOauth = new weChatOauth();
        $accessToken = $wechatOauth->get_access_token();
        echo $accessToken;
    }

    /**
     * 生成公众号菜单
     */
    public function createMenu()
    {
        $data = //测试数据
            '{
            "button":[{	
               "type":"click",
                "name":"运营平台",
               "key":"V1001_TODAY_MUSIC"
             },
             {	
               "type":"click",
                "name":"点击付款",
               "key":"V1001_TODAY_MUSIC"
             },
            {
           "name":"菜单",
           "sub_button":[
           {	
               "type":"view",
               "name":"搜索",
               "url":"http://www.soso.com/"
            },
           
            {
               "type":"click",
               "name":"赞一下我们",
               "key":"V1001_GOOD"
            }]
             }]
             }';
        $wechatMenu = new weChatMenu();
        $result = $wechatMenu->createMenu($data);
        echo $result;
    }

}