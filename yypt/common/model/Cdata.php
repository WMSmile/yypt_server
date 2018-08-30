<?php
/**
 * Created by PhpStorm.
 * Description:
 * User: ChenXiaoHua
 * Mail: chenxiaohua@civilizationdata.com
 */

namespace yypt\common\model;


use think\Db;

class Cdata extends Common
{
    protected $connection = [
        // 数据库类型
        'type'        => 'pgsql',
        // 数据库连接DSN配置
        'dsn'         => '',
        // 服务器地址
        'hostname'    => 'localhost',
        // 数据库名
        'database'    => 'cdata_rental_production',
        // 数据库用户名
        'username'    => 'postgres',
        // 数据库密码
        'password'    => '',
        // 数据库连接端口
        'hostport'    => '5230', 
        // 数据库连接参数
        'params'      => [],
        // 数据库编码默认采用utf8
        'charset'     => 'utf8',
        // 数据库表前缀
        'prefix'      => '',
        // 数据库调试模式
        'debug'          => true,
        // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
        'deploy'         => 0,
        // 数据库读写是否分离 主从式有效
        'rw_separate'    => false,
        // 读写分离后 主服务器数量
        'master_num'     => 1,
        // 指定从服务器序号
        'slave_no'       => '',
        // 是否严格检查字段是否存在
        'fields_strict'  => true,
        // 数据集返回类型 array 数组 collection Collection对象
        'resultset_type' => 'array',
        // 是否自动写入时间戳字段
        //'auto_timestamp' => false,
        'auto_timestamp' => 'datetime',
        // 是否需要进行SQL性能分析
        'sql_explain'    => false,
        // 时间字段是否自动格式化输出
        'datetime_format' => false,
        'datetime_format' => 'Y-m-d H:i:s',

    ];
    protected $createTime = 'created_at';
    protected $updateTime = 'updated_at';
    //protected $name = 'goods';
    public function cdata($table='')
        {   if($table){
                 $data = Db::connect($this->connection)->name($table);
             }else{
                $data = Db::connect($this->connection);
             }

        return $data;
    }
    public function getGoodsData()
    {
        return $this->cdata('goods')->select();
    }

    //零售订单
    public function getRetailOderList($keywords,$page,$limit){
        $map = [];
        if ($keywords) {

            $map['nickname'] = !empty($keywords['nickname']) ?  ['like', '%'.trim($keywords['nickname']).'%']: '';
            $map['status'] =  !empty($keywords['status']) ?  $keywords['status']: '';
            $map['oid'] =!empty($keywords['oid']) ?  trim($keywords['oid']): [];
            $map['pay_at'] =!empty($keywords['pay_at']) ?  ['egt',$keywords['pay_at']]: '';
            $map['created_at'] =!empty($keywords['created_at']) ?  ['egt',$keywords['created_at']]: '';
        }
        $map=array_filter($map);
        $dataCount= $this->retailView ()->where($map)->count('ro.id');
        $list= $this->retailView ()->where($map);
        // 若有分页
        if ($page && $limit) {
            $list = $list->page($page, $limit);
        }
        $list = $list->order(['ro.id'=>'desc'])
                     ->select();

        $data['list'] =$this->searchOderTypeAndReferrer($list);
        $data['dataCount'] =$dataCount;


        return $data;



    }

    //租赁订单列表
    public function getRentalOderList($keywords,$page,$limit){
        $map = [];
        if ($keywords) {

            $map['nickname'] = !empty($keywords['nickname']) ?  ['like', '%'.trim($keywords['nickname']).'%']: '';
            $map['status'] =  !empty($keywords['status']) ?  $keywords['status']: '';
            $map['oid'] =!empty($keywords['oid']) ?  trim($keywords['oid']): [];
            $map['pay_at'] =!empty($keywords['pay_at']) ?  ['egt',$keywords['pay_at']]: '';
            $map['created_at'] =!empty($keywords['created_at']) ?  ['egt',$keywords['created_at']]: '';
        }
        $map=array_filter($map);
        $dataCount= $this->rentalView ()->where($map)->count('ro.id');
        $list=$this->rentalView ()->where($map);
        // 若有分页
        if ($page && $limit) {
            $list = $list->page($page, $limit);
        }
        $list = $list->order(['ro.id'=>'desc'])
            ->select();
        $data['list'] =$this->searchOderTypeAndReferrer($list);
        $data['dataCount'] =$dataCount;
        return $data;

    }

