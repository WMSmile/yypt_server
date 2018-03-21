<?php
/**
 * Created by PhpStorm.
 * Description:微信公众号的消息入口（包括绑定入口）。用户在公众号发送的消息，微信服务器会推送到这里（index）
 * User: ChenXiaoHua
 * Mail: chenxiaohua@civilizationdata.com
 */

namespace yypt\wechat\controller;

use com\weChat\Common;
use com\weChat\dataService;
use com\weChat\weChatOauth;
use com\weChat\weChatReceive;
use com\weChat\wechatService;
use com\weChat\weChatUser;
use think\Log;


class Service
{
    private $WechatCommon='';//公共实例
    private $WechatReceive = '';//消息实例
    private $WechatOauth = '';//授权实例
    private $WechatUser='';//粉丝实例
    private $WechatMode='';//wechat数据模型
    private $FromUserName = '';//消息发送者
    private $ToUserName = '';  //消息接受者
    private $MsgType = '';  //接收消息的类型
    private $CreateTime = '';  //接收消息的时间
    private $Keyword = '';  //接收消息的值
    public function __construct()
    {
//        parent::__construct();
        $this->WechatCommon=new Common();
        $this->WechatReceive = new weChatReceive();
        $this->WechatOauth = new weChatOauth();
        $this->WechatUser = new weChatUser();
        $this->WechatMode=$weChat=model('WeChat');

    }

    public function index()
    {

        //获得参数 signature nonce token timestamp echostr
       // 这个echostr呢  只有说验证的时候才会echo  如果是验证过之后这个echostr是不存在的字段了

        $echoStr = $_GET["echostr"];
        if ($this->checkSignature()&&$echoStr) {
            echo $echoStr;
            //如果你不知道是否验证成功  你可以先echo echostr 然后再写一个东西
            exit;
        }else{
            $this->info();
        }


    }
    //验证微信开发者模式接入是否成功
    private function checkSignature()
    {
        //signature 是微信传过来的 类似于签名的东西
        $signature = $_GET["signature"];
        //微信发过来的东西
        $timestamp = $_GET["timestamp"];
        //微信传过来的值  什么用我不知道...
        $nonce     = $_GET["nonce"];
        //定义你在微信公众号开发者模式里面定义的token
        //$token  = "gVzaHNIx9RiO40KiXbJScNN2t7SLF2gl";
        $token=$this->WechatCommon->wechat_config['token'];
        //三个变量 按照字典排序 形成一个数组
        $tmpArr = array(
            $token,
            $timestamp,
            $nonce
        );
        // use SORT_STRING rule
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        //哈希加密  在laravel里面是Hash::
        $tmpStr = sha1($tmpStr);
        //按照微信的套路 给你一个signature没用是不可能的 这里就用得上了
        if ($tmpStr == $signature) {
            return true;
        } else {
            return false;
        }
    }

