<?php
namespace App\Home\Models;

use Models\Form as ModelsForm;

class Form extends ModelsForm {
    
    public function getOne($parameters = null, $languageId = null) {
        if($languageId !== false) {
            $languageId = ($languageId && is_int($languageId)) ? $languageId : LANGUAGE_ID;
            if(is_numeric($parameters)) {
                $parameters = [
                    'conditions' => 'id=' . $parameters . ' AND language_id=' . $languageId
                ];
            } else if(is_string($parameters)) {
                $parameters = [
                    'conditions' => $parameters . ' AND language_id=' . $languageId
                ];
            } else if(is_array($parameters)) {
                $parameters['conditions'] = $parameters['conditions'] ?? '';
                $parameters['conditions'] .=  (empty($parameters['conditions']) ? '' : ' AND ') . 'language_id=' . $languageId;
            }
        }
        $system = $this->getDI()->getConfig()->system;
        return self::getInfo($parameters, (bool)$system->data_cache_on);
    }
}