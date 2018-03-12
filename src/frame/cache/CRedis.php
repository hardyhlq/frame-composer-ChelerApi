<?php
namespace frame\cache;

class CRedis
{

    private $redis; //redis对象

    /**
     * 初始化Redis
     * $_CONFIG_['redis'][0]   = array('127.0.0.1', '6379','123456');
     * @author magus.lee
     */
    public function add_server($servers)
    {
        $this->redis = new \Redis();
        if (!is_array($servers) || empty($servers)) exit('redis server is null!');

        $this->redis->connect($servers['host'], $servers['port']);
        if (isset($servers['password']) && !empty($servers['password'])) {
            $this->redis->auth($servers['password']);
        }
        if (isset($servers['db'])) {
            $this->redis->select($servers['db']);
        }
    }

    /**
     * 设置值
     * @param string $key KEY名称
     * @param string|array $value 获取得到的数据
     * @param int $timeOut 时间
     */
    public function set($key, $value, $timeOut = 0)
    {
        $retRes = $this->redis->set($key, $value);
        if ($timeOut > 0) $this->redis->setTimeout($key, $timeOut);
        return $retRes;
    }

    /**
     * 通过KEY获取数据
     * @param string $key KEY名称
     */
    public function get($key)
    {
        $result = $this->redis->get($key);
        return $result;
    }

    /**
     * 删除一条数据
     * @param string $key KEY名称
     */
    public function delete($key)
    {
        return $this->redis->delete($key);
    }

    /**
     * 清空数据
     */
    public function flushAll()
    {
        return $this->redis->flushAll();
    }

    /**
     * 数据入队列
     * @param string $key KEY名称
     * @param string|array $value 获取得到的数据
     * @param bool $right 是否从右边开始入
     */
    public function push($key, $value, $right = true)
    {
        return $right ? $this->redis->rPush($key, $value) : $this->redis->lPush($key, $value);
    }

    /**
     * 数据出队列
     * @param string $key KEY名称
     * @param bool $left 是否从左边开始出数据
     */
    public function pop($key, $left = true)
    {
        $val = $left ? $this->redis->lPop($key) : $this->redis->rPop($key);
        return true;
    }

    /**
     * 数据自增
     * @param string $key KEY名称
     */
    public function increment($key)
    {
        return $this->redis->incr($key);
    }

    /**
     * 数据自减
     * @param string $key KEY名称
     */
    public function decrement($key)
    {
        return $this->redis->decr($key);
    }

    /**
     * key是否存在，存在返回ture
     * @param string $key KEY名称
     */
    public function exists($key)
    {
        return $this->redis->exists($key);
    }

    /**
     * 返回redis对象
     * 直接调用redis自身方法
     */
    public function redis()
    {
        return $this->redis;
    }
}