<?php

defined('SW_') or die('Access Error');

class SWComment extends SWTable{

	function __construct(){
		
		//设置对象数据表名
		$this->tableName=TBL_COMMENT;
		
		//设置对象数据表
		$this->table=array(
			'id'=>array('type'=>'int','default'=>0),
			'parentid'=>array('type'=>'int','default'=>0),
			'postid'=>array('type'=>'int','default'=>0),
			'content'=>array('type'=>'string','default'=>''),
			'author'=>array('type'=>'string','default'=>''),
			'email'=>array('type'=>'string','default'=>''),
			'url'=>array('type'=>'string','default'=>''),
			'ip'=>array('type'=>'string','default'=>''),
			'posttime'=>array('type'=>'int','default'=>time()),
			'state'=>array('type'=>'int','default'=>0),
			'type'=>array('type'=>'int','default'=>1)
		);
		
		//根据数据表获取对象属性
		$this->setAttr();

	}
	
	/*=====================================================================
	* 评论修改方法
	* 这个方法只修改部分字段，特定字段不会修改
	======================================================================*/
	function commentUpdate(){
		
		//指定只更新这些字段
		$arrAllow=array('content','author','email','url','posttime','state');

		$this->edit($arrAllow);

		return true;

	}
	
	/*=====================================================================
	* 评论状态修改方法，直接设置state值
	* 默认为审核评论通过（state=1）
	======================================================================*/
	function setState($state=1){
		
		$this->state=$state;
		$this->editSingle('state');

	}

	/*=====================================================================
	* 删除一个分章下的所有评论
	======================================================================*/
	function deleteByPostid($postid){
		
		if($postid>0){
			
			$sql="delete from ".$this->tableName." where postid=".$postid." ;";
			$db=SW::getDb();
			$res=$db->execute($sql);
			
		}
	
	}
	
	/*=====================================================================
	* 重写delete方法，递归删除回复
	======================================================================*/
	function delete(){
		
		//首先删除自身
		parent::delete();
		
		//递归删除回复
		$arrComment=$this->getMore(array('replay'=>$this->id));
		foreach($arrComment as $objComment){
			
			$objComment->delete();
			
		}
		
	}
	
	/*=====================================================================
	* 获取评论的级数
	* 请勿传入参数，level参数用来递归寻找父评论
	======================================================================*/
	function getLevel($level=0){
		
		//如果有父评论
		if($this->parentid>0){
			
			$objComment=new $this();
			$objComment->getById($this->parentid);
			
			if($objComment){
				$level++;
				$level=$objComment->getLevel($level);
			}
			
		}
		
		return $level;
		
	}

	/*=====================================================================
	* 重写condition方法
	* 设置查询语句的条件（where），具体见函数内逻辑
	======================================================================*/
	function condition($arr){
		
		//默认没有筛选的情况下全部显示
		$strRe='1=1';
		
		if(is_array($arr)){
						
			$arr1=array();

			//根据id筛选（多个）,这个是绝对的，设置了这个就忽略了其他条件
			if(isset($arr['ids'])){
				
				$arr['ids']=$this->sqlValue($arr['ids'],'int',',');
				$strRe=$this->tableName.".id in (".$arr['ids'].")";
				
			}else{
			
				//查询审核通过的评论，同时包含IP地址与访客相同且时间为2小时内的未审核评论（忽略state）
				if(isset($arr['ip'])){

					$arr['ip']=$this->sqlValue($arr['ip'],'string',',');
					$arr1[]="(".$this->tableName.".state=1 or (".$this->tableName.".state=0 and ".$this->tableName.".ip in (".$arr['ip'].") and ".$this->tableName.".posttime>".(time()-7200)."))";
				
				//根据state筛选（多个）
				}elseif(isset($arr['state'])){
					
					$arr['state']=$this->sqlValue($arr['state'],'int',',');
					$arr1[]=$this->tableName.".state in (".$arr['state'].")";
					
				}
				
				//查询评论下的回复（只限一级，一般用于递归查询中）
				if(isset($arr['replay'])){
					
					$arr['replay']=SWFunc::checkInt($arr['replay']);
					$arr1[]=$this->tableName.".parentid=".$arr['replay'];
				
				//只查询不包含回复的评论
				}elseif(isset($arr['onlyparent'])){
					
					$arr1[]=$this->tableName.".parentid=0";
					
				}

				//根据type筛选（多个）
				if(isset($arr['type'])){
					
					$arr['type']=$this->sqlValue($arr['type'],'int',',');
					$arr1[]=$this->tableName.".type in (".$arr['type'].")";
					
				}
				
				//根据搜索筛选
				if(isset($arr['search'])){
					
					$arr1[]=$this->tableName.".content like ('%".$arr['search']."%')";
					
				}
				
				//根据文章id筛选
				if(isset($arr['postid'])){
					
					$arr['postid']=$this->sqlValue($arr['postid'],'int',',');
					$arr1[]=$this->tableName.".postid in (".$arr['postid'].")";
					
				}

				$strRe=implode(' and ',$arr1);
				
			}
			
		}
		
		return $strRe;
		
	}
	
	/*=====================================================================
	* 获取该评论的父级文章对象
	======================================================================*/
	function getPost(){
		
		$objPost=new SWPost();
		$objPost->getById($this->postid);
		return $objPost;

	}
	
}

?>
