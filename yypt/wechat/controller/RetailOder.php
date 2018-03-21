<?php
/**
 * Created by PhpStorm.
 * Description:租赁订单
 * User: ChenXiaoHua
 * Mail: chenxiaohua@civilizationdata.com
 */

namespace yypt\wechat\controller;
use yypt\common\controller\ApiCommon;

class RetailOder extends ApiCommon
{
    public function index(){
        $goods=model('Cdata');
        $param = $this->param;
        $keywords = !empty($param['keywords']) ? json_decode($param['keywords'],true): '';
        $page = !empty($param['page']) ? $param['page']: '';
        $limit = !empty($param['limit']) ? $param['limit']: '';
        $data=$goods->getRetailOderList($keywords, $page, $limit);
        return resultArray(['data' => $data]);
    }

}