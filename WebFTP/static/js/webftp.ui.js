// +----------------------------------------------------------------------
// | Copyright (C) 浩天科技 www.ihotte.com admin@ihotte.com
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author   左手边的回忆 QQ:858908467 E-mail:858908467@qq.com
// +----------------------------------------------------------------------
//**/
//+------------------------------------------------------------------------------
//* 文件$ID ： webftp.ui.js
//+------------------------------------------------------------------------------
//* 路径$ID ： static/js/webftp.ui.js
//+------------------------------------------------------------------------------
//* 程序版本： 浩天 WebFTP V1.0.0 2011-10-01
//+------------------------------------------------------------------------------
//* 功能简介： 界面(UI)初始化
//+------------------------------------------------------------------------------
//* 注意事项： 请勿私自删除此版权信息！
//+------------------------------------------------------------------------------

//mainMenu
function init_mainMenu(){
	//main-menu
	$("#main-menu").find("li").mouseover(function(){
         $(this).addClass('focus');
    });
    $("#main-menu").find("li").mouseout(function(){
         $(this).removeClass('focus');
    });
}

//处理 返回top
function init_gotop(){
	var _top = $(window).scrollTop() + $(window).height() - 80;
	var _left = $(window).width() / 2 + 485;
	$("#js_go_top").css({
		top: _top + "px",left: _left + "px"
	}).click(function(){
		$(window).scrollTop(0);$(this).hide();
	});
	if(($(document).height() > $(window).height()) && $(window).scrollTop() > 0){
		$("#js_go_top").show();
	}else{
		$("#js_go_top").hide();	
	}
}
//处理文件列表边框
function init_list(){
     var ch = $('#list_main_center').outerHeight();
	 $("#list_main_left").height(ch);
     $("#list_main_right").height(ch);
}
//处理footer
function init_footer(){ 
  var html = '';
  html += '<a href="readme.txt" target="_blank">关于WebFTP</a> - ';
  html += '<a href="javascript:alert(\'请联系QQ：858908467\');">加入我们</a> - ';
  html += '<a href="readme.txt" target="_blank">服务条款</a> - ';
  html += '<a href="javascript:void(0);">使用帮助</a> - ';
  html += '<a href="http://bbs.ihotte.com/" target="_blank">意见反馈</a> <br/>';
  html += 'CopyRight ©2011-2012 <a href="http://www.ihotte.com/" target="_blank">ihotte.com</a> All Rights Reserved.';  
  $('#footer').html(html);
}

//UI初始化
function initUI(){
    init_list();
	init_footer();
    init_gotop();
	init_mainMenu();
	init_contextMenu();
	
	init_Main_Menu();
	init_Dir_Menu();
	init_File_Menu();
	
	init_fmtitle();
	init_toolbar();
	init_show();
	
}


function init_toolbar(){
	$('#tool_sort').hover(function(){$('#drop_sort').hide().show();},function(){});	
	$('#drop_sort').hover(function(){},function(){$('#drop_sort').hide();});
		
	if(!jQuery.cookie('list_order_sort')){ 
		$('#list_order_sort_'+list_order_sort).addClass('checked');
		jQuery.cookie('list_order_sort', list_order_sort, {expires: 3600*24});
	}else{
		$('#list_order_sort_'+jQuery.cookie('list_order_sort')).addClass('checked');
	}
	$('.list_order_sort').click(function(){
		$('.list_order_sort').removeClass('checked');
		$(this).addClass('checked');
		list_order_sort = $(this).attr('sort');
		jQuery.cookie('list_order_sort', list_order_sort, {expires: 3600*24});
		opendir(current_path,{reload:true});
	});
	
	if(!jQuery.cookie('list_order_type')){ 
		$('#list_order_type_'+list_order_type).addClass('checked');
		jQuery.cookie('list_order_type', list_order_type, {expires: 3600*24}); 
	}else{
		$('#list_order_type_'+jQuery.cookie('list_order_type')).addClass('checked');
	}
	$('.list_order_type').click(function(){
		$('.list_order_type').removeClass('checked');
		$(this).addClass('checked');
		list_order_type = $(this).attr('type');
		jQuery.cookie('list_order_type', list_order_type, {expires: 3600*24});
		opendir(current_path,{reload:true});
	});
}


function init_fmtitle(){
	$(".fmTitle").fmTitle({
		style:"red",
		color:"#757168",
		size:"12px"
	});
}




//UI监听
$(function(){
    setInterval("init_list()", 100);
	$(window).scroll(function(){
	   init_gotop();
    }).resize(function(){
	   init_gotop();
    });
});