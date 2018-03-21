<?php
namespace yypt\admin\controller;

use yypt\common\model\GoodsCategory;
use yypt\common\controller\ApiCommon;

class Brand extends ApiCommon{
    private $brandModel;
    
    public function _initialize(){
        parent::_initialize();
        
        $this->brandModel = new \yypt\common\model\GoodsBrand();
    }
    
    public function index(){
        $p = $this->request->param('p', 0);
        $name = $this->request->param('n');
        $limit = $this->request->param('limit', 15);
        
        $where = [];
        
        if($name) $where['name'] = ['like', "%$name%"];
        
        $info = $this->brandModel->getInfo($p, $where, $limit);
        $count = $this->brandModel->getCount($where);
        
        return resultArray(['data'=>['info'=>$info, 'count'=>$count]]);
    }
    
    public function read(){
        $info = $this->brandModel->getInfoById($this->param['id']);
        
        $category = new GoodsCategory();
        $parent = $category->getParentCategory();
        $cate = [];
        if($info['parent_cat_id'] && $info['cat_id']){
            $cate = $category->getParentCategory($info['parent_cat_id']);
        }
        
        $data = ['cate'=>$cate, 'info'=>$info, 'parent'=>$parent];
        
        return resultArray(['data' => $data]);
    }
    
    public function save(){
        $res = $this->brandModel->add();
        
        if(isset($res['msg'])){
            return resultArray(['error'=>$res['msg']]);
        }
        
        return resultArray(['data'=>$res]);
    }
    
    public function delete(){
        $id = $this->request->param('id');
        $res = $this->brandModel->delDataById($id);
        
        if (!$res) {
            return resultArray(['error' => $this->brandModel->getError()]);
        }
        return resultArray(['data' => '删除成功']);   
    }
    
}