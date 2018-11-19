<?php
namespace App\Home\Controllers;

use App\Home\Models\Category;
use App\Home\Models\CategoryContent;

class SearchController extends CommonController {
    
    public function indexAction() {
        if($this->config->system->html_cache_on) {
            $this->view->cache([
                'lifetime' => $this->config->system->html_search_cache_time,
                'key' => $this->request->getURI()
            ]);
            if ($this->view->getCache()->exists($this->request->getURI())) {
                return;
            }
        }
        $keyword = $this->get('keyword');
        if(empty($keyword)) {
            return $this->error('没有关键词！');
        }
        $keywords = preg_replace ('/\s+/',' ',$keyword); 
        $keywords=explode(' ',$keywords);
        $categoryId = $this->get('category_id', 'absint', 0);
        $category = new Category();
        $categoryGroup = $category->categoryGroup($categoryId);
        $categoryIds = array_column($categoryGroup, 'id');
        $categoryIds = implode(',', $categoryIds);
        $where = '';
        if(!empty($categoryIds)) {
            $where = 'a.category_id in(' . $categoryIds . ') AND ';
        }
        $where .= 'a.status=1 AND ';
        $type = $this->get('type', 'int!', 0);
        $where2 = [];
        foreach ($keywords as $value) {
            switch ($type) {
                // 标题+描述+关键词
                case 1:
                    $where2[] = 'a.title LIKE "%' . $value . '%" OR a.keywords LIKE "%' . $value . '%" OR a.description LIKE "%' . $value . '%"';
                    break;
                // 标题+描述+关键词+全文
                case 2:
                    $where2[] = 'a.title LIKE "%' . $value . '%" OR a.keywords LIKE "%' . $value . '%" OR a.description LIKE "%' . $value . '%" OR c.content LIKE "%' . $value . '%"';
                    break;
                // 标题
                default:
                    $where2[] = 'a.title LIKE "%' . $value . '%"';
                    break;
            }
        }
        $where .= '(' . implode(' OR ', $where2) . ')';
        $categoryContent = new CategoryContent();
        $count = $categoryContent->getCountBySearch($where, $type);
        $listRows = intval($this->config->system->tpl_seach_page) <= 0 ? 10 : $this->config->system->tpl_seach_page;
        $paginator = $this->di->get('paginator', [$count, $listRows]);
        $list = $categoryContent->getListBySearch($where, $paginator->getLimit(), $paginator->getOffset(), $type);
        $this->view->list = $list;
        $this->view->paginator = $paginator;
        $this->view->nav = [
            [
                'name' => '搜索',
                'url' => $this->url->get('search/index/keyword/搜索')
            ],
            [
                'name' => $keyword,
                'url' => $this->request->getURI()
            ],
        ];
        $this->view->common = $this->media($keyword . ' - 搜索', $keyword);
        $renderView = empty($this->config->system->search_tpl) ? 'search/index' : $this->config->system->search_tpl;
        $this->view->pick($renderView);
    }
}