<div class="page_function">
  <div class="info">
    <h3>推荐位管理</h3>
    <small>管理内容的推荐位置</small> </div>
  <div class="exercise"> 
   <a href="javascript:menuload('{{ url('position/index') }}')">推荐位列表</a>
   {% if positionAddPower %}
   <a href="javascript:menuload('{{ url('position/add') }}')">添加推荐位</a>
   {% endif %}
   </div>
</div>
<div class="page_main">
  <div class="page_table table_list">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <th width="10%"><center>序号</center></th>
        <th width="40%">名称</th>
        <th width="20%"><center>顺序</center></th>
        <th width="20%"><center>操作</center></th>
      </tr>
      {% for position in positionList %}
      <tr>
        <td><center>{{ loop.index }}</center></td>
        <td>{{ position.name }}</td>
        <td><center>{{ position.sequence }}</center></td>
        <td><center>
        {% if positionInfoPower %}
        <a href="{{ url('position/info/id/')~position.id }}">查看</a>
        {% endif %}
        {% if positionDeletePower %}
         &nbsp;&nbsp;<a href="javascript:void(0);" onclick="del('{{ position.id }}',this)">删除</a>
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
		name:'确定要删除此推荐位？',
		url:"{{ url('position/delete') }}",
		data:{id: id},
		tip:1,
		success:function(json){
			if(json.status == 10000) {
				$(obj).parents('tr').remove();
			}
		}
	});
}
</script>