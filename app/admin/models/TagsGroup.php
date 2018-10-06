<?php
namespace App\Admin\Models;

use Models\TagsGroup as ModelsTagsGroup;
use Common\Common;
use Common\Validate;

class TagsGroup extends ModelsTagsGroup {
    
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
            'name0' => ['name', 'stringlength', '分组名称长度必须大于1位|分组名称长度必须小于100位', [1, 100]],
            'name1' => ['name', 'callback', '分组名称已存在', function($data) {
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
    
    public function add(array $data) {
        $data = Common::arraySlice(['name'], $data);
        $message = (new Validate())->addRules(self::getRules(['name0', 'name1']))->validate($data);
        if (count($message)) {
            return $this->errorMessage($message);
        }
        $adminGroupInfo = $this->getDI()->getSession()->get('adminGroupInfo');
        $data['language_id'] = $adminGroupInfo['language_id'];
        return $this->create($data);
    }
    
    public function edit(array $data) {
        $data = Common::arraySlice(['id', 'name'], $data);
        $message = (new Validate())->addRules(self::getRules(['id0', 'name0', 'name1']))->validate($data);
        if (count($message)) {
            return $this->errorMessage($message);
        }
        $info = self::findFirst($data['id']);
        $this->assign($info->toArray());
        return $this->update($data);
    }
    
    public function del(int $id) {
        $message = (new Validate())->addRules(self::getRules(['id0']))->validate(['id' => $id]);
        if(count($message)) {
            return $this->errorMessage($message);
        }
        $phql = 'UPDATE App\Admin\Models\Tags SET tags_group_id=0 WHERE tags_group_id=' . $id;
        self::getModelsManager()->executeQuery($phql);
        $info = self::findFirst($id);
        return $info->delete();
    }
}