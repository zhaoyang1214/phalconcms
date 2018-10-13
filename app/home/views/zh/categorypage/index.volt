<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8"/>
<meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=EDGE">
<title>{{ common['title'] }}</title>
<meta name="keywords" content="{{ common['keywords'] }}">
<meta name="description" content="{{ common['description'] }}">
<style>
ul, li {
    list-style: none;
    float: left;
    width: 32px;
}
</style>
</head>
<body>
导航：
{% for value in nav %}
<a href="{{ url('category/' ~ value['urlname']) }}" target="_blank">{{ value['name'] }}</a>>
{% endfor%}
<hr>
<hr>
<hr>
内容：
{{ content }}
<hr>
{{ paginator.show() }}
</body>
</html>