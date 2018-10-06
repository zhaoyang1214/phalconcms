<?php
namespace App\Home\Models;

use Models\Fragment as ModelsFragment;

class Fragment extends ModelsFragment {
    
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
    
    public function getOneBySign(string $sign, $languageId = null) {
        if($languageId !== false) {
            $languageId = ($languageId && is_int($languageId)) ? $languageId : LANGUAGE_ID;
            $parameters = [
                'conditions' => 'sign=:sign: AND language_id=' . $languageId,
                'bind' => [
                    'sign' => $sign
                ]
            ];
        }
        $system = $this->getDI()->getConfig()->system;
        return self::getInfo($parameters, (bool)$system->data_cache_on);
    }
    
    public function getAll($parameters = [ ], $languageId = null) {
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
        return self::getList($parameters, (bool)$system->data_cache_on);
    }
}