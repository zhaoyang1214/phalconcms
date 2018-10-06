<?php
/**
 * @desc 处理错误类
 * @author ZhaoYang
 * @date 2018年5月6日 下午11:58:46
 */
namespace App\Admin\Controllers;

use Phalcon\Mvc\Controller;

class ErrorController extends Controller {

    public function error404Action() {
    
    }

    public function menuErrorAction() {
        $this->flashSession->error('很抱歉，暂时没有可显示的功能！');
        $this->view->pick('error/error');
    }
}