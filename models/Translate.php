<?php
namespace Models;

use Common\BaseModel;

class Translate extends BaseModel {

    protected static $_tableName= 'translate';
    
    /**
     * @desc 翻译查询
     * @param string $string 待翻译的字符串
     * @param string $lang 要翻译成的语言标识，默认为常量LANGUAGE
     * @return: string
     * @author: ZhaoYang
     * @date: 2018年7月26日 上午1:22:25
     */
    public function t(string $string = '', string $lang = null) {
        $di = $this->getDI();
        if (empty($string) || $di->getConfig()->system->language_status != 1) {
            return $string;
        }
        if (empty($lang)) {
            $lang = LANGUAGE;
        }
        if ($lang == 'zh') {
            return $string;
        }
        $language = Language::getInfoByLang($lang, 1);
        if ($language === false) {
            return $string;
        }
        $sign = md5("{$string}to{$lang}");
        $result = self::findFirst([
            'columns' => 'translated_text',
            'conditions' => 'sign=:sign:',
            'bind' => [
                'sign' => $sign
            ],
            'cache' => [
                'key' => self::createCacheKey($sign),
                'lifetime' => 86400000
            ]
        ]);
        if ($result !== false) {
            return $result->translated_text;
        }
        $translateDriverList = TranslateDriver::getList('status=1', true);
        if ($translateDriverList === false) {
            return $string;
        }
        foreach ($translateDriverList as $translateDriver) {
            if (class_exists($translateDriver->class_name) && !empty($translateDriver->config)) {
                $translate = new $translateDriver->class_name($translateDriver->config);
                $dst = $translate->query($string, 'zh', $lang);
                $file = BASE_PATH . 'runtime/' . MODULE_NAME . '/logs/translate/{Y/m-d}/info-{Y-m-d-H}.log';
                $message = "[{$translateDriver->name}] [query:$string] [result:{$dst}]";
                $di->get('logger', [
                    $file
                ])->info($message);
                if ($dst !== false) {
                    try {
                        (new Translate())->create([
                            'sign' => $sign,
                            'source_text' => $string,
                            'translated_text' => $dst,
                            'translated_language_id' => $language->id
                        ]);
                    } catch (\Exception $e) {
                        $message = "[{$translateDriver->name}] [query:$string] [result:{$dst}] [插入异常]";
                        $di->get('logger', [
                            $file
                        ])->info($message);
                    }
                    $string = $dst;
                    break;
                }
            }
        }
        return $string;
    }
    
}