<?php
define('SW_',true);
require_once('_common.php');

//获取所有方法文件
foreach(SWXmlrpc::getMethodFiles() as $value){
	require_once(SW::path('framework.xmlrpcs.'.$value));
}

//开始执行server服务
SWXmlrpc::response();

?>