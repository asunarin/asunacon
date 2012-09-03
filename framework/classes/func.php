<?php

defined('SW_') or die('Access Error');

/*=====================================================================
* 这个类用来存放页面级的公共函数，所有都为静态方法，不用声明对象
======================================================================*/
class SWFunc{

	/*=====================================================================
	* 检查是否是数字，否则返回0
	======================================================================*/
	function checkInt($int){
		if(is_numeric($int)){
			return intval($int);
		}else{
			return 0;
		}
	}

	/*=====================================================================
	* 检查是否是字符串，否则返回''
	======================================================================*/
	function checkString($str){
		if($str){
			return trim(strval($str));
		}else{
			return '';
		}
	}

	/*=====================================================================
	* 过滤特殊字符，转成安全字符串
	* 注意：不要对$_GET,$_POST等参数过滤，因为这些参数在全局过滤过
	======================================================================*/
	function filterString($str){
		if($str){
			$str=stripslashes($str);
			$str=addslashes($str);
			return $str;
		}else{
			return '';
		}
	}

	/*=====================================================================
	* 检查时间字符串并转换为时间戳，如果不对，则返回time()
	======================================================================*/
	function checkToTimestamp($str){
		return strtotime($str)?strtotime($str):time();
	}
	
	/*=====================================================================
	* 过滤html标签，返回指定长度的字符串，$length为0则返回全部
	======================================================================*/
	function closeHtml($str,$length=0,$plus=''){

		$str=self::checkString(kses($str,array()));
		if($length>0){
			$str=self::sliceStr($str,$length,$plus);
		}
		return $str;

	}
	
	/*=====================================================================
	* 匹配并修复未关闭的html标签
	* 这个函数来源于网络，感谢作者
	======================================================================*/
	function closeTags($html){

		/*截取最后一个 < 之前的内容，确保字符串中所有HTML标签都以 > 结束*/
		$html = preg_replace("~<[^<>]+?$~i", "", $html);
		/*自动匹配补齐未关闭的HTML标签*/
		#put all opened tags into an array
		preg_match_all("#<([a-z]+)( .*[^/])?(?!/)>#iU", $html, $result);
		$openedtags = $result[1];
		#put all closed tags into an array
		preg_match_all("#</([a-z]+)>#iU", $html, $result);
		$closedtags = $result[1];
		$len_opened = count($openedtags);
		# all tags are closed
		if (count($closedtags) == $len_opened)
		{
			return $html;
		}
		$openedtags = array_reverse($openedtags);
		# close tags
		for ($i = 0; $i < $len_opened; $i++)
		{
			if (!in_array($openedtags[$i], $closedtags))
			{
				$html .= '</' . $openedtags[$i] . '>';
			} else
			{
				unset($closedtags[array_search($openedtags[$i], $closedtags)]);
			}
		}
		return $html;

	}
	
	/*=====================================================================
	* 转换纯文本，删除所有html标签，同时替换换行为<br />
	======================================================================*/
	function transText($str){

		$str=self::closeHtml($str);
		$str=str_replace("\r\n","\n",$str);
		$str=str_replace("\r","\n",$str);
		$str=preg_replace("/\n{2,}/i","\n\n", $str);
		$str=str_replace("\n",'<br />',$str);
		return $str;

	}
	
