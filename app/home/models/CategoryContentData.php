<?php
namespace App\Home\Models;

use Models\CategoryContentData as ModelsCategoryContentData;

class CategoryContentData extends ModelsCategoryContentData {
    
    public function getOne($parameters = null) {
        $system = $this->getDI()->getConfig()->system;
        return self::getInfo($parameters, (bool)$system->data_cache_on);
    }
}