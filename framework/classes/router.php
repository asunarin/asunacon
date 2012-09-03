<?php

defined('SW_') or die('Access Error');

/*=====================================================================
* 路由类，处理页面参数和定义重写规则等
======================================================================*/
class SWRouter{
	
	//页面请求参数
	public $request=array();
	
	//当前请求的页面
	public $requestPage='';
	
	/*=====================================================================
	* 重写规则方法
	* 这个方法比较复杂，请看下面说明
	* url是指生成这个连接的格式（正向解析，通过参数生成连接地址）
	* regex是指这个地址的正则匹配（反向解析，通过地址获取参数值）
	* args是这个匹配式中各项对应的变量名
	======================================================================*/
	private function rewriteRule(){

		return array(
			
			array('name'=>'index','url'=>'','regex'=>'%^\/?$%','args'=>array()),

			array('name'=>'index','url'=>'p[pagenum]/','regex'=>'%^p(\d+)\/?$%','args'=>array('pagenum')),
			
			array('name'=>'list','url'=>'','regex'=>'%list\/?$%i','args'=>array()),

			array('name'=>'list','url'=>'list/p[pagenum]/','regex'=>'%^list\/p(\d+)\/?$%','args'=>array('pagenum')),

			array('name'=>'search','url'=>'search/[searchstring]/','regex'=>'%^search\/([^\/]+?)\/?$%','args'=>array('searchstring')),

			array('name'=>'search','url'=>'search/[searchstring]/p[pagenum]/','regex'=>'%^search\/([^\/]+?)\/p(\d+)\/?$%','args'=>array('searchstring','pagenum')),

			array('name'=>'search','url'=>'archive/[year]/[month]/','regex'=>'%^archive\/(\d+?)\/(\d+?)\/?$%','args'=>array('year','month')),

			array('name'=>'search','url'=>'archive/[year]/[month]/p[pagenum]/','regex'=>'%^archive\/(\d+?)\/(\d+?)\/p(\d+?)\/?$%','args'=>array('year','month','pagenum')),

			array('name'=>'search','url'=>'tag/[tagname]/','regex'=>'%tag\/([^\/]+?)\/?$%','args'=>array('tagname')),

			array('name'=>'search','url'=>'tag/[tagname]/p[pagenum]/','regex'=>'%tag\/([^\/]+?)\/p(\d+?)\/?$%','args'=>array('tagname','pagenum')),
			
			array('name'=>'post','url'=>'post/[postid]/','regex'=>'%^post\/(\d+)\/?$%i','args'=>array('postid')),

			array('name'=>'page','url'=>'page/[pagename]/','regex'=>'%page\/([^\/]+?)\/?$%i','args'=>array('pagename')),
            
            array('name'=>'archives','url'=>'archives/','regex'=>'%archives\/?$%i','args'=>array()),

			array('name'=>'actions','url'=>'actions/[action]/','regex'=>'%^actions\/([^\/]+?)\/?$%i','args'=>array('action'))
			
		);

	}
	
	/*=====================================================================
	* 获取地址的请求部分，不包含程序根目录外的路径
	* http://localhost/swan/123/23/3/aaa.html?p=2
	* 返回值:123/23/3/aaa.html?p=2
	======================================================================*/
	private function getStartUrl($url){
		
		$start_url='';

		$arrUrl=parse_url($url);

		if($arrUrl){

			if(!empty($arrUrl['path'])) $start_url.=$arrUrl['path'];
			if(!empty($arrUrl['query'])) $start_url.='?'.$arrUrl['query'];
		
		}
		
		//网址路径
		$baseUrl=SW::urlPath('',true);
		
		if($baseUrl!='/'){
			$start_url=str_replace($baseUrl,'',$start_url);
			$start_url=str_replace('\\','/',$start_url);
			$start_url=str_replace('//','/',$start_url);
		}

		return ltrim($start_url,'/');

	}
	
