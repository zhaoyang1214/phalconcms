<div class="page_function">
  <div class="info">
    <h3>表单管理</h3>
    <small>可以添加或者修改表单功能</small> </div>
  <div class="exercise"> 
   <a href="{{ url('form/index') }}">表单列表</a>
   {% if formAddPower %}
   <a href="{{ url('form/add') }}">表单添加</a>
   {% endif %}
   </div>
</div>
<div class="page_main">
  <div class="page_table table_list">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <th width="10%"><center>序号</center></th>
        <th width="30%">表单编号</th>
        <th width="20%">表单</th>
        <th width="20%"><center>表单名</center></th>
        <th width="20%"><center>表单操作</center></th>
      </tr>
      {% for form in formList %}
      <tr>
        <td><center>{{ loop.index }}</center></td>
        <td>{{ form.no }}</td>
        <td>{{ form.name }}</td>
        <td><center>{{ form.table }}</center></td>
        <td><center>
        {% if formfieldIndexPower %}
        <a href="{{ url('formfield/index/form_id/')~form.id }}">字段管理</a>
        {% endif %}
        {% if formInfoPower %}
        &nbsp;&nbsp;<a href="{{ url('form/info/id/')~form.id }}">查看</a>
        {% endif %}
        {% if formDeletePower %}
         &nbsp;&nbsp;<a href="javascript:void(0);" onclick="del('{{ form.id }}',this)">删除</a>
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
loadmenu();
function loadmenu(){
	var doc = window.parent.document;
	$.get('{{ url('form/manage/origin/2') }}', function(result){
        $(doc).contents().find("#nav").html(result);
  	});
}

function delmenu(id){
	var doc = window.parent.document;
	$(doc).contents().find("#formlist_id_" + id).remove();
}
{% if formDeletePower %}
//删除
function del(id,obj) {
	var obj;
	ajaxpost({
		name:'删除模型后会同时删除模型表和内容！ ',
		url:"{{ url('form/delete') }}",
		data:{id: id},
		tip:1,
		success:function(json){
			if(json.status == 10000) {
				$(obj).parents('tr').remove();
				delmenu(id);
			}
		}
	});
}
{% endif %}
</script>