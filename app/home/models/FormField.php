<?php
namespace App\Home\Models;

use Models\FormField as ModelsFormField;
use Phalcon\Mvc\Model\Exception;

class FormField extends ModelsFormField {
    
    public function getAll($parameters = [ ]) {
        $system = $this->getDI()->getConfig()->system;
        return self::getList($parameters, (bool)$system->data_cache_on);
    }
    
    public function getFieldValue($formFieldList, string $fieldName, $value) {
        if(is_object($formFieldList)) {
            $formFieldList = $formFieldList->toArray();
        } else if(!is_array($formFieldList)) {
            throw new Exception('The formFieldList must is array !');
        }
        foreach ($formFieldList as $formField) {
            if($formField['field'] == $fieldName) {
                goto formatting;
            }
        }
        throw new Exception('The formField ' . $fieldName . ' is not found !');
        formatting:
        
        switch ($formField['type']) {
            // 单行文本框
            case 1:
                switch($formField['property']) {
                    case 1:
                    case 2:
                    case 5:
                        break;
                    case 4:
                        $config = empty($formField['config']) ? [] : explode("\n", $formField['config']);
                        $config[0] = $config[0] ?? 'Y-m-d H:i:s';
                        $value = !empty($value) ? date($config[0], strtotime($value)): $value;
                        break;
                }
                break;
                // 多行文本框
            case 2:
                // 文件上传
            case 4:
                // 单图片上传
            case 5:
                break;
                // 编辑器
            case 3:
                $value = htmlspecialchars_decode($value);
                break;
                // 组图上传
            case 6:
                $values = json_decode($value, true) ?? [ ];
                break;
                // 下拉
            case 7:
                // 单选
            case 8:
                $configArr = explode("\n", $formField['config']);
                foreach ($configArr as $v) {
                    $v = trim($v);
                    preg_match('/^\s*(\w+)\s*=\s*([^\s]+)\s*$/', $v, $matches);
                    if($matches[1] == $value) {
                        $value = $matches[2];
                        break;
                    }
                }
                break;
                // 多选
            case 9:
                $configArr = explode("\n", $formField['config']);
                $fieldHtml = '';
                $values = explode(',', $value);
                $value = '';
                foreach ($configArr as $k => $v) {
                    $v = trim($v);
                    preg_match('/^\s*(\w+)\s*=\s*([^\s]+)\s*$/', $v, $matches);
                    if(in_array($matches[1], $values)) {
                        $value[$matches[1]] = $matches[2];
                    }
                }
                break;
                
        }
        return $value;
    }
    
}