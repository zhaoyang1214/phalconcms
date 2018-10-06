<?php
namespace App\Admin\Controllers;

use App\Admin\Models\Admin;
use App\Admin\Models\AdminLog;
use App\Admin\Models\AdminAuth;
use App\Admin\Models\Translate;
use App\Admin\Models\AdminGroup;
use Library\Tools\Paginator;
use Common\Validate;

class AdminController extends CommonController {

    /**
     * @desc 管理员登录
     * @author: ZhaoYang
     * @date: 2018年7月10日 下午9:54:00
     */
    public function loginAction() {
        if ($this->request->isAjax() && $this->request->isPost()) {
            $requestData = $this->post();
            $requestData['ip'] = $this->request->getClientAddress();
            $cache = $this->dataCache;
            $key = self::createCacheKey(__FUNCTION__ . $requestData['ip'], [ 
                $requestData['username']
            ]);
            $lifetime = strtotime('tomorrow') - time();
            // 如果使用的生存时间不是配置文件的时间，建议在get、save、exists等方法中加上设置时间
            if (!$cache->exists($key, $lifetime)) {
                $cache->save($key, 0, $lifetime);
            } else if ($cache->get($key, $lifetime) == 10) {
                return $this->sendJson('您今日已输错十次密码，账户已被冻结，次日解封！', 10003);
            }
            $admin = new Admin();
            $checkLoginRes = $admin->checkLogin($requestData);
            if ($checkLoginRes === false) {
                $cache->increment($key);
                if ($cache->get($key, $lifetime) == 5) {
                    return $this->sendJson('您今日已输错五次密码，每日只允许输错十次，十次后账户将被冻结，次日解封！', 10002);
                }
                return $this->sendJson($admin->getMessages()[0]->getMessage(), 10001);
            }
            $this->session->remove('languageInfo');
            $adminInfo = $checkLoginRes->toArray();
            $adminInfo['logintime'] = date('Y-m-d H:i:s');
            $adminInfo['ip'] = $requestData['ip'];
            $this->session->set('adminInfo', $adminInfo);
            $adminGroupInfo = AdminGroup::getInfo($adminInfo['admin_group_id']);
            $this->session->set('adminGroupInfo', $adminGroupInfo->toArray());
            $adminLog = new AdminLog();
            $adminLog->create([ 
                'admin_id' => $adminInfo['id'],
                'ip' => $requestData['ip']
            ]);
            return $this->sendJson('登陆成功！');
        }
    }

    /**
     * @desc 退出
     * @author: ZhaoYang
     * @date: 2018年7月15日 下午5:05:52
     */
    public function loginOutAction() {
        $this->session->remove('adminInfo');
        $this->session->remove('adminGroupInfo');
        $this->session->remove('languageInfo');
        $this->success('退出成功！');
        return $this->sendJson();
    }

    /**
     * @desc 管理员管理
     * @author: ZhaoYang
     * @date: 2018年7月27日 上午1:23:34
     */
    public function manageAction() {
        $adminAuthInfo = AdminAuth::getInfoByConAct($this->dispatcher->getControllerName(), $this->dispatcher->getActionName());
        $authList = [ ];
        if ($adminAuthInfo !== false) {
            $authList = (new AdminAuth())->getAllowList($adminAuthInfo->id);
            $this->view->authName = (new Translate())->t($adminAuthInfo->name);
        }
        $this->view->authList = $authList;
        $this->view->pick('index/manage');
    }

    /**
     * @desc 管理员列表
     * @author: ZhaoYang
     * @date: 2018年8月1日 下午8:54:58
     */
    public function indexAction() {
        $admin = new Admin();
        $count = $admin->getAllowCount();
        $paginator = new Paginator($count);
        $adminList = $admin->getAllowList($paginator->getLimit(true));
        $this->view->setTemplateBefore('common');
        $this->view->pageShow = $paginator->show();
        $this->view->adminList = $adminList;
        $this->view->admin = $admin;
        $this->view->adminGroupInfo = $this->session->get('adminGroupInfo');
        $this->view->adminInfo = $this->session->get('adminInfo');
        $this->view->adminAddPower = $admin->checkPower('Admin', 'add');
        $this->view->adminInfoPower = $admin->checkPower('Admin', 'info');
        $this->view->adminEditInfoPower = $admin->checkPower('Admin', 'editInfo');
        $this->view->adminDeletePower = $admin->checkPower('Admin', 'delete');
    }