	/*=====================================================================
	* UBB转换函数
	======================================================================*/
	function transUbb($Text){
		$Text=htmlspecialchars($Text);
		$Text=stripslashes($Text);
		$Text=ereg_replace("\r\n","\n",$Text);
		$Text=ereg_replace("\r","\n",$Text);
		$Text=nl2br($Text);
		$Text=preg_replace("/\\t/is","　　",$Text);
		//$Text=ereg_replace(" ","&nbsp;",$Text);

		$Text=preg_replace("/\[h1\](.+?)\[\/h1\]/is","<h1>\\1</h1>",$Text);
		$Text=preg_replace("/\[h2\](.+?)\[\/h2\]/is","<h2>\\1</h2>",$Text);
		$Text=preg_replace("/\[h3\](.+?)\[\/h3\]/is","<h3>\\1</h3>",$Text);
		$Text=preg_replace("/\[h4\](.+?)\[\/h4\]/is","<h4>\\1</h4>",$Text);
		$Text=preg_replace("/\[h5\](.+?)\[\/h5\]/is","<h5>\\1</h5>",$Text);
		$Text=preg_replace("/\[h6\](.+?)\[\/h6\]/is","<h6>\\1</h6>",$Text);
		$Text=preg_replace("/\[center\](.+?)\[\/center\]/is","<center>\\1</center>",$Text);
		$Text=preg_replace("/\[big\](.+?)\[\/big\]/is","<big>\\1</big>",$Text);
		$Text=preg_replace("/\[small\](.+?)\[\/small\]/is","<small>\\1</small>",$Text);    

		$Text=preg_replace("/\[url\](http:\/\/.+?)\[\/url\]/is","<a href=\"\\1\">\\1</a>",$Text);
		$Text=preg_replace("/\[url\](.+?)\[\/url\]/is","<a href=\"http://\\1\">http://\\1</a>",$Text);
		$Text=preg_replace("/\[url=(http:\/\/.+?)\](.*)\[\/url\]/is","<a href=\"\\1\">\\2</a>",$Text);
		$Text=preg_replace("/\[url=(.+?)\](.*)\[\/url\]/is","<a href=\"http://\\1\">\\2</a>",$Text);    

		$Text=preg_replace("/\[img\](.+?)\[\/img\]/is","<img src=\"\\1\" border=\"0\" />",$Text);
		$Text=preg_replace("/\[color=(.+?)\](.+?)\[\/color\]/is","<font color=\"\\1\">\\2</font>",$Text);
		$Text=preg_replace("/\[size=(.+?)\](.+?)\[\/size\]/is","<font size=\"\\1\">\\2</font>",$Text);
		$Text=preg_replace("/\[sup\](.+?)\[\/sup\]/is","<sup>\\1</sup>",$Text);
		$Text=preg_replace("/\[sub\](.+?)\[\/sub\]/is","<sub>\\1</sub>",$Text);
		$Text=preg_replace("/\[pre\](.+?)\[\/pre\]/is","<pre>\\1</pre>",$Text);
		$Text=preg_replace("/\[email\](.+?)\[\/email\]/is","<a href=\"mailto:\\1\">\\1</a>",$Text);
		$Text=preg_replace("/\[i\](.+?)\[\/i\]/is","<i>\\1</i>",$Text);
		$Text=preg_replace("/\[b\](.+?)\[\/b\]/is","<b>\\1</b>",$Text);
		$Text=preg_replace("/\[quote\](.+?)\[\/quote\]/is","<blockquote><font size=\"1\" face=\"Courier New\">quote:</font><hr>\\1<hr></blockquote>", $Text);
		$Text=preg_replace("/\[code\](.+?)\[\/code\]/is","<blockquote><font size=\"1\" face=\"Times New Roman\">code:</font><hr color=\"lightblue\"><i>\\1</i><hr color=\"lightblue\"></blockquote>", $Text);
		$Text=preg_replace("/\[sig\](.+?)\[\/sig\]/is","<div style=\"text-align: left; color: darkgreen; margin-left: 5%;\"><br><br>--------------------------<br />\\1<br />--------------------------</div>", $Text);
		$Text=ereg_replace("\[hr\]","<hr />",$Text);
		return $Text;
	}
	
	/*=====================================================================
	* 返回真实的字符串长度
	======================================================================*/
	function strLength($str,$charset='utf-8') {       
		$n = 0; $p = 0; $c = '';
		$len = strlen($str);
		if($charset == 'utf-8') {
			for($i = 0; $i < $len; $i++) {
				$c = ord($str{$i});
				if($c > 252) {
					$p = 5;
				} elseif($c > 248) {
					$p = 4;
				} elseif($c > 240) {
					$p = 3;
				} elseif($c > 224) {
					$p = 2;
				} elseif($c > 192) {
					$p = 1;
				} else {
					$p = 0;
				}
				$i+=$p;$n++;
			}
		} else {
			for($i = 0; $i < $len; $i++) {
				$c = ord($str{$i});
				if($c > 127) {
					$p = 1;
				} else {
					$p = 0;
			}
				$i+=$p;$n++;
			}
		}       
		return $n;
    }
	
	/*=====================================================================
	* 字符串截取函数，支持非ANSI字符
	======================================================================*/
	function sliceStr($string, $sublen ,$plus=''){

		$start = 0;
		$code = 'UTF-8';
		if($code == 'UTF-8')
		{
			$pa = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/";
			preg_match_all($pa, $string, $t_string);
			if(count($t_string[0]) - $start > $sublen) return join('', array_slice($t_string[0], $start, $sublen)).$plus;
			return join('', array_slice($t_string[0], $start, $sublen));
		}
		else
		{
			$start = $start*2;
			$sublen = $sublen*2;
			$strlen = strlen($string);
			$tmpstr = '';
	
			for($i=0; $i< $strlen; $i++)
			{
				if($i>=$start && $i< ($start+$sublen))
				{
					if(ord(substr($string, $i, 1))>129)
					{
						$tmpstr.= substr($string, $i, 2);
					}
					else
					{
						$tmpstr.= substr($string, $i, 1);
					}
				}
				if(ord(substr($string, $i, 1))>129) $i++;
			}
			if(strlen($tmpstr)< $strlen ) $tmpstr.= $plus;
			return $tmpstr;
		}
	}
	
