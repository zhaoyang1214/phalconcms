<?php
namespace App\Admin\Models;

use Models\Form as ModelsForm;
use Common\Common;
use Common\Validate;
use Phalcon\Db\Column;

class Form extends ModelsForm {
    
    public function beforeCreate() {
        $this->no = md5(md5($this->name . microtime(true)));
    }
    
    /**
     * @desc 校验规则
     * @author: ZhaoYang
     * @date: 2018年8月6日 下午9:26:07
     */
    public function rules() {
        return [
            // 修改删除检查权限
            'id0' => ['id', 'callback', '非法操作', function($data) {
                if(!isset($data['id'])) {
                    return false;
                }
                $form = self::findFirst(intval($data['id']));
                $session = $this->getDI()->getSession();
                $adminGroupInfo = $session->get('adminGroupInfo');
                if($form === false || ($adminGroupInfo['language_power'] == 1 && $adminGroupInfo['language_id'] != $form->language_id)) {
                    return false;
                }
                $adminInfo = $session->get('adminInfo');
                if($adminGroupInfo['keep'] & 1 || !(empty($adminGroupInfo['form_ids']) || !in_array($data['id'], explode(',', $adminGroupInfo['form_ids'])))) {
                    return true;
                }
                return false;
            }],
            'name0' => ['name', 'stringlength', '表单名称长度必须大于1位|表单名称必须小于50位', [1, 50]],
            'name1' => ['name', 'callback', '表单名称已存在', function($data) {
                if(!isset($data['name'])) {
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
            'table0' => ['table', 'regex', '表名必须为2-20位数字字母下划线', '/\w{2,20}/'],
            'table1' => ['table', 'callback', '表名已存在', function($data) {
                if(!isset($data['table'])) {
                    return false;
                }
                $parameters = [
                    'columns' => 'table',
                    'conditions'  => 'table=:table:',
                    'bind' => [
                        'table' => $data['table']
                    ]
                ];
                if(isset($data['id']) && !empty($data['id'])){
                    $parameters['conditions']  .= ' AND id<>:id:';
                    $parameters['bind']['id'] = $data['id'];
                }
                return self::findFirst($parameters) ? false : true;
            }],
            'sort0' => ['sort', 'regex', '排序必须为0-20位数字字母下划线空格逗号', '/[\s\w,]{0,20}/'],
            'display0' => ['display', 'inclusionin', '前台表单选择错误', [0,1]],
            'page0' => ['page', 'digit', '分页必须为数字'],
            'alone_tpl0' => ['alone_tpl', 'inclusionin', '独立模板选择错误', [0,1]],
            'return_type0' => ['return_type', 'inclusionin', '前台提交返回类型选择错误', [0,1]],
            'is_captcha0' => ['is_captcha', 'inclusionin', '使用图片验证码选择错误', [0,1]],
        ];
    }
    
    /**
     * @desc 获取可访问的表单总数
     * @return int
     * @author: ZhaoYang
     * @date: 2018年8月5日 下午10:08:41
     */
    public function getAllowCount() {
        $session = $this->getDI()->getSession();
        $adminGroupInfo = $session->get('adminGroupInfo');
        $where = [ ];
        if (!($adminGroupInfo['keep'] & 1)) {
            if(empty($adminGroupInfo['form_ids'])){
                return [ ];
            }
            $where[] = 'id IN(' . $adminGroupInfo['form_ids'] . ')';
        }
        if($adminGroupInfo['language_power'] & 1){
            $where[] = 'language_id=' . $adminGroupInfo['language_id'];
        }
        $parameters = [ ];
        if(!empty($where)) {
            $parameters['conditions'] = implode(' AND ', $where);
        }
        return self::count($parameters);
    }
    
    /**
     * @desc 获取可访问的所有多功能表单
     * @param int $limit
     * @param int $offset
     * @return \Phalcon\Mvc\Model\ResultsetInterface 
     * @author: ZhaoYang
     * @date: 2018年7月29日 上午3:05:04
     */
    public function getAllowList(int $limit = null, int $offset = null) {
        $session = $this->getDI()->getSession();
        $adminGroupInfo = $session->get('adminGroupInfo');
        $where = [ ];
        if (!($adminGroupInfo['keep'] & 1)) {
            if(empty($adminGroupInfo['form_ids'])){
                return [ ];
            }
            $where[] = 'id IN(' . $adminGroupInfo['form_ids'] . ')';
        }
        if($adminGroupInfo['language_power'] & 1){
            $where[] = 'language_id=' . $adminGroupInfo['language_id'];
        }
        $parameters = [ ];
        if(!empty($where)) {
            $parameters['conditions'] = implode(' AND ', $where);
        }
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
     * @date: 2018年8月6日 下午9:02:49
     */
    public function add(array $data) {
        $data = Common::arraySlice(['name', 'table', 'sort', 'display', 'page', 'alone_tpl', 'tpl', 'where', 'return_type', 'return_msg', 'return_url', 'is_captcha'], $data);
        $message = (new Validate())->addRules(self::getRules(['name0', 'name1', 'table0', 'table1', 'sort0', 'display0', 'page0', 'alone_tpl0','return_type0','is_captcha0']))->validate($data);
        if (count($message)) {
            return $this->errorMessage($message);
        }
        $adminGroupInfo = $this->getDI()->getSession()->get('adminGroupInfo');
        $data['language_id'] = $adminGroupInfo['language_id'];
        $tableName = static::$_tablePrefix . self::$_tableName. '_data_' . $data['table'];
        try {
            $createTableRes = $this->getWriteConnection()->createTable($tableName, null, [
                'columns' => [
                    new Column('id', [
                        'type'          => Column::TYPE_INTEGER,
                        'size'          => 10,
                        'notNull'       => true,
                        'autoIncrement' => true,
                        'primary'       => true,
                        'unsigned'      => true,
                    ])
                ],
                'options' => [
                    'ENGINE' => 'InnoDB',
                    'AUTO_INCREMENT' => 1,
                    'TABLE_COLLATION' => 'utf8_general_ci',
                ]
            ]);
        } catch (\Exception $e) {
            $createTableRes = false;
            $errMsg = $e->getMessage();
        } finally {
            if($createTableRes === false) {
                return $this->errorMessage($errMsg ?? '创建 ' . $data['tables'] . ' 表失败');
            }
        }
        try {
            $createDataRes = $this->create($data);
        } catch (\Exception $e) {
            $createDataRes = false;
            $errMsg = $e->getMessage();
        } finally {
            if($createDataRes === false) {
                $this->getWriteConnection()->dropTable($tableName);
                return $this->errorMessage($errMsg ?? '添加失败！');
            }
        }
        return true;
    }
    
     /**
      * @desc 修改
      * @param array $data
      * @return: bool
      * @author: ZhaoYang
      * @date: 2018年8月8日 上午12:32:37
      */
    public function edit(array $data) {
        $data = Common::arraySlice(['id', 'sort', 'display', 'page', 'alone_tpl', 'tpl', 'where', 'return_type', 'return_msg', 'return_url', 'is_captcha'], $data);
        $message = (new Validate())->addRules(self::getRules(['id0', 'sort0', 'display0', 'page0', 'alone_tpl0','return_type0','is_captcha0']))->validate($data);
        if (count($message)) {
            return $this->errorMessage($message);
        }
        $form = self::findFirst($data['id']);
        $this->assign($form->toArray());
        return $this->update($data);
    }
    
    /**
     * @desc 删除
     * @param int $id 
     * @return bool
     * @author: ZhaoYang
     * @date: 2018年8月8日 上午12:57:40
     */
    public function del(int $id) {
        $message = (new Validate())->addRules(self::getRules(['id0']))->validate(['id' => $id]);
        if(count($message)) {
            return $this->errorMessage($message);
        }
        $form = self::findFirst($id);
        $delRes = $form->delete();
        if($delRes) {
            $this->getWriteConnection()->dropTable(static::$_tablePrefix . self::$_tableName. '_data_' . $form->table);
        }
        return $delRes;
    }
}