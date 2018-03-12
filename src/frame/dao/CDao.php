<?php
namespace frame\dao;

use frame\dao\db\DB;
use frame\ChelerApi;
use frame\dao\nosql\CNosqlInit;

/**
 * 数据层基类
 * @author lonphy
 */
class CDao
{
    /**
     * @var DB
     */
    public $db = NULL;

    /**
     * @var CNosqlInit
     */
    public $nosql = NULL;

    /**
     * @var CNosqlInit
     */
    public $mongoHandle = NULL;

    /**
     * 运行数据库
     * 1. 初始化DB类  DAO中调用方法    $this->dao->db
     * @return DB
     */
    public function run_db()
    {

        if ($this->db === NULL) {
            $this->db = ChelerApi::loadClass('frame\dao\db\DB');
            $this->db->db('default');

        }
        return $this->db;
    }

    /**
     * 运行nosql
     */
    public function run_nosql($type, $server)
    {
        if ($this->nosql == NULL) {
            $this->nosql = ChelerApi::loadclass('frame\dao\nosql\CNosqlInit');
            $this->nosql->init($type, $server);
        }
        return $this->nosql;
    }

    /**
     * 运行nosql
     */
    public function run_mongo($type, $server)
    {
        if ($this->mongoHandle == NULL) {
            $mongoClass = ChelerApi::loadclass('frame\dao\nosql\CNosqlInit');
            $this->mongoHandle = $mongoClass->init($type, $server);
        }
        return $this->mongoHandle;
    }
}