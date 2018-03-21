<?php

/**
 * Created by PhpStorm.
 * Description: 用户组
 * User: ChenXiaoHua
 * Mail: chenxiaohua@civilizationdata.com
 */
namespace yypt\common\model;


class Group extends Common 
{
    /**
     * 为了数据库的整洁，同时又不影响Model和Controller的名称
     * 我们约定每个模块的数据表都加上相同的前缀，比如微信模块用weixin作为数据表前缀
     */
	protected $name = 'admin_group';

	/**
	 * [getDataList 获取列表]
	 * @return    [array]                         
	 */
	public function getDataList()
	{
		$cat = new \com\Category('admin_group', array('id', 'pid', 'title', 'title'));
		$data = $cat->getList('', 0, 'id');
		
		return $data;
	}
}