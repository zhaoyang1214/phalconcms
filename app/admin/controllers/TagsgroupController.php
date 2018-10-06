<?php
namespace App\Admin\Controllers;

use App\Admin\Models\Admin;
use App\Admin\Models\Translate;
use App\Admin\Models\TagsGroup;
use Library\Tools\Paginator;
use Common\Validate;

class TagsgroupController extends CommonController {

    /** 
     * @desc 列表  
     * @author ZhaoYang 
     * @date 2018年9月3日 下午5:32:35 
     */
    public function indexAction() {
        $tagsGroup = new TagsGroup();
        $count = $tagsGroup->getAllowCount();
        $paginator = new Paginator($count);
        $tagsGroupList = $tagsGroup->getAllowList($paginator->getLimit(), $paginator->getOffset());
        $admin = new Admin();
        $this->view->setTemplateBefore('common');
        $this->view->pageShow = $paginator->show();
        $this->view->tagsGroupList = $tagsGroupList;
        $this->view->tagsIndexPower = $admin->checkPower('tags', 'index');
        $this->view->tagsgroupAddPower = $admin->checkPower('tagsgroup', 'add');
        $this->view->tagsgroupInfoPower = $admin->checkPower('tagsgroup', 'info');
        $this->view->tagsgroupDeletePower = $admin->checkPower('tagsgroup', 'delete');
    }
    
    /** 
     * @desc 添加 
     * @author ZhaoYang 
     * @date 2018年9月3日 下午5:32:41 
     */
    public function addAction() {
        if($this->request->isPost()) {
            $tagsGroup = new TagsGroup();
            $addRes = $tagsGroup->add($this->post());
            if($addRes) {
                return $this->sendJson('添加成功！');
            }
            return $this->sendJson($tagsGroup->getMessages()[0]->getMessage(), 10001);
        }
        $admin = new Admin();
        $translate = new Translate();
        $this->view->actionUrl = $this->url->get('tagsgroup/add');
        $this->view->actionName = $translate->t('添加');
        $this->view->actionPower = $admin->checkPower('tagsgroup', 'add');
        $this->view->jumpButton = $translate->t('继续添加');
        $this->view->action = 'add';
        $this->view->tagsgroupIndexPower = $admin->checkPower('tagsgroup', 'index');
        $this->view->setTemplateBefore('common');
        $this->view->pick('tagsgroup/info');
    }
    
   /** 
    * @desc 查看  
    * @author ZhaoYang 
    * @date 2018年9月3日 下午5:32:46 
    */
    public function infoAction() {
        $data['id'] = $this->get('id', 'absint', 0);
        $message = (new Validate())->addRules((new TagsGroup())->getRules(['id0']))->validate($data);
        if(count($message)) {
            return $this->error('非法操作！');
        }
        $tagsGroup = TagsGroup::findFirst($data['id']);
        $admin = new Admin();
        $translate = new Translate();
        $actionPower = $admin->checkPower('tagsgroup', 'edit');
        $actionName = $actionPower ? '修改' : '查看';
        $this->view->actionUrl = $this->url->get('tagsgroup/edit');
        $this->view->actionPower = $actionPower;
        $this->view->actionName = $translate->t($actionName);
        $this->view->jumpButton = $translate->t('查看修改');
        $this->view->action = 'edit';
        $this->view->tagsgroupIndexPower = $admin->checkPower('tagsgroup', 'index');
        $this->view->tagsGroup = $tagsGroup;
        $this->view->setTemplateBefore('common');
    }
    
    /** 
     * @desc 修改  
     * @author ZhaoYang 
     * @date 2018年9月3日 下午5:32:51 
     */
    public function editAction() {
        $tagsGroup = new TagsGroup();
        $editRes = $tagsGroup->edit($this->post());
        if ($editRes === false) {
            return $this->sendJson($tagsGroup->getMessages()[0]->getMessage(), 10001);
        }
        return $this->sendJson('修改成功！');
    }
    
    /** 
     * @desc 删除  
     * @author ZhaoYang 
     * @date 2018年9月3日 下午5:32:55 
     */
    public function deleteAction() {
        $id = $this->post('id', 'absint', 0);
        $tagsGroup = new TagsGroup();
        $delRes = $tagsGroup->del($id);
        if($delRes === false) {
            return $this->sendJson($tagsGroup->getMessages()[0]->getMessage(), 10001);
        }
        return $this->sendJson('删除成功！');
    }
}