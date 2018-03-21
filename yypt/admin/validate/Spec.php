<?php
namespace yypt\admin\validate;

use think\Validate;

class Spec extends Validate{
	protected $rule = [
		['name', 'require', '规格名称不能为空'],
		['type_id', '>:0', '请选择属性模型'],
		['spec_values', 'require', '规格项不能为空'],
	];
}