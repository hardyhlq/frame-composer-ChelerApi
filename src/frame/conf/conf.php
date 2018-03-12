<?php
/**
 * 路由访问方式
 * 2. default：index.php?_v=1&m=user&c=index&a=run
 * 3. rewrite：/1/user/index/run?id=100
 */
$_CONFIG_['uriType'] = 'rewrite';

/*********************************namespace配置*****************************************/
$_CONFIG_['namespace']['app']                  = 'app/';
$_CONFIG_['namespace']['GPBMetadata']          = 'protobuf/';
/*********************************Controller配置*****************************************/
/**
 * Controller控制器配置参数
 * 1. 你可以配置控制器默认的文件夹，默认的后缀，Action默认后缀，默认执行的Action和Controller
 * 2. 一般情况下，你可以不需要修改该配置参数
 * 3. $_CONFIG_['ismodule']参数，当你的项目比较大的时候，可以选用module方式，
 * 开启module后，你的URL种需要带m的参数，原始：index.php?c=index&a=run, 加module：
 * index.php?m=user&c=index&a=run , module就是$_CONFIG_['controller']['path']目录下的
 * 一个文件夹名称，请用小写文件夹名称
 */
$_CONFIG_['ismodule'] = false; //开启module方式
$_CONFIG_['controller']['controller_postfix']    = 'Controller'; //控制器文件后缀名
$_CONFIG_['controller']['action_postfix']        = ''; //Action函数名称后缀
$_CONFIG_['controller']['default_controller']    = 'Index'; //默认执行的控制器名称
$_CONFIG_['controller']['default_action']        = 'index'; //默认执行的Action函数
$_CONFIG_['controller']['module_list']           = array('test', 'index'); //module白名单
$_CONFIG_['controller']['default_module']        = 'index'; //默认执行module
$_CONFIG_['controller']['default_before_action'] = 'before'; //默认前置的ACTION名称
$_CONFIG_['controller']['default_after_action']  = 'after'; //默认后置ACTION名称
