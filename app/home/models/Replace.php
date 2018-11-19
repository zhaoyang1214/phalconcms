<?php
namespace App\Home\Models;

use Models\Replace as ModelsReplace;

class Replace extends ModelsReplace {
    
    public function getOne($parameters = null, $languageId = null) {
        $where = [];
        if(is_numeric($parameters)) {
            $where[] = 'id=' . $parameters;
            $parameters = [];
        } else if(is_string($parameters)) {
            $where[] = $parameters;
            $parameters = [];
        } else if(is_array($parameters)) {
            $where[] = $parameters['conditions'] ?? '';
        } else {
            $parameters = [];
        }
        if($languageId !== false) {
            $languageId = ($languageId && is_int($languageId)) ? $languageId : LANGUAGE_ID;
            $where[] = 'language_id=' . $languageId;
        }
        $parameters['conditions'] = implode(' AND ', $where);
        $system = $this->getDI()->getConfig()->system;
        return self::getInfo($parameters, (bool)$system->data_cache_on);
    }
    
    public function getAll($parameters = [ ], $languageId = null) {
        $where = [];
        if(is_numeric($parameters)) {
            $where[] = 'id=' . $parameters;
            $parameters = [];
        } else if(is_string($parameters)) {
            $where[] = $parameters;
            $parameters = [];
        } else if(is_array($parameters)) {
            $where[] = $parameters['conditions'] ?? '';
        } else {
            $parameters = [];
        }
        if($languageId !== false) {
            $languageId = ($languageId && is_int($languageId)) ? $languageId : LANGUAGE_ID;
            $where[] = 'language_id=' . $languageId;
        }
        $parameters['conditions'] = implode(' AND ', $where);
        $system = $this->getDI()->getConfig()->system;
        return self::getList($parameters, (bool)$system->data_cache_on);
    }
    
    public function replaceContent(string $content) {
        $system = $this->getDI()->getConfig()->system;
        $dataCache = (bool)$system->data_cache_on;
        if($dataCache) {
            $key = self::createCacheKey(__FUNCTION__, [$content]);
            $cache = $this->getDI()->get('modelsCache');
            if($cache->exists($key)) {
                return $cache->get($key);
            }
        }
        $list = self::getAll('status=1');
        foreach ($list as $replace) {
            $count = $replace->num > 0 ? $replace->num : null;
            $content = str_replace($replace->key, $replace->content, $content, $count);
        }
        if($dataCache) {
            $cache->save($key, $content);
        }
        return $content;
    }
}