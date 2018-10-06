<div class="page_function">
  <div class="info">
    <h3>附件管理</h3>
    <small>管理各个模块的附件</small> </div>
  <div class="exercise"> 
   <a href="javascript:menuload('{{ url('upload/index') }}')">附件列表</a>
   </div>
</div>
<div class="page_main">
<form action="{{ url('upload/index') }}">
<div class="page_menu">
  &nbsp;&nbsp;
  文件格式：
  <select name="ext">
    	<option value="0" >全部</option>
    	<option value="1" {% if ext==1 %} selected="selected" {% endif %}  >图片</option>
        <option value="2" {% if ext==2 %} selected="selected" {% endif %}  >媒体</option>
        <option value="3" {% if ext==3 %} selected="selected" {% endif %}  >文档</option>
        <option value="4" {% if ext==4 %} selected="selected" {% endif %}  >压缩</option>
        <option value="5" {% if ext==5 %} selected="selected" {% endif %}  >其他</option>
    </select>
  &nbsp;&nbsp;
  所属模块：
  <select name="module">
            	<option value="0">全部模块</option>
            	{% for key,value in modules %}
                <option value="{{ key }}"  {% if module==key %} selected="selected" {% endif %}  >{{ value }}</option>
                {% endfor %}
            </select>
  &nbsp;&nbsp;
  文件名称：
  <input name="title" type="text" class="text_value" id="title" value="{{ title }}" />
  &nbsp;&nbsp;<input type="submit"  class="button_small" value="搜索" />
  </div>
  </form>
  <div class="page_table table_list">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <th width="10%"><center>序号</center></th>
        <th width="50%">
        	文件名称
        </th>
        <th width="15%">上传时间</th>
        <th width="15%">
        	模块
        </th>
        <th width="10%"><center>附件操作</center></th>
      </tr>
      {% for upload in uploadList %}
      <tr>
        <td><center>{{ loop.index }}</center></td>
        <td>
        <a href="javascript:;" rel="{{ upload.file }}" {% if in_array(upload.ext, ['png','jpg','jpeg','gif','bmp']) %}class="class_pic"{% endif %}>{% if title is not empty %}{{ str_replace(title, '<font color="red">'~title~'</font>', upload.title) }}{% else %}{{ upload.title }}{% endif %}</a>
        &nbsp;&nbsp;<a href="{{ upload.title }}" download="{{ upload.title }}.{{ upload.ext }}">[下载]</a>
        </td>
        <td>{{ upload.time }}</td>
        <td>{{ upload.getModule(upload.module) }}</td>
        <td><center>
        {% if uploadDeletePower %}
        <a href="javascript:void(0);" onclick="del('{{ upload.id }}',this)">删除</a>
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
//缩略图
$(".class_pic").powerFloat({
    targetMode: "ajax"
});
//删除
function del(id,obj) {
	var obj;
	ajaxpost({
		name:'确定要删除此附件？',
		url:"{{ url('upload/delete') }}",
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