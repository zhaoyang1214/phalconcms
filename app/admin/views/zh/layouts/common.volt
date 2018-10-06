<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=EDGE">
<title>{% if config.system.sitename is not empty %}{{ config.system.sitename }} - {% endif %} 网站管理系统</title>
{{ assets.outputCss() }}

{{ assets.outputJs() }}
</head>
<body class="iframe">
{{ content() }}
<div class="fn_clear"></div>
  <div id="runtime">
  当前脚本运行时间：  {{ runtime }} 秒
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
</script>
</html>