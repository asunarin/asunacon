<?php

defined('SW_') or die('Access Error');

class SWOption extends SWTableOption{
	
	function __construct(){
		
		//设置对象数据表名
		$this->tableName=TBL_OPTION;
		
		//设置对象数据表
		$this->table=array(
			'name'=>array('type'=>'string','default'=>''),
			'type'=>array('type'=>'string','default'=>''),
			'value'=>array('type'=>'string','default'=>''),
			'default'=>array('type'=>'string','default'=>'')
		);
		
		//直接获取数据库中的属性
		$this->load();

	}
	
	/*=====================================================================
	* 保存常规option设置
	======================================================================*/
	function saveOption(){

		//保存设置的时候，只有这些字段会修改
		$arrAllow=array(
			'title',
			'description',
			'url',
			'author',
			'email',
			'account',
			'password',
			'pingback',
			'language',
			'timezone',
			'checkcomment',
			'receivepingback',
			'receivetrackback',
			'feedcount',
			'rewrite',
			'mail_type',
			'mail_address',
			'mail_server',
			'mail_port',
			'mail_account',
			'mail_password'
		);
		
		foreach($arrAllow as $value){

			$this->saveByName($value);

		}
		
		return true;

	}

	/*=====================================================================
	* 保存template设置
	======================================================================*/
	function saveTemplate(){
		
		//获取所有模板
		$arrTemplate=SWTemplate::getTemplate();

		//如果模板文件夹为空
		if(!$arrTemplate){
			$this->template='';
		
		//如果没选择或者模板名错误，那么就选择第一个模板
		}elseif(empty($arrTemplate[$this->template])){
			
			foreach($arrTemplate as $key=>$value){
				$this->template=$key;
				break;
			}
			
		}
		
		//保存这些字段
		$arrAllow=array(
			'template'
		);
		
		foreach($arrAllow as $value){

			$this->saveByName($value);

		}
		
		return true;
		
	}
	
}

?>
