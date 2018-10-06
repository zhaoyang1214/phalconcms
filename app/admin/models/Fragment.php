<?php
namespace App\Admin\Models;

use Models\Fragment as ModelsFragment;
use Common\Common;
use Common\Validate;

class Fragment extends ModelsFragment {
    
    /** 
     * @desc 规则 
     * @author ZhaoYang 
     * @date 2018年8月31日 上午10:23:35 
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
            'title0' => ['title', 'stringlength', '描述长度必须大于1位|描述长度必须小于100位', [1, 100]],
            'sign0' => ['sign', 'regex', '标识必须为2-100位以英文开头的数字、字母、下划线', '/[a-z]\w{1,99}/i'],
            'sign1' => ['sign', 'callback', '标识已存在', function($data) {
                if(!isset($data['sign'])) {
                    return false;
                }
                $parameters = [
                    'columns' => 'sign',
                    'conditions'  => 'sign=:sign:',
                    'bind' => [
                        'sign' => $data['sign']
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
     * @author: ZhaoYang
     * @date: 2018年8月31日 上午12:14:13
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
     * @author: ZhaoYang
     * @date: 2018年8月31日 上午12:14:20
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
     * @date 2018年8月31日 上午10:22:30 
     */
    public function add(array $data) {
        $data = Common::arraySlice(['title', 'sign', 'content'], $data);
        $message = (new Validate())->addRules(self::getRules(['title0', 'sign0', 'sign1']))->validate($data);
        if (count($message)) {
            return $this->errorMessage($message);
        }
        $data['content'] = htmlspecialchars($this->getDI()->getHtmlPurifier()->purify($data['content']));
        $adminGroupInfo = $this->getDI()->getSession()->get('adminGroupInfo');
        $data['language_id'] = $adminGroupInfo['language_id'];
        return $this->create($data);
    }
    
    /** 
     * @desc 修改 
     * @author ZhaoYang 
     * @date 2018年8月31日 上午10:55:57 
     */
    public function edit(array $data) {
        $data = Common::arraySlice(['id', 'title', 'sign', 'content'], $data);
        $message = (new Validate())->addRules(self::getRules(['id0', 'title0', 'sign0', 'sign1']))->validate($data);
        if (count($message)) {
            return $this->errorMessage($message);
        }
        $fragment = self::findFirst($data['id']);
        $this->assign($fragment->toArray());
        return $this->update($data);
    }
    
    /** 
     * @desc 删除 
     * @author ZhaoYang 
     * @date 2018年8月31日 上午10:58:00 
     */
    public function del(int $id) {
        $message = (new Validate())->addRules(self::getRules(['id0']))->validate(['id' => $id]);
        if(count($message)) {
            return $this->errorMessage($message);
        }
        $fragment = self::findFirst($id);
        return $fragment->delete();
    }
}