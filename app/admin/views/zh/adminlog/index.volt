<div class="page_function">
  <div class="info">
    <h3>登录记录</h3>
    <small></small> </div>
</div>
<div class="page_main">
  <div class="page_table table_list">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <th width="10%"><center>ID</center></th>
        <th width="50%"><center>登录时间</center></th>
        <th width="40%"><center>登录IP</center></th>
      </tr>
      {% for adminLog in adminLogList %}
      <tr>
        <td><center>{{ loop.index }}</center></td>
        <td><center>{{ adminLog.logintime }}</center></td>
        <td><center>{{ adminLog.ip }}</center></td>
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