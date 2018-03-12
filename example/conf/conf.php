<?php
/* 框架全局配置常量 */
error_reporting(E_ALL);

/* 框架全局配置变量 */
/*********************************基础配置*****************************************/
// 站点URL配置
$_CONFIG_['url']['url'] = 'http://capi.com/';
$_CONFIG_['url']['clw'] = 'http://test.linewin.cc/';
$_CONFIG_['url']['default_bucket'] = 'www-linewin-cc';
$_CONFIG_['url']['ossimage'] = 'http://ossimage.linewin.cc/';

// 是否开启调试
$_CONFIG_['debug'] = true;



$_CONFIG_['db']['default']['db_type'] = 0; //0-单个服务器，1-读写分离，2-随机
$_CONFIG_['db']['default'][0]['host'] = '121.40.133.157'; //主机
$_CONFIG_['db']['default'][0]['username'] = 'testDBForXa'; //数据库用户名
$_CONFIG_['db']['default'][0]['password'] = 'c%d7KI#hou&%'; //数据库密码
$_CONFIG_['db']['default'][0]['database'] = '17car'; //数据库
$_CONFIG_['db']['default'][0]['charset'] = 'utf8'; //数据库编码

$_CONFIG_['db']['cms']['db_type'] = 0; //0-单个服务器，1-读写分离，2-随机
$_CONFIG_['db']['cms'][0]['host'] = '121.40.133.157'; //主机
$_CONFIG_['db']['cms'][0]['username'] = 'testDBForXa'; //数据库用户名
$_CONFIG_['db']['cms'][0]['password'] = 'c%d7KI#hou&%'; //数据库密码
$_CONFIG_['db']['cms'][0]['database'] = '17car'; //数据库
$_CONFIG_['db']['cms'][0]['charset'] = 'utf8'; //数据库编码

$_CONFIG_['db']['clw2']['db_type'] = 0; //0-单个服务器，1-读写分离，2-随机
$_CONFIG_['db']['clw2'][0]['host'] = '121.40.133.157'; //主机
$_CONFIG_['db']['clw2'][0]['username'] = 'testDBForXa'; //数据库用户名
$_CONFIG_['db']['clw2'][0]['password'] = 'c%d7KI#hou&%'; //数据库密码
$_CONFIG_['db']['clw2'][0]['database'] = '17car_clw2'; //数据库
$_CONFIG_['db']['clw2'][0]['charset'] = 'utf8'; //数据库编码

$_CONFIG_['db']['car_gps_2016']['db_type'] = 0; //0-单个服务器，1-读写分离，2-随机
$_CONFIG_['db']['car_gps_2016'][0]['host'] = '121.40.133.157'; //主机
$_CONFIG_['db']['car_gps_2016'][0]['username'] = 'testDBForXa'; //数据库用户名
$_CONFIG_['db']['car_gps_2016'][0]['password'] = 'c%d7KI#hou&%'; //数据库密码
$_CONFIG_['db']['car_gps_2016'][0]['database'] = 'car_gps_2016'; //数据库
$_CONFIG_['db']['car_gps_2016'][0]['charset'] = 'utf8'; //数据库编码

$_CONFIG_['db']['car_gps_2017']['db_type'] = 0; //0-单个服务器，1-读写分离，2-随机
$_CONFIG_['db']['car_gps_2017'][0]['host'] = '121.40.133.157'; //主机
$_CONFIG_['db']['car_gps_2017'][0]['username'] = 'testDBForXa'; //数据库用户名
$_CONFIG_['db']['car_gps_2017'][0]['password'] = 'c%d7KI#hou&%'; //数据库密码
$_CONFIG_['db']['car_gps_2017'][0]['database'] = 'car_gps_2017'; //数据库
$_CONFIG_['db']['car_gps_2017'][0]['charset'] = 'utf8'; //数据库编码

