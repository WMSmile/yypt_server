<?php
namespace yypt\common\model;

use think\Loader;

class GoodsType extends Common{
	
	/**
	 * 获取数据信息
	 * @param number $page 页码
	 * @param array $where 查询条件
	 */
	public function getInfo($page = 0, $where = [], $limit = 15){
		return $this->db()
		->where($where)
		->page($page, $limit)
		->select();
	}
	
	/**
	 * 获取总数
	 * @param array $where
	 * @return number|string
	 */
	public function getCount($where = []){
	    return $this->db()->where($where)->count();
	}
	
	/**
	 * 根据id找到信息
	 * @param number $id
	 * @return array|string|\think\Model
	 */
	public function getInfoById($id){
		return $this->db()->where(['id'=>$id])->find();
	}
	
	/**
	 * 获得所有的信息
	 * @return \think\Collection|string
	 */
	public function getAll(){
		return $this->db()->select();
	}
	
	public function add(){
		$data = request()->post();
		$validate = Loader::validate("yypt\admin\\validate\GoodsType");
		
		if(!$validate->check($data)){
			return ['msg'=>$validate->getError()];
		}
		
		return $this->allowField(true)->isUpdate(($data['id'])?true:false)->save($data);
	}
	
}