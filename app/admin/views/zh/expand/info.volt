<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
{{ assets.outputCss() }}

{{ assets.outputJs() }}
<script type="text/javascript">
	{% if expand is defined and expand === false %}
		art.dialog.close();
		art.dialog.open.origin.location.reload();
	{% endif %}
</script>
</head>
<body scroll="no">
<div class="page_function">
  <div class="info">
    <h3>模型{{ actionName }}</h3>
    <small>使用以下功能进行模型修改{{ actionName }}</small> 
  </div>
</div>
<div class="page_form">
<form action="{{ actionUrl }}" method="post" id="form">
<div class="page_table form_table">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="120">模型名称</td>
        <td width="300">
        <input name="name" type="text" class="text_value" id="name" value="{% if expand.name is defined %}{{ expand.name }}{% endif %}" reg="\S" msg="模型名称不能为空" />
        </td>
      </tr>
      <tr>
        <td width="120">模型表名称</td>
        <td width="300">
        <input name="table" type="text" class="text_value {% if action=='edit' %}disabled{% endif %}" {% if action=='edit' %}disabled{% endif %} id="table" value="{% if expand.table is defined %}{{ expand.table }}{% endif %}" reg="[a-zA-Z_]" msg="模型表名只能为英文和下划线"  />
        </td>
      </tr>
    </table>
</div>

<div class="form_submit">
<input name="id" type="hidden" value="{% if expand.id is defined %}{{ expand.id }}{% endif %}" />
<button type="submit" class="button" click_tip="{{ actionName }}中...">{{ actionName }}</button> 
</div>
</form>
</div>
</div>
<script type="text/javascript">
var win = art.dialog.open.origin;
saveform({
	debug:true,
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
</script>
</body>