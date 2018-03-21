<?php
/**
 * Created by PhpStorm.
 * Description:
 * User: ChenXiaoHua
 * Mail: chenxiaohua@civilizationdata.com
 */

namespace yypt\wechat\controller;


use com\weChat\weChatUser;
use yypt\common\controller\ApiCommon;

class Fans extends ApiCommon
{
    public function index(){
        $WeChat = model('WeChat');
        $param = $this->param;
        $keywords = !empty($param['keywords']) ? $param['keywords']: '';
        $page = !empty($param['page']) ? $param['page']: '';
        $limit = !empty($param['limit']) ? $param['limit']: '';
        $data = $WeChat->weChatFansList($keywords, $page, $limit);
        return resultArray(['data' => $data]);
    }
    /**
     * 同步粉丝列表
     */
    public function sync()
    {
        $weChat=model('WeChat');
        $weChat->delFans();
        $service=new Service();
        if ($service->syncAllFans('')) {
            $service->syncBlackFans('');
            $this->write('微信管理', '同步全部微信粉丝成功');
            return resultArray(['data' => '同步获取所有粉丝成功']);

        }
        $this->error('同步获取粉丝失败，请稍候再试！');
    }
    /**
     * 设置黑名单
     */
    public function backadd()
    {
        $wechat = new weChatUser();
        $param = $this->param;
        $openIds = $param['Ids'];
        if (false !== $wechat->addBacklist($openIds)) {
            $wechat=model('WeChat');
            $wechat->setBack($openIds);
            $this->success("已成功将 " . count($openIds) . " 名粉丝移到黑名单!", '');
        }
        $this->error("设备黑名单失败，请稍候再试！{$wechat->errMsg}[{$wechat->errCode}]");
    }
}