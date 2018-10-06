<?php
namespace App\Admin\Controllers;

use App\Admin\Models\Admin;
use App\Admin\Models\AdminAuth;
use App\Admin\Models\Translate;
use App\Admin\Models\Category;
use Library\Tools\Paginator;
use App\Admin\Models\CategoryModel;

class CategoryController extends CommonController {

   /** 
    * @desc 栏目管理 
    * @author ZhaoYang 
    * @date 2018年9月7日 上午9:36:01 
    */
    public function manageAction() {
        $adminAuthInfo = AdminAuth::getInfoByConAct($this->dispatcher->getControllerName(), $this->dispatcher->getActionName());
        $authList = [ ];
        if ($adminAuthInfo !== false) {
            $authList = (new AdminAuth())->getAllowList($adminAuthInfo->id);
            $this->view->authName = (new Translate())->t($adminAuthInfo->name);
        }
        $this->view->authList = $authList;
        $this->view->list = (new CategoryModel)->getAllowCategoryList();
    }
    
   /** 
    * @desc 列表 
    * @author ZhaoYang 
    * @date 2018年9月7日 上午9:36:11 
    */
    public function indexAction() {
        $category = new Category();
        $count = $category->getAllowCount();
        $paginator = new Paginator($count, 50);
        $categoryList = $category->getAllowList();
        $categoryList = array_slice($categoryList, $paginator->getOffset(), $paginator->getLimit());
        $categoryModelList = CategoryModel::find()->toArray();
        $admin = new Admin();
        $this->view->setTemplateBefore('common');
        $this->view->pageShow = $paginator->show();
        $this->view->categoryList = $categoryList;
        $this->view->categoryModelList = array_column($categoryModelList, null, 'id');
        $this->view->list = (new CategoryModel)->getAllowCategoryList();
        $this->view->categorySequencePower = $admin->checkPower('category', 'sequence');
    }
    
    /** 
     * @desc 排序 
     * @author ZhaoYang 
     * @date 2018年9月14日 上午10:34:04 
     */
    public function sequenceAction() {
        $id = $this->post('id', 'absint');
        $sequence = $this->post('sequence', 'int!');
        $category = new Category();
        $editRes = $category->updateSequenceById($id, $sequence);
        if ($editRes === false) {
            return $this->sendJson($category->getMessages()[0]->getMessage(), 10001);
        }
        return $this->sendJson('修改成功！');
    }
}