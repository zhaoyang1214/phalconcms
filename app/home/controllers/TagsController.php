<?php
namespace App\Home\Controllers;

use App\Home\Models\Tags;
use Library\Tools\Paginator;
use App\Home\Models\TagsRelation;

class TagsController extends CommonController {
    
    public function __call($name, $args) {
        $tag = $this->filter->sanitize($this->dispatcher->getActionName(), ['string', 'trim']);
        $data = $this->get();
        $data['tag'] = $tag;
        return $this->forward('tags/info', $data);
    }
    
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
        $tags = new Tags();
        $count = $tags->getCount();
        $listRows = intval($this->config->system->tpl_tags_index_page) <= 0 ? 10 : $this->config->system->tpl_tags_index_page;
        $paginator = new Paginator($count, $listRows);
        $list = $tags->getAll([
            'limit' => $paginator->getLimit(),
            'offset' => $paginator->getOffset()
        ]);
        $this->view->list = $list;
        $this->view->paginator = $paginator;
        $this->view->nav = [
            [
                'name' => 'TAG',
                'url' => $this->url->get('tags/index')
            ]
        ];
        $this->view->common = $this->media('TAGS 列表');
        $renderView = empty($this->config->system->tags_index_tpl) ? 'tags/index' : $this->config->system->tags_index_tpl;
        $this->view->pick($renderView);
    }
    
    public function infoAction() {
        $tagName = $this->get('tag');
        if(empty($tagName)) {
            return $this->forward('error/error404');
        }
        $tags = new Tags();
        $tags = $tags->getOne([
            'conditions' => 'name=:name:',
            'bind' => [
                'name' => $tagName
            ]
        ]);
        if($tags === false) {
            return $this->forward('error/error404');
        }
        $tags->clickIncrement($tags->id);
        if($this->config->system->html_cache_on) {
            $this->view->cache([
                'lifetime' => $this->config->system->html_other_cache_time,
                'key' => $this->request->getURI()
            ]);
            if ($this->view->getCache()->exists($this->request->getURI())) {
                return;
            }
        }
        $tagsRelation = new TagsRelation();
        $count = $tagsRelation->getCountByTagsId($tags->id);
        $listRows = intval($this->config->system->tpl_tags_page) <= 0 ? 10 : $this->config->system->tpl_tags_page;
        $paginator = new Paginator($count, $listRows);
        $list = $tagsRelation->getAllByTagsId($tags->id, $paginator->getLimit(), $paginator->getOffset());
        $this->view->list = $list;
        $this->view->paginator = $paginator;
        $this->view->nav = [
            [
                'name' => 'TAG',
                'url' => $this->url->get('tags/index')
            ],
            [
                'name' => $tagName,
                'url' => $this->url->get('tags/info/tag/' . $tagName)
            ]
        ];
        $this->view->common = $this->media($tagName . ' - TAGS', $tagName);
        $renderView = empty($this->config->system->tags_info_tpl) ? 'tags/info' : $this->config->system->tags_info_tpl;
        $this->view->pick($renderView);
    }
}