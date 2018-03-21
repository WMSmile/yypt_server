<?php
/**
 * Created by PhpStorm.
 * Description:
 * User: ChenXiaoHua
 * Mail: chenxiaohua@civilizationdata.com
 */

namespace yypt\common\model;


class SystemLog extends Common
{
    public function add($data){

        $this->startTrans();
        try {
//            $param['create_time']=time();
//            $param['valid_token']=getRandChar('35');
//            $param['token']=getRandChar('35');
//            $param['encodingaeskey']=getRandChar('43');
            $this->insert($data);
            $this->commit();
            return true;
        } catch(\Exception $e) {
            $this->rollback();
            $this->error = '添加失败';
            return false;
        }
    }
}