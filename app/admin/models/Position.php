<?php
namespace App\Admin\Models;

use Models\Position as ModelsPosition;
use Common\Common;
use Common\Validate;

class Position extends ModelsPosition {
    
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
            'name0' => ['name', 'stringlength', '推荐位名称长度必须大于1位|推荐位名称长度必须小于255位', [1, 255]],
            'name1' => ['name', 'callback', '推荐位名称已存在', function($data) {
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
            'sequence0' => ['sequence', 'digit', '顺序只能为整数'],
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
        $parameters['order'] = 'sequence ASC';
        return self::find($parameters);
    }
    
    /** 
     * @desc 添加   
     * @author ZhaoYang 
     * @date 2018年9月4日 上午10:15:53 
     */
    public function add(array $data) {
        $data = Common::arraySlice(['name', 'sequence'], $data);
        $message = (new Validate())->addRules(self::getRules(['name0', 'name1', 'sequence0']))->validate($data);
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
     * @date 2018年9月4日 上午10:15:58 
     */
    public function edit(array $data) {
        $data = Common::arraySlice(['id', 'name', 'sequence'], $data);
        $message = (new Validate())->addRules(self::getRules(['id0', 'name0', 'name1', 'sequence0']))->validate($data);
        if (count($message)) {
            return $this->errorMessage($message);
        }
        $info = self::findFirst($data['id']);
        $this->assign($info->toArray());
        return $this->update($data);
    }
    
   /** 
    * @desc  删除   
    * @author ZhaoYang 
    * @date 2018年9月4日 上午10:16:03 
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