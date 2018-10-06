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
}