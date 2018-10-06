<div class="page_function">
  <div class="info">
    <h3>内容替换管理</h3>
    <small>内容替换非永久替换主要用于内容增加内链</small> </div>
  <div class="exercise"> 
   <a href="javascript:menuload('{{ url('replace/index') }}')">内容替换列表</a>
   {% if replaceAddPower %}
   <a href="javascript:menuload('{{ url('replace/add') }}')">添加内容替换</a>
   {% endif %}
   </div>
</div>
<div class="page_main">
  <div class="page_table table_list">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <th width="10%"><center>序号</center></th>
        <th width="20%">被替换内容</th>
        <th width="30%"><center>替换后内容</center></th>
        <th width="10%"><center>替换次数</center></th>
        <th width="10%"><center>状态</center></th>
        <th width="20%"><center>内容替换操作</center></th>
      </tr>
      {% for replace in replaceList %}
      <tr>
        <td><center>{{ loop.index }}</center></td>
        <td>{{ replace.key }}</td>
        <td><center>{{ replace.content }}</center></td>
        <td><center>{% if replace.num > 0 %}{{ replace.num }}{% else %}不限制{% endif %}</center></td>
        <td align="center">
        {% if replace.status %}
        <font color=green><b>√</b></font>
        {% else %}
        <font color=red><b>×</b></font>
        {% endif %}
        </td>
        <td><center>
        {% if replaceInfoPower %}
        <a href="{{ url('replace/info/id/')~replace.id }}">查看</a>
        {% endif %}
        {% if replaceDeletePower %}
         &nbsp;&nbsp;<a href="javascript:void(0);" onclick="del('{{ replace.id }}',this)">删除</a>
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
		name:'确定要删除此替换？',
		url:"{{ url('replace/delete') }}",
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