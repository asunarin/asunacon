<?php

defined('SW_') or die('Access Error');

class SWPage extends SWTable{

	function __construct(){
		
		//设置对象数据表名
		$this->tableName=TBL_PAGE;
		
		//设置对象数据表
		$this->table=array(
			'id'=>array('type'=>'int','default'=>0),
			'parentid'=>array('type'=>'int','default'=>0),
			'parentmap'=>array('type'=>'string','default'=>''),
			'title'=>array('type'=>'string','default'=>''),
			'content'=>array('type'=>'string','default'=>''),
			'alias'=>array('type'=>'string','default'=>''),
			'posttime'=>array('type'=>'int','default'=>time()),
			'edittime'=>array('type'=>'int','default'=>time()),
			'state'=>array('type'=>'int','default'=>0),
			'flag'=>array('type'=>'int','default'=>0),
			'ordernum'=>array('type'=>'int','default'=>0),
			'onmenu'=>array('type'=>'int','default'=>0)
		);
		
		//根据数据表获取对象属性
		$this->setAttr();

	}
	
	/*=====================================================================
	* 根据别名获取页面属性，同getById
	======================================================================*/
	public function getByAlias($alias){
		
		$sql="select * from ".$this->tableName." where alias='".$alias."' limit 0,1 ;";
		$db=SW::getDb();
		$res=$db->query($sql);
		
		if(count($res)>0){
			
			$this->setAttr($res[0]);
			return true;
			
		}else{
			
			$this->setAttr();
			return false;
			
		}
		
	}

	/*=====================================================================
	* 重写save方法
	* 页面的alias不能重复
	* 生成父页面路径ParentMap
	* 生成所有子页面对象的ParentMap
	======================================================================*/
	function save(){

		$sql="select * from ".$this->tableName." where alias='".$this->alias."' and id<>".$this->id." limit 0,1 ;";
		$db=SW::getDb();
		$res=$db->query($sql);
		
		//如果不重复，才能进行操作
		if(count($res)<1){
			
			$this->getParentMap();
			
			parent::save();
			
			//查询生成所有子页面parentmap，这个必须在父页面保存之后
			$arrChildren=$this->getMore(array('allChildren'=>$this->id),array('id'=>'asc'));
			foreach($arrChildren as $child){
				$child->getParentMap();
				$child->editSingle('parentmap');
			}
		
		//如果重复了，那么设置id为0
		}else{
			
			$this->id=0;

		}

	}
	
	/*=====================================================================
	* 页面逻辑删除方法，其实就是修改flag属性
	* $resume为真时为恢复文章，即设置flag=1
	======================================================================*/
	function trash($resume=false){

		if($this->id>0){
			
			$this->flag=$resume?'1':'0';
			$this->editSingle('flag');

		}

	}
	
	/*=====================================================================
	* 重写delete方法
	* 同时删除子页面
	======================================================================*/
	public function delete(){
		
		$sql="delete from ".$this->tableName." where id=".$this->id." or FIND_IN_SET('".$this->id."', ".$this->tableName.".parentmap);";
		$db=SW::getDb();
		$res=$db->execute($sql);
		
	}
	
	/*=====================================================================
	* 获取父页面的对象
	* 如果未获取，返回NULL
	======================================================================*/
	function getParent(){

		if($this->parentid>0){
			
			$objTml=new $this();
			
			if($objTml->getById($this->parentid)){
				return $objTml;
			}else{
				return null;
			}

		}

	}
	
	/*=====================================================================
	* 生成parentmap，父页面路径
	* 实例：2,4,25
	======================================================================*/
	function getParentMap(){
		
		//获取父页面对象
		$objParent=$this->getParent();

		if($objParent){
			
			$this->parentmap=trim($objParent->parentmap.','.$this->parentid,',');

		}else{
			
			$this->parentmap='';
			
		}

	}

	/*=====================================================================
	* 获取页面级数
	======================================================================*/
	function getLevel(){
		
		if(!$this->parentmap) return 0;
		
		$arrTmp=explode(',',$this->parentmap);
		
		return count($arrTmp);

	}
	
