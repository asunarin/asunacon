<?php

defined('SW_') or die('Access Error');

/*=====================================================================
* 框架类
======================================================================*/
class SW{
	
	//全局变量数组
	static $arrGlb=array();

	//预处理行为
	static $preAction='';
	
	/*=====================================================================
	* 设置全局变量
	======================================================================*/
	function setVar($strKey,$value){
		SW::$arrGlb[$strKey]=$value;
	}
	
	/*=====================================================================
	* 获取全局变量
	======================================================================*/
	function getVar($strKey){
		return isset(SW::$arrGlb[$strKey])?SW::$arrGlb[$strKey]:false;
	}
	
	/*=====================================================================
	* 将对象放入全局数组中进行缓存
	* 注意，$name必须是类名
	* 这样不用每次使用对象的时候都new一个新的
	* 该方法适用于设置、信息等公共类的对象
	======================================================================*/
	function cacheObject($name){
		
		//首先检查是否已经存入，否则就存入
		if(!SW::getVar($name)) SW::setVar($name,new $name());
		$obj=SW::getVar($name);
		return $obj;

	}

	/*=====================================================================
	* 根据名称获取系统设置值
	======================================================================*/
	function getOption($strKey=''){
		
		//获取对象并设置缓存
		$obj=self::cacheObject('SWOption');

		//如果没有传入键值，那么返回option对象
		if($strKey){
			return $obj->$strKey;
		}else{
			return $obj;
		}

	}
	
	/*=====================================================================
	* 获取文件的绝对路径，一般用于包含文件的时候
	* 可以考虑判断文件是否存在，但是这样有损安全性
	======================================================================*/
	function path($str,$type='php'){
		
		$str=str_replace('.',SW_DS,$str);
        $str=SW_BASE.SW_DS.$str.'.'.$type;
        
        return $str;
        
	}

	/*=====================================================================
	* 获取文件夹的绝对路径，并包含最后的斜杠
	* 注意，这个方法并不判断目录是否存在
	======================================================================*/
	function dirPath($str){
		
		$str=str_replace('.',SW_DS,$str);
        $str=SW_BASE.SW_DS.($str?$str.SW_DS:'');
        
		return $str;
        
	}
	
	/*=====================================================================
	* 把文件的绝对路径转换为url的绝对路径
	* $dir为真时目标是目录，而不是文件
	* $type只有当$dir为否是才有效，表示目标文件类型（见SW::path）
	======================================================================*/
	function urlPath($str,$dir=false,$type='php'){
		
		return SWFunc::getUrlByPath($dir?SW::dirPath($str):SW::path($str,$type));

	}
	
	/*=====================================================================
	* 获取页面请求参数
	* $safe为真的时候将进行sql安全过滤
	======================================================================*/
	function request($name='',$type='get',$safe=true){
		
		$para=null;
		
		//如果开启了magic_quotes_gpc，那么强制屏蔽他
		//目前来看，不管是否开启magic_quotes_gpc，都要事先stripslashes
		
		//if (get_magic_quotes_gpc()) {
		
		$_POST = array_map(array('SWFunc','stripslashes_deep'), $_POST);
		$_GET = array_map(array('SWFunc','stripslashes_deep'), $_GET);
		$_COOKIE = array_map(array('SWFunc','stripslashes_deep'), $_COOKIE);
		$_REQUEST = array_map(array('SWFunc','stripslashes_deep'), $_REQUEST);
		
		//}
		
		switch($type){
			
			//GET请求，如果$name为空，将返回整个$_GET数组
			case 'get':
				if($safe){
					$_GET = array_map(array('SWFunc','addslashes_deep'), $_GET);
				}
				$para=($name?(isset($_GET[$name])?$_GET[$name]:''):$_GET);
			break;
			
			//POST请求，如果$name为空，将返回整个$_POST数组
			case 'post':
				if($safe){
					$_POST = array_map(array('SWFunc','addslashes_deep'), $_POST);
				}
				$para=($name?(isset($_POST[$name])?$_POST[$name]:''):$_POST);
			break;
			
			//获取COOKIES，如果$name为空，将返回整个$_COOKIE数组
			case 'cookie':
				if($safe){
					$_COOKIE = array_map(array('SWFunc','addslashes_deep'), $_COOKIE);
				}
				$para=($name?(isset($_COOKIE[$name])?$_COOKIE[$name]:''):$_COOKIE);
			break;
			
		}
		
		return $para;
		
    }
	
	/*=====================================================================
	* 设置session
	======================================================================*/
	function setSession($name,$value){
		$_SESSION[$name.'_'.SW_HASH]=$value;
	}

	/*=====================================================================
	* 获取session
	======================================================================*/
	function getSession($name){
		return isset($_SESSION[$name.'_'.SW_HASH])?$_SESSION[$name.'_'.SW_HASH]:'';
	}

	/*=====================================================================
	* 设置cookie
	======================================================================*/
	function setCookies($name,$value,$time=0){
		setcookie($name.'_'.SW_HASH,$value,$time,'/');
	}

	/*=====================================================================
	* 获取cookie
	======================================================================*/
	function getCookies($name){
		return SW::request($name.'_'.SW_HASH,'cookie');
	}
	
	/*=====================================================================
	* 获取数据库对象函数
	* 获取database对象，设置连接
	* 这个方法可以直接给连接参数，也可以读取config设置
	======================================================================*/
	function getDb($host='',$database='',$user='',$pass=''){
		
		$obj=new SWMysql();

		if($host && $database && $user){ //密码是可以为空的
			$obj->host=$host;
			$obj->database=$database;
			$obj->user=$user;
			$obj->pass=$pass;

		}else{
			$obj->host=SW_HOST;
			$obj->database=SW_DB;
			$obj->user=SW_USER;
			$obj->pass=SW_PASS;
		}
		
		return $obj;
	}
	
	/*=====================================================================
	* 自动载入方法
	======================================================================*/
	public static function autoloader_classes($name) {
		
		$filename=$name;
		
		//classes中只有SW开头的类才是合法的
		if(preg_match('/^sw.+/i',$name)){
			$filename=preg_replace('/^sw/i','',$name);
			require_once(SW::path('framework.classes.'.strtolower($filename)));
		}
		
	}
    
    /*=====================================================================
	* 检查数据库是否正确
	======================================================================*/
	public static function checkDatabase(){
        
        //检查是否存在option表
        $db=self::getDb();
        return $db->tableExists(TBL_OPTION);
        
	}

	/*=====================================================================
	* 检查版本是否和目前版本符合
	======================================================================*/
	public static function checkVersion(){

		//首先确认是否有version设置（alpha1版本中没有version）
		if(!SW::getOption()->nameExists('version')) return false;
		
		//检查版本号
		if(SWFunc::compareVersion(SW::getOption('version'),SW_VERSION)<0) return false;
		
		return true;

	}
	
	/*=====================================================================
	* 全局错误绑定函数
	======================================================================*/
	public static function errHandler($errno, $errstr, $errfile, $errline){
		switch($errno){
			case E_USER_ERROR:
			case E_WARNING:
			case E_NOTICE:
				__('<div style="padding:8px;background:red;color:white;position:fixed;z-index:9999;top:0;left:0;">
				<b>'.$errno.':</b> '.$errstr.' <b>AT:</b> '.$errfile.' line '.$errline.
				'</div>');
				exit();
			break;
		}
	}
	
	/*=====================================================================
	* 计算程序安装至今的天数
	======================================================================*/
	public static function getCreateDays(){

		return ceil((time()-SW_CREATE)/86400);

	}
    
}

?>
