<?php

defined('SW_') or die('Access Error');

class SWTrackback extends SWComment{
	
	function __construct(){
		
		//初始化父类
		parent::__construct();
		
		//设置默认值
		$this->type=3;
		
		//trackback的属性
		$this->tbUrl = '';
		$this->blog_name = '';
		$this->url = '';
		$this->title = '';
		$this->expert = '';
		
	}
	
	/*=====================================================================
	* 重写setAttr方法
	======================================================================*/
	function setAttr($res=array()){
		
		parent::setAttr($res);
		
		$this->blog_name=$this->author;
		$this->url=$this->url;
		$this->expert=$this->content;
		
		//这里的title属性将获取该post对象的title
		$objPost=$this->getPost();
		$this->title=$objPost->title;
		
	}
	
	/*=====================================================================
	* 重写add方法
	======================================================================*/
	function add(){
		
		$this->author=$this->blog_name;
		$this->email='';
		$this->url=$this->url;
		$this->content=$this->expert;
		
		parent::add();
		
	}
	
	/*=====================================================================
	* 发送trackback
	======================================================================*/
	function sendTrackback(){
		
		if(SWFunc::checkUrl($this->tgUrl)){
			
			$tb=new Trackback();
			
			//发送请求
			return $tb->ping($this->tbUrl,$this->url,$this->blog_name,$this->title,$this->expert);
		
		}
		
	}

	/*=====================================================================
	* 发送trackback请求的响应
	======================================================================*/
	function sendResponse($success=false, $err_response=""){
		
		$tb=new Trackback();

		//XML输出头
		header('Content-Type: text/xml; charset=utf-8');
		
		//发送响应
		__($tb->recieve($success,$err_response));

		//注意，发送响应之后要终止页面
		exit();
		
	}
	
	
	/*=====================================================================
	* 检查这个地址是不是已经对这篇文章发送过trackback了
	======================================================================*/
	function isPinged(){

		$sql="select count(id) as c from ".$this->tableName." where postid=".$this->postid." and url='".$this->url."' and type=3 ;";
		$db=SW::getDb();
		$res=$db->query($sql);
		
		return $res[0]['c']>0?true:false;
		
	}
	
}

?>