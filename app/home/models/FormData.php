<?php
namespace App\Home\Models;

use Models\FormData as ModelsFormData;

class FormData extends ModelsFormData {
    
    public function getCount($parameters = [ ]) {
        if(is_numeric($parameters)) {
            $parameters = [
                'conditions' => 'id=' . $parameters
            ];
        } else if(is_string($parameters)) {
            $parameters = [
                'conditions' => $parameters
            ];
        }
        $system = $this->getDI()->getConfig()->system;
        if($system->data_cache_on && !isset($parameters['cache'])) {
            $parameters['cache'] = $parameters['cache'] ?? [];
            $parameters['cache']['key'] = $parameters['cache']['key'] ?? self::createCacheKey(__FUNCTION__, $parameters);
        }
        return self::count($parameters);
    }
    
    public function getAll($parameters = [ ]) {
        $system = $this->getDI()->getConfig()->system;
        return self::getList($parameters, (bool)$system->data_cache_on);
    }
    
    public function getAllByTableName(string $tableName, $parameters = [ ]) {
        $form = (new Form())->getOne('table="' . $tableName . '"');
        if($form === false) {
            return false;
        }
        if(is_numeric($parameters)) {
            $parameters = [
                'conditions' => 'id=' . $parameters
            ];
        } else if(is_string($parameters)) {
            $parameters = [
                'conditions' => $parameters
            ];
        }
        $parameters['order'] = isset($parameters['order']) ? rtrim($parameters['order'], ',') . ',' . $form->sort : $form->sort;
        static::$_tableName = 'form_data_' . $tableName;
        $this->setSource(static::$_tablePrefix . static::$_tableName);
        return self::getAll($parameters);
    }
    
    /** 
     * @desc 格式化返回数据 
     * @param string $tableName 表名
     * @param mixed $parameters 参数
     * @return array
     * @author ZhaoYang 
     * @date 2018年11月9日 下午5:51:27 
     */
    public function getAllFormat(string $tableName, $parameters = [ ]) {
        $form = (new Form())->getOne('table="' . $tableName . '"');
        if($form === false) {
            return false;
        }
        $formField = new FormField();
        $formFieldList = $formField->getAll([
            'conditions' => 'form_id=' . $form->id,
            'order' => 'sequence ASC'
        ])->toArray();
        $formDatalist = self::getAllByTableName($tableName, $parameters);
        $columns = isset($parameters['columns']) ? explode(',', $parameters['columns']) : array_column($formFieldList, 'field');
        $list = [];
        foreach ($formDatalist as $formData) {
            $data = [];
            foreach ($columns as $column) {
                $data[$column] = $formField->getFieldValue($formFieldList, $column, $formData->{$column});
            }
            $list[] = $data;
        }
        return $list;
    }
}