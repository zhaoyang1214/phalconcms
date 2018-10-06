<div class="page_function">
<form action="{{ url('SystemSet/save') }}" id="form" name="form" method="post">
  <div class="info">
    <h3>系统设置</h3>
    <small>设置站点信息与网站的性能等</small> </div>
  </div>
  <div class="tab" id="tab"> <a class="selected" href="#tab1">站点设置</a><a href="#tab2">性能设置</a> <a href="#tab3">模板设置</a> <a href="#tab4">上传设置</a> </div>
  <div class="page_form">
    <div class="page_table form_table" id="tab1">
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="120" style="color:red;">网站名称</td>
          <td width="300"><input name="sitename" type="text" class="text_value" id="sitename" value="{{ config.system.sitename }}" /></td>
          <td>{<span>{</span> config.system.sitename }}</td>
        </tr>
        <tr>
          <td width="120" style="color:red;">网站副标题</td>
          <td width="300"><input name="seoname" type="text" class="text_value" id="seoname" value="{{ config.system.seoname }}" /></td>
          <td>{<span>{</span> config.system.seoname }}</td>
        </tr>
        <tr>
          <td width="120" style="color:red;">网站域名</td>
          <td width="300"><input name="siteurl" type="text" class="text_value" id="siteurl" value="{{ config.system.siteurl }}" /></td>
          <td>{<span>{</span> config.system.siteurl }}</td>
        </tr>
        <tr>
          <td width="120">站点关键词</td>
          <td width="300"><input name="keywords" type="text" class="text_value" id="keywords" value="{{ config.system.keywords }}" /></td>
          <td>{<span>{</span> config.system.keywords }}</td>
        </tr>
        <tr>
          <td width="120">站点描述</td>
          <td width="300"><textarea name="description" class="text_textarea" id="description">{{ config.system.description }}</textarea></td>
          <td>{<span>{</span> config.system.description }}</td>
        </tr>
        <tr>
          <td width="120">站长邮箱</td>
          <td width="300"><input name="masteremail" type="text" class="text_value" id="masteremail" value="{{ config.system.masteremail }}" /></td>
          <td>{<span>{</span> config.system.masteremail }}</td>
        </tr>
        <tr>
          <td width="120">版权信息</td>
          <td width="300"><input name="copyright" type="text" class="text_value" id="copyright" value="{{ config.system.copyright }}" /></td>
          <td>{<span>{</span> config.system.copyright }}</td>
        </tr>
         <tr>
          <td width="120" style="color:red;">备案号</td>
          <td width="300"><input name="beian" type="text" class="text_value" id="beian" value="{{ config.system.beian }}" /></td>
          <td>{<span>{</span> config.system.beian }}</td>
        </tr>
        <tr>
          <td width="120" style="color:red;">客服电话</td>
          <td width="300"><input name="telephone" type="text" class="text_value" id="telephone" value="{{ config.system.telephone }}" /></td>
          <td>{<span>{</span> config.system.telephone }}</td>
        </tr>
         <tr>
          <td width="120">联系人</td>
          <td width="300"><input name="linkman" type="text" class="text_value" id="lxr" value="{{ config.system.linkman }}" /></td>
          <td>{<span>{</span> config.system.linkman }}</td>
        </tr>
        
             <tr>
          <td width="120">传真</td>
          <td width="300"><input name="fax" type="text" class="text_value" id="fax" value="{{ config.system.fax }}" /></td>
          <td>{<span>{</span> config.system.fax }}</td>
        </tr>
         <tr>
          <td width="120">QQ</td>
          <td width="300"><input name="qq" type="text" class="text_value" id="qq"" value="{{ config.system.qq }}" /></td>
          <td>{<span>{</span> config.system.qq }}</td>
        </tr>
         <tr>
          <td width="120" style="color:red;">地址</td>
          <td width="300"><input name="addr" type="text" class="text_value" id="addr" value="{{ config.system.addr }}" /></td>
          <td>{<span>{</span> config.system.addr }}</td>
        </tr>
      </table>
    </div>
    <div class="page_table form_table" id="tab2">
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td>静态缓存：</td>
          <td>
            <input type="radio" name="html_cache_on" id="html_cache_on1" onclick="htmlcache(1)" value="1"   {% if config.system.html_cache_on==1 %}checked="checked"{% endif %}/>
            <label for="html_cache_on1">开启</label>
            <input type="radio" name="html_cache_on" id="html_cache_on2" value="0"  onclick="htmlcache(0)" {% if config.system.html_cache_on==0 %}checked="checked"{% endif %}/>
            <label for="html_cache_on2">关闭</label>
            </td>
          <td> 开启静态缓存后更改模板与设置请先清除缓存 </td>
        </tr>
        <tr class="htmlcache">
          <td width="120">首页更新时间：</td>
          <td width="300"><input name="html_index_cache_time" type="text" class="text_value" value="{{ config.system.html_index_cache_time }}" /></td>
          <td>(单位：秒) 开启智能生成静态后有效</td>
        </tr>
        <tr class="htmlcache">
          <td width="120">其他页更新时间：</td>
          <td width="300"><input name="html_other_cache_time" type="text" class="text_value" value="{{ config.system.html_other_cache_time }}" /></td>
          <td>(单位：秒) 开启智能生成静态后有效</td>
        </tr>
        <tr class="htmlcache">
          <td width="120">搜索更新时间：</td>
          <td width="300"><input name="html_search_cache_time" type="text" class="text_value" value="{{ config.system.html_search_cache_time }}" /></td>
          <td>(单位：秒) 开启智能生成静态后有效</td>
        </tr>
        <tr>
          <td>数据库缓存：</td>
          <td>
            <input type="radio" name="data_cache_on" id="data_cache_on1" value="1" {% if config.system.data_cache_on==1 %}checked="checked"{% endif %}/>
            <label for="data_cache_on1">开启</label>
            <input type="radio" name="data_cache_on" id="data_cache_on2" value="0" {% if config.system.data_cache_on==0 %}checked="checked"{% endif %}/>
            <label for="data_cache_on2">关闭</label>
            </td>
          <td>建议网站上线后开启</td>
        </tr>
        <tr>
          <td>模板缓存：</td>
          <td>
            <input type="radio" name="tpl_cache_on" id="tpl_cache_on1" value="1" {% if config.system.tpl_cache_on==1 %}checked="checked"{% endif %} />
            <label for="tpl_cache_on1">开启</label>
            <input type="radio" name="tpl_cache_on" id="tpl_cache_on2" value="0" {% if config.system.tpl_cache_on==0 %}checked="checked"{% endif %}/>
            <label for="tpl_cache_on2">关闭</label>
            </td>
          <td>更换修改模板请先清除缓存</td>
        </tr>
        {% if constant('LANGUAGE')=='zh' %}
        <tr>
          <td>多国语言：</td>
          <td>
            <input type="radio" name="language_status" id="language_status1" onclick="langopen(1)" value="1" {% if config.system.language_status==1 %}checked="checked"{% endif %}/>
            <label for="language_status1">开启</label>
            <input type="radio" name="language_status" id="language_status2" onclick="langopen(0)" value="0" {% if config.system.language_status==0 %}checked="checked"{% endif %}/>
            <label for="language_status2">关闭</label>
            </td>
          <td> 注意：开启多国语言后允许创建他国语言管理员 </td>
        </tr>
        {% endif %}
        <tr>
          <td>手机版：</td>
          <td>
            <input type="radio" name="mobile_open" id="mobile_open1"  onclick="mobileopen(1)" value="1"   {% if config.system.mobile_open==1 %}checked="checked"{% endif %}/>
            <label for="mobile_open1">开启</label>
            <input type="radio" name="mobile_open" id="mobile_open2" value="0"  onclick="mobileopen(0)" {% if config.system.mobile_open==0 %}checked="checked"{% endif %}/>
            <label for="mobile_open2">关闭</label>
            </td>
          <td> 注意：开启手机版后请自行移动手机模板</td>
        </tr>
        <tr class="mobile" style=" display:none">
          <td width="120">绑定域名</td>
          <td width="300"><input name="mobile_domain" type="text" class="text_value" id="MOBILE_DOMAIN" value="{{ config.system.mobile_domain }}" /></td>
          <td>开启手机版后生效，需绑定二级或者顶级域名，如：sj.duxcms.com</td>
        </tr>
        <tr class="mobile" style=" display:none">
          <td width="120">手机版绑定的模板主题</td>
          <td width="300"><input name="mobile_views" type="text" class="text_value" id="mobile_views" value="{{ config.system.mobile_views }}" /></td>
          <td>开启手机版后生效，需要指定目录，默认为mobile，如果开启了多国语言，多国语言目录会在此目录下</td>
        </tr>
        {% if constant('NOW_ENV') != 'pro' %}
        <tr>
          <td width="120">提示：</td>
          <td width="300"><font color="red">上线后将 config/define.php 中的常量 'NOW_ENV' 设为 'pro' </font></td>
          <td></td>
        </tr>
        {% endif %}
      </table>
    </div>
    <div class="page_table form_table" id="tab3">
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr class="theme" {% if config.system.language_status==1 %}style=" display:none"{% endif %}>
          <td width="120">前台模板主题</td>
          <td width="300"><input name="theme" type="text" class="text_value" value="{{ config.system.theme }}" /></td>
          <td>针对views目录下的文件夹</td>
        </tr>
        <tr>
          <td width="120">首页模板</td>
          <td width="300"><input name="index_tpl" type="text" class="text_value" value="{{ config.system.index_tpl }}" /></td>
          <td>定义首页访问的模板，默认为index/index</td>
        </tr>
        <tr>
          <td width="120">搜索模板</td>
          <td width="300"><input name="search_tpl" type="text" class="text_value" value="{{ config.system.search_tpl }}" /></td>
          <td>定义网站搜索的模板，默认为search/index</td>
        </tr>
        <tr>
          <td width="120">TAG主页模板</td>
          <td width="300"><input name="tags_index_tpl" type="text" class="text_value" value="{{ config.system.tags_index_tpl }}" /></td>
          <td>定义网站TAG集合页的模板，默认为tags/index</td>
        </tr>
        <tr>
          <td width="120">TAG详情页模板</td>
          <td width="300"><input name="tags_info_tpl" type="text" class="text_value" value="{{ config.system.tags_info_tpl }}" /></td>
          <td>定义TAG详情页模板，默认为tags/info</td>
        </tr>
        <tr>
          <td width="120">搜索结果分页数</td>
          <td width="300"><input name="tpl_seach_page" type="text" class="text_value" id="TPL_SEARCH_PAGE" value="{{ config.system.tpl_seach_page }}" /></td>
          <td>针对搜索结果的每页分页数</td>
        </tr>
        <tr>
          <td width="120">TAG主页分页数</td>
          <td width="300"><input name="tpl_tags_index_page" type="text" class="text_value" id="TPL_TAGS_INDEX_PAGE" value="{{ config.system.tpl_tags_index_page }}" /></td>
          <td>TAG集合页每页显示数量</td>
        </tr>
        <tr>
          <td width="120">TAG内容分页数</td>
          <td width="300"><input name="tpl_tags_page" type="text" class="text_value" id="TPL_TAGS_PAGE" value="{{ config.system.tpl_tags_page }}" /></td>
          <td>TAG内容列表每页显示数量</td>
        </tr>
      </table>
    </div>
    <div class="page_table form_table" id="tab4">
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
      	<tr>
          <td width="120">是否允许上传文件：</td>
          <td width="300">
          <input type="radio" name="upload_switch" id="upload_switch1" value="1"   {% if config.system.upload_switch==1 %}checked="checked"{% endif %}/>
            <label for="upload_switch1">开启</label>
            <input type="radio" name="upload_switch" id="upload_switch2" value="0" {% if config.system.upload_switch==0 %}checked="checked"{% endif %}/>
            <label for="upload_switch2">关闭</label>
          <td>后台不受此项影响</td>
        </tr>
        <tr>
          <td width="120">上传大小：</td>
          <td width="300"><input class="text_value" type='text' value="{{ config.system.file_size }}" name="file_size" /></td>
          <td>单位:M</td>
        </tr>
        <tr>
          <td>批量上传数：</td>
          <td><input class="text_value" type='text' value="{{ config.system.file_num }}" name="file_num"/></td>
          <td></td>
        </tr>
        <tr>
          <td>上传图片格式：</td>
          <td><input name="image_type" type='text' class="text_value" value="{{ config.system.image_type }}" /></td>
          <td></td>
        </tr>
        <tr>
          <td>上传视频格式：</td>
          <td><input name="video_type" type='text' class="text_value" value="{{ config.system.video_type }}" /></td>
          <td></td>
        </tr>
        <tr>
          <td>上传文件格式：</td>
          <td><input name="file_type" type='text' class="text_value" value="{{ config.system.file_type }}"  /></td>
          <td></td>
        </tr>
        <tr>
          <td>默认缩图开关：</td>
          <td>
            <input type="radio" name="thumbnail_switch" id="thumbnail_switch1" value="1"   {% if config.system.thumbnail_switch==1 %}checked="checked"{% endif %}/>
            <label for="thumbnail_switch1">开启</label>
            <input type="radio" name="thumbnail_switch" id="thumbnail_switch2" value="0" {% if config.system.thumbnail_switch==0 %}checked="checked"{% endif %}/>
            <label for="thumbnail_switch2">关闭</label>
            </td>
          <td>开关只针对上传时的缩图选项勾选</td>
        </tr>
        <tr>
          <td>默认缩图方式：</td>
          <td>
            <input type="radio" name="thumbnail_cutout" id="thumbnail_cutout1" value="1" {% if config.system.thumbnail_cutout==1 %}checked="checked"{% endif %}/>
            <label for="thumbnail_cutout1">裁剪</label>
            <input type="radio" name="thumbnail_cutout" id="thumbnail_cutout2" value="2" {% if config.system.thumbnail_cutout==2 %}checked="checked"{% endif %}/>
            <label for="thumbnail_cutout2">按比例</label>
            </td>
          <td></td>
        </tr>
        <tr>
          <td>默认缩图尺寸：</td>
          <td>最大宽度
            <input style="width:50px;" class="text_value" type='text' value="{{ config.system.thumbnail_maxwidth }}" name="thumbnail_maxwidth" id="THUMBNAIL_MAXWIDTH" />
            &nbsp;&nbsp;最大高度
            <input style="width:50px;" class="text_value" type='text' value="{{ config.system.thumbnail_maxheight }}" name="thumbnail_maxheight" id="THUMBNAIL_MAXHIGHT" /></td>
          <td>单位:px</td>
        </tr>
        <tr>
          <td>默认水印开关：</td>
          <td>
            <input type="radio" name="watermark_switch" id="watermark_switch1" value="1" {% if config.system.watermark_switch==1 %}checked="checked"{% endif %}/>
            <label for="watermark_switch1">开启</label>
            <input type="radio" name="watermark_switch" id="watermark_switch2" value="0" {% if config.system.watermark_switch==0 %}checked="checked"{% endif %}/>
            <label for="watermark_switch2">关闭</label>
            </td>
          <td>开关只针对上传时的缩图选项勾选</td>
        </tr>
        <tr>
          <td>默认水印位置：</td>
          <td><select name="watermark_place" id="WATERMARK_PLACE">
              <option value="0" {% if config.system.watermark_place==0 %}selected="selected"{% endif %}>随机</option>
              <option value="1" {% if config.system.watermark_place==1 %}selected="selected"{% endif %} >左上</option>
              <option value="2" {% if config.system.watermark_place==2 %}selected="selected"{% endif %} >中上</option>
              <option value="3" {% if config.system.watermark_place==3 %}selected="selected"{% endif %}>右上</option>
              <option value="4" {% if config.system.watermark_place==4 %}selected="selected"{% endif %}>左中</option>
              <option value="5" {% if config.system.watermark_place==5 %}selected="selected"{% endif %}>正中</option>
              <option value="6" {% if config.system.watermark_place==6 %}selected="selected"{% endif %}>右中</option>
              <option value="7" {% if config.system.watermark_place==7 %}selected="selected"{% endif %}>左下</option>
              <option value="8" {% if config.system.watermark_place==8 %}selected="selected"{% endif %}>中下</option>
              <option value="9" {% if config.system.watermark_place==9 %}selected="selected"{% endif %}>右下</option>
            </select></td>
          <td></td>
        </tr>
        <tr>
          <td>水印图片：</td>
          <td>
          <input style="width:195px;" name="watermark_image" type="text" class="text_value disabled" readonly="readonly" id="watermark_image" value="{{ config.system.watermark_image }}">
   			&nbsp;&nbsp;<input type="button" id="watermark_image_botton" class="button_small" value="选择图片">
   			<script>
				   $(document).ready(function() {
				       $('#watermark_image_botton').click(function(){
				           urldialog({
				               title:'上传水印图片',
				               url:"{{ url('ueditor/getUpfileHtml/type/image/id/watermark_image/') }}",
				               width:818,
				               height:668
				           });
				       });
				   });
				</script>
          <td></td>
        </tr>
      </table>
    </div>
  </div>
  {% if systemsetSavePower %}  
  <div class="form_submit">
    <button type="submit" click_tip="保存中..." class="button">保存</button>
  </div>
  {% endif %}
</form>
</div>
<script type="text/javascript">
function langopen(type){
	if(type){
		$('.theme').hide();
	}else{
		$('.theme').show();
	}
}
//表单选项
htmlcache({{ config.system.html_cache_on }});
function htmlcache(type){
	if(type){
		$('.htmlcache').show();
	}else{
		$('.htmlcache').hide();
	}
}
mobileopen({{ config.system.mobile_open }});
function mobileopen(type){
	if(type){
		$('.mobile').show();
	}else{
		$('.mobile').hide();
	}
}
//提交表单
saveform({
	//debug:true,
	success:function(msg){
		if(msg.status==10000){
			tip({msg:msg.data,time:1});
		}else{
			tip({msg:msg.message});
		}
	}
});
//tab菜单
$("#tab").idTabs();
</script>