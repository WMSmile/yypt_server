<?php
/**
 * Created by PhpStorm.
 * Description:库存
 * User: ChenXiaoHua
 * Mail: chenxiaohua@civilizationdata.com
 */

namespace yypt\wechat\controller;
use yypt\common\controller\ApiCommon;

class Inventories extends ApiCommon
{
    public function index(){
        $goods=model('Cdata');
        $param = $this->param;
        $keywords = !empty($param['keywords']) ?json_decode($param['keywords'],true): '';
        $page = !empty($param['page']) ? $param['page']: '';
        $limit = !empty($param['limit']) ? $param['limit']: '';
        $data=$goods->getInventoriesList($keywords, $page, $limit);
        return resultArray(['data'=>$data]);
    }
    public function add(){}
    public function update(){
        $goodsModel = model('Cdata');
        $param = $this->param;
        $data = $goodsModel->updateInventoriesById($param);
        if (!$data) {
            return resultArray(['error' => $goodsModel->getError()]);
        }
        return resultArray(['data' => '编辑成功']);
    }
    //库存地区
    public function regions(){
        $goods=model('Cdata');
        $param = $this->param;
        $type = !empty($param['type']) ? $param['type']: '';
        $skuId= !empty($param['sku_id']) ? $param['sku_id']: '';
        $data=$goods->getInventoriesRegions($type,$skuId);
        return resultArray(['data'=>$data]);


    }
    //所有地区
    public function regionsAll(){

        $regions=model('Regions');
        $param=$this->param;
        $data=$regions->getRegionsJson($param);
        return resultArray(['data'=>$data]);


    }
    //发货
    public function shipments(){
        $goods=model('Cdata');
        $param=$this->param;
        $data=$goods->addShipments($param);
        if (!$data) {
            return resultArray(['error' => $goods->getError()]);
        }
        return resultArray(['data' => '发货成功']);
    }
    public function getShipments(){
        $goods=model('Cdata');
        $param=$this->param;
        $data=$goods->getShipments($param['id']);
        return resultArray(['data'=>$data]);
    }

}