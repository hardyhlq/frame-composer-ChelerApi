<?php
/**
 * MongoDemo.php
 * 描述
 * User: lixin
 * Date: 18-2-7
 */

namespace app\dao\clw2;


use frame\runtime\MongoDao;

class MongoDemoDao extends MongoDao
{
    protected $col = "admin_log";
    protected $mongoDb = "tinyapi";
    protected $serverName = "log";//$_CONFIG_['mongo']['log']['server']     = '127.0.0.1';配置中的log

    public function insert() {
        $field = [
            'param' => ['a','b','c'],
            'startTime' => time(),
        ];

        $this->init_mongo($this->mongoDb, $this->col)->bulkWriteInster($field);
    }
}