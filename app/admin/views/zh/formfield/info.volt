<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
{{ assets.outputCss() }}

{{ assets.outputJs() }}
</head>
<body scroll="no">
<div class="page_function">
  <div class="info">
    <h3>字段{{ actionName }}</h3>
    <small>使用以下功能进行字段{{ actionName }}操作</small> </div>
</div>
<div class="page_form">
<form autocomplete="off" action="{{ actionUrl }}" method="post" id="form">
<div class="page_table form_table">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="100px" align="right">字段类型</td>
        <td width="310px">
          <select name="type" id="type" onchange="fildtype($(this).val())">
          	{% for key,value in formField.getType() %}
            <option value="{{ key }}" {% if formField.type is defined and formField.type==key %}selected="selected"{% endif %}>{{ value }}</option>
            {% endfor %}
            </select>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;字段属性&nbsp;&nbsp;&nbsp;
            <select name="property" id="property"  onchange="fildproperty($(this).val())">
            {% for key, value in formField.getProperty() %}
            <option value="{{ key }}">{{ value }}</option>
            {% endfor %}
            </select>
        </td>
        <td  id="property-msg" style="line-height: 120%;"></td>
      </tr>
      <tr>
        <td align="right">字段长度</td>
        <td>
            <input name="len" type="text" class="text_value" id="len" value="" reg="[0-9]" msg="字段长度只能为数字" style="width:45px;" />
        	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;小数位&nbsp;&nbsp;&nbsp;&nbsp;
        	<input name="decimal" type="text" class="text_value" id="decimal" value="" reg="[0-9]" msg="小数位只能为数字" style="width:45px;" />
        </td>
        <td id="len-msg">组图上传建议长度是250，如果图片较多可把值设大点</td>
      </tr>
      <tr>
        <td align="right">字段描述</td>
        <td>
          <input name="name" type="text" class="text_value" id="name" style="width:100px;" value="{% if formField.name is defined %}{{ formField.name }}{% endif %}" reg="\S" msg="字段描述不能为空" />
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;字段名&nbsp;&nbsp;&nbsp;&nbsp;
          <input name="field" type="text" class="text_value" id="field"  style="width:100px;" value="{% if formField.field is defined %}{{ formField.field }}{% endif %}" reg="[a-zA-Z_]" msg="字段名只能为英文和下划线" />
        </td>
        <td></td>
        </tr>
      <tr>
        <td align="right">字段提示</td>
        <td>
          <input name="tip" type="text" class="text_value" id="tip" value="{% if formField.tip is defined %}{{ formField.tip }}{% endif %}" style="width:150px;" />
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;字段顺序&nbsp;&nbsp;&nbsp;&nbsp;
          <input name="sequence" type="text" class="text_value" id="sequence" style="width:30px;" reg="[0-9]" msg="字段顺序只能为数字" value="{% if formField.sequence is defined %}{{ formField.sequence }}{% else %}0{% endif %}" />
        </td>
        <td>数字越小越靠前</td>
        </tr>
      <tr>
        <td align="right">是否必填</td>
        <td>
          <input name="is_must" id="is_must1" type="radio" value="1" {% if formField.is_must is defined and formField.is_must==1 %} checked="checked" {% endif %} />
         <label for="is_must1">&nbsp;是</label>
          &nbsp;&nbsp;
          <input name="is_must" id="is_must0" type="radio" value="0" {% if formField.is_must is not defined or formField.is_must==0 %} checked="checked" {% endif %}/>
          <label for="is_must0">&nbsp;否</label>
        </td>
        <td></td>
       </tr>
       <tr>
        <td align="right">是否唯一</td>
        <td>
          <input name="is_unique" id="is_unique1" type="radio" value="1" {% if formField.is_unique is defined and formField.is_unique==1 %} checked="checked" {% endif %} />
         <label for="is_unique1">&nbsp;是</label>
          &nbsp;&nbsp;
          <input name="is_unique" id="is_unique0" type="radio" value="0" {% if formField.is_unique is not defined or formField.is_unique==0 %} checked="checked" {% endif %}/>
          <label for="is_unique0">&nbsp;否</label>
        </td>
        <td>选择是则不允许有重复数据</td>
       </tr>
       <tr>
        <td align="right">后台列表显示</td>
        <td>
          <input name="admin_display" id="admin_display1" type="radio" value="1" {% if formField.admin_display is defined and formField.admin_display==1 %} checked="checked" {% endif %} />
          <label for="admin_display1">&nbsp;是</label>
          &nbsp;&nbsp;
          <input name="admin_display" id="admin_display0" type="radio" value="0" {% if formField.admin_display is not defined or formField.admin_display==0 %} checked="checked" {% endif %} />
          <label for="admin_display0">&nbsp;否</label>
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;后台列表显示长度&nbsp;&nbsp;&nbsp;&nbsp;
          <input name="admin_display_len" type="text" class="text_value" id="admin_display_len" style="width:30px;" reg="[0-9]" msg="后台列表显示长度只能为数字" value="{% if formField.admin_display_len is defined %}{{ formField.admin_display_len }}{% else %}0{% endif %}" />
        </td>
        <td>为0则不限制长度</td>
       </tr>
       <tr>
        <td align="right">默认内容</td>
        <td><input name="default" type="text"  class="text_value" id="default" value="{% if formField.default is defined %}{{ formField.default }}{% endif %}" />
        </td>
        <td id="default-msg"></td>
       </tr>
       <tr>
        <td align="right">字段配置</td>
        <td><textarea name="config" class="text_textarea" id="config">{% if formField.config is defined %}{{ formField.config }}{% endif %}</textarea>
        </td>
        <td id="config-msg" style="line-height: 120%;"></td>
       </tr>
    </table>
