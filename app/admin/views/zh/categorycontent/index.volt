<div class="page_function">
  <div class="info">
    <h3>{{ category.name }} - 内容管理</h3>
    <small>使用以下功能进行内容操作</small> </div>
  <div class="exercise"> 
   <a href="#">内容列表</a>
   {% if categorycontentAddPower %}
   <a href="{{ url('categorycontent/add/category_id/' ~ category.id) }}">添加内容</a>
   {% endif %}
   </div>
</div>
<div class="page_main">
  <div class="page_menu">
   <form action="{{ url('categorycontent/index/category_id/' ~ category.id) }}">
   	<select id="sequence" name="sequence">
  	<option value="1" {% if sequence==1 %} selected="selected" {% endif %} >更新时间 新->旧</option>
    <option value="2" {% if sequence==2 %} selected="selected" {% endif %} >更新时间 旧->新</option>
    <option value="3" {% if sequence==3 %} selected="selected" {% endif %} >内容ID 大->小</option>
    <option value="4" {% if sequence==4 %} selected="selected" {% endif %} >内容ID 小->大</option>
    <option value="5" {% if sequence==5 %} selected="selected" {% endif %} >添加时间 新->旧</option>
    <option value="6" {% if sequence==6 %} selected="selected" {% endif %} >添加时间 旧->新</option>
    <option value="7" {% if sequence==7 %} selected="selected" {% endif %} >访问次数 多->少</option>
    <option value="8" {% if sequence==8 %} selected="selected" {% endif %} >访问次数 少->多</option>
  </select>
  &nbsp;&nbsp;
  状态：
  <select id="status" name="status">
  	<option value="1" {% if status==1 %} selected="selected" {% endif %} >已发布</option>
    <option value="0" {% if status==0 %} selected="selected" {% endif %} >未发布</option>
  </select>
  &nbsp;&nbsp;
  推荐位：
  <select id="position" name="position">
  	<option value="0">默认</option>
  	{% for value in positionList %} 
  	<option value="{{ value.id }}"  {% if position==value.id %} selected="selected" {% endif %} >{{ value.name }}</option>
    {% endfor %}
  </select>
  &nbsp;&nbsp;
  搜索：
  <input style="width:100px" name="search" type="text" class="text_value" id="search" value="{{ search }}" />
  &nbsp;&nbsp;<input type="submit"  class="button_small"  value="搜索" />
   </form>
  </div>

  <div class="page_table table_list">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
      <th width="40"><center>选择</center></th>
        <th width="40"><center>ID</center></th>
        <th width="">标题</th>
        <th width="40"><center>审核</center></th>
        <th width="50"><center>访问量</center></th>
        <th width="130"><center>更新时间</center></th>
        
        <th width="80"><center>操作</center></th>
      </tr>
      {% for categoryContent in categoryContentList%}
      <tr id="del_{{ categoryContent.id }}">
        <td><center><input name="id[]" type="checkbox" id="id[]" value="{{ categoryContent.id }}" ></td>
        <td><center>{{ categoryContent.id }}</center></td>
        <td><span><a href="/categorycontent/{{ categoryContent.urltitle }}" target="_blank">{{ str_replace(search, '<font color="red">'~search~'</font>', categoryContent.title) }}</a>
        {% if categoryContent.image is not empty %}
        <a href="javascript:void(0);" rel="{{ categoryContent.image }}" class="class_pic">
        <img align="AbsMiddle" src="{{ static_url('images/ico/pic.png') }}" width="14" height="14" alt="" /></a>
        {% endif %}
        </span>
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
		
        <td><center>
        {% if categoryContent.status==1 %}
        <font color=green><b>√</b></font>
        {% endif %}
        {# 这里有个bug，不能用else，应该受上面的双重循环影响 #}
        {% if categoryContent.status==0 %}
        <font color=red><b>×</b></font>
        {% endif %}
        </center></td>
        <td><center>{{ categoryContent.views }}</center></td>
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
  {% if categorycontentAuditPower %}
  <input type="button" onclick="javascript:audit(1);"  class="button_small" value="发布" />
  <input type="button" onclick="javascript:audit(0);" class="button_small" value="草稿" />
  {% endif %}
  {% if categorycontentInfoPower %}
  <input type="button" onclick="javascript:batchDel();" class="button_small" value="删除" />
  {% endif %}
  {% if categorycontentMovePower %}
  <input type="button" onclick="javascript:$('#mobile').toggle();" class="button_small" value="移动" />
  <span id="mobile" style="display:none">
  			<select name="category_id"  id="category_id" >
              <option value="0">======选择栏目======</option>
              {% for value in categoryList %}
              <option value="{{ value['id'] }}" {% if value['type']==1 or value['category_model_id'] != category.category_model_id %}
                  style="background-color:#ccc"  disabled="disabled" {% endif %}>
                  {{ value['cname'] }}
              </option>
              {% endfor %}
            </select>
            <input type="button" onclick="javascript:move();" class="button_small" value="确认" />
  </span>
  {% endif %}
  </div>
  <ul class="pageMenu clearfix">
	{{ pageShow }}
	</ul>
</div>
{% endif %}
{{ partial('categorycontent/common') }}