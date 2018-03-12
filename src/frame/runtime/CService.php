<?php
/**
 * CApiService.php
 * 请求Api接口
 * User: lixin
 * Date: 17-12-12
 */

namespace frame\runtime;


use frame\ChelerApi;
use LQueue\interfaces\IOption;
use Message\Message;

class CService extends CCore
{
    /**
     * 服务端token返回错误
     */
    const TOKEN_ERROR_CODE = 1000;

    /**
     * 接口配置
     * @var array
     */
    protected $_conf = [];

    /**
     * 链接保存
     * @var array
     */
    protected $_client = [];

    /**
     * 配置中的项目名 默认tinyApi
     * @var string
     */
    public $project = 'tinyApi';

    /**
     * CApiService constructor.
     * @author lixin
     */
    public function __construct()
    {
        parent::__construct();
        $this->_conf = ChelerApi::getConfig('apiConfig');
        if (empty($this->_conf)) {
            throw new CException('cant not find api config', 500);
        }
    }

    /**
     * 获取队列对象
     * @param array $queue queue配置
     * @param int $timeout 超时时间
     * @return \LQueue\interfaces\IQueue
     * @throws CException
     * @throws \Exception
     * @author lixin
     */
    protected function getQueue(array $queue, int $timeout = 0)
    {
        if (empty($queue)) {
            throw new CException('your input queue is not found in config', 500);
        }
        if ($queue['method'] != 'redis' && $queue['method'] != 'nats') {
            throw new CException('the method is not supported in frame', 500);
        }

        $client = \LQueue\Factory::getQueue($queue['method']);

        $option = $client->getConnectOption();

        if (isset($queue['host']) && !empty($queue['host'])) {
            $option->setHost($queue['host']);
        }
        if (isset($queue['port']) && !empty($queue['port'])) {
            $option->setPort($queue['port']);
        }
        if (isset($queue['user']) && !empty($queue['user'])) {
            $option->setUser($queue['user']);
        }
        if (isset($queue['pass']) && !empty($queue['pass'])) {
            $option->setPass($queue['pass']);
        }
        if (isset($queue['db']) && !empty($queue['db'])) {
            $option->setDb($queue['db']);
        }

        if (!empty($timeout)) {
            $option->setTimeout($timeout);
        } else {
            $option->setTimeout($this->_conf[$this->project]['timeout']);
        }

        if (!isset($this->_client[md5($option)])) {
            $client->driver();
            $this->_client[md5($option)] = $client;
        }

        return $this->_client[md5($option)];
    }

    /**
     * 拼装请求
     * @param object $pbObj protobuf对象
     * @param null $priority 优先级
     * @param array $option 选项
     * @return mixed
     * @author lixin
     */
    public function getRequestStr($pbObj, $priority = null, $option = [])
    {
        $message = new Message();

        $message->setMessageId(genMsgID());

        $message->setBody($pbObj->serializeToString());
        if ($priority !== null) {
            $message->setPriority($priority);
        }
        if (!empty($option)) {
            $arr = new \Google\Protobuf\Internal\MapField(
                \Google\Protobuf\Internal\GPBType::STRING,
                \Google\Protobuf\Internal\GPBType::STRING
            );
            foreach ($option as $key => $value) {
                $arr[$key] = $value;
            }
            $message->setOptions($arr);
        }
        return $message->serializeToString();
    }
    
    /**
     * 关闭对应链接
     * @param IOption $option
     * @author lixin
     */
    protected function close($option)
    {
        if (isset($this->_client[md5($option)])) {
            $this->_client[md5($option)]->close();
            unset($this->_client[md5($option)]);
        }
    }

    /**
     * HTTP请求
     * @param array $api
     * @param array $param
     * @param int $timeout
     * @return array
     * @throws CException
     * @author lixin
     */
    protected function _query(array $api, array $param = [], int $timeout = 0) : array
    {
        if (empty($api)) {
            throw new CException('your input api is not found in config', 500);
        }

        if ($api['method'] != 'GET' && $api['method'] != 'POST') {
            throw new CException('the method is not supported in frame', 500);
        }

        // 设置超时时间
        if (empty($timeout)) {
            $timeout = $this->_conf[$this->project]['timeout'];
        }
        $env = ChelerApi::getConfig('env');

        switch ($env) {
            case 1:
                $domain = $this->_conf[$this->project]['domainLocal'];
                break;
            case 2:
                $domain = $this->_conf[$this->project]['domainDev'];
                break;
            case 3:
                $domain = $this->_conf[$this->project]['domainPre'];
                break;
            case 4:
                $domain = $this->_conf[$this->project]['domainProduct'];
                break;
            default:
                throw new CException('can not found env config', 500);
        }

        $uri = $domain . $api['name'];
        
        // 判断是否需要鉴权
        if ($this->_conf[$this->project]['authSwitch']) {
            // TODO 新增鉴权在这里加判断 暂时没有 所有先不加 用$this->project去读配置 然后自行实现鉴权
            throw new CException('do not implement', 500);
        } else {
            if ($api['method'] == 'GET') {
                return $this->_get($uri, [], $param, $timeout);
            } else {
                return $this->_post($uri, [], $param, $timeout);
            }
        }
    }

    /**
     * post请求
     * @param string $url 接口链接
     * @param array $header http头
     * @param array $data post请求参数
     * @param int $timeout 超时时间
     * @return array
     * @author lixin
     */
    private function _post(string $url, array $header = [], array $data = [], int $timeout = 2) : array
    {
        return $this->_httpQuery('POST', $url, $header, $data, $timeout);
    }

    /**
     * get请求
     * @param string $url 接口链接
     * @param array $header http头
     * @param array $data get请求参数
     * @param int $timeout 超时时间
     * @return array
     * @author lixin
     */
    private function _get(string $url, array $header = [], array $data = [], int $timeout = 2) : array
    {
        return $this->_httpQuery('GET', $url, $header, $data, $timeout);
    }

    /**
     * http请求
     * @param string $type
     * @param string $url
     * @param array $header
     * @param array $data
     * @param int $timeout
     * @return array
     * @author lixin
     */
    private function _httpQuery(string $type, string $url, array $header = [], array $data = [], int $timeout = 2) : array
    {
        if (!in_array($type, ['GET', 'POST'])) {
            return [
                'curlNo' => CURLE_UNSUPPORTED_PROTOCOL,
                'curlMsg' => 'framework does not support the request',
                'result' => [],
            ];
        }

        $ch = curl_init();

        if ($type == 'POST') {
            if (!empty($data)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
                curl_setopt($ch, CURLOPT_POST, true);
            }
        } else {
            if (!empty($data)) {
                $url .= '?' . http_build_query($data);
                curl_setopt($ch, CURLOPT_POST, false);
            }
        }

        if (substr($url, 0, 5) == 'https') {
            // 跳过证书检查
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            // 从证书中检查SSL加密算法是否存在
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);

        // 添加头
        if (!empty($header)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }

        //设置cURL允许执行的最长秒数
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

        $response = curl_exec($ch);
        $errNo = curl_errno($ch);
        $errMsg = curl_error($ch);
        curl_close($ch);

        if ($errNo == CURLE_OK) {
            $result = json_decode($response, 1);
            return [
                'curlNo' => CURLE_OK,
                'curlMsg' => 'curl ok',
                'result' => $result
            ];
        } else {
            return [
                'curlNo' => $errNo,
                'curlMsg' => $errMsg,
                'result' => [],
            ];
        }
    }
}