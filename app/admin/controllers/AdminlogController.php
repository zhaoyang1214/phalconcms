<?php
namespace App\Admin\Controllers;

use App\Admin\Models\AdminLog;
use Library\Tools\Paginator;

class AdminlogController extends CommonController {
    
    /** 
     * @desc 登录记录浏览 
     * @author ZhaoYang 
     * @date 2018年7月30日 下午2:52:48 
     */
    public function indexAction() {
        $adminInfo = $this->session->get('adminInfo');
        $conditions = 'admin_id=' . $adminInfo['id'];
        $count = AdminLog::count($conditions);
        $paginator = new Paginator($count);
        $adminLogList = AdminLog::find([
            'conditions' => $conditions,
            'limit' => $paginator->getLimit(),
            'offset' => $paginator->getOffset(),
            'order' => 'id DESC'
        ]);
        $this->view->setTemplateBefore('common');
        $this->view->adminLogList = $adminLogList;
        $this->view->pageShow = $paginator->show();
    }
}