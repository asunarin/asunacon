/*=====================================================================
* 通用callback回调函数
======================================================================*/
SWJS.setCallback=function(strPath,arrPara,func){

	$.post(strPath,arrPara,function(json){

		obj = $.evalJSON(json);

		if(func) func(obj);
		
	});

}

/*=====================================================================
* admin后台所用callback回调函数
* loadingMsg: 如果设置，则调用loading_box
* loadingName: loading_box的名称
* hideLoading: 是否显示loading功能（loading总开关）
======================================================================*/
SWJS.ajaxRequest=function(postpage,act,arrPara,func,loadingMsg,loadingName,hideLoading){
	
	if(!hideLoading){
		if(loadingMsg){
			if(!loadingName){
				if(!GLB['loadingBoxNum']) GLB['loadingBoxNum']=0;
				loadingName=GLB['loadingBoxNum'];
				GLB['loadingBoxNum']++;
			}
			SWJS.loadingMessage(loadingMsg,loadingName);
		}else{
			SWJS.loading(true);
		}
	}
		
	SWJS.setCallback('postback.php?postpage='+postpage+'&act='+act,arrPara,function(obj){
	
		if(!hideLoading){
			if(loadingMsg){
				SWJS.loadingMessage('',loadingName);
			}else{
				SWJS.loading(false);
			}
		}
		
		if(!obj.login){
			
			window.location.href='login.php';

		}else if(func){

			func(obj);
		
		}else if(obj.msg && obj.err<2){

			SWJS.showMessage(obj.msg,obj.err);
			
		}
	
	});
	
}

/*=====================================================================
* 获取表单项的值
* 返回字符串，多个值用“,”分隔
======================================================================*/
SWJS.fValue=function(str,type){

	var res='';
	
	if(type=='id'){
		var obj=$('[id='+str+']');
	}else{
		var obj=$('[name='+str+']');
	}

	var id=obj.attr('id');

	if(typeof(KE)!=='undefined' && KE.g[id]){
		try{KE.util.setData(id);}catch(e){}
	}

	if(obj.is('input')){
		
		switch(obj.attr('type')){
			case 'checkbox':
				res=obj.attr("checked")?obj.val():'';
			break;
			case 'radio':
				obj.each(function(){if($(this).attr("checked")) res=$(this).val();});
			break;
			default:
				res=obj.val();
			break;
		}

	}else if(obj.is('textarea')){
		
		res=obj.val();

	}else if(obj.is('select')){
		
		res=obj.val();

	}
	
	if(Object.prototype.toString.apply(res) !== '[object Array]'){
		res=[res];
	}

	return res.join(',');

}

/*=====================================================================
* 初始化human message函数
======================================================================*/
SWJS.setupMessage=function(){
	
	$('#humanMsgLog p').click(function() {
		$(this).siblings('ul').slideToggle();
		$(this).toggleClass('logisopen');
	});
	
}

/*=====================================================================
* 显示操作信息，成功或错误
======================================================================*/
SWJS.showMessage=function(strMsg,err){
	
	/*
	 * HUMANIZED MESSAGES 1.0
	 * idea - http://www.humanized.com/weblog/2006/09/11/monolog_boxes_and_transparent_messages
	 * home - http://humanmsg.googlecode.com
	 * 
	 * Modify for Swan by VAL/ZYI
	*/
	if(!GLB['messageBoxNum']) GLB['messageBoxNum']=0;
	
	$('#msg_container').append('<div id="msg_box_'+GLB['messageBoxNum']+'" class="humanMsg ui-corner-all opa80" '+(err!=0?'style="background-color:#cc0000;"':'')+'><p>'+strMsg+'</p></div>');
	var num=GLB['messageBoxNum']; //这么做是因为GLB['messageBoxNum']为全局变量
	setTimeout(function(){$('#msg_box_'+num).fadeOut('slow');}, 3000);
	
	// Prepend message to log and Slide it down
	$('#humanMsgLog').show().children('ul').prepend('<li>'+strMsg+'</li>').children('li:first').slideDown(200);			
	if ( $('#humanMsgLog ul').css('display') == 'none') {
		$('#humanMsgLog p').animate({ bottom: 40 }, 200, function() {
			$(this).animate({ bottom: 0 }, 300, function() { $(this).css({ bottom: 0 }) })
		})
	}
	
	GLB['messageBoxNum']++;

}

