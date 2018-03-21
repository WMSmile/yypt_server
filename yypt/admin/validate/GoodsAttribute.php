<?php
namespace yypt\admin\validate;

use think\Validate;

class GoodsAttribute extends Validate{
	protected $rule = [
		['name', 'require', '分类名称不能为空'],
		['type_id', '>:0', '请选择属性模型'],	
	];
}