<?php
// +----------------------------------------------------------------------
// | Copyright (C) 浩天科技 www.ihotte.com admin@ihotte.com
// +----------------------------------------------------------------------
// | Licensed: ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author:   左手边的回忆 QQ:858908467 E-mail:858908467@qq.com
// +----------------------------------------------------------------------
/**
 +------------------------------------------------------------------------------
 * 文件$ID ： type.conf.php
 +------------------------------------------------------------------------------
 * 路径$ID ： conf/type.conf.php
 +------------------------------------------------------------------------------
 * 程序版本： 浩天 WebFTP V1.0.0 2011-10-01
 +------------------------------------------------------------------------------
 * 功能简介： 文件默认配置文件
 +------------------------------------------------------------------------------
 * 注意事项： 请勿私自删除此版权信息！
 +------------------------------------------------------------------------------
 **/
 
if(!defined('INC_ROOT')){die('Forbidden Access');}
return array(
	//'ADMIN_NAME'            => 'admin',
	//'ADMIN_PASS'            => md5('admin'), 
	'MAX_TIME_LIMIT'        => 600,
	'DEFAULT_CHARSET'       => 'utf-8',
	
	'APP_DEBUG'             => false,
	'SYSTEM_NAME'           => 'WebFTP',	
	'SYSTEM_VERSION'        => 'V 1.3.0',
	'LOG_EXCEPTION_RECORD'  => true,//记录日志
	'LOG_EXCEPTION_TYPE'    => 'EMERG,ALERT,CRIT,ERR,WARNING,INFO,DEBUG,SQL',
	//'LOG_EXCEPTION_TYPE'    => 'EMERG,ALERT,CRIT,ERR,WARNING,NOTICE,INFO,DEBUG,SQL',
	'LOG_FILE_SIZE'         => 1024*100,
	/*日志级别 从上到下，由低到高
    'EMERG';  // 严重错误: 导致系统崩溃无法使用
    'ALERT';  // 警戒性错误: 必须被立即修改的错误
    'CRIT';  // 临界值错误: 超过临界值的错误，例如一天24小时，而输入的是25小时这样
    'ERR';  // 一般错误: 一般性错误
    'WARNING';  // 警告性错误: 需要发出警告的错误
    'NOTICE';  // 通知: 程序可以运行但是还不够完美的错误
    'INFO';  // 信息: 程序输出信息
    'DEBUG';  // 调试: 调试信息
    'SQL';  // SQL：SQL语句 注意只在调试模式开启时有效
	*/
	
	
	/* Cookie设置 */
	'COOKIE_EXPIRE'         => 3600*24*10,
	'COOKIE_DOMAIN'         => '',
	'COOKIE_PATH'           => '/',
	'COOKIE_PREFIX'         => 'ihotte_webftp_',
     
	'ZH_PATH_VIEW'         => true,//是否显示中文目录，会降低性能
	'IMG_FILE_VIEW'        => true,//是否开启图片预览
	'CACHE_DATA_DEL'       => false,//是否删除数据缓存
  	  
	'CACHE_MAIN_ON'        => true,//是否启用浏览器缓存
	'CACHE_MAIN_TIME'      => 60,//浏览器数据缓存周期(秒)
	'CACHE_CUT_TIME'       => 20,//浏览器剪贴板缓存周期(秒)
    
	//本地初始根目录
	'ROOT_PATH'            => './../',
	//远程初始根目录
	'FTP_ROOT_PATH'        => './',
);
?>