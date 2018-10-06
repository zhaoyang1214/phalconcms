<?php
namespace App\Admin\Models;

use Models\TagsRelation as ModelsTagsRelation;

class TagsRelation extends ModelsTagsRelation {
    
    public function deleteByTagsId(int $tagsId) {
        $phql = 'DELETE FROM App\Admin\Models\TagsRelation WHERE tags_id=' . $tagsId;
        return self::getModelsManager()->executeQuery($phql);
    }
    
    public function deleteById($id) {
        $phql = 'DELETE FROM App\Admin\Models\TagsRelation WHERE id IN(' . $id . ')';
        return self::getModelsManager()->executeQuery($phql);
    }
    
    public function deleteByCategoryContentId(int $categoryContentId) {
        $phql = 'DELETE FROM App\Admin\Models\TagsRelation WHERE category_content_id=' . $categoryContentId;
        return self::getModelsManager()->executeQuery($phql);
    }
}