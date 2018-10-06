<?php
/**
 * @desc 公共控制器，鉴权及初始化后台配置等
 * @author: ZhaoYang
 * @date: 2018年7月7日 下午8:52:52
 */
namespace App\Admin\Controllers;

use Common\BaseController;
use App\Admin\Models\Language;
use App\Admin\Models\SystemConfig;
use Phalcon\Config;
use App\Admin\Models\Admin;
use Phalcon\Mvc\Application\Exception;
use App\Admin\Models\Translate;

class CommonController extends BaseController {

    public function beforeExecuteRoute($dispatcher) {
        $this->initConfig();
        $admin = new Admin();
        if (!$admin->checkPower($this->dispatcher->getControllerName(), $this->dispatcher->getActionName())) {
            if ($admin->checkIsLogged()) {
                $errorMessage = '您无访问该操作权限！';
                $errorCode = -10000;
                $jumpUrl = null;
            } else {
                $errorMessage = '由于您长时间未操作，登录已过期，请重新登陆！';
                $errorCode = -10001;
                $jumpUrl = 'admin/login';
            }
            if ($this->request->isAjax()) {
                $this->sendJson($errorMessage, $errorCode);
            } else {
                $this->error($errorMessage, $jumpUrl, true);
            }
            return false;
        }
    
    }

    /**
     * @desc 初始化配置
     * @author: ZhaoYang
     * @date: 2018年7月9日 上午1:01:12
     */
    private function initConfig() {
        $systemConfigInfo = SystemConfig::getInfoByLanguageId(1);
        if ($systemConfigInfo !== false) {
            $this->config->system->merge(new Config($systemConfigInfo->config));
        }
        $language = new Language();
        $languageInfo = $language->getNowLanguageInfo($this->request('language'));
        $systemConfigInfo = SystemConfig::getInfoByLanguageId($languageInfo['id']);
        if ($systemConfigInfo !== false) {
            $this->config->system->merge(new Config($systemConfigInfo->config));
        }
        if ($this->config->system->language_status) {
            $this->config->system->theme = $languageInfo['theme'];
            $this->config->services->view->view_path = dirname($this->config->services->view->view_path) . DS . $languageInfo['admin_theme'] . DS;
            $this->config->services->url->static_base_uri = dirname($this->config->services->url->static_base_uri) . DS . $languageInfo['admin_theme'] . DS;
        }
        if (!is_dir($this->config->services->view->view_path)) {
            throw new Exception($this->config->services->view->view_path . '模板目录不存在');
        }
        $this->view->setViewsDir($this->config->services->view->view_path);
    }

    public function afterExecuteRoute() {
        $this->view->runtime = $this->runtime();
        if ($this->config->system->language_status) {
            $this->view->translateModel = new Translate();
        }
        $this->dispatcher->setControllerName(strtolower($this->dispatcher->getControllerName()));
        $this->initCss();
        $this->initJs();
    }

    /**
     * @desc 初始化css
     * @author: ZhaoYang
     * @date: 2018年7月16日 下午11:07:24
     */
    protected function initCss() {
        $this->assets->addCss('css/base.css');
        $this->assets->addCss('css/style.css');
    }

    /**
     * @desc 初始化js
     * @author: ZhaoYang
     * @date: 2018年7月16日 下午11:07:46
     */
    protected function initJs() {
        $this->assets->addJs('js/jquery.js');
        $this->assets->addJs('js/duxui.js');
        $this->assets->addJs('dialog/jquery.artDialog.js?skin=default');
        $this->assets->addJs('dialog/plugins/iframeTools.js');
        $this->assets->addJs('js/common.js');
    }
}