	/*=====================================================================
	* 检查email格式，返回true或false
	======================================================================*/
	function checkEmail($email) {  
		// First, we check that there's one @ symbol, and that the lengths are right   
		if (!ereg("[^@]{1,64}@[^@]{1,255}", $email)) {   
			// Email invalid because wrong number of characters in one section, or wrong number of @ symbols.   
			return false;   
		}   
		// Split it into sections to make life easier   
		$email_array = explode("@", $email);   
		$local_array = explode(".", $email_array[0]);   
		for ($i = 0; $i < sizeof($local_array); $i++) {   
			if (!ereg("^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$", $local_array[$i])) {   
				return false;   
			}   
		}
		// Check if domain is IP. If not, it should be valid domain name   
		if (!ereg("^\[?[0-9\.]+\]?$", $email_array[1])) {
			$domain_array = explode(".", $email_array[1]);   
			if (sizeof($domain_array) < 2) {   
				// Not enough parts to domain 
				return false;   
			}   
			for ($i = 0; $i < sizeof($domain_array); $i++) {   
				if (!ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$", $domain_array[$i])) {   
					return false;   
				}   
			}   
		}   
		return true;   
	}

	/*=====================================================================
	* 检查url地址格式，返回格式化后的地址
	* 如果地址格式错误，将返回空
	* 如果addSlash为真，将会对目录型的url最后加上斜杠
	* 如果delQuery为真，将删除包括?#在内的请求部分
	======================================================================*/
	function checkUrl($url,$addSlash=false,$delQuery=false) {

		$url=self::closeHtml($url);

		if($url){
		
			//把反斜杠换成斜杠
			$url=str_replace('\\','/',$url);
			
			//匹配正则
			$regex = "((https?)\:\/\/)?"; // SCHEME
			$regex .= "([a-z0-9-\.]+)"; // Host or IP
			$regex .= "(\:[0-9]{2,5})?"; // Port
			$regex .= "((\/([a-z0-9+\$_-]\.?)+)*(\/?))"; // Path
			$regex .= "((\?[a-z+&\$_.-][a-z0-9;:@&%=+\/\$_.-]*)?"; // GET Query
			$regex .= "(#[a-z_.-][a-z0-9+\$_.-]*)?)"; // Anchor 
			
			$matchs=array();
			if(preg_match("/^$regex$/i", $url, $matchs)){
				
				//判断host是否正确
				$host=empty($matchs[3])?'':$matchs[3];
				if(!preg_match("/^([a-z0-9-.]*)\.([a-z]{2,3})$/i",$host) && !preg_match("/^localhost$/i",$host) && !preg_match("/^([1-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])(\.([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])){3}$/",$host)){
					
					$url='';
				
				}else{
				
					//判断是否以http://或https://打头
					if(empty($matchs[1])) $url='http://'.$url;
					
					//如果只有host，没有path，那么判断反斜杠
					if($addSlash && empty($matchs[5]) && empty($matchs[9])){
						$url.='/';
						
					//判断最后是否已经有斜杠，是否是文件型请求
					}elseif($addSlash && empty($matchs[9]) && empty($matchs[8]) && $matchs[5]!='/' && !preg_match("/\./",$matchs[6])){
						$url.='/';
					}
					
					//删除请求后缀
					if($delQuery && !empty($matchs[9])){
						$url=str_replace($matchs[9],'',$url);
					}

				}
				
			}else{
				
				$url='';
			}
		
		}

		return $url;
	
	}
	
	/*=====================================================================
	* 检查字符串中字符是否符合要求，预设几种level
	* min和max为字符的长度范围，为0则不判断
	======================================================================*/
	function checkCharacter($str,$level=1,$min=0,$max=0){
		
		if(!$str) return false;

		//检查字数
		if($min && self::strLength($str)<$min) return false;
		if($max && self::strLength($str)>$max) return false;
		
		//判断是那种类型的检查
		switch($level){

			//匹配非符号字符串，一般用于用户名或昵称
			case 1: 
				if(preg_match("/[^\w\x{3400}-\x{4DBF}\x{4E00}-\x{9FAF}]/u",$str)) return false;
			break;

			//匹配密码用字符串
			case 2: 
				if(preg_match("/[^\x20-\x7e]/",$str)) return false;
			break;

			//匹配0-9a-zA-Z_等，一般用于account帐号
			case 3: 
				if(preg_match("/[^\d\w\_]/",$str)) return false;
			break;

		}

		return true;

	}
	
	/*=====================================================================
	* 把文件的绝对路径转换为url的绝对路径
	* 比如/var/www/site/a.php 转为 /site/a.php
	* 注意，$path必须是文件的绝对路径
	======================================================================*/
	function getUrlByPath($path){

		$url='/'.substr($path,strlen($_SERVER['DOCUMENT_ROOT']));
		$url=str_replace('\\','/',$url);
		$url=str_replace('//','/',$url);

		return $url;

	}

