<?php
namespace frame;

use frame\runtime\CClassLoader;
use frame\runtime\CCore;
use frame\runtime\CException;
use frame\runtime\CRequest;

class ChelerApi extends CCore
{
    /**
     * 时间
     * @var int
     */
    public static $time = 0;

    /**
     * 框架启动函数
     */
    public static function run()
    {
        self::setupLoader();

        // 路由分发
        $router = ChelerApi::loadClass('frame\runtime\CRouter');
        $router->router();
        // 路由执行
        $routerExec = ChelerApi::loadClass('frame\runtime\CRouterExec');
        $routerExec->exec();
    }


    /**
     * 框架实例化php类函数，单例模式
     * 全局使用方法：ChelerApi::loadclass($className)
     * @param string $className
     * @return object
     */
    public static function loadClass($className)
    {
        return parent::load($className);
    }

    /**
     * XSS过滤，输出内容过滤
     * 全局使用方法：ChelerApi::output($string, $type = 'encode');
     * @param string $string 需要过滤的字符串
     * @param string $type encode HTML处理 | decode 反处理
     * @return string
     */
    public static function output(string $string, string $type = 'encode')
    {
        $html = array("&", '"', "'", "<", ">", "%3C", "%3E");
        $html_code = array("&amp;", "&quot;", "&#039;", "&lt;", "&gt;", "&lt;", "&gt;");
        if ($type == 'encode') {
            if (function_exists('htmlspecialchars')) return htmlspecialchars($string);
            $str = str_replace($html, $html_code, $string);
        } else {
            if (function_exists('htmlspecialchars_decode')) return htmlspecialchars_decode($string);
            $str = str_replace($html_code, $html, $string);
        }
        return $str;
    }


    /**
     * 获取时间戳
     * 1. 静态时间戳函数
     * 全局使用方法：ChelerApi::getTime();
     * @return int
     */
    public static function getTime() : int
    {
        if (self::$time > 0) {
            return self::$time;
        }
        self::$time = time();
        return self::$time;
    }

    /**
     * 获取全局配置文件
     * 全局使用方法：ChelerApi::getConfig('controller.path')
     * @param string $path 获取的配置路径 多级用点号分隔
     * @return mixed
     */
    public static function getConfig(string $path = '')
    {
        global $_CONFIG_;
        if (empty($path)) {
            return $_CONFIG_;
        }
        $tmp = $_CONFIG_;
        $paths = explode('.', $path);
        foreach ($paths as $item) {
            $tmp = $tmp[$item];
        }
        return $tmp;
    }
    
    /**
     * 设置配置文件，框架意外慎用！
     * @param $key
     * @param $value
     * @return bool|string
     */
    public static function setConfig($key, $value)
    {
        global $_CONFIG_;
        $_CONFIG_[$key] = $value;
        return $_CONFIG_;
    }

    /**
     * 获取项目路径
     * 全局使用方法：ChelerApi::getAppPath('controller.path')
     * @param $path
     * @return String
     */
    public static function getAppPath(string $path = '')
    {
        $path = rtrim($path, '/');
        if (!defined('APP_PATH')) return $path;
        return rtrim(APP_PATH, '/') . '/' . $path;
    }

    /**
     * 【静态】基础服务层
     * 全局使用方法：ChelerApi::getService($serviceName)
     * @param string $serviceName
     * @return mixed
     */
    public static function getService(string $serviceName)
    {
        static $objs = [];
        $className = '\\app\\service\\' . $serviceName . 'Service';
        $hash = md5($className);
        if (!isset($objs[$hash])) {
            $objs[$hash] = new $className;
        }

        return $objs[$hash];
    }

    /**
     * 【静态】基础服务层
     * 全局使用方法：ChelerApi::getDao($daoName)
     * @param string $daoName
     * @return mixed
     */
    public static function getDao(string $daoName)
    {
        static $objs = [];
        $className = '\\app\\dao\\' . $daoName . 'Dao';
        $hash = md5($className);
        if (!isset($objs[$hash])) {
            $objs[$hash] = new $className;
        }

        return $objs[$hash];
    }

    /**
     * 【静态】基础服务层
     * 全局使用方法：ChelerApi::getMongoDao($daoName)
     * @param string $daoName
     * @return mixed
     */
    public static function getMongoDao(string $daoName)
    {
        static $objs = [];
        $className = '\\app\\dao\\' . $daoName . 'Dao';
        $hash = md5($className);
        if (!isset($objs[$hash])) {
            $objs[$hash] = new $className;
        }

        return $objs[$hash];
    }

    /**
     * 框架错误机制
     * @param string $msg
     * @param int $code
     * @throws CException
     * @author lixin
     */
    public static function throwError(string $msg, $code = 10000)
    {
        throw new CException($msg, $code);
    }

    /**
     * 返回404错误页面
     */
    public static function return404()
    {
        header('HTTP/1.1 404 Not Found');
        header("status: 404 Not Found");
        exit;
    }

    /**
     * 返回405错误页面
     */
    public static function return405()
    {
        header('HTTP/1.1 405 Method not allowed');
        header("status: 405 Method not allowed");
        exit;
    }

    /**
     * 返回500错误页面
     */
    public static function return500()
    {
        header('HTTP/1.1 500 Internal Server Error');
        header("status: 500 Internal Server Error");
        exit;
    }

    /**
     * 安装类加载器
     */
    private static function setupLoader()
    {
        CClassLoader::register();
    }
}