<div class="page_function">
  <div class="info">
    <h3>内容替换{{ actionName }}</h3>
    <small>使用以下功能进行内容替换{{ actionName }}操作</small> 
  </div>
</div>
<div class="tab" id="tab"> <a class="selected" href="#">{{ actionName }}内容替换</a> 
{% if replaceIndexPower %}
<a  href="javascript:menuload('{{ url('replace/index') }}')">返回内容替换列表</a>
{% endif %}
</div>
<div class="page_form">
<form action="{{ actionUrl }}" method="post" id="form">
<div class="page_table form_table">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="100" align="right">被替换内容</td>
        <td width="300">
        <input name="key" type="text" class="text_value" id="key" value="{% if replace.key is defined %}{{ replace.key }}{% endif %}" reg="\S" msg="内容替换名称不能为空" />
        </td>
        <td></td>
      </tr>
      <tr>
        <td width="100" align="right">替换后内容</td>
        <td>
        <input name="content" type="text" class="text_value" id="content" reg="\S" msg="替换后内容不能为空" value="{% if replace.content is defined %}{{ replace.content }}{% endif %}" />
        </td>
        <td></td>
      </tr>
      <tr>
        <td width="100" align="right">替换次数</td>
        <td>
        <input name="num" type="text" class="text_value" id="num" value="{% if replace.num is defined %}{{ replace.num }}{% else %}1{% endif %}" reg="[0-9]" msg="替换次数只能为数字" />
        </td>
        <td>0表示不限制次数</td>
      </tr>
      <tr>
        <td align="right">状态</td>
        <td>
          <input name="status" id="status1" type="radio" value="1" {% if replace.status is defined and replace.status==1 %} checked="checked" {% endif %} />
         <label for="status1">&nbsp;开启</label>
          &nbsp;&nbsp;
          <input name="status" id="status0" type="radio" value="0" {% if replace.status is not defined or replace.status==0 %} checked="checked" {% endif %}/>
          <label for="status0">&nbsp;关闭</label>
        </td>
        <td>关闭则不替换</td>
       </tr>
    </table>
</div>
{% if actionPower %}
<!--普通提交-->
<div class="form_submit">
<input name="id" type="hidden" value="{% if replace.id is defined %}{{ replace.id }}{% endif %}" />
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
	listurl:"{{ url('replace/index') }}",
	name : '{{ jumpButton }}'
});
</script>