    public function infoMsg(){
        $postObj = $this->WechatReceive->getRev();//获取微信服务器发来的内容

        $this->FromUserName = $this->WechatReceive->getRevFrom();//消息发送者
        $this->ToUserName = $this->WechatReceive->getRevTo();//消息接受者
        $this->CreateTime = $this->WechatReceive->getRevCtime();//接收消息的时间
        $this->MsgType = $this->WechatReceive->getRevType();//接收消息的类型
        // 获取并同步粉丝信息到数据库
        $this->_updateFansInfo(true);
        // 分别执行对应类型的操作
        switch ($postObj->getRevType()) {
            case weChatReceive::MSGTYPE_TEXT:
                return $this->_keys("WechatKeys#keys#" . $this->wechat->getRevContent());
            case weChatReceive::MSGTYPE_EVENT:
                return $this->_event();
            case weChatReceive::MSGTYPE_IMAGE:
                return $this->_image();
            case weChatReceive::MSGTYPE_LOCATION:
                return $this->_location();
            default:
                return 'success';
        }
    }
    public function info()
    {


       // if (isset($_GET["echostr"])) exit($_GET["echostr"]);

        $postObj = $this->WechatReceive->getRev();//获取微信服务器发来的内容

        $this->FromUserName = $this->WechatReceive->getRevFrom();//消息发送者
        $this->ToUserName = $this->WechatReceive->getRevTo();//消息接受者
        $this->CreateTime = $this->WechatReceive->getRevCtime();//接收消息的时间
        $this->MsgType = $this->WechatReceive->getRevType();//接收消息的类型

        $this->Keyword = trim($this->WechatReceive->getRevContent());//接收消息的值

        /*
                * 1、click：点击推事件
                * 用户点击click类型按钮后，微信服务器会通过消息接口推送消息类型为event的结构给开发者（参考消息接口指南）
                * 并且带上按钮中开发者填写的key值，开发者可以通过自定义的key值与用户进行交互；
                */
        if ($this->MsgType == 'event' && $postObj->Event == 'CLICK') {
            $this->Keyword = trim($this->WechatReceive->getRevEvent());//接收消息的值
        }
        // 获取并同步粉丝信息到数据库
        $this->_updateFansInfo(true);

        //回复文本消息
        $map['keys'] = $this->Keyword;
        $info = $this->WechatMode->getTableData('wechat_keys',$map);
        Log::save($map);
        $this->_keys($info);


        if($this->Keyword=='你好'){
           //$wx_text=$wx_text['content'];
            $this->WechatReceive->sendText($info['content'], $this->FromUserName, $this->ToUserName);

        }
        //回复图文消息
//        $wx_img = Db::name('wx_img')->where($map)->find();
        if($this->Keyword=='运营平台')
        {
            $wx_img = array('title'=>'欢迎使用运营平台','desc'=>'全新电商管理模式','pic'=>'http://113.204.47.108:8009/static/img/loginbg.55f81cf.png','url'=>'http://113.204.47.108:8009/');

            $this->WechatReceive->sendImg($this->FromUserName, $this->ToUserName, $wx_img['title'], $wx_img['desc'], $wx_img['pic'], $wx_img['url']);
       }

        //匹配其他输入
        if ($this->Keyword == 'what') {

        }
        //发送客服消息（文本）
        if ($this->Keyword == 'ggg') {
            $data = array(
                'touser' => $this->FromUserName,
                'msgtype' => 'text',
                'text' => array('content' => '欢迎加入轻谈科技')
            );
            $result = $this->WechatReceive->sendCustomMessage($data);
        }
        //发送模板消息
        if ($this->Keyword == '15') {
            $this->WechatReceive->senTempBonus('', 'http://www.baidu.com', array('value' => '你的组织成功消费一笔订单', 'color' => '#173177'), '1', '2017-5-5', '到账商城余额', '2017-5-5',array('value' => '你的组织成功消费一笔订单', 'color' => '#173177'));
        }
        if ($this->Keyword == '16') {
            $this->WechatReceive->senTempOrderPay('', 'http://www.baidu.com', array('value' => '恭喜你支付成功！', 'color' => '#173177'), '1', '2015555', array('value' => '备注', 'color' => '#173177'));
        }
        if ($this->Keyword == '17') {
            $this->WechatReceive->senTempOrderStatus('', 'http://www.baidu.com', array('value' => '订单已发货！', 'color' => '#173177'), '55656165', '发货', array('value' => '备注', 'color' => '#173177'));
        }
        if ($this->Keyword == '18') {
            $this->WechatReceive->senTempRefund('', 'http://www.baidu.com', array('value' => '退款成功！', 'color' => '#173177'), '达大厦', '5.00', array('value' => '备注', 'color' => '#173177'));
        }

        //发送模板消息
        if ($this->Keyword == '20') {
            $res1  = $this->WechatReceive->setTMIndustry('10', '11');
            file_put_contents('g22.txt', json_encode($res1));

            $res = $this->WechatReceive->getTemplateList();
            file_put_contents('g11.txt', json_encode($res));
        }
    }

    public function _keys($info){
        if (is_array($info) && isset($info['type'])) {

            // 数据状态检查
            if (array_key_exists('status', $info) && empty($info['status'])) {
                return 'success';
            }
            switch ($info['type']) {

                case 'keys': // 关键字
                    if (empty($info['content']) && empty($info['name'])) {
                        return 'success';
                    }
                    return $this->_keys('wechat_keys#keys#' . (empty($info['content']) ? $info['name'] : $info['content']));
                case 'text': // 文本消息
                   return $this->WechatReceive->sendText($info['content'], $this->FromUserName, $this->ToUserName);
                case 'news': // 图文消息
                    return $this->_news($info);
                case 'image':
                    return;
            }

        }
    }
    public function _news($info){
        $wx_img = array(
            'title'=>$info['title'],
            'desc'=>$info['content'],
            'pic'=>'http://yypt.civilizationdata.cn/server/'.$info['path_url'],
            'url'=>$info['url']);

        $this->WechatReceive->sendImg($this->FromUserName, $this->ToUserName, $wx_img['title'], $wx_img['desc'], $wx_img['pic'], $wx_img['url']);



    }

