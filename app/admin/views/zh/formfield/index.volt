<div class="page_function">
  <div class="info">
    <h3>{{ form.name }}字段管理</h3>
    <small>管理表单内的字段</small> </div>
  <div class="exercise"> 
   <a href="javascript:menuload('{{ url('formfield/index/form_id/')~form.id }}')">字段列表</a>
   {% if formfieldAddPower %}
   <a href="javascript:;" onclick="add()">添加字段</a>
   {% endif %}
   </div>
</div>
<div class="page_main">
  <div class="page_table table_list">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <th width="10%"><center>序号</center></th>
        <th width="20%"><center>字段描述</center></th>
        <th width="15%"><center>字段名</center></th>
        <th width="10%"><center>字段类型</center></th>
        <th width="10%"><center>字段属性</center></th>
        <th width="10%"><center>字段顺序</center></th>
        <th width="10%"><center>后台列表显示</center></th>
        <th width="15%"><center>字段操作</center></th>
      </tr>
      {% for formField in formFieldList %}
      <tr>
        <td><center>{{ loop.index }}</center></td>
        <td><center>{{ formField.name }}</center></td>
        <td><center>{{ formField.field }}</center></td>
        <td><center>{{ formField.getType(formField.type) }}</center></td>
        <td><center>{{ formField.getProperty(formField.property) }}</center></td>
        <td><center>{{ formField.sequence }}</center></td>
        <td><center>{{ formField.getAdminDisplay(formField.admin_display) }}</center></td>
        <td><center>
        {% if formfieldInfoPower %}
        <a href="javascript:;" onclick="edit({{ formField.id }})">查看</a>
        {% endif %}
        {% if formfieldDeletePower %}
        &nbsp;&nbsp;<a href="javascript:void(0);" onclick="del('{{ formField.id }}',this)">删除</a></center></td>
      	{% endif %}
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
{% if formfieldAddPower %}
//添加
function add() {
	var documentHeight = $(document).height();
	urldialog({
	title:'字段添加',
	width:800,
	height:documentHeight > 650 ? 650 : documentHeight,
	url:'{{ url('formfield/add/form_id/')~form.id }}'
	});
};
{% endif %}
{% if formfieldInfoPower %}
//修改
function edit(id) {
	var documentHeight = $(document).height();
	urldialog({
	title:'字段编辑',
	width:800,
	height:documentHeight > 650 ? 650 : documentHeight,
	url:'{{ url('formfield/info/id/') }}'+id
	});
};
{% endif %}
{% if formfieldDeletePower %}
//删除
function del(id,obj) {
	var obj;
	ajaxpost({
		name:'删除此字段将删除字段下的内容！ ',
		url:"{{ url('formfield/delete') }}",
		data:{id: id},
		tip:1,
		success:function(json){
			if(json.status == 10000) {
				$(obj).parents('tr').remove();
			}
		}
	});
}
{% endif %}
</script>