<?php
namespace App\Home\Models;

use Models\Expand as ModelsExpand;

class Expand extends ModelsExpand {
    
    public function getOne($parameters = null) {
        $system = $this->getDI()->getConfig()->system;
        return self::getInfo($parameters, (bool)$system->data_cache_on);
    }
}