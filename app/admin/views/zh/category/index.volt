<div class="page_function">
  <div class="info">
    <h3>栏目管理</h3>
    <small>使用以下功能进行栏目添加操作</small> </div>
  <div class="exercise"> 
   <a href="javascript:menuload('{{ url('category/index') }}')">栏目列表</a>
   {% for v in list %}
   <a href="javascript:menuload('{{ url(v['category']~'/add') }}')">添加{{ v['name'] }}栏目</a>
   {% endfor %}
   </div>
</div>
<div class="page_main">
  <div class="page_table table_list">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <th width="10%"><center>ID</center></th>
        <th width="15%">栏目名称</th>
        <th width="20%">url名称</th>
        <th width="10%"><center>顺序</center></th>
        <th width="10%"><center>栏目显示</center></th>
        <th width="10%"><center>栏目属性</center></th>
        <th width="10%"><center>栏目类型</center></th>
        <th width="15%"><center>栏目操作</center></th>
      </tr>
      {% for category in categoryList %}
      <tr>
        <td><center>{{ category['id'] }}</center></td>
        <td>
        <a href="/category/{{ category['urlname'] }}" target="_blank">{{ category['cname'] }}</a>
        {% if category['image'] is not empty %}
        <a href="javascript:;" rel="{{ category['image'] }}" class="class_pic"><img align="AbsMiddle" src="{{ static_url('images/ico/pic.png') }}" width="14" height="14" alt="{{ category['name'] }}" /></a>
        {% endif %}
        </td>
        <td>{{ category['urlname'] }}</td>
        <td><center>
        {% if categorySequencePower %}
        <input type="text" value="{{ category['sequence'] }}" class="sequence" onblur="sequence({{ category['id'] }},$(this).val())" />
        {% else %}
        {{ category['sequence'] }}
        {% endif %}
        </center></td>
        <td><center>
        {% if category['is_show'] %}
        <font color=green><b>√</b></font>
        {% else %}
        <font color=red><b>×</b></font>
        {% endif %}
        </center></td>
        <td><center>
        {% if category['type']==1 %}
        	频道
        {% elseif category['type']==2 %}
        	列表
        {% endif %}
        </center></td>
        <td><center>{{ categoryModelList[category['category_model_id']]['name'] }}</center></td>
        <td><center>
        <a href="{{ url(categoryModelList[category['category_model_id']]['category']~'/info/id/'~category['id']) }}">查看</a>
        &nbsp;&nbsp;<a href="javascript:void(0);" onclick="del('{{ url(categoryModelList[category['category_model_id']]['category']~'/delete') }}',{{ category['id'] }},this)">删除</a>
        </center>
         </td>
      </tr>
      {% endfor %}
    </table>
  </div>
</div>

<div class="page_tool">
  <ul class="pageMenu clearfix">
	{{ pageShow }}
</ul>
<script>
//栏目形象图
$(".class_pic").powerFloat({
    targetMode: "ajax"
});
//栏目删除
function del(url,id,obj) {
	var obj;
	ajaxpost({
		name:'删除此栏目会删除栏目下的内容！',
		url:url,
		data:{id:id},
		tip:true,
		success:function(){
			$(obj).parents('tr').remove();
		}
	});
}
//栏目排序
function sequence(id,sequence){
	ajaxpost_w({
		url:'{{ url('category/sequence') }}',
		data:{ id:id, sequence:sequence },
		tip:1,
		success:function(json){
			window.location.href='{{ request.getURI() }}';
		}
	});
}
</script>