<?php
namespace App\Admin\Controllers;

use App\Admin\Models\Admin;
use App\Admin\Models\Tags;
use Library\Tools\Paginator;
use App\Admin\Models\TagsGroup;

class TagsController extends CommonController {

    /**
     * @desc 列表 
     * @author: ZhaoYang
     * @date: 2018年9月1日 下午9:25:09
     */
    public function indexAction() {
        $sequence = $this->get('sequence', 'absint', 0);
        switch($sequence) {
            case 1:
                $order = 'click DESC';
                break;
            case 2:
                $order = 'click ASC';
                break;
            default:
                $order = 'App\Admin\Models\Tags.id DESC';
        }
        $conditions = [ ];
        $tagsGroupId = $this->get('tags_group_id', null, '-1');
        if($tagsGroupId != '-1') {
            $conditions[ ] = 'tags_group_id=' . abs($tagsGroupId);
        }
        $name = $this->get('name', null, '');
        if($name != '') {
            $conditions[ ] = " App\Admin\Models\Tags.name LIKE '%$name%'";
        }
        $tags = new Tags();
        $count = $tags->getAllowCount($conditions);
        $paginator = new Paginator($count);
        $tagsList = $tags->getAllowList($paginator->getLimit(), $paginator->getOffset(), $order, $conditions);
        $admin = new Admin();
        $this->view->setTemplateBefore('common');
        $this->view->pageShow = $paginator->show();
        $this->view->tagsList = $tagsList;
        $this->view->sequence = $sequence;
        $this->view->tags_group_id = $tagsGroupId;
        $this->view->name = $name;
        $this->view->tagsGroupList = (new TagsGroup())->getAllowList();
        $this->view->tagsGroupingPower = $admin->checkPower('tags', 'grouping');
        $this->view->tagsDeletePower = $admin->checkPower('tags', 'delete');
        $this->view->tagsgroupIndexPower = $admin->checkPower('tagsgroup', 'index');
        $this->view->tagsgroupAddPower = $admin->checkPower('tagsgroup', 'add');
    }
    
    /**
     * @desc 添加 
     * @author: ZhaoYang
     * @date: 2018年9月1日 下午9:25:27
     */
    /* public function addAction() {
        if($this->request->isPost()) {
            $tags = new Tags();
            $addRes = $tags->add($this->post());
            if($addRes) {
                return $this->sendJson('添加成功！');
            }
            return $this->sendJson($tags->getMessages()[0]->getMessage(), 10001);
        }
        $admin = new Admin();
        $translate = new Translate();
        $this->view->actionUrl = $this->url->get('tags/add');
        $this->view->actionName = $translate->t('添加');
        $this->view->actionPower = $admin->checkPower('tags', 'add');
        $this->view->jumpButton = $translate->t('继续添加');
        $this->view->action = 'add';
        $this->view->tagsIndexPower = $admin->checkPower('tags', 'index');
        $this->view->setTemplateBefore('common');
        $this->view->pick('tags/info');
    } */
    
    /** 
     * @desc 标签分组 
     * @author ZhaoYang 
     * @date 2018年8月31日 下午1:55:43 
     */
    public function groupingAction() {
        $tags = new Tags();
        $editRes = $tags->grouping($this->post());
        if ($editRes === false) {
            return $this->sendJson($tags->getMessages()[0]->getMessage(), 10001);
        }
        return $this->sendJson('修改成功！');
    }
    
    /**
     * @desc 删除 
     * @author: ZhaoYang
     * @date: 2018年9月1日 下午9:26:29
     */
    public function deleteAction() {
        $ids = $this->post('id');
        $idArr = explode(',', trim($ids, ','));
        $this->db->begin();
        $tags = new Tags();
        foreach ($idArr as $id) {
            $delRes = $tags->del(intval($id));
            if($delRes === false) {
                $this->db->rollback();
                return $this->sendJson($tags->getMessages()[0]->getMessage(), 10001);
            }
        }
        $this->db->commit();
        return $this->sendJson('删除成功！');
    }
}