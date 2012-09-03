<?php

defined('SW_') or die('Access Error');

/*=====================================================================
* 继承该类的子类必须有且仅有一张数据表
* 如果并不是数据表对象，请不要继承该类
* 同时该数据表必须以id作为主键
======================================================================*/
class SWTable{
	
	//这个属性用于设置对象数据表名，所以强制规定一个对象不可以涉及操作一张以上的表
	protected $tableName='';
	
	//这个对象所对应的数据表的字段
	protected $table=array();
    
	/*=====================================================================
	* 对用作sql查询的变量进行设置
	* $type是数据表属性（$this->table）的$type值
	* 如果不设置$type，变量一律加上单引号
	* $split将分割字符串：aaa,bbb,ccc将返回'aaa','bbb','ccc'
	======================================================================*/
	protected function sqlValue($value,$type='string',$split=''){
		
		$arrTmp=array();
		if($split){
			$arrTmp=explode($split,$value);
		}else{
			$arrTmp[]=$value;
		}
		
		$arrRe=array();
		
		foreach($arrTmp as $value){

			switch($type){
				case 'int':
					$arrRe[]=$value?$value:'0';
					break;
				default:
					$arrRe[]=$value?"'".$value."'":"''";
					break;
			}
		
		}
		
		return implode($split,$arrRe);
		
	}
	
	/*=====================================================================
	* 通过数据表字段设置对象属性
	* $arr可以是一个数据库的select返回对象
	* 这个方法可以保证所有的对象属性都在$this->table范围内
	======================================================================*/
	protected function setAttrTable($arr=array()){
		
		//如果不传入数据，会将$this->table中所有项设置进对象属性
		if(count($arr)<1){
		
			foreach($this->table as $key=>$value){
				
				$this->$key=$value['default'];

			}
		
		//如果传入数据，只设置传入数据对应$this->table键的值
		}else{
			
			foreach($this->table as $key=>$value){
				
				if(isset($arr[$key])) $this->$key=$arr[$key];

			}

		}

	}
	
	/*=====================================================================
	* 获取对象属性的方法，通过sql查询返回数组设置对象属性
	* 注意，这里的$res必须是数据库返回的一维数组($res[0])
	* 与setAttrTable的区别在于这个方法是可扩展重写的
	* 注意，获取对象时的附加操作应写在这个方法而不是getById
	======================================================================*/
	protected function setAttr($res=array()){
		
		$this->setAttrTable($res);
		
	}
	
