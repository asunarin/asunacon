function search(bWait,cPage){
	if(GLB['setTimeout_search']) clearTimeout(GLB['setTimeout_search']);
	if(bWait){
		GLB['setTimeout_search']=setTimeout(function(){
			commentSearch(cPage);
		},500);
	}else{
		commentSearch(cPage);
	}
}

function goPage(cPage){
	search(false,cPage);
}

function setState(intState,id){
	var strSelect=id;
	if(!strSelect) strSelect=getSelect();
	if(strSelect){
		SWJS.ajaxRequest('comments','state',{state:intState,ids:strSelect},function(obj){
			commentSearch(1);
			SWJS.showMessage(obj.msg,obj.err);
		});
	}
}

function del(id){
	var strSelect=id;
	if(!strSelect) strSelect=getSelect();
	if(strSelect){
		SWJS.ajaxRequest('comments','delete',{ids:strSelect},function(obj){
			commentSearch(1);
			SWJS.showMessage(obj.msg,obj.err);
		});
	}
}

function commentSearch(cPage){
	var strSearch=$('#search').val();
	var strState=getState();
	var strType=getType();
	var strPostid=getPostid();
	var strPages=getPages(cPage);
	SWJS.ajaxRequest('comments','search',{search:strSearch,state:strState,type:strType,postid:strPostid,pages:strPages},function(obj){
		if(obj.err==0){
			SWJS.buildPages(cPage,10,obj.maxpage,'goPage');
			$("#amount").html(strPages.replace(',',' - ')+' / '+obj.maxpage);
			fillComments(obj.comment);
			$('#select').html('0');
		}else{
			SWJS.showMessage(obj.msg,obj.err);
		}
	});
}

function fillComments(arr){
	var str='';
	if(arr.length==0){
		str+='	<li class="selectitem" style="border-top:0;">\n';
		str+='		<div class="cell-line opa50">\n';
		str+='			'+SWJS.lang('Selections_Empty')+'\n';
		str+='		</div>\n';
		str+='	</li>\n';
	}else{
		for(var i=0;i<arr.length;i++){
			var style='';
			var style2='';
			if(arr[i]['state']==2){
				style='red';
				style2='opa50';
			}else if(arr[i]['state']==0){
				style='yellow"';
				style2='opa50';
			}
			str+='	<li class="selectitem '+style+'" '+(i==0?'style="border-top:0;"':'')+'>\n';
			str+='		<div class="cell-line '+style2+'">\n';
			str+='			<div class="cell5"><input type="checkbox" id="p_'+arr[i]['id']+'" /></div>\n';
			str+='			<div class="cell20">\n';
			str+='				<img src="http://www.gravatar.com/avatar/'+SWJS.md5(arr[i]['email'])+'?s=16&d=http://www.gravatar.com/avatar/ad516503a11cd5ca435acc9bb6523536?s=16&r=G" width="16" height="16" /> <strong>'+(arr[i]['url']?'<a href="'+arr[i]['url']+'" target="_blank">'+arr[i]['author']+'</a>':arr[i]['author'])+'</strong>\n';
			str+='			</div>\n';
			str+='			<div class="cell35">\n';
			str+='				<a href="comments.php?postid='+arr[i]['postid']+'">&laquo;'+arr[i]['posttitle']+'&raquo;</a>\n';
			str+='			</div>\n';
			str+='			<div class="cell15">'+SWJS.lang('Comment_State')+': '+arr[i]['statename']+'</div>\n';
			str+='			<div class="cell25">'+arr[i]['posttime']+'</div>\n';
			str+='			<div style="clear:both"></div>\n';
			str+='		</div>\n';
			str+='		<div class="cell-line">\n';
			str+='			<div class="cell25" style="font-style:italic;">'+arr[i]['ip']+'<br /><a href="mailto:'+arr[i]['email']+'">'+arr[i]['email']+'</a>'+(arr[i]['url']?'<br /><a href="mailto:'+arr[i]['url']+'">'+arr[i]['url']+'</a>':'')+'<br /></div>\n';
			str+='			<div class="cell75"><div class="content">'+arr[i]['content']+'</div></div>\n';
			str+='			<div style="clear:both"></div>\n';
			str+='		</div>\n';
			str+='		<div class="icon">\n';
			if(arr[i]['state']==0){
				str+='		<a href="javascript:setState(1,'+arr[i]['id']+');">'+SWJS.lang('Opt_Approved')+'</a> | \n';
				str+='		<a href="javascript:setState(2,'+arr[i]['id']+');">'+SWJS.lang('Opt_Spam')+'</a> | \n';
			}else if(arr[i]['state']==1){
				str+='		<a href="javascript:setState(2,'+arr[i]['id']+');">'+SWJS.lang('Opt_Spam')+'</a> | \n';
			}else if(arr[i]['state']==2){
				str+='		<a href="javascript:setState(1,'+arr[i]['id']+');">'+SWJS.lang('Opt_Approved')+'</a> | \n';
			}
			str+='			<a href="comment.php?id='+arr[i]['id']+'">'+SWJS.lang('Edit')+'</a> | \n';
			str+='			<a href="javascript:del('+arr[i]['id']+');" style="color:brown">'+SWJS.lang('Opt_Delete')+'</a>\n';
			str+='		</div>\n';
			str+='	</li>\n';
		}
	}
	$('#itemlist').html(str);
	initSelect();
}

