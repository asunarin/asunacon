<?php

defined('SW_') or die('Access Error');

class SWInstall{
	
	var $title='My Swan Blog';
	var $description='Another swan blog';
	var $url='';
	var $language='zh_cn';
	var $timezone='Asia/Shanghai';
	var $author='User';
	var $email='youremail@youremail.com';
	var $account='admin';
	var $password='';
	
	var $dbhost='localhost';
	var $dbname='swan';
	var $dbuser='root';
	var $dbpass='';
	var $dbprefix='sw_';

	/*=====================================================================
	* 获取http访问的程序根路径
	======================================================================*/
	function getRootUrl(){
		
		return 'http://'.$_SERVER['HTTP_HOST'].SW::urlPath('',true);
		
	}
	
	/*=====================================================================
	* 验证数据库设置是否正确
	======================================================================*/
	function checkDatabase(){
		
		$db=@mysql_connect($this->dbhost,$this->dbuser,$this->dbpass);
		
		if(!$db || !@mysql_select_db($this->dbname,$db)) return false;
		
		return true;
		
	}
	
	/*=====================================================================
	* 生成config文件
	* 路径：/config.php
	======================================================================*/
	function createConfig(){
		
		$path=SW::path('config');
		
		//创建文件
		$f=@fopen($path,'wb');
		$res=@fwrite($f,$this->getConfig());
		@fclose($f);
		
		return $res?true:false;
		
	}
	
	/*=====================================================================
	* 获取config文件字符串
	======================================================================*/
	function getConfig(){
		
		$str="<?php
		defined('SW_') or die('Access Error');
		define('SW_HASH','".SWFunc::randomString(16)."');
		define('SW_CREATE','".time()."');
		define('SW_HOST','".$this->dbhost."');
		define('SW_DB','".$this->dbname."');
		define('SW_USER','".$this->dbuser."');
		define('SW_PASS','".$this->dbpass."');
		define('SW_PREFIX','".$this->dbprefix."');
		?>";
		
		return trim(str_replace("\t",'',$str));
		
	}
	
	/*=====================================================================
	* 导入数据库（生成config文件之后才能执行）
	======================================================================*/
	function importData(){
		
		$db=SW::getDb($this->dbhost,$this->dbname,$this->dbuser,$this->dbpass);

		foreach($this->getImportSql() as $sql){
			$db->execute($sql);
		}
				
		//添加默认option设置
		$sql="
		INSERT INTO `".$this->dbprefix."option` (`name`, `type`, `value`, `default`) VALUES
		('title', 'string', '".$this->title."', ''),
		('description', 'string', '".$this->description."', ''),
		('url', 'string', '".$this->url."', ''),
		('author', 'string', '".$this->author."', ''),
		('email', 'string', '".$this->email."', ''),
		('account', 'string', '".$this->account."', ''),
		('password', 'string', '".md5($this->password)."', ''),
		('pingback', 'bool', '0', ''),
		('language', 'string', '".$this->language."', ''),
		('timezone', 'string', '".$this->timezone."', ''),
		('checkcomment', 'bool', '0', ''),
		('receivepingback', 'bool', '1', ''),
		('receivetrackback', 'bool', '1', ''),
		('feedcount', 'int', '10', ''),
		('version', 'string', '".SW_VERSION."', ''),
		('template', 'string', 'default', ''),
		('rewrite', 'bool', '0', ''),
		('template_option', 'json', '0', ''),
		('mail_type', 'string', '', ''),
		('mail_address', 'string', '', ''),
		('mail_server', 'string', '', ''),
		('mail_port', 'int', '0', ''),
		('mail_account', 'string', '', ''),
		('mail_password', 'string', '', '');
		";
		$db->execute($sql);
		
	}
	
	/*=====================================================================
	* 生成导入的SQL语句
	======================================================================*/
	function getImportSql(){
		
		//导入的SQL语句
		$arrSql=array();
		
		$arrSql[]="CREATE TABLE IF NOT EXISTS `".$this->dbprefix."comment` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `parentid` int(11) NOT NULL,
		  `postid` int(11) NOT NULL,
		  `content` text NOT NULL,
		  `author` varchar(255) NOT NULL,
		  `email` varchar(255) NOT NULL,
		  `url` varchar(255) NOT NULL,
		  `ip` varchar(255) NOT NULL,
		  `posttime` int(11) NOT NULL,
		  `state` tinyint(4) NOT NULL,
		  `type` tinyint(4) NOT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
		
		$arrSql[]="CREATE TABLE IF NOT EXISTS `".$this->dbprefix."option` (
		  `name` varchar(255) NOT NULL,
		  `type` varchar(255) NOT NULL,
		  `value` text NOT NULL,
		  `default` text NOT NULL,
		  PRIMARY KEY  (`name`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;";

		$arrSql[]="CREATE TABLE IF NOT EXISTS `".$this->dbprefix."page` (
		  `id` int(11) NOT NULL auto_increment,
		  `parentid` int(11) NOT NULL,
		  `parentmap` varchar(255) NOT NULL,
		  `title` varchar(255) NOT NULL,
		  `content` text NOT NULL,
		  `alias` varchar(255) NOT NULL,
		  `posttime` int(11) NOT NULL,
		  `edittime` int(11) NOT NULL,
		  `state` tinyint(4) NOT NULL,
		  `flag` tinyint(4) NOT NULL,
		  `ordernum` int(11) NOT NULL,
		  `onmenu` tinyint(4) NOT NULL,
		  PRIMARY KEY  (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
		
		$arrSql[]="CREATE TABLE IF NOT EXISTS `".$this->dbprefix."post` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `title` varchar(255) NOT NULL,
		  `content` text NOT NULL,
		  `state` tinyint(4) NOT NULL,
		  `allowcomment` tinyint(4) NOT NULL,
		  `posttime` int(11) NOT NULL,
		  `edittime` int(11) NOT NULL,
		  `alias` varchar(255) NOT NULL,
		  `flag` tinyint(4) NOT NULL,
		  `log_pingback` text NOT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
		
		$arrSql[]="CREATE TABLE IF NOT EXISTS `".$this->dbprefix."tag` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `postid` int(11) NOT NULL,
		  `name` varchar(255) NOT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
		
		return $arrSql;

	}
	
}

?>
