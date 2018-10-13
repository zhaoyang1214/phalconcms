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
这是英文模板
<br>
<br>
<br>
{% for category in model.setModel('Category').categoryGroup(0,3) %}
<a href="{{ url('category/' ~ category['urlname']) }}" target="_blank">{{ category['name'] }}</a><br>
{% for category1 in category['child'] %}
--<a href="{{ url('category/' ~ category1['urlname']) }}" target="_blank">{{ category1['name'] }}</a><br>
{% for category2 in category1['child'] %}
----<a href="{{ url('category/' ~ category2['urlname']) }}" target="_blank">{{ category2['name'] }}</a><br>
{% endfor%}
{% endfor%}
{% endfor%}
</body>
</html>