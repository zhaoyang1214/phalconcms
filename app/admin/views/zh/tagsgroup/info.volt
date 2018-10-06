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
    <h3>TAG组{{ actionName }}</h3>
    <small>使用以下功能进行TAG组{{ actionName }}操作</small> </div>
</div>
<div class="page_form">
<form autocomplete="off" action="{{ actionUrl }}" method="post" id="form">
<div class="page_table form_table">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="120" align="right">分组名称</td>
        <td><input name="name" type="text"  class="text_value" id="name" value="{% if tagsGroup.name is defined %}{{ tagsGroup.name }}{% endif %}" />
        </td>
       </tr>
    </table>
</div>
<!--普通提交-->
{% if actionPower %}
<div class="form_submit">
<input name="id" type="hidden" value="{% if tagsGroup.id is defined %}{{ tagsGroup.id }}{% endif %}">
<button type="submit" class="button"click_tip="{{ actionName }}中...">{{ actionName }}</button> 
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
</script>
</body>