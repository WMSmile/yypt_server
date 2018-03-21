<?php
/**
 * Created by PhpStorm.
 * Description:
 * User: ChenXiaoHua
 * Mail: chenxiaohua@civilizationdata.com
 */

namespace yypt\weChat\validate;


use think\Validate;

class weChatInventories extends Validate
{
    protected $rule = array(
        'uuid'      		=> 'require',
    );
    protected $message = array(
        'uuid.require'    		=> '主板编号必须填写',
    );

}