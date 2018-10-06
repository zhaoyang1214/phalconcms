<?php
namespace App\Admin\Models;

use Models\Admin as ModelsAdmin;
use Phalcon\Mvc\Model\Message;
use Common\Validate;
use Common\Common;


class Admin extends ModelsAdmin {

    private static $salt = 'phalcon';
    
    /**
     * @desc 定义过滤规则
     * @return array
     * @author: ZhaoYang
     * @date: 2018年7月25日 上午12:24:11
     */
    public function rules() {
        return [
            // 修改资料
            'id0' => ['id', 'callback', '非法操作', function($data) {
                if(!isset($data['id'])) {
                    return false;
                }
                $data['id'] = intval($data['id']);
                $admin = self::findFirst($data['id']);
                if($admin === false) {
                    return false;
                }
                $adminGroup = AdminGroup::findFirst($admin->admin_group_id);
                $session = $this->getDI()->getSession();
                $adminGroupInfo = $session->get('adminGroupInfo');
                $adminInfo = $session->get('adminInfo');
                if(!($adminGroupInfo['grade'] <= $adminGroup->grade && ($adminGroupInfo['language_power'] == 0 || $adminGroupInfo['language_id'] == $adminGroup->language_id))) {
                    return false;
                }
                if($adminGroup->grade < $adminGroupInfo['grade'] || ($adminGroupInfo['language_power'] && $adminGroupInfo['language_id'] != $adminGroup->language_id)){
                    return false;
                }
                if($adminGroupInfo['admin_power'] & 8 && $adminInfo['id'] == $data['id']) {
                    return true;
                }
                if($adminGroupInfo['admin_power'] & 16 && $adminGroupInfo['grade'] == $adminGroup->grade) {
                    return true;
                }
                if($adminGroupInfo['admin_power'] & 32 && $adminGroupInfo['grade'] < $adminGroup->grade) {
                    return true;
                }
                return false;
            }],
            // 设置
            'id1' => ['id', 'callback', '非法操作', function($data) {
                if(!isset($data['id'])) {
                    return false;
                }
                $data['id'] = intval($data['id']);
                $admin = self::findFirst($data['id']);
                if($admin === false) {
                    return false;
                }
                $adminGroup = AdminGroup::findFirst('id=' . $admin->admin_group_id);
                if(!$adminGroup->checkRUDPower()){
                    return false;
                }
                return true;
            }],
            'username0' => ['username', 'regex', '用户名或密码错误', '/^[\w@\.]{5,20}$/'],
            'username1' => ['username', 'regex', '姓名必须为6-20位数字、字母、 _、@、.', '/^[\w@\.]{6,20}$/'],
            'username2' => ['username', 'callback', '该用户已存在！', function($data) {
                if(!isset($data['username'])) {
                    return false;
                }
                $parameters = [
                    'columns' => 'username',
                    'conditions'  => 'username=:username:',
                    'bind' => [
                        'username' => $data['username']
                    ]
                ];
                if(isset($data['id']) && !empty($data['id'])){
                    $parameters['conditions']  .= ' AND id<>:id:';
                    $parameters['bind']['id'] = $data['id'];
                }
                return self::findFirst($parameters) ? false : true;
            }],
            'password0' => ['password', 'regex', '密码必须为6-20位数字、字母、 _、@、.', '/^[\w@\.]{6,20}$/'],
            'password20' => ['password2', 'confirmation', '两次密码必须一致', 'password'],
            'nicename0' => ['nicename', 'stringlength', '昵称长度必须大于1|昵称长度必须小于20', [1,20]],
            'status0' => ['status', 'inclusionin', '状态选择错误', [0, 1]],
            // 修改的时候使用
            'status1' => ['status', 'callback', '请先开启多国语言和该管理员所属语言', function($data) {
                if(!isset($data['status'])) {
                    return false;
                }
                if($data['status'] == 0) {
                    return true;
                }
                if(isset($data['admin_group_id'])) {
                    find_admin_group:
                    $adminGroup = AdminGroup::findFirst(intval($data['admin_group_id']));
                    if($adminGroup === false) {
                        return false;
                    }
                    if($adminGroup->language_id == 1) {
                        return true;
                    }
                } else if(isset($data['id'])) {
                    $admin = self::findFirst(intval($data['id']));
                    if($admin === false) {
                        return false;
                    }
                    $data['admin_group_id'] = $admin->admin_group_id;
                    goto find_admin_group;
                } else {
                    return false;
                }
                $di = $this->getDI();
                $systemConfig = $di->getConfig()->system;
                if($systemConfig->language_status == 0) {
                    return false;
                }
                $language = Language::findFirst($adminGroup->language_id);
                if($language === false || $language->status == 0) {
                    return false;
                }
                return true;
            }],
            'admin_group_id0' => ['admin_group_id', 'callback', '管理员组选择错误', function ($data) {
                if(!isset($data['admin_group_id'])) {
                    return false;
                }
                $adminGroupList = (new AdminGroup())->getAllowLowGradeList()->toArray();
                $adminGroupIdArr = array_column($adminGroupList, 'id');
                if(in_array($data['admin_group_id'], $adminGroupIdArr)) {
                    return true;
                }
                return false;
            }],
        ];
    }
    
