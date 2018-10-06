<div class="page_function">
  <div class="info">
    <h3>{{ expand.name }} - 字段管理</h3>
    <small>使用以下功能进行字段编辑操作</small> </div>
  <div class="exercise"> 
   <a href="javascript:menuload('{{ url('expandfield/index/expand_id/')~expand.id }}')">字段列表</a>
   {% if expandfieldAddPower %}
   <a href="javascript:;" onclick="add()">添加字段</a>
   {% endif %}
   </div>
</div>
<div class="page_main">
  <div class="page_table table_list">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
      	<th width="10%"><center>序号</center></th>
        <th width="15%">字段名称</th>
        <th width="15%">字段</th>
        <th width="20%">显示方式</th>
        <th width="10%"><center>必填</center></th>
        <th width="10%"><center>字段顺序</center></th>
        <th width="20%"><center>操作</center></th>
      </tr>
      {% for expandField in expandFieldList %}
      <tr>
      	<td><center>{{ loop.index }}</center></td>
        <td align="left">{{ expandField.name }}</td>
        <td align="left">{{ expandField.field }}</td>
        <td align="left">{{ expandField.getType(expandField.type) }} ({{ expandField.getProperty(expandField.property) }})</td>
        <td align="center">
        {% if expandField.is_must %}
        <font color=green><b>√</b></font>
        {% else %}
        <font color=red><b>×</b></font>
        {% endif %}
        </td>
        <td align="center"><center>{{ expandField.sequence }}</center></td>
        <td><center>
         {% if expandfieldInfoPower %}
         <a href="javascript:;" onclick="edit({{ expandField.id }})" >查看</a>&nbsp;&nbsp;
         {% endif %}
         {% if expandfieldDeletePower %}
         <a href="javascript:void(0);" onclick="del('{{ expandField.id }}',this)">删除</a>
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
{% if expandfieldAddPower %}
//添加
function add() {
	var documentHeight = $(document).height();
	urldialog({
	title:'字段添加',
	width:800,
	height:documentHeight > 550 ? 550 : documentHeight,
	url:'{{ url('expandfield/add/expand_id/')~expand.id }}'
	});
};
{% endif %}
{% if expandfieldInfoPower %}
//修改
function edit(id) {
	var documentHeight = $(document).height();
	urldialog({
	title:'字段编辑',
	width:800,
	height:documentHeight > 550 ? 550 : documentHeight,
	url:'{{ url('expandfield/info/id/') }}'+id
	});
};
{% endif %}
{% if expandfieldDeletePower %}
//删除
function del(id,obj) {
	var obj;
	ajaxpost({
		name:'删除此字段会删除字段下的内容！',
		url:"{{ url('expandfield/delete') }}",
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