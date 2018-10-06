<?php
namespace App\Home\Models;

use Models\CategoryJump as ModelsCategoryJump;

class CategoryJump extends ModelsCategoryJump {
    
    public function getOne($parameters = null) {
        $system = $this->getDI()->getConfig()->system;
        return self::getInfo($parameters, (bool)$system->data_cache_on);
    }
}