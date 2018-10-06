<?php
namespace App\Admin\Models;

use Models\AdminAuth as ModelsAdminAuth;

class AdminAuth extends ModelsAdminAuth {
    
    // 默认权限
    const DEFAULT_ALLOW = [
        'Admin-login',
        'Test-test',
    ];
    
    // 登录后默认的权限
    const LOGGED_DEFAULT_ALLOW = [
        'Index-index',
        'Index-cleanCache',
        'Index-toolSystem',
        'Admin-loginOut',
        'ueditor-index',
        'Ueditor-getUpfileHtml',
        'Categorycontent-getKeywords',
    ];
    
     /**
      * @desc 根据controller和action获取单条记录
      * @param string $controller 控制器名
      * @param string $action 动作名
      * @return: Model
      * @author: ZhaoYang
      * @date: 2018年7月10日 上午1:17:16
      */
    public static function getInfoByConAct(string $controller, string $action, $cache = true, $lifetime = null) {
        return self::getInfo([
            'conditions' => 'controller=:controller: AND action=:action:',
            'bind' => [
                'controller' => $controller,
                'action' => $action
            ]
        ], $cache, $lifetime);
    }
    
    /**
     * @desc 根据pid获取管理员权限列表
     * @param int $pid 权限id
     * @return array
     * @author: ZhaoYang
     * @date: 2018年7月14日 下午11:21:41
     */
    public function getAllowList(int $pid = null) {
        $di = $this->getDI();
        $session = $di->getSession();
        $adminInfo = $session->get('adminInfo');
        $adminGroupInfo = $session->get('adminGroupInfo');
        $parameters = [
            'conditions' => 'status=1',
            'order' => 'sequence ASC'
        ];
        if(!is_null($pid)) {
            $parameters['conditions'] .= ' AND pid=' . $pid;
        }
        if (!($adminGroupInfo['keep'] & 4)) {
            if(empty($adminGroupInfo['admin_auth_ids'])){
                return [ ];
            }
            $parameters['conditions'] .= ' AND id IN(' . $adminGroupInfo['admin_auth_ids'] . ')';
        }
        $list = self::getList($parameters, true);
        if ($list === false) {
            $list = [ ];
        } else {
            $list = $list->toArray();
        }
        if ($di->getConfig()->system->language_status == 1) {
            foreach($list as $k => $v){
                $list[$k]['name'] = (new Translate())->t($v['name']);
            }
            /* $key = 'Admin_getAuthList' . md5(json_encode($list));
             $dataCache = $di->getDataCache();
             if($dataCache->exists($key, 86400000)){
             $list = $dataCache->get($key, 86400000);
             }else{
             foreach($list as $k => $v){
             $list[$k]['name'] = (new Translate())->t($v['name']);
             }
             $dataCache->save($key, $list, 86400000);
             } */
        }
        return $list;
    }
}