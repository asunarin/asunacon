<?php
// +----------------------------------------------------------------------
// | Copyright (C) 浩天科技 www.ihotte.com admin@ihotte.com
// +----------------------------------------------------------------------
// | Licensed: ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author:   左手边的回忆 QQ:858908467 E-mail:858908467@qq.com
// +----------------------------------------------------------------------
/**
 +------------------------------------------------------------------------------
 * 文件$ID ： function.php
 +------------------------------------------------------------------------------
 * 路径$ID ： inc/function.php
 +------------------------------------------------------------------------------
 * 程序版本： 浩天 WebFTP V1.0.0 2011-10-01
 +------------------------------------------------------------------------------
 * 功能简介： 系统函数库
 +------------------------------------------------------------------------------
 * 注意事项： 请勿私自删除此版权信息！
 +------------------------------------------------------------------------------
 **/
 
if(!defined('INC_ROOT')){die('Forbidden Access');}

function checklogin($ajaxReturn = true, $authcheck = true){
    @$admin_name = ((isset($_SESSION['user_name'])&&!empty($_SESSION['user_name'])) && ($_SESSION['user_name'] === $_COOKIE[C('COOKIE_PREFIX').'user_name']))?true:false;
    @$admin_pass = ((isset($_SESSION['user_password'])&&!empty($_SESSION['user_password'])) && ($_SESSION['user_password'] === $_COOKIE[C('COOKIE_PREFIX').'user_password']))?true:false;
    @$admin_auth = ((isset($_SESSION['user_auth'])&&!empty($_SESSION['user_auth'])) && ($_SESSION['user_auth'] === $_COOKIE[C('COOKIE_PREFIX').'user_auth']))?$_SESSION['user_auth']:'';
	if(!$admin_name || !$admin_pass){
		if($ajaxReturn){		
			$data = array();
			$data['statusCode'] = 300;
			$data['message'] = '登陆超时或未登录！';
			exit(json_encode($data));
		}else{
			return false;
		}	   
	}else{
		if($authcheck){
			if( in_array('*', explode('|', $admin_auth)) || in_array(APP_ACTION, explode('|', $admin_auth)) ){
				return true;
			}else{
				if($ajaxReturn){		
					$data = array();
					$data['statusCode'] = 300;
					$data['message'] = 'Sorry,你没有此操作权限！';
					exit(json_encode($data));
				}else{
					return false;
				}
			}  
		}else{
			return true;
		}		
	}
}

function u2g($str){
	return @iconv('UTF-8', 'GB2312//IGNORE', $str);
}
function g2u($str){
	return @iconv('GB2312', 'UTF-8//IGNORE', $str);
}

//去除UTF-8 BOM 文件头
function stripBOM($string){
	$string = trim($string);
	if(chr(239).chr(187).chr(191) == substr($string,0,3)){
		return substr($string,3);
	}else{
		return $string;
	}
}


//文件大小格式化
function dealsize($size){
	$danwei = array( 'Byte','KB','MB','GB' );
	$d = 0;
	while ( $size >= 900 ){
		$size = round($size*100/1024)/100;
		$d++;
	}
	return $size.' '.$danwei[$d];
}

//获取扩展名
function get_ext($filename){
	$filename = basename(strtolower($filename));
	$arr = explode('.',$filename);
	$type = $arr[count($arr)-1];
	return strtolower($type);
}

//获取文件编码
function get_encode($file){
	//暂时只支持('UTF-8 BOM', 'UTF-8','GB2312','ASCII')	
	/*
	$signal  = fread(fopen($file,'rb'),2);
	if($signal == chr(239).chr(187)){return 'UTF-8 BOM';}
	fclose($fp);
	*/
	$string = file_get_contents($file);
	if(chr(239).chr(187).chr(191) == substr($string, 0, 3)) return 'UTF-8 BOM';
	if($string === iconv('UTF-8', 'UTF-8',  iconv('UTF-8', 'UTF-8', $string)))   return 'UTF-8';
	if($string === iconv('UTF-8', 'ASCII',  iconv('ASCII', 'UTF-8', $string)))   return 'ASCII';
	if($string === iconv('UTF-8', 'GB2312', iconv('GB2312', 'UTF-8', $string)))  return 'GB2312';
	return '无法识别';
}
function is_utf8($string){	
		return preg_match('/^([\x09\x0A\x0D\x20-\x7E])+/xs', trim($string));
}

