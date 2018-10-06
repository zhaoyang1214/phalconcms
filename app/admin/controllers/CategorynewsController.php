<?php
namespace App\Admin\Controllers;

use App\Admin\Models\Admin;
use App\Admin\Models\Translate;
use App\Admin\Models\Expand;
use App\Admin\Models\Category;
use App\Admin\Models\CategoryContent;
use App\Admin\Models\CategoryContentData;

class CategorynewsController extends CommonController {

    /**
     * @desc 添加
     * @author ZhaoYang
     * @date 2018年9月9日 下午5:02:00
     */
    public function addAction() {
        if($this->request->isPost()) {
            $data = $this->post();
            $data['seo_content'] = $this->post('seo_content', false);
            $data['category_model_id'] = 1;
            $category = new Category();
            $addRes = $category->addAll($data);
            if($addRes) {
                return $this->sendJson('添加成功！');
            }
            return $this->sendJson($category->getMessages()[0]->getMessage(), 10001);
        }
        $admin = new Admin();
        $translate = new Translate();
        $category = new Category();
        $this->view->actionUrl = $this->url->get('categorynews/add');
        $this->view->actionName = $translate->t('添加');
        $this->view->actionPower = $admin->checkPower('categorynews', 'add');
        $this->view->jumpButton = $translate->t('继续添加');
        $this->view->action = 'add';
        $this->view->categoryIndexPower = $admin->checkPower('category', 'index');
        $this->view->categoryList = $category->getAllowList();
        $this->view->category = $category;
        $this->view->expandList = (new Expand())->getAllowList();
        $this->view->setTemplateBefore('common');
        $this->view->pick('categorynews/info');
    }
    
    /**
     * @desc 查看
     * @author ZhaoYang
     * @date 2018年9月9日 下午5:02:11
     */
    public function infoAction() {
        $category = new Category();
        $info = $category->getInfoById($this->get('id', 'absint', 0));
        if ($info === false) {
            $this->error($category->getMessages()[0]->getMessage(), false);
        }
        $admin = new Admin();
        $translate = new Translate();
        $category = new Category();
        $actionPower = $admin->checkPower('categorynews', 'edit');
        $actionName = $actionPower ? '修改' : '查看';
        
        $this->view->actionUrl = $this->url->get('categorynews/edit');
        $this->view->actionName = $translate->t($actionName);
        $this->view->actionPower = $actionPower;
        $this->view->jumpButton = $translate->t('查看修改');
        $this->view->action = 'edit';
        $this->view->categoryIndexPower = $admin->checkPower('category', 'index');
        $this->view->categoryList = $category->getAllowList();
        $this->view->category = $info;
        $this->view->expandList = (new Expand())->getAllowList();
        $this->view->setTemplateBefore('common');
        $this->view->pick('categorynews/info');
    }
    
    /** 
     * @desc 修改 
     * @author ZhaoYang 
     * @date 2018年9月9日 下午5:02:19 
     */
    public function editAction() {
        $data = $this->post();
        $data['seo_content'] = $this->post('seo_content', false);
        $category = new Category();
        $editRes = $category->edit($data);
        if ($editRes === false) {
            return $this->sendJson($category->getMessages()[0]->getMessage(), 10001);
        }
        return $this->sendJson('修改成功！');
    }
    
    /** 
     * @desc 删除 
     * @author ZhaoYang 
     * @date 2018年9月9日 下午5:02:23 
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
        $categoryContent = new CategoryContent();
        $categoryContentList = CategoryContent::find('category_id=' . $id)->toArray();
        if(!empty($categoryContentList)) {
            $delRes = $categoryContent->deleteByCategoryId($id);
            if($delRes === false) {
                $this->db->rollback();
                return $this->sendJson('删除失败！', 10001);
            }
            $categoryContentIds = array_column($categoryContentList, 'id');
            $delRes = (new CategoryContentData())->deleteByCategoryContentId(implode(',', $categoryContentIds));
            if($delRes === false) {
                $this->db->rollback();
                return $this->sendJson('删除失败！', 10001);
            }
        }
        $this->db->commit();
        return $this->sendJson('删除成功！');
    }
}