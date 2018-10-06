<div class="title"><a href="javascript:void(0)">{{ authName }}管理</a></div>
    <ul class="load menu">
    	{% for v in authList %}
    	<li><a href="{{ url(v['controller'] ~ '/' ~ v['action']) }}" {% if loop.index==1 %}class="selected"{% endif %}>{{ v['name'] }}</a></li>
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