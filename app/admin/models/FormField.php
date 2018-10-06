<?php
namespace App\Admin\Models;

use Models\FormField as ModelsFormField;
use Common\Validate;
use Phalcon\Db\Column;
use Common\Common;
use Phalcon\Db\Index;

class FormField extends ModelsFormField {
    
    /**
     * @desc 校验规则
     * @author: ZhaoYang
     * @date: 2018年8月8日 下午11:22:41
     */
    public function rules() {
        return [
            'id0' => ['id', 'callback', '非法操作', function($data) {
                if(!isset($data['id'])) {
                    return  false;
                }
                $formField = self::findFirst(intval($data['id']));
                if($formField === false) {
                    return false;
                }
                $message = (new Validate())->addRules((new Form())->getRules(['id0']))->validate(['id' => $formField->form_id]);
                if(count($message)) {
                    return false;
                }
                return true;
            }],
            'form_id0' => ['form_id', 'callback', '非法操作', function($data) {
                if(!isset($data['form_id'])) {
                    return false;
                }
                $message = (new Validate())->addRules((new Form())->getRules(['id0']))->validate(['id' => $data['form_id']]);
                if(count($message)) {
                    return false;
                }
                return true;
            }],
            'name0' => ['name', 'stringlength', '字段描述长度必须大于1位|字段描述必须小于50位', [1, 50]],
            'name1' => ['name', 'callback', '字段描述已存在', function($data) {
                if(!isset($data['name']) || !isset($data['form_id'])) {
                    return false;
                }
                $parameters = [
                    'columns' => 'name',
                    'conditions'  => 'form_id=:form_id: AND name=:name:',
                    'bind' => [
                        'form_id' => $data['form_id'],
                        'name' => $data['name']
                    ]
                ];
                if(isset($data['id']) && !empty($data['id'])){
                    $parameters['conditions']  .= ' AND id<>:id:';
                    $parameters['bind']['id'] = $data['id'];
                }
                return self::findFirst($parameters) ? false : true;
            }],
            'field0' => ['field', 'stringlength', '字段描述长度必须大于1位|字段描述必须小于50位', [1, 50]],
            'field1' => ['field', 'callback', '字段描述已存在', function($data) {
                if(!isset($data['field']) || !isset($data['form_id'])) {
                    return false;
                }
                $parameters = [
                    'columns' => 'field',
                    'conditions'  => 'form_id=:form_id: AND field=:field:',
                    'bind' => [
                        'form_id' => $data['form_id'],
                        'field' => $data['field']
                    ]
                ];
                if(isset($data['id']) && !empty($data['id'])){
                    $parameters['conditions']  .= ' AND id<>:id:';
                    $parameters['bind']['id'] = $data['id'];
                }
                return self::findFirst($parameters) ? false : true;
            }],
            'type0' => ['type', 'inclusionin', '字段类型选择错误', [1, 2, 3, 4, 5, 6, 7, 8, 9]],
            'property0' => ['property', 'callback', '字段属性选择错误',  function($data) {
                if(!isset($data['type']) || !isset($data['property'])) {
                    return false;
                }
                switch($data['property']) {
                    case 1:
                        return true;
                        break;
                    case 2:
                        if(in_array($data['type'], [1, 7, 8])) {
                            return true;
                        }
                        break;
                    case 3:
                        if(in_array($data['type'], [2, 3])) {
                            return true;
                        }
                        break;
                    case 4:
                    case 5:
                        if($data['type'] == 1) {
                            return true;
                        }
                        break;
                }
                return false;
            }],
            'len0' => ['len', 'between', '字段长度必须是0-21844之间的整数', [0, 21844]],
            'len1' => ['len', 'regex', '字段长度必须是0-21844之间的整数', '/^\d{1,5}$/'],
            'decimal0' => ['decimal', 'between', '小数位必须是0-30之间的整数', [0, 30]],
            'decimal1' => ['decimal', 'regex', '小数位必须是0-30之间的整数', '/^\d{1,2}$/'],
            'default0' => ['default', 'callback', '默认值错误', function($data) {
                if(!isset($data['default']) || !isset($data['property'])) {
                    return false;
                }
                if(empty($data['default'])) {
                    return true;
                }
                switch($data['property']) {
                    case 1:
                        $pattern = '/^.{0,' . $data['len'] . '}$/';
                        break;
                    case 2:
                        $pattern = '/^\d{0,' . $data['len'] . '}$/';
                        break;
                    case 3:
                        return true;
                    case 4:
                        $pattern = '/^\d{4}(-\d{2}){2}( \d{2}(:\d{2}){2})?$/';
                        break;
                    case 5:
                        $pattern = '/^\d{0,' . $data['len'] . '}' . ($data['decimal'] > 0 ? '\.\d{1,' . $data['decimal'] . '}' : '') . '$/';
                        break;
                    case 6:
                        $default = intval($data['default']);
                        if($default >= -128 && $default <= 127) {
                            $pattern = '/^-?\d{1,3}$/';
                            break;
                        }
                        return false;
                    default :
                        return false;
                }
                if(preg_match($pattern, $data['default'])) {
                    return true;
                }
                return false;
            }],
            'config0' => ['config', 'callback', '字段配置错误', function($data) {
                if(!isset($data['config']) || !isset($data['type']) || !isset($data['property'])) {
                    return false;
                }
                if(!in_array($data['type'], [7, 8, 9])) {
                    return true;
                }
                if(empty($data['config'])) {
                    return false;
                }
                $configArr = explode("\n", $data['config']);
                foreach ($configArr as $v) {
                    $v = trim($v);
                    if($data['property'] == 1) {
                        if(!preg_match('/^\s*(\w+)\s*=\s*[^\s]+\s*$/', $v, $matches)) {
                            return false;
                        }
                    } else {
                        if(!preg_match('/^\s*(\d+)\s*=\s*[^\s]+\s*$/', $v, $matches)) {
                            return false;
                        }
                    }
                    $configValueArr[] = $matches[1];
                }
                if(count($configValueArr) != count(array_unique($configValueArr))) {
                    return false;
                }
                return true;
            }],
            'is_must0' => ['is_must', 'inclusionin', '是否必填选择错误', [0,1]],
            'is_unique0' => ['is_unique', 'inclusionin', '是否唯一选择错误', [0,1]],
            'admin_display0' => ['admin_display', 'inclusionin', '是否后台显示选择错误', [0,1]],
        ];
    }
    
