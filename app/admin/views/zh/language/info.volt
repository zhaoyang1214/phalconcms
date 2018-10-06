<div class="page_function">
  <div class="info">
    <h3>语言{{ actionName }}</h3>
    <small>使用以下功能进行语言{{ actionName }}操作</small> 
  </div>
</div>
<div class="tab" id="tab"> <a class="selected" href="#">{{ actionName }}语言</a> 
{% if languageIndexPower %}
<a  href="javascript:menuload('{{ url('Language/index') }}')">返回语言列表</a>
{% endif %}
</div>
<div class="page_form">
<form action="{{ actionUrl }}" method="post" id="form">
<div class="page_table form_table">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      
      <tr>
        <td width="100" align="right">语言名称</td>
        <td width="300">
        <input name="name" type="text" class="text_value" id="name" value="{% if language.name is defined %}{{ language.name }}{% endif %}" reg="\S" msg="语言名称不能为空" />
        </td>
        <td></td>
      </tr>
      <tr>
        <td width="100" align="right">中文名称</td>
        <td width="300">
        <input name="zh_name" type="text" class="text_value" id="zh_name" value="{% if language.zh_name is defined %}{{ language.zh_name }}{% endif %}" reg="\S" msg="中文名称不能为空" />
        </td>
        <td></td>
      </tr>
      <tr>
        <td width="100" align="right">语言标识</td>
        <td>
        <input name="lang" type="text" class="text_value {% if action=='edit' %}disabled{% endif %}" id="lang" value="{% if language.lang is defined %}{{ language.lang }}{% endif %}" reg="\S" msg="语言标识不能为空" {% if action=='edit' %}disabled{% endif %}/>
        </td>
        <td>最好根据浏览器的“Accept-Language”来设置</td>
      </tr>
      <tr>
        <td width="100" align="right">前台模板主题</td>
        <td>
        <input name="theme" type="text" class="text_value" id="theme" value="{% if language.theme is defined %}{{ language.theme }}{% endif %}" reg="\S" msg="前台模板主题不能为空" />
        </td>
        <td></td>
      </tr>
      <tr>
        <td width="100" align="right">后台模板主题</td>
        <td>
        <input name="admin_theme" type="text" class="text_value" id="admin_theme" value="{% if language.admin_theme is defined %}{{ language.admin_theme }}{% endif %}" reg="\S" msg="后台模板主题不能为空" />
        </td>
        <td></td>
      </tr>
      <tr>
        <td width="100" align="right">域名</td>
        <td>
        <input name="domain" type="text" class="text_value" id="domain" value="{% if language.domain is defined %}{{ language.domain }}{% endif %}"/>
        </td>
        <td>不包含“http://”或“https://”</td>
      </tr>
      {% if action=='edit' %}
      <tr>
        <td width="100" align="right">状态</td>
        <td>
        <input type="radio" name="status" id="status1" value="1" {% if language.status is defined and language.status==1%} checked {% endif %} />
        <label for="status1">开启</label>
        <input type="radio"  name="status" id="status2" value="0" {% if language.status is not defined or language.status!=1 %} checked {% endif %}/>
        <label for="status2">关闭</label>
        </td>
        <td></td>
      </tr>
      {% endif %}
    </table>
</div>
{% if actionPower %}
<!--普通提交-->
<div class="form_submit">
{% if action=='edit' %}
<input name="id" type="hidden" value="{{ language.id }}" />
{% endif %}
<button type="submit" click_tip="{{ actionName }}中..." class="button">{{ actionName }}</button> 
</div>
{% endif %}
</form>
</div>
</div>
<script type="text/javascript">
//提交表单
	savelistform({
		//debug : true,
		addurl : "{{ request.getURI() }}",
		listurl : "{{ url('Language/index') }}",
		name : '{{ jumpButton }}'
	});
</script>