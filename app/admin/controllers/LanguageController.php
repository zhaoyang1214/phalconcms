<?php
namespace App\Admin\Controllers;

use Library\Tools\Paginator;
use App\Admin\Models\Language;
use App\Admin\Models\Admin;
use App\Admin\Models\Translate;

class LanguageController extends CommonController {

    /**
     * @desc 语言管理列表
     * @author: ZhaoYang
     * @date: 2018年7月18日 下午10:44:49
     */
    public function indexAction() {
        $count = Language::count();
        $paginator = new Paginator($count);
        $languageList = Language::find([ 
            'limit' => $paginator->getLimit(),
            'offset' => $paginator->getOffset()
        ]);
        $admin = new Admin();
        $this->view->setTemplateBefore('common');
        $this->view->pageShow = $paginator->show();
        $this->view->languageList = $languageList;
        $this->view->languageAddPower = $admin->checkPower('Language', 'add');
        $this->view->languageInfoPower = $admin->checkPower('Language', 'info');
    }

    /** 
     * @desc 添加 
     * @author ZhaoYang 
     * @date 2018年7月25日 下午4:49:18 
     */
    public function addAction() {
        if ($this->request->isPost()) {
            $language = new Language();
            $addRes = $language->add($this->post());
            if ($addRes === false) {
                return $this->sendJson($language->getMessages()[0]->getMessage(), 10001);
            }
            return $this->sendJson('添加成功！');
        }
        $admin = new Admin();
        $translate = new Translate();
        $this->view->actionUrl = $this->url->get('Language/add');
        $this->view->actionName = $translate->t('添加');
        $this->view->actionPower = $admin->checkPower('Language', 'add');
        $this->view->jumpButton = $translate->t('继续添加');
        $this->view->action = 'add';
        $this->view->languageIndexPower = $admin->checkPower('Language', 'index');
        $this->view->setTemplateBefore('common');
        $this->view->pick('language/info');
    }

    /**
     * @desc 查看详情
     * @author: ZhaoYang
     * @date: 2018年7月26日 上午12:27:21
     */
    public function infoAction() {
        $id = $this->get('id', 'absint', 0);
        $language = Language::findFirst($id);
        if ($language === false) {
            return $this->error('该语言不存在');
        }
        $admin = new Admin();
        $translate = new Translate();
        $actionPower = $admin->checkPower('Language', 'edit');
        $actionName = $actionPower ? '修改' : '查看';
        $this->view->actionUrl = $this->url->get('Language/edit');
        $this->view->actionName = $translate->t($actionName);
        $this->view->actionPower = $actionPower;
        $this->view->jumpButton = $translate->t('查看修改');
        $this->view->action = 'edit';
        $this->view->languageIndexPower = $admin->checkPower('Language', 'index');
        $this->view->language = $language;
        $this->view->setTemplateBefore('common');
    }

    /**
     * @desc 修改
     * @author: ZhaoYang
     * @date: 2018年7月26日 上午12:41:37
     */
    public function editAction() {
        $language = new Language();
        $editRes = $language->edit($this->post());
        if ($editRes === false) {
            return $this->sendJson($language->getMessages()[0]->getMessage(), 10001);
        }
        return $this->sendJson('修改成功！');
    }
}