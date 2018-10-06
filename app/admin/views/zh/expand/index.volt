<div class="page_function">
  <div class="info">
    <h3>扩展模型管理</h3>
    <small>将栏目与扩展模型进行绑定来实现附加字段功能</small> </div>
  <div class="exercise"> 
   <a href="javascript:menuload('{{ url('expand/index') }}')">模型列表</a>
   {% if expandAddPower %}
   <a href="javascript:;" onclick="add()">添加模型</a>
   {% endif %}
   </div>
</div>
<div class="page_main">
  <div class="page_table table_list">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <th width="10%"><center>序号</center></th>
        <th width="30%">模型名称</th>
        <th width="30%"><center>模型数据表</center></th>
        <th width="30%"><center>模型操作</center></th>
      </tr>
      {% for expand in expandList %}
      <tr>
        <td><center>{{ loop.index }}</center></td>
        <td>{{ expand.name }}</td>
        <td><center>{{ expand.table }}</center></td>
        <td><center>
        {% if expandfieldIndexPower %}
        <a href="{{ url('expandfield/index/expand_id/')~expand.id }}">字段管理</a>
        {% endif %}
        {% if expandInfoPower %}
         &nbsp;&nbsp;<a  href="javascript:void(0);" onclick="edit('{{ expand.id }}')">查看</a>
         {% endif %}
        {% if expandDeletePower %}
         &nbsp;&nbsp;<a href="javascript:void(0);" onclick="del('{{ expand.id }}',this)">删除</a>
         {% endif %}
         </center></td>
      </tr>
      {% endfor %}
    </table>
  </div>
</div>

<div class="page_tool">
  <div class="page"></div>
</div>
<script>
//添加
function add() {
	urldialog({
	height:300,		
	title:'模型添加',
	url:'{{ url('expand/add') }}'
	});
};
//修改
function edit(id) {
	urldialog({
	height:300,		
	title:'模型编辑',
	url:'{{ url('expand/info/id/') }}'+id
	});
};


//删除
function del(id,obj) {
	var obj;
	ajaxpost({
		name:'删除模型后会同时删除模型表和内容！ ',
		url:"{{ url('expand/delete') }}",
		data:{id: id},
		tip:1,
		success:function(json){
			if(json.status==10000) {
				$(obj).parents('tr').remove();				
			}
		}
	});
}
</script>