    /** 
     * @desc 获取字段类型 
     * @param int $type 字段类型
     * @return array|string
     * @author ZhaoYang 
     * @date 2018年8月10日 上午10:08:16 
     */
    public function getType(int $type = null) {
        $typeArr = [ 
            1 => '单行文本',
            2 => '多行文本',
            3 => '编辑器',
            4 => '文件上传',
            5 => '单图片上传',
            6 => '组图上传',
            7 => '下拉菜单',
            8 => '单选',
            9 => '多选'
        ];
        if (is_null($type)) {
            return $typeArr;
        }
        return $typeArr[$type] ?? '未知';
    }
    
    /** 
     * @desc 获取字段属性 
     * @param int $property 字段属性
     * @return array|string
     * @author ZhaoYang 
     * @date 2018年8月10日 下午2:36:15 
     */
    public function getProperty(int $property = null) {
        $propertyArr = [ 
            1 => 'varchar',
            2 => 'int',
            3 => 'text',
            4 => 'datetime',
            5 => 'decimal'
            // 6 => 'tinyint',
        ];
        if (is_null($property)) {
            return $propertyArr;
        }
        return $propertyArr[$property] ?? '未知';
    }
    
    public function getAdminDisplay(int $adminDisplay = null) {
        $adminDisplayArr = [
            0 => '否',
            1 => '是',
        ];
        if (is_null($adminDisplay)) {
            return $adminDisplayArr;
        }
        return $adminDisplayArr[$adminDisplay] ?? '未知';
    }
    
