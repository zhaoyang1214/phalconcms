<div class="page_function">
  <div class="info">
    <h3>管理员管理</h3>
    <small>使用以下功能进行管理员管理</small> </div>
  <div class="exercise"> 
   <a href="javascript:menuload('{{ url('Admin/index') }}')">管理员列表</a>
   {% if adminAddPower %}
   <a href="javascript:menuload('{{ url('Admin/add') }}')">添加管理员</a>
   {% endif %}
   </div>
</div>
<div class="page_main">
  <div class="page_table table_list">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <th width="5%"><center>序号</center></th>
        <th width="20%">管理员帐号</th>
        <th width="15%">管理员名称</th>
        <th width="15%">管理组</th>
        <th width="20%"><center>创建时间</center></th>
        <th width="5%"><center>状态</center></th>
        <th width="20%"><center>操作</center></th>
      </tr>
      {% for v in adminList %}
      <tr>
        <td><center>{{ loop.index }}</center></td>
        <td>{{ v.username }}</td>
        <td>{{ v.nicename }}</td>
        <td>{{ v.group_name }}</td>
        <td><center>{{ v.regtime }}</center></td>
        <td><center>{{ admin.getStatus(v.status) }}</center></td>
        <td><center>
        {% if adminInfoPower and v.grade > adminGroupInfo['grade'] %}
        <a href="{{ url('Admin/info/id/')~v.id }}">查看</a>
        {% endif %}
        {% if adminEditInfoPower and ((v.id==adminInfo['id'] and bitwise(adminGroupInfo['admin_power'], '&', 8)) or (v.grade==adminGroupInfo['grade'] and bitwise(adminGroupInfo['admin_power'], '&', 16)) or (v.grade > adminGroupInfo['grade'] and bitwise(adminGroupInfo['admin_power'], '&', 32))) %}
        &nbsp;&nbsp;<a href="{{ url('Admin/editInfo/id/')~v.id }}">修改资料</a>
        {% endif %}
        {% if adminDeletePower and v.grade>adminGroupInfo['grade'] %}
        &nbsp;&nbsp;<a href="javascript:void(0);" onclick="del('{{ v.id }}',this)">删除</a>
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
{% if adminDeletePower %}
//删除
function del(id,obj) {
	var obj;
	ajaxpost({
		name:'是否确认删除删除此管理员吗？',
		url:"{{ url('Admin/delete') }}",
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