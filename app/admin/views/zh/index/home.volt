<div class="page_function">
  <div class="info">
    <h3>管理首页</h3>
    <small><font color="#333">欢迎使用 网站后台管理系统</small> </font></div>
  <div class="tip">{{ adminInfo['username'] }} [{{ adminInfo['nicename'] }}]&nbsp;&nbsp;您当前登录时间为：{{ adminInfo['logintime'] }}&nbsp;&nbsp;登录IP为: {{ adminInfo['ip'] }}</div>
<!--[if lt IE 8]>
<div class="index_tip"><p>您正在使用IE6或IE7为了您更好的体验，请升级浏览器至IE8以上或者更换其他浏览器<a href="http://www.microsoft.com/china/windows/internet-explorer/" target="_blank">Internet Explorer 8</a> 其他浏览器:
<a href="http://www.mozillaonline.com/">Firefox</a> / <a href="http://www.google.com/chrome/?hl=zh-CN">Chrome</a> / <a href="http://www.apple.com.cn/safari/">Safari</a> / <a href="http://www.operachina.com/">Opera</a></p></div>
<![endif]-->
</div>
<div class="page_main">
<h3>基本信息</h3>
  <div class="page_table table_list">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="120">程序版本: </td>
        <td width="250">v{{ config.system.version }}</td>
        <td width="120">phalcon版本: </td>
        <td width="250">v{{ version() }}</td>
      </tr>
      <tr>
        <td width="120">当前语言: </td>
        <td width="250">{{ language.name }}</td>
        <td width="120">当前模板: </td>
        <td>{{ config.system.theme }}</td>
      </tr>
      <tr>
        <td width="120">多国语言: </td>
        <td width="250">
        {% if config.system.language_status %}<font color=green>已开启</font>{% else %}<font color=red>未开启</font>{% endif %}
        </td>
        <td width="120">缓存状态: </td>
        <td>
        {% if config.system.tpl_cache_on %}<font title="已开启" color=green>模板</font>{% else %}<font  title="未开启" color=red>模板</font>{% endif %}
         &nbsp;&nbsp;
         {% if config.system.data_cache_on %}<font title="已开启" color=green>数据</font>{% else %}<font  title="未开启" color=red>数据</font>{% endif %}
        &nbsp;&nbsp;
        {% if config.system.html_cache_on %}<font title="已开启" color=green>静态</font>{% else %}<font  title="未开启" color=red>静态</font>{% endif %}
        </td>
      </tr>
      <tr>
        <td width="120">实用工具: </td>
        <td width="250"><a href="javascript:;" onclick="system()">环境信息</a></td>
        <td width="120">服务器时间: </td>
        <td>{{ date('Y-m-d G:i T') }}</td>
      </tr>
      <tr>
        <td width="120">栏目数: </td>
        <td width="250">{{ categoryCount }}</td>
        <td width="120">内容数: </td>
        <td>{{ categoryContentCount }}</td>
      </tr>
      <tr>
        <td width="120">TAG数: </td>
        <td width="250">{{ tagsCount }}</td>
        <td width="120">附件数: </td>
        <td>{{ uploadCount }}</td>
      </tr>
    </table>
  </div>

  <h3>程序信息</h3>
  <div class="page_table  table_list">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="120">GitHub: </td>
        <td width="250"><a href="https://github.com/zhaoyang1214" target="_blank">https://github.com/zhaoyang1214</a></td>
      </tr>
      <tr>
        <td width="120">QQ: </td>
        <td>1134856531</td>
      </tr>
      <tr>
        <td width="120">开发者: </td>
        <td width="250">赵阳</td>
      </tr>


    </table>
  </div>
</div>
<script>
function system() {
	urldialog({
	width:700,
	height:450,
	title:'环境信息',
	url:'{{ url('Index/toolSystem') }}'
	});
};
</script>