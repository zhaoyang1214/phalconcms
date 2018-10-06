<?php
namespace App\Admin\Models;

use Models\AdminLog as ModelsAdminLog;

class AdminLog extends ModelsAdminLog {
    
    public function beforeCreate() {
        $this->logintime = date('Y-m-d H:i:s');
    }
}