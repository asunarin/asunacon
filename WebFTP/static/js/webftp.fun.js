// +----------------------------------------------------------------------
// | Copyright (C) 浩天科技 www.ihotte.com admin@ihotte.com
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author   左手边的回忆 QQ:858908467 E-mail:858908467@qq.com
// +----------------------------------------------------------------------
//**/
//+------------------------------------------------------------------------------
//* 文件$ID ： webftp.fun.js
//+------------------------------------------------------------------------------
//* 路径$ID ： static/js/webftp.fun.js
//+------------------------------------------------------------------------------
//* 程序版本： 浩天 WebFTP V1.0.0 2011-10-01
//+------------------------------------------------------------------------------
//* 功能简介： 系统JS函数库
//+------------------------------------------------------------------------------
//* 注意事项： 请勿私自删除此版权信息！
//+------------------------------------------------------------------------------

function getTotalHeight(){
	if($.browser.msie){
		return document.compatMode == "CSS1Compat"? document.documentElement.clientHeight : document.body.clientHeight;
	}else{
		return self.innerHeight;
	}
}

function getTotalWidth (){
	if($.browser.msie){
		return document.compatMode == "CSS1Compat"? document.documentElement.clientWidth : document.body.clientWidth;
	}else{
		return self.innerWidth;
	}
}


function codeExplorer(){
	asyncbox.open({
　　　	url : './module/codeExplorer/',
　　　	data : '&id=100&name=asyncbox',
　　　	width : getTotalWidth()-18,
　　　	height : getTotalHeight()-18
　});
}

function switchCache(){
	if(1 == cache_main_on){
		cache_main_on = 0;
		$("a[rel='cacheStyle']").html('开启缓存');
	}else if(0 == cache_main_on){
		cache_main_on = 1;
		$("a[rel='cacheStyle']").html('关闭缓存');
	}else{
	
	}
}
function switchStyle(){
	var style = parseInt(list_view_on);
	if(1 == style){
		list_view_on = 0;
		$("a[rel='listStyle']").html('视图风格');
	}else{
		list_view_on = 1;
		$("a[rel='listStyle']").html('列表风格');
	}
	refresh(true);
}
function propertyStyle(){
	var style = parseInt(property_view_on);
	if(1 == style){
		property_view_on = 0;
		$("a[rel='propertyStyle']").html('开启提示');
	}else{
		property_view_on = 1;
		$("a[rel='propertyStyle']").html('关闭提示');
	}
}
function imageStyle(){
	var style = parseInt(img_file_view);
	if(1 == style){
		img_file_view = 0;
		$("a[rel='imageStyle']").html('开启缩略');
	}else{
		img_file_view = 1;
		$("a[rel='imageStyle']").html('关闭缩略');
	}
}

//重置密码
function resetpass(){
	asyncbox.prompt('密码修改-'+window.poweredby, '请输入原始密码:', '','text',function(action,val){
		var value = $.trim(val);
		if(value.length < 4){asyncbox.tips('密码长度不能小于4位','error');return;}
　　	if(action == 'ok'){
			$.ajax({
				type: "POST", url: 'login.php', timeout:10000, error: ajaxErrorMsg,
				data:{action:'resetpass','type':'check','oldpassword':value},
				success:function(data){
					var json = parseJson(data);
					if(200 == json.statusCode){
						asyncbox.prompt('密码修改-'+window.poweredby, '请输入新密码:', '','text',function(action,val){
							var value = $.trim(val);
							if(value.length < 4){asyncbox.tips('密码长度不能小于4位','error');return;}
　　						if(action == 'ok'){
									$.ajax({
										type: "POST", url: 'login.php', timeout:10000, error: ajaxErrorMsg,
										data:{action:'resetpass','type':'update','newpassword':value},
										success:function(data){
											var json = parseJson(data);
											if(200 == json.statusCode){
												asyncbox.alert(json.message,'密码修改-'+window.poweredby);
											}else{
												asyncbox.error(json.message,'密码修改-'+window.poweredby);
											}
									    }
								    });
							}
　						});
					}else{
						asyncbox.error(json.message,'修改密码-'+window.poweredby);
					}
				}
			});

　　　	}
　	});
}

