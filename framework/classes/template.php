<?php

defined('SW_') or die('Access Error');

class SWTemplate{

	/*=====================================================================
	* 构造函数，初始化一些参数
	======================================================================*/
	public function __construct(){


	}
	
	/*=====================================================================
	* 取得所有模板，并获得配置xml信息
	* 在/templates/目录下
	* 返回一个数组
	======================================================================*/
	public static function getTemplate(){

		$arr=array();
		
		//首先取得templates下所有文件夹
		$arrDirs=SWFunc::getDirs(SW::dirPath('templates'));

		//循环文件夹，寻找xml
		foreach($arrDirs as $value){

			if(file_exists(SW::path('templates.'.$value.'.template','xml'))){

				$arrTmp=SWFunc::xml2array(SW::path('templates.'.$value.'.template','xml'));
				if($arrTmp['template']){
					
					$arr[$value]=$arrTmp['template'];
					
					//获取模板缩略图，true或false
					$arr[$value]['screenshot']=file_exists(SW::path('templates.'.$value.'.screenshot','png'));
					
				}

			}
		
		}		

		return $arr;

	}
	
	/*=====================================================================
	* 获取当前模板的路径
	======================================================================*/
	public static function getTemplatePath(){
		if(SW::getOption('template')){
			return 'templates.'.SW::getOption('template');
		}else{
			return 'templates.default';
		}
	}
	
	/*=====================================================================
	* 获取当前模板的URL地址
	======================================================================*/
	public static function getTemplateUrl(){
		if(SW::getOption('template')){
			return SW::getOption('url').'templates/'.SW::getOption('template').'/';
		}else{
			return SW::getOption('url').'default/';
		}
	}

	/*=====================================================================
	* 获取用户模板配置信息
	======================================================================*/
	public function getTemplateOption(){


		
	}

	/*=====================================================================
	* 将用户模板配置信息设置到对象的属性
	======================================================================*/
	public function setTemplateOption(){


		
	}

}

?>
