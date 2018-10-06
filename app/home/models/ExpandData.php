<?php
namespace App\Home\Models;

use Models\ExpandData as ModelsExpandData;

class ExpandData extends ModelsExpandData {
    
    public function getOne($parameters = null) {
        $system = $this->getDI()->getConfig()->system;
        return self::getInfo($parameters, (bool)$system->data_cache_on);
    }
}