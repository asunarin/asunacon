<?php

defined('SW_') or die('Access Error');

//默认的错误报告级别
error_reporting(E_ERROR | E_WARNING | E_PARSE);

//开启session
session_start();

//定义全局常量
define('SW_DS',DIRECTORY_SEPARATOR);
define('SW_BASE',dirname(dirname(__FILE__)));

//定义系统版本
define('SW_VERSION','1.2.0');

//全局打印函数
function __($str){echo $str;}

//全局翻译函数（这个函数并不作输出）
function LANG($key,$arrWords=array()){return SWLanguage::lang($key,$arrWords);}

//引入框架类
require_once('classes/sw.php');

//进行错误捕获（生产环境禁用）
/*
error_reporting( E_ALL | E_NOTICE | E_STRICT );
set_error_handler(array(new SW(),"errHandler"));
*/

//引入lib文件
require_once(SW::path('framework.libs.kses'));
require_once(SW::path('framework.libs.snoopy'));
require_once(SW::path('framework.libs.trackback'));
require_once(SW::path('framework.libs.xmlrpc'));
require_once(SW::path('framework.libs.xmlrpc_wrappers'));
require_once(SW::path('framework.libs.xmlrpcs'));
require_once(SW::path('framework.libs.feedcreator'));
require_once(SW::path('framework.libs.phpmailer'));

//自动载入class
spl_autoload_register(array('SW','autoloader_classes'));

//如果没有config文件，则进行安装
if(!file_exists(SW::path('config'))){
	
	SW::$preAction='install';
	
	if(!defined('SW_PAGE_SETUP')){
		header("Location: ".SW::urlPath('admin.setup.install'));
		exit();
	}

}else{
	
	//引入config文件
	require_once(SW::path('config'));

	//定义数据表常量
	define('TBL_COMMENT',SW_PREFIX.'comment');
	define('TBL_OPTION',SW_PREFIX.'option');
	define('TBL_PAGE',SW_PREFIX.'page');
	define('TBL_POST',SW_PREFIX.'post');
	define('TBL_TAG',SW_PREFIX.'tag');
	
	//判断是否需要升级
	if(!SW::checkVersion()){
		
		SW::$preAction='upgrade';
		
		if(!defined('SW_PAGE_UPGRADE')){
			header("Location: ".SW::urlPath('admin.setup.upgrade'));
			exit();
		}
	
	//程序初始化开始...
	}else{
		
		SW::$preAction='';
		
		//设置时区
		if(SW::getOption('timezone')) date_default_timezone_set(SW::getOption('timezone'));
		
	}

}

?>
