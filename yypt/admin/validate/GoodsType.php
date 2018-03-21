<?php
namespace yypt\admin\validate;

use think\Validate;

class GoodsType extends Validate{
	protected $rule = [
		['name', 'require', '模型名称不能为空'],
	];
}