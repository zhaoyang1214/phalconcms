<?php
/**
 * @desc 重写url方法
 * @author: ZhaoYang
 * @date: 2018年9月28日 上午12:50:02
 */
namespace Library\Extensions;

use Phalcon\Mvc\Url;

class UrlExtension extends Url {
    
    /** 
     * @desc 开启多国语言后，生成路由自动加上语言 ，不能重写get方法
     * @author ZhaoYang 
     * @date 2018年9月28日 上午9:52:12 
     */
    public function getLang($uri = null, $args = null, $baseUri = null) {
        if($this->getDI()->getConfig()->system->language_status == 1) {
            if(is_object($args)) {
                $args->language = LANGUAGE;
            } else {
                $args['language'] = LANGUAGE;
            }
        }
        return parent::get($uri, $args, $baseUri);
    }
}