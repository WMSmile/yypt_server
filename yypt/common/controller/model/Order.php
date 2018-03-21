<?php
/**
 * Created by PhpStorm.
 * Description:
 * User: ChenXiaoHua
 * Mail: chenxiaohua@civilizationdata.com
 */

namespace yypt\common\model;


class Order extends Common
{
    /**
     * 为了数据库的整洁，同时又不影响Model和Controller的名称
     * 我们约定每个模块的数据表都加上相同的前缀，比如微信模块用weixin作为数据表前缀
     */
    protected $name = 'order';

    public function getRetailOderList($keywords, $page, $limit){
        $map = [];
        if ($keywords) {
            $map['order_code'] = ['like', '%'.$keywords.'%'];
        }

        $dataCount = $this->where($map)->count('id');

        $list = $this
            ->where($map)
            ->alias('o')
            ->join('member m', 'm.uid=o.user_id', 'LEFT')
            ->join('order_goods og', 'og.goods_id=o.id', 'LEFT');

        // 若有分页
        if ($page && $limit) {
            $list = $list->page($page, $limit);
        }

        $list = $list
            ->field('o.id,o.order_code,og.goods_name,o.create_time,o.pay_status,m.nickname')
            ->select();

        $data['list'] = $list;
        $data['dataCount'] = $dataCount;

        return $data;
    }


}