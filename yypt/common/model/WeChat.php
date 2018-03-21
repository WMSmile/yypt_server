<?php
/**
 * Created by PhpStorm.
 * Description:微信数据配置模型
 * User: ChenXiaoHua
 * Mail: chenxiaohua@civilizationdata.com
 */

namespace yypt\common\model;

class WeChat extends Common
{


    //关键字列表
    public function weChatKeysList($keywords, $page, $limit){
        $map=[];
        if($keywords){
            $map['type']=!empty($keywords['type']) ? trim($keywords['type']): '';
        }
        $map=array_filter($map);
       // $dataCount= $this->dbTable('wechat_keys')->where($map)->count('id');
        $list=$this->dbTable('wechat_keys') ->where($map);
        // 若有分页
        if ($page && $limit) {
            $list = $list->page($page, $limit);
        }
        $list = $list->order(['id'=>'desc'])
            ->select();
        $dataCount=count($list);
        $data['list']=$list;
        $data['dataCount']=$dataCount;
        return $data;
    }
    //获取关键字内容
    public function getKeys($table,$field,$value){
        return $this->dbTable($table)->where($field, $value)->find();
    }
    public function createKeys($param)
    {
        if (empty($param['keys'])) {
            $this->error = '请输入关键字';
            return false;
        }
        if (empty($param['type'])) {
            $this->error = '请选择类型';
            return false;
        }
        $this->startTrans();

        try {
            db('wechat_keys')->insert($param);
            //$this->dbTable('wechat_keys')->update($param);

            //$this->cdata('inventories')->allowField(true)->save($param, ['id' => $id]);
            $this->commit();
            return true;

        } catch(\Exception $e) {
            $this->rollback();
            $this->error = '添加失败';
            return false;
        }
    }

    public function updateKeys($id,$param){
        if (empty($param['keys'])) {
            $this->error = '请输入关键字';
            return false;
        }
        if (empty($param['type'])) {
            $this->error = '请选择类型';
            return false;
        }

        $checkData = db('wechat_keys')->find($id);
        if (!$checkData) {
            $this->error = '暂无此数据';
            return false;
        }
        $this->startTrans();

        try {
            db('wechat_keys')->update($param);
            //$this->dbTable('wechat_keys')->update($param);

            //$this->cdata('inventories')->allowField(true)->save($param, ['id' => $id]);
            $this->commit();
            return true;

        } catch(\Exception $e) {
            $this->rollback();
            $this->error = '编辑失败';
            return false;
        }
    }
    //删除关键字
    public function delKeys($id){
        $this->startTrans();
        try {
            db('wechat_keys')->delete($id);
            $this->commit();
            return true;
        } catch(\Exception $e) {
            $this->error = '删除失败';
            $this->rollback();
            return false;
        }
    }
    public function statusKeys($param){
        $this->startTrans();
        try {
            db('wechat_keys')->update($param);
            $this->commit();
            return true;
        } catch(\Exception $e) {
            $this->error = '修改状态失败';
            $this->rollback();
            return false;
        }
    }

    public function weChatMenuList($keywords, $page, $limit){

        $db=db('wechat_menu')->select();
//        $list=[];
//        $i=0;
//        foreach ($db as $value=>$key){
//         if($value['pindex']==0){
//             $list[$i]=$value;
//         }else{
//             foreach ($list as $v=>$k){
//                 if($value['pindex']==$v['index']){
//                     $list[$k]['child']=$v;
//                 }
//             }
//         }
//        }
        $data['list']=$db;

        return $data;
    }
    public function createMenu($param){
        $this->startTrans();

        try {
            db('wechat_menu')->where('1=1')->delete();
            db('wechat_menu')->insertAll($param);
            //$this->dbTable('wechat_keys')->update($param);

            //$this->cdata('inventories')->allowField(true)->save($param, ['id' => $id]);
            $this->commit();
            return true;

        } catch(\Exception $e) {
            $this->rollback();
            $this->error = '添加失败';
            return false;
        }
    }
    public function updateMenu(){

    }

