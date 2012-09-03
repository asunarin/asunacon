<?php
defined('SW_') or die('Access Error');

switch(SW::request('act','get')){
	
	case 'send':

		//创建pingback对象
		$objPb=new SWPingback();
		
		$objPb->postid=SWFunc::checkInt(SW::request('postid','post'));
		$objPb->sourceURI=SWFunc::checkUrl(SW::request('sourceURI','post'));
		$objPb->targetURI=SWFunc::checkUrl(SW::request('targetURI','post'));
		
		//检查链接
		if(!$objPb->sourceURI || !$objPb->targetURI){

			SWAdmin::postJson('',1);

		}else{
		
			//发送ping请求
			if($objPb->sendPingback()){
				SWAdmin::postJson('',0);
			}else{
				SWAdmin::postJson('',1);
			}

		}
		
	break;
	
}

?>