// +----------------------------------------------------------------------
// | Copyright (C) 浩天科技 www.ihotte.com admin@ihotte.com
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author   左手边的回忆 QQ:858908467 E-mail:858908467@qq.com
// +----------------------------------------------------------------------
//**/
//+------------------------------------------------------------------------------
//* 文件$ID ： webftp.ajax.js
//+------------------------------------------------------------------------------
//* 路径$ID ： static/js/webftp.ajax.js
//+------------------------------------------------------------------------------
//* 程序版本： 浩天 WebFTP V1.0.0 2011-10-01
//+------------------------------------------------------------------------------
//* 功能简介： AJAX通信API接口
//+------------------------------------------------------------------------------
//* 注意事项： 请勿私自删除此版权信息！
//+------------------------------------------------------------------------------

//获取目录属性
function getproperty(){
    waitme();
    $.ajax({
       type: "POST",url: 'do.php', timeout:60000,data: 'action=property&path=' + window.current_path,
	   error: ajaxErrorMsg,
       success: function(data){
	       var json = parseJson(data);
		   asyncbox.alert(json.message,'目录属性-'+window.poweredby);
		   waitmeoff();
	   }
    });   
}

//文件搜索
function search(){
	asyncbox.tips('Sorry, 暂不支持！','error');return;
}

//新建文件、目录
function newbuild(type,option){
	var type   = type || 0;
    var option = option || {};
	var szMsg  = '[\\/:*?"\'<>|：？“’《》]';
    if('file' == type){
	    var path = window.current_path;
		if(path.charAt(path.length-1) != "/"){path+="/";}	    
　		asyncbox.prompt('新建文件-'+window.poweredby, '请输入新文件名（<font color="red">不能含非法字符</font>）:', 'newfile.php','text',function(action,val){
　　		if(action == 'ok'){
				for(i=1;i<szMsg.length+1;i++){
					if(val.indexOf(szMsg.substring(i-1,i))>-1){
						asyncbox.tips('请勿输入非法字符如:'+szMsg,'error');return;
					}
				}
				if('' == val){
					asyncbox.tips('文件名不能留空','error');return;
				}else{
					option.path    = path;
					option.type = 'file';
					option.name = val;
					newbuild('do',option);
				}			
　　　		}
　		});
	}else if('dir' == type){
		var path = window.current_path;
		if(path.charAt(path.length-1) != "/"){path+="/";}	    
　		asyncbox.prompt('新建目录-'+window.poweredby, '请输入新目录名（<font color="red">不能含非法字符</font>）:', 'newdir', 'text',function(action,val){
　　		if(action == 'ok'){
				for(i=1;i<szMsg.length+1;i++){
					if(val.indexOf(szMsg.substring(i-1,i))>-1){
						asyncbox.tips('请勿输入非法字符如:'+szMsg,'error');return;
					}
				}
				if('' == val){
					asyncbox.tips('目录名不能留空','error');return;
				}else{
					option.path = path;
					option.type = 'dir';
					option.name = val;
					newbuild('do',option);
				}
　　　		}
　		});
    }else if('do' == type ){
	    if('' == $.trim(option.name)){
			asyncbox.tips('目录名不能留空','error');return;
		}		
		waitme();
		$.ajax({
            type: "POST", url: 'do.php', timeout:10000, error: ajaxErrorMsg,
			data:{'action':'newbuild', 'path':option.path, 'type':option.type, 'name':option.name},				
            success:function(data){
	            var json = parseJson(data);
	            if(200 == json.statusCode){					    
					asyncbox.alert(json.message,'新建文件目录-'+window.poweredby);
	            }else{					    
	                asyncbox.error(json.message,'新建文件目录-'+window.poweredby);
	            }
				refresh(true);
	        }	
		});
    }else{
	   asyncbox.tips('Sorry,未知操作！','error');return;
	}
}

