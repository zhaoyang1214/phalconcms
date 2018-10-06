<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
{{ assets.outputCss() }}

{{ assets.outputJs() }}

<link rel="stylesheet" type="text/css" href="/plugins/imgcropping/css/cropper.min.css">
<link rel="stylesheet" type="text/css" href="/plugins/imgcropping/css/ImgCropping.css">


<style type="text/css">
body, html { overflow: hidden; }
.page_form { }
</style>
</head>
<body scroll="no">
<div class="page_function">
  <div class="info">
    <h3>单图片上传</h3>
    <small>  单个图片大小为{{ config.system.file_size }}MB</small> </div>
</div>
<div class="page_form">
  <div class="page_table form_table">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
	  <tr>
        <td style="height:auto; padding:0px;"><div id="file_list" class="uploadify-queue" style="height: auto; overflow:hidden;"></td>
      </tr>
      <tr>
        <td>
  			<div class="tailoring-container">
			    <div class="tailoring-content">
			            <div class="tailoring-content-one">
			                <label title="上传图片" for="chooseImg" class="l-btn choose-btn">
			                    <input type="file" accept="image/jpg,image/jpeg,image/png" name="file" id="chooseImg" class="hidden" onchange="selectImg(this)">
			                  	  选择图片
			                </label>
			                <label class="choose-btn tip" >
			                   		 当前裁剪图片大小：<span>0 KB</span>
			                </label>
			            </div>
			            <div class="tailoring-content-two">
			                <div class="tailoring-box-parcel">
			                    <img id="tailoringImg">
			                </div>
			                <div class="preview-box-parcel">
			                    <p>图片预览：</p>
			                    <div class="square previewImg"></div>
			                    <div class="circular previewImg"></div>
			                </div>
			            </div>
			            <div class="tailoring-content-three">
			                <button class="l-btn cropper-reset-btn">复位</button>
			                <button class="l-btn cropper-rotate-btn">旋转</button>
			                <button class="l-btn cropper-scaleX-btn">换向</button>
			                <button class="l-btn sureCut" id="sureCut">确定</button>
			                <span>
			                	宽：
			                <input type="text" name="width" id="width" value="0"/> X 
			                	高：
			                <input type="text" name="height" id="height" value="0"/>
			                <button class="l-btn" id="setWidthHeight">设置剪裁大小</button>
			                </span>
			            </div>
			        </div>
			</div>
		</td>
      </tr>
    </table>
  </div>
</div>
<script src="/plugins/imgcropping/js/cropper.min.js"></script>
<script type="text/javascript">
//弹出框水平垂直居中
(window.onresize = function () {
    var win_height = $(window).height();
    var win_width = $(window).width();
    if (win_width <= 768){
        $(".tailoring-content").css({
            "top": (win_height - $(".tailoring-content").outerHeight())/2,
            "left": 0
        });
    }else{
        $(".tailoring-content").css({
            "top": (win_height - $(".tailoring-content").outerHeight())/2,
            "left": (win_width - $(".tailoring-content").outerWidth())/2
        });
    }
})();

