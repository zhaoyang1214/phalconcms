<?php
namespace App\Home\Models;

use Models\FormField as ModelsFormField;

class FormField extends ModelsFormField {
    
    public function getAll($parameters = [ ]) {
        $system = $this->getDI()->getConfig()->system;
        return self::getList($parameters, (bool)$system->data_cache_on);
    }
}