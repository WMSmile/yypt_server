<?php
/**
 * Created by PhpStorm.
 * Description: 基础框架路由配置文件
 * User: ChenXiaoHua
 * Mail: chenxiaohua@civilizationdata.com
 */
return [
    // 定义资源路由
    '__rest__'=>[
        'admin/rules'		   =>'admin/rules',
        'admin/groups'		   =>'admin/groups',
        'admin/users'		   =>'admin/users',
        'admin/menus'		   =>'admin/menus',
        'admin/structures'	   =>'admin/structures',
        'admin/posts'          =>'admin/posts',
        'admin/goods'          =>'admin/goods',
        'admin/category'       => 'admin/category',
        'admin/brand'          => 'admin/brand',
        'admin/type'           => 'admin/type',
        'admin/weChat'          =>'admin/weChat',
        'wechat/rentalOder'    =>'wechat/rentalOder',
        'wechat/retailOder'    =>'wechat/retailOder',
        'wechat/Inventories'    =>'wechat/Inventories',
        'wechat/Keys'    =>'wechat/Keys',
        'wechat/Fans'    =>'wechat/Fans',
        'wechat/Menu'    =>'wechat/Menu',
        'wechat/pay'    =>'wechat/Pay',
        'shop'=>'shop'
//        'uploads'              =>'/uploads',
    ],
    // 测试





    'admin/base/pg' => ['admin/base/pg', ['method' => 'GET']],
    //----------//
	// 【基础】登录
	'admin/base/login' => ['admin/base/login', ['method' => 'POST']],
	// 【基础】记住登录
	'admin/base/relogin'	=> ['admin/base/relogin', ['method' => 'POST']],
	// 【基础】修改密码
	'admin/base/setInfo' => ['admin/base/setInfo', ['method' => 'POST']],
	// 【基础】退出登录
	'admin/base/logout' => ['admin/base/logout', ['method' => 'POST']],
	// 【基础】获取配置
	'admin/base/getConfigs' => ['admin/base/getConfigs', ['method' => 'POST']],
	// 【基础】获取验证码
	'admin/base/getVerify' => ['admin/base/getVerify', ['method' => 'GET']],
	// 【基础】上传图片
	'admin/upload' => ['admin/upload/index', ['method' => 'POST']],
	// 保存系统配置
	'admin/systemConfigs' => ['admin/systemConfigs/save', ['method' => 'POST']],
	// 【规则】批量删除
	'admin/rules/deletes' => ['admin/rules/deletes', ['method' => 'POST']],
	// 【规则】批量启用/禁用
	'admin/rules/enables' => ['admin/rules/enables', ['method' => 'POST']],
	// 【用户组】批量删除
	'admin/groups/deletes' => ['admin/groups/deletes', ['method' => 'POST']],
	// 【用户组】批量启用/禁用
	'admin/groups/enables' => ['admin/groups/enables', ['method' => 'POST']],
	// 【用户】批量删除
	'admin/users/deletes' => ['admin/users/deletes', ['method' => 'POST']],
	// 【用户】批量启用/禁用
	'admin/users/enables' => ['admin/users/enables', ['method' => 'POST']],
	// 【菜单】批量删除
	'admin/menus/deletes' => ['admin/menus/deletes', ['method' => 'POST']],
	// 【菜单】批量启用/禁用
	'admin/menus/enables' => ['admin/menus/enables', ['method' => 'POST']],
	// 【组织架构】批量删除
	'admin/structures/deletes' => ['admin/structures/deletes', ['method' => 'POST']],
	// 【组织架构】批量启用/禁用
	'admin/structures/enables' => ['admin/structures/enables', ['method' => 'POST']],
	// 【部门】批量删除
	'admin/posts/deletes' => ['admin/posts/deletes', ['method' => 'POST']],
	// 【部门】批量启用/禁用
	'admin/posts/enables' => ['admin/posts/enables', ['method' => 'POST']],
    //推送服务
    'wechat/service' => ['wechat/service/index', ['method' => 'GET|POST']],
    //支付异步通知
    'wechat/pay/notify' => ['wechat/pay/notify', ['method' => 'GET|POST']],
    'wechat/pay/gzh' => ['wechat/pay/gzh', ['method' => 'GET']],
    //地区
    'wechat/inventories/regionsAll' => ['wechat/inventories/regionsAll', ['method' => 'GET']],
    //获取有库存区域
    'weChat/inventories/regions' => ['wechat/inventories/regions', ['method' => 'GET']],
    //发货
    'weChat/inventories/shipments' => ['wechat/inventories/shipments', ['method' => 'POST']],
    // 获取系统配置
    'wechat/base/getConfigs' => ['wechat/base/getConfigs', ['method' => 'GET']],
    //【微信公众号】同步粉丝
    'weChat/sync' => ['wechat/fans/sync', ['method' => 'GET']],
    //【微信公众号】设置关键字状态
    'weChat/keysStatus' => ['wechat/keys/status', ['method' => 'GET']],
    //【微信公众号】删除关键字
    'keys/del' => ['wechat/keys/delete', ['method' => 'GET']],
    //设置到黑名单
    'weChat/backAdd' => ['wechat/fans/backadd', ['method' => 'POST']],
    //【微信公众号】保存系统配置
    'wechat/systemConfigs' => ['wechat/systemConfigs/save', ['method' => 'POST']],
    //【微信公众号】获取token信息
    'weChatApi/getAccessToken'=>['wechat/WeChatApi/getAccessToken', ['method' => 'GET']],
    //【微信公众号】列表
    'weChatSetting/list'=>['wechat/Index/weChatList', ['method' => 'GET']],
    //【微信公众号】添加公众号
    'weChatSetting/saveWeChat'=>['wechat/Index/saveWeChat', ['method' => 'POST']],
    //【微信公众号】删除公众号
    'weChatSetting/del'=>['wechat/Index/weChatDel', ['method' => 'GET']],
    //【微信公众号】设置微信菜单
    'weChatApi/createMenu'=>['wechat/WeChatApi/createMenu', ['method' => 'GET']],
    //【微信公众号】删除微信菜单
    'weChatMenu/del'=>['wechat/menu/delete', ['method' => 'GET']],
    //【微信公众号】获取粉丝
    'weChatApi/getUserLists'=>['wechat/WeChatApi/getUserLists', ['method' => 'GET']],
    // 【分类】找到父级列表
    'admin/category/parent' => ['admin/category/parent', ['method' => 'GET']],
    // 【属性】 商品属性列表
    'admin/type/attribute' => ['admin/type/attribute', ['method' => 'GET']],
    // 【属性】 商品属性编辑
    'admin/type/editAttr' => ['admin/type/editAttribute',['method' => 'GET']],
    // 【属性】 商品属性保存
    'admin/type/saveAttr' => ['admin/type/saveAttribute',['method' => 'POST']],
    // 【属性】 商品属性删除
    'admin/type/deleteAttr' => ['admin/type/deleteAttribute',['method' => 'GET']],
    // 【规格】 商品规格列表
    'admin/type/spec' => ['admin/type/spec',['method' => 'GET']],
    // 【规格】 商品规格编辑
    'admin/type/editSpec' => ['admin/type/editSpec',['method' => 'GET']],
    // 【规格】 商品规格保存
    'admin/type/saveSpec' => ['admin/type/saveSpec',['method' => 'POST']],
    // 【规格】 商品规格删除
    'admin/type/deleteSpec' => ['admin/type/deleteSpec',['method' => 'GET']],
    // 【类型】 找到类型
    'admin/type/type' => ['admin/type/type',['method' => 'GET']],



	
	// MISS路由
	'__miss__'  => 'admin/base/miss',
];