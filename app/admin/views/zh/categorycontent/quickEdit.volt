<style>
html,body { overflow:hidden;}
</style>
<div class="page_function">
  <div class="info">
    <h3>快速编辑</h3> <small>快速编辑一些常用内容</small> </div>
</div>
<div class="page_form">
  <form action="{{ url('categorycontent/quickEdit') }}" method="post" id="form" autocomplete="off">
    <div class="page_table form_table">
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="100" align="right">标题</td>
          <td width="100%"><input name="title" type="text" class="text_value" id="title" value="{{ categoryContent.title }}" reg="\S" msg="标题不能为空" /></td>
        </tr>
        <tr>
          <td width="100" align="right">推荐位</td>
          <td>
          	{% set position_array = (categoryContent.position is defined and categoryContent.position is not empty) ? explode(',', categoryContent.position) : [] %}
          	{% for value in positionList %} 
            <input name="position[]" id="position{{ loop.index }}" type="checkbox" value="{{ value.id }}" {% if in_array( value.id,position_array) %}checked="checked"{% endif %} /><label for="position{{ loop.index }}"> {{ value.name }}</label>&nbsp;&nbsp;
          	{% endfor %}
            </td>
        </tr>
        <tr>
          <td width="100" align="right">英文URL名称</td>
          <td><input name="urltitle" type="text" class="text_value" id="urltitle" value="{{ categoryContent.urltitle }}" /></td>
        </tr>
        <tr>
        <td width="100" align="right">描述</td>
        <td><textarea name="description" class="text_textarea" id="description">{{ categoryContent.description }}</textarea>
        </td>
        </tr>
      <tr>
          <td width="100" align="right">关键词</td>
          <td><input name="keywords" type="text" class="text_value" id="keywords" value="{{ categoryContent.keywords }}" />
          </td>
        </tr>
      <tr>
        <td width="100" align="right">更新时间</td>
        <td>
          <span style=" float:left"><input name="updatetime"  id="updatetime" type="text" class="text_value" style="width:250px;" value="{{ date('Y-m-d H:i:s') }}" reg="\S" msg="更新时间不能为空" /></span><div id="updatetime_button" class="time" style="float:left"></div>
          <script>$('#updatetime_button').calendar({ id:'#updatetime',format:'yyyy-MM-dd HH:mm:ss'});</script>
        </td>
        </tr>
      <tbody id="expand">
      </tbody>
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
    
    <!--普通提交-->
    <div class="form_submit">
      <input name="id" type="hidden" value="{{ categoryContent.id }}" />
      <button type="submit" class="button">保存</button>
    </div>
    
  </form>
</div>
</div>
<script type="text/javascript">
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
var win = art.dialog.open.origin;
//提交表单
saveform({success:function(msg){
	if(msg.status==10000){
		win.location.reload();
	    art.dialog.close();
	}else{
		tip({msg:msg.message});
	}
}});
</script>