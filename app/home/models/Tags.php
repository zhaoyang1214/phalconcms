<?php
namespace App\Home\Models;

use Models\Tags as ModelsTags;

class Tags extends ModelsTags {
    
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
    
    public function getCount($parameters = [ ], $languageId = null) {
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
        if($system->data_cache_on && !isset($parameters['cache'])) {
            $parameters['cache'] = $parameters['cache'] ?? [];
            $parameters['cache']['key'] = $parameters['cache']['key'] ?? self::createCacheKey(__FUNCTION__, $parameters);
        }
        return self::count($parameters);
    }
    
    public function getAllByCategoryContentId(int $categoryContentId) {
        $phql = "SELECT b.id,b.tags_group_id,b.name,b.click,b.language_id
        FROM App\Home\Models\TagsRelation AS a
        LEFT JOIN App\Home\Models\Tags AS b ON a.tags_id=b.id
        WHERE a.category_content_id={$categoryContentId}";
        $query = self::getModelsManager()->createQuery($phql);
        $system = $this->getDI()->getConfig()->system;
        if($system->data_cache_on) {
            $query->cache([
                'key' => self::createCacheKey(__FUNCTION__, [$categoryContentId])
            ]);
        }
        return $query->execute();
    }
    
    public function tagLink(string $content, int $categoryContentId) {
        $di = $this->getDI();
        $system = $di->getConfig()->system;
        $dataCache = (bool)$system->data_cache_on;
        if($dataCache) {
            $key = self::createCacheKey(__FUNCTION__, [$content, $categoryContentId]);
            $cache = $this->getDI()->get('modelsCache');
            if($cache->exists($key)) {
                return $cache->get($key);
            }
        }
        $list = self::getAllByCategoryContentId($categoryContentId);
        foreach ($list as $tags) {
            $content = str_replace($tags->name, '<a href="' . $di->getUrl()->get('tags/info/tag/' . $tags->name) . '" target="_blank">' . $tags->name . '</a>', $content);
        }
        if($dataCache) {
            $cache->save($key, $content);
        }
        return $content;
    }
    
    public function clickIncrement(int $id, int $step = 1) {
        $phql = 'UPDATE ' . __CLASS__ . ' SET click=click+' . $step . ' WHERE id=' . $id;
        return self::getModelsManager()->executeQuery($phql);
    }
    
    public function getListByTagsGroupId(int $tagsGroupId, int $limit = 1000, $order = null) {
        $parameters = [
            'conditions' => 'tags_group_id=' . $tagsGroupId,
            'limit' => $limit,
            'order' => is_null($order) ? 'click DESC' : $order
        ];
        return self::getAll($parameters);
    }
}