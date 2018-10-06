<div class="title"><a href="javascript:void(0)">栏目管理</a></div>
    <ul class="load menu">
      	{% for v in authList %}
	   	<li><a href="{{ url(v['controller'] ~ '/' ~ v['action']) }}" {% if loop.first %}class="selected"{% endif %}>{{ v['name'] }}</a></li>
	   	{% endfor %}
      {% for v in list %}
      <li><a href="{{ url(v['category']~'/add') }}">添加{{ v['name'] }}栏目</a></li>
      {% endfor %}
    </ul>
<script>
url=$(".load li:first a").attr("href");
if(!url||url=='#'){
	ajaxload('{{ url('Error/menuError') }}');
}else{
	ajaxload(url);
}
</script>