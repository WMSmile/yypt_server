<?php
namespace yypt\common\model;

use think\Loader;

/**
 * 商品分类模型
 * @author Superbee
 *
 */
class GoodsCategory extends Common{
    
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
     * 根据id找到父类信息
     * @param number $pid 父类id
     * @return \think\Collection|string
     */
    public function getParentCategory($pid = 0){
        return $this->db()->where(['parent_id'=>$pid])->field('id, name, parent_id, level')->select();
    }
    
    /**
     * 获得列表信息
     * @param array $info 类型信息
     * @return \think\Collection|string|array
     */
    public function getCategoryInfo($info){
        
        switch ($info['level']){
            case 2:
                $info = $this->getParentCategory($info['parent_id']);
                return [$info, []];
            case 3:
                $res = $this->getLevelCate($info);
                $info1 = $this->getParentCategory($res[1]);
                $info2 = $this->getParentCategory($res[2]);
                return [$info1, $info2];
        }
        
        return [[], []];
    }
    
    /**
     * 获取等级对应信息
     * @param array $info 类型信息
     * @return array|mixed[]
     */
    public function getLevelCate($info){
        if(empty($info) || empty($info['parent_id_path'])) return [];
        $arr = [];
        $res = explode("_", $info['parent_id_path']);
        
        if(isset($res[3])) $arr[3] = $res[3];
        if(isset($res[2])) $arr[2] = $res[2];
        if(isset($res[1])) $arr[1] = $res[1];
        
        return $arr;
    }
    
    /**
     * 添加/更新 数据
     * @return number|string
     */
    public function add(){
        $data = request()->post();
        $validate = Loader::validate("yypt\admin\\validate\GoodsCategory");
        
        if(!$validate->check($data)){
            return ['msg'=>$validate->getError()];
        }
        
        $path = [0];
        // 给父id赋值
        if($data['pid_1']){
            $path[] = $data['pid_1'];
            
            if($data['pid_2']){
                $path[] = $data['pid_2'];
                $data['parent_id'] = $data['pid_2'];
                $data['level'] = 3;
            }else{
                $data['level'] = 2;
                $data['parent_id'] = $data['pid_1'];
            }
        }else{
            $data['level'] = 1;
        }
        unset($data['pid_1']);
        unset($data['pid_2']);
        
        // 如果类型的id为空
        if(empty($data['id'])){
            $data['id'] = $this->db()->insertGetId($data);
        }
        $path[] = $data['id'];
        $data['parent_id_path'] = implode("_", $path);
        
        return $this->db()->update($data);
    }
    
    /**
     * 删除一条数据
     * @param number $id
     * @return number
     */
    public function remove($id){
        return $this->db()->delete(['id'=>$id]);
    }
    
}