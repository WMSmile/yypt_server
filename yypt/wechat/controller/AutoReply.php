<?php
/**
 * Created by PhpStorm.
 * Description:自动回复
 * User: ChenXiaoHua
 * Mail: chenxiaohua@civilizationdata.com
 */

namespace yypt\wechat\controller;


use yypt\common\controller\ApiCommon;

class AutoReply extends ApiCommon
{
    public function index(){
        $userModel = model('WeChat');
        $param = $this->param;
        $type = !empty($param['type']) ? $param['type']: '';
        $keywords = !empty($param['keywords']) ? $param['keywords']: '';
        $page = !empty($param['page']) ? $param['page']: '';
        $limit = !empty($param['limit']) ? $param['limit']: '';
        $data = $userModel->getRepayList($type,$keywords, $page, $limit);
        return resultArray(['data' => $data]);

    }


}