	/*=====================================================================
	* 重写condition方法
	* 设置查询语句的条件（where），具体见函数内逻辑
	======================================================================*/
	function condition($arr){
		
		//默认没有筛选的情况下，flag为1
		$strRe='flag=1';
		
		if(is_array($arr)){
						
			$arr1=array();

			//根据id筛选（多个）,这个是绝对的，设置了这个就忽略了其他条件
			if(isset($arr['ids'])){
				
				$arr['ids']=$this->sqlValue($arr['ids'],'int',',');
				$strRe=$this->tableName.".id in (".$arr['ids'].")";
				
			//选出所有可选的父页面，传入要获取父页面的pageid,这个是绝对的，设置了这个就忽略了其他条件
			//其实就是非自身和其子页面的所有页面
			}elseif(isset($arr['parentsCanBe'])){
			
				$arr['parentsCanBe']=SWFunc::checkInt($arr['parentsCanBe']);
				$strRe=$this->tableName.".id<>".$arr['parentsCanBe']." and ".$this->tableName.".state=1 and ".$this->tableName.".flag=1 and not FIND_IN_SET('".$arr['parentsCanBe']."', ".$this->tableName.".parentmap)";
				
			//获取所有子页面，传如父页面id,这个是绝对的，设置了这个就忽略了其他条件
			//这里不附加条件，不管子页面是什么状态都会获取
			}elseif(isset($arr['allChildren'])){
			
				$arr['allChildren']=SWFunc::checkInt($arr['allChildren']);
				$strRe=$this->tableName.".id<>".$arr['allChildren']." and FIND_IN_SET('".$arr['allChildren']."', ".$this->tableName.".parentmap)";
			
			}else{
			
				//根据state筛选（多个）
				if(isset($arr['state'])){
					
					$arr['state']=$this->sqlValue($arr['state'],'int',',');
					$arr1[]=$this->tableName.".state in (".$arr['state'].")";
					
				}
				
				//根据搜索筛选
				if(isset($arr['search'])){
					
					$arr1[]="(".$this->tableName.".title like ('%".$arr['search']."%') or ".$this->tableName.".content like ('%".$arr['search']."%'))";
					
				}
				
				//根据menu筛选
				if(isset($arr['onmenu'])){
					
					$arr['onmenu']=SWFunc::checkInt($arr['onmenu']);
					$arr1[]=$this->tableName.".onmenu in (".$arr['onmenu'].")";
					
				}
				
				//根据子页面筛选
				if(isset($arr['children'])){
					
					$arr['children']=SWFunc::checkInt($arr['children']);
					$arr1[]=$this->tableName.".id<>".$arr['children']." and FIND_IN_SET('".$arr['children']."', ".$this->tableName.".parentmap)";
					
				}
				
				$arr2=array();
				
				//默认情况下全部都是flag=1的
				if(count($arr1)>0){
					
					$arr1[]=$this->tableName.".flag=1";
					$arr2[]="(".implode(' and ',$arr1).")";
					
				}
				
				//如果包含已删除的
				if(isset($arr['trash'])){
					
					$arr2[]="flag=0";
				
				}
				
				if(count($arr2)>0) $strRe="(".implode(' or ',$arr2).")";
				
			}
			
		}
		
		return $strRe;
		
	}
	
	/*=====================================================================
	* 一个对getMore方法，对返回数组作树状排序
	======================================================================*/
	public function getMoreTree($parentid,$condition=array(),$order=array(),$start=0,$end=0){
		
		$arrPage=$this->getMore($condition,$order,$start,$end);
		
		$arrRe=array();
		
		//排序
		$this->orderTree($parentid,$arrPage,$arrRe);
		return $arrRe;

	}
	
	/*=====================================================================
	* 对文章树状排序
	* P
	* 	C
	* 	C
	* 		CC
	* 	C
	* P
	* 	C
	* 		CC
	* 	C
	======================================================================*/
	public function orderTree($parentid,$arrPage,&$arrRe){
		
		$arrParent=array();
		
		//寻找子节点，装入容器
		foreach($arrPage as $value){
			
			if($value->parentid==$parentid){
				$arrParent[]=$value;
				$arrRe[]=$value;
				$this->orderTree($value->id,$arrPage,$arrRe);
			}
			
		}
		
	}
	
}

?>
