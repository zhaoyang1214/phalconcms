<div class="page_function">
  <div class="info">
    <h3>自定义变量{{ actionName }}</h3>
    <small>使用以下功能进行自定义变量{{ actionName }}操作</small> 
  </div>
</div>
<div class="tab" id="tab"> <a class="selected" href="#">{{ actionName }}变量</a> 
{% if fragmentIndexPower %}
<a  href="javascript:menuload('{{ url('fragment/index') }}')">变量列表</a>
{% endif %}
</div>
<div class="page_form">
<form action="{{ actionUrl }}" method="post" id="form">
<div class="page_table form_table">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      
      <tr>
        <td width="100" align="right">描述</td>
        <td width="800">
        <input name="title" type="text" class="text_value" id="title" value="{% if fragment.title is defined %}{{ fragment.title }}{% endif %}" reg="\S" msg="描述不能为空" />
        </td>
        <td></td>
      </tr>
      <tr>
        <td width="100" align="right">标识</td>
        <td>
        <input name="sign" type="text" class="text_value" id="sign" reg="[a-zA-Z_0-9]" msg="标识只能为以英文开头的英文、数字、下划线" value="{% if fragment.sign is defined %}{{ fragment.sign }}{% endif %}" />
        </td>
        <td></td>
      </tr>
      <tr>
	    <td align="right">内容</td>
	    <td>
	        <script src="{{ static_url('js/ueditor.config.js') }}" type="text/javascript"></script>
			<script src="/plugins/ueditor/ueditor.all.js" type="text/javascript"></script>
			<script src="/plugins/ueditor/lang{% if constant('LANGUAGE') == 'zh' %}/zh-cn/zh-cn.js{% else %}/en/en.js{% endif %}" type="text/javascript"></script>
			<script name="content" id="content" type="text/plain" style="width:100%; height:400px;">{% if fragment.content is defined %}{{ fragment.content|htmlspecialchars_decode }}{% endif %}</script>
			<script type="text/javascript">UE.getEditor("content", {"serverUrl":"{{ url('ueditor/index/origin/3') }}"});</script>
	  	</td>
	  	<td></td>
	  </tr>
    </table>
</div>
{% if actionPower %}
<!--普通提交-->
<div class="form_submit">
<input name="id" type="hidden" value="{% if fragment.id is defined %}{{ fragment.id }}{% endif %}" />
<button type="submit" class="button" click_tip="{{ actionName }}中...">{{ actionName }}</button> 
{% endif %}
</div>
</form>
</div>
</div>
<script type="text/javascript">
//提交表单
savelistform({
	//debug: true,
	addurl:"{{ request.getURI() }}",
	listurl:"{{ url('fragment/index') }}",
	name : '{{ jumpButton }}'
});
</script>