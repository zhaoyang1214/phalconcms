<div class="page_function">
  <div class="info">
    <h3>翻译驱动管理</h3>
    <small>用于翻译驱动添加和修改</small> </div>
  <div class="exercise"> 
   <a href="javascript:menuload('{{ url('TranslateDriver/index') }}')">翻译驱动列表</a>
   {% if translatedriverAddPower %}
   <a href="javascript:menuload('{{ url('TranslateDriver/add') }}')">翻译驱动添加</a>
   {% endif %}
   </div>
   
</div>
<div class="page_main">
  <div class="page_table table_list">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <th width="10%"><center>序号</center></th>
        <th width="20%"><center>驱动名称</center></th>
        <th width="40%"><center>类名</center></th>
        <th width="10%"><center>状态</center></th>
        <th width="20%"><center>操作</center></th>
      </tr>
      {% for translateDriver in translateDriverList %}
      <tr>
        <td><center>{{ translateDriver.id }}</center></td>
        <td><center>{{ translateDriver.name }}</center></td>
        <td><center>{{ translateDriver.class_name }}</center></td>
        <td><center>{{ translateDriver.getStatus(translateDriver.status) }}</center></td>
        <td><center>
        {% if translatedriverInfoPower %}
        <a href="{{ url('translateDriver/info/id/')~translateDriver.id }}">查看</a>
        {% endif %}
        </center></td>
      </tr>
      {% endfor %}
    </table>
  </div>
</div>

