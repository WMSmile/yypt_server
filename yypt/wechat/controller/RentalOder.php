<?php
/**
 * Created by PhpStorm.
 * Description:
 * User: ChenXiaoHua
 * Mail: chenxiaohua@civilizationdata.com
 */

namespace yypt\wechat\controller;
use yypt\common\controller\ApiCommon;

class RentalOder extends ApiCommon
{
    public function  index(){
        $goods=model('Cdata');
        $param = $this->param;
        $keywords = !empty($param['keywords']) ?json_decode($param['keywords'],true): '';
        $page = !empty($param['page']) ? $param['page']: '';
        $limit = !empty($param['limit']) ? $param['limit']: '';
        $data=$goods->getRentalOderList($keywords, $page, $limit);
        return resultArray(['data' => $data]);
    }
}