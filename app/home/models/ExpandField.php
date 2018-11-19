<?php
namespace App\Home\Models;

use Models\ExpandField as ModelsExpandField;
use Phalcon\Mvc\Model\Exception;

class ExpandField extends ModelsExpandField {
    
    public function getAll($parameters = [ ]) {
        $system = $this->getDI()->getConfig()->system;
        return self::getList($parameters, (bool)$system->data_cache_on);
    }
    
    public function getFieldValue($expandFieldList, string $fieldName, $value) {
        if(is_object($expandFieldList)) {
            $expandFieldList = $expandFieldList->toArray();
        } else if(!is_array($expandFieldList)) {
            throw new Exception('The expandFieldList must is array !');
        }
        foreach ($expandFieldList as $expandField) {
            if($expandField['field'] == $fieldName) {
                goto formatting;
            }
        }
        throw new Exception('The expandField ' . $fieldName . ' is not found !');
        formatting:
        
        switch ($expandField['type']) {
            // 单行文本框
            case 1:
                switch($expandField['property']) {
                    case 1:
                    case 2:
                    case 5:
                        break;
                    case 4:
                        $config = empty($expandField['config']) ? [] : explode("\n", $expandField['config']);
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
                $configArr = explode("\n", $expandField['config']);
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
                $configArr = explode("\n", $expandField['config']);
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