    public function beforeCreate() {
        $this->regtime = date('Y-m-d H:i:s');
        $this->password = md5(self::$salt . md5($this->password));
    }
    
    public function beforeUpdate() {
        if($this->hasChanged('password')) {
            $this->password = md5(self::$salt . md5($this->password));
        }
    }
    
    /**
     * @desc 校验操作权限
     * @param string $controllerName 控制器名称
     * @param string $actionName 方法名称
     * @return: bool
     * @author: ZhaoYang
     * @date: 2018年7月10日 上午1:01:27
     */
    public function checkPower(string $controllerName, string $actionName) {
        $controllerName = strtolower($controllerName);
        $actionName = strtolower($actionName);
        $defaultAllow = array_map('strtolower', AdminAuth::DEFAULT_ALLOW);
        if (in_array($controllerName . '-' . $actionName, $defaultAllow)) {
            return true;
        }
        $session = $this->getDI()->getSession();
        if ($session->has('adminInfo')) {
            $loggedDefaultAllow = array_map('strtolower', AdminAuth::LOGGED_DEFAULT_ALLOW);
            if (in_array($controllerName . '-' . $actionName, $loggedDefaultAllow)) {
                return true;
            }
            $adminInfo = $session->get('adminInfo');
            $adminGroupInfo = $session->get('adminGroupInfo');
            if ($adminGroupInfo['keep'] & 4) {
                return true;
            }
            $adminAuthInfo = AdminAuth::getInfoByConAct($controllerName, $actionName);
            if ($adminAuthInfo && in_array($adminAuthInfo->id, explode(',', $adminGroupInfo['admin_auth_ids']))) {
                return true;
            }
        }
        return false;
    }

    /**
     * @desc 检测用是否登录
     * @return: bool
     * @author: ZhaoYang
     * @date: 2018年7月10日 下午8:08:04
     */
    public function checkIsLogged() {
        $session = $this->getDI()->getSession();
        if ($session->has('adminInfo')) {
            return true;
        }
        return false;
    }
    
     /**
      * @desc 校验登录
      * @param array $data 
      * string username 用户名
      * string password 密码
      * @return: bool|Model
      * @author: ZhaoYang
      * @date: 2018年7月14日 下午1:23:37
      */
    public function checkLogin(array $data) {
        $message = (new Validate())->addRules(self::getRules(['username0', 'password0']))->validate($data);
        if (count($message)) {
            return $this->errorMessage('用户名或密码错误');
        }
        $info = self::getInfo([ 
            'conditions' => 'username=:username:',
            'bind' => [ 
                'username' => $data['username']
            ]
        ]);
        if ($info === false || $info->password != md5(self::$salt . md5($data['password']))) {
            return $this->errorMessage('用户名或密码错误');
        }
        if ($info->status != 1) {
            return $this->errorMessage('账户已被禁用！');
        }
        return $info;
    }
    
    /**
     * @desc 获取可访问管理员总数
     * @author: ZhaoYang
     * @date: 2018年8月1日 下午11:30:36
     */
    public function getAllowCount() {
        $session = $this->getDI()->getSession();
        $adminGroupInfo = $session->get('adminGroupInfo');
        $adminInfo = $session->get('adminInfo');
        $phql = 'SELECT COUNT(a.id) AS num
                 FROM App\Admin\Models\Admin AS a
                 LEFT JOIN App\Admin\Models\AdminGroup AS b ON a.admin_group_id=b.id';
        $where = [ ];
        if($adminGroupInfo['admin_power'] & 4) {
            $where[] = 'b.grade>' . $adminGroupInfo['grade'];
        }
        if($adminGroupInfo['admin_power'] & 2) {
            $where[] = 'b.grade=' . $adminGroupInfo['grade'];
        } else if($adminGroupInfo['admin_power'] & 1) {
            $where[] = 'a.id=' . $adminInfo['id'];
        }
        $where = implode(' OR ', $where);
        if(empty($where)) {
            $where = 'a.id=0';
        } else if($adminGroupInfo['language_power'] & 1){
            $where = "({$where}) AND language_id={$adminGroupInfo['language_id']}";
        }
        $phql .= ' WHERE ' . $where;
        $res = self::getModelsManager()->executeQuery($phql)->getFirst();
        return $res->num;
    }
    