//重命名搜索
function rename(type,option){
	var type   = type || 0;
    var option = option || {};
	var szMsg  = '[\\/:*?"\'<>|：？“’《》]';
    if(1 == type){
	    var path = window.current_path;
		if(path.charAt(path.length-1) != "/"){path+="/";}
	    option.path    = path;
	    option.oldname = $(option.el).attr('filename');
		option.newname = $(option.el).attr('filename');
　		asyncbox.prompt('文件重命名-'+window.poweredby, '请输入新文件名（<font color="red">不能含非法字符</font>）:<br />重命名 '+option.oldname, option.oldname,'text',function(action,val){
　　		if(action == 'ok'){
				for(i=1;i<szMsg.length+1;i++){
					if(val.indexOf(szMsg.substring(i-1,i))>-1){
						asyncbox.tips('请勿输入非法字符如:'+szMsg,'error');return;
					}
				}
				option.newname = val;
				rename(3,option);
　　　		}
　		});
    }else if(2 == type){ 
		var path = window.current_path;
		if(path.charAt(path.length-1) != "/"){path+="/";}
	    option.path    = path;
	    option.oldname = $(option.el).attr('dirname');
		option.newname = $(option.el).attr('dirname');
		asyncbox.prompt('目录重命名-'+window.poweredby, '请输入新目录名（<font color="red">不能含非法字符</font>）:<br />重命名 '+option.oldname, option.oldname,'text',function(action,val){
　　		if(action == 'ok'){
				for(i=1;i<szMsg.length+1;i++){
					if(val.indexOf(szMsg.substring(i-1,i))>-1){
						asyncbox.tips('请勿输入非法字符如:'+szMsg,'error');return;
					}
				}
				option.newname = val;
				rename(3,option);
　　　		}		
　		});			
    }else if(3 == type ){
	    if($.trim(option.newname) == $.trim(option.oldname)){
			asyncbox.tips('新旧文件名不能相同','error');return;
		}		
		waitme();
		$.ajax({
            type: "POST", url: 'do.php', timeout:10000, error: ajaxErrorMsg,
			data:{'action':'rename', 'path':option.path, 'oldname':option.oldname, 'newname':option.newname},				
            success:function(data){
	            var json = parseJson(data);
	            if(200 == json.statusCode){					    
					asyncbox.alert(json.message,'文件目录重命名-'+window.poweredby);
	            }else{					    
	                asyncbox.error(json.message,'文件目录重命名-'+window.poweredby);
	            }
				refresh(true);
	        }	
		});
    }else{
	   asyncbox.tips('Sorry,未知操作！','error');return;
	}
}
//权限编辑
function chmod(type,option){
    var type = type;
    var option = option || {};
    if(1 == type){
        var file = encodeURI($(option.el).attr('filepath') + $(option.el).attr('filename'));
		option.file = file;option.chmod = $(option.el).attr('chmod');
        chmod(3,option);
    }else if(2 == type){ 
	   var dir = encodeURI($(option.el).attr('dirpath') + $(option.el).attr('dirname') + '/');
       option.file = dir; option.chmod = $(option.el).attr('chmod');
       chmod(3,option);	
    }else if(3 == type ){
	    var data = selectCheck();
	    if(1 > data.length && !option.file){
		    asyncbox.tips('请至少选择一个文件或目录!','error');return;
	    }else{
		    var files = option.file || encodeURI(data.join('|'));
            option.chmod = option.chmod	|| 777;	
	        asyncbox.open({
　　　          url : 'do.php',width : 480, height : 360, scrolling:'no',
                data : {action:'chmod',type:3,mode:'show',files:files,chmod:option.chmod},title:'修改权限',　
			    tipsbar : {title : '目录文件权限修改',content : '目录文件权限修改...'},
			    btnsbar : [{text: '提交更改',action  : 'save_chmod' },{text: '关闭窗口',action  : 'close_widow' }],
　　　          callback : function(action,iframe){               
　　　　　          var $this = $(this);
				    if('save_chmod' == action){
				        $.ajax({
                            type: "POST",url: 'do.php', timeout:60000,data:'action=chmod&type=3&mode=save&chmod='+iframe.get_chmod_num()+'&deep='+iframe.get_chmod_deep()+'&files='+files,error: ajaxErrorMsg,
                            success:function(data){
	                			var json = parseJson(data);
	                			if(200 == json.statusCode){					    
	                    			asyncbox.alert(json.message,'权限变更-'+window.poweredby);
	                			}else{					    
	                    			asyncbox.error(json.message,'权限变更-'+window.poweredby);
	                			}
				    			refresh(true);
	            		    }	
						});
				    }else if('close_widow' == action){
                             return;			
                    }
                }
　          });
        }
    }else{
	   asyncbox.tips('Sorry,未知操作！','error');return;
	}
}
//打开目录
function opendir(path,option){
    waitme(); 
    var path   = path || window.root_path;
	var option = option || {};
	if (path.charAt(path.length-1) != "/"){path+="/";}
	if(debug){asyncbox.tips('opendir("'+path+'")','success');}
	//缓存可用直接载入缓存
	if(window.cache_main_on && !option.reload){
	   var json = jQuery.rookie(jQuery.md5(path));
	   if(json && 200 == json.statusCode){
	      display(json);return;
	   }
	}
	//处理文件列表排序
	var order = '';
	var list_order_type = jQuery.cookie('list_order_type');
	var list_order_sort = jQuery.cookie('list_order_sort');
	if(list_order_type && list_order_sort){
		order = list_order_type + '|' +list_order_sort;
	}
    $.ajax({
       type: "POST",url: 'do.php', timeout:20000,data: 'action=list&path=' + encodeURIComponent(path) +'&order='+order,
	   error: ajaxErrorMsg,
       success: function(data){
			var json = parseJson(data);
			display(json);
			if(window.cache_main_on && 200 == json.statusCode){
				jQuery.rookie(jQuery.md5(path),json,{expire:window.cache_main_time});
			}
		   
	   }
    });	
}
//主菜单API接口
function openDir(type,option){
    var type = type;
    var option = option || {};
	var path = $(option.el).attr('dirpath') + $(option.el).attr('dirname');
	opendir(path,{});
}