    /**
     * @desc 生成列
     * @param $data 列数据
     * @return Phalcon\Db\Column
     * @author: ZhaoYang
     * @date: 2018年8月12日 上午12:33:33
     */
    public function column($data) {
        $definition = [ ];
        switch ($data['property']) {
            case 1: 
                $definition['type'] = Column::TYPE_VARCHAR;
                $definition['size'] = $data['len'];
                $definition['default'] = $data['default'];
                break;
            case 2:
                $definition['type'] = Column::TYPE_INTEGER;
                $definition['size'] = $data['len'] > 11 ? 11 : $data['len'];
                if($data['default'] !== '') {
                    $definition['default'] = intval($data['default']);
                }
                break;
            case 3:
                $definition['type'] = Column::TYPE_TEXT;
                break;
            case 4:
                $definition['type'] = Column::TYPE_DATETIME;
                if($data['default'] !== '') {
                    $definition['default'] = $data['default'];
                }
                break;
            case 5:
                $definition['type'] = Column::TYPE_DECIMAL;
                $definition['size'] = $data['len'] > 65 ? 65 : $data['len'];
                $definition['scale'] = $data['decimal'];
                if($data['default'] !== '') {
                    $definition['default'] = intval($data['default']);
                }
                break;
        }
        return new Column($data['field'], $definition);
    }
    
     /**
      * @desc 添加
      * @param array $data 要插入的数据
      * @return bool
      * @author: ZhaoYang
      * @date: 2018年8月11日 下午5:34:50
      */
    public function add(array $data) {
        $data = Common::arraySlice(['form_id', 'name', 'field', 'type', 'property', 'len', 'decimal', 'default', 'sequence', 'tip', 'config', 'is_must', 'is_unique', 'admin_display', 'admin_display_len'], $data, true);
        $message = (new Validate())->addRules(self::getRules(['form_id0', 'name0', 'name1', 'field0', 'field1', 'type0', 'property0', 'len0','decimal0', 'default0','config0','is_must0','is_unique0','admin_display0']))->validate($data);
        if (count($message)) {
            return $this->errorMessage($message);
        }
        $form = Form::findFirst($data['form_id']);
        $tableName = static::$_tablePrefix . 'form_data_' . $form->table;
        $connet = $this->getWriteConnection();
        $column = $this->column($data);
        try {
            $addColumRes = $connet->addColumn($tableName, null, $column);
        } catch (\Exception $e) {
            $addColumRes = false;
            $errMsg = $e->getMessage();
        } finally {
            if(!$addColumRes) {
                return $this->errorMessage($errMsg ?? '添加字段错误');
            }
        }
        if($data['is_unique'] == 1) {
            try {
                $addIndexRes = $connet->addIndex($tableName, null, new Index($data['field'], [$data['field']], 'UNIQUE'));
            } catch (\Exception $e) {
                $addIndexRes = false;
                $errMsg = $e->getMessage();
            } finally {
                if(!$addIndexRes) {
                    $connet->dropColumn($tableName, null, $data['field']);
                    return $this->errorMessage($errMsg ?? '添加索引错误');
                }
            }
        }
        try {
            $createDataRes = $this->create($data);
        } catch (\Exception $e) {
            $createDataRes = false;
            $errMsg = $e->getMessage();
        } finally {
            if(!$createDataRes) {
                $connet->dropColumn($tableName, null, $data['field']);
                return $this->errorMessage($errMsg ?? '添加失败！');
            }
        }
        (new FormData())->clearModelsMetadata($form->table);
        return true;
    }
    
