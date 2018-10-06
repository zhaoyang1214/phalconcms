<?php
namespace App\Admin\Models;

use Models\SystemConfig as ModelsSystemConfig;
use Common\Validate;
use Phalcon\Mvc\Model\Exception;

class SystemConfig extends ModelsSystemConfig {
    
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
    
    /**
     * @desc 定义过滤规则
     * @author: ZhaoYang
     * @date: 2018年7月25日 上午12:24:11
     */
    public function rules() {
        return [
            0 => ['html_index_cache_time', 'digit', '首页更新时间只能为整数'],
            1 => ['html_other_cache_time', 'digit', '其他页更新时间只能为整数'],
            2 => ['html_search_cache_time', 'digit', '搜索更新时间只能为整数'],
            3 => ['tpl_seach_page', 'digit', '搜索结果分页数只能为整数'],
            4 => ['tpl_tags_page', 'digit', 'TAG内容分页数只能为整数'],
            5 => ['tpl_tags_index_page', 'digit', 'TAG主页分页数只能为整数'],
            6 => ['file_size', 'digit', '上传大小只能为整数'],
            7 => ['file_num', 'digit', '批量上传数只能为整数'],
            8 => ['thumbnail_maxwidth', 'digit', '默认缩图尺寸-最大宽度只能为整数'],
            9 => ['thumbnail_maxheight', 'digit', '默认缩图尺寸-最大高度只能为整数'],
            10 => ['language_status', 'callback', '请先在语言管理中开启他国语言！', function ($data) {
                if(!isset($data['language_status'])){
                    return false;
                }
                if($data['language_status'] == 0){
                    return true;
                }
                return Language::findFirst('status=1 AND id<>1') ? true : false;
            }],
            11 => ['language_status', 'callback', '请先在语言管理中关闭他国语言！', function ($data) {
                if(!isset($data['language_status'])){
                    return false;
                }
                if($data['language_status'] == 1){
                    return true;
                }
                return Language::findFirst('status=1 AND id<>1') ? false : true;
            }],
        ];
    }
    
    /**
     * @desc 保存配置
     * @param array $data config数据
     * @return bool
     * @author: ZhaoYang
     * @date: 2018年7月18日 上午1:01:50
     */
    public function saveConfig(array $data) {
        $message = (new Validate())->addRules(self::getRules([0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11]))->validate($data);
        if (count($message)) {
            return $this->errorMessage($message);
        }
        $adminGroupInfo = $this->getDI()->getSession()->get('adminGroupInfo');
        $info = self::getInfo('language_id=' . $adminGroupInfo['language_id']);
        if(!empty($data['image_type'])) {
            $data['image_type'] = preg_replace('/[^,a-z0-9]/', '', trim(strtolower(str_replace('，', ',', $data['image_type'])), ','));
        }
        if(!empty($data['video_type'])) {
            $data['video_type'] = preg_replace('/[^,a-z0-9]/', '', trim(strtolower(str_replace('，', ',', $data['video_type'])), ','));
        }
        if(!empty($data['file_type'])) {
            $data['file_type'] = preg_replace('/[^,a-z0-9]/', '', trim(strtolower(str_replace('，', ',', $data['file_type'])), ','));
        }
        $data = [
            'config' => $data,
            'language_id' => $adminGroupInfo['language_id']
        ];
        $this->deleteCacheByPrefix(self::createCacheKey());
        if ($info === false) {
            return $this->create($data);
        } else {
            $this->assign($info->toArray());
            return $this->update($data);
        }
    }
}