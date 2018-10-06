<?php
namespace App\Admin\Controllers;

use App\Admin\Models\Admin;
use App\Admin\Models\AdminAuth;
use App\Admin\Models\Translate;
use App\Admin\Models\Form;
use Library\Tools\Paginator;
use Common\Validate;

class FormController extends CommonController {

   /**
    * @desc 表单管理
    * @author: ZhaoYang
    * @date: 2018年8月5日 下午6:00:50
    */
    public function manageAction() {
        $adminAuthInfo = AdminAuth::getInfoByConAct($this->dispatcher->getControllerName(), $this->dispatcher->getActionName());
        $authList = [ ];
        if ($adminAuthInfo !== false) {
            $authList = (new AdminAuth())->getAllowList($adminAuthInfo->id);
            $this->view->authName = (new Translate())->t($adminAuthInfo->name);
        }
        $this->view->authList = $authList;
        $this->view->list = (new Admin())->checkPower('Formdata', 'index') ? (new Form())->getAllowList() : [ ];
        $this->view->origin = $this->request('origin');
    }
    
    /**
     * @desc 列表
     * @author: ZhaoYang
     * @date: 2018年8月5日 下午9:13:38
     */
    public function indexAction() {
        $form = new Form();
        $count = $form->getAllowCount();
        $paginator = new Paginator($count);
        $formList = $form->getAllowList($paginator->getLimit(), $paginator->getOffset());
        $admin = new Admin();
        $this->view->setTemplateBefore('common');
        $this->view->pageShow = $paginator->show();
        $this->view->formList = $formList;
        $this->view->formAddPower = $admin->checkPower('form', 'add');
        $this->view->formInfoPower = $admin->checkPower('form', 'info');
        $this->view->formDeletePower = $admin->checkPower('form', 'delete');
        $this->view->formfieldIndexPower = $admin->checkPower('formfield', 'index');
    }
    
    /**
     * @desc 添加
     * @author: ZhaoYang
     * @date: 2018年8月6日 上午12:13:36
     */
    public function addAction() {
        if($this->request->isPost()) {
            $form = new Form();
            $addRes = $form->add($this->post());
            if($addRes) {
                return $this->sendJson('添加成功！');
            }
            return $this->sendJson($form->getMessages()[0]->getMessage(), 10001);
        }
        $admin = new Admin();
        $translate = new Translate();
        $this->view->actionUrl = $this->url->get('form/add');
        $this->view->actionName = $translate->t('添加');
        $this->view->actionPower = $admin->checkPower('form', 'add');
        $this->view->jumpButton = $translate->t('继续添加');
        $this->view->action = 'add';
        $this->view->formIndexPower = $admin->checkPower('form', 'index');
        $this->view->setTemplateBefore('common');
        $this->view->pick('form/info');
    }
    
    /**
     * @desc 查看
     * @author: ZhaoYang
     * @date: 2018年8月7日 上午12:14:03
     */
    public function infoAction() {
        $data['id'] = $this->get('id', 'absint', 0);
        $message = (new Validate())->addRules((new Form())->getRules(['id0']))->validate($data);
        if(count($message)) {
            return $this->error('非法操作！');
        }
        $form = Form::findFirst($data['id']);
        $admin = new Admin();
        $translate = new Translate();
        $actionPower = $admin->checkPower('form', 'edit');
        $actionName = $actionPower ? '修改' : '查看';
        $this->view->actionUrl = $this->url->get('form/edit');
        $this->view->actionPower = $actionPower;
        $this->view->actionName = $translate->t($actionName);
        $this->view->jumpButton = $translate->t('查看修改');
        $this->view->action = 'edit';
        $this->view->formIndexPower = $admin->checkPower('form', 'index');
        $this->view->form = $form;
        $this->view->setTemplateBefore('common');
    }
    
    /**
     * @desc 修改
     * @author: ZhaoYang
     * @date: 2018年8月8日 上午12:31:35
     */
    public function editAction() {
        $form = new Form();
        $editRes = $form->edit($this->post());
        if ($editRes === false) {
            return $this->sendJson($form->getMessages()[0]->getMessage(), 10001);
        }
        return $this->sendJson('修改成功！');
    }
    
    /**
     * @desc 删除
     * @author: ZhaoYang
     * @date: 2018年8月8日 上午12:49:33
     */
    public function deleteAction() {
        set_time_limit(300);
        ignore_user_abort(true);
        $id = $this->post('id', 'absint', 0);
        $form = new Form();
        $delRes = $form->del($id);
        if($delRes === false) {
            return $this->sendJson($form->getMessages()[0]->getMessage(), 10001);
        }
        return $this->sendJson('删除成功！');
    }
}