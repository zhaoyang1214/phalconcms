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
    <h3>模型修改</h3>
    <small>使用以下功能进行模型修改操作</small> 
  </div>
</div>
<div class="page_form">
<form action="{{ actionUrl }}" method="post" id="form">
<div class="page_table form_table">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="120">模型名称</td>
        <td width="300">
        <input name="name" type="text" class="text_value" id="name" value="{% if categoryModel.name is defined %}{{ categoryModel.name }}{% endif %}" reg="\S" msg="模型名称不能为空" />
        </td>
      </tr>
      <tr>
        <td width="120">状态</td>
        <td width="300">
        <input name="status" id="status1" type="radio" value="1" {% if categoryModel.status is not defined or categoryModel.status==1 %}checked="checked"{% endif %} />
        <label for="status1">正常</label>&nbsp;&nbsp;
        <input name="status" id="status2" type="radio" value="0" {% if categoryModel.status is defined and categoryModel.status==0 %}checked="checked"{% endif %}/>
       <label for="status2"> 禁用 </label>
        </td>
      </tr>
      <tr>
        <td width="120">内容来源(一行一个)</td>
        <td width="300"><textarea name="befrom" class="text_textarea"  id="befrom" reg="\S" msg="内容来源不能为空">{% if categoryModel.befrom %}{{ categoryModel.befrom }}{% endif %}</textarea>
        </td>
      </tr>
    </table>
</div>

{% if actionPower %}
<div class="form_submit">
<input name="id" type="hidden" value="{% if categoryModel.id %}{{ categoryModel.id }}{% endif %}" />
<button type="submit" class="button" click_tip="{{ actionName }}中...">{{ actionName }}</button> 
</div>
{% endif %}
</form>
</div>
</div>
<script type="text/javascript">
var win = art.dialog.open.origin;
saveform({
	success:function(msg){
		if(msg.status==10000){
			tip({msg:msg.data,time:1});
			win.location.reload();
	    	art.dialog.close();
		}else{
			tip({msg:msg.message});
		}
	}
});
</script>
</body>
</body>