	/*=====================================================================
	* 根据id将获取对象属性
	* 返回是否获取到
	======================================================================*/
	public function getById($id){
		
		$sql="select * from ".$this->tableName." where id=".$id." limit 0,1 ;";
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
	* 将所有$this->table中的属性添加进表中
	======================================================================*/
	public function add(){
		
		$arrFields=array();
		$arrValues=array();
		
		foreach($this->table as $key=>$value){
			if($key=='id') continue;
			$arrFields[]=$key;
			$arrValues[]=$this->sqlValue($this->$key,$value['type']);
		}
		
		$sql="insert into ".$this->tableName." (".implode(',',$arrFields).") values (".implode(',',$arrValues).") ;";
		$db=SW::getDb();
		$res=$db->execute($sql);

		//同时获得新添的ID
		$this->id=$db->lastInsertId;
		
	}
	
	/*=====================================================================
	* 将所有$this->table中的属性根据id更新进表中
	* $allow为允许更新的字段，为空则全部更新
	======================================================================*/
	public function edit($allow=array()){
		
		$arrUpdate=array();

		if(count($allow)>0){
			
			foreach($allow as $value){

				//如果字段不在table中，跳过
				if(!isset($this->table[$value])) continue;

				//跳过id主键
				if($value=='id') continue;

				$arrUpdate[]=$value.'='.$this->sqlValue($this->$value,$this->table[$value]['type']);
			}

		}else{
			
			foreach($this->table as $key=>$value){

				//跳过id主键
				if($key=='id') continue;

				$arrUpdate[]=$key.'='.$this->sqlValue($this->$key,$value['type']);
			}

		}
		
		$sql="update ".$this->tableName." set ".implode(',',$arrUpdate)." where id=".$this->id." ;";
		$db=SW::getDb();
		$res=$db->execute($sql);
		
	}
	
	/*=====================================================================
	* 根据表中字段名通过id更新字段值
	* 这是$this->edit的简单版，一次只更新单个字段，区别在于这个方法可以更新id字段
	* 注意，更新id字段是危险行为！
	======================================================================*/
	public function editSingle($name){
		
		//字段必须在table中
		if(isset($this->table[$name])){
				
			$sql="update ".$this->tableName." set ".$name."=".$this->sqlValue($this->$name,$this->table[$name]['type'])." where id=".$this->id." ;";
			$db=SW::getDb();
			$res=$db->execute($sql);
			
		}
		
	}
	
	/*=====================================================================
	* 保存方法，id为0则add，否则edit
	* 注意: edit将保存所有字段
	======================================================================*/
	public function save(){
		
		if($this->id>0){
			$this->edit();
		}else{
			$this->add();
		}

		return true;
				
	}
	
	/*=====================================================================
	* 根据id删除表中记录
	======================================================================*/
	public function delete(){
		
		$sql="delete from ".$this->tableName." where id=".$this->id." ;";
		$db=SW::getDb();
		$res=$db->execute($sql);
		
	}

	/*=====================================================================
	* 获取下一个id
	======================================================================*/
	public function getNextId(){
		
		$sql="SHOW TABLE STATUS WHERE Name = '".$this->tableName."' ;";
		$db=SW::getDb();
		$res=$db->query($sql);
		return $res[0]['Auto_increment'];
		
	}
	
	/*=====================================================================
	* 设置查询语句的条件（where），具体见函数内逻辑
	* 返回查询字符串（部分）
	* 注意：这个方法逻辑非常复杂，一般会根据要求在子类中重写该方法！
	======================================================================*/
	public function condition($arr){
		
		//默认没有筛选的情况下，为全部
		$strRe='1=1';
		
		$arr1=array();
		
		foreach($arr as $key=>$value){
			
			$arr1[]=$this->tableName.".".$key." in (".$this->sqlValue($value,$this->table[$key]['type'],',').")";
			
		}
		
		if(count($arr1)>0) $strRe=implode(' and ',$arr1);
		
		return $strRe;
		
	}
	
	/*=====================================================================
	* 根据条件获取所有对象列表并返回
	* 注意，请合理设置查询数量，否则会产生大量数据库查询
	* 可以设置回调函数，回调函数接受和返回一个参数，即返回的数组
	* 回调函数可以书字符串，也可以是数组
	======================================================================*/
	public function getMore($condition=array(),$order=array(),$start=0,$end=0,$callBack=''){
		
		//生成排序字符串
		$arrTmp=array();
		foreach($order as $key=>$value){
			$arrTmp[]=$key.' '.$value;
		}
		if(count($arrTmp)>0){
			$strOrder='order by '.implode(',',$arrTmp);
		}else{
			$strOrder='';
		}
		
		//获取查询字符串
		$strCondition=$this->condition($condition);
		if($strCondition) $strCondition="where ".$strCondition;
		
		//生成查询字符串
		$sql="select * from ".$this->tableName." ".$strCondition." ".$strOrder." ";
		$sql.=(($end-$start)>0?"limit ".$start.",".($end-$start)." ;":" ;");
		
		$db=SW::getDb();
		$res=$db->query($sql);

		$arrTmp=array();
		
		if(count($res)>0){
			
			foreach($res as $value){
				
				//这个用法很经典，大家可以学习
				$objTmp=new $this();

				$objTmp->setAttr($value);

				$arrTmp[]=$objTmp;

			}

		}
		
		//执行回调函数
		if($callBack){
				
			$arrTmp=call_user_func($callBack,$arrTmp);
							
		}
			
		return $arrTmp;

	}
	
	/*=====================================================================
	* 和getMore功能一样，但是返回一个记录条数
	======================================================================*/
	public function getMoreCount($condition=array(),$start=0,$end=0){

		//获取查询字符串
		$strCondition=$this->condition($condition);
		if($strCondition) $strCondition="where ".$strCondition;

		//生成查询字符串
		$sql="select count(*) as morecount from ".$this->tableName." ".$strCondition." ";
		$sql.=(($end-$start)>0?"limit ".$start.",".($end-$start)." ;":" ;");

		$db=SW::getDb();
		$res=$db->query($sql);

		if(count($res>0)) return $res[0]['morecount'];

		return 0;
		
		//return count($this->getMore($condition,array(),$start,$end));
		
	}
}

?>
