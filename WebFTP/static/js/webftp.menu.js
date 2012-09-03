// +----------------------------------------------------------------------
// | Copyright (C) 浩天科技 www.ihotte.com admin@ihotte.com
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author   左手边的回忆 QQ:858908467 E-mail:858908467@qq.com
// +----------------------------------------------------------------------
//**/
//+------------------------------------------------------------------------------
//* 文件$ID ： webftp.menu.js
//+------------------------------------------------------------------------------
//* 路径$ID ： static/js/webftp.menu.js
//+------------------------------------------------------------------------------
//* 程序版本： 浩天 WebFTP V1.0.0 2011-10-01
//+------------------------------------------------------------------------------
//* 功能简介： 菜单界面初始化接口
//+------------------------------------------------------------------------------
//* 注意事项： 请勿私自删除此版权信息！
//+------------------------------------------------------------------------------


//初始化文本菜单域
function init_contextMenu(){
  var main_menu = '';
  main_menu += '<ul id="myMainMenu" class="contextMenu">';
  main_menu += '<li class="main"><a href="#readme" action="#readme">系统菜单</a></li>';
  main_menu += '<li class="set"><a href="#set" action="#set">系统设置</a></li>';
  main_menu += '<li class="cut separator"><a href="#cut" action="#cut">剪切所选</a></li>';
  main_menu += '<li class="copy"><a href="#copy" action="#copy">复制所选</a></li>';
  main_menu += '<li class="paste"><a href="#paste" action="#paste">粘贴所选</a></li>';
  main_menu += '<li class="delete"><a href="#delete" action="#delete">删除所选</a></li>';
  main_menu += '<li class="zip separator"><a href="#zip" action="#zip">压缩文件</a></li>';
  main_menu += '<li class="search separator"><a href="#search" action="#search">搜索文件</a></li>';
  main_menu += '<li class="search"><a href="#chmod" action="#chmod">权限编辑</a></li>';
  main_menu += '<li class="download separator"><a href="#download" action="#download">打包下载</a></li>';
  main_menu += '<li class="selectall"><a href="#selectall" action="#selectall">全选反选</a></li>'; 
  main_menu += '</ul>';
  var dir_menu = '';
  dir_menu += '<ul id="myDirMenu" class="contextMenu">';
  dir_menu += '<li class="main"><a href="#readme" action="#readme">目录菜单</a></li>';
  dir_menu += '<li class="open separator"><a href="#open" action="#open">打开目录</a></li>';
  dir_menu += '<li class="rename"><a href="#rename" action="#rename">命名目录</a></li>';
  dir_menu += '<li class="cut  separator"><a href="#cut" action="#cut">剪切目录</a></li>';
  dir_menu += '<li class="copy"><a href="#copy" action="#copy">复制目录</a></li>';
  dir_menu += '<li class="delete"><a href="#delete" action="#delete">删除目录</a></li>';
  dir_menu += '<li class="search separator"><a href="#chmod" action="#chmod">权限编辑</a></li>';
  dir_menu += '<li class="zip separator"><a href="#zip" action="#zip">压缩目录</a></li>';
  dir_menu += '<li class="download"><a href="#download" action="#download">打包下载</a></li>';
  dir_menu += '</ul>';
  var file_menu = '';
  file_menu += '<ul id="myFileMenu" class="contextMenu">';
  file_menu += '<li class="main"><a href="#readme" action="#readme">文件菜单</a></li>';
  file_menu += '<li class="edit separator"><a href="#edit" action="#edit">编辑文件</a></li>';
  file_menu += '<li class="rename"><a href="#rename" action="#rename">命名文件</a></li>';
  file_menu += '<li class="cut  separator"><a href="#cut" action="#cut">剪切文件</a></li>';
  file_menu += '<li class="copy"><a href="#copy" action="#copy">复制文件</a></li>';
  file_menu += '<li class="delete"><a href="#delete" action="#delete">删除文件</a></li>';
  file_menu += '<li class="zip separator"><a href="#zip" action="#zip">压缩文件</a></li>';
  file_menu += '<li class="unzip"><a href="#unzip" action="#unzip">解压文件</a></li>';
  file_menu += '<li class="search"><a href="#chmod" action="#chmod">权限编辑</a></li>';
  file_menu += '<li class="view separator"><a href="#view" action="#view">预览文件</a></li>';
  file_menu += '<li class="download"><a href="#download" action="#download">下载文件</a></li>';
  file_menu += '</ul>';
  $('#mycontextMenu').html(main_menu + dir_menu + file_menu);  
}


//初始化系统主菜单
function init_Main_Menu(){
 $("#list tr").has('a').contextMenu({menu: 'myMainMenu'},function(action, el, pos) {actionMenu('main',{action:action,el:el,pos:pos})});
}

//初始化目录主菜单
function init_Dir_Menu(){
 $("#list a[id^='dir-id-']").contextMenu({menu: 'myDirMenu'},function(action, el, pos) {actionMenu('dir',{action:action,el:el,pos:pos})});
}

//初始化文件主菜单
function init_File_Menu(){
 $("#list a[id^='file-id-']").contextMenu({menu: 'myFileMenu'},function(action, el, pos) {actionMenu('file',{action:action,el:el,pos:pos})	});
}

//菜单指令调度
function actionMenu(type,option){
    var type = type || '';
    var option = option || {};
    if('main' == type){
        switch(option.action){
            case 'set':admin(3,option);break;
			
	        case 'cut':cut(3,option);break;
	        case 'copy':copy(3,option);break;
			case 'paste':paste(3,option);break;
	        case 'delete':del(3,option);break;
			
			case 'zip':zip(3, option);break;
			
			case 'search':search(3,option);break;				        
			case 'chmod':chmod(3, option);break;
			
	        case 'download':download(3,option);break;
		    case 'selectall': selectAll();break;
	    }
    }else if('dir' == type){
        switch(option.action){
            case 'open':openDir(2,option);break;
			case 'rename':rename(2,option);break;
			
	        case 'cut':cut(2,option);break;
	        case 'copy':copy(2,option);break;
	        case 'delete':del(2,option);break;
		
			case 'chmod':chmod(2, option);break;
			
	        case 'zip':zip(2, option);break;		
	        case 'download':download(2,option);break;
	    }
    }else if('file' == type){
        switch(option.action){
	        case 'edit':edit(1,option);break;
			case 'rename':rename(1,option);break;
			
	        case 'cut':cut(1,option);break;
	        case 'copy':copy(1,option);break;
	        case 'delete':del(1,option);break;
			
	        case 'zip':zip(1, option);break;
	        case 'unzip':unzip(1, option);break;
			case 'chmod':chmod(1, option);break;
			case 'view':viewzip(1, option);break;
	        case 'download':download(1,option);break;	    
	    }
    }else{
        asyncbox.error('未知操作指令 !','系统提示-'+window.poweredby);
    }
}