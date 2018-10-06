<?php
namespace App\Home\Controllers;

use App\Home\Models\Category;
use App\Home\Models\CategoryJump;

class CategoryjumpController extends CommonController {

    public function indexAction() {
        $id = $this->get('id', 'absint', 0);
        $category = new Category();
        $category = $category->getOne($id);
        if($category === false) {
            return $this->forward('error/error404');
        }
        $categoryJump = new CategoryJump();
        $categoryJump = $categoryJump->getOne('category_id=' . $id);
        if($categoryJump === false || empty($categoryJump->url)) {
            return $this->forward('error/error404');
        }
        return $this->response->redirect($categoryJump->url, substr($categoryJump->url, 0, 4) == 'http' ? true : false);
    }
}