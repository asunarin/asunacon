/*=====================================================================
* 附件列表方法
======================================================================*/
SWJS.attachment=function(){
	var strPath=$('#current_path').val()?$('#current_path').val():'';
	$('#attachmenttab_box .loading').show();
	SWJS.ajaxRequest('attachment','search',{path:strPath},function(obj){
		if(obj.err==0){
			arrList=obj.list;
			var str='';
			var strPathNav='';
			var current_path=$('#current_path').val(); //当前的路径
			var current_file=$('#current_file').val(); //当前上传的文件

			$('#upload_box').append(str);
			$('#upload_box li:hidden').fadeIn('fast');

			strPathNav+='<a href="javascript:SWJS.attachment_goPath(\'\')">attached</a> / ';
			if(current_path){
				var arrTmp=current_path.split('.');
				var strTmp='';
				for(var i=0;i<arrTmp.length;i++){
					strTmp+=(strTmp?'.':'')+arrTmp[i];
					strPathNav+='<a href="javascript:SWJS.attachment_goPath(\''+strTmp+'\')">'+arrTmp[i]+'</a> / ';
				}
			}
			str+='	<li>\n';
			str+='		<table cellpadding="0" cellspacing="0" border="0" width="100%">\n';
			str+='			<tr>\n';
			str+='				<td width="25"><img src="images/att_up.png" /></td>\n';
			str+='				<td><strong>'+strPathNav+'</strong></td>\n';
			str+='				<td width="150"></td>\n';
			str+='				<td width="150"></td>\n';
			str+='				<td width="25"></td>\n';
			str+='			</tr>\n';
			str+='		</table>\n';
			str+='	</li>\n';
			for(var i=0;i<arrList.length;i++){
				if(current_file==arrList[i]['filename']){
					var currentStyle='color:yellow;';
				}else{
					var currentStyle='';
				}
				str+='	<li>\n';
				str+='		<table cellpadding="0" cellspacing="0" border="0" width="100%">\n';
				str+='			<tr>\n';
				if(arrList[i]['is_dir']){
					str+='				<td width="25"><img src="images/att_folder.png" /></td>\n';
					str+='				<td><strong><a href="javascript:SWJS.attachment_goPath(\''+(current_path?current_path+'.':'')+arrList[i]['filename']+'\')">'+arrList[i]['filename']+'</a></strong></td>\n';
					str+='				<td width="150">'+arrList[i]['filesize']+' bytes</td>\n';
					str+='				<td width="150">'+arrList[i]['datetime']+'</td>\n';
					str+='				<td width="80" class="imageicon"></td>\n';
				}else if(arrList[i]['is_photo']){
					str+='				<td width="25"><img src="images/att_photo.png" /></td>\n';
					if(GLB['editor']){
						str+='				<td><a href="javascript:GLB[\'editor\'].pasteHTML(\'<img src=\\\''+SWJS.urlRoot+'attached/'+(current_path?current_path.replace(/\./g,'/')+'/':'')+arrList[i]['filename']+'\\\' />\');" style="'+currentStyle+'">'+arrList[i]['filename']+'</a></td>\n';
					}else{
						str+='				<td>'+arrList[i]['filename']+'</td>\n';
					}
					str+='				<td width="150">'+arrList[i]['filesize']+' bytes</td>\n';
					str+='				<td width="150">'+arrList[i]['datetime']+'</td>\n';
					str+='				<td width="80" class="imageicon"><a href="'+SWJS.urlRoot+'attached/'+(current_path?current_path.replace(/\./g,'/')+'/':'')+arrList[i]['filename']+'" target="_blank"><img src="images/att_newwindow.png" title="'+SWJS.lang('Preview')+'" /></a></td>\n';
				}else{
					str+='				<td width="25"><img src="images/att_document.png" /></td>\n';
					str+='				<td style="'+currentStyle+'">'+arrList[i]['filename']+'</td>\n';
					str+='				<td width="150">'+arrList[i]['filesize']+' bytes</td>\n';
					str+='				<td width="150">'+arrList[i]['datetime']+'</td>\n';
					str+='				<td width="80" class="imageicon"></td>\n';
				}
				str+='			</tr>\n';
				str+='		</table>\n';
				str+='	</li>\n';
			}
			$('#attachment_box').html(str);

			//初始化上传
			if(!GLB['attachment_upload']){
				SWJS.attachment_upload(strPath);
			}else{
				GLB['attachment_upload'].action('_fileupload.php?path='+strPath);
			}

		}else{
			SWJS.showMessage(obj.msg,obj.err);
		}
		$('#attachmenttab_box .loading').hide();
	},'','',true);
}

/*=====================================================================
* 附件路径跳转
======================================================================*/
SWJS.attachment_goPath=function(strPath){
	$('#current_path').val(strPath);
	SWJS.attachment();
}

/*=====================================================================
* 上传方法
======================================================================*/
SWJS.attachment_upload=function(strPath){
	var objUpload=$('#upload').upload({
		height:25,
		width:100,
		name: 'file',
		action: '_fileupload.php?path='+strPath,
		enctype: 'multipart/form-data',
		params: {},
		autoSubmit: true,
		onSubmit: function() {SWJS.loadingMessage('Uploading...','uploadloading');},
		onComplete: function(res) {
			SWJS.loadingMessage('','uploadloading');
			obj = $.evalJSON(res);

			if(obj.error==0){
				//跳转到上传文件的目录
				$('#current_path').val(obj.path);
				$('#current_file').val(obj.name);
				SWJS.attachment();

			}else{
				alert(obj.message);
			}

		},
		onSelect: function() {}
	});

	//设置全局变量
	GLB['attachment_upload']=objUpload;

};

/*=====================================================================
* 默认执行
======================================================================*/
$(function(){

	$('#attachmenttab').click(function(){
        //定义是否已近打开过列表
        if(!GLB['attachment_open']){
            SWJS.attachment();
            GLB['attachment_open']=true;
        }
    });
    
    $('#attachmenttab_box').html('<a href="#" id="upload" class="button" style="margin-left:0;width:88px;">'+SWJS.lang('Upload_Click')+'</a><input id="current_path" type="hidden" value="" /><input id="current_file" type="hidden" value="" /><div class="loading"><img src="images/loading3.gif" /></div><ul id="attachment_box" class="attachmentlist"></ul>');

});
