<div class="page_function">
  <div class="info">
    <h3>{{ form.name }}{{ actionName }}</h3>
    <small>使用以下功能进{{ form.name }}{{ actionName }}操作</small> 
  </div>
</div>
<div class="tab" id="tab"> <a class="selected" href="#">{{ form.name }}{{ actionName }}</a> 
{% if formdataIndexPower %}
<a  href="javascript:menuload('{{ url('formdata/index/form_id/')~form.id }}')">返回{{ form.name }}列表</a>
{% endif %}
</div>
<div class="page_form">
<form action="{{ url('formdata/add') }}" method="post" id="form">
<div class="page_table form_table">
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr style="height:0px !important">
	    <td width="15%" style="height:0px !important" align="right"></td>
	    <td style="height:0px !important"></td>
	    <td width="25%" style="height:0px !important"></td>
    </tr>
    {% for formField in formFieldList %}
    	{{ formField.getFieldHtml(formData)}}
    {% endfor %}
	
   </table>
</div>
{% if actionPower %}
<!--普通提交-->
<div class="form_submit">
<input name="id" type="hidden" value="{% if formData.id is defined %}{{ formData.id }}{% endif %}" />
<input name="form_id" type="hidden" value="{{ form.id }}" />
<button type="submit" class="button" click_tip="{{ actionName }}中...">{{ actionName }}</button>
</div>
{% endif %}
</form>
</div>
</div>
<script type="text/javascript">
//提交表单
savelistform({
	//debug: true,
	addurl:"{{ request.getURI() }}",
	data: function(){
		$('input.editor-input').each(function(){
			var name = $(this).attr("name");
			$(this).val(UE.getEditor('editor-'+name).getContent());
		});
	},
	listurl:"{{ url('formdata/index/form_id/')~form.id }}",
	name : '{{ jumpButton }}'
});
</script>