<?php

namespace frame\runtime;


/**
 * 框架路由
 *
 * @author lonphy(dev@lonphy.com)
 * @version 1.0
 */
class CRouter
{
    /**
     * 路由分发-路由分发核心函数
     * @return bool
     */
    public function router() : bool
    {
        $request = $this->getRequest();
        $this->parseRewriteUri($request);
        return true;
    }

    /**
     * 路由分发，获取Uri数据参数
     * 1. 对Service变量中的uri进行过滤
     * 2. 配合全局站点url处理request
     * @return array|mixed
     */
    private function getRequest()
    {
        $filter_param = ['<', '>', '"', "'", '%3C', '%3E', '%22', '%27', '%3c', '%3e'];
        $uri = str_replace($filter_param, '', $_SERVER['REQUEST_URI']);
        $posi = strpos($uri, '?');

        if ($posi) {
            $uri = substr($uri, 0, $posi);
        }

        if (strpos($uri, '.php')) {
            $uri = explode('.php', $uri);
            $uri = $uri[1];
        }
        return $uri;
    }

    /**
     * 解析rewrite方式的路由
     * 1. 解析index.php/user/new/username/?id=100
     * 2. 解析成数组，array()
     * @param $request
     * @return array|bool|string
     */
    private function parseRewriteUri($request)
    {
        if (!$request)
            return false;
        $request = trim($request, '/');
        if ($request == '')
            return false;
        $request = explode('/', $request);
        if (!is_array($request) || count($request) !== 4)
            return false;
        if (isset($request[0]))
            $_GET['_v'] = $request[0]; // 版本
        if (isset($request[1]))
            $_GET['m'] = $request[1];
        if (isset($request[2]))
            $_GET['c'] = $request[2];
        if (isset($request[3]))
            $_GET['a'] = $request[3];
        return $request;
    }

}
