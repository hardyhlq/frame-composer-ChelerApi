<?php
namespace app\service\demo;

use app\dao\clw2\CarMemberCarDao;
use frame\ChelerApi;
use frame\runtime\CService;
use Protocol\SmsTask;

/**
 * DemoService.php
 * 描述
 * User: lixin
 * Date: 18-2-5
 */
class DemoService extends CService
{
    /**
     * 项目配置名
     * @var string
     */
    public $project = 'sms';

    public function index()
    {
        return $this->_getCarMemberCarDao()->getInfoByField(['id' => 1]);
    }

    public function protoTest(string $templateId, string $phone, string $content, int $time)
    {
        $smsTask = new SmsTask();
        $smsTask->setTime($time);
        $smsTask->setTemplateId($templateId);
        $smsTask->setPhone($phone);
        $smsTask->setContent($content);
        $str = $this->getRequestStr($smsTask);
        $client = $this->getQueue($this->_conf[$this->project]['queue']['smsTasks']);
        $client->enQueue($this->_conf[$this->project]['queue']['smsTasks']['name'], $str);
        $this->close($client->getConnectOption());
    }

    /**
     * @return CarMemberCarDao
     */
    private function _getCarMemberCarDao()
    {
        return ChelerApi::getDao('clw2\CarMemberCar');
    }
}