<?php
/**
 * Created by PhpStorm.
 * Description:
 * User: ChenXiaoHua
 * Mail: chenxiaohua@civilizationdata.com
 */

namespace yypt\wechat\controller;

use think\Db;
use yypt\common\controller\Common;

class Base extends Common
{
    public function getConfigs()
    {
        $systemConfig = cache('DB_WECHAT_CONFIG_DATA');
        if (!$systemConfig) {
            //获取所有系统配置
            $systemConfig = model('admin/SystemConfig')->getDataList();
            cache('DB_WECHAT_CONFIG_DAT', null);
            cache('DB_WECHAT_CONFIG_DAT', $systemConfig, 36000); //缓存配置
        }
        return resultArray(['data' => $systemConfig]);
    }


}