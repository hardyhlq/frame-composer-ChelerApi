<?php
namespace frame\cache;

class CMemcached
{

    private $memcache;
    private $page_cache_key, $page_cache_time, $page_cache_type;

    /**
     * Memcache缓存-页面缓存开始标记
     * 1. 设置缓存的key值，value值,缓存时间和缓存类型
     * 2. 缓存时间设置为0，则是永久缓存
     * @param  string $key 缓存键值
     * @param  string $time 缓存时间
     * @return
     */
    public function page_cache_start($key, $time = 0)
    {
        $this->page_cache_key = '_page_cache_' . $key;
        $this->page_cache_time = $time;
        $page = $this->get($this->page_cache_key);
        if (!$page) {
            ob_start();
        } else {
            echo $page;
            exit;
        }
    }

    /**
     * Memcache缓存-页面缓存结束标记
     * @return
     */
    public function page_cache_end()
    {
        $this->set($this->page_cache_key, ob_get_contents(), $this->page_cache_time);
        $page = $this->get($this->page_cache_key);
        ob_end_clean(); //清空缓冲
        echo $page;
    }

    /**
     * 设置缓存内容
     * @param  string $itemKey 元素的key
     * @param  mixed $itemValue 元素数据
     * @param  integer $expireTime 过期时间 <=0表示永远有效，单位秒
     * @return true
     */
    public function set($itemKey, $itemValue, $expireTime = 0)
    {
        //过期时间，0-2592000秒（30天），由于超过按照时间戳处理，这里处理为永久有效
        //参见 http://cn2.php.net/manual/zh/memcached.expiration.php
        $expireTime = intval($expireTime);
        if ($expireTime < 0 || $expireTime > 2592000) {
            $expireTime = 0;
        }

        return $this->memcache->set($itemKey, $itemValue, $expireTime);
    }

    /**
     * 获取指定元素key的内容
     * @param  string /array $keyList 元素的key
     * @return mixed
     */
    public function get($keyList)
    {
        if (is_string($keyList)) {
            return $this->memcache->get($keyList);
        } else if (is_array($keyList)) {
            return $this->memcache->getMulti($keyList);
        } else {
            return false;
        }
    }

    /**
     * 删除指定元素key的内容
     * @param  string $itemKey 元素的key
     * @return true
     */
    public function clear($key)
    {
        return $this->memcache->delete($key);
    }

    /**
     * Memcache缓存-清空所有缓存
     * 不建议使用该功能
     * @return
     */
    public function clearAll()
    {
        return $this->memcache->flush();
    }

    /**
     * 字段自增-用于记数
     * @param string $key KEY值
     * @param int $step 新增的step值
     */
    public function increment($key, $step = 1)
    {
        return $this->memcache->increment($key, (int)$step);
    }

    /**
     * 字段自减-用于记数
     * @param string $key KEY值
     * @param int $step 新增的step值
     */
    public function decrement($key, $step = 1)
    {
        return $this->memcache->decrement($key, (int)$step);
    }

    /**
     * 关闭Memcache链接
     */
    public function close()
    {
        return $this->memcache->close();
    }

    /**
     * 替换数据
     * @param string $key 期望被替换的数据
     * @param string $value 替换后的值
     * @param int $time 时间值
     */
    public function replace($key, $value, $time = 0)
    {
        return $this->memcache->replace($key, $value, $time);
    }

    /**
     * 获取Memcache的版本号
     */
    public function getVersion()
    {
        return $this->memcache->getVersion();
    }

    /**
     * 获取Memcache的状态数据
     */
    public function getStats()
    {
        return $this->memcache->getStats();
    }

    /**
     * Memcache缓存-设置链接服务器
     * 支持多MEMCACHE服务器
     * 配置文件中配置Memcache缓存服务器：
     * @param  string $servers 配置的server名字
     */
    public function add_server($servers)
    {
        $this->memcache = new \Memcached();

        if (!is_array($servers) || empty($servers)) exit('memcache server is null!');
        $this->memcache->addServer($servers['server'], $servers['port']);
    }
}
