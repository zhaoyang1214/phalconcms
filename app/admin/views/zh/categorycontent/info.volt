<div class="page_function">
  <div class="info">
    <h3>{{ category.name }}内容{{ actionName }}</h3>
    <small>使用以下功能进行内容{{ actionName }}操作</small> </div>
</div>
<div class="tab" id="tab"> 
<a class="selected" href="#tab1">基本内容</a> 
<a href="#tab2">高级信息</a> 
<a href="#tab3">扩展信息</a> 
<a href="javascript:history.go(-1)">返回上级</a></div>
<div class="page_form">
  <form action="{{ actionUrl }}" method="post" id="form" autocomplete="off">
    <div class="page_table form_table" id="tab1">
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="15%" align="right">栏目</td>
          <td><select name="category_id"  reg="." id="category_id" msg="栏目未选择" onChange="get_fields()" >
              <option value="">======选择栏目======</option>
              {% for value in categoryList %}
              <option value="{{ value['id'] }}" {% if value['type']==1 or value['category_model_id'] != category.category_model_id %}
                  style="background-color:#ccc"  disabled="disabled" {% endif %} {% if category.id is defined and category.id==value['id']%}selected="selected"{% endif %}>
                  {{ value['cname'] }}
              </option>
              {% endfor %}
            </select>
            </td>
          <td  width="25%"></td>
        </tr>
        <tr>
          <td align="right">标题</td>
          <td colspan="2">
          <input name="title" type="text" class="text_value" id="title" style="float:left; width:360px;" value="{% if categoryContent.title is defined %}{{ categoryContent.title }}{% endif %}" reg="\S" msg="标题不能为空" />
            <div class="corol_button"></div>
            <div onclick="fontbold()" class="bold_button"></div>
            <input id="font_color" name="font_color" type="hidden" value="{% if categoryContent.font_color is defined %}{{ categoryContent.font_color }}{% endif %}" />
            <input id="font_bold" name="font_bold" type="hidden" value="{% if categoryContent.font_bold is defined %}{{ categoryContent.font_bold }}{% else %}0{% endif %}" /></td>
        </tr>
        <tr>
          <td align="right">推荐位</td>
          <td>
          	{% set position_array = (categoryContent.position is defined and categoryContent.position is not empty) ? explode(',', categoryContent.position) : [] %}
          	{% for value in positionList %} 
            <input name="position[]" id="position{{ loop.index }}" type="checkbox" value="{{ value.id }}" {% if in_array( value.id,position_array) %}checked="checked"{% endif %} /><label for="position{{ loop.index }}"> {{ value.name }}</label>&nbsp;&nbsp;
          	{% endfor %}
            </td>
          <td></td>
        </tr>
         <tr>
        <td align="right">内容形象图</td>
        <td colspan="2">
        	<input name="image" type="text" class="text_value disabled" readonly="readonly" id="image" value="{% if categoryContent.image is defined %}{{ categoryContent.image }}{% endif %}">
   			&nbsp;&nbsp;<input type="button" id="image_botton" class="button_small" value="选择图片">
       		<script>
			   $(document).ready(function() {
			       $('#image_botton').click(function(){
			           urldialog({
			               title:'单图片上传',
			               url:"{{ url('ueditor/getUpfileHtml/type/image/id/image/origin/2') }}",
			               width:818,
			               height:668
			           });
			       });
			   });
			</script>
          <input type="button" class="button_small" onclick="get_one_pic()" value="提取第一张图" />
        </td>
        </tr>
        <tr>
      <tr>
        <td align="right">内容</td>
        <td colspan="2">
          <script src="{{ static_url('js/ueditor.config.js') }}" type="text/javascript"></script>
			<script src="/plugins/ueditor/ueditor.all.js" type="text/javascript"></script>
			<script src="/plugins/ueditor/lang{% if constant('LANGUAGE') == 'zh' %}/zh-cn/zh-cn.js{% else %}/en/en.js{% endif %}" type="text/javascript"></script>
			<script name="content" id="content" type="text/plain" style="width:100%; height:400px;">{% if categoryContentData.content is defined %}{{ categoryContentData.content|htmlspecialchars_decode }}{% endif %}</script>
			<script type="text/javascript">UE.getEditor("content", {"serverUrl":"{{ url('ueditor/index/origin/2') }}"});</script>
          </td>
      </tr>
      <tr>
          <td align="right">内容来源</td>
          <td><input name="copyfrom" type="text" class="text_value" id="copyfrom" value="{% if categoryContent.copyfrom is defined %}{{ categoryContent.copyfrom }}{% endif %}" /></td>
          <td></td>
      </tr>
      <tr>
        <td align="right">描述</td>
        <td colspan="2"><textarea name="description" class="text_textarea" id="description">{% if categoryContent.description is defined %}{{ categoryContent.description }}{% endif %}</textarea>
          &nbsp;&nbsp;<input type="button" id="" onclick="javascript:get_description()" class="button_small" value="提取描述" />
        </td>
        </tr>
      <tr>
          <td align="right">关键词</td>
          <td colspan="2"><input name="keywords" type="text" class="text_value" id="keywords" value="{% if categoryContent.keywords is defined %}{{ categoryContent.keywords }}{% endif %}" />
            &nbsp;&nbsp;<input type="button" id="" onclick="javascript:get_keywords()" class="button_small" value="提取关键词" />
            &nbsp;&nbsp;<input name="taglink" id="taglink" type="checkbox" value="1" {% if categoryContent.taglink is defined and categoryContent.taglink==1 %}checked="checked"{% endif %} />
            <label for="taglink"> 内容自动链接</label>
          </td>
        </tr>
      
      {% if categorycontentAuditPower %}
      <tr>
        <td align="right">状态</td>
        <td>
        <input name="status" id="status1" type="radio" value="1"  {% if categoryContent.status is defined and categoryContent.status==1 %}checked="checked"{% endif %}  /><label for="status1"> 发布</label>&nbsp;&nbsp;<input name="status" type="radio" id="status0" value="0"  {% if categoryContent.status is not defined or categoryContent.status==0 %}checked="checked"{% endif %}  /><label for="status0"> 草稿</label>&nbsp;&nbsp;
        </td>
        <td></td>
      </tr>
      {% endif %}
      </table>
    </div>
    
    <div class="page_table form_table" id="tab2">
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="15%" align="right">副标题</td>
          <td><input name="subtitle" type="text" class="text_value" id="subtitle" value="{% if categoryContent.subtitle is defined %}{{ categoryContent.subtitle }}{% endif %}" /></td>
          <td width="25%"></td>
        </tr>
        <tr>
          <td align="right">英文URL名称</td>
          <td><input name="urltitle" type="text" class="text_value" id="urltitle" value="{% if categoryContent.urltitle is defined %}{{ categoryContent.urltitle }}{% endif %}" /></td>
          <td></td>
        </tr>
      <tr>
        <td align="right">访问量</td>
        <td>
        <input name="views" type="text" class="text_value" id="views" value="{% if categoryContent.views is defined %}{{ categoryContent.views }}{% else %}0{% endif %}" />
        </td>
        <td>内容浏览量</td>
      </tr>
      <tr>
        <td align="right">顺序</td>
        <td>
        <input name="sequence" type="text" class="text_value" id="sequence" value="{% if categoryContent.sequence is defined %}{{ categoryContent.sequence }}{% else %}0{% endif %}" />
        </td>
        <td>(自定义顺序)</td>
      </tr>
      <tr>
        <td align="right">跳转到</td>
        <td>
        <input name="url" type="text" class="text_value" id="url" value="{% if categoryContent.url is defined %}{{ categoryContent.url }}{% endif %}" />
        </td>
        <td>URL链接，支持标签</td>
      </tr>
      <tr>
        <td align="right">更新时间</td>
        <td>
        <input name="updatetime"  id="updatetime" type="text" class="text_value" style="width:260px; float:left" value="{% if categoryContent.updatetime is defined %}{{ categoryContent.updatetime }}{% else %}{{ date('Y-m-d H:i:s') }}{% endif %}" reg="\S" msg="更新时间不能为空" /><div id="updatetime_button" class="time"></div>
        <script>$('#updatetime_button').calendar({ id:'#updatetime',format:'yyyy-MM-dd HH:mm:ss'});</script>
        </td>
        <td></td>
      </tr>
      <tr>
        <td align="right">内容模板</td>
        <td>
        <input name="tpl" type="text" class="text_value" id="tpl" value="{% if categoryContent.tpl is defined %}{{ categoryContent.tpl }}{% endif %}" />
        </td>
        <td>留空采用栏目指定模板</td>
      </tr>
      </table>
    </div>
    <div class="page_table form_table" id="tab3">
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
      	  <tr>
            <td width="15%" align="right" style="padding:0px; height:0px;"></td>
            <td style="padding:0px; height:0px;"></td>
            <td width="25%" style="padding:0px; height:0px;"></td>
          </tr>
          	{% for expandField in expandFieldList %}
		    	{{ expandField.getFieldHtml(expandData)}}
		    {% elsefor %}
		    <tr><td width="100" align="left">暂无扩展信息</td><td width="350"></td><td></td></tr>
		    {% endfor %}
      </table>
    </div>
    {% if actionPower %}
    <!--普通提交-->
    <div class="form_submit">
      <input name="id" type="hidden" value="{% if categoryContent.id is defined %}{{ categoryContent.id }}{% endif %}" />
      <button type="submit" class="button" click_tip="{{ actionName }}中...">{{ actionName }}</button>
    </div>
    {% endif %}
  </form>