	/*=====================================================================
	* 获取当前页面的请求部分，返回值类似于“getStartUrl”
	======================================================================*/
	private function getCurrentUrl(){
		
		//整个请求的地址（不包含域名）
		$start_url = ( isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI']	: $_SERVER['SCRIPT_NAME'] .	( isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '') . ( (isset($_SERVER['QUERY_STRING']) && ($_SERVER['QUERY_STRING'] != '')) ? '?' . $_SERVER['QUERY_STRING'] : ''));
		
		//网址路径
		$baseUrl=SW::urlPath('',true);
		
		if($baseUrl!='/'){
			//这里只能替换掉开头的baseurl
			$url=preg_replace('%^'.$baseUrl.'%i','',$start_url);
			$url=str_replace('\\','/',$url);
			$url=str_replace('//','/',$url);
		}else{
			$url=$start_url;
		}

		return ltrim($url,'/');
	}

	/*=====================================================================
	* 根据地址获取页面请求参数
	* 如果不传入$url那么就根据当前页面地址获取
	======================================================================*/
	final public function getRequest($url=''){

		//如果未开启重写
		if(!SW::getOption('rewrite')){

			$this->requestPage=SW::request('sw','get')?SW::request('sw','get'):'index';
			$this->request=SW::request('','get');

		}else{
		
			$strUrl=$url?$this->getStartUrl($url):$this->getCurrentUrl();

			//循环搜索对应的规则
			$rules=$this->rewriteRule();
			$arrMatch=array();
			$arrRule=array();
			foreach($rules as $rule){
				
				if(preg_match($rule['regex'],$strUrl,$arrMatch)){
					$arrRule=$rule;
					break;
				}

			}
			
			//获取请求的参数
			$arrRequest=array();
			if($arrRule){
				for($i=0;$i<count($arrRule['args']);$i++){
					$arrRequest[$arrRule['args'][$i]]=SWFunc::filterString(urldecode($arrMatch[$i+1]));
				}

				//设置对象属性
				$this->requestPage=$arrRule['name'];
				$this->request=$arrRequest;

			}else{
				$this->requestPage='';
				$this->request=array();
			}
		
		}

	}
	
	/*=====================================================================
	* 根据参数获取URL地址
	* encode表示是否要对参数进行url编码
	======================================================================*/
	final public function getUrl($requestPage='',$request=array(),$encode=true){

		//如果未传入requestPage，那么就读取本对象的参数
		if(!$requestPage){
			$requestPage=$this->requestPage;
			$request=$this->request;
		}

		$strUrl=SW::getOption('url');
		
		//如果未开启重写
		if(!SW::getOption('rewrite')){

			$arrPlus=array();
			foreach($request as $key=>$value) $arrPlus[]=$key.'='.$value;
			$strPlus=implode('&',$arrPlus);
			$strUrl.='?sw='.$requestPage.($strPlus?'&'.$strPlus:'');

		}else{
			
			//循环搜索对应的规则，可能会有多个
			$rules=$this->rewriteRule();
			$arrTmp=array();
			foreach($rules as $rule){
				
				if($rule['name']==$requestPage){
					$arrTmp[]=$rule;
				}

			}

			//循环匹配上的规则，用传入参数判断是哪条
			$urlTmp='';
			foreach($arrTmp as $rule){
				$flag=false;
				foreach($request as $key=>$value){
					if(!in_array($key,$rule['args'])){
						$flag=false;
						break;
					}else{
						$flag=true;
					}
				}
				if($flag || (!$rule['args'] && !$request)){
					$urlTmp=$rule['url'];
					break;
				}
			}

			//替换规则中的参数
			foreach($request as $key=>$value){
				
				$urlTmp=str_replace('['.$key.']',($encode?urlencode($value):$value),$urlTmp);

			}

			$strUrl.=$urlTmp;

		}

		return $strUrl;

	}

}

?>
