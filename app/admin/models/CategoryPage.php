<?php
namespace App\Admin\Models;

use Models\CategoryPage as ModelsCategoryPage;
use Common\Common;

class CategoryPage extends ModelsCategoryPage {
    
    public function add(array $data) {
        $data = Common::arraySlice(['category_id', 'content'], $data);
        $data['content'] = htmlspecialchars($this->getDI()->getHtmlPurifier()->purify($data['content']));
        return $this->create($data);
    }
    
    public function updateByCategoryId(int $categoryId, string $content) {
        $content = htmlspecialchars($this->getDI()->getHtmlPurifier()->purify($content));
        $info = self::findFirst('category_id=' . $categoryId);
        if($info === false) {
            return $this->errorMessage('未找到该记录');
        }
        $this->assign($info->toArray());
        return $this->update([
            'content' => $content
        ]);
    }
}