</div>
</div>
<script type="text/javascript">
//tab菜单
$("#tab").idTabs();

//提交表单
savelistform({
	//debug: true,
	addurl:"{{ request.getURI() }}",
	data: function(){
		$('input.editor-input').each(function(){
			var name = $(this).attr("name");
			$(this).val(UE.getEditor('editor-'+name).getContent());
		});
	},
	listurl:"javascript:history.go(-1)",
	name : '{{ jumpButton }}'
});
//TAG
$('#keywords').tagsInput(
{
	'defaultText':'关键词会转为tag'
});
$('.corol_button').soColorPacker({
	textChange:false, 
callback:function(c){
	$('#title').css("color", c.color);
	$('#font_color').val(c.color);
	}
});
//高级模式
function advanced(){
	$('.advanced').toggle();
}
function get_one_pic(){
	var content=UE.getEditor('content').getAllHtml();
	var imgreg = /<img.*?(?:>|\/>)/gi;
	var srcreg = /src=[\'\"]?([^\'\"]*)[\'\"]?/i;
	var arr = content.match(imgreg);
	var src = arr[0].match(srcreg);
	$("#image").val(src[1]);
}

//内容来源列表
function befrom_list(id){
	var list = [ 
	{% set befromlist = categoryModel.befrom is not empty ? explode("\n", categoryModel.befrom) : [] %}
	{% for value in befromlist %}
	{
		href: "javascript:;\" onclick=\"befrom_val('"+id+"','{{ value }}');\"",
		text: "{{ value }}"
	},
	{% endfor %}
	{
		text: "请选择内容来源"
	}];
	return list;
	
}
	
//来源赋值
function befrom_val(id,val){
	$('#'+id).val(val);
	$.powerFloat.hide();
	return false;
}
	
function get_description(){
	var content=UE.getEditor('content').getContent();
	content=content.substring(0,1000);
	content=content.replace(/\s+/g," ")
	content=content.replace(/[\r\n]/g," ");
	content = content.replace(/<\/?[^>]*>/g,'');
	if(content.length > 250){
	    content = content.substring(0,250);
	}
	$("#description").val(content);
}
function get_keywords(){	
	ajaxpost_w({
		url:'{{ url('categorycontent/getKeywords') }}',
		data:{text:$('#title').val()+ ' ' +$('#description').val()},
		tip:2,
		success:function(json){
			var data = json.data;
			var msg = '';
			for(var i=0;i<data.length;i++) {
				if(msg!='') {
					msg += ',';
				}
				msg += data[i].word;
			}
			console.log(msg);
			$('#keywords').importTags(msg);
		},
		failure:function(){
		},
		msg:'关键词获取完毕'
	});
}

function fontbold()
{
	if($('#font_bold').val()==0){
		$('#title').css("font-weight",'bold');
		$('#font_bold').val(1);
		}else{
		$('#title').css("font-weight",'normal');	
		$('#font_bold').val(0);
	}
}

//模板赋值
function tpl_val(id,val){
	$('#'+id).val(val);
	$.powerFloat.hide();
	return false;
}


//页面执行
$(document).ready(function() {
	//模板选择
	/* $("#tpl").powerFloat({
		width: 302,
		eventType: "click",
		edgeAdjust:false,
		target:tpl_list('tpl'),
		targetMode: "list"
	});
	*/
	//来源选择
	$("#copyfrom").powerFloat({
		width: 302,
		eventType: "click",
		edgeAdjust:false,
		target:befrom_list('copyfrom'),
		targetMode: "list"
	}); 
});
</script>