//弹出图片裁剪框
$("#replaceImg").on("click",function () {
    $(".tailoring-container").toggle();
});
//图像上传
function selectImg(file) {
    if (!file.files || !file.files[0]){
        return;
    }
    var reader = new FileReader();
    reader.onload = function (evt) {
        var replaceSrc = evt.target.result;
        //更换cropper的图片
        $('#tailoringImg').cropper('replace', replaceSrc,false);//默认false，适应高度，不失真
    }
    reader.readAsDataURL(file.files[0]);
}
//cropper图片裁剪
 $('#tailoringImg').cropper({
    aspectRatio: 1/1,//默认比例
    preview: '.previewImg',//预览视图
    guides: true,  //裁剪框的虚线(九宫格)
    autoCropArea: 1,  //0-1之间的数值，定义自动剪裁区域的大小，默认0.8
    movable: true, //是否允许移动图片
    dragCrop: true,  //是否允许移除当前的剪裁框，并通过拖动来新建一个剪裁框区域
    movable: true,  //是否允许移动剪裁框
    resizable: true,  //是否允许改变裁剪框的大小
    zoomable: true,  //是否允许缩放图片大小
    mouseWheelZoom: true,  //是否允许通过鼠标滚轮来缩放图片
    touchDragZoom: true,  //是否允许通过触摸移动来缩放图片
    rotatable: true,  //是否允许旋转图片
    crop: function(e) {
        // 输出结果数据裁剪图像。
        $("#width").val(e.width.toFixed(2));
        $("#height").val(e.height.toFixed(2));
        var cas = $('#tailoringImg').cropper('getCroppedCanvas');//获取被裁剪后的canvas
        var base64url = cas.toDataURL('image/png'); //转换为base64地址形式
        var reader = new FileReader();
        reader.onload = function (evt) {  //图片加载完成   
        	var size = parseFloat(evt.total/1024);
        	$(".tip span").text(size.toFixed(2) + ' KB');
        };
        reader.readAsDataURL(dataURLtoBlob(base64url));
    },
    /* cropend: function(e) {
    	var cas = $('#tailoringImg').cropper('getCroppedCanvas');//获取被裁剪后的canvas
        var base64url = cas.toDataURL('image/png'); //转换为base64地址形式
        var reader = new FileReader();
        reader.onload = function (evt) {  //图片加载完成   
        	var size = parseFloat(evt.total/1024);
        	$(".tip span").text(size.toFixed(2) + ' KB');
        };
        reader.readAsDataURL(dataURLtoBlob(base64url));
    }, */
    ready: function(e) {
    	$('#tailoringImg').cropper('setAspectRatio', e.currentTarget.width/e.currentTarget.height);
    	$('#tailoringImg').cropper('setData',{width:e.currentTarget.width,height:e.currentTarget.height});
    }
}); 

$('#setWidthHeight').click(function(){
	var file = $("#chooseImg").get(0);
	if (!file.files || !file.files[0]){
        return;
    }
	var width = parseFloat($("#width").val()).toFixed(2);
	if(width <= 0) {
		width = 1;
	}
	$("#width").val(width);
	var height = parseFloat($("#height").val()).toFixed(2);
	if(height <= 0) {
		height = 1;
	}
	$("#height").val(height);
	$('#tailoringImg').cropper('setAspectRatio', width/height);
	$('#tailoringImg').cropper('setData',{width:parseFloat(width),height:parseFloat(height)});
});
//旋转
$(".cropper-rotate-btn").on("click",function () {
    $('#tailoringImg').cropper("rotate", 45);
});
//复位
$(".cropper-reset-btn").on("click",function () {
    $('#tailoringImg').cropper("reset");
});
//换向
var flagX = true;
$(".cropper-scaleX-btn").on("click",function () {
    if(flagX){
        $('#tailoringImg').cropper("scaleX", -1);
        flagX = false;
    }else{
        $('#tailoringImg').cropper("scaleX", 1);
        flagX = true;
    }
    flagX != flagX;
});

//裁剪后的处理
$("#sureCut").on("click",function () {
    if ($("#tailoringImg").attr("src") == null ){
        return false;
    }else{
        sub_lock('正在上传...');  
        var cas = $('#tailoringImg').cropper('getCroppedCanvas');//获取被裁剪后的canvas
        var base64url = cas.toDataURL('image/png').split(","); //转换为base64地址形式
        $.ajax({
			type:'post',
			url:"{{ url('ueditor/index/origin/'~origin~'/action/uploadscrawl') }}",
			data:{ {{ fieldName }}:base64url[1]},
			dataType: "json",
			async:true,
			success: function(data){
				if(data.state == 'SUCCESS'){
					sub_lock_close('上传完毕');
					var win = art.dialog.open.origin;
                	$('#{{ id }}',win.document).val(data.url);
					art.dialog.close();
				}else{
					sub_lock_close(data.state);
				}
			}
		}); 
    }
});

//关闭裁剪框
function closeTailor() {
    $(".tailoring-container").toggle();
}

function dataURLtoBlob(dataurl) {
    var arr = dataurl.split(','),
    bstr = atob(arr[1]),
    n = bstr.length,
    u8arr = new Uint8Array(n);
    if(arr[0] === 'data:'){
    	return new Blob();
    }
    var mime = arr[0].match(/:(.*?);/)[1];
    while (n--) {
        u8arr[n] = bstr.charCodeAt(n);
    }
    return new Blob([u8arr], {
        type: mime
    });
}

	</script>
</body>
