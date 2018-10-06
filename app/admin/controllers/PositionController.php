<?php
namespace App\Admin\Controllers;

use App\Admin\Models\Admin;
use App\Admin\Models\Translate;
use App\Admin\Models\Position;
use Library\Tools\Paginator;
use Common\Validate;

class PositionController extends CommonController {

    /** 
     * @desc  列表  
     * @author ZhaoYang 
     * @date 2018年9月4日 上午10:19:33 
     */
    public function indexAction() {
        $position = new Position();
        $count = $position->getAllowCount();
        $paginator = new Paginator($count);
        $positionList = $position->getAllowList($paginator->getLimit(), $paginator->getOffset());
        $admin = new Admin();
        $this->view->setTemplateBefore('common');
        $this->view->pageShow = $paginator->show();
        $this->view->positionList = $positionList;
        $this->view->positionAddPower = $admin->checkPower('position', 'add');
        $this->view->positionInfoPower = $admin->checkPower('position', 'info');
        $this->view->positionDeletePower = $admin->checkPower('position', 'delete');
    }
    
    /** 
     * @desc 添加 
     * @author ZhaoYang 
     * @date 2018年9月4日 上午10:19:38 
     */
    public function addAction() {
        if($this->request->isPost()) {
            $position = new Position();
            $addRes = $position->add($this->post());
            if($addRes) {
                return $this->sendJson('添加成功！');
            }
            return $this->sendJson($position->getMessages()[0]->getMessage(), 10001);
        }
        $admin = new Admin();
        $translate = new Translate();
        $this->view->actionUrl = $this->url->get('position/add');
        $this->view->actionName = $translate->t('添加');
        $this->view->actionPower = $admin->checkPower('position', 'add');
        $this->view->jumpButton = $translate->t('继续添加');
        $this->view->action = 'add';
        $this->view->positionIndexPower = $admin->checkPower('position', 'index');
        $this->view->setTemplateBefore('common');
        $this->view->pick('position/info');
    }
    
   /** 
    * @desc 查看  
    * @author ZhaoYang 
    * @date 2018年9月4日 上午10:19:43 
    */
    public function infoAction() {
        $data['id'] = $this->get('id', 'absint', 0);
        $message = (new Validate())->addRules((new Position())->getRules(['id0']))->validate($data);
        if(count($message)) {
            return $this->error('非法操作！');
        }
        $position = Position::findFirst($data['id']);
        $admin = new Admin();
        $translate = new Translate();
        $actionPower = $admin->checkPower('position', 'edit');
        $actionName = $actionPower ? '修改' : '查看';
        $this->view->actionUrl = $this->url->get('position/edit');
        $this->view->actionPower = $actionPower;
        $this->view->actionName = $translate->t($actionName);
        $this->view->jumpButton = $translate->t('查看修改');
        $this->view->action = 'edit';
        $this->view->positionIndexPower = $admin->checkPower('position', 'index');
        $this->view->position = $position;
        $this->view->setTemplateBefore('common');
    }
    
    /** 
     * @desc 修改  
     * @author ZhaoYang 
     * @date 2018年9月4日 上午10:19:48 
     */
    public function editAction() {
        $position = new Position();
        $editRes = $position->edit($this->post());
        if ($editRes === false) {
            return $this->sendJson($position->getMessages()[0]->getMessage(), 10001);
        }
        return $this->sendJson('修改成功！');
    }
    
    /** 
     * @desc 删除 
     * @author ZhaoYang 
     * @date 2018年9月4日 上午10:19:54 
     */
    public function deleteAction() {
        $id = $this->post('id', 'absint', 0);
        $position = new Position();
        $delRes = $position->del($id);
        if($delRes === false) {
            return $this->sendJson($position->getMessages()[0]->getMessage(), 10001);
        }
        return $this->sendJson('删除成功！');
    }
}