<?php
namespace App\Admin\Models;

use Models\AdminGroup as ModelsAdminGroup;
use Common\Common;
use Common\Validate;
use Phalcon\Mvc\Model\Exception;

class AdminGroup extends ModelsAdminGroup {
    
    /**
     * @desc 校验规则
     * @author: ZhaoYang
     * @date: 2018年7月29日 下午10:02:21
     */
    public function rules() {
        return [
            0 => ['id', 'callback', '非法操作', function($data) {
                if(!isset($data['id'])) {
                    return false;
                }
                $data['id'] = intval($data['id']);
                $oldAdminGroupInfo = self::findFirst($data['id']);
                if($oldAdminGroupInfo === false || !$oldAdminGroupInfo->checkRUDPower()) {
                    return false;
                }
                return true;
            }],
            1 => ['name', 'stringlength', '管理组名称长度必须大于2|管理组名称长度必须小于50', [2, 50]],
            2 => ['name', 'callback', '管理组名称已存在', function($data) {
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
            3 => ['grade', 'regex', '等级必须是两位数整数', '/^[1-9]\d$/'],
            4 => ['grade', 'callback', '管理等级错误', function($data) {
                if(!isset($data['grade'])) {
                    return false;
                }
                $session = $this->getDI()->getSession();
                $adminGroupInfo = $session->get('adminGroupInfo');
                if($data['grade'] <= $adminGroupInfo['grade']) {
                    return false;
                }
                return true;
            }],
            5 => ['keep', 'callback', '选择操作权限错误', function($data) {
                if(!isset($data['keep'])) {
                    return false;
                }
                $data['keep'] = intval($data['keep']);
                $session = $this->getDI()->getSession();
                $adminGroupInfo = $session->get('adminGroupInfo');
                if(($data['keep'] | $adminGroupInfo['keep']) != $adminGroupInfo['keep']) {
                    return false;
                }
                return true;
            }],
            6 => ['group_power', 'callback', '管理组列表权限错误', function($data) {
                if(!isset($data['group_power'])) {
                    return false;
                }
                $data['group_power'] = intval($data['group_power']);
                $session = $this->getDI()->getSession();
                $adminGroupInfo = $session->get('adminGroupInfo');
                if(($data['group_power'] | $adminGroupInfo['group_power']) != $adminGroupInfo['group_power']) {
                    return false;
                }
                return true;
            }],
            7 => ['admin_power', 'callback', '管理员列表权限错误', function($data) {
                if(!isset($data['admin_power'])) {
                    return false;
                }
                $data['admin_power'] = intval($data['admin_power']);
                $session = $this->getDI()->getSession();
                $adminGroupInfo = $session->get('adminGroupInfo');
                if(($data['admin_power'] | $adminGroupInfo['admin_power']) != $adminGroupInfo['admin_power']) {
                    return false;
                }
                return true;
            }],
            8 => ['language_power', 'callback', '是否受语言限制选择错误', function($data) {
                if(!isset($data['language_power']) || !in_array($data['language_power'], [0, 1], true)) {
                    return false;
                }
                $session = $this->getDI()->getSession();
                $adminGroupInfo = $session->get('adminGroupInfo');
                if($adminGroupInfo['language_power'] && !$data['language_power']) {
                    return false;
                }
                return true;
            }],
            9 => ['language_id', 'callback', '语言选择错误', function($data) {
                if(!isset($data['language_id'])) {
                    return false;
                }
                $data['language_id'] = intval($data['language_id']);
                $di = $this->getDI();
                $adminGroupInfo = $di->getSession()->get('adminGroupInfo');
                if($adminGroupInfo['language_power'] || (!$di->getConfig()->system->language_status && $data['language_id'] != 1)) {
                    return false;
                }
                $languageInfo = Language::findFirst(intval($data['language_id']));
                if($languageInfo === false || !$languageInfo->status) {
                    return false;
                }
                return true;
            }],
            10 => ['admin_auth_ids', 'callback', '功能操作权限选择错误', function($data) {
                if(!isset($data['admin_auth_ids']) || !is_string($data['admin_auth_ids'])) {
                    return false;
                }
                $session = $this->getDI()->getSession();
                $adminGroupInfo = $session->get('adminGroupInfo');
                if(empty($data['admin_auth_ids']) || (isset($data['keep']) && ($data['keep'] & 4))) {
                    return true;
                }
                $adminAuthIdArr = explode(',', $data['admin_auth_ids']);
                if($adminGroupInfo['keep'] & 4) {
                    $adminAuthList = (new AdminAuth())->getAllowList();
                    $allowAuthIdArr = array_column($adminAuthList, 'id');
                } else {
                    $allowAuthIdArr =explode(',', $adminGroupInfo['admin_auth_ids']);
                }
                if(!empty(array_diff($adminAuthIdArr, $allowAuthIdArr))) {
                    return false;
                }
                return true;
            }],
            11 => ['category_ids', 'callback', '栏目内容权限选择错误', function($data) {
                if(!isset($data['category_ids']) || !is_string($data['category_ids'])) {
                    return false;
                }
                $session = $this->getDI()->getSession();
                $adminGroupInfo = $session->get('adminGroupInfo');
                if(empty($data['category_ids']) || (isset($data['keep']) && ($data['keep'] & 2))) {
                    return true;
                }
                $categoryIdArr = explode(',', $data['category_ids']);
                if($adminGroupInfo['keep'] & 2) {
                    $categoryList = (new Category())->getAllowList();
                    $allowAuthIdArr = array_column($categoryList, 'id');
                } else {
                    $allowAuthIdArr =explode(',', $adminGroupInfo['category_ids']);
                }
                if(!empty(array_diff($categoryIdArr, $allowAuthIdArr))) {
                    return false;
                }
                return true;
            }],
            12 => ['form_ids', 'callback', '多功能表单权限选择错误', function($data) {
                if(!isset($data['form_ids']) || !is_string($data['form_ids'])) {
                    return false;
                }
                $session = $this->getDI()->getSession();
                $adminGroupInfo = $session->get('adminGroupInfo');
                if(empty($data['form_ids']) || (isset($data['keep']) && ($data['keep'] & 1))) {
                    return true;
                }
                $data['form_ids'] = trim($data['form_ids'], ',');
                $formIdArr = explode(',', $data['form_ids']);
                if($adminGroupInfo['keep'] & 1) {
                    $formList = (new Form())->getAllowList()->toArray();
                    $allowAuthIdArr = array_column($formList, 'id');
                } else {
                    $allowAuthIdArr =explode(',', $adminGroupInfo['form_ids']);
                }
                if(!empty(array_diff($formIdArr, $allowAuthIdArr))) {
                    return false;
                }
                return true;
            }],
        ];
    }
    
    /**
     * @desc 检测删改查权限（只能删改查下级）
     * @param int $grade 等级
     * @param int $languageId 语言id
     * @return bool
     * @author: ZhaoYang
     * @date: 2018年8月4日 下午4:02:58
     */
    public function checkRUDPower(int $grade = null, int $languageId = null) {
        if(is_null($grade) && isset($this->grade)) {
            $grade = $this->grade;
        } else {
            throw new Exception('grade不能为空');
        }
        if(is_null($languageId) && isset($this->language_id)) {
            $languageId = $this->language_id;
        } else {
            throw new Exception('languageId不能为空');
        }
        $session = $this->getDI()->getSession();
        $adminGroupInfo = $session->get('adminGroupInfo');
        if($adminGroupInfo['grade'] < $grade && ($adminGroupInfo['language_power'] == 0 || $adminGroupInfo['language_id'] == $languageId)) {
            return true;
        }
        return false;
    }
    
    
    /**
     * @desc 获取可访问管理组总数
     * @return int
     * @author: ZhaoYang
     * @date: 2018年8月2日 下午11:14:54
     */
    public function getAllowCount() {
        $session = $this->getDI()->getSession();
        $adminGroupInfo = $session->get('adminGroupInfo');
        $adminInfo = $session->get('adminInfo');
        $where = [ ];
        if ($adminGroupInfo['group_power'] & 4) {
            $where[ ] = 'grade>' . $adminGroupInfo['grade'];
        }
        if ($adminGroupInfo['group_power'] & 2) {
            $where[ ] = 'grade=' . $adminGroupInfo['grade'];
        } else if ($adminGroupInfo['group_power'] & 1) {
            $where[ ] = 'id=' . $adminGroupInfo['id'];
        }
        $where = implode(' OR ', $where);
        if ($adminGroupInfo['language_power']) {
            $where = "({$where}) AND language_id={$adminGroupInfo['language_id']}";
        }
        if(empty($where)) {
            $where = 'id=0';
        }
        return self::count($where);
    }
    
    /**
     * @desc 获取可访问的管理组列表
     * @param int $limit
     * @param int $offset
     * @return \Phalcon\Mvc\Model\ResultsetInterface 
     * @author: ZhaoYang
     * @date: 2018年8月2日 下午11:16:19
     */
    public function getAllowList(int $limit = null, int $offset = null) {
        $session = $this->getDI()->getSession();
        $adminGroupInfo = $session->get('adminGroupInfo');
        $adminInfo = $session->get('adminInfo');
        $where = [ ];
        if ($adminGroupInfo['group_power'] & 4) {
            $where[ ] = 'grade>' . $adminGroupInfo['grade'];
        }
        if ($adminGroupInfo['group_power'] & 2) {
            $where[ ] = 'grade=' . $adminGroupInfo['grade'];
        } else if ($adminGroupInfo['group_power'] & 1) {
            $where[ ] = 'id=' . $adminGroupInfo['id'];
        }
        $where = implode(' OR ', $where);
        if ($adminGroupInfo['language_power']) {
            $where = "({$where}) AND language_id={$adminGroupInfo['language_id']}";
        }
        if(empty($where)) {
            $where = 'id=0';
        }
        $parameters = [
            'conditions' => $where
        ];
        if(!is_null($limit)) {
            $parameters['limit'] = $limit;
        }
        if(!is_null($offset)) {
            $parameters['offset'] = $offset;
        }
        return self::find($parameters);
    }
    
    /**
     * @desc 添加
     * @param array $data 要插入的数据
     * @return bool
     * @author: ZhaoYang
     * @date: 2018年7月29日 下午7:19:53
     */
    public function add(array $data) {
        // 获取所需数据
        $data = Common::arraySlice(['name', 'grade', 'keep', 'group_power', 'admin_power', 'language_power', 'language_id', 'admin_auth_ids', 'category_ids', 'form_ids'], $data);
        if(!isset($data['language_id'])) {
            $adminGroupInfo = $this->getDI()->getSession()->get('adminGroupInfo');
            $data['language_id'] = $adminGroupInfo['language_id'];
        }
        // 校验数据
        $message = (new Validate())->addRules(self::getRules([1, 2, 3, 4]))->addRules(self::getRules([5, 6, 7, 8, 9, 10, 11, 12], false))->validate($data);
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
     * @date: 2018年7月31日 上午12:45:25
     */
    public function edit(array $data) {
        $message = (new Validate())->addRules(self::getRules([0, 1, 2, 3, 4]))->addRules(self::getRules([5, 6, 7, 8, 9, 10, 11, 12], false))->validate($data);
        if (count($message)) {
            return $this->errorMessage($message);
        }
        $adminGroup = self::findFirst($data['id']);
        $this->assign($adminGroup->toArray());
        return $this->update($data);
    }
    
    /**
     * @desc 获取下级管理组列表
     * @return \Phalcon\Mvc\Model\ResultsetInterface 
     * @author ZhaoYang
     * @date 2018年8月3日 上午11:02:47
     */
    public function getAllowLowGradeList() {
        $session = $this->getDI()->getSession();
        $adminGroupInfo = $session->get('adminGroupInfo');
        $where = 'grade>' . $adminGroupInfo['grade'];
        if ($adminGroupInfo['language_power']) {
            $where = "({$where}) AND language_id={$adminGroupInfo['language_id']}";
        }
        return self::find($where);
    }
    
}