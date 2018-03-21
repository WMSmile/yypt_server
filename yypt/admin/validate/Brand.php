<?php
namespace yypt\admin\validate;

use think\Validate;

class Brand extends Validate{
	protected $rule = [
		['name', 'require', '分类名称不能为空'],
		['sort', 'number', '排序必须为数字'],
	];
}