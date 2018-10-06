<?php
namespace App\Admin\Models;

use Models\CategoryContentData as ModelsCategoryContentData;
use Common\Common;

class CategoryContentData extends ModelsCategoryContentData {
    
    /** 
     * @desc 根据category_content_id删除记录 
     * @param int|string $categoryContentId 
     * @return bool 
     * @author ZhaoYang 
     * @date 2018年9月14日 下午2:02:45 
     */
    public function deleteByCategoryContentId($categoryContentId) {
        $phql = 'DELETE FROM App\Admin\Models\CategoryContentData WHERE category_content_id in(' . $categoryContentId . ')';
        return self::getModelsManager()->executeQuery($phql);
    }
    
    public function add(array $data) {
        $data = Common::arraySlice(['category_content_id', 'content'], $data);
        $data['content'] = htmlspecialchars($this->getDI()->getHtmlPurifier()->purify($data['content']));
        return $this->create($data);
    }
    
    public function edit(array $data) {
        $data = Common::arraySlice(['category_content_id', 'content'], $data);
        $data['content'] = htmlspecialchars($this->getDI()->getHtmlPurifier()->purify($data['content']));
        $info = self::findFirst('category_content_id=' . $data['category_content_id']);
        $this->assign($info->toArray());
        return $this->update($data);
    }
}