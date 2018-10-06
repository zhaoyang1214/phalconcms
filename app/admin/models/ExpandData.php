<?php
namespace App\Admin\Models;

use Models\ExpandData as ModelsExpandData;

class ExpandData extends ModelsExpandData {
    
    public function checkData(array $data, int $expandId) {
        $di = $this->getDI();
        $filter = $di->getFilter();
        $defaultFilter = $di->getConfig()->services->filter->default_filter;
        $defaultFilter = isset($defaultFilter) ? explode(',', $defaultFilter) : [ ];
        $expandFieldList = ExpandField::find([
            'conditions' => 'expand_id=' . $expandId,
            'order' => 'sequence ASC'
        ]);
        $newData = [ ];
        if(!empty($data['id'])) {
            $data['id'] = abs($data['id']);
            $info = self::findFirst($data['id']);
            if($info === false) {
                return $this->errorMessage('非法操作！');
            }
            $newData['id'] = $data['id'];
        }
        foreach ($expandFieldList as $expandField) {
            $value = $data[$expandField->field] ?? null;
            switch ($expandField->type) {
                case 1:
                    switch ($expandField->property) {
                        case 2:
                            $value = $filter->sanitize($value, 'int!');
                            break;
                        case 4:
                            $value = date('Y-m-d H:i:s', strtotime($value));
                            break;
                        case 5:
                            $value = $filter->sanitize($value, 'float!');
                            break;
                        default:
                            $value = $filter->sanitize($value, $defaultFilter);
                    }
                    break;
                case 2:
                case 4:
                case 5:
                    $value = $filter->sanitize($value, $defaultFilter);
                    break;
                case 3:
                    $purifier = $di->getHtmlPurifier();
                    $value = htmlspecialchars($purifier->purify($value));
                    break;
                case 6:
                    $urlArr = is_array($data[$expandField->field . '_url']) ? $data[$expandField->field . '_url'] : [];
                    $values = [];
                    foreach($urlArr as $k => $v) {
                        $values[$k] = [
                            'url' => $v,
                            'thumbnail_url' => $data[$expandField->field . '_thumbnail_url'][$k] ?? '',
                            'title' => $data[$expandField->field . '_title'][$k] ?? '',
                            'order' => $data[$expandField->field . '_order'][$k] ?? '0',
                        ];
                    }
                    array_multisort (array_column($values, 'order'), SORT_ASC, $values);
                    $value = empty($values) ? null : json_encode($values);
                    break;
                case 7:
                case 8:
                    if(is_null($value)) {
                        break;
                    }
                    $configArr = explode("\n", $expandField->config);
                    $values = [ ];
                    foreach ($configArr as $v) {
                        $v = trim($v);
                        preg_match('/^\s*(\w+)\s*=\s*([^\s]+)\s*$/', $v, $matches);
                        $values[] = $matches[1];
                    }
                    if(!in_array($value, $values)) {
                        return $this->errorMessage($expandField->name . '选择错误！');
                    }
                    break;
                case 9:
                    if(is_null($value)) {
                        break;
                    }
                    $configArr = explode("\n", $expandField->config);
                    $values = [ ];
                    foreach ($configArr as $v) {
                        $v = trim($v);
                        preg_match('/^\s*(\w+)\s*=\s*([^\s]+)\s*$/', $v, $matches);
                        $values[] = $matches[1];
                    }
                    foreach($value as $v) {
                        if(!in_array($v, $values)) {
                            return $this->errorMessage($expandField->name . '选择错误！');
                        }
                    }
                    $value = implode(',', $value);
                    break;
            }
            if($expandField->is_must && is_null($value)) {
                return $this->errorMessage($expandField->name . '不能为空！');
            }
            /* if($expandField->is_unique) {
                $parameters = [
                    'conditions' => "{$expandField->field}=:{$expandField->field}:",
                    'bind' => [
                        $expandField->field => $value
                    ]
                    ];
                if(isset($newData['id'])) {
                    $parameters['conditions'] .= 'AND id<>:id:';
                    $parameters['bind']['id'] = $newData['id'];
                }
                $count = self::count($parameters);
                if($count) {
                    return $this->errorMessage($expandField->name . '已存在！');
                }
            } */
            $newData[$expandField->field] = $value;
        }
        return $newData;
    }
    
    /**
     * @desc 自动添加或修改
     * @author: ZhaoYang
     * @date: 2018年9月24日 下午9:56:30
     */
    public function addOrEdit(array $data, int $expandId, int $categoryContentId) {
        unset($data['id']);
        $data = $this->checkData($data, $expandId);
        if($data === false) {
            return false;
        }
        $data['category_content_id'] = $categoryContentId;
        $info = self::findFirst('category_content_id=' . $categoryContentId);
        try {
            if($info === false) {
                return $this->create($data);
            } else {
                $this->assign($info->toArray());
                return $this->update($data);
            }
        } catch (\Exception $e) {
            return $this->errorMessage($e->getMessage());
        }
    }
    
    public function deleteByCategoryContentId(int $categoryContentId) {
        $list = self::find('category_content_id=' . $categoryContentId);
        if(!empty($list)) {
            foreach ($list as $v) {
                $res = $v->delete();
                if($res === false) {
                    return $this->errorMessage('刪除失败');
                }
            }
        }
        return true;
    }
}