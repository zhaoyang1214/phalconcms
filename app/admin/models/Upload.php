<?php
namespace App\Admin\Models;

use Models\Upload as ModelsUpload;

class Upload extends ModelsUpload {
    
    public function getAllowCount(array $conditions = null) {
        $parameters = [ ];
        if(!empty($conditions)) {
            $parameters['conditions'] = implode(' AND ', $conditions);
        }
        return self::count($parameters);
    }
    
    public function getAllowList(int $limit = null, int $offset = null, array $conditions = null) {
        $parameters = [ ];
        if(!empty($conditions)) {
            $parameters['conditions'] = implode(' AND ', $conditions);
        }
        if(!is_null($limit)) {
            $parameters['limit'] = $limit;
        }
        if(!is_null($offset)) {
            $parameters['offset'] = $offset;
        }
        $parameters['order'] = 'id DESC';
        return self::find($parameters);
    }
    
    public function getModule(int $module = null) {
        $moduleArr = [
            -1 => '未绑定模块',
            1 => '栏目模块',
            2 => '内容模块',
            3 => '扩展模块',
            4 => '表单模块',
        ];
        if (is_null($module)) {
            return $moduleArr;
        }
        return $moduleArr[$module] ?? '未知';
    }
    
    public function del(int $id) {
        $info = self::findFirst($id);
        if($info === false) {
            return $this->errorMessage('非法请求！');
        }
        $filename = PUBLIC_PATH . $info->file;
        if(is_file($filename)) {
            @unlink($filename);
        }
        return $info->delete();
    }
}