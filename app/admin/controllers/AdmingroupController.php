<?php
namespace App\Admin\Controllers;

use App\Admin\Models\AdminGroup;
use Library\Tools\Paginator;
use App\Admin\Models\Admin;
use App\Admin\Models\Category;
use App\Admin\Models\Form;
use App\Admin\Models\AdminAuth;
use App\Admin\Models\Language;
use App\Admin\Models\Translate;

class AdmingroupController extends CommonController {

    /**
     * @desc 管理组浏览
     * @author: ZhaoYang
     * @date: 2018年7月27日 上午12:47:15
     */
    public function indexAction() {
        $adminGroup = new AdminGroup();
        $count = $adminGroup->getAllowCount();
        $paginator = new Paginator($count);
        $adminGroupList = $adminGroup->getAllowList($paginator->getLimit(), $paginator->getOffset());
        $admin = new Admin();
        $this->view->setTemplateBefore('common');
        $this->view->pageShow = $paginator->show();
        $this->view->adminGroupList = $adminGroupList;
        $this->view->adminGroupInfo = $this->session->get('adminGroupInfo');
        $this->view->admingroupAddPower = $admin->checkPower('Admingroup', 'add');
        $this->view->admingroupInfoPower = $admin->checkPower('Admingroup', 'info');
        $this->view->admingroupDeletePower = $admin->checkPower('Admingroup', 'delete');
    }

