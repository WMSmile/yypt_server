<?php
namespace yypt\common\model;

use think\Loader;
use think\Db;

class GoodsAttribute extends Common{
	
	/**
	 * 获取数据信息
	 * @param number $page 页码
	 * @param array $where 查询条件
	 */
	public function getInfo($page = 0, $where = [], $limit = 15){
		return $this->db()
		->alias('a')
		->field('a.id, a.name, t.name tname, a.attr_index, a.attr_type, a.input_type, a.values, a.sort')
		->join('goods_type t', 't.id = a.type_id', 'left')
		->where($where)
		->page($page, $limit)
		->select();
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
	 * 根据商品id找到信息
	 * @param number $goods_id
	 * @return \think\Collection|string
	 */
	public function getInfoByGoodsId($goods_id){
	    return $this->db()->where(['goods_id'=>$goods_id])->select();
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
		$validate = Loader::validate("yypt\admin\\validate\GoodsAttribute");
		
		if(!$validate->check($data)){
			return ['msg'=>$validate->getError()];
		}
		
		return $this->allowField(true)->isUpdate(($data['id'])?true:false)->save($data);
	}

	/**
	 * 返回js所用的属性值
	 * @param int $type
	 * @return string[][]|array[][]|string[][]
	 */
	public function getAttrShowPageByType($type){
	    $array = [];
	    if(empty($type)) return $array;
		$res = $this->db()->where(['type_id'=>$type])->order('sort')->select();
		
		foreach ($res as $k=>$v){
			$temp = [];
			$temp['id'] = $v['id'];
			$temp['name'] = 'attr_'.$v['id'];
			$temp['show'] = $v['name'];
			$temp['input'] = $v['input_type'];	// 0 唯一属性 1单选属性 2多选属性
			$temp['type'] = $v['attr_type'];	// 0 手工录入 1从列表中选择 2多行文本框
			$temp['index_value'] = explode("|", $v['values']);
			
			$array[] = $temp;
		}
		
		return $array;		
	}
	
	/**
	 * 根据商品id初始化属性信息
	 * @param int $type
	 * @param int $goods_id
	 * @return string[][]|array[][]|string[][]
	 */
	public function initAttrInfo($type, $goods_id){
	    $array = [];
	    if(empty($type) && empty($goods_id)) return $array;
	    $res = $this->db()->where(['type_id'=>$type])->order('sort')->select();
	    
	    $attr = Db::name("GoodsAttr")->where(['goods_id'=>$goods_id])->select();
	    $attr = convertArrayKey($attr, 'attr_id');
	    
	    foreach ($res as $k=>$v){
	        $temp = [];
	        $temp['id'] = $v['id'];
	        $temp['name'] = 'attr_'.$v['id'];
	        $temp['show'] = $v['name'];
	        $temp['input'] = $v['input_type'];	    // 0 唯一属性 1复选属性
	        $temp['type'] = $v['attr_type'];		// 0 手工录入 1从列表中选择 2多行文本框
	        if(isset($attr[$v['id']])) $temp['value'] = $attr[$v['id']]['attr_value'];	            
	        $temp['index_value'] = explode("|", $v['values']);
	        
	        $array[] = $temp;
	    }
	    
	    return $array;	
	}
}