/*=====================================================================
* 显示正在运行的提示框
======================================================================*/
SWJS.loadingMessage=function(strMsg,boxName){
	
	if(!boxName) boxName='';

	var haveit=($('#loading_box_'+boxName).html())?true:false;
    	
	if(strMsg){
		
		SWJS.loading(false);
		
		var str='<img src="images/loading2.gif" style="float: left; margin-right:8px;" /> <strong>'+strMsg+'</strong>';

		if(haveit){
			$('#loading_box_'+boxName).html(str);
		}else{
			$('#loading_container').append('<div id="loading_box_'+boxName+'" class="humanMsg ui-corner-all opa80">'+str+'</div>');
		}

	}else if(haveit){
        
		$('#loading_box_'+boxName).remove();
        
	}

}

/*=====================================================================
* callback时显示loading图标
* 或显示正在运行的提示框
======================================================================*/
SWJS.loading=function(bShow){
	
	if(bShow){

		$('#img_state').html('<img src="images/loading.gif" />');

	}else{

		$('#img_state').html('');

	}

}

/*=====================================================================
* 下拉菜单函数
======================================================================*/
SWJS.dropMenu=function(){

	var thisDom=null;

	$('.dropmenu').hover( function(e) {

		if(GLB['setTimeout_dropmenu']) clearTimeout(GLB['setTimeout_dropmenu']);
		//首先隐藏打开的菜单
		$('.dropmenu').children('li').not('.default').hide();
		thisDom=$(this);
		thisDom.children('li').not('.default').show();


	}, function(e) {

		thisDom=$(this);
		GLB['setTimeout_dropmenu'] = setTimeout(function(){thisDom.children('li').not('.default').hide();}, 500);

	});

};

/*=====================================================================
* 下拉块按钮函数
======================================================================*/
SWJS.dropButton=function(){

	$(".dropbutton").click(function(){
		var id=$(this).attr('id')+'_box';
		if(!$("#"+id).is(":visible")){
			$('.dropbox').hide();
			$(".dropbutton").removeClass('dropbutton_down');
			$("#"+id).fadeIn('fast');
			$(this).addClass('dropbutton_down');
		}else{
			$("#"+id).fadeOut('fast');
			$(".dropbutton").removeClass('dropbutton_down');
		}
	});

};

/*=====================================================================
* 顶部主菜单函数
======================================================================*/
SWJS.mainMenu=function(strTitle){

	$('#mainmenu').each(function(){
		//alert($(this).find('a').html());
		if($(this).find('a').html() == strTitle) alert(strTitle);

	});
	/*
	$('#mainmenu a').html(strTitle?strTitle:'Menu');

	var thisDom=null;

	$('#mainmenu').hover( function(e) {

		if(GLB['setTimeout_mainmenu']) clearTimeout(GLB['setTimeout_mainmenu']);
		thisDom=$(this);
		thisDom.children('li').not('.default').show();


	}, function(e) {

		thisDom=$(this);
		GLB['setTimeout_mainmenu'] = setTimeout(function(){thisDom.children('li').not('.default').hide();}, 500);

	});

	$('#mainmenu li').hover( function(e) {

		$(this).find('.submenu').show();

	}, function(e) {

		$(this).find('.submenu').hide();

	});
	*/
};

