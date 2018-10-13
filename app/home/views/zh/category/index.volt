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
<a href="{{ url('category/' ~ value['urlname']) }}" target="_blank">{{ value['name'] }}</a>>
{% endfor%}
<hr>
{% for value in list %}
<a href="{{ url('categorycontent/' ~ value.urltitle) }}" target="_blank">{{ value.title }}</a> 地点：{{ value.addr }} 日期：{{ value.mydate }} 位置：{% if value.posi==1 %}左{% else %}右{% endif %}<br>
照片墙：
<pre>
{% set images = json_decode(value.images, true) %}
{{ var_dump(images) }}

{% endfor%}
<hr>
{{ paginator.show() }}
</body>
</html>