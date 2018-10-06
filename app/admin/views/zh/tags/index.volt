<div class="page_function">
  <div class="info">
    <h3>TAG管理</h3>
    <small>管理内容关键词分离的TAG标签</small> </div>
  <div class="exercise">
   <a href="javascript:menuload('{{ url('tags/index') }}')">TAG列表</a> 
  {% if tagsgroupIndexPower %}
  <a href="javascript:menuload('{{ url('tagsgroup/index') }}')">TAG分组管理</a>
  {% endif %}
  {% if tagsgroupAddPower %}
  <a  href="javascript:;" onclick="add()">添加TAG组</a> 
  {% endif %}
  </div>
</div>
<div class="page_main">
  <form action="{{ url('tags/index') }}">
  <div class="page_menu"> 排序：
    <select name="sequence" id="sequence">
	  <option value="0" {% if sequence==0 %} selected="selected" {% endif %} >默认</option>
      <option value="1" {% if sequence==1 %} selected="selected" {% endif %} >点击率 高->低</option>
      <option value="2" {% if sequence==2 %} selected="selected" {% endif %} >点击率 低->高</option>
    </select>
    &nbsp;&nbsp;
    分组筛选：
    <select name="tags_group_id" id="tags_group_id">
      <option value="-1" >全部</option>
      <option value="0" {% if tags_group_id==0 %} selected="selected" {% endif %} >未分组</option>
      {% for tagsGroup in tagsGroupList %}
  	  <option value="{{ tagsGroup.id }}"  {% if tags_group_id==tagsGroup.id %} selected="selected" {% endif %} >{{ tagsGroup.name }}</option>
      {% endfor %}
    </select>
    &nbsp;&nbsp;
    名称：
    <input style="width:100px" name="name" type="text" class="text_value" id="name" value="{{ name }}" />
    &nbsp;&nbsp;
    <input type="submit"  class="button_small" value="搜索" />
  </div>
  </form>
  <div class="page_table table_list">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <th width="100"><center>
            选择
          </center></th>
        <th width="100">序号</th>
        <th>名称</th>
        <th width="150">分组</th>
        <th width="150"><center>
            点击数
          </center></th>
      </tr>
      {% for tags in tagsList %}
      <tr>
        <td align="center"><center>
          <input name="id[]" type="checkbox" id="id[]" value="{{ tags.id }}" ></td>
        <td>{{ loop.index }}</td>
        <td>{{ tags.name }}</td>
        <td>{{ tags.tagsgroup_name }}</td>
        <td><center>
            {{ tags.click }}
          </center></td>
      </tr>
      {% endfor %}
    </table>
  </div>
</div>
<div class="page_tool">
  <div class="function">
  <input type="button" onclick="javascript:selectall('id[]');" class="button_small" value="全选" />
  {% if tagsDeletePower %}
  <input type="button" onclick="javascript:audit(1);" class="button_small" value="删除" />
  {% endif %}
  {% if tagsGroupingPower %}
  <input type="button" onclick="javascript:$('#mobile').toggle();" class="button_small" value="分组" />
  <span id="mobile" style="display:none">
  			<select name="cid"  id="cid" >
              <option value="0">选择分组</option>
               {% for tagsGroup in tagsGroupList %}
		  	  <option value="{{ tagsGroup.id }}" >{{ tagsGroup.name }}</option>
		      {% endfor %}
            </select>
            <input type="button" onclick="javascript:audit(2);" class="button_small" value="确认" />
  </span>
  {% endif %}
  </div>
  <div class="page_tool" >
<ul class="pageMenu clearfix">
	{{ pageShow }}
</ul>
</div>
</div>
<script>
function add() {
	urldialog({
	title:'TAG组添加',
	height:350,
	url:'{{ url('tagsgroup/add') }}'
	});
};
//选择
function selectall(name){   
    $("[name='"+name+"']").each(function(){//反选   
    if($(this).attr("checked")){   
          $(this).removeAttr("checked");   
    }else{   
          $(this).attr("checked",'true');   
    }   
    })  
}
//批量操作
function audit(status){
	var str="";
	$("[name='id[]']").each(function(){
	    if($(this).attr("checked")){
			  str+=$(this).val()+","; 
	    }
    })
	if(str=='') {
		art.dialog.tips('请选择', 2);
		return false;
	}
	ajaxpost({
		name:'您确认要继续进行操作吗？操作将无法撤销！',
		url: status ==1 ? "{{ url('tags/delete') }}" : "{{ url('tags/grouping') }}",
		data:{id:str, tags_group_id:$('#cid').val()},
		tip:1,
		success:function(json){
			if(json.status==10000) {
				window.location.reload();
			}
		}
	});
}
</script>