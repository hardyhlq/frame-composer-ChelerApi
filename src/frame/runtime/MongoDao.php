<?php
/**
 * BaseMongoDb.php
 * 描述
 * User: lixin
 * Date: 18-2-7
 */

namespace frame\runtime;


use frame\dao\CDao;
use frame\dao\nosql\driver\CMongoInit;

class MongoDao extends CCore
{
    /**
     * 配置中的服务名字
     * @var string
     */
    protected $serverName;

    /**
     * 数据库链接
     * @var CDao
     */
    protected $mongoClass;

    /**
     * 数据库对象
     * @var CMongoInit
     */
    public $mongoHandle;

    /**
     * 表名
     * @var string
     */
    protected $col;

    /**
     * 初始化
     * 
     */
    public function __construct()
    {
        parent::__construct();

        $this->mongoClass = $this->load('\frame\dao\CDao'); //导入Dao
        $this->mongoHandle = $this->mongoClass->run_mongo("MONGO", $this->serverName);
    }

    /**
     * 分库初始化DB
     * 如果有多数据库链接的情况下，会调用该函数来自动切换DB
     * @param string $db
     * @return \frame\dao\nosql\driver\CMongoInit
     */
    public function init_mongo($db = 'default', $col)
    {
        return $this->mongoClass->mongoHandle->switchDb($db)->collection($col);
    }
}