// 循环创建目录
function mk_dir($dir, $mode = 0777) {
    if (is_dir($dir) || @mkdir($dir, $mode))
        return true;
    if (!mk_dir(dirname($dir), $mode))
        return false;
    return @mkdir($dir, $mode);
}

// 获取配置值
function C($name=null, $value=null) {
    static $_config = array();
	
    // 无参数时获取所有
    if(empty($name)){
        return $_config;
	}
    // 优先执行设置获取或赋值
    if (is_string($name)){
		$name = strtoupper($name);
        if(!strpos($name, '.')) {
           
            if (is_null($value)){
                return isset($_config[$name]) ? $_config[$name] : null;
			}else{
				$_config[$name] = $value;
			}        
            return;
        }
        // 二维数组设置和获取支持
        $name = explode('.', $name);
		if(!isset($name[2])){
			if (is_null($value)){
				return isset($_config[$name[0]][$name[1]]) ? $_config[$name[0]][$name[1]] : null;
			}else{
				$_config[$name[0]][$name[1]] = $value;
			}
		}else{
			if (is_null($value)){
				return isset($_config[$name[0]][$name[1]][$name[2]]) ? $_config[$name[0]][$name[1]][$name[2]] : null;
			}else{
				$_config[$name[0]][$name[1]][$name[2]] = $value;
			}	
		}
        return;
    }
    // 批量设置
    if (is_array($name)){
        return $_config = array_merge($_config, array_change_key_case($name,CASE_UPPER));
	}
    return null; // 避免非法参数
}

// 记录和统计时间（微秒）
function G($start,$end='',$dec=3) {
    static $_info = array();
    if(!empty($end)) { // 统计时间
        if(!isset($_info[$end])) {
            $_info[$end]   =  microtime(TRUE);
        }
        return number_format(($_info[$end]-$_info[$start]),$dec);
    }else{ // 记录时间
        $_info[$start]  =  microtime(TRUE);
    }
}


/**
  +----------------------------------------------------------
 * Cookie 设置、获取、清除
  +----------------------------------------------------------
 * 1 获取cookie: cookie('name')
 * 2 清空当前设置前缀的所有cookie: cookie(null)
 * 3 删除指定前缀所有cookie: cookie(null,'think_') | 注：前缀将不区分大小写
 * 4 设置cookie: cookie('name','value') | 指定保存时间: cookie('name','value',3600)
 * 5 删除cookie: cookie('name',null)
  +----------------------------------------------------------
 * $option 可用设置prefix,expire,path,domain
 * 支持数组形式对参数设置:cookie('name','value',array('expire'=>1,'prefix'=>'think_'))
 * 支持query形式字符串对参数设置:cookie('name','value','prefix=tp_&expire=10000')
 */
function cookie($name, $value='', $option=null) {
    // 默认设置
    $config = array(
        'prefix' => C('COOKIE_PREFIX'), // cookie 名称前缀
        'expire' => C('COOKIE_EXPIRE'), // cookie 保存时间
        'path' => C('COOKIE_PATH'), // cookie 保存路径
        'domain' => C('COOKIE_DOMAIN'), // cookie 有效域名
    );
    // 参数设置(会覆盖黙认设置)
    if (!empty($option)) {
        if (is_numeric($option))
            $option = array('expire' => $option);
        elseif (is_string($option))
            parse_str($option, $option);
        $config = array_merge($config, array_change_key_case($option));
    }
    // 清除指定前缀的所有cookie
    if (is_null($name)) {
        if (empty($_COOKIE))
            return;
        // 要删除的cookie前缀，不指定则删除config设置的指定前缀
        $prefix = empty($value) ? $config['prefix'] : $value;
        if (!empty($prefix)) {// 如果前缀为空字符串将不作处理直接返回
            foreach ($_COOKIE as $key => $val) {
                if (0 === stripos($key, $prefix)) {
                    setcookie($key, '', time() - 3600, $config['path'], $config['domain']);
                    unset($_COOKIE[$key]);
                }
            }
        }
        return;
    }
    $name = $config['prefix'] . $name;
    if ('' === $value) {
        return isset($_COOKIE[$name]) ? $_COOKIE[$name] : null; // 获取指定Cookie
    } else {
        if (is_null($value)) {
            setcookie($name, '', time() - 3600, $config['path'], $config['domain']);
            unset($_COOKIE[$name]); // 删除指定cookie
        } else {
            // 设置cookie
            $expire = !empty($config['expire']) ? time() + intval($config['expire']) : 0;
            setcookie($name, $value, $expire, $config['path'], $config['domain']);
            $_COOKIE[$name] = $value;
        }
    }
}

