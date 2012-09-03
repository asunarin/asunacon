<?php

defined('SW_') or die('Access Error');

/*=====================================================================
* 设置类型的数据表结构
======================================================================*/
class SWTableOption{
	
	//这个属性用于设置对象数据表名，所以强制规定一个对象不可以涉及操作一张以上的表
	protected $tableName='';
	
	//这个对象所对应的数据表的字段
	protected $table=array();

	/*=====================================================================
	* 根据设置项type返回正确的php变量
	======================================================================*/
	protected function getValue($value,$type='string',$default=''){
		
		switch($type){
			
			case 'string':
				return SWFunc::checkString($value);
			break;

			case 'int':
				return SWFunc::checkInt($value);
			break;
			
			//布尔值兼容数字0，1或字符串false，true
			case 'bool':
				if($value=='false') return false;
				if($value=='true') return true;
				return (SWFunc::checkInt($value)?true:false);
			break;
			
			//json数据对象，如果为空返回一个空数组
			case 'json':
				if(!$value) return array();
				return (json_decode($value,true)); //强制返回数组类型
			break;

		}

	}
	
	/*=====================================================================
	* 获取设置数据
	======================================================================*/
	public function load(){
		
		//获取所有设置记录
		$sql="select * from ".$this->tableName." ;";
		$db=SW::getDb();
		$res=$db->query($sql);
		
		//循环设置所有属性
		foreach($res as $value){
			
			$this->$value['name']=$this->getValue($value['value'],$value['type'],$value['default']);

		}

	}
	
	/*=====================================================================
	* 检查该名称的设置是否存在
	======================================================================*/
	public function nameExists($name){
		
		$sql="select * from ".$this->tableName." where name='".$name."' limit 0,1 ;";
		$db=SW::getDb();
		if($db->query($sql)){
			return true;
		}else{
			return false;
		}

	}

	/*=====================================================================
	* 根据名称添加设置
	* 同时会判断该名称是否已经存在，则进行更新操作
	* 这个方法尽量避免使用，一般在升级系统时才使用
	======================================================================*/
	public function addByName($name,$type='string',$value='',$default=''){
		
		if(!$this->nameExists($name)){

			$sql="insert into ".$this->tableName." (name,type,value,`default`) values ('".$name."','".$type."','".$value."','') ;";
			$db=SW::getDb();
			$db->execute($sql);
			
			//插入完成后load
			$this->load();

		}else{

			//在更新的时候不会更新type，这点是安全的
			$this->$name=$value;
			$this->saveByName($name);

		}

	}
	
	/*=====================================================================
	* 根据名称保存设置，不判断类型（因为数据库中都是text类型）
	======================================================================*/
	public function saveByName($name){
		
		$sql="update ".$this->tableName." set value='".$this->$name."' where name='".$name."' ;";
		$db=SW::getDb();
		$db->execute($sql);

	}
	
}

?>
