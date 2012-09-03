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

//设置header为javascript脚本
header('Content-Type: application/x-javascript; charset=UTF-8');
header('Expires: '.gmdate("D, d M Y H:i:s",time()+360000).' GMT');

//导入需要的页面js单元（这个位于js/pages目录）
if(SW::request('load','get')){
	
	$arrTmp=explode(',',SW::request('load','get'));

	foreach($arrTmp as $value){
		if(!empty($value)) $arrLoad[]=$value;
	}

}

foreach($arrLoad as $file){
	
	//获取js脚本路径
	$path=SW::path('admin.js.'.$file,'js');

	switch($file){

		//SWJS类的附加属性和方法
		//后台js的初始化操作
		case 'swjs':
?>
//全局对象数组
var GLB={};

//全局js对象
var SWJS={};

//获取系统路径
SWJS.urlRoot='<?php __(SW::urlPath('',true));?>';

/*=====================================================================
* 获取系统语言
======================================================================*/
<?php
            //输出js语言数组
            __('SWJS.lang='.SWLanguage::makeLangJS());

		break;

		//引入编辑器
		case 'swjs_editor':

			//设置编辑器根路径，引入编辑器
			$out.=@file_get_contents(SW::path('admin.js.editor.xheditor','js'))."\n";

		break;

	}

	//执行读取操作
	if(@file_exists($path)){
				
		$out.=@file_get_contents($path)."\n";
	
	}

}

//输出
__($out);
ob_end_flush();

?>
