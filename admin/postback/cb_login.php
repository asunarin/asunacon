<?php
defined('SW_') or die('Access Error');

switch(SW::request('act','get')){
	
	//登录操作
	case 'login':
		
		$account=SW::request('account','post');
		$password=SW::request('password','post');
				
		if(SWAdmin::checkAccount($account,md5($password))){
			
			SWAdmin::setLogin();
			SWAdmin::postJson('',0);
			SWAdmin::setMessage(LANG('Login Succeed'),0);
			
		}else{
			
			SWAdmin::postJson(LANG('Invalid_Account_Or_Password'),1);
			
		}
		
	break;
	
	//注销操作
	case 'logout':
	
		SWAdmin::setLogout();
		SWAdmin::postJson('',0);
		SWAdmin::setMessage(LANG('Logout Succeed'),0);
		
	break;
	
}

?>
