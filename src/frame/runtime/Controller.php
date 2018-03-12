<?php
/**
 * Controller.php
 * 描述
 * User: lixin
 * Date: 18-2-6
 */

namespace frame\runtime;


use frame\ChelerApi;

class Controller extends CCore
{
    /**
     *
     * @var \frame\controller\CController
     */
    protected $controller;

    /**
     * @var string api日志document id
     */
    protected $docId;

    /**
     * 初始化
     */
    public function __construct()
    {
        parent::__construct();
        $this->controller = ChelerApi::load('\frame\controller\CController'); 
    }

    /**
     * 控制器 api输出
     * Controller中使用方法：$this->apiReturn()
     *
     * @param int $status
     *            0:错误信息|1:正确信息
     * @param string $message
     *            显示的信息
     * @param array $data
     *            传输的信息
     * @return object
     */
    private function apiReturn($status, $message = '', $data = array())
    {
        $uri = $this->controller->getUri();
        $pos = strpos($uri, '?');
        $return_data = [
            'code' => $status,
            'data' => $data,
            'msg' => $message,
            'request' => $pos === false ? $uri : substr($uri, 0, $pos)
        ];

        exit(json_encode($return_data));
    }

    /**
     * API成功返回
     *
     * @param int $code
     * @param string $message
     * @param mixed $data
     */
    protected function apiSuccess($code, $message = '', $data = array())
    {
        $this->apiReturn($code, $message, $data);
    }

    /**
     * API失败返回
     *
     * @param int $code
     * @param string $message
     * @param mixed $data
     */
    protected function apiError($code, $message = '', $data = array())
    {
        $this->apiReturn($code, $message, $data);
    }
}