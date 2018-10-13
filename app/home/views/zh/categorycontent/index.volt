<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8"/>
<meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=EDGE">
<title>{{ common['title'] }}</title>
<meta name="keywords" content="{{ common['keywords'] }}">
<meta name="description" content="{{ common['description'] }}">
<style>
html, body { width: 100%; height: 100%; overflow: hidden; margin: 0; }
html { _height: auto; _padding: 50px 0 0px; }
</style>
</head>
<body>
导航：
{% for value in nav %}
<a href="{{ url('category/' ~ value['urlname']) }}" target="_blank">{{ value['name'] }}</a><br>>
{% endfor%}
<hr>
标题：{{ categoryContent.title }}
<hr>
内容：
{{ content }}
<hr>
地点：{{ expandData.addr }} 
日期：{{ expandData.mydate }} 
位置：{% if expandData.posi==1 %}左{% else %}右{% endif %}<br>
照片墙：
<pre>
{% set images = json_decode(expandData.images, true) %}
{{ var_dump(images) }}

{{ paginator.show() }}
<hr>
上一篇：{% if  prevCategoryContent===false %}没有上一篇{% else %}<a href="{{ url('categorycontent/' ~ prevCategoryContent.urltitle) }}">{{ prevCategoryContent.title }}</a>{% endif %}

下一篇：{% if  nextCategoryContent===false %}没有下一篇{% else %}<a href="{{ url('categorycontent/' ~ nextCategoryContent.urltitle) }}">{{ nextCategoryContent.title }}</a>{% endif %}
<hr>
</body>
</html>