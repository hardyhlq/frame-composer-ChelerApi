<?php
namespace frame\runtime;

use frame\ChelerApi;

/**
 * 路由执行器
 *
 * @author lonphy(dev@lonphy.com)
 * @version 1.0
 */
class CRouterExec
{
    private $controller_postfix = 'Controller'; //控制器后缀
    private $action_postfix = ''; //动作后缀
    private $default_controller = 'Index'; //默认执行的控制器名称
    private $default_action = 'index'; //默认执行动作名称
    private $default_version = 'v1'; //默认版本
    private $default_module = 'index';
    private $default_before_action = 'before';//默认的前置Action
    private $default_after_action = 'after'; //默认的后置Action


    /**
     * 框架运行核心函数
     * 1. 设置参数
     * 2. 获取controller
     * 3. 运行前置Action
     * 4. 运行正常Action
     * 5. 运行后置Action
     */
    public function exec()
    {
        $this->filterVMCA();
        //验证方法是否合法，如果请求参数不正确，则直接返回404
        $controllerObj = $this->checkRequest();
        $this->run_before_action($controllerObj);//前置Action
        $this->run_action($controllerObj); //正常流程Action
        $this->run_after_action($controllerObj); //后置Action
    }

    /**
     * 验证请求是否合法
     * 1. 如果请求参数m,c,a都为空，则走默认的
     */
    private function checkRequest()
    {
        $controller = isset($_GET['c']) ? trim($_GET['c']) : '';
        $action = isset($_GET['a']) ? trim($_GET['a']) : '';
        $client_version = isset($_GET['_v']) ? trim($_GET['_v']) : '';
        $module = isset($_GET['m']) ? trim($_GET['m']) : '';

        if ($controller == '' && $action == '' && $client_version == '' && $module == '') {
            $controller = $_GET['c'] = $this->default_controller;
            $module = $_GET['m'] = $this->default_module;
            $_GET['a'] = $this->default_action;
            $client_version = $_GET['_v'] = $this->default_version;
        }

        //controller处理，如果导入Controller文件失败，则返回404
        $path = '\\app\\controller\\';
        $controllerClass = $controller . $this->controller_postfix;

        /**
         * 直接加载类验证
         * @author magus.lee
         */
        $controllerObj = null;
        $controllerFullPath = $path . $client_version . '\\' . $module . "\\" . $controllerClass;

        $controllerObj = ChelerApi::loadClass($controllerFullPath);
        define('API_VERSION', $client_version);

        // 未找到返回404
        if (is_null($controllerObj)) {
            ChelerApi::return404();
        }

        return $controllerObj;
    }

    /**
     * 框架运行控制器中的Action函数
     * 1. 获取Action中的a参数
     * 2. 检测是否在白名单中，不在则选择默认的
     * 3. 检测方法是否存在，不存在则运行默认的
     * 4. 运行函数
     * @param object $controller 控制器对象
     */
    private function run_action($controller)
    {
        $action = trim($_GET['a']);
        if (!method_exists($controller, $action)) {
            ChelerApi::throwError('Can not find default method : ' . $action);
        }

        $controller->$action();
    }

    /**
     * 运行框架前置类
     * 1. 检测方法是否存在，不存在则运行默认的
     * 2. 运行函数
     * @param object $controller 控制器对象
     * @return file
     */
    private function run_before_action($controller)
    {
        $before_action = $this->default_before_action . $this->action_postfix;
        if (!method_exists($controller, $before_action)) return false;
        $controller->$before_action();
    }

    /**
     * 运行框架后置类
     * 1. 检测方法是否存在，不存在则运行默认的
     * 2. 运行函数
     * @param object $controller 控制器对象
     * @return bool
     */
    private function run_after_action($controller)
    {
        $after_action = $this->default_after_action . $this->action_postfix;
        if (!method_exists($controller, $after_action)) return false;
        $controller->$after_action();
    }

    /**
     * version-module-controller-action数据处理
     * @return string
     */
    private function filterVMCA()
    {
        if (isset($_GET['m'])) {
            if (!$this->_filter($_GET['m'])) unset($_GET['m']);
        }
        if (isset($_GET['c'])) {
            if (!$this->_filter($_GET['c'])) unset($_GET['c']);
        }
        if (isset($_GET['a'])) {
            if (!$this->_filter($_GET['a'])) unset($_GET['a']);
        }
        if (isset($_GET['_v'])) {
            if (!$this->_filter($_GET['_v'])) unset($_GET['_v']);
        }
    }

    /**
     * 过滤str
     * @param $str
     * @return int
     */
    private function _filter($str)
    {
        return preg_match('/^[A-Za-z0-9_]+$/', trim($str));
    }
}