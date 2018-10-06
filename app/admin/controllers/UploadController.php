<?php
namespace App\Admin\Controllers;

use App\Admin\Models\Admin;
use App\Admin\Models\Upload;
use Library\Tools\Paginator;

class UploadController extends CommonController {
    
    /** 
     * @desc 列表 
     * @author ZhaoYang 
     * @date 2018年9月4日 下午3:49:18 
     */
    public function indexAction() {
        $ext = $this->get('ext', 'int!');
        $module = $this->get('module', 'int!');
        $title = $this->get('title');
        $conditions = [ ];
        $imageExt = '"png","jpg","jpeg","gif","bmp"';
        $videoExt = '"flv","swf","avi","rm","rmvb","mpeg","mpg","ogg","ogv","mov","wmv","mp4","webm","mp3","wav","mid"';
        $documentExt = '"doc","docx","xls","xlsx","ppt","pptx","pdf","txt","md","xml"';
        $zipExt = '"rar","zip","tar","gz","7z","bz2","cab","iso"';
        switch ($ext) {
            case 1:
                $conditions[] = 'ext in(' . $imageExt . ')';
                break;
            case 2:
                $conditions[] = 'ext in(' . $videoExt . ')';
                break;
            case 3:
                $conditions[] = 'ext in(' . $documentExt . ')';
                break;
            case 4:
                $conditions[] = 'ext in(' . $zipExt . ')';
                break;
            case 5:
                $conditions[] = "ext not in({$imageExt},{$videoExt},{$documentExt},{$zipExt})";
                break;
        }
        if(!empty($module)) {
            $conditions[] = 'module=' . $module;
        }
        if(!empty($title)) {
            $conditions[] = 'title LIKE "%' . $title . '%"';
        }
        $upload = new Upload();
        $count = $upload->getAllowCount($conditions);
        $paginator = new Paginator($count);
        $uploadList = $upload->getAllowList($paginator->getLimit(), $paginator->getOffset(), $conditions);
        $admin = new Admin();
        $this->view->setTemplateBefore('common');
        $this->view->pageShow = $paginator->show();
        $this->view->uploadList = $uploadList;
        $this->view->modules = $upload->getModule();
        $this->view->uploadDeletePower = $admin->checkPower('upload', 'delete');
        $this->view->ext = $ext;
        $this->view->module = $module;
        $this->view->title = $title;
    }
    
    /** 
     * @desc 删除 
     * @author ZhaoYang 
     * @date 2018年9月4日 下午3:49:34 
     */
    public function deleteAction() {
        $id = $this->post('id', 'absint', 0);
        $upload = new Upload();
        $delRes = $upload->del($id);
        if($delRes === false) {
            return $this->sendJson($upload->getMessages()[0]->getMessage(), 10001);
        }
        return $this->sendJson('删除成功！');
    }
}