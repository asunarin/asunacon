<?php

defined('SW_') or die('Access Error');

/*=====================================================================
* XML-RPC 服务端类，所有方法和属性都为静态
======================================================================*/
class SWXmlrpc{

	//方法数组
	static $arrMethod=array();
	
	/*=====================================================================
	* 发送xml-rpc响应
	======================================================================*/
	function response(){
		
		//执行server任务
		$server = new xmlrpc_server(self::$arrMethod,false);
		$server->response_charset_encoding='utf-8';
		$server->setDebug(0);
		$server->service();

	}
	
	/*=====================================================================
	* 注册server方法
	======================================================================*/
	function addMethods($method,$arr){
		
		self::$arrMethod[$method]=$arr;

	}
	
	/*=====================================================================
	* 获取所有方法文件名
	* 目录：api/methods/
	======================================================================*/
	function getMethodFiles(){
		
		$arrFiles=array();

		$arr=SWFunc::getFiles(SW::dirPath('framework.xmlrpcs'));
		foreach($arr as $value){
			array_push($arrFiles,$value['name']);
		}

		return $arrFiles;

	}
	
}

?>