/*=====================================================================
* 添加顶部状态栏图标
======================================================================*/
SWJS.addTopIcon=function(src,link,id,alt){

	var str='';

	if(link){
		str='<img src="'+src+'" class="icon" '+(alt?'alt="'+alt+'" ':'')+'/>';
		str='<a href="'+link+'" '+(id?'id="'+id+'" ':'')+'>'+str+'</a>';
	}else{
		str='<img src="'+src+'" class="icon" '+(id?'id="'+id+'" ':'')+(alt?'alt="'+alt+'" ':'')+'/>';
	}

	$('#menu_icon').append(str);
		
};

/*=====================================================================
* 显示分页函数
======================================================================*/
SWJS.buildPages=function(cPage,perPage,record,strCallback){

	var maxPage=Math.ceil(record/perPage);
	if(maxPage<1) maxPage=1;
	if(cPage>maxPage) cPage=maxPage;
	var start=1;
	if((cPage-5)>1) start=cPage-5;
	if((maxPage-cPage)<5) start=maxPage-9;
    if(start<1) start=1;
    
	var str='';
    str+='<a href="javascript:'+strCallback+'(1);" class="ui-corner-all">&lt;&lt;</a> <a href="javascript:'+strCallback+'('+(cPage-1>0?cPage-1:1)+');" class="ui-corner-all">&lt;</a> ';
    for(var i=start;i<(start+10);i++){
		if(i>maxPage) break;
		str+='<a href="javascript:'+strCallback+'('+i+');" class="ui-corner-all '+(i==cPage?'cur':'')+'">'+i+'</a> ';
    }
    str+='<a href="javascript:'+strCallback+'('+(cPage+1<=maxPage?cPage+1:maxPage)+');" class="ui-corner-all">&gt;</a> <a href="javascript:'+strCallback+'('+maxPage+');" class="ui-corner-all">&gt;&gt;</a> ';

    $('.pagenav').html(str);
		
};

