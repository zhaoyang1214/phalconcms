<?php
namespace App\Admin\Models;

use Models\CategoryContent as ModelsCategoryContent;
use Library\Vendors\Pinyin\Pinyin;
use Common\Validate;
use Common\Common;

class CategoryContent extends ModelsCategoryContent {
    
    public function beforeCreate() {
        $adminInfo = $this->getDI()->getSession()->get('adminInfo');
        $this->admin_id = $adminInfo['id'];
        $this->admin_nicename = $adminInfo['nicename'];
    }
    
    public function rules() {
        return [
            'id0' => ['id', 'callback', '非法操作', function($data) {
                if(!isset($data['id'])) {
                    return  false;
                }
                $info = self::findFirst(intval($data['id']));
                if($info === false) {
                    return false;
                }
                $message = (new Validate())->addRules((new Category())->getRules(['id0']))->validate(['id' => $info->category_id]);
                if(count($message)) {
                    return false;
                }
                return true;
            }],
            'category_id0' => ['category_id', 'callback', '栏目选择错误', function($data) {
                if(!isset($data['category_id'])) {
                    return  false;
                }
                $message = (new Validate())->addRules((new Category())->getRules(['id0']))->validate(['id' => intval($data['category_id'])]);
                if(count($message)) {
                    return false;
                }
                return true;
            }],
            'title0' => ['title', 'stringlength', '标题长度必须大于1位|标题必须小于100位', [1, 100]],
        ];
    }
    
    public function deleteByCategoryId(int $categoryId) {
        $phql = 'DELETE FROM App\Admin\Models\CategoryContent WHERE category_id=' . $categoryId;
        return self::getModelsManager()->executeQuery($phql);
    }
    
    public function getAllowCount(string $conditions = null, bool $joinPosition = false) {
        $session = $this->getDI()->getSession();
        $adminGroupInfo = $session->get('adminGroupInfo');
        $where = [ ];
        if (!($adminGroupInfo['keep'] & 1)) {
            if(empty($adminGroupInfo['category_ids'])){
                return [ ];
            }
            $where[] = 'a.category_id IN(' . $adminGroupInfo['category_ids'] . ')';
        }
        if($adminGroupInfo['language_power'] & 1){
            $where[] = 'b.language_id=' . $adminGroupInfo['language_id'];
        }
        if(!empty($conditions)) {
            $where[] = $conditions;
        }
        $where = empty($where) ? '' : 'WHERE ' . implode(' AND ', $where);
        $positionSql = $joinPosition ? 'LEFT JOIN App\Admin\Models\CategoryContentPosition AS d ON d.category_content_id=a.id' : '';
        $phql = "SELECT COUNT(*) AS num
                 FROM App\Admin\Models\CategoryContent AS a
                 LEFT JOIN App\Admin\Models\Category AS b ON b.id=a.category_id
                 LEFT JOIN App\Admin\Models\CategoryModel AS c ON c.id=b.category_model_id
                 {$positionSql}
                 {$where}
                 LIMIT 1";
        $result = self::getModelsManager()->executeQuery($phql)->getFirst();
        return $result->num;
    }
    
    public function getAllowList(string $conditions = null, string $limit = '', bool $joinPosition = false, string $order = '') {
        $session = $this->getDI()->getSession();
        $adminGroupInfo = $session->get('adminGroupInfo');
        $where = [ ];
        if (!($adminGroupInfo['keep'] & 2)) {
            if(empty($adminGroupInfo['category_ids'])){
                return [ ];
            }
            $where[] = 'a.category_id IN(' . $adminGroupInfo['category_ids'] . ')';
        }
        if($adminGroupInfo['language_power'] & 1){
            $where[] = 'b.language_id=' . $adminGroupInfo['language_id'];
        }
        if(!empty($conditions)) {
            $where[] = $conditions;
        }
        $where = empty($where) ? '' : 'WHERE ' . implode(' AND ', $where);
        if(empty($order)) {
            $order = 'a.updatetime DESC';
        }
        $order = 'ORDER BY ' . $order;
        if(!empty($limit)) {
            $limit = 'LIMIT ' . $limit;
        }
        $positionSql = $joinPosition ? 'LEFT JOIN App\Admin\Models\CategoryContentPosition AS d ON d.category_content_id=a.id' : '';
        $phql = "SELECT a.id,a.category_id,a.title,a.urltitle,a.subtitle,a.font_color,a.font_bold,a.keywords,a.description,a.updatetime,a.inputtime,a.image,a.jump_url,a.sequence,a.tpl,a.status,a.copyfrom,a.views,a.position,a.taglink,b.name AS category_name,c.content AS content_c
                 FROM App\Admin\Models\CategoryContent AS a
                 LEFT JOIN App\Admin\Models\Category AS b ON b.id=a.category_id
                 LEFT JOIN App\Admin\Models\CategoryModel AS c ON c.id=b.category_model_id
                 {$positionSql}
                 {$where}
                 {$order},a.id ASC
                 {$limit}";
        return self::getModelsManager()->executeQuery($phql);
    }
    
