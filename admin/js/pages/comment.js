function getComment(){
	var arr={
		id:SWJS.fValue('id'),
		content:SWJS.fValue('content'),
		author:SWJS.fValue('author'),
		email:SWJS.fValue('email'),
		url:SWJS.fValue('url'),
		posttime:SWJS.fValue('posttime'),
		state:SWJS.fValue('state')
	}
	return arr;
}

function save(){
	var arrComment=getComment();
	SWJS.ajaxRequest('comment','save',arrComment,function(obj){
		if(obj.err==0){
			window.location.href='comment.php?id='+obj.id;
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
