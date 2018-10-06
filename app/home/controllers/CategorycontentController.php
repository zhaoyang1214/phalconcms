<?php
namespace App\Home\Controllers;

use App\Home\Models\CategoryContent;
use App\Home\Models\Category;
use App\Home\Models\CategoryContentData;
use App\Home\Models\ExpandData;
use App\Home\Models\Expand;
use App\Home\Models\Replace;
use App\Home\Models\Tags;
use Library\Tools\Paginator;

class CategorycontentController extends CommonController {
    
    public function __call($name, $args) {
        $urltitle = $this->filter->sanitize($this->dispatcher->getActionName(), ['string', 'trim']);
        $categoryContent = (new CategoryContent())->getOne([
            'conditions' => 'urltitle=:urltitle: AND status=1',
            'bind' => [
                'urltitle' => $urltitle
            ]
        ]);
        if($categoryContent === false) {
            return $this->forward('error/error404');
        }
        $data = $this->get();
        $data['id'] = $categoryContent->id;
//         $data['categoryContent'] = $categoryContent->toArray();
        return $this->forward('Categorycontent/index', $data);
    }
    
    public function indexAction() {
        $id = $this->get('id', 'absint', 0);
        $categoryContent = (new CategoryContent())->getOne($id);
        if($categoryContent === false || $categoryContent->status != 1) {
            return $this->forward('error/error404');
        }
        $categoryContent->viewsIncrement($id);
        if($this->config->system->html_cache_on) {
            $this->view->cache([
                'lifetime' => $this->config->system->html_other_cache_time,
                'key' => $this->request->getURI()
            ]);
            if ($this->view->getCache()->exists($this->request->getURI())) {
                return;
            }
        }
        if(!empty($categoryContent->url)) {
            return $this->response->redirect($categoryContent->url, substr($categoryContent->url, 0, 4) == 'http' ? true : false);
        }
        $category = (new Category())->getOne($categoryContent->category_id);
        if($category->expand_id) {
            $expand = (new Expand())->getOne($category->expand_id);
            $expandData = (new ExpandData($expand->table))->getOne('category_content_id=' . $categoryContent->id);
            $this->view->expandData = $expandData;
        }
        $categoryContentData = (new CategoryContentData())->getOne('category_content_id=' . $categoryContent->id);
        $contentArr = explode('[page]', htmlspecialchars_decode($categoryContentData->content));
        $paginator = new Paginator(count($contentArr), 1);
        $content = array_slice($contentArr, $paginator->getOffset(), $paginator->getLimit());
        $content = implode('', $content);
        if(!empty($content)) {
            $content = (new Replace())->replaceContent($content);
            if($categoryContent->taglink) {
                $content = (new Tags())->tagLink($content, $categoryContent->id);
            }
        }
        $this->view->category = $category;
        $this->view->categoryContent = $categoryContent;
        $this->view->content = $content;
        $this->view->paginator = $paginator;
        $this->view->prevCategoryContent = $categoryContent->getPrevContent($categoryContent, $category);
        $this->view->nextCategoryContent = $categoryContent->getNextContent($categoryContent, $category);
        $this->view->nav = $category->getParents($category->id);
        $this->view->parentCategory = $category->getOne($category->pid);
        $this->view->topCategory = $category->getAll('pid=0');
        $this->view->common = $this->media($categoryContent->title . '-' . $category->name, $categoryContent->keywords, $categoryContent->description);
        $renderView = empty($category->content_tpl) ? 'categorycontent/index' : $category->content_tpl;
        $this->view->pick($renderView);
    }
}