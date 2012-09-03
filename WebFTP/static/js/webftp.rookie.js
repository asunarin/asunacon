// +----------------------------------------------------------------------
// | Copyright (C) 浩天科技 www.ihotte.com admin@ihotte.com
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author   左手边的回忆 QQ:858908467 E-mail:858908467@qq.com
// +----------------------------------------------------------------------
//**/
//+------------------------------------------------------------------------------
//* 文件$ID ： webftp.rookie.js
//+------------------------------------------------------------------------------
//* 路径$ID ： static/js/webftp.rookie.js
//+------------------------------------------------------------------------------
//* 程序版本： 浩天 WebFTP V1.0.0 2011-10-01
//+------------------------------------------------------------------------------
//* 功能简介： Rookie本地高速大容量缓存API接口
//+------------------------------------------------------------------------------
//* 注意事项： 请勿私自删除此版权信息！
//+------------------------------------------------------------------------------
//* 操作简介： 
//+------------------------------------------------------------------------------
//* (1)设置rookie的值： 
//* 	1)jQuery.rookie(name, value); 
//* 	2)jQuery.rookie(name, value, {expire:生命周期(1 day),crossBrowser:跨浏览器(false)});
//* (2)获取rookie的值：jQuery.rookie(name); 
//* (3)删除rookie的值：jQuery.rookie(name, null); 
//+------------------------------------------------------------------------------

jQuery.rookie = function(name, value, options) {
  var name    = name;
  var value   = value;
  var options = options || {};
  var cache = {};
  if (typeof value === 'undefined'){   
    Rookie(function(){ return cache = this.read( name ); });return cache;
  }else if(value === null){
    Rookie(function(){ this.clear( name ); });return;
  }else{
    options.expire = (1/24/3600)*(options.expire || 3600*24);
    options.crossBrowser = options.crossBrowser || false;
    Rookie(function(){this.write(name,value,{crossBrowser:options.crossBrowser,expire:options.expire});});
  }
}