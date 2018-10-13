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
        $parameters['order'] = isset($parameters['order']) ? $parameters['order'] . $form->sort : $form->sort;
        static::$_tableName = 'form_data_' . $tableName;
        $this->setSource(static::$_tablePrefix . static::$_tableName);
        return self::getAll($parameters);
    }
}