    /**
     * @desc 修改
     * @param array $data 要修改的数据
     * @return bool
     * @author: ZhaoYang
     * @date: 2018年8月12日 下午5:25:16
     */
    public function edit(array $data) {
        $data = Common::arraySlice(['id', 'name', 'len', 'decimal', 'default', 'sequence', 'tip', 'config', 'is_must', 'is_unique', 'admin_display', 'admin_display_len'], $data, true);
        $formField = self::findFirst(intval($data['id']));
        if($formField === false) {
            return $this->errorMessage('非法操作！');
        }
        $data['form_id'] = $formField->form_id;
        $data['property'] = $formField->property;
        $data['type'] = $formField->type;
        $data['field'] = $formField->field;
        $message = (new Validate())->addRules(self::getRules(['id0', 'name0', 'name1', 'len0', 'len1', 'decimal0', 'decimal1', 'default0', 'config0', 'is_must0', 'is_unique0', 'admin_display0']))->validate($data);
        if(count($message)) {
            return $this->errorMessage($message);
        }
        $form = Form::findFirst($formField->form_id);
        $tableName = static::$_tablePrefix . 'form_data_' . $form->table;
        $connet = $this->getWriteConnection();
        try {
            $editColumRes = $connet->modifyColumn($tableName, null, $this->column($data), $this->column($formField->toArray()));
        } catch (\Exception $e) {
            $editColumRes = false;
            $errMsg = $e->getMessage();
        } finally {
            if(!$editColumRes) {
                return $this->errorMessage($errMsg ?? '修改字段错误！');
            }
        }
        if($data['is_unique'] != $formField->is_unique) {
            try {
                if($data['is_unique'] == 1) {
                    $editIndexRes = $connet->addIndex($tableName, null, new Index($formField->field, [$formField->field], 'UNIQUE'));
                } else {
                    $editIndexRes = $connet->dropIndex($tableName, null, $formField->field);
                }
            } catch (\Exception $e) {
                $editIndexRes = false;
                $errMsg = $e->getMessage();
            } finally {
                if(!$editIndexRes) {
                    $connet->modifyColumn($tableName, null, $this->column($formField->toArray()));
                    return $this->errorMessage($errMsg ?? '修改索引错误');
                }
            }
        }
        try {
            $this->assign($formField->toArray());
            $editDataRes = $this->update($data);
        } catch (\Exception $e) {
            $editDataRes = false;
            $errMsg = $e->getMessage();
        } finally {
            if(!$editDataRes) {
                $connet->modifyColumn($tableName, null, $this->column($formField->toArray()));
                if($data['is_unique'] != $formField->is_unique) {
                    if($data['is_unique'] == 1) {
                        $connet->dropIndex($tableName, null, $formField->field);
                    } else {
                        $connet->addIndex($tableName, null, new Index($formField->field, [$formField->field], 'UNIQUE'));
                    }
                }
                return $this->errorMessage($errMsg ?? '修改失败！');
            }
        }
        (new FormData())->clearModelsMetadata($form->table);
        return true;
    }
    
    /**
     * @desc 删除
     * @param int $id
     * @return: bool
     * @author: ZhaoYang
     * @date: 2018年8月12日 下午6:35:45
     */
    public function del(int $id) {
        $message = (new Validate())->addRules(self::getRules(['id0']))->validate(['id' => $id]);
        if(count($message)) {
            return $this->errorMessage($message);
        }
        $formField = self::findFirst($id);
        $data = $formField->toArray();
        $form = Form::findFirst($formField->form_id);
        $tableName = static::$_tablePrefix . 'form_data_' . $form->table;
        $delRes = $formField->delete();
        if($delRes) {
            $dropColumnRes = $this->getWriteConnection()->dropColumn($tableName, null, $data['field']);
            if(!$dropColumnRes) {
                $this->create($data);
            }
        }
        (new FormData())->clearModelsMetadata($form->table);
        return $delRes;
    }
    
