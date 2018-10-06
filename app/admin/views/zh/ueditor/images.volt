<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="/plugins/webuploader/webuploader.css">
<link rel="stylesheet" type="text/css" href="/plugins/webuploader/upfile/upfilestyle.css">
{{ assets.outputCss() }}

{{ assets.outputJs() }}
<script type="text/javascript" src="/plugins/webuploader/webuploader.min.js"></script>

<style type="text/css">
body, html { }
.page_form { }

.ke-input-text { border: 1px solid #cccccc;  width: 290px; background-color:#fff; box-shadow:1px 1px 3px #f0f0f0 inset;  padding: 4px;line-height:18px; height:17px;
}
.ke-input-number { width: 50px; }
.ke-input-color { border: 1px solid #A0A0A0; background-color: #FFFFFF; font-size: 12px; width: 60px; height: 20px; line-height: 20px; padding-left: 5px; overflow: hidden; cursor: pointer; display: -moz-inline-stack; display: inline-block; vertical-align: middle; zoom: 1;  *display: inline;
}

#uploader .filelist li p.title {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    overflow: hidden;
    white-space: nowrap;
    text-overflow : ellipsis;
    top: 5px;
    text-indent: 5px;
    text-align: left;
}

</style>
</head><body scroll="no">
<div class="page_function">
  <div class="info">
    <h3>批量图片上传</h3>
    <small>  当前可上传文件最大数量为{{ config.system.file_num }}个，单个文件大小为{{ config.system.file_size }}MB</small> </div>
</div>
<div class="page_form">
  <div class="page_table form_table">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td>
          <label for="watermark_switch">添加水印</label>
          <input id="watermark_switch" type="checkbox" name="watermark_switch" {% if config.system.watermark_switch %} checked="checked" {% endif %} value="1">
          &nbsp;&nbsp;
          <label for="watermark_place">水印位置 </label>
          <select id="watermark_place" name="watermark_place">
            <option {% if config.system.watermark_place == 0 %} selected="selected" {% endif %} value="0">随机</option>
            <option {% if config.system.watermark_place == 1 %} selected="selected" {% endif %} value="1">左上</option>
            <option {% if config.system.watermark_place == 2 %} selected="selected" {% endif %} value="2">中上</option>
            <option {% if config.system.watermark_place == 3 %} selected="selected" {% endif %} value="3">右上</option>
            <option {% if config.system.watermark_place == 4 %} selected="selected" {% endif %} value="4">左中</option>
            <option {% if config.system.watermark_place == 5 %} selected="selected" {% endif %} value="5">正中</option>
            <option {% if config.system.watermark_place == 6 %} selected="selected" {% endif %} value="6">右中</option>
            <option {% if config.system.watermark_place == 7 %} selected="selected" {% endif %} value="7">左下</option>
            <option {% if config.system.watermark_place == 8 %} selected="selected" {% endif %} value="8">中下</option>
            <option {% if config.system.watermark_place == 9 %} selected="selected" {% endif %} value="9">右下</option>
          </select>
          &nbsp;&nbsp;
          <label for="thumbnail_switch">是否缩图</label>
          <input id="thumbnail_switch" type="checkbox" name="thumbnail_switch"  {% if config.system.thumbnail_switch %} checked="checked" {% endif %} value="1">
          &nbsp;&nbsp;
          <label for="thumbnail_maxwidth">宽度</label>
          <input id="thumbnail_maxwidth" class="ke-input-text ke-input-number" type="text" maxlength="4" value="{{ config.system.thumbnail_maxwidth }}" name="thumbnail_maxwidth">
          &nbsp;&nbsp;
          <label for="thumbnail_maxheight">高度 </label>
          <input id="thumbnail_maxheight" class="ke-input-text ke-input-number" type="text" maxlength="4" value="{{ config.system.thumbnail_maxheight }}" name="thumbnail_maxheight">
          &nbsp;&nbsp;
          <select id="thumbnail_cutout" name="thumbnail_cutout">
            <option {% if config.system.thumbnail_cutout == 1 %} selected="selected" {% endif %} value="1">裁剪</option>
            <option {% if config.system.thumbnail_cutout == 0 %} selected="selected" {% endif %} value="0">按比例</option>
          </select>
        </td>
      </tr>
	  <tr>
        <td style="height:auto; padding:0px;"><div id="file_list" class="uploadify-queue" style="height: auto; overflow:hidden;"></td>
      </tr>
      <tr>
        <td>
  			<div id="wrapper">
		        <div id="container">
		            <!--头部，相册选择和格式选择-->
		            <div id="uploader">
		                <div class="queueList">
		                    <div id="dndArea" class="placeholder">
		                        <div id="filePicker"></div>
		                        <p>或将图片拖到这里，单次最多可选{{ config.system.file_num }}张</p>
		                    </div>
		                </div>
		                <div class="statusBar" style="display:none;">
		                    <div class="progress">
		                        <span class="text">0%</span>
		                        <span class="percentage"></span>
		                    </div><div class="info"></div>
		                    <div class="btns">
		                        <div id="filePicker2"></div><div class="uploadBtn">开始上传</div>
		                    </div>
		                </div>
		            </div>
		        </div>
		    </div>
		</td>
      </tr>
      
    </table>
  </div>
