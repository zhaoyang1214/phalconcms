<?php
namespace App\Admin\Controllers;

use App\Admin\Models\Admin;
use App\Admin\Models\Translate;
use App\Admin\Models\CategoryModel;

class CategorymodelController extends CommonController {

    /**
     * @desc 列表
     * @author: ZhaoYang
     * @date: 2018年9月25日 下午11:00:18
     */
    public function indexAction() {
        $admin = new Admin();
        $this->view->setTemplateBefore('common');
        $this->view->categoryModelList = CategoryModel::find();
        $this->view->categorymodelInfoPower = $admin->checkPower('categorymodel', 'info');
    }
    
    public function infoAction() {
        $id = $this->get('id', 'absint', 0);
        $categoryModel = CategoryModel::findFirst($id);
        if($categoryModel === false) {
            $this->error('模型不存在');
        }
        $admin = new Admin();
        $translate = new Translate();
        $actionPower = $admin->checkPower('categorymodel', 'edit');
        $actionName = $actionPower ? '修改' : '查看';
        $this->view->actionUrl = $this->url->get('categorymodel/edit');
        $this->view->actionPower = $actionPower;
        $this->view->actionName = $translate->t($actionName);
        $this->view->jumpButton = $translate->t('查看修改');
        $this->view->action = 'edit';
        $this->view->categoryModel = $categoryModel;
    }
    
    public function editAction() {
        $categoryModel = new CategoryModel();
        $editRes = $categoryModel->edit($this->post());
        if ($editRes === false) {
            return $this->sendJson($categoryModel->getMessages()[0]->getMessage(), 10001);
        }
        return $this->sendJson('修改成功！');
    }
    
}