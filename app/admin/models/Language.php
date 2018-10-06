<?php
namespace App\Admin\Models;

use Models\Language as ModelsLanguage;
use Common\Common;
use Common\Validate;

class Language extends ModelsLanguage {

    /** 
     * @desc 校验规则 
     * @return array 
     * @author ZhaoYang 
     * @date 2018年7月27日 上午9:31:48 
     */
    public function rules() {
        return [
            0 => ['id', 'digit', '该记录不存在'],
            1 => ['name', 'presenceof', '语言名称不能为空'],
            2 => ['name', 'callback', '语言名称已存在', function($data){
                if(!isset($data['name'])){
                    return false;
                }
                $parameters = [
                    'columns' => 'name',
                    'conditions'  => 'name=:name:',
                    'bind' => [
                        'name' => $data['name']
                    ]
                ];
                if(isset($data['id']) && !empty($data['id'])){
                    $parameters['conditions']  .= ' AND id<>:id:';
                    $parameters['bind']['id'] = $data['id'];
                }
                return self::findFirst($parameters) ? false : true;
            }],
            3 => ['zh_name', 'presenceof', '中文名称不能为空'],
            4 => ['zh_name', 'callback', '中文名称已存在', function($data){
                if(!isset($data['zh_name'])){
                    return false;
                }
                $parameters = [
                    'columns' => 'zh_name',
                    'conditions'  => 'zh_name=:zh_name:',
                    'bind' => [
                        'zh_name' => $data['zh_name']
                    ]
                ];
                if(isset($data['id']) && !empty($data['id'])){
                    $parameters['conditions']  .= ' AND id<>:id:';
                    $parameters['bind']['id'] = $data['id'];
                }
                return self::findFirst($parameters) ? false : true;
            }],
            5 => ['lang', 'regex', '语言标识只能为2-20个大小写字符和-', '/^[a-zA-Z][a-zA-Z-]{1,20}$/'],
            6 => ['lang', 'callback', '语言标识已存在', function($data){
                if(!isset($data['lang'])){
                    return false;
                }
                $parameters = [
                    'columns' => 'lang',
                    'conditions'  => 'lang=:lang:',
                    'bind' => [
                        'lang' => $data['lang']
                    ]
                ];
                if(isset($data['id']) && !empty($data['id'])){
                    $parameters['conditions']  .= ' AND id<>:id:';
                    $parameters['bind']['id'] = $data['id'];
                }
                return self::findFirst($parameters) ? false : true;
            }],
            7 => ['theme', 'regex', '前台模板主题只能为2-20个字母数字、-、_', '/^[a-zA-Z][\w-]{1,20}$/'],
            8 => ['admin_theme', 'regex', '后台模板主题只能为2-20个字母数字、-、_', '/^[a-zA-Z][\w-]{1,20}$/'],
            9 => ['admin_theme', 'callback', '后台模板主题不存在', function($data){
                if(is_dir(dirname($this->getDI()->getConfig()->services->view->view_path) . DS . $data['admin_theme'])){
                    return true;
                }
                return false;
            }],
            10 => ['domain', 'callback', '域名已存在', function($data){
                if(!isset($data['domain'])){
                    return false;
                }
                if(empty($data['domain'])){
                    return true;
                }
                $parameters = [
                    'columns' => 'domain',
                    'conditions'  => 'domain=:domain:',
                    'bind' => [
                        'domain' => $data['domain']
                    ]
                ];
                if(isset($data['id']) && !empty($data['id'])){
                    $parameters['conditions']  .= ' AND id<>:id:';
                    $parameters['bind']['id'] = $data['id'];
                }
                return self::findFirst($parameters) ? false : true;
            }],
            11 => ['status', 'callback', '请先在翻译驱动管理中开启驱动并支持该语言', function($data){
                if(!isset($data['status']) || !isset($data['id']) || !in_array($data['status'], array_keys($this->getStatus()))){
                    return false;
                }
                if(!$data['status']){
                    return true;
                }
                $translateDriverList = TranslateDriver::find('status=1');
                if($translateDriverList === false) {
                    return false;
                }
                $language = self::findFirst($data['id']);
                if ($language == false) {
                    return false;
                }
                foreach ($translateDriverList as $translateDriver) {
                    $config = $translateDriver->config;
                    if(isset($config['map']) && in_array($language->lang, $config['map'])){
                        return true;
                    }
                }
                return false;
            }],
            12 => ['status', 'callback', '有该国语言管理员，不允许关闭！', function ($data) {
                if(!isset($data['status']) || !isset($data['id']) || $data['id'] == 1){
                    return false;
                }
                if($data['status']){
                    return true;
                }
                $adminGroupList = AdminGroup::find('language_id=' . $data['id']);
                $adminGroupList = $adminGroupList->toArray();
                if(empty($adminGroupList)) {
                    return true;
                }
                $adminGroupIdArr = array_column($adminGroupList, 'id');
                $parameters = [
                    'columns' => 'id',
                    'conditions'  => 'status=1 AND admin_group_id IN(' . implode(',', $adminGroupIdArr) . ')'
                ];
                return Admin::findFirst($parameters) ? false : true;
            }],
        ];
    }

