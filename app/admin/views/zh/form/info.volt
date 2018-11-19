<div class="page_function">
  <div class="info">
    <h3>表单{{ actionName }}</h3>
    <small>使用以下功能进行表单{{ actionName }}操作</small>  
  </div>
</div>
<div class="tab" id="tab"> <a class="selected" href="#">{{ actionName }}表单</a> 
{% if formIndexPower %}
<a  href="javascript:menuload('{{ url('form/index') }}')">返回表单列表</a>
{% endif %}
</div>
<div class="page_form">
<form action="{{ actionUrl }}" method="post" id="form">
<div class="page_table form_table">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      
      <tr>
        <td width="100" align="right">表单名称</td>
        <td width="300">
        <input name="name" type="text" class="text_value  {% if action=='edit' %}disabled{% endif %}" id="name" value="{% if form.name is defined %}{{ form.name }}{% endif %}" reg="\S" msg="表单名称不能为空"  {% if action=='edit' %}disabled{% endif %} />
        </td>
        <td></td>
      </tr>
      <tr>
        <td width="100" align="right">表名</td>
        <td>
        <input name="table" type="text" class="text_value  {% if action=='edit' %}disabled{% endif %}" id="table" value="{% if form.table is defined %}{{ form.table }}{% endif %}" reg="[a-zA-Z0-9_]" msg="表单名只能为英文数字和下划线"  {% if action=='edit' %}disabled{% endif %}/>
        </td>
        <td>数据表名</td>
      </tr>
      <tr>
        <td width="100" align="right">表单顺序</td>
        <td>
        <input name="sequence" type="text" class="text_value" id="sequence" value="{% if form.sequence is defined %}{{ form.sequence }}{% else %}0{% endif %}"/>
        </td>
        <td>数字越小越靠前</td>
      </tr>
      <tr>
        <td width="100" align="right">内容排序</td>
        <td>
        <input name="sort" type="text" class="text_value" id="sort" value="{% if form.sort is defined %}{{ form.sort }}{% else %}id DESC{% endif %}" reg="\S" />
        </td>
        <td>会自动创建自增主键id</td>
      </tr>
      
      <tr>
        <td width="100" align="right">前台表单</td>
        <td>
        
        <input name="display" id="display1" type="radio" onclick="reception(1)" value="1" {% if form.display is not defined or form.display==1 %}checked="checked"{% endif %}/>
        <label for="display1">&nbsp;是</label>
        &nbsp;&nbsp;
        <input name="display" id="display0" type="radio" onclick="reception(0)" value="0"  {% if form.display is defined and form.display!=1 %}checked="checked"{% endif %}/>
        <label for="display0">&nbsp;否</label>
        </td>
        <td>是否在前台显示此表单的分页列表内容</td>
      </tr>

      <tr class="reception">
        <td width="100" align="right">前台提交返回</td>
        <td>
        <input name="return_type" id="return_type0" type="radio" value="0" {% if form.return_type is not defined or form.return_type==0 %}checked="checked"{% endif %} />
        <label for="return_type0">&nbsp;JS消息框</label>
        &nbsp;&nbsp;
        <input name="return_type" id="return_type1" type="radio" value="1" {% if form.return_type is defined and form.return_type!=0 %}checked="checked"{% endif %}/>
        <label for="return_type1">&nbsp;JSON</label>
        </td>
        <td>前台表单提交后的动作</td>
      </tr>

      <tr class="reception">
        <td width="100" align="right">提交成功后消息</td>
        <td>
        <input name="return_msg" type="text" class="text_value" id="return_msg" value="{% if form.return_msg is defined %}{{ form.return_msg }}{% else %}提交成功{% endif %}" reg="\S" msg="提交成功消息不能为空" />
        </td>
        <td>表单提交成功后返回的消息</td>
      </tr>
      
      <tr class="reception">
        <td width="100" align="right">成功后返回地址</td>
        <td>
        <input name="return_url" type="text" class="text_value" id="return_url" value="{% if form.return_url is defined %}{{ form.return_url }}{% endif %}"  />
        </td>
        <td>表单提交成功后返回的消息(留空返回当前表单)</td>
      </tr>
      
      <tr class="reception">
        <td width="100" align="right">前台分页数</td>
        <td>
        <input name="page" type="text" class="text_value" id="page" value="{% if form.page is defined %}{{ form.page }}{% else %}10{% endif %}" reg="[0-9]" msg="分页数只能为数字" />
        </td>
        <td>前台列表显示的分页数</td>
      </tr>
      
      <tr class="reception">
        <td width="100" align="right">前台列表条件</td>
        <td>
        <input name="where" type="text" class="text_value" id="where" value="{% if form.where is defined %}{{ form.where }}{% endif %}" />
        </td>
        <td></td>
      </tr>
      
      <tr class="reception">
        <td width="100" align="right">独立模板</td>
        <td>
        <input name="alone_tpl" id="alone_tpl1" type="radio" value="1" {% if form.alone_tpl is not defined or form.alone_tpl==1 %}checked="checked"{% endif %} />
        <label for="alone_tpl1">&nbsp;是</label>
        &nbsp;&nbsp;
        <input name="alone_tpl" id="alone_tpl0" type="radio" value="0" {% if form.alone_tpl is defined and form.alone_tpl!=1 %}checked="checked"{% endif %}/>
        <label for="alone_tpl0">&nbsp;否</label>
        </td>
        <td>否的话外部调用默认模板(from/index)</td>
      </tr>
      <tr class="reception">
        <td width="100" align="right">前台模板</td>
        <td>
        <input name="tpl" type="text" class="text_value" id="tpl" value="{% if form.tpl is defined %}{{ form.tpl }}{% endif %}" />
        </td>
        <td></td>
      </tr>

	<tr class="reception">
        <td width="100" align="right">使用图片验证码</td>
        <td>
        <input name="is_captcha" id="is_captcha1" type="radio" value="1" {% if form.is_captcha is not defined or form.is_captcha==1 %}checked="checked"{% endif %} />
        <label for="is_captcha1">&nbsp;是</label>
        &nbsp;&nbsp;
        <input name="is_captcha" id="is_captcha0" type="radio" value="0" {% if form.is_captcha is defined and form.is_captcha!=1 %}checked="checked"{% endif %}/>
        <label for="is_captcha0">&nbsp;否</label>
        </td>
        <td></td>
      </tr>
     
    </table>
</div>
{% if actionPower %}
<!--普通提交-->
<div class="form_submit">
<input name="id" type="hidden" value="{% if form.id is defined %}{{ form.id }}{% endif %}" />
<button type="submit" class="button" click_tip="{{ actionName }}中...">{{ actionName }}</button> 
</div>
{% endif %}
</form>
</div>
</div>
<script type="text/javascript">
reception({% if form.display is defined %}{{ form.display }}{% else %}1{% endif %});
function reception(type){
	if(type){
		$('.reception').show();
	}else{
		$('.reception').hide();
	}
}
savelistform({
	//debug: true,
	addurl:"{{ request.getURI() }}",
	listurl:"{{ url('form/index/origin/1') }}",
	name : '{{ jumpButton }}'
});
</script>