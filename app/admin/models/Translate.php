<?php
namespace App\Admin\Models;

use Models\Translate as ModelsTranslate;
use Common\Validate;
use Common\Common;

class Translate extends ModelsTranslate {

    /**
     * @desc 定义过滤规则
     * @author: ZhaoYang
     * @date: 2018年7月25日 上午12:24:11
     */
    public function rules() {
        return [
            0 => ['id', 'digit', '该记录不存在'],
            1 => ['translated_text', 'stringlength', '译文长度必须大于等于1|译文长度必须小于等于250', [1, 250]]
        ];
    }

    /**
     * @desc 翻译修改
     * @param array $data 要修改的参数
     * @return bool
     * @author: ZhaoYang
     * @date: 2018年7月23日 下午9:04:16
     */
    public function edit(array $data) {
        $data = Common::arraySlice(['id', 'translated_text'], $data);
        $message = (new Validate())->addRules(self::getRules([0, 1]))->validate($data);
        if (count($message)) {
            return $this->errorMessage($message);
        }
        $translate = self::findFirst($data['id']);
        if ($translate == false) {
            return $this->errorMessage('该记录不存在');
        }
        $this->assign($translate->toArray());
        $result = $this->update($data);
        $result && $this->deleteCacheByPrefix(self::createCacheKey($translate->sign));
        return $result;
    }
    
}