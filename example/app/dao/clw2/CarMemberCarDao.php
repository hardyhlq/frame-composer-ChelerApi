<?php

/**
 * CarMemberCarDao.php
 * 描述
 * User: lixin
 * Date: 18-2-5
 */
namespace app\dao\clw2;

use frame\runtime\Dao;

class CarMemberCarDao extends Dao
{
    protected $table_name = 'car_member_car';
    protected $db = 'clw2';//库名

    /**
     * 根据搜索条件获取数据
     * @param array $field
     * @return array
     * @author lixin
     */
    public function getInfoByField(array $field) : array
    {
//        $page = [
//            'offset' => 10,
//            'num' => 10,
//        ];
//        $field = [];
//        $glue = [];
//        $id_key = "id";
//        $sort = "DESC";
//        return $this->init_db($this->db)->get_list($page, $field, $glue, $id_key, $sort, $this->table_name);

        $this->getRedis('demo')->set("a","c");
        $this->getMemcache('demo')->set("a","22222");
        echo "<hr/>";
        echo $this->getMemcache('demo')->get("a");
        echo "<hr/>";
        return $this->init_db($this->db)->get_all_by_field($field, $this->table_name);
    }
}