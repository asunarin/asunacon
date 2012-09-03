function edit(){
	var tagname=$('#tagname').val();
	var tagchange=$('#tagchange').val();
	if(tagname && tagchange && (tagname != tagchange)){
		SWJS.ajaxRequest('tags','edit',{name:tagname,changeTo:tagchange},function(obj){
			if(obj.err){
				SWJS.showMessage(obj.msg,obj.err);
			}else{
				window.location.href='tags.php';
			}
		});
	}
}

function del(){
	var tagname=$('#tagname').val();
	if(tagname){
		SWJS.ajaxRequest('tags','delete',{name:tagname},function(obj){
			if(obj.err){
				SWJS.showMessage(obj.msg,obj.err);
			}else{
				window.location.href='tags.php';
			}
		});
	}
}

function showEdit(tagname){
	$('#edittag').slideDown('fast');
	$('#tagname').val(tagname);
	$('#tagchange').val(tagname);
}

function closeEdit(){
	$('#tagname').val('');
	$('#tagchange').val('');
	$('#edittag').slideUp('fast');
}

$(function(){});
