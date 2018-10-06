<?php
namespace App\Home\Models;

use Models\CategoryContent as ModelsCategoryContent;
use Phalcon\Mvc\Model\Resultset\Simple as Resultset;

class CategoryContent extends ModelsCategoryContent {
    
    public function getCount(string $conditions = null) {
        $where = empty($conditions) ? '' : 'WHERE ' . $conditions;
        $phql = "SELECT COUNT(*) AS num
        FROM App\Home\Models\CategoryContent AS a
        {$where}
        LIMIT 1";
        $query = self::getModelsManager()->createQuery($phql);
        $system = $this->getDI()->getConfig()->system;
        if($system->data_cache_on) {
            $query->cache([
                'key' => self::createCacheKey(__FUNCTION__, [$where])
            ]);
        }
        return (int)$query->execute()->getFirst()->num;
    }
    
    public function getContentList(string $conditions = null, string $limit = null, int $expandId = 0, string $order = '') {
        $system = $this->getDI()->getConfig()->system;
        $dataCache = (bool)$system->data_cache_on;
        if($dataCache) {
            $key = self::createCacheKey(__FUNCTION__, [$conditions, $limit, $expandId, $order]);
            $cache = $this->getDI()->get('modelsCache');
            if($cache->exists($key)) {
                return $cache->get($key);
            }
        }
        $where = empty($conditions) ? '' : 'WHERE ' . $conditions;
        $limit = empty($limit) ? '' : 'LIMIT ' . $limit;
        $categoryContentSource = $this->getSource();
        $categorySource = (new Category())->getSource();
        $expandFields = '';
        $leftJoinExpandData = '';
        if($expandId) {
            $expand = Expand::getInfo($expandId, $dataCache);
            $expandDataSource = (new ExpandData($expand->table))->getSource();
            $leftJoinExpandData = 'LEFT JOIN ' . $expandDataSource . ' AS c ON c.category_content_id=a.id';
            $expandFieldList = ExpandField::getList('expand_id=' . $expandId, $dataCache)->toArray();
            $expandFields = array_column($expandFieldList, 'field');
            $expandFields = implode(',c.', $expandFields);
            if(!empty($expandFields)) {
                $expandFields = ',c.' . $expandFields;
            }
        }
        if(!empty($order)) {
            $order = 'ORDER BY a.' . $order;
        }
        $sql = "SELECT a.*,b.name as category_name,b.subname as category_subname,b.category_model_id{$expandFields}
        FROM {$categoryContentSource} AS a
        LEFT JOIN {$categorySource} AS b ON a.category_id=b.id
        {$leftJoinExpandData}
        {$where}
        {$order},a.id ASC
        {$limit}";
        $connect = $this->getReadConnection();
        $list = new Resultset(null, $this, $connect->query($sql));
        if($dataCache) {
            $cache->save($key, $list);
        }
        return $list;
    }
    
    public function getOne($parameters = null) {
        $system = $this->getDI()->getConfig()->system;
        return self::getInfo($parameters, (bool)$system->data_cache_on);
    }
    
    public function viewsIncrement(int $id, int $step = 1) {
        $phql = 'UPDATE ' . __CLASS__ . ' SET views=views+' . $step . ' WHERE id=' . $id;
        return self::getModelsManager()->executeQuery($phql);
    }
    
    /**
     * @desc 获取上一个栏目内容
     * @param int $fetchMode 结果集类型，1：对象，2：数组
     * @return object|bool
     * @author: ZhaoYang
     * @date: 2018年10月4日 上午3:15:47
     */
    public function getPrevContent($categoryContent, $category, int $fetchMode=1) {
        if(is_object($categoryContent)) {
            $categoryContent = $categoryContent->toArray();
        }
        if(is_object($category)) {
            $category = $category->toArray();
        }
        $system = $this->getDI()->getConfig()->system;
        $dataCache = (bool)$system->data_cache_on;
        if($dataCache) {
            $key = self::createCacheKey(__FUNCTION__, [$categoryContent, $category]);
            $cache = $this->getDI()->get('modelsCache');
            if($cache->exists($key)) {
                return $cache->get($key);
            }
        }
        list($field, $order) = explode(' ', $category['content_order']);
        $operators = trim($order) == 'ASC' ? '<' : '>';
        $where = "WHERE a.category_id={$category['id']} AND a.status=1 AND (a.{$field}{$operators}'{$categoryContent[$field]}' OR a.{$field}='{$categoryContent[$field]}' AND a.id<{$categoryContent['id']})";
        $categoryContentSource = $this->getSource();
        $categorySource = (new Category())->getSource();
        $expandFields = '';
        $leftJoinExpandData = '';
        if($category['expand_id']) {
            $expand = Expand::getInfo($category['expand_id'], $dataCache);
            $expandDataSource = (new ExpandData($expand->table))->getSource();
            $leftJoinExpandData = 'LEFT JOIN ' . $expandDataSource . ' AS c ON c.category_content_id=a.id';
            $expandFieldList = ExpandField::getList('expand_id=' . $category['expand_id'], $dataCache)->toArray();
            $expandFields = array_column($expandFieldList, 'field');
            $expandFields = implode(',c.', $expandFields);
            if(!empty($expandFields)) {
                $expandFields = ',c.' . $expandFields;
            }
        }
        $sql = "SELECT a.*,b.name as category_name,b.subname as category_subname,b.category_model_id{$expandFields}
        FROM {$categoryContentSource} AS a
        LEFT JOIN {$categorySource} AS b ON a.category_id=b.id
        {$leftJoinExpandData}
        {$where}
        ORDER BY a.{$category['content_order']},a.id ASC
        LIMIT 1";
        $connect = $this->getReadConnection();
        $info = $connect->fetchOne($sql, $fetchMode);
        if($dataCache) {
            $cache->save($key, $info);
        }
        return $info;
    }
    
