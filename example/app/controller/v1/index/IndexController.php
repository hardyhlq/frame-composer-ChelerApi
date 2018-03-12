<?php

namespace app\controller\v1\index;

use app\dao\clw2\CarMemberCarDao;
use app\dao\clw2\MongoDemoDao;
use frame\ChelerApi;
use frame\runtime\Controller;
use app\service\demo\DemoService;

/**
 * Index.php
 * 描述
 * Date: 18-2-5
 */
class IndexController extends Controller
{
    function index()
    {
//        $this->getRedis("demo")->set("lx",123);
//        echo $this->getRedis("demo")->get("lx");

//        $this->getMemcache("demo")->set("lx",12222223);
//        echo $this->getMemcache("demo")->get("lx");exit;
//        echo "hello world<br/>";
//        echo "<hr/>";
//
//        // service
//        var_dump($this->_getDemoService()->index());
//        echo "<hr/>";
//        echo $this->_getDemoService()->protoTest("1","13212345678","test",1);

//        $this->_getMongoDemoDao()->insert();
        $this->apiSuccess("200","ok","213 world");
    }

    /**
     * @return DemoService
     */
    private function _getDemoService()
    {
        return ChelerApi::getService('demo\Demo');
    }

    /**
     * @return MongoDemoDao
     */
    private function _getMongoDemoDao()
    {
        return ChelerApi::getMongoDao('clw2\MongoDemo');
    }
    
}