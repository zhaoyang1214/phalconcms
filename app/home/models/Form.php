<?php
namespace App\Home\Models;

use Models\Form as ModelsForm;

class Form extends ModelsForm {
    
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
}