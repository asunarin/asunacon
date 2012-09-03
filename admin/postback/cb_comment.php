<?php
defined('SW_') or die('Access Error');

switch(SW::request('act','get')){

	case 'save':

		$objComment=new SWComment();

		$strErr='';

		$objComment->id=SWFunc::checkInt(SW::request('id','post'));
		
		$objComment->content=SWFunc::closeHtml(SW::request('content','post'));
		if(!$objComment->content){
			$strErr=LANG('Invalid Comment_Content');
		}

		$objComment->author=SWFunc::closeHtml(SW::request('author','post'));
		if(!$objComment->author){
			$strErr=LANG('Invalid Comment_Author');
		}

		$objComment->email=SWFunc::checkString(SW::request('email','post'));
		if(!SWFunc::checkEmail($objComment->email)){
			$strErr=LANG('Invalid Comment_Email');
		}

		$objComment->url=SWFunc::checkUrl(SW::request('url','post'));

		$objComment->posttime=SWFunc::checkToTimestamp(SW::request('posttime','post'));

		$objComment->state=SWFunc::checkInt(SW::request('state','post'));
		if($objComment->state<0 or $objComment->state>2){
			$strErr=LANG('Invalid Comment_State');
		}

		if(!$strErr){
		
			$objComment->commentUpdate();

			SWAdmin::postJson('',0,array('id'=>$objComment->id));
			SWAdmin::setMessage(LANG('Save Comment Succeed'),0);

		}else{
			
			SWAdmin::postJson($strErr,1);

		}

	break;
	
}

?>