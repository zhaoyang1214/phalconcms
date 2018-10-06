<div class="page_function">
  <div class="info">
    <h3>自定义变量管理</h3>
    <small>使用以下功能进行自定义变量管理操作</small> </div>
  <div class="exercise"> 
   <a href="javascript:menuload('{{ url('fragment/index') }}')">自定义变量列表</a>
   {% if fragmentAddPower %}
   <a href="javascript:menuload('{{ url('fragment/add') }}')">添加自定义变量</a>
   {% endif %}
   </div>
</div>
<div class="page_main">
  <div class="page_table table_list">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <th width="10%"><center>序号</center></th>
        <th width="30%">描述</th>
        <th width="30%"><center>标识</center></th>
        <th width="30%"><center>操作</center></th>
      </tr>
      {% for fragment in fragmentList %}
      <tr>
        <td><center>{{ loop.index }}</center></td>
        <td>{{ fragment.title }}</td>
        <td><center>{{ fragment.sign }}</center></td>
        <td><center>
        {% if fragmentInfoPower %}
        <a href="{{ url('fragment/info/id/')~fragment.id }}">查看</a>
        {% endif %}
        {% if fragmentDeletePower %}
         &nbsp;&nbsp;<a href="javascript:void(0);" onclick="del('{{ fragment.id }}',this)">删除</a>
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
//自定义变量
function del(id,obj) {
	var obj;
	ajaxpost({
		name:'删除此自定义变量？',
		url:"{{ url('fragment/delete') }}",
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