//安全退出
function loginout(){
　  asyncbox.confirm('<font color="red">确定退出WebFTP ?</font>','注销登录-'+window.poweredby,function(action){
　　　  if(action == 'ok'){
　　　　　window.location.href = 'login.php?action=out';return;
　　　  }
　  });
}

function get_ext_class(ext){
	var ext  = ext || 'unknown';
	var type = 'unknown';
	var type_arr = parseJson(type_conf) || {};
	$.each(type_arr, function(idx, item){
		for(var id in item){
			if(ext == item[id]){
				type = idx;
				break;
			}
		}
	});
	return type;
}
function get_language(ext){
	var ext  = ext || 'unknown';
	var language = 'unknown';
	var edit_conf_arr = parseJson(edit_conf) || {};
	$.each(edit_conf_arr.EDIT_ALLOW_TYPE, function(idx, item){
		for(var id in item){
			if(ext == item[id]){
				language = idx;
				break;
			}
		}
	});
	return language;
}

//解析JSON字符串成JSON对象
function parseJson(str){
 var str = str || '{}';
 if($.isPlainObject(str)){return str}
 return eval('('+str+')') || {};
}

//Error Msg
function ajaxErrorMsg(xhr, textStatus, thrownError){
    var $option = this;
    var msg = '';
	waitmeoff();
	if('timeout' == textStatus){
	    msg += '<div>Http status: AJAX请求超时</div>';
	}else if('error' == textStatus){
	    msg += '<div>Http status:  '+xhr.status+' '+xhr.statusText+'</div>';
		msg += '<div>Http readyState:  '+xhr.readyState+'</div>';
        msg += '<div>thrownError:  '+thrownError+'</div>';
        msg += '<div>responseText: '+xhr.responseText+'</div>';
	}else{
	    msg += xhr;
	}
    asyncbox.error(msg, '请求出错-'+window.poweredby);
}

