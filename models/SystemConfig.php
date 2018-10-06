<?php
namespace Models;

use Common\BaseModel;

class SystemConfig extends BaseModel {
    
    protected static $_tableName= 'system_config';
    
    protected $config;
    
    public function getConfig() {
        return empty($this->config) ? [ ] : json_decode($this->config, true);;
    }

    /**
     * @desc 根据languageId获取配置信息
     * @param int $languageId language_id
     * @return: Model
     * @author: ZhaoYang
     * @date: 2018年7月8日 下午3:53:29
     */
    public static function getInfoByLanguageId(int $languageId = 0, bool $cache = true, $lifetime = null) {
        return self::getInfo([
            'conditions' => 'language_id=:language_id:',
            'bind' => [
                'language_id' => $languageId
            ]
        ], $cache, $lifetime);
    }
    
}