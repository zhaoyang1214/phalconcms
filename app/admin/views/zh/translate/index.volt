<div class="page_function">
  <div class="info">
    <h3>翻译管理</h3>
    <small>用于翻译驱动的管理和翻译的修改</small> </div>
  <div class="exercise"> 
   <a href="javascript:menuload('{{ url('Translate/index') }}')">翻译列表</a>
   </div>
   
</div>
<div class="page_main">
<div class="page_menu">
<form action="{{ url('Translate/index') }}">
 搜索：
  <select id="text_type" name="text_type">
  	<option value="1" {% if textType==1 %} selected {% endif %}>原文</option>
    <option value="2" {% if textType==2 or textType is empty %} selected {% endif %} >译文</option>
  </select>
  &nbsp;&nbsp;
  <input style="width:100px" name="search" type="text" class="text_value" id="search" value="{{ search }}" />
  &nbsp;&nbsp;<input type="submit"  class="button_small" value="搜索" />
  </form>
  </div>
  <div class="page_table table_list">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <th width="10%"><center>序号</center></th>
        <th width="40%"><center>原文</center></th>
        <th width="40%"><center>译文</center></th>
        <th width="10%"><center>操作</center></th>
      </tr>
      {% for translate in translateList %}
      <tr>
        <td><center>{{ loop.index }}</center></td>
        <td><center>{% if textType==1 %} {{ str_replace(search, '<font color="red">'~search~'</font>', translate.source_text) }} {% else %} {{ translate.source_text }} {% endif %}</center></td>
        <td><center>{% if textType==2 %} {{ str_replace(search, '<font color="red">'~search~'</font>', translate.translated_text) }} {% else %} {{ translate.translated_text }} {% endif %}</center></td>
        <td><center>
        {% if translateInfoPower %}
        <a href="{{ url('Translate/info/id/')~translate.id }}">查看</a>
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
