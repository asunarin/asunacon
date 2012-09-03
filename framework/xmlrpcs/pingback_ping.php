<?php
defined('SW_') or die('Access Error');

//注册这个方法
SWXmlrpc::addMethods('pingback.ping',array(
	'function'=>'pingback_ping::server',
	'signature'=>pingback_ping::$signature,
	'docstring'=>pingback_ping::$docstring
));
	
class pingback_ping{

	//文档
	static $docstring="
	*=====================================================================
	* pingback.ping
	* pingback服务器端，该接口文档见：
	* http://hixie.ch/specs/pingback/pingback-1.0
	*
	* 传入参数：sourceURI, targetURI
	* 返回参数：'Done'
	*
	* 错误代码：
	* 0: A generic fault code. Servers MAY use this error code instead of any of the others if they do not have a way of determining the correct fault code.
	* 16: The source URI does not exist.
	* 17: The source URI does not contain a link to the target URI, and so cannot be used as a source.
	* 32: The specified target URI does not exist. This MUST only be used when the target definitely does not exist, rather than when the target may exist but is not recognised. See the next error.
	* 33: The specified target URI cannot be used as a target. It either doesn't exist, or it is not a pingback-enabled resource. For example, on a blog, typically only permalinks are pingback-enabled, and trying to pingback the home page, or a set of posts, will fail with this error.
	* 48: The pingback has already been registered.
	* 49: Access denied.
	* 50: The server could not communicate with an upstream server, or received an error from an upstream server, and therefore could not complete the request. This is similar to HTTP's 402 Bad Gateway error. This error SHOULD be used by pingback proxies when propagating errors. 
	======================================================================*
	";

	//参数格式
	static $signature=array(array("string","string","string"));

	//服务端方法
	function server($m){

		//判断是否开启接受pingback
		if(!SW::getOption('receivepingback')) return new xmlrpcresp(0,49,'Access denied');
		
		//接收参数
		$sourceURI=SWFunc::checkUrl(php_xmlrpc_decode($m->getParam(0)));
		$targetURI=SWFunc::checkUrl(php_xmlrpc_decode($m->getParam(1)));
		
		if(!$sourceURI) return new xmlrpcresp(0,16,'The source URI does not exist');
		if(!$targetURI) return new xmlrpcresp(0,32,'The specified target URI does not exist. This MUST only be used when the target definitely does not exist, rather than when the target may exist but is not recognised');
		
		//创建pingback对象
		$objPb=new SWPingback();
		$objPb->sourceURI=$sourceURI;
		$objPb->targetURI=$targetURI;

		//获取文章id
		//创建路由对象，用于获取文章id
		$objRouter=new SWRouter();
		$objRouter->getRequest($targetURI);
		if($objRouter->requestPage=='post') $objPb->postid=$objRouter->request['postid'];

		if(!$objPb->postid) return new xmlrpcresp(0,33,'The specified target URI cannot be used as a target. It either doesn\'t exist, or it is not a pingback-enabled resource. For example, on a blog, typically only permalinks are pingback-enabled, and trying to pingback the home page, or a set of posts, will fail with this error');
		
		//判断文章是否可评论
		$objPost=$objPb->getPost();
		if(!$objPost->id || !$objPost->allowcomment || $objPost->state!=1 || $objPost->flag!=1) return new xmlrpcresp(0,33,'The specified target URI cannot be used as a target. It either doesn\'t exist, or it is not a pingback-enabled resource. For example, on a blog, typically only permalinks are pingback-enabled, and trying to pingback the home page, or a set of posts, will fail with this error');
		
		//判断这个pingback是否已经存在了
		if($objPb->isPinged()) return new xmlrpcresp(0,48,'The pingback has already been registered');
		
		//获取客户端内容
		if(!$objPb->getSourceContent()) return new xmlrpcresp(0,16,'The source URI does not exist');
		if(!$objPb->author || !$objPb->content) return new xmlrpcresp(0,17,'The source URI does not contain a link to the target URI, and so cannot be used as a source');
		
		//如果以上都没问题了，那么保存pingback
		$objPb->id=0;
		$objPb->ip=SWFunc::getIp();
		$objPb->posttime=time();
		$objPb->state=SW::getOption('checkcomment')?0:1;
		
		$objPb->add();

		//返回响应信息
		return new xmlrpcresp(new xmlrpcval('Done'));

	}

}

?>
