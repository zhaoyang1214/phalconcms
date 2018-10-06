<?php
namespace App\Home\Controllers;

class IndexController extends CommonController {

    public function indexAction() {
        if($this->config->system->html_cache_on) {
            $this->view->cache([
                'lifetime' => $this->config->system->html_index_cache_time,
                'key' => $this->request->getURI()
            ]);
            if ($this->view->getCache()->exists($this->request->getURI())) {
                return;
            }
        }
        $renderView = empty($system->index_tpl) ? 'index/index' : $this->config->system->index_tpl;
        $this->view->common = $this->media();
        $this->view->pick($renderView);
    }
}