//显示文件目录 List
function display(json){
	if(1 == parseInt(list_view_on)){
		display2(json);return false;//大图预览模式
	}
    var html_dirs  = '';
	var html_files = '';
	var files_show  = '';
	if(200 != json.statusCode){
		var message = json.message || '加载出错,如开启本地缓存,浏览器需支持Flash';
		asyncbox.error(message,window.poweredby);
		return false;
	}
    pathmenu(json.path);//生成路径菜单
	//返回上级目录
	  html_dirs += '<tr class="">';
      html_dirs += '  <td><input class="dir-disabled" name="dir-disabled" type="checkbox" value="" disabled /></td>';
      html_dirs += '  <td><span class="ext ext_folder_go"></span></td>';
      html_dirs += '  <td><a href="javascript:opendir(\'' + json.path.parent + '\');" class="js-slide-to">返回上级目录</a></td>';
      //html_dirs += '  <td><span class="ext ext_folder_go"></span><a href="javascript:del(3,{});" class="js-slide-to">删除</a></td>';
      //html_dirs += '  <td><span class="ext ext_folder_go"></span><a href="javascript:cut(3,{});" class="js-slide-to">剪切</a></td>';
	  //html_dirs += '  <td><span class="ext ext_folder_go"></span><a href="javascript:paste(3,{});" class="js-slide-to">粘贴</a></td>';
	  html_dirs += '  <td>修改时间</td>';
	  html_dirs += '  <td>文件大小</td>';
	  html_dirs += '  <td>文件权限</td>';
      html_dirs += '</tr>';
	//文件夹列表
	if( 0 <json.dirs.length){
	  $.each(json.dirs,function(idx,item){
	    html_dirs += '<tr class="">';
        html_dirs += '  <td><input class="dir-checkbox-id" name="dir-checkbox" type="checkbox" value="'+json.path.current + item.name+'" /></td>';
        html_dirs += '  <td><span class="ext ext_folder_open"></span></td>';
        html_dirs += '  <td><a href="javascript:opendir(\'' + json.path.current + item.name + '\')" id="dir-id-'+ idx+'" dirname="'+ item.name +'" dirpath="'+ json.path.current +'" chmod="'+item.chmod+'">' + item.name + '</a></td>';
        html_dirs += '  <td>' + item.mtime + '</td>';
        html_dirs += '  <td>' + item.size + '</td>';
		html_dirs += '  <td>' + item.chmod + '</td>';
        html_dirs += '</tr>';
      });
	}
	//文件列表
	if( 0 <json.files.length){
	 $.each(json.files,function(idx,item){
        switch(item.ext){
		    case 'jpg': files_show = 'rel="show" colortitle="图片大小：<font color=red>'+item.size+'</font>  图片文件名: <font color=red>'+item.name+'</font>"';break;
			case 'jpeg':files_show = 'rel="show" colortitle="图片大小：<font color=red>'+item.size+'</font>  图片文件名: <font color=red>'+item.name+'</font>"';break;
			case 'gif': files_show = 'rel="show" colortitle="图片大小：<font color=red>'+item.size+'</font>  图片文件名: <font color=red>'+item.name+'</font>"';break;
			case 'png': files_show = 'rel="show" colortitle="图片大小：<font color=red>'+item.size+'</font>  图片文件名: <font color=red>'+item.name+'</font>"';break;
			case 'bmp': files_show = 'rel="show" colortitle="图片大小：<font color=red>'+item.size+'</font>  图片文件名: <font color=red>'+item.name+'</font>"';break;
			default:    files_show = '';
		}
	    html_dirs += '<tr class="">';
        html_dirs += '  <td><input class="file-checkbox-id" name="file-checkbox" type="checkbox" value="'+json.path.current + item.name+'" /></td>';
        html_dirs += '  <td><span class="ext ext_' + get_ext_class(item.ext) + '"></span></td>';
        html_dirs += '  <td><a target="_blank" href="'+ json.path.current + item.name + '" id="file-id-'+ idx+'" filename="'+ item.name +'" filepath="'+ json.path.current +'" fileext="'+ item.ext +'" '+files_show+'  chmod="'+item.chmod+'">' + item.name + '</a></td>';
        html_dirs += '  <td>' + item.mtime + '</td>';
        html_dirs += '  <td>' + item.size + '</td>';
		html_dirs += '  <td>' + item.chmod + '</td>';
        html_dirs += '</tr>';
      });
	}

	/*************************** 数据写入 ***************************************/
	$('#list_main_center').html('')
	var apptools = $('#apptools').html();
	var table = '';
	table += '<table class="tree-browser" cellpadding="0" cellspacing="0">';
    table += '<!--<thead>';
    table += '  <tr class="header">';
    table += '    <th width="32" class="dir-file-check" ><a title="全部选中" href="javascript:selectAll();">全选</a></th>';
    table += '    <th width="18" class="dir-file-ico">.</th>';
    table += '    <th width="auto" class="dir-file-name" style="overflow:hidden;">文件名</th>';
    table += '    <th width="180" class="dir-file-time">修改时间</th>';
    table += '   <th width="80" class="dir-file-size">文件大小</th>';
    table += '    <th width="80" class="dir-file-chmod">文件权限</th>';
	table += '  </tr>';
	table += '</thead>-->';
	table += '<tbody id="dirs-files-list">';
	table += '    <th><font color="green">文件列表加载中...</font></th>';
	table += '</tbody>';
	table += '</table>';
	$('#list_main_center').html(apptools + table);
	$("tbody[id='dirs-files-list']").html(html_dirs + html_files);

	refresh();
}
//显示文件目录 List
function display2(json){
	if(200 != json.statusCode){
		var message = json.message || '加载出错,如开启本地缓存,浏览器需支持Flash';
		asyncbox.error(message,window.poweredby);
		return false;
	}
    pathmenu(json.path);//生成路径菜单

	var files_show  = '';
	var view_body = '';
	//返回上级目录
	view_body += '<div>';
	view_body += '   <ol class="f_icon rounded">';
	view_body += '     <a href="javascript:opendir(\'' + json.path.parent + '\')">';
	view_body += '			<div class="ico_big ext_big_upto"></div>';
	view_body += '     </a>';
	view_body += '   </ol>';
	view_body += '   <ol class="f_name"><font color="green">返回上级目录</font></ol>';
	view_body += '</div>';

	//文件夹列表
	if( 0 <json.dirs.length){
		$.each(json.dirs,function(idx,item){
			var fmTitle = '';
			fmTitle += '目录名称: '+ item.name + '<br />';
			fmTitle += '目录大小: 暂未提供<br />';
			fmTitle += '目录权限: '+ item.chmod + '<br />';
			fmTitle += '修改时间: '+ item.mtime + '<br />';

			view_body += '<div style="position:relative;left:0px;top:0px;">';
			view_body += '   <ol class="f_icon rounded">';
			view_body += '     <a href="javascript:opendir(\'' + json.path.current + item.name + '\')" id="dir-id-'+ idx+'" dirname="'+ item.name +'" dirpath="'+ json.path.current +'" chmod="'+item.chmod+'">';
			view_body += '			<div content="'+ fmTitle +'"  class="fmTitle ico_big ext_big_dir"></div>';
			view_body += '     </a>';
			view_body += '   </ol>';
			view_body += '   <ol class="f_name"><font color="blue">'+ item.name +'</font></ol>';
			view_body += '   <span style="position:absolute;left:10px;top:85px;"><input class="dir-checkbox-id" name="dir-checkbox" type="checkbox" value="'+json.path.current + item.name+'" /></span>';
			view_body += '</div>';
		});
	}
	//文件列表
	if( 0 <json.files.length){
	 $.each(json.files,function(idx,item){
        switch(item.ext){
		    case 'jpg': files_show = 'rel="show" colortitle="图片大小：<font color=red>'+item.size+'</font>  图片文件名: <font color=red>'+item.name+'</font>"';break;
			case 'jpeg':files_show = 'rel="show" colortitle="图片大小：<font color=red>'+item.size+'</font>  图片文件名: <font color=red>'+item.name+'</font>"';break;
			case 'gif': files_show = 'rel="show" colortitle="图片大小：<font color=red>'+item.size+'</font>  图片文件名: <font color=red>'+item.name+'</font>"';break;
			case 'png': files_show = 'rel="show" colortitle="图片大小：<font color=red>'+item.size+'</font>  图片文件名: <font color=red>'+item.name+'</font>"';break;
			case 'bmp': files_show = 'rel="show" colortitle="图片大小：<font color=red>'+item.size+'</font>  图片文件名: <font color=red>'+item.name+'</font>"';break;
			default:    files_show = '';
		}
		var fmTitle = '';
		fmTitle += '文件名称: '+ item.name + '<br />';
		fmTitle += '文件大小: '+ item.size + '<br />';
		fmTitle += '文件权限: '+ item.chmod + '<br />';
		fmTitle += '文件类型: '+ item.ext + '<br />';
		fmTitle += '修改时间: '+ item.mtime + '<br />';

		view_body += '<div style="position:relative;left:0px;top:0px;">';
		view_body += '   <ol class="f_icon rounded">';
		view_body += '     <a target="_blank" href="'+ json.path.current + item.name + '" id="file-id-'+ idx+'" filename="'+ item.name +'" filepath="'+ json.path.current +'" fileext="'+ item.ext +'" '+files_show+'  chmod="'+item.chmod+'">';
		view_body += '			<div content="'+ fmTitle +'"  class="fmTitle ico_big ext_big_'+ get_ext_class(item.ext) +'"></div>';
		view_body += '     </a>';		
		view_body += '   </ol>';	
		view_body += '   <ol class="f_name"><font color="red">'+ item.name +'</font></ol>';
		view_body += '   <span style="position:absolute;left:10px;top:85px;"><input class="file-checkbox-id" name="file-checkbox" type="checkbox" value="'+json.path.current + item.name+'" /></span>';
		view_body += '</div>';
      });
	}
	var view_head   = '<table id="view-dirs-files-list"><tr><td class="rhumbnail">';
	var view_foot   = '</td></tr></table>';

	//写入列表数据
	var apptools = $('#apptools').html();
	$('#list_main_center').html( apptools + view_head + view_body + view_foot );

	//图片预览
	if(img_file_view){
		$('#list_main_center div').each(function(idx){
			var $show = $('ol:first', $(this)).find("a[rel='show']");
			var $file = $show.attr('filepath')+$show.attr('filename');
			//var $file = $show.attr('href');
			$show.find('div').css('background-image', 'url("do.php?action=imageview&file='+ encodeURIComponent($file)+'")');
		});
	}
	refresh();
}

