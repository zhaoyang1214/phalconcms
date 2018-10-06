<div class="page_function">
  <div class="info">
    <h3>{{ form.name }}管理</h3>
    <small>可以添加或者修改{{ form.name }}</small> </div>
  <div class="exercise"> 
   <a href="{{ url('formdata/index/form_id/')~form.id }}">{{ form.name }}列表</a>
   {% if formdataAddPower %}
   <a href="javascript:menuload('{{ url('formdata/add/form_id/')~form.id }}')">{{ form.name }}添加</a>
   {% endif %}
   </div>
</div>
<div class="page_main">
  <div class="page_table table_list">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <th width="10%"><center>序号</center></th>
        {% for formField in formFieldList %}
        <th><center>{{ formField.name }}</center></th>
        {% endfor %}
        <th><center>表单操作</center></th>
      </tr>
      {% for formData in formDataList %}
      <tr>
        <td><center>{{ loop.index }}</center></td>
        {% for formField in formFieldList %}
        <td><center>{{ formField.getFieldValue(formData) }}</center></td>
        {% endfor %}
        <td><center>
        {% if formdataInfoPower %}
        <a href="{{ url('formdata/info/id/')~formData.id~'/form_id/'~form.id }}">查看</a>
        {% endif %}
        {% if formdataDeletePower %}
        <a href="javascript:void(0);" onclick="del('{{ formData.id }}',this)">删除</a>
        {% endif %}
        </center></td>
      </tr>
      {% endfor %}
    </table>
  </div>
</div>

<div class="page_tool">
  <ul class="pageMenu clearfix">
	{{ pageShow }}
</ul>
</div>
<script>
//删除
function del(id,obj) {
	var obj;
	ajaxpost({
		name:'确定要删除此记录？',
		url:"{{ url('formdata/delete')}}",
		data:{id: id, form_id:{{ form.id }} },
		tip:1,
		success:function(json){
			if(json.status == 10000) {
				$(obj).parents('tr').remove();
			}
		}
	});
}
</script>