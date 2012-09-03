<?php

defined('SW_') or die('Access Error');

class SWTag extends SWTable{

	function __construct(){
		
		//设置对象数据表名
		$this->tableName=TBL_TAG;
		
		//设置对象数据表
		$this->table=array(
			'id'=>array('type'=>'int','default'=>0),
			'postid'=>array('type'=>'int','default'=>0),
			'name'=>array('type'=>'string','default'=>'')
		);
		
		//根据数据表获取对象属性
		$this->setAttr();

	}
	
	/*=====================================================================
	* 重写save方法
	* 需要先判断该关键字在此文章中是否存在
	======================================================================*/
	function save(){
				
		//首先判断该关键在在此文章中是否存在
		$sql="select * from ".$this->tableName." where postid=".$this->postid." and name='".$this->name."' limit 0,1 ;";
		$db=SW::getDb();
		$res=$db->query($sql);
		
		//如果不存在此标签，才能进行操作
		if(count($res)<1){
			
			parent::save();
			
		}

	}
	
	/*=====================================================================
	* 给一个文章通过字符串保存多个标签
	* 该方法会首先删除所有该postid下的标签，然后插入
	======================================================================*/
	function saveByPostid($postid,$arrName){
		
		if($postid>0){
			
			//首先删除该文章所有标签
			$this->deleteByPostid($postid);
						
			foreach($arrName as $value){
				
				$objTmp=new self();
				$objTmp->postid=$postid;
				$objTmp->name=$value;
				$objTmp->save();
				
			}
			
		}
		
	}
	
	/*=====================================================================
	* 删除一个分章下的所有标签
	======================================================================*/
	function deleteByPostid($postid){
		
		if($postid>0){
			
			$sql="delete from ".$this->tableName." where postid=".$postid." ;";
			$db=SW::getDb();
			$res=$db->execute($sql);
			
		}
	
	}
	
	/*=====================================================================
	* 根据标签名获取postid，并格式化postid字符串
	======================================================================*/
	function getPostidsByName($name,$split=','){
		
		$strRe='';
		
		$arrTag=$this->getMore(array('name'=>$name));
		
		if(count($arrTag)>0){
			
			$arrTmp=array();
			
			foreach($arrTag as $value){
				
				$arrTmp[]=$value->postid;
			
			}
			
			$strRe=implode($split,$arrTmp);
			
		}
		
		return $strRe;
		
	}

	/*=====================================================================
	* 根据名称修改所有标签
	* 如果标签重复，则返回错误
	======================================================================*/
	function editByName($name,$changeTo){
		
		//首先查询是否重复
		$sql="select * from ".$this->tableName." where name='".$changeTo."' limit 0,1 ;";
		$db=SW::getDb();
		$res=$db->query($sql);
		if($res) return false;

		//更新
		$sql="update ".$this->tableName." set name='".$changeTo."' where name='".$name."' ;";
		$res=$db->execute($sql);
		return true;

	}

	/*=====================================================================
	* 根据名称删除所有标签
	======================================================================*/
	function deleteByName($name){
		
		$sql="delete from ".$this->tableName." where name='".$name."' ;";
		$db=SW::getDb();
		$res=$db->execute($sql);

	}
	
	/*=====================================================================
	* 获取所有不重复的tag，并且计算count
	* 返回值为一个数组，num为该标签个数
	======================================================================*/
	public static function getAll($order=array(),$num=0){
		
		$objTag=new self();
		
		//排序条件
		$strOrder='id asc';
		if($order){
			$arrTmp=array();
			foreach($order as $key=>$value){
				$arrTmp[]=$key.' '.$value;
			}
			$strOrder=implode(',',$arrTmp);
		}
		
		//用group by分组查询
		$sql="select name,count(id) as num from ".$objTag->tableName." group by name order by ".$strOrder." ".($num>0?'limit 0,'.$num:'')." ;";
		$db=SW::getDb();
		$res=$db->query($sql);
		
		//获取标签权重
		if($res){
			$max=0;
			foreach($res as $key=>$value){
				$max=max($max,$value['num']);
			}
			
			for($i=0;$i<count($res);$i++){
				//权重算法from habari：round( 10 * log($count + 1) / log($max + 1.01) );
				$res[$i]['weight']=round( 10 * log($res[$i]['num'] + 1) / log($max + 1.01) ); 
			}
		}

		return $res;

	}

}

?>
