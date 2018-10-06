$.ajaxSetup ({
    cache: false
});

//绑定顶部ajax菜单
function navload() {
    $('.top_nav a').live("click",
    function() {
		url = $(this).attr("href");
		if (url !== '' && url !== '#') {
        $.get(url, function(result){
           $("#nav").html(result);
        });
		}
	return false;
	});
}


//绑定ajax超链接
function hrftload() {
    $('.load a,.url').live("click",
    function() {
        url = $(this).attr("href");
        if(typeof(url) == 'undefined') {
        	return false;
        }
		len=url.substring(url.length-1,url.length);
        if (len == "" || len == '#') {
            return false;
        }
		ajaxload(url);
        return false;
    });
	$('#nav ul a').live("click",function(){
		$("#nav ul a").removeClass('selected');
		$(this).addClass('selected');
	});
}

function ajaxload(url) {
    main_load(url);
}
//菜单超链接跳转
function menuload(url) {
    window.top.main_load(url);
}
//绑定表格隔行变色
function livetable() {
}


//提交锁屏
function sub_lock(txt = '') {
	if(txt == ''){
		txt = '系统正在处理您的请求，请稍后...';
	}
    //IE6位置
    if (!window.XMLHttpRequest) {
        $("#targetFixed").css("top", $(document).scrollTop() + 2);	
    }
    //创建半透明遮罩层
    if (!$("#overLay").size()) {
        $('<div id="overLay"></div>').prependTo($("body"));
        $("#overLay").css({
            width: "100%",
            backgroundColor: "#000",
            opacity: 0.1,
            position: "absolute",
            left: 0,
            top: 0,
            zIndex: 99
        }).height($(document).height());
    }
    art.dialog.tips(txt,60);
}
//锁屏关闭
function sub_lock_close(txt = '') {
	if(txt == ''){
		txt = '系统已经将您的请求处理完毕！';
	}
	$("#overLay").remove();
    art.dialog.tips(txt,0);
}

$(document).ready(function() {
livetable();	
})

//ajax提交含有确认提示
function ajaxpost(config,url,data,tip,success,failure,cancel){
	if(!config.name){
		var config = {
			name:config,
			url:url,
			data:data,
			tip:tip,
			success:success,
			failure:failure,
			cancel:cancel
		};
	}
	art.dialog.through({
	    content: config.name,
	    lock: true,
	    icon: 'warning',
	    button: [{
			name: '确认操作',
			callback: function() {
			window.top.sub_lock();
			$.ajax({
			type: 'POST',
			url: config.url,
			data: config.data,
			dataType: 'json',
			success: function(json) {
				window.top.sub_lock_close();
				if(config.tip){
				art.dialog.tips(json.message, 3);
				}
				config.success(json);
			}
		});
		},
		focus: true
		},
		{
			name: '取消',
			callback: function() {
				  if(typeof config.cancel == "function"){
					config.cancel();
				}
			}
		}]
	});
	
}

//ajax提交无确认提示
function ajaxpost_w(config,data,tip,success,failure,msg){
	if(!config.url){
		var config = {
			url:config,
			data:data,
			tip:tip,
			success:success,
			failure:failure,
			msg:msg
		};
	}
	//art.dialog.tips(config.start_tip, 3);
	$.ajax({
			type: 'POST',
			url: config.url,
			data: config.data,
			dataType: 'json',
			success: function(json) {
				if(config.tip==1){
					art.dialog.tips(json.message, 3);
				}
				if(config.tip==2&&msg!=''){
					art.dialog.tips(config.msg, 3);
				}
				if(typeof config.success == "function"){
					config.success(json);
				}
			}
	});
}

//弹出窗口
function urldialog(config,url){
	if(!config.title){
		var config = {
			title:config,
			url:url
		};
	}
	if(!config.width){
		config.width=640;
	}
	if(!config.height){
		config.height='100%';
	}
	art.dialog.open(
		config.url,
		{
			title:config.title,
			lock:true,
			width:config.width,
			height:config.height
		}
	);
}

//对话框
function dialog(config){
	art.dialog.through({
		title: config.title,
		content: config.content,
		lock: true,
		button:config.button
	});
}

//tip
function tip(config){
	if(!config.time){
		config.time=3;
	}
	if(!config.msg){
		config.msg='无法处理您的请求！';
	}
	art.dialog.tips(config.msg, config.time);

}


//标准表单保存
function savelistform(config,listurl,data){
	if(!config.addurl){
		var config = {
			addurl:config,
			listurl:listurl,
			data:data
		};
	}
	if(!config.time){
		config.time = 2;
	}
$('#form').mkform(function() {
	if(!config.debug){
		savebutton(0);
	}
	if(typeof config.data == "function"){
		if(config.data() === false) {
			savebutton(1);
			return false;
		}
	}
	setTimeout(function() {
		$('#form').ajaxSubmit({
			dataType: "json",
			type: 'post',
			success: function(json) {
				if(!config.debug){
					savebutton(1);
				}
				if (json.status != 10000) {
					art.dialog.tips(json.message, 3);
				} else {
					art.dialog.through({
						title: '操作成功！',
						content: json.message+' ' + config.time + ' 秒后自动返回列表!',
						lock: true,
						icon: 'succeed',
						button: [{
							name: config.name,
							callback: function() {
								clearInterval(interval);
								window.location.href=config.addurl
							},
							focus: true
						},
						{
							name: '返回列表',
							callback: function() {
								clearInterval(interval);
								window.location.href=config.listurl
							}
						}],
						init: function() {
							var that = this,wait=config.time;
							interval = setInterval(function(){
								--wait;
								if(wait <= 0) {
									clearInterval(interval);
									window.location.href=config.listurl
									return;
								};
								that.content(json.message+ ' ' + wait + ' 秒后自动返回列表!');
							}, 1000);
						}
					});

				}
				
			}
		});
	},
	100);
	return false;
});
}


//表单直接保存
function saveform(config,failure){
	if(!config.success){
		var config = {
			success:config,
			failure:failure
		};
	}
	$('#form').mkform(function(){
		if(!config.debug){
			savebutton(0);
		}
	if(typeof config.data == "function"){
		config.data();
	}
	setTimeout(function() {
	$('#form').ajaxSubmit({
		dataType: "json",
		success: function(json) {
			if(!config.debug){
				savebutton(1);
			}
			config.success(json);
		}
	});
	},
	100);
	return false;
	});
}

//按钮锁定
function savebutton(type,id){
	var type;
	var id;
	if(!id){
		id=":submit";
	}
	txt2 = $(id).attr('click_tip');
	if($(id).is('input')){
		txt=$(id).val();
		$(id).val(txt2);
	}else{
		txt=$(id).text();
		$(id).text(txt2);
	}
	$(id).attr('click_tip', txt);
	if(type==1){
		$(id).removeClass('button_ds');
		$(id).removeAttr("disabled");
	}else{
		$(id).addClass('button_ds');
		$(id).attr("disabled", "disabled");
	}
}
