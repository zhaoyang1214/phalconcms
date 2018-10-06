<?php
namespace App\Home\Controllers;

use Library\Tools\Paginator;
use App\Home\Models\Form;
use App\Home\Models\FormField;
use App\Home\Models\FormData;

class FormController extends CommonController {
    
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
        $no = $this->get('no');
        if(empty($no)) {
            return $this->forward('error/error404');
        }
        $form = (new Form())->getOne([
            'conditions' => 'no=:no:',
            'bind' => [
                'no' => $no
            ]
        ]);
        if($form === false || $form->display == 0) {
            return $this->forward('error/error404');
        }
        $formField = new FormField();
        $formFieldList = $formField->getAll([
            'conditions' => 'form_id=' . $form->id,
            'order' => 'sequence ASC'
        ]);
        $formData = new FormData($form->table);
        $where = htmlspecialchars_decode($form->where);
        $count = $formData->getCount($where);
        $listRows = intval($form->page) <= 0 ? 10 : $form->page;
        $paginator = new Paginator($count, $listRows=1);
        $list = $formData->getAll([
            'conditions' => $where,
            'order' => $form->sort,
            'limit' => $paginator->getLimit(),
            'offset' => $paginator->getOffset()
        ]);
        $this->view->form = $form;
        $this->view->formFieldList = $formFieldList;
        $this->view->list = $list;
        $this->view->paginator = $paginator;
        $this->view->nav = [
            [
                'name' => $form->name,
                'url' => $this->request->getURI()
            ],
        ];
        $this->view->common = $this->media($form->name);
        if($form->alone_tpl == 1) {
            
        }
        $renderView = $form->alone_tpl == 1 ? 'form/index' : $form->tpl;
        $this->view->pick($renderView);
    }
    
    public function verifyAction(){
        $captcha = new \Captcha();
        $content = $captcha->entry('form');
        return $this->response->setContent($content)->setContentType('image/png')->send();
    }
    
    public function addAction() {
        $no = $this->post('no');
        if(empty($no)) {
            return $this->forward('error/error404');
        }
        $form = (new Form())->getOne([
            'conditions' => 'no=:no:',
            'bind' => [
                'no' => $no
            ]
        ]);
        if($form === false || $form->display == 0) {
            return $this->forward('error/error404');
        }
        if($form->is_captcha) {
            $captcha = new \Captcha();
            $code = $this->post('verify_code');
            if(!$captcha->check($code, 'form')) {
                return $form->return_type ? $this->sendJson('验证码错误！', 10001) : $this->error('验证码错误！');
            }
        }
        $formData = new FormData($form->table);
        $addRes = $formData->add($this->post(null, false), $form->id);
        if($addRes) {
            $message = empty($form->return_msg) ? '添加成功！' : $form->return_msg;
            $url = empty($form->return_url) ? null : $form->return_url;
            return $form->return_type ? $this->sendJson($message) : $this->success($message, $url);
        }
        return $form->return_type ? $this->sendJson($formData->getMessages()[0]->getMessage(), 10001) : $this->error($message);
    }
    
    public function editAction() {
        $no = $this->post('no');
        if(empty($no)) {
            return $this->forward('error/error404');
        }
        $form = (new Form())->getOne([
            'conditions' => 'no=:no:',
            'bind' => [
                'no' => $no
            ]
        ]);
        if($form === false || $form->display == 0) {
            return $this->forward('error/error404');
        }
        if($form->is_captcha) {
            $captcha = new \Captcha();
            $code = $this->post('verify_code');
            if(!$captcha->check($code, 'form')) {
                return $form->return_type ? $this->sendJson('验证码错误！', 10001) : $this->error('验证码错误！');
            }
        }
        $formData = new FormData($form->table);
        $data = $this->post(null, false);
        $data['form_id'] = $form->id;
        $editRes = $formData->edit($data);
        if($editRes) {
            $message = empty($form->return_msg) ? '修改成功！' : $form->return_msg;
            $url = empty($form->return_url) ? null : $form->return_url;
            return $form->return_type ? $this->sendJson($message) : $this->success($message, $url);
        }
        return $form->return_type ? $this->sendJson($formData->getMessages()[0]->getMessage(), 10001) : $this->error($message);
    }
}