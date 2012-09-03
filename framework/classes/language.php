<?php

defined('SW_') or die('Access Error');

/*=====================================================================
* 语言类
======================================================================*/
class SWLanguage{
	
	function __construct(){}

     /*=====================================================================
	* 获取语言文件
    * 返回一个语言文件地址
	======================================================================*/
	public static function getLangFile(){
		
		return SW::path('languages.'.SW::getOption('language'));
		
	}
	
	 /*=====================================================================
	* 获取多语言支持，将包含语言文件
	* 同时获取多个词可以用空格隔开
	* 将替换语句中的{#XXX}号为arrWords['XXX']中对应的内容
	======================================================================*/
	public static function lang($key,$arrWords=array()){
		
		require(self::getLangFile());
		
		$arrLang=explode(' ',$key);
		$str='';
		
		foreach($arrLang as $value){

			if(!isset($lang[$value])){
				
				$str.=$value;

			}else{

				//如果没有对应键值的项目，则返回键值名称
				$str.=$lang[$value];

			}

		}
		
		//替换语句字符串操作
		foreach($arrWords as $key=>$value){
			
			$str=str_replace('{#'.$key.'}',$value,$str);
			
		}
		
		return $str;
		
	}

     /*=====================================================================
	* 生成一个js用的语言脚本
    * 将会生一个js函数，作用与php版的lang一样
	======================================================================*/
	public static function makeLangJS(){

        require(self::getLangFile());

        //获取$lang数组
        $arrTmp=array();
        foreach($lang as $key=>$value){
            $arrTmp[]="'".$key."':'".addslashes($value)."'";
        }
        $strTmp=implode(',',$arrTmp);

        $strJS='';
        $strJS.="function(str){\n";
        $strJS.="    var strRe='';\n";
        $strJS.="    var arrLang={".$strTmp."};\n";
        $strJS.="    var arr=str.split(' ');\n";
        $strJS.="    for(var i=0;i<arr.length;i++){\n";
        $strJS.="        strRe+=arrLang[arr[i]]?arrLang[arr[i]]:arr[i];\n";
        $strJS.="    }\n";
        $strJS.="    return strRe;\n";
        $strJS.="}\n";

        return $strJS;
		
	}

    /*=====================================================================
	* 取得所有语言文件的别表
	* 在/admin/languages/目录下
	* 返回一个数组
	======================================================================*/
	public static function getAllLanguages(){
		
		$arr=array();

		$arrFiles=SWFunc::getFiles(SW::dirPath('languages'));
		foreach($arrFiles as $value){

			$arr[]=$value['name'];

		}

		return $arr;
	
	}
	
}

?>