</div>

<script type="text/javascript">
(function( $ ){
    // 当domReady的时候开始初始化
    $(function() {
        var $wrap = $('#uploader'),

            // 图片容器
            $queue = $( '<ul class="filelist"></ul>' )
                .appendTo( $wrap.find( '.queueList' ) ),

            // 状态栏，包括进度和控制按钮
            $statusBar = $wrap.find( '.statusBar' ),

            // 文件总体选择信息。
            $info = $statusBar.find( '.info' ),

            // 上传按钮
            $upload = $wrap.find( '.uploadBtn' ),

            // 没选择文件之前的内容。
            $placeHolder = $wrap.find( '.placeholder' ),

            $progress = $statusBar.find( '.progress' ).hide(),

            // 添加的文件数量
            fileCount = 0,

            // 添加的文件总大小
            fileSize = 0,

            // 优化retina, 在retina下这个值是2
            ratio = window.devicePixelRatio || 1,

            // 缩略图大小
            thumbnailWidth = 110 * ratio,
            thumbnailHeight = 110 * ratio,

            // 可能有pedding, ready, uploading, confirm, done.
            state = 'pedding',

            // 所有文件的进度信息，key为file id
            percentages = {},
            // 判断浏览器是否支持图片的base64
            isSupportBase64 = ( function() {
                var data = new Image();
                var support = true;
                data.onload = data.onerror = function() {
                    if( this.width != 1 || this.height != 1 ) {
                        support = false;
                    }
                }
                data.src = "data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==";
                return support;
            } )(),

            // 检测是否已经安装flash，检测flash的版本
            flashVersion = ( function() {
                var version;

                try {
                    version = navigator.plugins[ 'Shockwave Flash' ];
                    version = version.description;
                } catch ( ex ) {
                    try {
                        version = new ActiveXObject('ShockwaveFlash.ShockwaveFlash')
                                .GetVariable('$version');
                    } catch ( ex2 ) {
                        version = '0.0';
                    }
                }
                version = version.match( /\d+/g );
                return parseFloat( version[ 0 ] + '.' + version[ 1 ], 10 );
            } )(),

            supportTransition = (function(){
                var s = document.createElement('p').style,
                    r = 'transition' in s ||
                            'WebkitTransition' in s ||
                            'MozTransition' in s ||
                            'msTransition' in s ||
                            'OTransition' in s;
                s = null;
                return r;
            })(),

            // WebUploader实例
            uploader;

        if ( !WebUploader.Uploader.support('flash') && WebUploader.browser.ie ) {

            // flash 安装了但是版本过低。
            if (flashVersion) {
                (function(container) {
                    window['expressinstallcallback'] = function( state ) {
                        switch(state) {
                            case 'Download.Cancelled':
                                alert('您取消了更新！')
                                break;

                            case 'Download.Failed':
                                alert('安装失败')
                                break;

                            default:
                                alert('安装已成功，请刷新！');
                                break;
                        }
                        delete window['expressinstallcallback'];
                    };

                    var swf = '/plugins/webuploader/upfile/expressInstall.swf';
                    // insert flash object
                    var html = '<object type="application/' +
                            'x-shockwave-flash" data="' +  swf + '" ';

                    if (WebUploader.browser.ie) {
                        html += 'classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" ';
                    }

                    html += 'width="100%" height="100%" style="outline:0">'  +
                        '<param name="movie" value="' + swf + '" />' +
                        '<param name="wmode" value="transparent" />' +
                        '<param name="allowscriptaccess" value="always" />' +
                    '</object>';

                    container.html(html);

                })($wrap);

            // 压根就没有安转。
            } else {
                $wrap.html('<a href="http://www.adobe.com/go/getflashplayer" target="_blank" border="0"><img alt="get flash player" src="http://www.adobe.com/macromedia/style_guide/images/160x41_Get_Flash_Player.jpg" /></a>');
            }

            return;
        } else if (!WebUploader.Uploader.support()) {
            alert( 'Web Uploader 不支持您的浏览器！');
            return;
        }

        // 实例化
        uploader = WebUploader.create({
            pick: {
                id: '#filePicker',
                label: '点击选择图片'
            },
            formData: {
            },
            dnd: '#dndArea',
            paste: '#uploader',
            swf: '../Uploader.swf',
            chunked: false,
            chunkSize: 512 * 1024,
            server: '{{ url('ueditor/index/action/uploadimage/origin/')~origin }}',
            // runtimeOrder: 'flash',

            accept: {
                title: 'Images',
                extensions: '{{ config.system.image_type }}',
                mimeTypes: 'image/*'
            },

            // 禁掉全局的拖拽功能。这样不会出现图片拖进页面的时候，把图片打开。
            disableGlobalDnd: true,
            fileNumLimit: {{ config.system.file_num }},
            fileSizeLimit: {{ config.system.file_size }} * 1024 * 1024 * {{ config.system.file_num }},    // 200 M
            fileSingleSizeLimit: {{ config.system.file_size }} * 1024 * 1024    // 50 M
        });

        // 拖拽时不接受 js, txt 文件。
        uploader.on( 'dndAccept', function( items ) {
            var denied = false,
                len = items.length,
                i = 0,
                // 修改js类型
                unAllowed = 'text/plain;application/javascript ';

            for ( ; i < len; i++ ) {
                // 如果在列表里面
                if ( ~unAllowed.indexOf( items[ i ].type ) ) {
                    denied = true;
                    break;
                }
            }

            return !denied;
        });

        uploader.on('dialogOpen', function() {
            console.log('here');
        });

        // uploader.on('filesQueued', function() {
        //     uploader.sort(function( a, b ) {
        //         if ( a.name < b.name )
        //           return -1;
        //         if ( a.name > b.name )
        //           return 1;
        //         return 0;
        //     });
        // });

        // 添加“添加文件”的按钮，
        uploader.addButton({
            id: '#filePicker2',
            label: '继续添加'
        }); 
        
        uploader.stop( true );

        uploader.on('ready', function() {
            window.uploader = uploader;
        });

        // 当有文件添加进来时执行，负责view的创建
        function addFile( file ) {
            var $li = $( '<li id="' + file.id + '">' +
                    '<p class="title">' + file.name + '</p>' +
                    '<p class="imgWrap"></p>'+
                    '<p class="progress"><span></span></p>' +
                    '</li>' ),

                $btns = $('<div class="file-panel">' +
                		'<span class="cancel">删除</span>' +
                        '<span class="rotateRight">向右旋转</span>' +
                        '<span class="rotateLeft">向左旋转</span></div>').appendTo( $li ),
                $prgress = $li.find('p.progress span'),
                $wrap = $li.find( 'p.imgWrap' ),
                $info = $('<p class="error"></p>').hide().appendTo($li),

                showError = function( code ) {
                    switch( code ) {
                        case 'exceed_size':
                            text = '图片大小超出';
                            break;

                        case 'interrupt':
                            text = '上传暂停';
                            break;
                        case 'http':
                            text = 'http请求错误';
                            break;
                        case 'not_allow_type':
                            text = '图片格式不允许';
                            break;
                        default:
                            text = '上传失败，请重试';
                            break;
                    }

                   // $info.text( text ).appendTo( $li );
                    $info.text(text).show();
                };
            if ( file.getStatus() === 'invalid' ) {
                showError( file.statusText );
            } else {
                // @todo lazyload
                $wrap.text( '预览中' );
                if ('|png|jpg|jpeg|bmp|gif|'.indexOf('|'+file.ext.toLowerCase()+'|') == -1) {
                    $wrap.empty().addClass('notimage').append('<i class="file-preview file-type-' + file.ext.toLowerCase() + '"></i>' +
                   '<span class="file-title" title="' + file.name + '">' + file.name + '</span>');
                } else {
                	uploader.makeThumb(file, function (error, src) {
                        if (error || !src) {
                            $wrap.text('不能预览');
                        } else {
                            var $img = $('<img src="' + src + '">');
                            $wrap.empty().append($img);
                            $img.on('error', function () {
                                $wrap.text('不能预览');
                            });
                        }
                    }, thumbnailWidth, thumbnailHeight);
                }

                percentages[ file.id ] = [ file.size, 0 ];
                file.rotation = 0;
            }

            file.on('statuschange', function( cur, prev ) {
                if ( prev === 'progress' ) {
                    $prgress.hide().width(0);
                } else if ( prev === 'queued' ) {
                    $li.off( 'mouseenter mouseleave' );
                    $btns.remove();
                }

                // 成功
                if ( cur === 'error' || cur === 'invalid' ) {
                    console.log( file.statusText );
                    showError( file.statusText );
                    percentages[ file.id ][ 1 ] = 1;
                } else if ( cur === 'interrupt' ) {
                    showError( 'interrupt' );
                } else if ( cur === 'queued' ) {
                    $info.remove();
                    $prgress.css('display', 'block');
                    percentages[ file.id ][ 1 ] = 0;
                } else if ( cur === 'progress' ) {
                    $info.remove();
                    $prgress.css('display', 'block');
                } else if ( cur === 'complete' ) {
                   /*  $prgress.hide().width(0);
                    $li.append( '<span class="success"></span>' ); */
                }

                $li.removeClass( 'state-' + prev ).addClass( 'state-' + cur );
            });

            $li.on( 'mouseenter', function() {
                $btns.stop().animate({height: 30});
            });

            $li.on( 'mouseleave', function() {
                $btns.stop().animate({height: 0});
            });

            $btns.on( 'click', 'span', function() {
                var index = $(this).index(),
                    deg;

                switch ( index ) {
                    case 0:
                        uploader.removeFile( file );
                        return;

                    case 1:
                        file.rotation += 90;
                        break;

                    case 2:
                        file.rotation -= 90;
                        break;
                }

                if ( supportTransition ) {
                    deg = 'rotate(' + file.rotation + 'deg)';
                    $wrap.css({
                        '-webkit-transform': deg,
                        '-mos-transform': deg,
                        '-o-transform': deg,
                        'transform': deg
                    });
                } else {
                    $wrap.css( 'filter', 'progid:DXImageTransform.Microsoft.BasicImage(rotation='+ (~~((file.rotation/90)%4 + 4)%4) +')');
                    // use jquery animate to rotation
                    // $({
                    //     rotation: rotation
                    // }).animate({
                    //     rotation: file.rotation
                    // }, {
                    //     easing: 'linear',
                    //     step: function( now ) {
                    //         now = now * Math.PI / 180;

                    //         var cos = Math.cos( now ),
                    //             sin = Math.sin( now );

                    //         $wrap.css( 'filter', "progid:DXImageTransform.Microsoft.Matrix(M11=" + cos + ",M12=" + (-sin) + ",M21=" + sin + ",M22=" + cos + ",SizingMethod='auto expand')");
                    //     }
                    // });
                }


            });
            //$li.insertBefore($wrap.find('.filePickerBlock'));
            $li.appendTo( $queue );
        }

        // 负责view的销毁
        function removeFile( file ) {
            var $li = $('#'+file.id);

            delete percentages[ file.id ];
            updateTotalProgress();
            $li.off().find('.file-panel').off().end().remove();
        }

        function updateTotalProgress() {
            var loaded = 0,
                total = 0,
                spans = $progress.children(),
                percent;

            $.each( percentages, function( k, v ) {
                total += v[ 0 ];
                loaded += v[ 0 ] * v[ 1 ];
            } );

            percent = total ? loaded / total : 0;


            spans.eq( 0 ).text( Math.round( percent * 100 ) + '%' );
            spans.eq( 1 ).css( 'width', Math.round( percent * 100 ) + '%' );
            updateStatus();
        }
        
        function setState( val ) {
            var file;

            if ( val != state ) {
            	var stats = uploader.getStats();
            	
            	$upload.removeClass( 'state-' + state );
                $upload.addClass( 'state-' + val );

                switch ( val ) {
                    case 'pedding':
                        $placeHolder.removeClass( 'element-invisible' );
                        $queue.hide();
                        $statusBar.addClass( 'element-invisible' );
                        uploader.refresh();
                        break;

                    case 'ready':
                        $placeHolder.addClass( 'element-invisible' );
                        $( '#filePicker2' ).removeClass( 'element-invisible');
                        $queue.show();
                        $statusBar.removeClass('element-invisible');
                        uploader.refresh();
                        break;

                    case 'uploading':
                        $( '#filePicker2' ).addClass( 'element-invisible' );
                        $progress.show();
                        $upload.text( '暂停上传' );
                        break;

                    case 'paused':
                        $progress.show();
                        $upload.text( '继续上传' );
                        break;

                    case 'confirm':
                        $progress.hide();
                        $( '#filePicker2' ).removeClass( 'element-invisible' );
                        //$progress.show(); $info.hide();
                        $upload.text( '开始上传' );

                        stats = uploader.getStats();
                        if ( stats.successNum && !stats.uploadFailNum ) {
                            setState( 'finish' );
                            return;
                        }
                        break;
                    case 'finish':
                        stats = uploader.getStats();
                        if ( stats.successNum ) {
                        	
                        } else {
                            // 没有成功的图片，重设
                            state = 'done';
                            location.reload();
                        }
                        break;
                }
                
                state = val;
                updateStatus();
            }

        }

        function updateStatus() {
            var text = '', stats;
            console.log(state);
            console.log(fileCount);
            stats = uploader.getStats();
            console.log(stats);
            if ( state === 'ready' ) {
                text = '文件大小 ' +
                        WebUploader.formatSize( fileSize ) + '。';
            } else if ( state === 'finish' ) {
                stats = uploader.getStats();
                console.log(stats);
                text = '已成功上传' + stats.successNum+ '张图片';
                if ( stats.uploadFailNum ) {
                	text += '，' + stats.uploadFailNum + '张上传失败，<a class="retry" href="#">重新上传</a>失败图片或<a class="ignore" href="#">忽略</a>';
                }
                if(fileCount == stats.successNum) {
                	art.dialog.close();
                }
            } else {
                stats = uploader.getStats();
                text = '共' + fileCount + '张（' +
                        WebUploader.formatSize( fileSize )  +
                        '），已上传' + stats.successNum + '张';

                if ( stats.uploadFailNum ) {
                    text += '，失败' + stats.uploadFailNum + '张';
                }
            }

            $info.html( text );
        }

        uploader.onUploadProgress = function( file, percentage ) {
            var $li = $('#'+file.id),
                $percent = $li.find('.progress span');

            $percent.css( 'width', percentage * 100 + '%' );
            percentages[ file.id ][ 1 ] = percentage;
            updateTotalProgress();
        };

        uploader.onFileQueued = function( file ) {
            fileCount++;
            fileSize += file.size;

            if ( fileCount === 1 ) {
                $placeHolder.addClass( 'element-invisible' );
                $statusBar.show();
            }

            addFile( file );
            //setState( 'ready' );
            //updateTotalProgress();
        };

        uploader.onFileDequeued = function( file ) {
            fileCount--;
            fileSize -= file.size;

            if ( !fileCount ) {
                setState( 'pedding' );
            }

            removeFile( file );
            updateTotalProgress();

        };
        
        uploader.on('filesQueued', function (file) {
            if (!uploader.isInProgress() && (state == 'pedding' || state == 'finish' || state == 'confirm' || state == 'ready')) {
                setState('ready');
            }
            updateTotalProgress();
        });

        uploader.on( 'all', function( type ) {
            var stats;
            switch( type ) {
                case 'uploadFinished':
                    setState( 'confirm' );
                    break;

                case 'startUpload':
                    setState( 'uploading' );
                    break;

                case 'stopUpload':
                    setState( 'paused' );
                    break;
            }
        });
        
        uploader.on('uploadBeforeSend', function (file, data, header) {
            //这里可以通过data对象添加POST参数
            data['watermark_switch'] = $("#watermark_switch").is(':checked') ? 1 : 0;
            data['watermark_place'] = $("#watermark_place").val();
            data['thumbnail_switch'] = $("#thumbnail_switch").is(':checked') ? 1 : 0;
            data['thumbnail_maxwidth'] = $("#thumbnail_maxwidth").val();
            data['thumbnail_maxheight'] = $("#thumbnail_maxheight").val();
            data['thumbnail_cutout'] = $("#thumbnail_cutout").val();
        });
        
        uploader.on('uploadSuccess', function (file, json) {
            var $file = $('#' + file.id);
            $('<p class="error"></p>').hide().appendTo($file);
            try {
                if (json.state == 'SUCCESS') {
                    $file.append('<span class="success"></span>');
                    var win = art.dialog.open.origin;
                    html="<li>\
							  <div class='pic' id='images_button'>\
								  <img src='" + json.thumbnail_url + "' width='125' height='105' />\
								  <input  id='{{ id }}_url[]' name='{{ id }}_url[]' type='hidden' value='" + json.url + "' />\
								  <input  id='{{ id }}_thumbnail_url[]' name='{{ id }}_thumbnail_url[]' type='hidden' value='" + json.thumbnail_url + "' />\
							  </div>\
							  <div class='title'>标题： <input name='{{ id }}_title[]' type='text' id='{{ id }}_title[]'  value='" + json.title + "' /></div>\
							  <div class='title'>排序： <input id='{{ id }}_order[]' name='{{ id }}_order[]' value='0' type='text' style='width:50px;' /> <a href='javascript:void(0);' onclick='$(this).parent().parent().remove()'>删除</a></div>\
						  </li>";
					$('#{{ id }}_list',win.document).append(html);
                } else {
                    $file.find('.error').text(json.state).show();
                    uploader.request('get-stats').numOfUploadFailed += 1;
                    uploader.request('get-stats').numOfSuccess -= 1;
                }
            } catch (e) {
                $file.find('.error').text('服务器返回出错').show();
                uploader.request('get-stats').numOfUploadFailed += 1;
                uploader.request('get-stats').numOfSuccess -= 1;
            }
        });

        uploader.onError = function( code) {
        	switch(code) {
        		case 'Q_TYPE_DENIED':
        			alert( '只能选择图片' );
        			break;
        		case 'F_EXCEED_SIZE':
        		case 'Q_EXCEED_SIZE_LIMIT':
        			alert( '图片大小超出限制' );
	    			break;
        		case 'Q_EXCEED_NUM_LIMIT':
        			break;
        		case 'F_DUPLICATE':
        			alert( '图片已选择' );
	    			break;
        		default:
        			alert( '图片错误' );
        	}
        };

        $upload.on('click', function() {
            if ( $(this).hasClass( 'disabled' ) ) {
                return false;
            }

            if ( state === 'ready' ) {
                uploader.upload();
            } else if ( state === 'paused' ) {
                uploader.upload();
            } else if ( state === 'uploading' ) {
                uploader.stop();
            }
        });

        $info.on( 'click', '.retry', function() {
            uploader.retry();
        } );

        $info.on( 'click', '.ignore', function() {
        	art.dialog.close();
        } );

        $upload.addClass( 'state-' + state );
        updateTotalProgress();
    });

})( jQuery );

$("#thumbnail_cutout").change(function(){
	var width = 50,height = 50;
	if($(this).val() == 1) {
		width = 210;
		height = 110;
	}
	$("#thumbnail_maxwidth").val(width);
	$("#thumbnail_maxheight").val(height);
});
	</script>
</body>
