<?php
namespace yypt\admin\controller;

use think\Db;
use yypt\common\model\GoodsCategory;
use yypt\common\controller\ApiCommon;
use yypt\common\model\GoodsBrand;
use yypt\common\model\GoodsType;
use yypt\common\model\GoodsAttribute;
use yypt\common\model\Spec;

class Goods extends ApiCommon{
	
	private $goodsModel, $categoryModel, $brandModel, $typeModel;
	
	public function _initialize(){
		parent::_initialize();
		
		$this->goodsModel = new \yypt\common\model\Goods();
		$this->categoryModel = new GoodsCategory();
		$this->brandModel = new GoodsBrand();
		$this->typeModel = new GoodsType();
	}
	
	public function index(){
		$p = $this->request->get('p', 0);
		$name = $this->request->get('n');
		$limit = $this->request->param('limit', 15);
		
		$where = [];
		if($name) $where['goods_name'] = ['like', "%$name%"];
		
		$info = $this->goodsModel->getInfo($p, $where, $limit);
		$count = $this->goodsModel->getCount($where);
		
		return resultArray(['data'=>['info'=>$info, 'count'=>$count]]);
	}
	
	public function read(){
		$id = $this->request->param('id');
		
		$info = $this->goodsModel->getInfoById($id);
		$parent = $this->categoryModel->getParentCategory();
		$level = [];      // 分类等级
		$cate1 = [];      // 分类内容1
		$cate2 = [];      // 分类内容2
		$attr = [];       // 属性
		$spec = [];       // 规格
		$imgs = [];       // 图片
		$column = [];     // 列名
		$colData = [];    // 列值
		
		if($info){
		    $category = $this->categoryModel->getInfoById($info['cat_id']);
		    $level = $this->categoryModel->getLevelCate($category);
		    list($cate1, $cate2) = $this->categoryModel->getCategoryInfo($category);
		    
		    $specModel = new Spec();
		    $attributeModel = new GoodsAttribute();
		    
		    $attr = $attributeModel->initAttrInfo($info['goods_type'], $id);
		    list($column, $colData, $spec) = $specModel->initSpecInfo($info['goods_type'], $id);
		    
		    $imgs = $this->initImage($id);
		}
		
		$brands = $this->brandModel->getAll();
		$types = $this->typeModel->getAll();
		
		$data = ['info'=>$info, 'parent'=>$parent, 'brands'=>$brands, 'spec'=>$spec, 'attr'=>$attr, 'imgs'=>$imgs,
		    'column'=>$column, 'colinfo'=>$colData, 'types'=>$types, 'lv'=>$level, 'cate1'=>$cate1, 'cate2'=>$cate2];
		
		return resultArray(['data'=>$data]);
	}
	
	public function save(){
		$res = $this->goodsModel->saveGoods($this->request->post());
		
		if($res){
    		return resultArray(['data'=>$res]);
		}
		
	    return resultArray(['error'=>$this->goodsModel->getError()]);
	}
	
	public function parent(){
		$pid = $this->request->get('id');
		$parent = $this->categoryModel->getParentCategory($pid);
		
		return json($parent);
	}
	
	public function type(){
		$specModel = new Spec();
		$attributeModel = new GoodsAttribute();
		
		$typeId = $this->request->get('id');
		$spec = $specModel->getSpecShowPageByType($typeId);
		$attr = $attributeModel->getAttrShowPageByType($typeId);
		$res = ['spec'=>$spec, 'attr'=>$attr];
		return json($res);
	}
	
	private function initImage($goods_id){
	    if(empty($goods_id)) return ;
	    $res = Db::name("GoodsImage")->where(['goods_id'=>$goods_id])->order('sort')->select();
	    
	    $array = [];
	    foreach ($res as $k=>$v){
	        $temp = [];
	        $temp['name'] = "image$k";
	        $temp['url'] = "http://".$this->request->host().$this->request->root().DIRECTORY_SEPARATOR.$v['image'];
	        $temp['path'] = $v['image'];
	        $array[] = $temp;
	    }
	    
	    return $array;
	}
}