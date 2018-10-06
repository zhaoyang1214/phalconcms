<?php
/**
 * @desc 入口文件
 * @author ZhaoYang
 * @date 2018年5月3日 下午5:16:27
 */
use \Phalcon\Mvc\Application;
use Phalcon\Loader;

// 检查版本，搭建用到php7一些新特性
version_compare(PHP_VERSION, '7.0.0', '>') || exit('Require PHP > 7.0.0 !');
extension_loaded('phalcon') || exit('Please open the Phalcon extension !');
//脚本运行开始时间
$GLOBALS['script_start_time'] = microtime(true);
// 引入自定义常量文件
require '../config/define.php';

version_compare(PHALCON_VERSION, '3.0.0', '>') || exit('Require Phalcon > 3.0.0 !');

// 设置时区
date_default_timezone_set('Asia/Shanghai');

NOW_ENV != 'dev' && error_reporting(E_ALL & ~E_NOTICE);

try {
    
    // 引入注册服务
    $di = require BASE_PATH . 'config/services.php';
    
    $loaderConfig = $di->getConfig()->application->loader->toArray();
    
    // 处理请求
    $application = new Application($di);
    
    // 组装应用程序模块
    $modules = [ ];
    foreach (MODULE_ALLOW_LIST as $v) {
        $ucV = ucfirst($v);
        $modules[$v] = [
            'className' => APP_NAMESPACE . '\\' . $ucV . '\\Module',
            'path' => APP_PATH . $v . '/Module.php'
        ];
        // 将各模块模型注册自动加载
        $loaderConfig['namespaces'][APP_NAMESPACE . '\\' . $ucV . '\\Models'] = APP_PATH . $v . '/models/';
    }
    
    // 注册自动加载
    $loader = new Loader();
    $loader->registerClasses($loaderConfig['classes'])
    ->registerNamespaces($loaderConfig['namespaces'])
    ->registerFiles($loaderConfig['files'])
    ->registerDirs($loaderConfig['directories'])
    ->register();
    
    // 加入模块分组配置
    $application->registerModules($modules);
    
    // 输出请求内容
    echo $application->handle()->getContent();
} catch (\Throwable $e) {
    $previous = $e->getPrevious();
    if(!is_object($application->config)){
        goto SYSTEMERROR;
    }
    $applicationConfig = $application->config->application;
    if ($applicationConfig->debug->state ?? false) {
        if (empty($applicationConfig->debug->path)) {
            SYSTEMERROR:
            echo 'Exception： <br/>', '所在文件：', $e->getFile(), '<br/>所在行：', $e->getLine(), '<br/>错误码：', $e->getCode(), '<br/>错误消息：', $e->getMessage();
            if (!is_null($previous)) {
                echo '<br/>前一个Exception： <br/>', '所在文件：', $previous->getFile(), '<br/>所在行：', $previous->getLine(), '<br/>错误码：', $previous->getCode(), '<br/>错误消息：', $previous->getMessage();
            }
            exit();
        }
        $errorType = 'debug';
    } else {
        $errorType = 'error';
    }
    $errorFile = $applicationConfig->$errorType->path;
    $errorMessage = 'Exception： [所在文件：' . $e->getFile() . '] [所在行：' . $e->getLine() . '] [错误码：' . $e->getCode() . '] [错误消息：' . $e->getMessage() . '] '/*  . PHP_EOL . '[异常追踪信息：' . $e->getTraceAsString() . ']' */;
    if (!is_null($previous)) {
        $errorMessage .= '  前一个Exception： [所在文件：' . $previous->getFile() . '] [所在行：' . $previous->getLine() . '] [错误码：' . $previous->getCode() . '] [错误消息：' . $previous->getMessage() . '] '/*  . PHP_EOL . '[异常追踪信息：' . $previous->getTraceAsString() . ']' */;
    }
    $application->di->get('logger', [$errorFile])->$errorType($errorMessage);
}