<?php

defined('SW_') or die('Access Error');

class SWXmlrpcClient{

	function __construct(){
		
		//初始化对象属性
		$this->method='';
		$this->server='';
		$this->para=array();
		$this->timeout=3;
		$this->debug=0;

		$this->faultCode=0;
		$this->faultString='';
		$this->response=array();

	}
	
	/*=====================================================================
	* 发送xml-rpc请求
	* 返回一个xmlrpcresp对象为服务器响应
	======================================================================*/
	function call(){
		
		//创建xmlrpcmsg对象
		$objMsg=new xmlrpcmsg($this->method,$this->para);
		
		//创建xmlrpc_client对象
		$objClient=new xmlrpc_client($this->server);
		$objClient->setDebug($this->debug);
		
		//发送请求
		$objResp=$objClient->send($objMsg,$this->timeout);

		$this->faultCode=$objResp->faultCode();
		$this->faultString=$objResp->faultString();

		if($this->faultCode){

			return false;

		}else{

			return $objResp->value();

		}
		
	}
	
	/*=====================================================================
	* pingback.ping
	======================================================================*/
	function pingback_ping($arr){

		$this->method='pingback.ping';
		$this->server=$arr['server'];
		
		$this->para=array();
		
		$this->para[]=new xmlrpcval($arr['sourceURI']);
		$this->para[]=new xmlrpcval($arr['targetURI']);

		$resp=$this->call();
		return $resp?true:false;

	}

}

?>