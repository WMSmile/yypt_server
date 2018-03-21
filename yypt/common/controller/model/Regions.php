<?php
/**
 * Created by PhpStorm.
 * Description:
 * User: ChenXiaoHua
 * Mail: chenxiaohua@civilizationdata.com
 */

namespace yypt\common\model;


class Regions extends Common
{
    public  function getRegionsJson($param)
    {
        $pid=!empty($param['pid']) ? $param['pid']: 0;

            $province= $this->field('id as value,name as label,parent_id as pid,type')->where('parent_id',$pid)->order('id')->select();
            foreach ($province as $key=>$v){
                if($v['type']!='County'){

                    $city=$this->getProvince($v['value']);
                    foreach ($city as $ck=>$c){
                        $county=$this->getProvince($c['value']);
                        $city[$ck]['children']=$county;
                    }
                    $province[$key]['children']=$city;
                }

            }
            $data['regions']=$province;
        return $data;
    }

    public function getProvince($pid){
      return   $this->field('id as value,name as label,parent_id as pid,type')->where('parent_id',$pid)->order('id')->select();
    }


    public static function recursion($data, $id=0) {
        $list = array();

        foreach($data as $key=> $v) {

            if($v['parent_id'] == $id) {
                $list[$key]['value']=$v['id'];
                $list[$key]['label']=$v['name'];
                $city=array();
               foreach ($data as $ck=> $cy) {
                   if($cy['parent_id']==$v['id']){
                       $city[$ck]['value']=$cy['id'];
                       $city[$ck]['label']=$cy['name'];
                       $county=array();
                       foreach ($data as $yk=> $ct){
                           if($ct['parent_id']==$cy['id']){
                               $county[$yk]['value']=$ct['id'];
                               $county[$yk]['label']=$ct['name'];
                           }
                       }
                       $city['children']=$county;
                   }


               }
                $list[$key]['children']=$city;

            }
        }
        return $list;
    }


}