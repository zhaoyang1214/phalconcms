<?php
namespace App\Admin\Models;

use Models\CategoryContentPosition as ModelsCategoryContentPosition;

class CategoryContentPosition extends ModelsCategoryContentPosition {
    
    public function addAll(array $positionIdArr, int $categoryContentId) {
        foreach ($positionIdArr as $positionId) {
            $result = $this->create([
                'category_content_id' => $categoryContentId,
                'position_id' => (int)$positionId
            ]);
            if($result === false) {
                return false;
            }
            $this->reset();
            unset($this->id);
        }
        return true;
    }
    
    public function deleteByPositionIds($positionIds) {
        $phql = 'DELETE FROM App\Admin\Models\CategoryContentPosition WHERE position_id in(' . $positionIds . ')';
        return self::getModelsManager()->executeQuery($phql);
    }
    
    public function deleteByCategoryContentId(int $categoryContentId) {
        $phql = 'DELETE FROM App\Admin\Models\CategoryContentPosition WHERE category_content_id=' . $categoryContentId;
        return self::getModelsManager()->executeQuery($phql);
    }
}