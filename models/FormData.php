<?php
namespace Models;

use Common\BaseModel;

class FormData extends BaseModel {
    
    public function onConstruct($_tableName = null) {
        if(isset($_tableName)) {
            static::$_tableName = 'form_data_' . $_tableName;
            $this->setSource(static::$_tablePrefix . static::$_tableName);
        }
    }
    
    public function setTableName($_tableName = null) {
        static::$_tableName = 'form_data_' . $_tableName;
        $this->setSource(static::$_tablePrefix . static::$_tableName);
    }
    
    /**
     * @desc 校验和组装数据
     * @author: ZhaoYang
     * @date: 2018年8月26日 下午10:05:16
     */
    public function checkData(array $data, int $formId) {
        $di = $this->getDI();
        $filter = $di->getFilter();
        $defaultFilter = $di->getConfig()->services->filter->default_filter;
        $defaultFilter = isset($defaultFilter) ? explode(',', $defaultFilter) : [ ];
        $formFieldList = FormField::find([
            'conditions' => 'form_id=' . $formId,
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
        foreach ($formFieldList as $formField) {
            $value = $data[$formField->field] ?? null;
            switch ($formField->type) {
                case 1:
                    switch ($formField->property) {
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
                    $urlArr = is_array($data[$formField->field . '_url']) ? $data[$formField->field . '_url'] : [];
                    $values = [];
                    foreach($urlArr as $k => $v) {
                        $values[$k] = [
                            'url' => $v,
                            'thumbnail_url' => $data[$formField->field . '_thumbnail_url'][$k] ?? '',
                            'title' => $data[$formField->field . '_title'][$k] ?? '',
                            'order' => $data[$formField->field . '_order'][$k] ?? '0',
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
                    $configArr = explode("\n", $formField->config);
                    $values = [ ];
                    foreach ($configArr as $v) {
                        $v = trim($v);
                        preg_match('/^\s*(\w+)\s*=\s*([^\s]+)\s*$/', $v, $matches);
                        $values[] = $matches[1];
                    }
                    if(!in_array($value, $values)) {
                        return $this->errorMessage($formField->name . '选择错误！');
                    }
                    break;
                case 9:
                    if(is_null($value)) {
                        break;
                    }
                    $configArr = explode("\n", $formField->config);
                    $values = [ ];
                    foreach ($configArr as $v) {
                        $v = trim($v);
                        preg_match('/^\s*(\w+)\s*=\s*([^\s]+)\s*$/', $v, $matches);
                        $values[] = $matches[1];
                    }
                    foreach($value as $v) {
                        if(!in_array($v, $values)) {
                            return $this->errorMessage($formField->name . '选择错误！');
                        }
                    }
                    $value = implode(',', $value);
                    break;
            }
            if($formField->is_must && is_null($value)) {
                return $this->errorMessage($formField->name . '不能为空！');
            }
            if($formField->is_unique) {
                $parameters = [
                    'conditions' => "{$formField->field}=:{$formField->field}:",
                    'bind' => [
                        $formField->field => $value
                    ]
                    ];
                if(isset($newData['id'])) {
                    $parameters['conditions'] .= 'AND id<>:id:';
                    $parameters['bind']['id'] = $newData['id'];
                }
                $count = self::count($parameters);
                if($count) {
                    return $this->errorMessage($formField->name . '已存在！');
                }
            }
            $newData[$formField->field] = $value;
        }
        return $newData;
    }
    
    /**
     * @desc 添加
     * @author: ZhaoYang
     * @date: 2018年8月26日 下午10:05:05
     */
    public function add(array $data, int $formId) {
        $data = $this->checkData($data, $formId);
        if($data === false) {
            return false;
        }
        try {
            return $this->create($data);
        } catch (\Exception $e) {
            return $this->errorMessage($e->getMessage());
        }
        
    }
    
    /**
     * @desc 修改
     * @author: ZhaoYang
     * @date: 2018年8月27日 上午12:18:14
     */
    public function edit(array $data) {
        $data = $this->checkData($data, abs($data['form_id']));
        if($data === false) {
            return false;
        }
        $formData = self::findFirst($data['id']);
        $this->assign($formData->toArray());
        try {
            return $this->update($data);
        } catch (\Exception $e) {
            return $this->errorMessage($e->getMessage());
        }
    }
    
}