	/*=====================================================================
	* 获取目录下的所有文件
	* type为过滤文件类型，默认是php文件
	* 目录名必须以/结尾
	======================================================================*/
	function getFiles($dir,$filetype='php') {

		$arr=array();

		//判断是否是目录
		if (is_dir($dir)){

			//打开目录并循环文件夹
			if ($dh = opendir($dir)) {

				while (($file = readdir($dh)) !== false) {
					
					if(filetype($dir . $file)!='file') continue;

					//获取文件类型
					$arrTmp=explode('.',$file);

					if(count($arrTmp)>1){
						$type=$arrTmp[count($arrTmp)-1];
						array_pop($arrTmp);
						$name=implode('.',$arrTmp);
					}else{
						$type='';
						$name=$arrTmp[0];
					}
					
					if(!$filetype || $filetype==$type){
						$arr[]=array('name'=>$name,'type'=>$type);
					}

				}

				closedir($dh);

			}

		}

		return $arr;

	}

	/*=====================================================================
	* 获取目录下的所有子目录
	* 目录名必须以/结尾
	======================================================================*/
	function getDirs($dir) {

		$arr=array();

		//判断是否是目录
		if (is_dir($dir)){

			//打开目录并循环文件夹
			if ($dh = opendir($dir)) {

				while (($file = readdir($dh)) !== false) {
					
					//如果不是目录，跳过
					if(filetype($dir . $file)!='dir') continue;
					
					//如果是.或者..，跳过
					if($file=='.' || $file=='..') continue;

					$arr[]=$file;

				}

				closedir($dh);

			}

		}

		return $arr;

	}
	
	/*=====================================================================
	* 这个函数来自php官方，屏蔽数组内所有转义符"\"等
	======================================================================*/
	function stripslashes_deep($value){
		return is_array($value)?array_map(array('self','stripslashes_deep'), $value):stripslashes($value);
	}

	/*=====================================================================
	* 转义所有特殊字符（stripslashes_deep的反操作）
	======================================================================*/
	function addslashes_deep($value){
		return is_array($value)?array_map(array('self','addslashes_deep'), $value):addslashes($value);
	}
	
	/*=====================================================================
	* 把xml文档转成array的函数
	======================================================================*/
	function xml2array($url, $get_attributes = 1, $priority = 'tag')
	{
		$contents = "";
		if (!function_exists('xml_parser_create'))
		{
			return array ();
		}
		$parser = xml_parser_create('');
		if (!($fp = @ fopen($url, 'rb')))
		{
			return array ();
		}
		while (!feof($fp))
		{
			$contents .= fread($fp, 8192);
		}
		fclose($fp);
		xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8");
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
		xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
		xml_parse_into_struct($parser, trim($contents), $xml_values);
		xml_parser_free($parser);
		if (!$xml_values)
			return; //Hmm...
		$xml_array = array ();
		$parents = array ();
		$opened_tags = array ();
		$arr = array ();
		$current = & $xml_array;
		$repeated_tag_index = array ();
		foreach ($xml_values as $data)
		{
			unset ($attributes, $value);
			extract($data);
			$result = array ();
			$attributes_data = array ();
			if (isset ($value))
			{
				if ($priority == 'tag')
					$result = $value;
				else
					$result['value'] = $value;
			}
			if (isset ($attributes) and $get_attributes)
			{
				foreach ($attributes as $attr => $val)
				{
					if ($priority == 'tag')
						$attributes_data[$attr] = $val;
					else
						$result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr'
				}
			}
			if ($type == "open")
			{
				$parent[$level -1] = & $current;
				if (!is_array($current) or (!in_array($tag, array_keys($current))))
				{
					$current[$tag] = $result;
					if ($attributes_data)
						$current[$tag . '_attr'] = $attributes_data;
					$repeated_tag_index[$tag . '_' . $level] = 1;
					$current = & $current[$tag];
				}
				else
				{
					if (isset ($current[$tag][0]))
					{
						$current[$tag][$repeated_tag_index[$tag . '_' . $level]] = $result;
						$repeated_tag_index[$tag . '_' . $level]++;
					}
					else
					{
						$current[$tag] = array (
							$current[$tag],
							$result
						);
						$repeated_tag_index[$tag . '_' . $level] = 2;
						if (isset ($current[$tag . '_attr']))
						{
							$current[$tag]['0_attr'] = $current[$tag . '_attr'];
							unset ($current[$tag . '_attr']);
						}
					}
					$last_item_index = $repeated_tag_index[$tag . '_' . $level] - 1;
					$current = & $current[$tag][$last_item_index];
				}
			}
			elseif ($type == "complete")
			{
				if (!isset ($current[$tag]))
				{
					$current[$tag] = $result;
					$repeated_tag_index[$tag . '_' . $level] = 1;
					if ($priority == 'tag' and $attributes_data)
						$current[$tag . '_attr'] = $attributes_data;
				}
				else
				{
					if (isset ($current[$tag][0]) and is_array($current[$tag]))
					{
						$current[$tag][$repeated_tag_index[$tag . '_' . $level]] = $result;
						if ($priority == 'tag' and $get_attributes and $attributes_data)
						{
							$current[$tag][$repeated_tag_index[$tag . '_' . $level] . '_attr'] = $attributes_data;
						}
						$repeated_tag_index[$tag . '_' . $level]++;
					}
					else
					{
						$current[$tag] = array (
							$current[$tag],
							$result
						);
						$repeated_tag_index[$tag . '_' . $level] = 1;
						if ($priority == 'tag' and $get_attributes)
						{
							if (isset ($current[$tag . '_attr']))
							{
								$current[$tag]['0_attr'] = $current[$tag . '_attr'];
								unset ($current[$tag . '_attr']);
							}
							if ($attributes_data)
							{
								$current[$tag][$repeated_tag_index[$tag . '_' . $level] . '_attr'] = $attributes_data;
							}
						}
						$repeated_tag_index[$tag . '_' . $level]++; //0 and 1 index is already taken
					}
				}
			}
			elseif ($type == 'close')
			{
				$current = & $parent[$level -1];
			}
		}
		return ($xml_array);
	}

