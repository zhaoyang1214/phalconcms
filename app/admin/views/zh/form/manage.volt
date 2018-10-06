<div class="title"><a href="javascript:void(0)">{{ authName }}管理</a></div>
    <ul class="load menu">
      	{% for v in authList %}
    	<li><a href="{{ url(v['controller'] ~ '/' ~ v['action']) }}" {% if loop.first %}class="selected"{% endif %}>{{ v['name'] }}</a></li>
    	{% endfor %}
    	{% for v in list %}
    	<li id="formdata_id_{{ v.id }}"><a href="{{ url('formdata/index/form_id/')~v.id }}">{{ v.name }}</a></li>
   		{% endfor %}
    </ul>
    
<script>
{% if is_null(origin) %}
url=$(".load li:first a").attr("href");
if(!url||url=='#'){
	ajaxload('{{ url('Error/menuError') }}');
}else{
	ajaxload(url);
}
{% endif %}
</script>