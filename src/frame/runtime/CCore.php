<?php
/**
 * 核心类
 * 保存单例加载的实例
 * User: lixin
 * Date: 18-2-3
 */

namespace frame\runtime;


use frame\ChelerApi;
use frame\dao\nosql\CNosqlInit;
use frame\dao\nosql\driver\CMongoInit;

class CCore
{
    /**
     * 单例容器
     * @var array
     */
    public static $instance = [];

    /**
     * CCore constructor.
     */
    public function __construct()
    {
        $this->run_register_global(); //注册全局变量
    }


    /**
     * 框架核心加载-框架的所有类都需要通过该函数出去
     * 1. 单例模式
     * 2. 可以加载-Controller，Service，View，Dao，Util，Library中的类文件
     * 3. 框架加载核心函数
     * 使用方法：$this->load($className)
     *
     * @param string $className 类名称(带命名空间的  )
     * @return mixed 加载的对象
     */
    protected static function load(string $className)
    {
        if (!isset(self::$instance['loadclass'][$className])) {
            if (!class_exists($className)) {
                ChelerApi::throwError($className . ' is not exist!');
            }

            $obj = new $className;
            self::$instance['loadclass'][$className] = $obj;
            return $obj;
        }
        return self::$instance['loadclass'][$className];
    }

    /**
     *	系统获取library下面的类
     *  1. 通过$this->getLibrary($class) 就可以加载Library下面的类
     *  2. 单例模式-通过load核心函数加载
     *  全局使用方法：$this->getLibrary($class)
     *  @param  string  $class  类名称
     *  @return object
     */
    public static function getLibrary($class) {
        $fullClassName = '\frame\library\\L'.$class;
        return self::load($fullClassName);
    }

    /**
     *	系统获取Util类函数
     *  1. 通过$this->getUtil($class) 就可以加载Util下面的类
     *  2. 单例模式-通过load核心函数加载
     *  全局使用方法：$this->getUtil($class)
     *  @param  string  $class  类名称
     */
    public static function getUtil($class) {
        $fullClassName = '\frame\util\\U'.$class;
        return self::load($fullClassName);
    }

    /**
     * 获取缓存对象
     * 全局使用方法：$this->getMemcache()
     *
     * @param  string $serverName
     * @return mixed memcache对象
     * @author lixin
     */
    public function getMemcache(string $serverName)
    {
        if (!isset(self::$instance['_cache_'][$serverName])) {
            $cache = $this->load('\frame\cache\CMemcached');
            $config = ChelerApi::getConfig('memcache');
            $cache->add_server($config[$serverName]);
            self::$instance['_cache_'][$serverName] = $cache;
        }
        return self::$instance['_cache_'][$serverName];
    }

    /**
     * 获取缓存对象
     * 全局使用方法：$this->getRedis()
     *
     * @param  string $serverName
     * @return mixed redis对象
     * @author lixin
     */
    public function getRedis(string $serverName)
    {
        if (!isset(self::$instance['_redis_'][$serverName])) {
            $redis = $this->load('\frame\cache\CRedis');
            $config = ChelerApi::getConfig('redis');
            $redis->add_server($config[$serverName]);
            self::$instance['redis'][$serverName] = $redis;
        }
        return self::$instance['redis'][$serverName];
    }

    /**
     * 获取CNosqlInit对象
     * @param string $type redis|mongo
     * @param array $server 配置
     * @return CNosqlInit
     * @author lixin
     */
    public function getNosql($type, $server)
    {
        if (isset(self::$instance['_nosql_']) && self::$instance['_nosql_'] != NULL) {
            return self::$instance['_nosql_'];
        } else {
            $dao = $this->load('\frame\dao\CDao'); //导入Dao
            self::$instance['_nosql_'] = $dao->run_nosql($type, $server); //初始化nosql
            return self::$instance['_nosql_'];
        }
    }

    /**
     * 获取NOSQL对象中的Mongo
     * 全局使用方法：$this->getMongo()->
     * 使用Mongo，你的服务器端需要安装Mongo
     * 需要在配置文件中配置$InitPHP_conf['mongo'][服务器server]
     * 如果多个mongo分布，则直接可以改变$server就可以切换
     * @param string $server 配置
     * @return CMongoInit
     * @author lixin
     */
    public function getMongo($server = 'default') {
        $instance_name = 'mongo_' . $server;

        if (isset(CNosqlInit::$instance[$instance_name]) && CNosqlInit::$instance[$instance_name] != NULL) {
            return CNosqlInit::$instance[$instance_name];
        } else {
            return $this->getNosql('MONGO',$server)->init('MONGO', $server);
        }
    }

    /**
     * 注册到框架全局可用变量
     * @param string $name 变量名称
     * @param val $value 变量值
     */
    public function register_global($name, $value)
    {
        self::$instance['global'][$name] = $value;
        $this->$name = $value;
    }

    /**
     * 运行全局变量
     */
    private function run_register_global()
    {
        if (isset(self::$instance['global']) && !empty(self::$instance['global'])) {
            foreach (self::$instance['global'] as $key => $value) {
                $this->$key = $value;
            }
        }
    }
}