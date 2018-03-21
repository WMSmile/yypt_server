<?php
/**
 * Created by PhpStorm.
 * Description:
 * User: ChenXiaoHua
 * Mail: chenxiaohua@civilizationdata.com
 */

namespace yypt\wechat\controller;


use yypt\common\controller\ApiCommon;

class Keys extends ApiCommon
{
    public function index(){
        $WeChat = model('WeChat');
        $param = $this->param;
        $type = !empty($param['type']) ? $param['type']: '';
        $keywords = !empty($param['keywords']) ? $param['keywords']: '';
        $page = !empty($param['page']) ? $param['page']: '';
        $limit = !empty($param['limit']) ? $param['limit']: '';
        $data = $WeChat->weChatKeysList($keywords, $page, $limit);
        return resultArray(['data' => $data]);
    }
    public function save()
    {
        $weChat = model('WeChat');
        $param = $this->param;
        $data = $weChat->createKeys($param);
        if (!$data) {
            return resultArray(['error' => $weChat->getError()]);
        }
        return resultArray(['data' => '添加成功']);
    }
    public function update(){
        $goodsModel = model('WeChat');
        $param = $this->param;
        $data = $goodsModel->updateKeys($param['id'],$param);
        if (!$data) {
            return resultArray(['error' => $goodsModel->getError()]);
        }
        return resultArray(['data' => '编辑成功']);
    }
    public function delete(){
        $weChat=model('WeChat');
        $param = $this->param;
        $res=$weChat->delKeys($param['id']);
        if (!$res) {
            return resultArray(['error' => $weChat->getError()]);
        }
        return resultArray(['data' => '删除成功']);

    }
    public function status(){
        $weChat=model('WeChat');
        $param = $this->param;
        $res=$weChat->statusKeys($param);
        if (!$res) {
            return resultArray(['error' => $weChat->getError()]);
        }
        if($param['status']){
            return resultArray(['data' => '关键字禁用成功']);
        }
        return resultArray(['data' => '关键字启用成功']);

    }

}