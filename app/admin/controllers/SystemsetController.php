<?php
namespace App\Admin\Controllers;

use App\Admin\Models\SystemConfig;
use App\Admin\Models\Admin;

class SystemsetController extends CommonController {

    /** 
     * @desc 系统设置 
     * @author ZhaoYang 
     * @date 2018年7月17日 下午6:43:07 
     */
    public function indexAction() {
        $this->view->setTemplateBefore('common');
        $this->view->systemsetSavePower = (new Admin())->checkPower('Systemset', 'save');
    }

    /**
     * @desc 设置保存
     * @author: ZhaoYang
     * @date: 2018年7月18日 上午12:56:22
     */
    public function saveAction() {
        if (!($this->request->isAjax() && $this->request->isPost())) {
            return $this->sendJson('请求错误', 10002);
        }
        $systemConfig = new SystemConfig();
        $res = $systemConfig->saveConfig($this->post());
        if ($res === false) {
            return $this->sendJson($systemConfig->getMessages()[0]->getMessage(), 10001);
        } else {
            return $this->sendJson('保存成功');
        }
    }
}