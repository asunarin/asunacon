<?php

defined('SW_') or die('Access Error');

class SWPingback extends SWComment{
	
	function __construct(){
		
		//初始化父类
		parent::__construct();
		
		//设置默认值
		$this->type=2;
		
		//pingback属性
		$this->sourceURI='';
		$this->targetURI='';
		
	}
	
	/*=====================================================================
	* 重写setAttr方法
	======================================================================*/
	function setAttr($res=array()){
		
		parent::setAttr($res);
		
		$this->sourceURI=$this->url;
		
	}
	
	/*=====================================================================
	* 重写add方法
	======================================================================*/
	function add(){
		
		$this->email='';
		$this->url=$this->sourceURI;
		
		parent::add();
		
	}
	
	/*=====================================================================
	* 通过postid和url地址获取该文章的pingback
	* 同一个地址的pingback只能对同一篇文章发一次
	======================================================================*/
	function getPingback($postid,$url){
		
		$sql="select * from ".$this->tableName." where postid=".$postid." and url='".$url."' and type=2 limit 0,1 ;";
		$db=SW::getDb();
		$res=$db->query($sql);
		
		if(count($res)>0){
			$this->setAttr($res[0]);
		}else{
			$this->setAttr();
		}
		
	}
	
	/*=====================================================================
	* 发送pingback
	======================================================================*/
	function sendPingback(){

		//首先获取文章对象
		$objPost=$this->getPost();
		if(!$objPost->id) return false;

		$sourceURI=SWFunc::checkUrl($this->sourceURI);
		$targetURI=SWFunc::checkUrl($this->targetURI);

		//检查地址是否已经发过
		if($objPost->hasLogPingback($targetURI)) return false;
		
		//无论成功与否，都保存发送列表
		if($targetURI) $objPost->addLogPingback($targetURI);
		
		//获取xml-rpc地址
		$server=SWFunc::checkUrl($this->findPoint());
		
		if($server && $sourceURI && $targetURI){

			//创建客户端参数
			$arr=array(
				'server'=>$server,
				'sourceURI'=>$this->sourceURI,
				'targetURI'=>$this->targetURI
			);

			//执行发送方法
			$objRpc=new SWXmlrpcClient();
			$res=$objRpc->pingback_ping($arr);

			return $res;

		}else{

			return false;
		
		}
	
	}
	
	/*=====================================================================
	* 寻找页面中的pingback标记，并返回xmlrpc地址
	======================================================================*/
	function findPoint(){
				
		$server='';
			
		//创建snoopy对象
		$snoopy = new Snoopy;
		$snoopy->timed_out=3;
		
		if($snoopy->fetch($this->targetURI)){
		
			//匹配header
			if(!empty($snoopy->headers)){
				foreach($snoopy->headers as $header){
					if(preg_match('/^X-Pingback: (\S*)/im',$header,$matches)){
						$server=$matches[1];
						break;
					}
				}
			}
			
			//如果没有匹配上header，那么匹配link标签
			if(!$server){
				if(preg_match('/<link rel="pingback" href="([^"]+)" ?\/?'.'>/is', $snoopy->results, $matches)) $server = $matches[1];
			}
			
		}
		
		return SWFunc::checkUrl($server);
	
	}
	
	/*=====================================================================
	* 检查这个地址是不是已经对这篇文章发送过pingback了
	======================================================================*/
	function isPinged(){

		$sql="select count(id) as c from ".$this->tableName." where postid=".$this->postid." and url='".$this->sourceURI."' and type=2 ;";
		$db=SW::getDb();
		$res=$db->query($sql);
		
		return $res[0]['c']>0?true:false;
		
	}
	
	/*=====================================================================
	* 通过sourceURI获取客户端的信息，包括title，excerpt等
	* 这个是服务端方法，返回一个包含title和excerpt的数组
	======================================================================*/
	function getSourceContent(){
				
		//创建snoopy对象
		$snoopy = new Snoopy;
		$snoopy->timed_out=3;
		
		if($snoopy->fetch($this->sourceURI)){
			
			//匹配title
			preg_match('/<title>([^<]*?)<\/title>/is', $snoopy->results, $matches);
			$this->author=empty($matches[1])?'':addslashes(SWFunc::closeHtml($matches[1],200));
			
			//匹配包含链接的内容
			$this->content='';
			preg_match('/<body[^>]*>(.+)<\/body>/is', $snoopy->results, $matches);
			$source_content=empty($matches[1])?'':$matches[1];
			if($source_content){
				
				//首先找到链接
				if(preg_match('%<a[^>]*?href\\s*=\\s*("|\'|)'.$this->targetURI.'\\1[^>]*?'.'>(.+?)</a>%ius', $source_content, $matches)){
					
					$myLink=$matches[0];

					//把链接替换成标识符
					$source_content=str_replace($myLink, '{targetURI}', $source_content);

					//关闭所有html标签
					$source_content=SWFunc::closeHtml($source_content);

					//替换换行等符号使之成为一行
					$source_content=preg_replace('/[\s\r\n\t]+/', ' ', $source_content );
					$source_content=preg_replace('/\s{2,}/is', ' ', $source_content);
					
					//获取连接上下文
					if(preg_match('/.{0,100}?\{targetURI\}.{0,100}/ius', $source_content,$excerpt)) {
						$source_content=str_replace('{targetURI}',$myLink,$excerpt[0]);
						$this->content='[...] '.addslashes($source_content).' [...]';
					}

				}

			}
			
			return true;
		
		}
		
		return false;
		
	}
	
}

?>
