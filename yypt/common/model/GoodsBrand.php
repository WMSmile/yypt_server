<?php
namespace yypt\common\model;

use think\Loader;

class GoodsBrand extends Common{
    
    protected $pk = "id";
	
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
	 * @param array $where 查询条件
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
		
		$validate = Loader::validate('yypt\admin\\validate\Brand');
		
		if(!$validate->check($data)){
			return ['msg'=>$validate->getError()];
		}
		$category = new GoodsCategory();
		
		$cat_name = '';
		if($data['parent_cat_id']) {
			$cat1 = $category->getInfoById($data['parent_cat_id']); 
			$cat_name .= $cat1['name'];
		}
		if($data['cat_id'])	{
			$cat2 = $category->getInfoById($data['cat_id']); 
			$cat_name .= '——'.$cat2['name'];
		}
		$data['cat_name'] = $cat_name;
		
		return $this->allowField(true)->isUpdate($data['id']?true:false)->save($data);
	}
	
	public function remove($id){
		return $this->db()->where(['id'=>$id])->delete();
	}
}