</div>
{% if actionPower %}
<!--普通提交-->
<div class="form_submit">
<input name="form_id" type="hidden" value="{% if formField.form_id is defined %}{{ formField.form_id }}{% else %}{{ formId }}{% endif %}">
<input name="id" type="hidden" value="{% if formField.id is defined %}{{ formField.id }}{% endif %}">
<button type="submit" class="button" click_tip="{{ actionName }}中...">{{ actionName }}</button> 
</div>
{% endif %}
</form>
</div>
</div>
<script type="text/javascript">
//提交表单
var win = art.dialog.open.origin;
saveform({
	//debug:true,
	success:function(json){
		if(json.status == 10000) {
			art.dialog.tips(json.message, 2);
			win.location.reload();
	    	art.dialog.close();
		}else {
			art.dialog.tips(json.message, 3);
		}
	}
});

fildtype({% if action=='edit' %}{{ formField.type }},{{ formField.property }}{% else %}1{% endif %});
{% if action=='edit' %}
fildproperty({{ formField.property }},{{ formField.len }},{{ formField.decimal }},"{{ formField.default }}");
$('#type').addClass('disabled');
$('#type').attr('disabled', true);
$('#property').addClass('disabled');
$('#property').attr('disabled', true);
$('#field').addClass('disabled');
$('#field').attr('readonly', true);
{% endif %}

//获取最佳设置
function fildtype(type, property=false){
	type=parseInt(type);
	$('#property option').hide();
	$('#property option:selected').removeProp('selected');
	$('#property option[value="1"]').show();
	switch (type) {
		case 1:
			$('#property option[value="2"]').show();
			$('#property option[value="4"]').show();
			$('#property option[value="5"]').show();
			$('#property option[value="6"]').show();
			break;
		case 2:
			$('#property option[value="3"]').show();
			break;
		case 3:
			$('#property option[value="3"]').show();
			$('#property option[value="3"]').attr('selected', true);
			break;
		case 4:
		case 5:
		case 9:
			break;
		case 6:
			$('#property option[value="3"]').show();
			break;
		case 7:
		case 8:
			$('#property option[value="2"]').show();
			$('#property option[value="6"]').show();
			break;
   }
	if(property !== false) {
		$('#property option[value="' + property + '"]').attr('selected', true);
	}
	fildproperty($('#property option:selected').val());
}

