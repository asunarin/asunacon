<?php
define('SW_',true);
require_once('_common.php');

$arrLoad=array();
$out='';

//开启gzip压缩
if(extension_loaded('zlib')){
	ob_start("ob_gzhandler");
	header('Content-Encoding: gzip');
}else{
	ob_start();
}

//设置header为css样式表
header('Content-Type: text/css; charset=UTF-8');

//导入需要的页面js单元（这个位于css/目录）
if(SW::request('load','get')){
	
	$arrTmp=explode(',',SW::request('load','get'));

	foreach($arrTmp as $value){
		if(!empty($value)) $arrLoad[]=$value;
	}

}

foreach($arrLoad as $file){
	
	//获取css样式表路径
	$path=SW::path('admin.css.'.$file,'css');

	//执行读取操作
	if(@file_exists($path)){
				
		$out.=@file_get_contents($path)."\n";
	
	}

}

//输出
__($out);
ob_end_flush();

?>