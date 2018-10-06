<?php
namespace App\Admin\Models;

use Models\CategoryModel as ModelsCategoryModel;
use Common\Common;

class CategoryModel extends ModelsCategoryModel {
    
    public function getAllowCategoryList() {
        $list = self::find('status=1')->toArray();
        $admin = new Admin();
        foreach ($list as $k => $v) {
            if(!$admin->checkPower($v['category'], 'add')) {
                unset($list[$k]);
            }
        }
        return $list;
    }
    
    public function edit(array $data) {
        $data = Common::arraySlice(['id', 'name', 'status', 'befrom'], $data);
        $info = self::findFirst($data['id']);
        $this->assign($info->toArray());
        return $this->update($data);
    }
}