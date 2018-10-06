<?php
namespace App\Admin\Controllers;

use App\Admin\Models\TranslateDriver;
use App\Admin\Models\Admin;
use App\Admin\Models\Language;
use App\Admin\Models\Translate;

class TranslatedriverController extends CommonController {

    /**
     * @desc 翻译驱动管理
     * @author: ZhaoYang
     * @date: 2018年7月24日 上午1:25:30
     */
    public function indexAction() {
        $translateDriverList = TranslateDriver::find();
        $this->view->setTemplateBefore('common');
        $this->view->translateDriverList = $translateDriverList;
        $admin = new Admin();
        $this->view->translatedriverAddPower = $admin->checkPower('Translatedriver', 'add');
        $this->view->translatedriverInfoPower = $admin->checkPower('Translatedriver', 'info');
    }

    /**
     * @desc 添加翻译驱动
     * @author: ZhaoYang
     * @date: 2018年7月24日 上午1:43:00
     */
    public function addAction() {
        if ($this->request->isPost()) {
            $translateDriver = new TranslateDriver();
            $addRes = $translateDriver->add($this->post());
            if ($addRes === false) {
                return $this->sendJson($translateDriver->getMessages()[0]->getMessage(), 10001);
            }
            return $this->sendJson('添加成功！');
        }
        $admin = new Admin();
        $translate = new Translate();
        $this->view->actionUrl = $this->url->get('Translatedriver/add');
        $this->view->actionName = $translate->t('添加');
        $this->view->actionPower = $admin->checkPower('Translatedriver', 'add');
        $this->view->jumpButton = $translate->t('继续添加');
        $this->view->action = 'add';
        $this->view->translatedriverIndexPower = $admin->checkPower('Translatedriver', 'index');
        $this->view->setTemplateBefore('common');
        $this->view->pick('translatedriver/info');
    }

    /**
     * @desc 查看详情
     * @author: ZhaoYang
     * @date: 2018年7月24日 下午11:42:10
     */
    public function infoAction() {
        $id = $this->get('id', 'absint', 0);
        $translateDriver = TranslateDriver::findFirst($id);
        if ($translateDriver === false) {
            return $this->error('该记录不存在');
        }
        $languageList = Language::find();
        if ($languageList === false) {
            return $this->error('请先添加语言！');
        }
        $translateAdapter = new $translateDriver->class_name();
        $needSetConfig = $translateAdapter::needSetConfig();
        $baseLanguage = $translateAdapter::baseLanguage();
        $needSetConfig = $translateAdapter::needSetConfig();
        $baseLanguage = $translateAdapter::baseLanguage();
        $admin = new Admin();
        $translate = new Translate();
        $actionPower = $admin->checkPower('Translatedriver', 'edit');
        $actionName = $actionPower ? '修改' : '查看';
        $this->view->actionUrl = $this->url->get('Translatedriver/edit');
        $this->view->actionName = $translate->t($actionName);
        $this->view->actionPower = $actionPower;
        $this->view->jumpButton = $translate->t('查看修改');
        $this->view->action = 'edit';
        $this->view->translatedriverIndexPower = $admin->checkPower('Translatedriver', 'index');
        $this->view->translateDriver = $translateDriver;
        $this->view->translateDriverConfig = $translateDriver->config;
        $this->view->needSetConfig = is_array($needSetConfig) ? $needSetConfig : [ ];
        $this->view->baseLanguage = is_array($baseLanguage) ? $baseLanguage : [ ];
        $this->view->languageList = $languageList;
        $this->view->setTemplateBefore('common');
    }

    /**
     * @desc 翻译修改
     * @author: ZhaoYang
     * @date: 2018年7月23日 下午9:03:46
     */
    public function editAction() {
        $translateDriver = new TranslateDriver();
        $editRes = $translateDriver->edit($this->post());
        if ($editRes === false) {
            return $this->sendJson($translateDriver->getMessages()[0]->getMessage(), 10001);
        }
        return $this->sendJson('修改成功！');
    }
}