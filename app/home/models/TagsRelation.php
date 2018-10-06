<?php
namespace App\Home\Models;

use Models\TagsRelation as ModelsTagsRelation;

class TagsRelation extends ModelsTagsRelation {
    
    public function getCountByTagsId(int $tagsId, $languageId = null) {
        $where = 'c.tags_id=' . $tagsId;
        if($languageId !== false) {
            $languageId = ($languageId && is_int($languageId)) ? $languageId : LANGUAGE_ID;
            $where .= ' AND b.language_id=' . $languageId;
        }
        $query = self::getModelsManager()->createBuilder()
        ->columns('count(a.id) AS num')
        ->addFrom('App\Home\Models\CategoryContent', 'a')
        ->leftJoin('App\Home\Models\Category', 'a.category_id=b.id', 'b')
        ->leftJoin(__CLASS__, 'a.id=c.category_content_id', 'c')
        ->where($where)
        ->getQuery();
        $system = $this->getDI()->getConfig()->system;
        if($system->data_cache_on) {
            $query = $query->cache([
                'key' => self::createCacheKey(__FUNCTION__, [$tagsId, $languageId])
            ]);
        }
        return (int)$query->getSingleResult()->num;
    }
    
    public function getAllByTagsId(int $tagsId, int $limit, int $offset, $languageId = null) {
        $where = 'c.tags_id=' . $tagsId;
        if($languageId !== false) {
            $languageId = ($languageId && is_int($languageId)) ? $languageId : LANGUAGE_ID;
            $where .= ' AND b.language_id=' . $languageId;
        }
        $query = self::getModelsManager()->createBuilder()
        ->columns('a.id,a.category_id,a.title,a.urltitle,a.subtitle,a.font_color,a.font_bold,a.keywords,a.description,a.updatetime,a.inputtime,a.image,a.url,a.sequence,a.tpl,a.status,a.copyfrom,a.views,a.position,a.taglink,b.name as category_name,b.subname as category_subname,b.category_model_id')
        ->addFrom('App\Home\Models\CategoryContent', 'a')
        ->leftJoin('App\Home\Models\Category', 'a.category_id=b.id', 'b')
        ->leftJoin(__CLASS__, 'a.id=c.category_content_id', 'c')
        ->where($where)
        ->orderBy('a.updatetime desc')
        ->limit($limit, $offset)
        ->getQuery();
        $system = $this->getDI()->getConfig()->system;
        if($system->data_cache_on) {
            $query = $query->cache([
                'key' => self::createCacheKey(__FUNCTION__, [$tagsId, $limit, $offset, $languageId])
            ]);
        }
        return $query->execute();
    }
}