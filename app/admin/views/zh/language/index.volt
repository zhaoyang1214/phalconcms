<div class="page_function">
  <div class="info">
    <h3>语言管理</h3>
    <small>用于多国语言的添加与修改和删除</small> </div>
  <div class="exercise"> 
   <a href="javascript:menuload('{{ url('language/index') }}')">语言列表</a>
   {% if languageAddPower %}
   <a href="javascript:menuload('{{ url('language/add') }}')">添加语言</a>
   {% endif %}
   </div>
   
</div>
<div class="page_main">
  <div class="page_table table_list">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <th width="7%"><center>ID</center></th>
        <th width="10%"><center>语言名称</center></th>
        <th width="15%"><center>中文名称</center></th>
        <th width="10%"><center>语言标识</center></th>
        <th width="10%"><center>前台主题</center></th>
        <th width="10%"><center>后台主题</center></th>
        <th width="17%"><center>域名</center></th>
        <th width="8%"><center>状态</center></th>
        <th width="13%"><center>操作</center></th>
      </tr>
      {% for language in languageList %}
      <tr>
        <td><center>{{ language.id }}</center></td>
        <td><center>{{ language.name }}</center></td>
        <td><center>{{ language.zh_name }}</center></td>
        <td><center>{{ language.lang }}</center></td>
        <td><center>{{ language.theme }}</center></td>
        <td><center>{{ language.admin_theme }}</center></td>
        <td><center>{{ language.domain }}</center></td>
        <td><center>{{ language.getStatus(language.status) }}</center></td>
        <td><center>{% if languageInfoPower %}<a href="{{ url('language/info/id/')~language.id }}">查看</a>{% endif %}</center></td>
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
