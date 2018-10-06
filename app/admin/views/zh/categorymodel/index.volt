<div class="page_function">
  <div class="info">
    <h3>模型管理</h3>
    <small>模型包括文章、图片、视频等发布功能</small> </div>
  <div class="exercise"> 
   <a href="javascript:menuload('{{ url('categorymodel/index') }}')">模型列表</a>
   </div>
</div>
<div class="page_main">
  <div class="page_table table_list">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <th width="10%"><center>ID</center></th>
        <th width="30%">模型名称</th>
        <th width="30%"><center>模型状态</center></th>
        <th width="30%"><center>模型操作</center></th>
      </tr>
      {% for categoryModel in categoryModelList %}
      <tr>
        <td><center>{{ categoryModel.id }}</center></td>
        <td>{{ categoryModel.name }}模型</td>
        <td><center>{% if categoryModel.status==1 %}开启{% else %}禁用{% endif %}</center></td>
        <td><center>
         {% if categorymodelInfoPower %}
         <a  href="javascript:void(0);" onclick="edit('{{ url('categorymodel/info/id/' ~ categoryModel.id) }}')">查看</a>
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
function edit(url) {
	urldialog({
	title:'模型配置',
	url:url,
	height:570
	});
};
</script>