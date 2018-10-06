<?php
namespace App\Admin\Controllers;

use App\Admin\Models\FormData;
use Common\Validate;
use App\Admin\Models\Form;
use Library\Tools\Paginator;
use App\Admin\Models\Admin;
use App\Admin\Models\FormField;
use App\Admin\Models\Translate;

class FormdataController extends CommonController {

    /** 
     * @desc 表单数据浏览 
     * @author ZhaoYang 
     * @date 2018年8月13日 下午5:58:00 
     */
    public function indexAction() {
        $formId = $this->get('form_id', 'absint', 0);
        $message = (new Validate())->addRules((new Form())->getRules(['id0']))->validate(['id' => $formId]);
        if(count($message)) {
            return $this->error('非法操作！');
        }
        $form = Form::findFirst($formId);
        $formData = new FormData($form->table);
        $count = $formData::count();
        $paginator = new Paginator($count);
        $formDataList = $formData::find([
            'order' => empty($form->sort) ? 'id ASC' : $form->sort,
            'limit' => $paginator->getLimit(),
            'offset' => $paginator->getOffset()
        ]);
        $admin = new Admin();
        $this->view->setTemplateBefore('common');
        $this->view->pageShow = $paginator->show();
        $this->view->formDataList = $formDataList;
        $this->view->formFieldList = FormField::find([
            'conditions' => 'admin_display=1 AND form_id=' . $formId,
            'order' => 'sequence ASC'
        ]);
        $this->view->form = $form;
        $this->view->formdataAddPower = $admin->checkPower('formdata', 'add');
        $this->view->formdataInfoPower = $admin->checkPower('formdata', 'info');
        $this->view->formdataDeletePower = $admin->checkPower('formdata', 'delete');
    }
    
    /**
     * @desc 添加
     * @author: ZhaoYang
     * @date: 2018年8月13日 下午9:31:38
     */
    public function addAction() {
        $formId = $this->request('form_id', 'absint', 0);
        $message = (new Validate())->addRules((new Form())->getRules(['id0']))->validate(['id' => $formId]);
        if(count($message)) {
            return $this->request->isPost() ? $this->sendJson('非法操作！') : $this->error('非法操作！');
        }
        $form = Form::findFirst($formId);
        if($form === false) {
            return $this->request->isPost() ? $this->sendJson('非法操作！') : $this->error('非法操作！');
        }
        if($this->request->isPost()) {
            $formData = new FormData($form->table);
            $addRes = $formData->add($this->post(null, false), $formId);
            if($addRes) {
                return $this->sendJson('添加成功！');
            }
            return $this->sendJson($formData->getMessages()[0]->getMessage(), 10001);
        }
        $formFieldList = FormField::find([
            'conditions' => 'form_id=' . $formId,
            'order' => 'sequence ASC'
        ]);
        $admin = new Admin();
        $translate = new Translate();
        $this->view->actionUrl = $this->url->get('formdata/add');
        $this->view->actionName = $translate->t('添加');
        $this->view->actionPower = $admin->checkPower('formdata', 'add');
        $this->view->jumpButton = $translate->t('继续添加');
        $this->view->action = 'add';
        $this->view->formFieldList = $formFieldList;
        $this->view->form = $form;
        $this->view->formData = null;
        $this->view->formdataIndexPower = $admin->checkPower('formdata', 'index');
        $this->view->pick('formdata/info');
        $this->view->setTemplateBefore('common');
    }
    
    /**
     * @desc 查看
     * @author: ZhaoYang
     * @date: 2018年8月27日 上午12:14:57
     */
    public function infoAction() {
        $formId = $this->get('form_id', 'absint', 0);
        $message = (new Validate())->addRules((new Form())->getRules(['id0']))->validate(['id' => $formId]);
        if(count($message)) {
            return $this->error('非法操作！');
        }
        $form = Form::findFirst($formId);
        if($form === false) {
            return $this->error('非法操作！');
        }
        $formData = (new FormData($form->table))::findFirst($this->get('id', 'absint', 0));
        if($formData === false) {
            return $this->error('该记录不存在！');
        }
        $formFieldList = FormField::find([
            'conditions' => 'form_id=' . $formId,
            'order' => 'sequence ASC'
        ]);
        $admin = new Admin();
        $translate = new Translate();
        $this->view->actionUrl = $this->url->get('formdata/edit');
        $this->view->actionName = $translate->t('修改');
        $this->view->actionPower = $admin->checkPower('formdata', 'edit');
        $this->view->jumpButton = $translate->t('查看修改');
        $this->view->action = 'edit';
        $this->view->formFieldList = $formFieldList;
        $this->view->form = $form;
        $this->view->formData = $formData;
        $this->view->formdataIndexPower = $admin->checkPower('formdata', 'index');
        $this->view->setTemplateBefore('common');
    }
    
    /**
     * @desc 修改
     * @author: ZhaoYang
     * @date: 2018年8月27日 上午12:15:06
     */
    public function editAction() {
        $formId = $this->post('form_id', 'absint', 0);
        $message = (new Validate())->addRules((new Form())->getRules(['id0']))->validate(['id' => $formId]);
        if(count($message)) {
            return $this->sendJson('非法操作！');
        }
        $form = Form::findFirst($formId);
        if($form === false) {
            return $this->sendJson('非法操作！');
        }
        $formData = new FormData($form->table);
        $editRes = $formData->edit($this->post(null, false));
        if($editRes) {
            return $this->sendJson('修改成功！');
        }
        return $this->sendJson($formData->getMessages()[0]->getMessage(), 10001);
    }
    
    /** 
     * @desc 删除 
     * @author ZhaoYang 
     * @date 2018年8月27日 下午5:15:37 
     */
    public function deleteAction() {
        $formId = $this->post('form_id', 'absint', 0);
        $id = $this->post('id', 'absint', 0);
        $message = (new Validate())->addRules((new Form())->getRules(['id0']))->validate(['id' => $formId]);
        if(count($message)) {
            return $this->sendJson('非法操作！');
        }
        $form = Form::findFirst($formId);
        if($form === false) {
            return $this->sendJson('非法操作！');
        }
        $formData = (new FormData($form->table))::findFirst($id);
        if($formData === false) {
            return $this->error('该记录不存在！');
        }
        $delRes = $formData->delete();
        return $delRes ? $this->sendJson('删除成功！') : $this->sendJson('删除失败！', 10001);
    }
    
}