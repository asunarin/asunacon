function getOption(){
	var arr={
		title:SWJS.fValue('title'),
		description:SWJS.fValue('description'),
		url:SWJS.fValue('url'),
		author:SWJS.fValue('author'),
		email:SWJS.fValue('email'),
		account:SWJS.fValue('account'),
		password:SWJS.fValue('password'),repassword:SWJS.fValue('repassword'),
		debug:SWJS.fValue('debug'),
		pingback:SWJS.fValue('pingback'),
		language:SWJS.fValue('language'),
		timezone:SWJS.fValue('timezone'),
		rewrite:SWJS.fValue('rewrite'),
		checkcomment:SWJS.fValue('checkcomment'),
		receivepingback:SWJS.fValue('receivepingback'),
		receivetrackback:SWJS.fValue('receivetrackback'),
		feedcount:SWJS.fValue('feedcount'),
		mail_type:SWJS.fValue('mail_type'),
		mail_address:SWJS.fValue('mail_address'),
		mail_server:SWJS.fValue('mail_server'),
		mail_port:SWJS.fValue('mail_port'),
		mail_account:SWJS.fValue('mail_account'),
		mail_password:SWJS.fValue('mail_password')
	}
	return arr;
}

function save(){
	var arrOption=getOption();
	SWJS.ajaxRequest('option','save',arrOption,function(obj){
		if(obj.err==0){
			SWJS.showMessage(obj.msg,obj.err);
			$("input[name='password']").val('');
			$("input[name='repassword']").val('');
		}else{
			SWJS.showMessage(obj.msg,obj.err);
		}
	});
}

$(function(){
		
	$('#save').click(function(){
		save();
	});
	
});
