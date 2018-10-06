<?php
namespace App\Admin\Controllers;

use App\Admin\Models\Admin;
use App\Admin\Models\AdminAuth;
use Common\Common;
use App\Admin\Models\Translate;
use App\Admin\Models\Language;
use App\Admin\Models\Category;
use App\Admin\Models\CategoryContent;
use App\Admin\Models\Tags;
use App\Admin\Models\Upload;

class IndexController extends CommonController {

    /**
     * @desc 后台首页
     * @author: ZhaoYang
     * @date: 2018年7月15日 下午3:51:12
     */
    public function indexAction() {
        $menuList = (new AdminAuth())->getAllowList(0);
        $adminInfo = $this->session->get('adminInfo');
        $this->view->menuList = $menuList;
        $this->view->username = $adminInfo['username'];
        $this->view->nicename = $adminInfo['nicename'];
    }

    /**
     * @desc 后台首页管理
     * @author: ZhaoYang
     * @date: 2018年7月15日 下午3:51:37
     */
    public function manageAction() {
        $adminAuthInfo = AdminAuth::getInfoByConAct($this->dispatcher->getControllerName(), $this->dispatcher->getActionName());
        $authList = [ ];
        if ($adminAuthInfo !== false) {
            $authList = (new AdminAuth())->getAllowList($adminAuthInfo->id);
            $this->view->authName = (new Translate())->t($adminAuthInfo->name);
        }
        $this->view->authList = $authList;
    }

    /**
     * @desc 后台首页
     * @author: ZhaoYang
     * @date: 2018年7月16日 下午11:01:24
     */
    public function homeAction() {
        $adminInfo = $this->session->get('adminInfo');
        $this->view->setTemplateBefore('common');
        $this->view->adminInfo = $adminInfo;
        $this->view->language = Language::findFirst(LANGUAGE_ID);
        $this->view->categoryCount = (new Category())->getAllowCount();
        $this->view->categoryContentCount = (new CategoryContent())->getAllowCount();
        $this->view->tagsCount = (new Tags())->getAllowCount();
        $this->view->uploadCount = (new Upload())->getAllowCount();
    }

    /**
     * @desc 环境信息
     * @author: ZhaoYang
     * @date: 2018年7月16日 下午11:01:53
     */
    public function toolSystemAction() {
    
    }

    /**
     * @desc 清除缓存
     * @author: ZhaoYang
     * @date: 2018年7月16日 上午12:37:20
     */
    public function cleanCacheAction() {
        $type = $this->get('type', 'int!');
        $services = $this->config->services;
        switch ($type) {
            case 0:
            case 1:
                Common::delDir($services->view_engine_volt->compiled_path);
                Common::delDir($services->view_engine_smarty->compile_dir);
                if ($type) {
                    break;
                }
            case 2:
                $this->viewCache->flush();
                if ($type) {
                    break;
                }
            case 3:
                $this->modelsCache->flush();
                if ($type) {
                    break;
                }
            case 4:
                $this->dataCache->flush();
                if ($type) {
                    break;
                }
            case 5:
                (new Admin())->clearAllModelsMetadata();
        }
        return $this->sendJson('清除缓存成功');
    }
}