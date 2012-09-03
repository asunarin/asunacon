function getPost(){
    if(GLB['tags_obj']) GLB['tags_obj'].update();
	var arr={
		id:SWJS.fValue('id'),
		title:SWJS.fValue('title'),
		content:SWJS.fValue('content'),
		tag:SWJS.fValue('tag'),
		state:SWJS.fValue('state'),
		allowcomment:SWJS.fValue('allowcomment'),
		posttime:SWJS.fValue('posttime'),
		alias:SWJS.fValue('alias'),
		flag:SWJS.fValue('flag')
	}
	return arr;
}

function save(strState){
	if(GLB['setInterval_autosave']) clearInterval(GLB['setInterval_autosave']);
	var arrPost=getPost();
	switch(strState){
		case 'publish':
			arrPost.state=1;
		break;
		case 'draft':
			arrPost.state=0;
		break;
	}
	SWJS.ajaxRequest('post','save',arrPost,function(obj){
		if(obj.err==0){
			var pingNum=0;
			var arrPb=obj.pingback;
			if(obj.source && arrPb.length>0){
				for(var i=0;i<arrPb.length;i++){
					SWJS.ajaxRequest('pingback','send',{postid:obj.id,sourceURI:obj.source,targetURI:arrPb[i]},function(obj2){pingNum++;},'Pinging: '+arrPb[i]);
				}
			}
			$('body').ajaxComplete(function(){
				if(pingNum==arrPb.length){
					window.location.href='post.php?id='+obj.id;
				}
			}); 
		}else{
			SWJS.showMessage(obj.msg,obj.err);
		}
	});
}

function autoSave(){
	if(!GLB['autosave_flag']){
		GLB['autosave_content']=SWJS.fValue('content');
		GLB['autosave_flag']=true;
	}
	if(GLB['autosave_flag'] && SWJS.fValue('content')!=GLB['autosave_content']){
		var arrPost=getPost();
		arrPost.state=0;
		arrPost.autosave=1;
		SWJS.ajaxRequest('post','save',arrPost,function(obj){
			if(SWJS.fValue('id')==0){
				$('[name=id]').val(obj.id);
			}
			GLB['autosave_content']=SWJS.fValue('content');
		},SWJS.lang('Post_Auto_Save'));
	}
}

$(function(){

	GLB['tags_obj'] = new $.TextboxList('#tags', {});
    
    $('#tagadd a').click(function(){
        if($('#tagadd input').val()){
            GLB['tags_obj'].add($('#tagadd input').val());
            $('#tagadd input').val('');
        }
    });
	
	$('#draft').click(function(){
		save('draft');
	});

	$('#publish').click(function(){
		save('publish');
	});

	$('#save').click(function(){
		save('');
	});

	if(SWJS.fValue('id')==0 || SWJS.fValue('state')==0){
		GLB['setInterval_autosave']=setInterval(autoSave,180000);
	}	
		
});
