<?php
namespace App\Admin\Controllers;

use Library\Tools\Paginator;
use App\Admin\Models\Translate;
use App\Admin\Models\Language;
use App\Admin\Models\Admin;

class TranslateController extends CommonController {

    /**
     * @desc 翻译管理
     * @author: ZhaoYang
     * @date: 2018年7月22日 下午9:51:49
     */
    public function indexAction() {
        $textType = $this->get('text_type', 'int!');
        $search = $this->get('search');
        $where = [ ];
        if (!empty($textType) && !empty($search)) {
            switch ($textType) {
                case 1:
                    $where = [ 
                        'conditions' => 'source_text LIKE :source_text:',
                        'bind' => [ 
                            'source_text' => "%$search%"
                        ]
                    ];
                    break;
                case 2:
                    $where = [ 
                        'conditions' => 'translated_text LIKE :translated_text:',
                        'bind' => [ 
                            'translated_text' => "%$search%"
                        ]
                    ];
            }
        }
        $count = Translate::count($where);
        $paginator = new Paginator($count);
        $translateList = Translate::find(array_merge([ 
            'limit' => $paginator->getLimit(),
            'offset' => $paginator->getOffset(),
            'order' => 'id DESC'
        ], $where));
        $admin = new Admin();
        $this->view->setTemplateBefore('common');
        $this->view->pageShow = $paginator->show();
        $this->view->translateList = $translateList;
        $this->view->textType = $textType;
        $this->view->search = $search;
        $this->view->translateInfoPower = $admin->checkPower('Translate', 'info');
    }

    /**
     * @desc 查看详情
     * @author: ZhaoYang
     * @date: 2018年7月23日 上午12:43:52
     */
    public function infoAction() {
        $id = $this->get('id', 'absint', 0);
        $translate = Translate::findFirst($id);
        if ($translate === false) {
            return $this->error('该记录不存在');
        }
        $language = Language::findFirst($translate->translated_language_id);
        $admin = new Admin();
        $actionPower = $admin->checkPower('Translate', 'edit');
        $actionName = $actionPower ? '修改' : '查看';
        $this->view->setTemplateBefore('common');
        $this->view->translate = $translate;
        $this->view->language = $language;
        $this->view->translateIndexPower = $admin->checkPower('Translate', 'index');
        $this->view->actionName = $translate->t($actionName);;
        $this->view->translateEditPower = $actionPower;
    }

    /**
     * @desc 翻译修改
     * @author: ZhaoYang
     * @date: 2018年7月23日 下午9:03:46
     */
    public function editAction() {
        $translate = new Translate();
        $editRes = $translate->edit($this->post());
        if ($editRes === false) {
            return $this->sendJson($translate->getMessages()[0]->getMessage(), 10001);
        }
        return $this->sendJson('修改成功！');
    }
}