    //筛选订单类型和时间根据 订单类型，推荐人，retail_orders表id和p.orid连接  推荐人判断referrer_type，Staff，User
    public function searchOderTypeAndReferrer($data){
        foreach ($data as $key=>$val){
            if($val['order_type']=='RetailOrder'){
                $data[$key]['order_type']='零售订单';
            }elseif($val['order_type']=='RentalOrder'){
                $data[$key]['order_type']='租赁订单';
            }else{
                $data[$key]['order_type']=null;
                $data[$key]['pay_at']=null;
            }
            if($val['referrer_type']=='Staff'&& $val['referrer_id']!=''){
               $staffs= $this->cdata('staffs')->find($val['referrer_id']);
               $data[$key]['referrer_name']=$staffs['name'];
            }else if($val['referrer_type']=='User'&& $val['referrer_id']!=''){
                $staffs= $this->cdata('users')->find($val['referrer_id']);
                $data[$key]['referrer_name']=$staffs['nickname'];

            }else{
                $data[$key]['referrer_name']=null;
            }

        }
        return $data;
    }
    //库存列表
    public function getInventoriesList($keywords,$page,$limit){
        $map=[];
        if($keywords){
            $map['goods_name']=!empty($keywords['goods_name']) ? ['like', '%'.trim($keywords['goods_name']).'%']: '';
            $map['region_name']=!empty($keywords['region_name']) ? trim($keywords['region_name']): '';
        }
        $map=array_filter($map);
        $dataCount= $this->inventoriesView ()->where($map)->count('r.id');
        $list=$this->inventoriesView()->where($map);
            // 若有分页
        if ($page && $limit) {
            $list = $list->page($page, $limit);
        }
        $list = $list->order(['r.id'=>'desc'])
            ->select();
        $data['list']=$list;
        $data['dataCount']=$dataCount;
        return $data;
    }
    //有库存的区域
    public function getInventoriesRegions($type,$skuId){
        $map=[];
        if($type=='rent'){
            $map['rent'] = ['neq',0];
        }else{
            $map['sell'] = ['neq',0];
        }

        $map['sku_id'] =$skuId;
        $inventoriesRegions=$this->inventoriesView()->where($map)->select();
        $data['region']=$inventoriesRegions;
        return $data;

    }
    public function addShipments($param){
        $map['region_id']=$param['region_id'];
        $map['sku_id']=$param['sku_id'];
        $upData['id']=$param['order_id'];
        $order_type=!empty($param['order_type']) ? trim($param['order_type']): '';

        //库存更新成功后

            $data['order_type']=$order_type;
            $data['order_id']=!empty($param['order_id']) ? trim($param['order_id']): '';
            $data['name']=!empty($param['name']) ? trim($param['name']): '';
            $data['number']=!empty($param['number']) ? trim($param['number']): '';
            $data['uuid']=!empty($param['uuid']) ? trim($param['uuid']): '';
            $data['created_at']=date("Y-m-d H:i:s");
//            print_r($data);
//            die;
            $data=array_filter($data);

            //发货成功


        $this->startTrans();

        try {
            $shipments=$this->cdata('shipments')->data($data)->insert();
            if($order_type=='RentalOrder'){
                $this->cdata('inventories')->where($map)->setDec('rent');
                $upData['status']='shippend';
                $this->cdata('rental_orders')->update($upData);

            }else{
                $inventories=$this->cdata('inventories')->where($map)->setDec('sell');
            }

            //$this->cdata('inventories')->allowField(true)->save($param, ['id' => $id]);
            $this->commit();
            return true;

        } catch(\Exception $e) {
            $this->rollback();
            $this->error = '发货失败';
            return false;
        }


    }

