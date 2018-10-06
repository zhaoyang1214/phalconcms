<script>
//快速编辑
$('tr').hover(
	function () {
		$(this).find('.quickeditor').show();
	},
	function () {
		$(this).find('.quickeditor').hide();
	}
);
function quickeditor(url){
	urldialog({
	title:'快速编辑',
	url:url,
	width:550,
	height:570
	});
}
//选择
function selectall(name){   
    $("[name='"+name+"']").each(function(){//反选   
    if($(this).attr("checked")){   
          $(this).removeAttr("checked");   
    }else{   
          $(this).attr("checked",'true');   
    }   
    })  
}
//批量操作
function audit(status){
	var str="";
	/* $("[name='id[]']").each(function(){
    if($(this).attr("checked")){
		  str+=$(this).val()+","; 
    }
    }) */
    $("[name='id[]']:checked").each(function(){
    	str+=$(this).val()+","; 
    })
    if(str == "") {
    	art.dialog.tips('请选择', 3);
    	return;
    }
	
	ajaxpost({
		name:'您确认要继续进行操作吗？操作将无法撤销！',
		url:"{{ url('categorycontent/audit') }}",
		data:{status: status, id:str},
		tip:1,
		success:function(json){
			if(json.status==10000) {
				window.location.reload();
			}
		}
	});
}
//栏目形象图
$(".class_pic").powerFloat({
    targetMode: "ajax"
});
//删除
function del(id,obj, url) {
	var obj;
	ajaxpost({
		name:'确认要删除本内容吗?删除无法恢复！',
		url:url,
		data:{id: id},
		tip:1,
		success:function(json){
			if(json.status==10000) {
				$(obj).parent('tr').remove();
			}
		}
	});
}
function batchDel(){
	var str="";
	var obj = $("[name='id[]']:checked");
	obj.each(function(){
    	str+=$(this).val()+","; 
    })
    if(str == "") {
    	art.dialog.tips('请选择要删除的选项', 3);
    	return;
    }
	
	ajaxpost({
		name:'确认要删除选中的内容吗?删除无法恢复！',
		url:"{{ url('categorycontent/delete') }}",
		data:{id:str},
		tip:1,
		success:function(json){
			if(json.status==10000) {
				obj.parents('tr').remove();
			}
		}
	});
}

function move(){
	var str="";
	var obj = $("[name='id[]']:checked");
	obj.each(function(){
    	str+=$(this).val()+","; 
    })
    if(str == "") {
    	art.dialog.tips('请选择要移动的选项', 3);
    	return;
    }
	var category_id = $('#category_id').val();
	if(category_id == 0) {
		art.dialog.tips('请选择栏目', 3);
    	return;
	}
	ajaxpost({
		name:'确认要移动选中的内容吗?',
		url:"{{ url('categorycontent/move') }}",
		data:{id:str, category_id:category_id},
		tip:1,
		success:function(json){
			if(json.status==10000) {
				window.location.reload();
			}
		}
	});
}
</script>