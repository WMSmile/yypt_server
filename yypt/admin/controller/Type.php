<?php
namespace yypt\admin\controller;

use yypt\common\controller\ApiCommon;
use yypt\common\model\GoodsAttribute;
use yypt\common\model\GoodsType;
use yypt\common\model\Spec;

class Type extends ApiCommon{
    private $typeModel, $attributeModel, $specModel;
    
    public function _initialize(){
        parent::_initialize();
        
        $this->typeModel = new GoodsType();
        $this->attributeModel = new GoodsAttribute();
        $this->specModel = new Spec();
    }
    
    public function index(){
        $p = $this->request->get('p', 0);
        $name = $this->request->get('n');
        $limit = $this->request->param("limit");
        
        $where = [];
        
        if($name) $where['name'] = ['like', "%$name%"];
        
        $info = $this->typeModel->getInfo($p, $where, $limit);
        $count = $this->typeModel->getCount($where);
        
        return resultArray(['data'=>['info'=>$info, 'count'=>$count]]);
    }
    
    public function read(){
        $info = $this->typeModel->getInfoById($this->request->param('id'));
        
        return resultArray(['data'=>$info]);
    }
    
    public function save(){
        $res = $this->typeModel->add();
        
        if(isset($res['msg'])){
            return resultArray(['error'=>$res['msg']]);
        }
        
        return resultArray(['data'=>$res]);
    }
    
    public function delete(){
        $id = $this->request->param('id');
        $res = $this->typeModel->delDataById($id);
        
        if (!$res) {
            return resultArray(['error' => $this->typeModel->getError()]);
        }
        return resultArray(['data' => '删除成功']);
    }
    
    public function attribute(){
        $p = $this->request->get('p', 0);
        $type = $this->request->param('type');
        
        $where = [];
        if($type) $where['type_id'] = $type;
        
        $info = $this->attributeModel->getInfo($p, $where);
        
        return resultArray(['data'=>$info]);
    }
    
    public function editAttribute(){
        $id = $this->request->param('id');
        $info = $this->attributeModel->getInfoById($id);
        $types = $this->typeModel->getAll();
        
        $data = ['types'=>$types, 'info'=>$info];
        return resultArray(['data'=>$data]);
    }
    
    public function saveAttribute(){
        $res = $this->attributeModel->add();
        
        if(isset($res['msg'])){
            return resultArray(['error'=>$res['msg']]);
        }
        
        return resultArray(['data'=>$res]);
    }
    
    public function deleteAttribute(){
        $id = $this->request->get('id');
        $res = $this->attributeModel->delDataById($id);
        
        if (!$res) {
            return resultArray(['error' => $this->attributeModel->getError()]);
        }
        return resultArray(['data' => '删除成功']);
    }
    
    public function spec(){
        $p = $this->request->param('p', 0);
        $type = $this->request->param('type');
        
        $where = [];
        if($type) $where['type_id'] = $type;
        
        $info = $this->specModel->getInfo($p, $where);
        
        return resultArray(['data'=>$info]);
    }
    
    public function editSpec(){
        $id = $this->request->param('id');
        
        $info = $this->specModel->getInfoById($id);
        $types = $this->typeModel->getAll();
        
        $data = ['info'=>$info, 'types'=>$types];        
        return resultArray(['data'=>$data]);
    }
    
    public function saveSpec(){
        $res = $this->specModel->add();
        
        if(isset($res['msg'])){
            return resultArray(['error'=>$res['msg']]);
        }
        
        return resultArray(['data'=>$res]);
    }
    
    public function deleteSpec(){
        $id = $this->request->get('id');
        $res = $this->specModel->removeSpec($id);
        
        if (!$res) {
            return resultArray(['error' => $this->specModel->getError()]);
        }
        return resultArray(['data' => '删除成功']);
    }
    
    public function type(){
        $typeId = $this->request->get('id');
        
        $spec = $this->specModel->getSpecShowPageByType($typeId);
        $attr = $this->attributeModel->getAttrShowPageByType($typeId);
        $res = ['spec'=>$spec, 'attr'=>$attr];
        return json(['data'=>$res]);
    }
}