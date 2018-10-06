<?php
namespace App\Admin\Controllers;

use App\Admin\Models\Admin;
use App\Admin\Models\AdminAuth;
use App\Admin\Models\Translate;
use App\Admin\Models\Expand;
use Library\Tools\Paginator;
use App\Admin\Models\Category;

class ExpandController extends CommonController {

   /**
    * @desc 扩展管理
    * @author: ZhaoYang
    * @date: 2018年8月28日 上午12:25:09
    */
    public function manageAction() {
        $adminAuthInfo = AdminAuth::getInfoByConAct($this->dispatcher->getControllerName(), $this->dispatcher->getActionName());
        $authList = [ ];
        if ($adminAuthInfo !== false) {
            $authList = (new AdminAuth())->getAllowList($adminAuthInfo->id);
            $this->view->authName = (new Translate())->t($adminAuthInfo->name);
        }
        $this->view->authList = $authList;
        $this->view->pick('index/manage');
    }
    
    /**
     * @desc 扩展列表
     * @author: ZhaoYang
     * @date: 2018年8月28日 上午12:25:31
     */
    public function indexAction() {
        $expand = new Expand();
        $count = $expand->getAllowCount();
        $paginator = new Paginator($count);
        $expandList = $expand->getAllowList($paginator->getLimit(), $paginator->getOffset());
        $admin = new Admin();
        $this->view->setTemplateBefore('common');
        $this->view->pageShow = $paginator->show();
        $this->view->expandList = $expandList;
        $this->view->expandAddPower = $admin->checkPower('expand', 'add');
        $this->view->expandInfoPower = $admin->checkPower('expand', 'info');
        $this->view->expandDeletePower = $admin->checkPower('expand', 'delete');
        $this->view->expandfieldIndexPower = $admin->checkPower('expandfield', 'index');
    }
    
    /** 
     * @desc 添加 
     * @author ZhaoYang 
     * @date 2018年8月28日 下午5:02:23 
     */
    public function addAction() {
        if($this->request->isPost()) {
            $expand = new Expand();
            $addRes = $expand->add($this->post());
            if($addRes) {
                return $this->sendJson('添加成功！');
            }
            return $this->sendJson($expand->getMessages()[0]->getMessage(), 10001);
        }
        $admin = new Admin();
        $translate = new Translate();
        $this->view->actionUrl = $this->url->get('expand/add');
        $this->view->actionName = $translate->t('添加');
        $this->view->actionPower = $admin->checkPower('expand', 'add');
        $this->view->jumpButton = $translate->t('继续添加');
        $this->view->action = 'add';
        $this->view->formIndexPower = $admin->checkPower('expand', 'index');
        $this->view->pick('expand/info');
    }
    
    /**
     * @desc 查看
     * @author: ZhaoYang
     * @date: 2018年8月29日 下午11:42:08
     */
    public function infoAction() {
        $expand = new Expand();
        $info = $expand->getInfoById($this->get('id', 'absint', 0));
        if ($info === false) {
            $this->error($expand->getMessages()[0]->getMessage(), false);
        }
        $admin = new Admin();
        $translate = new Translate();
        $actionPower = $admin->checkPower('expand', 'edit');
        $actionName = $actionPower ? '修改' : '查看';
        $this->view->actionUrl = $this->url->get('expand/edit');
        $this->view->actionPower = $actionPower;
        $this->view->actionName = $translate->t($actionName);
        $this->view->action = 'edit';
        $this->view->expand = $info;
    }
    
    /**
     * @desc 修改
     * @author: ZhaoYang
     * @date: 2018年8月29日 下午11:51:23
     */
    public function editAction() {
        $expand = new Expand();
        $editRes = $expand->edit($this->post());
        if ($editRes === false) {
            return $this->sendJson($expand->getMessages()[0]->getMessage(), 10001);
        }
        return $this->sendJson('修改成功！');
    }
    
    /**
     * @desc 删除
     * @author: ZhaoYang
     * @date: 2018年8月30日 上午12:34:07
     */
    public function deleteAction() {
        set_time_limit(300);
        ignore_user_abort(true);
        $id = $this->post('id', 'absint', 0);
        if(!empty($id) && Category::count('expand_id=' . $id)) {
            return $this->sendJson('该扩展已被栏目使用，不允许删除', 10001);
        }
        $expand = new Expand();
        $delRes = $expand->del($id);
        if($delRes === false) {
            return $this->sendJson($expand->getMessages()[0]->getMessage(), 10001);
        }
        return $this->sendJson('删除成功！');
    }
}