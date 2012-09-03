function search(bWait,cPage){
	if(GLB['setTimeout_search']) clearTimeout(GLB['setTimeout_search']);
	if(bWait){
		GLB['setTimeout_search']=setTimeout(function(){
			pageSearch(cPage);
		},500);
	}else{
		pageSearch(cPage);
	}
}

function goPage(cPage){
	search(false,cPage);
}

function trash(id){
	var strSelect=id;
	if(!strSelect) strSelect=getSelect();
	if(strSelect){
		SWJS.ajaxRequest('pages','trash',{ids:strSelect},function(obj){
			pageSearch(1);
			SWJS.showMessage(obj.msg,obj.err);
		});
	}
}

function resume(id){
	var strSelect=id;
	if(strSelect){
		SWJS.ajaxRequest('pages','resume',{id:strSelect},function(obj){
			pageSearch(1);
			SWJS.showMessage(obj.msg,obj.err);
		});
	}
}

function del(id){
	var strSelect=id;
	if(strSelect){
		SWJS.ajaxRequest('pages','delete',{id:strSelect},function(obj){
			pageSearch(1);
			SWJS.showMessage(obj.msg,obj.err);
		});
	}
}

function pageSearch(cPage){
	var strSearch=$('#search').val();
	var strState=getState();
	var strTrash=getTrash();
	var strPages=getPages(cPage);
	SWJS.ajaxRequest('pages','search',{search:strSearch,state:strState,trash:strTrash,pages:strPages},function(obj){
		if(obj.err==0){
			SWJS.buildPages(cPage,10,obj.maxpage,'goPage');
			$("#amount").html(strPages.replace(',',' - ')+' / '+obj.maxpage);
			fillPages(obj.page);
			$('#select').html('0');
		}else{
			SWJS.showMessage(obj.msg,obj.err);
		}
	});
}

function fillPages(arr){
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
			var style3='';
			if(arr[i]['flag']==0){
				style='red';
				style2='opa50';
			}else if(arr[i]['state']==0){
				style='yellow"';
				style2='opa50';
			}
			if(arr[i]['level']){
				style3='padding-left:8px;border-left:'+(arr[i]['level']*10)+'px #f0f0f0 solid;';
			}
			str+='	<li class="selectitem '+style+'" style="'+(i==0?'border-top:0;':'')+'">\n';
			str+='	<div style="'+style3+'">\n';
			str+='		<div class="cell-line '+style2+'">\n';
			str+='			<div class="cell5"><input type="checkbox" id="p_'+arr[i]['id']+'" /></div>\n';
			str+='			<div class="cell30">';
			str+='				<strong>'+(arr[i]['title']?arr[i]['title']:'<span style="color:gray;">&lt; '+SWJS.lang('Page_No_Title')+' &gt;</span>')+(arr[i]['alias']?' <span style="color:gray;">('+arr[i]['alias']+')</span>':'')+'</strong>\n';
			str+='			</div>\n';
			str+='			<div class="cell20">'+(arr[i]['level']?SWJS.lang('Page_Parent')+': '+arr[i]['parentname']:SWJS.lang('Page_On_Menu')+': '+(arr[i]['onmenu']?SWJS.lang('Yes'):SWJS.lang('No')))+'</div>\n';
			str+='			<div class="cell20">'+SWJS.lang('Page_State')+': '+arr[i]['statename']+'</div>\n';
			str+='			<div class="cell25">'+arr[i]['posttime']+'</div>\n';
			str+='			<div style="clear:both"></div>\n';
			str+='		</div>\n';
			str+='		<div class="content">'+arr[i]['content']+'</div>\n';
			str+='		<div class="icon">\n';
			str+='			<a href="page.php?id='+arr[i]['id']+'">'+SWJS.lang('Edit')+'</a> | \n';
			if(arr[i]['flag']==0){
				str+='		<a href="javascript:resume('+arr[i]['id']+');">'+SWJS.lang('Opt_Resume')+'</a> | \n';
				str+='		<a href="javascript:del('+arr[i]['id']+');" style="color:brown">'+SWJS.lang('Opt_Drop')+'</a>\n';
			}else{
				str+='		<a href="javascript:trash('+arr[i]['id']+');" style="color:brown">'+SWJS.lang('Opt_Delete')+'</a>\n';
			}
			str+='		</div>\n';
			str+='	</div>\n';
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
			case 'publish':
				arrTmp.push('1');
			break;
			case 'draft':
				arrTmp.push('0');
			break;
		}
	});
	return arrTmp.length>0?arrTmp.join(','):'';
}

function getTrash(){
	var arrTmp=[];
	$('#type li .active').each(function(){
		if($(this).attr('name')=='trash'){
			arrTmp.push('0');
		}
	});
	return arrTmp.length>0?'1':'';
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
		objInput.parents('.selectitem').css('background','#FFFDE2');
	}else{
		i--;
		objInput.parents('.selectitem').css('background','');
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

	$('#trashsel').click(function(){
		trash();
	});
	
	search(false,1);

});
