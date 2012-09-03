<?php

defined('SW_') or die('Access Error');

/*=====================================================================
* 该类用作admin全局类，所有都为静态方法，不需要声明对象
======================================================================*/
class SWAdmin{
	
	//页面头需要引入的页面js
	static $js=array();

	//页面头需要引入的页面css
	static $css=array();

	//页面的title
	static $title='';

	//页面的功能组（主要用于菜单的分类）
	static $group='';
	
	/*=====================================================================
	* 显示session中的提示信息，同时删除该session值
	* 这是以js方式显示，将在常规admin框架中使用
	======================================================================*/
	function showMessage(){
	
		if(SW::getSession('SW_message')){
			
			__('<script type="text/javascript">$(function(){SWJS.showMessage("'.SW::getSession('SW_message').'",'.(SW::getSession('SW_err')?SW::getSession('SW_err'):0).');});</script>');

		}
		
		SW::setSession('SW_message',null);
		SW::setSession('SW_err',null);

	}
	
	/*=====================================================================
	* 设置session提示信息
	======================================================================*/
	function setMessage($strMsg,$err){
		
		SW::setSession('SW_message',$strMsg);
		SW::setSession('SW_err',$err);

	}
	
	/*=====================================================================
	* 编码并显示json对象，需要设置err和msg信息
	======================================================================*/
	function postJson($strMsg,$err,$arr=array(),$login=true){

		$arrTmp=array(
			'err'=>$err,
			'msg'=>$strMsg,
			'login'=>$login
		);
		
		foreach($arr as $key=>$value){
			$arrTmp[$key]=$value;
		}
		
		__(json_encode($arrTmp));

	}

	/*=====================================================================
	* 检查用户是否登录
	* 如果exit为真，那么判断未登录的时候直接终止页面，否则返回true或者false
	* 如果callback为真，那么是ajax请求页面，不会直接跳转（只有在exit为真时才有效）
	======================================================================*/
	function checkLogin($exit=true,$callback=false){
		
		$account=SW::getCookies('SW_account');
		$password=SW::getCookies('SW_password');
		
		//如果没有登录
		if(!SWAdmin::checkAccount($account,$password)){

			if(!$exit) return false;
			
			SWAdmin::setMessage(LANG('No_Login_Or_Timeout'),1);
			
			if(!$callback){
			
				header("Location: login.php"); 
				
			}else{
				
				SWAdmin::postJson('',0,array(),false);
				
			}
			
			exit();
			
		}

		return true;

	}
	
	/*=====================================================================
	* 保存用户登录信息，设置cookie操作
	======================================================================*/
	function setLogin(){
		
		//cookie保存为浏览器进程
		SW::setCookies('SW_account',SW::getOption('account'));
		SW::setCookies('SW_password',SW::getOption('password'));

	}
	
	/*=====================================================================
	* 保存用户登录信息，设置cookie操作
	======================================================================*/
	function setLogout(){
		
		//cookie保存为空
		SW::setCookies('SW_account','');
		SW::setCookies('SW_password','');

	}
	
	/*=====================================================================
	* 检查用户名和密码是否正确
	======================================================================*/
	function checkAccount($account,$password){

		if($account==SW::getOption('account') && $password==SW::getOption('password')){
			
			return true;
			
		}else{
			
			return false;
			
		}

	}

	/*=====================================================================
	* 根据文章state数字返回state名称
	======================================================================*/
	function getPostState($state){
		
		$name='';

		switch($state){
			case 0:
				$name=LANG('State_Draft');
			break;
			case 1:
				$name=LANG('State_Published');
			break;
			default:
				$name=$state;
			break;
		}

		return $name;

	}

	/*=====================================================================
	* 根据页面state数字返回state名称
	======================================================================*/
	function getPageState($state){
		
		return self::getPostState($state);

	}

	/*=====================================================================
	* 根据评论state数字返回state名称
	======================================================================*/
	function getCommentState($state){
		
		$name='';

		switch($state){
			case 0:
				$name=LANG('State_Unapproved');
			break;
			case 1:
				$name=LANG('State_Approved');
			break;
			case 2:
				$name=LANG('State_Spam');
			break;
			default:
				$name=$state;
			break;
		}

		return $name;

	}

	/*=====================================================================
	* 根据评论type数字返回type名称
	======================================================================*/
	function getCommentType($type){
		
		$name='';

		switch($type){
			case 1:
				$name=LANG('Comment');
			break;
			case 2:
				$name=LANG('Pingback');
			break;
			case 3:
				$name=LANG('Trackback');
			break;
			default:
				$name=$state;
			break;
		}

		return $name;

	}

	/*=====================================================================
	* 根据时间返回据现在多久，30天之后将直接显示时间
	======================================================================*/
	function getTimeLength($time,$format='Y-m-d'){
		
		$str=date($format,$time);
		$dTime=time()-$time;

		if($dTime>=0 && $dTime<60){
			$str='1'.LANG('Minutes Ago');
		}elseif($dTime>=60 && $dTime<3600){
			$str=floor($dTime/60).LANG('Minutes Ago');
		}elseif($dTime>=3600 && $dTime<86400){
			$str=floor($dTime/3600).LANG('Hours Ago');
		}elseif($dTime>=86400 && $dTime<604800){
			$str=floor($dTime/86400).LANG('Days Ago');
		}elseif($dTime>=604800 && $dTime<2592000){
			$str=floor($dTime/604800).LANG('Weeks Ago');
		}elseif($dTime>=2592000 && $dTime<31536000){
			$str=floor($dTime/2592000).LANG('Months Ago');
		}elseif($dTime>=31536000){
			$str=floor($dTime/31536000).LANG('Years Ago');
		}

		return $str;
	}

}

?>
