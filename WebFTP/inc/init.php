<?php
// +----------------------------------------------------------------------
// | Copyright (C) 浩天科技 www.ihotte.com admin@ihotte.com
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author   左手边的回忆 QQ:858908467 E-mail:858908467@qq.com
// +----------------------------------------------------------------------
/**
 +------------------------------------------------------------------------------
 * 文件ID ：  init.php
 +------------------------------------------------------------------------------
 * 功能简介： 系统初始化
 +------------------------------------------------------------------------------
 * 注意事项： 请不要私自删除此版权信息
 +------------------------------------------------------------------------------
 **/

// +----------------------------------------------------------------------
// | Copyright (C) 浩天科技 www.ihotte.com admin@ihotte.com
// +----------------------------------------------------------------------
// | Licensed: ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author:   左手边的回忆 QQ:858908467 E-mail:858908467@qq.com
// +----------------------------------------------------------------------
/**
 +------------------------------------------------------------------------------
 * 文件$ID ： function.php
 +------------------------------------------------------------------------------
 * 路径$ID ： inc/function.php
 +------------------------------------------------------------------------------
 * 程序版本： 浩天 WebFTP V1.0.0 2011-10-01
 +------------------------------------------------------------------------------
 * 功能简介： 系统函数库
 +------------------------------------------------------------------------------
 * 注意事项： 请勿私自删除此版权信息！
 +------------------------------------------------------------------------------
 **/

//error_reporting(0);
//set_time_limit(0);
header('Content-Type:text/html; charset=utf-8');
//设置默认时区(无需修改)
if(function_exists('date_default_timezone_set')){
	date_default_timezone_set('PRC');
}


define('ROOT', realpath(dirname(__FILE__).'/../').'/');
define('INC_ROOT',  ROOT.'inc/');
define('FTP_ROOT',  ROOT.'ftp/');
define('CONF_ROOT', ROOT.'conf/');
define('DATA_PATH', ROOT.'data/');
define('DATA_PUBLIC_PATH',DATA_PATH.'Public/');
define('DATA_CACHE_PATH', DATA_PATH.'Cache/');
define('DATA_GROUP_PATH', DATA_PATH.'Group/');
define('DATA_USER_PATH',  DATA_PATH.'User/');
define('DATA_LOG_PATH',  DATA_PATH.'Log/');

//加载系统函数库
require(INC_ROOT.'function.php');
require(INC_ROOT.'Base.class.php');

//加载兼容函数库
if(version_compare(PHP_VERSION,'5.2.0','<')){
 require(INC_ROOT.'compat.php');
}



//初始化配置参数
C(require(CONF_ROOT.'setting.conf.php'));
C('type_conf',  require(CONF_ROOT.'type.conf.php'));
C('edit_conf',	require(CONF_ROOT.'edit.conf.php'));
C('list_conf',  require(CONF_ROOT.'list.conf.php'));
C('upload_conf',require(CONF_ROOT.'upload.conf.php'));
C('search_conf',require(CONF_ROOT.'search.conf.php'));



//dump(C());
set_time_limit((int)C('MAX_TIME_LIMIT'));
set_error_handler('throw_exception');

//开启SESSION
session_start();
?>