<div class="page_function">
  <div class="info">
    <h3>管理员{{ actionName }}</h3>
    <small>使用以下功能进行管理员{{ actionName }}</small> 
  </div>
</div>
<div class="tab" id="tab"> <a class="selected" href="#">{{ actionName }}管理员</a>
{% if adminIndexPower %}
 <a  href="javascript:menuload('{{ url('Admin/index') }}')">返回管理员列表</a>
 {% endif %}
 </div>
<div class="page_form">
<form action="{{ actionUrl }}" method="post" id="form">
<div class="page_table form_table">
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
  	{% if action=='add' or action=='edit' %}
    <tr>
      <td width="100" align="right">管理组</td>
      <td width="300"><select name="admin_group_id" id="admin_group_id">
        {% for adminGroup in adminGroupList %}
        <option value="{{ adminGroup.id }}" {% if admin.admin_group_id is defined and admin.admin_group_id==adminGroup.id %}selected{% endif %}>{{ adminGroup.name }}</option>
        {% endfor %}
      </select></td>
      <td></td>
    </tr>
    <tr>
      <td width="100" align="right">帐号</td>
      <td width="300"><input name="username" type="text" class="text_value" id="username" value="{% if admin.username is defined %}{{ admin.username }}{% endif %}" reg="\S" msg="管理员帐号不能为空" /></td>
      <td>请输入6-20位数字、字母、 _、@、.</td>
    </tr>
   	{% endif %}
    <tr>
      <td width="100" align="right">昵称</td>
      <td width="300"><input name="nicename" type="text" class="text_value" id="nicename" value="{% if admin.nicename is defined %}{{ admin.nicename }}{% endif %}" reg="\S" msg="管理员昵称不能为空" /></td>
      <td></td>
    </tr>
    {% if action=='add' or action=='editInfo' %}
    <tr>
      <td width="100" align="right">密码</td>
      <td width="300"><input name="password" type="password" class="text_value" id="password" {% if action=='add' %} reg="\S" msg="密码不能为空"{% endif %} /></td>
      <td>{% if action!='add' %}不修改密码请勿填写！{% endif %} 密码为6-20位数字、字母、 _、@、.</td>
    </tr>
    <tr>
      <td width="100" align="right">确认密码</td>
      <td width="300"><input name="password2" type="password" class="text_value" id="password2" {% if action=='add' %} reg="\S" msg="确认密码不能为空"{% endif %} /></td>
      <td></td>
    </tr>
    {% endif %}
    {% if action=='add' or action=='edit' %}
    <tr>
      <td width="100" align="right">状态</td>
      <td width="300"><input name="status" id="status1" type="radio" value="1" {% if admin.status is not defined or admin.status==1 %}checked="checked"{% endif %} />
        <label for="status1">正常</label>&nbsp;&nbsp;
        <input name="status" id="status2" type="radio" value="0" {% if admin.status is defined and admin.status==0 %}checked="checked"{% endif %}/>
       <label for="status2"> 禁用 </label></td>
      <td></td>
    </tr>
    {% endif %}
  </table>
</div>
{% if actionPower %}
<!--普通提交-->
<div class="form_submit">
<input name="id" type="hidden" value="{% if admin.id is defined %}{{ admin.id }}{% endif %}" />
<button type="submit" class="button" click_tip="{{ actionName }}中...">{{ actionName }}</button> 
</div>
{% endif %}
</form>
</div>
</div>
<script type="text/javascript">
//提交表单
savelistform({
	//debug: true,
	addurl:"{{ request.getURI() }}",
	listurl:"{{ url('Admin/index') }}",
	name : '{{ jumpButton }}'
});
</script>