<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
{{ assets.outputCss() }}

{{ assets.outputJs() }}
<style>
body,html {
	width:700px;
}
</style>
</head>
<body scroll="no">
<div class="page_function">
  <div class="info">
    <h3>环境信息</h3>
    <small>当前服务器环境信息</small> </div>
</div>
<div class="page_main">
  <div class="page_table">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="120">操作系统: </td>
        <td width="150">{{ constant('PHP_OS') }}</td>
        <td width="120">服务器地址: </td>
        <td>{{ request.getServerAddress() }}:{{ request.getPort() }}</td>
      </tr>
      <tr>
        <td>服务器时间: </td>
        <td>{{ date('Y-m-d G:i T') }}</td>
        <td>WEB服务器: </td>
        <td>{{ request.getServer('SERVER_SOFTWARE') }}</td>
      </tr>
      <tr>
        <td>服务器语言: </td>
        <td>{{ request.getServer('HTTP_ACCEPT_LANGUAGE') }}</td>
        <td>PHP版本: </td>
        <td>{{ constant('PHP_VERSION') }}</td>
      </tr>
      <tr>
        <td>图像处理支持: </td>
        <td>{% if function_exists('imageline') %}<font color=green><b>√</b></font>{% else %}<font color=red><b>×</b></font>{% endif %}</td>
        <td>Session支持: </td>
        <td>{% if function_exists('session_start') %}<font color=green><b>√</b></font>{% else %}<font color=red><b>×</b></font>{% endif %}</td>
      </tr>
      <tr>
        <td>脚本运行内存: </td>
        <td>{% set memoryLimit=get_cfg_var('memory_limit') %}{{ memoryLimit? memoryLimit:"无" }}</td>
        <td>上传大小限制: </td>
        <td>{% set uploadMaxFilesize=get_cfg_var('upload_max_filesize') %}{{ uploadMaxFilesize? uploadMaxFilesize:"不允许上传文件" }}</td>
      </tr>
      <tr>
        <td>POST提交限制: </td>
        <td>{{ get_cfg_var('post_max_size') }}</td>
        <td>脚本超时时间: </td>
        <td>{{ get_cfg_var('max_execution_time') }} s</td>
      </tr>
      <tr>
        <td>被屏蔽的函数: </td>
        <td colspan="3">{% set disableFunctions=get_cfg_var('disable_functions') %}{{ disableFunctions? disableFunctions:"无" }}</td>
      </tr>
    </table>
  </div>
</div>

</body>