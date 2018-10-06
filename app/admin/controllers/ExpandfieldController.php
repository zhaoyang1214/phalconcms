<?php
namespace App\Admin\Controllers;

use Common\Validate;
use App\Admin\Models\Expand;
use App\Admin\Models\ExpandField;
use Library\Tools\Paginator;
use App\Admin\Models\Admin;
use App\Admin\Models\Translate;

class ExpandfieldController extends CommonController {

    /** 
     * @desc 字段列表 
     * @author ZhaoYang 
     * @date 2018年8月30日 下午4:10:07 
     */
    public function indexAction() {
       $expandId = $this->get('expand_id', 'absint', 0);
       $message = (new Validate())->addRules((new Expand())->getRules(['id0']))->validate(['id' => $expandId]);
       if(count($message)) {
           return $this->error('非法操作！');
       }
       $count = ExpandField::count('expand_id=' . $expandId);
       $paginator = new Paginator($count);
       $expandFieldList = ExpandField::find([
           'conditions' => 'expand_id=' . $expandId,
           'order' => 'sequence ASC',
           'limit' => $paginator->getLimit(),
           'offset' => $paginator->getOffset()
       ]);
       $admin = new Admin();
       $this->view->setTemplateBefore('common');
       $this->view->pageShow = $paginator->show();
       $this->view->expandFieldList = $expandFieldList;
       $this->view->expand = Expand::findFirst($expandId);
       $this->view->expandfieldAddPower = $admin->checkPower('expandfield', 'add');
       $this->view->expandfieldInfoPower = $admin->checkPower('expandfield', 'info');
       $this->view->expandfieldDeletePower = $admin->checkPower('expandfield', 'delete');
    }

    /** 
     * @desc 添加 
     * @author ZhaoYang 
     * @date 2018年8月30日 下午4:13:01 
     */
    public function addAction() {
        $expandField = new ExpandField();
        if($this->request->isPost()) {
            $data = $this->post();
            $data['expand_id'] = $this->post('expand_id', 'absint', 0);
            $data['type'] = $this->post('type', 'absint', 0);
            $data['property'] = $this->post('property', 'absint', 0);
            $data['len'] = $this->post('len', 'absint', 0);
            $data['decimal'] = $this->post('decimal', 'absint', 0);
            $data['sequence'] = $this->post('sequence', 'int!', 0);
            $data['is_must'] = $this->post('is_must', 'absint', 0);
            $addRes = $expandField->add($data);
            if($addRes) {
                return $this->sendJson('添加成功！');
            }
            return $this->sendJson($expandField->getMessages()[0]->getMessage(), 10001);
        }
        $admin = new Admin();
        $translate = new Translate();
        $this->view->actionUrl = $this->url->get('expandfield/add');
        $this->view->actionName = $translate->t('添加');
        $this->view->actionPower = $admin->checkPower('expandfield', 'add');
        $this->view->jumpButton = $translate->t('继续添加');
        $this->view->action = 'add';
        $this->view->expandField = $expandField;
        $this->view->expandId = $this->get('expand_id', 'absint', 0);
        $this->view->expandIndexPower = $admin->checkPower('expandfield', 'index');
        $this->view->pick('expandfield/info');
    }
    
    /** 
     * @desc 查看 
     * @author ZhaoYang 
     * @date 2018年8月30日 下午4:13:08 
     */
    public function infoAction() {
        $data['id'] = $this->get('id', 'absint', 0);
        $message = (new Validate())->addRules((new ExpandField())->getRules(['id0']))->validate($data);
        if(count($message)) {
            return $this->error('非法操作！');
        }
        $expandField = ExpandField::findFirst($data['id']);
        $admin = new Admin();
        $translate = new Translate();
        $actionPower = $admin->checkPower('expandfield', 'edit');
        $actionName = $actionPower ? '修改' : '查看';
        $this->view->actionUrl = $this->url->get('expandfield/edit');
        $this->view->actionPower = $actionPower;
        $this->view->actionName = $translate->t($actionName);
        $this->view->jumpButton = $translate->t('查看修改');
        $this->view->action = 'edit';
        $this->view->expandIndexPower = $admin->checkPower('expandfield', 'index');
        $this->view->expandField = $expandField;
        $this->view->expandId = $expandField->expand_id;
    }
    
    /** 
     * @desc 修改 
     * @author ZhaoYang 
     * @date 2018年8月30日 下午4:13:15 
     */
    public function editAction() {
        $data = $this->post();
        $data['id'] = $this->post('id', 'absint', 0);
        $data['len'] = $this->post('len', 'absint', 0);
        $data['decimal'] = $this->post('decimal', 'absint', 0);
        $data['sequence'] = $this->post('sequence', 'int!', 0);
        $data['is_must'] = $this->post('is_must', 'absint', 0);
        $expandField = new ExpandField();
        $editRes = $expandField->edit($data);
        if($editRes) {
            return $this->sendJson('修改成功！');
        }
        return $this->sendJson($expandField->getMessages()[0]->getMessage(), 10001);
    }
    
    /** 
     * @desc 删除 
     * @author ZhaoYang 
     * @date 2018年8月30日 下午4:13:23 
     */
    public function deleteAction() {
        $id = $this->post('id', 'absint', 0);
        $expandField = new ExpandField();
        $delRes = $expandField->del($id);
        if($delRes) {
            return $this->sendJson('删除成功！');
        }
        return $this->sendJson($expandField->getMessages()[0]->getMessage(), 10001);
    }
}