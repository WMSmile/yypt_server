<?php
/**
 * Created by PhpStorm.
 * Description: 商品模型
 * User: ChenXiaoHua
 * Mail: chenxiaohua@civilizationdata.com
 */

namespace yypt\common\model;

use think\Db;

class Goods extends Common{
//     protected $autoWriteTimestamp = 'datetime';
//     protected $createTime = 'create_time';
//    protected $updateTime = 'last_update';
    
    /**
     * 获取数据信息
     * @param number $page 页码
     * @param array $where 查询条件
     */
    public function getInfo($page = 0, $where = [], $limit = 15){
        return $this->db()
        ->alias("a")
        ->field("a.id, a.goods_name, a.goods_code, a.shop_price, a.is_recommend,
                a.is_new, a.is_hot, a.is_on_sale, c.name cname")
        ->join("yy_goods_category c", "c.id = a.cat_id", "LEFT")
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
    
    /**
     * 添加保存商品
     * @param array $data
     */
    public function saveGoods($data){
        $data['cat_id'] = ($data['pid_3'])?$data['pid_3']:(($data['pid_2'])?$data['pid_2']:$data['pid_1']);

        $data['create_time']= date('Y-m-d H:i:s', time());
        $data['on_time']= date('Y-m-d H:i:s', time());
        $data['last_update']=date('Y-m-d H:i:s', time());

        $this->allowField(true)->isUpdate((isset($data['id']) && $data['id'])?true:false)->save($data);


        $this->saveGoodsAttr($this->data['id'], $data);
        $this->saveGoodsSpec($this->data['id'], $data);
        $this->saveGoodsImgs($this->data['id'], $data);
        return true;
    }
    
    /**
     * 保存商品图片
     * @param int $goods_id
     * @param array $post
     */
    private function saveGoodsImgs($goods_id, $post){
        $goodsImageModel = Db::name("GoodsImage");
        
        if(!isset($post["imgs"]) || empty($post['imgs'])){
            $goodsImageModel->where(['goods_id'=>$goods_id])->delete();
            return ;
        }
        
        $res = $goodsImageModel->where(['goods_id'=>$goods_id])->order('sort')->select();
        $imgs = convertArrayKey($res, "image");
        
        foreach ($post['imgs'] as $k => $v){
            if(!isset($v['path'])) continue;
            $img = $v['path'];
            
            if(array_key_exists($img, $imgs)){
                $imgData = $imgs[$img];
                $imgData['sort'] = $k; // 更新顺序
                $goodsImageModel->update($imgData);
                
                unset($imgs[$img]);
            }else{
                $goodsImageModel->insert(['goods_id'=>$goods_id, 'image'=>$img, 'sort'=>$k]);
            }
        }
        
        foreach ($imgs as $v){
            $goodsImageModel->delete($v);            
        }
    }
    
    /**
     * 保存商品属性
     * @param int $goods_id 商品id
     * @param int $goods_type 商品类型
     * @param array $post 提交的post数据
     */
    private function saveGoodsAttr($goods_id, $post){
        $goodsAttrModel = Db::name("GoodsAttr");
        
        // 属性类型被更改了 就先删除以前的属性类型 或者没有属性 则删除
        if($post['goods_type'] == 0)
        {
            $goodsAttrModel->where(['goods_id'=>$goods_id])->delete();
            return;
        }
        
        if(!isset($post['attr'])) return ;
        $attrList = $goodsAttrModel->where(['goods_id'=>$goods_id])->select();
        $old_goods_attr = convertArrayKey($attrList, 'attr_id'); // 数据库中的的属性  并以attr_id 为键
        
        // post 提交的属性 以attr数组为
        foreach($post['attr'] as $v) {
            $attr_id = $v['id'];
            $attr_value = (isset($v['value']))? $v['value'] : "";
            
            // 判断更新、删除或者添加
            if(isset($old_goods_attr[$attr_id])){
                
                if(empty($attr_value)) {
                    $goodsAttrModel->delete($old_goods_attr[$attr_id]);
                    continue;
                }
                
                // 判断是否更新
                if($attr_value != $old_goods_attr[$attr_id]['attr_value']){
                    $old_goods_attr[$attr_id]['attr_value'] = $attr_value;
                    $goodsAttrModel->update($old_goods_attr[$attr_id]);
                }
                
            }else{
                if(empty($attr_value)) continue;    // 空值不添加
                $goodsAttrModel->insert(['goods_id'=>$goods_id, 'attr_id'=>$attr_id, 'attr_value'=>$attr_value]);
            }
            
        }
        
    }
    
    /**
     * 保存商品规格信息
     * @param int $goods_id 商品id
     * @param array $post 提交的post数据
     */
    private function saveGoodsSpec($goods_id, $post){
        $goodsSpecPriceModel = Db::name("SpecGoodsPrice");
        
        // 如果没有设置规格或者类型被更改了，则全部删除以前规格
        if(!isset($post['spec']) || $post['goods_type'] == 0){
            $goodsSpecPriceModel->where(['goods_id'=>$goods_id])->delete();
            return ;
        }
        
        if(!isset($post['spec'])) return ;
        
        $res = $goodsSpecPriceModel->where(['goods_id'=>$goods_id])->select();
        $res = convertArrayKey($res, 'key');
        
        $info = $post['spec'];
        $keyArr = "";
        
        foreach ($info as $v){
            $k = $v['key'];
            $keyArr .= "$k,";
            $data = ['key'=>$k, 'goods_id'=>$goods_id, 'key_name'=>$v['key_name'], 'price'=>$v['price'], 'store_count'=>$v['store_count'], 'sku'=>$v['sku']];
            
            if(isset($res[$k])){
                $data['id'] = $res[$k]['id'];
                $goodsSpecPriceModel->update($data);
            }else{
                $goodsSpecPriceModel->insert($data);
            }
        }
        
        // 删除不在key值内的多余数据
        if($keyArr){
            Db::name('spec_goods_price')->where(['goods_id'=>$goods_id])->whereNotIn('key',$keyArr)->delete();
        }
        
    }
}