function getState(){
	var arrTmp=[];
	$('#type li .active').each(function(){
		switch($(this).attr('name')){
			case 'approved':
				arrTmp.push('1');
			break;
			case 'unapproved':
				arrTmp.push('0');
			break;
			case 'spam':
				arrTmp.push('2');
			break;
		}
	});
	return arrTmp.length>0?arrTmp.join(','):'';
}

function getType(){
	var arrTmp=[];
	$('#type li .active').each(function(){
		switch($(this).attr('name')){
			case 'pingback':
				arrTmp.push('2');
			break;
			case 'trackback':
				arrTmp.push('3');
			break;
		}
	});
	return arrTmp.length>0?arrTmp.join(','):'1';
}

function getPostid(){
	var arrTmp=[];
	$('#type li .active').each(function(){
		if($(this).attr('name')=='postid'){
			arrTmp.push($(this).attr('value'));
		}
	});
	return arrTmp.length>0?arrTmp.join(','):'';
}

function getPages(cPage){
	if(!cPage) cPage=1;
	var thisNum=(cPage-1)*10+1;
	var nextNum=thisNum+9;
	return thisNum+','+nextNum;
}

function initSelect(){
	$('#itemlist input:checkbox').change(function(){
		setSelectState($(this));
	});
	getSelectAll();
	$('.selectitem').hover(
		function(){$(this).find('.icon').css('visibility','visible')},
		function(){$(this).find('.icon').css('visibility','hidden')}
	);
}

function setSelectState(objInput){
	var i=$('#select').html()?Number($('#select').html()):0;
	if(objInput.attr('checked')){
		i++;
		objInput.parents('.selectitem').css('background-color','#FFFDE2');
	}else{
		i--;
		objInput.parents('.selectitem').css('background-color','');
	}
	$('#select').html(i);
	getSelectAll();
}

function getSelect(){
	var arrTmp=[];
	$('#itemlist input:checkbox[checked=true]').each(function(){
		arrTmp.push($(this).attr('id').replace('p_',''));
	});
	return arrTmp.length>0?arrTmp.join(','):'';
}

function getSelectAll(){
	if($('#itemlist input:checkbox[checked=true]').length==$('#itemlist input:checkbox').length){
		$('#selectall').attr('checked',true);
	}else{
		$('#selectall').attr('checked',false);
	}
}

$(function(){
		
	$('#selectall').change(function(){
		var bAll=$(this).attr('checked');
		$('#itemlist input:checkbox').each(function(){
			if($(this).attr('checked')==!bAll){
				$(this).attr('checked',bAll);
				setSelectState($(this));
			}
		});
	})
	
	$("#search").change(function(){
		search(true,1);
	});

	$('#type li a').click(function(){
		$(this).toggleClass('active');
		search(true,1);
	});

	$('#approvedsel').click(function(){
		setState(1);
	});

	$('#unapprovedsel').click(function(){
		setState(0);
	});

	$('#spamsel').click(function(){
		setState(2);
	});

	$('#deletesel').click(function(){
		del();
	});

	search(false,1);

});