	/*=====================================================================
	* 递归的将对象属性转化为数组
	* 这个函数比较复杂，递归算法，需要细细品味
	======================================================================*/
	function obj2array($obj){
		if(is_array($obj)){
			$arr=array();
			foreach($obj as $key=>$value){
				$arr[$key]=self::obj2array($value);
			}
			return $arr;
		}elseif(is_object($obj)){
			$arr=get_object_vars($obj);
			foreach($arr as $key=>$value){
				$arr[$key]=self::obj2array($value);
			}
			return $arr;
		}else{
			return $obj;
		}
	}
	
	/*=====================================================================
	* 获取访问者真实IP
	======================================================================*/
	function getIp(){

		$ip='';
		if(!empty($_SERVER["HTTP_CLIENT_IP"])){
			$ip = $_SERVER["HTTP_CLIENT_IP"];
		}
		if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ips = explode (", ", $_SERVER['HTTP_X_FORWARDED_FOR']);
			if ($ip) { array_unshift($ips, $ip); $ip = FALSE; }
				for ($i = 0; $i < count($ips); $i++) {
					if (!eregi ("^(10│172.16│192.168).", $ips[$i])) {
						$ip = $ips[$i];
						break;
					}
				}
		}
		return trim($ip ? $ip : $_SERVER['REMOTE_ADDR']);

	}

	/*=====================================================================
	* 判断是否是内网IP
	======================================================================*/
	function is_internal_ip($ip){

		$ip = ip2long($ip);
		
		//如果ip格式不正确，返回true
		if(!$ip) return true;
		
		$net_a = ip2long('10.255.255.255') >> 24; //A类网预留ip的网络地址
		$net_b = ip2long('172.31.255.255') >> 20; //B类网预留ip的网络地址
		$net_c = ip2long('192.168.255.255') >> 16; //C类网预留ip的网络地址

		return $ip >> 24 === $net_a || $ip >> 20 === $net_b || $ip >> 16 === $net_c; 

	}

	/*=====================================================================
	* 判断是否是内网URL
	======================================================================*/
	function is_internal_url($url){
		
		$url=self::checkUrl($url);
		
		if(preg_match("/^((https?)\:\/\/)?([a-z0-9-\.]+)(\:[0-9]{2,5})?\/?/i", $url, $matchs)){
			
			$host=empty($matchs[3])?'':$matchs[3];
			
			//如果匹配上了域名格式
			if(preg_match("/^([a-z0-9-.]*)\.([a-z]{2,3})$/i", $host)) return false;
						
			//是否是有效的IP
			if(preg_match("/^([1-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])" .
            "(\.([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])){3}$/", $host)){
				
				//如果是127开头的IP
				if(!preg_match("/^127\./i", $host) && !self::is_internal_ip($host)) return false;
				
			}
			
		}
		
		return true;
		
	}
	
	/*=====================================================================
	* 生成随机字符串
	======================================================================*/
	function randomString($len=8, $pool='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'){
		
		$str='';
		
		for($i=0; $i < $len; $i++){
			$str .= substr($pool, mt_rand(0, strlen($pool) -1), 1);
		}
		
		return $str;
		
		
	}
	
	/*=====================================================================
	* 获取uuid（唯一标识符）
	======================================================================*/
	function uuid($key = '', $prefix = ''){

		if(!$key) $key = uniqid(rand());

		$chars = md5($key);
		$uuid  = substr($chars,0,8) . '-';
		$uuid .= substr($chars,8,4) . '-';
		$uuid .= substr($chars,12,4) . '-';
		$uuid .= substr($chars,16,4) . '-';
		$uuid .= substr($chars,20,12);

		return $prefix . $uuid;
	}
	
	/*=====================================================================
	* 比较两个版本，返回比较值
	* -1：小于，1：大于，0：等于
	* 以上比较针对v1比v2
	* 格式类似"1.0.1 beta2"
	======================================================================*/
	function compareVersion($v1,$v2){

		$v1=strtolower(trim($v1));
		$v2=strtolower(trim($v2));

		if($v1==$v2) return 0;
		
		//版本匹配正则
		$regex='([\d\.]+?)(\s+(dev|alpha|beta)(\d+)?)?';

		preg_match("/^$regex$/i", $v1, $matchs1);
		preg_match("/^$regex$/i", $v2, $matchs2);
		
		//如果格式都错误，返回等于
		if(!$matchs1 && !$matchs2){
			return 0;
		}elseif(!$matchs1 && $matchs2){
			return -1;
		}elseif($matchs1 && !$matchs2){
			return 1;
		}

		//比较主版本
		$main1=explode('.',$matchs1[1]);
		$main2=explode('.',$matchs2[1]);
		for($i=0;$i<3;$i++){
			$main1[$i]=empty($main1[$i])?0:SWFunc::checkInt($main1[$i]);
			$main2[$i]=empty($main2[$i])?0:SWFunc::checkInt($main2[$i]);
			if($main1[$i]<$main2[$i]) return -1;
			if($main1[$i]>$main2[$i]) return 1;
		}

		//后缀比较
		$plus1=empty($matchs1[3])?'3':$matchs1[3];
		$plus2=empty($matchs2[3])?'3':$matchs2[3];
        $plus1=str_replace('dev','0',$plus1);
		$plus2=str_replace('dev','0',$plus2);
		$plus1=str_replace('alpha','1',$plus1);
		$plus2=str_replace('alpha','1',$plus2);
		$plus1=str_replace('beta','2',$plus1);
		$plus2=str_replace('beta','2',$plus2);
		$plus1=SWFunc::checkInt($plus1);
		$plus2=SWFunc::checkInt($plus2);
		if($plus1<$plus2) return -1;
		if($plus1>$plus2) return 1;

		//后缀版本比较
		$plus1=empty($matchs1[4])?0:SWFunc::checkInt($matchs1[4]);
		$plus2=empty($matchs2[4])?0:SWFunc::checkInt($matchs2[4]);
		if($plus1<$plus2) return -1;
		if($plus1>$plus2) return 1;

		return 0;

	}
	
	/*=====================================================================
	* 获取世界所有timezone列表
	======================================================================*/
	function timezoneList(){
		return array("Africa/Abidjan","Africa/Accra","Africa/Addis_Ababa","Africa/Algiers","Africa/Asmara","Africa/Asmera","Africa/Bamako","Africa/Bangui","Africa/Banjul","Africa/Bissau","Africa/Blantyre","Africa/Brazzaville","Africa/Bujumbura","Africa/Cairo","Africa/Casablanca","Africa/Ceuta","Africa/Conakry","Africa/Dakar","Africa/Dar_es_Salaam","Africa/Djibouti","Africa/Douala","Africa/El_Aaiun","Africa/Freetown","Africa/Gaborone","Africa/Harare","Africa/Johannesburg","Africa/Kampala","Africa/Khartoum","Africa/Kigali","Africa/Kinshasa","Africa/Lagos","Africa/Libreville","Africa/Lome","Africa/Luanda","Africa/Lubumbashi","Africa/Lusaka","Africa/Malabo","Africa/Maputo","Africa/Maseru","Africa/Mbabane","Africa/Mogadishu","Africa/Monrovia","Africa/Nairobi","Africa/Ndjamena","Africa/Niamey","Africa/Nouakchott","Africa/Ouagadougou","Africa/Porto-Novo","Africa/Sao_Tome","Africa/Timbuktu","Africa/Tripoli","Africa/Tunis","Africa/Windhoek","America/Adak","America/Anchorage","America/Anguilla","America/Antigua","America/Araguaina","America/Argentina/Buenos_Aires","America/Argentina/Catamarca","America/Argentina/ComodRivadavia","America/Argentina/Cordoba","America/Argentina/Jujuy","America/Argentina/La_Rioja","America/Argentina/Mendoza","America/Argentina/Rio_Gallegos","America/Argentina/San_Juan","America/Argentina/San_Luis","America/Argentina/Tucuman","America/Argentina/Ushuaia","America/Aruba","America/Asuncion","America/Atikokan","America/Atka","America/Bahia","America/Barbados","America/Belem","America/Belize","America/Blanc-Sablon","America/Boa_Vista","America/Bogota","America/Boise","America/Buenos_Aires","America/Cambridge_Bay","America/Campo_Grande","America/Cancun","America/Caracas","America/Catamarca","America/Cayenne","America/Cayman","America/Chicago","America/Chihuahua","America/Coral_Harbour","America/Cordoba","America/Costa_Rica","America/Cuiaba","America/Curacao","America/Danmarkshavn","America/Dawson","America/Dawson_Creek","America/Denver","America/Detroit","America/Dominica","America/Edmonton","America/Eirunepe","America/El_Salvador","America/Ensenada","America/Fort_Wayne","America/Fortaleza","America/Glace_Bay","America/Godthab","America/Goose_Bay","America/Grand_Turk","America/Grenada","America/Guadeloupe","America/Guatemala","America/Guayaquil","America/Guyana","America/Halifax","America/Havana","America/Hermosillo","America/Indiana/Indianapolis","America/Indiana/Knox","America/Indiana/Marengo","America/Indiana/Petersburg","America/Indiana/Tell_City","America/Indiana/Vevay","America/Indiana/Vincennes","America/Indiana/Winamac","America/Indianapolis","America/Inuvik","America/Iqaluit","America/Jamaica","America/Jujuy","America/Juneau","America/Kentucky/Louisville","America/Kentucky/Monticello","America/Knox_IN","America/La_Paz","America/Lima","America/Los_Angeles","America/Louisville","America/Maceio","America/Managua","America/Manaus","America/Marigot","America/Martinique","America/Mazatlan","America/Mendoza","America/Menominee","America/Merida","America/Mexico_City","America/Miquelon","America/Moncton","America/Monterrey","America/Montevideo","America/Montreal","America/Montserrat","America/Nassau","America/New_York","America/Nipigon","America/Nome","America/Noronha","America/North_Dakota/Center","America/North_Dakota/New_Salem","America/Panama","America/Pangnirtung","America/Paramaribo","America/Phoenix","America/Port-au-Prince","America/Port_of_Spain","America/Porto_Acre","America/Porto_Velho","America/Puerto_Rico","America/Rainy_River","America/Rankin_Inlet","America/Recife","America/Regina","America/Resolute","America/Rio_Branco","America/Rosario","America/Santiago","America/Santo_Domingo","America/Sao_Paulo","America/Scoresbysund","America/Shiprock","America/St_Barthelemy","America/St_Johns","America/St_Kitts","America/St_Lucia","America/St_Thomas","America/St_Vincent","America/Swift_Current","America/Tegucigalpa","America/Thule","America/Thunder_Bay","America/Tijuana","America/Toronto","America/Tortola","America/Vancouver","America/Virgin","America/Whitehorse","America/Winnipeg","America/Yakutat","America/Yellowknife","Antarctica/Casey","Antarctica/Davis","Antarctica/DumontDUrville","Antarctica/Mawson","Antarctica/McMurdo","Antarctica/Palmer","Antarctica/Rothera","Antarctica/South_Pole","Antarctica/Syowa","Antarctica/Vostok","Arctic/Longyearbyen","Asia/Aden","Asia/Almaty","Asia/Amman","Asia/Anadyr","Asia/Aqtau","Asia/Aqtobe","Asia/Ashgabat","Asia/Ashkhabad","Asia/Baghdad","Asia/Bahrain","Asia/Baku","Asia/Bangkok","Asia/Beirut","Asia/Bishkek","Asia/Brunei","Asia/Calcutta","Asia/Choibalsan","Asia/Chongqing","Asia/Chungking","Asia/Colombo","Asia/Dacca","Asia/Damascus","Asia/Dhaka","Asia/Dili","Asia/Dubai","Asia/Dushanbe","Asia/Gaza","Asia/Harbin","Asia/Ho_Chi_Minh","Asia/Hong_Kong","Asia/Hovd","Asia/Irkutsk","Asia/Istanbul","Asia/Jakarta","Asia/Jayapura","Asia/Jerusalem","Asia/Kabul","Asia/Kamchatka","Asia/Karachi","Asia/Kashgar","Asia/Katmandu","Asia/Kolkata","Asia/Krasnoyarsk","Asia/Kuala_Lumpur","Asia/Kuching","Asia/Kuwait","Asia/Macao","Asia/Macau","Asia/Magadan","Asia/Makassar","Asia/Manila","Asia/Muscat","Asia/Nicosia","Asia/Novosibirsk","Asia/Omsk","Asia/Oral","Asia/Phnom_Penh","Asia/Pontianak","Asia/Pyongyang","Asia/Qatar","Asia/Qyzylorda","Asia/Rangoon","Asia/Riyadh","Asia/Saigon","Asia/Sakhalin","Asia/Samarkand","Asia/Seoul","Asia/Shanghai","Asia/Singapore","Asia/Taipei","Asia/Tashkent","Asia/Tbilisi","Asia/Tehran","Asia/Tel_Aviv","Asia/Thimbu","Asia/Thimphu","Asia/Tokyo","Asia/Ujung_Pandang","Asia/Ulaanbaatar","Asia/Ulan_Bator","Asia/Urumqi","Asia/Vientiane","Asia/Vladivostok","Asia/Yakutsk","Asia/Yekaterinburg","Asia/Yerevan","Atlantic/Azores","Atlantic/Bermuda","Atlantic/Canary","Atlantic/Cape_Verde","Atlantic/Faeroe","Atlantic/Faroe","Atlantic/Jan_Mayen","Atlantic/Madeira","Atlantic/Reykjavik","Atlantic/South_Georgia","Atlantic/St_Helena","Atlantic/Stanley","Australia/ACT","Australia/Adelaide","Australia/Brisbane","Australia/Broken_Hill","Australia/Canberra","Australia/Currie","Australia/Darwin","Australia/Eucla","Australia/Hobart","Australia/LHI","Australia/Lindeman","Australia/Lord_Howe","Australia/Melbourne","Australia/North","Australia/NSW","Australia/Perth","Australia/Queensland","Australia/South","Australia/Sydney","Australia/Tasmania","Australia/Victoria","Australia/West","Australia/Yancowinna","Brazil/Acre","Brazil/DeNoronha","Brazil/East","Brazil/West","Canada/Atlantic","Canada/Central","Canada/East-Saskatchewan","Canada/Eastern","Canada/Mountain","Canada/Newfoundland","Canada/Pacific","Canada/Saskatchewan","Canada/Yukon","CET","Chile/Continental","Chile/EasterIsland","CST6CDT","Cuba","EET","Egypt","Eire","EST","EST5EDT","Etc/GMT","Etc/GMT+0","Etc/GMT+1","Etc/GMT+10","Etc/GMT+11","Etc/GMT+12","Etc/GMT+2","Etc/GMT+3","Etc/GMT+4","Etc/GMT+5","Etc/GMT+6","Etc/GMT+7","Etc/GMT+8","Etc/GMT+9","Etc/GMT-0","Etc/GMT-1","Etc/GMT-10","Etc/GMT-11","Etc/GMT-12","Etc/GMT-13","Etc/GMT-14","Etc/GMT-2","Etc/GMT-3","Etc/GMT-4","Etc/GMT-5","Etc/GMT-6","Etc/GMT-7","Etc/GMT-8","Etc/GMT-9","Etc/GMT0","Etc/Greenwich","Etc/UCT","Etc/Universal","Etc/UTC","Etc/Zulu","Europe/Amsterdam","Europe/Andorra","Europe/Athens","Europe/Belfast","Europe/Belgrade","Europe/Berlin","Europe/Bratislava","Europe/Brussels","Europe/Bucharest","Europe/Budapest","Europe/Chisinau","Europe/Copenhagen","Europe/Dublin","Europe/Gibraltar","Europe/Guernsey","Europe/Helsinki","Europe/Isle_of_Man","Europe/Istanbul","Europe/Jersey","Europe/Kaliningrad","Europe/Kiev","Europe/Lisbon","Europe/Ljubljana","Europe/London","Europe/Luxembourg","Europe/Madrid","Europe/Malta","Europe/Mariehamn","Europe/Minsk","Europe/Monaco","Europe/Moscow","Europe/Nicosia","Europe/Oslo","Europe/Paris","Europe/Podgorica","Europe/Prague","Europe/Riga","Europe/Rome","Europe/Samara","Europe/San_Marino","Europe/Sarajevo","Europe/Simferopol","Europe/Skopje","Europe/Sofia","Europe/Stockholm","Europe/Tallinn","Europe/Tirane","Europe/Tiraspol","Europe/Uzhgorod","Europe/Vaduz","Europe/Vatican","Europe/Vienna","Europe/Vilnius","Europe/Volgograd","Europe/Warsaw","Europe/Zagreb","Europe/Zaporozhye","Europe/Zurich","Factory","GB","GB-Eire","GMT","GMT+0","GMT-0","GMT0","Greenwich","Hongkong","HST","Iceland","Indian/Antananarivo","Indian/Chagos","Indian/Christmas","Indian/Cocos","Indian/Comoro","Indian/Kerguelen","Indian/Mahe","Indian/Maldives","Indian/Mauritius","Indian/Mayotte","Indian/Reunion","Iran","Israel","Jamaica","Japan","Kwajalein","Libya","MET","Mexico/BajaNorte","Mexico/BajaSur","Mexico/General","MST","MST7MDT","Navajo","NZ","NZ-CHAT","Pacific/Apia","Pacific/Auckland","Pacific/Chatham","Pacific/Easter","Pacific/Efate","Pacific/Enderbury","Pacific/Fakaofo","Pacific/Fiji","Pacific/Funafuti","Pacific/Galapagos","Pacific/Gambier","Pacific/Guadalcanal","Pacific/Guam","Pacific/Honolulu","Pacific/Johnston","Pacific/Kiritimati","Pacific/Kosrae","Pacific/Kwajalein","Pacific/Majuro","Pacific/Marquesas","Pacific/Midway","Pacific/Nauru","Pacific/Niue","Pacific/Norfolk","Pacific/Noumea","Pacific/Pago_Pago","Pacific/Palau","Pacific/Pitcairn","Pacific/Ponape","Pacific/Port_Moresby","Pacific/Rarotonga","Pacific/Saipan","Pacific/Samoa","Pacific/Tahiti","Pacific/Tarawa","Pacific/Tongatapu","Pacific/Truk","Pacific/Wake","Pacific/Wallis","Pacific/Yap","Poland","Portugal","PRC","PST8PDT","ROC","ROK","Singapore","Turkey","UCT","Universal","US/Alaska","US/Aleutian","US/Arizona","US/Central","US/East-Indiana","US/Eastern","US/Hawaii","US/Indiana-Starke","US/Michigan","US/Mountain","US/Pacific","US/Pacific-New","US/Samoa","UTC","W-SU","WET","Zulu");
	}
    
}

?>
