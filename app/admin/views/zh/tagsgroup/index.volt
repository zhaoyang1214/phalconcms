<div class="page_function">
  <div class="info">
    <h3>TAG分组管理</h3>
    <small>管理TAG所属分类</small> </div>
  <div class="exercise"> 
  {% if tagsIndexPower %}
   <a href="javascript:menuload('{{ url('tags/index') }}')">TAG列表</a> 
   {% endif %}
  <a href="javascript:menuload('{{ url('tagsgroup/index') }}')">TAG分组管理</a>
  {% if tagsgroupAddPower %}
  <a  href="javascript:;" onclick="add()">添加TAG组</a> 
  {% endif %}
   </div>
</div>
<div class="page_main">
  <div class="page_table table_list">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <th width="100">序号</th>
        <th>名称</th>
        <th width="150"><center>操作</center></th>
      </tr>
      {% for tagsGroup in tagsGroupList %}
      <tr>
        <td>{{ loop.index }}</td>
        <td>{{ tagsGroup.name }}</td>
        <td><center>
        	{% if tagsgroupInfoPower %}
          <a  href="javascript:;" onclick="edit({{ tagsGroup.id }})">编辑</a>&nbsp;&nbsp;
          {% endif %}
          {% if tagsgroupDeletePower %}
          <a  href="javascript:;" onclick="del({{ tagsGroup.id }},this)">删除</a>
          {% endif %}
        </center></td>
      </tr>
      {% endfor %}
    </table>
  </div>
</div>

<div class="page_tool" >
<ul class="pageMenu clearfix">
	{{ pageShow }}
</ul>
</div>
<script>
function add() {
	urldialog({
	title:'添加TAG组',
	height:350,
	url:'{{ url('tagsgroup/add') }}'
	});
};
//修改
function edit(id) {
	urldialog({
	title:'字段编辑',
	height:350,
	url:'{{ url('tagsgroup/info/id/') }}'+id
	});
};
//删除
function del(id,obj) {
	var obj;
	ajaxpost({
		name:'删除此分组将会把此分组下的TAG归类到无分组！ ',
		url:"{{ url('tagsgroup/delete') }}",
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