<?php
namespace App\Admin\Models;

use Models\TranslateDriver as ModelsTranslateDriver;
use Common\Validate;
use Common\Common;
use Phalcon\Mvc\Model\Exception;
use Phalcon\Validation\Message;

class TranslateDriver extends ModelsTranslateDriver {

    protected $config;
    
    public function __set($property, $value) {
        if($property == 'config'){
            $this->setConfig($value);
        }else{
            parent::__set($property, $value);
        }
    }
    
    public function setConfig($value) {
        if(empty($value)){
            $this->config = '';
        }else if(is_array($value)){
            $this->config = json_encode($value, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        }else if(is_string($value) && json_decode($value, true)) {
            $this->config = $value;
        }else{
            throw new Exception('config的值必须是数组或json字符串');
        }
    }
    
    public function getConfig() {
        return empty($this->config) ? [ ] : json_decode($this->config, true);;
    }
    
    /**
     * @desc 定义过滤规则
     * @author: ZhaoYang
     * @date: 2018年7月25日 上午12:24:11
     */
    public function rules() {
        return [
            'id0' => ['id', 'digit', '该记录不存在'],
            'name0' => ['name', 'presenceof', '驱动名称不能为空'],
            'name1' => ['name', 'uniqueness', '该驱动名称已存在', $this],
            'class_name0' => ['class_name', 'presenceof', '类名不能为空'],
            'class_name1' => ['class_name', 'callback', '该类已存在', function ($data) {
                if(!isset($data['class_name'])) {
                    return false;
                }
                $parameters = [
                    'columns' => 'class_name',
                    'conditions'  => 'class_name=:class_name:',
                    'bind' => [
                        'class_name' => $data['class_name']
                    ]
                ];
                if(isset($data['id']) && !empty($data['id'])){
                    $parameters['conditions']  .= ' AND id<>:id:';
                    $parameters['bind']['id'] = $data['id'];
                }
                return self::findFirst($parameters) ? false : true;
            }],
            'class_name2' => ['class_name', 'callback', '该类不存在或未继承 \Library\Translate\AdapterInterface 接口', function ($data) {
                if(!isset($data['class_name'])) {
                    return false;
                }
                if (class_exists($data['class_name']) && (new $data['class_name']()) instanceof \Library\Translate\AdapterInterface) {
                    return true;
                }
                return false;
            }],
            'status0' => ['status', 'callback', '请先关闭其他驱动不支持翻译的语言或开启其他驱动', function($data) {
                if(!isset($data['status']) || !isset($data['id'])) {
                    return false;
                }
                if($data['status']){
                    return true;
                }
                $languageList = Language::find('status=1 AND id<>1');
                if($languageList === false) {
                    return true;
                }
                $langList = array_column($languageList->toArray(), 'lang');
                $translateDriverList = self::find([
                    'conditions' => 'status=1 AND id<>:id:',
                    'bind' => [
                        'id' => $data['id']
                    ]
                ]);
                if($translateDriverList === false) {
                    return false;
                }
                foreach ($translateDriverList as $translateDriver) {
                    $config = $translateDriver->getConfig();
                    if(isset($config['map'])){
                        $langList = array_diff($langList, array_values($config['map']));
                    }
                }
                if(empty($langList)) {
                    return true;
                }
                return false;
            }],
        ];
    }
    
    /**
     * @desc 获取状态
     * @param int $status 状态码
     * @return string
     * @author: ZhaoYang
     * @date: 2018年7月24日 上午1:42:32
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
     * @desc 添加数据
     * @param array $data 要插入的数据
     * @param bool
     * @author: ZhaoYang
     * @date: 2018年7月24日 下午8:01:47
     */
    public function add(array $data) {
        $data = Common::arraySlice(['name', 'class_name'], $data);
        $message = (new Validate())->addRules(self::getRules(['name0', 'name1', 'class_name0', 'class_name2']))->validate($data);
        if (count($message)) {
            return $this->errorMessage($message);
        }
        return $this->create($data);
    }
    
    /**
     * @desc 修改
     * @param 要修改的数据
     * @return bool
     * @author: ZhaoYang
     * @date: 2018年7月25日 上午12:39:39
     */
    public function edit(array $data) {
        $data = Common::arraySlice(['id', 'status', 'config'], $data);
        $message = (new Validate())->addRules(self::getRules(['id0', 'status0']))->validate($data);
        if (count($message)) {
            return $this->errorMessage($message);
        }
        $translateDriver = self::findFirst($data['id']);
        if ($translateDriver == false) {
            return $this->errorMessage('该记录不存在');
        }
        $this->assign($translateDriver->toArray());
        $result = $this->update($data);
        $result && $this->deleteCacheByPrefix(self::createCacheKey());
        return $result;
    }
}