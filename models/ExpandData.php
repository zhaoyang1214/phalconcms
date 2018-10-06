<?php
namespace Models;

use Common\BaseModel;

class ExpandData extends BaseModel {
    
    public function onConstruct($_tableName = null) {
        if(isset($_tableName)) {
            static::$_tableName = 'expand_data_' . $_tableName;
            $this->setSource(static::$_tablePrefix . static::$_tableName);
        }
    }
    
    public function setTableName($_tableName = null) {
        static::$_tableName = 'expand_data_' . $_tableName;
        $this->setSource(static::$_tablePrefix . static::$_tableName);
    }
    
}