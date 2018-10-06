<?php
namespace App\Admin\Controllers;

use App\Admin\Models\Admin;
use App\Admin\Models\Translate;
use App\Admin\Models\Fragment;
use Library\Tools\Paginator;
use Common\Validate;

class FragmentController extends CommonController {

    /**
     * @desc 列表
     * @author: ZhaoYang
     * @date: 2018年8月31日 上午12:06:26
     */
    public function indexAction() {
        $fragment = new Fragment();
        $count = $fragment->getAllowCount();
        $paginator = new Paginator($count);
        $fragmentList = $fragment->getAllowList($paginator->getLimit(), $paginator->getOffset());
        $admin = new Admin();
        $this->view->setTemplateBefore('common');
        $this->view->pageShow = $paginator->show();
        $this->view->fragmentList = $fragmentList;
        $this->view->fragmentAddPower = $admin->checkPower('fragment', 'add');
        $this->view->fragmentInfoPower = $admin->checkPower('fragment', 'info');
        $this->view->fragmentDeletePower = $admin->checkPower('fragment', 'delete');
    }
    
    /**
     * @desc 添加
     * @author: ZhaoYang
     * @date: 2018年8月31日 上午12:06:38
     */
    public function addAction() {
        if($this->request->isPost()) {
            $data = $this->post();
            $data['content'] = $this->post('content', false);
            $fragment = new Fragment();
            $addRes = $fragment->add($data);
            if($addRes) {
                return $this->sendJson('添加成功！');
            }
            return $this->sendJson($fragment->getMessages()[0]->getMessage(), 10001);
        }
        $admin = new Admin();
        $translate = new Translate();
        $this->view->actionUrl = $this->url->get('fragment/add');
        $this->view->actionName = $translate->t('添加');
        $this->view->actionPower = $admin->checkPower('fragment', 'add');
        $this->view->jumpButton = $translate->t('继续添加');
        $this->view->action = 'add';
        $this->view->fragmentIndexPower = $admin->checkPower('fragment', 'index');
        $this->view->setTemplateBefore('common');
        $this->view->pick('fragment/info');
    }
    
   /**
    * @desc 查看
    * @author: ZhaoYang
    * @date: 2018年8月31日 上午12:06:44
    */
    public function infoAction() {
        $data['id'] = $this->get('id', 'absint', 0);
        $message = (new Validate())->addRules((new Fragment())->getRules(['id0']))->validate($data);
        if(count($message)) {
            return $this->error('非法操作！');
        }
        $fragment = Fragment::findFirst($data['id']);
        $admin = new Admin();
        $translate = new Translate();
        $actionPower = $admin->checkPower('fragment', 'edit');
        $actionName = $actionPower ? '修改' : '查看';
        $this->view->actionUrl = $this->url->get('fragment/edit');
        $this->view->actionPower = $actionPower;
        $this->view->actionName = $translate->t($actionName);
        $this->view->jumpButton = $translate->t('查看修改');
        $this->view->action = 'edit';
        $this->view->fragmentIndexPower = $admin->checkPower('fragment', 'index');
        $this->view->fragment = $fragment;
        $this->view->setTemplateBefore('common');
    }
    
    /**
     * @desc 修改
     * @author: ZhaoYang
     * @date: 2018年8月31日 上午12:06:52
     */
    public function editAction() {
        $data = $this->post();
        $data['content'] = $this->post('content', false);
        $fragment = new Fragment();
        $editRes = $fragment->edit($data);
        if ($editRes === false) {
            return $this->sendJson($fragment->getMessages()[0]->getMessage(), 10001);
        }
        return $this->sendJson('修改成功！');
    }
    
    /**
     * @desc 删除
     * @author: ZhaoYang
     * @date: 2018年8月31日 上午12:07:00
     */
    public function deleteAction() {
        $id = $this->post('id', 'absint', 0);
        $fragment = new Fragment();
        $delRes = $fragment->del($id);
        if($delRes === false) {
            return $this->sendJson($fragment->getMessages()[0]->getMessage(), 10001);
        }
        return $this->sendJson('删除成功！');
    }
}