    /** 
     * @desc 获取字段html 
     * @author ZhaoYang 
     * @date 2018年8月14日 下午2:21:40 
     */
    public function getFieldHtml(\Phalcon\Mvc\Model $formData = null) {
        $url = $this->getDi()->getUrl();
        if(!isset($this->id)) {
            return '';
        }
        $value = is_null($formData) ? $this->default : ($formData->{$this->field} ?? '');
        switch ($this->type) {
            // 单行文本框
            case 1:
                $fieldHtml = '<input type="text" name="' . $this->field . '" id="' . $this->field . '" ';
                switch($this->property) {
                    case 1:
                        $fieldHtml .= 'value="' . $value . '"  ' . ($this->is_must ? "reg='\S' msg='{$this->name}不能为空！'" : '') . ' class="text_value"/>';
                        break;
                    case 2:
                        $fieldHtml .= 'value="' . $value . '"  ' . ($this->is_must ? "reg='[0-9]' msg='{$this->name}必须为数字！'" : '') . ' class="text_value"/>';
                        break;
                    case 4:
                        $config = empty($this->config) ? [] : explode("\n", $this->config);
                        $config[0] = $config[0] ?? 'Y-m-d H:i:s';
                        $value = !empty($value) ? date($config[0], strtotime($value)): $value;
                        $fieldHtml .= 'value="' . $value . '"  ' . ($this->is_must ? "reg='\S' msg='{$this->name}必须为数字！'" : '') . ' class="text_value disabled" readonly="readonly" style="width:120px; float:left" />
                            <div id="' . $this->field . '_button" class="time"></div>
                			<script type="text/javascript">
                				$("#' . $this->field . '_button").calendar({ id:"#' . $this->field . '",format:"' . ($config[1] ?? 'yyyy-MM-dd HH:mm:ss') . '"});
                			</script>';
                        break;
                    case 5:
                        $fieldHtml .= 'value="' . $value . '"  ' . ($this->is_must ? "reg='[0-9\.]' msg='{$this->name}必须为数字！'" : '') . ' class="text_value"/>';
                        break;
                }
                break;
            // 多行文本框
            case 2:
                $fieldHtml = '<textarea name="' . $this->field . '" class="text_textarea" id="' . $this->field . '" ' . ($this->is_must ? "reg='\S' msg='{$this->name}不能为空！'" : '') . '>' . $value . '</textarea>';
                break;
            // 编辑器
            case 3:
                $value = htmlspecialchars_decode($value);
                $langJs = LANGUAGE == 'zh' ? '/zh-cn/zh-cn.js' : '/en/en.js';
                $fieldHtml = <<<EOF
                    <script src="{$url->getStatic('js/ueditor.config.js')}" type="text/javascript"></script>
             		<script src="/plugins/ueditor/ueditor.all.js" type="text/javascript"></script>
             		<script src="/plugins/ueditor/lang{$langJs}" type="text/javascript"></script>
             		<script name="{$this->field}" id="{$this->field}" type="text/plain" style="width:100%; height:400px;">{$value}</script>
             		<script type="text/javascript">UE.getEditor("{$this->field}", {"serverUrl":"{$url->get('ueditor/index/origin/4')}"});</script>
EOF;
                break;
            // 文件上传
            case 4:
                $must = $this->is_must ? "reg='\S' msg='{$this->name}不能为空！'" : '';
                $fieldHtml = <<<EOF
                    <input name="{$this->field}" type="text" class="text_value disabled" readonly="readonly" id="{$this->field}" value="{$value}" {$must}>
     &nbsp;&nbsp;<input type="button" id="{$this->field}_botton" class="button_small" value="选择文件">
         <script>
	        $(document).ready(function() {
	            $('#{$this->field}_botton').click(function(){
	                urldialog({
	                    title:'单文件上传',
	                    url:"{$url->get("ueditor/getUpfileHtml/type/file/id/{$this->field}/origin/4")}",
	                    width:620,
	                    height:500
	                });
	            });
	        });
	     </script>             
EOF;
                break;
            // 单图片上传
            case 5:
                $must = $this->is_must ? "reg='\S' msg='{$this->name}不能为空！'" : '';
                $fieldHtml = <<<EOF
                    <input name="{$this->field}" type="text" class="text_value disabled" readonly="readonly" id="{$this->field}" value="{$value}" {$must}>
   			&nbsp;&nbsp;<input type="button" id="{$this->field}_botton" class="button_small" value="选择图片">
               		<script>
        			   $(document).ready(function() {
        			       $('#{$this->field}_botton').click(function(){
        			           urldialog({
        			               title:'单图片上传',
        			               url:"{$url->get("ueditor/getUpfileHtml/type/image/id/{$this->field}/origin/4")}",
        			               width:818,
        			               height:668
        			           });
        			       });
        			   });
        			</script>
EOF;
                break;
            // 组图上传
            case 6:
                $values = json_decode($value, true) ?? [ ];
                $liHtml = '';
                foreach ($values as $v) {
                    $liHtml .= <<<EOF
                        <li>
        		             <div class="pic" id="images_button">
        		             <img src="{$v['thumbnail_url']}" width="125" height="105" />
        			              <input  id="{$this->field}_url[]" name="{$this->field}_url[]" type="hidden" value="{$v['url']}" />
        			              <input  id="{$this->field}_thumbnail_url[]" name="{$this->field}_thumbnail_url[]" type="hidden" value="{$v['thumbnail_url']}" />
        		             </div>
        		             <div class="title">标题： <input name="{$this->field}_title[]" type="text" id="{$this->field}_title[]" value="{$v['title']}" /></div>
        		             <div class="title">排序： <input id="{$this->field}_order[]" name="{$this->field}_order[]" value="{$v['order']}" type="text" style="width:50px;" /> <a href="javascript:void(0);" onclick="$(this).parent().parent().remove()">删除</a></div>
        		         </li>                                    
EOF;
                }
                $fieldHtml = <<<EOF
                    <input type="button" id="{$this->field}_button" class="button_small" value="上传多图" />
                   	<div class="fn_clear"></div>
                   	<div class="images">
            	        <ul id="{$this->field}_list" class="images_list">
            		          {$liHtml} 
            	        </ul>
                        <div style="clear:both"></div>
                   	</div>
                   	<script>
            	        $(document).ready(function() {
            	            $("#{$this->field}_button").click(function(){
            	                urldialog({
            	                    title:'组图上传',
            	                    url:"{$url->get("ueditor/getUpfileHtml/type/images/id/{$this->field}/origin/4")}",
            	                    width:725,
            	                    height:545
            	                });
            	            });
            	        });
                    </script>
EOF;
                break;
            case 7:
                $configArr = explode("\n", $this->config);
                $optionHtml = '';
                foreach ($configArr as $v) {
                    $v = trim($v);
                    preg_match('/^\s*(\w+)\s*=\s*([^\s]+)\s*$/', $v, $matches);
                    $selected = $matches[1] == $value ? 'selected="selected"' : '';
                    $optionHtml .= "<option value='{$matches[1]}' {$selected}>{$matches[2]}</option>";
                }
                $fieldHtml = "<select name='{$this->field}' id='{$this->field}'>{$optionHtml}</select>";                
                break;
            case 8:
                $configArr = explode("\n", $this->config);
                $fieldHtml = '';
                foreach ($configArr as $k => $v) {
                    $v = trim($v);
                    preg_match('/^\s*(\w+)\s*=\s*([^\s]+)\s*$/', $v, $matches);
                    $checked = $matches[1] == $value ? 'checked="checked"' : '';
                    $fieldHtml .= "<input type='radio' name='{$this->field}' id='{$this->field}{$k}' value='{$matches[1]}' {$checked} /><label for='{$this->field}{$k}'>&nbsp;&nbsp;{$matches[2]}</label>&nbsp;&nbsp;";
                }
                break;
            case 9:
                $configArr = explode("\n", $this->config);
                $fieldHtml = '';
                $value = explode(',', $value);
                foreach ($configArr as $k => $v) {
                    $v = trim($v);
                    preg_match('/^\s*(\w+)\s*=\s*([^\s]+)\s*$/', $v, $matches);
                    $checked = in_array($matches[1], $value) ? 'checked="checked"' : '';
                    $fieldHtml .= "<input type='checkbox' name='{$this->field}[]' id='{$this->field}{$k}' value='{$matches[1]}' {$checked}/><label for='{$this->field}{$k}'>&nbsp;&nbsp;{$matches[2]}</label>&nbsp;&nbsp;";
                }
                break;
                
        }
        $html = <<<EOF
                    <tr>
                    <td align="right">$this->name</td>
                        <td>
                            $fieldHtml
                		</td>
                		<td>$this->tip</td>
                	</tr>
EOF;
        return $html;
    }
    
