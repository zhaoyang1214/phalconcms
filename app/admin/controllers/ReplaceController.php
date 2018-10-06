<?php
namespace App\Admin\Controllers;

use App\Admin\Models\Admin;
use App\Admin\Models\Translate;
use App\Admin\Models\Replace;
use Library\Tools\Paginator;
use Common\Validate;

class ReplaceController extends CommonController {

    /** 
     * @desc 列表 
     * @author ZhaoYang 
     * @date 2018年8月31日 下午1:55:13 
     */
    public function indexAction() {
        $replace = new Replace();
        $count = $replace->getAllowCount();
        $paginator = new Paginator($count);
        $replaceList = $replace->getAllowList($paginator->getLimit(), $paginator->getOffset());
        $admin = new Admin();
        $this->view->setTemplateBefore('common');
        $this->view->pageShow = $paginator->show();
        $this->view->replaceList = $replaceList;
        $this->view->replaceAddPower = $admin->checkPower('replace', 'add');
        $this->view->replaceInfoPower = $admin->checkPower('replace', 'info');
        $this->view->replaceDeletePower = $admin->checkPower('replace', 'delete');
    }
    
    /** 
     * @desc 添加 
     * @author ZhaoYang 
     * @date 2018年8月31日 下午1:55:28 
     */
    public function addAction() {
        if($this->request->isPost()) {
            $replace = new Replace();
            $addRes = $replace->add($this->post());
            if($addRes) {
                return $this->sendJson('添加成功！');
            }
            return $this->sendJson($replace->getMessages()[0]->getMessage(), 10001);
        }
        $admin = new Admin();
        $translate = new Translate();
        $this->view->actionUrl = $this->url->get('replace/add');
        $this->view->actionName = $translate->t('添加');
        $this->view->actionPower = $admin->checkPower('replace', 'add');
        $this->view->jumpButton = $translate->t('继续添加');
        $this->view->action = 'add';
        $this->view->replaceIndexPower = $admin->checkPower('replace', 'index');
        $this->view->setTemplateBefore('common');
        $this->view->pick('replace/info');
    }
    
   /** 
    * @desc 查看 
    * @author ZhaoYang 
    * @date 2018年8月31日 下午1:55:36 
    */
    public function infoAction() {
        $data['id'] = $this->get('id', 'absint', 0);
        $message = (new Validate())->addRules((new Replace())->getRules(['id0']))->validate($data);
        if(count($message)) {
            return $this->error('非法操作！');
        }
        $replace = Replace::findFirst($data['id']);
        $admin = new Admin();
        $translate = new Translate();
        $actionPower = $admin->checkPower('replace', 'edit');
        $actionName = $actionPower ? '修改' : '查看';
        $this->view->actionUrl = $this->url->get('replace/edit');
        $this->view->actionPower = $actionPower;
        $this->view->actionName = $translate->t($actionName);
        $this->view->jumpButton = $translate->t('查看修改');
        $this->view->action = 'edit';
        $this->view->replaceIndexPower = $admin->checkPower('replace', 'index');
        $this->view->replace = $replace;
        $this->view->setTemplateBefore('common');
    }
    
    /** 
     * @desc 修改 
     * @author ZhaoYang 
     * @date 2018年8月31日 下午1:55:43 
     */
    public function editAction() {
        $replace = new Replace();
        $editRes = $replace->edit($this->post());
        if ($editRes === false) {
            return $this->sendJson($replace->getMessages()[0]->getMessage(), 10001);
        }
        return $this->sendJson('修改成功！');
    }
    
    /** 
     * @desc 删除 
     * @author ZhaoYang 
     * @date 2018年8月31日 下午1:55:50 
     */
    public function deleteAction() {
        $id = $this->post('id', 'absint', 0);
        $replace = new Replace();
        $delRes = $replace->del($id);
        if($delRes === false) {
            return $this->sendJson($replace->getMessages()[0]->getMessage(), 10001);
        }
        return $this->sendJson('删除成功！');
    }
}