<?php

defined('SW_') or die('Access Error');

class SWUpgrade{
	
	function __construct(){
		
		//初始版本从1.0.0 beta1开始
		$this->step=SW::getOption('version');

	}
	
	/*=====================================================================
	* 更新系统，将根据版本号作出跟新步骤
	* 执行这个方法前需确认程序已经安装
	======================================================================*/
	function upgrade(){

        //如果版本小于1.0.0 beta1，那么不能升级
		if(SWFunc::compareVersion($this->step,'1.0.0 beta1')<0){

			return false;
			
		}
		
		//如果版本小于1.1.0
		if(SWFunc::compareVersion($this->step,'1.1.0')<0){
			
			$arrSql=array();
			//添加onmenu开关
			$arrSql[]="ALTER TABLE  `".TBL_PAGE."` ADD  `onmenu` TINYINT NOT NULL ;";
			//添加父id等相关字段
			$arrSql[]="ALTER TABLE  `".TBL_PAGE."` ADD  `parentid` INT NOT NULL AFTER  `id` ,
			ADD  `parentmap` VARCHAR( 255 ) NOT NULL AFTER  `parentid` ;";
			//将菜单项设置为开
			$arrSql[]="UPDATE  `".TBL_PAGE."` SET  `onmenu`=1 ;";
			//添加回复相关字段
			$arrSql[]="ALTER TABLE `".TBL_COMMENT."` ADD `parentid` INT NOT NULL AFTER `id` ;";
			
			$db=SW::getDb();
			foreach($arrSql as $sql){
				$db->execute($sql);
			}
		
		}

		//如果版本小于1.2.0
		if(SWFunc::compareVersion($this->step,'1.2.0')<0){
			
			$arrSql=array();
			//添加mail服务器相关配置开关
			$arrSql[]="
			INSERT INTO `".TBL_OPTION."` (`name`, `type`, `value`, `default`) VALUES
			('mail_type', 'string', '', ''),
			('mail_address', 'string', '', ''),
			('mail_server', 'string', '', ''),
			('mail_port', 'int', '0', ''),
			('mail_account', 'string', '', ''),
			('mail_password', 'string', '', '');
			";
			
			$db=SW::getDb();
			foreach($arrSql as $sql){
				$db->execute($sql);
			}
		
		}

		//最终更新版本为当前版本
		SW::getOption()->version=SW_VERSION;
		SW::getOption()->saveByName('version');
		return true;
		
	}
	
}

?>