    /**
     * @desc 获取列表显示
     * @author: ZhaoYang
     * @date: 2018年8月26日 下午11:17:27
     */
    public function getFieldValue(\Phalcon\Mvc\Model $formData) {
        if(!isset($this->id)) {
            return '';
        }
        $value = $formData->{$this->field};
        switch ($this->type) {
            // 单行文本框
            case 1:
                switch($this->property) {
                    case 1:
                        if($this->admin_display_len) {
                            $suffix = mb_strlen($value) > $this->admin_display_len ? '...' : '';
                            $value = mb_substr($value, 0, $this->admin_display_len) . $suffix;
                        }
                        break;
                    case 2:
                    case 5:
                        break;
                    case 4:
                        $config = empty($this->config) ? [] : explode("\n", $this->config);
                        $config[0] = $config[0] ?? 'Y-m-d H:i:s';
                        $value = !empty($value) ? date($config[0], strtotime($value)): $value;
                        break;
                }
                break;
            // 多行文本框
            case 2:
                if($this->admin_display_len) {
                    $suffix = mb_strlen($value) > $this->admin_display_len ? '...' : '';
                    $value = mb_substr($value, 0, $this->admin_display_len) . $suffix;
                }
                break;
            // 编辑器
            case 3:
                $value = htmlspecialchars_decode($value);
                if($this->admin_display_len) {
                    $suffix = mb_strlen($value) > $this->admin_display_len ? '...' : '';
                    $value = mb_substr($value, 0, $this->admin_display_len) . $suffix;
                }
                break;
            // 文件上传
            case 4:
                $value = '<a href="' . $value . '">' . basename($value) . '</a>';
                break;
            // 单图片上传
            case 5:
                $value = '<img src="' . $value . '" alt="" style="max-width:170px; max-height:90px; _width:170px; _height:90px;" />';
                break;
            // 组图上传
            case 6:
                $values = json_decode($value, true) ?? [ ];
                $value = '';
                foreach ($values as $k => $v) {
                    if($this->admin_display_len && $this->admin_display_len==$k) {
                        break;
                    }
                    $value .= '<img src="' . $v['thumbnail_url'] . '" alt="' . $v['title'] . '" style="max-width:170px; max-height:90px; _width:170px; _height:90px;margin-left: 5px;" />';
                }
            	break;
            // 下拉
            case 7:
            // 单选
            case 8:
                $configArr = explode("\n", $this->config);
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
                $configArr = explode("\n", $this->config);
                $fieldHtml = '';
                $values = explode(',', $value);
                $value = '';
                foreach ($configArr as $k => $v) {
                    if($this->admin_display_len && $this->admin_display_len==$k) {
                        $value .= count($values) > $this->admin_display_len ? '...' : '';
                        break;
                    }
                    $v = trim($v);
                    preg_match('/^\s*(\w+)\s*=\s*([^\s]+)\s*$/', $v, $matches);
                    if(in_array($matches[1], $values)) {
                        $value .= $matches[2] . ' ';
                    }
                }
                break;
                
        }
        return $value;
    }
    
}