$_CONFIG_['db']['message']['db_type'] = 0; //0-单个服务器，1-读写分离，2-随机
$_CONFIG_['db']['message'][0]['host'] = '121.40.133.157'; //主机
$_CONFIG_['db']['message'][0]['username'] = 'testDBForXa'; //数据库用户名
$_CONFIG_['db']['message'][0]['password'] = 'c%d7KI#hou&%'; //数据库密码
$_CONFIG_['db']['message'][0]['database'] = 'car_message'; //数据库
$_CONFIG_['db']['message'][0]['charset'] = 'utf8'; //数据库编码
$_CONFIG_['db']['message'][0]['pconnect'] = 0; //是否持久链接

$_CONFIG_['db']['obdlog']['db_type'] = 0; //0-单个服务器，1-读写分离，2-随机
$_CONFIG_['db']['obdlog'][0]['host'] = '121.40.133.157'; //主机
$_CONFIG_['db']['obdlog'][0]['username'] = 'testDBForXa'; //数据库用户名
$_CONFIG_['db']['obdlog'][0]['password'] = 'c%d7KI#hou&%'; //数据库密码
$_CONFIG_['db']['obdlog'][0]['database'] = 'car_obd_log'; //数据库
$_CONFIG_['db']['obdlog'][0]['charset'] = 'utf8'; //数据库编码
$_CONFIG_['db']['obdlog'][0]['pconnect'] = 0; //是否持久链接

$_CONFIG_['db']['position']['db_type'] = 0; //0-单个服务器，1-读写分离，2-随机
$_CONFIG_['db']['position'][0]['host'] = '121.40.133.157'; //主机
$_CONFIG_['db']['position'][0]['username'] = 'testDBForXa'; //数据库用户名
$_CONFIG_['db']['position'][0]['password'] = 'c%d7KI#hou&%'; //数据库密码
$_CONFIG_['db']['position'][0]['database'] = 'car_position'; //数据库
$_CONFIG_['db']['position'][0]['charset'] = 'utf8'; //数据库编码
$_CONFIG_['db']['position'][0]['pconnect'] = 0; //是否持久链接

$_CONFIG_['db']['erp']['db_type'] = 0; //0-单个服务器，1-读写分离，2-随机
$_CONFIG_['db']['erp'][0]['host'] = '121.40.133.157'; //主机
$_CONFIG_['db']['erp'][0]['username'] = 'testDBForXa'; //数据库用户名
$_CONFIG_['db']['erp'][0]['password'] = 'c%d7KI#hou&%'; //数据库密码
$_CONFIG_['db']['erp'][0]['database'] = 'erp'; //数据库
$_CONFIG_['db']['erp'][0]['charset'] = 'utf8'; //数据库编码
$_CONFIG_['db']['erp'][0]['pconnect'] = 0; //是否持久链接

// mongo
$_CONFIG_['mongo']['group']['username']   = '';
$_CONFIG_['mongo']['group']['password']   = '';
$_CONFIG_['mongo']['group']['server']     = '127.0.0.1';
$_CONFIG_['mongo']['group']['port']       = '27017';
$_CONFIG_['mongo']['group']['verify']     = '';

// log
$_CONFIG_['mongo']['log']['server']     = '127.0.0.1';
$_CONFIG_['mongo']['log']['port']       = '27017';

/*********************************Memcache缓存配置*****************************************/
/**
 * 缓存配置参数
 * 1. 您如果使用缓存 需要配置memcache的服务器和文件缓存的缓存路径
 * 2. memcache可以配置分布式服务器，根据$_CONFIG_['memcache']的KEY值去进行添加
 * 3. 根据您的实际情况配置
 */

$_CONFIG_['memcache']['demo']['server']   = '127.0.0.1';
$_CONFIG_['memcache']['demo']['port']   = '11211';

$_CONFIG_['redis']['demo']['server']   = '121.40.133.157';
$_CONFIG_['redis']['demo']['password']   = '';
$_CONFIG_['redis']['demo']['port']   = '6379';
$_CONFIG_['redis']['demo']['db']   = 0;

/********************************* 其他扩展配置 ********************************************/
// 环境配置 1本地 2测试 3预发布 4线上
$_CONFIG_['env']  = 1;
require('conf/api.conf.php');