    /**
     * 同步粉丝状态
     * @param bool $subscribe 关注状态
     */
    protected function _updateFansInfo($subscribe = true)
    {
        if ($subscribe) {
            $fans = $this->getFansInfo($this->FromUserName);
            if (empty($fans) || empty($fans['subscribe'])) {
                $userInfo = $this->WechatUser->getUserInfo($this->FromUserName);
                $userInfo['subscribe'] = intval($subscribe);
                $this->setFansInfo($userInfo,  $this->WechatUser->appid);
            }
        } else {
            $data = ['subscribe' => '0', 'appid' => $this->WechatUser->appid, 'openid' =>$this->FromUserName];
            $this->WechatMode->dbSave('wechat_fans', $data, 'openid');
        }
    }

    /**
     * 保存/更新粉丝信息
     * @param array $user
     * @param string $appid
     * @return bool
     */
    public function setFansInfo($user, $appid = '')
    {
        if (!empty($user['subscribe_time'])) {
            $user['subscribe_at'] = date('Y-m-d H:i:s', $user['subscribe_time']);
        }
        if (!empty($user['tagid_list']) && is_array($user['tagid_list'])) {
            $user['tagid_list'] = join(',', $user['tagid_list']);
        }else{
            unset($user['tagid_list']);
        }
        foreach (['country', 'province', 'city', 'nickname', 'remark'] as $k) {
            isset($user[$k]) && $user[$k] = emojiEncode($user[$k]);
        }
        $user['appid'] = $appid;
        return $this->WechatMode->dbSave('wechat_fans', $user, 'openid');
    }

    /**
     * 读取粉丝信息
     * @param string $openid 微信用户openid
     * @param string $appid 公众号appid
     * @return array|false
     */
    public function getFansInfo($openid, $appid = null)
    {
        $map = ['openid' => $openid];
        is_string($appid) && $map['appid'] = $appid;
        $user =$this->WechatMode->getTableData('wechat_fans',$map);
        foreach (['country', 'province', 'city', 'nickname', 'remark'] as $k) {
            isset($user[$k]) && $user[$k] = emojiDecode($user[$k]);
        }
        return $user;
    }

    /**
     * 同步获取粉丝列表
     * @param string $next_openid
     * @return bool
     */
    public function syncAllFans($next_openid = '')
    {

        $wechat = $this->WechatUser;
        $appid = $wechat->getAppid();
        if (false === ($result = $this->WechatUser->getUserList($next_openid)) || empty($result['data']['openid'])) {
           Log::error("获取粉丝列表失败, {$wechat->errMsg} [{$wechat->errCode}]");
            return false;
        }

        foreach (array_chunk($result['data']['openid'], 100) as $openids) {
            if (false === ($info = $wechat->getUserBatchInfo($openids)) || !is_array($info)) {
                Log::error("获取用户信息失败, {$wechat->errMsg} [{$wechat->errCode}]");
                return false;
            }
            foreach ($info as $user) {
                if (false === $this->setFansInfo($user, $appid)) {
                    Log::error('更新粉丝信息更新失败!');
                    return false;
                }
                if ($result['next_openid'] === $user['openid']) {
                    unset($result['next_openid']);
                }
            }
        }

        return empty($result['next_openid']) ? true : $this->syncAllFans($result['next_openid']);
    }

    /**
     * 同步获取黑名单信息
     * @param string $next_openid
     * @return bool
     */
    public function syncBlackFans($next_openid = '')
    {
        $wechat = $this->WechatUser;
        $result = $wechat->getBacklist($next_openid);
        if ($result === false || empty($result['data']['openid'])) {
            if (empty($result['total'])) {
                return true;
            }
            Log::error("获取粉丝黑名单列表失败，{$wechat->errMsg} [{$wechat->errCode}]");
            return false;
        }
        foreach ($result['data']['openid'] as $openid) {
            if (false === ($user = $wechat->getUserInfo($openid))) {
                Log::error("获取用户[{$openid}]信息失败，$wechat->errMsg");
                return false;
            }
            $user['is_back'] = '1';
            if (false === $this->setFansInfo($user)) {
                Log::error('更新粉丝信息更新失败！');
                return false;
            }
            if ($result['next_openid'] === $openid) {
                unset($result['next_openid']);
            }
        }
        return empty($result['next_openid']) ? true : $this->syncBlackFans($result['next_openid']);
    }

}