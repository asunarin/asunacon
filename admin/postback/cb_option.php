<?php
defined('SW_') or die('Access Error');

switch(SW::request('act','get')){
	
	case 'save':

		$objOption=new SWOption();

		$strErr='';
		
		$objOption->title=SWFunc::closeHtml(SW::request('title','post'));
		if(!$objOption->title){
			$strErr=LANG('Invalid Option_Title');
		}
		
		$objOption->description=SWFunc::closeHtml(SW::request('description','post'));
		
		$objOption->url=SWFunc::checkUrl(SW::request('url','post'),true);
		if(!$objOption->url){
			$strErr=LANG('Invalid Option_Url');
		}
		
		$objOption->author=SWFunc::closeHtml(SW::request('author','post'));
		if(!$objOption->author){
			$strErr=LANG('Invalid Option_Author');
		}
		
		$objOption->email=SWFunc::checkString(SW::request('email','post'));
		if(!SWFunc::checkEmail($objOption->email)){
			$strErr=LANG('Invalid Option_Email');
		}
		
		$objOption->account=SWFunc::checkString(SW::request('account','post'));
		if(!SWFunc::checkCharacter($objOption->account,3)){
			$strErr=LANG('Invalid Account');
		}
		
		//如果没有输入密码，那么密码不变，否则判断两次输入是否一样，同时进行认证
		if(
			SWFunc::checkString(SW::request('password','post')) &&
			SW::request('password','post')==SW::request('repassword','post') &&
			SWFunc::checkCharacter(SW::request('password','post'),2,4,16)
		){
			$objOption->password=md5(SWFunc::checkString(SW::request('password','post')));
		}elseif(SWFunc::checkString(SW::request('password','post'))){
			$strErr=LANG('Invalid Password');
		}
		
		$objOption->pingback=SWFunc::checkInt(SW::request('pingback','post'))>0?1:0;
		
		$objOption->language=SWFunc::checkString(SW::request('language','post'));
		if(!$objOption->language){
			$strErr=LANG('Invalid Option_Language');
		}
		
		$objOption->timezone=SWFunc::checkString(SW::request('timezone','post'));
		if(!$objOption->timezone){
			$strErr=LANG('Invalid Option_Timezone');
		}

		$objOption->timezone=SWFunc::checkString(SW::request('timezone','post'));
		if(!$objOption->timezone){
			$strErr=LANG('Invalid Option_Timezone');
		}

		$objOption->rewrite=SWFunc::checkInt(SW::request('rewrite','post'))>0?1:0;
		
		$objOption->checkcomment=SWFunc::checkInt(SW::request('checkcomment','post'))>0?1:0;
		
		$objOption->receivepingback=SWFunc::checkInt(SW::request('receivepingback','post'))>0?1:0;
		
		$objOption->receivetrackback=SWFunc::checkInt(SW::request('receivetrackback','post'))>0?1:0;

		$objOption->feedcount=SWFunc::checkInt(SW::request('feedcount','post'));
		if(!$objOption->feedcount) $objOption->feedcount=10; //默认值为10
		
		//邮箱配置
		$objOption->mail_type=SWFunc::checkString(SW::request('mail_type','post'));
		if($objOption->mail_type!='smtp') $objOption->mail_type='';

		$objOption->mail_address=SWFunc::checkString(SW::request('mail_address','post'));
		if($objOption->mail_address && !SWFunc::checkEmail($objOption->mail_address)){
			$strErr=LANG('Invalid Option_Mail_Address');
		}

		$objOption->mail_server=SWFunc::checkString(SW::request('mail_server','post'));
		
		$objOption->mail_port=SWFunc::checkInt(SW::request('mail_port','post'));

		$objOption->mail_account=SWFunc::checkString(SW::request('mail_account','post'));

		$objOption->mail_password=SWFunc::checkString(SW::request('mail_password','post'));
		
		//如果邮箱设置为smtp模式，那么内容需要强制填写
		if($objOption->mail_type=='smtp'){
			
			if(!$objOption->mail_address) $strErr=LANG('Invalid Option_Mail_Address');
			if(!$objOption->mail_server) $strErr=LANG('Invalid Option_Mail_Server');
			if(!$objOption->mail_port) $objOption->mail_port=25; //默认端口是25
			if(!$objOption->mail_account) $strErr=LANG('Invalid Option_Mail_Account');
			if(!$objOption->mail_password) $strErr=LANG('Invalid Option_Mail_Password');

		}

		//错误处理
		if(!$strErr){
			$objOption->saveOption();
			
			//重新载入设置信息和登录信息
			SW::getOption()->load();
			SWAdmin::setLogin();
			
			SWAdmin::postJson(LANG('Save Setting Succeed'),0);
		}else{
			SWAdmin::postJson($strErr,1);
		}

	break;

}

?>
