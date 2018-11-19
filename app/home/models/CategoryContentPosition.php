<?php
namespace App\Home\Models;

use Models\CategoryContentPosition as ModelsCategoryContentPosition;

class CategoryContentPosition extends ModelsCategoryContentPosition {
    
    public function getOne($parameters = null) {
        $system = $this->getDI()->getConfig()->system;
        return self::getInfo($parameters, (bool)$system->data_cache_on);
    }
}