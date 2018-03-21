<?php

/**
 * Created by PhpStorm.
 * Description: 系统配置
 * User: ChenXiaoHua
 * Mail: chenxiaohua@civilizationdata.com
 */
namespace yypt\admin\controller;
use yypt\common\controller\ApiCommon;
class SystemConfigs extends ApiCommon
{
    public function save()
    {
        $configModel = model('SystemConfig');
        $param = $this->param;
        $data = $configModel->createData($param);
        if (!$data) {
            return resultArray(['error' => $configModel->getError()]);
        } 
        return resultArray(['data' => '添加成功']);	
    }
}
 