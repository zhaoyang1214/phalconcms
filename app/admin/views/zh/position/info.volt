<div class="page_function">
  <div class="info">
    <h3>推荐位{{ actionName }}</h3>
    <small>使用以下功能进行推荐位{{ actionName }}操作</small> 
  </div>
</div>
<div class="tab" id="tab">

 <a class="selected" href="#">{{ actionName }}推荐位</a> 
 {% if positionIndexPower %}
 <a href="javascript:menuload('{{ url('position/index') }}')">推荐位列表</a>
 {% endif %}
 </div>
<div class="page_form">
<form action="{{ actionUrl }}" method="post" id="form">
<div class="page_table form_table">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      
      <tr>
        <td width="100" align="right">推荐位名称</td>
        <td width="300">
        <input name="name" type="text" class="text_value" id="name" value="{% if position.name is defined %}{{ position.name }}{% endif %}" reg="\S" msg="名称不能为空" />
        </td>
        <td></td>
      </tr>
      <tr>
        <td width="100" align="right">推荐位顺序</td>
        <td>
        <input name="sequence" type="text" class="text_value" id="sequence" value="{% if position.sequence is defined %}{{ position.sequence }}{% else %}0{% endif %}" reg="^[0-9]*$" msg="只能是数字" /> 
        </td>
        <td>针对后台的显示顺序(顺序排列)，数字越小越排在前面</td>
      </tr>     
      
    </table>
</div>

<!--普通提交-->
<div class="form_submit">
{% if actionPower %}
<input name="id" type="hidden" value="{% if position.id is defined %}{{ position.id }}{% endif %}" />
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
	listurl:"{{ url('position/index') }}",
	name : '{{ jumpButton }}'
});
</script>