    public function add(array $data) {
        $data = Common::arraySlice(['category_id', 'title', 'urltitle', 'subtitle', 'font_color', 'font_bold', 'keywords', 'description', 'updatetime', 'image', 'jump_url', 'sequence', 'tpl', 'status', 'copyfrom', 'views', 'position', 'taglink'], $data);
        if(empty($data['urltitle'])) {
            $pinyin = new Pinyin();
            $data['urltitle'] = $pinyin->permalink($data['title'], '');
            if(strlen($data['urltitle']) > 100) {
                $data['urltitle'] = substr($data['urltitle'], 0, 68) . md5(substr($data['urltitle'], 68));
            }
            reurltitle:
            $info = self::findFirst('urltitle="' . $data['urltitle'] . '"');
            if($info !== false) {
                $data['urltitle'] .= md5(microtime(true));
                goto reurltitle;
            }
        }
        if(empty($data['updatetime'])) {
            $data['updatetime'] = date('Y-m-d H:i:s');
        }
        $data['inputtime'] = date('Y-m-d H:i:s');
        $data['status'] = (new Admin())->checkPower('categorycontent', 'audit') ? $data['status'] : 0;
        $data['position'] = implode(',', $data['position']);
        $validate = new Validate();
        $message = $validate->addRules(self::getRules(['category_id0', 'title0']))->validate($data);
        if (count($message)) {
            return $this->errorMessage($message);
        }
        return $this->create($data);
    }
    
    public function getInfoById(int $id) {
        $message = (new Validate())->addRules(self::getRules(['id0']))->validate(['id' => $id]);
        if(count($message)) {
            return $this->errorMessage('非法操作！');
        }
        return self::findFirst($id);
    }
    
    public function quickEdit(array $data) {
        $updateData = Common::arraySlice(['id', 'title', 'position', 'urltitle', 'description', 'keywords', 'updatetime'], $data);
        if(empty($updateData['urltitle'])) {
            $pinyin = new Pinyin();
            $updateData['urltitle'] = $pinyin->permalink($updateData['title'], '');
            if(strlen($updateData['urltitle']) > 100) {
                $updateData['urltitle'] = substr($updateData['urltitle'], 0, 68) . md5(substr($updateData['urltitle'], 68));
            }
            reurltitle:
            $info = self::findFirst('urltitle="' . $updateData['urltitle'] . '" AND id<>' . $updateData['id']);
            if($info !== false) {
                $updateData['urltitle'] .= md5(microtime(true));
                goto reurltitle;
            }
        }
        if(empty($data['updatetime'])) {
            $data['updatetime'] = date('Y-m-d H:i:s');
        }
        $validate = new Validate();
        $message = $validate->addRules(self::getRules(['id0', 'title0']))->validate($updateData);
        if (count($message)) {
            return $this->errorMessage($message);
        }
        if((new Admin())->checkPower('categorycontent', 'audit')) {
            $updateData['status'] = $data['status'];
        }
        $updateData['position'] = implode(',', $updateData['position']);
        $info = self::findFirst($updateData['id']);
        $this->assign($info->toArray());
        return $this->update($updateData);
    }
    
    public function editStatus(int $id, int $status=0) {
        $data = [
            'id' => $id,
            'status' => $status
        ];
        $validate = new Validate();
        $message = $validate->addRules(self::getRules(['id0']))->validate($data);
        if (count($message)) {
            return $this->errorMessage($message);
        }
        $info = self::findFirst($data['id']);
        $this->assign($info->toArray());
        return $this->update($data);
    }
    
    public function edit(array $data) {
        $data = Common::arraySlice(['id', 'category_id', 'title', 'urltitle', 'subtitle', 'font_color', 'font_bold', 'keywords', 'description', 'updatetime', 'image', 'jump_url', 'sequence', 'tpl', 'status', 'copyfrom', 'views', 'position', 'taglink'], $data);
        if(empty($data['urltitle'])) {
            $pinyin = new Pinyin();
            $data['urltitle'] = $pinyin->permalink($data['title'], '');
            if(strlen($data['urltitle']) > 100) {
                $data['urltitle'] = substr($data['urltitle'], 0, 68) . md5(substr($data['urltitle'], 68));
            }
            reurltitle:
            $info = self::findFirst('urltitle="' . $data['urltitle'] . '" AND id<>' . $data['id']);
            if($info !== false) {
                $data['urltitle'] .= md5(microtime(true));
                goto reurltitle;
            }
        }
        if(empty($data['updatetime'])) {
            $data['updatetime'] = date('Y-m-d H:i:s');
        }
        if(!(new Admin())->checkPower('categorycontent', 'audit')) {
            unset($data['status']);
        }
        $data['position'] = implode(',', $data['position']);
        $validate = new Validate();
        $message = $validate->addRules(self::getRules(['id0', 'category_id0', 'title0']))->validate($data);
        if (count($message)) {
            return $this->errorMessage($message);
        }
        $info = self::findFirst($data['id']);
        $this->assign($info->toArray());
        return $this->update($data);
    }
}