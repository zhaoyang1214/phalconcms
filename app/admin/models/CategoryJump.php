<?php
namespace App\Admin\Models;

use Models\CategoryJump as ModelsCategoryJump;
use Common\Common;

class CategoryJump extends ModelsCategoryJump {
    
    public function add(array $data) {
        $data = Common::arraySlice(['category_id', 'url'], $data);
        return $this->create($data);
    }
    
    public function updateByCategoryId(int $categoryId, string $url) {
        $info = self::findFirst('category_id=' . $categoryId);
        if($info === false) {
            return $this->errorMessage('未找到该记录');
        }
        $this->assign($info->toArray());
        return $this->update([
            'url' => $url
        ]);
    }
}