//下载文件
function download(type,option){
    var type = type;
    var option = option || {};
	var tool = 'top=50,left=120,width=500,height=560,scrollbars=yes,toolbar=yes,menubar=yes,scrollbars=yes,resizable=yes,location=yes,status=yes';
    if(1 == type){
        var file = encodeURI($(option.el).attr('filepath') + $(option.el).attr('filename'));
        var url  = 'do.php?action=downfile&type=1&file='+$.trim(file);
		window.open(url, '下载文件', tool);
    }else if(2 == type){ 
	   var dir = encodeURI($(option.el).attr('dirpath') + $(option.el).attr('dirname') + '/');
       var url  = 'do.php?action=downfile&type=2&dir='+$.trim(dir);	
	   window.open(url, '下载文件', tool);
    }else if(3 == type){
	   var data = selectCheck();
	   if(0 < data.length){
	        var files = encodeURI(data.join('|'));
			if(debug){asyncbox.tips('download: do.php?action=downfile&type=3&files='+$.trim(files),'success');}
            var url  = 'do.php?action=downfile&type=3&files='+$.trim(files);
			window.open(url, '下载文件', tool);
	   }else{
	        asyncbox.tips('请至少选择一个文件!','error');return;
	   }
    }else{
	   asyncbox.tips('Sorry,未知操作！','error');return;
	}
}

//文本编辑
function edit(type, option){
    var type = type || 0;
    var option = option || {};
	if('unknown' == get_language($(option.el).attr('fileext'))){
	    asyncbox.tips('Sorry,暂不支持编辑'+ $(option.el).attr('fileext') +'格式文件！','error');
	    return;
	}else{		
        var file = ($(option.el).attr('filepath') + $(option.el).attr('filename'));
		var edit_conf_temp = parseJson(edit_conf) || {};
		var edit_width = edit_conf_temp.EDITOR_CONF.WIDTH || 900; var edit_height = edit_conf_temp.EDITOR_CONF.HEIGHT || 600;
　      asyncbox.open({
　　　      id : 'editfile', url : 'do.php',width : edit_width,height : edit_height, 
            data : {action:'editfile',type:'show',file:file},title:'编辑：'+$(option.el).attr('filename'),　
			tipsbar : {title : '文件编辑',content : '文件编辑器...'},
			btnsbar : $.btn.OKOKOKOKOKCANCEL,
			btnsbar : [
				{text: '获取代码',action  : 'file_getcode' },
				{text: '预览文件',action  : 'file_view' },
				/*{text: '代码高亮',action  : 'file_highlight' },*/
				{text: '显示行号',action  : 'file_line' },
				{text: '自动完成',action  : 'file_autoComplete' },
				{text: '保存文件',action  : 'file_save' },
				{text: '关闭窗口',action  : 'close_widow' }
			],
　　　      callback : function(action, iframe){               
　　　　　      var $this = this;
				if('file_getcode' == action){
				    iframe.getcode();return false;
				}else if('file_view' == action){
					iframe.view();return false;
				}else if('file_highlight' == action){
					iframe.highlight();return false;
				}else if('file_line' == action){
					iframe.line();return false;
				}else if('file_autoComplete' == action){
					iframe.autoComplete();return false;
				}else if('file_save' == action){
					iframe.save();return false;
				}else if('close_widow' == action){
					var msg = '<font color="red">关闭窗口会造成未保存代码丢失,    <br />确定关闭？</font>';
　					asyncbox.confirm(msg,'代码编辑-'+window.poweredby,function(action){
　　　					if(action == 'ok'){
　　　　　					$.close($this.id);
　　　					}
　					});
					return false;
				}		
　　　      }
　      });	
        return;	
	}	
}


