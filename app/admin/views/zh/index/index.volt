<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8"/>
<meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=EDGE">
<title>{% if config.system.sitename is not empty %}{{ config.system.sitename }} - {% endif %} 网站后台管理系统</title>
{{ assets.outputCss() }}

{{ assets.outputJs() }}

<link href="{{ static_url('ztree/css/zTreeStyle.css') }}" rel="stylesheet" type="text/css" />
<script src="{{ static_url('ztree/jquery.ztree.js') }}"></script>
<script src="{{ static_url('ztree/jquery.ztree.exhide.js') }}"></script>
<style>
html, body { width: 100%; height: 100%; overflow: hidden; margin: 0; }
html { _height: auto; _padding: 50px 0 0px; }
</style>
</head>
<body>
<!--头部-->
<div id="head">
  <div id="logo"><img src="{{ static_url('images/logo.png') }}" height="50" /></div>
  <div class="top_nav">
    <ul>
    	{% for v in menuList %}
    	<li><a href="{{ url(v['controller'] ~ '/' ~ v['action']) }}">{{ v['name'] }}</a></li>
    	{% endfor %}
    </ul>
  </div>
  <div id="tool"> 欢迎登陆: {{ username }} [{{ nicename }}]&nbsp;&nbsp; 
  <a href="#" onclick="logout()">退出</a>&nbsp;&nbsp;&nbsp;
  <a href="#" id="cache" class="menu">清除缓存</a>&nbsp;&nbsp;&nbsp;
  <a href="{% if config.system.siteurl is defined and config.system.siteurl is not empty %}{{ config.system.siteurl }}{% else %}{{ request.getScheme() }}://{{ request.getHttpHost() }}{% endif %}" target="_blank">网站首页</a> </div>
</div>
<!--左边-->
<div id="nav" class="scroll-pane"></div>

<!--右边-->
<div id="main_right">
  <div class="loading" id="content_loading" style="display:none"></div>
  <iframe id="main" name="main" src="" frameborder="0"></iframe>
</div>
</body>
<script>
var myLayout;
var pane;
$(document).ready(function() {

	//主框架
	function frameheight() {
		var mainheight = $(window).height()-30;
		$('.scroll-pane').height(mainheight);
	};
	frameheight();
	$(window).resize(frameheight);
	//绑定顶级菜单
	 navload();
	 
	//绑定超链接
	hrftload();

	//加载第一页面
	$.get($(".top_nav a:first").attr("href"), function(result){
		$("#nav").html(result);
	}); 
 
	function frameheight() {
	var mainheight = $(window).height()-70;
    $('#nav').height(mainheight);
	};
	frameheight();
	$(window).resize(frameheight); 


	//清除缓存
	$("#cache").powerFloat({
    width: 110,
	eventType: "hover",
    target: [
        {
            href: "javascript:clear('0')",
            text: "清除所有缓存"
        },
        {
            href: "javascript:clear('1')",
            text: "清除模板缓存"
        },
		{
            href: "javascript:clear('2')",
            text: "清除静态缓存"
        },
		{
            href: "javascript:clear('3')",
            text: "清除模型缓存"
        },
		{
            href: "javascript:clear('4')",
            text: "清除数据缓存"
        },
		{
            href: "javascript:clear('5')",
            text: "清除元数据缓存"
        }
    ],
    targetMode: "list"
    });

});
//AJAX访问
function main_load(url){
	$('#content_loading').fadeIn(0);
	$("#main").attr("src", url);
	$("#main").load(function(){
	/*
    first = $("#nav ul a:first").attr("href");
    firstname = $("#nav ul a:first").text();
    url = $(obj).attr("href");
    urlname = $(obj).text();
    html = '<li> <a href="' + first + '">' + firstname + '</a> </li>';
    if (first !== url) {
        html += '<li> <a href="' + url + '">' + urlname + '</a> </li>';
    }
    $('#page_nav ul').html(html);
	*/
    $('#content_loading').fadeOut(1);
	});
}

//退出
function logout(){
	dialog({
		title: '退出确认',
		content: '是否退出网站管理系统？ ',
		button: [{
			name: '退出',
			callback: function() {
				$.ajax({
					type: 'POST',
					url: '{{ url('admin/loginOut') }}',
					data: {
						'out':'true'
					},
					dataType: 'json',
					success: function(json) {
						window.location.href="{{ url('admin/login') }}";
					}

				});

			}
		},
		{
			name: '取消'
		}]

	});
}


//清除缓存
function clear(type){
	var url = '{{ url('index/cleanCache?type=') }}' + type;
	$.get(url, function(json){
		tip({msg:json.data});
		$.powerFloat.hide();
  	},'json');
}

</script>
</html>