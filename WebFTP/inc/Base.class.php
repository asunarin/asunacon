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
 * 文件$ID ： Base.class.php
 +------------------------------------------------------------------------------
 * 路径$ID ： inc/Base.class.php
 +------------------------------------------------------------------------------
 * 程序版本： 浩天 WebFTP V1.0.0 2011-10-01
 +------------------------------------------------------------------------------
 * 功能简介： 文件、目录处理 Base Class
 +------------------------------------------------------------------------------
 * 注意事项： 请勿私自删除此版权信息！
 +------------------------------------------------------------------------------
 **/
 
if(!defined('INC_ROOT')){die('Forbidden Access');}
class Base
{
	public function chmod($path,$chmod,&$info,&$err){
		if (!is_dir($path)){return false;}
		$info['file'] = $info['dir'] = 0;
		$err['file'] = $err['dir'] = array();
		if ($this->getPerm($path) != '0777'){$this->setChmod($path,0777);}
		$this->myList($path,$dirs,$files);
		foreach($files as $f){
			if($this->setChmod($f,$chmod)){
				$info['file']++;
			}else{
				$err['file'][] = $f;
			}
		}
		foreach($dirs as $f){
			if($this->setChmod($f,$chmod)){
				$info['dir']++;
			}else{
				$err['dir'][] = $f;
			}
		}
		if ($this->setChmod($path,$chmod)){$info["dir"]++;}
		return $info["dir"];
	}
	//强制删除文件夹
	public function del_dir($path,&$info,&$err){
		$dirs = array(); $files = array();
		if (!is_dir($path)){return false;}
		//if ($this->getPerm($path) != '0777'){$this->setChmod($path,"0777");}
		$this->myList($path,$dirs,$files);
		foreach($files as $f){
			if(@unlink($f)){
				$info['file']++;
			}else{
				$err['file'][] = $f;
			}
		}
		for($i=count($dirs)-1;$i>=0;$i--){
			$f = $dirs[$i];
			if (@rmdir($f)){
				$info["dir"]++;
			}else{
				$err['dir'][] = $f;
			}
		}
		if(@rmdir($path)){$info["dir"]++;}
		return $info["dir"];
	}
	//读取目录
	public function show_dir($path,&$dir,&$file,&$size,$deep=0){
		$this->myList($path,$dir,$file,$deep);
		if (substr($path,-1)!="/") $path.="/";
		for($i=0;$i<count($dir);$i++){
			$dir[$i]=str_replace($path,"",$dir[$i]);
		}
		sort($dir);
		sort($file);
		if ($size!='not'){
			$size=array();
			for($i=0;$i<count($file);$i++) $size[$i]=filesize($file[$i]);
		}
		for($i=0;$i<count($file);$i++){
			$file[$i]=str_replace($path,'',$file[$i]);
		}
		return true;
	}
	//$Base->copy($from,$to,$cover,$cut,$coverfiles);
	public function copy($from,$to,$cover=false,$cut=false,&$coverfiles,&$info){
	    $info['dir'] = $info['file'] = $info['size'] = 0;
		if (is_array($from) && is_array($to)){
			if (count($from)!=count($to)){return false;}
			$all=true;
			for($i=0;$i<count($from);$i++)
			{
				if (!file_exists($from[$i])){continue;}
				$this->move0($from[$i],$to[$i],$cover,$cut,$coverfiles,$info) or $all=false;
			}
			return $all;
		}else{
			return false;
		}
	}
	//文件、目录复制分流
	public function move0($from,$to,$cover,$cut,&$coverfiles,&$info){
		if ($this->getPerm($to) != '0777'){$this->setChmod($to,0777);}
		if ($cut && $this->getPerm($from) != '0777'){$this->setChmod($from,0777);}
		if(is_dir($from)){
			return $this->move1($from,$to,$cover,$cut,$coverfiles,$info);
		}elseif (is_file($from)){
			return $this->move2($from,$to,$cover,$cut,$coverfiles,$info);
		}else{
			return false;
		}
	}

