<?php
namespace yypt\admin\controller;

use yypt\common\model\GoodsCategory;
use yypt\common\controller\ApiCommon;

class Category extends ApiCommon {
    
    private $categoryModel;
    
    public function _initialize(){
        parent::_initialize();
        
        $this->categoryModel = new GoodsCategory();
    }
    
    public function index(){
        $p = $this->request->param('p', 0);
        $name = $this->request->param('n');
        $limit = $this->request->param('limit', config(''));
        
        $where = [];
        
        if($name) $where['name'] = ['like', "%$name%"];
        
        $info = $this->categoryModel->getInfo($p, $where, $limit);
        $count = $this->categoryModel->getCount($where);
        
        return resultArray(['data'=>['info'=>$info, 'count'=>$count]]);
    }
    
    public function read(){
        $info = $this->categoryModel->getInfoById($this->param['id']);
        $parent = $this->categoryModel->getParentCategory();
        $level = $this->categoryModel->getLevelCate($info);
        list($cate, $temp) = $this->categoryModel->getCategoryInfo($info);
        
        $data = ['data'=>['info'=>$info, 'parent'=>$parent, 'cate'=>$cate, 'lv'=>$level]];
        return resultArray($data);
    }
    
    public function parent(){
        $pid = $this->request->get('id');
        $parent = $this->categoryModel->getParentCategory($pid);
        
        return resultArray(['data'=>$parent]);
    }
    
    public function save(){
        $res = $this->categoryModel->add();
        
        if(isset($res['msg'])){
            return resultArray(['error'=>$res['msg']]);
        }
        
        return resultArray(['data'=>$res]);
    }
    
    public function delete()
    {
        $data = $this->categoryModel->remove($this->param['id']);
        if (!$data) {
            return resultArray(['error' => $this->categoryModel->getError()]);
        }
        return resultArray(['data' => '删除成功']);
    }
    
}