//路径菜单
function pathmenu(path){
	var path = path || {};
	window.root_path    = path.root;
	window.parent_path  = path.parent;
	window.current_path = path.current;
	var path_str = current_path.replace(window.root_path, '');
	var path_arr = path_str.split('/');
	var menu = '<span id="list_head_left"></span><span id="list_head_center">';
	menu += '当前目录：<a href="javascript:opendir(root_path);">根目录/</a>';
	var current_mulu = root_path;
	if (current_mulu.charAt(current_mulu.length-1) != "/"){current_mulu+="/";}
	$.each( path_arr, function(idx, mulu){
		if(mulu){
			current_mulu += mulu+'/';
			menu += '<a href="javascript:opendir(\''+ current_mulu +'\');">' + mulu + '/</a>';
		}
	});
	menu += '</span><span id="list_head_right">';
	$('#list_head').html(menu);
	refresh();
}

function waitme(){
	$('#loading').css('display','block');
	return true;
}
function waitmeoff(){
	$('#loading').css('display','none');
	return true;
}
//界面刷新
function refresh(reload){
    var reload = reload || false;
	if(reload){
		opendir(current_path,{reload:true});
	}else{
		initUI();
		waitmeoff();
	}
}

//文件全选
function selectAll(){
	$(":input[name='dir-checkbox']").each(function(){
		$(this).attr('checked', !$(this).attr('checked'));
    });
	$(":input[name='file-checkbox']").each(function(){
		$(this).attr('checked', !$(this).attr('checked'));
    });
}

//文件全选
function selectCheck(){
    var data = new Array();
	$(":input[name='dir-checkbox']").each(function(){
           if($(this).attr("checked")==true){
             data.push($(this).attr("value"));
           }
    });
	$(":input[name='file-checkbox']").each(function(){
           if($(this).attr("checked")==true){
             data.push($(this).attr("value"));
           }
    });
    return data;
}



function gohome(){
    asyncbox.open({
      url : 'http://www.ihotte.com',title :'浩天官网',
      width : 1000,height : 500,
      tipsbar : {
          title : '浩天官网',
          content : '浩天官网...'
      },
      btnsbar : $.btn.OKCANCEL
    });
}
function gobbs(){
    asyncbox.open({
      url : 'http://bbs.ihotte.com',title :'浩天官方论坛',
      width : 1000,height : 500,
      tipsbar : {
          title : '浩天论坛',
          content : '浩天论坛...'
      },
      btnsbar : $.btn.OKCANCEL
    });
}
