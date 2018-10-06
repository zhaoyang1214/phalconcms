<div class="page_function">
  <div class="info">
    <h3>翻译驱动{{ actionName }}</h3>
    <small>使用以下功能进行翻译驱动{{ actionName }}操作</small> 
  </div>
</div>
<div class="tab" id="tab"> <a class="selected" href="#">{{ actionName }}翻译驱动</a> 
{% if translatedriverIndexPower %}
<a  href="javascript:menuload('{{ url('TranslateDriver/index') }}')">返回语言列表</a>
{% endif %}
</div>
<div class="page_form">
<form action="{{ actionUrl }}" method="post" id="form">
<div class="page_table form_table">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      
      <tr>
        <td width="150" align="right">驱动名称</td>
        <td width="300">
        <input type="text" name="name" class="text_value {% if action=='edit' %}disabled{% endif %}" id="name" value="{% if action=='edit' %}{{ translateDriver.name }}{% endif %}" reg="\S" msg="驱动名称不能为空" {% if action=='edit' %}disabled{% endif %} />
        </td>
        <td></td>
      </tr>
       <tr>
        <td width="150" align="right">类名</td>
        <td width="300">
        <input type="text" name="class_name" class="text_value {% if action=='edit' %}disabled{% endif %}" id="class_name" value="{% if action=='edit' %}{{ translateDriver.class_name }}{% endif %}" reg="\S" msg="类名不能为空" {% if action=='edit' %}disabled{% endif %}/>
        </td>
        <td></td>
      </tr>
      {% if action=='edit' %}
      <tr>
          <td width="150" align="right">状态</td>
          <td width="300">
            <input type="radio" name="status" id="status1" value="1"  {% if translateDriver.status==1 %}checked="checked"{% endif %}/>
            <label for="status1">开启</label>
            <input type="radio"  name="status" id="status2" value="0"  {% if translateDriver.status==0 %}checked="checked"{% endif %}/>
            <label for="status2">关闭</label>
          </td>
          <td></td>
        </tr>
      {% for configKey,configName in needSetConfig %}
      <tr>
        <td width="150" align="right">{{ configName }}</td>
        <td width="300">
        <input type="text" name="config[{{ configKey }}]" class="text_value" value="{% if translateDriverConfig[configKey] is defined %}{{ translateDriverConfig[configKey] }}{% endif %}" reg="\S" msg="{{ configName }}不能为空" />
        </td>
        <td></td>
      </tr>
      {% endfor %}
      <tr>
        <td width="150" align="right">该驱动支持的语言</td>
        <td width="300">系统支持的语言</td>
        <td><font color="red">驱动支持的语言要与系统支持的语言一一对应（没有的语言不用选择）</font></td>
      </tr>
      {% for languageKey,languageName in baseLanguage %}
      <tr>
        <td width="150" align="right">{{ languageName }}</td>
        <td width="300">
        <select name="config[map][{{ languageKey }}]">
			<option value="">----暂无----</option>
			{% for language in languageList %}
			<option value="{{ language.lang }}" {% if translateDriverConfig['map'][languageKey] is defined and translateDriverConfig['map'][languageKey]==language.lang %} selected {% endif %}>{{ language.lang }}（{{ language.zh_name }}）</option>
			{% endfor %}
		</select> 
        </td>
        <td></td>
      </tr>
      {% endfor %}
      {% endif %}
    </table>
</div>
{% if actionPower %}
<!--普通提交-->
<div class="form_submit">
{% if action=='edit' %}
<input name="id" type="hidden" value="{{ translateDriver.id }}" />
{% endif %}
<button type="submit" class="button" click_tip="{{ actionName }}中...">{{ actionName }}</button> 
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
		listurl : "{{ url('TranslateDriver/index') }}",
		name : '{{ jumpButton }}'
	});
</script>