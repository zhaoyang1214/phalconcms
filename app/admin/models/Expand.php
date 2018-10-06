<?php
namespace App\Admin\Models;

use Models\Expand as ModelsExpand;
use Common\Common;
use Common\Validate;
use Phalcon\Db\Column;
use Phalcon\Db\Index;

class Expand extends ModelsExpand {
    
    /** 
     * @desc 规则 
     * @author ZhaoYang 
     * @date 2018年8月29日 下午6:00:56 
     */
    public function rules() {
        return [
            'id0' => ['id', 'callback', '非法操作', function($data) {
                if(!isset($data['id'])) {
                    return false;
                }
                $expand = self::findFirst(intval($data['id']));
                $session = $this->getDI()->getSession();
                $adminGroupInfo = $session->get('adminGroupInfo');
                if($expand === false || ($adminGroupInfo['language_power'] == 1 && $adminGroupInfo['language_id'] != $expand->language_id)) {
                    return false;
                }
                return true;
            }],
            'name0' => ['name', 'stringlength', '模型名称长度必须大于1位|模型名称必须小于50位', [1, 50]],
            'name1' => ['name', 'callback', '模型名称已存在', function($data) {
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
        ];
    }
    
    /** 
     * @desc 获取可访问扩展模型数量 
     * @return int
     * @author ZhaoYang 
     * @date 2018年8月28日 下午4:23:15 
     */
    public function getAllowCount() {
        $session = $this->getDI()->getSession();
        $adminGroupInfo = $session->get('adminGroupInfo');
        $parameters = [ ];
        if($adminGroupInfo['language_power'] & 1){
            $parameters['conditions'] = 'language_id=' . $adminGroupInfo['language_id'];
        }
        return self::count($parameters);
    }
    
    /** 
     * @desc 获取可访问扩展模型列表 
     * @author ZhaoYang 
     * @date 2018年8月28日 下午4:25:29 
     */
    public function getAllowList(int $limit = null, int $offset = null) {
        $session = $this->getDI()->getSession();
        $adminGroupInfo = $session->get('adminGroupInfo');
        $parameters = [ ];
        if($adminGroupInfo['language_power'] & 1){
            $parameters['conditions'] = 'language_id=' . $adminGroupInfo['language_id'];
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
     * @author ZhaoYang 
     * @date 2018年8月29日 下午5:58:27 
     */
    public function add(array $data) {
        $data = Common::arraySlice(['name', 'table'], $data);
        $message = (new Validate())->addRules(self::getRules(['name0', 'name1', 'table0', 'table1']))->validate($data);
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
                    ]),
                    new Column('category_content_id', [
                        'type'          => Column::TYPE_INTEGER,
                        'size'          => 10,
                        'notNull'       => true,
                        'autoIncrement' => false,
                        'primary'       => false,
                        'unsigned'      => true,
                    ])
                ],
                'indexes' => [
                    new Index('category_content_id', ['category_content_id'])
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
     * @desc 查看单条记录
     * @author: ZhaoYang
     * @date: 2018年8月29日 下午11:55:55
     */
    public function getInfoById(int $id) {
        $message = (new Validate())->addRules(self::getRules(['id0']))->validate(['id' => $id]);
        if(count($message)) {
            return $this->errorMessage('非法操作！');
        }
        return self::findFirst($id);
    }
    
    /**
     * @desc 修改
     * @author: ZhaoYang
     * @date: 2018年8月29日 下午11:52:48
     */
    public function edit(array $data) {
        $data = Common::arraySlice(['id', 'name'], $data);
        $message = (new Validate())->addRules(self::getRules(['id0', 'name0', 'name1']))->validate($data);
        if (count($message)) {
            return $this->errorMessage($message);
        }
        $expand = self::findFirst($data['id']);
        $this->assign($expand->toArray());
        return $this->update($data);
    }
    
    /**
     * @desc 删除
     * @author: ZhaoYang
     * @date: 2018年8月30日 上午12:34:55
     */
    public function del(int $id) {
        $message = (new Validate())->addRules(self::getRules(['id0']))->validate(['id' => $id]);
        if(count($message)) {
            return $this->errorMessage($message);
        }
        $expand = self::findFirst($id);
        $delRes = $expand->delete();
        if($delRes) {
            $this->getWriteConnection()->dropTable(static::$_tablePrefix . self::$_tableName. '_data_' . $expand->table);
        }
        return $delRes;
    }
}