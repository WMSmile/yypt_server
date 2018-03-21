<?php
/**
 * Created by PhpStorm.
 * Description:
 * User: ChenXiaoHua
 * Mail: chenxiaohua@civilizationdata.com
 */

namespace yypt\wechat\controller;


use yypt\common\controller\ApiCommon;

class Index extends ApiCommon
{
    public function index()
    {   $param = $this->param;
        $cid = !empty($param['cid']) ? $param['cid']: '';
        if(empty($cid) && !is_numeric($cid)){
            $this->error('参数有误');
        }
        $weChat=model('WeChat');
        $data=$weChat->getWeChat($cid);
        return resultArray(['data' => $data]);

    }
    public function weChatList(){
        $weChat=model('WeChat');
        $data = $weChat->getWeChatList($this->userInfo['id']);
        return resultArray(['data' => $data]);
    }
    public function  editWeChat(){
        $goodsModel = model('WeChat');
        $param = $this->param;
        $data = $goodsModel->updateWeChat($param);
        if (!$data) {
            return resultArray(['error' => $goodsModel->getError()]);
        }
        return resultArray(['data' => '编辑成功']);
    }
    public function saveWeChat()
    {
        $weChat = model('WeChat');
        $param = $this->param;
        $param['user_id']=$this->userInfo['id'];
        if($param['id']){
            $data = $weChat->updateWeChat($param);
        }else{
            $data = $weChat->createWeChat($param);
        }

        if (!$data) {
            return resultArray(['error' => $weChat->getError()]);
        }
        return resultArray(['data' => '添加成功']);
    }
    public function weChatDel(){
        $weChat = model('WeChat');
        $param = $this->param;
        $data = $weChat->delWeChat($param['id']);
        if (!$data) {
            return resultArray(['error' => $weChat->getError()]);
        }
        return resultArray(['data' => '删除成功']);
    }

}