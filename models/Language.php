<?php
namespace Models;

use Common\BaseModel;

class Language extends BaseModel {

    protected static $_tableName= 'language';
    
    /**
     * @desc 根据domain获取单条记录
     * @param string $domain 域名
     * @param int $status 状态
     * @param bool $cache 是否缓存
     * @param int $lifetime 缓存时间
     * @return: Model
     * @author: ZhaoYang
     * @date: 2018年7月9日 下午11:20:38
     */
    public static function getInfoByDomain(string $domain, int $status, bool $cache = true, int $lifetime = null) {
        return self::getInfo([
            'conditions' => 'domain=:domain: AND status=:status:',
            'bind' => [
                'domain' => $domain,
                'status' => $status
            ]
        ], $cache, $lifetime);
    }
    
    /**
     * @desc 根据标识(lang)获取单条记录
     * @param string $lang 标识
     * @param int $status 状态
     * @param bool $cache 是否缓存
     * @param int $lifetime 缓存时间
     * @return: Model
     * @author ZhaoYang
     * @date 2018年7月27日 上午9:35:07
     */
    public static function getInfoByLang(string $lang, int $status, $cache = true, $lifetime = null) {
        return self::getInfo([
            'conditions' => 'lang=:lang: AND status=:status:',
            'bind' => [
                'lang' => $lang,
                'status' => $status
            ]
        ], $cache, $lifetime);
    }
    
}