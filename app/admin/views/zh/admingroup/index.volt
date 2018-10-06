<div class="page_function">
  <div class="info">
    <h3>管理组管理</h3>
    <small>使用以下功能进行管理组添加操作</small> </div>
  <div class="exercise"> 
   <a href="javascript:menuload('{{ url('Admingroup/index') }}')">管理组列表</a>
   {% if admingroupAddPower %}
   <a href="javascript:menuload('{{ url('Admingroup/add') }}')">添加管理组</a>
   {% endif %}
   </div>
</div>
<div class="page_main">
  <div class="page_table table_list">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <th width="20%"><center>ID</center></th>
        <th width="50%">管理组名称</th>
        <th width="30%"><center>操作</center></th>
      </tr>
      {% for adminGroup in adminGroupList %}
      <tr>
        <td><center>{{ loop.index }}</center></td>
        <td>{{ adminGroup.name }}</td>
        <td><center>
        {% if admingroupInfoPower and adminGroup.grade>adminGroupInfo['grade'] %}
        <a href="{{ url('Admingroup/info/id/')~adminGroup.id }}">查看</a>
        {% endif %}
        {% if admingroupDeletePower and adminGroup.grade>adminGroupInfo['grade'] %}
        &nbsp;&nbsp;<a href="javascript:void(0);" onclick="del('{{ adminGroup.id }}',this)">删除</a>
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
{% if admingroupDeletePower %}
//删除
function del(id,obj) {
	var obj;
	ajaxpost({
		name:'是否确认删除删除此用户组？',
		url:"{{ url('Admingroup/delete') }}",
		data:{id: id},
		tip:1,
		success:function(json){
			if(json.status==10000) {
				$(obj).parents('tr').remove();
			}
		}
	});
}
{% endif %}
</script>