    /**
     * @desc 获取下一个栏目内容
     * @return object|bool
     * @param int $fetchMode 结果集类型，1：对象，2：数组
     * @author: ZhaoYang
     * @date: 2018年10月4日 上午3:15:47
     */
    public function getNextContent($categoryContent, $category, int $fetchMode=1) {
        if(is_object($categoryContent)) {
            $categoryContent = $categoryContent->toArray();
        }
        if(is_object($category)) {
            $category = $category->toArray();
        }
        $system = $this->getDI()->getConfig()->system;
        $dataCache = (bool)$system->data_cache_on;
        if($dataCache) {
            $key = self::createCacheKey(__FUNCTION__, [$categoryContent, $category]);
            $cache = $this->getDI()->get('modelsCache');
            if($cache->exists($key)) {
                return $cache->get($key);
            }
        }
        list($field, $order) = explode(' ', $category['content_order']);
        $operators = trim($order) == 'ASC' ? '>' : '<';
        $where = "WHERE a.category_id={$category['id']} AND a.status=1 AND (a.{$field}{$operators}'{$categoryContent[$field]}' OR a.{$field}='{$categoryContent[$field]}' AND a.id>{$categoryContent['id']})";
        $categoryContentSource = $this->getSource();
        $categorySource = (new Category())->getSource();
        $expandFields = '';
        $leftJoinExpandData = '';
        if($category['expand_id']) {
            $expand = Expand::getInfo($category['expand_id'], $dataCache);
            $expandDataSource = (new ExpandData($expand->table))->getSource();
            $leftJoinExpandData = 'LEFT JOIN ' . $expandDataSource . ' AS c ON c.category_content_id=a.id';
            $expandFieldList = ExpandField::getList('expand_id=' . $category['expand_id'], $dataCache)->toArray();
            $expandFields = array_column($expandFieldList, 'field');
            $expandFields = implode(',c.', $expandFields);
            if(!empty($expandFields)) {
                $expandFields = ',c.' . $expandFields;
            }
        }
        $sql = "SELECT a.*,b.name as category_name,b.subname as category_subname,b.category_model_id{$expandFields}
        FROM {$categoryContentSource} AS a
        LEFT JOIN {$categorySource} AS b ON a.category_id=b.id
        {$leftJoinExpandData}
        {$where}
        ORDER BY a.{$category['content_order']},a.id ASC
        LIMIT 1";
        $connect = $this->getReadConnection();
        $info = $connect->fetchOne($sql, $fetchMode);
        if($dataCache) {
            $cache->save($key, $info);
        }
        return $info;
    }
    
    public function getCountBySearch(string $where, int $type=0) {
        $query = self::getModelsManager()->createBuilder()
        ->columns('count(a.id) AS num')
        ->addFrom('App\Home\Models\CategoryContent', 'a')
        ->leftJoin('App\Home\Models\Category', 'a.category_id=b.id', 'b');
        if($type == 2) {
            $query = $query->leftJoin('App\Home\Models\CategoryContentData', 'a.id=c.category_content_id', 'c');
        }
        $query = $query->where($where)->getQuery();
        $system = $this->getDI()->getConfig()->system;
        if($system->data_cache_on) {
            $query = $query->cache([
                'key' => self::createCacheKey(__FUNCTION__, [$where, $type])
            ]);
        }
        return (int)$query->getSingleResult()->num;
    }
    
    public function getListBySearch(string $where, int $limit, int $offset, int $type=0) {
        $query = self::getModelsManager()->createBuilder()
        ->columns('a.id,a.category_id,a.title,a.urltitle,a.subtitle,a.font_color,a.font_bold,a.keywords,a.description,a.updatetime,a.inputtime,a.image,a.url,a.sequence,a.tpl,a.status,a.copyfrom,a.views,a.position,a.taglink,b.name as category_name,b.subname as category_subname,b.category_model_id')
        ->addFrom('App\Home\Models\CategoryContent', 'a')
        ->leftJoin('App\Home\Models\Category', 'a.category_id=b.id', 'b');
        if($type == 2) {
            $query = $query->leftJoin('App\Home\Models\CategoryContentData', 'a.id=c.category_content_id', 'c');
        }
        $query = $query->where($where)
        ->orderBy('a.updatetime desc')
        ->limit($limit, $offset)
        ->getQuery();
        $system = $this->getDI()->getConfig()->system;
        if($system->data_cache_on) {
            $query = $query->cache([
                'key' => self::createCacheKey(__FUNCTION__, [$where, $type])
            ]);
        }
        return $query->execute();
    }
}