function fildproperty(property, len=false, decimal=false, defaultValue=false) {
	property=parseInt(property);
	$('#decimal').val(0);
	$('#decimal').addClass('disabled');
	$('#decimal').attr('readonly', true);
	$('#len').val(0);
	$('#len').addClass('disabled');
	$('#len').attr('readonly', true);
	$('#default').val('');
	$('#default').removeClass('disabled');
	$('#default').attr('readonly', false);
	$('#default-msg').text('');
	var msg = '';
	switch(property) {
		case 1:
			msg="字符串，字段长度：1-21844";
			$('#len').val(250);
			$('#len').removeClass('disabled');
			$('#len').attr('readonly', false);
			break;
		case 2:
			msg="整型（-2147483648 到 2147483647），字段长度：1-11";
			$('#len').val(11);
			$('#len').removeClass('disabled');
			$('#len').attr('readonly', false);
			$('#default-msg').text('默认值只能为整数，允许为空');
			break;
		case 3:
			msg="文本（65,535 bytes）";
			$('#default').addClass('disabled');
			$('#default').attr('readonly', true);
			$('#default-msg').text('text类型不允许设置默认值');
			break;
		case 4:
			msg="日期（xxxx-xx-xx xx:xx:xx）";
			$('#default-msg').text('默认值只能设置为 xxxx-xx-xx xx:xx:xx 格式，允许为空');
			break;
		case 5:
			msg="定点数（M，D）- 整数部分（M）最大为 65（默认 10），小数部分（D）最大为 30（默认 0）";
			$('#len').val(10);
			$('#len').removeClass('disabled');
			$('#len').attr('readonly', false);
			$('#decimal').val(0);
			$('#decimal').removeClass('disabled');
			$('#decimal').attr('readonly', false);
			$('#default-msg').text('默认值只能设为整数或小数，允许为空');
			break;
		case 6:
			msg="短整型（-128到127），字段长度：1-4";
			$('#len').val(4);
			$('#len').removeClass('disabled');
			$('#len').attr('readonly', false);
			$('#default-msg').text('默认值只能设为整数(-128 - 127)');
			break;
	}
	$('#property-msg').text(msg);
	if(len !== false) {
		$('#len').val(len);
	}
	if(decimal !== false) {
		$('#decimal').val(decimal);
	}
	if(defaultValue !== false) {
		$('#default').val(defaultValue);
	}
	changeMsg();
}

function changeMsg() {
	var type = parseInt($("#type").val());
	var property = parseInt($("#property").val());
	var configMsg = '',defaultMsg = '', lenMsg='';
	console.log(type);
	console.log(property);
	switch(type) {
		case 1:
			switch(property) {
				case 4:
					configMsg = '配置后台显示格式和时间选择器格式，默认：<br/>Y-m-d H:i:s<br/>yyyy-MM-dd HH:mm:ss';
			}
			break;
		case 6:
			switch(property) {
				case 1:
					$("#len").val(2500);
					lenMsg='每张图片（加截图）建议长度为200-250<br>2500 为 10 张图片的长度';
			}
			break;
		case 7:
		case 8:
			configMsg = '需要配置参数，格式为：<br>value=text<br>0=女<br>1=男<br><font color="red">当字段属性为 int、tingyint 时， value 必须为数字</font>';
			break;
		case 9:
			configMsg = '需要配置参数，格式为：<br>value=text<br>0=女<br>1=男<br><font color="red">当字段属性为 int、tingyint 时， value 必须为数字</font>';
			$('#default-msg').text('多个默认值请用逗号分开，例如（1,2,3）');
			break;
	}
	$("#config-msg").html(configMsg);
	$("#len-msg").html(lenMsg);
}


</script>
</body>