    /**
     * @desc 添加
     * @author: ZhaoYang
     * @date: 2018年7月28日 上午12:21:43
     */
    public function addAction() {
        if ($this->request->isPost()) {
            // 初步过滤、组装数据
            $data = $this->post();
            $data['grade'] = $this->post('grade', 'absint');
            if (isset($data['keep'])) {
                $data['keep'] = $this->post('keep', 'absint');
                $data['keep'] = is_array($data['keep']) ? array_sum($data['keep']) : 0;
            }
            if (isset($data['group_power'])) {
                $data['group_power'] = $this->post('group_power', 'absint');
                $data['group_power'] = is_array($data['group_power']) ? array_sum($data['group_power']) : 0;
            }
            if (isset($data['admin_power'])) {
                $data['admin_power'] = $this->post('admin_power', 'absint');
                $data['admin_power'] = is_array($data['admin_power']) ? array_sum($data['admin_power']) : 0;
            }
            if (isset($data['language_power'])) {
                $data['language_power'] = $this->post('language_power', 'absint');
            }
            if (isset($data['language_id'])) {
                $data['language_id'] = $this->post('language_id', 'absint');
            }
            if (isset($data['admin_auth_ids']) && is_string($data['admin_auth_ids'])) {
                $data['admin_auth_ids'] = trim($this->post('admin_auth_ids'), ',');
            }
            if (isset($data['category_ids']) && is_string($data['category_ids'])) {
                $data['category_ids'] = trim($this->post('category_ids'), ',');
            }
            if (isset($data['form_ids']) && is_string($data['form_ids'])) {
                $data['form_ids'] = trim($this->post('form_ids'), ',');
            }
            $adminGroup = new AdminGroup();
            $addRes = $adminGroup->add($data);
            if ($addRes === false) {
                return $this->sendJson($adminGroup->getMessages()[0]->getMessage(), 10001);
            }
            return $this->sendJson('添加成功！');
        }
        $adminAuthList = (new AdminAuth())->getAllowList();
        $cateGoryList = (new Category())->getAllowList();
        $formList = (new Form())->getAllowList()->toArray();
        $admin = new Admin();
        $translate = new Translate();
        $this->view->adminGroupInfo = $this->session->get('adminGroupInfo');
        $this->view->actionUrl = $this->url->get('Admingroup/add');
        $this->view->actionName = $translate->t('添加');
        $this->view->actionPower = $admin->checkPower('Admingroup', 'add');
        $this->view->jumpButton = $translate->t('继续添加');
        $this->view->action = 'add';
        $this->view->admingroupIndexPower = $admin->checkPower('Admingroup', 'index');
        $this->view->cateGoryTree = json_encode($cateGoryList, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $this->view->formTree = json_encode($formList, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $this->view->adminAuthTree = json_encode($adminAuthList, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $this->view->languageList = Language::getList('status=1');
        $this->view->setTemplateBefore('common');
        $this->view->pick('admingroup/info');
    }

    /**
     * @desc 查看
     * @author: ZhaoYang
     * @date: 2018年7月30日 下午10:07:22
     */
    public function infoAction() {
        $id = $this->get('id', 'absint', 0);
        $adminGroup = AdminGroup::findFirst('id=' . $id);
        if($adminGroup === false || !$adminGroup->checkRUDPower()) {
            return $this->error('该管理组不存在');
        }
        $allowAdminAuthIdArr = empty($adminGroup->admin_auth_ids) ? [ ] : explode(',', $adminGroup->admin_auth_ids);
        $allowCateGoryIdArr = empty($adminGroup->category_ids) ? [ ] : explode(',', $adminGroup->category_ids);
        $allowFormIdArr = empty($adminGroup->form_ids) ? [ ] : explode(',', $adminGroup->form_ids);
        $adminAuthList = (new AdminAuth())->getAllowList();
        foreach ($adminAuthList as &$adminAuth) {
            if (in_array($adminAuth['id'], $allowAdminAuthIdArr)) {
                $adminAuth['checked'] = true;
            }
        }
        $cateGoryList = (new Category())->getAllowList();
        foreach ($cateGoryList as &$cateGory) {
            if (in_array($cateGory['id'], $allowCateGoryIdArr)) {
                $cateGory['checked'] = true;
            }
        }
        $formList = (new Form())->getAllowList()->toArray();
        foreach ($formList as &$form) {
            if (in_array($form['id'], $allowFormIdArr)) {
                $form['checked'] = true;
            }
        }
        $admin = new Admin();
        $translate = new Translate();
        $actionPower = $admin->checkPower('Admingroup', 'edit');
        $actionName = $actionPower ? '修改' : '查看';
        $this->view->adminGroup = $adminGroup;
        $this->view->adminGroupInfo = $this->session->get('adminGroupInfo');;
        $this->view->actionUrl = $this->url->get('Admingroup/edit');
        $this->view->actionPower = $actionPower;
        $this->view->actionName = $translate->t($actionName);
        $this->view->jumpButton = $translate->t('查看修改');
        $this->view->action = 'edit';
        $this->view->admingroupIndexPower = $admin->checkPower('Admingroup', 'index');
        $this->view->cateGoryTree = json_encode($cateGoryList, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $this->view->formTree = json_encode($formList, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $this->view->adminAuthTree = json_encode($adminAuthList, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $this->view->languageList = Language::getList('status=1');
        $this->view->setTemplateBefore('common');
    }

    /**
     * @desc 修改
     * @author: ZhaoYang
     * @date: 2018年7月31日 上午12:43:16
     */
    public function editAction() {
        $data = $this->post();
        $data['id'] = $this->post('id', 'absint', 0);
        $data['grade'] = $this->post('grade', 'absint');
        if (isset($data['keep'])) {
            $data['keep'] = $this->post('keep', 'absint');
            $data['keep'] = is_array($data['keep']) ? array_sum($data['keep']) : 0;
        } else {
            $data['keep'] = 0;
        }
        if (isset($data['group_power'])) {
            $data['group_power'] = $this->post('group_power', 'absint');
            $data['group_power'] = is_array($data['group_power']) ? array_sum($data['group_power']) : 0;
        } else {
            $data['group_power'] = 0;
        }
        if (isset($data['admin_power'])) {
            $data['admin_power'] = $this->post('admin_power', 'absint');
            $data['admin_power'] = is_array($data['admin_power']) ? array_sum($data['admin_power']) : 0;
        } else {
            $data['admin_power'] = 0;
        }
        if (isset($data['language_power'])) {
            $data['language_power'] = $this->post('language_power', 'absint');
        }
        if (isset($data['language_id'])) {
            $data['language_id'] = $this->post('language_id', 'absint');
        }
        if (isset($data['admin_auth_ids']) && is_string($data['admin_auth_ids'])) {
            $data['admin_auth_ids'] = trim($this->post('admin_auth_ids'), ',');
        }
        if (isset($data['category_ids']) && is_string($data['category_ids'])) {
            $data['category_ids'] = trim($this->post('category_ids'), ',');
        }
        if (isset($data['form_ids']) && is_string($data['form_ids'])) {
            $data['form_ids'] = trim($this->post('form_ids'), ',');
        }
        $adminGroup = new AdminGroup();
        $editRes = $adminGroup->edit($data);
        if ($editRes === false) {
            return $this->sendJson($adminGroup->getMessages()[0]->getMessage(), 10001);
        }
        return $this->sendJson('修改成功！');
    }

    /**
     * @desc 删除
     * @author: ZhaoYang
     * @date: 2018年7月31日 下午8:54:00
     */
    public function deleteAction() {
        $id = $this->post('id', 'absint', 0);
        $adminGroup = AdminGroup::findFirst($id);
        if ($adminGroup === false || !$adminGroup->checkRUDPower()) {
            return $this->sendJson('您无权限操作该用户组', 10001);
        }
        $count = Admin::count('admin_group_id=' . $id);
        if ($count) {
            return $this->sendJson('该管理组下有管理员，无法删除！', 10002);
        }
        $delRes = $adminGroup->delete();
        return $delRes ? $this->sendJson('删除成功！') : $this->sendJson('删除失败！', 10001);
    }
}