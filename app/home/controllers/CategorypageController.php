<?php
namespace App\Home\Controllers;

use App\Home\Models\Category;
use App\Home\Models\CategoryPage;
use App\Home\Models\Replace;

class CategorypageController extends CommonController {

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
        $categoryPage = new CategoryPage();
        $categoryPage = $categoryPage->getOne('category_id=' . $id);
        if($categoryPage === false) {
            return $this->forward('error/error404');
        }
        $contentArr = explode('[page]', htmlspecialchars_decode($categoryPage->content));
        $paginator = $this->di->get('paginator', [count($contentArr), 1]);
        $content = array_slice($contentArr, $paginator->getOffset(), $paginator->getLimit());
        $content = (new Replace())->replaceContent(implode('', $content));
        $this->view->nav = $category->getParents($id);
        $this->view->category = $category;
        $this->view->content = $content;
        $this->view->paginator = $paginator;
        $this->view->parentCategory = $category->getOne($category->pid);
        $this->view->common = $this->media($category->name, $category->keywords, $category->description);
        $this->view->topCategory = $category->getTopCategory($category->id);
        $renderView = empty($category->category_tpl) ? 'categorypage/index' : $category->category_tpl;
        $this->view->pick($renderView);
    }
}