function save(strTemplate){
	SWJS.ajaxRequest('template','save',{template:strTemplate},function(obj){
		if(obj.err){
			SWJS.showMessage(obj.msg,obj.err);
		}else{
			window.location.href='template.php';
		}
	});
}
