<?php

defined('SW_') or die('Access Error');

class SWMysql{
    
	//数据库服务器
    var $host='localhost';

	//数据库名称
	var $database='';

	//数据库用户名
	var $user='';

	//数据库用户密码
	var $pass='';
	
	//最后操作的Id
	var $lastInsertId=0;
	
	//类内部链接对象
	var $conn=null;

	//静态属性：数据库操作次数
	static $queryCount=0;

	//静态属性：数据库操作语句记录
	static $queryLog=array();
	
	/*=====================================================================
	* 数据库连接方法
	======================================================================*/
	private function open(){
		
		try{
			
			$this->conn=mysql_connect($this->host,$this->user,$this->pass);
			mysql_select_db($this->database,$this->conn);

			//设置数据库使用utf-8编码
			mysql_unbuffered_query("set names utf8");
			
		}catch(Exception $e){
			
			die('Connection Error!!!');
			
		}
		
	}
	
	/*=====================================================================
	* 数据库连接关闭方法
	======================================================================*/
	private function close(){
		
		try{
			
			mysql_close($this->conn);
			
		}catch(Exception $e){
			
			die('Connection Error!!!');
			
		}
		
	}
	
	/*=====================================================================
	* 执行查询语句，返回一个查询结果数组
	======================================================================*/
	public function query($sql){
		
		$this->open();
		
		$result=mysql_query($sql);
		$this->rows=mysql_num_rows($result);
		
		$row=array();
		while($row[]=mysql_fetch_array($result,MYSQL_ASSOC)){}

		//删除最后一个空行
		array_pop($row);
		
		mysql_free_result($result);

		$this->close();
		
		//递增查询次数
		self::$queryCount++;
		
		//添加查询日志
		array_push(self::$queryLog,$sql);
		
		return $row;
		
	}
	
	/*=====================================================================
	* 执行查询语句，返回一个查询结果数组
	======================================================================*/
	public function execute($sql){
		
        $this->open();

		mysql_query($sql);

		//获取执行影响的行数
		$row=mysql_affected_rows();
		
		//获取最后插入或者修改的Id值
		$this->lastInsertId=mysql_insert_id();

		$this->close();
		
		//递增查询次数
		self::$queryCount++;

		//添加查询日志
		array_push(self::$queryLog,$sql);
		
		return $row;
		
    }
    
	/*=====================================================================
	* 获取指定表中所有字段的名称和属性
	======================================================================*/
    public function getFields($table){
    	
		$this->open();
		
		$fields=mysql_list_fields($this->database,$table,$this->conn);

		//表内字段数量
		$cols=mysql_num_fields($fields);

		$result=array();

		for($i=0;$i<$cols;$i++){
			$result[]=array(

				//字段名称
				'name'=>mysql_field_name($fields,$i),
				
				//字段数据类型
				'type'=>mysql_field_type($fields,$i),

				//字段长度
				'len'=>mysql_field_len($fields,$i),

				//字段的Flag值
				'flags'=>mysql_field_flags($fields,$i)
			);
		}
		
		$this->close();
		
		//递增查询次数
		self::$queryCount++;

		//添加查询日志，操作为获取表中字段
		array_push(self::$queryLog,'Get Fields From ['.$table.']');
		
		return $result;
		
    }
    
    /*=====================================================================
	* 检查表是否存在
	======================================================================*/
    public function tableExists($table){
        
        $this->open();
        //查询数据库中所有表
		$result=mysql_query("SHOW TABLES FROM ".$this->database);
        
        //如果匹配，返回true
		while($row=mysql_fetch_array($result)){
            if($row[0]==$table) return true;
        }
        
        return false;
    }
    
}

?>
