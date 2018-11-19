<?php
namespace App\Home\Models;

use Phalcon\Mvc\Model as MvcModel;
use Common\Common;
use Phalcon\Text;

class Model extends MvcModel {
    
    public function table(string $table) {
        $dbConfig = $this->getDI()->getConfig()->services->db;
        $this->useDynamicUpdate($dbConfig->use_dynamic_update);
        $ormOptions = Common::convertArrKeyUnderline($dbConfig->orm_options->toArray());
        $this->setup($ormOptions);
        $this->setSource($dbConfig->prefix . $table);
        return $this;
    }
    
    public function getModel(string $modelName) {
        $class = 'App\\Home\\Models\\' . ucfirst($modelName);
        if(class_exists($class)) {
            return new $class();
        }
        return false;
    }
    
    public function createCacheKey(string $functionName = '', array $parameters = []) {
        if (isset($parameters['cache'])) {
            unset($parameters['cache']);
        }
        $key = 'model_';
        $tableNmae = $this->getSource();
        if (!empty($tableNmae)) {
            $key .= $tableNmae . '_';
        }
        if (!empty($functionName)) {
            $key .= $functionName . '_';
        }
        if (!empty($parameters)) {
            $key .= md5(json_encode($parameters));
        }
        return $key;
    }
    
    /** 
     * @desc 获取单条记录 
     * @param mixed $parameters 查询参数
     * @param mixed $languageId 为false则不受语言限制，为null使用默认语言限制，为正整数则使用参数
     * @author ZhaoYang 
     * @date 2018年9月30日 上午10:41:48 
     */
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
        if($system->data_cache_on && !isset($parameters['cache'])) {
            $parameters['cache'] = $parameters['cache'] ?? [];
            $parameters['cache']['key'] = $parameters['cache']['key'] ?? self::createCacheKey(__FUNCTION__, $parameters);
        }
        return self::findFirst($parameters);
    }
    
    /**
     * @desc 获取多条记录
     * @param mixed $parameters 查询参数
     * @param mixed $languageId 为false则不受语言限制，为null使用默认语言限制，为正整数则使用参数
     * @author ZhaoYang
     * @date 2018年9月30日 上午10:41:48
     */
    public function getAll($parameters = null, $languageId = null) {
        if($languageId !== false) {
            $languageId = ($languageId && is_int($languageId)) ? $languageId : LANGUAGE_ID;
            if(is_null($parameters)) {
                $parameters['conditions'] = 'language_id=' . $languageId;
            } else if(is_numeric($parameters)) {
                $parameters['conditions'] = 'id=' . $parameters . ' AND language_id=' . $languageId;
            } else if(is_string($parameters)) {
                $parameters['conditions'] .= ' AND language_id=' . $languageId;
            } else if(is_array($parameters)) {
                $parameters['conditions'] = $parameters['conditions'] ?? '';
                $parameters['conditions'] .=  (empty($parameters['conditions']) ? '' : ' AND ') . 'language_id=' . $languageId;
            }
        }
        $system = $this->getDI()->getConfig()->system;
        if($system->data_cache_on && !isset($parameters['cache'])) {
            $parameters['cache'] = $parameters['cache'] ?? [];
            $parameters['cache']['key'] = $parameters['cache']['key'] ?? self::createCacheKey(__FUNCTION__, $parameters);
        }
        return self::find($parameters);
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
            if(is_null($parameters)) {
                $parameters['conditions'] = 'language_id=' . $languageId;
            } else if(is_numeric($parameters)) {
                $parameters['conditions'] = 'id=' . $parameters . ' AND language_id=' . $languageId;
            } else if(is_string($parameters)) {
                $parameters['conditions'] .= ' AND language_id=' . $languageId;
            } else if(is_array($parameters)) {
                $parameters['conditions'] = $parameters['conditions'] ?? '';
                $parameters['conditions'] .=  (empty($parameters['conditions']) ? '' : ' AND ') . 'language_id=' . $languageId;
            }
        }
        $system = $this->getDI()->getConfig()->system;
        if($system->data_cache_on && !isset($parameters['cache'])) {
            $parameters['cache'] = $parameters['cache'] ?? [];
            $parameters['cache']['key'] = $parameters['cache']['key'] ?? self::createCacheKey(__FUNCTION__, $parameters);
        }
        return self::count($parameters);
    }
    
}