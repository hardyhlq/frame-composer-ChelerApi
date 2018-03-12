<?php
/**
 * api.conf.php
 * 接口配置文件
 * Date: 17-12-12
 */
global $_CONFIG_;
$_CONFIG_['apiConfig'] = [
    // 一个项目配置一个
    'tinyApi' => [
        // 校验方式是否开启
        'authSwitch' => false,
        // 超时 单位秒
        'timeout' => 120,
        // 本地域名
        'domainLocal' => 'http://erp.tinyapi.dev/index.php',
        // 测试域名
        'domainDev' => 'http://tinyapi.linewin.cc',
        // 预发布域名
        'domainPre' => 'http://pre-tinyapi.geni4s.com',
        // 正式域名
        'domainProduct' => 'http://tinyapi.geni4s.com',
        // 接口配置
        'api' => [
            'getCarInfo' => [
                'method' => 'GET',
                'name' => '/erpCar/car/getCarInfo',
            ],
        ],
    ],

    'sms' => [
        // 校验方式是否开启
        'authSwitch' => false,
        // 超时 单位秒
        'timeout' => 2,
        // 配置
        'queue' => [
            // 一个队列一个
            'smsTasks' => [
                // 队列名
                'name' => 'SmsTasks',
                // 队列类型
                'method' => 'redis',  // 或nats
                // 地址
                'host' => $_CONFIG_['redis']['demo']['server'], //在conf.php中配置
                // 端口
                'port' => $_CONFIG_['redis']['demo']['port'],  //在conf.php中配置
                // 用户名
                'user' => '', //nats需要
                // 密码
                'pass' => $_CONFIG_['redis']['demo']['password'],
                // db（正式服上使用redis的1号db）
                'db' => $_CONFIG_['redis']['demo']['db'],
            ],
        ],
    ],
];