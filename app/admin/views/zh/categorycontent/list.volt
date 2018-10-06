<div class="page_function">
  <div class="info">
    <h3>内容首页</h3>
    <small>请使用左边菜单对内容进行管理，本功能用于管理条件内容</small> </div>
    <div class="tip">栏目总数：{{ categoryCount }}个，内容总数：{{ count }}条，未审核内容{{ notAuditCount }}条</div>
</div>
<div class="page_main">
	<div class="page_menu">
  &nbsp;&nbsp;
  当前列表：
  <font color=green>{% if position==0 and search=='' %}未审核内容{% else %}筛选内容{% endif %}</font>
  &nbsp;&nbsp;
  推荐位：
  <select id="position" onchange="javascript:location.href='{{ url('categorycontent/list/position/') }}' + this.value;">
  	<option value="0">全部</option>
    {% for value in positionList %} 
  	<option value="{{ url('categorycontent/list/position/'~value.id) }}"  {% if position==value.id %} selected="selected" {% endif %} >{{ value.name }}</option>
    {% endfor %}
  </select>
  &nbsp;&nbsp;
  内容标题：
  <input style="width:150px" name="search" type="text" class="text_value" id="search" value="{{ search }}" />
  &nbsp;&nbsp;<input type="button"  class="button_small" onclick="javascript:location.href='{{ url('categorycontent/list/position/') }}' + $('#position option:selected').val() + '/search/'+$('#search').val();" value="搜索" />
  </div>
  <div class="page_table table_list">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
      <th width="40"><center>选择</center></th>
        <th width="40"><center>ID</center></th>
        <th width="">标题</th>
        <th width="">栏目</th>
        <th width="40"><center>审核</center></th>
        <th width="130"><center>更新时间</center></th>
        <th width="80"><center>操作</center></th>
      </tr>
      {% for categoryContent in categoryContentList%}
      <tr id="del_{{ categoryContent.id }}">
        <td><center><input name="id[]" type="checkbox" id="id[]" value="{{ categoryContent.id }}" ></td>
        <td><center>{{ categoryContent.id }}</center></td>
        <td><span>{{ str_replace(search, '<font color="red">'~search~'</font>', categoryContent.title) }}</span>
        {% if categoryContent.position is not empty %}
        	{% for value1 in explode(',', categoryContent.position) %} 
        		{% for value2 in positionList %} 
        			{% if value1 ==value2.id %}
        				<span class="tags_span">[{{ value2.name }}]</span> 
         				{% break %}
         			{% endif %}
         		{% endfor %}
        	{% endfor %}
        {% endif %}
         {% if categorycontentQuickEditPower %}
         &nbsp;&nbsp;<a class="quickeditor" style=" display:none" href="javascript:;" onclick="quickeditor('{{ url('categorycontent/quickEdit/id/' ~ categoryContent.id) }}')">[快速编辑]</a>
         {% endif %}
        </td>
        <td>
        <a href="#">{{ categoryContent.category_name }}</a>
        </td>
        <td><center>
        {% if categoryContent.status==1 %}
        <font color=green><b>√</b></font>
        {% endif %}
        {# 这里有个bug，不能用else，应该受上面的双重循环影响 #}
        {% if categoryContent.status==0 %}
        <font color=red><b>×</b></font>
        {% endif %}
        </center></td>
        <td><center>
        {{ categoryContent.updatetime }}
        </center></td>
        
        <td><center>
        {% if categorycontentInfoPower %}
        <a href="{{ url(categoryContent.content_c ~ '/info/id/' ~ categoryContent.id) }}">查看</a>
        {% endif %}
        {% if categorycontentInfoPower %}
        &nbsp;&nbsp;<a href="javascript:void(0);" onclick="del('{{ categoryContent.id }}',this,'{{ url(categoryContent.content_c ~ '/delete') }}')">删除</a>
        {% endif %}
        </center></td>
      </tr>
      {% endfor %}
    </table>
  </div>
</div>
{% if count(categoryContentList) %}
<div class="page_tool">
  <div class="function">
  <input type="button" onclick="javascript:selectall('id[]');" class="button_small" value="全选" />
  <input type="button" onclick="javascript:selectall('id[]');" class="button_small" value="反选" />
  {% if categorycontentAuditPower %}
  <input type="button" onclick="javascript:audit(1);"  class="button_small" value="发布" />
  <input type="button" onclick="javascript:audit(0);" class="button_small" value="草稿" />
  {% endif %}
  </div>
  <ul class="pageMenu clearfix">
	{{ pageShow }}
	</ul>
</div>
{% endif %}
{{ partial('categorycontent/common') }}

