<?php
namespace App\Home\Controllers;

use App\Home\Models\Category;
use App\Home\Models\CategoryContent;
use App\Home\Models\ExpandField;

class CategorynewsController extends CommonController {

    public function indexAction() {
        if($this->config->system->html_cache_on) {
            $this->view->cache([
                'lifetime' => $this->config->system->html_other_cache_time,
                'key' => $this->request->getURI()
            ]);
            if ($this->view->getCache()->exists($this->request->getURI())) {
                return;
            }
        }
        $id = $this->get('id', 'absint', 0);
        $category = new Category();
        $category = $category->getOne($id);
        if($category === false) {
            return $this->forward('error/error404');
        }
        if($category->type == 1) {
            $categorySons = $category->getSons($id);
            $categoryIds = array_column($categorySons, 'id');
            $categoryIds[] = $category->id;
            $categoryIds = implode(',', $categoryIds);
        } else {
            $categoryIds = $id;
        }
        $where = 'a.category_id in(' . $categoryIds . ') AND a.status=1';
        $categoryContent = new CategoryContent();
        $count = $categoryContent->getCount($where);
        $listRows = intval($category->page) ? $category->page : 10;
        $paginator = $this->di->get('paginator', [$count, $listRows]);
        $list = $categoryContent->getContentList($where, $paginator->getLimit(true), $category->expand_id, $category->content_order);
        $parentCategory = $category->getOne($category->pid);
        $expandField = new ExpandField();
        $this->view->nav = $category->getParents($id);
        $this->view->list = $list;
        $this->view->paginator = $paginator;
        $this->view->parentCategory = $parentCategory;
        $this->view->common = $this->media($category->name, $category->keywords, $category->description);
        $this->view->topCategory = $category->getTopCategory($category->id);
        $this->view->category = $category;
        $this->view->expandField = $expandField;
        $this->view->expandFieldList = $expandField->getAll('expand_id=' . $category->expand_id);
        $renderView = empty($category->category_tpl) ? 'category/index' : $category->category_tpl;
        $this->view->pick($renderView);
    }
}