    //零售视图
    public function retailView(){
        $table1 = ['retail_orders'=>'ro'];
        $table2 = ['users'=>'u'];
        $table3 = ['payments'=>'p'];
        $field1 = ['id','oid','receivable','created_at','status','referrer_id','referrer_type','note','address'];
        $field2 = ['name','phone','nickname'];
        $field3 = ['order_type','order_id','created_at'=>'pay_at'];
        $on2 = 'u.id=ro.user_id';
        $on3 = 'p.order_id=ro.id';
        $type = 'LEFT';
        return $this->cdata()->view($table1,$field1)
            -> view($table2,$field2,$on2,$type)
            -> view($table3,$field3,$on3,$type);
    }

    //租赁视图
    public function  rentalView(){

        $table1 = ['rental_orders'=>'ro'];
        $table2 = ['users'=>'u'];
        $table3 = ['payments'=>'p'];
        $field1 = ['id','oid','receivable','created_at','status','referrer_id','referrer_type','note','address','start_at','started_at','snapshoot'];//start_at预期收货时间，started_at用户确认收货的时间
        $field2 = ['name','phone','nickname'];
        $field3 = ['order_type','order_id','created_at'=>'pay_at'];
        $on2 = 'u.id=ro.user_id';
        $on3 = 'p.order_id=ro.id';
        $type = 'LEFT';
        return $this->cdata()->view($table1,$field1)
        -> view($table2,$field2,$on2,$type)
        -> view($table3,$field3,$on3,$type);

    }

    public function inventoriesView(){
        $table1 = ['inventories'=>'i'];
        $table2 = ['regions'=>'r'];
        $table3 = ['skus'=>'s'];
        $table4 = ['goods'=>'g'];
        $field1 = ['id','sell','rent','region_id','sku_id','created_at','updated_at'];
        $field2 = ['name'=>'region_name'];
        $field3 = ['good_id','current_price','original_price'];
        $field4 = ['name'=>'goods_name'];
        $on2 = 'r.id=i.region_id';
        $on3 = 's.id=i.sku_id';
        $on4 = 'g.id=s.good_id';
        $type = 'LEFT';
        return $this->cdata()->view($table1,$field1)
            -> view($table2,$field2,$on2,$type)
            -> view($table3,$field3,$on3,$type)
            -> view($table4,$field4,$on4,$type);
    }


    public function getOrderItems($keywords){
//        $map = [];
//        if ($keywords) {
//            $map['goodsName'] = ['like', '%'.$keywords.'%'];
//        }
//
//        $dataCount = $this->alias('goods')->where($map)->count('id');
//        return $this->cdata('goods')->where()
//
//
//        $list = $this
//            ->where($map)
//            ->alias('goods')
//            ->join('__GOODS_TYPE__ gt', 'gt.id=goods.goods_type', 'LEFT')
//            ->join('__GOODS_BRAND__ gb', 'gb.id=goods.brand_id', 'LEFT');
//
//        // 若有分页
//        if ($page && $limit) {
//            $list = $list->page($page, $limit);
//        }
//
//        $list = $list
//            ->field('goods.*,gt.name as t_name, gb.name as b_name')
//            ->select();
//
//        $data['list'] = $list;
//        $data['dataCount'] = $dataCount;
//
//        return $data;



    }

    /**
     * 通过id修改库存
     * @param  array   $param  [description]
     */
    public function updateInventoriesById($param)
    {

        if (empty($param['sell'])) {
            $this->error = '请输入销售库存';
            return false;
        }
        if (empty($param['rent'])) {
            $this->error = '请输入租赁库存';
            return false;
        }
        $this->startTrans();

        try {

            $this->cdata('inventories')->update($param);

            //$this->cdata('inventories')->allowField(true)->save($param, ['id' => $id]);
            $this->commit();
            return true;

        } catch(\Exception $e) {
            $this->rollback();
            $this->error = '编辑失败';
            return false;
        }
    }
    //通过order_id,order_type获取发货信息
    public function getShipments($param){
       $shipments= $this->cdata('shipments')->where($param)->select();
        $data['shipments']=$shipments;
        return $data;

    }



}
