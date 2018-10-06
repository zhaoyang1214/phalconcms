<?php
namespace App\Home\Models;

use Models\Language as ModelsLanguage;

class Language extends ModelsLanguage {

    public function getNowLanguageInfo(string $language = null) {
        $di = $this->getDI();
        // 判断全局配置
        $systemConfigInfo = $di->getConfig()->system->toArray();
        if (isset($systemConfigInfo['language_status']) && $systemConfigInfo['language_status'] == 1) {
            // 优先根据url中的language参数识别
            if (!empty($language)) {
                $languageInfo = self::getInfoByLang($language, 1);
                if ($languageInfo !== false) {
                    goto LANGUAGE_INFO_TO_ARRAY;
                }
            }
            $request = $this->getDI()->getRequest();
            // 再根据域名识别
            $languageInfo = self::getInfoByDomain($request->getHttpHost(), 1);
            if ($languageInfo !== false) {
                goto LANGUAGE_INFO_TO_ARRAY;
            }
            // 根据浏览器提供的参数
            $httpAcceptLanguage = $request->getLanguages();
            if (isset($httpAcceptLanguage[0])) {
                $language = $httpAcceptLanguage[0]['language'];
                $languageInfo = self::getInfoByLang($language, 1);
                if ($languageInfo !== false) {
                    goto LANGUAGE_INFO_TO_ARRAY;
                }
            }
            if (isset($httpAcceptLanguage[1])) {
                $language = $httpAcceptLanguage[1]['language'];
                $languageInfo = self::getInfoByLang($language, 1);
                if ($languageInfo !== false) {
                    goto LANGUAGE_INFO_TO_ARRAY;
                }
            }
            if (isset($systemConfigInfo['lang'])) {
                // 根据设置的默认值识别
                $languageInfo = self::getInfoByLang($systemConfigInfo['lang'], 1);
                if ($languageInfo !== false) {
                    goto LANGUAGE_INFO_TO_ARRAY;
                }
            }
        }
        $languageInfo = self::getInfo(1, true);
        LANGUAGE_INFO_TO_ARRAY:
        $languageInfo = $languageInfo->toArray();
        SET_DEFINE_LANGUAGE:
        defined('LANGUAGE') || define('LANGUAGE', $languageInfo['lang']);
        defined('LANGUAGE_ID') || define('LANGUAGE_ID', $languageInfo['id']);
        return $languageInfo;
    }
    
}