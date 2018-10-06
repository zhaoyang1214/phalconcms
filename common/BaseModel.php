<?php
/**
 * @desc 基础模型
 * @author: ZhaoYang
 * @date: 2018年6月19日 下午9:31:51
 */
namespace Common;

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Exception;
use Phalcon\Mvc\Model\Message;

class BaseModel extends Model {

    protected static $_tableName;

    protected static $_tablePrefix;
    
    /**
     * @desc 初始化
     * @author: ZhaoYang
     * @date: 2018年6月19日 下午9:32:08
     */
    public function initialize() {
        $dbConfig = $this->getDI()->getConfig()->services->db;
        static::$_tablePrefix = $dbConfig->prefix;
        $this->useDynamicUpdate($dbConfig->use_dynamic_update);
        $ormOptions = Common::convertArrKeyUnderline($dbConfig->orm_options->toArray());
        $this->setup($ormOptions);
        $this->setSource(static::$_tablePrefix . static::$_tableName);
    }

    /**
     * @desc 创建缓存键名
     * @param string $functionName 当前方法名
     * @param array $parameters 参数
     * @return: string 键名
     * @author: ZhaoYang
     * @date: 2018年7月8日 下午5:57:08
     */
    public static function createCacheKey(string $functionName = '', array $parameters = []) {
        if (isset($parameters['cache'])) {
            unset($parameters['cache']);
        }
        $key = 'model_';
        $tableNmae = static::$_tableName;
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
     * @param int|string|array $parameters 查询参数
     * @param bool $cache 是否缓存
     * @param int $lifetime 缓存时间，默认使用配置中的时间
     * @return: Model
     * @author: ZhaoYang
     * @date: 2018年7月8日 下午8:18:12
     */
    public static function getInfo($parameters, bool $cache = false, int $lifetime = null) {
        if (is_numeric($parameters)) {
            $parameters = [ 
                'conditions' => 'id=:id:',
                'bind' => [ 
                    'id' => $parameters
                ]
            ];
        }else if(is_string($parameters)){
            $parameters = [
                'conditions' => $parameters,
            ];
        }
        if ($cache) {
            if(!isset($parameters['cache']['key'])) {
                $parameters['cache'] = [
                    'key' => self::createCacheKey(__FUNCTION__, $parameters)
                ];
            }
            if (!is_null($lifetime)) {
                $parameters['cache']['lifetime'] = $lifetime;
            }
        }
        return self::findFirst($parameters);
    }
    
    /**
     * @desc 获取多条记录
     * @param string|array $parameters 查询参数
     * @param bool $cache 是否缓存
     * @param int $lifetime 缓存时间，默认使用配置中的时间
     * @author: ZhaoYang
     * @date: 2018年7月14日 下午10:15:41
     */
    public static function getList($parameters, bool $cache = false, int $lifetime = null) {
        if(is_string($parameters)){
            $parameters = [
                'conditions' => $parameters,
            ];
        }
        if ($cache) {
            if(!isset($parameters['cache']['key'])) {
                $parameters['cache'] = [
                    'key' => self::createCacheKey(__FUNCTION__, $parameters)
                ];
            }
            if (!is_null($lifetime)) {
                $parameters['cache']['lifetime'] = $lifetime;
            }
        }
        return self::find($parameters);
    }

    /**
     * @desc 删除缓存
     * @param string $cacheKey 缓存键名
     * @return: bool
     * @author: ZhaoYang
     * @date: 2018年7月8日 下午5:15:09
     */
    public function deleteCache(string $cacheKey) {
        $cache = $this->getDI()->getModelsCache();
        $deleteResult = true;
        if ($cache->exists($cacheKey)) {
            $deleteResult = $cache->delete($cacheKey);
        }
        return $deleteResult;
    }
    
    /**
     * @desc 根据前缀删除模型缓存
     * @param string $cachePrefix 缓存前缀或键名
     * @return: bool
     * @author: ZhaoYang
     * @date: 2018年7月8日 下午4:59:49
     */
    public function deleteCacheByPrefix(string $cachePrefix = null) {
        $di = $this->getDI();
        $modelsCacheConfig = $di->getConfig()->services->models_cache;
        $prefix = $modelsCacheConfig->backend->prefix ?? '';
        $cache = $di->getModelsCache();
        $keys = $cache->queryKeys($cachePrefix);
        foreach ($keys as $key) {
            $key = substr($key, strlen($prefix));
            $cache->delete($key);
        }
        return true;
    }

    /**
     * @desc 向模型中注入错误信息
     * @param string|\Phalcon\Validation\Message\Group|\Phalcon\Validation\Message|\Phalcon\Mvc\Model\Message $message 错误信息内容或对象
     * @param string|array $field=null 字段
     * @param string $type=null 错误信息类型
     * @param \Phalcon\Mvc\ModelInterface $model=null 模型
     * @param int|null $code=null 错误信息提示码
     * @return: bool
     * @author: ZhaoYang
     * @date: 2018年7月12日 上午2:10:34
     */
    public function errorMessage($message, $field = null, string $type = null, \Phalcon\Mvc\ModelInterface $model = null, int $code = null) {
        if (is_string($message)) {
            $this->appendMessage(new Message($message, $field, $type, $model, $code));
        } else if ($message instanceof \Phalcon\Validation\Message) {
            $this->appendMessage(new Message($message->getMessage(), $message->getField(), $message->getType(), $model, $message->getCode()));
        } else if ($message instanceof \Phalcon\Mvc\Model\Message) {
            $this->appendMessage($message);
        } else if ($message instanceof \Phalcon\Validation\Message\Group) {
            foreach ($message as $msg) {
                $this->appendMessage(new Message($msg->getMessage(), $msg->getField(), $msg->getType(), $model, $msg->getCode()));
            }
        } else {
            throw new Exception('$message参数错误');
        }
        return false;
    }
    
    /**
     * @desc 获取验证规则
     * @param array $indexs 验证规则的数组索引，为[]时获取全部规则
     * @param bool $isMustCheck 是否必须验证
     * @return: array
     * @author: ZhaoYang
     * @date: 2018年7月12日 上午12:40:28
     */
    public function getRules(array $indexs = [], bool $isMustCheck = true) {
        $rules = static::rules();
        if (!is_array($rules)) {
            throw new Exception('数组规则错误');
        }
        if (empty($indexs)) {
            foreach ($rules as $k => $v) {
                $rules[$k][4] = $isMustCheck ? 1 : 0;
            }
            return $rules;
        }
        $returnRules = [ ];
        foreach ($indexs as $index) {
            if (!isset($rules[$index])) {
                throw new Exception("索引为{$index}的规则不存在");
            }
            $returnRules[$index] = $rules[$index];
            $returnRules[$index][4] = $isMustCheck ? 1 : 0;
        }
        return $returnRules;
    }
    
    /**
     * @desc 获取表名
     * @return string
     * @author: ZhaoYang
     * @date: 2018年8月13日 上午2:06:04
     */
    public static function getTableName() {
        return static::$_tableName;
    }

    /**
     * @desc 清除单个模型元数据
     * @param string $tableName 对于扩展表和表单创建的表需要传递表名
     * @author ZhaoYang
     * @date 2018年8月13日 上午10:16:58
     */
    public function clearModelsMetadata(string $tableName = null) {
        $namespaceToArr = explode('\\', static::class);
        $className = end($namespaceToArr);
        $modelsMetadataConfig = $this->getDI()->getConfig()->services->models_metadata->options;
        foreach (MODULE_ALLOW_LIST as $v) {
            $class = APP_NAMESPACE . '\\' . ucfirst($v) . '\\Models\\' . $className;
            if (class_exists($class)) {
                $model = new $class($tableName);
                if ($modelsMetadataConfig->adapter == 'files') {
                    $prepareVirtualPath = strtolower(str_replace('\\', '_', $class));
                    $mapFile = $modelsMetadataConfig->meta_data_dir . 'map-' . $prepareVirtualPath . '.php';
                    $metaFile = $modelsMetadataConfig->meta_data_dir . 'meta-' . $prepareVirtualPath . '-' . $model->getSource() . '.php';
                    if (is_file($mapFile)) {
                        @unlink($mapFile);
                    }
                    if (is_file($metaFile)) {
                        @unlink($metaFile);
                    }
                } else {
                    $modelsMetadata = $model->getModelsMetaData();
                    $modelsMetadata->readMetaData($model);
                    $modelsMetadata->reset();
                }
            }
        }
    }
    
    /** 
     * @desc 清除所有元数据（扩展表和表单创建的表除外） 
     * @author ZhaoYang 
     * @date 2018年8月13日 下午4:58:24 
     */
    public function clearAllModelsMetadata() {
        $services = $this->getDI()->getConfig()->services;
        $modelsMetadataConfig = $services->models_metadata->options;
        if ($modelsMetadataConfig->adapter == 'files') {
            Common::delDir($modelsMetadataConfig->meta_data_dir);
        } else {
            $modelFilesPath = BASE_PATH . 'models/';
            if(is_dir($modelFilesPath)) {
                $modelFiles = scandir($modelFilesPath);
                foreach ($modelFiles as $modelFile) {
                    $skipFiles = ['.', '..', 'FormData.php', 'ExpandData.php'];
                    if(in_array($modelFile, $skipFiles)) {
                        continue;
                    }
                    $class = 'Models\\' . basename($modelFile, '.php');
                    if(class_exists($class)) {
                        (new $class())->clearModelsMetadata();
                    }
                }
            }
        }
    }
}