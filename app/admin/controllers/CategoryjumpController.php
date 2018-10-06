<?php
namespace App\Admin\Controllers;

use App\Admin\Models\Admin;
use App\Admin\Models\Translate;
use App\Admin\Models\Category;
use App\Admin\Models\Categoryjump;

class CategoryjumpController extends CommonController {

    /**
     * @desc 添加
     * @author: ZhaoYang
     * @date: 2018年9月15日 下午10:21:36
     */
    public function addAction() {
        if($this->request->isPost()) {
            $data = $this->post();
            $data['category_model_id'] = 3;
            $this->db->begin();
            $category = new Category();
            $addRes = $category->add($data);
            if($addRes === false) {
                return $this->sendJson($category->getMessages()[0]->getMessage(), 10001);
            }
            $data['category_id'] = $category->id;
            $categoryJump = new Categoryjump();
            $addRes = $categoryJump->add($data);
            if($addRes === false) {
                $this->db->rollback();
                return $this->sendJson($categoryJump->getMessages()[0]->getMessage(), 10001);
            }
            $this->db->commit();
            return $this->sendJson('添加成功！');
        }
        $admin = new Admin();
        $translate = new Translate();
        $category = new Category();
        $this->view->actionUrl = $this->url->get('categoryjump/add');
        $this->view->actionName = $translate->t('添加');
        $this->view->actionPower = $admin->checkPower('categoryjump', 'add');
        $this->view->jumpButton = $translate->t('继续添加');
        $this->view->action = 'add';
        $this->view->categoryIndexPower = $admin->checkPower('category', 'index');
        $this->view->categoryList = $category->getAllowList();
        $this->view->setTemplateBefore('common');
        $this->view->pick('categoryjump/info');
    }
    
    /**
     * @desc 查看
     * @author: ZhaoYang
     * @date: 2018年9月15日 下午10:21:43
     */
    public function infoAction() {
        $category = new Category();
        $info = $category->getInfoById($this->get('id', 'absint', 0));
        if ($info === false) {
            $this->error($category->getMessages()[0]->getMessage(), false);
        }
        $categoryJump = (new Categoryjump())->findFirst('category_id=' . $info->id);
        $admin = new Admin();
        $translate = new Translate();
        $category = new Category();
        $actionPower = $admin->checkPower('categoryjump', 'edit');
        $actionName = $actionPower ? '修改' : '查看';
        
        $this->view->actionUrl = $this->url->get('categoryjump/edit');
        $this->view->actionName = $translate->t($actionName);
        $this->view->actionPower = $actionPower;
        $this->view->jumpButton = $translate->t('查看修改');
        $this->view->action = 'edit';
        $this->view->categoryIndexPower = $admin->checkPower('category', 'index');
        $this->view->categoryList = $category->getAllowList();
        $this->view->category = $info;
        $this->view->categoryJump = $categoryJump;
        $this->view->setTemplateBefore('common');
        $this->view->pick('categoryjump/info');
    }
    
    /**
     * @desc 修改
     * @author: ZhaoYang
     * @date: 2018年9月15日 下午10:21:49
     */
    public function editAction() {
        $data = $this->post();
        $this->db->begin();
        $category = new Category();
        $editRes = $category->edit($data);
        if ($editRes === false) {
            return $this->sendJson($category->getMessages()[0]->getMessage(), 10001);
        }
        $url = $this->post('url');
        $categoryJump = new Categoryjump();
        $editRes = $categoryJump->updateByCategoryId($category->id, $url);
        if ($editRes === false) {
            $this->db->rollback();
            return $this->sendJson($categoryJump->getMessages()[0]->getMessage(), 10001);
        }
        $this->db->commit();
        return $this->sendJson('修改成功！');
    }
    
    /**
     * @desc 删除 
     * @author: ZhaoYang
     * @date: 2018年9月15日 下午10:21:56
     */
    public function deleteAction() {
        set_time_limit(300);
        ignore_user_abort(true);
        $id = $this->post('id', 'absint', 0);
        $this->db->begin();
        $category = new Category();
        $delRes = $category->del($id);
        if($delRes === false) {
            return $this->sendJson($category->getMessages()[0]->getMessage(), 10001);
        }
        $categoryJump = Categoryjump::find('category_id=' . $id);
        $delRes = $categoryJump->delete();
        if($delRes === false) {
            $this->db->rollback();
            return $this->sendJson($categoryJump->getMessages()[0]->getMessage(), 10001);
        }
        $this->db->commit();
        return $this->sendJson('删除成功！');
    }
}