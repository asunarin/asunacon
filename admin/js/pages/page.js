function getPage(){
	var arr={
		id:SWJS.fValue('id'),
		parentid:SWJS.fValue('parentid'),
		title:SWJS.fValue('title'),
		content:SWJS.fValue('content'),
		alias:SWJS.fValue('alias'),
		state:SWJS.fValue('state'),
		flag:SWJS.fValue('flag'),
		ordernum:SWJS.fValue('ordernum'),
		onmenu:SWJS.fValue('onmenu')
	}
	return arr;
}

function save(strState){
	if(GLB['setInterval_autosave']) clearInterval(GLB['setInterval_autosave']);
	var arrPage=getPage();
	switch(strState){
		case 'publish':
			arrPage.state=1;
		break;
		case 'draft':
			arrPage.state=0;
		break;
	}
	SWJS.ajaxRequest('page','save',arrPage,function(obj){
		if(obj.err==0){
			window.location.href='page.php?id='+obj.id;
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
		var arrPage=getPage();
		arrPage.state=0;
		arrPage.autosave=1;
		SWJS.ajaxRequest('page','save',arrPage,function(obj){
			if(SWJS.fValue('id')==0){
				$('[name=id]').val(obj.id);
			}
			GLB['autosave_content']=SWJS.fValue('content');
		},SWJS.lang('Page_Auto_Save'));
	}
}

function checkParent(){
	if(SWJS.fValue('parentid')==0){
		$('#onmenu').attr('disabled','');
	}else{
		$('#onmenu').attr('disabled','disabled');
		$('#onmenu').attr('checked','');
	}
}

$(function(){
	
	$('#draft').click(function(){
		save('draft');
	});

	$('#publish').click(function(){
		save('publish');
	});

	$('#save').click(function(){
		save('');
	});
	
	$('#parentid').change(function(){
		checkParent();
	});

	if(SWJS.fValue('id')==0 || SWJS.fValue('state')==0){
		GLB['setInterval_autosave']=setInterval(autoSave,180000);
	}
	
	checkParent();
	
});
