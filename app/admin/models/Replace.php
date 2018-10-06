<?php
namespace App\Admin\Models;

use Models\Replace as ModelsReplace;
use Common\Common;
use Common\Validate;

class Replace extends ModelsReplace {
    
    /** 
     * @desc 规则  
     * @author ZhaoYang 
     * @date 2018年8月31日 下午1:59:43 
     */
    public function rules() {
        return [
            'id0' => ['id', 'callback', '非法操作', function($data) {
                if(!isset($data['id'])) {
                    return false;
                }
                $info = self::findFirst(intval($data['id']));
                $session = $this->getDI()->getSession();
                $adminGroupInfo = $session->get('adminGroupInfo');
                if($info === false || ($adminGroupInfo['language_power'] == 1 && $adminGroupInfo['language_id'] != $info->language_id)) {
                    return false;
                }
                return true;
            }],
            'key0' => ['key', 'stringlength', '关键字长度必须大于1位|关键字长度必须小于255位', [1, 255]],
            'key1' => ['key', 'callback', '关键字已存在', function($data) {
                if(!isset($data['key'])) {
                    return false;
                }
                $parameters = [
                    'columns' => 'key',
                    'conditions'  => 'key=:key:',
                    'bind' => [
                        'key' => $data['key']
                    ]
                ];
                if(isset($data['id']) && !empty($data['id'])){
                    $parameters['conditions']  .= ' AND id<>:id:';
                    $parameters['bind']['id'] = $data['id'];
                }
                return self::findFirst($parameters) ? false : true;
            }],
            'content0' => ['content', 'stringlength', '替换内容长度必须大于1位|替换内容长度必须小于1000位', [1, 1000]],
            'num0' => ['num', 'digit', '替换次数只能为整数'],
        ];
    }
    
    public function getAllowCount() {
        $session = $this->getDI()->getSession();
        $adminGroupInfo = $session->get('adminGroupInfo');
        $parameters = [ ];
        if($adminGroupInfo['language_power'] & 1){
            $parameters['conditions'] = 'language_id=' . $adminGroupInfo['language_id'];
        }
        return self::count($parameters);
    }
    
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
     * @date 2018年8月31日 下午2:04:49 
     */
    public function add(array $data) {
        $data = Common::arraySlice(['key', 'content', 'num', 'status'], $data);
        $message = (new Validate())->addRules(self::getRules(['key0', 'key1', 'content0', 'num0']))->validate($data);
        if (count($message)) {
            return $this->errorMessage($message);
        }
        $adminGroupInfo = $this->getDI()->getSession()->get('adminGroupInfo');
        $data['language_id'] = $adminGroupInfo['language_id'];
        return $this->create($data);
    }
    
    /** 
     * @desc 修改  
     * @author ZhaoYang 
     * @date 2018年8月31日 下午2:05:07 
     */
    public function edit(array $data) {
        $data = Common::arraySlice(['id', 'key', 'content', 'num', 'status'], $data);
        $message = (new Validate())->addRules(self::getRules(['id0', 'key0', 'key1', 'content0', 'num0']))->validate($data);
        if (count($message)) {
            return $this->errorMessage($message);
        }
        $info = self::findFirst($data['id']);
        $this->assign($info->toArray());
        return $this->update($data);
    }
    
    /** 
     * @desc 删除  
     * @author ZhaoYang 
     * @date 2018年8月31日 下午2:05:14 
     */
    public function del(int $id) {
        $message = (new Validate())->addRules(self::getRules(['id0']))->validate(['id' => $id]);
        if(count($message)) {
            return $this->errorMessage($message);
        }
        $info = self::findFirst($id);
        return $info->delete();
    }
}