    /**
     * @desc 添加
     * @author: ZhaoYang
     * @date: 2018年8月2日 下午9:08:18
     */
    public function addAction() {
        if ($this->request->isPost()) {
            $admin = new Admin();
            $addRes = $admin->add($this->post());
            if ($addRes === false) {
                return $this->sendJson($admin->getMessages()[0]->getMessage(), 10001);
            }
            return $this->sendJson('添加成功！');
        } else {
            $adminGroupList = (new AdminGroup())->getAllowLowGradeList();
            if (count($adminGroupList) == 0) {
                return $this->error('暂无可选择的管理组组，请先添加管理组！');
            }
            $admin = new Admin();
            $translate = new Translate();
            $this->view->actionUrl = $this->url->get('Admin/add');
            $this->view->actionName = $translate->t('添加');
            $this->view->actionPower = $admin->checkPower('Admin', 'add');
            $this->view->jumpButton = $translate->t('继续添加');
            $this->view->action = 'add';
            $this->view->adminIndexPower = $admin->checkPower('Admin', 'index');
            $this->view->adminGroupList = $adminGroupList;
            $this->view->setTemplateBefore('common');
            $this->view->pick('admin/info');
        }
    }

    /**
     * @desc 查看
     * @author: ZhaoYang
     * @date: 2018年8月3日 下午11:47:13
     */
    public function infoAction() {
        $data['id'] = $this->get('id', 'absint', 0);
        $message = (new Validate())->addRules((new Admin())->getRules(['id1']))->validate($data);
        if(count($message)) {
            return $this->error('非法操作！');
        }
        $admin = Admin::findFirst($data['id']);
        $adminGroupList = (new AdminGroup())->getAllowLowGradeList();
        if (count($adminGroupList) == 0) {
            return $this->error('暂无可选择的管理组组，请先添加管理组！');
        }
        $translate = new Translate();
        $actionPower = $admin->checkPower('Admin', 'edit');
        $actionName = $actionPower ? '修改' : '查看';
        $this->view->actionUrl = $this->url->get('Admin/edit');
        $this->view->actionPower = $actionPower;
        $this->view->actionName = $translate->t($actionName);
        $this->view->jumpButton = $translate->t('查看修改');
        $this->view->action = 'edit';
        $this->view->adminIndexPower = $admin->checkPower('Admin', 'index');
        $this->view->adminGroupList = $adminGroupList;
        $this->view->admin = $admin;
        $this->view->setTemplateBefore('common');
    }
    
    /**
     * @desc 修改
     * @author: ZhaoYang
     * @date: 2018年8月4日 上午12:58:07
     */
    public function editAction() {
        $admin = new Admin();
        $editRes = $admin->edit($this->post());
        if($editRes === false) {
            return $this->sendJson($admin->getMessages()[0]->getMessage());
        }
        return $this->sendJson('修改成功！');
    }

    /**
     * @desc 修改资料
     * @author: ZhaoYang
     * @date: 2018年8月4日 下午2:37:20
     */
    public function editInfoAction() {
        $admin = new Admin();
        if($this->request->isPost()) {
            $editRes = $admin->editInfo($this->post());
            if($editRes === false) {
                return $this->sendJson($admin->getMessages()[0]->getMessage());
            }
            return $this->sendJson('修改成功！');
        } else {
            $data['id'] = $this->get('id', 'absint', 0);
            $message = (new Validate())->addRules($admin->getRules(['id0']))->validate($data);
            if(count($message)) {
                return $this->error('非法操作！');
            }
            $admin = Admin::findFirst($data['id']);
            $translate = new Translate();
            $this->view->actionUrl = $this->url->get('Admin/editInfo');
            $this->view->actionPower = $admin->checkPower('Admin', 'editInfo');
            $this->view->actionName = $translate->t('修改资料');
            $this->view->jumpButton = $translate->t('查看修改');
            $this->view->action = 'editInfo';
            $this->view->adminIndexPower = $admin->checkPower('Admin', 'index');
            $this->view->admin = $admin;
            $this->view->setTemplateBefore('common');
            $this->view->pick('admin/info');
        }
    }
    
    /**
     * @desc 删除
     * @author: ZhaoYang
     * @date: 2018年8月4日 下午6:10:52
     */
    public function deleteAction() {
        $data['id'] = $this->post('id', 'absint', 0);
        $message = (new Validate())->addRules((new Admin())->getRules(['id1']))->validate($data);
        if(count($message)) {
            return $this->sendJson('非法操作！', 10001);
        }
        $admin = Admin::findFirst($data['id']);
        $delRes = $admin->delete();
        return $delRes ? $this->sendJson('删除成功！') : $this->sendJson('删除失败！', 10001);
    }
}