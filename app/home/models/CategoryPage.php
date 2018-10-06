<?php
namespace App\Home\Models;

use Models\CategoryPage as ModelsCategoryPage;

class CategoryPage extends ModelsCategoryPage {
    
    public function getOne($parameters = null) {
        $system = $this->getDI()->getConfig()->system;
        return self::getInfo($parameters, (bool)$system->data_cache_on);
    }
}