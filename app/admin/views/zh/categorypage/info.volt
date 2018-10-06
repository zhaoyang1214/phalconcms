<div class="page_function">
  <div class="info">
    <h3>{{ actionName }}单页面</h3>
    <small>使用以下功能进行{{ actionName }}单页面操作</small> 
  </div>
</div>
<div class="tab" id="tab"> 
<a class="selected" href="#">{{ actionName }}单页面</a> 
{% if categoryIndexPower %}
<a  href="javascript:menuload('{{ url('category/index') }}')">返回列表</a>
{% endif %}
</div>
<div class="page_form">
<form action="{{ actionUrl }}" method="post" id="form">
<div class="page_table form_table">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="100" align="right">上级栏目</td>
        <td width="300">
          <select name="pid" id="pid">
            <option value="0">=====顶级栏目=====</option>
            {% for value in categoryList %}
            <option {% if category.pid is defined and category.pid==value['id']%}selected="selected"{% endif %} value="{{ value['id'] }}">{{ value['cname'] }}</option>
            {% endfor %}
            </select>
            &nbsp;&nbsp;<a href="javascript:;" onclick="advanced()">高级设置</a>
          </td>
        <td></td>
      </tr>
      <tr>
        <td width="100" align="right">单页面名称</td>
        <td width="300">
          <input name="name" type="text" class="text_value" id="name" value="{% if category.name is defined %}{{ category.name }}{% endif %}" reg="\S" msg="单页面名称不能为空" />
          </td>
        <td></td>
      </tr>
      <tr class="advanced">
        <td width="100" align="right">副名称</td>
        <td width="300">
        <input name="subname" type="text" class="text_value" id="subname" value="{% if category.subname is defined %}{{ category.subname }}{% endif %}" />
        </td>
        <td></td>
      </tr>
      <tr class="advanced">
        <td width="100" align="right">单页面URL名称</td>
        <td width="300">
        <input name="urlname" type="text" class="text_value" id="urlname" value="{% if category.urlname is defined %}{{ category.urlname }}{% endif %}" />
        </td>
        <td></td>
      </tr>
      <tr>
        <td width="100" align="right">单页面形象图</td>
        <td colspan="2">        
          <input name="image" type="text" class="text_value disabled" readonly="readonly" id="image" value="{% if category.image is defined %}{{ category.image }}{% endif %}">
   			&nbsp;&nbsp;<input type="button" id="image_botton" class="button_small" value="选择图片">
       		<script>
			   $(document).ready(function() {
			       $('#image_botton').click(function(){
			           urldialog({
			               title:'单图片上传',
			               url:"{{ url('ueditor/getUpfileHtml/type/image/id/image/origin/1') }}",
			               width:818,
			               height:668
			           });
			       });
			   });
			</script>
        </td>
        </tr>
      
      <tr>
        <td width="100" align="right">内容<br><br><br>使用"[page]"分页</td>
        <td colspan="2">
        	<script src="{{ static_url('js/ueditor.config.js') }}" type="text/javascript"></script>
			<script src="/plugins/ueditor/ueditor.all.js" type="text/javascript"></script>
			<script src="/plugins/ueditor/lang{% if constant('LANGUAGE') == 'zh' %}/zh-cn/zh-cn.js{% else %}/en/en.js{% endif %}" type="text/javascript"></script>
			<script name="content" id="content" type="text/plain" style="width:100%; height:400px;">{% if categoryPage.content is defined %}{{ categoryPage.content|htmlspecialchars_decode }}{% endif %}</script>
			<script type="text/javascript">UE.getEditor("content", {"serverUrl":"{{ url('ueditor/index/origin/1') }}"});</script>
        </td>
        </tr>
      <tr class="advanced">
        <td width="100" align="right">关键词</td>
        <td width="300">
        <input name="keywords" type="text" class="text_value" id="keywords" value="{% if category.keywords is defined %}{{ category.keywords }}{% endif %}" />
        </td>
        <td>以,号分割</td>
      </tr>
      <tr class="advanced">
        <td width="100" align="right">描述</td>
        <td width="300"><textarea name="description" class="text_textarea" id="description">{% if category.description is defined %}{{ category.description }}{% endif %}</textarea>
        </td>
        <td>对本单页面的简单介绍</td>
      </tr>
      <tr class="advanced">
        <td width="100" align="right">SEO内容</td>
        <td><textarea name="seo_content" class="text_textarea" id="seo_content">{% if category.seo_content is defined %}{{ category.seo_content|htmlspecialchars_decode }}{% endif %}</textarea>
        </td>
        <td>meta标签</td>
      </tr>
      <tr class="advanced">
        <td width="100" align="right">单页面显示</td>
        <td width="300">
          	<input id="is_show1" name="is_show" type="radio" value="1" {% if category.is_show is not defined or category.is_show==1 %}checked="checked"{% endif %} />
	        <label for="is_show1">&nbsp;&nbsp;显示</label>
	        &nbsp;&nbsp;
	        <input id="is_show0" name="is_show" type="radio" value="0" {% if category.is_show is defined and category.is_show==0 %}checked="checked"{% endif %} />
	        <label for="is_show0">&nbsp;&nbsp;隐藏</label>
          </td>
        <td>控制单页面调用的显示与隐藏</td>
      </tr>
      <tr>
        <td width="100" align="right">栏目顺序</td>
        <td width="300">
        <input name="sequence" type="text" class="text_value" id="sequence" value="{% if category.sequence is defined %}{{ category.sequence }}{% else %}0{% endif %}" />
        </td>
        <td>越小越排在前面</td>
      </tr>
      <tr>
        <td width="100" align="right">单页面模板</td>
        <td width="300">
        <input name="category_tpl" type="text" class="text_value" id="category_tpl" value="{% if category.category_tpl is defined %}{{ category.category_tpl }}{% else %}categorypage/index{% endif %}"  />
        </td>
        <td>用于频道或列表的显示</td>
      </tr>
    </table>
</div>
{% if actionPower %}
<!--普通提交-->
<div class="form_submit">
<input name="id" type="hidden" value="{% if category.id is defined %}{{ category.id }}{% endif %}" />
<button type="submit" class="button" click_tip="{{ actionName }}中...">{{ actionName }}</button> 
</div>
{% endif %}
</form>
</div>
</div>
{{ partial('category/info') }}