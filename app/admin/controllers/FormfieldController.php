<?php
namespace App\Admin\Controllers;

use Common\Validate;
use App\Admin\Models\Form;
use App\Admin\Models\FormField;
use Library\Tools\Paginator;
use App\Admin\Models\Admin;
use App\Admin\Models\Translate;

class FormfieldController extends CommonController {

    /**
     * @desc 字段列表
     * @author: ZhaoYang
     * @date: 2018年8月8日 下午10:57:55
     */
    public function indexAction() {
       $formId = $this->get('form_id', 'absint', 0);
       $message = (new Validate())->addRules((new Form())->getRules(['id0']))->validate(['id' => $formId]);
       if(count($message)) {
           return $this->error('非法操作！');
       }
       $count = FormField::count('form_id=' . $formId);
       $paginator = new Paginator($count);
       $formFieldList = FormField::find([
           'conditions' => 'form_id=' . $formId,
           'order' => 'sequence ASC',
           'limit' => $paginator->getLimit(),
           'offset' => $paginator->getOffset()
       ]);
       $admin = new Admin();
       $this->view->setTemplateBefore('common');
       $this->view->pageShow = $paginator->show();
       $this->view->formFieldList = $formFieldList;
       $this->view->form = Form::findFirst($formId);
       $this->view->formfieldAddPower = $admin->checkPower('formfield', 'add');
       $this->view->formfieldInfoPower = $admin->checkPower('formfield', 'info');
       $this->view->formfieldDeletePower = $admin->checkPower('formfield', 'delete');
    }

    /** 
     * @desc 添加 
     * @author ZhaoYang 
     * @date 2018年8月10日 下午3:29:16 
     */
    public function addAction() {
        $formField = new FormField();
        if($this->request->isPost()) {
            $data = $this->post();
            $data['form_id'] = $this->post('form_id', 'absint', 0);
            $data['type'] = $this->post('type', 'absint', 0);
            $data['property'] = $this->post('property', 'absint', 0);
            $data['len'] = $this->post('len', 'absint', 0);
            $data['decimal'] = $this->post('decimal', 'absint', 0);
            $data['sequence'] = $this->post('sequence', 'int!', 0);
            $data['is_must'] = $this->post('is_must', 'absint', 0);
            $data['is_unique'] = $this->post('is_unique', 'absint', 0);
            $data['admin_display'] = $this->post('admin_display', 'absint', 0);
            $data['admin_display_len'] = $this->post('admin_display_len', 'absint', 0);
            $addRes = $formField->add($data);
            if($addRes) {
                return $this->sendJson('添加成功！');
            }
            return $this->sendJson($formField->getMessages()[0]->getMessage(), 10001);
        }
        $admin = new Admin();
        $translate = new Translate();
        $this->view->actionUrl = $this->url->get('formfield/add');
        $this->view->actionName = $translate->t('添加');
        $this->view->actionPower = $admin->checkPower('formfield', 'add');
        $this->view->jumpButton = $translate->t('继续添加');
        $this->view->action = 'add';
        $this->view->formField = $formField;
        $this->view->formId = $this->get('form_id', 'absint', 0);
        $this->view->formIndexPower = $admin->checkPower('formfield', 'index');
        $this->view->pick('formfield/info');
    }
    
    /**
     * @desc 查看
     * @author: ZhaoYang
     * @date: 2018年8月12日 下午1:42:05
     */
    public function infoAction() {
        $data['id'] = $this->get('id', 'absint', 0);
        $message = (new Validate())->addRules((new FormField())->getRules(['id0']))->validate($data);
        if(count($message)) {
            return $this->error('非法操作！');
        }
        $formField = FormField::findFirst($data['id']);
        $admin = new Admin();
        $translate = new Translate();
        $actionPower = $admin->checkPower('formfield', 'edit');
        $actionName = $actionPower ? '修改' : '查看';
        $this->view->actionUrl = $this->url->get('formfield/edit');
        $this->view->actionPower = $actionPower;
        $this->view->actionName = $translate->t($actionName);
        $this->view->jumpButton = $translate->t('查看修改');
        $this->view->action = 'edit';
        $this->view->formIndexPower = $admin->checkPower('formfield', 'index');
        $this->view->formField = $formField;
        $this->view->formId = $formField->form_id;
    }
    
    /**
     * @desc 修改
     * @author: ZhaoYang
     * @date: 2018年8月12日 下午5:13:27
     */
    public function editAction() {
        $data = $this->post();
        $data['id'] = $this->post('id', 'absint', 0);
        $data['len'] = $this->post('len', 'absint', 0);
        $data['decimal'] = $this->post('decimal', 'absint', 0);
        $data['sequence'] = $this->post('sequence', 'int!', 0);
        $data['is_must'] = $this->post('is_must', 'absint', 0);
        $data['is_unique'] = $this->post('is_unique', 'absint', 0);
        $data['admin_display'] = $this->post('admin_display', 'absint', 0);
        $data['admin_display_len'] = $this->post('admin_display_len', 'absint', 0);
        $formField = new FormField();
        $editRes = $formField->edit($data);
        if($editRes) {
            return $this->sendJson('修改成功！');
        }
        return $this->sendJson($formField->getMessages()[0]->getMessage(), 10001);
    }
    
    /**
     * @desc 删除
     * @author: ZhaoYang
     * @date: 2018年8月12日 下午6:34:10
     */
    public function deleteAction() {
        $id = $this->post('id', 'absint', 0);
        $formField = new FormField();
        $delRes = $formField->del($id);
        if($delRes) {
            return $this->sendJson('删除成功！');
        }
        return $this->sendJson($formField->getMessages()[0]->getMessage(), 10001);
    }
}