/*=====================================================================
* JS用MD5加密函数
======================================================================*/
SWJS.md5=function(str){
	if(!str||str.constructor!=String) return "";
		function MD5(sMessage) {
		function RotateLeft(lValue, iShiftBits) { return (lValue<<iShiftBits) | (lValue>>>(32-iShiftBits)); }
		function AddUnsigned(lX,lY) {
			var lX4,lY4,lX8,lY8,lResult;
			lX8 = (lX & 0x80000000);
			lY8 = (lY & 0x80000000);
			lX4 = (lX & 0x40000000);
			lY4 = (lY & 0x40000000);
			lResult = (lX & 0x3FFFFFFF)+(lY & 0x3FFFFFFF);
			if (lX4 & lY4) return (lResult ^ 0x80000000 ^ lX8 ^ lY8);
			if (lX4 | lY4) {
				if (lResult & 0x40000000) return (lResult ^ 0xC0000000 ^ lX8 ^ lY8);
				else return (lResult ^ 0x40000000 ^ lX8 ^ lY8);
			}
			else return (lResult ^ lX8 ^ lY8);
		}
		function F(x,y,z) { return (x & y) | ((~x) & z); }
		function G(x,y,z) { return (x & z) | (y & (~z)); }
		function H(x,y,z) { return (x ^ y ^ z); }
		function I(x,y,z) { return (y ^ (x | (~z))); }
		function FF(a,b,c,d,x,s,ac) {
			a = AddUnsigned(a, AddUnsigned(AddUnsigned(F(b, c, d), x), ac));
			return AddUnsigned(RotateLeft(a, s), b);
		}
		function GG(a,b,c,d,x,s,ac) {
			a = AddUnsigned(a, AddUnsigned(AddUnsigned(G(b, c, d), x), ac));
			return AddUnsigned(RotateLeft(a, s), b);
		}
		function HH(a,b,c,d,x,s,ac) {
			a = AddUnsigned(a, AddUnsigned(AddUnsigned(H(b, c, d), x), ac));
			return AddUnsigned(RotateLeft(a, s), b);
		}
		function II(a,b,c,d,x,s,ac) {
			a = AddUnsigned(a, AddUnsigned(AddUnsigned(I(b, c, d), x), ac));
			return AddUnsigned(RotateLeft(a, s), b);
		}
		function ConvertToWordArray(sMessage) {
			var lWordCount;
			var lMessageLength = sMessage.length;
			var lNumberOfWords_temp1=lMessageLength + 8;
			var lNumberOfWords_temp2=(lNumberOfWords_temp1-(lNumberOfWords_temp1 % 64))/64;
			var lNumberOfWords = (lNumberOfWords_temp2+1)*16;
			var lWordArray=Array(lNumberOfWords-1);
			var lBytePosition = 0;
			var lByteCount = 0;
			while ( lByteCount < lMessageLength ) {
				lWordCount = (lByteCount-(lByteCount % 4))/4;
				lBytePosition = (lByteCount % 4)*8;
				lWordArray[lWordCount] = (lWordArray[lWordCount] | (sMessage.charCodeAt(lByteCount)<<lBytePosition));
				lByteCount++;
			}
			lWordCount = (lByteCount-(lByteCount % 4))/4;
			lBytePosition = (lByteCount % 4)*8;
			lWordArray[lWordCount] = lWordArray[lWordCount] | (0x80<<lBytePosition);
			lWordArray[lNumberOfWords-2] = lMessageLength<<3;
			lWordArray[lNumberOfWords-1] = lMessageLength>>>29;
			return lWordArray;
		}
		function WordToHex(lValue) {
			var WordToHexValue="",WordToHexValue_temp="",lByte,lCount;
			for (lCount = 0;lCount<=3;lCount++) {
				lByte = (lValue>>>(lCount*8)) & 255;
				WordToHexValue_temp = "0" + lByte.toString(16);
				WordToHexValue = WordToHexValue + WordToHexValue_temp.substr(WordToHexValue_temp.length-2,2);
			}
			return WordToHexValue;
		}
		var x=Array();
		var k,AA,BB,CC,DD,a,b,c,d
		var S11=7, S12=12, S13=17, S14=22;
		var S21=5, S22=9 , S23=14, S24=20;
		var S31=4, S32=11, S33=16, S34=23;
		var S41=6, S42=10, S43=15, S44=21;
		x = ConvertToWordArray(sMessage);
		a = 0x67452301; b = 0xEFCDAB89; c = 0x98BADCFE; d = 0x10325476;
		for (k=0;k<x.length;k+=16) {
			AA=a; BB=b; CC=c; DD=d;
			a=FF(a,b,c,d,x[k+0], S11,0xD76AA478);
			d=FF(d,a,b,c,x[k+1], S12,0xE8C7B756);
			c=FF(c,d,a,b,x[k+2], S13,0x242070DB);
			b=FF(b,c,d,a,x[k+3], S14,0xC1BDCEEE);
			a=FF(a,b,c,d,x[k+4], S11,0xF57C0FAF);
			d=FF(d,a,b,c,x[k+5], S12,0x4787C62A);
			c=FF(c,d,a,b,x[k+6], S13,0xA8304613);
			b=FF(b,c,d,a,x[k+7], S14,0xFD469501);
			a=FF(a,b,c,d,x[k+8], S11,0x698098D8);
			d=FF(d,a,b,c,x[k+9], S12,0x8B44F7AF);
			c=FF(c,d,a,b,x[k+10],S13,0xFFFF5BB1);
			b=FF(b,c,d,a,x[k+11],S14,0x895CD7BE);
			a=FF(a,b,c,d,x[k+12],S11,0x6B901122);
			d=FF(d,a,b,c,x[k+13],S12,0xFD987193);
			c=FF(c,d,a,b,x[k+14],S13,0xA679438E);
			b=FF(b,c,d,a,x[k+15],S14,0x49B40821);
			a=GG(a,b,c,d,x[k+1], S21,0xF61E2562);
			d=GG(d,a,b,c,x[k+6], S22,0xC040B340);
			c=GG(c,d,a,b,x[k+11],S23,0x265E5A51);
			b=GG(b,c,d,a,x[k+0], S24,0xE9B6C7AA);
			a=GG(a,b,c,d,x[k+5], S21,0xD62F105D);
			d=GG(d,a,b,c,x[k+10],S22,0x2441453);
			c=GG(c,d,a,b,x[k+15],S23,0xD8A1E681);
			b=GG(b,c,d,a,x[k+4], S24,0xE7D3FBC8);
			a=GG(a,b,c,d,x[k+9], S21,0x21E1CDE6);
			d=GG(d,a,b,c,x[k+14],S22,0xC33707D6);
			c=GG(c,d,a,b,x[k+3], S23,0xF4D50D87);
			b=GG(b,c,d,a,x[k+8], S24,0x455A14ED);
			a=GG(a,b,c,d,x[k+13],S21,0xA9E3E905);
			d=GG(d,a,b,c,x[k+2], S22,0xFCEFA3F8);
			c=GG(c,d,a,b,x[k+7], S23,0x676F02D9);
			b=GG(b,c,d,a,x[k+12],S24,0x8D2A4C8A);
			a=HH(a,b,c,d,x[k+5], S31,0xFFFA3942);
			d=HH(d,a,b,c,x[k+8], S32,0x8771F681);
			c=HH(c,d,a,b,x[k+11],S33,0x6D9D6122);
			b=HH(b,c,d,a,x[k+14],S34,0xFDE5380C);
			a=HH(a,b,c,d,x[k+1], S31,0xA4BEEA44);
			d=HH(d,a,b,c,x[k+4], S32,0x4BDECFA9);
			c=HH(c,d,a,b,x[k+7], S33,0xF6BB4B60);
			b=HH(b,c,d,a,x[k+10],S34,0xBEBFBC70);
			a=HH(a,b,c,d,x[k+13],S31,0x289B7EC6);
			d=HH(d,a,b,c,x[k+0], S32,0xEAA127FA);
			c=HH(c,d,a,b,x[k+3], S33,0xD4EF3085);
			b=HH(b,c,d,a,x[k+6], S34,0x4881D05);
			a=HH(a,b,c,d,x[k+9], S31,0xD9D4D039);
			d=HH(d,a,b,c,x[k+12],S32,0xE6DB99E5);
			c=HH(c,d,a,b,x[k+15],S33,0x1FA27CF8);
			b=HH(b,c,d,a,x[k+2], S34,0xC4AC5665);
			a=II(a,b,c,d,x[k+0], S41,0xF4292244);
			d=II(d,a,b,c,x[k+7], S42,0x432AFF97);
			c=II(c,d,a,b,x[k+14],S43,0xAB9423A7);
			b=II(b,c,d,a,x[k+5], S44,0xFC93A039);
			a=II(a,b,c,d,x[k+12],S41,0x655B59C3);
			d=II(d,a,b,c,x[k+3], S42,0x8F0CCC92);
			c=II(c,d,a,b,x[k+10],S43,0xFFEFF47D);
			b=II(b,c,d,a,x[k+1], S44,0x85845DD1);
			a=II(a,b,c,d,x[k+8], S41,0x6FA87E4F);
			d=II(d,a,b,c,x[k+15],S42,0xFE2CE6E0);
			c=II(c,d,a,b,x[k+6], S43,0xA3014314);
			b=II(b,c,d,a,x[k+13],S44,0x4E0811A1);
			a=II(a,b,c,d,x[k+4], S41,0xF7537E82);
			d=II(d,a,b,c,x[k+11],S42,0xBD3AF235);
			c=II(c,d,a,b,x[k+2], S43,0x2AD7D2BB);
			b=II(b,c,d,a,x[k+9], S44,0xEB86D391);
			a=AddUnsigned(a,AA); b=AddUnsigned(b,BB); c=AddUnsigned(c,CC); d=AddUnsigned(d,DD);
		}
		var temp= WordToHex(a)+WordToHex(b)+WordToHex(c)+WordToHex(d);
		return temp.toLowerCase();
	}
	return MD5(str);
};
