<div class="page_function">
  <div class="info">
    <h3>翻译{{ actionName }}</h3>
    <small>使用以下功能进行翻译{{ actionName }}操作</small> 
  </div>
</div>
<div class="tab" id="tab"> <a class="selected" href="#">{{ actionName }}翻译</a> 
{% if translateIndexPower %}
<a  href="javascript:menuload('{{ url('Translate/index') }}')">返回语言列表</a>
{% endif %}
</div>
<div class="page_form">
<form action="{{ url('Translate/edit') }}" method="post" id="form">
<div class="page_table form_table">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      
      <tr>
        <td width="100" align="right">原文</td>
        <td width="300">
        <textarea name="source_text"  class="text_textarea disabled" disabled>{{ translate.source_text }}</textarea>
        </td>
        <td></td>
      </tr>
       <tr>
        <td width="100" align="right">to</td>
        <td width="300">
        <input type="text" class="text_value disabled" id="name" value="{{ language.name }}({{ language.zh_name }})" disabled/>
        </td>
        <td></td>
      </tr>
      <tr>
        <td width="100" align="right">译文</td>
        <td>
        <textarea name="translated_text"  class="text_textarea"  reg="\S" msg="译文不能为空">{{ translate.translated_text }}</textarea>
        </td>
        <td></td>
      </tr>
    </table>
</div>
{% if translateEditPower %}
<!--普通提交-->
<div class="form_submit">
<input name="id" type="hidden" value="{{ translate.id }}" />
<button type="submit" class="button" click_tip="{{ actionName }}中...">{{ actionName }}</button> 
</div>
{% endif %}
</form>
</div>
</div>
<script type="text/javascript">
//提交表单

	savelistform({
		//debug : true,
		addurl : "{{ request.getURI() }}",
		listurl : "{{ url('Translate/index') }}",
		name : '查看修改'
	});
</script>