    //删除关键字
    public function delMenu($id){
        $this->startTrans();
        try {
            db('wechat_keys')->delete($id);
            $this->commit();
            return true;
        } catch(\Exception $e) {
            $this->error = '删除失败';
            $this->rollback();
            return false;
        }
    }


    //粉丝列表
    public function weChatFansList($keywords, $page, $limit){
        $map=[];
        if($keywords){
            $map['type']=!empty($keywords['type']) ? trim($keywords['type']): '';
        }
        $map=array_filter($map);
        $list=$this->dbTable('wechat_fans') ->where($map);
        // 若有分页
        if ($page && $limit) {
            $list = $list->page($page, $limit);
        }
        $list = $list->order(['id'=>'desc'])
            ->select();
        $dataCount=count($list);
        $data['list']=$list;
        $data['dataCount']=$dataCount;
        return $data;
    }

    //同步粉丝前删除粉丝
    public function delFans(){
       db('wechat_fans')->where('1=1')->delete();
        return true;
    }
    public function setBack($openIds){
        $this->dbTable('wechat_fans')->where('openid', 'in', $openIds)->setField('is_back', '1');
        return true;
    }

        //获取公众号接入信息
    public function  getWeChat($cid){

        $Mp=$this->dbTable('wechat')->where(['id'=>$cid])->find();
        $data['url']= getHostDomain().url('mp/Entr/index',['mid'=>$cid]);
        $data['valid_token']=$Mp['valid_token'];
        $data['encodingaeskey']=$Mp['encodingaeskey'];
        return $data;
    }
    //获取公众号列表
    public function getWeChatList($uid){
        $list=$this->dbTable('wechat')
           // ->where(['user_id'=>$uid])
            ->select();
        foreach ($list as $key=>$v){
           $list[$key]['url']=getHostDomain().'/wechat/service/?mid='.$v['id'];
        }
        $data['list']=$list;
        return $data;
    }
    public function getWeChatData($id){
        $list=$this->dbTable('wechat')->find($id);
        return $list;
    }
    /**
     * 添加公众号
     * @param  array   $param  [description]
     */
    public function createWeChat($param)
    {
        $this->startTrans();
        try {
            $param['create_time']=time();
            $param['valid_token']=getRandChar('32');
            $param['token']=getRandChar('32');
            $param['encodingaeskey']=getRandChar('43');
            $this->dbTable('wechat')->insert($param);
            $this->commit();
            return true;
        } catch(\Exception $e) {
            $this->rollback();
            $this->error = '添加失败';
            return false;
        }
    }
    public function updateWeChat($param){
        $this->startTrans();
        try {
//            $param['create_time']=time();
//            print_r($param);
//            die;
            $this->dbTable('wechat')->update($param);
            $this->commit();
            return true;
        } catch(\Exception $e) {
            $this->rollback();
            $this->error = '添加失败';
            return false;
        }
    }
    public function delWeChat($id){
        $this->startTrans();
        try {
            $this->dbTable('wechat')->delete($id);
            $this->commit();
            return true;
        } catch(\Exception $e) {
            $this->error = '删除失败';
            $this->rollback();
            return false;
        }
    }

    public function getTableData($table,$map)
    {
        return $this->dbTable($table)->where($map)->find();
        //return Db::name($table)->where($map)->find();
    }

    /**
     * 数据增量保存
     * @param \think\db\Query|string $dbQuery 数据查询对象
     * @param array $data 需要保存或更新的数据
     * @param string $key 条件主键限制
     * @param array $where 其它的where条件
     * @return bool
     */
    public function dbSave($dbQuery, $data, $key = 'id', $where = [])
    {
       $db = is_string($dbQuery) ? db($dbQuery) : $dbQuery;
        $where[$key] = isset($data[$key]) ? $data[$key] : '';
        if ($db->where($where)->count() > 0) {
            return $db->where($where)->update($data) !== false;
        }

        return $db->insert($data) !== false;
    }


}