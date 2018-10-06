<?php
namespace App\Home\Models;

use Models\Category as ModelsCategory;

class Category extends ModelsCategory {
    
    /**
     * @desc 获取单条记录
     * @param mixed $parameters 查询参数
     * @param mixed $languageId 为false则不受语言限制，为null使用默认语言限制，为正整数则使用参数
     * @author ZhaoYang
     * @date 2018年9月30日 上午10:41:48
     */
    public function getOne($parameters = null, $languageId = null) {
        if($languageId !== false) {
            $languageId = ($languageId && is_int($languageId)) ? $languageId : LANGUAGE_ID;
            if(is_numeric($parameters)) {
                $parameters = [
                    'conditions' => 'id=' . $parameters . ' AND is_show=1 AND language_id=' . $languageId
                ];
            } else if(is_string($parameters)) {
                $parameters = [
                    'conditions' => $parameters . ' AND is_show=1 AND language_id=' . $languageId
                ];
            } else if(is_array($parameters)) {
                $parameters['conditions'] = $parameters['conditions'] ?? '';
                $parameters['conditions'] .=  (empty($parameters['conditions']) ? '' : ' AND ') . ' is_show=1 AND language_id=' . $languageId;
            }
        }
        $system = $this->getDI()->getConfig()->system;
        return self::getInfo($parameters, (bool)$system->data_cache_on);
    }
    
    /**
     * @desc 获取多条记录
     * @param mixed $parameters 查询参数
     * @param mixed $languageId 为false则不受语言限制，为null使用默认语言限制，为正整数则使用参数
     * @author ZhaoYang
     * @date 2018年9月30日 上午10:41:48
     */
    public function getAll($parameters = [ ], $languageId = null) {
        if($languageId !== false) {
            $languageId = ($languageId && is_int($languageId)) ? $languageId : LANGUAGE_ID;
            if(is_numeric($parameters)) {
                $parameters = [
                    'conditions' => 'id=' . $parameters . ' AND is_show=1 AND language_id=' . $languageId
                ];
            } else if(is_string($parameters)) {
                $parameters = [
                    'conditions' => $parameters . ' AND is_show=1 AND language_id=' . $languageId
                ];
            } else if(is_array($parameters)) {
                $parameters['conditions'] = $parameters['conditions'] ?? '';
                $parameters['conditions'] .=  (empty($parameters['conditions']) ? '' : ' AND ') . ' is_show=1 AND language_id=' . $languageId;
            }
        }
        $system = $this->getDI()->getConfig()->system;
        return self::getList($parameters, (bool)$system->data_cache_on);
    }
    
    /**
     * @desc 获取总记录数
     * @param mixed $parameters 查询参数
     * @param mixed $languageId 为false则不受语言限制，为null使用默认语言限制，为正整数则使用参数
     * @author ZhaoYang
     * @date 2018年9月30日 上午10:41:48
     */
    public function getCount($parameters = null, $languageId = null) {
        if($languageId !== false) {
            $languageId = ($languageId && is_int($languageId)) ? $languageId : LANGUAGE_ID;
            if(is_numeric($parameters)) {
                $parameters = [
                    'conditions' => 'id=' . $parameters . ' AND is_show=1 AND language_id=' . $languageId
                ];
            } else if(is_string($parameters)) {
                $parameters = [
                    'conditions' => $parameters . ' AND is_show=1 AND language_id=' . $languageId
                ];
            } else if(is_array($parameters)) {
                $parameters['conditions'] = $parameters['conditions'] ?? '';
                $parameters['conditions'] .=  (empty($parameters['conditions']) ? '' : ' AND ') . ' is_show=1 AND language_id=' . $languageId;
            }
        }
        $system = $this->getDI()->getConfig()->system;
        if($system->data_cache_on && !isset($parameters['cache'])) {
            $parameters['cache'] = $parameters['cache'] ?? [];
            $parameters['cache']['key'] = $parameters['cache']['key'] ?? self::createCacheKey(__FUNCTION__, $parameters);
        }
        return self::count($parameters);
    }
    
    /**
     * @desc 获取栏目及其子栏目
     * @author: ZhaoYang
     * @date: 2018年10月2日 上午12:43:52
     */
    public function categoryGroup(int $id=0, int $maxDepth=null) {
        $category = $this->getAll()->toArray();
        $toolsCategory = new \Library\Tools\Category($category);
        $category = $toolsCategory->setMaxDepth($maxDepth)->categoryGroup($id);
        return $category;
    }
    
    /**
     * @desc 获取导航
     * @author: ZhaoYang
     * @date: 2018年10月2日 上午12:43:36
     */
    public function getParents(int $id=0, int $maxDepth = null) {
        $category = $this->getAll()->toArray();
        $toolsCategory = new \Library\Tools\Category($category);
        $parents = $toolsCategory->setMaxDepth($maxDepth)->getParents($id);
        return array_reverse($parents);
    }
    
    public function getSons(int $id=0, int $maxDepth = null) {
        $category = $this->getAll()->toArray();
        $toolsCategory = new \Library\Tools\Category($category);
        $sons = $toolsCategory->setMaxDepth($maxDepth)->getSons($id);
        return $sons;
    }
}