//批量上传
function upload(type, option){
    var type = type || 0;
    var option = option || {};
	waitme();
    asyncbox.open({
　　　  url : 'do.php',width : 520,height : 500, 
        title :'批量上传文件- '+window.poweredby,
        data : {action:'upload',type:'show',path:window.current_path},　　　
        tipsbar : {title : '文件上传',content : '批量上传文件...'},
	    btnsbar : [{text: '关闭窗口',action  : 'close_window' }],
	    callback : function(action){refresh(true);}
　  });
    return;
}

//删除文件
function del(type,option){
 var type = type || 0;
 var option = option || {};
 if(1 == type){      
    	asyncbox.confirm('<font color="red">删除操作不可恢复,请谨慎操作,确定删除：<font color="blue">'+ $(option.el).attr('filename') +' ?</font></font>', '文件删除-'+window.poweredby,function(action){
	    	if(action == 'ok'){
		    	$.ajax({
                	type: "POST",url: 'do.php', timeout:10000,
                    data: 'action=delete&type=1&file='+ encodeURI($(option.el).attr('filepath') + $(option.el).attr('filename')),
                	success:function(data){
	                	var json = parseJson(data);
	                	if(200 == json.statusCode){					    
	                    	asyncbox.alert(json.message, '文件删除-'+window.poweredby);
	                	}else{					    
	                    	asyncbox.error(json.message, '文件删除-'+window.poweredby);
	                	}
				    	refresh(true);
	            	},
                	error: ajaxErrorMsg
            	});
			}else{
		    	return;
			}
	    }); 
    }else if(2 == type){ 
    	asyncbox.confirm('<font color="red">删除操作不可恢复,请谨慎操作,确定删除：<font color="blue">'+$(option.el).attr('dirname')+' ?</font></font>',  '目录删除-'+window.poweredby,function(action){
	    	if(action == 'ok'){
		    	$.ajax({
                	type: "POST",url: 'do.php', timeout:20000,
                	data: 'action=delete&type=2&dir='+ encodeURI($(option.el).attr('dirpath') + $(option.el).attr('dirname') + '/'),
                	success:function(data){
	                	var json = parseJson(data);
	                	if(200 == json.statusCode){					    
	                    	asyncbox.alert(json.message, '目录删除-'+window.poweredby);
	                	}else{					    
	                    	asyncbox.error(json.message, '目录删除-'+window.poweredby);
	                	}
				    	refresh(true);
	            	},
                	error: ajaxErrorMsg
            	});
			}else{
		    	return;
			}
	    });    
    }else if(3 == type){
	    var data = selectCheck();
		if(1 > data.length){asyncbox.tips('请至少选择一个文件或目录!','error');return;}
		var confirm_str = '';
		$.each( data, function(idx, item){
			confirm_str += '<font color="blue">删除：'+item+'</font><br />';
		}); 
        asyncbox.confirm('<font color="red">删除操作不可恢复,请谨慎操作,确定删除 ?</font><br />'+confirm_str, '批量删除-'+window.poweredby, function(action){
	        if(action == 'ok'){
			    var data = selectCheck();
				if(1 > data.length){asyncbox.tips('请至少选择一个文件或目录!','error');return;}else{ waitme();}
	            var files = data.join('|');
		        $.ajax({
                    type: "POST",url: 'do.php', timeout:30000,
                    data: 'action=delete&type=3&files='+ (files),
                    success:function(data){
	                    var json = parseJson(data);
	                    if(200 == json.statusCode){
	                        asyncbox.alert(json.message, '批量删除-'+window.poweredby);
	                    }else{
	                        asyncbox.error(json.message, '批量删除-'+window.poweredby);
	                    }
				        refresh(true);
	                },
                    error: ajaxErrorMsg
                });
		    }else{
		        return;
		    }
        });
    }
}

