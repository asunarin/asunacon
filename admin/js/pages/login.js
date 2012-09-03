function login(){
	SWJS.ajaxRequest('login','login',{account:SWJS.fValue('account'),password:SWJS.fValue('password')},function(obj){
		if(obj.err==0){
			window.location.href='index.php';
		}else{
			SWJS.showMessage(obj.msg,obj.err);
		}
	});
}

$(function(){});