    /**
     * @desc 获取可访问管理员列表
     * @author: ZhaoYang
     * @date: 2018年8月1日 下午9:06:02
     */
    public function getAllowList(string $limit = null) {
        $session = $this->getDI()->getSession();
        $adminGroupInfo = $session->get('adminGroupInfo');
        $adminInfo = $session->get('adminInfo');
        $phql = 'SELECT a.id,a.username,a.nicename,a.regtime,a.status,b.name AS group_name,b.grade 
                 FROM App\Admin\Models\Admin AS a 
                 LEFT JOIN App\Admin\Models\AdminGroup AS b ON a.admin_group_id=b.id';
        $where = [ ];
        if ($adminGroupInfo['admin_power'] & 4) {
            $where[] = 'b.grade>' . $adminGroupInfo['grade'];
        }
        if ($adminGroupInfo['admin_power'] & 2) {
            $where[] = 'b.grade=' . $adminGroupInfo['grade'];
        } else if ($adminGroupInfo['admin_power'] & 1) {
            $where[] = 'a.id=' . $adminInfo['id'];
        }
        $where = implode(' OR ', $where);
        if (empty($where)) {
            $where = 'a.id=0';
        } else if($adminGroupInfo['language_power'] & 1){
            $where = "({$where}) AND language_id={$adminGroupInfo['language_id']}";
        }
        $phql .= ' WHERE ' . $where;
        if (!is_null($limit)) {
            $phql .= ' LIMIT ' . $limit;
        }
        return self::getModelsManager()->executeQuery($phql);
    }
    
    /**
     * @desc 获取状态
     * @author: ZhaoYang
     * @date: 2018年8月2日 上午12:07:23
     */
    public function getStatus(int $status = null) {
        $statusArr = [
            0 => '禁用',
            1 => '正常'
        ];
        if (is_null($status)) {
            return $statusArr;
        }
        return $statusArr[$status] ?? '未知';
    }
    
    /** 
     * @desc 添加 
     * @author ZhaoYang 
     * @date 2018年8月3日 上午11:14:00 
     */
    public function add(array $data) {
        $data = Common::arraySlice(['username', 'password', 'password2', 'nicename', 'status', 'admin_group_id'], $data);
        $message = (new Validate())->addRules(self::getRules(['username1', 'username2', 'password0', 'password20', 'nicename0', 'status0', 'admin_group_id0']))->validate($data);
        if (count($message)) {
            return $this->errorMessage($message);
        }
        return $this->create($data);
    }
    
    /**
     * @desc 修改
     * @author: ZhaoYang
     * @date: 2018年8月4日 上午12:59:21
     */
    public function edit(array $data) {
        $data = Common::arraySlice(['id', 'admin_group_id', 'username', 'nicename', 'status'], $data, true);
        $message = (new Validate())->addRules(self::getRules(['id1', 'username1', 'username2', 'nicename0', 'status0', 'status1', 'admin_group_id0']))->validate($data);
        if(count($message)) {
            return $this->errorMessage($message);;
        }
        $admin = self::findFirst($data['id']);
        $adminArr = $admin->toArray();
        $this->assign($adminArr);
        // 更新的时候使用了快照对照
        $this->setSnapshotData($adminArr);
        return $this->update($data);
    }
    
    /**
     * @desc 修改资料
     * @author: ZhaoYang
     * @date: 2018年8月4日 下午5:55:11
     */
    public function editInfo(array $data) {
        $data = Common::arraySlice(['id', 'password', 'password2', 'nicename'], $data);
        if(empty($data['password'])) {
            unset($data['password']);
            unset($data['password2']);
        }
        $message = (new Validate())->addRules(self::getRules(['id0', 'nicename0']))->addRules(self::getRules(['id0', 'nicename0'], false))->validate($data);
        if(count($message)) {
            return $this->errorMessage($message);;
        }
        $admin = self::findFirst($data['id']);
        $adminArr = $admin->toArray();
        $this->assign($adminArr);
        $this->setSnapshotData($adminArr);
        return $this->update($data);
    }
}