//预览ZIP文档
function viewzip(type,option){
	var type = type || 0;
    var option = option || {};
	if('zip' != $(option.el).attr('fileext')){
	   asyncbox.tips('Sorry,暂不支持预览非ZIP格式文件！','error');
	   return;
	}
	waitme();
	$.ajax({
        type: 'POST',url: 'do.php', timeout:600000, dataType:"json",
        data: encodeURI('action=viewzip&path='+ $(option.el).attr('filepath') +'&name=' + $(option.el).attr('filename')),					
        success:function(data){
	        var json = parseJson(data);
	        if(200 == json.statusCode){
				asyncbox.alert(json.message, 'ZIP预览-'+window.poweredby);
				refresh(true);
	        }else{
	            asyncbox.error(json.message, 'ZIP预览-'+window.poweredby);
	        }						
	    },
        error: ajaxErrorMsg					
    });
}
//压缩ZIP文档
function zip(type,option){
    var type = type;
    var option = option || {};
    if(1 == type){
        var file = encodeURI($(option.el).attr('filepath') + $(option.el).attr('filename'));
		option.file = file;option.chmod = $(option.el).attr('chmod'); option.type = 1;
        zip(3,option);
    }else if(2 == type){ 
	   var dir = encodeURI($(option.el).attr('dirpath') + $(option.el).attr('dirname') + '/');
       option.file = dir; option.chmod = $(option.el).attr('chmod'); option.type = 2;
       zip(3,option);	
    }else if(3 == type ){
	    var data = selectCheck();
		option.type = option.type || 3;
	    if(1 > data.length && !option.file){
		    asyncbox.tips('请至少选择一个文件或目录!','error');return;
	    }else{
		    var files = option.file || encodeURI(data.join('|'));
			waitme();
	        $.ajax({
				type: 'POST',url: 'do.php', timeout:600000, dataType:"json",
				data: 'action=zip&type='+option.type+'&files='+ files,					
				success:function(data){
					var json = parseJson(data);
					if(200 == json.statusCode){
						asyncbox.alert(json.message, 'ZIP压缩-'+window.poweredby);
						refresh(true);
					}else{
						asyncbox.error(json.message, 'ZIP压缩-'+window.poweredby);
					}						
				},
				error: ajaxErrorMsg					
			});
        }
    }else{
	   asyncbox.tips('Sorry,未知操作！','error');return;
	}
}
//解压ZIP文档
function unzip(type,option){
    var type = type || 0;
    var option = option || {};
	if('zip' != $(option.el).attr('fileext')){
	   asyncbox.tips('Sorry,暂不支持解压非ZIP格式文件！','error');
	   return;
	}
    asyncbox.prompt('ZIP解压-'+window.poweredby,'请输入解压目录（解压到当前请输入 <font color="red">"."</font>）:',$(option.el).attr('filename').replace('.'+$(option.el).attr('fileext'),''),'text',function(action,val){　　　  
        if(action == 'ok'){
		   // if('' == val){ asyncbox.error('解压目录不能为空！','ZIP解压-'+window.poweredby);return;}  
			if('' == val){ asyncbox.tips('Sorry,解压目录不能为空！','error');	return;} 		   	   
            asyncbox.confirm('<font color="red">存在同名文件覆盖 ?</font>', 'ZIP解压-'+window.poweredby, function(action){
　　　          if(action == 'ok'){var remove = '1';}else{var remove = '0';}        
                waitme();
         	    $.ajax({
                    type: 'POST',url: 'do.php', timeout:600000, dataType:"json",
                    data: 'action=unzip&path='+ $(option.el).attr('filepath') +'&name=' + $(option.el).attr('filename') +'&unzippath='+val+'&remove='+remove,					
                    success:function(data){
	                    var json = parseJson(data);
	                    if(200 == json.statusCode){
					        asyncbox.alert(json.message, 'ZIP解压-'+window.poweredby);
							refresh(true);
	                    }else{
	                        asyncbox.error(json.message, 'ZIP解压-'+window.poweredby);
	                    }						
	                },
                    error: ajaxErrorMsg					
                });
			});
　　　  }else{
          return;
        }
　  });
}