// 浏览器友好的变量输出
function dump($var, $echo=true, $label=null, $strict=true) {
    $label = ($label === null) ? '' : rtrim($label) . ' ';
    if (!$strict) {
        if (ini_get('html_errors')) {
            $output = print_r($var, true);
            $output = "<pre>" . $label . htmlspecialchars($output, ENT_QUOTES) . "</pre>";
        } else {
            $output = $label . print_r($var, true);
        }
    } else {
        ob_start();
        var_dump($var);
        $output = ob_get_clean();
        if (!extension_loaded('xdebug')) {
            $output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $output);
            $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
        }
    }
    if ($echo) {
        echo($output);
        return null;
    }else
        return $output;
}

// 数组保存到文件
function arr2file($filename, $arr=''){
	if(is_array($arr)){
		$con = var_export($arr,true);
	}else{
		$con = $arr;
	}
	$con = "<?php if(!defined('INC_ROOT')){die('Forbidden Access');};?>\n<?php\nreturn $con;\n?>";
	return file_put_contents($filename, $con);
}

//兼容转义字符处理
if(get_magic_quotes_gpc()) {
	function stripslashes_deep($value){
		$value = is_array($value) ? array_map('stripslashes_deep', $value) :  stripslashes($value);
		return $value;
	}
	$_POST    = array_map('stripslashes_deep', $_POST);
	$_GET     = array_map('stripslashes_deep', $_GET);
	$_COOKIE  = array_map('stripslashes_deep', $_COOKIE);
	$_REQUEST = array_map('stripslashes_deep', $_REQUEST);

}

// 自定义异常处理
function throw_exception($errno, $errmsg, $errfile, $errline, $errvars){
	if(!C('LOG_EXCEPTION_RECORD')) return;
	$user_errors = array(E_USER_ERROR, E_USER_WARNING, E_USER_NOTICE);
    $errortype = array (
        E_ERROR              => 'EMERG',
        E_WARNING            => 'WARNING',//非致命的 run-time 错误。不暂停脚本执行。
        E_PARSE              => 'EMERG',//语法错误
        E_NOTICE             => 'NOTICE',//Run-time 通知。
        E_CORE_ERROR         => 'EMERG',
        E_CORE_WARNING       => 'WARNING',
        E_COMPILE_ERROR      => 'EMERG',
        E_COMPILE_WARNING    => 'WARNING',
        E_USER_ERROR         => 'EMERG',//致命的用户生成的错误。
        E_USER_WARNING       => 'WARNING',//非致命的用户生成的警告。
        E_USER_NOTICE        => 'NOTICE',//用户生成的通知。
        E_STRICT             => 'NOTICE',
        E_RECOVERABLE_ERROR  => 'EMERG',//可捕获的致命错误。
		'INFO'               => 'INFO',//信息: 程序输出信息
		'DEBUG'              => 'DEBUG',// 调试: 调试信息
		'SQL'                => 'SQL',// SQL：SQL语句 注意只在调试模式开启时有效
    );

	if(isset($errortype[$errno]))
		$error['type'] = $errortype[$errno];
	else
		$error['type'] = $errno;
	if(!in_array($error['type'], explode(',',C('LOG_EXCEPTION_TYPE')))) return;
	
	$err  = date('[ Y-m-d H:i:s (T) ]').'  ';
    $err .= $error['type'].':  ';
	$err .= $errmsg.'  ';
    $err .= $errfile.'  ';
    $err .= '第'.$errline.'行  ';

    if (in_array($errno, $user_errors)) {
        $err .= "<vartrace>" . wddx_serialize_value($errvars, "Variables") . "</vartrace>";
    }
    $err .= "\n";
    
    $destination = DATA_LOG_PATH.date('y_m_d').".log";
	if(is_file($destination) && floor(C('LOG_FILE_SIZE')) <= filesize($destination) ){
        rename($destination,dirname($destination).'/'.time().'-'.basename($destination));
	}
    error_log($err, 3, $destination);
}
?>