<?php
namespace App\Admin\Models;

use Models\Tags as ModelsTags;
use Common\Validate;

class Tags extends ModelsTags {
    
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
            'name0' => ['name', 'stringlength', 'tag名称长度必须大于1位|tag名称长度必须小于100位', [1, 100]],
            'name1' => ['name', 'callback', 'tag名称已存在', function($data) {
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
    
    public function getAllowCount(array $conditions = null) {
        $session = $this->getDI()->getSession();
        $adminGroupInfo = $session->get('adminGroupInfo');
        if($adminGroupInfo['language_power'] & 1){
            $conditions[] = 'language_id=' . $adminGroupInfo['language_id'];
        }
        $conditions = implode(' AND ', $conditions);
        $result = self::query()->columns('count(*) num')->where($conditions)->limit(1, 0)->execute()->getFirst();
        return $result->num;
    }
    
    public function getAllowList(int $limit = null, int $offset = null, string $order = null, array $conditions = null) {
        $session = $this->getDI()->getSession();
        $adminGroupInfo = $session->get('adminGroupInfo');
        if($adminGroupInfo['language_power'] & 1){
            $conditions[] = 'App\Admin\Models\Tags.language_id=' . $adminGroupInfo['language_id'];
        }
        $conditions = implode(' AND ', $conditions);
        $result = self::query()
        ->columns('App\Admin\Models\Tags.id,App\Admin\Models\Tags.name,App\Admin\Models\Tags.click,b.name AS tagsgroup_name')
        ->leftJoin('App\Admin\Models\TagsGroup', 'tags_group_id=b.id', 'b')
        ->where($conditions)
        ->orderBy($order)
        ->limit($limit, $offset)
        ->execute();
        return $result;
    }
    
    public function grouping(array $data) {
        if(empty($data['id']) || empty($data['tags_group_id'])) {
            return $this->errorMessage('参数错误');
        }
        $message = (new Validate())->addRules((new TagsGroup)->getRules(['id0']))->validate(['id' => $data['tags_group_id']]);
        if(count($message)) {
            return $this->errorMessage($message);
        }
        $ids = trim($data['id'], ',');
        $phql = 'UPDATE App\Admin\Models\Tags SET tags_group_id=' . $data['tags_group_id'] . ' WHERE id in(' . $ids . ')';
        return self::getModelsManager()->executeQuery($phql);
    }
    
    public function del(int $id) {
        $message = (new Validate())->addRules(self::getRules(['id0']))->validate(['id' => $id]);
        if(count($message)) {
            return $this->errorMessage($message);
        }
        (new TagsRelation())->deleteByTagsId($id);
        $info = self::findFirst($id);
        return $info->delete();
    }
}