    /**
     * @desc 获取当前用户使用的语言信息
     * @param string $language 通过url获取的language变量
     * @return: Model
     * @author: ZhaoYang
     * @date: 2018年7月8日 下午8:20:00
     */
    public function getNowLanguageInfo(string $language = null) {
        $di = $this->getDI();
        $session = $di->getSession();
        // 从session中获取
        if ($session->has('languageInfo')) {
            $languageInfo = $session->get('languageInfo');
            goto SET_DEFINE_LANGUAGE;
        }
        // 从session中获取adminGroupInfo信息，间接获取
        if ($session->has('adminGroupInfo')) {
            $adminGroupInfo = $session->get('adminGroupInfo');
            $languageInfo = self::getInfo($adminGroupInfo['language_id'], true)->toArray();
            $session->set('languageInfo', $languageInfo);
            goto SET_DEFINE_LANGUAGE;
        }
        // 判断全局配置
        $systemConfigInfo = $di->getConfig()->system->toArray();
        if (isset($systemConfigInfo['language_status']) && $systemConfigInfo['language_status'] == 1) {
            // 优先根据url中的language参数识别
            if (!empty($language)) {
                $languageInfo = self::getInfoByLang($language, 1);
                if ($languageInfo !== false) {
                    goto LANGUAGE_INFO_TO_ARRAY;
                }
            }
            $request = $this->getDI()->getRequest();
            // 再根据域名识别
            $languageInfo = self::getInfoByDomain($request->getHttpHost(), 1);
            if ($languageInfo !== false) {
                goto LANGUAGE_INFO_TO_ARRAY;
            }
            // 根据浏览器提供的参数
            $httpAcceptLanguage = $request->getLanguages();
            if (isset($httpAcceptLanguage[0])) {
                $language = $httpAcceptLanguage[0]['language'];
                $languageInfo = self::getInfoByLang($language, 1);
                if ($languageInfo !== false) {
                    goto LANGUAGE_INFO_TO_ARRAY;
                }
            }
            if (isset($httpAcceptLanguage[1])) {
                $language = $httpAcceptLanguage[1]['language'];
                $languageInfo = self::getInfoByLang($language, 1);
                if ($languageInfo !== false) {
                    goto LANGUAGE_INFO_TO_ARRAY;
                }
            }
            if (isset($systemConfigInfo['lang'])) {
                // 根据设置的默认值识别
                $languageInfo = self::getInfoByLang($systemConfigInfo['lang'], 1);
                if ($languageInfo !== false) {
                    goto LANGUAGE_INFO_TO_ARRAY;
                }
            }
        }
        $languageInfo = self::getInfo(1, true);
        LANGUAGE_INFO_TO_ARRAY:
        $languageInfo = $languageInfo->toArray();
        SET_DEFINE_LANGUAGE:
        defined('LANGUAGE') || define('LANGUAGE', $languageInfo['lang']);
        defined('LANGUAGE_ID') || define('LANGUAGE_ID', $languageInfo['id']);
        return $languageInfo;
    }
    
    /**
     * @desc 获取状态
     * @param int $status 状态
     * @return string
     * @author: ZhaoYang
     * @date: 2018年7月24日 上午1:42:01
     */
    public function getStatus(int $status = null) {
        $statusArr = [ 
            0 => '禁用',
            1 => '启用'
        ];
        if (is_null($status)) {
            return $statusArr;
        }
        return $statusArr[$status] ?? '未知';
    }
    
    /**
     * @desc 添加
     * @param array $data 待添加的数据
     * @return bool
     * @author: ZhaoYang
     * @date: 2018年7月25日 下午10:31:20
     */
    public function add(array $data) {
        $data = Common::arraySlice(['name', 'zh_name', 'lang', 'theme', 'admin_theme', 'domain'], $data);
        $message = (new Validate())->addRules(self::getRules([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]))->validate($data);
        if (count($message)) {
            return $this->errorMessage($message);
        }
        return $this->create($data);
    }
    
    /**
     * @desc 修改
     * @param array $data 要修改的数据
     * @return bool
     * @author: ZhaoYang
     * @date: 2018年7月26日 上午12:42:12
     */
    public function edit(array $data) {
        $data = Common::arraySlice(['id', 'name', 'zh_name', 'theme', 'admin_theme', 'domain', 'status'], $data);
        $message = (new Validate())->addRules(self::getRules([0, 1, 2, 3, 4, 7, 8, 9, 10, 11, 12]))->validate($data);
        if (count($message)) {
            return $this->errorMessage($message);
        }
        $language = self::findFirst($data['id']);
        if ($language == false) {
            return $this->errorMessage('该记录不存在');
        }
        $this->assign($language->toArray());
        $result = $this->update($data);
        $result && $this->deleteCacheByPrefix(self::createCacheKey());
        return $result;
    }
}