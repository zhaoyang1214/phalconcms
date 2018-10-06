<div class="title"><a href="javascript:void(0)">内容管理</a></div>
	<ul class="load menu">
      	{% for v in authList %}
    	<li><a href="{{ url(v['controller'] ~ '/' ~ v['action']) }}" {% if loop.first %}class="selected"{% endif %}>{{ v['name'] }}</a></li>
    	{% endfor %}
    </ul>
	<ul id="tree" class="ztree load">
	</ul>

<script>
var zTree;
var setting = {
    view: {
        showLine: true,
        selectedMulti: false
    },
    data: {
        simpleData: {
            enable: true,
            idKey: "id",
            pIdKey: "pid",
            rootPId: ""
        }
    },
	callback: {
		onClick: onClick
	}
};
var zNodes = {{ list }};

function onClick(e,treeId, treeNode) {
	var zTree = $.fn.zTree.getZTreeObj("tree");
	if(treeNode.url==null){
	zTree.expandNode(treeNode);
	}
	
}
$(document).ready(function() {
    var t = $("#tree");
    t = $.fn.zTree.init(t, setting, zNodes);
    ajaxload('{{ url('categorycontent/list') }}');
});
</script>    
