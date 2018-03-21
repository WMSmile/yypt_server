<?php
namespace yypt\common\model;

use think\Loader;
use think\Db;

class Spec extends Common{
	
	/**
	 * 获取数据信息
	 * @param number $page 页码
	 * @param array $where 查询条件
	 */
	public function getInfo($page = 0, $where = [], $limit = 15){
		return $this->db()
		->alias('a')
		->field('a.id, t.name tname, a.name, a.sort, a.type_id, a.spec_index, a.spec_values')
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
	 * 获得所有的信息
	 * @return \think\Collection|string
	 */
	public function getAll(){
		return $this->db()->select();
	}
	
	public function add(){
		$data = request()->post();
		$validate = Loader::validate('yypt\admin\\validate\Spec');
		
		if(!$validate->check($data)){
			return ['msg'=>$validate->getError()];
		}
		
		$values = explode("|", $data['spec_values']);
		
		if(empty($data['id'])){
			$data['id'] = $this->db()->insertGetId($data);
		}else{
			$this->db()->update($data);
		}
		
		$this->saveSpecItem($data['id'], $values);		
		
		return $data['type_id']; 
	}
	
	/**
	 * 获取规格项目
	 * @param number $id
	 * @return \think\Collection|string
	 */
	public function getSpecItemById($id){
		return Db::name('specItem')->where('spec_id', '=', $id)->select();
	}
	
	/**
	 * 获取规格信息
	 * @param number $id
	 * @return string
	 */
	public function getStringItemById($id){
		$res = Db::name('specItem')->where('spec_id', '=', $id)->select();
		$return = [];
		
		foreach ($res as $v){
			$return[] = $v['item'];
		}
		
		return implode("|", $return);
	}
	
	private function saveSpecItem($specId, $values){
		$itemModel = Db::name('specItem');
		$res = $itemModel->where(['spec_id'=>$specId])->select();
		
		if($res){
			$exist= [];
			
			foreach ($res as $k=>$v){
				if(in_array($v['item'], $values)){
					$exist[] = $v['item'];
					unset($res[$k]);
				}
			}
			
			foreach ($res as $v){
				$itemModel->delete($v);
			}
			
			foreach ($values as $k=>$v){
				if (in_array($v, $exist)) unset($values[$k]);
			}
		}
		
		if($values){
			foreach ($values as $v){
				$itemModel->insert(['spec_id'=>$specId, 'item'=>$v]);
			}
		}
		
	}
	
	public function removeSpec($id){
	    Db::name('specItem')->where(['spec_id'=>$id])->delete();
	    return $this->db()->where(['id'=>$id])->delete();
	}
	
	/**
	 * 获取规格值
	 * @param int $type
	 * @return \think\Collection|string
	 */
	public function getSpecShowPageByType($type){
	    if(empty($type)) return [];
	    
	    $res = Db::name("Spec")->where(['type_id'=>$type])->where(['spec_index'=>1])->order('sort')->select();
		$key_res = convertArrayKey($res, 'id');	// 转换数组形式
		
		$ids = [];
		foreach ($res as $v){
			$ids[] = $v['id'];
		}
		
		$children = Db::name('specItem')->whereIn('spec_id', $ids)->select();
		foreach ($children as $v){
			if(array_key_exists($v['spec_id'], $key_res)){
				$key_res[$v['spec_id']]['child'][] = $v;
			}
		}
		
		return array_values($key_res);
	}
	
	/**
	 * 根据商品id初始化规格信息
	 * @param int $type
	 * @param int $goods_id
	 * @return array|array
	 */
	public function initSpecInfo($type, $goods_id){
	    if(empty($type) && empty($goods_id)) return [];
	    
	    // 找到类型下面的规格
	    $res = Db::name('Spec')->where(['type_id'=>$type])->where(['spec_index'=>1])->order('sort')->select();
	    $key_res = convertArrayKey($res, 'id');    // 转换键值对形式
	    
	    $ids = [];
	    foreach ($res as $v){
	        $ids[] = $v['id'];
	    }
	    
	    $column = [];       // 列名
	    $data = [];         // 数据
	    $children = Db::name('specItem')->whereIn('spec_id', $ids)->select();
	    
	    $spec = Db::name('SpecGoodsPrice')->where(['goods_id'=>$goods_id])->select();
	    $key_child = convertArrayKey($children, "id");
	    $exits = [];
	    // 添加names并找到表头
	    foreach ($spec as $v){
	        $val = explode("_", $v['key']);
	        $exits = array_merge($exits, $val);    // 合并数组
	        
	        foreach ($val as $key){
	            if(isset($key_child[$key]))$v['names'][] = $key_child[$key]['item'];
	        }
	        $data[] = $v;
	    }
	    $exits = array_unique($exits);     // 去掉重复值
	    
	    // 处理别选中的button
	    foreach ($children as $v){
	        if(in_array($v['id'], $exits)){
	            $v['isClick'] = 'success';
	            $name = $key_res[$v['spec_id']]['name'];
	            if(!in_array($name, $column)) $column[] = $name;    // 添加列名
	        }else{
	            $v['isClick'] = 'default';
	        }
	        
	        if(array_key_exists($v['spec_id'], $key_res)){
	            $key_res[$v['spec_id']]['child'][] = $v;
	        }
	    }
	    $column = array_unique($column);     // 去掉重复值
	    
	    return [$column, $data, array_values($key_res)];
	}
	
	/**
	 * 根据商品id找到规格售价信息
	 * @param int $goods_id
	 * @return array
	 */
	public function getSpecPriceByGoodsId($goods_id){
	    if(empty($goods_id)) return [null, null];
	   
	    $spec = Db::name('SpecGoodsPrice')->where(['goods_id'=>$goods_id])->select();
	   
	    $isfirst = true;
	    $columns = [];
	    foreach ($spec as $k=>$v){
	       $temp_col = [];
    	   $value = [];
	       $col = $v['key_name'];
	       $colname = explode(" ", $col);
	       
	       foreach ($colname as $val){
	           if(empty($val)) continue;
	           $temp = explode(":", $val);
	           $temp_col[] = $temp[0];
	           $value[] = $temp[1];
	       }
	       
	       $spec[$k]['value'] = $value;
	       
           if($isfirst){
               $columns = $temp_col;
               $isfirst = false;
           }
	    }
	   
	    return [$spec, $columns];
	}
}