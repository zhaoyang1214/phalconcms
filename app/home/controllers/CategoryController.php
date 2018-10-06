<?php
namespace App\Home\Controllers;

use App\Home\Models\Category;
use App\Home\Models\CategoryModel;

class CategoryController extends CommonController {
    
    public function __call($name, $args) {
        $urlname = $this->filter->sanitize($this->dispatcher->getActionName(), ['string', 'trim']);
        $category = (new Category())->getOne([
            'conditions' => 'urlname=:urlname:',
            'bind' => [
                'urlname' => $urlname
            ]
        ]);
        if($category === false) {
            return $this->forward('error/error404');
        }
        $categoryModel = CategoryModel::getInfo($category->category_model_id, true);
        $data = $this->get();
        $data['id'] = $category->id;
        return $this->forward($categoryModel->category . '/index', $data);
    }
}