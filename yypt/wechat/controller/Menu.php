<?php
/**
 * Created by PhpStorm.
 * Description:
 * User: ChenXiaoHua
 * Mail: chenxiaohua@civilizationdata.com
 */

namespace yypt\wechat\controller;


use yypt\common\controller\ApiCommon;

class Menu extends ApiCommon
{
    public function index(){
        $WeChat = model('WeChat');
        $param = $this->param;
        $keywords = !empty($param['keywords']) ? $param['keywords']: '';
        $page = !empty($param['page']) ? $param['page']: '';
        $limit = !empty($param['limit']) ? $param['limit']: '';
        $data = $WeChat->weChatMenuList($keywords, $page, $limit);
        return resultArray(['data' => $data]);
    }
    public function save()
    {
        $weChat = model('WeChat');
        $param = $this->param;
        $data = $weChat->createMenu($param);
        if (!$data) {
            return resultArray(['error' => $weChat->getError()]);
        }
        return resultArray(['data' => '添加成功']);
    }
    public function delete(){
        $weChat=model('WeChat');
        $param = $this->param;
        $res=$weChat->delMenu($param['id']);
        if (!$res) {
            return resultArray(['error' => $weChat->getError()]);
        }
        return resultArray(['data' => '删除成功']);

    }
    public function update()
    {
        $ruleModel = model('Rule');
        $param = $this->param;
        $data = $param['data'];
        



        $data = $ruleModel->updateDataById($param, $param['id']);
        if (!$data) {
            return resultArray(['error' => $ruleModel->getError()]);
        }
        return resultArray(['data' => '编辑成功']);
    }


}