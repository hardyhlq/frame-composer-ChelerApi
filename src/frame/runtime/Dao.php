<?php
/**
 * Dao.php
 * 数据库
 * Date: 18-2-5
 */

namespace frame\runtime;

class Dao extends CCore
{
    /**
     * 数据库类
     * @var mixed
     */
    protected $dao;

    /**
     * 数据库链接
     * @var mixed
     */
    protected $db;

    /**
     * 表名
     * @var string
     */
    protected $table_name;

    /**
     * 初始化
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->dao = $this->load('\frame\dao\CDao'); //导入Dao
        $this->dao->run_db();
    }

    /**
     * 分库初始化DB
     * 如果有多数据库链接的情况下，会调用该函数来自动切换DB
     * @param string $db
     * @return \frame\dao\db\DB
     */
    public function init_db($db = 'default')
    {
        $this->dao->db->db($db);
        return $this->dao->db;
    }
    
    /**
     * 获取缓存KEY
     * @param string $key
     * @return string
     * @author lixin
     */
    protected function _cache_key($key)
    {
        return $this->table_name . '_' . $key;
    }

    /**
     * 开始事务操作
     * DAO中使用方法：$this->dao->db->transaction_start()
     * @author lxm update 2013-12-13
     */
    public function transaction_start()
    {
        $this->init_db($this->db)->transaction_start();
    }

    /**
     * 提交事务
     * DAO中使用方法：$this->dao->db->transaction_commit()
     * @author lxm update 2013-12-13
     */
    public function transaction_commit()
    {
        $this->init_db($this->db)->transaction_commit();
    }

    /**
     * 回滚事务
     * DAO中使用方法：$this->dao->db->transaction_rollback()
     * @author lxm update 2013-12-13
     */
    public function transaction_rollback()
    {
        $this->init_db($this->db)->transaction_rollback();
    }
}