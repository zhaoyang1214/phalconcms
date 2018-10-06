<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=EDGE">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>{% if config.system.sitename is defined and config.system.sitename is not empty %}{{ config.system.sitename }} - {% endif %}后台管理系统</title>
<link href="{{ static_url('css/main.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ static_url('css/style_admin.css') }}" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="{{ static_url('js/jquery.js') }}"></script>
<script type="text/javascript" src="{{ static_url('js/duxui.js') }}"></script>
<script type="text/javascript" src="{{ static_url('js/common.js') }}"></script>
<script type="text/javascript" src="{{ static_url('dialog/jquery.artDialog.js?skin=default') }}"></script>
<script type="text/javascript" src="{{ static_url('dialog/plugins/iframeTools.js') }}"></script>
</head>

<body>
<div class="container">
	<div class="login_name">
    	<h3>网站后台管理系统</h3>
    </div>
    <div class="login">
    	<div class="login_box">
            <strong>用户登录</strong>
			<form action="{{ url('admin/login') }}" method="post" id="form">
				<ul class="login_list">
					<li>
						<input id="username" name="username" type="text" placeholder="请输入用户名" class="username t1" tabindex="1">
					</li>
					<li>
						<input id="password" name="password" type="password" placeholder="请输入密码" class="passwd t1" tabindex="2">
					</li>
					<li><input type="submit" value="登录" click_tip="登录中..." class="login_btn" tabindex="3"></li>
				</ul>
			</form>
         </div>
    </div>
</div>

</body>
<script type="text/javascript">
//消息提醒
var msg1 = '{{ flash.output() }}';
var msg2 = '{{ flashSession.output() }}';
if(msg1){
	tip({msg:msg1});
}else if(msg2){
	tip({msg:msg2});
}
$("#user").focus();
saveform({
	success:function(msg){
		if(msg.status==10000){
			tip({msg:msg.data,time:1});
			window.location.href="{{ url('index/index') }}";
		}else{
			tip({msg:msg.message});
		}
	}
});
</script>
</html>