//文件剪切
function cut(type,option){
    var type    = type || 0;
    var option  = option || {};
	var timeout = window.cache_cut_time || 15;
	copy(type,option);
	jQuery.cookie('cut_type', 'cut', {expires: timeout});
}
//文件复制
function copy(type,option){
    var type    = type || 0;
    var option  = option || {};
	var timeout = window.cache_cut_time || 15;
	var path    = window.current_path;
    if(1 == type){		
		jQuery.cookie('cut_path_from', path, {expires: timeout});
        jQuery.cookie('cut_files', $(option.el).attr('filename'), {expires: timeout});
    }else if(2 == type){    
	    jQuery.cookie('cut_path_from', path, {expires: timeout});
        jQuery.cookie('cut_files', $(option.el).attr('dirname'), {expires: timeout});
    }else if(3 == type){	
        var data = selectCheck();
	    if(0 < data.length){
		    $.each(data,function(idx, item){
			     data[idx] = item.replace(window.current_path,'');
			});
	        var files = (data.join('|'));
			if(debug){asyncbox.tips('copy:'+data.join('|'), 'success');}
			jQuery.cookie('cut_path_from', path, {expires: timeout});
            jQuery.cookie('cut_files', files, {expires: timeout});
	    }else{
	        asyncbox.tips('Sorry,请至少选择一个文件!','error');return;
	    }
    }
	jQuery.cookie('cut_type', 'copy', {expires: timeout});
	jQuery.cookie('cut_cover', '0', {expires: timeout});
}

//文件粘贴
function paste(type,option){ 
    var type    = type || 0;
    var option  = option || {}; 
    var path_from   = jQuery.cookie('cut_path_from');
	var path_to     = jQuery.cookie('cut_path_to') || window.current_path;
    var files  = jQuery.cookie('cut_files');
	var type   = jQuery.cookie('cut_type');
	var cover  = jQuery.cookie('cut_cover') || '0';
    var sure   = option.sure || '0';
	
    //递归死循环检测
	var $return = false;	
	if(files){		
		$.each(files.split('|'), function(idx, item){	
			if(-1 != path_to.lastIndexOf(path_from+item+'/')){
				asyncbox.tips('Sorry, 源路径和目标路径冲突(递归死循环), 无法执行粘贴操作!','error');
				$return =  true;
			}			
		});
		
	}
	if($return){
		return;
	}else{
		if(path_from == path_to){
			asyncbox.tips('Sorry，源路径不能与目标路径相同!','error');return;		
		}else if( (path_from && path_to && files) && ('cut' == type || 'copy' == type) ){	
			waitme();
			$.ajax({
				type: "POST",url: 'do.php', timeout:60000,
				data: {action:'paste',sure:sure,type:type,path_from:path_from,path_to:path_to,files:files,cover:cover},
				success:paste_callback,
				error: ajaxErrorMsg
			});	
		}else{
			asyncbox.tips('Sorry, 剪贴板过期或没选择文件, 无法执行粘贴操作!','error');return;
		}
	}	
}
function paste_callback(data){
	var timeout = window.cache_cut_time || 30;
	var json = parseJson(data);
	if(200 == json.statusCode){
	    asyncbox.alert(json.message, '文件粘贴-'+window.poweredby);
		jQuery.cookie('cut_files', null);
		jQuery.cookie('cut_path_to', null);
		jQuery.cookie('cut_path_from', null);
		jQuery.cookie('cut_type', null);
		jQuery.cookie('cut_cover', null);
		refresh(true);	   
	}else if(201 == json.statusCode){
		jQuery.cookie('cut_files', json.data.files, {expires: timeout});
		jQuery.cookie('cut_path_to', json.data.path_to, {expires: timeout});
		jQuery.cookie('cut_path_from', json.data.path_from, {expires: timeout});
		jQuery.cookie('cut_type', json.data.type, {expires: timeout});
		jQuery.cookie('cut_cover', 1, {expires: timeout});
		asyncbox.confirm('<font color="red">覆盖操作不可恢复,请谨慎操作,确定覆盖?：<br /><font color="blue">'+json.message+'</font>',  '是否覆盖-文件粘贴-'+window.poweredby, function(action){
	    	if(action == 'ok'){
		    	paste();return;
			}else{
				jQuery.cookie('cut_files', null);
				jQuery.cookie('cut_path_to', null);
				jQuery.cookie('cut_path_from', null);
				jQuery.cookie('cut_type', null);
				jQuery.cookie('cut_cover', null);
		    	return;
			}
	    });
	}else{
		asyncbox.error(json.message, '文件粘贴-'+window.poweredby);
        return false;
	}
}