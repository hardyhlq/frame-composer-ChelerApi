<?php
namespace frame\dao\nosql;

use frame\ChelerApi;
use frame\dao\nosql\driver\CMongoInit;
use frame\runtime\CException;

class CNosqlInit
{
    public static $instance = array();  //单例模式获取nosql类
    private $nosql_type = array('MONGO');

    /**
     * 获取Nosql对象
     * @param string $type
     * @param string $server
     * @return CMongoInit|mixed
     * @throws CException
     * @author lixin
     */
    public function init($type = 'MONGO', $server = 'default')
    {
        $InitPHP_conf = ChelerApi::getConfig(); //需要设置文件缓存目录
        $type = strtoupper($type);
        $type = (in_array($type, $this->nosql_type)) ? $type : 'MONGO';
        switch ($type) {
            case 'MONGO' :
                $instance_name = 'mongo_' . $server;
                if (isset(CNosqlInit::$instance[$instance_name])) {
                    return CNosqlInit::$instance[$instance_name];
                }
                $mongo = new CMongoInit();
                $mongo->init($InitPHP_conf['mongo'][$server]);
                CNosqlInit::$instance[$instance_name] = $mongo;
                return $mongo;
                break;
            default:
                throw new CException("unsupported nosql", 500);
        }
    }

}