	//移动目录
	public function move1($from,$to,$cover,$cut,&$coverfiles,&$info) {
		if (!is_dir($to)){mk_dir($to,0777);$info['dir']++;}
		$dirs = array();$files = array();
		$this->myList($from,$dirs,$files,0);
		foreach($dirs as $d){
			$this->move1($d,str_replace($from,$to,$d),$cover,$cut,$coverfiles,$info);
		}
		foreach($files as $f){
			$this->move2($f,str_replace($from,$to,$f),$cover,$cut,$coverfiles,$info);
		}
		//递归返回后删除此目录
		if ($cut && @rmdir($from)){}
		return true;
	}
	//移动文件
	function move2($from,$to,$cover,$cut,&$coverfiles,&$info){
		if (!file_exists($from)){return false;}
		if (file_exists($to)){
			$coverfiles[]=$to;
			if ($cover){
				$info['size'] += filesize($from);
				@unlink($to);
				if($this->move3($from,$to,$cut)){$info['file']++;}
			}
		}else{
		    $info['size'] += filesize($from);
			if($this->move3($from,$to,$cut)){$info['file']++;}
		}
		return true;
	}
	function move3($from,$to,$cut){
		return ($cut)?rename($from,$to):copy($from,$to);
	}

	//获取目录详细属性
	public function getProperty($path){
		$dirs = array(); $files = array(); $info = array();
		$this->myList($path,$dirs,$files);
		$info["dir"]      = count($dirs);
		$info["file"]     = count($files);
		$info["writable"] = is_writable($path);
		$info["chmod"]    = substr(sprintf('%o', @fileperms($path)), -4);
		$info["size"]     = 0;
		foreach($files as $f){$info["size"] += filesize($f);}
		return $info;
	}

	//搜索文件
	//$instr 为包含的字符串
	//$type=0,1,2 分别表示 搜索全部，只搜文件名，只搜文件内容
	//$case表示区分大小写,
	function searchFile($path = '',$instr = '', $type = 0, $case = false){
		if(empty($path) || empty($instr)){return false;}
		if(!file_exists($path) || !is_dir($path)){return false;}
		$dirs = array(); $files = array(); $return=array();
		$this->my_list($path,$dirs,$files);
		if (!$case){$instr=strtolower($instr);}
		foreach($files as $f){
			$fname = ($case)?$f:strtolower($f);
			if ((strpos($fname,$instr) !== false && $type != 2)){
				$return[]=$f;
				continue;
			}
			if (!in_array($this->getExt($f),C('SEARCH_FILE_TYPE'))){continue;}
			if (file_exists($f) && $type!=1){
				$content = file_get_contents($f);
				if (!$case){
					if(strpos(strtolower($content),$instr) !== false){$return[]=$f;}else{continue;}
				}else{
					if(strpos($content,$instr) !== false){$return[]=$f;}else{continue;}
				}
			}
		}
		return $return;
	}
	//获取扩展名
	protected function getExt($filename){
		$filename = basename(strtolower($filename));
		$arr = explode('.',$filename);
		$type = $arr[count($arr)-1];
		return strtolower($type);
	}

	//获取目录及文件权限
	protected function getPerm($path){
		return substr(sprintf('%o', @fileperms($path)), -4);
	}
	//修改文件权限
	protected function setChmod($file = '', $mode = 0777){
		if(!empty($file)){return @chmod($file,$mode);}
	}
	protected function is_utf8($string){
		return preg_match('%^(?:
      [\x09\x0A\x0D\x20-\x7E] # ASCII
      | [\xC2-\xDF][\x80-\xBF] # non-overlong 2-byte
      | \xE0[\xA0-\xBF][\x80-\xBF] # excluding overlongs
      | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} # straight 3-byte
      | \xED[\x80-\x9F][\x80-\xBF] # excluding surrogates
      | \xF0[\x90-\xBF][\x80-\xBF]{2} # planes 1-3
      | [\xF1-\xF3][\x80-\xBF]{3} # planes 4-15
      | \xF4[\x80-\x8F][\x80-\xBF]{2} # plane 16
      )*$%xs', $string);
	}
	//循环列出文件及目录
	protected function myList($path,&$dir,&$file,$deepest=-1,$deep=0){
		if(substr($path,-1)!='/'){$path .='/';}
		if(!is_array($file)){$file=array();}
		if(!is_array($dir)){$dir=array();}
		clearstatcache();
		if(true === is_readable($path) && false !== $handle=opendir($path)){			
			while(($val = readdir($handle)) !== false){
				if ($val=='.' || $val=='..'){continue;}
				$value = strval($path.$val);
				if (is_file($value)){
					$file[]=$value;
				}else if(is_dir($value)){
					$dir[]=$value;
					if($deep<$deepest || $deepest==-1){
						$this->myList($value."/",$dir,$file,$deepest,$deep+1);
					}
				}
			}
			closedir($handle);
		}
		return true;
	}
}
?>