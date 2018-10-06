<?php
namespace App\Home\Controllers;

use Common\BaseController;
use App\Home\Models\SystemConfig;
use Phalcon\Config;
use App\Home\Models\Language;
use Phalcon\Application\Exception;
use App\Home\Models\Translate;
use App\Home\Models\Model;

class CommonController extends BaseController {
    
    public function beforeExecuteRoute($dispatcher) {
        $this->initConfig();
    }
    
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
        $this->config->services->view_engine_volt->compile_always = !(bool)$this->config->system->tpl_cache_on;
        if($this->config->system->mobile_open && $this->config->system->mobile_domain == $this->request->getHttpHost()) {
            $this->config->services->view->view_path = dirname($this->config->services->view->view_path) . DS . $this->config->system->mobile_views . DS;
            $this->config->services->url->static_base_uri = dirname($this->config->services->url->static_base_uri) . DS . $this->config->system->mobile_views . DS;
        }
        if ($this->config->system->language_status) {
            $this->config->system->theme = $languageInfo['theme'];
        }
        $this->config->services->view->view_path = dirname($this->config->services->view->view_path) . DS . $this->config->system->theme . DS;
        $this->config->services->url->static_base_uri = dirname($this->config->services->url->static_base_uri) . DS . $this->config->system->theme . DS;
        if (!is_dir($this->config->services->view->view_path)) {
            throw new Exception($this->config->services->view->view_path . '模板目录不存在');
        }
        $this->view->setViewsDir($this->config->services->view->view_path);
    }
    
    public function afterExecuteRoute() {
        if ($this->config->system->language_status) {
            $this->view->translateModel = new Translate();
        }
        $this->dispatcher->setControllerName(strtolower($this->dispatcher->getControllerName()));
        $this->view->model = new Model();
    }
    
    /**
     * @desc 重写父类方法
     * @author ZhaoYang
     * @date 2018年7月26日 上午9:19:40
     */
    protected function success(string $message, string $jumpUrl = null, bool $redirect = false, bool $externalRedirect = false) {
        return parent::success((new Translate())->t($message), $jumpUrl, $redirect, $externalRedirect);
    }
    
    /**
     * @desc 重写父类方法
     * @author ZhaoYang
     * @date 2018年7月26日 上午9:19:40
     */
    protected function error(string $message, string $jumpUrl = null, bool $redirect = false, bool $externalRedirect = false) {
        return parent::error((new Translate())->t($message), $jumpUrl, $redirect, $externalRedirect);
    }
    
    /**
     * @desc 重写父类方法
     * @author ZhaoYang
     * @date 2018年7月26日 上午9:19:40
     */
    protected function sendJson($responseData, int $status = 10000, int $jsonOptions = null, int $depth = 512) {
        if ($status != 10000 || is_string($responseData)) {
            $responseData = (new Translate())->t($responseData);
        }
        return parent::sendJson($responseData, $status, $jsonOptions, $depth);
    }
    
    protected function media(string $title = '', string $keywords = '', string $description = '') {
        $system = $this->config->system;
        $title .= (empty($title) ? '' : ' - ') . $system->sitename;
        if(empty($keywords)){
            $keywords = $system->keywords;
        }
        if(empty($description)){
            $description = $system->description;
        }
        return [
            'title' => $title,
            'keywords' => $keywords,
            'description' => $description
        ];
    }
}